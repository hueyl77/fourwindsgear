<?php

class ITwebexperts_Payperrentals_Model_Sales_Payment_Transaction extends Mage_Sales_Model_Order_Payment_Transaction {
	
	protected function _construct()
    {
    	parent::_construct();
        $this->_init('payperrentals/sales_payment_transaction');
        return $this;
    }
    
}