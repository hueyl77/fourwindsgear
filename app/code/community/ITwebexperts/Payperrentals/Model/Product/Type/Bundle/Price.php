<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle Price Model
 *
 * @category Mage
 * @package  Mage_Bundle
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class ITwebexperts_Payperrentals_Model_Product_Type_Bundle_Price extends Mage_Bundle_Model_Product_Price
{

    /**
     * Get Total price  for Bundle items
     *
     * @param Mage_Catalog_Model_Product $product
     * @param null|float $qty
     * @return float
     */
    public function getTotalBundleItemsPrice($product, $qty = null)
    {
        $price = 0.0;
        if ($product->hasCustomOptions()) {
            $customOption = $product->getCustomOption('bundle_selection_ids');
            if ($customOption) {
                $selectionIds = unserialize($customOption->getValue());
                $selections = $product->getTypeInstance(true)->getSelectionsByIds($selectionIds, $product);
                $selections->addTierPriceData();
                Mage::dispatchEvent('prepare_catalog_product_collection_prices', array(
                    'collection' => $selections,
                    'store_id' => $product->getStoreId(),
                ));

                //I need to check the price base on an attribute added only to bundle type -- reservation price per product or general
                //for specific product it works on add to cart but not for general
                //here I use the parent or the children price base on

                foreach ($selections->getItems() as $selection) {
                    if ($selection->isSalable()) {
                        $selectionQty = $product->getCustomOption('selection_qty_' . $selection->getSelectionId());
                        if ($selectionQty) {

                            if ($selection->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE && $product->getBundlePricingtype() == ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype::PRICING_BUNDLE_FORALL) {
                                $price = 0;
                                if(is_object($product->getCustomOption('info_buyRequest'))) {
                                    $source = unserialize($product->getCustomOption('info_buyRequest')->getValue());
                                    if (!isset($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION])) {
                                        continue;
                                    }
                                    $startingDate
                                        = $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION];
                                    $endingDate
                                        = $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION];

                                    $customerGroup = ITwebexperts_Payperrentals_Helper_Data::getCustomerGroup();
                                    $price = $qty * ITwebexperts_Payperrentals_Helper_Price::calculatePrice(
                                        $product, $startingDate, $endingDate, $qty, $customerGroup
                                    );
                                    break;
                                }

                            } else if ($selection->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE) {

                                $Product = Mage::getModel('catalog/product')->load($selection->getProductId());
                                if(is_object($product->getCustomOption('info_buyRequest'))) {
                                    $source = unserialize($product->getCustomOption('info_buyRequest')->getValue());
                                    if(isset($source['start_date']) && isset($source['end_date'])) {
	                                    $customerGroup = ITwebexperts_Payperrentals_Helper_Data::getCustomerGroup();
	                                    $price = $price + $selectionQty->getValue() * ITwebexperts_Payperrentals_Helper_Price::calculatePrice(
	                                            $Product, $source['start_date'], $source['end_date'], $selectionQty,
	                                            $customerGroup
	                                        );
                                    }
                                }
                            } else {
                                $price += $this->getSelectionFinalTotalPrice($product, $selection, $qty,
                                    $selectionQty->getValue());
                            }
                        }
                    }
                }
            }
        }
        return $price;
    }

    /**
     * Get product final price
     *
     * @param   double $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  double
     */
    public function getFinalPrice($qty = null, $product)
    {
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = $this->getBasePrice($product, $qty);
        $product->setFinalPrice($finalPrice);
        Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $product, 'qty' => $qty));
        $finalPrice = $product->getData('final_price');

        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        $isReservation = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($product->getId(), 'is_reservation');
        $bundlePriceType = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
            $product->getId(), 'bundle_pricingtype'
        );
        if ($isReservation != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_DISABLED ) {
            if($bundlePriceType == ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype::PRICING_BUNDLE_FORALL) {

                if (is_object($product->getCustomOption('info_buyRequest'))) {
                    $source = unserialize($product->getCustomOption('info_buyRequest')->getValue());
                    if (isset($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION])) {
                        $startingDate
                            = $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION];
                        $endingDate
                            = $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION];
                    }
                }
                if (isset($startingDate) && isset($endingDate)) {
                    $customerGroup = ITwebexperts_Payperrentals_Helper_Data::getCustomerGroup();
                    $finalPrice = ITwebexperts_Payperrentals_Helper_Price::calculatePrice(
                        $product->getId(), $startingDate, $endingDate, $qty, $customerGroup
                    );
                }
            }else{
                $finalPrice = $this->getTotalBundleItemsPrice($product, $qty);
            }
        } else {
            $finalPrice += $this->getTotalBundleItemsPrice($product, $qty);
        }

        $product->setFinalPrice($finalPrice);
        return max(0, $product->getData('final_price'));
    }


}
