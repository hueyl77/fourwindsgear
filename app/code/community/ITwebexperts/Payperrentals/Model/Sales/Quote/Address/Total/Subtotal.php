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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ITwebexperts_Payperrentals_Model_Sales_Quote_Address_Total_Subtotal extends Mage_Sales_Model_Quote_Address_Total_Subtotal
{
    /**
     * Address item initialization
     *
     * @param  $item
     * @return bool
     */
    protected function _initItem($address, $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $quoteItem = $item->getAddress()->getQuote()->getItemById($item->getQuoteItemId());
        } else {
            $quoteItem = $item;
        }
        $product = $quoteItem->getProduct();
        $product->setCustomerGroupId($quoteItem->getQuote()->getCustomerGroupId());

        /**
         * Quote super mode flag mean what we work with quote without restriction
         */
        if ($item->getQuote()->getIsSuperMode()) {
            if (!$product) {
                return false;
            }
        } else {
            if (!$product || !$product->isVisibleInCatalog()) {
                return false;
            }
        }
        $isReservationBundle = false;
        if ($quoteItem->getParentItem() && $quoteItem->getParentItem()->getProductType() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE) {
            $isReservation = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($quoteItem->getParentItem()->getProductId(), 'is_reservation');
            $bundlePriceType = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId(
                $quoteItem->getParentItem()->getProductId(), 'bundle_pricingtype'
            );

            if($isReservation != ITwebexperts_Payperrentals_Model_Product_Isreservation::STATUS_DISABLED
                && $bundlePriceType == ITwebexperts_Payperrentals_Model_Product_Bundlepricingtype::PRICING_BUNDLE_FORALL) {
                $isReservationBundle = true;
                if (!$quoteItem->getParentItem()->getIsCalculatedBundle()) {
                    $quoteItem->getParentItem()->setIsCalculatedBundle(true);
                    $finalPrice = $quoteItem->getParentItem()->getProduct()->getFinalPrice(
                        $quoteItem->getParentItem()->getQty()
                    );
                    $item->setPrice($finalPrice)
                        ->setBaseOriginalPrice($finalPrice);
                    /*This is needed because the price is already calculated for all the qtys*/
                    $item->setQty(1);
                    $item->calcRowTotal();
                }
            }
        }


        if (!$isReservationBundle) {
            if ($quoteItem->getParentItem() && $quoteItem->isChildrenCalculated()) {

                $finalPrice = $quoteItem->getParentItem()->getProduct()->getPriceModel()->getChildFinalPrice(
                    $quoteItem->getParentItem()->getProduct(),
                    $quoteItem->getParentItem()->getQty(),
                    $quoteItem->getProduct(),
                    $quoteItem->getQty()
                );
                $item->setPrice($finalPrice)
                    ->setBaseOriginalPrice($finalPrice);
                $item->calcRowTotal();

            } else if (!$quoteItem->getParentItem()) {
                $source = unserialize($quoteItem->getProduct()->getCustomOption('info_buyRequest')->getValue());
                $finalPrice = $product->getFinalPrice($quoteItem->getQty());
                $item->setData(ITwebexperts_Payperrentals_Helper_Price::DAMAGE_WAIVER_OPTION, 0);
                if ($product->getCustomOption(
                    ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION
                )
                ) {
                    if ($product->getTypeId() == 'configurable') {
                        $children = $item->getChildren();
                        if (count($children)) {
                            $damageProduct = $children[0]->getProduct();
                        } else {
                            $damageProduct = $product;
                        }
                    } else {
                        $damageProduct = $product;
                    }
                    $damageWaiverPrice = ITwebexperts_Payperrentals_Helper_Price::getDamageWaiver(
                        $damageProduct, $finalPrice
                    );

                    $item->setData(
                        ITwebexperts_Payperrentals_Helper_Price::DAMAGE_WAIVER_OPTION_PRICE, $damageWaiverPrice
                    );
                    $forceDamageWaiver = Mage::helper('payperrentals/config')->forceDamageWaiver();
                    if (isset($source[ITwebexperts_Payperrentals_Helper_Price::DAMAGE_WAIVER_OPTION]) || $forceDamageWaiver) {
                        if ((bool)$source[ITwebexperts_Payperrentals_Helper_Price::DAMAGE_WAIVER_OPTION] || $forceDamageWaiver) {
                            if ($damageWaiverPrice) {
                                $finalPrice += $damageWaiverPrice;
                                $item->setData(
                                    ITwebexperts_Payperrentals_Helper_Price::DAMAGE_WAIVER_OPTION, $damageWaiverPrice
                                );
                            }
                        }
                    }
                }

                $item->setPrice($finalPrice)
                    ->setBaseOriginalPrice($finalPrice);
                $item->calcRowTotal();
                $this->_addAmount($item->getRowTotal());
                $this->_addBaseAmount($item->getBaseRowTotal());
                $address->setTotalQty($address->getTotalQty() + $item->getQty());
            }
        }

        return true;
    }

    /**
     * Remove item
     *
     * @param  $address
     * @param  $item
     * @return Mage_Sales_Model_Quote_Address_Total_Subtotal
     */
    protected function _removeItem($address, $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Item) {
            $address->removeItem($item->getId());
            if ($address->getQuote()) {
                $address->getQuote()->removeItem($item->getId());
            }
        } elseif ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $address->removeItem($item->getId());
            if ($address->getQuote()) {
                $address->getQuote()->removeItem($item->getQuoteItemId());
            }
        }
        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Subtotal
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $address->addTotal(array(
            'code' => $this->getCode(),
            'title' => Mage::helper('payperrentals')->__('Subtotal'),
            'value' => $address->getSubtotal()
        ));
        return $this;
    }

    /**
     * Get Subtotal label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('payperrentals')->__('Subtotal');
    }
}
