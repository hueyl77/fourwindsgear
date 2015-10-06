<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Returnlate extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_sales_returnlate';
        $this->_blockGroup = 'payperrentals';
        $this->_headerText = Mage::helper('sales')->__('Late Returns');
        parent::__construct();
        $this->_removeButton('add');
    }
}
