<?php
/**
 *
 * @author Enrique Piatti
 */ 
class ITwebexperts_Payperrentals_Model_CatalogInventory_Observer extends Mage_CatalogInventory_Model_Observer
{


    /**
     * Removes error statuses from quote and item, set by this observer
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @param int $code
     * @return Mage_CatalogInventory_Model_Observer
     */
    protected function _removeErrorsFromQuoteAndItem($item, $code)
    {
        //if(ITwebexperts_Payperrentals_Helper_Data::isReservationType($item->getProduct()) && ($item->getProduct()->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_CONFIGURABLE || $item->getProduct()->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE || $item->getProduct()->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_GROUPED)){
          //  return $this;
        //}
        if ($item->getHasError()) {
            $params = array(
                'origin' => 'cataloginventory',
                'code' => $code
            );
            $item->removeErrorInfosByParams($params);
        }

        $quote = $item->getQuote();
        $quoteItems = $quote->getItemsCollection();
        $canRemoveErrorFromQuote = true;

        foreach ($quoteItems as $quoteItem) {
            if ($quoteItem->getItemId() == $item->getItemId()) {
                continue;
            }

            $errorInfos = $quoteItem->getErrorInfos();
            foreach ($errorInfos as $errorInfo) {
                if ($errorInfo['code'] == $code) {
                    $canRemoveErrorFromQuote = false;
                    break;
                }
            }

            if (!$canRemoveErrorFromQuote) {
                break;
            }
        }

        if ($quote->getHasError() && $canRemoveErrorFromQuote) {
            $params = array(
                'origin' => 'cataloginventory',
                'code' => $code
            );
            $quote->removeErrorInfosByParams(null, $params);
        }

        return $this;
    }

    /**
     * Check product inventory data when quote item quantity declaring
     *
     * @param  Varien_Event_Observer $observer
     * @return Mage_CatalogInventory_Model_Observer
     */
    public function checkQuoteItemQty($observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        /* @var $quoteItem Mage_Sales_Model_Quote_Item */
        if (!$quoteItem || !$quoteItem->getProductId() || !$quoteItem->getQuote()
            || $quoteItem->getQuote()->getIsSuperMode()) {
            return $this;
        }

        /**
         * Get Qty
         */
        $qty = $quoteItem->getQty();

        /**
         * Check if product in stock. For composite products check base (parent) item stosk status
         */
        $stockItem = $quoteItem->getProduct()->getStockItem();
        $parentStockItem = false;
        if ($quoteItem->getParentItem()) {
            $parentStockItem = $quoteItem->getParentItem()->getProduct()->getStockItem();
        }
        if ($stockItem) {
            if (!$stockItem->getIsInStock() || ($parentStockItem && !$parentStockItem->getIsInStock())) {
                $quoteItem->addErrorInfo(
                    'cataloginventory',
                    Mage_CatalogInventory_Helper_Data::ERROR_QTY,
                    Mage::helper('cataloginventory')->__('This product is currently out of stock.')
                );
                $quoteItem->getQuote()->addErrorInfo(
                    'stock',
                    'cataloginventory',
                    Mage_CatalogInventory_Helper_Data::ERROR_QTY,
                    Mage::helper('cataloginventory')->__('Some of the products are currently out of stock.')
                );
                return $this;
            } else {
                // Delete error from item and its quote, if it was set due to item out of stock
                $this->_removeErrorsFromQuoteAndItem($quoteItem, Mage_CatalogInventory_Helper_Data::ERROR_QTY);
            }
        }

        /**
         * Check item for options
         */
        $options = $quoteItem->getQtyOptions();
        if ($options && $qty > 0) {
            $qty = $quoteItem->getProduct()->getTypeInstance(true)->prepareQuoteItemQty($qty, $quoteItem->getProduct());
            $quoteItem->setData('qty', $qty);

            if ($stockItem) {
                $result = $stockItem->checkQtyIncrements($qty);
                if ($result->getHasError()) {
                    $quoteItem->addErrorInfo(
                        'cataloginventory',
                        Mage_CatalogInventory_Helper_Data::ERROR_QTY_INCREMENTS,
                        $result->getMessage()
                    );

                    $quoteItem->getQuote()->addErrorInfo(
                        $result->getQuoteMessageIndex(),
                        'cataloginventory',
                        Mage_CatalogInventory_Helper_Data::ERROR_QTY_INCREMENTS,
                        $result->getQuoteMessage()
                    );
                } else {
                    // Delete error from item and its quote, if it was set due to qty problems
                    $this->_removeErrorsFromQuoteAndItem(
                        $quoteItem,
                        Mage_CatalogInventory_Helper_Data::ERROR_QTY_INCREMENTS
                    );
                }
            }

            $quoteItemHasErrors = false;
            foreach ($options as $option) {
                $optionValue = $option->getValue();
                /* @var $option Mage_Sales_Model_Quote_Item_Option */
                $optionQty = $qty * $optionValue;
                $increaseOptionQty = ($quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty) * $optionValue;

                $stockItem = $option->getProduct()->getStockItem();

                if ($quoteItem->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                    $stockItem->setProductName($quoteItem->getName());
                }

                /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
                if (!$stockItem instanceof Mage_CatalogInventory_Model_Stock_Item) {
                    Mage::throwException(
                        Mage::helper('cataloginventory')->__('The stock item for Product in option is not valid.')
                    );
                }

                /**
                 * define that stock item is child for composite product
                 */
                $stockItem->setIsChildItem(true);
                /**
                 * don't check qty increments value for option product
                 */
                $stockItem->setSuppressCheckQtyIncrements(true);

                $qtyForCheck = $this->_getQuoteItemQtyForCheck(
                    $option->getProduct()->getId(),
                    $quoteItem->getId(),
                    $increaseOptionQty
                );

                $result = $stockItem->checkQuoteItemQty($optionQty, $qtyForCheck, $optionValue, $quoteItem, $option->getProduct(), true);
                foreach($quoteItem->getChildren() as $children){
                    $children->setParentProductQty($qtyForCheck);
                    $children->setParentProductQtyOption(intval($qtyForCheck/$qty));
                }
                if (!is_null($result->getItemIsQtyDecimal())) {
                    $option->setIsQtyDecimal($result->getItemIsQtyDecimal());
                }

                if ($result->getHasQtyOptionUpdate()) {
                    $option->setHasQtyOptionUpdate(true);
                    $quoteItem->updateQtyOption($option, $result->getOrigQty());
                    $option->setValue($result->getOrigQty());
                    if($result->getQty()) {
                        $qty = $result->getQty();
                    }
                    /**
                     * if option's qty was updates we also need to update quote item qty
                     */
                    $quoteItem->setData('qty', intval($qty));
                }
                if (!is_null($result->getMessage())) {
                    $option->setMessage($result->getMessage());
                    $quoteItem->setMessage($result->getMessage());
                }
                if (!is_null($result->getItemBackorders())) {
                    $option->setBackorders($result->getItemBackorders());
                }

                if ($result->getHasError()) {
                    $option->setHasError(true);
                    $quoteItemHasErrors = true;

                    $quoteItem->addErrorInfo(
                        'cataloginventory',
                        Mage_CatalogInventory_Helper_Data::ERROR_QTY,
                        $result->getMessage()
                    );

                    $quoteItem->getQuote()->addErrorInfo(
                        $result->getQuoteMessageIndex(),
                        'cataloginventory',
                        Mage_CatalogInventory_Helper_Data::ERROR_QTY,
                        $result->getQuoteMessage()
                    );
                } elseif (!$quoteItemHasErrors) {
                    // Delete error from item and its quote, if it was set due to qty lack
                    $this->_removeErrorsFromQuoteAndItem($quoteItem, Mage_CatalogInventory_Helper_Data::ERROR_QTY);
                }

                $stockItem->unsIsChildItem();
            }
        } else {
            /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
            if (!$stockItem instanceof Mage_CatalogInventory_Model_Stock_Item) {
                Mage::throwException(Mage::helper('cataloginventory')->__('The stock item for Product is not valid.'));
            }

            /**
             * When we work with subitem (as subproduct of bundle or configurable product)
             */
            if ($quoteItem->getParentItem()) {
                $rowQty = $quoteItem->getParentItem()->getQty() * $qty;
                /**
                 * we are using 0 because original qty was processed
                 */
                $qtyForCheck = $this->_getQuoteItemQtyForCheck(
                    $quoteItem->getProduct()->getId(),
                    $quoteItem->getId(),
                    0
                );
            } else {
                $increaseQty = $quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty;
                $rowQty = $qty;
                $qtyForCheck = $this->_getQuoteItemQtyForCheck(
                    $quoteItem->getProduct()->getId(),
                    $quoteItem->getId(),
                    $increaseQty
                );
            }

            $productTypeCustomOption = $quoteItem->getProduct()->getCustomOption('product_type');
            if (!is_null($productTypeCustomOption)) {
                // Check if product related to current item is a part of grouped product
                if ($productTypeCustomOption->getValue() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
                    $stockItem->setProductName($quoteItem->getProduct()->getName());
                    $stockItem->setIsChildItem(true);
                }
            }

            $result = $stockItem->checkQuoteItemQty($rowQty, $qtyForCheck, $qty, $quoteItem, $quoteItem->getProduct());

            if ($stockItem->hasIsChildItem()) {
                $stockItem->unsIsChildItem();
            }

            if (!is_null($result->getItemIsQtyDecimal())) {
                $quoteItem->setIsQtyDecimal($result->getItemIsQtyDecimal());
                if ($quoteItem->getParentItem()) {
                    $quoteItem->getParentItem()->setIsQtyDecimal($result->getItemIsQtyDecimal());
                }
            }

            /**
             * Just base (parent) item qty can be changed
             * qty of child products are declared just during add process
             * exception for updating also managed by product type
             */
            if ($result->getHasQtyOptionUpdate()
                && (!$quoteItem->getParentItem()
                    || $quoteItem->getParentItem()->getProduct()->getTypeInstance(true)
                        ->getForceChildItemQtyChanges($quoteItem->getParentItem()->getProduct())
                )
            ) {
                $quoteItem->setData('qty', $result->getOrigQty());
            }

            if($result->getQty()) {
                $qty = $result->getQty();
                if($quoteItem->getParentItem()) {
                    $quoteItem->getParentItem()->setData('qty', intval($qty));
                    $quoteItem->getParentItem()->addErrorInfo(
                        'cataloginventory',
                        3,
                        $result->getMessage()
                    );
                    Mage::getSingleton('checkout/session')->addNotice(Mage::helper('payperrentals')->__('For some products in cart the quantities has been adjusted'));
                }else{
                    $quoteItem->setData('qty', intval($qty));
                    $quoteItem->addErrorInfo(
                        'cataloginventory',
                        3,
                        $result->getMessage()
                    );
                    Mage::getSingleton('checkout/session')->addNotice(Mage::helper('payperrentals')->__('For some products in cart the quantities has been adjusted'));
                }
            }

            if (!is_null($result->getItemUseOldQty())) {
                $quoteItem->setUseOldQty($result->getItemUseOldQty());
            }
            if (!is_null($result->getMessage())) {
                $quoteItem->setMessage($result->getMessage());
            }

            if (!is_null($result->getItemBackorders())) {
                $quoteItem->setBackorders($result->getItemBackorders());
            }

            if ($result->getHasError()) {
                if(!ITwebexperts_Payperrentals_Helper_Data::isUsingGlobalDatesShoppingCart()) {
                    $quoteItem->addErrorInfo(
                        'cataloginventory',
                        Mage_CatalogInventory_Helper_Data::ERROR_QTY,
                        $result->getMessage()
                    );
                }

                $quoteItem->getQuote()->addErrorInfo(
                    $result->getQuoteMessageIndex(),
                    'cataloginventory',
                    Mage_CatalogInventory_Helper_Data::ERROR_QTY,
                    $result->getQuoteMessage()
                );
            } else {
                // Delete error from item and its quote, if it was set due to qty lack
                $this->_removeErrorsFromQuoteAndItem($quoteItem, Mage_CatalogInventory_Helper_Data::ERROR_QTY);
            }
        }

        return $this;
    }


}
