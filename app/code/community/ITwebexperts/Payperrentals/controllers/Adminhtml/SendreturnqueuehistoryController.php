<?php
/**
 * Class ITwebexperts_Payperrentals_Adminhtml_SendreturnqueuehistoryController
 */
class ITwebexperts_Payperrentals_Adminhtml_SendreturnqueuehistoryController
    extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/payperrentals/rentalqueue/srqueuehistory');
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
    public function massPrintAction()
    {
        $prepareKey = $this->getRequest()->getParam('massaction_prepare_key');
        $ids = $this->getRequest()->getParam($prepareKey);

        $labelType = $this->getRequest()->getParam('labelType');
        $printMethod = $this->getRequest()->getParam('printMethod');
        $row_start = $this->getRequest()->getParam('row_start');
        $col_start = $this->getRequest()->getParam('col_start');

        $labelInfo = array(
            'xmlData' => file_get_contents('js/itwebexperts_payperrentals/labelPrinter/dymo_labels/' . $labelType . '.label'),
            'data' => array()
        );

        foreach (explode(',', $ids) as $label) {
            $barcodeType = 'Code128Auto';
            if ($label > 0) {
                $sendReturnId = Mage::getModel('payperrentals/rentalqueue')
                    ->load($label);
                $resOrder = Mage::getModel('payperrentals/sendreturn')->load($sendReturnId->getSendreturnId());
                $snArr = explode(',', $resOrder->getSn());

                $customer = Mage::getModel('customer/customer')->load($resOrder->getCustomerId());
                /** @var $address Mage_Customer_Model_Address */
                $address = Mage::getModel('customer/address')->load($customer->getDefaultShipping());

                /** replace \n and blank space to avoid indenting in pdf */
                $addressFormated = str_replace("\n","",$address->format('html_special'));
                $re = "/(<br\\s?\\/>)\\s*/";
                $addressFormated = preg_replace($re,'$1',$addressFormated);

                /** regex remove trailing <br/> */
                $re = "/<br\\/>\\z/";
                $addressFormated = preg_replace($re,'',$addressFormated);

                $product = Mage::getModel('catalog/product')->load($resOrder->getProductId());
                $productName = $product->getName();
                $productDescription = $product->getDescription();

                foreach ($snArr as $sn) {
                    $labelInfo['data'][] = array(
                        'ProductsName' => $productName,
                        'Barcode' => $sn,
                        'BarcodeType' => $barcodeType,
                        'ProductsDescription' => $productDescription,
                        'Address' => $sn . "\n\n" . $addressFormated,
                        'products_name' => $productName,
                        'barcode' => $sn,
                        'barcode_type' => $barcodeType,
                        'products_description' => $productDescription,
                        'customers_address' => $addressFormated
                    );
                }
            }
        }

        if ($printMethod == 'dymo') {
            $html = array(
                'labelInfo' => $labelInfo
            );
            $this
                ->getResponse()
                ->setBody(Zend_Json::encode($html));
        } else if ($printMethod == 'pdf') {
            Mage::helper('payperrentals/labels')->setData($labelInfo['data']);
            Mage::helper('payperrentals/labels')->setLabelsType($labelType);
            Mage::helper('payperrentals/labels')->setStartLocation($row_start, $col_start);
            Mage::helper('payperrentals/labels')->buildPDF();

        } else {
            $csv = new Varien_File_Csv();
            $sepString = $this->getRequest()->getParam('field_separator');
            $sep = ';';
            switch ($sepString) {
                case 'tab'       :
                    $sep = '	';
                    break;
                case 'semicolon' :
                    $sep = ';';
                    break;
                case 'colon'     :
                    $sep = ':';
                    break;
                case 'comma'     :
                    $sep = ',';
            }

            if ($sep) {
                $csv->setDelimiter($sep);
            }

            $io = new Varien_Io_File();
            $path = Mage::getBaseDir('var') . DS . 'export' . DS; //best would be to add exported path through config
            $name = md5(microtime());
            $file = $path . DS . $name . '.csv';
            /**
             * It is possible that you have name collision (summer/winter time +1/-1)
             * Try to create unique name for exported .csv file
             */
            while (file_exists($file)) {
                sleep(1);
                $name = md5(microtime());
                $file = $path . DS . $name . '.csv';
            }
            $io->setAllowCreateFolders(true);
            $io->open(array('path' => $path));
            $io->streamOpen($file, 'w+');
            $io->streamLock(true);
            $headers = array(
                'ProductsName',
                'Barcode',
                'BarcodeType',
                'ProductsDescription',
                'Address',
                'products_name',
                'barcode',
                'barcode_type',
                'products_description',
                'customers_address'
            );
            $io->streamWriteCsv($headers, $sep);
            foreach ($labelInfo['data'] as $row) {
                $io->streamWriteCsv($row, $sep);
            }

            $io->streamUnlock();
            $io->streamClose();


            //$csv->saveData($file, $labelInfo['data']);

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"labelSpreadsheet.csv\";");
            header("Content-Transfer-Encoding: binary");
            //echo $io->streamReadCsv($sep);
            echo file_get_contents($file);
            //echo $csv->getData($file);
            die();
        }
    }

    /**
     *
     */
    public function exportCsvAction()
    {
        $fileName = 'sendreturnqueue.csv';
        $content = $this->getLayout()->createBlock('payperrentals/adminhtml_html_sendreturnqueuegrid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     *
     */
    public function exportXmlAction()
    {
        $fileName = 'sendreturn.xml';
        $content = $this->getLayout()->createBlock('payperrentals/adminhtml_html_sendreturnqueuegrid')
            ->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * @param $fileName
     * @param $content
     * @param string $contentType
     */
    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
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

