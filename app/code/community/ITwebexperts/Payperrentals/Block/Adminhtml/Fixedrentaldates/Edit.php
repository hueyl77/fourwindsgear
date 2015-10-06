<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Fixedrentaldates_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct(){
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'payperrentals';
        $this->_controller = 'adminhtml_fixedrentaldates';
        $this->_mode = 'edit';

        $this->_updateButton('save', 'label', Mage::helper('payperrentals')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('payperrentals')->__('Delete'));
    }

    public function getHeaderText(){
        return Mage::helper('payperrentals')->__('Editing Fixed Rental Dates');
    }
}