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
class ITwebexperts_Payperrentals_Model_Sales_Quote_Address_Total_Depositppr extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    protected $_code = 'depositppr';

    public function __construct()
    {
        $this->setCode('depositppr');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $address->setDepositpprAmount(0);
        $address->setBaseDepositpprAmount(0);

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this; //this makes only address type shipping to come through
        }
        $quote = $address->getQuote();
        $quote->setDepositpprAmount(0);

        $depositAmt = ITwebexperts_Payperrentals_Helper_Data::getDeposit($quote);
        if ($depositAmt > 0) {
            $address->setDepositpprAmount($depositAmt);
            $address->setBaseDepositpprAmount($depositAmt);

            $quote->setDepositpprAmount($depositAmt);
            if(Mage::helper('payperrentals/config')->isChargedDeposit()) {
                $address->setGrandTotal($address->getGrandTotal() + $address->getDepositpprAmount());
                $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseDepositpprAmount());
            }
        }

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        //
        if ($address->getDepositpprAmount() > 0) {
            $depositTitle = Mage::helper('payperrentals')->__('Deposit');
            $amt = $address->getDepositpprAmount();
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => $depositTitle,
                'value' => $amt
            ));
        }
        return $this;
    }

    public function getLabel()
    {
        $depositTitle = Mage::helper('payperrentals')->__('Deposit');

        return $depositTitle;
    }

}
