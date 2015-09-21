<?php

/**
 * Class ITwebexperts_Payperrentals_Helper_Price
 */
class ITwebexperts_Payperrentals_Helper_Price extends Mage_Core_Helper_Abstract
{

    /**
     *
     */
    const ZERO_DATETIME = '0000-00-00 00:00:00';
    const DAMAGE_WAIVER_OPTION = 'damage_waiver';
    const DAMAGE_WAIVER_OPTION_PRICE = 'damage_waiver_price';

    /**
     * @return ITwebexperts_Payperrentals_Helper_Price
     */
    protected static function _getHelper()
    {
        return Mage::helper('payperrentals/price');
    }

    /**
     * @param $product
     * @param $nr
     *
     * @return array
     */
    public static function getPriceList(
        Mage_Catalog_Model_Product $product, $nr = -1, $startDate = null, $endDate = null
    )
    {
        // customer group and store id
        $customerGroup = ITwebexperts_Payperrentals_Helper_Data::getCustomerGroup();
        if (Mage::app()->getStore()->isAdmin()) {
            $storeID = Mage::getSingleton('adminhtml/session_quote')->getStoreId();
        } else {
            $storeID = Mage::app()->getStore()->getId();
        }
        // init prices array
        $priceList = array();
        // Get prices collection
        $priceCollection = Mage::getModel('payperrentals/reservationprices')
            ->getCollection()
            ->addEntityStoreFilter($product->getId(), $storeID)
            //->addOrderFilter('ptype ASC')
            ->addOrderFilter('id ASC');
        $priceCollection->getSelect()->joinLeft(
            array('pricedates' => Mage::getSingleton('core/resource')->getTableName(
                'payperrentals/reservationpricesdates'
            )), 'main_table.reservationpricesdates_id=pricedates.id', array('description')
        );
        // flags
        $k = 0;
        // iterate
        foreach ($priceCollection AS $item) {
            if ($k >= $nr && $nr > -1) {
                break;
            }
            // continue flags
            $continueC = true;

            // customer group validation
            if ($item->getCustomersGroup() == '-1' || $item->getCustomersGroup() == $customerGroup) {
                $continueC = false;
            }

            // Continue if required
            if ($continueC) {
                continue;
            }
            // price item
            $priceList[] = array(
                'price' => floatval($item->getPrice()),
                'qty_start' => (int)$item->getQtyStart(),
                'qty_end' => (int)$item->getQtyEnd(),
                'period_type' => $item->getPtype(),
                'number_of' => $item->getNumberof(),
                'period_sec_value' => ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                    $item->getNumberof(), $item->getPtype()
                ),
                'priceadditional' => floatval($item->getPriceadditional()),
                'period_type_additional' => $item->getPtypeadditional(),
                'pricedate_description' => $item->getDescription()
            );
            $k++;
        }

        if (Mage::helper('payperrentals/config')->getSortPriceListConfig(Mage::app()->getStore()->getId())) {
            $sortType = Mage::helper('payperrentals/config')->getSortPriceListType(Mage::app()->getStore()->getId());
            if ($sortType == 1) {
                usort($priceList, array(__CLASS__, 'priceMultiSortAsc'));
            } else {
                usort($priceList, array(__CLASS__, 'priceMultiSortDesc'));
            }
        }

        // return
        return $priceList;
    }


    /**
     * @param Mage_Catalog_Model_Product $product
     * @param                            $nr
     * @param bool $simple
     *
     * @return string
     */
    public static function getPriceListHtml($product, $nr = -1, $simple = false, $startDate = null, $endDate = null)
    {
        // init output
        /** product can be not Mage_Catalog_Model_Product class if used custom extension overwritted this class*/
        $html = '';
        if (is_numeric($product)) {
            $product = Mage::getModel('catalog/product')->load($product);
        }
        if (!is_object($product)) {
            return $html;
        }
        if (is_object($product)) {
            if ($product->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_GROUPED
            ) { // Gouped
                $html .= self::_getGroupedPriceListHtml($product, $nr, $simple, $startDate, $endDate);
            } elseif ($product->getTypeId() != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE
                || $product->getBundlePricingtype()
                == ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype::PRICING_BUNDLE_FORALL
            ) { // Default
                $html .= self::_getDefaultPriceListHtml($product, $nr, $simple, $startDate, $endDate);
            } else { // Bundle
                $html .= self::_getDefaultPriceListHtml($product, $nr, $simple, $startDate, $endDate);
            }
        }
        // return output
        return $html;
    }


    /**
     * @param Mage_Catalog_Model_Product $product
     * @param                            $nr
     * @param bool $simple
     *
     * @return string
     */
    protected static function _getBundlePriceListHtml(Mage_Catalog_Model_Product $product, $nr = -1, $simple = false)
    {
        $html = '';

        return $html;
    }

    /**
     * uasort method for sorting price asc
     *
     * @param $firstPeriod
     * @param $secondPeriod
     *
     * @return int
     */
    private static function priceMultiSortAsc($firstPeriod, $secondPeriod)
    {
        if (!array_key_exists('period_sec_value', $firstPeriod)
            || !array_key_exists(
                'period_sec_value', $secondPeriod
            )
        ) {
            return 0;
        }
        if ($firstPeriod['period_sec_value'] == $secondPeriod['period_sec_value']) {
            return 0;
        }
        return ($firstPeriod['period_sec_value'] > $secondPeriod['period_sec_value']) ? 1 : -1;
    }

    /**
     * uasort method for sorting price desc
     *
     * @param $firstPeriod
     * @param $secondPeriod
     *
     * @return int
     */
    private static function priceMultiSortDesc($firstPeriod, $secondPeriod)
    {
        if (!array_key_exists('period_sec_value', $firstPeriod)
            || !array_key_exists(
                'period_sec_value', $secondPeriod
            )
        ) {
            return 0;
        }
        if ($firstPeriod['period_sec_value'] == $secondPeriod['period_sec_value']) {
            return 0;
        }
        return ($firstPeriod['period_sec_value'] < $secondPeriod['period_sec_value']) ? 1 : -1;
    }

    private static function _printAvailableDate($product){
        $availableDate = null;
        if($product->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
            $range = ITwebexperts_Payperrentals_Helper_Data::getFirstAvailableDateRange($product);
        }else{
            $bundleDefaultSelections = ITwebexperts_Payperrentals_Helper_Data::getDefaultSelectionsForBundle($product);
            $minimumDate = null;
            foreach($bundleDefaultSelections as $productId){
                $newRange = ITwebexperts_Payperrentals_Helper_Data::getFirstAvailableDateRange($productId);
                if(is_null($minimumDate) || strtotime($minimumDate) < $newRange['start_date']){
                    $minimumDate = $newRange['start_date'];
                    $range = $newRange;
                }
            }
        }
        if(isset($range)) {
            $availableDate = Mage::helper('payperrentals')->__('Next Available Date: ') . ITwebexperts_Payperrentals_Helper_Date::formatDbDate($range['start_date']);
        }
        return $availableDate;
    }

    /**
     * HTML Price list for rental products. Used on product listing, product view, and admin order creator
     *
     * @param Mage_Catalog_Model_Product $product
     * @param                            $nr
     * @param bool $simple
     *
     * @return string
     */
    protected static function _getDefaultPriceListHtml(
        Mage_Catalog_Model_Product $product, $nr = -1, $simple = false, $startDate = null, $endDate = null
    )
    {
        if(Mage::app()->getStore()->isAdmin()){
            $nr = -1;
        }
        $buyoutHtml = '';
        $priceList = self::getPriceList($product, $nr, $startDate, $endDate);
        $isRentalBuyout = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($product->getId(), 'payperrentals_enable_buyout');
        if ($isRentalBuyout) {
            $buyoutPrice = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($product->getId(), 'payperrentals_buyoutprice');;
            $buyoutHtml = Mage::helper('payperrentals')->__('Buyout: ') . Mage::helper('core')->currency(
                    $buyoutPrice, true, false
                );
        }

        if(!Mage::registry('current_product') && ITwebexperts_Payperrentals_Helper_Config::showNextAvailableDateOnListing()){
            $availableDate = self::_printAvailableDate($product);
        }elseif(ITwebexperts_Payperrentals_Helper_Config::showNextAvailableDateOnView() && Mage::registry('current_product')){
            $availableDate = self::_printAvailableDate($product);
        }

        $html = '';
        $taxHelper = Mage::helper('tax');
        foreach ($priceList AS $price) {
            if ($price['priceadditional'] != 0) {
                $additionalPrice = Mage::helper('core')->currency(
                    Mage::helper("tax")->getPrice($product, $price['priceadditional'])
                );
                $additionalTimePeriod = ITwebexperts_Payperrentals_Helper_Html::getTextForType(
                    1, $price['period_type_additional'], true
                );
                $priceAdditionalHtml = ' + ' . $additionalPrice . '/' . $additionalTimePeriod;
            } else {
                $priceAdditionalHtml = null;
            }

            $normalPriceNotTax = Mage::helper("tax")->getPrice($product, $price['price']);
            $specialPriceNotTax = (Mage::getModel('catalogrule/rule')->calcProductPriceRule($product, $normalPriceNotTax));
            $normalPriceWithTax = Mage::helper("tax")->getPrice($product, $price['price'], true);
            $specialPriceWithTax = (Mage::getModel('catalogrule/rule')->calcProductPriceRule($product, $normalPriceWithTax));
            if(!$specialPriceNotTax || $normalPriceNotTax == $specialPriceNotTax) {
                $priceVal = Mage::helper('core')->currency($normalPriceNotTax);
                $priceValInclTax = Mage::helper('core')->currency(
                    $normalPriceWithTax
                );

            }else{
                $priceVal = '<span style="text-decoration: line-through;padding-right: 5px;">'.Mage::helper('core')->currency($normalPriceNotTax).'</span>'. Mage::helper('core')->currency($specialPriceNotTax);
                $priceValInclTax = '<span style="text-decoration: line-through;padding-right: 5px;">'.Mage::helper('core')->currency($normalPriceWithTax).'</span>'. Mage::helper('core')->currency(
                        $specialPriceWithTax
                    );

            }

            $qtyText = '';
            if ($price['qty_start']) {
                $qtyText .= ' ' . Mage::helper('payperrentals')->__('if quantity is bigger than') . ' '
                    . $price['qty_start'];
            }
            if ($price['qty_end']) {
                if ($price['qty_end']) {
                    $qtyText .= ' ' . Mage::helper('payperrentals')->__('and') . ' ';
                }
                $qtyText .= ' ' . Mage::helper('payperrentals')->__('if quantity is lower than') . ' '
                    . $price['qty_end'];
            }
            $typeText = ITwebexperts_Payperrentals_Helper_Html::getTextForType(
                $price['number_of'], $price['period_type']
            );
            $pricedate_description = "";
            if ($price['pricedate_description'] != null) {
                $pricedate_description = $price['pricedate_description'] . ": ";
            }
            if ($taxHelper->displayBothPrices()) {
                $html
                    .= $pricedate_description . $typeText . ' ' . Mage::helper('payperrentals')->__('Price Excl. Tax:')
                    . ' ' . $priceVal . $qtyText
                    . '&nbsp;&nbsp;<br/>';
                $html
                    .= $typeText . ' ' . Mage::helper('payperrentals')->__('Price Incl. tax:') . ' ' . $priceValInclTax
                    . $qtyText
                    . '&nbsp;&nbsp;<br/>';
            } else {
                $html .= $pricedate_description . $typeText . ':' . ' ' . $priceVal . $priceAdditionalHtml . $qtyText
                    . '&nbsp;&nbsp;<br/>';
            }
        }
        if ($nr == -1 && !$simple && $html != '') {
            $html = '<div class="ppr-headline">' . Mage::helper('payperrentals')->__('Pricing:') . '</div>' . $html;
        }
        if ($nr == -1 && !$simple) {
            $html .= $buyoutHtml;
            $html .= '<br/>';
        } else {
            if (ITwebexperts_Payperrentals_Helper_Config::showBuyoutPrice()) {
                $html .= $buyoutHtml;
                $html .= '<br/>';
            }
        }
        if(isset($availableDate) && !is_null($availableDate) && !Mage::app()->getStore()->isAdmin()){
            $html .= $availableDate;
            $html .= '<br/>';
        }

        return $html;
    }


    /**
     * @param Mage_Catalog_Model_Product $product
     * @param                            $nr
     * @param bool $simple
     *
     * @return string
     */
    protected static function _getGroupedPriceListHtml(Mage_Catalog_Model_Product $product, $nr = -1, $simple = false, $startDate = null, $endDate = null){
        $html = '';
        $associatedProducts = $associatedProducts = $product->getTypeInstance(true)
            ->getAssociatedProducts($product);
        // child html
        $childHtml = array();
        foreach ($associatedProducts as $childProduct) {
            $childPriceList = self::getPriceList($product, $nr, $startDate, $endDate);
            if (count($childPriceList)) {
                foreach ($childPriceList AS $childPrice) {
                    if (!isset($childHtml[$childPrice['period_type']])) {
                        $childHtml[$childPrice['period_type']] = 0;
                    }
                    $childHtml[$childPrice['period_type']] += $childPrice['price'];
                }
            }
        }
        // main html
        foreach ($childHtml AS $childType => $childPrice) {
            // headline
            $childPrice = Mage::helper('core')->currency(Mage::helper("tax")->getPrice($product, $childPrice));
            if ($nr == -1 && !$simple) {
                $html .= '<div class="ppr-headline">' . Mage::helper('payperrentals')->__('Pricing:') . '</div>';
            }
            // pricing
            $typeText = ITwebexperts_Payperrentals_Helper_Html::getTextForType(1, $childType);
            $html
                .= $typeText . ' ' . Mage::helper('payperrentals')->__('Price:') . ' ' . Mage::helper('core')->currency(
                    $childPrice
                ) . '&nbsp;&nbsp;<br/>';
        }
        return $html;
    }

    /**
     * Calculate price for a rental product
     * If using bundles, you will need to use getPriceForAnyProductType()
     *
     * @param $productId
     * @param $startingDate format YYYY-MM-DD HH:MM:SS
     * @param $endingDate   format YYYY-MM-DD HH:MM:SS
     * @param $qty
     * @param $customerGroup
     *
     * @return float|int|mixed
     */
    public static function calculatePrice(
        $product, $startingDate, $endingDate, $qty, $customerGroup, $useCurrency = false
    )
    {
        if(is_numeric($product)){
            $product = Mage::getModel('catalog/product')->load($product);
        }
        $typeId = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($product->getId(), 'type_id');

        if ($typeId == 'simple') {
            return $product->getPrice();
        }
        $useNonSequential = Mage::helper('payperrentals/config')->isNonSequentialSelect(
            Mage::app()->getStore()->getId()
        );

        // Error check
        if ($startingDate == '' || ($endingDate == '' && !$useNonSequential)) {
            return "Invalid start or end date. Start Date: $startingDate, End Date: $endingDate";
        }
        if (!$useNonSequential) {
            $finalPriceAmount = self::_calculatePrice(
                $product, $startingDate, $endingDate, $qty, $customerGroup, $useCurrency
            );
        } else {
            if ($useNonSequential) {
                $startDateArr = explode(',', $startingDate);
                $endDateArr = explode(',', $startingDate);
            } else {
                $startDateArr = array($startingDate);
                $endDateArr = array($endingDate);
            }
            $finalPriceAmount = 0;
            foreach ($startDateArr as $count => $startingDate) {
                $endingDate = $endDateArr[$count];
                $finalPriceAmount += self::_calculatePrice(
                    $product, $startingDate, $endingDate, $qty, $customerGroup, false
                );
            }

        }
        if ($useCurrency) {
            $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
            $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
            $finalPriceAmount = Mage::helper('directory')->currencyConvert(
                $finalPriceAmount, $baseCurrencyCode, $currentCurrencyCode
            );
        }
        if(is_numeric($product)){
            $product = Mage::getModel('catalog/product')->load($product);
        }
        if(!is_object($product)){
            return 0;
        }
        $specialPriceNotTax = Mage::getModel('catalogrule/rule')->calcProductPriceRule($product, $finalPriceAmount);
        if($specialPriceNotTax) {
            return $specialPriceNotTax;
        }else{
            return $finalPriceAmount;
        }
    }

    public static function arrayPermutations($array) {
        // initialize by adding the empty set
        $results = array(array( ));

        foreach ($array as $element)
            foreach ($results as $combination)
                array_push($results, array_merge(array($element), $combination));

        return $results;
    }

    /**
     * @param $pricesArray
     * @param $useProRated
     * @param $rentalPeriodInSeconds
     */
    private static function getFinalPriceNew($pricesArray, $useProRated, $rentalPeriodInSeconds)
    {

        /** @var ITwebexperts_Payperrentals_Helper_Pricegreedy $priceGreedyHelper */
        /*Must use the combinations... a greedy solution does not suffice*/
        $priceGreedyHelper = Mage::helper('payperrentals/pricegreedy');
        $seconds = array();
        $additionalSeconds = array();
        $additionalPrices = array();
        $prices = array();
        $maxSum = -1;
        $minSum = -1;
        foreach($pricesArray as $count => $priceArr) {
            $seconds[] = $priceArr['mainPricePointInSeconds'];
            $prices[$priceArr['mainPricePointInSeconds']] = $priceArr['mainPricePoint'];
            if(isset($priceArr['additionalPricePoint'])) {
                $additionalSeconds[$priceArr['mainPricePointInSeconds']] = $priceArr['additionalPricePointInSeconds'];
                $additionalPrices[$priceArr['additionalPricePointInSeconds']] = $priceArr['additionalPricePoint'];
            }
            if($useProRated){
                //if not additional
                if($count == 0) {
                    $referencePricePerSecond = $priceArr[$count]['mainPricePoint'] / $priceArr[$count]['mainPricePointInSeconds'];
                }
                if($priceArr['mainPricePointInSeconds'] > $rentalPeriodInSeconds){
                    $maxSum = $priceArr['mainPricePoint'];
                    if($count - 1 >= 0) {
                        $referencePricePerSecond = $priceArr[$count - 1]['mainPricePoint']/$priceArr[$count-1]['mainPricePointInSeconds'];
                    }
                    break;
                }
            }
        }

        if($useProRated){
            $minSum = $referencePricePerSecond * $rentalPeriodInSeconds;
            if($minSum > $maxSum){
                $minSum = $maxSum;
            }
            return $minSum;
        }

        $powerSet = self::arrayPermutations($seconds);

        foreach($powerSet as $newSeconds) {
            if (count($newSeconds) == 0) continue;

            $priceGreedyHelper->setSeconds($newSeconds);
            $secondCombinations = $priceGreedyHelper->getCombination($rentalPeriodInSeconds);
            $groupedSeconds = $priceGreedyHelper->groupBy($secondCombinations);
            if ($groupedSeconds != false) {
                $sumAmount = 0;
                foreach ($groupedSeconds as $key => $group) {
                    //if isset additional[key] and group*key > $rentalPeriodInSeconds then use (group-1)*key + additional
                    if(isset($additionalSeconds[$key]) && $group*$key >= $rentalPeriodInSeconds){
                        $newAdditionalPeriod = $rentalPeriodInSeconds - $key;
                        $newAdditionalSeconds = array($additionalSeconds[$key]);
                        $priceGreedyHelper->setSeconds($newAdditionalSeconds);
           		        $secondAdditionalCombinations = $priceGreedyHelper->getCombination($newAdditionalPeriod);
        		        $groupedAdditionalSeconds = $priceGreedyHelper->groupBy($secondAdditionalCombinations );

                        $priceAdditionalAmount = 0;

                        foreach($groupedAdditionalSeconds as $keyAdditional => $groupAdditional) {
                            $priceAdditionalAmount += $groupAdditional *  $additionalPrices[$keyAdditional];
                        }
                    }
                    if(!isset($priceAdditionalAmount) || $priceAdditionalAmount>$group * $prices[$key]){
                        $sumAmount += $group * $prices[$key];
                    }else{
                        $sumAmount += $prices[$key] + $priceAdditionalAmount;
                    }
                }
                if ($minSum > $sumAmount || $minSum == -1) {
                    $sumAmounts['groupInfinite'] = $groupedSeconds;
                    $sumAmounts['sumAmount'] = $sumAmount;
                    $minSum = $sumAmount;
                }
            }
        }
        return $minSum;

    }

    /**
     * Takes in a prices array and based on pro-rated, add-on day pricing and previous price points
     * gives the lowest price.
     *
     *
     * For debugging:
     *
     * $selectedPricePointIndex - the closest $pricesArray index to the rental period without going over the rental period
     *
     * $additionalPointsPriceCalculated - array of possible additional price points for any remaining rental time. Each one will
     * have in the key the method used to calculate it (ie using_pro_rated)
     *
     * @param $pricesArray
     * @param $useProRated
     * @param $rentalPeriodInSeconds
     * @param int $startRemainingTime
     * @return mixed
     */

    private static function getFinalPrice($pricesArray, $useProRated, $rentalPeriodInSeconds, $startRemainingTime = 0)
    {
        /**
         * Sort by main price point in seconds and find closest period in seconds
         * to the rental period. If only 1 price point then use it.
         * $selectedPricePointIndex = Price point closest to selected rental period
         */
        $dayInSeconds = 86400;
        $additionalPointsPriceCalculated = '';
        $selectedPricePointIndex = 0;
        /**
         * Sets how many price points back to check for add-on price
         * when no add-on price is set for selected price point
         */
        $maxPricePointsBackToCheck = 3;
        usort(
            $pricesArray, function ($a, $b) {
                if ($a['mainPricePointInSeconds'] == $b['mainPricePointInSeconds']) {
                    return ($a['matchSpecialDates'] == "yes" ? 1 : -1);
                }
                return $a['mainPricePointInSeconds'] - $b['mainPricePointInSeconds'];
            }
        );
        $totalPricePoints = count($pricesArray);
        $posInArray = 0;
        foreach ($pricesArray as $priceItem) {

            if (
                ($priceItem['mainPricePointInSeconds'] >= $rentalPeriodInSeconds) || ($totalPricePoints == 1)
                || ($posInArray + 1) == $totalPricePoints
            ) {
                $selectedPricePointIndex = $posInArray;
                break;
            }
            $posInArray++;
        }

        /**
         * Say we are at position 4 in the price array
         * That means there are 4 price points back to check for array items 0 - 3
         */
        $pricePointsBack = min($maxPricePointsBackToCheck, $selectedPricePointIndex);


        /**
         * Check 1 price point ahead, but only if it exists
         */
        $maxHighIndexToCheck = min($totalPricePoints, $selectedPricePointIndex + 1);


        /**
         * Calculate price for $selectedPricePoint, and go back $pricePointsBack
         */
        for ($i = $selectedPricePointIndex - $pricePointsBack; $i <= $maxHighIndexToCheck; $i++) {
            if (isset($pricesArray[$i])) {

                if($startRemainingTime > 0 && !is_null($pricesArray[$i]['additionalPricePoint']) && ($startRemainingTime - $rentalPeriodInSeconds) >= $pricesArray[$i]['mainPricePointInSeconds']){
                    $secondsRemainingForAdditional[$i] = $rentalPeriodInSeconds;
                    $pricesArray[$i]['mainPricePoint'] = 0;
                }else {
                    $secondsRemainingForAdditional[$i]
                        = $rentalPeriodInSeconds - $pricesArray[$i]['mainPricePointInSeconds'];
                }
                /**
                 * Here is where things get interesting. We will build a $additionalPointsPriceCalculated array
                 * in the format $additionalPointsPriceCalculated[current_pricepoint][add_on_price_type][calculated_value]
                 * it is very useful for debugging
                 */
                if ($secondsRemainingForAdditional[$i] >= 1) {

                    /**
                     * $additionalPointsPriceCalculated using pro-rated
                     */
                    if ($useProRated) {
                        $selectedPricePointInDays = ($pricesArray[$i]['mainPricePointInSeconds'] / $dayInSeconds);
                        $selectedPricePointPricePerDay
                            = $pricesArray[$i]['mainPricePoint'] / $selectedPricePointInDays;
                        $remainingTimeInDays = ceil($secondsRemainingForAdditional[$i] / $dayInSeconds);
                        $additionalPointsPriceCalculated[$i]['using_pro_rated'][] = ($remainingTimeInDays *
                            $selectedPricePointPricePerDay);
                    }

                    /**
                     * $additionalPointsPriceCalculated as factor of main price point
                     */

                    $remainingByFactorOfMainPricePoint = ceil(
                        $secondsRemainingForAdditional[$i] / $pricesArray[$i]['mainPricePointInSeconds']
                    );
                    $additionalPointsPriceCalculated[$i]['using_main_price_point']
                        = ($pricesArray[$i]['mainPricePoint'] *
                        $remainingByFactorOfMainPricePoint);

                    /**
                     * $additionalPointsPriceCalculated as factor of multiple main price points +
                     * previous price point
                     */

                    $mainPricePointsRemaining = floor($secondsRemainingForAdditional[$i] / $pricesArray[$i]['mainPricePointInSeconds']);
                    $previousPriceIndex = $i - 1;
                    if($mainPricePointsRemaining >= 1 && $previousPriceIndex >= 0) {
                       $additionalPriceForMain = $mainPricePointsRemaining * $pricesArray[$i]['mainPricePoint'];
                        $remainderInSecondsforMultiple = $secondsRemainingForAdditional[$i] % $pricesArray[$i]['mainPricePointInSeconds'];
                        $previousPriceMultiplier = ceil($remainderInSecondsforMultiple / $pricesArray[$previousPriceIndex]['mainPricePointInSeconds']);
                        $additionalPointsPriceCalculated[$i]['using_multiplemain_and_previous'] = $additionalPriceForMain + ($previousPriceMultiplier * $pricesArray[$previousPriceIndex]['mainPricePoint']);
                    }


                    /**
                     * $additionalPointsPriceCalculated using previous price points as add-on price
                     */

                    for (
                        $previousPricePointIndex = $i - 1; $previousPricePointIndex >= 0;
                        $previousPricePointIndex--
                    ) {

                        $remainingByFactorOfPreviousPricePoint = ceil(
                            $secondsRemainingForAdditional[$i] /
                            $pricesArray[$previousPricePointIndex]['mainPricePointInSeconds']
                        );

                        $previousPriceForRemaining = ($pricesArray[$previousPricePointIndex]['mainPricePoint'] *
                            $remainingByFactorOfPreviousPricePoint);
                        $additionalPointsPriceCalculated[$i]['using_previous_price'][]
                            = $previousPriceForRemaining;

                        /**
                         * Check if we have to combine previous price point add-on price
                         * Like if 1 month = $100 1 week = $50 and 1 day = 5 if 1 month 1 week 1 day is
                         * selected return $155 a combination of the 3
                         */

                        // if is $remainingByFactorOfPreviousPricePoint >1 means there is seconds remaining to use add-ons for
                        if ($remainingByFactorOfPreviousPricePoint > 1) {
                            $previousPriceForRemainingWithoutRemainder
                                = ($pricesArray[$previousPricePointIndex]['mainPricePoint'] *
                                ($remainingByFactorOfPreviousPricePoint - 1));

                            // Basically $prevPriceIndex is the same as above, but need separate index for counter
                            for (
                                $prevPriceIndex = $previousPricePointIndex; $prevPriceIndex > 0;
                                $prevPriceIndex--
                            ) {

                                /**
                                 * Formula is
                                 * Secondary Price Point + ((Seconds Remaining / Price Point in Seconds) * Price Point)
                                 *
                                 * calculate price using previous price points
                                 */
                                $remainderInSecondsPreviousPricePoint = $secondsRemainingForAdditional[$i] - ($pricesArray[$previousPricePointIndex]['mainPricePointInSeconds']);

                                $remainingByFactorOfPreviousPricePoint = ceil(
                                    $remainderInSecondsPreviousPricePoint /
                                    $pricesArray[$prevPriceIndex - 1]['mainPricePointInSeconds']
                                );
                                $priceUsingComboOfPricePoints = $remainingByFactorOfPreviousPricePoint *
                                    $pricesArray[$prevPriceIndex - 1]['mainPricePoint'];

                                /**
                                 * Get previous price point, but without the price for the remainder and add to it
                                 * the $priceUsingComboOfPricePoints
                                 */
                                $additionalPointsPriceCalculated[$i][
                                'using_combo(' . $previousPriceForRemainingWithoutRemainder . '+'
                                . $remainingByFactorOfPreviousPricePoint . '*' . $pricesArray[$prevPriceIndex
                                - 1]['mainPricePoint'] . ')'][]
                                    =
                                    $previousPriceForRemainingWithoutRemainder + $priceUsingComboOfPricePoints;
                                $additionalPointsPriceCalculated[$i]['using_combo'][]
                                    =
                                    $previousPriceForRemainingWithoutRemainder + $priceUsingComboOfPricePoints;
                            }
                        }
                    }

                    /**
                     * $additionalPointsPriceCalculated using add-on price from main price point
                     */
                    if (isset($pricesArray[$i]['additionalPricePoint'])) {
                        $additionalNumber = ceil(
                            $secondsRemainingForAdditional[$i] / $pricesArray[$i]['additionalPricePointInSeconds']
                        );
                        $additionalPointsPriceCalculated[$i]['using_add_on'] = ($additionalNumber *
                            $pricesArray[$i]['additionalPricePoint']);
                    }


                    /**
                     * Here check all $additionalPointsPriceCalculated arrays in order of priority, then lowest price
                     */

                    /** Priority #1 if is using special dates, we can only use special dates add ons */
                    if (array_key_exists(
                        'final_price_using_special_addons', $additionalPointsPriceCalculated[$i]
                    )) {
                        $selectedAddOnPrice[$i]
                            = $additionalPointsPriceCalculated[$i]['final_price_using_special_addons'];
                    } /** Priority #1 add-on price */
                    else {
                        if (array_key_exists('using_add_on', $additionalPointsPriceCalculated[$i])) {
                            $selectedAddOnPrice[$i] = $additionalPointsPriceCalculated[$i]['using_add_on'];
                            /** Priority #2 pro-rated price */
                        } else {
                            if (array_key_exists('using_pro_rated', $additionalPointsPriceCalculated[$i])) {
                                $selectedAddOnPrice[$i] = $additionalPointsPriceCalculated[$i]['using_pro_rated'];
                            } else {
                                /**
                                 * Priority #3 Lowest price of: main price as add-on, prior price point as add-on,
                                 * combo of prior price points as add-on
                                 */
                                if (isset($additionalPointsPriceCalculated[$i]['using_combo'])
                                    && is_array(
                                        $additionalPointsPriceCalculated[$i]['using_combo']
                                    )
                                ) {
                                    foreach ($additionalPointsPriceCalculated[$i]['using_combo'] as $value) {
                                        $toCheck[] = $value;
                                    }
                                }
                                if (isset($additionalPointsPriceCalculated[$i]['using_main_price_point'])) {
                                    $toCheck[] = $additionalPointsPriceCalculated[$i]['using_main_price_point'];
                                }
                                if (isset($additionalPointsPriceCalculated[$i]['using_multiplemain_and_previous'])) {
                                    $toCheck[] = $additionalPointsPriceCalculated[$i]['using_multiplemain_and_previous'];
                                }
                                if (isset($additionalPointsPriceCalculated[$i]['using_previous_price'])
                                    && is_array(
                                        $additionalPointsPriceCalculated[$i]['using_previous_price']
                                    )
                                ) {
                                    foreach ($additionalPointsPriceCalculated[$i]['using_previous_price'] as $value) {
                                        $toCheck[] = $value;
                                    }
                                }
                                $selectedAddOnPrice[$i] = min($toCheck);
                            }
                        }
                    }
                }

                if ($secondsRemainingForAdditional[$i] > 1) {
                    $minimumAddonPrice = $selectedAddOnPrice[$i];
                    if (is_array($minimumAddonPrice)) {
                        $minimumAddonPrice = min($minimumAddonPrice);
                    }
                    $finalPriceAmountArray[$i] = $pricesArray[$i]['mainPricePoint'] + $minimumAddonPrice;
                } else {
                    $finalPriceAmountArray[$i] = $pricesArray[$i]['mainPricePoint'];
                }
            }
        }
        $finalPriceAmount = min($finalPriceAmountArray);

        return $finalPriceAmount;
    }

    /**
     * Function to remove disabled days and dates from the start-end interval
     * @param $rentalPeriodInSeconds
     * @param $startingDate
     * @param $endingDate
     * @param $start_date
     * @param $end_date
     * @param $product
     */
    private static function reCalculatePeriodFromSettings(&$rentalPeriodInSeconds, $startingDate, $endingDate, $product)
    {

        // Add extra time from admin settings field
        if (Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_ADDTIME_NUMBER) != ''
            && Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_ADDTIME_NUMBER) > 0
            && !(date('Y-m-d', strtotime($startingDate)) == date('Y-m-d', strtotime($endingDate)) && date('H:i:s', strtotime($endingDate)) == '23:59:59')
        ) {
            $secondsToAdd = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_ADDTIME_NUMBER),
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_ADDTIME_TYPE)
            );
            $rentalPeriodInSeconds += $secondsToAdd;
        }

        $disabledDays = ITwebexperts_Payperrentals_Helper_Data::getDisabledDays($product->getId(), false, true);
        $blockedDates = ITwebexperts_Payperrentals_Helper_Data::getDisabledDates($product->getId(), true);
        foreach ($blockedDates as $dateFormattedString) {
            if (strtotime($dateFormattedString) >= strtotime($startingDate)
                && strtotime($dateFormattedString) <= strtotime($endingDate)
            ) {
                $secondsToRemove = 60 * 60 * 24;
                $rentalPeriodInSeconds -= $secondsToRemove;
            }
        }
        if (count($disabledDays) > 0) {
            $startTimePadding = strtotime(date('Y-m-d', strtotime($startingDate)));
            $endTimePadding = strtotime(date('Y-m-d', strtotime($endingDate)));
            while ($startTimePadding <= $endTimePadding) {
                $dayofWeek = date('D', $startTimePadding);
                if (in_array($dayofWeek, $disabledDays)) {
                    $secondsToRemove = 60 * 60 * 24;
                    $rentalPeriodInSeconds -= $secondsToRemove;
                }
                $startTimePadding += 60 * 60 * 24;
            }
        }
    }

    private static function getPricesArray($pricesCollection, &$pricesArray, $startDate, $endDate, $qty, $customerGroup)
    {
        $hasSpecials = false;

        foreach ($pricesCollection as $priceItem) {

            // BEGIN customer group, quantity, and date from/to Check
            $continueCustomerGroup = true;
            $continueQuantityStart = true;
            $continueQuantityEnd = true;
            if ($priceItem->getCustomersGroup() == '-1' || $priceItem->getCustomersGroup() == $customerGroup) {
                $continueCustomerGroup = false;
            }
            if ($priceItem->getQtyStart() != '' && $priceItem->getQtyStart() > 0
                && $qty >= $priceItem->getQtyStart()
                || $priceItem->getQtyStart() == ''
                || $priceItem->getQtyStart() == 0
            ) {
                $continueQuantityStart = false;
            }
            if ($priceItem->getQtyEnd() != '' && $priceItem->getQtyEnd() > 0 && $qty <= $priceItem->getQtyEnd()
                || $priceItem->getQtyEnd() == ''
                || $priceItem->getQtyEnd() == 0
            ) {
                $continueQuantityEnd = false;
            }


            if ($continueCustomerGroup || $continueQuantityStart || $continueQuantityEnd
            ) {
                continue;
            }
            // END customer group, quantity, and date from/to Check. If they pass, set price

            /**
             * Build $pricesArray
             */
            $pricePoint = $priceItem->getPrice();
            $pricePointInSeconds = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                $priceItem->getNumberof(), $priceItem->getPtype()
            );
            $additionalPricePoint = $priceItem->getPriceadditional();
            if ($additionalPricePoint == 0) {
                $additionalPricePoint = null;
            }
            /*Additional Number Of is always considered 1*/
            $additionalPricePointInSeconds = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                1, $priceItem->getPtypeadditional()
            );
            if ($priceItem->getReservationpricesdatesId() == null
                || $priceItem->getReservationpricesdatesId() == 0
            ) {
                $isForSpecial = null;
            } else {
                $isForSpecial = "yes";
            }


            if (is_null($isForSpecial)) {
                $pricesArray[] = array(
                    'mainPricePointInSeconds' => $pricePointInSeconds,
                    'mainPricePoint' => (float)$pricePoint,
                    'additionalPricePointInSeconds' => $additionalPricePointInSeconds,
                    'additionalPricePoint' => $additionalPricePoint,
                    'start' => strtotime($startDate),
                    'end' => strtotime($endDate),
                    'special' => false,
                );
            }else{
                $pricesArrayTemp = array(
                    'mainPricePointInSeconds' => $pricePointInSeconds,
                    'mainPricePoint' => (float)$pricePoint,
                    'additionalPricePointInSeconds' => $additionalPricePointInSeconds,
                    'additionalPricePoint' => $additionalPricePoint,
                    'special' => true,
                );
                $hasSpecials = true;
                self::getReservationpricesdates($priceItem->getReservationpricesdatesId(), $pricesArrayTemp, $startDate, $endDate, $pricesArray);
            }

        }
        return $hasSpecials;

    }

    /**
     * Function to return prices collection for a product
     * @param $product
     * @return mixed
     */
    private static function getPricesCollection($product)
    {

        if (Mage::app()->getStore()->isAdmin()) {
            $storeID = Mage::getSingleton('adminhtml/session_quote')->getStoreId();
        } else {
            $storeID = Mage::app()->getStore()->getId();
        }

        $pricesCollection = Mage::getModel('payperrentals/reservationprices')
            ->getCollection()
            ->addEntityStoreFilter($product->getId(), $storeID)
            ->addOrderFilter('ptype DESC')
            ->addOrderFilter('numberof DESC');

        return $pricesCollection;
    }

    /**
     * Returns an array of prices array for every intersecting interval
     * @param $pricesArray
     */
    //here we go through all the pricesArray generated early and we split the start -end date interval by special dates
    //the rest will be not special dates. Intersecting specials intervals is very complex and won't be considered for now. will need some kind of priority

    private static function getPriceForAllPricesArray($pricesArray, $useProRated, $remainingTime){
        $notSpecialDates = array();
        $startRemainingTime = $remainingTime;
        $finalPriceAmount = 0;

        while(count($pricesArray)) {
            $i = 0;

            foreach ($pricesArray as $iPrice) {
                if ($iPrice['special']) {
                    $k = 0;
                    $specialPricesArray = array($iPrice);
                    foreach ($pricesArray as $jPrice) { //it will not consider for now the intersecting specials. Is very complex
                        if ($i == $k || !$jPrice['special']) {
                            $k++;
                            continue;
                        }

                        if($iPrice['start'] == $jPrice['start'] && $iPrice['end'] == $jPrice['end']){
                            $specialPricesArray[] = $jPrice;
                            unset($pricesArray[$k]);
                        }

                        $k++;
                    }
                    if(($iPrice['end'] == $iPrice['start']) || (($iPrice['end'] - $iPrice['start']) == $iPrice['mainPricePointInSeconds']) && $remainingTime != $startRemainingTime){
                        $calculateSecondsDifference = $iPrice['end'] - $iPrice['start'] + $iPrice['mainPricePointInSeconds'];
                    }else{
                        $calculateSecondsDifference = $iPrice['end'] - $iPrice['start'];
                    }
                    $finalPriceAmount += self::getFinalPrice($specialPricesArray, $useProRated, $calculateSecondsDifference);

                    if(($iPrice['end'] - $iPrice['start']) < $iPrice['mainPricePointInSeconds']){
                        $remainingTime -= $iPrice['mainPricePointInSeconds'];
                    }else{
                        $remainingTime -= $calculateSecondsDifference;
                    }

                    if($remainingTime <= 0){
                        $pricesArray = array();
                        break;
                    }
                    unset($pricesArray[$i]);
                    $pricesArray = array_values($pricesArray);
                    break;
                } else {
                    $notSpecialDates[] = $iPrice;
                    unset($pricesArray[$i]);
                    $pricesArray = array_values($pricesArray);
                    break;
                }
                $i++;
            }
        }
        if(count($notSpecialDates) && $remainingTime > 0) {
            //here additionalPricePointInSeconds very complex
            $finalPriceAmount += self::getFinalPrice($notSpecialDates, $useProRated, $remainingTime, $startRemainingTime);
        }

        return $finalPriceAmount;
    }

    private static function _calculatePrice($product, $startingDate, $endingDate, $qty, $customerGroup, $useCurrency = false)
    {
        if (is_numeric($product)) {
            $product = ITwebexperts_Payperrentals_Helper_Data::initProduct($product);
        }
        if(!is_object($product)){
            return 0;
        }

        list($startingDate, $endingDate) = ITwebexperts_Payperrentals_Helper_Date::convertDatepickerToDbFormat(
            $startingDate, $endingDate
        );

        $rentalPeriodInSeconds = ITwebexperts_Payperrentals_Helper_Date::getDifferenceInSeconds($startingDate, $endingDate);

        if ($product->getPayperrentalsPricingtype() == ITwebexperts_Payperrentals_Model_Product_Pricingtype::PRICING_PRORATED) {
            $useProRated = true;
        } else {
            $useProRated = false;
        }

        $pricesCollection = self::getPricesCollection($product);

        // If no prices, return 0
        if (count($pricesCollection) == 0) {
            return 0;
        }

        self::reCalculatePeriodFromSettings($rentalPeriodInSeconds, $startingDate, $endingDate, $product);

        $pricesArray = array();
        $hasSpecials = self::getPricesArray($pricesCollection, $pricesArray, $startingDate, $endingDate, $qty, $customerGroup);
        if($hasSpecials && $rentalPeriodInSeconds > 0){
            $finalPriceAmount = self::getPriceForAllPricesArray($pricesArray, $useProRated, $rentalPeriodInSeconds);

        }else {
            $finalPriceAmount = self::getFinalPrice($pricesArray, $useProRated, $rentalPeriodInSeconds);
        }

        return $finalPriceAmount;
    }

    /**
     * Function to add to pricesArray a special price if it is into the start-end interval
     * @param $rentingStartSeconds
     * @param $recurringStartDate
     * @param $rentingEndSeconds
     * @param $recurringEndDate
     * @param $pricesArrayTemp
     * @param $pricesArray
     */
    private static function addPriceToPricesArrayForSpecialDate($rentingStartSeconds, $recurringStartDate, $rentingEndSeconds, $recurringEndDate, $pricesArrayTemp, &$pricesArray){
        if ($rentingStartSeconds <= strtotime($recurringStartDate) && $rentingEndSeconds >= strtotime($recurringEndDate) && $rentingEndSeconds >= strtotime($recurringStartDate) && $rentingStartSeconds <= strtotime($recurringEndDate)) {
            $pricesArrayTemp['start'] = strtotime($recurringStartDate);
            $pricesArrayTemp['end'] = strtotime($recurringEndDate);
            $pricesArray[] = $pricesArrayTemp;
        }elseif ($rentingStartSeconds >= strtotime($recurringStartDate) && $rentingEndSeconds >= strtotime($recurringEndDate) && $rentingEndSeconds >= strtotime($recurringStartDate) && $rentingStartSeconds <= strtotime($recurringEndDate) ) {
            $pricesArrayTemp['start'] = $rentingStartSeconds;
            $pricesArrayTemp['end'] = strtotime($recurringEndDate);
            $pricesArray[] = $pricesArrayTemp;
        }elseif ($rentingStartSeconds >= strtotime($recurringStartDate) && $rentingEndSeconds <= strtotime($recurringEndDate) && $rentingEndSeconds >= strtotime($recurringStartDate) && $rentingStartSeconds <= strtotime($recurringEndDate)) {
            $pricesArrayTemp['start'] = $rentingStartSeconds;
            $pricesArrayTemp['end'] = $rentingEndSeconds;
            $pricesArray[] = $pricesArrayTemp;
        }elseif ($rentingStartSeconds <= strtotime($recurringStartDate) && $rentingEndSeconds <= strtotime($recurringEndDate) && $rentingEndSeconds >= strtotime($recurringStartDate) && $rentingStartSeconds <= strtotime($recurringEndDate) ) {
            $pricesArrayTemp['start'] = strtotime($recurringStartDate);
            $pricesArrayTemp['end'] = $rentingEndSeconds;
            $pricesArray[] = $pricesArrayTemp;
        }
    }


    /**
     * Returns array of date start & end timestamps that a price should apply to this is used by reservationprices if
     * a price applies to certain dates/times and it uses the reservationpricesdates table to
     * get the info.
     *
     * @param $reservationpricesdatesId
     *
     * @return array of timestamps
     */
    //So we have an interval start - end and a set of special prices.
    //we go through all the special dates...the while is because the prices are repeated by a period of time
    //we check if the special date is in the start -end interval and we add the price in our array.
    private static function getReservationpricesdates($reservationpricesdatesId, $pricesArrayTemp, $rentingStartDate, $rentingEndDate, &$pricesArray)
    {
        $reservationpricesdatesItem = Mage::getModel('payperrentals/reservationpricesdates')->load(
            $reservationpricesdatesId
        );


        $startDate = $reservationpricesdatesItem->getDateFrom();
        $endDate = $reservationpricesdatesItem->getDateTo();

        list($startDate, $endDate) = ITwebexperts_Payperrentals_Helper_Date::convertDatepickerToDbFormat(
            $startDate, $endDate
        );

        $specialStartDate = strtotime(date('Y-m-d H:i:s', strtotime($startDate)));
        $specialEndDate = strtotime(date('Y-m-d H:i:s', strtotime($endDate)));
        $difference = $specialEndDate - $specialStartDate;

        switch ($reservationpricesdatesItem->getDisabledType()) {

            case 'Daily':
                $repeatDays = $reservationpricesdatesItem->getRepeatDays();
                $repeatDaysArr = explode(',', $repeatDays);
                $rentingStartSeconds = strtotime($rentingStartDate);
                $rentingEndSeconds = strtotime($rentingEndDate);
                $startSeconds = strtotime($rentingStartDate);
                $endSeconds = strtotime($rentingEndDate);
                while(true) {
                    $yearMonthDayStart = date('Y-m-d', $startSeconds);
                    if(in_array(date('w', strtotime($yearMonthDayStart)), $repeatDaysArr)) {
                        $recurringStartDate = $yearMonthDayStart . ' ' . date('H:i:s', $specialStartDate);
                        $recurringEndDate = date('Y-m-d H:i:s', strtotime($recurringStartDate) + $difference);
                        self::addPriceToPricesArrayForSpecialDate($rentingStartSeconds, $recurringStartDate, $rentingEndSeconds, $recurringEndDate, $pricesArrayTemp, $pricesArray);
                    }
                    if($startSeconds > $endSeconds){
                        break;
                    }
                    $startSeconds += 60 * 60 * 24;
                }

                break;

            case 'Weekly'://some day which repeats only weekly
                $rentingStartSeconds = strtotime($rentingStartDate);
                $rentingEndSeconds = strtotime($rentingEndDate);
                $startSeconds = strtotime($rentingStartDate);
                $endSeconds = strtotime($rentingEndDate);
                while(true) {
                    $yearMonthDayStart = date('Y-m-d', $startSeconds);
                    if(date('l', $specialStartDate) == date('l', strtotime($yearMonthDayStart))) {
                        $recurringStartDate = $yearMonthDayStart . ' ' . date('H:i:s', $specialStartDate);
                        $recurringEndDate = date('Y-m-d H:i:s', strtotime($recurringStartDate) + $difference);
                        self::addPriceToPricesArrayForSpecialDate($rentingStartSeconds, $recurringStartDate, $rentingEndSeconds, $recurringEndDate, $pricesArrayTemp, $pricesArray);
                    }
                    if($startSeconds > $endSeconds){
                        break;
                    }
                    $startSeconds += 60 * 60 * 24;
                }

                break;

            case 'Monthly'://some day which repeats only monthly
                $rentingStartSeconds = strtotime($rentingStartDate);
                $rentingEndSeconds = strtotime($rentingEndDate);
                $startSeconds = strtotime($rentingStartDate);
                $endSeconds = strtotime($rentingEndDate);
                while(true) {
                    $yearMonthStart = date('Y-m', $startSeconds);
                    $recurringStartDate = $yearMonthStart . '-' . date('d H:i:s', $specialStartDate);
                    $recurringEndDate = date('Y-m-d H:i:s', strtotime($recurringStartDate) + $difference);
                    self::addPriceToPricesArrayForSpecialDate($rentingStartSeconds, $recurringStartDate, $rentingEndSeconds, $recurringEndDate, $pricesArrayTemp, $pricesArray);
                    if($startSeconds > $endSeconds){
                        break;
                    }
                    $startSeconds += 60 * 60 * 24 * (intval(date('t', mktime(0,0,0,date('m', $startSeconds),31,date('Y', $startSeconds)))));
                }

                break;


            case 'Yearly'://some day which repeats only yearly
                $rentingStartSeconds = strtotime($rentingStartDate);
                $rentingEndSeconds = strtotime($rentingEndDate);
                $startSeconds = strtotime($rentingStartDate);
                $endSeconds = strtotime($rentingEndDate);
                while(true) {
                    $yearStart = date('Y', $startSeconds);
                    $recurringStartDate = $yearStart . '-' . date('m-d H:i:s', $specialStartDate);
                    $recurringEndDate = date('Y-m-d H:i:s', strtotime($recurringStartDate) + $difference);
                    self::addPriceToPricesArrayForSpecialDate($rentingStartSeconds, $recurringStartDate, $rentingEndSeconds, $recurringEndDate, $pricesArrayTemp, $pricesArray);
                    if($startSeconds > $endSeconds){
                        break;
                    }
                    $startSeconds += 60 * 60 * 24 * (intval(date('z', mktime(0,0,0,12,31,$yearStart))) + 1);
                }

                break;

            case 'Never'://some day which never repeats
                $rentingStartSeconds = strtotime($rentingStartDate);
                $rentingEndSeconds = strtotime($rentingEndDate);
                $recurringStartDate = date('Y-m-d H:i:s', $specialStartDate);
                $recurringEndDate = date('Y-m-d H:i:s', strtotime($recurringStartDate) + $difference);
                self::addPriceToPricesArrayForSpecialDate($rentingStartSeconds, $recurringStartDate, $rentingEndSeconds, $recurringEndDate, $pricesArrayTemp, $pricesArray);
                break;
        }

        return true;
    }

    /**
     * Function to get options prices
     * @param $product
     * @param $price
     * @return int
     */
    private static function getOptionsPrice($product, $price)
    {
        $optionPrice = 0;
        if ($optionIds = Mage::app()->getRequest()->getParam('options')) {
            $basePrice = $price;
            foreach ($optionIds as $optionId => $oValue) {

                if ($option = $product->getOptionById($optionId)) {

                    //$quoteItemOption = $product->getCustomOption('option_' . $option->getId());
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option);
                    //->setQuoteItemOption($quoteItemOption);
                    if (is_array($oValue)) {
                        foreach ($oValue as $iValue) {
                            $optionPrice += $group->getOptionPrice($iValue, $basePrice);
                        }
                    } else {
                        $optionPrice += $group->getOptionPrice($oValue, $basePrice);
                    }
                }
            }
        }

        return $optionPrice;
    }


    /**
     * This function will return the price for any product type.
     * A configurable product has attributes
     * A bundle product is composed from bundle_options and bundle_quantities for every option.
     * A bundle product has an extra bundle_option_qty1 for qtys which are set as default and can't be changed by the user.
     * @param $product
     * @param $attributes
     * @param $bundleOptions
     * @param $bundleOptionsQty1 this is for holding default Bundle Quantities
     * @param $bundleOptionsQty
     * @param $startDate
     * @param $endDate
     * @param $qty
     * @param null $onClick
     *
     * @return int
     */

    public static function getPriceForAnyProductType(
        $product, $attributes, $bundleOptions, $bundleOptionsQty1, $bundleOptionsQty, $startDate, $endDate,
        $qty, &$onClick = null
    )
    {
        if ($qty == '' || $qty == 0) {
            $qty = 1;
        }
        $productId = $product->getId();
        $customerGroup = ITwebexperts_Payperrentals_Helper_Data::getCustomerGroup();
        $qtyArr = ITwebexperts_Payperrentals_Helper_Inventory::getQuantityArrayForProduct(
            $product, $qty, $attributes, $bundleOptions, $bundleOptionsQty1, $bundleOptionsQty
        );
        $typeId = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'type_id');

        $priceAmount = 0;
        if ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE) {
            $priceAmount = $qty * ITwebexperts_Payperrentals_Helper_Price::calculatePrice(
                    key($qtyArr), $startDate, $endDate, $qty, $customerGroup
                );
        } elseif ($typeId == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_GROUPED) {
            reset($qtyArr);
            $productAssoc = Mage::getModel('catalog/product')->load(key($qtyArr));
            $onClick = "setLocation('" . Mage::helper('checkout/cart')->getAddUrl(
                    $productAssoc,
                    array('_query' => array('options' => array('start_date' => $startDate, 'end_date' => $endDate,
                        'qty' => $qty, 'is_filtered' => true),
                        'start_date' => $startDate, 'end_date' => $endDate, 'qty' => $qty,
                        'is_filtered' => true))
                ) . "');";
            $priceAmount = $qty * ITwebexperts_Payperrentals_Helper_Price::calculatePrice(
                    key($qtyArr), $startDate, $endDate, $qty, $customerGroup
                );
        } elseif ($typeId != ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE
            || $product->getBundlePricingtype()
            == ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype::PRICING_BUNDLE_FORALL
        ) {
            $priceAmount = $qty * ITwebexperts_Payperrentals_Helper_Price::calculatePrice(
                    $product->getId(), $startDate, $endDate, $qty, $customerGroup
                );

        } elseif ($bundleOptions) {
            foreach ($qtyArr as $iProduct => $iQty) {
                $priceVal = ITwebexperts_Payperrentals_Helper_Price::calculatePrice(
                    $iProduct, $startDate, $endDate, $qty, $customerGroup
                );
                if ($priceVal == -1) {
                    $priceAmount = -1;
                    break;
                }
                $priceAmount += $iQty * $priceVal;
            }
            $priceAmount *= $qty;
        }
        if ($priceAmount != -1) {
            $hasMultiply = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($product->getId(), 'payperrentals_has_multiply');;
            if ($hasMultiply == ITwebexperts_Payperrentals_Model_Product_Hasmultiply::STATUS_ENABLED) {
                $priceAmount += self::getOptionsPrice($product, $priceAmount/$qty) * $qty;
            } else {
                $priceAmount += self::getOptionsPrice($product, $priceAmount);
            }
        }

        return $priceAmount;
    }

    public static function getDamageWaiver($product, $startingDate, $endingDate, $customerGroup, $qty = 1)
    {
        if (is_numeric($product)) {
            $product = Mage::getModel('catalog/product')->load($product);
        }

        $useTimes = ITwebexperts_Payperrentals_Helper_Data::useTimes($product->getId());
        if (Mage::app()->getStore()->isAdmin()) {
            $storeID = Mage::getSingleton('adminhtml/session_quote')->getStoreId();
        } else {
            $storeID = Mage::app()->getStore()->getId();
        }


        $start_date = new Zend_Date(date('Y-m-d H:i:s', strtotime($startingDate)), 'yyyy-MM-dd HH:mm:ss');
        $end_date = new Zend_Date(date('Y-m-d H:i:s', strtotime($endingDate)), 'yyyy-MM-dd HH:mm:ss');

        if ($useTimes == 1 && $start_date->get('yyyy-MM-dd') == $end_date->get('yyyy-MM-dd')
            && $startingDate != $endingDate
        ) {
            $start_date->setHour(0);
            $start_date->setMinute(0);
            $start_date->setSecond(0);
            $end_date->setHour(0);
            $end_date->setMinute(0);
            $end_date->setSecond(0);
            $end_date = $end_date->addDay(1);
        }

        $difference = $end_date->sub($start_date);
        $measure = new Zend_Measure_Time($difference->toValue(), Zend_Measure_Time::SECOND);
        $currentDate = date('Y-m-d H:i:s');
        $seconds = $measure->getValue();

        /*add extra time*/
        if (Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_ADDTIME_NUMBER) != ''
            && Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_ADDTIME_NUMBER) > 0
        ) {
            $secondsToAdd = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_ADDTIME_NUMBER),
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_ADDTIME_TYPE)
            );
            $seconds += $secondsToAdd;
        }


        $coll = Mage::getModel('payperrentals/reservationprices')
            ->getCollection()
            ->addEntityStoreFilter($product->getId(), $storeID)
            ->addOrderFilter('ptype DESC')
            ->addOrderFilter('numberof DESC');

        $usageStoreId = (count($coll->getItemsByColumnValue('store_id', $storeID))) ? $storeID : 0;
        $damageWaiver = false;
        $maxDamageWaiver = 0;
        $maxSeconds = 0;
        $currentSeconds = 0;
        /** @var $item ITwebexperts_Payperrentals_Model_Reservationprices */
        foreach ($coll as $item) {
            if ($item->getStoreId() != $usageStoreId) {
                continue;
            }
            $continueC = true;
            $continueQS = true;
            $continueQE = true;
            if ($item->getCustomersGroup() == '-1' || $item->getCustomersGroup() == $customerGroup) {
                $continueC = false;
            }
            if ($item->getQtyStart() != '' && $item->getQtyStart() > 0 && $qty >= $item->getQtyStart()
                || $item->getQtyStart() == ''
                || $item->getQtyStart() == 0
            ) {
                $continueQS = false;
            }
            if ($item->getQtyEnd() != '' && $item->getQtyEnd() > 0 && $qty <= $item->getQtyEnd()
                || $item->getQtyEnd() == ''
                || $item->getQtyEnd() == 0
            ) {
                $continueQE = false;
            }

            if ($continueC || $continueQS || $continueQE) {
                continue;
            }
            $itemDamageWaiver = $item->getDamageWaiver();
            $itemSeconds = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                $item->getNumberof(), $item->getPtype()
            );
            if ($itemSeconds > $maxSeconds) {
                $maxSeconds = $itemSeconds;
                $maxDamageWaiver = $itemDamageWaiver;
            }
            if ($seconds <= $itemSeconds) {
                if ($currentSeconds == 0 || $currentSeconds > $itemSeconds) {
                    $currentSeconds = $itemSeconds;
                    $damageWaiver = $itemDamageWaiver;
                }
            }
        }
        if ($damageWaiver === false && $seconds >= $maxSeconds) {
            $damageWaiver = $maxDamageWaiver;
        }
        return $damageWaiver;
    }

    public static function getDamageWaiverHtml(
        $item, $damageWaiver, $selected = false, $choice = true
    )
    {
        $product = $item->getProduct();
        if(is_object($item->getQuote())){
            $product->setCustomerGroupId($item->getQuote()->getCustomerGroupId());
        }

        $html = '<div>';
        $request = Mage::app()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        if (!$choice || ($module == 'checkout' && $controller == 'onepage')) {
            if (!$selected) {
                $html .= '<strong>' . Mage::helper('payperrentals')->__(' No') . '</strong>';
            } else {
                $html .= '<strong>' . Mage::helper('payperrentals')->__(
                        ' Yes +%s', Mage::helper('checkout')->formatPrice(
                            $item->getData(ITwebexperts_Payperrentals_Helper_Price::DAMAGE_WAIVER_OPTION_PRICE), true,
                            true
                        )
                    ) . '</strong>';
            }
        } else {
            $html .= '<input type="radio" name="cart[' . $item->getId()
                . '][damage_waiver]" class="damage-waiver-input" id="damageWaiverNo' . $item->getId() . '" value="0" ';
            if (!$selected) {
                $html .= 'checked="checked"';
            }
            $html .= '/>';
            $html .= '<label for="damageWaiverNo' . $item->getId() . '">' . Mage::helper('payperrentals')->__(' No')
                . '</label>';
            $html .= '</div>';
            $html .= '<div>';
            $html .= '<input type="radio" name="cart[' . $item->getId()
                . '][damage_waiver]" class="damage-waiver-input" id="damageWaiverYes' . $item->getId() . '" value="1" ';
            if ($selected) {
                $html .= 'checked="checked"';
            }
            $html .= '/>';
            $html .= '<label for="damageWaiverYes' . $item->getId() . '">' . Mage::helper('payperrentals')->__(
                    ' Yes +%s', Mage::helper('checkout')->formatPrice(
                        $item->getData(ITwebexperts_Payperrentals_Helper_Price::DAMAGE_WAIVER_OPTION_PRICE), true, true
                    )
                ) . '</label>';
        }
        $html .= '</div>';

        return $html;
    }

    public static function getQuoteDamageWaiver(Mage_Sales_Model_Quote $quote)
    {
        $quoteItems = $quote->getItemsCollection();
        $damageWaiverAmount = 0;
        foreach ($quoteItems as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getData(ITwebexperts_Payperrentals_Helper_Price::DAMAGE_WAIVER_OPTION)) {
                $damageWaiverAmount += $item->getData(ITwebexperts_Payperrentals_Helper_Price::DAMAGE_WAIVER_OPTION)
                    * $item->getQty();
            }

        }
        return $damageWaiverAmount;
    }

    public static function getBuyoutPrice($product, $configurableParent = null)
    {
        if (is_object($configurableParent) && $configurableParent->getPayperrentalsBuyoutprice()) {
            $buyoutPrice = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($configurableParent->getId(), 'payperrentals_buyoutprice');;
        } else {
            $buyoutPrice = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($product->getId(), 'payperrentals_buyoutprice');;
        }
        return $buyoutPrice;
    }

}