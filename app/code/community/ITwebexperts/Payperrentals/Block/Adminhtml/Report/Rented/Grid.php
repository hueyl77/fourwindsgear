<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Report_Rented_Grid extends Mage_Adminhtml_Block_Report_Grid
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
        $this->getCollection()->initReport('payperrentals/reports_product_rent_collection');
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

        $this->addColumn('time_diff', array(
            'header' => Mage::helper('payperrentals')->__('Amount of Time Rented'),
            'index' => 'time_diff',
            'sortable' => false,
            'align' => 'left',
            'width' => '300px',
            'filter' => false,
            'renderer' => 'payperrentals/adminhtml_report_grid_period'
        ));

        $this->addColumn('ordered_qty', array(
            'header' => Mage::helper('payperrentals')->__('Times Rented'),
            'index' => 'ordered_qty',
            'sortable' => false,
            'align' => 'right',
            'width' => '100px',
            'type' => 'number'
        ));

        $this->addColumn('stock_inventory', array(
            'header' => Mage::helper('payperrentals')->__('Total Inventory'),
            'index' => 'stock_inventory',
            'sortable' => false,
            'align' => 'right',
            'width' => '100px',
            'type' => 'number'
        ));

        $this->addColumn('today_booked', array(
            'header' => Mage::helper('payperrentals')->__('Booked Inventory'),
            'index' => 'today_booked',
            'sortable' => false,
            'align' => 'right',
            'width' => '100px',
            'type' => 'number'
        ));

        $this->addColumn('current_stock', array(
            'header' => Mage::helper('payperrentals')->__('Current Inventory'),
            'index' => 'current_stock',
            'sortable' => false,
            'align' => 'right',
            'width' => '100px',
            'type' => 'number'
        ));

        $this->addColumn('revenue', array(
            'header' => Mage::helper('payperrentals')->__('Revenue'),
            'index' => 'revenue',
            'sortable' => false,
            'align' => 'right',
            'width' => '100px',
            'total' => 'sum',
            'type' => 'price',
            'filter' => 'adminhtml/widget_grid_column_filter_range',
            'currency_code' => Mage::app()->getStore()->getCurrentCurrencyCode()
        ));

        $this->addExportType('*/*/exportRentedCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportRentedExcel', Mage::helper('adminhtml')->__('Excel XML'));

        parent::_prepareColumns();

        return $this;
    }

    /**
     * Rewrite set filter function
     * @param $_data mixed
     *
     * @return ITwebexperts_Payperrentals_Block_Adminhtml_Report_Stock_Grid
     * */
    protected function _setFilterValuesAfter($_data)
    {
        foreach ($this->getColumns() as $_columnId => $_column) {
            if (isset($_data[$_columnId]) && (!empty($_data[$_columnId]) || strlen($_data[$_columnId]) > 0) && $_column->getFilter()) {
                $_column->getFilter()->setValue($_data[$_columnId]);
                ITwebexperts_Payperrentals_Model_Mysql4_Reports_Product_Report_Abstract::addReportFilter($this->getCollection(), $_columnId, $_data[$_columnId], true);
            }
        }
        return $this;
    }

    public function getRowUrl($_item)
    {
        return false;
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/index', array('_current' => true));
    }
}
