<?php

class ITwebexperts_Payperrentals_Model_Source_Excludedaysfrom
{
    const CALENDAR = 0;
    const PRICE = 1;
    const BOTH = 2;

    public function toOptionArray()
    {
        return array(
            array('value' => self::CALENDAR, 'label' => Mage::helper('payperrentals')->__('Calendar')),
            array('value' => self::PRICE, 'label' => Mage::helper('payperrentals')->__('Price')),
            array('value' => self::BOTH, 'label' => Mage::helper('payperrentals')->__('Both')),
        );
    }

}