<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Order_View_Items_Renderer_Bundle
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Order_View_Items_Renderer_Bundle extends Mage_Bundle_Block_Adminhtml_Sales_Order_View_Items_Renderer{

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

    public function getValueHtml($item)
    {
        $result = $this->escapeHtml($item->getName());
        if (!$this->isShipmentSeparately($item)) {
            if ($attributes = $this->getSelectionAttributes($item)) {
                $result =  sprintf('%d', $attributes['qty']) . ' x ' . $result;
            }
        }

        if ($item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
            $result .= Mage::helper('payperrentals/rendercart')->getBundleOptionPriceAsHtml($item, $this->getItem()->getOrder());
        } else {
            if (!$this->isChildCalculated($item)) {
                if ($attributes = $this->getSelectionAttributes($item)) {
                    $result .= " " . $this->getItem()->getOrder()->formatPrice($attributes['price']);
                }
            }
        }
        return $result;
    }
}
