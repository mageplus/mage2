<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Eav\Model\Attribute\Data;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Model\Attribute\Data\Select
     */
    protected $model;

    protected function setUp()
    {
        $timezoneMock = $this->getMock('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $loggerMock = $this->getMock('\Magento\Framework\Logger', [], [], '', false);
        $localeResolverMock = $this->getMock('\Magento\Framework\Locale\ResolverInterface');

        $this->model = new Select($timezoneMock, $loggerMock, $localeResolverMock);
    }

    /**
     * @covers \Magento\Eav\Model\Attribute\Data\Select::outputValue
     *
     * @param string $format
     * @param mixed $value
     * @param mixed $expectedResult
     * @dataProvider outputValueDataProvider
     */
    public function testOutputValue($format, $value, $expectedResult)
    {
        $entityMock = $this->getMock('\Magento\Framework\Model\AbstractModel', [], [], '', false);
        $entityMock->expects($this->once())->method('getData')->will($this->returnValue($value));

        $sourceMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Source\AbstractSource', [], [], '', false);
        $sourceMock->expects($this->any())->method('getOptionText')->will($this->returnValue(123));

        $attributeMock = $this->getMock('\Magento\Eav\Model\Attribute', [], [], '', false);
        $attributeMock->expects($this->any())->method('getSource')->will($this->returnValue($sourceMock));

        $this->model->setEntity($entityMock);
        $this->model->setAttribute($attributeMock);
        $this->assertEquals($expectedResult, $this->model->outputValue($format));
    }

    /**
     * @return array
     */
    public function outputValueDataProvider()
    {
        return [
            [
                'format' => \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_JSON,
                'value' => 'value',
                'expectedResult' => 'value'
            ],
            [
                'format' => \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_TEXT,
                'value' => '',
                'expectedResult' => ''
            ],
            [
                'format' => \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_TEXT,
                'value' => 'value',
                'expectedResult' => '123'
            ],
        ];
    }

    /**
     * @covers \Magento\Eav\Model\Attribute\Data\Select::validateValue
     *
     * @param mixed $value
     * @param mixed $originalValue
     * @param bool $isRequired
     * @param array $expectedResult
     * @dataProvider validateValueDataProvider
     */
    public function testValidateValue($value, $originalValue, $isRequired, $expectedResult)
    {
        $entityMock = $this->getMock('\Magento\Framework\Model\AbstractModel', [], [], '', false);
        $entityMock->expects($this->any())->method('getData')->will($this->returnValue($originalValue));

        $attributeMock = $this->getMock('\Magento\Eav\Model\Attribute', [], [], '', false);
        $attributeMock->expects($this->any())->method('getStoreLabel')->will($this->returnValue('Label'));
        $attributeMock->expects($this->any())->method('getIsRequired')->will($this->returnValue($isRequired));

        $this->model->setEntity($entityMock);
        $this->model->setAttribute($attributeMock);
        $this->assertEquals($expectedResult, $this->model->validateValue($value));
    }

    /**
     * @return array
     */
    public function validateValueDataProvider()
    {
        return [
            [
                'value' => false,
                'originalValue' => 'value',
                'isRequired' => false,
                'expectedResult' => true,
            ],
            [
                'value' => false,
                'originalValue' => null,
                'isRequired' => true,
                'expectedResult' => ['"Label" is a required value.'],
            ],
            [
                'value' => false,
                'originalValue' => null,
                'isRequired' => false,
                'expectedResult' => true,
            ],
            [
                'value' => false,
                'originalValue' => '0',
                'isRequired' => true,
                'expectedResult' => true,
            ],
            [
                'value' => 'value',
                'originalValue' => '',
                'isRequired' => true,
                'expectedResult' => true,
            ]
        ];
    }

    /**
     * @covers \Magento\Eav\Model\Attribute\Data\Select::compactValue
     */
    public function testCompactValue()
    {
        $entityMock = $this->getMock('\Magento\Framework\Model\AbstractModel', [], [], '', false);
        $entityMock->expects($this->once())->method('setData')->with('attrCode', 'value');

        $attributeMock = $this->getMock('\Magento\Eav\Model\Attribute', [], [], '', false);
        $attributeMock->expects($this->any())->method('getAttributeCode')->will($this->returnValue('attrCode'));

        $this->model->setAttribute($attributeMock);
        $this->model->setEntity($entityMock);
        $this->model->compactValue('value');
    }

    /**
     * @covers \Magento\Eav\Model\Attribute\Data\Select::compactValue
     */
    public function testCompactValueWithFalseValue()
    {
        $entityMock = $this->getMock('\Magento\Framework\Model\AbstractModel', [], [], '', false);
        $entityMock->expects($this->never())->method('setData');

        $this->model->setEntity($entityMock);
        $this->model->compactValue(false);
    }
}
