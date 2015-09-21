<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Sales_Order_Items_Renderer_Bundle
 */
class ITwebexperts_Payperrentals_Block_Sales_Order_Items_Renderer_Bundle extends Mage_Bundle_Block_Sales_Order_Items_Renderer
{

    /**
     * @return array
     */
    public function getItemOptions()
    {
	$result = array();
	if ($options = $this->getOrderItem()->getProductOptions()) {


            $newResult = Mage::helper('payperrentals/rendercart')->renderDates($options, $this->getOrderItem());
	    $result = array_merge($newResult, $result);
	    if (isset($options['options'])) {
		$result = array_merge($result, $options['options']);
	    }
	    if (isset($options['additional_options'])) {
		$result = array_merge($result, $options['additional_options']);
	    }
	    if (isset($options['attributes_info'])) {
		$result = array_merge($result, $options['attributes_info']);
	    }

	}
	return $result;
    }

    /**
     * @param $item
     * @return string
     */
    public function getValueHtml($item)
    {
        if ($attributes = $this->getSelectionAttributes($item)) {

            if ($item->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {
                $formattedPrice = Mage::helper('payperrentals/rendercart')->getBundleOptionPriceAsHtml($item, $this->getOrder());
            }else {
                $formattedPrice = $this->getOrder()->formatPrice($attributes['price']);
            }

            return sprintf('%d', $attributes['qty']) . ' x ' .
            $this->htmlEscape($item->getName()) .
            " " . $formattedPrice;


        } else {
            return $this->htmlEscape($item->getName());
        }
    }

}