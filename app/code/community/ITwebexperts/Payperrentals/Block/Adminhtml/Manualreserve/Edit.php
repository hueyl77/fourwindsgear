<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Manualreserve_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct(){
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'payperrentals';
        $this->_controller = 'adminhtml_manualreserve';
        $this->_mode = 'edit';

        $this->_updateButton('save', 'label', Mage::helper('payperrentals')->__('Save Reservation'));
        $this->_updateButton('delete', 'label', Mage::helper('payperrentals')->__('Delete Reservation'));
        /** don't show delete button if reservation is for an order */
        $id = Mage::app()->getRequest()->getParam('id');
        $resitem = Mage::getModel('payperrentals/reservationorders')->load($id);

        if($resitem->getOrderId() != 0 && $resitem->getOrderId() != null){
            $this->_removeButton('delete');
        }
    }

    public function getHeaderText(){
        return Mage::helper('payperrentals')->__('Editing Reservation');
    }
}