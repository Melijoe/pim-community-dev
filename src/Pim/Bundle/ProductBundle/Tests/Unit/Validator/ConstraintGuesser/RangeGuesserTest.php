<?php

namespace Pim\Bundle\ProductBundle\Tests\Unit\Validator\ConstraintGuesser;

use Pim\Bundle\ProductBundle\Validator\ConstraintGuesser\RangeGuesser;
use Oro\Bundle\FlexibleEntityBundle\AttributeType\AbstractAttributeType;

/**
 * @author    Gildas Quemener <gildas.quemener@gmail.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RangeGuesserTest extends ConstraintGuesserTest
{
    public function setUp()
    {
        $this->target = new RangeGuesser;
    }

    public function testInstanceOfContraintGuesserInterface()
    {
        $this->assertInstanceOf('Oro\Bundle\FlexibleEntityBundle\Form\Validator\ConstraintGuesserInterface', $this->target);
    }

    public function testSupportAttribute()
    {
        $this->assertTrue($this->target->supportAttribute(
            $this->getAttributeMock(array(
                'backendType' => AbstractAttributeType::BACKEND_TYPE_INTEGER
            ))
        ));

        $this->assertTrue($this->target->supportAttribute(
            $this->getAttributeMock(array(
                'backendType' => AbstractAttributeType::BACKEND_TYPE_METRIC
            ))
        ));

        $this->assertTrue($this->target->supportAttribute(
            $this->getAttributeMock(array(
                'backendType' => AbstractAttributeType::BACKEND_TYPE_PRICE
            ))
        ));

        $this->assertTrue($this->target->supportAttribute(
            $this->getAttributeMock(array(
                'backendType' => AbstractAttributeType::BACKEND_TYPE_DATE
            ))
        ));

        $this->assertTrue($this->target->supportAttribute(
            $this->getAttributeMock(array(
                'backendType' => AbstractAttributeType::BACKEND_TYPE_DATETIME
            ))
        ));
    }

    public function testGuessMinConstraint()
    {
        $constraints = $this->target->guessConstraints($this->getAttributeMock(array(
            'backendType' => AbstractAttributeType::BACKEND_TYPE_INTEGER,
            'numberMin'   => 100,
        )));

        $this->assertContainsInstanceOf('Pim\Bundle\ProductBundle\Validator\Constraints\Range', $constraints);
        $this->assertConstraintsConfiguration('Pim\Bundle\ProductBundle\Validator\Constraints\Range', $constraints, array(
            'min' => 100,
        ));
    }

    public function testGuessNegativeNotAllowedConstraint()
    {
        $constraints = $this->target->guessConstraints($this->getAttributeMock(array(
            'backendType'     => AbstractAttributeType::BACKEND_TYPE_INTEGER,
            'negativeAllowed' => false,
            'numberMin'       => 100,
        )));

        $this->assertContainsInstanceOf('Pim\Bundle\ProductBundle\Validator\Constraints\Range', $constraints);
        $this->assertConstraintsConfiguration('Pim\Bundle\ProductBundle\Validator\Constraints\Range', $constraints, array(
            'min' => 0,
        ));
    }

    public function testGuessMaxConstraint()
    {
        $constraints = $this->target->guessConstraints($this->getAttributeMock(array(
            'backendType' => AbstractAttributeType::BACKEND_TYPE_INTEGER,
            'numberMax'   => 300,
        )));

        $this->assertContainsInstanceOf('Pim\Bundle\ProductBundle\Validator\Constraints\Range', $constraints);
        $this->assertConstraintsConfiguration('Pim\Bundle\ProductBundle\Validator\Constraints\Range', $constraints, array(
            'max' => 300
        ));
    }

    public function testGuessMinMaxConstraint()
    {
        $constraints = $this->target->guessConstraints($this->getAttributeMock(array(
            'backendType' => AbstractAttributeType::BACKEND_TYPE_INTEGER,
            'numberMin'   => 100,
            'numberMax'   => 300,
        )));

        $this->assertContainsInstanceOf('Pim\Bundle\ProductBundle\Validator\Constraints\Range', $constraints);
        $this->assertConstraintsConfiguration('Pim\Bundle\ProductBundle\Validator\Constraints\Range', $constraints, array(
            'min' => 100,
            'max' => 300
        ));
    }

    public function testDoNotGuessRangeConstraint()
    {
        $constraints = $this->target->guessConstraints($this->getAttributeMock(array(
            'backendType' => AbstractAttributeType::BACKEND_TYPE_INTEGER,
        )));

        $this->assertEquals(0, count($constraints));
    }

    public function testGuessMinDateConstraint()
    {
        $constraints = $this->target->guessConstraints($this->getAttributeMock(array(
            'backendType' => AbstractAttributeType::BACKEND_TYPE_DATE,
            'dateMin'     => '2012-01-01',
        )));

        $this->assertContainsInstanceOf('Pim\Bundle\ProductBundle\Validator\Constraints\Range', $constraints);
        $this->assertConstraintsConfiguration('Pim\Bundle\ProductBundle\Validator\Constraints\Range', $constraints, array(
            'min' => '2012-01-01',
        ));
    }
}