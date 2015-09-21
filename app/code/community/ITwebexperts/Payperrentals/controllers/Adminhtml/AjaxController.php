<?php
/** used for vendor marketplace module - needs vendor controller */
if(Mage::helper('itwebcommon')->isVendorInstalled() && preg_match('/vendors/',Mage::helper('core/url')->getCurrentUrl())){
       class ITwebexperts_Payperrentals_Adminhtml_AjaxControllerExtender extends VES_Vendors_Controller_Action
   {}
} else {
       class ITwebexperts_Payperrentals_Adminhtml_AjaxControllerExtender extends Mage_Adminhtml_Controller_Action
    {}
}

/**
 * Class ITwebexperts_Payperrentals_Adminhtml_AjaxController
 */
class ITwebexperts_Payperrentals_Adminhtml_AjaxController extends ITwebexperts_Payperrentals_Adminhtml_AjaxControllerExtender
{

    protected function _isAllowed(){
        return true;
    }

    /**
     * @param $nr
     * @return string
     */
    protected function unique_id($nr)
    {
        $better_token = md5(uniqid(rand(), true));
        $unique_code = substr($better_token, $nr);
        return $unique_code;
    }

    /**
     *
     */
    public function printLabelsAction()
    {

        $labelType = $this->getRequest()->getParam('labelType');
        $printMethod = $this->getRequest()->getParam('printMethod');
        $row_start = $this->getRequest()->getParam('row_start');
        $col_start = $this->getRequest()->getParam('col_start');
        $labelids = $this->getRequest()->getParam('labelids');

        $labelInfo = array(
            'xmlData' => file_get_contents(Mage::getBaseUrl() . 'js/itwebexperts_payperrentals/labelPrinter/dymo_labels/' . $labelType . '.label'),
            'data' => array()
        );

        foreach ($labelids as $label) {
            $barcodeType = 'Code128Auto';
            if ($label > 0) {
                $resOrder = Mage::getModel('payperrentals/sendreturn')->load($label);
                $snArr = explode(',', $resOrder->getSn());

                $customer = Mage::getModel('customer/customer')->load($resOrder->getCustomerId());
                /** @var $address Mage_Customer_Model_Address */
                $address = Mage::getModel('customer/address')->load($customer->getDefaultShipping());
                $addressFormated = $address->format('html_special');
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
                    $sep = '    ';
                case 'semicolon' :
                    $sep = ';';
                case 'colon'     :
                    $sep = ':';
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
    public function getGridReservationHtmlAction()
    {
        $htmlGrid = $this->getLayout()
            ->createBlock('payperrentals/adminhtml_html_sendreturngridproduct', 'my.payperrentals.sendreturngridproduct')
            ->toHtml();
        $html = array(
            'html' => $htmlGrid
        );

        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($html));
    }

    /**
     *
     */
    public function getGridReservationCustomerHtmlAction()
    {
        $htmlGrid = $this->getLayout()
            ->createBlock('payperrentals/adminhtml_html_sendreturngridcustomer', 'my.payperrentals.sendreturngridproduct')
            ->toHtml();
        $html = array(
            'html' => $htmlGrid
        );

        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($html));
    }

    /**
     *
     */
    public function getProductSkuAction()
    {
        $productId = $this->getRequest()->getParam('productId');
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $results = array();

        $categoryIds = $product->getCategoryIds();
        foreach ($categoryIds as $categoryId) {
            /** @var $category Mage_Catalog_Model_Category */
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $nr = ($category->getProductCount() + 1) . '';
            $nr = str_pad($nr, 6, "0", STR_PAD_LEFT);
            $results[] = strtoupper(substr($category->getName(), 0, 3)) . '-' . $nr;
        }


        $this->getResponse()->setBody(Zend_Json::encode($results));
    }

    /**
     *
     */
    public function getProductsAction()
    {
        $query = $this->getRequest()->getParam('value');
        $_store = Mage::getSingleton('adminhtml/session_quote')->getStore();

        $results = array();

        $coll = Mage::getModel('catalog/product')
            ->getCollection()
            ->addStoreFilter($_store)
            ->addAttributeToSelect('name')
            ->addAttributeToFilter(
                'status',
                array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            )
            ->addAttributeToFilter(
                array(
                    array(
                        'attribute' => 'name',
                        'like' => '%' . $query . '%'
                    ),
                    array(
                        'attribute' => 'sku',
                        'like' => '%' . $query . '%'
                    )
                ));

        foreach ($coll as $item) {
            $results[] = $item->getName();
        }

        $html = array(
            'suggestions' => $results
        );
        $this->getResponse()->setBody(Zend_Json::encode($html));
    }

    /**
     *
     */
    public function getSerialNumbersAction()
    {
        $query = $this->getRequest()->getParam('value');
        $productId = $this->getRequest()->getParam('productId');
        $results = array();
        $collectionSerialNumbers = Mage::getModel('payperrentals/serialnumbers')
            ->getCollection()
            ->addEntityIdFilter($productId)
            ->addSelectFilter("sn like '%" . $query . "%' AND (status = 'A')");

        foreach ($collectionSerialNumbers as $item) {
            $results[] = $item->getSn();
        }
        $html = array(
            'suggestions' => $results
        );
        $this->getResponse()->setBody(Zend_Json::encode($html));

    }

    /**
     *
     */
    public function getAllSerialsandStatusAction()
    {
        $query = $this->getRequest()->getParam('value');
        $productId = $this->getRequest()->getParam('productId');
        $results = array();
        $collectionSerialNumbers = Mage::getModel('payperrentals/serialnumbers')
            ->getCollection()
            ->addEntityIdFilter($productId);

        foreach ($collectionSerialNumbers as $item) {
            $results[] = array(
                'serial'=>$item->getSn(),
                'status'=>Mage::getModel('payperrentals/serialnumbers')->statusToText($item->getStatus())
            );
        }
        $html = array(
            'suggestions' => $results
        );
        $this->getResponse()->setBody(Zend_Json::encode($html));

    }

    /**
     *
     */
    public function getSerialNumbersbyItemIdAction()
    {
        $query = $this->getRequest()->getParam('value');
        $productId = $this->getRequest()->getParam('productId');
        $startDate = $this->getRequest()->getParam('startDate');
        $endDate = $this->getRequest()->getParam('endDate');

        $results = array();
        $filteredSerials = array();
        if(!is_null($startDate) && !is_null($endDate) && $startDate != '' && $endDate != '') {
            $collFilters = Mage::getModel('payperrentals/sendreturn')
                ->getCollection()
                ->addSelectFilter("product_id = '" . $productId . "'")
                ->addSelectFilter(
                    "send_date <= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($startDate)
                    . "' AND res_enddate >= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($endDate) . "'"
                )
                ->addSelectFilter("return_date = '" . '0000-00-00 00:00:00' . "'");
            foreach ($collFilters as $iSerial) {
                $filteredSerials = array_merge($filteredSerials, explode(',',$iSerial['sn']));
            }
        }

        $coll = Mage::getModel('payperrentals/serialnumbers')
            ->getCollection()
            ->addEntityIdFilter($productId)
            ->addSelectFilter("NOT FIND_IN_SET(sn,'" . implode(',', $filteredSerials) . "')")
            ->addSelectFilter("sn like '%" . $query . "%' AND (status = 'A')");

        foreach ($coll as $item) {
            $results[] = $item->getSn();
        }
        $html = array(
            'suggestions' => $results
        );
        $this->getResponse()->setBody(Zend_Json::encode($html));
    }

    /**
     *
     */
    public function getSentSerialNumbersAction()
    {
        $query = $this->getRequest()->getParam('value');

        $results = array();

        $coll = Mage::getModel('payperrentals/serialnumbers')
            ->getCollection()
            ->addSelectFilter("sn like '%" . $query . "%' AND status='O'");

        foreach ($coll as $item) {
            $results[] = $item->getSn();
        }

        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($results));
    }


    /**
     *
     */
    public function returnSelectedAction()
    {
        $resids = $this->getRequest()->getParam('returnRes');
        $returnPerCustomer = array();
        foreach ($resids as $id) {
            $resOrder = Mage::getModel('payperrentals/sendreturn')->load($id);
            $sn = explode(',', $resOrder->getSn());
            $returnTime = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
            foreach ($sn as $serial) {
                Mage::getResourceSingleton('payperrentals/serialnumbers')
                    ->updateStatusBySerial($serial, 'A');
            }
            Mage::getResourceSingleton('payperrentals/sendreturn')
                ->updateReturndateById($id, $returnTime);

            if ($resOrder->getOrderId() != 0) {
                $order = Mage::getModel('sales/order')->load($resOrder->getOrderId());
                $order->setReturnDatetime($returnTime);
                $order->save();
            }
            $product = Mage::getModel('catalog/product')->load($resOrder->getProductId());
            if ($resOrder->getResStartdate() != '0000-00-00 00:00:00') {
                $returnPerCustomer[$order->getCustomerEmail()]['is_queue'] = false;
                $returnPerCustomer[$order->getCustomerEmail()][$resOrder->getOrderId()][$product->getId()]['name'] = $product->getName();
                $returnPerCustomer[$order->getCustomerEmail()][$resOrder->getOrderId()][$product->getId()]['serials'] = $resOrder->getSn();
                $returnPerCustomer[$order->getCustomerEmail()][$resOrder->getOrderId()][$product->getId()]['start_date'] = $resOrder->getResStartdate();
                $returnPerCustomer[$order->getCustomerEmail()][$resOrder->getOrderId()][$product->getId()]['end_date'] = $resOrder->getResEndDate();
                $returnPerCustomer[$order->getCustomerEmail()][$resOrder->getOrderId()][$product->getId()]['send_date'] = $resOrder->getSendDate();
                $returnPerCustomer[$order->getCustomerEmail()][$resOrder->getOrderId()][$product->getId()]['return_date'] = $returnTime;
            } else {
                $customerId = Mage::getModel('customer/customer')->load($resOrder->getCustomerId());

                $returnPerCustomer[$customerId->getEmail()]['is_queue'] = true;
                $returnPerCustomer[$customerId->getEmail()][$product->getId()]['name'] = $product->getName();
                $returnPerCustomer[$customerId->getEmail()][$product->getId()]['serials'] = $resOrder->getSn();
                $returnPerCustomer[$customerId->getEmail()][$product->getId()]['send_date'] = $resOrder->getSendDate();
                $returnPerCustomer[$customerId->getEmail()][$product->getId()]['return_date'] = $returnTime;
            }
        }
        ITwebexperts_Payperrentals_Helper_Emails::sendEmail('return', $returnPerCustomer);
        $error = '';

        $results = array(
            'error' => $error
        );
        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($results));
    }

    /**
     *
     */
    public function sendSelectedAction()
    {
        $sns = $this->getRequest()->getParam('sn');
        $resids = $this->getRequest()->getParam('sendRes');
        if (!$resids) {
            $results = array(
                'error' => Mage::helper('payperrentals')->__('Please select send items.')
            );
            $this->getResponse()->setBody(Zend_Json::encode($results));
            return $this;
        }
        $returnPerCustomer = array();
        $savedQtys = array();
        foreach ($resids as $id) {
            $resOrder = Mage::getModel('payperrentals/reservationorders')->load($id);
            $product = Mage::getModel('catalog/product')->load($resOrder->getProductId());
            $sn = array();
            if ($product->getPayperrentalsUseSerials()) {
                foreach ($sns as $sid => $serialArr) {
                    if ($sid == $id) {
                        foreach ($serialArr as $serial) {
                            if ($serial != '') {
                                //todo check if serial exists and has status A
                                $sn[] = $serial;
                            }
                        }
                    }
                }
                if (count($sn) < $resOrder->getQty()) {
                    $coll = Mage::getModel('payperrentals/serialnumbers')
                        ->getCollection()
                        ->addEntityIdFilter($resOrder->getProductId())
                        ->addSelectFilter("NOT FIND_IN_SET(sn, '" . implode(',', $sn) . "') AND status='A'");
                    $j = 0;
                    foreach ($coll as $item) {
                        $sn[] = $item->getSn();
                        if ($j >= $resOrder->getQty() - count($sn)) {
                            break;
                        }
                        $j++;
                    }

                }

                foreach ($sn as $serial) {
                    Mage::getResourceSingleton('payperrentals/serialnumbers')
                        ->updateStatusBySerial($serial, 'O');
                }
            }
            $serialNumber = implode(',', $sn);
            $sendTime = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
            $sendReturn = Mage::getModel('payperrentals/sendreturn')
                ->setOrderId($resOrder->getOrderId())
                ->setProductId($resOrder->getProductId())
                ->setResStartdate($resOrder->getStartDate())
                ->setResEnddate($resOrder->getEndDate())
                ->setSendDate($sendTime)
                //->setReturnDate('0000-00-00 00:00:00')
                ->setQty($resOrder->getQty())//here needs a check this should always be true
                ->setSn($serialNumber)
                ->save();

            Mage::getResourceSingleton('payperrentals/reservationorders')->updateSendReturnById($id, $sendReturn->getId());

            $order = Mage::getModel('sales/order')->load($resOrder->getOrderId());
            $order->setSendDatetime($sendTime);
            $order->save();
            $product = Mage::getModel('catalog/product')->load($sendReturn->getProductId());
            $returnPerCustomer[$order->getCustomerEmail()][$resOrder->getOrderId()][$product->getId()]['name'] = $product->getName();
            $returnPerCustomer[$order->getCustomerEmail()][$resOrder->getOrderId()][$product->getId()]['serials'] = $sendReturn->getSn();
            $returnPerCustomer[$order->getCustomerEmail()][$resOrder->getOrderId()][$product->getId()]['start_date'] = $sendReturn->getResStartdate();
            $returnPerCustomer[$order->getCustomerEmail()][$resOrder->getOrderId()][$product->getId()]['end_date'] = $sendReturn->getResEndDate();
            $returnPerCustomer[$order->getCustomerEmail()][$resOrder->getOrderId()][$product->getId()]['send_date'] = $sendReturn->getSendDate();
            //$returnPerCustomer[$order->getCustomerEmail()][$resOrder->getOrderId()][$product->getId()]['return_date'] = $resOrder->getReturnDate();
            /** Create shipment */
            if ($order->canShip()) {
                $savedQtys[$resOrder->getOrderItemId()] = $resOrder->getQty();
                $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
                $shipment->register();
                $shipment->setEmailSent(true);
                $shipment->getOrder()->setIsInProcess(true);
                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder())
                    ->save();
                $shipment->sendEmail();
            }
        }

        $error = '';

        $results = array(
            'error' => $error
        );
        $this->getResponse()->setBody(Zend_Json::encode($results));
        return $this;
    }

    /**
     *
     */
    public function sendSelectedQueueAction()
    {
        $sns = $this->getRequest()->getParam('sn');
        $resids = $this->getRequest()->getParam('sendRes');
        $excluded = array();
        foreach ($resids as $id) {
            $resOrder = Mage::getModel('payperrentals/rentalqueue')->load($id);
            $product = Mage::getModel('catalog/product')->load($resOrder->getProductId());
            $totalQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($product);
            $prodSentProduct = ITwebexperts_Payperrentals_Helper_Data::getSentItemsByProduct($resOrder->getProductId());
            $prodAllowed = Mage::helper('payperrentals/membership')->getNumberAllowedForMembership($resOrder->getCustomerId());
            $prodSent = ITwebexperts_Payperrentals_Helper_Data::getSentItemsForCustomer($resOrder->getCustomerId());
            $sn = array();
            if (($prodAllowed - $prodSent > 0) && ($totalQty >= 0)) {
                if ($product->getPayperrentalsUseSerials()) {
                    foreach ($sns as $sid => $serialArr) {
                        if ($sid == $id) {
                            foreach ($serialArr as $serial) {
                                if ($serial != '') {
                                    //todo check if serial exists and has status A
                                    $sn[] = $serial;
                                }
                            }
                        }
                    }
                    if (count($sn) < 1) {
                        $coll = Mage::getModel('payperrentals/serialnumbers')
                            ->getCollection()
                            ->addEntityIdFilter($resOrder->getProductId())
                            ->addSelectFilter("NOT FIND_IN_SET(sn, '" . implode(',', $sn) . "') AND status='A'");
                        $j = 0;
                        foreach ($coll as $item) {
                            $sn[] = $item->getSn();
                            if ($j >= 1 - count($sn)) {
                                break;
                            }
                            $j++;
                        }

                    }

                    foreach ($sn as $serial) {
                        Mage::getResourceSingleton('payperrentals/serialnumbers')
                            ->updateStatusBySerial($serial, 'O');
                    }
                }
                $serialNumber = implode(',', $sn);
                $orderId = Mage::helper('payperrentals/membership')->getOrderFromCustomer($resOrder->getCustomerId());
                $sendTime = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
                $sendReturn = Mage::getModel('payperrentals/sendreturn')
                    ->setOrderId($orderId)
                    ->setCustomerId($resOrder->getCustomerId())
                    ->setProductId($resOrder->getProductId())
                    /** commenting out dates because in public function formatDate ( $date, $includeTime = true)
                     * lib/Varien/Db/Adapter/Pdo/Mysql.php converting to timestamp causes 1970-01-01 dates but
                     * inserting no date makes it what we want 0000-00-00
                     */
                    //->setResStartdate('0000-00-00 00:00:00')
                    //->setResEnddate('0000-00-00 00:00:00')
                    ->setSendDate($sendTime)
                    //->setReturnDate('0000-00-00 00:00:00')
                    ->setQty(1)//here needs a check this should always be true
                    ->setSn($serialNumber)
                    ->save();

                Mage::getResourceSingleton('payperrentals/rentalqueue')->updateSendReturnById($id, $sendReturn->getId());
                Mage::dispatchEvent('send_queue_after', array(
                    'customerId' => $resOrder->getCustomerId(),
                    'productId' => $resOrder->getProductId(),
                    'orderId' => $orderId
                ));
                $customer = Mage::getModel('customer/customer')->load($resOrder->getCustomerId());
                $product = Mage::getModel('catalog/product')->load($sendReturn->getProductId());
                $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
                $returnPerCustomer[$customer->getEmail()][$product->getId()]['name'] = $product->getName();
                $returnPerCustomer[$customer->getEmail()][$product->getId()]['serials'] = $sendReturn->getSn();
                //$returnPerCustomer[$order->getCustomerEmail()][$product->getId()]['start_date'] = $sendReturn->getResStartdate();
                //$returnPerCustomer[$order->getCustomerEmail()][$product->getId()]['end_date'] = $sendReturn->getResEndDate();
                $returnPerCustomer[$customer->getEmail()][$product->getId()]['send_date'] = $sendTime;

                //
            } else {
                $excluded[] = $id;
            }
        }
        ITwebexperts_Payperrentals_Helper_Emails::sendEmail('send_queue', $returnPerCustomer);
        $error = '';

        $results = array(
            'error' => $error,
            'excluded' => $excluded
        );
        $this->getResponse()->setBody(Zend_Json::encode($results));
    }

    /**
     *
     */
    public function returnSelectedQueueAction()
    {
        $resids = $this->getRequest()->getParam('returnRes');
        /* @var $sendReturns ITwebexperts_Payperrentals_Model_Mysql4_Sendreturn_Collection */
        $sendReturns = Mage::getResourceModel('payperrentals/sendreturn_collection');
        $sendReturns->addFieldToFilter('id', array('in' => $resids));
        $returnTime = date('Y-m-d H:i:s', (int)Mage::getModel('core/date')->timestamp(time()));
        foreach ($sendReturns as $sendReturn) {
            $sendReturn->setReturnDate($returnTime);
            $serialNumbers = $sendReturn->getSn() ? explode(',', $sendReturn->getSn()) : array();
            foreach ($serialNumbers as $serial) {
                Mage::getResourceSingleton('payperrentals/serialnumbers')
                    ->updateStatusBySerial($serial, 'A');
            }
            $sendReturn->save();
        }
        $results = array(
            'customerUrl' => $this->getUrl('adminhtml/customer/edit', array('id' => $this->getRequest()->getParam('customer_id')))
        );
        $this->getResponse()->setBody(Zend_Json::encode($results));
    }

    /**
     * Filters dates for send & return items
     *
     * @param $date
     * @return bool|int|string
     */

    private function filterDate($date,$addOrSubtract){
        if ($date == '') {
            if ($addOrSubtract == 'subtract') {
                $date = strtotime("-1 year", time());
                return date('Y-m-d',$date);
            } else if ($addOrSubtract == 'add')
                $date = strtotime("+1 year", time());
                return date('Y-m-d',$date);
            }
        $date = new Zend_Date($date, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
        return $date->get('yyyy-MM-dd');
    }


    /**
     *
     */
    public function getSendItemsAction()
    {
        $startDatefrom = $this->filterDate($this->getRequest()->getParam('startDatefrom'),'subtract');
        $startDateto = $this->filterDate($this->getRequest()->getParam('startDateto'),'add');
        $endDatefrom = $this->filterDate($this->getRequest()->getParam('endDatefrom'),'subtract');
        $endDateto = $this->filterDate($this->getRequest()->getParam('endDateto'),'add');

        $sendItemsHtml = '<table class="data" cellspacing="0">
            <thead>
                <tr class="headings">
                    <th>' . Mage::helper('payperrentals')->__('Order Id - Customer Name') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Product Name') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Sku') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Start Date') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('End Date') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Send Date') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Return Date') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Quantity') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Serial Numbers') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Mark For Send') . '<br/>
                        <button class="sendSelected" type="button"><span>' . Mage::helper('payperrentals')->__('Send Selected') . '</span></button>
                    </th>
                </tr>
            </thead>
        ';
        $sendItemsHtml .= '<tbody>';
        $forStore = urldecode($this->getRequest()->getParam('forStore'));
        $coll = Mage::getModel('payperrentals/reservationorders')->getCollection()->getToSendReturnCollection($startDatefrom,$startDateto,$endDatefrom,$endDateto,$forStore);

        foreach ($coll as $item) {
            $sendItemsHtml .= '<tr id="resorder_' . $item->getId() . '">';

                $order = Mage::getModel('sales/order')->load($item->getOrderId());
                $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
                $orderIncId = $order->getIncrementId();
                $sendItemsHtml .= '<td>' . $orderIncId . ' - ' . $customerName . '</td>';

            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $productName = $product->getName();
            $sendItemsHtml .= '<td>' . $productName . '</td>';
            $sendItemsHtml .= '<td>' . Mage::getModel('catalog/product')->load($item->getProductId())->getSku() . '</td>';
            $sendItemsHtml .= '<td>' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($item->getStartDate()) . '</td>';
            $sendItemsHtml .= '<td>' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($item->getEndDate()) . '</td>';
            $sendItemsHtml .= '<td>' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($item->getSendDate()) . '</td>';
            $sendItemsHtml .= '<td>' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($item->getReturnDate()) . '</td>';

            $sendItemsHtml .= '<td>' . $item->getQty() . '</td>';
            if ($product->getPayperrentalsUseSerials()) {
                $sendItemsHtml .= '<td>';
                for ($j = 0; $j < $item->getQty(); $j++) {
                    $sendItemsHtml .= '<input type="text" name="sn[' . $item->getId() . '][]" class="sn" prid="' . $item->getProductId() . '" /><br/>';
                }
                $sendItemsHtml .= '</td>';
            } else {
                $sendItemsHtml .= '<td>&nbsp;';
                $sendItemsHtml .= '</td>';
            }
            $sendItemsHtml .= '<td><input type="checkbox" name="sendRes[]" class="sendRes" value="' . $item->getId() . '"/></td>';
            $sendItemsHtml .= '</tr>';
        }

        $sendItemsHtml .= '</tbody></table>';
        $html['html'] = $sendItemsHtml;
        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($html));
    }

    /**
     *  Used on Reservations > Send Return Report to ajax get items to be returned
     */

    public function getReturnItemsAction()
    {
        $startDatefrom = $this->filterDate($this->getRequest()->getParam('startDatefrom'),'subtract');
        $startDateto = $this->filterDate($this->getRequest()->getParam('startDateto'),'add');
        $endDatefrom = $this->filterDate($this->getRequest()->getParam('endDatefrom'),'subtract');
        $endDateto = $this->filterDate($this->getRequest()->getParam('endDateto'),'add');

        $returnItemsHtml = '<table class="data" cellspacing="0">
            <thead>
                <tr class="headings">
                    <th>' . Mage::helper('payperrentals')->__('Order Id - Customer Name') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Product Name') . '</th>
                       <th>' . Mage::helper('payperrentals')->__('Sku') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Start Date') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('End Date') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Send Date') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Quantity') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Serial Numbers') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Mark For Return') . '<br/>
                        <button class="returnSelected" type="button"><span>' . Mage::helper('payperrentals')->__('Return Selected') . '</span></button>
                    </th>
                </tr>
            </thead>
        ';
        $returnItemsHtml .= '<tbody>';

        $forStore = urldecode($this->getRequest()->getParam('forStore'));
        $coll = Mage::getModel('payperrentals/reservationorders')->getCollection()->getToSendReturnCollection($startDatefrom,$startDateto,$endDatefrom,$endDateto,$forStore,'return');

        foreach ($coll as $item) {
            $returnItemsHtml .= '<tr id="resorder_' . $item->getId() . '">';
            $order = Mage::getModel('sales/order')->load($item->getOrderId());
            $orderIncId = $order->getIncrementId();
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $returnItemsHtml .= '<td>' . $orderIncId . ' - ' . $customerName . '</td>';
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $productName = $product->getName();
            $returnItemsHtml .= '<td>' . $productName . '</td>';
            $returnItemsHtml .= '<td>' . Mage::getModel('catalog/product')->load($item->getProductId())->getSku() . '</td>';
            $returnItemsHtml .= '<td>' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($item->getStartDate()) . '</td>';
            $returnItemsHtml .= '<td>' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($item->getEndDate()) . '</td>';
            $returnItemsHtml .= '<td>' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($item->getSendDate()) . '</td>';

            $returnItemsHtml .= '<td>' . $item->getQty() . '</td>';
            $returnItemsHtml .= '<td>';
            $snArr = explode(',', $item->getSn());
            foreach ($snArr as $sn) {
                $returnItemsHtml .= $sn . '<br/>';
            }
            $returnItemsHtml .= '</td>';
            $returnItemsHtml .= '<td><input type="checkbox" name="returnRes[]" class="returnRes" value="' . $item->getSendreturnid() . '"/></td>';
            $returnItemsHtml .= '</tr>';
        }

        $returnItemsHtml .= '</tbody></table>';
        $html['html'] = $returnItemsHtml;
        $this->getResponse()->setBody(Zend_Json::encode($html));
    }

    /**
     *
     */
    public function getSendQueueItemsAction()
    {
        //todo ppr move all the returned items to a separate table just to keep the history and keep the tables cleaner?
        $collection = Mage::getResourceModel('sales/recurring_profile_collection')
            ->addFieldToFilter('state', 'active')
            ->addFieldToFilter('customer_id',array('neq'=>'null'));

        if (urldecode($this->getRequest()->getParam('forStore'))) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('forStore'));
        }

        $sendItemsHtml = '<table class="data" cellspacing="0">
            <thead>
                <tr class="headings">
                    <th>' . Mage::helper('payperrentals')->__('Customer Name') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Product Name') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Date Added') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Rentals Allowed') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Rentals Out') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Rentals Needed') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Serial Number') . '</th>';
        if(ITwebexperts_Payperrentals_Helper_Data::isDeliveryDatesInstalled()) {
            $sendItemsHtml .= '<th>' . Mage::helper('deliverydates')->__('Delivery Date') . '</th>';
        }
        $sendItemsHtml .= '<th>' . Mage::helper('payperrentals')->__('Mark For Send') . '<br/>
                        <button class="sendSelected" type="button"><span>' . Mage::helper('payperrentals')->__('Send Selected') . '</span></button>
                    </th>
                </tr>
            </thead>
        ';
        $sendItemsHtml .= '<tbody>';

        /**
         * Check for manual memberships
         * @var  $customersColl */
        $customersColl = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('*');
        //var_dump(count($customersColl));
        foreach($customersColl as $customer) {
            $customer_id = $customer->getId();
            $prodAllowed = Mage::helper('payperrentals/membership')->getNumberAllowedForMembershipManual($customer_id);
            $deliverydate = $customer->getDeliveryDate();
            $customerName = $customer->getName();

            if ($prodAllowed <= 0) {
                /**
                 * is a regular membership
                 * @var $coll
                 */
                foreach ($collection as $coll) {
                    $itemSerialized = unserialize($coll->getOrderItemInfo());
                    $orderSerialized = unserialize($coll->getOrderInfo());
                    if ($itemSerialized['product_type'] == 'membershippackage') {
                        $prodAllowed = Mage::helper('payperrentals/membership')->getNumberAllowedForMembership($customer_id);
                    }
                }
            }
            if ($prodAllowed > 0) {
                $prodSent = ITwebexperts_Payperrentals_Helper_Data::getSentItemsForCustomer($customer_id);
                if ($prodAllowed - $prodSent >= 0) {
                    $coll1 = Mage::getModel('payperrentals/rentalqueue')
                        ->getCollection()
                        ->addCustomerIdFilter($customer_id)
                        ->addSelectFilter('sendreturn_id = "0"')
                        ->addOrderFilter('date_added ASC')
                        ->addOrderFilter('sort_order ASC');

                    foreach ($coll1 as $item) {
                        $product = Mage::getModel('catalog/product')->load($item->getProductId());
                        /** check if product is part from a grouped product
                         * check if the customer has in queue another item which is part from
                         * the same grouped product and the position is a lower one and the item is unavailable
                         * I make an array of unavailable products(the problem is the season 3 might be in the list
                         * before season 2 and would be a problem, so the punctual check is better/although somehow
                         * in different cases someone might want season 3 before 2 :) */

                        $totalQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($product);
                        $prodSentProduct = ITwebexperts_Payperrentals_Helper_Data::getSentItemsByProduct($item->getProductId());
                        if (($prodAllowed - $prodSent >= 0) && ($prodSentProduct <= $totalQty)) {

                            $sendItemsHtml .= '<tr id="resqueue_' . $item->getId() . '">';
                            $productName = $product->getName();
                            $sendItemsHtml .= '<td>' . $customerName . '</td>';
                            $sendItemsHtml .= '<td>' . $productName . '</td>';
                            $sendItemsHtml .= '<td>' . Mage::helper('core')->formatDate($item->getDateAdded(), 'medium', false) . '</td>';
                            $sendItemsHtml .= '<td>' . $prodAllowed . '</td>';
                            $sendItemsHtml .= '<td>' . $prodSent . '</td>';
                            $sendItemsHtml .= '<td>' . ($prodAllowed - $prodSent) . '</td>';

                            //check if sn is enabled
                            $sendItemsHtml .= '<td>';
                            $sendItemsHtml .= '<input type="text" name="sn[' . $item->getId() . '][]" class="sn" prid="' . $item->getProductId() . '" /><br/>';
                            $sendItemsHtml .= '</td>';
                            if (ITwebexperts_Payperrentals_Helper_Data::isDeliveryDatesInstalled()) {
                                if ($deliverydate == null) {
                                    $deliverydate = '';
                                } else {
                                    $deliverydate = Mage::helper('payperrentals/date')->formatDbDate($deliverydate);
                                }
                                $sendItemsHtml .= '<td>' . $deliverydate . '</td>';
                            }
                            $sendItemsHtml .= '<td><input type="checkbox" name="sendRes[]" class="sendRes" value="' . $item->getId() . '"/></td>';
                            $sendItemsHtml .= '</tr>';
                        }
                    }
                }
            }
        }

        $sendItemsHtml .= '</tbody></table>';
        $html['html'] = $sendItemsHtml;
        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($html));
    }

    /**
     *
     */
    public function getRentalQueueSendAction()
    {
        //todo ppr move all the returned items to a separate table just to keep the history and keep the tables cleaner?
        $store_id = Mage::app()->getStore()->getId();
        $customer_id = $this->getRequest()->getParam('customer_id');
        $prodAllowed = Mage::helper('payperrentals/membership')->getNumberAllowedForMembership($customer_id);
        $prodSent = ITwebexperts_Payperrentals_Helper_Data::getSentItemsForCustomer($customer_id);

        $sendItemsHtml = '<table class="data" cellspacing="0">
            <thead>
                <tr class="headings">
                    <th colspan="3">' . Mage::helper('payperrentals')->__('Products allowed to be sent ') . ($prodAllowed - $prodSent) . '</th>
                </tr>
                <tr class="headings">
                    <th>' . Mage::helper('payperrentals')->__('Product Name') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Serial Number') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Mark For Send') . '<br/>
                        <button class="sendSelected" type="button"><span>' . Mage::helper('payperrentals')->__('Send Selected') . '</span></button>
                    </th>
                </tr>
            </thead>
        ';
        $sendItemsHtml .= '<tbody>';

        $coll = Mage::getModel('payperrentals/rentalqueue')
            ->getCollection()
            ->addCustomerIdFilter($customer_id)
            //->addStoreIdFilter($store_id)
            ->addSelectFilter('sendreturn_id = "0"')
            //->addOrderFilter('date_added ASC')
            ->addOrderFilter('sort_order ASC');

        foreach ($coll as $item) {
            $sendItemsHtml .= '<tr id="resqueue_' . $item->getId() . '">';
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $productName = $product->getName();
            $totalQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($product);
            $prodSentProduct = ITwebexperts_Payperrentals_Helper_Data::getSentItemsByProduct($item->getProductId());
            $sendItemsHtml .= '<td>' . $productName . '</td>';
            //check if sn is enabled
            $sendItemsHtml .= '<td>';
            $sendItemsHtml .= '<input type="text" name="sn[' . $item->getId() . '][]" class="sn" prid="' . $item->getProductId() . '" /><br/>';
            $sendItemsHtml .= '</td>';

            if (($prodAllowed - $prodSent > 0) && ($totalQty > 0)) {
                $sendItemsHtml .= '<td><input type="checkbox" name="sendRes[]" class="sendRes" value="' . $item->getId() . '"/></td>';
            }
            $sendItemsHtml .= '</tr>';
        }


        $sendItemsHtml .= '</tbody></table>';
        $html['html'] = $sendItemsHtml;
        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($html));
    }


    /**
     *
     */
    public function getReturnQueueItemsAction()
    {
        $startDate = $this->getRequest()->getParam('startDate');
        //if (!$startDate) {
        //  $startDate = Mage::getModel('core/date')->date('Y-m-d ', time());
        //}
        /*$zDate = new Zend_Date($startDate, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
        $startDate = $zDate->get('yyyy-MM-dd');*/
        $filter = "(res_startdate='0000-00-00 00:00:00' OR res_startdate='1970-01-01 00:00:00') AND (return_date='0000-00-00 00:00:00' OR return_date='1970-01-01 00:00:00')";
        if ($startDate != '') {
            /**
             * NOT SURE Why using res_startdate='0000-00-00 00:00:00' filter, because after ship order res_startdate = order_send_datetime
             * If using this filter function everywhere will return 0 items
             * */
            /*$filter = "send_date >= '" . ITwebexperts_Payperrentals_Helper_Data::toDbDate($startDate) . "' AND return_date='0000-00-00 00:00:00' AND res_startdate='0000-00-00 00:00:00'";*/
            $filter = "send_date >= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($startDate) . "' AND (return_date='0000-00-00 00:00:00' OR return_date='1970-01-01 00:00:00')";
        }
        $returnItemsHtml = '<table class="data" cellspacing="0">
            <thead>
                <tr class="headings">
                    <th>' . Mage::helper('payperrentals')->__('Customer Name') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Product Name') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Send Date') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Date Added') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Rentals Allowed') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Rentals Out') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Rentals Needed') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Serial Numbers') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Mark For Return') . '<br/>
                        <button class="returnSelected" type="button"><span>' . Mage::helper('payperrentals')->__('Return Selected') . '</span></button>
                    </th>
                </tr>
            </thead>
        ';
        $returnItemsHtml .= '<tbody>';

        $coll = Mage::getModel('payperrentals/sendreturn')
            ->getCollection()
            ->addSelectFilter($filter)
            ->orderByCustomerId();
        if (urldecode($this->getRequest()->getParam('forStore'))) {
            $coll->getSelect()->joinLeft(array('so' => Mage::getSingleton('core/resource')->getTableName('sales_flat_order')), 'main_table.order_id = ' . 'so.entity_id', array('so.store_id as store_id'));
            $coll->getSelect()->where('so.store_id=?', $this->getRequest()->getParam('forStore'));
        }

        $customerId = 0;
        foreach ($coll as $item) {
            $returnItemsHtml .= '<tr id="resorder_' . $item->getId() . '">';
            //if ($customerId != $item->getCustomerId()) {
            $customerId = $item->getCustomerId();
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $returnItemsHtml .= '<td>' . $customerName . '</td>';
            //} else {
            //    $returnItemsHtml .= '<td>' . '' . '</td>';
            // }

            /**TODO check rentals out and needed*/
            $queueCollection = Mage::getModel('payperrentals/rentalqueue')
                ->getCollection()
                ->addCustomerIdFilter($customerId)
                ->addFieldToFilter('sendreturn_id', $item->getId())
                ->addProductIdFilter($item->getProductId())
                ->addFieldToSelect(array('date_added'));
            if ($itemsOut = $queueCollection->getSize()) {
                $queueItem = $queueCollection->getFirstItem();
            } else {
                $queueItem = null;
            }
            $prodSent = ITwebexperts_Payperrentals_Helper_Data::getSentItemsForCustomer($customerId);
            $dateAdded = (is_null($queueItem)) ? '' : Mage::helper('core')->formatDate($queueItem->getDateAdded(), 'medium', false);
            $customerAllowedToQueue = Mage::helper('payperrentals/membership')->getNumberAllowedForMembership($customerId);

            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $productName = $product->getName();
            $returnItemsHtml .= '<td>' . $productName . '</td>';
            $returnItemsHtml .= '<td>' . Mage::helper('core')->formatDate($item->getSendDate(), 'medium', false) . '</td>';
            $returnItemsHtml .= '<td>' . $dateAdded . '</td>';

            $returnItemsHtml .= '<td>' . $customerAllowedToQueue . '</td>';
            $returnItemsHtml .= '<td>' . $prodSent . '</td>';
            $returnItemsHtml .= '<td>' . ($customerAllowedToQueue - $prodSent) . '</td>';

            $returnItemsHtml .= '<td>';
            $snArr = explode(',', $item->getSn());
            foreach ($snArr as $sn) {
                $returnItemsHtml .= $sn . '<br/>';
            }
            $returnItemsHtml .= '</td>';
            $returnItemsHtml .= '<td><input type="checkbox" name="returnRes[]" class="returnRes" value="' . $item->getId() . '"/></td>';
            $returnItemsHtml .= '</tr>';
        }

        $returnItemsHtml .= '</tbody></table>';
        $html['html'] = $returnItemsHtml;
        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($html));
    }

    /**
     *
     */
    public function getReturnBySerialQueueAction()
    {
        $snI = $this->getRequest()->getParam('sn');
        $returnItemsHtml = '<table class="data" cellspacing="0">
            <thead>
                <tr class="headings">
                    <th>' . Mage::helper('payperrentals')->__('Customer Name') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Product Name') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Send Date') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Return Date') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Serial Numbers') . '</th>
                </tr>
            </thead>
        ';
        $returnItemsHtml .= '<tbody>';

        $coll = Mage::getModel('payperrentals/sendreturn')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addSelectFilter("FIND_IN_SET('" . $snI . "', sn) AND return_date='0000-00-00 00:00:00' AND res_startdate='0000-00-00 00:00:00'")
            ->orderByCustomerId();
        $customerId = 0;
        foreach ($coll as $item) {


            $sn = explode(',', $item->getSn());
            $returnTime = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
            foreach ($sn as $serial) {
                Mage::getResourceSingleton('payperrentals/serialnumbers')
                    ->updateStatusBySerial($serial, 'A');
            }
            Mage::getResourceSingleton('payperrentals/sendreturn')
                ->updateReturndateById($item->getId(), $returnTime);

            if ($item->getOrderId() != 0) {
                $order = Mage::getModel('sales/order')->load($item->getOrderId());
                $order->setReturnDatetime($returnTime);
                $order->save();
            }
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if ($item->getResStartdate() != '0000-00-00 00:00:00') {
                $returnPerCustomer[$order->getCustomerEmail()]['is_queue'] = false;
                $returnPerCustomer[$order->getCustomerEmail()][$item->getOrderId()][$product->getId()]['name'] = $product->getName();
                $returnPerCustomer[$order->getCustomerEmail()][$item->getOrderId()][$product->getId()]['serials'] = $item->getSn();
                $returnPerCustomer[$order->getCustomerEmail()][$item->getOrderId()][$product->getId()]['start_date'] = $item->getResStartdate();
                $returnPerCustomer[$order->getCustomerEmail()][$item->getOrderId()][$product->getId()]['end_date'] = $item->getResEndDate();
                $returnPerCustomer[$order->getCustomerEmail()][$item->getOrderId()][$product->getId()]['send_date'] = $item->getSendDate();
                $returnPerCustomer[$order->getCustomerEmail()][$item->getOrderId()][$product->getId()]['return_date'] = $returnTime;
            } else {
                $customerId = Mage::getModel('customer/customer')->load($item->getCustomerId());
                $returnPerCustomer[$customerId->getEmail()]['is_queue'] = true;
                $returnPerCustomer[$customerId->getEmail()][$product->getId()]['name'] = $product->getName();
                $returnPerCustomer[$customerId->getEmail()][$product->getId()]['serials'] = $item->getSn();
                $returnPerCustomer[$customerId->getEmail()][$product->getId()]['send_date'] = $item->getSendDate();
                $returnPerCustomer[$customerId->getEmail()][$product->getId()]['return_date'] = $returnTime;
            }

            $returnItemsHtml .= '<tr id="resorder_' . $item->getId() . '">';
            //if ($customerId != $item->getCustomerId()) {
            $customerId = $item->getCustomerId();
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $returnItemsHtml .= '<td>' . $customerName . '</td>';
            // } else {
            //$returnItemsHtml .= '<td></td>';
            //}
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $productName = $product->getName();
            $returnItemsHtml .= '<td>' . $productName . '</td>';
            $returnItemsHtml .= '<td>' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($item->getSendDate()) . '</td>';
            $returnItemsHtml .= '<td>' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($returnTime) . '</td>';
            $returnItemsHtml .= '<td>';
            $snArr = explode(',', $item->getSn());
            foreach ($snArr as $sn) {
                $returnItemsHtml .= $sn . '<br/>';
            }
            $returnItemsHtml .= '</td>';
            $returnItemsHtml .= '</tr>';
        }
        ITwebexperts_Payperrentals_Helper_Emails::sendEmail('return', $returnPerCustomer);

        $returnItemsHtml .= '</tbody></table>';
        $html['html'] = $returnItemsHtml;
        $this->getResponse()->setBody(Zend_Json::encode($html));
    }



    /**
     *
     */
    public function getReturnBySerialAction()
    {
        $snI = $this->getRequest()->getParam('sn');

        $returnItemsHtml = '<table class="data" cellspacing="0">
            <thead>
                <tr class="headings">
                    <th>' . Mage::helper('payperrentals')->__('Order Id - Customer Name') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Product Name') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Send Date on Order') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Return Date on Order') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Real Send Date') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Quantity') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Serial Numbers') . '</th>
                    <th>' . Mage::helper('payperrentals')->__('Mark For Return') . '<br/>
                        <button class="returnSelected" type="button"><span>' . Mage::helper('payperrentals')->__('Return Selected') . '</span></button>
                    </th>
                </tr>
            </thead>
        ';
        $returnItemsHtml .= '<tbody>';

        $coll = Mage::getModel('payperrentals/sendreturn')
            ->getCollection()
            ->addSelectFilter("FIND_IN_SET('" . $snI . "', sn) AND return_date='0000-00-00 00:00:00'")
            ->orderByOrderId();
        $orderId = 0;
        foreach ($coll as $item) {
            $returnItemsHtml .= '<tr id="resorder_' . $item->getId() . '">';

            if ($orderId != $item->getOrderId()) {
                $orderId = $item->getOrderId();
                $order = Mage::getModel('sales/order')->load($item->getOrderId());
                $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
                $returnItemsHtml .= '<td>' . $orderId . ' - ' . $customerName . '</td>';
            } else {
                $returnItemsHtml .= '<td></td>';
            }
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $productName = $product->getName();
            $returnItemsHtml .= '<td>' . $productName . '</td>';
            $returnItemsHtml .= '<td>' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($item->getResStartdate()) . '</td>';
            $returnItemsHtml .= '<td>' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($item->getResEnddate()) . '</td>';
            $returnItemsHtml .= '<td>' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($item->getSendDate()) . '</td>';

            $returnItemsHtml .= '<td>' . $item->getQty() . '</td>';
            $returnItemsHtml .= '<td>';
            $snArr = explode(',', $item->getSn());
            foreach ($snArr as $sn) {
                $returnItemsHtml .= $sn . '<br/>';
            }
            $returnItemsHtml .= '</td>';
            $returnItemsHtml .= '<td><input type="checkbox" name="returnRes[]" class="returnRes" value="' . $item->getId() . '"/></td>';
            $returnItemsHtml .= '</tr>';
        }

        $returnItemsHtml .= '</tbody></table>';
        $html['html'] = $returnItemsHtml;
        $this->getResponse()->setBody(Zend_Json::encode($html));
    }


    /**
     *
     */
    public function generateSerialsAction()
    {
        $generatedSerials = array();

        $snsIds = $this->getRequest()->getParam('sns');
        $nrs = urldecode($this->getRequest()->getParam('nr'));
        $i = 0;
        if (empty($snsIds)) {
            $snsIds = array();
        }
        while (true) {
            $genL = $this->unique_id(20);
            if (!in_array($genL, $snsIds)) {
                $generatedSerials[] = $genL;
                $i++;
            }
            if ($i >= $nrs) break;
        }

        $serials['sn'] = $generatedSerials;
        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($serials));
    }


    /**
     *
     */
    public function reorderQuoteItemsAction()
    {
        $quoteArr = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getItemsCollection();

        $splitArr = explode(',', $this->getRequest()->getParam('data'));
        $newOrderArr = array();

        foreach ($splitArr as $itemAr) {
            $idArr = explode('_', $itemAr);
            $newOrderArr[] = $idArr[3];
        }

        $newQuoteArr = array();

        foreach ($quoteArr as $quoteItem) {
            $newQuoteArr[$quoteItem->getId()] = clone $quoteItem;
        }

        $ordered = array();
        foreach ($newOrderArr as $key) {
            if (array_key_exists($key, $newQuoteArr)) {
                $ordered[$key] = $newQuoteArr[$key];
                unset($newQuoteArr[$key]);
            }
        }
        $myQuoteArr = $ordered + $newQuoteArr;
        foreach ($quoteArr as $quoteItem) {
            $quoteItem->delete();
        }

        /*Mage::getSingleton('adminhtml/session_quote')->getQuote()->save();*/
        foreach ($myQuoteArr as $quoteItem1) {
            Mage::getSingleton('adminhtml/session_quote')->getQuote()->addItem($quoteItem1);
        }
        Mage::getSingleton('adminhtml/session_quote')->getQuote()->save();

    }

    /**
     *
     */
    public function getUpdateCustomerAction()
    {
        if (urldecode($this->getRequest()->getParam('customer_id'))) {
            //Mage::getSingleton('adminhtml/sales_order_create')
            $id = urldecode($this->getRequest()->getParam('customer_id'));
            $customer = Mage::getModel('customer/customer')->load($id);
            //Mage::getSingleton('adminhtml/session_quote')->getQuote()->setCustomerId($id);
            Mage::getSingleton('adminhtml/session_quote')->getQuote()->setCustomer($customer);
            Mage::getSingleton('adminhtml/session_quote')->getQuote()->save();
        }
    }

    /**
     *
     */
    public function getDateDetailsAction()
    {
        //todo should be filtered by clicked date
        $orderArr = explode('||', urldecode($this->getRequest()->getParam('start')));
        $details = Mage::helper('payperrentals/inventory')->getDateDetails($orderArr);
        $this->getResponse()->setBody(Zend_Json::encode($details));
    }

    /**
     *
     */
    public function getEventsAction()
    {
        $startDate = date('Y-m-d', urldecode($this->getRequest()->getParam('start'))) . ' 00:00:00';
        $endDate = date('Y-m-d', urldecode($this->getRequest()->getParam('end'))) . ' 23:59:59';
        $productIds = explode(',', urldecode($this->getRequest()->getParam('productsids')));
        $currentView = urldecode($this->getRequest()->getParam('current_view'));
        $events = Mage::helper('payperrentals/inventory')->getEvents($startDate,$endDate,$productIds,$currentView);
        $this->getResponse()->setBody(Zend_Json::encode($events));
    }

    /**
     *
     */
    public function getSerialeventsAction()
    {
        $start_date = date('Y-m-d', urldecode($this->getRequest()->getParam('start'))) . ' 00:00:00';
        $end_date = date('Y-m-d', urldecode($this->getRequest()->getParam('end'))) . ' 00:00:00';

        $productIds = explode(',', urldecode($this->getRequest()->getParam('productsids')));


        $events = array();
        foreach ($productIds as $prid) {
            $coll = Mage::getModel('payperrentals/sendreturn')
                ->getCollection()
                ->addSelectFilter("res_startdate >= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                        date('Y-m-d', strtotime('-1 month', strtotime($start_date)))
                    ) . "' AND res_enddate <= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                        date('Y-m-d', strtotime('+1 month', strtotime($end_date)))
                    ) . "' AND sn='" . $prid . "'");
            //echo $prid;
            //var_export($coll->getData());
            foreach ($coll as $mItem) {
                $Product = Mage::getModel('catalog/product')->load($mItem->getProductId());

                $turnoverTimeBefore = ITwebexperts_Payperrentals_Helper_Config::getTurnoverTimeBefore($Product->getId());
                $turnoverTimeAfter = ITwebexperts_Payperrentals_Helper_Config::getTurnoverTimeAfter($Product->getId());

                $stDate = date('Y-m-d H:i:s', strtotime($mItem->getSendDate()));

                if ($mItem->getReturnDate() != '0000-00-00 00:00:00') {
                    $enDate = date('Y-m-d H:i:s', strtotime($mItem->getReturnDate()));
                } else {
                    $enDate = date('Y-m-d H:i:s', strtotime($mItem->getResEnddate()));
                }

                $order = Mage::getModel('sales/order')->load($mItem->getOrderId());

                $shippingId = $order->getShippingAddressId();
                if (empty($shippingId)) {
                    $shippingId = $order->getBillingAddressId();
                }
                $address = Mage::getModel('sales/order_address')->load($shippingId);
                $customerName = $address->getFirstname() . ' ' . $address->getLastname();
                $rest = ' - Customer Name: ' . $customerName;
                $rest .= '<br/> - Order Date: ' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate(
                        $order->getCreatedAt(), false
                    );
                $rest .= '<br/>' . '- Item Sent on: ' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate(
                        $mItem->getSendDate(), false
                    );
                $rest .= '<br/>' . '- Item should have been sent on: ' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate(
                        $mItem->getResStartdate(), false
                    );
                if ($mItem->getReturnDate() == '0000-00-00 00:00:00') {
                    $rest .= '<br/>' . '- Item Not Returned' . '<br/>' . '- Should be returned on: ' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate(
                            $mItem->getResEnddate(), false
                        );
                } else {
                    $rest .= '<br/>' . '- Item Returned on: ' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate(
                            $mItem->getReturnDate(), false
                        )
                        . '<br/>' . '- Should have been returned on: ' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate(
                            $mItem->getResEnddate(), false
                        );
                }
                $evb = array(
                    'title' => ' Order: ' . $mItem->getOrderId(),
                    'url' => $rest,
                    'textColor' => Mage::getUrl('adminhtml/sales_order/view', array('order_id' => $order->getEntityId())),
                    'start' => $stDate,
                    'end' => $enDate,
                    'resource' => $mItem->getSn()
                );
                if ($mItem->getReturnDate() == '0000-00-00 00:00:00') {
                    $evb['backgroundColor'] = '#cc0000';
                    $evb['className'] = 'notreturnedColor '.$mItem->getSn();
                } else {
                    $evb['backgroundColor'] = '#00FF00';
                    $evb['className'] = 'returnedColor '.$mItem->getSn();
                }

                $events[] = $evb;
            }


        }

        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($events));
    }

    private function formatDates($params){
        $params = $this->_filterDates($params, array('startDate', 'endDate'));

        $startdate = $params['startDate'];
        $enddate = $params['endDate'];
        $starttime = $this->getRequest()->getParam('startTime');
        $endtime = $this->getRequest()->getParam('endTime');

        $startdate = date('Y-m-d H:i:s', strtotime($startdate . ' ' . $starttime));
        $enddate = date('Y-m-d H:i:s', strtotime($enddate . ' ' . $endtime));

        $html = '';
        $retArr = array();
        if ($startdate == $enddate || strtotime($startdate) > strtotime($enddate)) {
            $html .= '<b>' . Mage::helper('payperrentals')->__('Estimate Dates is Wrong') . '</b><br/>';
            $html .= '<b>' . Mage::helper('payperrentals')->__('Dropoff & Pickup Dates MUST BE Different and Dropoff Date MUST BE BIGGER Pickup Date') . '</b>';
            $retArr['can_update'] = false;
            $retArr['html'] = $html;
        } else {
            $html .= 'Dropoff Date: ' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($startdate, false, true) . '<br/>';
            $html .= 'Pickup Date: ' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($enddate, false, true) . '<br/>';
            $retArr['can_update'] = true;
            $retArr['start_date'] = $startdate;
            $retArr['end_date'] = $enddate;
            $retArr['html'] = $html;
        }
        return $retArr;
    }

    /**
     *
     */
    public function setEstimatedCreateAction()
    {
        $retArr = $this->formatDates($this->getRequest()->getPost());
        if($retArr['can_update']){
            Mage::getSingleton('adminhtml/session_quote')->getQuote()->setSendDatetime($retArr['start_date']);
            Mage::getSingleton('adminhtml/session_quote')->getQuote()->setReturnDatetime($retArr['end_date']);
            Mage::getSingleton('adminhtml/session_quote')->getQuote()->save();

        }
        $response['html'] = $retArr['html'];
        $this->getResponse()->setBody(Zend_Json::encode($response));
    }

    /**
     *
     */
    public function setEstimatedAction()
    {
        $retArr = $this->formatDates($this->getRequest()->getPost());
        if($retArr['can_update']){
            $orderid = $this->getRequest()->getParam('orderid');
            $order = Mage::getModel('sales/order')->load($orderid);
            $order->setSendDatetime($retArr['start_date']);
            $order->setReturnDatetime($retArr['end_date']);
            $order->save();
            Mage::getResourceSingleton('payperrentals/reservationorders')->updatePickupDropoffByOrderId($orderid, $retArr['start_date'], $retArr['end_date']);
            //getreservationorder with order_id and change the items
        }

        $html['html'] = $retArr['html'];
        $this->getResponse()->setBody(Zend_Json::encode($html));
    }

    public function UpdateInitialsAction()
    {
        $events = array();
        ITwebexperts_Payperrentals_Helper_Date::saveDatesForGlobalUse($this->getRequest()->getPost());
        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($events));
    }

    public function getUpdateAllDatesAction()
    {
        $quoteArr = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getItemsCollection();
        $isR4q = false;
        if(count($quoteArr) == 0){
            try {
                $quoteArr = Mage::getSingleton('request4quote/adminhtml_session_quote')->getQuote()->getItemsCollection();
                $isR4q = true;
            }
            catch(Exception $e){
                $quoteArr = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getItemsCollection();
                $isR4q = false;
            }
        }

        $events = array();
        list($startDate, $endDate) = ITwebexperts_Payperrentals_Helper_Date::saveDatesForGlobalUse($this->getRequest()->getPost());
        foreach ($quoteArr as $quoteItem) {
            if (!($quoteItem->getParentItem() && $quoteItem->getParentItem()->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE)) {
                if(!$isR4q) {
                    $optionCollection = Mage::getModel('sales/quote_item_option')->getCollection()
                        ->addItemFilter(array($quoteItem->getId()));
                }else{
                    $optionCollection = Mage::getModel('request4quote/quote_item_option')->getCollection()
                        ->addItemFilter(array($quoteItem->getId()));
                }
                $optionArr = $optionCollection->getOptionsByItem($quoteItem);

                foreach ($optionArr as $option) {
                    if ($option->getCode() == 'info_buyRequest') {
                        $infoBuyRequest = unserialize($option->getValue());
                        $infoBuyRequest['start_date'] = $startDate;
                        $infoBuyRequest['end_date'] = $endDate;
                        foreach ($infoBuyRequest as $item => $value) {
                            $events['itemConfigs'][$quoteItem->getId()][$item] = $value;
                        }
                    }

                }
                $events['itemId'][] = $quoteItem->getId();
            } else {

            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($events));
    }

    /**
     *
     */
    public function getUpdateOneDateAction()
    {

        $productId = (int)$this->getRequest()->getParam('id');

        $configureResult = new Varien_Object();
        $configureResult->setOk(true);
        $configureResult->setProductId($productId);
        $sessionQuote = Mage::getSingleton('adminhtml/session_quote');
        $configureResult->setCurrentStoreId($sessionQuote->getStore()->getId());
        $configureResult->setCurrentCustomerId($sessionQuote->getCustomerId());

        // Render page
        /* @var $helper Mage_Adminhtml_Helper_Catalog_Product_Composite */
        $helper = Mage::helper('adminhtml/catalog_product_composite');
        $helper->renderConfigureResult($this, $configureResult);

        $event = array(
            'event' => '',
        );

        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($event));
    }



    /**
     *
     */
    //TODO can be a problem when there are 2 same products with different dates on the same order
    public function getProductsPricesAction()
    {
        if (!$this->getRequest()->getParam('orderId')) {
            return;
        }
        $output = array();
        $sourceOrder = Mage::getModel('sales/order')->load($this->getRequest()->getParam('orderId'));
        $output = Mage::helper('payperrentals/inventory')->getProductPrices($sourceOrder);
        $this->getResponse()->setBody(Zend_Json::encode($output));
    }

    public function getExtendPopupAction(){
        if (!$this->getRequest()->getParam('order_id')) {
            return;
        }
        $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));
        $orderDatesArr = ITwebexperts_Payperrentals_Helper_Data::isSingleOrder($order);

        $html = ITwebexperts_Payperrentals_Helper_Extend::getExtendHtml($this->getRequest()->getParam('order_id'));

        $jsonReturn = array(
            'content' => $html,
            'minDate' => date('r',strtotime('+1 day', strtotime($orderDatesArr['end_date'])))
        );

        $this->getResponse()->setBody(Zend_Json::encode($jsonReturn));
    }

    public function getExtendProductsAction(){
        if (!$this->getRequest()->getParam('order_id')) {
            return;
        }
        $html = ITwebexperts_Payperrentals_Helper_Extend::getExtendProductsHtml($this->getRequest()->getParam('order_id'), $this->getRequest()->getParam('date'));
        $jsonReturn = array(
            'content' => $html,

        );

        $this->getResponse()->setBody(Zend_Json::encode($jsonReturn));
    }

    /**
     *
     */
    public function getPriceAndAvailabilityAction()
    {
        if (!$this->getRequest()->getParam('product_id')) {
            return;
        }

        $product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('product_id'));

        list($startDate, $endDate) = ITwebexperts_Payperrentals_Helper_Date::saveDatesForGlobalUse($this->getRequest()->getPost(), $this->getRequest()->getParam('saveDates'));

        $qty = intval(urldecode($this->getRequest()->getParam('qty')));
        $attributes = $this->getRequest()->getParam('super_attribute') ? $this->getRequest()->getParam('super_attribute') : null;
        $bundleOptions = $this->getRequest()->getParam('bundle_option') ? $this->getRequest()->getParam('bundle_option') : null;
        $bundleOptionsQty1 = $this->getRequest()->getParam('bundle_option_qty1') ? $this->getRequest()->getParam('bundle_option_qty1') : null;
        $bundleOptionsQty = $this->getRequest()->getParam('bundle_option_qty') ? $this->getRequest()->getParam('bundle_option_qty') : null;

        $normalPrice = '';
        $onClick = '';
        $isAvailable = true;
        $priceAmount = ITwebexperts_Payperrentals_Helper_Price::getPriceForAnyProductType($product, $attributes, $bundleOptions, $bundleOptionsQty1, $bundleOptionsQty, $startDate, $endDate, $qty, $normalPrice, $onClick);
        if($priceAmount == -1){
            $isAvailable = false;
        }
        list($qtyPerProduct, $qtyArr) = ITwebexperts_Payperrentals_Helper_Inventory::getQuantityForAnyProductTypePerProduct($product, $startDate, $endDate, $attributes, $bundleOptions, $bundleOptionsQty1, $bundleOptionsQty, false);

        $htmlStock = '<div class="grid"><table><thead><th>Product</th><th>Available Stock</th><th>Remaining Stock</th></thead><tbody>';
        $bundleProductName = '';
        $bundleQty = 0;
        $maxQty = 0;
        foreach($qtyPerProduct as $iProduct => $iQty){

            $typeId = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($iProduct, 'type_id');
            $productName = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($iProduct, 'name');

            if($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE) {
                $bundleProductName = $productName;
                $bundleQty = $iQty;
                continue;
            }

            $htmlStock .= '<tr>';
            $htmlStock .= '<td>'.$productName.'</td>';

            $htmlStock .= '<td>' . ((string)$iQty == 'is_overbook' ? $qty : $iQty) . '</td>';
            if(isset($qtyArr[$iProduct])){
                $htmlStock .= '<td>' . ((string)$iQty == 'is_overbook' ? $qty : ($iQty - $qty * $qtyArr[$iProduct])) . '</td>';
            }else {
                $htmlStock .= '<td>' . ((string)$iQty == 'is_overbook' ? $qty : ($iQty - $qty)) . '</td>';
            }

            $htmlStock .= '</tr>';
            $maxQty = $iQty;
        }
        if($bundleProductName != '') {
            $htmlStock
                .= '<tr><td>' . $bundleProductName . '</td><td>' . (
                ((string)$bundleQty =='is_overbook' ? $qty:$bundleQty)) . '</td><td>' . ((string)$bundleQty =='is_overbook' ? $qty:($bundleQty - $qty)) . '</td></tr>';
            $maxQty = $bundleQty;
        }

        $htmlStock .= '</tbody></table></div>';
        $jsonReturn = array(
            'amount' => $priceAmount,
            'available' => $isAvailable,
            'qty' => $qty,
            'stockAvail' => ((string)$maxQty =='is_overbook' ? $qty:($maxQty)),
            'stockRest' => ((string)$maxQty =='is_overbook' ? $qty:($maxQty - $qty)),
            'stockText' => $htmlStock,
            'formatAmount' => Mage::helper('core')->currency($priceAmount)
        );

        $this->getResponse()->setBody(Zend_Json::encode($jsonReturn));
    }

    /**
     * Return the quantity available for new start and end dates. This function takes into account the
     * original start and end dates and how much quantity was already reserved.
     *
     * Usually this function is used by the manually edit reservation page
     *
     * @param $productid
     * @param int $alreadyReservedQty
     * @param $startDate
     * @param $endDate
     * @param $newStartDate
     * @param $newEndDate
     */

    public function getQtyAvailableAction()
    {
        $index = $this->getRequest()->getParam('index');
        $productid = $this->getRequest()->getParam('product_id' . $index);
        $originalprodid = $this->getRequest()->getParam('originalprodid' . $index);
        $startdate = $this->getRequest()->getParam('originalstart_date' . $index);
        $enddate = $this->getRequest()->getParam('originalend_date' . $index);
        $newstart = $this->getRequest()->getParam('start_date' . $index);
        $newend = $this->getRequest()->getParam('end_date' . $index);

        /** We need to check date overlap using dates turnover before and after */
        $turnoverAr = ITwebexperts_Payperrentals_Helper_Data::getTurnoverFromQuoteItemOrBuyRequest($productid, null, $startdate, $enddate);
        $startwithturnover = $turnoverAr['before'];
        $endwithturnover = $turnoverAr['after'];

        $turnoverArnew = ITwebexperts_Payperrentals_Helper_Data::getTurnoverFromQuoteItemOrBuyRequest($productid, null, $newstart, $newend);
        $newstartwithturnover = $turnoverArnew['before'];
        $newendwithturnover = $turnoverArnew['after'];

        /** Add original reservation quantity only if same product & dates overlap*/
        if($productid == $originalprodid && Mage::helper('payperrentals/date')->doDatesOverlap($startwithturnover, $endwithturnover, $newstartwithturnover, $newendwithturnover)) {
            $alreadyReservedQty = $this->getRequest()->getParam('original_qty' . $index);
        } else {
            $alreadyReservedQty = 0;
        }
        $qtyAvailable = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($productid,$newstart,$newend) + $alreadyReservedQty;
        $jsonReturn = array(
            'quantity' => 'Quantity Available: ' . $qtyAvailable,
            'quantityonly'  =>  $qtyAvailable
        );
        $this->getResponse()->setBody(Zend_Json::encode($jsonReturn));
    }


}