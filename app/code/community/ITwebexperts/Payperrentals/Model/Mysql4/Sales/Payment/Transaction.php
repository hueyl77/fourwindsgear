<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Sales_Payment_Transaction extends Mage_Sales_Model_Resource_Order_Payment_Transaction {
	
	protected function _construct()
    {
        $this->_init('payperrentals/payment_transaction', 'transaction_id');
    }
	
}