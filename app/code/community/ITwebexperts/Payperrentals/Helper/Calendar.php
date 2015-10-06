<?php
class ITwebexperts_Payperrentals_Helper_Calendar extends Mage_Core_Helper_Abstract {

    public function getHoursStart(){
        $_configHelper = Mage::helper('payperrentals/config');
        list($_storeOpen, $_storeClose) = $_configHelper->getStoreTime();
        return ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('start_time', $_storeOpen, $_storeClose, array(), false, $this->__('Start Time'), null);
    }

    public function getHoursEnd(){
        $_configHelper = Mage::helper('payperrentals/config');
        list($_storeOpen, $_storeClose) = $_configHelper->getStoreTime();
        return ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('end_time', $_storeOpen, $_storeClose, array(), false, $this->__('End Time'), null);
    }

    public function getHoursCount(){
        $_configHelper = Mage::helper('payperrentals/config');
        list($_storeOpen, $_storeClose) = $_configHelper->getStoreTime();
        return count(ITwebexperts_Payperrentals_Helper_Timebox::getTimeOptionsArray($_storeOpen, $_storeClose, array(), 'label'));
    }

    public function getHours(){

        /** @var $_configHelper ITwebexperts_Payperrentals_Helper_Config */
        $_configHelper = Mage::helper('payperrentals/config');
        list($_storeOpen, $_storeClose) = $_configHelper->getStoreTime();
        $_excludeHoursStart = array();
        $_excludeHoursEnd = array();

        $_hourStartMonday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('start_time_monday', $_storeOpen, $_storeClose, $_excludeHoursStart, false, $this->__('Start Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::MONDAY);
        $_hourEndMonday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('end_time_monday', $_storeOpen, $_storeClose, $_excludeHoursEnd, false, $this->__('End Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::MONDAY);

        $_hourStartTuesday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('start_time_tuesday', $_storeOpen, $_storeClose, $_excludeHoursStart, false, $this->__('Start Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::TUESDAY);
        $_hourEndTuesday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('end_time_tuesday', $_storeOpen, $_storeClose, $_excludeHoursEnd, false, $this->__('End Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::TUESDAY);

        $_hourStartWednesday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('start_time_wednesday', $_storeOpen, $_storeClose, $_excludeHoursStart, false, $this->__('Start Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::WEDNESDAY);
        $_hourEndWednesday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('end_time_wednesday', $_storeOpen, $_storeClose, $_excludeHoursEnd, false, $this->__('End Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::WEDNESDAY);

        $_hourStartThursday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('start_time_thursday', $_storeOpen, $_storeClose, $_excludeHoursStart, false, $this->__('Start Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::THURSDAY);
        $_hourEndThursday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('end_time_thursday', $_storeOpen, $_storeClose, $_excludeHoursEnd, false, $this->__('End Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::THURSDAY);

        $_hourStartFriday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('start_time_friday', $_storeOpen, $_storeClose, $_excludeHoursStart, false, $this->__('Start Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::FRIDAY);
        $_hourEndFriday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('end_time_friday', $_storeOpen, $_storeClose, $_excludeHoursEnd, false, $this->__('End Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::FRIDAY);

        $_hourStartSaturday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('start_time_saturday', $_storeOpen, $_storeClose, $_excludeHoursStart, false, $this->__('Start Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::SATURDAY);
        $_hourEndSaturday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('end_time_saturday', $_storeOpen, $_storeClose, $_excludeHoursEnd, false, $this->__('End Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::SATURDAY);

        $_hourStartSunday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('start_time_sunday', $_storeOpen, $_storeClose, $_excludeHoursStart, false, $this->__('Start Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::SUNDAY);
        $_hourEndSunday = ITwebexperts_Payperrentals_Helper_Timebox::getTimeInput('end_time_sunday', $_storeOpen, $_storeClose, $_excludeHoursEnd, false, $this->__('End Time'), ITwebexperts_Payperrentals_Model_Product_Excludedaysweek::SUNDAY);

        return $_hourStartMonday.$_hourEndMonday.$_hourStartTuesday.$_hourEndTuesday.$_hourStartWednesday.$_hourEndWednesday.$_hourStartThursday.$_hourEndThursday.$_hourStartFriday.$_hourEndFriday.$_hourStartSaturday.$_hourEndSaturday.$_hourStartSunday.$_hourEndSunday;
    }

    public function getCalendar($product = null, $forceUseTimes = false, $quoteItemdId = null, $quoteItem = null){
        $prodArray = array(
                'product' => $product,
                'force_use_time' => $forceUseTimes,
                'quote_item_id' => $quoteItemdId,
                'quote_item' => $quoteItem
        );

        return Mage::app()->getLayout()
            ->createBlock('payperrentals/html_calendar', 'my.front.calendar')
            ->setData($prodArray)
            ->toHtml();
    }
}