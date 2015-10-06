<?php
class ITwebexperts_Payperrentals_Block_Checkout_onepage_review_Signature extends Mage_Core_Block_Template
{
    public function getRentalcontract()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $vars['customerfirst'] = $quote->getCustomerFirstname();
        $vars['customerlast'] = $quote->getCustomerLastname();
        $terms = Mage::getStoreConfig('payperrentals/contract/terms');
        $processor = Mage::getModel('core/email_template');
        $processor->setTemplateText($terms);
        $terms = $processor->getProcessedTemplate($vars);
        return $terms;
    }

}