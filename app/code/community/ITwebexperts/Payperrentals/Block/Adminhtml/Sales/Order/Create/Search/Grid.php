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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create items grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Order_Create_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('sales_order_create_search_grid');
		$this->setRowClickCallback('order.productGridRowClick.bind(order)');
		$this->setCheckboxCheckCallback('order.productGridCheckboxCheck.bind(order)');
		$this->setRowInitCallback('order.productGridRowInit.bind(order)');
		$this->setDefaultSort('category_ids');
		$this->setUseAjax(true);
		if ($this->getRequest()->getParam('collapse')) {
			$this->setIsCollapsed(true);
		}
	}

	/**
	 * Retrieve quote store object
	 * @return Mage_Core_Model_Store
	 */
	public function getStore()
	{
		return Mage::getSingleton('adminhtml/session_quote')->getStore();
	}

	/**
	 * Retrieve quote object
	 * @return Mage_Sales_Model_Quote
	 */
	public function getQuote()
	{
		return Mage::getSingleton('adminhtml/session_quote')->getQuote();
	}

	protected function _addColumnFilterToCollection($column)
	{
		// Set custom filter for in product flag
		if ($column->getId() == 'in_products') {
			$productIds = $this->_getSelectedProducts();
			if (empty($productIds)) {
				$productIds = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
			} else {
				if($productIds) {
					$this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
				}
			}
		} else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}

	/**
	 * Prepare collection to be displayed in the grid
	 *
	 * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
	 */
	protected function _prepareCollection()
	{
		$attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
		/* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
		$collection = Mage::getModel('catalog/product')->getCollection();
		$collection
				->setStore($this->getStore())
				->addAttributeToSelect($attributes)
				->addAttributeToSelect('sku')
				->addStoreFilter()
				->addAttributeToFilter('type_id', array_keys(
			Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray()
		))
				->addAttributeToSelect('gift_message_available');
		$_res = Mage::getSingleton('core/resource');
		$_eav = Mage::getModel('eav/config');
		$_nameAttr = $_eav->getAttribute('catalog_category', 'name');
		$_nameTable = $_res->getTableName('catalog/category') . '_' . $_nameAttr->getBackendType();
		$_nameAttrId = $_nameAttr->getAttributeId();

        $collection->joinField('qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left');


        /*$collection->joinTable(array('ccp'=>$res->getTableName('catalog/category_product')),
            'product_id=entity_id', array('single_category_id' => 'category_id'),
            null, 'left')
                ->groupByAttribute('entity_id');*/

        $collection->getSelect()->columns(new Zend_Db_Expr("(SELECT IFNULL(GROUP_CONCAT(ccp.category_id SEPARATOR ','), '') FROM " . $_res->getTableName('catalog/category_product') . " AS ccp WHERE ccp.product_id=e.entity_id) as category_ids"));
        $collection->getSelect()->columns(new Zend_Db_Expr("(SELECT IFNULL(GROUP_CONCAT(ccev.value SEPARATOR ', '), '') FROM $_nameTable AS ccev     WHERE FIND_IN_SET(ccev.entity_id, category_ids) AND attribute_id=$_nameAttrId) as category_names"));

            /* Not usefull code*/
				/*->groupByAttribute('entity_id')
				->joinTable(array('cc' => $nametable),
			"entity_id=single_category_id", array('single_category_name' => 'value'), array(
			'attribute_id'=> $nameattrid, 'store_id' => $this->getStore()->getId()), 'left')
				->getSelect()->columns(array('category_names' => new Zend_Db_Expr("IFNULL(GROUP_CONCAT(`cc`.`value` SEPARATOR '; '), '')")));*/
        $collection->addAttributeToFilter('status', array('in'=>Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * Prepare columns
	 *
	 * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('entity_id', array(
			'header'    => Mage::helper('payperrentals')->__('ID'),
			'sortable'  => true,
			'width'     => '60',
			'index'     => 'entity_id'
		));
		$this->addColumn('name', array(
			'header'    => Mage::helper('payperrentals')->__('Product Name'),
			'renderer'  => 'payperrentals/adminhtml_sales_order_create_search_grid_renderer_product',
			'index'     => 'name'
		));
		$this->addColumn('sku', array(
			'header'    => Mage::helper('payperrentals')->__('SKU'),
			'width'     => '80',
			'index'     => 'sku'
		));

        /**
         * Not using this code
         * */
		/*$collection = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('name');
		$options = array();
		foreach ($collection as $item){
			if($item->getId() != ''){
				$options[$item->getId()] = $item->getName();
			}
		}*/

		$this->addColumn('category_names',
			array(
				'header'   => Mage::helper('payperrentals')->__('Categories'),
				'index'    => 'category_names',
				'width'    => '150px',
				'type' => 'text',
                'filter' => false,
                'sortable' => false
				/*'options'  => $options*/
			));
		$this->addColumn('price', array(
			'header'    => Mage::helper('payperrentals')->__('Price'),
			'column_css_class' => 'price',
			'align'     => 'center',
			'type'      => 'currency',
			'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
			'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
			'index'     => 'price',
			'renderer'  => 'payperrentals/adminhtml_sales_order_create_search_grid_renderer_price'
		));

		$this->addColumn('in_products', array(
			'header'    => Mage::helper('payperrentals')->__('Select'),
			'header_css_class' => 'a-center',
			'type'      => 'checkbox',
			'name'      => 'in_products',
			'values'    => $this->_getSelectedProducts(),
			'align'     => 'center',
			'index'     => 'entity_id',
			'sortable'  => false,
		));

		$this->addColumn('qty', array(
			'filter'    => false,
			'sortable'  => false,
			'header'    => Mage::helper('payperrentals')->__('Qty To Add'),
			'renderer'  => 'payperrentals/adminhtml_sales_order_create_search_grid_renderer_qty',
			'name'      => 'qty',
			'inline_css'=> 'qty',
			'align'     => 'center',
			'type'      => 'input',
			'validate_class' => 'validate-number',
			'index'     => 'empty',
			'width'     => '1',
		));

		$this->addColumn('currentstock', array(
			'filter'    => false,
			'sortable'  => false,
			'header'    => Mage::helper('payperrentals')->__('Total Stock'),
			'renderer'  => 'payperrentals/adminhtml_sales_order_create_search_grid_renderer_currentstock',
			'name'      => 'currentqty',
			'inline_css'=> 'currentqty',
			'align'     => 'center',
			'type'      => 'input',
			'validate_class' => 'validate-number',
			'index'     => 'entity_id',
			'width'     => '1',
		));

		$this->addColumn('remainingstock', array(
			'filter'    => false,
			'sortable'  => false,
			'header'    => Mage::helper('payperrentals')->__('Remaining Stock'),
			'renderer'  => 'payperrentals/adminhtml_sales_order_create_search_grid_renderer_remainingstock',
			'name'      => 'remainingqty',
			'inline_css'=> 'remainingqty',
			'align'     => 'center',
			'type'      => 'input',
			'validate_class' => 'validate-number',
			'index'     => 'entity_id',
			'width'     => '1',
		));

		return parent::_prepareColumns();
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/loadBlock', array('block'=>'search_grid', '_current' => true, 'collapse' => null));
	}

	protected function _getSelectedProducts()
	{
		$products = $this->getRequest()->getPost('products', array());

		return $products;
	}

	/**
	 * Retrieve gift message save model
	 *
	 * @deprecated after 1.4.2.0
	 * @return Mage_Adminhtml_Model_Giftmessage_Save
	 */
	protected function _getGiftmessageSaveModel()
	{
		return Mage::getSingleton('adminhtml/giftmessage_save');
	}

	/*
	 * Add custom options to product collection
	 *
	 * return Mage_Adminhtml_Block_Widget_Grid
	 */
	protected function _afterLoadCollection() {
		$this->getCollection()->addOptionsToResult();
		return parent::_afterLoadCollection();
	}

    public function getSelectCountSql(){
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        return $countSelect->reset()->from($this->getSelect(), array())->columns('COUNT(*)');
    }
}
