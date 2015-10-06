<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Order_Info
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Order_Info
    extends Mage_Adminhtml_Block_Sales_Order_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface{

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
        return Mage::helper('payperrentals')->__('Reservations');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('payperrentals')->__('Reservation Information');
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
		return !$this->getItemsCollection()->getSize();
	}

    /**
     * @return mixed
     */
    public function getItemsCollection(){
		if(!$this->_collection){
			$order = $this->getOrder();
			$this->_collection = Mage::getModel('payperrentals/reservationorders')->getCollection()->addOrderIdFilter($order->getId())->load();
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

    /**
     * @return mixed
     */
    public function getCollection(){
		return $this->getItemsCollection();
	}


}
