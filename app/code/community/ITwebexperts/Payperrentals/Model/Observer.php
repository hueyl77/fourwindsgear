<?php

class ITwebexperts_Payperrentals_Model_Observer
{

    /**
     * Constant used to keep a clone of the payment object if transaction is a authorize.net one, between events
     * todo This can be made easier without it by using directly the clone inside the quoteMergeAfter. But needs testing, maybe is not possible.
     */
    const AUTH_NET_REGISTER_CODE = 'ppr_authnet_payment';

    /**
     * Used to add sendreturn table record when shipment is made
     *
     * Event fired before Shipment for an order is saved
     * @param Varien_Event_Observer $observer
     */

    public function salesOrderShipmentSaveBefore(Varien_Event_Observer $observer)
    {
        /** @var $shipment Mage_Sales_Model_Order_Shipment */
        $shipment = $observer->getEvent()->getShipment();
        $items = $shipment->getAllItems();
        $productids = array();
        foreach($items as $item){
            $productids[] = $item->getProductId();
        }

        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order')->load($shipment->getOrderId());

        /** @var $serialNumbers array  having index as order item id and value as array of serial numbers */
        if (Mage::app()->getRequest()->getParam('sn')) {
            $serialNumbers = Mage::app()->getRequest()->getParam('sn');
        } else {
            $serialNumbers = array();
        }

        /**
         * Get a collection with reservations for the order
         */

        $collectionReservations = Mage::getModel('payperrentals/reservationorders')
            ->getCollection()
            ->addSelectFilter("order_id = '" . $order->getId() . "'")
            ->addFieldToFilter('product_id', array('in' => $productids))
            ->addSelectFilter("product_type = '" . ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE . "'")
            ->addShippedFilter();


        /** @var array $reservationIds used to get the ids from the collection */
        $reservationIds = array();

        /** @var array $orderItems use to get the order items ids with the index being the reservation id */
        $orderItems = array();

        foreach ($collectionReservations as $iReservation) {
            $reservationIds[] = $iReservation->getId();
            $orderItems[$iReservation->getId()] = $iReservation->getOrderItemId();
        }
        $shipItemsArray = Mage::app()->getRequest()->getParam('shipment');

        Mage::helper('payperrentals/inventory')->processShipment($collectionReservations, $serialNumbers, $orderItems, $items, $shipItemsArray);

    }

    /**
     * Event fired after invoice is saved
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function invoiceSaveAfter(Varien_Event_Observer $observer)
    {
        /** @var $invoice Mage_Sales_Model_Order_Invoice */
        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->getBaseDepositpprAmount()) {
            /** @var $order Mage_Sales_Model_Order */
            $order = $invoice->getOrder();
            $order->setDepositpprAmountInvoiced($invoice->getDepositpprAmount());
            $order->setBaseDepositpprAmountInvoiced($invoice->getBaseDepositpprAmount());
        }
        return $this;
    }

    /**
     * Event fired after credit memo is saved
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */

    public function creditmemoSaveAfter(Varien_Event_Observer $observer)
    {
        /* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();

        if ($creditmemo->getDepositpprAmount()) {
            $order = $creditmemo->getOrder();
            $order->setDepositpprAmountRefunded($creditmemo->getDepositpprAmount());
            $order->setBaseDepositpprAmountRefunded($creditmemo->getBaseDepositpprAmount());
        }
        return $this;
    }

    /**
     * Function used For all the email handling. Check helper for more info
     */
    public function sendReturnEmail()
    {
        ITwebexperts_Payperrentals_Helper_Emails::sendReturnEmail();
    }

    /**
     * Event fired when returning the json config for bundle products in product view
     * Used to send the product type, so we know is a bundle product when doing calculations on frontend
     * @param Varien_Event_Observer $event
     */


    public function bundleProductConfig(Varien_Event_Observer $event)
    {
        $selection = $event['selection'];
        $responseObject = $event['response_object'];

        $additional['typeid'] = $selection->getTypeId();
        $responseObject->setAdditionalOptions($additional);
    }

    /**
     * Check the controller actions to see when cart is updated and fire an event
     * @param Varien_Event_Observer $observer
     */

    public function hookToControllerActionPreDispatch(Varien_Event_Observer $observer)
    {
        //we compare action name to see if that's action for which we want to add our own event
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_updatePost') {
            //We are dispatching our own event before action ADD is run and sending parameters we need
            Mage::dispatchEvent(
                'cart_updatepost_before', array('request' => $observer->getControllerAction()->getRequest())
            );
        }

    }

    /**
     * Event fired before updating cart to clean the reservation quotes table for bundle and configurable items
     */

    public function beforeUpdatePostCart()
    {
        $request = Mage::app()->getRequest();
        $updateAction = (string)$request->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $quoteItems = Mage::getSingleton('checkout/cart')->getQuote()->getItemsCollection();
                foreach ($quoteItems as $item) {
                    $itemId = $item->getId();
                    $aChildQuoteItems = Mage::getModel("sales/quote_item")
                        ->getCollection()
                        ->setQuote(Mage::getSingleton('checkout/cart')->getQuote())
                        ->addFieldToFilter("parent_item_id", $itemId);

                    Mage::getResourceModel('payperrentals/reservationquotes')->deleteByQuoteItem($item);

                    foreach ($aChildQuoteItems as $cItems) {
                        Mage::getResourceModel('payperrentals/reservationquotes')->deleteByQuoteItem($cItems);
                    }
                }
                Mage::getSingleton('checkout/session')->setIsExtendedQuote(false);
                break;
        }

    }

    /**
     * Event fired before updating items in cart so it add damage waiver flag to the buyRequest
     * @param Varien_Event_Observer $observer
     *
     * @throws Exception
     */
    public function checkDamageWaiver(Varien_Event_Observer $observer)
    {
        $data = $observer->getInfo();

        /** @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getSingleton('checkout/cart')->getQuote();

        foreach ($data as $itemId => $itemInfo) {
            $item = $quote->getItemById($itemId);
            if (!$item) {
                continue;
            }
            if (isset($itemInfo['damage_waiver'])) {
                $option = $item->getOptionByCode('info_buyRequest');
                $buyRequest = new Varien_Object($option ? unserialize($option->getValue()) : null);

                if ($itemInfo['damage_waiver']) {
                    $buyRequest->setDamageWaiver(1);
                } else {
                    $buyRequest->setDamageWaiver(0);
                }
                if ($option) {
                    $option->setValue(serialize($buyRequest->getData()));
                }
                $option->save();
            }
        }
    }

    /**
     * Event fired when a product is removed from the cart
     * @param Varien_Event_Observer $event
     *
     * @return $this
     */

    public function removeReservationFromCart(Varien_Event_Observer $event)
    {
        /** @var $quoteItem Mage_Sales_Model_Quote_Item*/
        $quoteItem = $event->getQuoteItem();

        if (!$quoteItem) {
            $quoteItem = $event->getItem();
        }

        if ($quoteItem->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE
            && $quoteItem->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE
            && $quoteItem->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE
        ) {
            return $this;
        }

        $aChildQuoteItems = Mage::getModel("sales/quote_item")
            ->getCollection()
            ->setQuote($quoteItem->getQuote())
            ->addFieldToFilter("parent_item_id", $quoteItem->getId());

        Mage::getResourceModel('payperrentals/reservationquotes')->deleteByQuoteItem($quoteItem);

        foreach ($aChildQuoteItems as $cItems) {
            Mage::getResourceModel('payperrentals/reservationquotes')->deleteByQuoteItem($cItems);
        }

        if($quoteItem->getQuote()->getItemsCount() == 1){
            Mage::getSingleton('checkout/session')->setIsExtendedQuote(false);
        }

        return $this;
    }

    /**
     * Event fired before saving the order and process the data in admin side so it checked the availability of the product
     * todo This should not be necessary since admin is allowed to overbook
     * @param Varien_Event_Observer $eventData
     *
     * @return $this
     * @throws Mage_Core_Exception
     */

    public function createOrderBeforeSave(Varien_Event_Observer $eventData)
    {
        $updateItems = array();
        if ($eventData['request_model']->getPost('item')) {
            $updateItems = $eventData['request_model']->getPost('item');
        }
        $quoteObject = $eventData['order_create_model']->getQuote();
        if (is_object($quoteObject)) {
            foreach ($quoteObject->getAllItems() as $item) {
                if ($item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE
                    || $item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE
                ) {
                    $buyRequest = $item->getBuyRequest();
                    if (isset($updateItems[$item->getItemId()])) {
                        $updateItemsArr = $updateItems[$item->getItemId()];
                    }

                    if (isset($updateItemsArr['qty'])) {
                        $qty = $updateItemsArr['qty'];
                    } else {
                        $qty = $buyRequest->getQty();
                    }
                    if (isset($updateItemsArr['start_date'])) {
                        $startDate = $updateItemsArr['start_date'];
                    } else {
                        $startDate = $buyRequest->getStartDate();
                    }
                    if (isset($updateItemsArr['end_date'])) {
                        $endDate = $updateItemsArr['end_date'];
                    } else {
                        $endDate = $buyRequest->getEndDate();
                    }
                    /** @var $inventoryHelper ITwebexperts_Payperrentals_Helper_Inventory */
                    $inventoryHelper = Mage::helper('payperrentals/inventory');

                    $isAvailable = $inventoryHelper->isAvailable(
                        $item->getProduct()->getId(), $startDate, $endDate, $qty, $item
                    );

                    if ((!isset($updateItemsArr['action']) || $updateItemsArr['action'] != 'remove')
                    && !Mage::app()->getStore()->isAdmin() && !$isAvailable) {
                        Mage::throwException(
                            'Product ' . $item->getProduct()->getName()
                            . ' is not available for that qty on the selected dates'
                        );
                        return $this;
                    }
                    /*if(Mage::app()->getStore()->isAdmin()){
                         if(!$isAvailable){
                             if(!ITwebexperts_Payperrentals_Helper_Inventory::isAdminOverbook()){
                                 Mage::throwException(
                                     'Product ' . $item->getProduct()->getName()
                                     . ' is not available for that qty on the selected dates'
                                 );
                                 return $this;
                             }
                         }
                     }*/

                }
            }
        }
        return $this;
    }

    /**
     * Clean the prices table when product is deleted.
     * todo Normally it should  clean all the other tables
     * @param Varien_Event_Observer $event
     */

    public function deleteProductBefore(Varien_Event_Observer $event)
    {
        $product = $event['product'];
        Mage::getResourceModel('payperrentals/reservationprices')->deleteByEntityId($product->getId());
        Mage::getResourceModel('payperrentals/serialnumbers')->deleteByEntityId($product->getId());
        //delete reservationorders
        //delete sendreturn
        //delete rentalqueue
    }

    public function quoteMergeAfter($event)
    {
        $source = $event->getSource();
        Mage::getResourceModel('payperrentals/reservationquotes')->deleteByQuoteId($source->getId());
        $this->sameDatesAllProducts($event);
    }

    /**
     * Used to reset the cart if is empty
     *
     * @return $this
     */
    public function afterCartSave()
    {

        /** @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        if (ITwebexperts_Payperrentals_Helper_Data::isUsingGlobalDatesShoppingCart() && $quote->getItemsCount() == 0 || !Mage::helper('payperrentals/config')->keepSelectedDays()) {
            Mage::getSingleton('core/session')->unsetData('startDateInitial');
            Mage::getSingleton('core/session')->unsetData('endDateInitial');
        }
        return $this;
    }

    /**
     * @param $product
     * @param $quoteItem
     *
     * @return bool|int|mixed
     */

    private function getSelectedQtyByTheCustomerForTheProduct($product, $quoteItem)
    {
        if ($product->isConfigurable() && $quoteItem->getParentItem()) {
            $qty = $quoteItem->getParentItem()->getQty(); //ITwebexperts_Payperrentals_Helper_Data::getUpdatingQty($quoteItem->getParentItem());
        } else {
            $qty = $quoteItem->getQty(); //ITwebexperts_Payperrentals_Helper_Data::getUpdatingQty($quoteItem);
        }

        return $qty;
    }

    /**
     * This function is used to return the configurable product
     * @param $product
     * @param $source
     */
    private function getProduct($product, $source)
    {
        if ($product->isConfigurable()) {

            if (isset($source['super_attribute'])) {
                $attributes = $source['super_attribute'];
                $product = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes(
                    $attributes, $product
                );
                $product = Mage::getModel('catalog/product')->load($product->getId());
            } else {
                ITwebexperts_Payperrentals_Helper_Html::updateReservationError(
                    Mage::helper("payperrentals")->__("You need to select attributes")
                );
                return $this;
            }
        }
        return $product;

    }

    /**
     * @param $item
     * @param $product
     * @param $source
     * @param $qty
     * @param $startDate
     * @param $endDate
     */

    private function saveSimpleReservationProduct($item, $product, $source, $qty, $startDate, $endDate)
    {
        $turnoverAr = ITwebexperts_Payperrentals_Helper_Data::getTurnoverFromQuoteItemOrBuyRequest($product, $source, $startDate, $endDate);

        $BQuoteItem = Mage::getModel('payperrentals/reservationquotes')
            ->setProductId($product->getId())
            ->setStartDate($startDate)
            ->setEndDate($endDate)
            ->setStartTurnoverBefore($turnoverAr['before'])
            ->setEndTurnoverAfter($turnoverAr['after'])
            ->setQuoteItemId($item->getId())
            ->setQty($qty)
            ->setQuoteId($item->getQuote()->getId());
        if($product->getVendorId() && $product->getVendorId() != 0){
            $BQuoteItem->setVendorId($product->getVendorId());
        }

        Mage::dispatchEvent('ppr_set_stock', array('item' => $item, 'res_order' => $BQuoteItem));
        $BQuoteItem->save();
    }

    /**
     * @param $quoteItem
     * @param $product
     * @param $source
     * @param $qty
     * @param $startDate
     * @param $endDate
     */

    private function saveConfigurableReservationProduct($quoteItem, $product, $source, $qty, $startDate, $endDate)
    {
        $aChildQuoteItems = Mage::getModel("sales/quote_item")
            ->getCollection()
            ->setQuote($quoteItem->getQuote())
            ->addFieldToFilter("parent_item_id", $quoteItem->getId());

        $isConfigurable = false;
        if ($quoteItem->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE) {
            $isConfigurable = true;
        }
        foreach ($aChildQuoteItems as $cItems) {
            if ($cItems->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
                Mage::getResourceModel('payperrentals/reservationquotes')->deleteByQuoteItemAndDates(
                    $cItems, $startDate, $endDate
                );
                $this->saveSimpleReservationProduct($cItems, $product, $source, $qty, $startDate, $endDate);
                /**
                 * For some reason if product is configurable the quote item qty is 2 I have to set it to 1
                 * Bug from Magento or some stuff I'm missing
                 */
                if ($isConfigurable) {
                    $cItems->setQty(1);
                    $cItems->save();
                }
            }
        }
    }

    /**
     * @param $quoteItem
     * @param $source
     *
     * @return bool
     */

    private function excludedFromUpdate($quoteItem, $source)
    {
        if (ITwebexperts_Payperrentals_Helper_Data::isBuyout($quoteItem->getBuyRequest())) {
            return true;
        }

        if (!$quoteItem->hasDataChanges()) {
            return true;
        }
        /*if ($quoteItem->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
            return true;
        }*/
        if ($quoteItem->getParentItem() && $quoteItem->getParentItem()->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE) {
            return true;
        }
        if ($quoteItem->isDeleted()) {
            return true;
        }
        if (Mage::getModel('sales/order_item')->load($quoteItem->getId(), 'quote_item_id')->getId()) {
            return true;
        }

        if (!isset($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION]) || $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION] == '') {
            return true;
        }
        return false;
    }

    /**
     * When product is added to shopping cart add an entry to reservationquotes with reservation data
     * @param Varien_Event_Observer $event
     *
     * @return $this
     */

    public function updateCartReservation(Varien_Event_Observer $event)
    {
        /** @var $quoteItem Mage_Sales_Model_Quote_Item */
        $quoteItem = $event->getItem();

        /** @var $product Mage_Catalog_Model_Product */
        $product = $quoteItem->getProduct();

        $source = unserialize($product->getCustomOption('info_buyRequest')->getValue());

        if ($this->excludedFromUpdate($quoteItem, $source)) {
            return $this;
        }

        list($startDateArr, $endDateArr) = ITwebexperts_Payperrentals_Helper_Data::getStartEndDates($source);

        foreach ($startDateArr as $count => $startDate) {
            $endDate = $endDateArr[$count];
            if ($quoteItem->getProductType() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE) {

                $qty = $this->getSelectedQtyByTheCustomerForTheProduct($product, $quoteItem);
                $product = $this->getProduct($product, $source);

                if ($quoteItem->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
                    Mage::getResourceModel('payperrentals/reservationquotes')->deleteByQuoteItemAndDates(
                        $quoteItem, $startDate, $endDate
                    );
                    $this->saveSimpleReservationProduct($quoteItem, $product, $source, $qty, $startDate, $endDate);
                }
                $this->saveConfigurableReservationProduct($quoteItem, $product, $source, $qty, $startDate, $endDate);

            } else {
                $qty = $this->getSelectedQtyByTheCustomerForTheProduct($product, $quoteItem);
                ITwebexperts_Payperrentals_Helper_Data::createQuantities($source, $qty, true);
                $selectionIds = $source['bundle_option'];
                $selections = $product->getTypeInstance(true)->getSelectionsByIds($selectionIds, $product);
                Mage::getResourceModel('payperrentals/reservationquotes')->deleteByQuoteItemAndDates(
                    $quoteItem, $startDate, $endDate
                );
                foreach ($selections->getItems() as $selection) {

                    if ($selection->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
                        $qty = ITwebexperts_Payperrentals_Helper_Data::getQuantityForSelectionProduct($selection, $qty);
                        $this->saveSimpleReservationProduct($quoteItem, $selection, $source, $qty, $startDate, $endDate);
                    }
                }

            }
        }
        return $this;
    }

    /**
     * Event fired when quote is converted to order
     * @param Varien_Event_Observer $observer
     */

    public function convertQuoteToOrder(Varien_Event_Observer $observer)
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();

        if ($quote->getSendDatetime()) {
            $order->setSendDatetime($quote->getSendDatetime());
        }
        if ($quote->getReturnDatetime()) {
            $order->setReturnDatetime($quote->getReturnDatetime());
        }
    }

    /**
     * Event fired when order is placed
     * @param Varien_Event_Observer $observer
     */

    public function reserveInventoryAndSignature(Varien_Event_Observer $observer)
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();

        if (Mage::helper('payperrentals/config')->reserveInventoryNoInvoice()
            && !Mage::helper('payperrentals/config')->reserveByStatus()
        ) {
            $items = $observer->getEvent()->getOrder()->getItemsCollection();
            ITwebexperts_Payperrentals_Helper_Data::reserveOrder($items, $order);
        }
        if(!Mage::app()->getStore()->isAdmin()) {
            $this->saveSignature($observer);
        }
    }

    /**
     * Event fired when order status is changed
     * @param Varien_Event_Observer $observer
     */

    public function reserveInventoryByStatus(Varien_Event_Observer $observer)
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        $statusOrder = $observer->getEvent()->getOrder()->getStatus();
        $statusArr = explode(
            ',', Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_RESERVED_STATUSES)
        );
        if (Mage::helper('payperrentals/config')->reserveByStatus() && count($statusArr) > 0
            && in_array(
                $statusOrder, $statusArr
            )
        ) {
            $items = $observer->getEvent()->getOrder()->getItemsCollection();
            ITwebexperts_Payperrentals_Helper_Data::reserveOrder($items, $order);
        }
    }

    /**
     * Event fired when invoice is paid
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */

    public function convertToOrder(Varien_Event_Observer $observer)
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getInvoice()->getOrder();
        $statusOrder = $observer->getInvoice()->getOrder()->getStatus();
        $statusArr = explode(
            ',', Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_RESERVED_STATUSES)
        );

        if (!Mage::helper('payperrentals/config')->reserveInventoryNoInvoice()
            || Mage::helper('payperrentals/config')->reserveByStatus() && !in_array($statusOrder, $statusArr)
        ) {
            $items = $observer->getInvoice()->getOrder()->getItemsCollection();
            ITwebexperts_Payperrentals_Helper_Data::reserveOrder($items, $order);
        }


        return $this;
    }

    /**
     * Event fired before order is saved
     * @param Varien_Event_Observer $observer
     */

    public function saveOrderBefore(Varien_Event_Observer $observer)
    {
        $items = $observer->getEvent()->getOrder()->getItemsCollection();
        foreach ($items as $item) {
            $Product = Mage::getModel('catalog/product')->load($item->getProductId());
            if ($Product->getTypeId() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE
                && $Product->getTypeId() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE
            ) {
                continue;
            }

            $data = $item->getProductOptionByCode('info_buyRequest');
            if(isset($data[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION])) {
                $startDate = $data[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION];
                $endDate = $data[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION];
                $observer->getEvent()->getOrder()->setStartDatetime($startDate);
                $observer->getEvent()->getOrder()->setEndDatetime($endDate);
            }

            if (isset($data[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::NON_SEQUENTIAL])) {
                $nonSequential = $data[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::NON_SEQUENTIAL];
                if ($nonSequential == 1) {
                    $dates = explode(',', $startDate);
                }
            }

            if (isset($dates)) {
                foreach ($dates as $date) {
                    $coll = Mage::getModel('payperrentals/ordertodates')
                        ->getCollection()
                        ->addSelectFilter("orders_id='" . $observer->getEvent()->getOrder()->getId() . "'")
                        ->addSelectFilter("event_date='" . $date . "'");
                    if ($coll->getSize() == 0) {
                        $model = Mage::getModel('payperrentals/ordertodates')
                            ->setOrdersId($observer->getEvent()->getOrder()->getId())
                            ->setEventDate($date);
                        $model->setId(null)->save();
                    }
                }
            }

            break;
        }
        Mage::unregister('already_saved');
        Mage::getSingleton('core/session')->unsetData('startDateInitial');
        Mage::getSingleton('core/session')->unsetData('endDateInitial');
    }

    /**
     * Event fired when order is cancelled
     * @param Varien_Event_Observer $observer
     */

    public function cancelOrder(Varien_Event_Observer $observer)
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        if ($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED
            || $order->getState() == Mage_Sales_Model_Order::STATE_CLOSED
        ) {
            foreach ($order->getItemsCollection() as $iItem) {
                if ($iItem->getParentItem()) {
                    $qty = $iItem->getParentItem()->getQtyCanceled();
                } else {
                    $qty = $iItem->getQtyCanceled();
                }
                Mage::getResourceSingleton('payperrentals/reservationorders')->cancelByOrderItemId(
                    $order->getId(), $iItem->getItemId(), $qty
                );
                ITwebexperts_Payperrentals_Helper_Inventory::updateInventory($iItem->getProductId());
            }
        }
    }

    /**
     * Evcent fired after saving the credit memo
     * @param Varien_Event_Observer $observer
     */

    public function creditMemoRefund(Varien_Event_Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        /** @var $order Mage_Sales_Model_Order */
        $order = $creditmemo->getOrder();
        foreach ($creditmemo->getAllItems() as $iItem) {
            Mage::getResourceSingleton('payperrentals/reservationorders')->cancelByOrderItemId(
                $order->getId(), $iItem->getOrderItemId(), $iItem->getQty()
            );
            ITwebexperts_Payperrentals_Helper_Inventory::updateInventory($iItem->getOrderItem()->getProduct()->getId());
        }
    }

    /**
     * Event fired when order item is cancelled, refunded
     * @param Varien_Event_Observer $observer
     */

    public function cancelOrderItem(Varien_Event_Observer $observer)
    {
        $orderItem = $observer->getEvent()->getDataObject();
        if (!($orderItem instanceof Mage_Sales_Model_Order_Item)) {
            return;
        }
        if ($orderItem->getStatusId() == Mage_Sales_Model_Order_Item::STATUS_REFUNDED) {

            if ($orderItem->getParentItem()) {
                $qty = $orderItem->getParentItem()->getQtyCanceled();
            } else {
                $qty = $orderItem->getQtyCanceled();
            }
            Mage::getResourceSingleton('payperrentals/reservationorders')->cancelByOrderItemId(
                $orderItem->getOrder()->getId(), $orderItem->getItemId(), $qty
            );
            ITwebexperts_Payperrentals_Helper_Inventory::updateInventory($orderItem->getProductId());
        }
    }

    /**
     * Event fired to show dates excluded form in product edit
     * @param $observer
     */

    public function showExclude(Varien_Event_Observer $observer)
    {
        $form = $observer->getForm();
        if ($excludedDates = $form->getElement('res_excluded_dates')) {
            $excludedDates->setRenderer(
                Mage::getSingleton('core/layout')->createBlock(
                    'payperrentals/adminhtml_catalog_product_edit_tab_payperrentals_excludeddates'
                )
            );
        }
    }

    /**
     * Event fired before saving customer data
     * @param Varien_Event_Observer $observer
     */

    public function saveCustomerData(Varien_Event_Observer $observer)
    {

        $data = $observer['request']->getPost();
        $customer = $observer['customer'];
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setEntity($customer)
            ->setFormCode('adminhtml_customer_membership')
            ->ignoreInvisible(false);

        $formData = $customerForm->extractData($observer['request'], 'membership');
        $formData['membershippackage_cc'] = Mage::getModel('core/encryption')->encrypt($formData['membershippackage_cc']);
        $errors = $customerForm->validateData($formData);
        if ($errors !== true) {
            foreach ($errors as $error) {
                Mage::getSingleton('adminhtml/session')->addError($error);
            }
            Mage::getSingleton('adminhtml/session')->setCustomerData($data);
            Mage::app()->getResponse()->setRedirect(
                $this->getUrl('*/customer/edit', array('id' => $customer->getId()))
            );
            return;
        }

        $customerForm->compactData($formData);
    }

    /**
     * Event fired to show the serial numbers form on product edit
     * @param Varien_Event_Observer $observer
     */

    public function showSerialnumbers(Varien_Event_Observer $observer)
    {
        $form = $observer->getForm();
        if ($serialNumbers = $form->getElement('res_serialnumbers')) {
            $serialNumbers->setRenderer(
                Mage::getSingleton('core/layout')->createBlock(
                    'payperrentals/adminhtml_catalog_product_edit_tab_payperrentals_serialnumbers','serialnumbers'
                )
            );
        }
    }

    /**
     * Event fired to show the prices form on product edit
     * @param Varien_Event_Observer $observer
     */

    public function showPrices(Varien_Event_Observer $observer)
    {
        $form = $observer->getForm();
        if ($reservationPrices = $form->getElement('res_prices')) {
            $reservationPrices->setRenderer(
                Mage::getSingleton('core/layout')->createBlock(
                    'payperrentals/adminhtml_catalog_product_edit_tab_payperrentals_reservationprices'
                )
            );
        }
    }

    /**
     * Event fired before product/edit/form is saved so it keeps the selected data if an error occurs
     * @param Varien_Event_Observer $event
     */

    public function editBeforeSave(Varien_Event_Observer $event)
    {
        $form = $event['form'];
        if (Mage::getSingleton('adminhtml/session')->getData('ppr')) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getData('ppr'));
            Mage::getSingleton('adminhtml/session')->unsetData('ppr');
        }
    }

    /**
     * Prepare request data for add to cart, fired in the model/reservation/prepareaddtocart
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */

    public function prepareBuyRequestCartAdvanced(Varien_Event_Observer $observer)
    {
        $buyRequest = $observer->getEvent()->getBuyRequest();
        $requestParams = Mage::app()->getRequest()->getParams();

        if (Mage::app()->getRequest()->getControllerName() != 'cart'
            || Mage::app()->getRequest()->getActionName() != 'add'
            || !$buyRequest
            || !$requestParams
        ) {
            return $this;
        }

        if (count($buyRequest->getData()) != count($requestParams)) {
            $buyRequest->setData($requestParams);
        }

        return $this;
    }

    /**
     * Event fired before sending request to paypal
     * @param Varien_Event_Observer $evt
     */

    public function updatePaypalTotal(Varien_Event_Observer $evt)
    {
        $paypalCart = $evt['paypal_cart'];
        if ($paypalCart->getSalesEntity()->getDepositpprAmount()) {
            $paypalCart->addItem(
                Mage::helper('payperrentals')->__('Rental Deposit'), 1,
                $paypalCart->getSalesEntity()->getDepositpprAmount(), 'rentaldeposit'
            );
        }
    }

    /**
     * Event fired before quote is submitted
     * @param Varien_Event_Observer $observer
     */

    public function quoteSubmitBefore(Varien_Event_Observer $observer)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();
        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();

        Mage::unregister(self::AUTH_NET_REGISTER_CODE);
        if ($quote->getDepositpprAmount()) {
            $methodInstance = $quote->getPayment()->getMethodInstance();
            switch ($methodInstance->getCode()) {
                case Mage_Paygate_Model_Authorizenet::METHOD_CODE:
                    $payment = clone $order->getPayment();
                    Mage::register(self::AUTH_NET_REGISTER_CODE, $payment);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Event fired after quote is submitted
     * @param Varien_Event_Observer $observer
     */

    public function quoteSubmitAfter(Varien_Event_Observer $observer)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();
        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        if ($quote->getDepositpprAmount()) {
            if (Mage::registry(self::AUTH_NET_REGISTER_CODE)) {
                $payment = Mage::registry(self::AUTH_NET_REGISTER_CODE);
                $payment->setOrder($order);
                $payment->setAmountOrdered($quote->getDepositpprAmount());
                $payment->setBaseAmountOrdered($quote->getDepositpprAmount());
                $payment->setShippingAmount(0);
                $payment->setBaseShippingAmount(0);
                $payment->authorize(true, $quote->getDepositpprAmount());
                $payment->save();
                $originTransaction = $payment->getCreatedTransaction();
                if ($originTransaction) {
                    $pprTransaction = Mage::getModel('payperrentals/sales_payment_transaction');
                    $pprTransaction->setData($originTransaction->getData());
                    $pprTransaction->setOrderPaymentObject($payment);
                    $pprTransaction->save();
                }
            }
        }
    }

    /**
     * Event fired when order collection is loaded, to get the rest of the fields to show into the order grid
     * @param Varien_Event_Observer $observer
     */

    public function salesOrderGridCollectionLoadBefore(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getOrderGridCollection();
        $collection = (!$collection) ? Mage::getResourceModel('sales/order_grid_collection') : $collection;

        $resource = Mage::getSingleton('core/resource');
        $collection->getSelect()->joinLeft(
            array('sfo' => $resource->getTableName('sales/order')),
            'sfo.entity_id=main_table.entity_id', array('sfo.total_qty_ordered as total_qty_ordered')
        );
    }

    /**
     * Event fired when invoice collection is loaded, to get the rest of the fields to show into the invoice grid
     * @param Varien_Event_Observer $observer
     */

    public function salesOrderInvoiceCollectionLoadBefore(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getOrderInvoiceGridCollection();
        $collection = (!$collection) ? Mage::getResourceModel('sales/order_invoice_grid_collection') : $collection;

        $resource = Mage::getSingleton('core/resource');
        $collection->getSelect()->joinLeft(
            array('sfo' => $resource->getTableName('sales/order')),
            'sfo.entity_id=main_table.order_id',
            array('main_table.increment_id as incr_id', 'sfo.start_datetime as start_datetime',
                'sfo.end_datetime as end_datetime')
        );

    }

    /**
     * Event fired when creditmemo collection is loaded, to get the rest of the fields to show into the credit memo grid
     * @param Varien_Event_Observer $observer
     */

    public function salesOrderCreditmemoCollectionLoadBefore(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getOrderCreditmemoGridCollection();
        $collection = (!$collection) ? Mage::getResourceModel('sales/order_creditmemo_grid_collection') : $collection;

        $resource = Mage::getSingleton('core/resource');
        $collection->getSelect()->joinLeft(
            array('sfo' => $resource->getTableName('sales/order')),
            'sfo.entity_id=main_table.order_id',
            array('sfo.start_datetime as start_datetime', 'sfo.end_datetime as end_datetime')
        );

    }

    /**
     * Event fired when shipment collection is loaded, to get the rest of the fields to show into the shipment grid
     * @param Varien_Event_Observer $observer
     */

    public function salesOrderShipmentCollectionLoadBefore(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getOrderShipmentGridCollection();
        $collection = (!$collection) ? Mage::getResourceModel('sales/order_shipment_grid_collection') : $collection;

        $resource = Mage::getSingleton('core/resource');
        $collection->getSelect()->joinLeft(
            array('sfow' => $resource->getTableName('sales/order')),
            'sfow.entity_id=main_table.order_id',
            array('sfow.start_datetime as sstart_datetime', 'sfow.end_datetime as send_datetime')
        );

    }

    /**
     * Event fired when setting shipping price on checkout after address is saved
     * @param Varien_Event_Observer $observer
     */

    public function setShippingCostOnExtend(Varien_Event_Observer $observer){

        $quote = $observer->getQuote();

        // Check your customer attribute of choice here instead of the customer id
        if (Mage::getSingleton('checkout/session')->getIsExtendedQuote() || Mage::getSingleton('adminhtml/session_quote')->getIsExtendedQuote()) {

            $store = Mage::app()->getStore($quote->getStoreId());
            $carriers = Mage::getStoreConfig('carriers', $store);
            foreach ($carriers as $carrierCode => $carrierConfig) {

                // F for fixed, P for percentage
                $store->setConfig("carriers/{$carrierCode}/price", '0');

            }
        }
    }

    protected function getRentalEditUrl($block){
        if(Mage::helper('itwebcommon')->isVendorAdmin()){
            return $block->getUrl(
                'vendors/manualreserve/edit', array('order_id' => $block->getOrder()->getId()));
        } else {
            return $block->getUrl(
                'payperrentals_admin/adminhtml_manualreserve/edit', array('order_id' => $block->getOrder()->getId()));
        }
    }

    /**
     * Start List of functions which handle the grids actions
     * todo refactor all these
     */

    public function addExportAction($observer)
    {
        $block = $observer->getEvent()->getBlock();

        /** @var $block Mage_Adminhtml_Block_Widget_Grid_Massaction */
        if (get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction'
            && $block->getRequest()->getControllerName() == 'sales_order'
        ) {
            $block->addItem(
                'payperrentals1', array(
                    'label' => Mage::helper('payperrentals')->__('Delete Order Completely'),
                    'url' => Mage::helper('adminhtml')->getUrl('payperrentals/adminhtml_salesgrid/massDelete'),
                )
            );

            $block->addItem(
                'payperrentals2', array(
                    'label' => Mage::helper('payperrentals')->__('Reserve Inventory'),
                    'url' => Mage::helper('adminhtml')->getUrl('payperrentals/adminhtml_salesgrid/massReserve'),
                )
            );
        }

        if(get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction' && $block->getRequest()->getControllerName() == 'catalog_product') {

            $convertoptions = Mage::getSingleton('payperrentals/product_convert')->getOptionArray();
            array_unshift($convertoptions, array('label'=>'', 'value'=>''));

            $block->addItem('convert', array(
                'label' =>  Mage::helper('payperrentals')->__('Convert product type'),
                'url'   =>  Mage::helper('adminhtml')->
                getUrl('payperrentals/adminhtml_productgrid/massConvert', array('_current'=>true)),
                'additional' =>  array(
                    'visibility'    =>  array(
                        'name'  =>  'convertoption',
                        'type'  =>  'select',
                        'class' =>  'required-entry',
                        'label' =>  Mage::helper('payperrentals')->__("Convert"),
                        'values'    =>  $convertoptions
                    )
                )
            ));
        }

        /** @var $block Mage_Adminhtml_Block_Sales_Order_View */
        if ($block->getType() == 'adminhtml/sales_order_view') {
            $order = $block->getOrder();
            $_returnCollection = Mage::getResourceModel('payperrentals/sendreturn_collection')
                ->addFieldToFilter('order_id', array('in' => array($order->getId(), $order->getIncrementId())));
            $_totalQtyReturned = 0;

            $_returnedUnixtimeDate = 0;
            foreach($_returnCollection as $_returnItem) {
                if($_returnItem->getReturnDate() != '0000-00-00 00:00:00' && $_returnItem->getReturnDate() != '1970-01-01 00:00:00')
                {
                    $_totalQtyReturned += $_returnItem->getQty();
                    if ($_returnedUnixtimeDate < strtotime($_returnItem->getReturnDate())) {
                        $_returnedUnixtimeDate = strtotime($_returnItem->getReturnDate());
                    }
                }
            }

            $_shipmentCollection = Mage::getResourceModel('payperrentals/sendreturn_collection')
                ->addFieldToFilter('order_id', array('in' => array($order->getEntityId(), $order->getIncrementId())));
            $_totalQtyShipped = 0;
            foreach($_shipmentCollection as $_shipmentItem) {
                if($_shipmentItem->getSendDate() != '0000-00-00 00:00:00' && $_shipmentItem->getSendDate() != '1970-01-01 00:00:00') {
                    $_totalQtyShipped += $_shipmentItem->getQty();

                }
            }

            if ((!$_totalQtyReturned) || ($_totalQtyReturned < $_totalQtyShipped)) {
                $block->addButton(
                    'order_return', array(
                        'label' => Mage::helper('payperrentals')->__('Return'),
                        'onclick' => 'setLocation(\'' . $block->getUrl(
                                '*/sales_order_return/new', array('order_id' => $block->getOrder()->getId())
                            ) . '\')',
                    )
                );
            }
            $block->addButton(
                'order_edit_dates', array(
                    'label' => Mage::helper('payperrentals')->__('Edit Rental Dates'),
                    'onclick' => 'setLocation(\'' . $this->getRentalEditUrl($block) . '\')',
                )
            );
            if(ITwebexperts_Payperrentals_Helper_Extend::isExtensibleOrder($block->getOrder()->getId())) {
                $block->addButton(
                    'order_extend', array(
                        'label'   => Mage::helper('payperrentals')->__('Extend Order'),
                        'onclick' => 'extendOrder(\'' . $block->getOrder()->getId() . '\', \'' . $block->getOrder()->getIncrementId() . '\')',
                    )
                );
            }

            if(ITwebexperts_Payperrentals_Helper_Data::isLateOrder($block->getOrder()->getId())) {
                if(!Mage::helper('itwebcommon')->isVendorAdmin()) {
                $block->addButton(
                    'order_extend', array(
                        'label'   => Mage::helper('payperrentals')->__('Charge Late Fee'),
                        'onclick' => 'showLateFeePopup(' . '\'popup_form_policy\', '.$block->getOrder()->getId() . ')',
                    )
                );
            }

            }
            $block->addButton(
                'rentalcontract', array(
                    'label' => Mage::helper('payperrentals')->__('Rental Contract'),
                    'onclick' => 'setLocation(\'' . $block->getUrl(
                            'payperrentals_admin/adminhtml_rentalcontract/generate', array('order_id' => $block->getOrder()->getId())
                        ) . '\')',
                )
            );
            if(Mage::helper('payperrentals')->orderContainsReservation($block->getOrder()->getId()) && Mage::helper('payperrentals/config')->enabledDigitalSignature()){
            $block->addButton(
                'signature', array(
                    'label' => Mage::helper('payperrentals')->__('Capture Signature'),
                    'onclick' => 'showSignaturePopup(\'' . $block->getUrl(
                            'payperrentals_admin/adminhtml_signature/view', array('order_id' => $block->getOrder()->getId())
                        ) . '\')',
                )
            );
        }
        }
    }

    public function filterCallbackSerials($collection, $column)
    {
        /** @var $column Mage_Adminhtml_Block_Widget_Grid_Column */
        $value = $column->getFilter()->getValue();


        $serialCollection = Mage::getModel('payperrentals/serialnumbers')->getCollection();
        $serialTable = $serialCollection->getTable('payperrentals/serialnumbers');

        /** @var $collection Mage_Sales_Model_Mysql4_Order_Collection */
        $collection->getSelect()->joinLeft(
            array("ni" => $serialTable), "e.entity_id = ni.entity_id", array('ni.*')
        );

        $collection->getSelect()->where(
                "FIND_IN_SET(ni.sn, '" . $value . "')"
            );

        $collection->getSelect()
            ->group('e.entity_id');

    }

    public function filterCallbackDates($collection, $column)
    {
        /** @var $column Mage_Adminhtml_Block_Widget_Grid_Column */
        $value = $column->getFilter()->getValue();
        if (isset($value['from'])) {
            $from = $value['from']->get('YYYY-MM-dd HH:mm:ss');
        }
        if (isset($value['to'])) {
            $to = $value['to']->get('YYYY-MM-dd HH:mm:ss');
        }

        $orderToDatesCollection = Mage::getModel('payperrentals/ordertodates')->getCollection();
        $orderToDates = $orderToDatesCollection->getTable('payperrentals/ordertodates');

        /** @var $collection Mage_Sales_Model_Mysql4_Order_Collection */
        $collection->getSelect()->joinLeft(
            array("ni" => $orderToDates), "main_table.entity_id = ni.orders_id", array('ni.*')
        );
        if (isset($from) && isset($to)) {
            $collection->getSelect()->where(
                "ni.event_date>='" . $from . "' AND ni.event_date<='" . $to . "'"
            );
        } elseif (isset($from)) {
            $collection->getSelect()->where(
                "ni.event_date>='" . $from . "'"
            );
        } elseif (isset($to)) {
            $collection->getSelect()->where(
                "ni.event_date<='" . $to . "'"
            );
        }
        $collection->getSelect()
            ->group('main_table.entity_id');

    }

    protected function getShippingRenderer(){
        if(Mage::helper('itwebcommon')->isVendorAdmin()){
            return 'simarketplace/adminhtml_grid_column_renderer_shippingState';
        } else {
            return 'payperrentals/adminhtml_grid_column_renderer_shippingState';
        }
    }

    protected function getReturnRenderer(){
        if(Mage::helper('itwebcommon')->isVendorAdmin()){
            return 'simarketplace/adminhtml_grid_column_renderer_returnState';
        } else {
            return 'payperrentals/adminhtml_grid_column_renderer_returnState';
        }
    }

    /**
     * @var $_block Mage_Adminhtml_Block_Widget_Grid
     * @return $this
     * */
    public function appendCustomColumns($_observer)
    {
        $_block = $_observer->getBlock();
        if (!isset($_block)) {
            return $this;
        }
        if ($_block->getType() == 'adminhtml/catalog_product_grid') {
            if (!Mage::helper('payperrentals/config')->hideBookedInventoryInGrid(Mage_Core_Model_App::ADMIN_STORE_ID)) {
                /** @var $_block ITwebexperts_Payperrentals_Block_Adminhtml_Catalog_Product_Grid */
                $_block->addColumnAfter(
                    'booked_qty', array(
                        'header' => Mage::helper('payperrentals')->__('Booked'),
                        'index' => 'entity_id',
                        'width' => '60px',
                        'align' =>  'right',
                        'type' => 'number',
                        'renderer' => 'payperrentals/adminhtml_catalog_product_renderer_booked',
                        'sortable' => false,
                        'filter' => false,
                    ), 'qty'
                );
                $after = 'booked_qty';
                if(ITwebexperts_Payperrentals_Helper_Data::isMaintenanceInstalled()){
                    $_block->addColumnAfter(
                        'maintenance_qty', array(
                            'header' => Mage::helper('payperrentals')->__('Maintenance'),
                            'index' => 'entity_id',
                            'align' =>  'right',
                            'width' => '70px',
                            'type' => 'number',
                            'renderer' => 'ITwebexperts_Maintenance_Block_Adminhtml_Catalog_Product_Renderer_Maintenance',
                            'sortable' => false,
                            'filter' => false,
                        ), 'booked_qty'
                    );
                    $after = 'maintenance_qty';
                }

                $_block->addColumnAfter(
                    'available_inventory', array(
                        'header' => Mage::helper('payperrentals')->__('Available'),
                        'index' => 'entity_id',
                        'width' => '60px',
                        'type' => 'number',
                        'align' =>  'right',
                        'sortable' => false,
                        'filter' => false,
                        'renderer' => 'payperrentals/adminhtml_catalog_product_renderer_available'
                    ), $after
                );
                if(Mage::helper('payperrentals/config')->showSerialsColumn()){
                    $_block->addColumnAfter(
                        'serials', array(
                        'header' => Mage::helper('payperrentals')->__('Serials'),
                        'index' => 'entity_id',
                        'width' => '60px',
                        'type' => 'text',
                        'align' =>  'left',
                        'filter_condition_callback' => array($this, 'filterCallbackSerials'),
                        'renderer' => 'payperrentals/adminhtml_catalog_product_renderer_serials'
                    ), 'available_inventory'
                    );
                }

            }

            Mage::register('is_product_grid', true);
        }

        /** Changed to preg_match so vendor module also matches the pattern */
        $blocktype = $_block->getType();
        if (preg_match('/.*sales_order_grid$/',$blocktype) ||
            preg_match('/.*sales_invoice_grid$/',$blocktype) ||
            preg_match('/.*sales_shipment_grid$/',$blocktype) ||
            preg_match('/.*sales_creditmemo_grid$/',$blocktype)
        ) {
            if (!Mage::helper('payperrentals/config')->isNonSequentialSelect(Mage::app()->getStore()->getId())) {
                if (preg_match('/.*sales_order_grid$/',$blocktype)) {
                    /** @var $_block Mage_Adminhtml_Block_Sales_Order_Grid */
                    $_block->addColumnAfter(
                        'sfo_start_datetime', array(
                            'header' => Mage::helper('payperrentals')->__('Start Date'),
                            'index' => 'start_datetime',
                            'renderer' => 'payperrentals/adminhtml_html_renderer_datetime',
                        'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
                            'width' => '120px',
                            'type' => 'datetime',
                            'filter_index' => 'main_table.start_datetime',
                        ), 'shipping_name'
                    );
                    $_block->addColumnAfter(
                        'sfo_end_datetime', array(
                            'header' => Mage::helper('payperrentals')->__('End Date'),
                            'index' => 'end_datetime',
                            'renderer' => 'payperrentals/adminhtml_html_renderer_datetime',
                        'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
                            'width' => '120px',
                            'type' => 'datetime',
                            'filter_index' => 'main_table.end_datetime',
                        ), 'sfo_start_datetime'
                    );
                } else {
                    /** @var $_block Mage_Adminhtml_Block_Sales_Order_Grid */
                    if (preg_match('/.*adminhtml\/sales_invoice_grid$/',$blocktype)) {
                        $_block->addColumnAfter(
                            'sfo_incr_id', array(
                                'header' => Mage::helper('payperrentals')->__('Invoice #'),
                                'width' => '160px',
                                'type' => 'text',
                                'index' => 'incr_id',
                                'filter_condition_callback' => array($this, 'filterCallbackIncrement')
                            ), 'massaction'
                        );
                    }
                    if (preg_match('/.*adminhtml\/sales_shipment_grid$/',$blocktype)) {
                        $_block->addColumnAfter(
                            'start_datetime', array(
                            'header' => Mage::helper('payperrentals')->__('Start Date'),
                            'index' => 'sstart_datetime',
                            'renderer' => 'payperrentals/adminhtml_html_renderer_datetime',
                            'filter' => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
                            'width' => '120px',
                            'type' => 'datetime',
                            'filter_index' => 'sfow.start_datetime',
                        ), 'billing_name'
                        );
                        $_block->addColumnAfter(
                            'sfo_end_datetime', array(
                            'header' => Mage::helper('payperrentals')->__('End Date'),
                            'index' => 'send_datetime',
                            'renderer' => 'payperrentals/adminhtml_html_renderer_datetime',
                            'filter' => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
                            'width' => '120px',
                            'type' => 'datetime',
                            'filter_index' => 'sfow.end_datetime',
                        ), 'sfo_start_datetime'
                        );
                    }else{
                        $_block->addColumnAfter(
                            'start_datetime', array(
                            'header' => Mage::helper('payperrentals')->__('Start Date'),
                            'index' => 'start_datetime',
                            'renderer' => 'payperrentals/adminhtml_html_renderer_datetime',
                            'filter' => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
                            'width' => '120px',
                            'type' => 'datetime',
                            'filter_index' => 'sfo.start_datetime',
                        ), 'billing_name'
                        );
                        $_block->addColumnAfter(
                            'sfo_end_datetime', array(
                            'header' => Mage::helper('payperrentals')->__('End Date'),
                            'index' => 'end_datetime',
                            'renderer' => 'payperrentals/adminhtml_html_renderer_datetime',
                            'filter' => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
                            'width' => '120px',
                            'type' => 'datetime',
                            'filter_index' => 'sfo.end_datetime',
                        ), 'sfo_start_datetime'
                        );
                    }
                    $_block->sortColumnsByOrder();
                }
                if (!preg_match('/.*sales_invoice_grid$/',$blocktype) && !preg_match('/.*sales_shipment_grid$/',$blocktype)) {
                    $_block->addColumnAfter(
                        'shipping_state', array(
                            'header' => Mage::helper('payperrentals')->__('Shipping'),
                            'index' => 'total_qty_shipped',
                        'renderer' => $this->getShippingRenderer(),
                            'width' => '120px',
                            'type' => 'options',
                            'options' => Mage::getSingleton('payperrentals/sendreturn')->_getShippingStates(),
                            'sortable' => false,
                            'filter' => false,
                        ), 'status'
                    );
                    $_block->addColumnAfter(
                        'return_state', array(
                            'header' => Mage::helper('payperrentals')->__('Return'),
                            'index' => 'total_qty_returned',
                        'renderer' => $this->getReturnRenderer(),
                            'width' => '120px',
                            'type' => 'options',
                            'options' => Mage::getSingleton('payperrentals/sendreturn')->_getReturnStates(),
                            'sortable' => false,
                            'filter' => false,
                        ), 'shipping_state'
                    );
                }
            } else {
                if (!Mage::helper('payperrentals/config')->isNonSequentialSelect(Mage::app()->getStore()->getId())) {
                    if (preg_match('/.*sales_order_grid$/',$blocktype)) {
                        $_block->addColumnAfter(
                            'sfo_start_datetime', array(
                                'header' => Mage::helper('payperrentals')->__('Start Date'),
                                'index' => 'start_datetime',
                                'renderer' => 'payperrentals/adminhtml_html_renderer_datetime',
                            'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
                                'width' => '120px',
                                'type' => 'datetime',
                                'filter_index' => 'main_table.start_datetime',
                            ), 'shipping_name'
                        );
                        $_block->addColumnAfter(
                            'sfo_end_datetime', array(
                                'header' => Mage::helper('payperrentals')->__('End Date'),
                            'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
                                'index' => 'end_datetime',
                                'renderer' => 'payperrentals/adminhtml_html_renderer_datetime',
                                'width' => '120px',
                                'type' => 'datetime',
                                'filter_index' => 'main_table.end_datetime',
                            ), 'sfo_start_datetime'
                        );
                        $_block->addColumnAfter(
                            'shipping_state', array(
                                'header' => Mage::helper('payperrentals')->__('Shipping'),
                                'index' => 'total_qty_shipped',
                                'renderer' => 'payperrentals/adminhtml_grid_column_renderer_shippingState',
                                'width' => '120px',
                                'type' => 'options',
                                'options' => Mage::getSingleton('payperrentals/sendreturn')->_getShippingStates(),
                                'sortable' => false,
                                'filter' => false,
                            ), 'status'
                        );
                        $_block->addColumnAfter(
                            'return_state', array(
                                'header' => Mage::helper('payperrentals')->__('Return'),
                                'index' => 'total_qty_returned',
                                'renderer' => 'payperrentals/adminhtml_grid_column_renderer_returnState',
                                'width' => '120px',
                                'type' => 'options',
                                'options' => Mage::getSingleton('payperrentals/sendreturn')->_getReturnStates(),
                                'sortable' => false,
                                'filter' => false,
                            ), 'shipping_state'
                        );
                    } else {
                        $_block->addColumnAfter(
                            'sfo_start_datetime', array(
                                'header' => Mage::helper('payperrentals')->__('Start Date'),
                                'index' => 'start_datetime',
                                'renderer' => 'payperrentals/adminhtml_html_renderer_datetime',
                            'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
                                'width' => '120px',
                                'type' => 'datetime',
                                'filter_index' => 'sfo.start_datetime',
                            ), 'shipping_name'
                        );
                        $_block->addColumnAfter(
                            'sfo_end_datetime', array(
                                'header' => Mage::helper('payperrentals')->__('End Date'),
                                'index' => 'end_datetime',
                                'renderer' => 'payperrentals/adminhtml_html_renderer_datetime',
                            'filter'    => 'payperrentals/adminhtml_widget_grid_column_filter_datetimeppr',
                                'width' => '120px',
                                'type' => 'datetime',
                                'filter_index' => 'sfo.end_datetime',
                            ), 'sfo_start_datetime'
                        );
                    }
                } else {
                    $_block->addColumnAfter(
                        'sfo_dates', array(
                            'header' => Mage::helper('payperrentals')->__('Dates'),
                            'index' => 'entity_id',
                            'renderer' => 'payperrentals/adminhtml_html_renderer_dates',
                            'width' => '120px',
                            'type' => 'datetime',
                            'filter_condition_callback' => array($this, 'filterCallbackDates')
                        ), 'shipping_name'
                    );
                }
            }

        }

        return $this;
    }

    /**
     * @var $_block Mage_Adminhtml_Block_Widget_Grid
     * @return $this
     * */
    public function appendDateFields($_observer)
    {
        $_block = $_observer->getBlock();
        if (!isset($_block)) {
            return $this;
        }

        if (($_block->getType() == 'adminhtml/catalog_product_grid')
            && (!Mage::helper('payperrentals/config')->hideBookedInventoryInGrid(Mage_Core_Model_App::ADMIN_STORE_ID))
        ) {
            /** @var $_block ITwebexperts_Payperrentals_Block_Adminhtml_Catalog_Product_Grid */
            $resetButton = $_block->getChild('reset_filter_button')->toHtml();
            $_block->setChild(
                'reset_filter_button',
                $_block->getLayout()
                    ->createBlock('core/template')
                    ->setTemplate('payperrentals/product/grid/dates.phtml')
                    ->setData(array('reset_button' => $resetButton))
            );
        }

        return $this;
    }

    public function filterCallbackIncrement($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $collection->getSelect()->where("main_table.increment_id LIKE ?", '%' . $value . '%');
    }

    public function getAllIds($collection)
    {
        $idsSelect = clone $collection->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);

        $idsSelect->columns($collection->getResource()->getIdFieldName(), 'e');
        return $collection->getConnection()->fetchCol($idsSelect);
    }

    public function loadBeforeCollection($observer){
        $productCollectionObj = $observer->getEvent()->getCollection();
        $productCollectionIds = $this->getAllIds($productCollectionObj);
        $inventoryHelper = Mage::helper('payperrentals/inventory');
        $configHelper = Mage::helper('payperrentals/config');
        if($configHelper->hideProductsNotAvailable() && Mage::app()->getRequest()->getModuleName() != 'checkout') {
            $startDate = Mage::getSingleton('core/session')->getData('startDateInitial');
            $endDate = Mage::getSingleton('core/session')->getData('endDateInitial');
            $productsArr = array();
            if ($startDate && $endDate) {
                foreach ($productCollectionIds as $productId) {
                    if (ITwebexperts_Payperrentals_Helper_Data::isReservationOnly($productId)) {
                        $isAvailable = $inventoryHelper->isAvailable(
                            $productId, $startDate, $endDate, 1);
                        if ($isAvailable) {
                            $productsArr[] = $productId;
                        }
                    } else {
                        $productsArr[] = $productId;
                    }
                }
            }
            $productCollectionObj->addIdFilter($productsArr);
        }
        return $this;
    }



    /**
     * @var $_block Mage_Adminhtml_Block_Widget_Grid
     * @return $this
     * */
    public function renameQtyColumn($_observer)
    {
        $block = $_observer->getBlock();
        if (!isset($block)) {
            return $this;
        }
        /**@var $block Mage_Adminhtml_Block_Widget_Grid */
        if ($block->getType() == 'adminhtml/catalog_product_grid') {
            $qtyColumn = $block->getColumn('qty');
            if ($qtyColumn || $qtyColumn = $qtyColumn = $block->getColumn('qtys')) {
                $qtyColumn->setHeader(Mage::helper('payperrentals')->__('Total Inventory'));
                $qtyColumn->setData('renderer', 'payperrentals/adminhtml_catalog_product_renderer_qty');
                $priceColumn = $block->getColumn('price');
                if (is_object($priceColumn)) {
                    $priceColumn->setData('renderer', 'payperrentals/adminhtml_catalog_product_renderer_price');
                    $priceColumn->setData('filter', 'payperrentals/adminhtml_widget_grid_column_filter_price');

                    $priceColumn->setData('filter_condition_callback', array(
                        Mage::getBlockSingleton('payperrentals/adminhtml_widget_grid_column_filter_price'),
                        '_filterPrice'
                    ));
                }
            }
        }
        if ($block->getType() == 'adminhtml/sales_invoice_grid'
            || $block->getType() == 'adminhtml/sales_shipment_grid'
            || $block->getType() == 'adminhtml/sales_creditmemo_grid'
        ) {
            $block->removeColumn('increment_id');
        }

        return $this;
    }

    /**
     * Attach rental contract to new order email
     *
     * @param $observer
     */
    public function sendContract($observer)
    {
        $mailTemplate = $observer->getEvent()->getTemplate();
        $order = $observer->getEvent()->getObject();
        $storeId = $order->getStoreId();

        if (Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_ATTACHCONTRACT, $storeId)) {
            //Create Pdf and attach to email - play nicely with PdfCustomiser
            $pdf = Mage::getModel('payperrentals/contractpdf')->renderContract($order,'F');
            $orderAttachmentName = Mage::getModel('payperrentals/contractpdf')->getContractFilename($order);
            $mailTemplate = Mage::helper('payperrentals/emails')->addFileAttachment($pdf,$mailTemplate);
        }
        $test = 'test';

    }

    /**
     * Saves rental contract signature to order
     *
     * @param Varien_Event_Observer $observer
     */

    public function saveSignature(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $post = Mage::app()->getFrontController()->getRequest()->getPost();
        $date = Mage::getModel('core/date')->date('Y-m-d');
        $signature = $post['signaturecode'];
        $signaturetext = $post['typedsignature'];
        $signatureimage = $post['signatureimage'];
        $signatureimage = str_replace('image/svg+xml,', '', $signatureimage);
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
    }

    /**
     * Checks if newly added product to cart uses same start and end dates as the other products
     *
     * @param Varien_Event_Observer $_observer
     */

    public function sameDatesAllProducts(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('payperrentals');
        if (!ITwebexperts_Payperrentals_Helper_Data::isUsingGlobalDatesShoppingCart() && Mage::getStoreConfig('payperrentals/calendar_options/enforce_same_dates') == 1) {
            if($observer->getEvent()->getQuote()) {
                $quote = $observer->getEvent()->getQuote();
            }else{
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            }
            $quoteItems = $quote->getAllItems();
            $firstQuoteItem = false;
            foreach ($quoteItems as $quoteItem) {
                if($quoteItem->getParentItem()) continue;
                $options = $quoteItem->getOptionsByCode();
                $buyRequest = $options['info_buyRequest'];
                if(Mage::getSingleton('checkout/session')->getIsExtendedQuote() && !isset($buyRequest['is_extended'])){
                    Mage::getSingleton('checkout/session')->addError(
                        $helper->__('This is an extended order you cannot add other items!')
                    );
                    Mage::app()->getFrontController()->getResponse()->setRedirect(
                        Mage::getUrl('checkout/cart')
                    );
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }
                $buyRequest = $quoteItem->getBuyRequest();
                if ($buyRequest->getStartDate() && $buyRequest->getEndDate()) {
                    if (!$firstQuoteItem) {
                    $startdatetime = new DateTime($buyRequest->getStartDate());
                        $startFormatted = $startdatetime->format('Y-m-d H:i:s');
                    $enddatetime = new DateTime($buyRequest->getEndDate());
                        $endFormatted = $enddatetime->format('Y-m-d H:i:s');

                        $counter = 0;
                        foreach ($quoteItems as $quoteItemNew) {
                            if($quoteItemNew->getParentItem()) continue;
                            $buyRequestNew = $quoteItemNew->getBuyRequest();
                            if ($buyRequestNew->getStartDate() && $buyRequestNew->getEndDate()) {
                                if ($counter > 0) {
                                    $startdatetimeNew = new DateTime($buyRequestNew->getStartDate());
                                    $startFormattedNew = $startdatetimeNew->format('Y-m-d H:i:s');
                                    $enddatetimeNew = new DateTime($buyRequestNew->getEndDate());
                                    $endFormattedNew = $enddatetimeNew->format('Y-m-d H:i:s');

                                    if ($startFormatted != $startFormattedNew || $endFormatted != $endFormattedNew) {
                            Mage::getSingleton('checkout/session')->addError(
                                $helper->__('All products must use the same start and end dates')
                            );
                            Mage::app()->getFrontController()->getResponse()->setRedirect(
                                Mage::getUrl('checkout/cart')
                            );
                            Mage::app()->getResponse()->sendResponse();
                            exit;
                        }
                    }
                                $counter++;
                            }
                        }
                    }
                }
                    break;
                }
            }

    }
}