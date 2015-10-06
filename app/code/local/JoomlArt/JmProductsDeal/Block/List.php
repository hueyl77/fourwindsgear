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

class JoomlArt_JmProductsDeal_Block_List extends Mage_Catalog_Block_Product_Abstract {

    var $_config = array ();

    public function __construct($attributes = array()) {
        $helper = Mage::helper ( 'joomlart_jmproductsdeal/data' );

        $this->_config ['show'] = $helper->get ( 'show', $attributes );
        if (! $this->_config ['show'])
            return;

        $this->_config ['template'] = $helper->get ( 'template', $attributes );
        if (! $this->_config ['template'])
            return;

        parent::__construct ();

        $this->_config ['mode'] = $helper->get ( 'mode', $attributes );
        $this->_config ['title'] = $helper->get ( 'title', $attributes );
        $this->_config ['catsid'] = $helper->get ( 'catsid', $attributes );

        $this->_config ['qty'] = $helper->get ( 'quanlity', $attributes );
        $this->_config ['qty'] = $this->_config ['qty'] > 0 ? $this->_config ['qty'] : $listall;

        $this->_config ['perrow'] = $helper->get ( 'perrow', $attributes );
        $this->_config ['perrow'] = $this->_config ['perrow'] > 0 ? $this->_config ['perrow'] : 3;

        $this->_config['width'] = $helper->get('width', $attributes);
        $this->_config['width'] = $this->_config['width']>0?$this->_config['width']:135;

        $this->_config['height'] = $helper->get('height', $attributes);
        $this->_config['height'] = $this->_config['height']>0?$this->_config['height']:135;

        $this->_config['max'] = $helper->get('max', $attributes);
        $this->_config['max'] = $this->_config['max']>0?$this->_config['max']:0;

        $this->_config ['showproductleft'] = $helper->get ( 'showproductleft', $attributes );
        $this->_config ['showdiscount'] = $helper->get ( 'showdiscount', $attributes );
        $this->_config ['showitemsold'] = $helper->get ( 'showitemsold', $attributes );
        $this->_config ['showsaleenddate'] = $helper->get ( 'showsaleenddate', $attributes );
        $this->_config ['showsaveamount'] = $helper->get ( 'showsaveamount', $attributes );
        $this->setProductCollection ( $this->getCategory () );
    }

    function _toHtml() {

        if (! $this->_config ['show'])
            return;

        $listall = $this->getListProducts ();

        $this->assign ( 'listall', $listall );
        $this->assign ( 'config', $this->_config );

        if (! isset ( $this->_config ['template'] ) || $this->_config ['template'] == '') {
            $this->_config ['template'] = 'joomlart/jmproductsdeal/list.phtml';
        }

        $this->setTemplate ( $this->_config ['template'] );

        if ($listall && $listall->count () > 0) {
            Mage::getModel ( 'review/review' )->appendSummary ( $listall );
        }

        return parent::_toHtml ();
    }

    function getListProducts() {
        $listall = null;
        if(is_null($this->_productCollection)){
            $this->_productCollection = $this->getListToDayProducts ();
        }

        return $this->_productCollection;
    }

    function getListToDayProducts() {
        $list = null;
        $perPage = ( int ) $this->_config ['qty'];
		
        $todayDate = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $todayDate = date("Y-m-d 00:00:00", strtotime($todayDate));

        $collection = Mage::getResourceModel('catalog/product_collection');

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addMinimalPrice()
            ->addFinalPrice()
			->addStoreFilter()
            ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
            ->addAttributeToFilter('special_to_date', array('or'=> array(
                0 => array('is' => new Zend_Db_Expr('null')),
                1 => array('date' => true, 'from' => $todayDate),
            )), 'left')
            ->addAttributeToSort('special_from_date', 'desc')
            ->setPageSize($perPage)
            ->setCurPage(1);

        if($this->_config['catsid']){
            // get array product_id
            $arr_productids = $this->getProductByCategory();
            $collection = $collection->addIdFilter($arr_productids);
        }
		
		//check if has specify special price
		$collection->getSelect()->where('price_index.final_price < price_index.price');
		
        $this->setProductCollection($collection);
		
		//Only get enabled products
		//Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
			
        if (($_products = $this->getProductCollection ()) && $_products->getSize ()) {

            if(method_exists($_products,"setMaxSize")){

                $_products->setMaxSize($perPage);
            }
            $list = $_products;
        }
	
        return $list;
    }

    function set($show=1, $mode='', $title='', $catsid='', $quanlity=9, $perrow=3, $template='', $width ='135', $height='135', $max ='80', $showproductleft =0, $showdiscount=0, $showsaleenddate=0, $showitemsold=0, $showsaveamount=0 ){
        if(!$mode || !$show){
            $this->_config['show'] = 0;
            return ;
        }

        if($mode) $this->_config['mode'] = $mode;
        if($title) $this->_config['title'] = $title;
        if($catsid!='') 	$this->_config['catsid'] = $catsid;
        if($quanlity)		$this->_config['qty'] = $quanlity;
        if($perrow)		$this->_config['perrow'] = $perrow;
        if($width)		$this->_config['width'] = $width;
        if($height)		$this->_config['height'] = $height;
        if($showproductleft)		$this->_config['showproductleft'] = $showproductleft;
        if($showdiscount)		$this->_config['showdiscount'] = $showdiscount;
        if($showitemsold)		$this->_config['showitemsold'] = $showitemsold;
        if($showsaleenddate)		$this->_config['showsaleenddate'] = $showsaleenddate;
        if($showsaveamount)		$this->_config['showsaveamount'] = $showsaveamount;
        if($max)		$this->_config['max'] = $max;

    }

    /**
     * check the array existed in the other array
     *
     */
    function inArray($source, $target) {
        for($j = 0; $j < sizeof ( $source ); $j ++) {
            if (in_array ( $source [$j], $target )) {
                return true;
            }
        }
    }

    function getProductByCategory() {
        $return = array();
        $pids = array();

        $products = Mage::getResourceModel ( 'catalog/product_collection' );

        foreach ($products->getItems() as $key => $_product){
            $arr_categoryids[$key] = $_product->getCategoryIds();

            if($this->_config['catsid']){
                if(stristr($this->_config['catsid'], ',') === FALSE) {
                    $arr_catsid[$key] =  array(0 => $this->_config['catsid']);
                }else{
                    $arr_catsid[$key] = explode(",", $this->_config['catsid']);
                }

                $return[$key] = $this->inArray($arr_catsid[$key], $arr_categoryids[$key]);
            }
        }

        foreach ($return as $k => $v){
            if($v==1) $pids[] = $k;
        }

        return $pids;
    }


    protected $_productsCount = null;

    const DEFAULT_PRODUCTS_COUNT = 6;

    protected function _beforeToHtml() {
        $collection = $this->getListProducts();

        return parent::_beforeToHtml();
    }

    public function setProductsCount($count) {
        $this->_productsCount = $count;
        return $this;
    }

    public function getProductsCount() {
        if (null === $this->_productsCount) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    }

}