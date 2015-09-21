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

class Wavethemes_Jmmegamenu_Block_Jmmegamenu extends Mage_Page_Block_Html_Topmenu {

    const CACHE_TAG = 'jm_megamenu';
    
    protected $megaHelper = null;
    protected $cacheLifeTime = 86400; //1days

    public function __construct($attributes = array()) {
        parent::__construct();

        if (!Mage::helper('jmmegamenu')->get('show', $attributes)) {
            return true;
        }
        
        //check cache status
        $cacheType = 'wavethemes_jmmegamenu';
        $useCache = Mage::app()->useCache($cacheType);
        
        //on/off block html cache
        if (!$useCache){
            //off default block cached
            $this->addData(array(
                'cache_lifetime' => null,
            ));
        }else{
            $this->addData(array(
                'cache_key' => $this->getCacheId($attributes),
                'cache_lifetime' => $this->cacheLifeTime
            ));
        }
        
        $this->megaHelper = Mage::helper('jmmegamenu/megaClass');

        $params = array(
            'animation' => Mage::helper('jmmegamenu')->get('animation', $attributes),
            'addition_class' => ''
        );
        $params = array_merge($params, $attributes);

        $this->megaHelper->setParams($params);
    }

    protected function _toHtml() {
        if (!Mage::helper('jmmegamenu')->get('show')) {
            return parent::_toHtml();
        }

        //bind data vars if has
        $attributes = get_object_vars($this->megaHelper->params);
        foreach ($attributes as $key => $value) {
            $data = $this->getData($key);
            if ($data) {
                $attributes[$key] = $data;
            }
        }
        $this->megaHelper->setParams($attributes);

        //set template
        $this->setTemplate("joomlart/jmmegamenu/output.phtml");

        $menuKey = (isset($this->megaHelper->params->menu_key) && $this->megaHelper->params->menu_key ) ? $this->megaHelper->params->menu_key : null;
        
        //get cache status
        $cacheType = 'wavethemes_jmmegamenu';
        $useCache = Mage::app()->useCache($cacheType);

        //get menu group id
        $cacheId = $this->getMenuGroupCacheId();
        $cacheContent = Mage::app()->loadCache($cacheId);
        if ($useCache && $cacheContent){
            $menuGroupId = $cacheContent;
        } else {
            $menuGroupId = Mage::helper('jmmegamenu')->getMenuId($menuKey);

            if ($useCache){
                try{
                    $tags = array(self::CACHE_TAG);
                    $lifeTime = (Mage::getStoreConfig('core/cache/lifetime')) ? Mage::getStoreConfig('core/cache/lifetime') : $this->cacheLifeTime;
                    Mage::app()->saveCache($menuGroupId, $cacheId, $tags, $lifeTime);
                } catch(Exception $e){
                    Mage::logException($e);
                }
            }
        }
        
        $this->megaHelper->setParams(array(
            'menu_group_id' => $menuGroupId
        ));

        if ($menuGroupId) {
            $cacheId = md5("jmmegamenu_{$menuGroupId}_html_items");
            $cacheContent = Mage::app()->loadCache($cacheId);
            if ($useCache && $cacheContent){
                $output = unserialize($cacheContent);
            }else{
                $output = $this->_generateMenuHtml($menuGroupId);
                if ($useCache){
                    try{
                        $tags = array(self::CACHE_TAG);
                        $lifeTime = (Mage::getStoreConfig('core/cache/lifetime')) ? Mage::getStoreConfig('core/cache/lifetime') : $this->cacheLifeTime;
                        $cacheContent = serialize($output);
                        Mage::app()->saveCache($cacheContent, $cacheId, $tags, $lifeTime);
                    } catch(Exception $e){
                        Mage::logException($e);
                    }
                }
            }
            
            $this->assign('menuoutput', $output);
        } else {
            echo Mage::helper('jmmegamenu')->__('There are not menu items found.');
        }

        return parent::_toHtml();
    }
    
    public function getMenuGroupCacheId(){
        $menuGroupCacheId = array(
            'JMMEGAMENU',
            $this->megaHelper->params->menu_key,
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
        );
        
        return md5(implode('|', $menuGroupCacheId));
    }
    
    protected function _generateMenuHtml($menuGroupId){
        //get menu items
        $collections = Mage::getModel('jmmegamenu/jmmegamenu')->getCollection()->setOrder("parent", "ASC")->setOrder("ordering", "ASC")->addFilter("status", 1, "eq")->addFilter("menugroup", $menuGroupId);
        //built menu tree
        $tree = array();
        foreach ($collections as $collection) {
            $collection->tree = array();
            $parent_tree = array();
            if (isset($tree[$collection->parent])) {
                $parent_tree = $tree[$collection->parent];
            }
            //Create tree
            array_push($parent_tree, $collection->menu_id);
            $tree[$collection->menu_id] = $parent_tree;

            $collection->tree = $parent_tree;
        }
        $this->megaHelper->getList($collections);

        //render html of menu
        ob_start();
        $this->megaHelper->genMenu();
        $output = ob_get_contents();
        ob_end_clean();
        
        return $output;
    }
    
    public function getCacheId($attributes = array()){
        $cacheIds = array(
            'block_jmmegamenu',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            serialize($this->getRequest()->getParams()),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            serialize($attributes)
        );
        
        return md5(implode('|', $cacheIds));
    }

}
