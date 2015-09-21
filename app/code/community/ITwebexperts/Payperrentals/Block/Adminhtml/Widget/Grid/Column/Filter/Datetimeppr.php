<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Widget_Grid_Column_Filter_Datetimeppr extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Datetime
{
    /*
     * This function overrides the default by removing any timezone conversions
     * since in the DB for payperrentals the datetime files are not stored in UTC
     */
    protected function _convertDate($date, $locale)
    {
        $dateObj = new Zend_Date();

        //set begining of day
        $dateObj->setHour(00);
        $dateObj->setMinute(00);
        $dateObj->setSecond(00);

        //set date with applying timezone of store
        $dateObj->set($date, Zend_Date::DATE_SHORT, $locale);

        return $dateObj;
    }
}