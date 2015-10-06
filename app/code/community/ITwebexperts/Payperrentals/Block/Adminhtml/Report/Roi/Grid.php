<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Report_Roi_Grid extends Mage_Adminhtml_Block_Report_Grid
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
        $this->getCollection()->initReport('payperrentals/reports_product_roi_collection');
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
            'sortable' => false,
            'align' => 'right',
            'width' => '100px',
            'type' => 'number'
        ));

        $this->addColumn('total_inventory_cost', array(
            'header' => Mage::helper('payperrentals')->__('Total Inventory Cost'),
            'index' => 'total_inventory_cost',
            'sortable' => false,
            'align' => 'right',
            'width' => '100px',
            'total' => 'sum',
            'filter' => 'adminhtml/widget_grid_column_filter_range',
            'type' => 'currency',
            'currency_code' => Mage::app()->getStore()->getCurrentCurrencyCode()
        ));

        $this->addColumn('monthly_revenue', array(
            'header' => Mage::helper('payperrentals')->__('Monthly Revenue'),
            'index' => 'monthly_revenue',
            'sortable' => false,
            'align' => 'right',
            'width' => '100px',
            'total' => 'sum',
            'filter' => 'adminhtml/widget_grid_column_filter_range',
            'type' => 'currency',
            'currency_code' => Mage::app()->getStore()->getCurrentCurrencyCode()
        ));

        $this->addColumn('gross_profit', array(
            'header' => Mage::helper('payperrentals')->__('Gross Profit / Loss'),
            'index' => 'gross_profit',
            'sortable' => false,
            'align' => 'right',
            'width' => '100px',
            'total' => 'sum',
            'filter' => 'adminhtml/widget_grid_column_filter_range',
            'type' => 'currency',
            'currency_code' => Mage::app()->getStore()->getCurrentCurrencyCode()
        ));

        $this->addExportType('*/*/exportRoiCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportRoiExcel', Mage::helper('adminhtml')->__('Excel XML'));

        parent::_prepareColumns();

        return $this;
    }

    public function getRowUrl($_item)
    {
        return false;
    }
}
