<?php

class ITwebexperts_Payperrentals_Helper_Inventory extends Mage_Core_Helper_Abstract
{

    /**
     * This function is used to get all the quantites for a product.
     * So if the product is a bundle it will return an array with the associated products ids and their quantities in the product
     * For simple types it will just return the product id and qty 1 and for configurable will return the if of the selected configurable.
     * A configurable product has attributes
     * A bundle product is composed from bundle_options and quantities for every option.
     * A bundle product has an extra bundle_option_qty1 for qtys which are set as default and can't be changed by the user.
     *
     * @param      $product
     * @param      $qty
     * @param null $attributes
     * @param null $bundleOption
     * @param null $bundleOptionQty1
     * @param null $bundleOptionQty
     * @param bool $multiplyQty
     *
     * @return array
     */
    public static function getQuantityArrayForProduct(
        $product, $qty, $attributes = null, $bundleOption = null, $bundleOptionQty1 = null,
        $bundleOptionQty = null, $multiplyQty = false
    ) {
        $qtyArr = array();
        if ($qty == '' || $qty == 0) {
            $qty = 1;
        }
        if (is_object($product)) {
            $productObj = $product;
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        $typeId = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'type_id');
        if ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE) {
            if (!isset($productObj)) {
                $productObj = ITwebexperts_Payperrentals_Helper_Data::initProduct($productId);
            }
            $childProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes(
                $attributes, $productObj
            );
            if (is_object($childProduct) && $childProduct->getTypeId() != 'simple') {
                $qtyArr[$childProduct->getId()] = $qty;
            } else {
                $qtyArr[$productId] = $qty;
            }
        } elseif ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_GROUPED) {
            $assocArr = ITwebexperts_Payperrentals_Helper_Data::getAssociatedProductIds($productId);
            foreach ($assocArr as $iAssoc) {
                $iTypeId = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($iAssoc, 'type_id');
                if ($iTypeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
                    $qtyArr[$iAssoc] = $qty;
                    break; //we only allow grouped products with 1 reservation type product
                }
            }
        } elseif ($typeId != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE) {
            $qtyArr[$productId] = $qty;
        } elseif (!is_null($bundleOption)) {
            $selectionIds = $bundleOption;

            list($source['bundle_option_qty1'], $source['bundle_option_qty']) = array($bundleOptionQty1,
                                                                                      $bundleOptionQty);
            ITwebexperts_Payperrentals_Helper_Data::createQuantities($source, $qty, $multiplyQty);

            if (!isset($productObj)) {
                $productObj = ITwebexperts_Payperrentals_Helper_Data::initProduct($productId);
            }
            $selections = $productObj->getTypeInstance(true)->getSelectionsByIds($selectionIds, $productObj);

            foreach ($selections->getItems() as $selection) {
                $bProductId = $selection->getProductId();
                $bTypeId = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($bProductId, 'type_id');
                //if ($bTypeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
                    $qty = ITwebexperts_Payperrentals_Helper_Data::getQuantityForSelectionProduct($selection, $qty);
                    if(!isset($qtyArr[$bProductId])) {
                        $qtyArr[$bProductId] = $qty;
                    }else{
                        $qtyArr[$bProductId] += $qty;
                    }
                //}
            }
        }
        return $qtyArr;
    }

    /**
     * This function will return for any product type the associated reservations ids
     *
     * @param      $productIds
     * @param null $attributes
     * @param null $options
     * @param bool $useRequired
     *
     * @return array
     */

    public static function getReservationProductsArrayIds(
        $productIds, $attributes = null, $options = null, $useRequired = false
    ) {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        $newProductIds = array();
        foreach ($productIds as $iProduct) {
            $typeId = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($iProduct, 'type_id');
            if ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE
                && !is_null(
                    $attributes
                )
            ) {
                $childProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes(
                    $attributes, ITwebexperts_Payperrentals_Helper_Data::initProduct($iProduct)
                );
                if (is_object($childProduct) && $childProduct->getTypeId() != 'simple') {
                    $newProductIds[] = $childProduct->getId();
                }
            } elseif ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_GROUPED) {
                $assocArr = ITwebexperts_Payperrentals_Helper_Data::getAssociatedProductIds($iProduct);
                foreach ($assocArr as $iAssoc) {
                    $iTypeId = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($iAssoc, 'type_id');
                    if ($iTypeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
                        $newProductIds[] = $iAssoc;
                        break; //we only allow grouped products with 1 reservation type product
                    }
                }
            } elseif ($typeId != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE) {
                $newProductIds[] = $iProduct;
            } elseif (!is_null($options)) {
                $product = ITwebexperts_Payperrentals_Helper_Data::initProduct($iProduct);
                $selections = $product->getTypeInstance(true)->getSelectionsByIds($options, $product);
                foreach ($selections->getItems() as $selection) {
                    $newProductIds[] = $selection->getProductId();
                }
            } elseif ($useRequired) {
                $product = ITwebexperts_Payperrentals_Helper_Data::initProduct($iProduct);
                if ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE
                    && is_null(
                        $attributes
                    )
                ) {
                    $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
                    $simpleCollection = $conf
                        ->getUsedProductCollection()
                        ->addAttributeToSelect('*')
                        ->addFilterByRequiredOptions();
                    foreach ($simpleCollection as $simpleProduct) {
                        $iTypeId = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
                            $simpleProduct->getId(), 'type_id'
                        );
                        if ($iTypeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
                            $newProductIds[] = $simpleProduct->getId();
                        }
                    }
                } else {
                    $optionCol = $product->getTypeInstance(true)
                        ->getOptionsCollection($product);
                    $selectionCol = $product->getTypeInstance(true)
                        ->getSelectionsCollection(
                            $product->getTypeInstance(true)->getOptionsIds($product),
                            $product
                        );
                    $optionCol->appendSelections($selectionCol);
                    foreach ($optionCol as $option) {
                        if ($option->required) {
                            $selectionsOpt = $option->getSelections();
                            foreach ($selectionsOpt as $selectionOpt) {
                                $newProductIds[] = $selectionOpt->getProductId();
                            }
                        }
                    }
                }
            }
        }
        return $newProductIds;
    }

    /**
     * Updates the product inventory_serialized field which holds a list of dates
     * and quantities reserved for each product.
     *
     * Should be called when reservation dates are changed, or an order is placed
     *
     * @param      $productIds
     * @param null $resOrders
     */
    public static function updateInventory($productIds, $resOrders = null)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        $productIds = array_unique($productIds);
        $booked = self::getAllBookedQtyForProduct($productIds, $resOrders);
        foreach ($productIds as $productId) {
            /*
             * this part is needed for multiple ids
             */
            if (isset($booked[$productId]) && !is_null($booked[$productId])) {
                Mage::getSingleton('catalog/product_action')
                    ->updateAttributes(
                        array($productId), array('inventory_serialized' => json_encode(
                            ITwebexperts_Payperrentals_Helper_Data::objectToArray($booked[$productId])
                        )), 0
                    );
            } else {
                Mage::getSingleton('catalog/product_action')
                    ->updateAttributes(array($productId), array('inventory_serialized' => null), 0);
            }
        }
    }

    /**
     * Function which completes the booked array with the disabled and excluded days, so they
     * can't be selected in the calendar. The report will ignore them
     *
     * @param array    $productIds
     * @param array    $booked
     * @param int      $currentTimestamp
     * @param datetime $stDate
     * @param datetime $enDate
     *
     * @return array
     */

    public static function isExcludedDay($productIds, $stDate, $enDate)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }

        $currentTimestamp = (int)Mage::getSingleton('core/date')->timestamp(time());

        foreach ($productIds as $id) {
            $disabledDays = ITwebexperts_Payperrentals_Helper_Data::getDisabledDays($id);
            $disabledDaysStart = ITwebexperts_Payperrentals_Helper_Data::getDisabledDaysStart();
            $disabledDaysEnd = ITwebexperts_Payperrentals_Helper_Data::getDisabledDaysEnd();
            //$paddingDays = ITwebexperts_Payperrentals_Helper_Data::getProductPaddingDays($id, $currentTimestamp);
            $paddingDays = ITwebexperts_Payperrentals_Helper_Data::getFirstAvailableDateRange($id, null, false, true);
            if(!$paddingDays) {
               return true;
            }
            $blockedDates = ITwebexperts_Payperrentals_Helper_Data::getDisabledDates($id);
            foreach ($blockedDates as $dateFormatted) {
                if (date('Y-m-d',strtotime($dateFormatted)) == date('Y-m-d',strtotime($stDate))
                    || date('Y-m-d',strtotime($dateFormatted)) == date('Y-m-d',strtotime($enDate))
                ) {
                    return true;
                }
            }
            foreach ($paddingDays as $dateFormatted) {
                if (strtotime($dateFormatted) >= strtotime($stDate)
                    && strtotime($dateFormatted) <= strtotime($enDate)
                ) {
                    return true;
                }
            }
            if (count($disabledDays) > 0) {
                $startTimePadding = strtotime(date('Y-m-d', strtotime($stDate)));
                $endTimePadding = strtotime(date('Y-m-d', strtotime($enDate)));

                $dayofWeek = date('D', $startTimePadding);
                if (in_array($dayofWeek, $disabledDays)) {
                    return true;
                }

                $dayofWeek = date('D', $startTimePadding);
                if (in_array($dayofWeek, $disabledDaysStart)) {
                    return true;
                }

                $dayofWeek = date('D', $endTimePadding);
                if (in_array($dayofWeek, $disabledDays)) {
                    return true;
                }

                $dayofWeek = date('D', $endTimePadding);
                if (in_array($dayofWeek, $disabledDaysEnd)) {
                    return true;
                }

            }
        }
        return false;
    }

    /**
     * Functions used to return an array in the format productId date object like this:
     * For every productId and date which has at least one item booked it will add it to an object
     * The object contains the qty for that date(Y-m-d H:i) and the booked orders is needed(mainly for inv rep)
     * The used function to return the array in the required format is getBooked
     *
     * @param      $productId
     * @param null $resOrders
     *
     * @return array|null
     */

    private static function getAllBookedQtyForProduct($productId, $resOrders = null)
    {
        $currentTimestamp = (int)Mage::getSingleton('core/date')->timestamp(time());
        $stDate = ITwebexperts_Payperrentals_Helper_Date::toDbDate($currentTimestamp - 365 * 24 * 3600, true);
        $enDate = ITwebexperts_Payperrentals_Helper_Date::toDbDate(
            strtotime('+' . ITwebexperts_Payperrentals_Helper_Data::CALCULATE_DAYS_AFTER . ' days', $currentTimestamp),
            true
        );

        $reserveOrderCollection = Mage::getModel('payperrentals/reservationorders')->getCollection();
        $reserveOrderCollection->addProductIdsFilter($productId);
        $reserveOrderCollection->addSelectFilter(
                "start_turnover_before <= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($enDate)
                . "' AND end_turnover_after >= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($stDate) . "'"
        );

        Mage::dispatchEvent('ppr_before_filter_order', array('collection' => $reserveOrderCollection));

        if (!is_null($resOrders)) {
            foreach ($resOrders as $resOrder) {
                $reserveOrderCollection->addItem($resOrder);
            }
        }

        $booked = self::getBooked($reserveOrderCollection, true);
        return $booked;
    }

    /**
     * Function used to return the booked object in the required format check phpdoc on getAllBookedQtyForProduct
     *
     * @param      $reservedCollection
     * @param      $isOrder
     * @param null $booked
     *
     * @return array|null
     */
    private static function getBooked($reservedCollection, $isOrder, $booked = null)
    {
        if (is_null($booked)) {
            $booked = array();
        }
        $configHelper = Mage::helper('payperrentals/config');
        foreach ($reservedCollection as $iReserved) {


            $start = strtotime($iReserved->getStartTurnoverBefore());
            $end = strtotime($iReserved->getEndTurnoverAfter());
            if (Mage::helper('payperrentals/config')->useReserveInventoryDropoffPickup()) {
                if($iReserved->getDropoff()){
                    $start = strtotime($iReserved->getDropoff());
                }
                if($iReserved->getPickup()){
                    $end = strtotime($iReserved->getPickup());
                }
            }

            if (Mage::helper('payperrentals/config')->useReserveInventorySendReturn()) {
                $sendReturnCollection = Mage::getModel('payperrentals/sendreturn')->getCollection();
                $sendReturnCollection->addSelectFilter("resorder_id=" . $iReserved->getId());
                $sendReturnCollection->addSelectFilter(
                    "return_date <= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($end, true) . "' AND return_date <> '0000-00-00 00:00:00'"
                );
                $sendReturnCollection->getSelect()
                    ->order('main_table.return_date DESC');

            }
            //a required option for bundle products is to have prices defined when times is enabled.
            //So use times and prices should be defined for all components of bundle products.

            $useTimes = ITwebexperts_Payperrentals_Helper_Data::useTimes($iReserved->getProductId()) == 2;
            if ($useTimes && (date('H:i:s', $start) != '00:00:00'
                || (date('H:i:s', $end) != '23:59:00'
                    && date('H:i:s', $end) != '23:58:59')
            )) {
                $timeIncrement = $configHelper->getTimeIncrement() * 60;
            } else {
                $timeIncrement = 3600 * 24;
            }
            $returnsArray = array();
            if(isset($sendReturnCollection)){
                foreach($sendReturnCollection as $sendReturnItem){
                    if($timeIncrement !== 3600 * 24) {
                        $dateReturn = date('Y-m-d H:i', strtotime($sendReturnItem->getReturnDate()));
                    }else{
                        $dateReturn = date('Y-m-d', strtotime($sendReturnItem->getReturnDate())).' 00:00';
                    }
                    if(strtotime($dateReturn) < $start){
                        $dateReturn = date('Y-m-d', $start).' 00:00';
                    }
                    if(!isset($returnsArray[$sendReturnItem->getProductId()][$dateReturn])) {
                        $returnsArray[$sendReturnItem->getProductId()][$dateReturn] = $sendReturnItem->getQty();
                    }else{
                        $returnsArray[$sendReturnItem->getProductId()][$dateReturn] += $sendReturnItem->getQty();
                    }
                }
            }
            $qtyReturnedArray = array();
            while ($start < $end) {
                if($timeIncrement !== 3600 * 24) {
                    $dateFormatted = date('Y-m-d H:i', $start);
                }else{
                    $dateFormatted = date('Y-m-d', $start).' 00:00';
                }
                if(!isset($qtyReturnedArray[$iReserved->getProductId()])){
                    $qtyReturnedArray[$iReserved->getProductId()] = 0;
                }
                if(isset($returnsArray[$iReserved->getProductId()]) && array_key_exists($dateFormatted, $returnsArray[$iReserved->getProductId()]) !== false){
                    $qtyReturnedArray[$iReserved->getProductId()] = $returnsArray[$sendReturnItem->getProductId()][$dateFormatted];
                }
                if (!isset($booked[$iReserved->getProductId()][$dateFormatted])) {
                    $vObject = new Varien_Object();
                    $vObject->setQty($iReserved->getQty() - $qtyReturnedArray[$iReserved->getProductId()]);
                    if ($isOrder) {
                        $vObject->setOrders(array($iReserved->getOrderId()));
                    }
                    $booked[$iReserved->getProductId()][$dateFormatted] = $vObject;
                } else {
                    $vObject = $booked[$iReserved->getProductId()][$dateFormatted];
                    $vObject->setQty($vObject->getQty() + $iReserved->getQty() - $qtyReturnedArray[$iReserved->getProductId()]);
                    if ($isOrder) {
                        $orderArr = $vObject->getOrders();
                        $orderArr = array_merge($orderArr, array($iReserved->getOrderId()));
                        array_unique($orderArr);
                        $vObject->setOrders($orderArr);
                    }
                    $booked[$iReserved->getProductId()][$dateFormatted] = $vObject;
                }
                $start += $timeIncrement;
            }
        }
        /**
         * Event used to complete the booked object with specific dates. Needed in case of maintenance module
         */
        $resultObject = new Varien_Object();
        $resultObject->setBooked($booked);
        Mage::dispatchEvent(
            'after_booked',
            array('result' => $resultObject, 'reserved_collection' => $reservedCollection, 'is_order' => $isOrder)
        );
        return $resultObject->getBooked();
    }

    /**
     * Function used to add qty to booked array between the excluded dates
     *
     * @param $excludedQtys array(start, end, qty)
     * @param $booked
     */

    private static function getBookedWithExcludedQtys($excludedQtysArr, $booked)
    {
        if (is_array($excludedQtysArr)) {
            foreach ($excludedQtysArr as $excludedQtys) {
                if (isset($booked[$excludedQtys['product_id']])) {
                    foreach ($booked[$excludedQtys['product_id']] as $dateFormatted => $vObject) {
                        if (strtotime($dateFormatted) >= strtotime($excludedQtys['start_date'])
                            && strtotime($dateFormatted) <= strtotime($excludedQtys['end_date'])
                        ) {
                            $vObject->setQty($vObject->getQty() - $excludedQtys['qty']);
                            $booked[$excludedQtys['product_id']][$dateFormatted] = $vObject;
                        }
                    }
                }
            }
        }
        return $booked;
    }

    /**
     * Returns an array of dates that a product is booked. Can accept an array of product ids,
     * this enables this function to work with bundles.
     *
     * @param      $productIds
     * @param null $stDate
     * @param null $enDate
     * @param bool $isReport
     *
     * @return array|mixed|null
     */
    public static function getBookedQtyForProducts(
        $productIds, $stDate = null, $enDate = null, $isReport = false, $excludedQtysForDates = null
    ) {
        /*Check if we are in the quote*/
        if (!Mage::registry('no_quote')) {
            $isQuote = Mage::getSingleton("checkout/session")->getQuoteId();
            if (!$isQuote) {
                $isQuote = false;
            }
        }

        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }

        foreach ($productIds as $iProduct) {
            if (!$isReport && ITwebexperts_Payperrentals_Helper_Inventory::isAllowedOverbook($iProduct)) {
                return array('booked' => array());
            }
        }

        $booked = array();
        foreach ($productIds as $iProduct) {
            $inventorySerialized = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
                $iProduct, 'inventory_serialized'
            );
            if ($inventorySerialized == 'not_updated') {
                self::updateInventory($iProduct);
                $inventorySerialized = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
                    $iProduct, 'inventory_serialized'
                );
            }

            if ($inventorySerialized) {
                $objArr = ITwebexperts_Payperrentals_Helper_Data::arrayToObject(
                    json_decode(
                        $inventorySerialized, true
                    )
                );
                $booked += array($iProduct => $objArr);
            }
        }

        if (!isset($isQuote) || !$isQuote) {
            $booked = self::getBookedWithExcludedQtys($excludedQtysForDates, $booked);
            return $booked;
        }


        if ($isQuote) {
            $reserveQuote = Mage::getModel('payperrentals/reservationquotes')->getCollection()
                ->addQuoteIdFilter($isQuote)
                ->addProductIdsFilter($productIds);
            if(Mage::registry('quote_item_id')){
                $reserveQuote->addQuoteItemIdFilterNot(Mage::registry('quote_item_id'));
            }
            Mage::dispatchEvent('ppr_before_filter_order', array('collection' => $reserveQuote));

            if (Mage::helper('payperrentals/config')->isHotelMode(Mage::app()->getStore()->getId())
                || date('Y-m-d', strtotime($stDate)) == date('Y-m-d', strtotime($enDate))
            ) {
                $reserveQuote->addSelectFilter(
                    "start_turnover_before <= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                        $enDate, false, -60
                    )
                    . "' AND DATE_SUB(end_turnover_after, INTERVAL 1 MINUTE) >= '"
                    . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                        $stDate
                    ) . "'"
                );
            } else {
                $reserveQuote->addSelectFilter(
                    "start_turnover_before <= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                        $enDate
                    )
                    . "' AND end_turnover_after >= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($stDate) . "'"
                );
            }
            $booked = self::getBooked($reserveQuote, false, $booked);
            $booked = self::getBookedWithExcludedQtys($excludedQtysForDates, $booked);
        }

        return $booked;
    }

    /**
     * Function to get if product is available between dates
     *
     * @param     $productId
     * @param int $qty
     * @param     $startDate
     * @param     $endDate
     *
     * @return bool
     */

    public function isAvailable($productId, $startDate, $endDate, $qty, $quoteItem = false)
    {
        if (self::isAllowedOverbook($productId)) {
            return true;
        }

        if ($quoteItem) {
            $collectionQuotes = Mage::getModel('payperrentals/reservationquotes')
                ->getCollection()
                ->addProductIdFilter($productId)
                ->addSelectFilter(
                    "start_date = '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($startDate)
                    . "' AND end_date = '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                        $endDate
                    ) . "' AND quote_item_id = '" . $quoteItem->getId() . "'"
                );

            $oldQty = 0;
            foreach ($collectionQuotes as $oldQuote) {
                $oldQty = $oldQuote->getQty();
            }

            if (Mage::app()->getRequest()->getParam('qty')) {
                $oldQty = 0;
            }
            $qty = $qty - $oldQty;
        }

        $maxQty = self::getQuantity($productId, $startDate, $endDate);

        if ($maxQty < $qty) {
            return false;
        }

        return true;
    }

    /**
     * Function to get maximum quantity available for specific start and end dates.
     * This function first gets max available inventory for a product, then reduces it
     * via the number reserved in the inventory_serialized EAV field for that time period.
     * It then returns the inventory for when the least amount of inventory is available.
     *
     * @param Mage_Catalog_Product|int $product
     * @param                          $startDate
     * @param                          $endDate
     *
     * @return int
     */

    public static function getQuantity($product, $startDate = null, $endDate = null, $options = null, $forceReal = false)
    {
        $productId = $product;
        if (is_object($product)) {
            $productId = $product->getId();
        }
        if (!is_object($product)) {
            $product = ITwebexperts_Payperrentals_Helper_Data::initProduct($product);
        }
        $isReservation = ITwebexperts_Payperrentals_Helper_Data::isReservationType($productId);
        $maxQty = 0;
        if (!$isReservation) {
            if (!is_object($product)) {
                $product = ITwebexperts_Payperrentals_Helper_Data::initProduct($product);
            }
            $maxQty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
        }

        if ($isReservation && ITwebexperts_Payperrentals_Helper_Data::isReservationOnly($productId)) {
            $payperrentalsUseSerials = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
                $productId, 'payperrentals_use_serials'
            );
            $payperrentalsQuantity = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
                $productId, 'payperrentals_quantity'
            );
            if (!$payperrentalsQuantity) {
                $payperrentalsQuantity = 0;
            }
            if ($payperrentalsUseSerials == ITwebexperts_Payperrentals_Model_Product_Useserials::STATUS_ENABLED) {
                $serialCollection = Mage::getModel('payperrentals/serialnumbers')
                    ->getCollection()
                    ->addEntityIdFilter($productId)
                    ->addSelectFilter("status <> 'B' AND status <> 'M'");
                $maxQty = $serialCollection->getSize();
            } else {
                $maxQty = $payperrentalsQuantity;
            }

            if (!is_null($startDate) && !is_null($endDate)) {
                //should add turnover times to start end date
                $bookedArray = self::getBookedQtyForProducts(
                    $productId, $startDate, $endDate, false,
                    isset($options['excluded_qty']) ? $options['excluded_qty'] : null
                );
                $maxQtyReserved = 1000000;
                if (isset($bookedArray[$productId])) {
                    foreach ($bookedArray[$productId] as $dateFormatted => $vObject) {
                        if(date('H:i', strtotime($dateFormatted)) == '00:00'){
                            $timeStart = strtotime($dateFormatted);
                            $timeEnd = strtotime($dateFormatted) + 86340;
                        }else{
                            $timeStart = strtotime($dateFormatted);
                            $timeEnd = strtotime($dateFormatted) + 3600;
                        }

                        if ($timeStart <= strtotime($endDate)
                            && $timeEnd >= strtotime($startDate)
                        ) {
                            if ($maxQtyReserved > ($maxQty - $vObject->getQty())) {
                                $maxQtyReserved = $maxQty - $vObject->getQty();
                            }
                        }
                    }
                    if ($maxQtyReserved == 1000000) {
                        $maxQtyReserved = $maxQty;
                    }
                    $maxQty = $maxQtyReserved;
                }
            }

        } elseif (!is_null($options)) {
            $maxQty = self::getQuantityForAnyProductTypeFromOptions($productId, $startDate, $endDate, $options, $forceReal);
        }

        $resultObject = new Varien_Object();
        $resultObject->setRetQty($maxQty);
        Mage::dispatchEvent(
            'after_getquantity', array('product'  => $productId, 'result' => $resultObject, 'start_date' => $startDate,
                                       'end_date' => $endDate)
        );
        return intval($resultObject->getRetQty());

    }

    /**
     * Usually used to return list of products within a bundle and the quantity available for each
     * for certain rental dates. This is useful to give the admin order creator more details
     * about each individual product in bundle inventory levels.
     *
     * @param      $product
     * @param      $startDate
     * @param      $endDate
     * @param null $attributes
     * @param null $bundleOptions
     * @param null $bundleOptionsQty1
     * @param null $bundleOptionsQty
     * @param bool $useRequired
     *
     * @return array
     */

    public static function getQuantityForAnyProductTypePerProduct(
        $product, $startDate, $endDate, $attributes = null, $bundleOptions = null, $bundleOptionsQty1 = null,
        $bundleOptionsQty = null, $useRequired = true, $forceReal = false
    ) {
        if (is_object($product)) {
            $productObj = $product;
            $productId = $product->getId();
        } else {
            $productId = $product;
        }
        $qtyPerProduct = array();
        $typeId = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'type_id');
        $isBundle = false;
        if ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE) {
            $isBundle = true;
            if (self::isAllowedOverbook($productId) && !$forceReal) {
                $qtyPerProduct[$productId] = 'is_overbook'; //always return true if overbooking
            }
        }
        if (is_null($attributes) && is_null($bundleOptions)) {
            $qtyArr = self::getReservationProductsArrayIds($productId, $attributes, $bundleOptions, $useRequired);

            foreach ($qtyArr as $iProduct) {
                $qtyPerProduct[$iProduct] = self::getQuantity($iProduct, $startDate, $endDate);

            }
        } else {
            if (!isset($productObj)) {
                $productObj = ITwebexperts_Payperrentals_Helper_Data::initProduct($productId);
            }
            $qtyArr = self::getQuantityArrayForProduct(
                $productObj, 1, $attributes, $bundleOptions, $bundleOptionsQty1, $bundleOptionsQty
            );
            $minQty = 1000000;
            $isAllowOverbooking = true;
            foreach ($qtyArr as $iProduct => $iQty) {
                if (self::isAllowedOverbook($iProduct) && !$forceReal) {
                    $qtyPerProduct[$iProduct] = 'is_overbook';
                    continue;
                }
                $isAllowOverbooking = false;
                $qtyPerProduct[$iProduct] = intval(self::getQuantity($iProduct, $startDate, $endDate));
                if ($isBundle) {
                    if ($minQty > intval(floor($qtyPerProduct[$iProduct] / $iQty))) {
                        $minQty = intval(floor($qtyPerProduct[$iProduct] / $iQty));
                    }
                }
            }
            if ($isBundle) {
                $qtyPerProduct[$productId] = $minQty;
            }
            if ($isBundle && $isAllowOverbooking) {
                $qtyPerProduct[$productId] = 'is_overbook';
            }
        }
        return array($qtyPerProduct, $qtyArr);
    }

    public static function isAdminOverbook(){
        $helperConfig = Mage::helper('payperrentals/config');
        if($helperConfig->allowAdminOverbook() && (Mage::app()->getRequest()->getControllerName() == 'sales_order_create' || Mage::app()->getRequest()->getControllerName() == 'sales_order_edit')){
            return true;
        }else{
            return false;
        }
    }

    public static function showAdminOverbookWarning(){
        $helperConfig = Mage::helper('payperrentals/config');
        if($helperConfig->showAdminOverbookWarning()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Function to return if an item allows overbooking
     *
     * @param $Product
     *
     * @return bool
     */
    public static function isAllowedOverbook($productId)
    {
        if (is_object($productId)) {
            $productId = $productId->getId();
        }

        $allowOverbooking = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
            $productId, 'allow_overbooking'
        );

        if ($allowOverbooking == ITwebexperts_Payperrentals_Model_Product_Allowoverbooking::STATUS_ENABLED
        ) {
            if (!Mage::app()->getStore()->isAdmin()) {
                return true;
            }else{
                return self::isAdminOverbook();
            }
        }else{
            if (Mage::app()->getStore()->isAdmin()) {
                return self::isAdminOverbook();
           }
        }
        return false;
    }

    public static function getQuantityForAnyProductTypeFromOptions($productId, $startDate, $endDate, $options, $forceReal = false)
    {
        list($qtyPerProduct, $qtyArr)
            = ITwebexperts_Payperrentals_Helper_Inventory::getQuantityForAnyProductTypePerProduct(
            $productId, $startDate, $endDate, isset($options['attributes']) ? $options['attributes'] : null,
            isset($options['bundle_option']) ? $options['bundle_option'] : null,
            isset($options['bundle_option_qty1']) ? $options['bundle_option_qty1'] : null,
            isset($options['bundle_option_qty']) ? $options['bundle_option_qty'] : null, false, $forceReal
        );

        $bundleQty = 0;
        $maxQty = 0;
        $isBundle = false;
        foreach ($qtyPerProduct as $iProduct => $iQty) {

            $typeId = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($iProduct, 'type_id');

            if ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE) {
                $bundleQty = $iQty;
                $isBundle = true;
                continue;
            }
            $maxQty = $iQty;
        }

        if ($isBundle) {
            $maxQty = $bundleQty;
        }
        if ($maxQty == 'is_overbook') {
            $maxQty = 10000000;
        }

        return $maxQty;
    }

    /**
     * Update sales_order_item table buyRequest start & end dates
     * Used by manually reserve inventory when dates are changed
     *
     * @param $orderItemId
     * @param $newStartDate
     * @param $newEndDate
     */

    public static function updateOrderBuyRequestStartEndDates($orderItemId, $newStartDate, $newEndDate)
    {
        $orderItemCollection = Mage::getModel('sales/order_item')->load($orderItemId);
        $buyrequest = $orderItemCollection->getBuyRequest();
        $buyrequest->setStartDate($newStartDate);
        $buyrequest->setEndDate($newEndDate);
        $productoptions = $orderItemCollection->getProductOptions();
        $productoptions['info_buyRequest'] = $buyrequest->getData();
        $orderItemCollection->setProductOptions($productoptions);
        $orderItemCollection->save();
        $order = Mage::getModel('sales/order')->load($orderItemCollection->getOrderId());
        $order->setStartDatetime($newStartDate);
        $order->setEndDatetime($newEndDate);
        $order->save();
        if($orderItemCollection->getParentItemId()){
            self::updateOrderBuyRequestStartEndDates($orderItemCollection->getParentItemId(), $newStartDate, $newEndDate);
        }
    }

    public static function getSerialsByProduct($productid, $orderid, $orderItemId = null)
    {
        $productids = array($productid);
        if(!is_null($orderItemId)){
            $salesOrderItem = Mage::getModel('sales/order_item')->getCollection()
                ->addFieldToFilter('parent_item_id', $orderItemId);
            foreach($salesOrderItem as $oItem){
                $productids[] = $oItem->getProductId();
            }
        }
        $sendreturnColl = Mage::getModel('payperrentals/sendreturn')->getCollection()
            ->addOrderIdFilter($orderid);
        $sendreturnColl->addFieldToFilter('product_id', array('in' => $productids))
        ->addFieldToFilter('return_date', array('0000-00-00 00:00:00', '1970-01-01 00:00:00'));
        $sendreturnColl->getSelect()
        ->columns('SUM(qty) as qty_shipped, SUM(qty_parent) as qty_parent_total, CONCAT_WS(",",sn) as sn_all')
        ->group(array('resorder_id','product_id'));
        $serials = $sendreturnColl->getFirstItem()->getSnAll();
        $serials = explode(',', $serials);

        return $serials;
    }

    public static function getMinSaleQuantity($productId){
        if (is_object($productId)) {
            $productId = $productId->getId();
        }
        $minQty = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_min_quantity');
        $useGlobalMin = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'use_global_min_qty');
        if($useGlobalMin){
            return Mage::helper('payperrentals/config')->getMinQtyAllowed();
        }
        if($minQty == ''){
            return 1;
        }
        return intval($minQty);
    }

    public static function getMaxSaleQuantity($productId){
        if (is_object($productId)) {
            $productId = $productId->getId();
        }
        $maxQty = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_max_quantity');
        $useGlobalMax = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'use_global_max_qty');
        if($useGlobalMax){
            return Mage::helper('payperrentals/config')->getMaxQtyAllowed();
        }
        if($maxQty == ''){
            return 10000;
        }
        return intval($maxQty);
    }

    public function getEvents($startDate,$endDate,$productIds,$currentView){
        $events = array();
        $bookedByIds = ITwebexperts_Payperrentals_Helper_Inventory::getBookedQtyForProducts(
            $productIds, $startDate, $endDate, true
        );
        $configHelper = Mage::helper('payperrentals/config');

        foreach($productIds as $productId){
            if(isset($bookedByIds[$productId])) {
                $oldDate = array();
                foreach ($bookedByIds[$productId] as $dateFormatted => $vObject) {

                    if(strtotime($dateFormatted) < strtotime($startDate) || strtotime($dateFormatted) > strtotime($endDate)){
                        continue;
                    }

                    if(($currentView == 'resourceMonth' || $currentView == 'resourceWeek') && in_array(date('Y-m-d', strtotime($dateFormatted)), $oldDate)){
                        continue;
                    }

                    $maxQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($productId);
                    if (date('H:i', strtotime($dateFormatted)) != '00:00' && $currentView == 'resourceDay' ) {
                        $time_increment = $configHelper->getTimeIncrement() * 60;
                        $start
                            = date('Y-m-d', strtotime($dateFormatted)) . ' ' . date('H:i', strtotime($dateFormatted))
                            . ':00';
                        $end = date('Y-m-d', strtotime($dateFormatted)) . ' ' . date(
                                'H:i', strtotime('+' . $time_increment . ' SECOND', strtotime($dateFormatted))
                            ) . ':00';
                        $isHour = true;
                    } else {
                        $start = date('Y-m-d', strtotime($dateFormatted)) . ' 00:00:00';
                        $end = date('Y-m-d', strtotime($dateFormatted)) . ' 23:59:59';
                        $oldDate[] =  date('Y-m-d', strtotime($dateFormatted));
                        $isHour = false;
                    }

                    $evb = array(
                        'title'    => $vObject->getQty() . '/' . ($maxQty - $vObject->getQty()),
                        'url'      => urlencode(
                            $dateFormatted . '||' . implode(';', $vObject->getOrders()) . '||' . $productId
                        ),
                        'start'    => $start,
                        'end'      => $end,
                        'qty'      => $vObject->getQty(),
                        'maxqty'   => ($maxQty - $vObject->getQty()),
                        'df'       => date('Y-m-d', strtotime($dateFormatted)),
                        'is_hour'  => $isHour,
                        'resource' => $productId
                    );
                    if ($maxQty - $vObject->getQty() < 0) {
                        $evb['backgroundColor'] = '#cc0000';
                        $evb['className'] = 'overbookColor';
                    }
                    $events[] = $evb;
                }
            }
        }

        return $events;
    }

    public function getDateDetails($orderArr){
        $orderIdsAr = explode(';', $orderArr[1]);
        $orderCollections = Mage::getModel('payperrentals/reservationorders')->getCollection()
            ->addProductIdFilter($orderArr[2]);

        Mage::dispatchEvent('ppr_before_filter_order', array('collection' => $orderCollections));

        $orderCollections->addOrderIdsFilter($orderIdsAr);
        //$orderCollections->groupByOrder();
        $hasDropoff = false;
        $hasTurnover = false;
        $orderList = '';
        foreach ($orderCollections as $orderItem) {
            $order = Mage::getModel('sales/order')->load($orderItem->getOrderId());
            if($order->getSendDatetime()){
                $hasDropoff = true;
            }
            if($order->getReturnDatetime()){
                $hasDropoff = true;
            }
            if($orderItem->getStartTurnoverBefore()){
                $hasTurnover = true;
            }
            if($orderItem->getEndTurnoverAfter()){
                $hasTurnover = true;
            }

        }
        foreach ($orderCollections as $orderItem) {
            $orderList .= '<tr>';
            $order = Mage::getModel('sales/order')->load($orderItem->getOrderId());

            $shippingId = $order->getShippingAddressId();
            if (empty($shippingId)) {
                $shippingId = $order->getBillingAddressId();
            }
            $address = Mage::getModel('sales/order_address')->load($shippingId);
            $customerName = $address->getFirstname() . ' ' . $address->getLastname();
            $isManualReservation = false;
            if($orderItem->getOrderId() == 0){
                $isManualReservation = true;
            }
            /* if is manual reservation, show order comments instead of first/last name since there is none */
            if($isManualReservation == true){
                $orderList .= '<td>';
                $orderList .= $this->__('None');
                $orderList .= '</td>';

                $orderList .= '<td>';
                $orderList .= $orderItem->getComments();
                $orderList .= '</td>';
            } else {
                $orderList .= '<td>';
                $orderList .= $order->getIncrementId();
                $orderList .= '</td>';

                $orderList .= '<td>';
                $orderList .= $customerName;
                $orderList .= '</td>';
            }

            $orderList .= '<td>';
            $orderList .= ITwebexperts_Payperrentals_Helper_Date::formatDbDate($orderItem->getStartDate());
            $orderList .= '</td>';
            $orderList .= '<td>';
            $orderList .= ITwebexperts_Payperrentals_Helper_Date::formatDbDate($orderItem->getEndDate());
            $orderList .= '</td>';
            if($hasDropoff){
                $orderList .= '<td>';
                if ($orderItem->getDropoff()) {
                    $orderList .= ITwebexperts_Payperrentals_Helper_Date::formatDbDate($orderItem->getDropoff());
                } else {
                    $orderList .= '&nbsp;';
                }
                $orderList .= '</td>';
            }
            if($hasDropoff){
                $orderList .= '<td>';
                if ($orderItem->getPickup()) {
                    $orderList .= ITwebexperts_Payperrentals_Helper_Date::formatDbDate($orderItem->getPickup());
                } else {
                    $orderList .= '&nbsp;';
                }
                $orderList .= '</td>';
            }
            if($hasTurnover){
                $orderList .= '<td>';
                if ($orderItem->getStartTurnoverBefore()) {
                    $orderList .= ITwebexperts_Payperrentals_Helper_Date::formatDbDate(
                        $orderItem->getStartTurnoverBefore()
                    );
                } else {
                    $orderList .= '&nbsp;';
                }
                $orderList .= '</td>';
            }
            if($hasTurnover){
                $orderList .= '<td>';
                if ($orderItem->getEndTurnoverAfter()) {
                    $orderList .= ITwebexperts_Payperrentals_Helper_Date::formatDbDate($orderItem->getEndTurnoverAfter());
                } else {
                    $orderList .= '&nbsp;';
                }
                $orderList .= '</td>';
            }

            $orderList .= '<td>';
            $orderList .= $orderItem->getQty();
            $orderList .= '</td>';

            if($isManualReservation == true){
                $orderList .= '<td>';
                $orderList .= $this->__('None');
                $orderList .= '</td>';
            } else {
                $orderList .= '<td>';
                if(Mage::helper('itwebcommon')->isVendorAdmin()){
                    $orderList .= '<a href="' . Mage::getUrl('vendors/sales_order/view', array('order_id' => $order->getEntityId())) . '">' . Mage::helper('payperrentals')->__('View') . '</a>';
                } else {
                $orderList .= '<a href="' . Mage::getUrl('adminhtml/sales_order/view', array('order_id' => $order->getEntityId())) . '">' . Mage::helper('payperrentals')->__('View') . '</a>';
                }
                $orderList .= '</td>';
            }
            $orderList .= '</tr>';
        }
        $orderList = '<table cellpadding="10" cellspacing="10" border="0" style="min-width:400px;">
                        <tr>
                            <td style="font-weight: bold">' . $this->__('Order') . '</td>
                            <td style="font-weight: bold">' . $this->__('Customer Name') . '</td>
                            <td style="font-weight: bold">' . $this->__('Start') . '</td>
                            <td style="font-weight: bold">' . $this->__('End') . '</td>' .
            (($hasDropoff) ? '<td style="font-weight: bold">' . $this->__('Dropoff') . '</td>
                                            <td style="font-weight: bold">' . $this->__('Pickup') . '</td>' : '') .
            (($hasTurnover) ? '<td style="font-weight: bold">' . $this->__('Start W/Turnover') . '</td>
                                             <td style="font-weight: bold">' . $this->__('End W/Turnover') . '</td>' : '') .
            '<td style="font-weight: bold">' . $this->__('Qty') . '</td>
                            <td style="font-weight: bold">' . $this->__('View Order') . '</td>
                       </tr>' .
            $orderList;
        $orderList .= '</table>';
        $details['html'] = $orderList;
        $details['date'] = ITwebexperts_Payperrentals_Helper_Date::formatDbDate($orderArr[0]);
        return $details;
    }

    public function getProductPrices($sourceOrder)
    {
        $output = array();
        $resOrders = Mage::getModel('payperrentals/reservationorders')
            ->getCollection()
            ->addOrderIdFilter($sourceOrder->getId());
        $hasReservations = ($resOrders->getSize() > 0)?true:false;
        $orderItems = $sourceOrder->getAllItems();
        foreach ($orderItems as $item) {

            $originalStartDate = $item->getBuyRequest()->getStartDate();
            $originalEndDate = $item->getBuyRequest()->getEndDate();


            $stockAvail = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity(
                $item->getProduct()->getId(), $originalStartDate, $originalEndDate, ITwebexperts_Payperrentals_Helper_Data::objectToArray($item->getBuyRequest())
            );
            if($stockAvail == '10000000'){
                $output[$item->getId()] = array(
                    'avail'     => '',
                    'remaining' => ''
                );
            }else {
                $output[$item->getId()] = array(
                    'avail'     => $stockAvail + ($hasReservations?$item->getBuyRequest()->getQty():0),
                    'remaining' => ($stockAvail)
                );
            }
        }
        return $output;
    }

    /**
     * @param array $serialNumbersForOrderItem
     */

    private function updateSerialNumbersStatus($serialNumbersForOrderItem)
    {
        foreach ($serialNumbersForOrderItem as $serial) {
            Mage::getResourceSingleton('payperrentals/serialnumbers')
                ->updateStatusBySerial($serial, 'O');
        }
    }

    /**
     * @param ITwebexperts_Payperrentals_Model_Reservationorders $reservationOrder
     * @param array $serialNumbers
     * @param array $orderItems
     * @param int $reservationId
     *
     * @return array
     */
    private function getAllTheSerialNumbersForOrder($reservationOrder, $serialNumbers, $orderItems, $reservationId, $shipQty)
    {
        /** @var $payperrentalsUseSerials bool */
        $payperrentalsUseSerials = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
            $reservationOrder->getProductId(), 'payperrentals_use_serials'
        );

        /** @var array $serialNumbersForOrderItems */
        $serialNumbersForOrderItems = array();

        /**
         * Get a list of manually entered serial numbers in the shipment form
         */
        if ($payperrentalsUseSerials) {
            foreach ($serialNumbers as $salesOrderItemId => $serialArr) {
                if ($salesOrderItemId == $orderItems[$reservationId]) {
                    foreach ($serialArr as $enteredSerial) {
                        if ($enteredSerial != '') {
                            if(!in_array($enteredSerial, $serialNumbersForOrderItems)) {
                                $serialNumbersForOrderItems[] = $enteredSerial;
                            }
                        }
                    }
                }
            }

            /*
            * Completes the array of entered serial numbers with non broken and not under maintenance serial numbers
            * */

            if (count($serialNumbersForOrderItems) < $shipQty) {
                $collectionSerials = Mage::getModel('payperrentals/serialnumbers')
                    ->getCollection()
                    ->addEntityIdFilter($reservationOrder->getProductId())
                    ->addSelectFilter(
                        "NOT FIND_IN_SET(sn, '" . implode(',', $serialNumbersForOrderItems)
                        . "') AND (status = 'A')"
                    );

                $j = 0;
                foreach ($collectionSerials as $item) {
                    /** @var $item ITwebexperts_Payperrentals_Model_Serialnumbers */
                    if(!in_array($item->getSn(), $serialNumbersForOrderItems)) {
                        $serialNumbersForOrderItems[] = $item->getSn();
                        if ($j >= $shipQty - count($serialNumbersForOrderItems)) {
                            break;
                        }
                        $j++;
                    }
                }

            }

            $this->updateSerialNumbersStatus($serialNumbersForOrderItems);
        }
        return $serialNumbersForOrderItems;
    }


    public function processShipment($collectionReservations, $serialNumbers, $orderItems, $items = null, $shipItemsArray = null, $qtys){

        foreach ($collectionReservations as $reservationOrder) {

            /** @var $reservationOrder ITwebexperts_Payperrentals_Model_Reservationorders */
            $reservationId = $reservationOrder->getId();

            if(!is_null($items)) {
                $shipqty = 0;
                $shipqtyparent = 0;
                foreach ($items as $item) {
                    if ($item->getProductId() == $reservationOrder->getProductId()) {
                        $shipqty = $item->getQty();
                        if ($item->getOrderItem()->getParentItem()) {
                            $parentItemId = $item->getOrderItem()->getParentItem()->getId();
                            if (isset($shipItemsArray['items'][$parentItemId])) {
                                $shipqtyparent = $shipItemsArray['items'][$parentItemId];
                            } else {
                                $shipqtyparent = 1;
                            }
                        }
                        break;
                    }
                }
            } else{
                $shipqty = $qtys[$orderItems[$reservationId]];
                $shipqtyparent = 0;
                $salesOrderItem = Mage::getModel('sales/order_item')->getCollection()
                    ->addFieldToFilter('item_id', $orderItems[$reservationId]);
                if($salesOrderItem->getFirstItem()->getParentItemId()){
                    $shipqtyparent = 1;
                }
            }
            $serialNumbersForOrder = $this->getAllTheSerialNumbersForOrder(
                $reservationOrder, $serialNumbers, $orderItems, $reservationId, $shipqty
            );
            $serialNumberForOrderAsString = implode(',', $serialNumbersForOrder);

            $sendDatetime = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
            Mage::getModel('payperrentals/sendreturn')
                ->setOrderId($reservationOrder->getOrderId())
                ->setProductId($reservationOrder->getProductId())
                ->setResStartdate($reservationOrder->getStartDate())
                ->setResEnddate($reservationOrder->getEndDate())
                ->setSendDate($sendDatetime)
                ->setQty($shipqty)
                ->setSn($serialNumberForOrderAsString)
                ->setQtyParent($shipqtyparent)
                ->setResorderId($reservationId)
                ->save();

            /*get current shipped qty*/
            $collectionSendReturn = Mage::getModel('payperrentals/sendreturn')
                ->getCollection()
                ->addSelectFilter("resorder_id = '" . $reservationId . "'")
                ->addSelectFilter("send_date <> '0000-00-00 00:00:00'");
            $collectionSendReturn->getSelect()
                ->columns('SUM(qty) as qty_shipped')
                ->group('resorder_id');

            Mage::getResourceSingleton('payperrentals/reservationorders')->updateShippedQty(
                $reservationId, (($collectionSendReturn->getFirstItem()->getQtyShipped()?$collectionSendReturn->getFirstItem()->getQtyShipped():0))
            );

            $order = Mage::getModel('sales/order')->load($reservationOrder->getOrderId());
            //$order->setSendDatetime($sendDatetime);
            //$order->save();

            $product = Mage::getModel('catalog/product')->load($reservationOrder->getProductId());
            if ($reservationOrder->getStartdate() != '0000-00-00 00:00:00') {
                $sendPerCustomer[$order->getCustomerEmail()][$reservationOrder->getOrderId()][$product->getId()]['name'] = $product->getName();
                $sendPerCustomer[$order->getCustomerEmail()][$reservationOrder->getOrderId()][$product->getId()]['serials'] = $serialNumberForOrderAsString;
                $sendPerCustomer[$order->getCustomerEmail()][$reservationOrder->getOrderId()][$product->getId()]['start_date'] = $reservationOrder->getStartDate();
                $sendPerCustomer[$order->getCustomerEmail()][$reservationOrder->getOrderId()][$product->getId()]['end_date'] = $reservationOrder->getEndDate();
                $sendPerCustomer[$order->getCustomerEmail()][$reservationOrder->getOrderId()][$product->getId()]['send_date'] = $sendDatetime;

            }
        }
        if(isset($sendPerCustomer)) {
            ITwebexperts_Payperrentals_Helper_Emails::sendEmail('send', $sendPerCustomer);
        }
    }

    /**
     * Returns items, used from ReturnContoller since we have 2 Return Controllers one for
     * Vendors one for regular Payperrentals we use the same function instead of repeating in controller
     *
     * @param $_sendItems
     * @throws Exception
     */

    public function processReturn($_sendItems){
        /* @var $sendReturns ITwebexperts_Payperrentals_Model_Mysql4_Sendreturn_Collection */
        $sendReturnCollection = Mage::getModel('payperrentals/sendreturn')->getCollection();
        $sendReturnCollection->addFieldToFilter('id', array('in' => $_sendItems));
        $returnTime = date('Y-m-d H:i:s', (int)Mage::getModel('core/date')->timestamp(time()));
        $qtyArray = Mage::app()->getRequest()->getParam('qty');
        if(Mage::app()->getRequest()->getParam('sn')) {
            $snArray = Mage::app()->getRequest()->getParam('sn');
        }
        foreach ($sendReturnCollection as $sendReturn) {
            $returnqtyparent = 0;
            $returnqty = 1;
            if(isset($qtyArray[$sendReturn->getId()])){
                $returnqty = $qtyArray[$sendReturn->getId()];
                if($sendReturn->getQtyParent()) {
                    if($qtyArray[$sendReturn->getId()]  % $sendReturn->getQty() == 0) {
                        $returnqtyparent = intval($qtyArray[$sendReturn->getId()] / $sendReturn->getQty());
                    }else{
                        Mage::throwException('Cannot return shipment');
                        return false;
                    }
                }
            }
           //$serialNumbersArray = $sendReturn->getSn()? explode(',', $sendReturn->getSn()) : array();
            $serialNumbers = array();
            if(isset($snArray[$sendReturn->getId()])){
                foreach($snArray[$sendReturn->getId()] as $snItem){
                    //if(in_array($snItem, $serialNumbersArray)){
                        $serialNumbers[] = $snItem;
                        if(count($serialNumbers) >= $returnqty) break;
                   // }
                }
            }else{
                $serialNumbers = array();
            }
            Mage::getModel('payperrentals/sendreturn')
                ->setOrderId($sendReturn->getOrderId())
                ->setProductId($sendReturn->getProductId())
                ->setResStartdate($sendReturn->getResStartdate())
                ->setResEnddate($sendReturn->getResEnddate())
                ->setReturnDate($returnTime)
                ->setQty($returnqty)//here needs a check this should always be true
                ->setSn(implode(',',$serialNumbers))
                ->setQtyParent($returnqtyparent)
                ->setResorderId($sendReturn->getResorderId())
                ->save();

            foreach ($serialNumbers as $serial) {
                Mage::getResourceSingleton('payperrentals/serialnumbers')
                    ->updateStatusBySerial($serial, 'A');
            }
            /*get current return qty*/
            $collectionSendReturn = Mage::getModel('payperrentals/sendreturn')
                ->getCollection()
                ->addSelectFilter("resorder_id = '" . $sendReturn->getResorderId() . "'")
                ->addSelectFilter("return_date <> '0000-00-00 00:00:00'");
            $collectionSendReturn->getSelect()
                ->columns('SUM(qty) as qty_returned')
                ->group('resorder_id');

            Mage::getResourceSingleton('payperrentals/reservationorders')->updateReturnedQty(
                $sendReturn->getResorderId(), (($collectionSendReturn->getFirstItem()->getQtyReturned()?$collectionSendReturn->getFirstItem()->getQtyReturned():0))
            );

            $order = Mage::getModel('sales/order')->load($sendReturn->getOrderId());

            $product = Mage::getModel('catalog/product')->load($sendReturn->getProductId());
            if ($sendReturn->getResStartdate() != '0000-00-00 00:00:00') {
                $returnPerCustomer[$order->getCustomerEmail()]['is_queue'] = false;
                $returnPerCustomer[$order->getCustomerEmail()][$sendReturn->getOrderId()][$product->getId()]['name'] = $product->getName();
                $returnPerCustomer[$order->getCustomerEmail()][$sendReturn->getOrderId()][$product->getId()]['serials'] = $sendReturn->getSn();
                $returnPerCustomer[$order->getCustomerEmail()][$sendReturn->getOrderId()][$product->getId()]['start_date'] = $sendReturn->getResStartdate();
                $returnPerCustomer[$order->getCustomerEmail()][$sendReturn->getOrderId()][$product->getId()]['end_date'] = $sendReturn->getResEnddate();
                $returnPerCustomer[$order->getCustomerEmail()][$sendReturn->getOrderId()][$product->getId()]['send_date'] = $sendReturn->getSendDate();
                $returnPerCustomer[$order->getCustomerEmail()][$sendReturn->getOrderId()][$product->getId()]['return_date'] = $returnTime;
            }
            ITwebexperts_Payperrentals_Helper_Inventory::updateInventory($sendReturn->getProductId());
        }
        if(isset($returnPerCustomer)) {
            ITwebexperts_Payperrentals_Helper_Emails::sendEmail('return', $returnPerCustomer);
        }
    }



}