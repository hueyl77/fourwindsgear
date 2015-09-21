<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Order_Info
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Order_Paypaldeposit
    extends Mage_Adminhtml_Block_Sales_Order_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface{

	protected $_pprCollection;	
		
    /**
     * @return mixed
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('payperrentals')->__('Paypal Deposit');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('payperrentals')->__('Reservation Deposit Information');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden(){
		// Show only if there are reserved items in order
        /** @var $order Mage_Sales_Model_Order */
        $order = $this->getOrder();
        if($order->getPayment()->getMethod() == 'paypal_express' && !(!$this->getItemsCollection()->getSize() || !$this->getOrder()->getDepositpprAmount())){
            return false;
        }else{
            return true;
        }

	}
	
	/**
     * @return mixed
     */
    public function getPprItemsCollection(){
		if(!$this->_pprCollection){
			$id = $this->getRequest()->getParam('order_id');
			$order = $this->getOrder();
			$this->_pprCollection = Mage::getModel('payperrentals/sales_payment_transaction')->getCollection()->addFieldToFilter('order_id', array('eq' => $order->getId()))->addPaymentInformation(array('method'))->load();
		}
		return $this->_pprCollection;
	}


	/**
     * @return mixed
     */
    public function getItemsCollection(){
		if(!$this->_collection){
			$id = $this->getRequest()->getParam('order_id');
			$order = $this->getOrder();
			$this->_collection = Mage::getModel('sales/order_payment_transaction')->getCollection()->addFieldToFilter('order_id', array('eq' => $order->getId()))->addPaymentInformation(array('method'))->load();
		}
		return $this->_collection;
	}
	
	
    /**
     * @param $id
     * @return mixed
     */
    public function getProductName($id){
		$_product = Mage::getModel('catalog/product')->load($id);
		return $_product->getName();
	}

    /**
     * @param $id
     * @return mixed
     */
    public function getProductSKU($id){
		$_product = Mage::getModel('catalog/product')->load($id);
		return $_product->getSKU();
	}
	
	public function getCollection(){
		return $this->getItemsCollection();
	}

}
