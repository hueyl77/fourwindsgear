<?php
class ITwebexperts_Payperrentals_Model_Sales_Order_Payment extends Mage_Sales_Model_Order_Payment {
	
	protected function _lookupTransaction($txnId, $txnType = false)
    {
        if (!$txnId) {
            if ($txnType && $this->getId()) {
                $collection = Mage::getModel('payperrentals/sales_payment_transaction')->getCollection()
                    ->setOrderFilter($this->getOrder())
                    ->addPaymentIdFilter($this->getId())
                    ->addTxnTypeFilter($txnType)
                    ->setOrder('created_at', Varien_Data_Collection::SORT_ORDER_DESC)
                    ->setOrder('transaction_id', Varien_Data_Collection::SORT_ORDER_DESC);
                foreach ($collection as $txn) {
                    $txn->setOrderPaymentObject($this);
                    $this->_transactionsLookup[$txn->getTxnId()] = $txn;
                    return $txn;
                }
            }
            return false;
        }
        if (isset($this->_transactionsLookup[$txnId])) {
            return $this->_transactionsLookup[$txnId];
        }
        $txn = Mage::getModel('payperrentals/sales_payment_transaction')
            ->setOrderPaymentObject($this)
            ->loadByTxnId($txnId);
        if ($txn->getId()) {
            $this->_transactionsLookup[$txnId] = $txn;
        } else {
            $this->_transactionsLookup[$txnId] = false;
        }
        return $this->_transactionsLookup[$txnId];
    }
	
}