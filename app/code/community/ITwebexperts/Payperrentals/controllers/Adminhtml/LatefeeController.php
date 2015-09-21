<?php
class ITwebexperts_Payperrentals_Adminhtml_LatefeeController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return true;
    }

    public function indexAction()
    {
        $orderid = $this->getRequest()->getParam('orderid');
        if($this->getRequest()->getParam('late_date')) {
            $lateReturnDate = ITwebexperts_Payperrentals_Helper_Date::toMysqlDate(
                $this->getRequest()->getParam('late_date'), true
            );
        }else{
            $lateReturnDate = date('Y-m-d H:i:s');
        }

        //if($this->getRequest()->getParam('custom_price') && $this->getRequest()->getParam('custom_price') != '') {
          //  $customPrice = $this->getRequest()->getParam('custom_price');
        //}

        /** @var $sourceOrder Mage_Sales_Model_Order */
        $sourceOrder = Mage::getModel('sales/order')->load($orderid);

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
        /** @var $quote Mage_Sales_Model_Quote */
        //$quoteNew = $converterOrder->toQuote($sourceOrder);

        $orderShippingAddress = $converterOrder->addressToQuoteAddress($sourceOrder->getShippingAddress());
        $orderBillingAddress = $converterOrder->addressToQuoteAddress($sourceOrder->getBillingAddress());
      //$orderPayment = $converterOrder->paymentToQuotePayment($sourceOrder->getPayment());

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
            if(strtotime($originalEndDate) > strtotime($lateReturnDate)){
                continue;
            }

            //check timeIncrement and check if product has times enabled
            $useTimes = ITwebexperts_Payperrentals_Helper_Data::useTimes($item->getProduct()->getId());
            if($useTimes == 0){
                $timeIncrement = 24 * 60 * 60;
            }

            $originalEndDatePlusTimeIncrement = strtotime($originalEndDate) + $timeIncrement;
            $originalEndDatePlusTimeIncrement = date( 'Y-m-d H:i:s', $originalEndDatePlusTimeIncrement );

            $productOptions = $item->getProductOptions();
            $buyRequestArray = $productOptions['info_buyRequest'];

            $buyRequestArray['start_date'] = $originalEndDatePlusTimeIncrement;
            $buyRequestArray['end_date'] = ($useTimes == 0)? date('Y-m-d', strtotime($lateReturnDate)).' 00:00:00': $lateReturnDate;
            $buyRequestArray['is_extended'] = true;
            if(!isset($customPrice)) {
                $lateFee = ITwebexperts_Payperrentals_Helper_LateFeesandReturns::calculateLateFee(
                    $item->getProduct(), $item->getQtyOrdered(), $item->getBuyRequest()->getStartDate(),
                    $originalEndDate, $lateReturnDate, $buyRequestArray
                );
            }else{
                $lateFee = $customPrice;
            }

            $buyRequest = new Varien_Object();
            $buyRequest->setData($buyRequestArray);

            $product = Mage::getModel('catalog/product')->load($item->getProductId());

            $additionalOptions[] = array(
                'label' =>  'Type',
                'value' =>  'Late Fee'
            );

            $product->addCustomOption('additional_options', serialize($additionalOptions));
            $itemNew = $quote->addProduct($product, $buyRequest);

            //$itemNew = $quote->getItembyProduct($product);
            $itemNew->setCustomPrice($lateFee);
            $itemNew->calcRowTotal();
            $quote->collectTotals();
        }

        $quote->save();

        $orderSession->setIsExtendedQuote(true);
        $this->_redirect('adminhtml/sales_order_create/index');

    }

}