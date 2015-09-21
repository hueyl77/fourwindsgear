<?php
class Wavethemes_Jmbasetheme_Block_Demo_Switcher extends Mage_Core_Block_Template
{
    public function __construct()
    {
        return parent::__construct();
    }
    
    protected function _toHtml()
    {
        //get all styles
        $model = Mage::getSingleton('jmbasetheme/demo');
        if (!$model->hasStyleDefined()){
            return '';
        } else{
            if (!$this->getTemplate()) {
                $this->setTemplate("joomlart/jmbasetheme/switcher.phtml");
            }
        }
        
        $helper = Mage::helper('jmbasetheme');
        $designPackage = $helper->getDesignPackage($this->getCurrentStoreCode(), 'frontend', $this->getCurrentWebsiteCode());
        $styleSrc = Mage::getBaseDir("design") . DS . "frontend" . DS . $designPackage . DS . 'demo' . DS . 'styles' .DS;
        $styles = $helper->listFiles($styleSrc);
        $this->assign('styles', $styles);
        
        return parent::_toHtml();
    }

    public function getCurrentWebsiteCode()
    {
        return Mage::app()->getWebsite()->getCode();
    }
    
    public function getCurrentStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    public function getCurrentStoreCode()
    {
        return Mage::app()->getStore()->getCode();
    }
    
}
