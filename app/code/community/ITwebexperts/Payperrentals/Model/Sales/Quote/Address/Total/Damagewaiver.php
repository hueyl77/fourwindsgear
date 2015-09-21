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


class ITwebexperts_Payperrentals_Model_Sales_Quote_Address_Total_Damagewaiver extends Mage_Sales_Model_Quote_Address_Total_Abstract {
    protected $_code = 'damagewaiver';

    public function __construct()
    {
        $this->setCode('damagewaiver');
    }
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $address->setDamageWaiverAmount(0);
        $address->setBaseDamageWaiverAmount(0);

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this; //this makes only address type shipping to come through
        }
        $quote = $address->getQuote();
        $quote->setDamageWaiverAmount(0);

        $depositAmt = ITwebexperts_Payperrentals_Helper_Price::getQuoteDamageWaiver($quote);
        if($depositAmt > 0){
            $exist_amount = $quote->getDamageWaiverAmount();
            $fee = $depositAmt;
            $balance = $fee - $exist_amount;

            $address->setDamageWaiverAmount($balance);
            $address->setBaseDamageWaiverAmount($balance);

            $quote->setDamageWaiverAmount($balance);
        } else {
            $address->setDamageWaiverAmount(0);
            $address->setBaseDamageWaiverAmount(0);

            $quote->setDamageWaiverAmount(0);
        }

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if ($address->getDamageWaiverAmount() > 0) {
            $address->addTotal(array(
                'code'=>$this->getCode(),
                'title'=>$this->getLabel(),
                'value'=> $address->getDamageWaiverAmount()
            ));
        }

        return $this;
    }

    public function getLabel()
    {
        return Mage::helper('payperrentals')->__('Damage Waiver');
    }

}
