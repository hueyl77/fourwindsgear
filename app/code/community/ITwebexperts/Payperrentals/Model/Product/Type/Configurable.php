<?php




class ITwebexperts_Payperrentals_Model_Product_Type_Configurable extends Mage_Catalog_Model_Product_Type_Configurable {

	public function prepareForCartAdvanced(Varien_Object $buyRequest, $product = null, $processMode = null){
		if(!$product) $product = $this->getProduct();
        $prepare = Mage::helper('payperrentals/rendercart')->prepareForCartAdvanced($buyRequest, $product, $processMode, 'configurable');
        if($prepare == 'call_parent'){
            return parent::prepareForCartAdvanced($buyRequest, $product, $processMode);
        }else{
            return $prepare;
        }
	}

	public function hasRequiredOptions($product = null)
	{
        if(ITwebexperts_Payperrentals_Helper_Data::isReservationAndRental($this->getProduct($product))){
            return false;
        }else{
            return parent::hasRequiredOptions($product);
        }
	}

	public function isVirtual($product = null)
    {
        $hasShipping = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($this->getProduct($product)->getId(),'payperrentals_has_shipping');
        $isReservation = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($this->getProduct($product)->getId(),'is_reservation');
        if($isReservation != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_DISABLED && $isReservation != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_NOTSET) {
            return ((ITwebexperts_Payperrentals_Helper_Config::removeShipping())
                || ($hasShipping
                    == ITwebexperts_Payperrentals_Model_Product_Hasshipping::STATUS_DISABLED));
        }else{
            return parent::isVirtual($product);
        }
	}
}
