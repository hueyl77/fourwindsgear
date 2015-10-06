<?php

class ITwebexperts_Payperrentals_Block_Catalog_Product_View_Type_Simple extends Mage_Catalog_Block_Product_View_Type_Simple
{
    public function __construct()
    {
        parent::__construct();
        if (preg_match('/^(1.(3.[0-9].[0-9]|4.[0-1].0]))/', Mage::getVersion()))
            $this->setTemplate('catalog/product/view/type/simple.phtml');
        else
            $this->setTemplate('catalog/product/view/type/default.phtml');
    }
}
