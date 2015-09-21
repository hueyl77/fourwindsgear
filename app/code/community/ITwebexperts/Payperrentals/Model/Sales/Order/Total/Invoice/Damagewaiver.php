<?php
class ITwebexperts_Payperrentals_Model_Sales_Order_Total_Invoice_Damagewaiver extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();

        $feeAmountLeft = floatval($order->getDamageWaiverAmount() - $order->getDamageWaiverAmountInvoiced());
        $baseFeeAmountLeft = floatval($order->getBaseDamageWaiverAmount() - $order->getBaseDamageWaiverAmountInvoiced());

        if($baseFeeAmountLeft > 0){
            $invoice->setDamageWaiverAmount($feeAmountLeft);
            $invoice->setBaseDamageWaiverAmount($baseFeeAmountLeft);
        }
        return $this;
    }
}
