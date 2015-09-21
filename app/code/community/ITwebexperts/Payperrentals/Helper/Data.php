<?php
/**
 * Class ITwebexperts_Payperrentals_Helper_Data
 */
class ITwebexperts_Payperrentals_Helper_Data extends Mage_Core_Helper_Abstract
{

    const PRODUCT_TYPE = 'reservation';
    const PRODUCT_TYPE_CONFIGURABLE = 'configurable';
    const PRODUCT_TYPE_GROUPED = 'grouped';
    const PRODUCT_TYPE_BUNDLE = 'bundle';

    const CALCULATE_DAYS_BEFORE = 7;
    const CALCULATE_DAYS_AFTER = 730;
    const DB_DATETIME_FORMAT = 'yyyy-MM-dd HH:m:s';
    const DB_ZERO_DATE = '0000-00-00 00:00:00';

    private static $_selectedQtys;
    static protected $_isFoomanInstalled;
    static protected $_isMaintenanceInstalled;
    static protected $_isDeliveryDatesInstalled;


    /**
     * Get config helper
     * @return ITwebexperts_Payperrentals_Helper_Config
     */
    public static function getConfigHelper()
    {
        return Mage::helper('payperrentals/config');
    }

    /**
     * Function to return customer group in admin and frontend
     * @return mixed
     */
    public static function getCustomerGroup()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote')->getCustomer()->getData('group_id');
        } else {
            return Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
    }



    public static function isDisabledDates($productId, $startTimestamp, $endTimestamp){
        $blockedDates = ITwebexperts_Payperrentals_Helper_Data::getDisabledDates($productId);
        $disabledDays = ITwebexperts_Payperrentals_Helper_Data::getDisabledDays($productId, true);
        $disabledDaysStart = ITwebexperts_Payperrentals_Helper_Data::getDisabledDaysStart($productId, true);
        $disabledDaysEnd = ITwebexperts_Payperrentals_Helper_Data::getDisabledDaysEnd($productId, true);
        $currentTimeStamp = Mage::getSingleton('core/date')->timestamp(time());
        $paddingDays = ITwebexperts_Payperrentals_Helper_Data::getProductPaddingDays($productId, $currentTimeStamp);
        $dayOfWeekStart = date('w', $startTimestamp);
        $dayOfWeekEnd = date('w', $endTimestamp);
        $formattedDateStart = date('Y-m-d H:i', $startTimestamp);
        $formattedDateEnd = date('Y-m-d H:i', $endTimestamp);

        return in_array($dayOfWeekStart, $disabledDaysStart) || in_array($dayOfWeekEnd, $disabledDaysEnd) || in_array($dayOfWeekStart, $disabledDays) || in_array($dayOfWeekEnd, $disabledDays) || in_array($formattedDateStart, $blockedDates) || in_array($formattedDateEnd, $blockedDates) || in_array($formattedDateStart, $paddingDays) || in_array($formattedDateEnd, $paddingDays);
    }

    /**
     * Get first available date range for product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param $startingDate
     * @param int $rangeSeconds
     *
     *@return array
     */

    public static function getFirstAvailableDateRange($product, $startingDate = null, $rangeSeconds = false)
    {
        if (!is_object($product)) {
            $product = Mage::getModel('catalog/product')->load((int)$product);
        } else {
            $product->load($product->getId());
        }

        if (!is_null($startingDate)) {
            $currentTimestamp = strtotime($startingDate);
        } else {
            $currentTimestamp = (int)Mage::getSingleton('core/date')->timestamp();
        }

        if ($rangeSeconds == false) {
            $periodInSecond = ITwebexperts_Payperrentals_Helper_Config::getMinimumPeriod($product->getId(), null, true);

        } else {
            $periodInSecond = $rangeSeconds;
        }

        $useTimes = self::useTimes($product->getId());
        $storeTime = self::getConfigHelper()->getStoreTime(Mage::app()->getStore()->getId());
        $configHelper = Mage::helper('payperrentals/config');
        $inventoryHelper = Mage::helper('payperrentals/inventory');


        if($useTimes){
            $timeIncrement = $configHelper->getTimeIncrement() * 60;
            $currentTimestamp = strtotime(date('Y-m-d', $currentTimestamp).' '.$storeTime[0]);
        } else {
            $timeIncrement = 3600 * 24;
            $currentTimestamp = strtotime('+0 day',strtotime(date('Y-m-d', $currentTimestamp).' 00:00:00'));
        }


        $isAvailable = false;


        while (!$isAvailable) {
            $start = $currentTimestamp;
            $end = $start + $periodInSecond;
            if (self::isDisabledDates($product->getId(), $start, $end)){
                $currentTimestamp += $timeIncrement;
                continue;
            }
            if($useTimes) {
                if ($end > strtotime(date('Y-m-d', $end).' '.$storeTime[1])) {
                    $start = strtotime('+1 day',strtotime(date('Y-m-d', $start).' '.$storeTime[0]));
                    $end = $start + $periodInSecond;
                }
            }
            $startDate = date('Y-m-d H:i:s', $start);
            $endDate = date('Y-m-d H:i:s', $end);

            $isAvailable = $inventoryHelper->isAvailable(
                $product->getId(), $startDate, $endDate, 1);

            $currentTimestamp += $timeIncrement;
        }

        return array(
            'start_date' => $startDate,
            'end_date'   => $endDate
        );
    }

    /**
     * Returns a collection of excluded dates (holidays)
     * for a product from the global and by product excluded dates
     *
     * @param $product
     * @return array
     */

    private static function getCollectionExcludedDates($product){
        if (!isset($product) || self::getAttributeCodeForId($product, 'use_global_exclude_dates')) {
            $globalExcludeDates = unserialize(Mage::helper('payperrentals/config')->getGlobalExcludeDates(Mage::app()->getStore()->getId()));
            $collectionExcluded = array();
            if (is_array($globalExcludeDates)) {
                foreach ($globalExcludeDates as $globalExcludeDate) {
                    $collectionExcluded[] = new Varien_Object($globalExcludeDate);
                }
            }
        } else {
            $collectionExcluded = Mage::getModel('payperrentals/excludeddates')
                ->getCollection()
                ->addProductStoreFilter($product, Mage::app()->getStore()->getId())
                ->addSelectFilter('disabled_type <> "daily"');
        }
        return $collectionExcluded;
    }

    /**
     * Returns a collection of excluded dates (holidays)
     * for a product from the global and by product excluded dates
     *
     * @param $product
     * @return array
     */

    private static function getCollectionExcludedTurnoverDates(){
            $globalExcludeDates = unserialize(Mage::helper('payperrentals/config')->getGlobalExcludeDates(Mage::app()->getStore()->getId()));
            $collectionExcluded = array();
            if (is_array($globalExcludeDates)) {
                foreach ($globalExcludeDates as $globalExcludeDate) {
                    if($globalExcludeDate['exclude_from_turnover'] == 1) {
                        $collectionExcluded[] = new Varien_Object($globalExcludeDate);
                    }
                }
            }

        return $collectionExcluded;
    }

    /**
     * Returns an array of excluded dates (holidays) for a product
     * from either the global or by product setting
     *
     * @param $product
     * @return array
     */
    public static function getDisabledDates($product = null, $isPrice = false)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return array();
        }

        $collectionExcluded = self::getCollectionExcludedDates($product);

        $blockedDates = array();

        foreach ($collectionExcluded as $item) {
            if($item->getDisabledFrom() != '' && $item->getDisabledTo() != '') {
                $startDate = ITwebexperts_Payperrentals_Helper_Date::toMysqlDate($item->getDisabledFrom(), true);
                $endDate = ITwebexperts_Payperrentals_Helper_Date::toMysqlDate($item->getDisabledTo(), true);
                $excludeFrom = $item->getExcludeDatesFrom();
                if ($isPrice && $excludeFrom == ITwebexperts_Payperrentals_Model_Source_Excludedaysfrom::CALENDAR) {
                    continue;
                }
                if (($excludeFrom == ITwebexperts_Payperrentals_Model_Source_Excludedaysfrom::CALENDAR ||
                        $excludeFrom == ITwebexperts_Payperrentals_Model_Source_Excludedaysfrom::BOTH) ||
                    $isPrice
                ) {
                    //list($startDate, $endDate) = ITwebexperts_Payperrentals_Helper_Date::convertDatepickerToDbFormat($startDate, $endDate);

                    $startTimePadding = strtotime(date('Y-m-d', strtotime($startDate)));
                    $endTimePadding = strtotime(date('Y-m-d', strtotime($endDate)));
                    while ($startTimePadding <= $endTimePadding) {
                        $dateFormatted = date('Y-m-d', $startTimePadding);
                        $blockedDates[] = $dateFormatted;

                        switch ($item->getDisabledType()) {
                            case 'dayweek'://for this case end_date should be =start_date
                                $currentDayOfWeek = date('l', $startTimePadding);
                                $nrWeeks = ITwebexperts_Payperrentals_Helper_Data::CALCULATE_DAYS_AFTER / 7;
                                $recurringStartDate = $startTimePadding;

                                for ($i = 0; $i < $nrWeeks; $i++) {
                                    $recurringStartDate = strtotime('next ' . $currentDayOfWeek, $recurringStartDate);
                                    $dateFormatted = date('Y-m-d', $recurringStartDate);
                                    $blockedDates[] = $dateFormatted;
                                }

                                break;
                            case 'monthly':
                                $nrMonths = ITwebexperts_Payperrentals_Helper_Data::CALCULATE_DAYS_AFTER / 30;
                                $recurringStartDate = $startTimePadding;

                                for ($i = 0; $i < $nrMonths; $i++) {
                                    $recurringStartDate = strtotime('+1 month', $recurringStartDate);
                                    $dateFormatted = date('Y-m-d', $recurringStartDate);
                                    $blockedDates[] = $dateFormatted;
                                }
                                break;
                            case 'yearly':
                                $nrYears = ITwebexperts_Payperrentals_Helper_Data::CALCULATE_DAYS_AFTER / 360;
                                $recurringStartDate = $startTimePadding;

                                for ($i = 0; $i < $nrYears; $i++) {
                                    $recurringStartDate = strtotime('+1 year', $recurringStartDate);
                                    $dateFormatted = date('Y-m-d', $recurringStartDate);
                                    $blockedDates[] = $dateFormatted;
                                }
                                break;
                        }

                        $startTimePadding += 60 * 60 * 24;
                    }
                }
            }
        }
        return $blockedDates;
    }

    /**
     * Function used to return in seconds a specific  period of time e.g. 3 months: 3 is periodNumber, months is periodtype
     *
     * @param $periodNumber
     * @param $periodType
     *
     * @return int
     */
    public static function getPeriodInSeconds($periodNumber, $periodType)
    {
        $period = 0;

        switch ($periodType) {
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::MINUTES:
                $period = $periodNumber * 60;
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::HOURS:
                $period = $periodNumber * 60 * 60;
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::DAYS:
                $period = $periodNumber * 60 * 60 * 24;
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::WEEKS:
                $period = $periodNumber * 60 * 60 * 24 * 7;
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::MONTHS:
                $period = $periodNumber * 60 * 60 * 24 * 30;
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::YEARS:
                $period = $periodNumber * 60 * 60 * 24 * 365;
                break;
        }

        return $period;
    }

    /**
     * @return string
     */
    public function getRentalqueueUrl()
    {
        return Mage::getUrl('payperrentals_front/customer_rentalqueue/index');
    }

    /**
     * Gets the quantity which was either updated by a shopping cart action,
     * either comes from listing or product page.
     * The function is needed in the observer called when shopping cart is updated
     * todo The function should be reevaluated, since normally this quantity should be the same as the quoteItem one
     * @param $quoteItem
     *
     * @return bool|int|mixed
     */
    public static function getUpdatingQty($quoteItem){
        $qty = false;

        if($quoteItem && $quoteItem->getId() && Mage::app()->getRequest()->getParam('update_cart_action') == 'update_qty'){
            $cartPost = Mage::app()->getRequest()->getParam('cart');
            foreach($cartPost as $qId => $iQty){
                if($quoteItem->getId() == $qId){
                    $qty = intval($iQty['qty']);
                    break;
                }
            }
        }elseif(Mage::app()->getRequest()->getParam('qty') && (is_object($quoteItem) && Mage::app()->getRequest()->getParam('product')  == $quoteItem->getProduct()->getId())){
            $qty = Mage::app()->getRequest()->getParam('qty');
        }elseif($quoteItem && $quoteItem->getId()){
            $qty = $quoteItem->getQty();
        }

        return $qty;
    }

    /**
     * @param $id
     * @return int
     */
    public static function useTimes($id)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            $storeID = Mage::getSingleton('adminhtml/session_quote')->getStoreId();
        } else {
            $storeID = Mage::app()->getStore()->getId();
        }

        if($id == -1){
            if(Mage::app()->getStore()->isAdmin()){
                return 1;
            }else{
                return 0;
            }
        }

        $productUseTimes = Mage::getResourceModel('catalog/product')
            ->getAttributeRawValue(
                $id,
                'payperrentals_use_times',
                $storeID
            );
        $collectionPrices = Mage::getModel('payperrentals/reservationprices')
            ->getCollection()
            ->addEntityStoreFilter($id, $storeID);

        $useTimes = $productUseTimes == ITwebexperts_Payperrentals_Model_Product_Usetimes::STATUS_ENABLED ? true : false;
        $time = 0;
        if ($useTimes == true) {
            $time = 1;
        }
        foreach ($collectionPrices as $item) {
            if ($item->getPtype() == ITwebexperts_Payperrentals_Model_Product_Periodtype::MINUTES || $item->getPtype() == ITwebexperts_Payperrentals_Model_Product_Periodtype::HOURS) {
                $time = 2;
                break;
            }
        }
        /*if ($time == 0 && Mage::app()->getStore()->isAdmin() && Mage::helper('payperrentals/config')->forceUseTimes($storeID)) {
            return 1;
        }*/
        return $time;
    }

    /**
     * Checks if an order exists in the reservationorders table
     *
     * @param $orderId
     * @return bool
     */

    private static function existsOrder($orderId){
        $coll = Mage::getModel('payperrentals/reservationorders')
            ->getCollection()
            ->addSelectFilter("order_id='" . $orderId . "'");

        return ($coll->getSize() != 0);
    }

    /**
     * Function used to create the quantities array for a bundle product
     * e.g: bundle product has 2XProduct 1 and 3XProduct 2, qty 2 and multipleQty true it will return 4XProduct1 and 6XProduct2
     * @param $selectedQtys
     * @param $qty
     * @param $multiplyQty
     */
    private static function prepareQuantityForBundleOption($selectedQtys, $qty, $multiplyQty ){
        if (!is_null($selectedQtys)) {
            foreach ($selectedQtys as $i1 => $j1) {
                if (is_array($j1)) {
                    foreach ($j1 as $k1 => $p1) {
                        if($multiplyQty) {
                            self::$_selectedQtys[$i1][$k1] = $qty * ($p1 == 0 ? 1 : $p1);
                        }else{
                            self::$_selectedQtys[$i1][$k1] = ($p1 == 0 ? 1 : $p1);
                        }

                    }
                } else {
                    if($multiplyQty) {
                        self::$_selectedQtys[$i1] = $qty * ($j1 == 0 ? 1 : $j1);
                    }else{
                        self::$_selectedQtys[$i1] = ($j1 == 0 ? 1 : $j1);
                    }
                }
            }
        }
    }

    public static function createQuantities($source, $qty, $multiplyQty = false){
        /**
         * I've added the bundle_option_qty1 because for default bundle items there was no way to get the quantity
         */
        $selectedQtys1 = isset($source['bundle_option_qty1']) ? $source['bundle_option_qty1'] : null;
        $selectedQtys2 = isset($source['bundle_option_qty']) ? $source['bundle_option_qty'] : null;

        self::$_selectedQtys = array();

        self::prepareQuantityForBundleOption($selectedQtys1, $qty, $multiplyQty);
        self::prepareQuantityForBundleOption($selectedQtys2, $qty, $multiplyQty);

    }

    public static function getQuantityForSelectionProduct($selection, $qty){
        if (isset(self::$_selectedQtys[$selection->getOptionId()][$selection->getSelectionId()])) {
            $qty = self::$_selectedQtys[$selection->getOptionId()][$selection->getSelectionId()];
        } elseif (isset(self::$_selectedQtys[$selection->getOptionId()])) {
            $qty = self::$_selectedQtys[$selection->getOptionId()];
        }

        if($qty == 0){
            $qty = 1;
        }

        return $qty;
    }

    /**
     * This function is needed because if product is bundle on shopping cart I have to change the quantity
     * for the bundle items to 1, because I'm calculating the price for all items.
     * Maybe this can be done better without this need.
     * Should be investigated when calculate price for bundle is refactored
     */
    private static function adjustQuantities($item){

        $prodOptions = $item->getProductOptions();

        if (isset($prodOptions['bundle_selection_attributes'])) {

            $qty = $item->getQtyOrdered(); //normally this hsould always be 1

            if ($item->getParentItem()
                && $item->getParentItem()->getProductType()
                == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE
            ) {
                $qty = $item->getParentItem()->getQtyOrdered();
            }

            $selectionIds = $prodOptions['info_buyRequest']['bundle_option'];
            self::createQuantities($prodOptions['info_buyRequest'], $qty, true);
            $ProductParent = Mage::getModel('catalog/product')->load(
                $item->getParentItem()->getProductId()
            );
            $selections = $ProductParent->getTypeInstance(true)->getSelectionsByIds(
                $selectionIds, $ProductParent
            );

            foreach ($selections->getItems() as $selection) {
                if ($selection->getProductId() == $item->getProductId()) {
                    $qty = self::getQuantityForSelectionProduct($selection, $qty);
                    break;
                }
            }

            $bAttr = unserialize($prodOptions['bundle_selection_attributes']);
            $bAttr['qty'] = $qty;
            $prodOptions['bundle_selection_attributes'] = serialize($bAttr);
            $item->setProductOptions($prodOptions);
            $item->setQtyOrdered($qty);
            $item->save();

        }
    }

    /**
     * Called from the reserveOrder() function to remove inventory
     * for rentals when they are purchased.
     *
     * @param $data
     * @param $item
     * @param $product
     * @return bool
     */

    private static function updateInventoryForBuyoutProduct($data, $item, $product){
        /* only update payperrentals inventory if is buyout, skip reservationorders table updates */
        isset($data['buyout'])?$isBuyout = $data['buyout']:$isBuyout = null;

        if ($isBuyout && $isBuyout == "true") {
            $quantityToDeduct = $item->getQtyOrdered();
            $payperrentalsQuantityNow = $product->getPayperrentalsQuantity();

            Mage::getSingleton('catalog/product_action')
                ->updateAttributes(array($product->getId()), array('payperrentals_quantity' => ($payperrentalsQuantityNow - $quantityToDeduct)), 0);
            ITwebexperts_Payperrentals_Helper_Inventory::updateInventory($product->getId());
            return true;
        }
        return false;
    }

    /**
     * Adds reservationorders table row for the reservation, and deletes the reservationquotes
     * table row. After that calls updateInventory which updates the inventory
     * serialized field.
     *
     * @param $items
     * @param $order
     */

    public static function reserveOrder($items, $order)
    {
        if(self::existsOrder($order->getId())) {
            return;
        }
        $newResOrders = array();
        foreach ($items as $item) {
            if ($item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE
                || $item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE
            ) {
                self::adjustQuantities($item);
            }

            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if ($product->getTypeId() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
                continue;
            }

            $data = $item->getProductOptionByCode('info_buyRequest');

            /* checks if product is buyout, if so it updates inventory and stops this function */
            if(self::updateInventoryForBuyoutProduct($data, $item, $product)){
                return;
            }

            list($startDateArr, $endDateArr) = ITwebexperts_Payperrentals_Helper_Data::getStartEndDates($data);

            foreach ($startDateArr as $count => $startDate) {
                $endDate = $endDateArr[$count];
                $qty = $item->getQtyOrdered();

                /* $shippingAddress = $order->getShippingAddress();
                 $turnoverAr = ITwebexperts_Payperrentals_Helper_Data::getTurnoverDatesForOrderItem(
                     $product->getId(), strtotime($startDate), strtotime($endDate), $order->getShippingMethod(),
                     (is_object($shippingAddress) ? $shippingAddress->getPostcode() : '')
                 );*/
                $turnoverAr = ITwebexperts_Payperrentals_Helper_Data::getTurnoverFromQuoteItemOrBuyRequest($product, $order, $startDate, $endDate);

                if ($item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
                    $resOrder = Mage::getModel('payperrentals/reservationorders')
                        ->setProductId($item->getProductId())
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setStartTurnoverBefore($turnoverAr['before'])
                        ->setEndTurnoverAfter($turnoverAr['after'])
                        ->setQty($qty)
                        ->setOrderId($order->getId())
                        ->setOrderItemId($item->getId());
                    if(Mage::helper('itwebcommon')->isVendorInstalled() && $product->getVendorId() && $product->getVendorId() != 0){
                        $resOrder->setVendorId($product->getVendorId());
                    }
                    Mage::dispatchEvent('ppr_set_stock', array('item' => $item, 'res_order' => $resOrder));

                    $resOrder->setId(null)->save();

                    /**
                     * This part is needed because for some reason the db transactions are not commited
                     */
                    $newResOrders[] = $item->getProductId();
                }
            }
            Mage::getResourceModel('payperrentals/reservationquotes')->deleteByQuoteItemId($item->getQuoteItemId());
        }

        foreach($newResOrders as $productId) {
            ITwebexperts_Payperrentals_Helper_Inventory::updateInventory($productId);
        }

    }

    /**
     * Function which return the deposit price for all the products in the quote
     * @param $quote
     * @return float|int
     */
    public static function getDeposit($quote)
    {
        $quoteItems = $quote->getItemsCollection();
        $depositAmount = 0;
        foreach ($quoteItems as $item) {
            $_product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
            if ($item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE || $item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE || $item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_GROUPED) continue;
            if ($item->getParentItem() && $item->getParentItem()->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE) {
                $qty1 = $item->getParentItem()->getQty();
            } else {
                $qty1 = 1;
            }
            $depositAmount += floatval($_product->getPayperrentalsDeposit()) * $item->getQty() * $qty1;
        }
        return $depositAmount;
    }


    /**
     * Function to return the number of sent items from a specific product
     * @param $productId
     * @return mixed
     */
    public static function getSentItemsByProduct($productId)
    {
        $coll = Mage::getModel('payperrentals/sendreturn')
            ->getCollection()
            ->addSelectFilter("product_id = '" . $productId . "'")
            ->addSelectFilter("res_startdate = '" . '0000-00-00 00:00:00' . "'")
            ->addSelectFilter("return_date = '" . '0000-00-00 00:00:00' . "'")//store filter?
        ;
        return $coll->getSize();
    }

    /**
     * Function to return the number of sent items for a specific customer
     * @param $customerId
     * @return mixed
     */
    public static function getSentItemsForCustomer($customerId)
    {
        $coll = Mage::getModel('payperrentals/sendreturn')
            ->getCollection()
            ->addSelectFilter("customer_id = '" . $customerId . "'")
            ->addSelectFilter("res_startdate = '" . '0000-00-00 00:00:00' . "'")
            ->addSelectFilter("return_date = '" . '0000-00-00 00:00:00' . "'")//store filter?
        ;
        return $coll->getSize();
    }

    /**
     * Checks if an order has only one start and end date for all products
     *
     * @param $order
     * @return mixed Array: true/false, start_date, end_date
     */

    public static function isSingleOrder($order)
    {
        $_start_date = -1;
        $_end_date = -1;
        $isSingle = 0;

        foreach ($order->getAllItems() as $_item) {
            if (is_object($_item->getOrderItem())) {
                $item = $_item->getOrderItem();
            } else {
                $item = $_item;
            }
            if ($item->getParentItem()) {
                continue;
            }
            //check the options and start end date
            if ($options = $item->getProductOptions()) {
                if (isset($options['info_buyRequest'])) {
                    if (isset($options['info_buyRequest'][ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION])) {
                        $start_date = $options['info_buyRequest'][ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION];
                        $end_date = $options['info_buyRequest'][ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION];
                    }

                    if (isset($start_date) && (strtotime($_start_date) != strtotime($start_date))) {
                        $_start_date = $start_date;
                        $isSingle++;
                    }
                    if (isset($end_date) && (strtotime($_end_date) != strtotime($end_date))) {
                        $_end_date = $end_date;
                        $isSingle++;
                    }
                }
            }
        }


        $retArr = array('bool' => false, 'start_date' => $_start_date, 'end_date' => $_end_date);

        if ($isSingle == 2) {
            $retArr = array('bool' => true, 'start_date' => $_start_date, 'end_date' => $_end_date);
        }

        $resultObject = new Varien_Object();
        $resultObject->setReturn($retArr);
        Mage::dispatchEvent('show_dates_is_single', array('result' => $resultObject));
        $returnArr = $resultObject->getReturn();
        return $returnArr;
    }

    /**
     * Get padding days for a product. Note: This is different than turnover time
     *
     * @param $product
     * @return array|bool|int|string
     */

    private static function getPaddingDays($product){
        if(is_object($product)){
            $useGlobalPaddingDays = $product->getUseGlobalPaddingDays();
            $payperrentalsPaddingDays = $product->getPayperrentalsPaddingDays();
        }else{
            $useGlobalPaddingDays = self::getAttributeCodeForId($product, 'use_global_padding_days');
            $payperrentalsPaddingDays = self::getAttributeCodeForId($product, 'payperrentals_padding_days');
        }
        if($product == null || $useGlobalPaddingDays){
            $daysPadding = Mage::helper('payperrentals/config')->getCalendarPaddingDays();
        } else {
            $daysPadding = $payperrentalsPaddingDays;
        }
        return (int)$daysPadding;
    }

    /**
     * Get product padding dates by global or product attribute params
     * Adds to padding the turnover time before because they need
     * to be added together.
     *
     * @param int $productId
     * @param $startDateTimestamp
     *
     *@return array
     */
    public static function getProductPaddingDays($productId, $startDateTimestamp)
    {
        $paddingDays = array();
        if (Mage::app()->getStore()->isAdmin()) {
            return $paddingDays;
        }

        $storeClose = intval(Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_STORE_CLOSE_TIME));
        $t1 = preg_replace('/^0+?|:00*/', '', date('H', $startDateTimestamp));

        $daysPadding = self::getPaddingDays($productId);

        if($t1 > $storeClose || ITwebexperts_Payperrentals_Helper_Config::isNextHourSelection()) $daysPadding++;

        if ($daysPadding > 0) {
            $startTimePadding = $startDateTimestamp;
            while ($daysPadding > 0) {
                $dateFormatted = date('Y-m-d', $startTimePadding) .' 00:00';
                $paddingDays[] = $dateFormatted;
                $startTimePadding = strtotime('+1 day', $startTimePadding);
                $daysPadding--;
            }
        }

        $turnoverAr = ITwebexperts_Payperrentals_Helper_Data::getTurnoverFromQuoteItemOrBuyRequest($productId, null, null, null, 'days');
        $turnoverTimeBefore = $turnoverAr['before'];
        if ($turnoverTimeBefore > 0) {
            if(!isset($startTimePadding)) {
                if ($t1 > $storeClose) $turnoverTimeBefore++;
                $startTimePadding = $startDateTimestamp;
            }
            while ($turnoverTimeBefore > 0) {
                $dateFormatted = date('Y-m-d', $startTimePadding).' 00:00';
                if (!in_array($dateFormatted, $paddingDays)) {
                    $paddingDays[] = $dateFormatted;
                }
                $startTimePadding = strtotime('+1 day', $startTimePadding);
                $turnoverTimeBefore--;
            }
        }

        return $paddingDays;
    }

    public static function getWeekdayForJs($weekDays, $asNumbers = false){

        $disabledDays = array();
        foreach ($weekDays as $disabledDay) {
            switch ($disabledDay) {
                case(ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::MONDAY):
                    $disabledDays[] = $asNumbers?1:'Mon';
                    break;
                case(ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::TUESDAY):
                    $disabledDays[] = $asNumbers?2:'Tue';
                    break;
                case(ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::WEDNESDAY):
                    $disabledDays[] = $asNumbers?3:'Wed';
                    break;
                case(ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::THURSDAY):
                    $disabledDays[] = $asNumbers?4:'Thu';
                    break;
                case(ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::FRIDAY):
                    $disabledDays[] = $asNumbers?5:'Fri';
                    break;
                case(ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::SATURDAY):
                    $disabledDays[] = $asNumbers?6:'Sat';
                    break;
                case(ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::SUNDAY):
                    $disabledDays[] = $asNumbers?0:'Sun';
                    break;
            }
        }
        return $disabledDays;

    }


    /**
     * Init product from productid
     *
     * @param $productid
     * @return product model
     */

    public static function initProduct($productid)
    {
        if (!($productid instanceof Mage_Catalog_Model_Product)) {
            $product = Mage::getModel('catalog/product')->load($productid);
        }
        return $product;
    }

    /**
     * Check if a particular product is using global dates
     *
     * @return bool
     */
    public static function isUsingGlobalDates()
    {
        $use_left = Mage::getStoreConfig('payperrentals/global/use_pprbox_left', Mage::app()->getStore());
        $use_right = Mage::getStoreConfig('payperrentals/global/use_pprbox_right', Mage::app()->getStore());
        $use_header = Mage::getStoreConfig('payperrentals/global/use_pprbox_header', Mage::app()->getStore());
        $use_shopping_cart = Mage::getStoreConfig('payperrentals/global/use_pprbox_shopping_cart', Mage::app()->getStore());

        return $use_shopping_cart || $use_header || $use_left || $use_right;
    }

    /**
     * Check if a particular product is using global dates
     *
     * @return bool
     */
    public static function isUsingGlobalDatesShoppingCart()
    {
        $use_shopping_cart = Mage::getStoreConfig('payperrentals/global/use_pprbox_shopping_cart', Mage::app()->getStore());

        return $use_shopping_cart;
    }


    /**
     * Update start & end dates from global dates for all items in
     * the shopping cart
     *
     * @param $fromDate
     * @param $toDate
     */

    public static function updateCurrentGlobalDates($fromDate, $toDate)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $oldQuote = Mage::getModel('checkout/cart')->getQuote();
        $quote = clone $oldQuote;
        if ($quote) {
            //$cart = Mage::getModel('checkout/cart')->getQuote();
            $cartUpdated = false;
            $startDate = date('Y-m-d H:i:s', strtotime($fromDate));
            $endDate = date('Y-m-d H:i:s', strtotime($toDate));
            foreach ($quote->getAllVisibleItems() as $quoteItem) {
                // TODO: check configurable, bundle, and grouped product types
                /** @var $quoteItem Mage_Sales_Model_Quote_Item */
                $product = $quoteItem->getProduct();
                if (ITwebexperts_Payperrentals_Helper_Data::isUsingGlobalDates($product)) {
                    $oldBuyRequest = $quoteItem->getBuyRequest();
                    $buyRequest = clone $oldBuyRequest;
                    $buyRequest->setData(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION, $startDate);
                    $buyRequest->setData(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION, $endDate);
                    $buyRequest->setIsFiltered(true);
                    $isException = false;
                    try {
                        $quote->removeItem($quoteItem->getId());
                    } catch (Exception $e) {
                        $isException = true;
                        $quote->addProduct($product, $oldBuyRequest);
                    }
                    if (!$isException) {
                        try {
                            $quote->addProduct($product, $buyRequest);
                            $cartUpdated = true;
                        }catch(Exception $e){
                            $cartUpdated = false;
                            break;
                        }
                    }
                }
            }
            if ($cartUpdated) {
                Mage::getSingleton('core/session')->setData('startDateInitial', $startDate);
                Mage::getSingleton('core/session')->setData('endDateInitial', $endDate);
                $quote->save();
                Mage::getModel('checkout/cart')->setQuote($quote);
            }
        }
    }

    /**
     * Get the days that a product is disabled using global or by product setting
     * for days of the week like monday, tuesday, wednesday, etc.
     *
     * @param $productId
     * @param $asNumbers
     * @return array
     */

    public static function getDisabledDays($productId, $asNumbers = false, $isPrice = false, $isTurnover = false)
    {
        if(is_null($productId)){
            $useGlobalExcludedDays = 1;
        }else {
            $useGlobalExcludedDays = self::getAttributeCodeForId($productId, 'global_excludedays');
        }
        if ($useGlobalExcludedDays == 0) {
            $resExcludedDaysweek = self::getAttributeCodeForId($productId, 'res_excluded_daysweek');
            $disabledDaysInt = explode(',', $resExcludedDaysweek);
        } else {
            $disabledDaysInt = explode(',', Mage::getStoreConfig(
                ITwebexperts_Payperrentals_Helper_Config::XML_PATH_DISABLED_DAYS_WEEK
            ));
        }
        if($isTurnover){
            return self::getWeekdayForJs($disabledDaysInt, $asNumbers);
        }
        if($isPrice && Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_DISABLED_DAYS_WEEK_FROM) == ITwebexperts_Payperrentals_Model_Source_Excludedaysfrom::CALENDAR){
            return array();
        }
        if((Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_DISABLED_DAYS_WEEK_FROM) == ITwebexperts_Payperrentals_Model_Source_Excludedaysfrom::CALENDAR ||
            Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_DISABLED_DAYS_WEEK_FROM) == ITwebexperts_Payperrentals_Model_Source_Excludedaysfrom::BOTH) ||
            $isPrice
        ) {
            return self::getWeekdayForJs($disabledDaysInt, $asNumbers);
        }else{
            return array();
        }
    }

    /**
     * Get the days that a product is disabled using global or by product setting
     * for days of the week like monday, tuesday, wednesday, etc.
     *
     * @param $productId
     * @param $asNumbers
     * @return array
     */

    public static function disabledDaysFrom()
    {

            return Mage::getStoreConfig(
                ITwebexperts_Payperrentals_Helper_Config::XML_PATH_DISABLED_DAYS_WEEK_FROM
            );

    }

    /**
     * Get the days that a product is disabled using global or by product setting
     * for days of the week like monday, tuesday, wednesday, etc.
     *
     * @param $productId
     * @param $asNumbers
     * @return array
     */

    public static function getDisabledDaysStart($asNumbers = false)
    {

        $disabledDaysInt = explode(',', Mage::getStoreConfig(
                ITwebexperts_Payperrentals_Helper_Config::XML_PATH_DISABLED_DAYS_WEEK_START
        ));

        return self::getWeekdayForJs($disabledDaysInt, $asNumbers);
    }

    /**
     * Get the days that a product is disabled using global or by product setting
     * for days of the week like monday, tuesday, wednesday, etc.
     *
     * @param $productId
     * @param $asNumbers
     * @return array
     */

    public static function getDisabledDaysEnd($asNumbers = false)
    {

        $disabledDaysInt = explode(',', Mage::getStoreConfig(
            ITwebexperts_Payperrentals_Helper_Config::XML_PATH_DISABLED_DAYS_WEEK_END
        ));

        return self::getWeekdayForJs($disabledDaysInt, $asNumbers);
    }

    public static function isReservation($product){
        list($typeId, $isReservation) = self::_getTypeIdAndIsReservation($product);
        return (
            ($isReservation == ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_ENABLED)
            || ($isReservation == ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_RENTALANDRESERVATION)
        );
    }

    public static function isReservationAndRental($product){
        list($typeId, $isReservation) = self::_getTypeIdAndIsReservation($product);
        return (
            ($isReservation == ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_ENABLED)
            || ($isReservation == ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_RENTALANDRESERVATION)
            || ($isReservation == ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_RENTAL)
        );
    }

    public static function isAddToQueue($product){
        list($typeId, $isReservation) = self::_getTypeIdAndIsReservation($product);
        return (
            ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE && $isReservation == ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_RENTAL)
            || ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE && $isReservation == ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_RENTALANDRESERVATION)
        );

    }

    public static function isAddToQueueGrouped($product){
        list($typeId, $isReservation) = self::_getTypeIdAndIsReservation($product);
        return (
        ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_GROUPED && $isReservation == ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_RENTAL)
        );
    }

    /**
     * Checks if product is buy request from buy request array
     *
     * @param $buyRequest Array
     * @return bool
     */

    public static function isBuyout($buyRequest){
        $isBuyout = false;
        if(is_object($buyRequest)){
            if($buyRequest->getBuyout() && $buyRequest->getBuyout() != 'false'){
                $isBuyout = true;
            }
            $buyRequest = unserialize($buyRequest->getValue());
        }
        if ($isBuyout || isset($buyRequest['buyout']) && $buyRequest['buyout'] != "false") {
            return true;
        }
        return false;
    }

    private static function _getTypeIdAndIsReservation($product){
        if (is_object($product)) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        $typeId = self::getAttributeCodeForId($productId, 'type_id');
        $isReservation = self::getAttributeCodeForId($productId, 'is_reservation');

        return array($typeId, $isReservation);
    }

    /**
     * Checks if a product is a reservation (can be reserved) and is not disabled
     *
     * @param $product
     *
     *@return bool
     */

    public static function isReservationType($product)
    {
        list($typeId, $isReservation) = self::_getTypeIdAndIsReservation($product);

        return ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE
            || ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE && $isReservation != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_DISABLED && $isReservation != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_NOTSET)
            || ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_GROUPED && $isReservation != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_DISABLED && $isReservation != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_NOTSET)
            || ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE && $isReservation != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_DISABLED && $isReservation != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_NOTSET)
        );
    }

    public static function isReservationOnly($product){
        if (is_object($product)) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }
        $typeId = self::getAttributeCodeForId($productId, 'type_id');
        $status = self::getAttributeCodeForId($productId, 'is_reservation');

        return ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE && $status != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_NOTSET && $status != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_DISABLED);
    }

    /**
     * Returns an array of product id for a grouped product
     * @param $groupedProductId
     *
     * @return array
     */

    public static function getAssociatedProductIds($groupedProductId)
    {
        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_read');
        $select = $conn->select()
            ->from($coreResource->getTableName('catalog/product_relation'), array('child_id'))
            ->where('parent_id = ?', $groupedProductId);

        return $conn->fetchCol($select);
    }

    /**
     * Gving an attribute code returns the value for the product
     * @param      $id
     * @param      $attributeCode
     * @param null $storeID
     *
     * @return array|bool|string
     */

    public static function getAttributeCodeForId($id, $attributeCode, $storeID = null)
    {
        if (is_null($storeID)) {
            if (Mage::app()->getStore()->isAdmin()) {
                $storeID = Mage::getSingleton('adminhtml/session_quote')->getStoreId();
            } else {
                $storeID = Mage::app()->getStore()->getId();
            }
        }
        return Mage::getResourceModel('catalog/product')->getAttributeRawValue($id, $attributeCode, $storeID);
    }

    /**
     * Functions used to return an array of dates formatted for js output Y-n-j
     * @param      $dateArray
     * @param bool $useQuotes
     *
     * @return array
     */

    public static function toFormattedDateArray($dateArray, $useQuotes = true){
        $dateFormatted = array();
        foreach($dateArray as $date){
            $dateFormattedString = date('Y-n-j', strtotime($date));
            $dateFormatted[] = (($useQuotes)?'"':'') . $dateFormattedString . (($useQuotes)?'"':'');
        }
        return $dateFormatted;
    }

    /**
     * Functions used to return an array of dates formatted for js output Y-n-j
     * @param      $dateArray
     * @param bool $useQuotes
     *
     * @return array
     */

    public static function toFormattedBookedArray($dateArray){
        $dateFormatted = array();
        foreach($dateArray as $date => $qty){
            $dateFormattedString = date('Y-n-j', strtotime($date));
            $dateFormatted[$dateFormattedString] =  $qty;
        }
        return $dateFormatted;
    }

    /**
     * Function to return the start and end dates as array for the case when dates are non-sequentials
     * @param $source
     *
     * @return array
     */

    public static function getStartEndDates($source){
        $nonSequential = array_key_exists(
            ITwebexperts_Payperrentals_Model_Product_Type_Reservation::NON_SEQUENTIAL, $source
        ) ? $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::NON_SEQUENTIAL] : null;
        $start_date_val = $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION];
        $end_date_val = $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION];

        if ($nonSequential == 1) {
            $startDateArr = explode(',', $start_date_val);
            $endDateArr = explode(',', $start_date_val);
        } else {
            $startDateArr = array($start_date_val);
            $endDateArr = array($end_date_val);
        }

        return array($startDateArr, $endDateArr);
    }

    /**
     * Convert object to multidimensional array
     * @param $obj
     *
     * @return array
     */

    public static function objectToArray($objArr) {
        $finalArr = array();
        foreach($objArr as $arrDate => $vObject){
            $finalArr[$arrDate]['q'] = $vObject->getQty();
            if($vObject->getOrders()) {
                $finalArr[$arrDate]['o'] = $vObject->getOrders();
            }
        }
        return $finalArr;
    }

    /**
     * Convert multidimensional array to object
     * @param $arr
     *
     * @return object
     */
    public static function arrayToObject($arr) {
        $finalArrObj = array();
        foreach($arr as $arrDate => $arrObj){
            $vObject = new Varien_Object();
            if(isset($arrObj['q'])){
                $vObject->setQty($arrObj['q']);
            }
            if(isset($arrObj['o'])){
                $vObject->setOrders($arrObj['o']);
            }
            $finalArrObj[$arrDate] = $vObject;
        }
        return $finalArrObj;
    }

    /**
     * @param $product
     * @param $quoteItem
     * @param $source
     * @param $startDate
     * @param $endDate
     *
     * @return array
     */

    public static function getTurnoverFromQuoteItemOrBuyRequest($product, $quoteItemOrBuyRequest = null, $startDate = null, $endDate = null, $type = 'date')
    {
        $shippingMethod = null;
        $postcode = null;
        $buyRequest = new Varien_Object();
        if(!is_null($quoteItemOrBuyRequest)) {
            if(is_object($quoteItemOrBuyRequest) && is_object($quoteItemOrBuyRequest->getShippingAddress())) {
                if ($quoteItemOrBuyRequest->getShippingAddress()->getShippingMethod()) {
                    $shippingMethod = $quoteItemOrBuyRequest->getShippingAddress()->getShippingMethod();
                }
                if ($quoteItemOrBuyRequest->getShippingAddress()->getPostcode()) {
                    $postcode = $quoteItemOrBuyRequest->getShippingAddress()->getPostcode();
                }
            }else if (is_array($quoteItemOrBuyRequest)) {
                $option = $quoteItemOrBuyRequest;
                $buyRequest = new Varien_Object($option);
            } else if(is_object($quoteItemOrBuyRequest) && is_object($quoteItemOrBuyRequest->getOptionByCode('info_buyRequest'))){
                $option = $quoteItemOrBuyRequest->getOptionByCode('info_buyRequest');
                $buyRequest = new Varien_Object(unserialize($option->getValue()));
            }else{
                $option = $quoteItemOrBuyRequest->getProductOptions();
                $buyRequest = new Varien_Object($option['info_buyRequest']);
            }
        }


        if(is_null($startDate)) {
            if ($buyRequest->getStartDate()) {
                $startDate = $buyRequest->getStartDate();
            }
        }
        if(is_null($endDate)) {
            if ($buyRequest->getEndDate()) {
                $endDate = $buyRequest->getEndDate();
            }
        }
        if (is_object($product)) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        if($buyRequest->getShippingMethod()){
            $shippingMethod = $buyRequest->getShippingMethod();
        }else if(is_object($quoteItemOrBuyRequest) && is_object($quoteItemOrBuyRequest->getQuote()) && is_object($quoteItemOrBuyRequest->getQuote()->getShippingAddress()) && $quoteItemOrBuyRequest->getQuote()->getShippingAddress()->getShippingMethod()){
            $shippingMethod = $quoteItemOrBuyRequest->getQuote()->getShippingAddress()->getShippingMethod();
        }
        //else if(is_object(Mage::getModel('checkout/session')->getQuote()->getShippingAddress()) && Mage::getModel('checkout/session')->getQuote()->getShippingAddress()->getShippingMethod()){
       //     $shippingMethod = Mage::getModel('checkout/session')->getQuote()->getShippingAddress()->getShippingMethod();
       // }
        else if(Mage::app()->getRequest()->getParam('shipping_method')){
            $shippingMethod = Mage::app()->getRequest()->getParam('shipping_method');
        }

        if($buyRequest->getZipCode()){
            $postcode = $buyRequest->getZipCode();
        }else if(is_object($quoteItemOrBuyRequest)  && is_object($quoteItemOrBuyRequest->getQuote()) && is_object($quoteItemOrBuyRequest->getQuote()->getShippingAddress()) && $quoteItemOrBuyRequest->getQuote()->getShippingAddress()->getPostcode()){
            $postcode = $quoteItemOrBuyRequest->getQuote()->getShippingAddress()->getPostcode();
        }
       // else if(is_object(Mage::getModel('checkout/session')->getQuote()->getShippingAddress()) && Mage::getModel('checkout/session')->getQuote()->getShippingAddress()->getPostcode()){
       //     $postcode = Mage::getModel('checkout/session')->getQuote()->getShippingAddress()->getPostcode();
       // }
        else if(Mage::app()->getRequest()->getParam('zip_code')){
            $postcode = Mage::app()->getRequest()->getParam('zip_code');
        }

        $endOrderDateTimestamp = strtotime($endDate);
        $startOrderDateTimestamp = strtotime($startDate);

        if(date('H:i:s', $endOrderDateTimestamp) == '00:00:00' && !Mage::helper('payperrentals/config')->isHotelMode(Mage::app()->getStore()->getId())){
            $endOrderDateTimestamp = strtotime(date('Y-m-d', $endOrderDateTimestamp).' 23:59:00');
        }else {
            $endOrderDateTimestamp -= 60;
        }

        $turnoverTimeBefore = ITwebexperts_Payperrentals_Helper_Config::getTurnoverTimeBefore($productId);
        $turnoverTimeAfter = ITwebexperts_Payperrentals_Helper_Config::getTurnoverTimeAfter($productId);

        Mage::dispatchEvent('ppr_get_turnover_dates_for_order_item', array('turnover_time_before' => &$turnoverTimeBefore, 'turnover_time_after' => &$turnoverTimeAfter, 'order_item_before_turnover_timestamp' => &$startOrderDateTimestamp, 'order_item_after_turnover_timestamp' => &$endOrderDateTimestamp, 'product' => $productId, 'shipping_method' => $shippingMethod, 'postcode' => $postcode));

        $collectionExcludedTurnover = ITwebexperts_Payperrentals_Helper_Data::getCollectionExcludedTurnoverDates();
        if(ITwebexperts_Payperrentals_Helper_Config::excludeDisabledDaysOfWeekFromTurnover()){
            $turnoverTimeBeforeCurr = $turnoverTimeBefore;
            if(!$startOrderDateTimestamp){
                $startOrderDateTimestamp = Mage::getSingleton('core/date')->timestamp(date('Y-m-d'));
            }
            $startOrderDateTimestampCurr = $startOrderDateTimestamp - $turnoverTimeBeforeCurr;
            $endOrderDateTimestampCurr = $startOrderDateTimestamp;
            $disabledDays = self::getDisabledDays(null, true,false, true);

            while($startOrderDateTimestampCurr <= $endOrderDateTimestampCurr){
                $currDateofWeek = date('w', $startOrderDateTimestampCurr);
                $currDate = date('Y-m-d', $startOrderDateTimestampCurr);
                if(in_array($currDateofWeek, $disabledDays) || in_array($currDate, $collectionExcludedTurnover)){
                    $turnoverTimeBefore+=86400;
                }
                $startOrderDateTimestampCurr += 86400;
            }

            $turnoverTimeAfterCurr = $turnoverTimeAfter;
            if($endOrderDateTimestamp) {
                $endOrderDateTimestampCurr = $endOrderDateTimestamp + $turnoverTimeAfterCurr;
                $startOrderDateTimestampCurr = $endOrderDateTimestamp;
                $disabledDays = self::getDisabledDays(null, true, false, true);

                while ($startOrderDateTimestampCurr <= $endOrderDateTimestampCurr) {
                    $currDateofWeek = date('w', $startOrderDateTimestampCurr);
                    $currDate = date('Y-m-d', $startOrderDateTimestampCurr);
                    if (in_array($currDateofWeek, $disabledDays) || in_array($currDate, $collectionExcludedTurnover)) {
                        $turnoverTimeAfter+=86400;
                    }
                    $startOrderDateTimestampCurr += 86400;
                }
            }
        }

        if($type == 'date') {
            $orderBeforeTurnoverTimestamp = $startOrderDateTimestamp - $turnoverTimeBefore;
            $orderAfterTurnoverTimestamp = $endOrderDateTimestamp + $turnoverTimeAfter;

            return array(
                'before' => date('Y-m-d H:i:s', $orderBeforeTurnoverTimestamp),
                'after'  => date('Y-m-d H:i:s', $orderAfterTurnoverTimestamp)
            );
        }elseif($type == 'days'){

            return array(
                'before' => intval($turnoverTimeBefore / 86400),
                'after'  => intval($turnoverTimeAfter / 86400)
            );
        }

    }

    /**
     * Check if the order is late
     * @param $orderId
     *
     * @return bool
     */

    public static function isLateOrder($orderId){
        $now = date('Y-m-d H:i:s');
        $order = Mage::getModel('sales/order')->load($orderId);
        $endDate = $order->getEndDatetime();
        $sendReturnColl = Mage::getModel('payperrentals/sendreturn')
            ->getCollection()
            ->addOrderIdFilter($orderId)
            ->getFirstItem();

        if(count($sendReturnColl->getData()) > 0 && $sendReturnColl->getReturnDate() != '0000-00-00 00:00:00'){
            if(strtotime($sendReturnColl->getReturnDate()) > strtotime($endDate)){
                return true;
            }
        }else {
            if (strtotime($endDate) < strtotime($now)) {
                return true;
            }
        }

        return false;
    }

    public static function getDefaultSelectionsForBundle($bundledProduct){
        $bundleDefaultSelections = array();
        if($bundledProduct->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE) {
            $typeInstance = $bundledProduct->getTypeInstance(true);

            $selectionCollection = $typeInstance->getSelectionsCollection(
                $typeInstance->getOptionsIds($bundledProduct), $bundledProduct
            );

            $optionCollection = $typeInstance->getOptionsCollection($bundledProduct);

            $options = $optionCollection->appendSelections($selectionCollection, false,
                Mage::helper('catalog/product')->getSkipSaleableCheck()
            );

            foreach ($options as $key => $value) {
                if (gettype($value->getDefaultSelection()) == 'object') {
                    $bundleDefaultSelections[] = $value->getDefaultSelection()->getProductId();
                }
            }
        }
        return $bundleDefaultSelections;
    }

    public static function isFoomanInstalled()
    {
        if (is_null(self::$_isFoomanInstalled)) {
            self::$_isFoomanInstalled = Mage::helper('core')->isModuleEnabled('Fooman_PdfCustomiser');
        }
        return self::$_isFoomanInstalled;

    }

    public static function isMaintenanceInstalled()
    {
        if (is_null(self::$_isMaintenanceInstalled)) {
            self::$_isMaintenanceInstalled = Mage::helper('core')->isModuleEnabled('ITwebexperts_Maintenance');
        }
        return self::$_isMaintenanceInstalled;
    }

    public static function isDeliveryDatesInstalled()
    {
        if (is_null(self::$_isDeliveryDatesInstalled)) {
            self::$_isDeliveryDatesInstalled = Mage::helper('core')->isModuleEnabled('ITwebexperts_Deliverydates');
        }
        return self::$_isDeliveryDatesInstalled;
    }

}