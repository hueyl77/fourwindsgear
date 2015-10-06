<?php


class ITwebexperts_Payperrentals_Block_Catalog_Product_Price extends Mage_Catalog_Block_Product_Price
{

    /**
     * @return string
     */
    public function getPrice(){
        $product = $this->getProduct();
        return  ITwebexperts_Payperrentals_Helper_Html::completeListingAndProductInfoWithExtraButtons($product);
    }
}
