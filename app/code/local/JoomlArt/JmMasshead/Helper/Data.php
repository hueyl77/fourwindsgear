<?php
/*------------------------------------------------------------------------
# $JA#PRODUCT_NAME$ - Version $JA#VERSION$ - Licence Owner $JA#OWNER$
# ------------------------------------------------------------------------
# Copyright (C) 2004-2009 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: J.O.O.M Solutions Co., Ltd
# Websites: http://www.joomlart.com - http://www.joomlancers.com
# This file may not be redistributed in whole or significant part.
-------------------------------------------------------------------------*/

class JoomlArt_JmMasshead_Helper_Data extends Mage_Core_Helper_Abstract {
    protected $defaultStoreCode = null;
    protected $activeStoreCode = null;
    protected $activeWebsiteCode = null;

    public function __construct()
    {
        //initial default store code
        $this->defaultStoreCode = Mage::app()->getWebsite(true)->getDefaultStore()->getCode();


        //initial current store code
        if ($this->isAdmin()) {
            $this->activeStoreCode = Mage::app()->getRequest()->getParam('store', null);
            $this->activeWebsiteCode = Mage::app()->getRequest()->getParam('website', null);
        } else {
            $this->activeStoreCode = Mage::app()->getStore()->getCode();
            $this->activeWebsiteCode = Mage::app()->getWebsite()->getCode();
        }
    }

    function get($var, $attributes, $storeCode = null, $websiteCode = null){
        if(isset($attributes[$var])){
            return $attributes[$var];
        }
        $websiteCode = ($websiteCode) ? $websiteCode : $this->activeWebsiteCode;
        $storeCode = ($storeCode) ? $storeCode : $this->activeStoreCode;
        if ($storeCode){
            $config = Mage::getStoreConfig("joomlart_jmmasshead/joomlart_jmmasshead/$var", $storeCode);
        }
        else if ($websiteCode){
            $config = Mage::app()->getWebsite($websiteCode)->getConfig("joomlart_jmmasshead/joomlart_jmmasshead/$var");
        }else{
            $config = Mage::getStoreConfig("joomlart_jmmasshead/joomlart_jmmasshead/$var");
        }
        return $config;
    }

    public function isAdmin()
    {
        if(Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'adminhtml'){
            return true;
        }

        return false;
    }
}
?>