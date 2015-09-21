<?php

class ITwebexperts_Payperrentals_Model_Source_GlobalExclude
{
    public function toOptionArray()
    {
        return array(
            array('label' => Mage::helper('payperrentals')->__('None'), 'value' => 'none'),
            array('label' => Mage::helper('payperrentals')->__('Day of Week'), 'value' => 'dayweek'),
            array('label' => Mage::helper('payperrentals')->__('Daily'), 'value' => 'daily'),
            array('label' => Mage::helper('payperrentals')->__('Monthly'), 'value' => 'monthly'),
            array('label' => Mage::helper('payperrentals')->__('Yearly'), 'value' => 'yearly')
        );
    }
}