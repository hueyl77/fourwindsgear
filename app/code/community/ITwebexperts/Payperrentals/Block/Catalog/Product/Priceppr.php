<?php




class ITwebexperts_Payperrentals_Block_Catalog_Product_Priceppr extends Mage_Catalog_Block_Product_Price
{

    public function getPrice(){
        $product = $this->getProduct();
        return ITwebexperts_Payperrentals_Helper_Html::completeListingAndProductInfoWithExtraButtons($product);
    }

    public function getPriceList()
    {
		return ITwebexperts_Payperrentals_Helper_Price::getPriceListHtml($this->getProduct(), Mage::getStoreConfig(
                ITwebexperts_Payperrentals_Helper_Config::XML_PATH_PRICING_ON_LISTING
            ));
	}

    /**
     * Convert block to html string
     *
     * @return string
     */
    protected function _toHtml()
    {
        $product = $this->getProduct();
        $isReservation = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($product->getId(), 'is_reservation');
        $typeId = $product->getTypeId();
        if($this->getTemplate() == $this->getTierPriceTemplate()){
            return parent::_toHtml();
        }
        if($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE || ($isReservation != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_DISABLED && $isReservation !== false)){
            $priceHtml = $this->getLayout()
                ->createBlock("payperrentals/catalog_product_price")
                ->setTemplate('payperrentals/catalog/product/priceppr.phtml')
                ->setProduct($product)
                ->setDisplayMinimalPrice($this->getDisplayMinimalPrice())
                ->setIdSuffix($this->getIdSuffix());
            $priceHtml = $priceHtml->toHtml();

            return $priceHtml;
        }
        return parent::_toHtml();
    }
}
