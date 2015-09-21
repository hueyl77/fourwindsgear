<?php
class ITwebexperts_Payperrentals_Adminhtml_RentalcontractController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return true;
    }

    public function generateAction(){
        $this->loadLayout();
        $orderid = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderid);
        Mage::register('current_order',$order);
        Mage::getModel('payperrentals/contractpdf')->renderContract($order);
    }
}