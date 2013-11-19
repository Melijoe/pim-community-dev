<?php

namespace Pim\Bundle\CatalogBundle\Entity;

use Pim\Bundle\FlexibleEntityBundle\Entity\Mapping\AbstractEntityAttributeOptionValue;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * Attribute option values
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @ExclusionPolicy("all")
 */
class AttributeOptionValue extends AbstractEntityAttributeOptionValue
{
    /**
     * Overrided to change target option name
     *
     * @var AttributeOption $option
     */
    protected $option;
}
