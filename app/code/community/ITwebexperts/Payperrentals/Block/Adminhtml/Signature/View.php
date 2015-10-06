<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Signature_View extends Mage_Adminhtml_Block_Template
{
    public function getOrderid(){
        return $this->getRequest()->getParam('order_id');
    }

    public function getOrder(){
        return Mage::getModel('sales/order')->load($this->getOrderid());
    }

    public function getOrderNumber(){
        return$this->getOrder()->getIncrementId();
    }

    public function getFormaction(){
        return Mage::helper("adminhtml")->getUrl('payperrentals_admin/adminhtml_signature/sign',array(
             'order_id'=>$this->getRequest()->getParam('order_id')
        ));
    }
}