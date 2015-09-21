<?php

class ITwebexperts_Payperrentals_Block_Bundle_Catalog_Product_Priceppr extends Mage_Bundle_Block_Catalog_Product_Price
{

    public function getPriceBundle(){
        $product = $this->getProduct();
        $htmlOutput = '';
        if (ITwebexperts_Payperrentals_Helper_Data::isReservationType($product)) {
            $bundlePriceType = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
                $product->getId(), 'bundle_pricingtype'
            );
            if ($bundlePriceType
                == ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype::PRICING_BUNDLE_FORALL
            ) {
                $htmlOutput = ITwebexperts_Payperrentals_Helper_Price::getPriceListHtml($product, Mage::getStoreConfig(
                    ITwebexperts_Payperrentals_Helper_Config::XML_PATH_PRICING_ON_LISTING
                ));
            } else {
                $htmlOutput = '';
            }
            $htmlOutput
                .= '<input type="hidden" class="ppr_attr_butname" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . Mage::helper('payperrentals')->__(
                    'Rent'
                ) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" />';
        }
        return $htmlOutput;
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
        if (Mage::registry('current_product') == $this->getProduct()) {
            return '<div class="pricingppr">'.$this->getPriceBundle().'</div>'.parent::_toHtml();
        } else {
            $product = $this->getProduct();
            $_isReservation = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
                $product->getId(), 'is_reservation'
            );
            if ($_isReservation != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_DISABLED && $_isReservation !== false) {
                $priceHtml = $this->getLayout()
                    ->createBlock("payperrentals/bundle_catalog_product_price")
                    ->setTemplate('payperrentals/bundle/catalog/product/priceppr.phtml')
                    ->setProduct($product)
                    ->setPriceElementIdPrefix('bundle-price-')
                    ->setIdSuffix($this->getIdSuffix())
                    ->toHtml();

                return $priceHtml;
            }
            return parent::_toHtml();
        }
    }

}
