<?php

namespace Pim\Bundle\ImportExportBundle\Processor;

use Symfony\Component\Translation\TranslatorInterface;
use Oro\Bundle\BatchBundle\Entity\StepExecution;
use Pim\Bundle\ImportExportBundle\Transformer\ORMProductTransformer;
use Pim\Bundle\ImportExportBundle\Validator\Import\ImportValidatorInterface;

/**
 * Product import processor
 * Allows to bind data into a product and validate them
 *
 * @author    Gildas Quemener <gildas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductProcessor extends AbstractTransformerProcessor
{
    /**
     * @var ORMProductTransformer
     */
    protected $transformer;

    /**
     * @var boolean
     */
    protected $enabled = true;

    /**
     * @var string
     */
    protected $categoriesColumn = 'categories';

    /**
     * @var string
     */
    protected $familyColumn  = 'family';

    /**
     * @var string
     */
    protected $groupsColumn  = 'groups';

    /**
     * @var boolean
     */
    protected $heterogeneous = false;

    /**
     * @var StepExecution
     */
    protected $stepExecution;

    /**
     * Constructor
     *
     * @param ImportValidatorInterface $validator
     * @param TranslatorInterface      $translator
     * @param ORMProductTransformer    $transformer
     */
    public function __construct(
        ImportValidatorInterface $validator,
        TranslatorInterface $translator,
        ORMProductTransformer $transformer
    ) {
        parent::__construct($validator, $translator);
        $this->transformer = $transformer;
    }

    /**
     * Set wether or not the created product should be activated or not
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Wether or not the created product should be activated or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set the categories column
     *
     * @param string $categoriesColumn
     */
    public function setCategoriesColumn($categoriesColumn)
    {
        $this->categoriesColumn = $categoriesColumn;
    }

    /**
     * Get the categories column
     *
     * @return string
     */
    public function getCategoriesColumn()
    {
        return $this->categoriesColumn;
    }

    /**
     * Set the groups column
     *
     * @param string $groupsColumn
     */
    public function setGroupsColumn($groupsColumn)
    {
        $this->groupsColumn = $groupsColumn;
    }

    /**
     * Get the categories column
     *
     * @return string
     */
    public function getGroupsColumn()
    {
        return $this->groupsColumn;
    }

    /**
     * Set the family column
     *
     * @param string $familyColumn
     */
    public function setFamilyColumn($familyColumn)
    {
        $this->familyColumn = $familyColumn;
    }

    /**
     * Get the family column
     *
     * @return string
     */
    public function getFamilyColumn()
    {
        return $this->familyColumn;
    }

    /**
     * Set wether or not the product data are heterogeneous, means different attributes for each product row
     *
     * @param boolean $heterogeneous
     */
    public function setHeterogeneous($heterogeneous)
    {
        $this->heterogeneous = $heterogeneous;
        $this->transformer->setHeterogeneous($heterogeneous);
    }

    /**
     * Wether or not the products are heterogeneous
     *
     * @return bool
     */
    public function isHeterogeneous()
    {
        return $this->heterogeneous;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFields()
    {
        return array(
            'enabled' => array(
                'type'    => 'switch',
                'options' => array(
                    'label' => 'pim_import_export.import.enabled.label',
                    'help'  => 'pim_import_export.import.enabled.help'
                )
            ),
            'categoriesColumn' => array(
                'options' => array(
                    'label' => 'pim_import_export.import.categoriesColumn.label',
                    'help'  => 'pim_import_export.import.categoriesColumn.help'
                )
            ),
            'familyColumn' => array(
                'options' => array(
                    'label' => 'pim_import_export.import.familyColumn.label',
                    'help'  => 'pim_import_export.import.familyColumn.help'
                )
            ),
            'groupsColumn' => array(
                'options' => array(
                    'label' => 'pim_import_export.import.groupsColumn.label',
                    'help'  => 'pim_import_export.import.groupsColumn.help'
                )
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function transform($item)
    {
        return $this->transformer->transform($item, array('enabled' => $this->enabled));
    }

    /**
     * {@inheritdoc}
     */
    protected function getMapping()
    {
        return array(
            $this->familyColumn     => 'family',
            $this->categoriesColumn => 'categories',
            $this->groupsColumn     => 'groups'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTransformedColumnsInfo()
    {
        return $this->transformer->getTransformedColumnsInfo();
    }

    /**
     * {@inheritdoc}
     */
    protected function getTransformerErrors()
    {
        return $this->transformer->getErrors();
    }

    /**
     * {@inheritdoc}
     */
    protected function mapValues(array &$values)
    {
        parent::mapValues($values);

        foreach ($values as $key => $value) {
            if (1 === preg_match('/-unit$/', $key)) {
                $metricValueKey = substr($key, 0, -5);
                if (!isset($values[$metricValueKey])) {
                    throw new \Exception(sprintf('Could not find matching metric value key for unit key "%s"', $value));
                }

                $values[$metricValueKey] .= sprintf(' %s', $value);
                unset($values[$key]);
            }
        }
    }
}
