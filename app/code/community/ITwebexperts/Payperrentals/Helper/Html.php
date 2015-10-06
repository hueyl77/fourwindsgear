<?php


class ITwebexperts_Payperrentals_Helper_Html extends Mage_Core_Helper_Abstract
{

    public static function completeListingAndProductInfoWithExtraButtons($product){
        if (Mage::getSingleton('core/session')->getData('startDateInitial') && !Mage::helper('payperrentals/config')->useListButtons(
            )
        ) {
            $htmlPrice = '';

            if(Mage::helper('payperrentals/config')->keepListingPriceAfterDatesSelection()){
                $htmlPrice = ITwebexperts_Payperrentals_Helper_Price::getPriceListHtml($product, Mage::getStoreConfig(
                    ITwebexperts_Payperrentals_Helper_Config::XML_PATH_PRICING_ON_LISTING
                ));
            }else{
                $priceVal = ITwebexperts_Payperrentals_Helper_Price::calculatePrice($product, Mage::getSingleton('core/session')->getData('startDateInitial'), Mage::getSingleton('core/session')->getData('endDateInitial'), 1, ITwebexperts_Payperrentals_Helper_Data::getCustomerGroup());
                if(!ITwebexperts_Payperrentals_Helper_Data::isAddToQueue($product) && !ITwebexperts_Payperrentals_Helper_Data::isAddToQueueGrouped($product)) {
                    $htmlPrice = (($product->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE)
                        ? Mage::helper('payperrentals')->__('Not available') : Mage::helper('payperrentals')->__(''));
                }
                if ($priceVal > 0) {
                    $htmlPrice = '<div class="price-box"><span class="price">Price: ' . Mage::helper('core')->currency($priceVal) . '</span></div>';
                }
                $buyoutHtml = '';
                $isRentalBuyout = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($product->getId(), 'payperrentals_enable_buyout');
                if ($isRentalBuyout) {
                    $buyoutPrice = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($product->getId(), 'payperrentals_buyoutprice');;
                    $buyoutHtml = Mage::helper('payperrentals')->__('Buyout: ') . Mage::helper('core')->currency(
                            $buyoutPrice, true, false
                        );
                }
                $htmlPrice .= $buyoutHtml;
            }

        } else {

            $htmlPrice = ITwebexperts_Payperrentals_Helper_Price::getPriceListHtml($product, Mage::getStoreConfig(
                    ITwebexperts_Payperrentals_Helper_Config::XML_PATH_PRICING_ON_LISTING
                ));
            if($product->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {

                //use list buttons
                if (Mage::getSingleton('core/session')->getData('startDateInitial')
                    && !Mage::registry(
                        'current_product'
                    )
                ) {
                    $selectedArray = Mage::helper('payperrentals/config')->getFixedSelection();
                    $notAvailable = false;
                    foreach ($selectedArray as $iDay) {
                        $startDate = Mage::getSingleton('core/session')->getData('startDateInitial');
                        $endDate = date('Y-m-d H:i:s', strtotime('+ '.$iDay. ' DAY', strtotime($startDate)));
                        $priceVal = ITwebexperts_Payperrentals_Helper_Price::calculatePrice($product, $startDate, $endDate, 1, ITwebexperts_Payperrentals_Helper_Data::getCustomerGroup());

                        if ($priceVal > 0) {
                            $selectedDayLink = Mage::helper('checkout/cart')->getAddUrl(
                                $product, array('_query' => array('options' => array('selected_days' => $iDay),
                                                                  'selected_days' => $iDay))
                            );
                            $htmlPrice
                                .=
                                '<input type="hidden" class="ppr_attr_sel_days" href="' . $selectedDayLink . '" value="'
                                . Mage::helper('payperrentals')->__('Rent %s days at %s', $iDay,strip_tags(Mage::helper('core')->currency($priceVal))) . '" />';
                        } else {
                            $notAvailable = true;
                            break;
                        }

                    }
                    if($notAvailable){
                        if(!ITwebexperts_Payperrentals_Helper_Data::isAddToQueue($product) && !ITwebexperts_Payperrentals_Helper_Data::isAddToQueueGrouped($product)) {
                            $htmlPrice = (($product->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE)
                                ? Mage::helper('payperrentals')->__('Not available') : Mage::helper('payperrentals')->__(''));
                        }else{
                            $htmlPrice = '';
                        }
                    }
                }
            }
        }
        $configHelper = Mage::helper('payperrentals/config');
        $inventoryHelper = Mage::helper('payperrentals/inventory');

        if($configHelper->showAvailabilityOnProductListing()) {
            if($product->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
                $isAvailable = $inventoryHelper->isAvailable(
                    $product->getId(), Mage::getSingleton('core/session')->getData('startDateInitial'), Mage::getSingleton('core/session')->getData('endDateInitial'), 1);
                if ($isAvailable) {
                    $htmlPrice .= '';//Mage::helper('payperrentals')->__('Available for selected dates');
                }else{
                    $htmlPrice .= Mage::helper('payperrentals')->__('Not available for selected dates');
                }

            }
        }

        if(ITwebexperts_Payperrentals_Helper_Data::isReservation($product) && ITwebexperts_Payperrentals_Helper_Data::isReservationType($product)){
            if(Mage::getSingleton('core/session')->getData('startDateInitial') && Mage::getSingleton('core/session')->getData('endDateInitial')) {
                $getAddUrl = Mage::helper('checkout/cart')->getAddUrl(
                    $product, array('_query' => array('options'        => array('start_date' => Mage::getSingleton('core/session')->getData('startDateInitial'), 'end_date' => Mage::getSingleton('core/session')->getData('endDateInitial')),
                                                      'start_date' => Mage::getSingleton('core/session')->getData('startDateInitial'),
                                                      'end_date' => Mage::getSingleton('core/session')->getData('endDateInitial')))
                );
                $htmlPrice
                    .= '<input type="hidden" href="'.$getAddUrl.'" class="ppr_attr_butname_global" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . Mage::helper('payperrentals')->__(
                        'Rent'
                    ) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" />';
            }else if(ITwebexperts_Payperrentals_Helper_Data::isUsingGlobalDates()) {
                $getAddUrl = Mage::helper('checkout/cart')->getAddUrl(
                    $product, array('_query' => array('options'        => array('start_date' => '', 'end_date' => ''),
                        'start_date' => '',
                        'end_date' => ''))
                );
                $htmlPrice
                    .= '<input type="hidden" href="'.$getAddUrl.'" class="ppr_attr_butname_global" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . Mage::helper('payperrentals')->__(
                        'Rent'
                    ) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" />';
            }else{
                $htmlPrice
                    .= '<input type="hidden" class="ppr_attr_butname" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . Mage::helper('payperrentals')->__(
                        'Rent'
                    ) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" />';
            }
            if (Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_ALLOW_LISTING_QTY) && Mage::app()->getRequest()->getRouteName() != 'wishlist'){
                $htmlPrice .= '<input type="hidden" class="ppr_attr_qty" prid="'.$product->getId().'"  value="'.Mage::helper('payperrentals')->__('Qty: ').'" />';
            }
        }
        if(Mage::helper('payperrentals/config')->isBuyout($product)){
            $buyoutLink = Mage::helper('checkout/cart')->getAddUrl(
                $product, array('_query' => array('options'        => array('buyout' => 'true'),
                                                  'buyout' => 'true'))
            );
            $htmlPrice .= '<input type="hidden" class="ppr_attr_buyout" href="'.$buyoutLink.'" value="'.Mage::helper('payperrentals')->__('Buy Now').'" />';
        }
        $rentalLink = Mage::helper('checkout/cart')->getAddUrl(
            $product, array('_query' => array('options'        => array('one_option' => 'no_value'),
                                              'is_reservation' => ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_RENTAL))
        );

        if(ITwebexperts_Payperrentals_Helper_Data::isAddToQueueGrouped($product)){
            $htmlPrice .= '<input type="hidden" class="ppr_attr_queue" href="'.$rentalLink.'" value="'.Mage::helper('payperrentals')->__('Add All To Queue').'" />';
        }elseif(ITwebexperts_Payperrentals_Helper_Data::isAddToQueue($product)){
            /** used to disable and gray out add to queue button if not enabled for membership */
            $isalloweditem = '';
            $disabled_queue_class = '';
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $isalloweditem = Mage::helper('payperrentals/membership')->hasMembership($product->getId());
                if ($isalloweditem == false) {
                    $disabled_queue_class = ' notallowed';
                }
            }
            $htmlPrice .= '<input type="hidden" class="ppr_attr_queue' . $disabled_queue_class . '" href="'.$rentalLink.'" value="'.Mage::helper('payperrentals')->__('Add To Queue').'" />';
        }

        return $htmlPrice;
    }

    /**
     * Translate a result array into a HTML table
     *
     * @param       array  $array      The result (numericaly keyed, associative inner) array. $data[0]['Heading'] = Value;$data[1]['Heading'] = Value
     * @param       bool   $recursive  Recursively generate tables for multi-dimensional arrays
     * @param       string $null       String to output for blank cells
     */
    public static function array2table($array, $recursive = false, $null = '&nbsp;')
    {
        // Sanity check
        if (empty($array) || !is_array($array)) {
            return false;
        }

        if (!isset($array[0]) || !is_array($array[0])) {
            $array = array($array);
        }

        // Start the table
        $table = "<table>\n";

        // The header
        $table .= "\t<tr>";
        // Take the keys from the first row as the headings
        foreach (array_keys($array[0]) as $heading) {
            $table .= '<th>' . $heading . '</th>';
        }
        $table .= "</tr>\n";

        // The body
        foreach ($array as $row) {
            $table .= "\t<tr>" ;
            foreach ($row as $cell) {
                $table .= '<td>';

                // Cast objects
                if (is_object($cell)) { $cell = (array) $cell; }

                if ($recursive === true && is_array($cell) && !empty($cell)) {
                    // Recursive mode
                    $table .= "\n" . array2table($cell, true, true) . "\n";
                } else {
                    $table .= (strlen($cell) > 0) ?
                        htmlspecialchars((string) $cell) :
                        $null;
                }

                $table .= '</td>';
            }

            $table .= "</tr>\n";
        }

        $table .= '</table>';
        return $table;
    }

    private static function showTextForPeriodType($periodNumber, $hidePeriodNumbers, $type)
    {
        if ($periodNumber == 1 || $hidePeriodNumbers) {
            $text = (!$hidePeriodNumbers ? ($periodNumber . ' ') : '') . Mage::helper('payperrentals')->__(
                    substr($type, 0, strlen($type) - 1)
                );
        } else {
            $text = $periodNumber . ' ' . Mage::helper('payperrentals')->__($type);
        }
        return $text;
    }

    /**
     * Function to return the text for the periodNumber - periodType pairs
     *
     * @param $periodNumber
     * @param $periodType
     *
     * @return string
     */
    public static function getTextForType($periodNumber, $periodType, $hidePeriodNumbers = false)
    {
        $text = '';
        /** @var $helperConfig ITwebexperts_Payperrentals_Helper_Config */
        $helperConfig = Mage::helper('payperrentals/config');
        if ($hidePeriodNumbers == false) {
            $hidePeriodNumbers = $helperConfig->hideTimePeriodNumbers();
        }
        switch ($periodType) {
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::MINUTES:
                $text = self::showTextForPeriodType($periodNumber, $hidePeriodNumbers, 'Minutes');
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::HOURS:
                $text = self::showTextForPeriodType($periodNumber, $hidePeriodNumbers, 'Hours');
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::DAYS:
                $text = self::showTextForPeriodType($periodNumber, $hidePeriodNumbers, 'Days');
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::WEEKS:
                $text = self::showTextForPeriodType($periodNumber, $hidePeriodNumbers, 'Weeks');
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::MONTHS:
                $text = self::showTextForPeriodType($periodNumber, $hidePeriodNumbers, 'Months');
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::YEARS:
                $text = self::showTextForPeriodType($periodNumber, $hidePeriodNumbers, 'Years');
                break;
        }

        return $text;
    }

    public static function showSingleDates($order, $_isSingleLine = true)
    {
        $isSingle = ITwebexperts_Payperrentals_Helper_Data::isSingleOrder($order);
        if ($isSingle['bool']) {
            if (!Mage::helper('payperrentals/config')->isNonSequentialSelect(Mage::app()->getStore()->getId())) {
                if ($isSingle['start_date'] != $isSingle['end_date'] && $isSingle['start_date'] != '') {
                    $_space = (!$_isSingleLine) ? '<br/>' : ' ';
                    return '<span style="font-weight:bold;font-size:12px">' . Mage::helper('payperrentals')->__('Start Date: ') . '</span><span style="font-weight:normal;font-size:12px;">'
                    . ITwebexperts_Payperrentals_Helper_Date::formatDbDate(
                        $isSingle['start_date'], false
                    ) . '</span>' . $_space . $_space . '<span style="font-weight:bold;font-size:12px">' . Mage::helper('payperrentals')->__('End Date: ')
                    . '</span><span style="font-weight:normal;font-size:12px;">' . ITwebexperts_Payperrentals_Helper_Date::formatDbDate(
                        $isSingle['end_date'], false
                    ) . '</span><br /><br />';
                } else {
                    if ($isSingle['start_date'] != '') {
                        return '<span style="font-weight:bold;font-size:12px;">' . Mage::helper('payperrentals')->__('Start Date:') . '</span><span style="font-weight:normal;font-size:12px;">'
                        . ITwebexperts_Payperrentals_Helper_Date::formatDbDate(
                            $isSingle['start_date'], false
                        ) . '</span>';
                    }
                }
            } else {
                if ($isSingle['start_date'] != '') {
                    return '<strong>' . Mage::helper('payperrentals')->__('Dates:') . '</strong><span>'
                    . ITwebexperts_Payperrentals_Helper_Date::localiseNonsequentialBuyRequest(
                        $isSingle['start_date'], true
                    ) . '</span>';
                }
            }
        }
        return '';
    }

    public static function updateReservationError($message)
    {
        $controllerName = Mage::app()->getRequest()->getControllerName();
        $actionName = Mage::app()->getRequest()->getActionName();
        if (($controllerName == 'cart') && ($actionName == 'index')) {
            Mage::getSingleton('core/session')->addError($message);
        } else {
            Mage::throwException($message);
        }
    }

    public static function showItemOptions($_item)
    {
        $return = '';
        $buyRequest = $_item->getBuyRequest();

        if ($_item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE
            || $_item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE
            || $_item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE
        ) {
            $return .= '<div> <strong>' . Mage::helper('payperrentals')->__('Start Date:') . '</strong>';
            $return .= $buyRequest->getStartDate();
            $return .= '</div>';
            $return .= '<div> <strong>' . Mage::helper('payperrentals')->__('End Date:') . '</strong>';
            $return .= $buyRequest->getEndDate();
            $return .= '</div>';
            $attributes = array();
            $superAttributes = array();
            if ($buyRequest->getSuperAttribute()) {
                $superAttributes = $buyRequest->getSuperAttribute();
            }

            foreach ($superAttributes as $attributeId => $attributeValue) {
                $collection = Mage::getResourceModel('eav/entity_attribute_collection')
                    ->addFieldToFilter('attribute_id', $attributeId);
                $product = Mage::getModel('catalog/product');

                $_attribute = $collection->getFirstItem()->setEntity($product->getResource());
                $return .= '<b>' . $_attribute->getFrontendLabel() . ':</b> ';
                $attribute_options = $_attribute->getSource()->getAllOptions(false);
                foreach ($attribute_options as $attr) {
                    if ($attr['value'] == $attributeValue) {
                        $return .= $attr['label'] . '<br/>';
                        break;
                    }

                }
            }
            $resultObject = new Varien_Object();
            $resultObject->setReturn($return);
            Mage::dispatchEvent('options_undername', array('item' => $_item, 'result' => $resultObject));
            $return = $resultObject->getReturn();
        }
        return $return;
    }

    public static function getGridNames()
    {
        $return = '';
        $nonSequential = Mage::helper('payperrentals/config')->isNonSequentialSelect(Mage::app()->getStore()->getId());

        if ($nonSequential) {
            $return .= '<th class="no-link">' . Mage::helper('payperrentals')->__('Start') . '</th>';
        } else {
            $return .= '<th class="no-link">' . Mage::helper('payperrentals')->__('Start') . '</th>';
            $return .= '<th class="no-link">' . Mage::helper('payperrentals')->__('End') . '</th>';
        }

        $resultObject = new Varien_Object();
        //$resultObject->setReturn($return);
        Mage::dispatchEvent('options_gridnames', array('result' => $resultObject));
        $return .= $resultObject->getReturn();
        return $return;

    }

    public static function getGridCols()
    {
        $return = '';
        $resultObject = new Varien_Object();
        $resultObject->setReturn($return);
        Mage::dispatchEvent('options_gridcols', array('result' => $resultObject));
        $return = $resultObject->getReturn();
        return $return;
    }

    public static function getGridButtons($class)
    {
        $return = '';
        $resultObject = new Varien_Object();
        $resultObject->setReturn($return);
        Mage::dispatchEvent('options_gridbuttons', array('result' => $resultObject, 'class' => $class));
        $return = $resultObject->getReturn();
        return $return;
    }

    public static function showGridColumns($_item)
    {
        $return = '';
        $nonSequential = Mage::helper('payperrentals/config')->isNonSequentialSelect(Mage::app()->getStore()->getId());
        if (ITwebexperts_Payperrentals_Helper_Data::isReservationType($_item->getProductId())) {
            if ($_item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE
                || $_item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE
                || $_item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE
            ) {
                $buyRequest = $_item->getBuyRequest();
                $_showTime = (bool)Mage::getResourceModel('catalog/product')
                    ->getAttributeRawValue(
                        $_item->getProductId(),
                        'payperrentals_use_times',
                        $_item->getStoreId()
                    );
                if ($nonSequential) {
                    $stDate = ITwebexperts_Payperrentals_Helper_Date::localiseNonsequentialBuyRequest(
                        $buyRequest->getStartDate(), $_showTime
                    );
                } else {
                    if($buyRequest->getStartTime()){
                        $buyStartDate = str_replace('00:00:00', $buyRequest->getStartTime(), $buyRequest->getStartDate());
                        $buyEndDate = str_replace('23:59:59', $buyRequest->getEndTime(), $buyRequest->getEndDate());
                    }else{
                        $buyStartDate = $buyRequest->getStartDate();
                        $buyEndDate = $buyRequest->getEndDate();
                    }
                    $stDate = ITwebexperts_Payperrentals_Helper_Date::formatDbDate(
                        $buyStartDate, !$_showTime
                    );
                    $enDate = ITwebexperts_Payperrentals_Helper_Date::formatDbDate(
                        $buyEndDate, !$_showTime
                    );
                }
                if ($nonSequential) {
                    $return .= '<td class="">' . $stDate . '</td>';
                } else {
                    $return .= '<td class="">' . $stDate . '</td>';
                    $return .= '<td class="">' . $enDate . '</td>';
                }

                $resultObject = new Varien_Object();
                //$resultObject->setReturn($return);
                Mage::dispatchEvent('options_grid', array('item' => $_item, 'result' => $resultObject));
                $return .= $resultObject->getReturn();
            }
        } else {
            if ($nonSequential) {
                $return .= '<td class="">' . '' . '</td>';
            } else {
                $return .= '<td class="">' . '' . '</td>';
                $return .= '<td class="">' . '' . '</td>';
            }
        }

        return $return;

    }

    public static function getFooterGridColspan()
    {
        $colspan = 5;
        if (Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_STARTEND_SHOW_AS_COLUMNS) == 0) {
            $colspan = 3;
        }
        return $colspan;
    }

    public static function getCalendarPopupImage(){
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'adminhtml/default/default/images/grid-cal.gif';
    }

    public static function getLocaleCodeForHtml(){
        return str_replace('_','-',Mage::app()->getLocale()->getLocaleCode());
    }

    public static function getDatepickerLocalePath(){
        $localeCode = self::getLocaleCodeForHtml();
        $file = Mage::getBaseDir() .'/js/itwebexperts_payperrentals/jquery/i18n/datepicker-'.$localeCode.'.js';
        if(is_file($file)){
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS) .'itwebexperts_payperrentals/jquery/i18n/datepicker-'.$localeCode.'.js';
        }else{
            $localeCode = substr($localeCode, 0, strpos($localeCode,'-'));
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS) .'itwebexperts_payperrentals/jquery/i18n/datepicker-'.$localeCode.'.js';
        }
    }

    public static function getDatetimepickerLocalePath(){
        $localeCode = self::getLocaleCodeForHtml();
        $file = $file = Mage::getBaseDir() .'/js/itwebexperts_payperrentals/jquery/timepicker/i18n/jquery-ui-timepicker-'.$localeCode.'.js';
        if(is_file($file)){
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS) .'itwebexperts_payperrentals/jquery/timepicker/i18n/jquery-ui-timepicker-'.$localeCode.'.js';
        }else{
            $localeCode = substr($localeCode, 0, strpos($localeCode,'-'));
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS) .'itwebexperts_payperrentals/jquery/timepicker/i18n/jquery-ui-timepicker-'.$localeCode.'.js';
        }
    }
}