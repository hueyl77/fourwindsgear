<?php


class ITwebexperts_Payperrentals_Block_Bundle_Catalog_Product_View_Type_Bundle_Option extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option{

	public function getSelectionTitlePrice($_selection, $includeContainer = true)
	{
		$price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection, 1);
        $formattedPrice = $this->formatPriceString($price, $includeContainer);
        if($_selection->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE ) {
            if($this->getProduct()->getBundlePricingtype() == ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype::PRICING_BUNDLE_PERPRODUCT){
                $formattedPrice = ITwebexperts_Payperrentals_Helper_Price::getPriceListHtml($_selection, -1, true);
            }else{
                $formattedPrice = '';
            }
        }

		$this->setFormatProduct($_selection);
		$priceTitle = $this->escapeHtml($_selection->getName());
		$priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
					. $formattedPrice
					. ($includeContainer ? '</span>' : '');

		return $priceTitle;
	}
	public function getSelectionQtyTitlePrice($_selection, $includeContainer = true)
	{
		$price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection);
        $formattedPrice = $this->formatPriceString($price, $includeContainer);
        if($_selection->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE ) {
            if($this->getProduct()->getBundlePricingtype() == ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype::PRICING_BUNDLE_PERPRODUCT){
                $formattedPrice = ITwebexperts_Payperrentals_Helper_Price::getPriceListHtml($_selection, -1, true);
            }else{
                $formattedPrice = '';
            }
        }
		$this->setFormatProduct($_selection);
		$priceTitle = $_selection->getSelectionQty()*1 . ' x ' . $this->escapeHtml($_selection->getName());
		$priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
					. '+' . $formattedPrice
					. ($includeContainer ? '</span>' : '');


		return  $priceTitle;
	}
}
