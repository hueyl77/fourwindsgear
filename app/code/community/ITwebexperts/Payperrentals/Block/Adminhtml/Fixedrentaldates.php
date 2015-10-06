<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Fixedrentaldates extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_fixedrentaldates';
        $this->_blockGroup = 'payperrentals';
        $this->_headerText = Mage::helper('payperrentals')->__('Fixed Rental Dates');
        parent::__construct();

    }
}