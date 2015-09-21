<?php


class ITwebexperts_Payperrentals_Block_Adminhtml_Manualreserve_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('reportGrid');
        $this->setDefaultSort('res_start');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return this
     */
    protected function _prepareCollection()
    {
        $_collection = Mage::getModel('payperrentals/reservationorders')->getCollection();
        $_collection->getSelect()->joinLeft(array('sales_order' => Mage::getSingleton('core/resource')->getTableName('sales/order')),
            'sales_order.entity_id=main_table.order_id',array('customer_firstname','customer_lastname'));
        if(Mage::helper('itwebcommon')->isVendorAdmin()){
            $_collection->getSelect()->joinLeft(array('product_entity' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity')),
                'product_entity.entity_id=main_table.product_id',array('vendor_id'));
            $_collection->addFieldToFilter('product_entity.vendor_id',array('eq'=>Mage::getSingleton('vendors/session')->getId()));
        }
        $this->setCollection($_collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('orderid', array(
            'header' => Mage::helper('payperrentals')->__('Order'),
            'index' => 'order_id',
        ));

        $this->addColumn('customer', array(
            'header' => Mage::helper('payperrentals')->__('Customer Name'),
            'index' => 'customer_firstname',
            'renderer'  =>  'ITwebexperts_Payperrentals_Block_Adminhtml_Manualreserve_Renderer_Customername'
        ));

        $this->addColumn('res_start', array(
            'header' => Mage::helper('payperrentals')->__('Start Date'),
            'renderer'  => 'ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime',
            'index' => 'start_date',
        ));
        $this->addColumn('res_end', array(
            'header' => Mage::helper('payperrentals')->__('End Date'),
            'renderer'  => 'ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Datetime',
            'index' => 'end_date',
        ));
        $this->addColumn('product', array(
            'header' => Mage::helper('payperrentals')->__('Product'),
            'index' => 'product_id',
            'renderer'  =>  'ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Productname'
        ));

        $this->addColumn('payperrentals_quantity', array(
            'header' => Mage::helper('payperrentals')->__('Quantity'),
            'align' => 'left',
            'type' => 'number',
            'index' => 'qty',
        ));
        $this->addColumn('comments', array(
            'header' => Mage::helper('payperrentals')->__('Comments'),
            'align' => 'left',
            'index' => 'comments',
        ));

        return parent::_prepareColumns();
    }

    /**
     * @param $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array
        ('id'   =>  $row->getId()));
    }
}