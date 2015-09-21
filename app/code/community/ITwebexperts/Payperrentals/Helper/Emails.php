<?php

/**
 * Class ITwebexperts_Payperrentals_Helper_Emails
 * todo refactor this class
 */
class ITwebexperts_Payperrentals_Helper_Emails extends Mage_Core_Helper_Abstract {
    const XML_PATH_SEND_RETURN_REMINDER_EMAIL_TEMPLATE = 'payperrentals/notificationemails/return_reminder';
    const XML_PATH_RETURN_REMINDER_AUTO_EMAIL_TEMPLATE = 'payperrentals/notificationemails/return_reminder_auto';
    const XML_PATH_TOBEBILLED_EMAIL_TEMPLATE = 'payperrentals/notificationemails/tobebilled_email';
    const XML_PATH_EXTENDREMINDER_EMAIL_TEMPLATE = 'payperrentals/notificationemails/extend_reminder_email';
    const XML_PATH_RETURN_EMAIL_TEMPLATE = 'payperrentals/notificationemails/return_email';
    const XML_PATH_RETURN_REMINDER_EMAIL_TEMPLATE = 'payperrentals/notificationemails/return_reminder';
    const XML_PATH_SEND_EMAIL_TEMPLATE = 'payperrentals/notificationemails/send_email';
    const XML_PATH_SEND_QUEUE_EMAIL_TEMPLATE = 'payperrentals/notificationemails/send_queue_email';
    const XML_PATH_REMINDER_DAYS = 'payperrentals/notificationemails/reminder_days';
    const XML_PATH_USE_SEND_RETURN = 'payperrentals/notificationemails/use_send_return_auto_email';
    const XML_PATH_USE_SEND = 'payperrentals/notificationemails/use_send_email';
    const XML_PATH_USE_EXTENDED = 'payperrentals/notificationemails/use_extend_reminder_email';
    const XML_PATH_EXTENDED_REMINDER_DAYS = 'payperrentals/notificationemails/extend_reminder_days';
    const XML_PATH_USE_SEND_QUEUE = 'payperrentals/notificationemails/use_send_queue_email';
    const XML_PATH_USE_RETURN = 'payperrentals/notificationemails/use_return_email';
    const XML_PATH_USE_RETURN_REMINDER = 'payperrentals/notificationemails/use_return_reminder_email';
    const XML_PATH_USE_TOBEBILLED_REMINDER = 'payperrentals/notificationemails/use_tobebilled_members_email';
    const XML_PATH_ADMIN_REMINDER_EMAILS = 'payperrentals/notificationemails/admin_reminder_emails';

    /**
     * Sends an email
     * @param      $mailSubject
     * @param      $templateId
     * @param      $emailCustomer
     * @param      $vars
     * @param bool $isAdmin
     *
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    private static function magentoSendEmail($mailSubject, $templateId, $emailCustomer, $vars, $isAdmin = false){
        $recipientEmail = array();
        $recipientName = array();
        if(!$isAdmin) {
            $recipientEmail[] = $emailCustomer;
            $recipientName[] = '';
        }else{
            $recepientEmailsArr = explode(',', Mage::getStoreConfig(self::XML_PATH_ADMIN_REMINDER_EMAILS));
            foreach ($recepientEmailsArr as $recipents) {
                $recArr = explode(';', $recipents);
                $recipientEmail[] = $recArr[1];
                $recipientName[] = $recArr[0];
            }
        }

        $mailTemplate = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email
        $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name
        $sender = array('name' => $from_name,
                        'email' => $from_email);
        $storeId = Mage::app()->getStore()->getId();
        $model = $mailTemplate->setReplyTo($sender['email'])->setTemplateSubject($mailSubject);
        foreach($recipientEmail as $pos => $email) {
            $model->sendTransactional($templateId, $sender, $email, $recipientName[$pos], $vars, $storeId);
        }
        if (!$mailTemplate->getSentSuccess()) {
            throw new Exception();
        }
        $translate->setTranslateInline(true);
    }

    /**
     * @param $emailItems
     *
     * @throws Exception
     */
    private static function sendItemInQueueEmail($emailItems){
        if (Mage::getStoreConfig(self::XML_PATH_USE_SEND_QUEUE)) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_SEND_QUEUE_EMAIL_TEMPLATE);
            $mailSubject = Mage::helper('payperrentals')->__('List of send items: ');

            foreach ($emailItems as $emailCustomer => $items) {
                $shippedItems = '';
                foreach ($items as $prid => $varArr) {
                    if (!empty($varArr['name'])) {
                        $shippedItems .= '<br/>Product Name: ' . $varArr['name'] . (!empty($varArr['serials']) ?
                                ' Serials: ' . $varArr['serials'] : '') . ' Send Date:' . $varArr['send_date'];
                    }
                }

                $vars = array(
                    'shippedItems' => $shippedItems,
                );

                self::magentoSendEmail($mailSubject, $templateId, $emailCustomer, $vars);
            }
        }
    }

    /**
     * @param $emailItems
     */
    private static function returnItemEmail($emailItems){
        if (Mage::getStoreConfig(self::XML_PATH_USE_RETURN)) {
            foreach ($emailItems as $emailCustomer => $items) {
                if ($items['is_queue']) {
                    self::returnItemInQueueEmail($emailCustomer, $items);
                } else {

                    self::returnReservationItemEmail($emailCustomer, $items);
                }
            }
        }
    }

    /**
     * @param $emailCustomer
     * @param $items
     *
     * @throws Exception
     */
    private static function returnItemInQueueEmail($emailCustomer, $items)
    {
        $shippedItems = '';
        $templateId = Mage::getStoreConfig(self::XML_PATH_RETURN_EMAIL_TEMPLATE);
        $mailSubject = Mage::helper('payperrentals')->__('List of returned items:');

        foreach ($items as $prid => $varArr) {
            if (!empty($varArr['name'])) {
                $shippedItems .= '<br/>Product Name: ' . $varArr['name'] . (!empty($varArr['serials']) ?
                        ' Serials: ' . $varArr['serials'] : '') . ' Send Date:' . $varArr['send_date']
                    . ' Return Date: ' . $varArr['return_date'];
            }
        }

        $vars = array(
            'receivedItems' => $shippedItems,
        );
        self::magentoSendEmail($mailSubject, $templateId, $emailCustomer, $vars);
    }

    /**
     * @param $emailItems
     *
     * @throws Exception
     */
    private static function sendItemEmail($emailItems)
    {
        if (Mage::getStoreConfig(self::XML_PATH_USE_SEND)) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_SEND_EMAIL_TEMPLATE);
            $mailSubject = Mage::helper('payperrentals')->__('List of send items:');
            foreach ($emailItems as $emailCustomer => $items) {
                $shippedItems = '';
                foreach ($items as $oid => $oitems) {
                    $shippedItems .= '<br/>From order id: ' . $oid . '<br/>';
                    foreach ($oitems as $prid => $varArr) {
                        if (!empty($varArr['name'])) {
                            $shippedItems .= '<br/>Product Name: ' . $varArr['name'] . (!empty($varArr['serials']) ?
                                    ' Serials: ' . $varArr['serials'] : '') . ' Reservation Start Date: '
                                . $varArr['start_date'] . ' Reservation End Date: ' . $varArr['end_date'];
                        }
                    }
                }


                $vars = array(
                    'shippedItems' => $shippedItems,
                );
                self::magentoSendEmail($mailSubject, $templateId, $emailCustomer, $vars);
            }
        }
    }

    /**
     * @param $emailCustomer
     * @param $items
     *
     * @throws Exception
     */
    private static function returnReservationItemEmail($emailCustomer, $items)
    {
        $shippedItems = '';
        $templateId = Mage::getStoreConfig(self::XML_PATH_RETURN_EMAIL_TEMPLATE);
        $mailSubject = Mage::helper('payperrentals')->__('List of returned items:');
        foreach ($items as $oid => $oitems) {
            if(is_array($oitems)) {
                $shippedItems .= '<br/>From order id: ' . $oid . '<br/>';
                foreach ($oitems as $prid => $varArr) {
                    if (!empty($varArr['name'])) {
                        $shippedItems .= '<br/>Product Name: ' . $varArr['name'] . (!empty($varArr['serials']) ?
                                ' Serials: ' . $varArr['serials'] : '') . ' Reservation Start Date: '
                            . $varArr['start_date'] . ' Reservation End Date: ' . $varArr['end_date']
                            . ' Send Date:' . $varArr['send_date'];
                    }
                }
            }
        }

        $vars = array(
            'receivedItems' => $shippedItems,
        );
        self::magentoSendEmail($mailSubject, $templateId, $emailCustomer, $vars);
    }

    /**
     * @param $emailItems
     *
     * @throws Exception
     */
    private static function returnReminderReservationOrQueueItemForCustomerEmail($emailItems){
        $templateId = Mage::getStoreConfig(self::XML_PATH_RETURN_REMINDER_EMAIL_TEMPLATE);
        $mailSubject = Mage::helper('payperrentals')->__('Return Reminder');

        foreach ($emailItems['reminderPerCustomer'] as $emailCustomer => $varArr) {
            $itemList = '';
            foreach ($varArr as $item) {
                $itemList .= $item . '<br/>';
            }
            $vars = array(
                'reminderdays' => $emailItems['reminderdays'],
                'itemList'     => $itemList,
		'customerName' => $emailItems['customerName']
            );
            self::magentoSendEmail($mailSubject, $templateId, $emailCustomer, $vars);
        }

    }

    /**
     * @param $emailItems
     *
     * @throws Exception
     */
    private static function extendReminderReservationItemForCustomerEmail($emailItems){
        $templateId = Mage::getStoreConfig(self::XML_PATH_EXTENDREMINDER_EMAIL_TEMPLATE);
        $mailSubject = Mage::helper('payperrentals')->__('Return Reminder');

        foreach ($emailItems['reminderPerCustomer'] as $emailCustomer => $varArr) {
            $itemList = '';
            foreach ($varArr as $item) {
                $itemList .= $item . '<br/>';
            }
            $vars = array(
                'reminderdays' => $emailItems['reminderdays'],
                'itemList' => $itemList
            );
            self::magentoSendEmail($mailSubject, $templateId, $emailCustomer, $vars);
        }

    }

    /**
     * @param $emailItems
     *
     * @throws Exception
     */
    private static function dailyReminderReservationsForAdminEmail($emailItems){
        $templateId = Mage::getStoreConfig(self::XML_PATH_RETURN_REMINDER_AUTO_EMAIL_TEMPLATE);
        $mailSubject = Mage::helper('payperrentals')->__('Reminder for today');

        $vars = array(
            'todayDate' => $emailItems['todayDate'],
            'nrOutgoing' => $emailItems['nrOutgoing'],
            'clientListOutgoing' => $emailItems['clientListOutgoing'],
            'listOutgoing' => $emailItems['listOutgoing'],
            'nrIncoming' => $emailItems['nrIncoming'],
            'clientListIncoming' => $emailItems['clientListIncoming'],
            'listIncoming' => $emailItems['listIncoming']
        );

        self::magentoSendEmail($mailSubject, $templateId, null, $vars, true);
    }

    /**
     * Function used to send emails in different cases:
     * 1. "send" - when an item is sent to the customer
     * 2. "return" - when an item is returned from the customer
     * 3. "reminder" - reminder for the customer to return specific items
     * @param $type
     * @param array $emailItems
     * @throws Exception
     */
    public static function sendEmail($type, $emailItems = array())
    {
        switch ($type) {
            case 'send_queue':
                self::sendItemInQueueEmail($emailItems);
                break;
            case 'send':
                self::sendItemEmail($emailItems);
                break;
            case 'return':
                self::returnItemEmail($emailItems);
                break;
            case 'return_reminder': //queue or reservation
                self::returnReminderReservationOrQueueItemForCustomerEmail($emailItems);
                break;
            case 'extend_reminder': //queue or reservation
                self::extendReminderReservationItemForCustomerEmail($emailItems);
                break;
            case 'reminder_admin':
                self::dailyReminderReservationsForAdminEmail($emailItems);
                break;
            case 'tobebilled':
                /*deprecated notused*/
                $templateId = 'payperrentals_notification_tobebilled';
                break;
        }

    }

    /**
     * @return array
     */
    private static function getListOutgoing()
    {
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('m/d/Y'))));
        $collReservationToBeSend = Mage::getModel('payperrentals/reservationorders')
            ->getCollection()
            ->addSelectFilter(
                "start_date >= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($startDate)
                . "' AND start_date <= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                    $endDate
                ) . "'"
            )
            ->addSelectFilter("product_type = '" . ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE . "'")
            ->addSendReturnFilter()
            ->orderByOrderId();

        $clientListOutgoing = '<ul>';
        $listOutgoing = '';
        $orderId = 0;
        $nrOutgoing = 0;

        foreach ($collReservationToBeSend as $item) {
            if ($orderId != $item->getOrderId()) {
                if ($orderId != 0 && isset($order) && is_object($order)) {

                    $items1 = $order->getAllItems();

                    foreach ($items1 as $itemId1 => $item1) {
                        $productQty = intval($item1->getQtyInvoiced());
                        $productName = $item1->getName();
                        if ($item1->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE
                            && $item1->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE
                            && $item1->getProductType()
                            != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE
                        ) {
                            $listOutgoing .= '<p style="margin-left:20px">' . $productQty . ' - ' . $productName
                                . '</p>';
                        }
                    }

                    $listOutgoing .= '<a style="font-size:15px;" href="' . Mage::getUrl(
                            'payperrentals_admin/adminhtml_sendreturnreport/index',
                            array('key'    => Mage::getSingleton('adminhtml/url')->getSecretKey(
                                'adminhtml_sendreturnreport', 'index'
                            ), 'startDate' => $startDate, 'endDate' => $endDate, 'sending' => '1')
                        ) . '">' . Mage::helper('payperrentals')->__('Send Equipment') . '</a>';
                }
                $orderId = $item->getOrderId();
                $order = Mage::getModel('sales/order')->load($item->getOrderId());
                $nrOutgoing++;
                $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                $customerName = '';
                if ($customer->getCompany()) {
                    $customerName = $customer->getCompany() . ' - ';
                }
                $customerName .= $customer->getFirstname() . ' ' . $customer->getLastname();
                $clientListOutgoing .= '<li>' . $customerName . '</li>';
                $customersecRow = '';
                if ($customer->getTelephone()) {
                    $customersecRow = $customer->getTelephone() . ' | ';
                }
                $customersecRow .= '<a href="mailto:' . $customer->getEmail() . '">' . $customer->getEmail() . '</a>';
                $listOutgoing .= '<p><a style="font-size:15px" href="' . Mage::getUrl(
                        'adminhtml/sales_order/view', array('order_id' => $order->getId())
                    ) . '">' . $customerName . '</a></p><p>' . $customersecRow . '</p>';

                $comments = $order->getStatusHistoryCollection();
                $commentsString = '';
                foreach ($comments as $hist_cmt) {
                    if ($hist_cmt->getComment()) {
                        $commentsString .= $hist_cmt->getComment() . '<br/>';
                    }
                }
                if ($commentsString != '') {
                    $listOutgoing
                        .= '<br/><p style="font-style:italic;color:#cccccc;">' . Mage::helper('payperrentals')->__(
                            'Order Notes'
                        ) . '</p>';
                    $listOutgoing .= $commentsString;
                }
                $listOutgoing
                    .=
                    '<br/><p style="font-style:italic;color:#cccccc;">' . Mage::helper('payperrentals')->__('Equipment')
                    . '</p>';

            }

            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $productName = $product->getName();
            $productQty = $item->getQty();
            $listOutgoing .= '<p style="margin-left:20px">' . $productQty . ' - ' . $productName . '</p>';

            $resStartDate = $item->getStartDate();
            $resEndDate = $item->getEndDate();
            if (date('m/d/Y', strtotime($resStartDate)) == date('m/d/Y', strtotime($resEndDate))) {
                $listOutgoing .= '<p style="margin-left:40px">' . date('H:i', strtotime($resStartDate)) . ' - ' . date(
                        'H:i', strtotime($resEndDate)
                    ) . '</p>';
            }

        }

        if ($orderId != 0 && isset($order) && is_object($order)) {
            $items1 = $order->getAllItems();
            foreach ($items1 as $itemId1 => $item1) {
                $productQty = intval($item1->getQtyInvoiced());
                $productName = $item1->getName();
                if ($item1->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE
                    && $item1->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE
                    && $item1->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE
                ) {
                    $listOutgoing .= '<p style="margin-left:20px">' . $productQty . ' - ' . $productName . '</p>';
                }
            }
            $url1 = Mage::getUrl(
                'payperrentals_admin/adminhtml_sendreturnreport/index',
                array('key'    => Mage::getSingleton('adminhtml/url')->getSecretKey(
                    'adminhtml_sendreturnreport', 'index'
                ), 'startDate' => $startDate, 'endDate' => $endDate, 'sending' => '1')
            );

            $listOutgoing
                .=
                '<a style="font-size:15px;" href="' . $url1 . '">' . Mage::helper('payperrentals')->__('Send Equipment')
                . '</a>';
        }

        $clientListOutgoing .= '</ul>';

        return array($listOutgoing, $clientListOutgoing, $nrOutgoing);
    }

    /**
     * @return array
     */
    private static function getListIncomingForAdmin(){
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('m/d/Y'))));
        $collReservationsToBeReturned = Mage::getModel('payperrentals/sendreturn')
            ->getCollection()
            ->addSelectFilter("res_enddate >= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($startDate) . "' AND res_enddate <= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                    $endDate
                ) . "' AND return_date='0000-00-00 00:00:00'")
            ->orderByOrderId();
        $clientListIncoming = '<ul>';
        $listIncoming = '';
        $orderId = 0;
        $nrIncoming = 0;
        foreach ($collReservationsToBeReturned as $item) {
            if ($orderId != $item->getOrderId()) {
                if ($orderId != 0  && isset($order) && is_object($order)) {
                    $items1 = $order->getAllItems();

                    foreach ($items1 as $itemId1 => $item1) {
                        $productQty = intval($item1->getQtyInvoiced());
                        $productName = $item1->getName();
                        if ($item1->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE && $item1->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE && $item1->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE) {
                            $listIncoming .= '<p style="margin-left:20px">' . $productQty . ' - ' . $productName . '</p>';
                        }
                    }
                    $listIncoming .= '<a style="font-size:15px;" href="' . Mage::getUrl('payperrentals_admin/sendreturnreport/index', array('startDate' => $startDate, 'endDate' => $endDate, 'returning' => '1', 'key' => Mage::getSingleton('adminhtml/url')->getSecretKey('adminhtml_sendreturnreport', 'index'))) . '">' . Mage::helper('payperrentals')->__('Return Equipment') . '</a>';
                }
                $orderId = $item->getOrderId();
                $order = Mage::getModel('sales/order')->load($item->getOrderId());
                $nrIncoming++;
                $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                $customerName = '';
                if ($customer->getCompany()) {
                    $customerName = $customer->getCompany() . ' - ';
                }
                $customerName .= $customer->getFirstname() . ' ' . $customer->getLastname();
                $clientListIncoming .= '<li>' . $customerName . '</li>';
                $customersecRow = '';
                if ($customer->getTelephone()) {
                    $customersecRow = $customer->getTelephone() . ' | ';
                }
                $customersecRow .= '<a href="mailto:' . $customer->getEmail() . '">' . $customer->getEmail() . '</a>';
                $listIncoming .= '<p><a style="font-size:15px" href="' . Mage::getUrl('adminhtml/sales_order/view', array('order_id' => $order->getId())) . '">' . $customerName . '</a></p><p>' . $customersecRow . '</p>';

                $comments = $order->getStatusHistoryCollection();
                $commentsString = '';
                foreach ($comments as $hist_cmt) {
                    if ($hist_cmt->getComment()) {
                        $commentsString .= $hist_cmt->getComment() . '<br/>';
                    }
                }
                if ($commentsString != '') {
                    $listIncoming .= '<br/><p style="font-style:italic;color:#cccccc;">' . Mage::helper('payperrentals')->__('Order Notes') . '</p>';
                    $listIncoming .= $commentsString;
                }
                $listIncoming .= '<br/><p style="font-style:italic;color:#cccccc;">' . Mage::helper('payperrentals')->__('Equipment') . '</p>';

            }

            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $productName = $product->getName();
            $productQty = $item->getQty();
            $listIncoming .= '<p style="margin-left:20px">' . $productQty . ' - ' . $productName . '</p>';


        }
        if ($orderId != 0 && isset($order) && is_object($order)) {
            $items1 = $order->getAllItems();

            foreach ($items1 as $itemId1 => $item1) {
                $productQty = intval($item1->getQtyInvoiced());
                $productName = $item1->getName();
                if ($item1->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE && $item1->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE && $item1->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE) {
                    $listIncoming .= '<p style="margin-left:20px">' . $productQty . ' - ' . $productName . '</p>';
                }
            }
            $listIncoming .= '<a style="font-size:15px;" href="' . Mage::getUrl('payperrentals_admin/sendreturnreport/index', array('startDate' => $startDate, 'endDate' => $endDate, 'returning' => '1', 'key' => Mage::getSingleton('adminhtml/url')->getSecretKey('adminhtml_sendreturnreport', 'index'))) . '">' . Mage::helper('payperrentals')->__('Return Equipment') . '</a>';
        }
        $clientListIncoming .= '</ul>';

        return array($listIncoming, $clientListIncoming, $nrIncoming);
    }

    /**
     *
     */
    private static function getReservationsItemsToBeReturnedOrSendForAdminEmail(){
        list($listOutgoing, $clientListOutgoing, $nrOutgoing) = self::getListOutgoing();
        list($listIncoming, $clientListIncoming, $nrIncoming) = self::getListIncomingForAdmin();

        $todayDate = ITwebexperts_Payperrentals_Helper_Date::formatDbDate(date('Y-m-d'), true, true);
        $emailItems = array(
            'nrOutgoing' => $nrOutgoing,
            'nrIncoming' => $nrIncoming,
            'clientListOutgoing' => $clientListOutgoing,
            'listOutgoing' => $listOutgoing,
            'clientListIncoming' => $clientListIncoming,
            'listIncoming' => $listIncoming,
            'todayDate' => $todayDate

        );

        if ($nrOutgoing > 0 || $nrIncoming > 0) {
            ITwebexperts_Payperrentals_Helper_Emails::sendEmail('reminder_admin', $emailItems);
        }
    }

    /**
     * @param bool $isExtended
     */
    private static function getReservationItemsToBeReturnedForCustomerEmail($isExtended = false){
        $reminderPerCustomer = array();

        $startDate = date('Y-m-d', strtotime('-3 day', strtotime(date('Y-m-d'))));
        //$startDate = date('Y-m-d');
        if(!$isExtended) {
            $endDate = date(
                'Y-m-d', strtotime(
                    '+' . (Mage::getStoreConfig(
                        self::XML_PATH_REMINDER_DAYS
                    )) . ' day', strtotime(date('m/d/Y'))
                )
            );
        }else{
            $endDate = date(
                'Y-m-d', strtotime(
                    '+' . (Mage::getStoreConfig(
                        self::XML_PATH_EXTENDED_REMINDER_DAYS
                    )) . ' day', strtotime(date('m/d/Y'))
                )
            );
        }

        $collReservationToBeReturned = Mage::getModel('payperrentals/sendreturn')
            ->getCollection()
            ->addSelectFilter("res_enddate >= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($startDate) . "' AND res_enddate <= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                    $endDate
                ) . "' AND return_date='0000-00-00 00:00:00'")
            ->groupByOrder();

        $clientListIncoming = '<ul>';
        $listIncoming = '';
        $nrIncoming = 0;
        $usedOrders = array();
        foreach ($collReservationToBeReturned as $item) {
                $order = Mage::getModel('sales/order')->load($item->getOrderId());

                $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            if (isset($usedOrders[$customer->getEmail()]) && in_array($item->getOrderId(), $usedOrders[$customer->getEmail()])) {
                continue;
            }
            $orderInfo
                = '<p>Order Id: ' . $order->getIncrementId() . '</p> <p> - Start Date: ' . $order->getStartDatetime()
                . '</p> <p> - Return Date: ' . $order->getEndDatetime() . '</p>';
                $clientListIncoming .= '<li>' . $orderInfo . '</li>';
                $nrIncoming++;

                if(!$isExtended) {
                    $listIncoming .= '<p><a style="font-size:15px" href="' . Mage::getUrl(
                            'sales/order/view', array('order_id' => $order->getId())
                        ) . '">' . $orderInfo . '</a></p>';
                }else{
                    $listIncoming .= '<p><a style="font-size:15px" href="' . Mage::getUrl(
                            'payperrentals_front/customer_extendorder', array('order_id' => $order->getId())
                        ) . '">' . $orderInfo . '</a></p>';
                }

            $usedOrders[$customer->getEmail()][] = $item->getOrderId();
        }
        foreach($usedOrders as $email => $orderIds) {
            foreach($orderIds as $orderId) {
                $order = Mage::getModel('sales/order')->load($orderId);
                $listIncoming
                    .= '<br/><p style="font-style:italic;color:#cccccc;">' . Mage::helper('payperrentals')->__(
                        'Products from order:'
                    ) . ' ' . $order->getIncrementId()
                    . '</p>';
            foreach ($order->getAllItems() as $orderItem) {
                if($orderItem->getParentItem()){
                    continue;
                }
                    //$productQty = intval($orderItem->getQtyInvoiced());
                $productName = $orderItem->getName();

                $listIncoming .= '<p style="margin-left:20px">' .  ' - ' . $productName . '</p>';
                }
            }

            $reminderPerCustomer[$email][] = $listIncoming;
        }

        $clientListIncoming .= '</ul>';
        $emailItems = array(
            'nrIncoming' => $nrIncoming,
            'clientListIncoming' => $clientListIncoming,
            'listIncoming' => $listIncoming,
            'reminderPerCustomer' => $reminderPerCustomer,
	    'customerName' 		 => $customer->getFirstname() . ' ' . $customer->getLastname(),
            'reminderdays'        => ! $isExtended ? Mage::getStoreConfig(self::XML_PATH_REMINDER_DAYS)
                : Mage::getStoreConfig(self::XML_PATH_EXTENDED_REMINDER_DAYS)

        );

        if ($nrIncoming > 0) {
            if(!$isExtended) {
                ITwebexperts_Payperrentals_Helper_Emails::sendEmail('return_reminder', $emailItems);
            }else{
                ITwebexperts_Payperrentals_Helper_Emails::sendEmail('extend_reminder', $emailItems);
            }
        }
    }



    /**
     * Function used for cron emails
     */

    public static function sendReturnEmail()
    {
        if (Mage::getStoreConfig(self::XML_PATH_USE_SEND_RETURN)) {
            self::getReservationsItemsToBeReturnedOrSendForAdminEmail();
        }

        if (Mage::getStoreConfig(self::XML_PATH_USE_EXTENDED)) {
            self::getReservationItemsToBeReturnedForCustomerEmail(true);
        }


        if (Mage::getStoreConfig(self::XML_PATH_USE_RETURN_REMINDER)) {
            self::getReservationItemsToBeReturnedForCustomerEmail();
        }
    }

}