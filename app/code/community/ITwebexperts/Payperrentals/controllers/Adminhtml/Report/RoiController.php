<?php

class ITwebexperts_Payperrentals_Adminhtml_Report_RoiController extends Mage_Adminhtml_Controller_Report_Abstract
{
    /**
     * Add report/sales breadcrumbs
     *
     * @return Mage_Adminhtml_Report_SalesController
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('payperrentals')->__('ROI Report'), Mage::helper('payperrentals')->__('ROI Report'));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));
        $this->_initAction()
            ->_setActiveMenu('payperrentals/reports/most_rented')
            ->_addBreadcrumb(Mage::helper('payperrentals')->__('ROI Report'), Mage::helper('payperrentals')->__('ROI Report'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_roi.grid');
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
     * @param array|Varien_Object $blocks
     * @return ITwebexperts_Payperrentals_Adminhtml_Report_RoiController
     */
    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData = $this->_filterDates($requestData, array('report_from', 'report_to'));
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new Varien_Object();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block) {
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }
        }

        return $this;
    }

    /**
     * Export ROI Products report to CSV format action
     *
     */
    public function exportRoiCsvAction()
    {
        $_fileName = 'roi_report.csv';
        $_content = $this->getLayout()
            ->createBlock('payperrentals/adminhtml_report_roi_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($_fileName, $_content);
    }

    /**
     * Export ROI Products report to XML format action
     *
     */
    public function exportRoiExcelAction()
    {
        $_fileName = 'roi_report.xml';
        $_content = $this->getLayout()
            ->createBlock('payperrentals/adminhtml_report_roi_grid')
            ->getExcel($_fileName);

        $this->_prepareDownloadResponse($_fileName, $_content);
    }
}