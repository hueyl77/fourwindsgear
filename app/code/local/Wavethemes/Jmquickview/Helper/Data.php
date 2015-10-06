<?php
class Wavethemes_Jmquickview_Helper_Data extends Mage_Core_Helper_Abstract
{
    var $defaultStoreCode = null;
    var $activeStoreCode = null;
    var $activeWebsiteCode = null;

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

    public function get($var, $attributes, $storeCode = null, $websiteCode = null)
    {
        $value = null;
        if(isset($attributes[$var])){
            $value = $attributes[$var];
        }else{
            $websiteCode = ($websiteCode) ? $websiteCode : $this->activeWebsiteCode;
            $storeCode = ($storeCode) ? $storeCode : $this->activeStoreCode;
            if ($storeCode){
                $configGroups = Mage::getStoreConfig("wavethemes_jmquickview", $storeCode);
            }
            else if ($websiteCode){
                $configGroups = Mage::app()->getWebsite($websiteCode)->getConfig('wavethemes_jmquickview');
            }else{
                $configGroups = Mage::getStoreConfig("wavethemes_jmquickview",$this->defaultStoreCode);
            }
            foreach($configGroups as $configs){
                if(isset($configs[$var])){
                    $value = $configs[$var];
                    break;
                }
            }
        }

        return $value;
    }

    public function isAdmin()
    {
        if(Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'adminhtml'){
            return true;
        }

        return false;
    }
}
	 