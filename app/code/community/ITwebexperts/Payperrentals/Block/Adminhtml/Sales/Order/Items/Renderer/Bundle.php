<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Order_Items_Renderer_Bundle
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Sales_Order_Items_Renderer_Bundle extends Mage_Bundle_Block_Adminhtml_Sales_Order_Items_Renderer
{
    /**
     * @return array
     */
    public function getOrderOptions($item = null){
        $result = array();

        if ($options = $this->getOrderItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }

        if ($options = $this->getOrderItem()->getProductOptions()) {
            $newResult = Mage::helper('payperrentals/rendercart')->renderDates($options);
            $result = array_merge($newResult, $result);
        }
        return $result;
    }
    /**
     * @param $item
     * @return string
     */
    public function getValueHtml($item)
    {
        $result = $this->htmlEscape($item->getName());
        if (!$this->isShipmentSeparately($item)) {
            if ($attributes = $this->getSelectionAttributes($item)) {
                $result =  sprintf('%d', $attributes['qty']) . ' x ' . $result;
            }
        }
        if ($item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
            $result .= Mage::helper('payperrentals/rendercart')->getBundleOptionPriceAsHtml($item, $this->getOrderItem()->getOrder());
        } else {
            if (!$this->isChildCalculated($item)) {
                if ($attributes = $this->getSelectionAttributes($item)) {
                    $result .= " " . $this->getOrderItem()->getOrder()->formatPrice($attributes['price']);
                }
            }
        }
        return $result;
    }
}
