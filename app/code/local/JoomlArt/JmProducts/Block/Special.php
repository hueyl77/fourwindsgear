<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * New products block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class JoomlArt_JmProducts_Block_Special extends Mage_Catalog_Block_Product_Abstract {

    protected $_config = array();
    protected $_productsCount = null;
    protected $cacheLifeTime = 86400; //1day
    
    const DEFAULT_PRODUCTS_COUNT = 1;
    const CACHE_TAG = 'jm_products';
    
    public function __construct($attributes = array()) {
        $helper = Mage::helper('joomlart_jmproducts/data');
        
        //check enable
        $this->_config['show'] = (int) $helper->get('show', $attributes);
        if (!$this->_config['show']){
            return;
        } 

        $this->_config['template'] = $helper->get('template', $attributes);

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
    }

    protected function _beforeToHtml() {
        $todayDate = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $collection = Mage::getResourceModel('catalog/product_collection');

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $collection = $this->_addProductAttributesAndPrices($collection)->addStoreFilter()
            ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
            ->addAttributeToFilter('special_to_date', array('or' => array(
                    0 => array('date' => true, 'from' => $todayDate),
                    1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left')
            ->addAttributeToSort('special_from_date', 'desc')
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1)
        ;
        $this->setProductCollection($collection);

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
    
    public function getCacheId($attributes = array()){
        $strConfigs = implode('_', $this->_config);
        $cacheIds = array(
            'jmproducts_special',
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
