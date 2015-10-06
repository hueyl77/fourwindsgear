<?php
class ITwebexperts_Payperrentals_Adminhtml_SignatureController extends Mage_Adminhtml_Controller_Action
{
    public function viewAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    public function successAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    public function signAction()
    {
        $date = Mage::getModel('core/date')->date('Y-m-d');
        $signature = $this->getRequest()->getParam('signaturecode');
        $signaturetext = $this->getRequest()->getParam('typedsignature');
        $signatureimage = $this->getRequest()->getParam('signatureimage');
        $signatureimage = str_replace('image/svg+xml,', '', $signatureimage);
        $orderid = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderid);
        $order->setSignatureDate($date);
        $order->setSignatureText($signaturetext);
        $order->setSignatureXynative($signature);

        /** upload image to media/pdfs/signatures */
        $contractModel = Mage::getModel('payperrentals/contractpdf');
        $path = $contractModel->getSignaturePath();
        $imagename = $contractModel->getSignatureFilename($order);
        $order->setSignatureImage($imagename);
        Mage::helper('payperrentals')->uploadFileandCreateDir($path,$signatureimage,$imagename);
        $order->save();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('contacts')->__('Signature has been saved to the rental contract successfully'));
        $this->_redirect('payperrentals_admin/adminhtml_signature/success', array(
            'order_id'=>$orderid
        ));
    }
}