<?php

class ITwebexperts_Payperrentals_Model_Source_Excludedaysoptions
{

    public function toOptionArray()
    {
        return array(
            /*array('value' => ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::DISABLED, 'label'=>Mage::helper('adminhtml')->__('Disabled')),*/
            array('value' => ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::MONDAY, 'label' => Mage::helper('payperrentals')->__('Monday')),
            array('value' => ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::TUESDAY, 'label' => Mage::helper('payperrentals')->__('Tuesday')),
            array('value' => ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::WEDNESDAY, 'label' => Mage::helper('payperrentals')->__('Wednesday')),
            array('value' => ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::THURSDAY, 'label' => Mage::helper('payperrentals')->__('Thursday')),
            array('value' => ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::FRIDAY, 'label' => Mage::helper('payperrentals')->__('Friday')),
            array('value' => ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::SATURDAY, 'label' => Mage::helper('payperrentals')->__('Saturday')),
            array('value' => ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::SUNDAY, 'label' => Mage::helper('payperrentals')->__('Sunday')),
        );
    }

}