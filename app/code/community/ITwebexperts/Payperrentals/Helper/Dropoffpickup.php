<?php
class ITwebexperts_Payperrentals_Helper_Dropoffpickup extends Mage_Core_Helper_Abstract
{
    /**
     * Returns dropoff input as html table columns
     *
     * @param int $storeOpen
     * @param int $storeClose
     * @param array $excludeHoursStart
     *
     * @return string
     */
    public function getDropoffInput($storeOpen, $storeClose, $excludeHoursStart){
        $html = '<td class="label">'. Mage::helper('payperrentals')->__('Dropoff Date:') . ' </td><td>';

        if(Mage::getSingleton('adminhtml/session_quote')->getOrder()->getSendDatetime()) {
            $startDate = Mage::getSingleton('adminhtml/session_quote')->getOrder()->getSendDatetime();
        }elseif(Mage::registry('current_order') && Mage::registry('current_order')->getSendDatetime()){
            $startDate = Mage::registry('current_order')->getSendDatetime();
        }else{
            $startDate = '';
        }
        $timeSelected = false;
        if($startDate != ''){
            $timeSelectedArr = explode(' ', $startDate);
            if(isset( $timeSelectedArr[1])) {
                $timeSelected = $timeSelectedArr[1];
            }
            /** @var $coreHelper Mage_Core_Helper_Data */
            $coreHelper = Mage::helper('core');
            $startDate = $coreHelper->formatDate($startDate, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, false);
        }

        $html .= Mage::app()->getLayout()->createBlock('core/html_date')
            ->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'))
            ->setFormat(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT))
            ->setName('order[estimate_send]')
            ->setValue($startDate)
            ->setId('estimateSend')
            ->setClass('datetime-picker input-text estimateSend')
            ->toHtml();

        $html .= '</td><td class="label">'. Mage::helper('payperrentals')->__('Time: ') . ' </td><td>';

        /** @var $helperTime ITwebexperts_Payperrentals_Helper_Timebox */
        $helperTime = Mage::helper('payperrentals/timebox');
        $hourStart = $helperTime->getTimeInput('estimateSendTime', $storeOpen, $storeClose, $excludeHoursStart, $timeSelected);

        $html .= $hourStart;
        $html .= '</td>';
        return $html;
    }

    /**
     * Returns pickup input as html table columns
     *
     * @param int $storeOpen
     * @param int $storeClose
     * @param array $excludeHoursEnd
     *
     * @return string
     */
    public function getPickupInput($storeOpen, $storeClose, $excludeHoursEnd){
        $html = '<td class="label">'. Mage::helper('payperrentals')->__('Pickup Date:') . ' </td><td>';
        if(Mage::getSingleton('adminhtml/session_quote')->getOrder()->getReturnDatetime()) {
            $endDate = Mage::getSingleton('adminhtml/session_quote')->getOrder()->getReturnDatetime();
        }elseif(Mage::registry('current_order') && Mage::registry('current_order')->getReturnDatetime()){
            $endDate = Mage::registry('current_order')->getReturnDatetime();
        }else{
            $endDate = '';
        }
        $timeSelected = false;
        if($endDate != ''){
            $timeSelectedArr = explode(' ', $endDate);
            if(isset( $timeSelectedArr[1])) {
                $timeSelected = $timeSelectedArr[1];
            }
            /** @var $coreHelper Mage_Core_Helper_Data */
            $coreHelper = Mage::helper('core');
            $endDate = $coreHelper->formatDate($endDate, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, false);
        }
        $html .= Mage::app()->getLayout()->createBlock('core/html_date')
            ->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'))
            ->setFormat(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT))
            ->setName('order[estimate_return]')
            ->setValue($endDate)
            ->setId('estimateReturn')
            ->setClass('datetime-picker input-text estimateReturn')
            ->toHtml();

        $html .= '</td><td class="label">'. Mage::helper('payperrentals')->__('Time: ') . ' </td><td>';

        $hourEnd = Mage::helper('payperrentals/timebox')->getTimeInput('estimateReturnTime', $storeOpen, $storeClose, $excludeHoursEnd, $timeSelected);

        $html .= $hourEnd;
        $html .= '</td>';
        return $html;
    }
}