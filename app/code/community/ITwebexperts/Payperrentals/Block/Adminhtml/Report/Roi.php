<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Report_Roi extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_report_roi';
        $this->_blockGroup = 'payperrentals';
        $this->_headerText = Mage::helper('reports')->__('ROI Report');
        parent::__construct();
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'searchByGridAndFilterData(true)'
        ));
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/index', array('_current' => true));
    }
}