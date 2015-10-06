<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Sendreturnqueuegrid
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Sendreturnqueuegrid
	extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * @var string
     */
    protected $_sendreturn_table = '';

    /**
     * @var string
     */
    protected $_massactionBlockName = 'payperrentals/adminhtml_sendreturn_grid_widget_massaction';

    /**
     *
     */
    public function __construct()
	{
		parent::__construct();
		$this->setId('sendreturnqueueGrid');
		//$this->setDefaultSort('created_time');
		//$this->setDefaultDir('DESC');
		$sendReturnRes = Mage::getModel('payperrentals/sendreturn')->getCollection();
		$this->_sendreturn_table = $sendReturnRes->getTable('payperrentals/sendreturn');
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
    protected function _prepareMassaction()
	{
		$this->setMassactionIdField('sendreturn_id');
		$this->getMassactionBlock()->setFormFieldName('sendreturn_id');
		$this->getMassactionBlock()->addItem('print', array(
			'label'=> Mage::helper('payperrentals')->__('Print Labels'),
			'url'  => $this->getUrl('*/*/massPrint', array('' => ''))
		));
		return $this;
	}

    /**
     * @return $this
     */
    protected function _prepareCollection()
	{

		$isIncludeNotSent = $this->getRequest()->getParam('include_not_sent');
		$isIncludeSent = $this->getRequest()->getParam('include_sent');
		$isIncludeNotReturned = $this->getRequest()->getParam('include_not_returned');
		$isIncludeReturned = $this->getRequest()->getParam('include_returned');

		/** @var $_collection ITwebexperts_Payperrentals_Model_Mysql4_Rentalqueue_Collection */
		$_collection = Mage::getModel('payperrentals/rentalqueue')->getCollection();
		$_collection->getSelect()->joinLeft($this->_sendreturn_table, 'main_table.sendreturn_id = ' . $this->_sendreturn_table . '.id', array(
			'send_date', 'return_date', 'sn'
		));
		$_collection->getSelect()->where('main_table.sendreturn_id != ?', '0');
		$_collection->getSelect()->where($this->_sendreturn_table . '.send_date != ?', '0000-00-00 00:00:00');
		//$this->_collection->getSelect()->where($sendReturnRes->getTable('payperrentals/sendreturn').'.send_date >= ?', ITwebexperts_Payperrentals_Helper_Data::toDbDate(date('Y-m-d',strtotime('-1 month',time()))));
		//echo 'ggg'.date('Y-m-d',strtotime('-1 month',time()));
		if ($isIncludeNotSent){
			//$this->_collection->getSelect()->where('main_table.sendreturn_id = ?','0');
		}
		//elseif($isIncludeSent){

		//}

		if ($isIncludeNotReturned){
		}
		else if ($isIncludeReturned){
			//$this->_collection->getSelect()->where($sendReturnRes->getTable('payperrentals/sendreturn').'.return_date != ?','0000-00-00 00:00:00');
		}
        if(urldecode($this->getRequest()->getParam('store'))) {
            $_collection->getSelect()->where('store_id=?', $this->getRequest()->getParam('store'));
        }
		$this->setCollection($_collection);
        $_sql = $_collection->getSelect()->__toString();

		parent::_prepareCollection();
		return $this;
	}

    /**
     * @return $this
     */
    protected function _prepareColumns()
	{

		$this->addColumn('customer_name', array(
			'header'       => Mage::helper('payperrentals')->__('Customer Name'),
			'align'        => 'left',
			//'index'     => 'customer_id',
			'index'        => 'customer_id',
			'filter'       => false,
			'filter_index' => $this->_sendreturn_table . '.name',
			'renderer'     => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Customername(),
			//'options'		=> $customers,
			'width'        => '200px'
		));
		$this->addColumn('customer_address', array(
			'header'   => Mage::helper('payperrentals')->__('Customer Address'),
			'align'    => 'left',
			'index'    => 'customer_id',
			'renderer' => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Customeraddress(),
			'filter'   => false,
			'sortable' => false,
			'width'    => '200px'
		));
		$this->addColumn('product_name', array(
			'header'   => Mage::helper('payperrentals')->__('Product Name'),
			'align'    => 'left',
			'index'    => 'product_id',
			'filter'   => false,
			'renderer' => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Productname(),
			'width'    => '200px'
		));

		$this->addColumn('send_date', array(
			'type'         => 'datetime',
			'header'       => Mage::helper('payperrentals')->__('Send Date'),
			'align'        => 'left',
			'index'        => 'send_date',
			'filter_index' => 'send_date',
			'renderer'     => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Senddate(),
            'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
			'width'        => '100px'
		));

		$this->addColumn('return_date', array(
			'type'         => 'datetime',
			'header'       => Mage::helper('payperrentals')->__('Return Date'),
			'align'        => 'left',
			'index'        => 'return_date',
			'filter_index' => 'return_date',
			'renderer'     => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Returndate(),
            'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
			'width'        => '100px'
		));

		$this->addColumn('sn', array(
			'header' => Mage::helper('payperrentals')->__('Serial Numbers'),
			'align'  => 'left',
			'index'  => 'sn',
			'width'  => '150px'
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
