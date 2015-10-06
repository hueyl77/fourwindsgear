<?php



class ITwebexperts_Payperrentals_Helper_Timebox extends Mage_Core_Helper_Abstract
{
    /**
     * Convert formatted time string (hh:mm:ss or hh:mm) to seconds
     * @param string $_timeString
     *
     * @return int
     * */
    public static function convertTimeToSecond($_timeString)
    {
        return strtotime($_timeString);
    }

    /**
     * Convert seconds to formatted string
     * @param string $_time
     * @param string $_format
     *
     * @return int
     */
    public static function convertSecondsToTime($_time, $_format = 'H:i:s')
    {
        return date($_format, $_time);
    }

    /**
     * Get time options range
     * @param string $_startTime
     * @param string $_endTime
     * @param array $_excludeHours
     * @param string $_optionType
     *
     * @return array
     */
    public static function getTimeOptionsArray($_startTime, $_endTime, $_excludeHours = array(), $_optionType = 'all')
    {
        $_configHelper = Mage::helper('payperrentals/config');
        $_storeId = (Mage::app()->getRequest()->getParam('store_id')) ? Mage::app()->getRequest()->getParam('store_id') : Mage::app()->getStore()->getId();
        $_timeIncrement = $_configHelper->getTimeIncrement($_storeId);
        $_options = array();
        $_startTime = self::convertTimeToSecond($_startTime);
        $_endTime = self::convertTimeToSecond($_endTime);
        if($_optionType != 'range'){
            $_endTime -= $_timeIncrement * 60;
        }
        while ($_startTime <= $_endTime) {
            /** Break calculation for last element range, because if $_startTime == $_endTime it give +1 odd element */
            $_endTimePeriod = strtotime('+' . $_timeIncrement . ' minutes', $_startTime);
            if($_optionType == 'range' && ($_endTimePeriod > $_endTime)) break;

            /** Exclude time if in array*/
            $_timeString = self::convertSecondsToTime($_startTime);
            if (in_array($_timeString, $_excludeHours)){
                $_startTime = $_endTimePeriod;
                continue;
            }
            switch ($_optionType) {
                case 'label':
                    if(Mage::helper('payperrentals/config')->isShortTimeFormat()) {
                        $_keyValue = preg_replace('/^0+?|:[0-9]*/', '', self::convertSecondsToTime($_startTime, 'h:i A'));
                    }else{
                        $_keyValue = preg_replace('/^0+?|:[0-9]*/', '', self::convertSecondsToTime($_startTime, 'H:i'));
                    }
                    $_options[$_keyValue]['colspan'] = (isset($_options[$_keyValue]) && isset($_options[$_keyValue]['colspan'])) ? $_options[$_keyValue]['colspan'] + 1 : 1;
                    $_options[$_keyValue]['label'] = $_keyValue;
                    break;
                case 'value':
                    $_options[] = $_timeString;
                    break;
                case 'range':
                    $_options[] = array(
                        'startPeriod' => $_timeString,
                        'endPeriod' => self::convertSecondsToTime($_endTimePeriod)
                    );
                    break;
                case 'all':
                default:
                if(Mage::helper('payperrentals/config')->isShortTimeFormat()) {
                    $_options[] = array(
                        'label' => self::convertSecondsToTime($_startTime, 'h:i A'),
                        'value' => $_timeString
                    );
                }else{
                    $_options[] = array(
                        'label' => self::convertSecondsToTime($_startTime, 'H:i'),
                        'value' => $_timeString
                    );
                }
            }
            $_startTime = $_endTimePeriod;
        }
        return $_options;
    }

    private static function _getStoreTimeByDay($day){
        $configHelper = Mage::helper('payperrentals/config');
        switch($day){
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::MONDAY:
                $storeTimeMonday = $configHelper->getStoreTimeMonday();
                $_startTimeStringDefault = $storeTimeMonday[0];
                $_endTimeStringDefault = $storeTimeMonday[1];
                break;
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::TUESDAY:
                $storeTimeTuesday = $configHelper->getStoreTimeTuesday();
                $_startTimeStringDefault = $storeTimeTuesday[0];
                $_endTimeStringDefault = $storeTimeTuesday[1];
                break;
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::WEDNESDAY:
                $storeTimeWednesday = $configHelper->getStoreTimeWednesday();
                $_startTimeStringDefault = $storeTimeWednesday[0];
                $_endTimeStringDefault = $storeTimeWednesday[1];
                break;
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::THURSDAY:
                $storeTimeThursday = $configHelper->getStoreTimeThursday();
                $_startTimeStringDefault = $storeTimeThursday[0];
                $_endTimeStringDefault = $storeTimeThursday[1];
                break;
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::FRIDAY:
                $storeTimeFriday = $configHelper->getStoreTimeFriday();
                $_startTimeStringDefault = $storeTimeFriday[0];
                $_endTimeStringDefault = $storeTimeFriday[1];
                break;
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::SATURDAY:
                $storeTimeSaturday = $configHelper->getStoreTimeSaturday();
                $_startTimeStringDefault = $storeTimeSaturday[0];
                $_endTimeStringDefault = $storeTimeSaturday[1];
                break;
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::SUNDAY:
                $storeTimeSunday = $configHelper->getStoreTimeSunday();
                $_startTimeStringDefault = $storeTimeSunday[0];
                $_endTimeStringDefault = $storeTimeSunday[1];
                break;
            default:
                $storeTime = $configHelper->getStoreTime();
                $_startTimeStringDefault = $storeTime[0];
                $_endTimeStringDefault = $storeTime[1];
                break;
        }
        if($_startTimeStringDefault == '' || $_endTimeStringDefault == ''){
            $storeTime = $configHelper->getStoreTime();
            $_startTimeStringDefault = $storeTime[0];
            $_endTimeStringDefault = $storeTime[1];
        }
        return array($_startTimeStringDefault, $_endTimeStringDefault);
    }

    public static function getTimeInput($_selectBoxId, $_startTimeString, $_endTimeString, $_excludeHours, $_selectValue = false, $_title = null, $_day = null)
    {
        list($_startTimeStringDefault, $_endTimeStringDefault) = self::_getStoreTimeByDay($_day);
        $_options = self::getTimeOptionsArray($_startTimeStringDefault, $_endTimeStringDefault, $_excludeHours);
        $_selectBoxIdClass = $_selectBoxId;
        if($_selectBoxId != 'start_time' && $_selectBoxId != 'end_time'){
            $_selectBoxIdClass = $_selectBoxId .' timeinputcls';
        }
        $hourSelect = Mage::app()->getLayout()->createBlock('core/html_select')
            ->setName($_selectBoxId)
            ->setId($_selectBoxId)
            ->setClass($_selectBoxIdClass);
        if(!is_null($_title)) {
            $hourSelect->setTitle($_title);
        }
        $hourSelect->setOptions($_options);
        if($_selectValue){
            $hourSelect->setValue($_selectValue);
        }
        return $hourSelect->toHtml();
    }

    public static function getTimeDetails($day = null){

        list($_storeOpen, $_storeClose) = self::_getStoreTimeByDay($day);
        $_timeHeaderAr = ITwebexperts_Payperrentals_Helper_Timebox::getTimeOptionsArray($_storeOpen, $_storeClose, array(), 'label');
        if(count($_timeHeaderAr)) {
            $_timeBodyAr = ITwebexperts_Payperrentals_Helper_Timebox::getTimeOptionsArray($_storeOpen, $_storeClose, array(), 'range');
        }

        switch($day){
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::MONDAY:
                $dayh = '-monday';
                break;
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::TUESDAY:
                $dayh = '-tuesday';
                break;
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::WEDNESDAY:
                $dayh = '-wednesday';
                break;
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::THURSDAY:
                $dayh = '-thursday';
                break;
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::FRIDAY:
                $dayh = '-friday';
                break;
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::SATURDAY:
                $dayh = '-saturday';
                break;
            case ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::SUNDAY:
                $dayh = '-sunday';
                break;
            default:
                $dayh = '';
                break;

        }

        $dayDetailsHtml = Mage::helper('payperrentals')->__('Day details');
        $busyTimeHtml = Mage::helper('payperrentals')->__('Busy Time');
        $availableTimeHtml = Mage::helper('payperrentals')->__('Available Time');
        $selectedTimeHtml = Mage::helper('payperrentals')->__('Selected Time');
        $timeDetailHtml = '';
        if(count($_timeHeaderAr)) {
            $headColumnHtml = '';
            foreach ($_timeHeaderAr as $_timeHeader) {
                $headColumnHtml .= '<td colspan = "' . $_timeHeader['colspan'] . '">' . $_timeHeader['label'] . ' </td >';
            }

            $bodyColumnHtml = '';
            foreach ($_timeBodyAr as $_timeBody) {
                $bodyColumnHtml .= '<td timestartvalue = "' . $_timeBody['startPeriod'] . '" timeendvalue = "' . $_timeBody['endPeriod'] . '">&nbsp;</td >';
            }

            $timeDetailHtml = <<<TDH
<div class="daydetails day-detail-container$dayh">
    <div class="day-detail-header">$dayDetailsHtml</div>
    <table class="day-detail">
        <thead>
        <tr>
            $headColumnHtml
        </tr>
        </thead>
        <tbody>
        <tr>
            $bodyColumnHtml
        </tr>
        </tbody>
    </table>

    <table class="day-detail-description">
        <tbody>
        <tr>
            <td class="busy"></td>
            <td class="description">$busyTimeHtml</td>
            <td class="available"></td>
            <td class="description">$availableTimeHtml</td>
            <td class="selected-time"></td>
            <td class="description">$selectedTimeHtml</td>
        </tr>
        </tbody>
    </table>
</div>
TDH;
        }
        return $timeDetailHtml;
    }

    public static function getNumberofBySeconds($seconds, $periodType) {
        $numberOf = 0;
        switch($periodType){
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::MINUTES:
                $numberOf = $seconds / 60;
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::HOURS:
                $numberOf = $seconds / (60 * 60);
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::DAYS:
                $numberOf = $seconds / (60 * 60 * 24);
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::WEEKS:
                $numberOf = $seconds / (60 * 60 * 24 * 7);
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::MONTHS:
                $numberOf = $seconds / (60 * 60 * 24 * 30);
                break;
            case ITwebexperts_Payperrentals_Model_Product_Periodtype::YEARS:
                $numberOf = $seconds / (60 * 60  * 24 * 365);
                break;
        }

        return (int)ceil($numberOf);
    }


    public function getProductGridDates()
    {
        $dates = array();
        $request = base64_decode(Mage::app()->getRequest()->getParam('product_filter'));
        parse_str($request, $request_values);

        if (empty($request_values)) {
            $dates['start_date'] = date('Y-m-d');
        } elseif (!empty($request_values['payperrental_custgrid_startdate'])) {
            $dates['start_date'] =ITwebexperts_Payperrentals_Helper_Date::toMysqlDate(
                $request_values['payperrental_custgrid_startdate'], true
            );
        } elseif (Mage::getSingleton('core/session')->getProductGridStart()) {
            $dates['start_date'] = Mage::getSingleton('core/session')->getProductGridStart();
        } else {
            $dates['start_date'] = date('Y-m-d');
        }
        Mage::getSingleton('core/session')->setProductGridStart($dates['start_date']);

        if (empty($request_values)) {
            $dates['end_date'] = date('Y-m-d');
        } elseif (!empty($request_values['payperrental_custgrid_enddate'])) {
            $dates['end_date'] = ITwebexperts_Payperrentals_Helper_Date::toMysqlDate(
                $request_values['payperrental_custgrid_enddate'], true
            );
        } elseif (Mage::getSingleton('core/session')->getProductGridEnd()) {
            $dates['end_date'] = Mage::getSingleton('core/session')->getProductGridEnd();
        } else {
            $dates['end_date'] = date('Y-m-d');
        }
        Mage::getSingleton('core/session')->setProductGridEnd($dates['end_date']);

        return $dates;
    }
}