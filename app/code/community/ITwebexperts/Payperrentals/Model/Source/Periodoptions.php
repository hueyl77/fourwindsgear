<?php

class ITwebexperts_Payperrentals_Model_Source_Periodoptions
{

	public function toOptionArray()
	{
		return array(
			array('value' => ITwebexperts_Payperrentals_Model_Product_Periodtype::MINUTES, 'label'=>Mage::helper('payperrentals')->__('Minutes')),
			array('value' => ITwebexperts_Payperrentals_Model_Product_Periodtype::HOURS, 'label'=>Mage::helper('payperrentals')->__('Hours')),
			array('value' => ITwebexperts_Payperrentals_Model_Product_Periodtype::DAYS, 'label'=>Mage::helper('payperrentals')->__('Days')),
			array('value' => ITwebexperts_Payperrentals_Model_Product_Periodtype::WEEKS, 'label'=>Mage::helper('payperrentals')->__('Weeks')),
			array('value' => ITwebexperts_Payperrentals_Model_Product_Periodtype::MONTHS, 'label'=>Mage::helper('payperrentals')->__('Months')),
			array('value' => ITwebexperts_Payperrentals_Model_Product_Periodtype::YEARS, 'label'=>Mage::helper('payperrentals')->__('Years')),
		);
	}

}
