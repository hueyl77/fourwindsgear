<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Returnlate_Renderer_Dayslate extends
    Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $mageTimeStamp = Mage::getModel('core/date')->timestamp(time());
        $endDateTimestamp = strtotime($row->getEndDatetime());
        $lateDifferenceInSeconds =  $mageTimeStamp - $endDateTimestamp;
        $daysLate = $lateDifferenceInSeconds / (60 * 60 * 24);
        $daysLateRounded = round($daysLate);
        return $daysLateRounded;
    }
}