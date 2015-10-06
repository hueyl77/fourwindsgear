<?php
/**
 * Class ITwebexperts_Payperrentals_Adminhtml_SerialreportController
 */
class ITwebexperts_Payperrentals_Adminhtml_SerialreportController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/payperrentals/reports/serialreport');
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
    public function getSerialeventsAction()
    {
        $json = array();

        $values = array();

        $values[] = array('from' => '/Date(1344357102000)/', 'to' => '/Date(1344357102000)/', 'desc' => '<b>Task #</b>1<br><b>Data</b>: [2011-02-02 09:00:00 - 2011-02-02 12:30:00] ', 'customClass' => 'ganttRed');
        $json[] = array('name' => 'Aadil', 'desc' => 'Progam', 'values' => $values);
        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($json));
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