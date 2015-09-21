<?php
class ITwebexperts_Payperrentals_Adminhtml_ExtendController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed(){
        return true;
    }

    public function indexAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $newDate = ITwebexperts_Payperrentals_Helper_Date::toMysqlDate($this->getRequest()->getParam('new_date'), true);


        /** @var $sourceOrder Mage_Sales_Model_Order */
        $sourceOrder = Mage::getModel('sales/order')->load($orderId);

        /** @var Mage_Adminhtml_Model_Session_Quote $orderSession */
        $orderSession = Mage::getSingleton('adminhtml/session_quote');
        $orderSession->clear();
        $customer = Mage::getModel('customer/customer')->load($sourceOrder->getCustomerId());
        $orderSession->setCustomer($customer);
        $orderSession->setCustomerId($sourceOrder->getCustomerId());
        $orderSession->setStoreId($sourceOrder->getStoreId());
        //$orderSession->setQuoteId($quote->getId());

        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $orderSession->getQuote();

        /** @var $converterOrder Mage_Sales_Model_Convert_Order */
        $converterOrder = Mage::getModel('sales/convert_order');

        $orderShippingAddress = $converterOrder->addressToQuoteAddress($sourceOrder->getShippingAddress());
        $orderBillingAddress = $converterOrder->addressToQuoteAddress($sourceOrder->getBillingAddress());
        // $orderPayment = $converterOrder->paymentToQuotePayment($sourceOrder->getPayment());

        $quote->setShippingAddress($orderShippingAddress);
        $quote->setBillingAddress($orderBillingAddress);
        //$quote->setPayment($orderPayment);

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

            $quote->addProduct($product, $buyRequest);
            $itemNew = $quote->getItembyProduct($product);
            $itemNew->calcRowTotal();
            $quote->collectTotals();
        }

        $quote->save();

        $orderSession->setIsExtendedQuote(true);
        $this->_redirect('adminhtml/sales_order_create/index');

    }

}