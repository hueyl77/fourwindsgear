<?php

class ITwebexperts_Payperrentals_Model_Product_Price extends Mage_Catalog_Model_Product_Type_Price {

	public function getPrice($product, $qty = null){
		$isBuyout = false;
		if($product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION)) {
			$from_date = $product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION)->getValue();
		}
		if($product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION)) {
			$to_date = $product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION)->getValue();
		}
		if ($product->getCustomOption(
			ITwebexperts_Payperrentals_Model_Product_Type_Reservation::BUYOUT_PRICE_OPTION
		)
		) {
			$isBuyout = $product->getCustomOption(
				ITwebexperts_Payperrentals_Model_Product_Type_Reservation::BUYOUT_PRICE_OPTION
			)->getValue();
		}

		$price = 0;
		if(is_null($qty) || $qty <= 0){
			$qty = 1;
		}
		$customerGroup = $this->_getCustomerGroupId($product);
		if(isset($from_date) && isset($to_date)){
			$price =  ITwebexperts_Payperrentals_Helper_Price::calculatePrice($product, $from_date, $to_date, $qty, $customerGroup);
			$product->setData('final_price', $price);
		}

		if ($isBuyout) {
			$priceTemp = ITwebexperts_Payperrentals_Helper_Price::getBuyoutPrice($product);
			$product->setData('final_price', $priceTemp);
			$price = $priceTemp;
		}
		
		return $price;
	}

	public function getFinalPrice($qty = null, $product){
		$resPrice = $this->getPrice($product, $qty);

		$optPrice = $this->getOptionsPrice($product, $resPrice);
		if($product->getPayperrentalsHasMultiply() == ITwebexperts_Payperrentals_Model_Product_Hasmultiply::STATUS_ENABLED && !is_null($qty)){
			$resPrice += $optPrice * $qty;
		}else{
			$resPrice += $optPrice;
		}
		return $resPrice;
	}
    public function getOptionsPrice($product, $price)
    {
        $optprice = 0;
        if ($optionIds = $product->getCustomOption('option_ids')) {
            $basePrice = $price;
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {

                    $quoteItemOption = $product->getCustomOption('option_'.$option->getId());
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setQuoteItemOption($quoteItemOption);

                    $optprice += $group->getOptionPrice($quoteItemOption->getValue(), $basePrice);
                }
            }
        }

        return $optprice;
    }
}
