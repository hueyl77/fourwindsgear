<?php
class ITwebexperts_Payperrentals_ExtendController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $newDate = ITwebexperts_Payperrentals_Helper_Date::toMysqlDate($this->getRequest()->getParam('new_date'), true);

        /** @var $sourceOrder Mage_Sales_Model_Order */
        $sourceOrder = Mage::getModel('sales/order')->load($orderId);

        $cart = Mage::getModel('checkout/cart');
        $cart->init();
        $cart->truncate();
        $orderItems = $sourceOrder->getAllItems();
        $configHelper = Mage::helper('payperrentals/config');

        foreach ($orderItems as $item){
            $timeIncrement = $configHelper->getTimeIncrement() * 60;
            if($item->getParentItem()){
                continue;
            }

            $originalEndDate = $item->getBuyRequest()->getEndDate();
            //check timeIncrement and check if product has times enabled
            if(ITwebexperts_Payperrentals_Helper_Data::useTimes($item->getProduct()->getId()) == 0){
                $timeIncrement = 24 * 60 * 60;
            }

            $originalEndDatePlusTimeIncrement = strtotime($originalEndDate) + $timeIncrement;
            $originalEndDatePlusTimeIncrement = date( 'Y-m-d H:i:s', $originalEndDatePlusTimeIncrement );

            $productOptions = $item->getProductOptions();
            $buyRequestArray = $productOptions['info_buyRequest'];

            $buyRequestArray['start_date'] = $originalEndDatePlusTimeIncrement;
            $buyRequestArray['end_date'] = $newDate;
            $buyRequestArray['is_extended'] = true;
            if(count($item->getChildrenItems()) > 0) {
                foreach ($item->getChildrenItems() as $child) {
                    $turnoverArr = ITwebexperts_Payperrentals_Helper_Data::getTurnoverFromQuoteItemOrBuyRequest($child->getProductId(), $child);
                    $buyRequestArray['excluded_qty'][] = array(
                        'product_id' => $child->getProductId(),
                        'start_date' => $turnoverArr['before'],
                        'end_date'   => $turnoverArr['after'],
                        'qty'        => $productOptions['info_buyRequest']['qty'],
                    );
                }
            }else{
                $turnoverArr = ITwebexperts_Payperrentals_Helper_Data::getTurnoverFromQuoteItemOrBuyRequest($item->getProductId(), $item);
                $buyRequestArray['excluded_qty'][] = array(
                    'product_id' => $item->getProductId(),
                    'start_date' => $turnoverArr['before'],
                    'end_date'   => $turnoverArr['after'],
                    'qty'        => $productOptions['info_buyRequest']['qty'],
                );
            }

            $buyRequest = new Varien_Object($buyRequestArray);

            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            try {
                $cart->addProduct($product, $buyRequest);
            }catch(Exception $e){
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }
        }
        $cart->save();
        Mage::getSingleton('checkout/session')->setIsExtendedQuote(true);
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        $this->_redirect('checkout/cart');

    }
}
