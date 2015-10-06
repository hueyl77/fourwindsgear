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
        return 'payperrentals/sendreturn_collection';
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
        $collection->addFieldToFilter('return_date', array("neq"=>'0000-00-00 00:00:00'));
        $collection->getSelect()->joinLeft(array('s' => Mage::getSingleton('core/resource')->getTableName('sales_flat_order')),
            'main_table.order_id = s.entity_id',
            array('start_datetime','end_datetime', 'send_datetime', 'return_datetime','increment_id','created_at'));

        parent::setCollection($collection);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('return_date', array(
            'header'    => Mage::helper('sales')->__('Date Returned'),
            'index'     => 'return_date',
            'type'      => 'datetime',
        ));

        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'index'     => 'increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Order Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('qty', array(
            'header' => Mage::helper('sales')->__('Qty'),
            'index' => 'qty',
            'type'  => 'number',
        ));

        $this->addColumn('sn', array(
            'header' => Mage::helper('sales')->__('Serials'),
            'index' => 'sn',
            'type'      => 'text'
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
