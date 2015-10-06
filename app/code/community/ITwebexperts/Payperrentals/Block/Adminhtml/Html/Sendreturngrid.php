<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Sendreturngrid
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Sendreturngrid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *
     */
    public function __construct()
  {
      parent::__construct();
      $this->setId('sendreturnGrid');
      //$this->setDefaultSort('created_time');
      //$this->setDefaultDir('DESC');
  }

    /**
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
		$isIncludeNotSent = $this->getRequest()->getParam('include_not_sent');
		$isIncludeNotReturned = $this->getRequest()->getParam('include_not_returned');

        	$resource = Mage::getSingleton('core/resource');
			$this->_collection = Mage::getModel('payperrentals/sendreturn')->getCollection();
			$this->_collection->getSelect()->joinLeft(array('sfo'=>$resource->getTableName('sales/order')), 'main_table.order_id = '.'sfo.entity_id','*');
			//$this->_collection->getSelect()->joinLeft(array('reservationorders'=>$resource->getTableName('payperrentals/reservationorders')), 'main_table.resorder_id = '.'reservationorders.id',array(''));
			//$this->_collection->getSelect()->where('product_type = ?',ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE);
			if(!$isIncludeNotSent){
                $this->_collection->addFieldToFilter('main_table.send_date',array('notnull'=>true));
			}
			if(!$isIncludeNotReturned && !$isIncludeNotSent){
                $this->_collection->addFieldToFilter('main_table.return_date',array('notnull'=>true));
                $this->_collection->addFieldToFilter('main_table.return_date',array('neq'=>'0000-00-00 00:00:00'));
                $this->_collection->addFieldToFilter('main_table.return_date',array('neq'=>'1970-01-01 00:00:00'));
			}
        if(urldecode($this->getRequest()->getParam('store'))) {
            $this->_collection->getSelect()->where('store_id=?', $this->getRequest()->getParam('store'));
        }
		$this->setCollection(
			$this->_collection
		);

		
		parent::_prepareCollection();
		return $this;
		
    }

    /**
     * @param $collection
     * @param $column
     */
    protected function _filterCategoriesCondition($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}
		$isIncludeAll = $this->getRequest()->getParam('include_all');
		if($isIncludeAll){
			$this->getCollection()->getSelect()->where('main_table.customer_id = ?',$value);
		}else{
			$this->getCollection()->getSelect()->where('sorder.customer_id = ?',$value);
		}
	}

    /**
     * @return $this
     */
    protected function _prepareColumns()
  {

	  $customerCollection = Mage::getModel('customer/customer')->getCollection();
	  $customers = array();
	  foreach($customerCollection as $cust){
		  $customer = Mage::getModel('customer/customer')->load($cust->getId());
		  $customers[$customer->getEntityId()] = $customer->getFirstname(). ' '.$customer->getLastname();
	  }


		  $this->addColumn('order_id', array(
			  'header'    => Mage::helper('payperrentals')->__('Order'),
			  'align'     =>'right',
			  'width'	=> '100px',
			  'index'     => 'increment_id',
			  //'filter_index' => 'sorder.increment_id',
		  ));
		  $this->addColumn('customer_name', array(
			  'header'    => Mage::helper('payperrentals')->__('Customer Name'),
			  'align'     =>'left',
			  'index'     => 'entity_id',
			  'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Customername(),
			  'options'		=> $customers,
			  'type'  => 'options',
			  'filter_index' => 'entity_id',
			  'filter_condition_callback' => array($this, '_filterCategoriesCondition'),
			  'width'		=> '200px'
		  ));
		  $this->addColumn('customer_address', array(
			  'header'    => Mage::helper('payperrentals')->__('Customer Address'),
			  'align'     =>'left',
			  'index'     => 'entity_id',
			  'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Customeraddress(),
			  'filter'	  =>false,
			  'sortable'  => false,
			  'width'	  => '200px'
		  ));


	  $this->addColumn('product_name', array(
          'header'    => Mage::helper('payperrentals')->__('Product Name'),
          'align'     => 'left',
          'index'     => 'product_id',
		  'filter_index' => 'product_id',
		  'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Productname(),
		  'width'		=> '200px'
      ));

	  $this->addColumn('res_startdate', array(
			'type'		=> 'datetime',
          'header'    => Mage::helper('payperrentals')->__('Reservation Start'),
          'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
          'align'     =>'left',
          'index'     => 'res_startdate',
		  'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime(),
		  'width'		=> '100px'
      ));

	  $this->addColumn('res_enddate', array(
		  'type'		=> 'datetime',
		  'header'    => Mage::helper('payperrentals')->__('Reservation End'),
          'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
		  'align'     =>'left',
		  'index'     => 'res_enddate',
		  'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime(),
		  'width'		=> '100px'
	  ));

	  $this->addColumn('send_date', array(
		  'type'		=> 'datetime',
		  'header'    => Mage::helper('payperrentals')->__('Send Date'),
          'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
		  'align'     =>'left',
		  'index'     => 'send_date',
		  'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Returndate(),
		  'width'		=> '100px'
	  ));

	  $this->addColumn('return_date', array(
		  'type'		=> 'datetime',
		  'header'    => Mage::helper('payperrentals')->__('Return Date'),
          'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
		  'align'     =>'left',
		  'index'     => 'return_date',
		  'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Returndate(),
		  'width'		=> '100px'
	  ));

	  $this->addColumn('qty', array(
		  'type'		=> 'number',
		  'header'    => Mage::helper('payperrentals')->__('Quantity'),
		  'align'     =>'left',
		  'index'     => 'qty',
		  'filter_index' => 'qty',
		  'width'		=> '50px'
	  ));

	  $this->addColumn('sn', array(
		  'header'    => Mage::helper('payperrentals')->__('Serial Numbers'),
		  'align'     =>'left',
		  'index'     => 'sn',
		  'width'		=> '150px'
	  ));

	 $this->addExportType('*/*/exportCsv', Mage::helper('payperrentals')->__('CSV'));
	 $this->addExportType('*/*/exportXml', Mage::helper('payperrentals')->__('XML'));
	  
      return parent::_prepareColumns();
  }

//    protected function filterdate($collection, $column)
//    {
//        $from = $column->getFilter()->getValue()['from'];
//        $from->setTimezone(Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE));
//        $to = $column->getFilter()->getValue()['to'];
//        $to->setTimezone(Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE));
//        $index = $column->getIndex();
//        $this->$collection->
//        return $this;
//    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
  {
	  $orderId = Mage::getModel('sales/order')->load($row->getOrderId())->getId();
      return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $orderId) );
  }

}
