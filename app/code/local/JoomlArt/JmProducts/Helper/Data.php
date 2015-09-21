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

class JoomlArt_JmProducts_Helper_Data extends Mage_Core_Helper_Abstract {

    var $defaultStoreCode = null;
    var $activeStoreCode = null;
    var $activeWebsiteCode = null;

    public function __construct() {
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

    public function isAdmin() {
        if (Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'adminhtml') {
            return true;
        }

        return false;
    }

    public function get($var, $attributes, $storeCode = null, $websiteCode = null) {
        $value = null;
        if (isset($attributes[$var])) {
            $value = $attributes[$var];
        } else {
            $websiteCode = ($websiteCode) ? $websiteCode : $this->activeWebsiteCode;
            $storeCode = ($storeCode) ? $storeCode : $this->activeStoreCode;
            if ($storeCode) {
                $configGroups = Mage::getStoreConfig("joomlart_jmproducts", $storeCode);
            } else if ($websiteCode) {
                $configGroups = Mage::app()->getWebsite($websiteCode)->getConfig('joomlart_jmproducts');
            } else {
                $configGroups = Mage::getStoreConfig("joomlart_jmproducts", $this->defaultStoreCode);
            }
            foreach ($configGroups as $configs) {
                if (isset($configs[$var])) {
                    $value = $configs[$var];
                    break;
                }
            }
        }

        return $value;
    }

    /**
     * Get Product collection by configs
     *
     * @param null $configs
     * @return ProductCollection
     */
    public function getProductCollection($configs = null) {
        $collection = null;
        if ($configs) {
            $limit = (isset($configs['qty'])) ? (int) $configs['qty'] : null;
            $pageSize = (isset($configs['qtyperpage'])) ? (int) $configs['qtyperpage'] : null;

            $orderBy = (isset($configs['order_by'])) ? (int) $configs['order_by'] : 'updated_at';
            $orderDir = (isset($configs['order_dir'])) ? (int) $configs['order_dir'] : 'desc';
            $currentPage = (isset($configs['page'])) ? (int) $configs['page'] : 1;

            $storeId = Mage::app()->getStore()->getId();

            $resourceModel = 'catalog/product_collection';
            if ($configs['mode'] == 'most_viewed' || $configs['mode'] == 'best_buy') {
                $resourceModel = 'reports/product_collection';
            }

            $collection = Mage::getResourceModel($resourceModel)
                ->setStoreId($storeId)
                ->addAttributeToSelect('*')
                ->addMinimalPrice()
                ->addFinalPrice()
                //->addTaxPercents()
                ->addStoreFilter($storeId);

            if ($configs['productsid']) {
                $configs['productsid'] = explode(",", $configs['productsid']);
                $collection->addAttributeToFilter('entity_id', array('in' => $configs['productsid']));
            }

            if (isset($configs['category_ids']) && $configs['category_ids'] && $configs['category_ids'] != 'all') {
                $this->addCategoryIdsFilter($collection, $configs['category_ids']);
            }

            $orderCondition = "e.{$orderBy} {$orderDir}";

            //Add more condition by mode
            if ($configs['mode'] == 'top_rated' || $configs['mode'] == 'most_reviewed') {
                $collection->getSelect()->joinLeft(
                    array('reviewed_table' => Mage::getConfig()->getTablePrefix() . 'review_entity_summary'), "reviewed_table.store_id = {$storeId} AND reviewed_table.entity_pk_value = e.entity_id", array()
                );

                if ($configs['mode'] == 'top_rated') {
                    $orderBy = 'rating_summary';
                }

                if ($configs['mode'] == 'most_reviewed') {
                    $orderBy = 'reviews_count';
                }

                $orderCondition = "reviewed_table.{$orderBy} {$orderDir}";
            } else if ($configs['mode'] == 'featured') {
                $collection->addAttributeToSelect('featured');
                $collection->addFieldToFilter(array(
                    array('attribute' => 'featured', 'eq' => '1')
                ));
            } else if ($configs['mode'] == 'most_viewed') {
                $collection->addViewsCount();
                $orderCondition = null;
            } else if ($configs['mode'] == 'best_buy') {
                $collection->addOrderedQty();
                $collection->setOrder('ordered_qty', 'desc');
                $orderCondition = null;
            }

            //Only get enabled products
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);

            //Check in/out stock
            if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
            }

            //Check for Flat mode
            $productFlatData = Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
            if ($productFlatData == "1") {
                if (in_array($configs['mode'], array('best_buy', 'most_viewed'))) {
                    $collection->getSelect()->joinLeft(
                        array('flat' => Mage::getConfig()->getTablePrefix() . 'catalog_product_flat_' . $storeId), "(e.entity_id = flat.entity_id ) ", array(
                        'flat.name AS name', 'flat.small_image AS small_image', 'flat.thumbnail AS thumbnail', 'flat.price AS price', 'flat.special_price as special_price', 'flat.special_from_date AS special_from_date', 'flat.special_to_date AS special_to_date'
                        )
                    );
                }
            }

            //set random order
            if ($configs['random'] && is_object($collection)) {
                $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
            }

            if ($orderCondition) {
                $collection->getSelect()->order($orderCondition);
            }

            //Set limit
            $collection->setMaxSize($limit);

            if (!isset($configs['get_total'])) {
                //set page size
                $collection->setPageSize($pageSize)->setCurPage($currentPage);
            }

            //echo $collection->getSelect()->assemble();die();
        }

        return $collection;
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
     * Build params for view more link from Block
     *
     * @param array $params
     * @return array
     */
    public function buildParams($params = array()) {
        //We can unset any params here if needed.
        if (isset($params['template'])) {
            unset($params['template']);
            $params['template'] = null;
        }
        return $params;
    }

}
