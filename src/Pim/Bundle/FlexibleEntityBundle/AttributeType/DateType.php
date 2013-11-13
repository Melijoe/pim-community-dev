<?php

namespace Pim\Bundle\FlexibleEntityBundle\AttributeType;

use Pim\Bundle\FlexibleEntityBundle\AttributeType\AbstractAttributeType;
use Pim\Bundle\FlexibleEntityBundle\Model\FlexibleValueInterface;

/**
 * Date attribute type
 */
class DateType extends AbstractAttributeType
{
    /**
     * {@inheritdoc}
     */
    protected function prepareValueFormOptions(FlexibleValueInterface $value)
    {
        $options = parent::prepareValueFormOptions($value);
        $options['widget'] = 'single_text';
        $options['input'] = 'datetime';

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pim_flexibleentity_date';
    }
}
