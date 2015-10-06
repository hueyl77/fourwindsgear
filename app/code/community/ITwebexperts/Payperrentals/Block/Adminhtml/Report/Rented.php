<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Report_Rented extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_report_rented';
        $this->_blockGroup = 'payperrentals';
        $this->_headerText = Mage::helper('reports')->__('Most Rented Products');
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