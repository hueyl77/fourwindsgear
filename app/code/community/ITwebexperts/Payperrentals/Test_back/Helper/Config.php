<?php

class ITwebexperts_Payperrentals_Test_Helper_Config extends EcomDev_PHPUnit_Test_Case
{
    /**
     * test getTurnoverConfig for Config Helper
     *
     * @test
     * @loadFixture
     * @dataProvider dataProvider
     */
    public function testGetTurnoverConfig($_testId, $_productId)
    {
        $_product = Mage::getModel('catalog/product')->load($_productId);
        $_expected = $this->expected('testId' . $_testId);
        $_turnoverConfig = Mage::helper('payperrentals/config')->getTurnoverConfig($_product);
        $this->assertEquals($_turnoverConfig['before']['value'], $_expected->getBeforeValue());
        $this->assertEquals($_turnoverConfig['before']['type'], $_expected->getBeforeType());
        $this->assertEquals($_turnoverConfig['after']['value'], $_expected->getAfterValue());
        $this->assertEquals($_turnoverConfig['after']['type'], $_expected->getAfterType());
    }
}