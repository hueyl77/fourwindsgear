<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Sales_Order_Items_Renderer
 */
class ITwebexperts_Payperrentals_Block_Sales_Order_Items_Renderer extends Mage_Sales_Block_Order_Item_Renderer_Default
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

}