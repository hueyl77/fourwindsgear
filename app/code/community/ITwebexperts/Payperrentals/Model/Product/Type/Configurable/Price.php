<?php

class ITwebexperts_Payperrentals_Model_Product_Type_Configurable_Price
    extends Mage_Catalog_Model_Product_Type_Configurable_Price
{

    public function getPrice($product, $qty = null)
    {
        if (ITwebexperts_Payperrentals_Helper_Data::isReservationAndRental($product)) {
            $selectedAttributes = array();
            $isBuyout = false;
            $configurableParent = null;
            if ($product->getCustomOption('attributes')) {
                $selectedAttributes = unserialize($product->getCustomOption('attributes')->getValue());
            }

            if ($product->getCustomOption(
                ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION
            )
            ) {
                $from_date = $product->getCustomOption(
                    ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION
                )->getValue();
            }

            if ($product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION)) {
                $to_date = $product->getCustomOption(
                    ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION
                )->getValue();
            }

            if ($product->getCustomOption(
                ITwebexperts_Payperrentals_Model_Product_Type_Reservation::BUYOUT_PRICE_OPTION
            )
            ) {
                $isBuyout = $product->getCustomOption(
                    ITwebexperts_Payperrentals_Model_Product_Type_Reservation::BUYOUT_PRICE_OPTION
                )->getValue();
                $configurableParent = $product;
            }

            if ($product->isConfigurable()) {
                $product = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes(
                    $selectedAttributes, $product
                );
                if (is_object($product)) {
                    $product = Mage::getModel('catalog/product')->load($product->getId());
                }
            }

            $price = 0;
            if (is_null($qty) || $qty <= 0) {
                $qty = 1;
            }

            if (isset($from_date) && isset($to_date) && is_object($product)) {
                $customerGroup = $this->_getCustomerGroupId($product);
                $price = ITwebexperts_Payperrentals_Helper_Price::calculatePrice(
                    $product, $from_date, $to_date, $qty, $customerGroup
                );
                $product->setData('final_price', $price);
            }

            if ($isBuyout) {
                $priceTemp = ITwebexperts_Payperrentals_Helper_Price::getBuyoutPrice($product, $configurableParent);
                $product->setData('final_price', $priceTemp);
                $price = $priceTemp;
            }

            return $price;
        } else {
            return parent::getPrice($product, $qty);
        }
    }

    public function getFinalPrice($qty = null, $product)
    {
        if (ITwebexperts_Payperrentals_Helper_Data::isReservationAndRental($product)) {
            $resPrice = $this->getPrice($product, $qty);

            $optPrice = $this->getOptionsPrice($product, 0);
            if ($product->getPayperrentalsHasMultiply() && !is_null($qty)) {
                $resPrice += $optPrice * $qty;
            } else {
                $resPrice += $optPrice;
            }
            return $resPrice;
        } else {
            return parent::getFinalPrice($qty, $product);
        }
    }

    public function getOptionsPrice($product, $price)
    {
        if (ITwebexperts_Payperrentals_Helper_Data::isReservationAndRental($product)) {
            $optprice = 0;
            if ($optionIds = $product->getCustomOption('option_ids')) {
                $basePrice = $price;
                foreach (explode(',', $optionIds->getValue()) as $optionId) {
                    if ($option = $product->getOptionById($optionId)) {

                        $quoteItemOption = $product->getCustomOption('option_' . $option->getId());
                        $group = $option->groupFactory($option->getType())
                            ->setOption($option)
                            ->setQuoteItemOption($quoteItemOption);

                        $optprice += $group->getOptionPrice($quoteItemOption->getValue(), $basePrice);
                    }
                }
            }

            return $optprice;
        } else {
            return parent::getOptionsPrice($product, $price);
        }
    }
}
