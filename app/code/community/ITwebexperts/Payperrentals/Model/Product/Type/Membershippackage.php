<?php


class ITwebexperts_Payperrentals_Model_Product_Type_Membershippackage extends Mage_Catalog_Model_Product_Type_Abstract {

    protected $_isDuplicable = false;
    protected $_product;

	public function prepareForCartAdvanced(Varien_Object $buyRequest, $product = null, $processMode = null){
		if(!$product) $product = $this->getProduct();

		return parent::prepareForCartAdvanced($buyRequest, $product);
	}

	public function prepareForCart(Varien_Object $buyRequest, $product = null) {
		if(!$product) $product = $this->getProduct();

		return parent::prepareForCart($buyRequest, $product);
	}

	public function isVirtual($product = null) {
		return true;
	}

}
