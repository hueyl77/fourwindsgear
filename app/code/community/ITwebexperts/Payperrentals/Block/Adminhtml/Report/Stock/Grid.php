<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Report_Stock_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('payperrentals/report/grid.phtml');
        $this->setFilterVisibility(true);
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $_filter = $this->getParam($this->getVarNameFilter(), null);
        $_filterData = array();
        parse_str(urldecode(base64_decode($_filter)), $_filterData);
        $this->getCollection()->initReport('payperrentals/reports_product_stock_collection');
        foreach ($this->getColumns() as $_columnId => $_column) {
            if (isset($_filterData[$_columnId]) && (!empty($_filterData[$_columnId]) || strlen($_filterData[$_columnId]) > 0) && $_column->getFilter()) {
                $_column->getFilter()->setValue($_filterData[$_columnId]);
            }
        }
        Mage::register('filter_data', $_filterData);
        Mage::register('grid_columns', $this->getColumns());

        $_categoryFilter = $this->getFilter('report_category');
        if ($_categoryFilter != 'none') {
            Mage::register('category_filter', $_categoryFilter);
        }

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header' => Mage::helper('payperrentals')->__('Product'),
            'index' => 'order_items_name',
            'sortable' => false,
            'align' => 'left',
            'width' => '400px',
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('payperrentals')->__('SKU'),
            'index' => 'sku',
            'width' => '400px',
            'sortable' => false,
        ));

        $this->addColumn('stock_inventory', array(
            'header' => Mage::helper('payperrentals')->__('Total Inventory'),
            'index' => 'stock_inventory',
            'sortable' => true,
            'align' => 'right',
            'width' => '100px',
            'type' => 'number'
        ));

        $this->addColumn('booked_inventory', array(
            'header' => Mage::helper('payperrentals')->__('Booked Inventory'),
            'index' => 'booked_inventory',
            'sortable' => true,
            'align' => 'right',
            'width' => '100px',
            'type' => 'number'
        ));

        $this->addColumn('total_sent_qty', array(
            'header' => Mage::helper('payperrentals')->__('Total Sent Inventory'),
            'index' => 'total_sent_qty',
            'sortable' => true,
            'align' => 'right',
            'width' => '100px',
            'type' => 'number'
        ));

        $this->addColumn('total_returned_qty', array(
            'header' => Mage::helper('payperrentals')->__('Total Returned Inventory'),
            'index' => 'total_returned_qty',
            'sortable' => true,
            'align' => 'right',
            'width' => '100px',
            'type' => 'number'
        ));

        $this->addColumn('current_stock', array(
            'header' => Mage::helper('payperrentals')->__('Current Inventory'),
            'index' => 'current_stock',
            'sortable' => true,
            'align' => 'right',
            'width' => '100px',
            'type' => 'number'
        ));

        $this->addExportType('*/*/exportStockCsv', Mage::helper('payperrentals')->__('CSV'));
        $this->addExportType('*/*/exportStockExcel', Mage::helper('payperrentals')->__('Excel XML'));

        parent::_prepareColumns();

        return $this;
    }

    /**
     * Rewrite set filter function
     * @param $_data mixed
     *
     * @return ITwebexperts_Payperrentals_Block_Adminhtml_Report_Stock_Grid
     * */
    /*protected function _setFilterValues($_data)
    {
        foreach ($this->getColumns() as $_columnId => $_column) {
            if (isset($_data[$_columnId]) && (!empty($_data[$_columnId]) || strlen($_data[$_columnId]) > 0) && $_column->getFilter()) {
                $_column->getFilter()->setValue($_data[$_columnId]);
                ITwebexperts_Payperrentals_Model_Mysql4_Reports_Product_Report_Abstract::addReportFilter($this->getCollection(), $_columnId, $_data[$_columnId]);
            }
        }
        return $this;
    }*/

    public function getRowUrl($_item)
    {
        return false;
    }
}