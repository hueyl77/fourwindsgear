<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Sendreturngridcustomer
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Sendreturngridcustomer extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('sendreturngridCustomer');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        //$this->setReloadParams(true);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('payperrentals_admin/adminhtml_reservationgridcustomer/grid', array('_current' => true, 'customer_id' => $this->getRequest()->getParam('customer_id')));
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
        $isIncludeAll = $this->getRequest()->getParam('include_all');
        $isIncludeNotSent = $this->getRequest()->getParam('include_not_sent');
        $isIncludeSent = $this->getRequest()->getParam('include_sent');
        $isIncludeNotReturned = $this->getRequest()->getParam('include_not_returned');
        $isIncludeReturned = $this->getRequest()->getParam('include_returned');
        $customerId = $this->getRequest()->getParam('customer_id');
        $_res = Mage::getSingleton('core/resource');
        if($isIncludeAll){
            $this->_collection = Mage::getModel('sales/order')->getCollection();
			$this->_collection->getSelect()->joinLeft(array('resorders'=>$_res->getTableName('payperrentals/reservationorders')), 'main_table.entity_id = '.'resorders.order_id',array('start_date','end_date','product_id','sendreturn_id'));
			$this->_collection->getSelect()->joinLeft(array('sendreturn'=>$_res->getTableName('payperrentals/sendreturn')), 'resorders.sendreturn_id = '.'sendreturn.id',array('send_date','return_date','qty','sn'));
			$this->_collection->getSelect()->where('resorders.product_type = ?',ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE);
            if($isIncludeNotSent){
				$this->_collection->getSelect()->where('resorders.sendreturn_id = ?','0');
            }
        }else{
            $this->_collection = Mage::getModel('payperrentals/reservationorders')->getCollection();
			$this->_collection->getSelect()->joinLeft(array('sorder'=>$_res->getTableName('sales/order')), 'main_table.order_id = '.'sorder.entity_id',array('main_table.order_id as orderid', 'main_table.product_id as productid','main_table.qty as qtys', 'sorder.increment_id as increments_id','sorder.store_id as store_id'));
			$this->_collection->getSelect()->joinLeft(array('sendreturn'=>$_res->getTableName('payperrentals/sendreturn')), 'main_table.sendreturn_id = '.'sendreturn.id',array('sendreturn.id as real_id','sendreturn.sn as sn'));
            $this->_collection->getSelect()->where('main_table.product_type = ?',ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE);

            $this->_collection->getSelect()->where('sorder.customer_id = ?',$customerId);

            if($isIncludeNotSent){
                $this->_collection->getSelect()->where('main_table.sendreturn_id = ?','0');
            }
            if($isIncludeSent){
                $this->_collection->getSelect()->where('main_table.sendreturn_id != ?','0');
            }
            if($isIncludeNotReturned){
				$this->_collection->getSelect()->where('sendreturn.return_date = ?','0000-00-00 00:00:00');
            }
            if($isIncludeReturned){
				$this->_collection->getSelect()->where('sendreturn.return_date != ?','0000-00-00 00:00:00');
            }
            //die(var_dump((string)$this->_collection->getSelect()));
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

        $this->getCollection()->getSelect()->where('sales_flat_order.customer_id = ?',$value);
    }


    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        $this->addColumn('order_id', array(
            'header'    => Mage::helper('payperrentals')->__('Order'),
            'align'     =>'right',
            'width'	=> '100px',
            'index'     => 'order_id',
            'filter_index'=>'main_table.order_id'
        ));
        /*$productCollection = Mage::getModel('catalog/product')->getCollection();
        $products = array();
        foreach($productCollection as $pr){
            $product = Mage::getModel('catalog/product')->load($pr->getId());
            $products[$product->getEntityId()] = $product->getName();
        }*/

        $this->addColumn('product_name', array(
            'header'    => Mage::helper('payperrentals')->__('Product Name'),
            'align'     => 'left',
            'index'     => 'product_id',
            //'type'  => 'options',
            //'options'	  => $products,
            'filter_index' => 'main_table.product_id',
            'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Productname(),
            'width'		=> '200px'
        ));

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

        $this->addColumn('send_date', array(
            'type'		=> 'datetime',
            'header'    => Mage::helper('payperrentals')->__('Send Date'),
            'align'     =>'left',
            'index'     => 'send_date',
            'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Returndate(),
            'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
            'width'		=> '100px'
        ));

        $this->addColumn('return_date', array(
            'type'		=> 'datetime',
            'header'    => Mage::helper('payperrentals')->__('Return Date'),
            'align'     =>'left',
            'index'     => 'return_date',
            'renderer'  => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Returndate(),
            'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
            'width'		=> '100px'
        ));

        $this->addColumn('qty', array(
            'type'		=> 'number',
            'header'    => Mage::helper('payperrentals')->__('Quantity'),
            'align'     =>'left',
            'index'     => 'qtys',
            'filter_index' => 'main_table.qtys',
            'width'		=> '50px'
        ));

        $this->addColumn('sn', array(
            'header'    => Mage::helper('payperrentals')->__('Serial Numbers'),
            'align'     =>'left',
            'index'     => 'sn',
            'width'		=> '150px'
        ));

        $this->addExportType('payperrentals_admin/adminhtml_reservationgridcustomer/exportCsv', Mage::helper('payperrentals')->__('CSV'));
        $this->addExportType('payperrentals_admin/adminhtml_reservationgridcustomer/exportXml', Mage::helper('payperrentals')->__('XML'));

        return parent::_prepareColumns();
    }


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
