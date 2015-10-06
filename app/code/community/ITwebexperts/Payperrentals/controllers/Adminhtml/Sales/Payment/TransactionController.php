<?php
class ITwebexperts_Payperrentals_Adminhtml_Sales_Payment_TransactionController extends Mage_Adminhtml_Controller_Action {
    protected function _isAllowed(){
        return true;
    }

	public function captureAction()
	{
		try {
			if ($id = $this->getRequest()->getParam('id')) {
				$transaction = Mage::getModel('payperrentals/sales_payment_transaction')->load($id);
				if ($transaction->getId()) {
					$payment = Mage::getModel('payperrentals/sales_order_payment')->load($transaction->getPaymentId());
					if ($payment->canCapture()) {
						$order = Mage::getModel('sales/order')->load($transaction->getOrderId());
						$payment->setOrder($order);
						$methodInstance = $payment->getMethodInstance();
						$methodInstance->capture($payment, $payment->getAmountOrdered());
						if ($payment->getCreatedTransaction()) {
							$captureCransaction = Mage::getModel('payperrentals/sales_payment_transaction')->setData($payment->getCreatedTransaction()->getData());
							$captureCransaction->setOrderPaymentObject($payment);
							$captureCransaction->save();
						}
						$transaction->setOrderPaymentObject($payment);
						$transaction->setIsClosed(1);
						$transaction->save();
						$this->_getSession()->addSuccess($this->__('Deposit captured succesfully.'));
					} else {
						$this->_getSession()->addError($this->__('Payment method does not allow online capture.'));
					}
				} else {
					$this->_getSession()->addError($this->__('Wrong transaction ID.'));
				}
			}
		} catch (Exception $e) {
			$this->_getSession()->addError($this->__($e->getMessage()));
		}
		
		$this->_redirectReferer();
	}
	
	public function capturePaypalAction()
	{
		try {
			if ($id = $this->getRequest()->getParam('id')) {
				$transaction = Mage::getModel('sales/order_payment_transaction')->load($id);
				if ($transaction->getId()) {
					$payment = Mage::getModel('sales/order_payment')->load($transaction->getPaymentId());

					$order = Mage::getModel('sales/order')->load($transaction->getOrderId());
					$payment->setOrder($order);
					$payment->setParentTransactionId($transaction->getTxnId());
					$methodInstance = $payment->getMethodInstance();
					
					$methodInstance->capture($payment, $order->getDepositpprAmount());
					if ($payment->getCreatedTransaction()) {
						$captureCransaction = Mage::getModel('payperrentals/sales_payment_transaction')->setData($payment->getCreatedTransaction()->getData());
						$captureCransaction->setOrderPaymentObject($payment);
						$captureCransaction->save();
					}
					$transaction->setOrderPaymentObject($payment);
					$transaction->setIsClosed(1);
					$transaction->save();
					$this->_getSession()->addSuccess($this->__('Deposit captured succesfully.'));

				} else {
					$this->_getSession()->addError($this->__('Wrong transaction ID.'));
				}
			}
		} catch (Exception $e) {
			$this->_getSession()->addError($this->__($e->getMessage()));
		}
		
		$this->_redirectReferer();
	}
	
}