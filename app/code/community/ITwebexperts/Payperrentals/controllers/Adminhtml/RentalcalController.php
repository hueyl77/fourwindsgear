<?php
class ITwebexperts_Payperrentals_Adminhtml_RentalcalController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/payperrentals/rentalcalendarmain/rentalcalendarbyorder');
    }

    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }

    public function byorderAction()
    {
//        $block = Mage::app()->getLayout()->createBlock('ITwebexperts_Payperrentals_Block_Adminhtml_Rentalcal');
//        $caltype = "byorder";
//        $block->assign($caltype);
        $caltype = '';
        Mage::register('caltype','byorder');
        $this->loadLayout()->renderLayout();
    }

    public function bystartAction()
    {
        $caltype = '';
        Mage::register('caltype','bystart');
        $this->loadLayout()->renderLayout();
    }
}