<?php

class ITwebexperts_Payperrentals_Model_Sendreturn extends Mage_Core_Model_Abstract{
	
	protected function _construct(){
		$this->_init('payperrentals/sendreturn');
	}

    public function _getShippingStates()
    {
        return array(
            '0' => Mage::helper('payperrentals')->__('Not Shipped'),
            '1' => Mage::helper('payperrentals')->__('Partially Shipped'),
            '2' => Mage::helper('payperrentals')->__('Shipped')
        );
    }

    public function _getReturnStates()
    {
        return array(
            '0' => Mage::helper('payperrentals')->__('Not Returned'),
            '1' => Mage::helper('payperrentals')->__('Partially Returned'),
            '2' => Mage::helper('payperrentals')->__('Returned')
        );
    }
}
