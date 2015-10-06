<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Fixedgrid
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Fixedgrid
	extends Mage_Adminhtml_Block_Widget_Grid
{


    /**
     *
     */
    public function __construct()
	{
		parent::__construct();
		$this->setId('fixedGrid');
		//$this->setDefaultSort('created_time');
		//$this->setDefaultDir('DESC');
	}

    /**
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
	{
		$storeId = (int)$this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}


    /**
     * @return $this
     */
    protected function _prepareCollection()
	{
		$_res = Mage::getSingleton('core/resource');
		$_collection = Mage::getModel('payperrentals/reservationorders')->getCollection();
		$_collection->getSelect()->joinLeft(array('sorder'=>$_res->getTableName('sales/order')), 'main_table.order_id = '.'sorder.entity_id',array('main_table.order_id as orderid', 'main_table.product_id as productid','main_table.qty as qtys', 'sorder.increment_id as increments_id','sorder.store_id as store_id'));
		$_collection->getSelect()->where('main_table.product_type = ?',ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE);/*main_table.fixeddate_id > 0 AND*/
		if(urldecode($this->getRequest()->getParam('store'))) {
			$_collection->getSelect()->where('store_id=?', $this->getRequest()->getParam('store'));
		}
		if(urldecode($this->getRequest()->getParam('fixeddate_id'))) {
			$_collection->getSelect()->where('fixeddate_id=?', $this->getRequest()->getParam('fixeddate_id'));
		}

		$this->setCollection($_collection);

		parent::_prepareCollection();
		return $this;
	}

    /**
     * @return $this
     */
    protected function _prepareColumns()
	{

		$this->addColumn('res_startdate', array(
			'type'		=> 'datetime',
			'header'    => Mage::helper('payperrentals')->__('Reservation Start'),
			'align'     =>'left',
			'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime(),
			'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
			'index'     => 'start_date',
			'width'		=> '100px'
		));

		$this->addColumn('res_enddate', array(
			'type'		=> 'datetime',
			'header'    => Mage::helper('payperrentals')->__('Reservation End'),
			'align'     =>'left',
			'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime(),
			'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
			'index'     => 'end_date',
			'width'		=> '100px'
		));

		$this->addColumn('product_name', array(
			'header'    => Mage::helper('payperrentals')->__('Product Name'),
			'align'     => 'left',
			'index'     => 'product_id',
			'filter_index' => 'main_table.product_id',
			'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Productname(),
			'width'		=> '200px'
		));


		$this->addColumn('increment_id', array(
			'header'    => Mage::helper('payperrentals')->__('Order'),
			'align'     =>'right',
			'width'	=> '100px',
			'index'     => 'increment_id',
			'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Order(),
			'filter_index'=>'main_table.increment_id'
		));

		$this->addColumn('qty', array(
			'type'		=> 'number',
			'header'    => Mage::helper('payperrentals')->__('Quantity'),
			'align'     =>'left',
			'index'     => 'qtys',
			'filter_index' => 'main_table.qtys',
			'width'		=> '50px'
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('payperrentals')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('payperrentals')->__('XML'));

		return parent::_prepareColumns();
	}

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
	{
		//$orderId = Mage::getModel('sales/order')->loadByIncrementId($row->getOrderId())->getId();
		//return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $orderId));
		return '';
	}
}
