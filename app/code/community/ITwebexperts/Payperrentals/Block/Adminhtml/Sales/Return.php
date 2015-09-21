<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Return extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_sales_return';
        $this->_blockGroup = 'payperrentals';
        $this->_headerText = Mage::helper('sales')->__('Return History');
        parent::__construct();
    }
}
