<?php

class ITwebexperts_Payperrentals_Model_Reservationorders extends Mage_Core_Model_Abstract{

	const TYPE_QUOTE = "quote";
	const TYPE_ORDER = "order";

	protected $_product;
	
	protected function _construct(){
		$this->_init('payperrentals/reservationorders');
	}
	
	public function getProduct(){
		if(!$this->_product){
			$this->_product = Mage::getModel('catalog/product')->load($this->getProductId());
		}
		return $this->_product;
	}

    /**
     * Updates name & description of sales flat order item table
     * this is used by the manual reserve controller after editing
     * rental dates and product
     *
     * @param $orderitemid
     * @param $productid
     * @throws Exception
     */
    public function updateSalesFlatOrderItem($orderitemid,$productid){
        $sfo = Mage::getModel('sales/order_item')->load($orderitemid);
        $product = Mage::getModel('catalog/product')->load($productid);
        $sfo->setSku($product->getSku());
        $sfo->setName($product->getName());
        $sfo->save();
    }

}
