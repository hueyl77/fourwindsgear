<?php

class ITwebexperts_Payperrentals_Model_Source_Timeincrementoptions
{

	public function toOptionArray()
	{
		return array(
			array('value' => '10', 'label'=>Mage::helper('payperrentals')->__('10 Minutes')),
            		array('value' => '15', 'label'=>Mage::helper('payperrentals')->__('15 Minutes')),
			array('value' => '30', 'label'=>Mage::helper('payperrentals')->__('30 Minutes')),
			array('value' => '60', 'label'=>Mage::helper('payperrentals')->__('60 Minutes')),
		);
	}

}
