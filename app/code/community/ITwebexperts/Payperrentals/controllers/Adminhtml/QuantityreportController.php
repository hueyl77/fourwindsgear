<?php

/**
 * Class ITwebexperts_Payperrentals_Adminhtml_QuantityreportController
 */
class ITwebexperts_Payperrentals_Adminhtml_QuantityreportController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/payperrentals/reports/qreport');
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->loadLayout()
            //->_addContent($this->getLayout()->createBlock('payperrentals/adminhtml_quantityreport'))
            ->renderLayout();
    }

    /**
     *
     */
    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        try {
            if (empty($post)) {
                Mage::throwException($this->__('Invalid form data'));
            }

            /* here's your form processing */

            $message = $this->__('Your form has been submitted successfully.');
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*');
    }
}