<?php
class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Returnlate_Renderer_Createlatefee extends
    Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $now = date("Y-m-d H:i:s");
        $data = $row->getData($this->getColumn()->getIndex());
        $htmlOut = '';

        $isDate = ITwebexperts_Payperrentals_Helper_Date::isFilteredDate($data);
        if(!$isDate || $isDate && strtotime($data) < strtotime($now)) {
            $button = "<button id=" . $row->getEntityId()
                . " onclick=\"showLateFeePopup('popup_form_policy',this.id)\")'>Charge Fee</button>";
            $lateFee = ITwebexperts_Payperrentals_Helper_LateFeesandReturns::calculateLateFeePriceForOrder($row->getOrderId(), null, null, $now);

            if($lateFee > 0) {
                $lateFee = Mage::helper('core')->currency($lateFee);
                $htmlOut = $button . '<br />' . $lateFee;
            }else{
                $htmlOut = '';
            }
        }

        return $htmlOut;

    }
}