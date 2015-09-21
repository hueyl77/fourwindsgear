<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Reservationpricesdates extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_reservationpricesdates';
        $this->_blockGroup = 'payperrentals';
        $this->_headerText = Mage::helper('payperrentals')->__('Reservation Prices By Dates & Times');
        parent::__construct();

    }
}