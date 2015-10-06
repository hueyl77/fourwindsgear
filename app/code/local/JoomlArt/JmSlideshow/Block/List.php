<?php

/* ------------------------------------------------------------------------
  # $JA#PRODUCT_NAME$ - Version $JA#VERSION$ - Licence Owner $JA#OWNER$
  # ------------------------------------------------------------------------
  # Copyright (C) 2004-2009 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
  # @license - Copyrighted Commercial Software
  # Author: J.O.O.M Solutions Co., Ltd
  # Websites: http://www.joomlart.com - http://www.joomlancers.com
  # This file may not be redistributed in whole or significant part.
  ------------------------------------------------------------------------- */

class JoomlArt_JmSlideshow_Block_List extends Mage_Catalog_Block_Product_Abstract {

    const CACHE_TAG = 'jm_slideshow';
    
    protected $_configs = array();
    protected $cacheLifeTime = 86400; //1day

    public function __construct($attributes = array()) {
        $helper = Mage::helper('joomlart_jmslideshow/data');

        //Handle configs value from configs or static block call line.
        foreach ($helper::$_params as $paramKey) {
            $this->_configs[$paramKey] = $helper->get($paramKey, $attributes);
        }

        //Check enabled sideshow
        if (!$this->_configs['show']){
            return;
        }
        
        parent::__construct();
        
        //get cache status
        $cacheType = 'joomlart_jmslideshow';
        $useCache = Mage::app()->useCache($cacheType);
        if ($useCache){
            //set block cache
            $this->addData(array(
                'cache_key' => $this->getCacheId($attributes),
                'cache_lifetime' => $this->cacheLifeTime
            ));
        }
    }

    protected function _beforeToHtml() {
        if (!$this->_configs['show']){
            return;
        }

        //Handle data from call line in xml (custom design)
        foreach ($this->_configs as $key => $value) {
            $data = $this->getData($key);
            if ($data) {
                $this->_configs[$key] = $data;
            }
        }

        $detect = Mage::helper('joomlart_jmslideshow/mobiledetect');

        if ($detect->isTablet()) {
            if ($this->_configs["mainWidthtablet"])
                $this->_configs["mainWidth"] = $this->_configs["mainWidthtablet"];
        }
        else if ($detect->isMobile()) {
            if ($this->_configs["mainWidthmobile"])
                $this->_configs["mainWidth"] = $this->_configs["mainWidthmobile"];
        }

        return parent::_beforeToHtml();
    }

    /**
     * Rendering block content
     *
     * @return string
     */
    protected function _toHtml() {
        if (!$this->_configs['show']){
            return;
        }

        //Check the animation used?
        if ($this->_configs['animation'] == 'vrtaccordion' || $this->_configs['animation'] == 'hrzaccordion') {
            $this->_configs['template'] = 'joomlart/jmslideshow/accordion.phtml';
        } else {
            $this->_configs['template'] = 'joomlart/jmslideshow/basic.phtml';
        }
        $this->setTemplate($this->_configs['template']);
        
        //get cache status
        $cacheType = 'joomlart_jmslideshow';
        $useCache = Mage::app()->useCache($cacheType);

        $cacheId = $this->getCacheId();
        $cacheContent = Mage::app()->loadCache($cacheId);
        if ($useCache && $cacheContent){
            $slideData = unserialize($cacheContent);
        }else{
            //Get slide data
            $slideData = Mage::helper('joomlart_jmslideshow/data')->getSlideShowData($this->_configs);
            if ($useCache){
                try{
                    $tags = array(self::CACHE_TAG);
                    $lifeTime = (Mage::getStoreConfig('core/cache/lifetime')) ? Mage::getStoreConfig('core/cache/lifetime') : $this->cacheLifeTime;
                    $cacheContent = serialize($slideData);
                    Mage::app()->saveCache($cacheContent, $cacheId, $tags, $lifeTime);

                } catch(Exception $e){
                    Mage::logException($e);
                }
            }
        }

        //Assign data to template
        $this->assign('config', $this->_configs);
        $this->assign('items', $slideData['items']);
        $this->assign('titles', $slideData['titles']);
        $this->assign('urls', $slideData['urls']);
        $this->assign('targets', $slideData['targets']);

        return parent::_toHtml();
    }
    
    public function getCacheId($attributes = array()){
        $strConfigs = implode('_', $this->_configs);
        $cacheIds = array(
            'jmslideshow',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            serialize($this->getRequest()->getParams()),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            serialize($attributes),
            $strConfigs,
        );
        
        return md5(implode('|', $cacheIds));
    }
}
