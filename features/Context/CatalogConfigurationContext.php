<?php

namespace Context;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Behat\Event\OutlineExampleEvent;

/**
 * A context for initializing catalog configuration
 *
 * @author    Filips Alpe <filips@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CatalogConfigurationContext extends RawMinkContext
{
    /**
     * @var boolean
     */
    protected $catalogInitialized = false;

    /**
     * @var string Catalog configuration path
     */
    protected $catalogPath = 'catalog';

    /**
     * Fixture reference repository
     * @var ReferenceRepository
     */
    protected $referenceRepository;

    /**
     * @var string Path of the entity loaders
     */
    protected $entityLoaderPath = 'Context\Loader';

    /**
     * @var array Entity loaders and corresponding files
     */
    protected $entityLoaders = array(
        'CurrencyLoader'       => 'currencies',
        'LocaleLoader'         => null,
        'CategoryLoader'       => 'categories',
        'ChannelLoader'        => 'channels',
        'AttributeGroupLoader' => 'attribute_groups',
        'AttributeLoader'      => 'attributes',
        'FamilyLoader'         => 'families',
        'GroupTypeLoader'      => 'group_types',
        'GroupLoader'          => 'groups',
        'AssociationLoader'    => 'associations',
        'UserAttrLoader'       => null,
        'UserLoader'           => 'users',
    );

    /**
     * Initialize the default catalog configuration if the configuration has not been initialized by background steps
     * and no configuration is specified in the scenario
     *
     * @param object $event
     *
     * @BeforeScenario
     */
    public function initialize($event)
    {
        if ($this->catalogInitialized) {
            return;
        }

        if ($event instanceof ScenarioEvent) {
            $steps = $event->getScenario()->getSteps();
        } elseif ($event instanceof OutlineExampleEvent) {
            $steps = $event->getOutline()->getSteps();
        }

        foreach ($steps as $step) {
            if (preg_match('/^(?:a|the) "([^"]*)" catalog configuration$/', $step->getText())) {
                return;
            }
        }

        $this->aCatalogConfiguration('default');
    }

    /**
     * @AfterScenario
     */
    public function resetState()
    {
        $this->catalogInitialized = false;
    }

    /**
     * Initialize the reference repository
     */
    private function initializeReferenceRepository()
    {
        $this->referenceRepository = new ReferenceRepository($this->getEntityManager());
    }

    /**
     * @param string $catalog
     *
     * @throws ExpectationException If configuration is not found
     * @Given /^(?:a|the) "([^"]*)" catalog configuration$/
     */
    public function aCatalogConfiguration($catalog)
    {
        $started = microtime(true);

        $directory = sprintf('%s/%s/%s', __DIR__, $this->catalogPath, strtolower($catalog));

        if (!file_exists($directory)) {
            throw $this->getMainContext()->createExpectationException(
                sprintf('No configuration found for catalog "%s", looked in "%s"', $catalog, $directory)
            );
        }

        $this->createCatalog($directory);

        $elapsed = number_format(microtime(true) - $started, 2);
        $this->printDebug(sprintf('Created "%s" catalog, took %s seconds', $catalog, (string) $elapsed));
    }

    /**
     * @param string $directory
     */
    private function createCatalog($directory)
    {
        $this->initializeReferenceRepository();

        foreach ($this->entityLoaders as $loaderName => $fileName) {
            $loader = sprintf('%s\%s', $this->entityLoaderPath, $loaderName);
            $file = $fileName !== null ? sprintf('%s/%s.yml', $directory, $fileName) : null;
            $this->runLoader($loader, $file);
        }

        $this->catalogInitialized = true;
    }

    /**
     * Run an entity loader
     * @param string $loaderClass
     * @param string $filePath
     */
    private function runLoader($loaderClass, $filePath)
    {
        $loader = new $loaderClass();
        $loader->setContainer($this->getContainer());
        $loader->setReferenceRepository($this->referenceRepository);
        if ($filePath !== null) {
            $loader->setFilePath($filePath);
        }
        $loader->load($this->getEntityManager());
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->getMainContext()->getEntityManager();
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private function getContainer()
    {
        return $this->getMainContext()->getContainer();
    }
}