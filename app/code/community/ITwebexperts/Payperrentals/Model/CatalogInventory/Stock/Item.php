<?php

class ITwebexperts_Payperrentals_Model_CatalogInventory_Stock_Item extends Mage_CatalogInventory_Model_Stock_Item
{
    /**
     * override for changing the current stock qty based on the quote_item
     * (this is a bad design from Magento, checkQuoteItemQty should receive the quoteItem in the original code too)
     *
     * @param mixed $qty
     * @param mixed $summaryQty
     * @param int $origQty
     * @param Mage_Sales_Model_Quote_Item $quoteItem
     *
     * @return Varien_Object
     */
    public function checkQuoteItemQty($qty, $summaryQty, $origQty = 0, $quoteItem = null, $product = null)
    {
        if ($quoteItem && ITwebexperts_Payperrentals_Helper_Data::isReservationOnly($product) && (!$quoteItem->getChildren() || Mage::app()->getStore()->isAdmin())) {
            $qtyOption = 1;
            if($quoteItem->getParentProductQty()){
                $qty = $quoteItem->getParentProductQty();
                $qtyOption = $quoteItem->getParentProductQtyOption();
            }
            $quoteItemId = $quoteItem->getId();
            if($quoteItem->getParentItem() && $quoteItem->getParentItem()->getProduct()){
                if($quoteItem->getParentItem()->getProduct()->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE_BUNDLE){
                    $quoteItemId = $quoteItem->getParentItem()->getItemId();
                }
            }


            $turnoverArr = ITwebexperts_Payperrentals_Helper_Data::getTurnoverFromQuoteItemOrBuyRequest(
                $product, $quoteItem
            );
            list($startDate, $endDate) = array($turnoverArr['before'], $turnoverArr['after']);

            $result = new Varien_Object();
            $result->setHasError(false);

            if (!is_numeric($qty)) {
                $qty = Mage::app()->getLocale()->getNumber($qty);
            }

            /**
             * Check quantity type
             */
            $result->setItemIsQtyDecimal($this->getIsQtyDecimal());

            if (!$this->getIsQtyDecimal()) {
                $result->setHasQtyOptionUpdate(true);
                $qty = intval($qty);
                /**
                 * Adding stock data to quote item
                 */
                $result->setItemQty($qty);

                if (!is_numeric($qty)) {
                    $qty = Mage::app()->getLocale()->getNumber($qty);
                }
                $origQty = intval($origQty);
                $result->setOrigQty($origQty);
            }
            if ($qty < ITwebexperts_Payperrentals_Helper_Inventory::getMinSaleQuantity($product)) {
                $result->setHasError(true)
                    ->setMessage(
                        Mage::helper('cataloginventory')->__(
                            'The minimum quantity allowed for purchase is %s.', ITwebexperts_Payperrentals_Helper_Inventory::getMinSaleQuantity($product)
                        )
                    )
                    ->setErrorCode('qty_min')
                    ->setQuoteMessage(
                        Mage::helper('cataloginventory')->__(
                            'Some of the products cannot be ordered in requested quantity.'
                        )
                    )
                    ->setQuoteMessageIndex('qty');
                return $result;
            }

            if ($qty > ITwebexperts_Payperrentals_Helper_Inventory::getMaxSaleQuantity($product)) {
                $result->setHasError(true)
                    ->setMessage(
                        Mage::helper('cataloginventory')->__(
                            'The maximum quantity allowed for purchase is %s.', ITwebexperts_Payperrentals_Helper_Inventory::getMaxSaleQuantity($product)
                        )
                    )
                    ->setErrorCode('qty_max')
                    ->setQuoteMessage(
                        Mage::helper('cataloginventory')->__(
                            'Some of the products cannot be ordered in requested quantity.'
                        )
                    )
                    ->setQuoteMessageIndex('qty');
                return $result;
            }

            $result->addData($this->checkQtyIncrements($qty)->getData());
            if ($result->getHasError()) {
                return $result;
            }

            $option = $quoteItem->getOptionByCode('info_buyRequest');
            $buyRequest = $option ? unserialize($option->getValue()) : null;
            if(Mage::app()->getStore()->isAdmin() && Mage::getSingleton('adminhtml/session_quote')->getOrderId()){
                $editedOrderId = Mage::getSingleton('adminhtml/session_quote')->getOrderId();
                $order = Mage::getModel('sales/order')->load($editedOrderId);
                $buyRequestArray = (array)$buyRequest;
                foreach ($order->getAllItems() as $oItem) {
                    if($oItem->getProduct()->getId() != $product->getId()) continue;

                    if (is_object($oItem->getOrderItem())) {
                        $item = $oItem->getOrderItem();
                    } else {
                        $item = $oItem;
                    }
                    if ($item->getParentItem()) {
                        continue;
                    }
                    //check for bundles
                    if(count($item->getChildrenItems()) > 0) {
                        foreach ($item->getChildrenItems() as $child) {
                            $turnoverArr = ITwebexperts_Payperrentals_Helper_Data::getTurnoverFromQuoteItemOrBuyRequest($child->getProductId(), $child);
                            $buyRequestArray['excluded_qty'][] = array(
                                'product_id' => $child->getProductId(),
                                'start_date' => $turnoverArr['before'],
                                'end_date'   => $turnoverArr['after'],
                                'qty'        => $item->getQtyOrdered(),
                            );
                        }
                    }else{
                        $turnoverArr = ITwebexperts_Payperrentals_Helper_Data::getTurnoverFromQuoteItemOrBuyRequest($item->getProductId(), $item);
                        $buyRequestArray['excluded_qty'][] = array(
                            'product_id' => $item->getProductId(),
                            'start_date' => $turnoverArr['before'],
                            'end_date'   => $turnoverArr['after'],
                            'qty'        => $item->getQtyOrdered(),
                        );
                    }
                }
                $buyRequest = new Varien_Object($buyRequestArray);

            }
            if(isset($buyRequest['start_date'])
            && isset($buyRequest['end_date'])) {
                if(strtotime($buyRequest['start_date']) > strtotime($buyRequest['end_date'])){
                    $message = Mage::helper('payperrentals')->__(
                        'Start Date is bigger then End Date. Please change!'
                    );
                    $result->setHasError(true)
                        ->setMessage($message)
                        ->setQuoteMessage($message);
                    return $result;
                }
            }

            $isAvailable = false;
            if (ITwebexperts_Payperrentals_Helper_Inventory::isAllowedOverbook($product->getId())) {
                $isAvailable = true;
            }

            if(ITwebexperts_Payperrentals_Helper_Data::isBuyout($buyRequest)){
                if(!$isAvailable) {
                    Mage::register('quote_item_id', $quoteItemId);
                    $maxQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity(
                        $product, null, null, $buyRequest
                    );
                    Mage::unregister('quote_item_id');
                    if (($maxQty) >= $qty) {
                        $isAvailable = true;
                    }
                }
                if($isAvailable){
                    return $result;
                }else{
                    $message = Mage::helper('payperrentals')->__(
                        'There is not enough quantity for buy this product'
                    );
                    $result->setHasError(true)
                        ->setMessage($message)
                        ->setQuoteMessage($message);
                    return $result;
                }
            }
            if (!Mage::app()->getStore()->isAdmin() && !Mage::getSingleton('checkout/session')->getIsExtendedQuote()
                && isset($buyRequest['start_date'])
                && isset($buyRequest['end_date'])
                && ITwebexperts_Payperrentals_Helper_Inventory::isExcludedDay(
                    $product->getId(), $buyRequest['start_date'], $buyRequest['end_date']
                )
            ) {
                $message = Mage::helper('payperrentals')->__(
                    'There are blocked dates or excluded days on your selected dates!'
                );
                $result->setHasError(true)
                    ->setMessage($message)
                    ->setQuoteMessage($message);
                return $result;
            } else {
                if (!$isAvailable
                    && isset($buyRequest['start_date'])
                    && isset($buyRequest['end_date'])) {
                    Mage::register('quote_item_id', $quoteItemId);
                    $maxQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity(
                        $product, $startDate, $endDate, $buyRequest
                    );
                    Mage::unregister('quote_item_id');
                    if ($maxQty >= $qty) {
                        $isAvailable = true;
                    }

                }
            }

            if (ITwebexperts_Payperrentals_Helper_Data::isUsingGlobalDatesShoppingCart($product) && !($buyRequest['start_date'])) {
                $message = Mage::helper('payperrentals')->__(
                    'Please select Global Dates!'
                );
                $result->setHasError(true)
                    ->setMessage($message)
                    ->setQuoteMessage($message);
                return $result;
            }

            if ((!Mage::app()->getStore()->isAdmin() && !ITwebexperts_Payperrentals_Helper_Data::isBuyout($buyRequest))
                && (isset($buyRequest['start_date'])
                    && strtotime($buyRequest['start_date']) < strtotime(date('Y-m-d'))
                    || isset($buyRequest['end_date']) && strtotime($buyRequest['end_date']) < strtotime(date('Y-m-d')))
            ) {
                $message = Mage::helper('payperrentals')->__(
                    'The selected Dates are in the past. Please select new dates!'
                );
                $result->setHasError(true)
                    ->setMessage($message)
                    ->setQuoteMessage($message);
                return $result;
            }

                    if(Mage::app()->getStore()->isAdmin() && ITwebexperts_Payperrentals_Helper_Inventory::isAllowedOverbook($product->getId()) && ITwebexperts_Payperrentals_Helper_Inventory::showAdminOverbookWarning()) {
                        Mage::register('quote_item_id', $quoteItemId);
                        $maxQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity(
                            $product, $startDate, $endDate, $buyRequest, true
                        );
                        Mage::unregister('quote_item_id');
                        if ($maxQty < $qty) {
                            $message = Mage::helper('cataloginventory')->__(
                                'Product is not available for the selected dates'
                            );

                            $result->setHasError(false)
                                ->setMessage($message)
                                ->setQuoteMessage($message)
                                ->setQuoteMessageIndex('qtyppr');
                            return $result;
                        }

                    }

            Mage::dispatchEvent('before_stock_check', array(
                'buyrequest' => $buyRequest,
                'isavailable' => &$isAvailable
            ));

            if (!$isAvailable) {
                if(isset($buyRequest['start_date'])
                && isset($buyRequest['end_date'])) {
                        //Mage::register('no_quote', 1);
                        Mage::register('quote_item_id', $quoteItemId);
                        $maxQty = ITwebexperts_Payperrentals_Helper_Inventory::getQuantity(
                            $product, $startDate, $endDate, $buyRequest
                        );
                        Mage::unregister('quote_item_id');
                        $maxQty = intval($maxQty/$qtyOption);
                        //Mage::unregister('no_quote', 1);
                    if($maxQty > 0) {
                        $message = Mage::helper('payperrentals')->__(
                            'Max quantity available for these dates is: ' . $maxQty . ', your quantity has been adjusted.'
                        );
                       // Mage::getSingleton('checkout/session')->addError($message);

                        $result->setHasQtyOptionUpdate(true);
                        //$result->setOrigQty($maxQty);
                        $result->setQty($maxQty);
                        $result->setItemQty($maxQty);
                        $result->setMessage($message)
                            ->setQuoteMessage($message)
                        ;

                        return $result;
                    }

                    $message = Mage::helper('cataloginventory')->__(
                        'The requested quantity for "%s" is not available.', $this->getProductName()
                    );
                }else{
                    $message = Mage::helper('cataloginventory')->__(
                        'Please select Start and End Dates'
                    );
                }
                $result->setHasError(true)
                    ->setMessage($message)
                    ->setQuoteMessage($message)
                    ->setQuoteMessageIndex('qtyppr');
                return $result;
            }

            return $result;
        }
        $return = parent::checkQuoteItemQty($qty, $summaryQty, $origQty);
        return $return;
    }


    protected function _beforeSave()
    {
        return parent::_beforeSave();
    }

}