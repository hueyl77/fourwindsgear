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

class JoomlArt_JmProducts_Block_Filter extends Mage_Catalog_Block_Product_Abstract {

    protected $_config = array();
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
    protected $_productCollection;
    protected $cacheLifeTime = 86400; //1day
    
    const CACHE_TAG = 'jm_products';

    public function __construct($attributes = array()) {
        $helper = Mage::helper('joomlart_jmproducts/data');

        $this->_config ['show'] = $helper->get('show', $attributes);
        if (!$this->_config ['show'])
            return;

        $this->_config ['template'] = $helper->get('template', $attributes);
        if (!$this->_config ['template'])
            return;

        parent::__construct();
        
        //get cache status
        $cacheType = 'joomlart_jmproducts';
        $useCache = Mage::app()->useCache($cacheType);
        if ($useCache){
            $this->addData(array(
                'cache_key' => $this->getCacheId($attributes),
                'cache_lifetime' => $this->cacheLifeTime
            ));
        }

        $this->_config ['mode'] = $helper->get('mode', $attributes);
        $this->_config ['title'] = $helper->get('title', $attributes);
        $this->_config ['catsid'] = $helper->get('catsid', $attributes);
        $this->_config ['productsid'] = $helper->get('productsid', $attributes);
        $this->_config ['qty'] = $helper->get('quanlity', $attributes);

        $this->_config['qtyperpage'] = $helper->get('quanlityperpage', $attributes);
        if (!$this->_config['qtyperpage']){
            $this->_config['qtyperpage'] = 10;
        }

        $this->_config ['perrow'] = $helper->get('perrow', $attributes);
        $this->_config['width'] = $helper->get('width', $attributes);
        $this->_config['height'] = $helper->get('height', $attributes);
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml() {
        //assign some settings from block attributes
        if ($this->getData("quanlity")) {
            $this->_config ['qty'] = $this->getData("quanlity");
        }
        if ($this->getData("perrow")) {
            $this->_config ['perrow'] = $this->getData("perrow");
        }
        if ($this->getData("mode")) {
            $this->_config ['mode'] = $this->getData("mode");
        }

        $collection = $this->getListProducts();

        $toolbar = $this->getToolbarBlock();

        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        $toolbar->addPagerLimit("grid", $this->_config['qtyperpage']);
        $toolbar->addPagerLimit("list", $this->_config['qtyperpage']);

        // set collection to toolbar and apply sort
        if (is_object($collection)) {
            $toolbar->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
            Mage::dispatchEvent('catalog_block_product_list_collection', array(
                'collection' => $collection
            ));
        }

        return parent::_beforeToHtml();
    }

    public function _toHtml() {
        if (!$this->_config ['show'])
            return;

        $this->_config ['title'] = $this->gettitle();

        if (!$this->_productCollection)
            $listall = $this->getListProducts();
        else
            $listall = $this->_productCollection;

        $this->assign('listall', $listall);
        $this->assign('config', $this->_config);

        if (!$this->getTemplate()) {
            $this->setTemplate('joomlart/jmproducts/viewall.phtml');
        }
        if ($listall && $listall->count() > 0) {
            Mage::getModel('review/review')->appendSummary($listall);
        }

        return parent::_toHtml();
    }

    public function getListProducts() {
        $this->_productCollection = $this->getListFilterProducts();
        return $this->_productCollection;
    }

    public function getListFilterProducts($pageSize = null, $currentPage = 1) {
        $limit = (int) $this->_config['qty'];

        if (!$pageSize)
            $pageSize = (int) $this->_config ['qtyperpage'];

        $params = $this->getRequest()->getParams();

        if ($params["p"]) {
            $currentPage = $params["p"];
            unset($params["p"]);
        }
        if ($params["mode"]) {
            unset($params["mode"]);
        }
        unset($params["filter"]);

        if ($params["cat"]) {
            $this->_config["catsid"] = $params["cat"];
            unset($params["cat"]);
        }

        if ($params["price"]) {
            $prices = explode("-", $params["price"]);
            $low_price = $prices[0];
            if ($prices[1]) {
                $high_price = $prices[1];
            }
            unset($params["price"]);
        }

        unset($params["order"]);
        unset($params["dir"]);
        unset($params["___store"]);
        unset($params["limit"]);

        $storeId = Mage::app()->getStore()->getId();
        $products = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId($storeId)
            ->addStoreFilter($storeId);
        $this->_addProductAttributesAndPrices($products);

        if ($this->_config['catsid']) {
            $this->addCategoryIdsFilter($products, $this->_config['catsid']);
        }

        if ($low_price && $high_price) {
            $products->addFieldToFilter('price', array('gteq' => $low_price));
            $products->addFieldToFilter('price', array('lteq' => $high_price));
        } elseif ($high_price) {
            $products->addAttributeToFilter('price', array('lteq' => $high_price));
        } elseif ($low_price) {
            $products->addAttributeToFilter('price', array('gteq' => $low_price));
        }

        if ($params) {
            foreach ($params as $kparam => $vparam) {
                $products->addAttributeToFilter($kparam, array('in' => array(0 => (int) $vparam)));
            }
        }

        //Only get enabled products
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);

        //Check in/out stock
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);
        }

        $products->setMaxSize($limit);
        $products->setPageSize($pageSize)->setCurPage($currentPage);

        $this->setProductCollection($products);

        return $products;
    }

    /**
     * Add condition to filter by category
     *
     * @param $collection
     * @param null $categoryIds
     */
    public function addCategoryIdsFilter(&$collection, $categoryIds = null, $matchType = 'OR') {
        $categoryIds = explode(",", $categoryIds);
        if ($categoryIds) {
            if (sizeof($categoryIds) == 1 || $matchType == 'AND') {
                foreach ($categoryIds as $catId) {
                    $categoryModel = Mage::getModel('catalog/category')->load($catId);
                    $collection->addCategoryFilter($categoryModel); //category filter
                }
            } else if ($matchType == 'OR') {
                $ctf = array();
                foreach ($categoryIds as $catId) {
                    $ctf[]['finset'] = $catId;
                }
                if ($ctf) {
                    $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                        ->addAttributeToFilter('category_id', array($ctf));
                    //->addAttributeToFilter('category_id',array($ctf))->groupByAttribute('entity_id');
                }
            }
        }
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock() {
        if ($blockName = $this->getToolbarBlockName()) {

            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }

        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode() {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml() {
        return $this->getChildHtml('toolbar');
    }
    
    public function getCacheId($attributes = array()){
        $strConfigs = implode('_', $this->_config);
        $cacheIds = array(
            'jmproducts_filter',
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
