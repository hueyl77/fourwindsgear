<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Queuepopularity
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Queuepopularity extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     *
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_queuepopularity';
        $this->_blockGroup = 'payperrentals';
        $this->_headerText = Mage::helper('payperrentals')->__('Queue Popularity');
        parent::__construct();
        $this->_removeButton('add');
        $this->_removeButton('delete');
    }
}