<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Sales_Payment_Transaction_Collection extends Mage_Sales_Model_Resource_Order_Payment_Transaction_Collection {
	
	protected function _construct()
    {
    	parent::_construct();
        $this->_init('payperrentals/sales_payment_transaction');
        return $this;
    }
	
}