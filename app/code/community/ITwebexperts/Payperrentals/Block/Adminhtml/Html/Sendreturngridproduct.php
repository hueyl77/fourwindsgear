<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Sendreturngridproduct
 * todo Refactoring and optimization _prepareCollection
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Sendreturngridproduct extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('sendreturngridProduct');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        //$this->setReloadParams(true);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('payperrentals_admin/adminhtml_reservationgrid/grid', array('_current' => true, 'product_id' => $this->getRequest()->getParam('product_id')));
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
        $productId = $this->getRequest()->getParam('product_id');
        $_res = Mage::getSingleton('core/resource');

            $this->_collection = Mage::getModel('payperrentals/reservationorders')->getCollection();
			$this->_collection->getSelect()->joinLeft(array('sorder'=>$_res->getTableName('sales/order')), 'main_table.order_id = '.'sorder.entity_id',array('main_table.order_id as orderid', 'main_table.product_id as productid','main_table.qty as qtys', 'sorder.increment_id as increments_id','sorder.store_id as store_id'));
			//$this->_collection->getSelect()->joinLeft(array('sendreturn'=>$_res->getTableName('payperrentals/sendreturn')), 'main_table.sendreturn_id = '.'sendreturn.id',array('sendreturn.id as real_id','sendreturn.sn as sn','sendreturn.send_date as send_date','sendreturn.return_date as return_date'));
            $this->_collection->getSelect()->where('main_table.product_type = ?', ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE);

            $this->_collection->getSelect()->where('main_table.product_id = ?', $productId);

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

        $this->getCollection()->getSelect()->where('sales_flat_order.customer_id = ?', $value);
    }


    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        $customerCollection = Mage::getModel('customer/customer')->getCollection();
        $customers = array();
        foreach ($customerCollection as $cust) {
            $customer = Mage::getModel('customer/customer')->load($cust->getId());
            $customers[$customer->getEntityId()] = $customer->getFirstname() . ' ' . $customer->getLastname();
        }

        $this->addColumn('increment_id', array(
            'header' => Mage::helper('payperrentals')->__('Order'),
            'align' => 'right',
            'width' => '100px',
            'index' => 'increments_id',
            'filter_index' => 'increments_id',
        ));

        $this->addColumn('customer_name', array(
            'header' => Mage::helper('payperrentals')->__('Customer Name'),
            'align' => 'left',
            //'index'     => 'customer_id',
            'index' => 'order_id',
            'renderer' => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Customername(),
            'options' => $customers,
            'type' => 'options',
            'filter_index' => 'main_table.order_id',
            'filter_condition_callback' => array($this, '_filterCategoriesCondition'),
            'width' => '200px'
        ));

        $this->addColumn('res_startdate', array(
            'type' => 'datetime',
            'header' => Mage::helper('payperrentals')->__('Reservation Start'),
            'align' => 'left',
            'index' => 'start_date',
            'renderer' => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime(),
            'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
            'width' => '100px'
        ));

        $this->addColumn('res_enddate', array(
            'type' => 'datetime',
            'header' => Mage::helper('payperrentals')->__('Reservation End'),
            'align' => 'left',
            'index' => 'end_date',
            'renderer' => new ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime(),
            'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
            'width' => '100px'
        ));

        $this->addColumn('qty', array(
            'type' => 'number',
            'header' => Mage::helper('payperrentals')->__('Quantity'),
            'align' => 'left',
            'index' => 'qtys',
            'width' => '50px',
            'filter_index' => 'main_table.qtys'
        ));

        $this->addExportType('payperrentals_admin/adminhtml_reservationgrid/exportCsv', Mage::helper('payperrentals')->__('CSV'));
        $this->addExportType('payperrentals_admin/adminhtml_reservationgrid/exportXml', Mage::helper('payperrentals')->__('XML'));

        return parent::_prepareColumns();
    }


    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        $orderId = Mage::getModel('sales/order')->load($row->getOrderId())->getId();
        return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $orderId));
    }

}
