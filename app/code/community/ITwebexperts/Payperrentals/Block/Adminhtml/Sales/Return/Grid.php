<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Return_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_return_grid');
        $this->setDefaultSort('return_date');
        $this->setDefaultDir('DESC');
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
        $collection->getSelect()->join(array('s' => Mage::getSingleton('core/resource')->getTableName('sales_flat_order')),
            'main_table.order_id = s.entity_id',
            array('start_datetime','end_datetime', 'send_datetime', 'return_datetime'));
        // Join send return table and remove rows that are blank dates for return
        $collection->getSelect()->join(array('s2' => Mage::getSingleton('core/resource')->getTableName('payperrentals/sendreturn')),
            'main_table.order_id = s2.order_id', array('return_date'));
        $collection->addAttributeToFilter('return_date', array("neq"=>'0000-00-00 00:00:00'));
        $expr
            = "(SELECT IFNULL(IF(s2.`return_date`<>'0000-00-00 00:00:00', IFNULL(s2.`return_date`,s2.`res_enddate`), s2.`res_enddate`), s2.`res_enddate`)) as `ends_date`";
        $collection->getSelect()->columns(new Zend_Db_Expr($expr));
        $collection->getSelect()->group('s.entity_id');
        parent::setCollection($collection);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('return_date', array(
            'header'    => Mage::helper('sales')->__('Date Returned'),
            'index'     => 'return_date',
            'type'      => 'datetime',
        ));

        $this->addColumn('order_increment_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'index'     => 'order_increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('sales')->__('Order Date'),
            'index'     => 'order_created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('total_qty', array(
            'header' => Mage::helper('sales')->__('Total Qty'),
            'index' => 'total_qty',
            'type'  => 'number',
        ));

        $this->addColumnAfter('start_datetime', array(
            'header' => Mage::helper('sales')->__('Start Date'),
            'index' => 'start_datetime',
            'type'  => 'datetime',
            'render' => 'ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime',
            'filter_index' => 'start_datetime',
        ), 'shipping_name');

        $this->addColumnAfter('end_datetime', array(
            'header' => Mage::helper('sales')->__('End Date'),
            'index' => 'end_datetime',
            'type'  => 'datetime',
            'render' => 'ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime',
            'filter_index' => 'end_datetime',
        ), 'start_datetime');

        $this->addColumnAfter('send_datetime', array(
            'header' => Mage::helper('sales')->__('Dropoff Date'),
            'index' => 'send_datetime',
            'type'  => 'datetime',
            'render' => 'ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime',
            'filter_index' => 'send_datetime',
        ), 'end_datetime');

        $this->addColumnAfter('return_datetime', array(
            'header' => Mage::helper('sales')->__('Pickup Date'),
            'index' => 'return_datetime',
            'type'  => 'datetime',
            'render' => 'ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime',
            'filter_index' => 'return_datetime',
        ), 'send_datetime');

        $this->addColumnAfter('create_latefee', array(
                'header'   => Mage::helper('payperrentals')->__('Is Late'),
                'index'    => 'ends_date',
                'renderer' => 'ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Returnlate_Renderer_Createlatefee',
                'width'    => '120px',
                'type'     => 'text',
                'sortable' => false,
                'filter'   => false,
            ),'late_fee');

        $this->sortColumnsByOrder();
        return $this;
    }


    /**
     * Get url for row
     *
     * @param string $row
     * @return string
     */
    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getOrderId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
