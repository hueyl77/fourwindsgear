<?php
class ITwebexperts_Payperrentals_Helper_LateFeesandReturns extends Mage_Core_Helper_Abstract
{
    /**
     * Calculate late fee for 1 product
     *
     * @param $product
     * @param $qty
     * @param $originalStartDate
     * @param $originalEndDate
     * @param $lateReturnDate
     * @param $options
     * @return int
     */

    public static function calculateLateFee($product, $qty, $originalStartDate, $originalEndDate, $lateReturnDate, $options = null)    {

        $originalRentalPrice = ITwebexperts_Payperrentals_Helper_Price::getPriceForAnyProductType($product, isset($options['attributes'])?$options['attributes']:null, isset($options['bundle_option'])?$options['bundle_option']:null, isset($options['bundle_option_qty1'])?$options['bundle_option_qty1']:null, isset($options['bundle_option_qty'])?$options['bundle_option_qty']:null, $originalStartDate, $originalEndDate, $qty);
        $rentalPriceIncludingLateTime = ITwebexperts_Payperrentals_Helper_Price::getPriceForAnyProductType($product, isset($options['attributes'])?$options['attributes']:null, isset($options['bundle_option'])?$options['bundle_option']:null, isset($options['bundle_option_qty1'])?$options['bundle_option_qty1']:null, isset($options['bundle_option_qty'])?$options['bundle_option_qty']:null, $originalStartDate, $lateReturnDate, $qty);
        $lateFeePrice = $rentalPriceIncludingLateTime - $originalRentalPrice;
        return $lateFeePrice;
    }

    /**
     * Calculate late fee for entire order
     * @param      $orderId
     * @param null $originalStartDate
     * @param null $originalEndDate
     * @param      $lateReturnDate
     *
     * @return int
     */

    public static function calculateLateFeePriceForOrder($orderId, $originalStartDate = null, $originalEndDate = null, $lateReturnDate)
    {
        $lateFee = 0;
        $items = Mage::getModel('sales/order')->load($orderId)->getAllItems();
        foreach($items as $item){
            $productOptions = $item->getProductOptions();
            $buyRequestArray = $productOptions['info_buyRequest'];
            if(is_null($originalStartDate))
            {
                $originalStartDate = $buyRequestArray['start_date'];
            }
            if(is_null($originalEndDate))
            {
                $originalEndDate = $buyRequestArray['end_date'];
            }
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $qty = $item->getQtyOrdered();
            $lateFee += self::calculateLateFee($product, $qty, $originalStartDate ,$originalEndDate, $lateReturnDate, $buyRequestArray);
        }
        return $lateFee;
    }
}