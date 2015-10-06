<?php
require_once(BP . DS . 'lib' . DS . 'tcpdf' . DS . 'config'. DS . 'tcpdf_config_mage.php');
require_once(BP . DS . 'lib' . DS . 'tcpdf' . DS . 'tcpdf.php');

class ITwebexperts_Payperrentals_Model_Contractpdf extends Mage_Core_Model_Abstract
{
    public function getFilePath(){
        return Mage::getBaseDir('media') . DS . 'pdfs' . DS;
    }

    /**
     * Return logo URL for emails
     * Take logo from skin if custom logo is undefined
     *
     * @param  Mage_Core_Model_Store|int|string $store
     * @return string
     */
    protected function _getLogoUrl($store)
    {
        $store = Mage::app()->getStore($store);
        $fileName = $store->getConfig(Mage_Core_Model_Email_Template::XML_PATH_DESIGN_EMAIL_LOGO);
        if ($fileName) {
            $uploadDir = Mage_Adminhtml_Model_System_Config_Backend_Email_Logo::UPLOAD_DIR;
            $fullFileName = Mage::getBaseDir('media') . DS . $uploadDir . DS . $fileName;
            if (file_exists($fullFileName)) {
                return Mage::getBaseUrl('media') . $uploadDir . '/' . $fileName;
            }
        }
        return Mage::getDesign()->getSkinUrl('images/logo_email.gif');
    }

    public function getContractFilename($order){
        return 'contract_' . $order->getIncrementId() . '.pdf';
    }

    public function getSignaturePath(){
        return $this->getFilePath() . 'signatures';
    }

    public function getSignatureFilepath($order){
        return $this->getSignaturePath() . DS . $order->getSignatureImage();
    }

    public function getSignatureFilename($order){
        return 'contract_' . $order->getIncrementId() . '.svg';
    }

    /**
     * Render a PDF file and show in browser or save to disk
     * If save to disk return file location
     *
     * Template is at app/design/frontend/base/default/template/payperrentals/contractpdf/pdf.phtml
     */

    public function renderContract($order, $destination = NULL){
        $storeid = $order->getStoreId();
        if ($storeid) {
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $initial = $appEmulation->startEnvironmentEmulation(
                $storeid, Mage_Core_Model_App_Area::AREA_FRONTEND, true
            );
        }

        $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true);
        $paymentBlock->getMethod()->setStore($storeid);
        $paymentBlockHtml = $paymentBlock->toHtml();


        $pdf = new TCPDF();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->startPage();
        $pdf->setImageScale(1.53);
        $vars['logourl'] = $this->_getLogoUrl($storeid);
        $vars['stylesheet'] = '<link rel="stylesheet" type="text/css" href="' .  Mage::getBaseUrl('skin') . 'frontend/base/default/itwebexperts_payperrentals/css/contract.css">';
        $vars['contracttitle'] = Mage::getStoreConfig('payperrentals/contract/contract_title');
        $vars['order'] = $order;
        $vars['customerfirst'] = $order->getCustomerFirstname();
        $vars['customerlast'] = $order->getCustomerLastname();
        $vars['billingaddress'] = $order->getBillingAddress()->format('html');
        if(Mage::helper('payperrentals/config')->removeShipping()) {
            $vars['shippingaddress'] = $order->getBillingAddress()->format('html');
        }else{
            $vars['shippingaddress'] = $order->getShippingAddress()->format('html');
        }
        $vars['billingcompany'] = $order->getBillingAddress()->getCompany();
        if(Mage::helper('payperrentals/config')->removeShipping()) {
            $vars['shippingcompany'] = $order->getBillingAddress()->getCompany();
        }else{
            $vars['shippingcompany'] = $order->getShippingAddress()->getCompany();
        }
        $vars['payment_html'] = $paymentBlockHtml;
        $countrycode = Mage::getStoreConfig('general/store_information/merchant_country');
        if($countrycode) {
            $vars['store_country'] = Mage::getModel('directory/country')->loadByCode($countrycode)->getName();
        }
        $vars['show_signature'] = Mage::getStoreConfig('payperrentals/contract/signature');
        if($vars['show_signature'] == 0){$vars['show_signature'] = NULL;}

        $processor = Mage::getModel('core/email_template');

        $terms = Mage::getStoreConfig('payperrentals/contract/terms');
        $processor->setTemplateText($terms);
        $terms = nl2br($processor->getProcessedTemplate($vars));
        $vars['terms'] = $terms;

        $headertext = Mage::getStoreConfig('payperrentals/contract/headertext') . '<br />';
        $processor->setTemplateText($headertext);
        $headertext = nl2br($processor->getProcessedTemplate($vars)) . '<br />';
        $vars['headertext'] = $headertext;
        if(Mage::helper('payperrentals/config')->enabledDigitalSignature() && $order->getSignatureImage() != null) {
            $vars['show_digitalsignature'] = 1;
        } else {$vars['show_digitalsignature'] = null;}

        $footertext = Mage::getStoreConfig('payperrentals/contract/footertext');
        $processor->setTemplateText($footertext);
        $footertext = '<br />' . nl2br($processor->getProcessedTemplate($vars));
        $vars['footertext'] = $footertext;
        $vars['date'] = Mage::helper('payperrentals/date')->formatDbDate($order->getSignatureDate(),true,true);
        $vars['digitalsignature'] = $this->getSignatureFilepath($order);
        $contractpdftemplate = Mage::app()->getLayout()->createBlock('core/template')
            ->setTemplate('payperrentals/contractpdf/pdf.phtml')->toHtml();
        $processor->setTemplateText($contractpdftemplate);
        $contracthtml = $processor->getProcessedTemplate($vars);


        $tagvs = array(
            'p' =>
                array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0)),
            'h6' =>
                array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0)),
            'h3' =>
                array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0))
        );
        $pdf->setHtmlVSpace($tagvs);
//        echo $contracthtml;
//        die();
        $pdf->writeHTML($contracthtml, false);
        $pdf->endPage();
        if ($storeid) {
            $appEmulation->stopEnvironmentEmulation($initial);
        }
        $orderfilename = $this->getContractFilename($order);
        $filePath = $this->getFilePath() . $orderfilename;
        Mage::helper('payperrentals')->uploadFileandCreateDir($this->getFilePath());
        if(!$destination) {
            return $pdf->Output($orderfilename);
        } else if ($destination == 'F'){
            $pdf->Output($filePath, 'F');
            return $filePath;
        } else if ($destination == 'D'){
            return $pdf->Output($orderfilename, 'D');
        }
        exit;
    }
}