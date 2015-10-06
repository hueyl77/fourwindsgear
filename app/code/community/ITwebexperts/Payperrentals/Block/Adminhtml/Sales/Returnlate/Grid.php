<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Returnlate_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_returnlate_grid');
        $this->setDefaultSort('days_late');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _getCollectionClass()
    {
        return 'sales/order_shipment_grid_collection';
    }

    /**
     * Prepare and set collection of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function setCollection($collection)
    {
        $collection->getSelect()->join(array(
                'sfo' => Mage::getSingleton('core/resource')->getTableName('sales_flat_order')),
            'main_table.order_id = sfo.entity_id',
            array('main_table.order_id as entity_id','start_datetime','end_datetime', 'send_datetime', 'return_datetime'));

        /**
         * Join send return table and remove rows that are blank dates for return
         * and end_date (due date) is after today
         */
        $collection->getSelect()->join(array(
                's2' => Mage::getSingleton('core/resource')->getTableName('payperrentals/sendreturn')),
            'main_table.order_id = s2.order_id', array('return_date','res_enddate'))->group('s2.order_id');

        $collection->addAttributeToFilter('s2.return_date', array(
            array("eq"=>'0000-00-00 00:00:00'),
            array("eq"=>'1970-01-01 00:00:00')));
        $collection->addAttributeToFilter('s2.res_enddate',array("lt"=>date('Y-m-d H:i:s')));

        parent::setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {


        $this->addColumn('days_late', array(
            'header'    => Mage::helper('payperrentals')->__('Days Late'),
            'type' => 'text',
            'align' => 'left',
            'width' => '50px',
            'index' =>  'end_datetime',
            'renderer'  => 'ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Returnlate_Renderer_Dayslate',
        ));


        $this->addColumnAfter('order_increment_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'index'     => 'order_increment_id',
            'type'      => 'text',
        ), 'days_late');

        $this->addColumnAfter('order_created_at', array(
            'header'    => Mage::helper('sales')->__('Order Date'),
            'index'     => 'order_created_at',
            'type'      => 'datetime',
        ),'order_increment_id');

        $this->addColumnAfter('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ),'order_created_at');

        $this->addColumnAfter('total_qty', array(
            'header' => Mage::helper('sales')->__('Total Qty'),
            'index' => 'total_qty',
            'type'  => 'number',
        ),'shipping_name');

        $this->addColumnAfter('start_datetime', array(
            'header' => Mage::helper('sales')->__('Start Date'),
            'index' => 'start_datetime',
            'type'  => 'datetime',
            'renderer' => 'payperrentals/adminhtml_html_renderer_datetime',
            'filter_index' => 'start_datetime',
        ),'shipping_name');

        $this->addColumnAfter('end_datetime', array(
            'header' => Mage::helper('sales')->__('End Date'),
            'index' => 'end_datetime',
            'type'  => 'datetime',
            'renderer' => 'payperrentals/adminhtml_html_renderer_datetime',
            'filter_index' => 'end_datetime',
        ),'start_datetime');

        $this->addColumnAfter('send_datetime', array(
            'header' => Mage::helper('sales')->__('Dropoff Date'),
            'index' => 'send_datetime',
            'type'  => 'datetime',
            'renderer' => 'ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime',
            'filter_index' => 'send_datetime',
        ),'end_datetime');

        $this->addColumnAfter('return_datetime', array(
            'header' => Mage::helper('sales')->__('Pickup Date'),
            'index' => 'return_datetime',
            'type'  => 'datetime',
            'renderer' => 'ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime',
            'filter_index' => 'return_datetime',
        ),'send_datetime');

//        $this->addColumnAfter('late_fee', array(
//            'header' => Mage::helper('sales')->__('Approx Late Fee'),
//            'renderer' => 'ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Returnlate_Renderer_Latefee',
//            'index'     => 'order_increment_id',
//            'type'      => 'text',
//        ),'return_datetime');

        $this->addColumnAfter('create_latefee', array(
            'header'   => Mage::helper('payperrentals')->__('Approx Late Fee'),
            'index'    => 'entity_id',
            'renderer' => 'ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Returnlate_Renderer_Createlatefee',
            'width'    => '120px',
            'type'     => 'text',
            'sortable' => false,
            'filter'   => false,
        ),'late_fee');

        $this->addColumnAfter('return_state', array(
            'header'   => Mage::helper('payperrentals')->__('Return'),
            'index'    => 'total_qty_ordered',
            'renderer' => 'payperrentals/adminhtml_grid_column_renderer_returnState',
            'width'    => '120px',
            'type'     => 'options',
            'options'  => Mage::getSingleton('payperrentals/sendreturn')->_getReturnStates(),
            'sortable' => false,
            'filter'   => false,
        ),'create_latefee');






        return parent::_prepareColumns();
    }


    /**
     * Get url for row
     *
     * @param string $row
     * @return string
     */
//    public function getRowUrl($row)
//    {
//        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
//            return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getOrderId()));
//        }
//        return false;
//    }

//    public function getGridUrl()
//    {
//        return $this->getUrl('*/*/grid', array('_current'=>true));
//    }


}
