<?php

/**
 * Class ITwebexperts_Payperrentals_Adminhtml_QuantityreportController
 */
class ITwebexperts_Payperrentals_Adminhtml_FixedreportController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/payperrentals/reservations/fixedreport');
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }

    /**
     *
     */
    public function exportCsvAction()
    {
        $fileName   = 'fixed.csv';
        $content    = $this->getLayout()->createBlock('payperrentals/adminhtml_html_fixedgrid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     *
     */
    public function exportXmlAction(){
        $fileName   = 'fixed.xml';
        $content    = $this->getLayout()->createBlock('payperrentals/adminhtml_html_fixedgrid')
            ->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * @param $fileName
     * @param $content
     * @param string $contentType
     */
    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
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