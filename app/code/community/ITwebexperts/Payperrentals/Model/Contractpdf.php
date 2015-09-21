<?php
require_once(BP . DS . 'lib' . DS . 'tcpdf' . DS . 'config'. DS . 'tcpdf_config_mage.php');
require_once(BP . DS . 'lib' . DS . 'tcpdf' . DS . 'tcpdf.php');

class ITwebexperts_Payperrentals_Model_Contractpdf extends Mage_Core_Model_Abstract
{
    const XML_PATH_DESIGN_EMAIL_LOGO            = 'design/email/logo';

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
        $fileName = $store->getConfig(Mage_Core_Model_Email_Template_Abstract::XML_PATH_DESIGN_EMAIL_LOGO);
        if ($fileName) {
            $uploadDir = Mage_Adminhtml_Model_System_Config_Backend_Email_Logo::UPLOAD_DIR;
            $fullFileName = Mage::getBaseDir('media') . DS . $uploadDir . DS . $fileName;
            if (file_exists($fullFileName)) {
                return Mage::getBaseUrl('media') . $uploadDir . '/' . $fileName;
            }
        }
        return Mage::getDesign()->getSkinUrl('images/logo_email.gif');
    }

    /**
     * Render a PDF file and show in browser or save to disk
     * If save to disk return file location
     *
     * Template is at app/design/frontend/base/default/template/payperrentals/contractpdf/pdf.phtml
     */

    public function renderContract($order, $saveToDisk = false){
        $storeid = $order->getStoreId();
        if ($storeid) {
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $initial = $appEmulation->startEnvironmentEmulation(
                $storeid, Mage_Core_Model_App_Area::AREA_FRONTEND, true
            );
        }


        $pdf = new TCPDF();
        $pdf->startPage();
        $pdf->setImageScale(1.53);
        $logourl = $this->_getLogoUrl($storeid);

        $style = '<link rel="stylesheet" type="text/css" href="' .  Mage::getBaseUrl('skin') . 'frontend/base/default/itwebexperts_payperrentals/css/contract.css">';

//        $styleNoninline = '<link rel="stylesheet" type="text/css" href="' .  Mage::getBaseUrl('skin') . 'frontend/base/default/css/email-non-inline.css">';

        $contracthtml = '<html><head>'  . $style . '</head><body>';
        $contracthtml .= '<img src="' . $logourl . '">';
        $title = '<h3>' . Mage::getStoreConfig('payperrentals/contract/contract_title') . '</h3>';
        $contracthtml .= $title;

        $vars['order'] = $order;
        $countrycode = Mage::getStoreConfig('general/store_information/merchant_country');
        $vars['store_country'] = Mage::getModel('directory/country')->loadByCode($countrycode)->getName();


        $processor = Mage::getModel('core/email_template');

        $terms = Mage::getStoreConfig('payperrentals/contract/terms');
        $processor->setTemplateText($terms);
        $terms = nl2br($processor->getProcessedTemplate($vars));
        $vars['terms'] = $terms;

        $headertext = Mage::getStoreConfig('payperrentals/contract/headertext') . '<br />';
        $processor->setTemplateText($headertext);
        $headertext = nl2br($processor->getProcessedTemplate($vars)) . '<br />';
        $vars['headertext'] = $headertext;

        $footertext = Mage::getStoreConfig('payperrentals/contract/footertext');
        $processor->setTemplateText($footertext);
        $footertext = '<br />' . nl2br($processor->getProcessedTemplate($vars));
        $vars['footertext'] = $footertext;

//        $labelprops = 'colspan="3" align="right" style="padding:3px 9px"';
//        $valueprops = 'align="right" style="padding:3px 9px"';
//        $totals = Mage::app()->getLayout()
//            ->createBlock('sales/order_totals','order_totals')
//            ->setTemplate('sales/order/totals.phtml')
//            ->setLabelProperties($labelprops)
//            ->setValueProperties($valueprops);
//
//        /** @var $items Mage_Sales_Block_Order_Email_Items */
//        $items = Mage::app()->getLayout()->createBlock('sales/order_email_items','items')
//            ->setTemplate('payperrentals/email/order/items.phtml')
//            ->setChild('order_totals',$totals)
//            ->addItemRender('default','payperrentals/sales_order_items_renderer','email/order/items/order/default.phtml')
//            ->setData('order', $order);
//
//        $items = $items->toHtml() . '<br /><br />';

        $contractpdftemplate = Mage::app()->getLayout()->createBlock('core/template')
            ->setTemplate('payperrentals/contractpdf/pdf.phtml')->toHtml();
        $processor->setTemplateText($contractpdftemplate);
        $contractpdftemplate = $processor->getProcessedTemplate($vars);

        $contracthtml .=  $contractpdftemplate . '</body></html>';

        echo $contracthtml;
        die();

        $pdf->writeHTML($contracthtml, false);
        $pdf->endPage();
        if ($storeid) {
            $appEmulation->stopEnvironmentEmulation($initial);
        }
        $orderfilename = 'contract_' . $order->getId() . '.pdf';
        if(!$saveToDisk) {
            return $pdf->Output($orderfilename);
        } else if ($saveToDisk){
            $filePath = $this->getFilePath() . $orderfilename;
            $pdf->Output($filePath, 'F');
            return $filePath;
        }
        exit;
    }
}