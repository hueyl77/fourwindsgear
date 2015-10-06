<?php




/**
 * Class ITwebexperts_Payperrentals_AjaxController
 */
class ITwebexperts_Payperrentals_AjaxController extends Mage_Core_Controller_Front_Action
{
    /**
     * Returns the array of booked days, for the quantity and selected dates
     *
     * @return array
     */

    public function updateBookedForProductAction()
    {
        if(!$this->getRequest()->getParam('product_id')){
            $bookedHtml = array(
                    'bookedDates' => '',
                    'fixedRentalDates' => '',
                    'isDisabled' => true,
                    'isShoppingCartDates' => ITwebexperts_Payperrentals_Helper_Data::isUsingGlobalDatesShoppingCart()?true:false,
                    'partiallyBooked' => ''
                );
            $this->getResponse()->setBody(Zend_Json::encode($bookedHtml));
            return;
        }
        $product = ITwebexperts_Payperrentals_Helper_Data::initProduct($this->getRequest()->getParam('product_id'));//todo might not be necessary

        $qty = $this->getRequest()->getParam('qty')?$this->getRequest()->getParam('qty'):1;
        $normalPrice = '';
        $needsConfigure = false;
        $isConfigurable = false;
        $isDisabled = false;
        $attributes = $this->getRequest()->getParam('super_attribute')?$this->getRequest()->getParam('super_attribute'):null;
        $bundleOptions = $this->getRequest()->getParam('bundle_option')?$this->getRequest()->getParam('bundle_option'):null;
        $bundleOptionsQty1 = $this->getRequest()->getParam('bundle_option_qty1')?$this->getRequest()->getParam('bundle_option_qty1'):null;
        $bundleOptionsQty = $this->getRequest()->getParam('bundle_option_qty')?$this->getRequest()->getParam('bundle_option_qty'):null;

        $qtyArr = ITwebexperts_Payperrentals_Helper_Inventory::getQuantityArrayForProduct(
            $product, 1, $attributes, $bundleOptions, $bundleOptionsQty1, $bundleOptionsQty, true
        );

        $maxQtyArr = array();
        $bookedDates = array();
        $partiallyBookedDates = array();
        $turnoverTimeBefore = 0;
        $turnoverTimeAfter = 0;
        $maxQtyForParentProduct = 10000;
        /**
         * We get the maximum qty available at this moment for every associated product
         */
        foreach($qtyArr as $iProduct => $iQty){
            if(ITwebexperts_Payperrentals_Helper_Inventory::isAllowedOverbook($iProduct)){
                $maxQtyForChildProduct = 100000;
            }else {
            $maxQtyForChildProduct = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity($iProduct);
            }
            $qtyForParentProduct = intval($maxQtyForChildProduct / $iQty) ;
            $maxQtyArr[$iProduct] = $maxQtyForChildProduct;
            if($maxQtyForParentProduct > $qtyForParentProduct){
                $maxQtyForParentProduct = $qtyForParentProduct;
            }

            if($maxQtyForChildProduct < $iQty * $qty){
                $isDisabled = true;
                break;
            }
        }

        if(!$isDisabled){
            $resProductArrIds = ITwebexperts_Payperrentals_Helper_Inventory::getReservationProductsArrayIds(
                $product->getId(), $attributes, $bundleOptions
            );

            $turnoverArr = ITwebexperts_Payperrentals_Helper_Data::getTurnoverFromQuoteItemOrBuyRequest($product->getId(), null,null, null, 'days');
            $turnoverTimeBefore = $turnoverArr['before'];
            $turnoverTimeAfter = $turnoverArr['after'];

            foreach($resProductArrIds as $resProductId) {
                $booked = ITwebexperts_Payperrentals_Helper_Inventory::getBookedQtyForProducts($resProductId);

                //Normally if shipping method with turnovers is used global turnovers should be null
                if($turnoverTimeAfter == 0 && $turnoverTimeBefore == 0) {
                    $turnoverArr = ITwebexperts_Payperrentals_Helper_Data::getTurnoverFromQuoteItemOrBuyRequest($resProductId, null, null, null, 'days');
                    $turnoverTimeBefore = $turnoverArr['before'];
                    $turnoverTimeAfter = $turnoverArr['after'];

                }
                /**
                 * We go product by product(in the associated products) and date by date and subtract from
                 * this moment qty the booked qty for that date This is how calendar is created.
                 */
                if(isset($booked[$resProductId])) {
                    foreach ($booked[$resProductId] as $dateFormatted => $vObject) {
                      $qtyAvailForProduct = intval(
                          ($maxQtyArr[$resProductId] - $vObject->getQty()) / $qtyArr[$resProductId]
                      );
                        $isPartial = false;
                        if (date('H:i', strtotime($dateFormatted)) != '00:00') {
                                if(!isset($partiallyBookedDates[$dateFormatted . ':00'])) {
                                    $partiallyBookedDates[$dateFormatted . ':00'] = $qtyAvailForProduct;
                                }else{
                                    $partiallyBookedDates[$dateFormatted . ':00'] = min($qtyAvailForProduct, $partiallyBookedDates[$dateFormatted . ':00']);
                                }
                            $isPartial = true;
                        }

                        if(!$isPartial) {
                            if (!isset($bookedDates[$dateFormatted])) {
                                $bookedDates[$dateFormatted] = $qtyAvailForProduct;
                            } else {
                                $bookedDates[$dateFormatted] = min($bookedDates[$dateFormatted], $qtyAvailForProduct);
                            }
                        }
                    }
                }

            }
            $bookedDates = ITwebexperts_Payperrentals_Helper_Data::toFormattedBookedArray($bookedDates, false);
        }

        $typeId = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($product->getId(), 'type_id');

        if ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE) {
            reset($qtyArr);
            $normalPrice = ITwebexperts_Payperrentals_Helper_Price::getPriceListHtml(key($qtyArr));
            $isConfigurable = true;
            $blockedDates = ITwebexperts_Payperrentals_Helper_Data::getDisabledDates(key($qtyArr));
            $fixedRentalDates = ITwebexperts_Payperrentals_Helper_Data::getFixedRentalDates(key($qtyArr));
        }else{
            $blockedDates = ITwebexperts_Payperrentals_Helper_Data::getDisabledDates($product->getId());
            $fixedRentalDates = ITwebexperts_Payperrentals_Helper_Data::getFixedRentalDates($product->getId());
        }

        $blockedDates = ITwebexperts_Payperrentals_Helper_Data::toFormattedDateArray($blockedDates, false);
        $fixedRentalDates = ITwebexperts_Payperrentals_Helper_Data::toFormattedArraysOfDatesArray($fixedRentalDates, false);

        $bookedHtml = array(
                'bookedDates' => $bookedDates,
                'fixedRentalDates' => $fixedRentalDates,
                'isDisabled' => $isDisabled,
                'blockedDates' => implode(',', $blockedDates),
                'isShoppingCartDates' => ITwebexperts_Payperrentals_Helper_Data::isUsingGlobalDatesShoppingCart()?true:false,
                'isConfigurable' => $isConfigurable,
                'needsConfigure' => $needsConfigure,
                'normalPrice' => $normalPrice,
                'partiallyBooked' => $partiallyBookedDates
            );


        $bookedHtml['turnoverTimeBefore'] = $turnoverTimeBefore;
        $bookedHtml['turnoverTimeAfter'] = $turnoverTimeAfter;
        $bookedHtml['maxQty'] = $maxQtyForParentProduct;

        $disabledDaysStart = ITwebexperts_Payperrentals_Helper_Data::getDisabledDaysStart();
        $disabledDaysEnd = ITwebexperts_Payperrentals_Helper_Data::getDisabledDaysEnd();

        if(count($disabledDaysStart) > 0) {
            $bookedHtml['disabledForStartRange'] = $disabledDaysStart;
        }
        if(count($disabledDaysEnd) > 0) {
            $bookedHtml['disabledForEndRange'] = $disabledDaysEnd;
        }

        if(Mage::helper('payperrentals/config')->getFutureReservationLimit($product) > 0){
            $bookedHtml['futureDate'] = date('Y-m-d', (time() + Mage::helper('payperrentals/config')->getFutureReservationLimit($product) * 3600 * 24));
        }

        Mage::dispatchEvent('ppr_get_booked_html_for_products', array('request_params' => $this->getRequest()->getParams(), 'booked_html' => &$bookedHtml, 'product' => $product));

        $this->getResponse()->setBody(Zend_Json::encode($bookedHtml));
    }

    /**
     * Sets the initial dates for the session when global calendar is used
     */
    public function setDateInitialAction()
    {
        $paramsAll = $this->getRequest()->getPost();
        ITwebexperts_Payperrentals_Helper_Date::saveDatesForGlobalUse($paramsAll, true);
        Mage::dispatchEvent('set_date_initials', array('postData' => $paramsAll));
        $jsonReturn =  array(
        );

        $this->getResponse()->setBody(Zend_Json::encode($jsonReturn));
    }

    /**
     * @deprecated
     * This function was used to return the next available date when fixed date selection was used.
     * Is better so no start date is automatically selected
     * @param $product
     * @param $startDate
     *
     * @return array
     */

    private function _getAvailDateAndSelDays($product, $startDate){
        $selDays = false;
        $availDate = false;
        if ($this->getRequest()->getParam('selDays')) {
            $selDays = (int)$this->getRequest()->getParam('selDays') + 1;
            $availDateArr = ITwebexperts_Payperrentals_Helper_Data::getFirstAvailableDateRange($product, $startDate,
                ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                    $selDays, ITwebexperts_Payperrentals_Model_Product_Periodtype::DAYS
                )
            );
            $availDate = $availDateArr['start_date'];
        }

        return array($selDays, $availDate);
    }

    /**
     * Get price for the selected quantity and dates
     */
    public function getPriceAction()
    {
        if (!$this->getRequest()->getParam('product_id') || !$this->getRequest()->getParam('start_date')) {
            $jsonReturn = array(
                'amount' => -1,
                'onclick' => '',
                'needsConfigure' => true,
                'formatAmount' => -1
            );

            $this->getResponse()->setBody(Zend_Json::encode($jsonReturn));
            return;
        }


        $productId = $this->getRequest()->getParam('product_id');
        $product = Mage::getModel('catalog/product')->load($productId);
        $qty = urldecode($this->getRequest()->getParam('qty'));

        list($startDate, $endDate) = ITwebexperts_Payperrentals_Helper_Date::saveDatesForGlobalUse($this->getRequest()->getPost());

        if ($this->getRequest()->getParam('is_fixed_date')) {
            //get all fixed dates id with names and hours in a html with rectangle classes.. and disable rent button... have price as an attribute
            //add onclick event and a hidden field which updates...also enable button and start end date to get the real price
            $startDate = date('Y-m-d', strtotime($startDate));
            $endDate = date('Y-m-d', strtotime($endDate));
            $fixedDatesArray = ITwebexperts_Payperrentals_Helper_Data::getFixedRentalDates($product);
            $fixedDatesDropdown = '';
            if (count($fixedDatesArray)) {
                $fixedDatesDropdown .= '<ul class="fixed_array">';
            }
            $hasAvailability = false;
            foreach ($fixedDatesArray as $fixedDate) {
                if (date('Y-m-d', strtotime($fixedDate['start_date'])) == date('Y-m-d', strtotime($startDate))) {
                    if(Mage::helper('payperrentals/inventory')->isAvailable($productId, $fixedDate['start_date'], $fixedDate['end_date'], $qty)) {
                        //if (date('Y-m-d', strtotime($fixedDate['start_date'])) == date('Y-m-d', strtotime($fixedDate['end_date']))) {
                         //   $fixedDatesDropdown .= '<li idname="' . $fixedDate['id'] . '">' . date('H:i', strtotime($fixedDate['start_date'])) /*. '   ' . $fixedDate['name']*/ . '</li>';
                        //} else {
                            $fixedDatesDropdown .= '<li idname="' . $fixedDate['id'] . '">' . Mage::helper('payperrentals')->__('Start: ') . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($fixedDate['start_date'], false, false) . ' &nbsp;&nbsp;&nbsp;  ' . Mage::helper('payperrentals')->__('End: ') . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($fixedDate['end_date'], false, false) /*. '   ' . $fixedDate['name']*/ . '</li>';
                        //}
                        $hasAvailability = true;
                    }
                }
            }
            if (count($fixedDatesArray)) {
                $fixedDatesDropdown .= '</ul>';
            }
            if(!$hasAvailability){
                $fixedDatesDropdown = Mage::helper('payperrentals')->__('Sorry, there is no availability left for this option');
            }
            $jsonReturn = array(
                'amount' => 0,
                'onclick' => '',
                'fixedDates' => $fixedDatesDropdown,
                'needsConfigure' => false,
                'formatAmount' => -1
            );
            $this->getResponse()->setBody(Zend_Json::encode($jsonReturn));
            return;
        }

        $attributes = $this->getRequest()->getParam('super_attribute') ? $this->getRequest()->getParam('super_attribute') : null;
        $bundleOptions = $this->getRequest()->getParam('bundle_option') ? $this->getRequest()->getParam('bundle_option') : null;
        $bundleOptionsQty1 = $this->getRequest()->getParam('bundle_option_qty1') ? $this->getRequest()->getParam('bundle_option_qty1') : null;
        $bundleOptionsQty = $this->getRequest()->getParam('bundle_option_qty') ? $this->getRequest()->getParam('bundle_option_qty') : null;
        $onClick = '';
        $priceAmount = ITwebexperts_Payperrentals_Helper_Price::getPriceForAnyProductType($product, $attributes, $bundleOptions, $bundleOptionsQty1, $bundleOptionsQty, $startDate, $endDate, $qty, $onClick);

        if (Mage::helper('payperrentals/config')->useListButtons() || ITwebexperts_Payperrentals_Helper_Data::isUsingGlobalDates($product)) {
            ITwebexperts_Payperrentals_Helper_Date::saveDatesForGlobalUse($this->getRequest()->getPost());
        }

        $jsonReturn = array(
            'amount' => $priceAmount,
            'onclick' => $onClick,
            'needsConfigure' => false,
            'formatAmount' => ($priceAmount != -1) ? Mage::helper('core')->currency($priceAmount) : -1
        );

        $this->getResponse()->setBody(Zend_Json::encode($jsonReturn));
    }

    /**
     *
     */
    public function updateSortableAction()
    {

        $item = $this->getRequest()->getParam('item');
        $customer_id = Mage::getSingleton('customer/session')->getCustomerId();
        $store_id = Mage::app()->getStore()->getId();
        $prodList = array('-1');
        if ($item) {
            foreach ($item as $position => $id) {
                Mage::getResourceSingleton('payperrentals/rentalqueue')
                    ->updateSortOrder($position, $customer_id, $store_id, $id);
                $prodList[] = $id;
            }
        }

        Mage::getResourceSingleton('payperrentals/rentalqueue')
            ->deleteByNotInProductList($customer_id, $store_id, $prodList);

        $html = array(
            'success' => ''
        );

        $this->getResponse()->setBody(Zend_Json::encode($html));
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


    public function checkPriceForCartAction()
    {
        /*This should not be used*/
        $_response = array();
                    /** @var $quoteItem Mage_Sales_Model_Quote_Item */
        $_response['noAllDate'] = false;
        $this->getResponse()->setBody(Zend_Json::encode($_response));
    }
}
