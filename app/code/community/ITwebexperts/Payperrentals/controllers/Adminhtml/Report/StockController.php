<?php

class ITwebexperts_Payperrentals_Adminhtml_Report_StockController extends Mage_Adminhtml_Controller_Report_Abstract
{
    /**
     * Add report/sales breadcrumbs
     *
     * @return ITwebexperts_Payperrentals_Adminhtml_Report_StockController
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('payperrentals')->__('Stock'), Mage::helper('payperrentals')->__('Stock'));
        return $this;
    }

    public function indexAction()
    {
        Mage::getSingleton('adminhtml/session')->addNotice(MAge::helper('payperrentals')->__('Booked inventory is inventory booked out for currently selected date.'));
        Mage::getSingleton('adminhtml/session')->addNotice(MAge::helper('payperrentals')->__('Current inventory is what is available for today\'s date.'));
        $this->_title($this->__('Reports'))->_title($this->__('Stock'));
        $this->_initAction()
            ->_setActiveMenu('payperrentals/reports/most_rented')
            ->_addBreadcrumb(Mage::helper('payperrentals')->__('Stock Report'), Mage::helper('payperrentals')->__('Stock Report'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_stock.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));
        $this->renderLayout();
    }

    /**
     * Report action init operations
     *
     * @param array|Varien_Object $_blocks
     * @return ITwebexperts_Payperrentals_Adminhtml_Report_StockController
     */
    public function _initReportAction($_blocks)
    {
        if (!is_array($_blocks)) {
            $_blocks = array($_blocks);
        }

        $_requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $_requestData = $this->_filterDates($_requestData, array('report_from', 'report_to'));
        $_requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $_params = new Varien_Object();

        foreach ($_requestData as $_key => $_value) {
            if (!empty($_value)) {
                $_params->setData($_key, $_value);
            }
        }

        foreach ($_blocks as $_block) {
            if ($_block) {
                $_block->setPeriodType($_params->getData('period_type'));
                $_block->setFilterData($_params);
            }
        }

        return $this;
    }

    /**
     * Export Stock Products report to CSV format action
     *
     */
    public function exportStockCsvAction()
    {
        $_fileName   = 'stock_inventory.csv';
        $_content    = $this->getLayout()
            ->createBlock('payperrentals/adminhtml_report_stock_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($_fileName, $_content);
    }

    /**
     * Export Stock Products report to XML format action
     *
     */
    public function exportStockExcelAction()
    {
        $_fileName   = 'stock_inventory.xml';
        $_content    = $this->getLayout()
            ->createBlock('payperrentals/adminhtml_report_stock_grid')
            ->getExcel($_fileName);

        $this->_prepareDownloadResponse($_fileName, $_content);
    }
}