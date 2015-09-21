<?php


class ITwebexperts_Payperrentals_Helper_Rendercart extends Mage_Core_Helper_Abstract
{
    /**
     * function used to show the price near the bundle options.
     * @param $item
     * @param $order
     *
     * @return string
     */
    public function getBundleOptionPriceAsHtml($item, $order)
    {
        $result = '';
        if ($item->getParentItem()) {
            $isReservation = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
                $item->getParentItem()->getProductId(), 'is_reservation'
            );
            $bundlePriceType = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
                $item->getParentItem()->getProductId(), 'bundle_pricingtype'
            );
            if ($isReservation) {
                if (!$bundlePriceType || $bundlePriceType
                    == ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype::PRICING_BUNDLE_PERPRODUCT
                ) {
                    if ($item instanceof Mage_Sales_Model_Order_Item) {
                        $options = $item->getProductOptions();
                    } else {
                        $options = $item->getOrderItem()->getProductOptions();
                    }
                    if (isset($options['info_buyRequest']['start_date'])) {
                        $selectionPrice = ITwebexperts_Payperrentals_Helper_Price::calculatePrice(
                            $item->getProductId(), $options['info_buyRequest']['start_date'],
                            $options['info_buyRequest']['end_date'], $options['info_buyRequest']['qty'],
                            ITwebexperts_Payperrentals_Helper_Data::getCustomerGroup()
                        );
                        $formattedPrice = $order->formatPrice($selectionPrice);
                    } else {
                        $formattedPrice = ITwebexperts_Payperrentals_Helper_Price::getPriceListHtml(
                            $item->getProductId(), -1, true
                        );
                    }
                } else {
                    $formattedPrice = '';
                }
                $result = " " . $formattedPrice;
            }
        }
        return $result;
    }

    private function _getOptionsArray($product, $options){
        $source = array();

        if (is_object($product) && !is_object($product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION)) && is_object($product->getCustomOption('info_buyRequest'))) {
            $source = unserialize($product->getCustomOption('info_buyRequest')->getValue());
        }elseif (isset($options['info_buyRequest']) && isset($options['info_buyRequest'][ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION])) {
            $source =  $options['info_buyRequest'];
        }elseif(is_object($product)) {
            if ($product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION)) {
                $start_date = $product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION)->getValue();
                $end_date = $product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION)->getValue();
                if (is_object($product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::NON_SEQUENTIAL))) {
                    $nonSequential = $product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::NON_SEQUENTIAL)->getValue();
                    $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::NON_SEQUENTIAL] = $nonSequential;
                }
                $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION] = $start_date;
                $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION] = $end_date;
            }
        }

        return $source;
    }

    public function renderDates($options, $item = null, $product = null, $isCart = false)
    {

        $isSingle = true;
        $nonSequential = 0;
        if ($item && $item->getOrder() && !$isCart) {
            $isSingleBool = ITwebexperts_Payperrentals_Helper_Data::isSingleOrder($item->getOrder());
            $isSingle = $isSingleBool['bool'];
        }else if ($item && $item->getQuote() && !$isCart) {
            $isSingle = false;
        }

        $productId = -1;
        $storeId = 1;
        $qty = 0;
        if (!is_null($item) && !is_null($item->getProductId())) {
            $productId = $item->getProductId();
            $storeId = $item->getStoreId();
            $qty = $item->getQty();
            $product = $item->getProduct();
        } else {
            if (!is_null($product) && is_object($product)) {
                $productId = $product->getId();
            } elseif (isset($options['info_buyRequest']['product'])) {
                $productId = $options['info_buyRequest']['product'];
            }
        }
        $showTime = (bool)Mage::getResourceModel('catalog/product')
            ->getAttributeRawValue(
                $productId,
                'payperrentals_use_times',
                $storeId
            );
        $customerGroup = ITwebexperts_Payperrentals_Helper_Data::getCustomerGroup();

        $source = $this->_getOptionsArray($product, $options);
        $options = array();

        $isBuyout = isset($source['buyout'])?$source['buyout']:'false';
        if($isBuyout != "false") {
            $options[] = array('label' => $this->__('Product Type'),
                               'value' => 'Rental Buyout',
                               'type' => 'reservation');
        }else if (isset($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION] ) && $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION] != '') {
            $startDate = ITwebexperts_Payperrentals_Helper_Date::formatDbDate($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION] , !$showTime, false);
            $endDate = ITwebexperts_Payperrentals_Helper_Date::formatDbDate($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION] , !$showTime, false);
            if (!isset($nonSequential) || $nonSequential == 0) {
                $endDate = ITwebexperts_Payperrentals_Helper_Date::formatDbDate($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION] , !$showTime, false);
                $options[] = array('label' => $this->__('Start Date'), 'value' => $startDate, 'type' => 'reservation');
                $options[] = array('label' => $this->__('End Date'), 'value' => $endDate, 'type' => 'reservation');

            } else {
                $options[] = array('label' => $this->__('Dates:'), 'value' => ITwebexperts_Payperrentals_Helper_Date::localiseNonsequentialBuyRequest($startDate, $showTime), 'type' => 'reservation');
            }

            if(!$isCart && $isSingle){
                $options = array();
            }

            $damageWaiver = ITwebexperts_Payperrentals_Helper_Price::getDamageWaiver($productId, $startDate, $endDate, $customerGroup, $qty);
            if ($damageWaiver) {
                $options[] = array(
                    'label' => $this->__('Damage Waiver:'),
                    'value' => ITwebexperts_Payperrentals_Helper_Price::getDamageWaiverHtml($item, $damageWaiver, (bool)$item->getBuyRequest()->getDamageWaiver(), $qty),
                    'type' => 'reservation'
                );
            }
        } else {
            return array();
        }

        $resultObject = new Varien_Object();
        $resultObject->setResult($options);
        Mage::dispatchEvent('render_cart', array('options' => $source, 'result' => $resultObject, 'product' => $product, 'item' => $item, 'is_cart' => $isCart));

        return $resultObject->getResult();

    }

    private function _completeBuyRequestObject(&$buyRequest, $product, $productType){
        $msg = '';
        $useNonSequential = Mage::helper('payperrentals/config')->isNonSequentialSelect(Mage::app()->getStore()->getId());

        if (!$buyRequest->getStartDate()) {
            //if (ITwebexperts_Payperrentals_Helper_Data::isUsingGlobalDates($product)) {
                if (Mage::getSingleton('core/session')->getData('startDateInitial')) {
                    $buyRequest->setStartDate(Mage::getSingleton('core/session')->getData('startDateInitial'));
                    if (Mage::getSingleton('core/session')->getData('endDateInitial')) {
                        $buyRequest->setEndDate(Mage::getSingleton('core/session')->getData('endDateInitial'));
                    }
                    $resultObject = new Varien_Object();

                    Mage::dispatchEvent('init_globals_prepare_advanced', array('buy_request' => $buyRequest, 'productType' => $productType, 'product' => $product, 'result' => $resultObject));
                    if ($resultObject->getResult() != '') {
                        $msg = $resultObject->getResult();
                    }
                } else {
                    if (!ITwebexperts_Payperrentals_Helper_Data::isUsingGlobalDatesShoppingCart($product)) {
                        $msg = Mage::helper('payperrentals')->__('Please specify reservation information');
                    }else{
                        $msg = 'call_parent';
                    }
                }
            //}
        }else{
            $this->_setNonSequentialInBuyRequest($buyRequest, $useNonSequential);

            if (!$buyRequest->getEndDate()) {
                $buyRequest->setEndDate($buyRequest->getStartDate());
            }

            list($startDate, $endDate) = ITwebexperts_Payperrentals_Helper_Date::convertDatepickerToDbFormat($buyRequest->getStartDate(), $buyRequest->getEndDate());
            $buyRequest->setStartDate($startDate);
            $buyRequest->setEndDate($endDate);

            if($buyRequest->getStartDate() == $buyRequest->getEndDate()){
                if(date('H:i:s', strtotime($buyRequest->getStartDate())) == '00:00:00'){
                    $buyRequest->setEndDate(date('Y-m-d', strtotime($buyRequest->getStartDate())).' 23:59:59');
                }
            }
        }
        if(!$buyRequest->getStartDate()) {
            if (ITwebexperts_Payperrentals_Helper_Config::NoCalendarUseTodayAsStartDate()) {
                if (ITwebexperts_Payperrentals_Helper_Config::isNextHourSelection()) {
                    $buyRequest->setStartDate(date('Y-m-d', strtotime('+1 day', time())) . ' 00:00:00');
                } else {
                    $buyRequest->setStartDate(date('Y-m-d', strtotime('+0 day', time())) . ' 00:00:00');
                }
            }
        }
            if ($buyRequest->getSelectedDays()) {
                $buyRequest->setEndDate(
                    date(
                        'Y-m-d H:i:s', strtotime(
                            '+ ' . $buyRequest->getSelectedDays() . ' DAY', strtotime($buyRequest->getStartDate())
                        )
                    )
                );
            }


        return $msg;
    }

    private function _addCustomOptions(&$product,$buyRequest){
        $product->addCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION, $buyRequest->getStartDate(), $product);
        $product->addCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION, $buyRequest->getEndDate(), $product);
        $product->addCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::NON_SEQUENTIAL, $buyRequest->getNonSequential(), $product);
    }



    private function _setNonSequentialInBuyRequest(&$buyRequest, $useNonSequential){
        if ($useNonSequential) {
            $buyRequest->setNonSequential(1);
        } else {
            $buyRequest->setNonSequential(0);
        }
    }

    /**
     * Adds rental start and end dates to custom options
     *
     * @param Varien_Object $buyRequest
     * @param null $product
     * @param null $processMode
     * @param string $productType
     * @return string
     * todo refactor /investigate more to additional_options(how the translations of options would work, might need a lot of effort)
     * this function should check if we are in global dates mode, should get the existing param etc, no need to modify query string on listing.
     * This will allow so adding a product from a different block from listing to work too
     */
    public function prepareForCartAdvanced(Varien_Object $buyRequest, $product = null, $processMode = null, $productType = 'simple')
    {
        if(ITwebexperts_Payperrentals_Helper_Data::isBuyout($buyRequest)){
            $product->addCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::BUYOUT_PRICE_OPTION, true, $product);
            return 'call_parent';
        }

        if (!ITwebexperts_Payperrentals_Helper_Sso::isAllowedRenting()) {
            return Mage::helper('payperrentals')->__('You are not allowed renting. Please login on CNH');
        }

        if ($productType != 'simple' && !ITwebexperts_Payperrentals_Helper_Data::isReservationAndRental($product)) {
            return 'call_parent';
        }

        if ($buyRequest->getIsReservation() == ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_RENTAL) {
            return Mage::helper('payperrentals/membership')->addProductToQueue($product, $buyRequest, (($productType == 'grouped')?true:false));
        }

        $msg = $this->_completeBuyRequestObject($buyRequest, $product, $productType);

        if($msg){
            return $msg;
        }

        $resultObject = new Varien_Object();
        Mage::dispatchEvent('prepare_advanced_before', array('buy_request' => $buyRequest, 'product_type' => $productType, 'product' => $product, 'result' => $resultObject));
        if ($resultObject->getResult() != '') {
            return $resultObject->getResult();
        }


        $this->_addCustomOptions($product, $buyRequest);

        ITwebexperts_Payperrentals_Helper_Date::saveDatesForGlobalUse(array('start_date' => $buyRequest->getStartDate(), 'end_date' => $buyRequest->getEndDate()));

            return 'call_parent';
    }

}