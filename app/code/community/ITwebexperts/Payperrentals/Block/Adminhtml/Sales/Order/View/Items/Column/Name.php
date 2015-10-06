<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Order_View_Items_Column_Name
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Order_View_Items_Column_Name extends Mage_Adminhtml_Block_Sales_Items_Column_Name{

    /**
     * @return array
     */
    public function getOrderOptions(){
        $result = array();



        if ($options = $this->getItem()->getProductOptions()) {
                $newResult = Mage::helper('payperrentals/rendercart')->renderDates($options, $this->getItem());
                $result = array_merge($newResult, $result);
		$result = array_merge($result, parent::getOrderOptions());
        }
        return $result;
    }
}
