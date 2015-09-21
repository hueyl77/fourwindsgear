<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Checkout_Cart_Item_Renderer
 */
class ITwebexperts_Payperrentals_Block_Checkout_Item_Renderer extends ITwebexperts_Payperrentals_Block_Checkout_Cart_Item_Renderer {

    protected function _getDamageWaiverHtml($damageWaiver)
    {
        return ITwebexperts_Payperrentals_Helper_Price::getDamageWaiverHtml($this->getItem(), $damageWaiver, (bool)$this->getItem()->getBuyRequest()->getDamageWaiver(), false);
    }

}
