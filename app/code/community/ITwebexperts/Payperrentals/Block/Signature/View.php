<?php
class ITwebexperts_Payperrentals_Block_Signature_View extends Mage_Core_Block_Template
{
    public function getOrderid(){
        return $this->getRequest()->getParam('order_id');
    }


    public function getOrderNumber(){
        return$this->getOrder()->getIncrementId();
    }

    public function getOrder(){
        return Mage::getModel('sales/order')->load($this->getOrderid());
    }

    public function getFormaction(){
        return Mage::getUrl('payperrentals_front/signature/submit');
    }

    public function getContractUrl(){
        return Mage::getUrl('payperrentals_front/signature/contract',array(
            'order_id'=>$this->getRequest()->getParam('order_id')
        ));
    }

    public function getContractPdf(){
        return Mage::getUrl('payperrentals_front/signature/download',array(
            'order_id'=>$this->getRequest()->getParam('order_id')
        ));
    }
}