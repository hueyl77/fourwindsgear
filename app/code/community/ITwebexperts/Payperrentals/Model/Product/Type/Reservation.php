<?php




class ITwebexperts_Payperrentals_Model_Product_Type_Reservation extends Mage_Catalog_Model_Product_Type_Abstract
{

    protected $_isDuplicable = false;
    protected $_product;

    const START_DATE_OPTION = 'start_date';
    const END_DATE_OPTION = 'end_date';
    const ORIGINAL_START_DATE_OPTION = 'original_start_date';
    const EXCLUDE_PRICE_OPTION = 'exclude_price';
    const BUYOUT_PRICE_OPTION = 'buyout_price';
    const NO_AVAIL_CHECK_OPTION = 'no_avail_check';
    const NON_SEQUENTIAL = 'non_sequential';
    const BUYOUT_MIN_SECONDS = 14552000; // 5 months + 1 second

    public function prepareForCartAdvanced(Varien_Object $buyRequest, $product = null, $processMode = null)
    {
        if (!$product) $product = $this->getProduct();

        Mage::dispatchEvent('prepare_buy_request_cart_advanced', array('buy_request' => $buyRequest, 'product' => $product));

        $prepare = Mage::helper('payperrentals/rendercart')->prepareForCartAdvanced($buyRequest, $product, $processMode, 'simple');
        if($prepare == 'call_parent'){
            return parent::prepareForCartAdvanced($buyRequest, $product, $processMode);
        } else {
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

    /**
     * Check is product available for sale
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isSalable($product = null)
    {
        $salable = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($this->getProduct($product)->getId(), 'status') == Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
        return $salable;
    }


    public function canConfigure($product = null)
    {
        return true;
    }

    public function isVirtual($product = null)
    {
        $hasShipping = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($this->getProduct($product)->getId(),'payperrentals_has_shipping');
        return ((ITwebexperts_Payperrentals_Helper_Config::removeShipping()) || ($hasShipping == ITwebexperts_Payperrentals_Model_Product_Hasshipping::STATUS_DISABLED));
    }
}
