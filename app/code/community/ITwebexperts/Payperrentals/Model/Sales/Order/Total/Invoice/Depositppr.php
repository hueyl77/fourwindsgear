<?php

class ITwebexperts_Payperrentals_Model_Sales_Order_Total_Invoice_Depositppr extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return $this|Mage_Sales_Model_Order_Invoice_Total_Abstract
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {

        if(Mage::helper('payperrentals/config')->isChargedDeposit()) {
            $order = $invoice->getOrder();
            $feeAmountLeft = floatval($order->getDepositpprAmount() - $order->getDepositpprAmountInvoiced());
            $baseFeeAmountLeft = floatval($order->getBaseDepositpprAmount() - $order->getBaseDepositpprAmountInvoiced());
            if ($baseFeeAmountLeft > 0) {
                $invoice->setGrandTotal($invoice->getGrandTotal() + $feeAmountLeft);
                $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseFeeAmountLeft);
                $invoice->setDepositpprAmount($feeAmountLeft);
                $invoice->setBaseDepositpprAmount($baseFeeAmountLeft);
            }

            return $this;
        }

        return parent::collect($invoice);
    }
}
