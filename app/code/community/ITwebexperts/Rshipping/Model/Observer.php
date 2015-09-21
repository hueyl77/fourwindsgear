<?php




/**
 * @category   Itwebexperts
 * @package    Itwebexperts_Rshipping
 * @copyright  Copyright (c) 2013
 *
 */
class ITwebexperts_Rshipping_Model_Observer
{

    static protected $_singletonFlag = false;

    public function saveShippingMethod(Varien_Event_Observer $observer)
    {
        if (Mage::registry('already_saved')) return;
        Mage::register('already_saved', true);
        if (!self::$_singletonFlag) {
            self::$_singletonFlag = true;
            /** @var $product Mage_Catalog_Model_Product */
            $product = $observer->getEvent()->getProduct();
            $shipMethod = $product->getShippingMethod();
            if (is_null($shipMethod)) return;
            $shippingForProduct = '';
            /** Remove all links before new save */
            /** @var $linked ITwebexperts_Rshipping_Model_Mysql4_Products_Collection */
            $linked = Mage::getModel('rshipping/products')->getCollection();
            $linked->addFieldToFilter('product_id', $product->getId());

            $needUpdate = false;
            if ($linked->getSize() == count($shipMethod)) {
                foreach ($linked as $linkedItem) {
                    if (array_search($linkedItem->getRshippingId(), $shipMethod) === false) {
                        $needUpdate = true;
                    }
                }
            } else {
                $needUpdate = true;
            }
            if (!$needUpdate) return;
            /** @var $linkToProduct ITwebexperts_Rshipping_Model_Products */
            foreach ($linked as $linkToProduct) {
                $linkToProduct->delete();
            }

            if ($shipMethod && is_array($shipMethod)) {
                foreach ($shipMethod as $methodId) {
                    $obj = Mage::getModel('rshipping/products');
                    $obj->setData('rshipping_id', $methodId);
                    $obj->setData('product_id', $product->getId());
                    try {
                        $obj->save();
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }
                $shippingForProduct = implode(',', $shipMethod);
            }
            $product->setShippingMethod($shippingForProduct);
        }
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }

    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Add shipping method after adding product to cart
     * @param Varien_Event_Observer $observer
     * */
    public function addShippingMethod(Varien_Event_Observer $observer)
    {
        $shippingMethod = $this->_getRequest()->getParam('shipping_method');
        /** @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $session->getQuote();
        /** @var $address Mage_Sales_Model_Quote_Address */
        $address = $quote->getShippingAddress();

        if (!is_null($shippingMethod) && $shippingMethod != '') {
            $sessionCost = $session->getQuoteShippingCost();
            if (!$sessionCost || $sessionCost == 0 || !$address->getShippingMethod()) {
                $country = Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY, Mage::app()->getStore()->getId());
                $customerSession = Mage::getSingleton('customer/session');
                if ($customerSession->isLoggedIn()) {
                    $customerAddress = $customerSession->getCustomer()->getDefaultShippingAddress();
                    if ($customerAddress != null) {
                        $country = ($customerAddress->getId()) ? $customerAddress->getCountryId() : Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY, Mage::app()->getStore()->getId());
                    }
                }
                $address->setCountryId($country);
                if ($this->_getRequest()->getParam('zip_code')) {
                    $address->setPostcode($this->_getRequest()->getParam('zip_code'));
                    $geoData = Mage::helper('rshipping')->getGeoData($this->_getRequest()->getParam('zip_code'));
                    if (array_key_exists('Address', $geoData) && array_key_exists('City', $geoData['Address'])) $address->setCity($geoData['Address']['City']);
                    if (array_key_exists('Address', $geoData) && array_key_exists('StateProvinceCode', $geoData['Address'])) {
                        $regionModel = Mage::getModel('directory/region')->loadByCode($geoData['Address']['StateProvinceCode'], $country);
                        if ($regionModel->getId()) {
                            $address->setRegionId($regionModel->getId());
                        } else {
                            $address->setRegion($geoData['Address']['StateProvinceCode']);
                        }
                    }
                }
                $address->setShippingMethod($shippingMethod);

                $address->setCollectShippingRates(true)->collectShippingRates();
                $quote->setShippingMethod($shippingMethod);
                $method = $quote->getShippingMethod();

                $carrier = null;
                if ($method) {
                    foreach ($address->getAllShippingRates() as $rate) {
                        /** @var $rate Mage_Sales_Model_Quote_Address_Rate */
                        if ($rate->getCode() == $method) {
                            $carrier = $rate->getCarrier();
                            $session->setQuoteShippingCost($address->getQuote()->getStore()->convertPrice($rate->getPrice(), false));
                            Mage::getSingleton('checkout/type_onepage')->getQuote()->getShippingAddress()->setCountryId($country)->setShippingMethod($method)->save();
                            break;
                        }
                    }
                }
                $session->getQuote()->save();
                $quote->setTotalsCollectedFlag(false);
                $quote->collectTotals();
                $session->resetCheckout();
                if ($carrier && $method)
                    $session->setData('rental_shipping', array(
                        'carrier' => $carrier,
                        'method' => $method
                    ));
            } else {
                Mage::log(Mage::helper('rshipping')->__('Can\'t add shipping method'));
            }
        }
    }

    /**
     * Clear shipping methods if cart empty
     * @param Varien_Event_Observer $observer
     * */
    public function salesQuoteRemoveItem(Varien_Event_Observer $observer)
    {
        $cartCollection = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $address = $quote->getShippingAddress();
        if (!count($cartCollection)) {
            $address->setShippingMethod('');
            $address->setCollectShippingRates(true)->collectShippingRates();
            $quote->save();
            Mage::getSingleton('checkout/session')->unsetData('rental_shipping');
        }
    }

    /**
     * Add shipping method after customer login
     * @param Varien_Event_Observer $observer
     * */
    public function salesQuoteMergeAfter(Varien_Event_Observer $observer)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();
        $rentalShipping = (Mage::getSingleton('checkout/session')->hasData('rental_shipping')) ? Mage::getSingleton('checkout/session')->getData('rental_shipping') : null;
        if (!is_null($rentalShipping)) {
            $quote->getShippingAddress()
                ->setShippingMethod($rentalShipping['method']);
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function updateTurnoverPeriods(Varien_Event_Observer &$observer)
    {
        /*
         * If shipping is enabled then turnover are got from the defined ones not the global ones.
         */
        $configHelper = Mage::helper('rshipping/config');
        if (!$configHelper->isEnabled(Mage::app()->getStore()->getId())) return;
        $turnoverTimeBeforeShip = 0; //$observer->getEvent()->getTurnoverTimeBefore();
        $turnoverTimeAfterShip = 0; //$observer->getEvent()->getTurnoverTimeAfter();
        $productId = $observer->getEvent()->getProduct();
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $shippingMethod = $observer->getEvent()->getShippingMethod();
        $postcode = $observer->getEvent()->getPostcode();

        /** Loading rental shipping method data */
        $rentalShippingMethod = Mage::getModel('rshipping/rshipping')->load($shippingMethod, 'shipping_method');
        if (!$rentalShippingMethod->getId()) return;

        /** Check if all methods allowing, without product configuration */
        $storeId = (Mage::app()->getRequest()->getParam('store_id')) ? Mage::app()->getRequest()->getParam('store_id') : Mage::app()->getStore()->getId();
        $isAllowAllMethods = $configHelper->isAllowAllMethods($storeId);
        if ($isAllowAllMethods) {
            $productAvailability = true;
        } else {
            $prodShippingMethod = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($product->getId(), 'shipping_method') ;
            $shippingMethodArr = array();
            if ($prodShippingMethod) {
                $shippingMethodArr = explode(',', $prodShippingMethod);
            }
            $productAvailability = (in_array($rentalShippingMethod->getId(), $shippingMethodArr) && $product->getAllowShipping()) ? true : false;
        }

        if ($productAvailability) {
            $startPaddingCalcTimeStamp = $observer->getEvent()->getOrderItemBeforeTurnoverTimestamp();

            /** Calculate ups transit time by API */
            $upsTransitTime = 0;
            if (stripos($rentalShippingMethod->getShippingMethod(), 'ups') !== false && $configHelper->isLiveTransitApi(Mage::app()->getStore()->getId()) && $rentalShippingMethod->getUseLiveUpsApi()) {
                $upsTransitTime = Mage::helper('rshipping')->getTimeInTransit($postcode, $product, $rentalShippingMethod->getShippingMethod());
            }
            $turnoverTimeBeforeShip += ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                    $rentalShippingMethod->getTurnoverBeforePeriod(), $rentalShippingMethod->getTurnoverBeforeType()
                ) + ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds($upsTransitTime, 3);
            $turnoverTimeAfterShip += ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                $rentalShippingMethod->getTurnoverAfterPeriod(), $rentalShippingMethod->getTurnoverAfterType()
            );

            /** Calculate turnover before for exists orders with ups shipping method */
            $turnoverTimeBeforeShipForIgnore = $turnoverTimeBeforeShip;
            if (stripos($rentalShippingMethod->getShippingMethod(), 'ups') !== false && $rentalShippingMethod->getIgnoreTurnoverDay()) {
                $ignoreTurnoverDayAr = explode(',', $rentalShippingMethod->getIgnoreTurnoverDay());
                while ($turnoverTimeBeforeShipForIgnore != 0) {
                    if (in_array(date('w', $startPaddingCalcTimeStamp - $turnoverTimeBeforeShipForIgnore), $ignoreTurnoverDayAr)) {
                        /* Plus 1 day if day ignored*/
                        $turnoverTimeBeforeShip += ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(1, 3);
                    }
                    $turnoverTimeBeforeShipForIgnore -= ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                        1, 3
                    );
                }
            }

            $observer->getEvent()->setTurnoverTimeBefore($turnoverTimeBeforeShip);
            $observer->getEvent()->setTurnoverTimeAfter($turnoverTimeAfterShip);
        }
    }

    public function updateBookedHtmlForShipping(Varien_Event_Observer &$observer){
        $requestParams = $observer->getEvent()->getRequestParams();
        $bookedHtml = $observer->getEvent()->getBookedHtml();
        $product = $observer->getEvent()->getProduct();
        if (array_key_exists('shipping_method', $requestParams)) {
            if ($requestParams['shipping_method'] != '') {
                $shippingMethodForLoad = $requestParams['shipping_method'];
            } elseif (array_key_exists('shipping_method_select_box', $requestParams) && $requestParams['shipping_method_select_box'] != 'null') {
                $shippingMethodForLoad = $requestParams['shipping_method_select_box'];
            } elseif (array_key_exists('shipping_method_select_box_additional', $requestParams)) {
                $shippingMethodForLoad = $requestParams['shipping_method_select_box_additional'];
            } else {
                $shippingMethodForLoad = '';
            }
            $shippingMethod = Mage::getModel('rshipping/rshipping')->load($shippingMethodForLoad, 'shipping_method');

            $bookedHtml['shippingMinRentalPeriod'] = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                $shippingMethod->getMinRentalPeriod(), $shippingMethod->getMinRentalType()
            );
            $bookedHtml['shippingMinRentalMessage'] = Mage::helper('payperrentals')->__('Minimum rental period is "%s". Please make sure you select at least that number',
                ITwebexperts_Payperrentals_Helper_Html::getTextForType(
                    $shippingMethod->getMinRentalPeriod(), $shippingMethod->getMinRentalType()
                )
            );


            $upsTransitTime = 0;
            if (stripos($shippingMethod->getShippingMethod(), 'ups') !== false && Mage::getStoreConfig('payperrentals/rshipping_ups/enabled') && $shippingMethod->getUseLiveUpsApi()) {
                $zip = $requestParams['zip_code'];
                $upsTransitTime = Mage::helper('rshipping')->getTimeInTransit($zip, $product, $shippingMethod->getShippingMethod());
            }

            $paddingDays = array();
            /**
             * Day cutoff time. If current time > cutoff time => disable today and start from tomorrow
             * */
            $currentTime = (int)Mage::getModel('core/date')->timestamp(time());
            if ($shippingMethod->getShippingCutoffTime() != '') {
                $shipCutoffTime = strtotime(date('Y-m-d', $currentTime) . ' ' . $shippingMethod->getShippingCutoffTime());
                if ($currentTime > $shipCutoffTime) {
                    //$bookedHtml['disableToday'] = true;
                    $dateFormatted = date('Y-m-d', $currentTime);
                    $paddingDays[] = $dateFormatted;
                }
            }

            if ($shippingMethod->getStartDisabledDays()) {
                $bookedHtml['disabledForStartRange'] = ITwebexperts_Payperrentals_Helper_Data::getWeekdayForJs(explode(',', $shippingMethod->getStartDisabledDays()));
            }else{
                $bookedHtml['disabledForStartRange'] = '';
            }

            if ($shippingMethod->getEndDisabledDays()) {
                $bookedHtml['disabledForEndRange'] = ITwebexperts_Payperrentals_Helper_Data::getWeekdayForJs(explode(',', $shippingMethod->getEndDisabledDays()));
            }else{
                $bookedHtml['disabledForEndRange'] = '';
            }
            if (stripos($shippingMethod->getShippingMethod(), 'ups') !== false
                && $shippingMethod->getIgnoreTurnoverDay()
            ) {
                $bookedHtml['ignoreTurnoverDay'] = ITwebexperts_Payperrentals_Helper_Data::getWeekdayForJs(explode(',', $shippingMethod->getIgnoreTurnoverDay()));
            }


            $turnoverBeforePeriod = (int)$shippingMethod->getTurnoverBeforePeriod() + $upsTransitTime;
            $turnoverAfterPeriod = (int)$shippingMethod->getTurnoverAfterPeriod();
            $bookedHtml['turnoverTimeBefore'] = $turnoverBeforePeriod;
            $bookedHtml['turnoverTimeAfter'] = $turnoverAfterPeriod;

            if ($turnoverBeforePeriod > 0) {
                $startTimePadding = Mage::getSingleton('core/date')->timestamp(time());
                while ($turnoverBeforePeriod > 0) {
                    $dateFormatted = date('Y-m-d', $startTimePadding).' 00:00';
                    if (!in_array($dateFormatted, $paddingDays)) {
                        $paddingDays[] = $dateFormatted;
                    }
                    $startTimePadding = strtotime('+1 day', $startTimePadding);
                    $turnoverBeforePeriod--;
                }
            }

                if(count($paddingDays) > 0){
                    $bookedHtml['disabledDatesPadding'] = ITwebexperts_Payperrentals_Helper_Data::toFormattedDateArray($paddingDays, false);
                }

            $observer->getEvent()->setBookedHtml($bookedHtml);
        }
    }

    /**
     * Update shipping price on the fly
     * @param Varien_Event_Observer $observer
     */
    public function salesQuoteCollectTotalsBefore(Varien_Event_Observer $observer)
    {
        /** @var $salesQuote Mage_Sales_Model_Quote */
        $salesQuote = $observer->getEvent()->getQuote();
        /** @var $salesQuoteAddress Mage_Sales_Model_Quote_Address */
        foreach ($salesQuote->getAllAddresses() as $salesQuoteAddress) {
            $shippingMethod = $salesQuoteAddress->getShippingMethod();
            if (empty($shippingMethod)) continue;
            /** @var $rentalShipping ITwebexperts_Rshipping_Model_Rshipping */
            $rentalShipping = Mage::getModel('rshipping/rshipping')->load($shippingMethod, 'shipping_method');
            if ($rentalShipping->getId() && $rentalShipping->getUseCustomShippingAmount() && $rentalShipping->getShippingAmount()) {
                /**
                 * Collect totals for change shipping amount
                 */
                $salesQuoteAddress->setSubtotal(0);
                $salesQuoteAddress->setBaseSubtotal(0);
                $salesQuoteAddress->setGrandTotal(0);
                $salesQuoteAddress->setBaseGrandTotal(0);
                $salesQuoteAddress->collectTotals();

                $shippingAmountType = $rentalShipping->getCustomShippingAmountType();
                $incrementValue = 1;
                if ($shippingAmountType == Mage::getSingleton('rshipping/source_shippingAmountType')->getProductAmountType()) {
                    $incrementValue = count($salesQuote->getAllItems());
                }
                $shippingAmount = $rentalShipping->getShippingAmount() * $incrementValue;
                /**
                 * Set flag for non recollect shipping
                 */
                $salesQuoteAddress->setCollectShippingRates(false);
                $shippingRates = $salesQuoteAddress->getShippingRatesCollection();
                foreach ($shippingRates as $shippingRate) {
                    $code = $shippingRate->getCode();
                    if ($code == $shippingMethod) {
                        $shippingRate->setPrice($shippingAmount);
                    }
                }
            }
        }
    }
}
