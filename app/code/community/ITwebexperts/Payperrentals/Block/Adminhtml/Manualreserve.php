<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Manualreserve extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_manualreserve';
        $this->_blockGroup = 'payperrentals';
        $this->_headerText = Mage::helper('payperrentals')->__('Manually Reserve Inventory');
        parent::__construct();

    }
}