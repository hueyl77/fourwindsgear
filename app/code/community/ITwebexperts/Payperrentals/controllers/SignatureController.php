<?php
class ITwebexperts_Payperrentals_SignatureController extends Mage_Core_Controller_Front_Action
{

//    function _construct()
//    {
//        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
//            Mage::getSingleton('customer/session')->authenticate($this);
//            return;
//        }
//    }

    /**
     * Check order view availability
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  bool
     */
    protected function _canViewOrder($order)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $availableStates = Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, $strict = true)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Try to load valid order by order_id and register it
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadValidOrder($orderId = null)
    {
        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('order_id');
        }
        if (!$orderId) {
            $this->_forward('noRoute');
            return false;
        }

        $order = Mage::getModel('sales/order')->load($orderId);

        if ($this->_canViewOrder($order)) {
            Mage::register('current_order', $order);
            return true;
        } else {
            $this->_redirect('*/*/history');
        }
        return false;
    }

    function signAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    function contractAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }
        $orderid = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderid);
        if(!Mage::registry('current_order')){
        Mage::register('current_order',$order);
        }
        Mage::getModel('payperrentals/contractpdf')->renderContract($order);
    }

    function downloadAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }
        $orderid = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderid);
        if(!Mage::registry('current_order')){
            Mage::register('current_order',$order);
        }
        Mage::getModel('payperrentals/contractpdf')->renderContract($order, 'D');
    }

    public function submitAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }
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
        $filename = $contractModel->getSignatureFilename($order);
        Mage::helper('payperrentals')->uploadFileandCreateDir($path,$signatureimage,$filename);
        $order->save();
        Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Signature has been saved to the rental contract successfully'));
        $this->_redirect('customer/account/index');
    }

}