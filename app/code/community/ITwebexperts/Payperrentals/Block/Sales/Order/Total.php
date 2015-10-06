<?php
/**
 * Class ITwebexperts_Payperrentals_Block_Sales_Order_Total
 */
class ITwebexperts_Payperrentals_Block_Sales_Order_Total extends Mage_Core_Block_Template
{
    /**
     * Get label cell tag properties
     *
     * @return string
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get order store object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get value cell tag properties
     *
     * @return string
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize reward points totals
     *
     * @return ITwebexperts_Payperrentals_Block_Sales_Order_Total
     */
    public function initTotals()
    {
        if ((float) $this->getOrder()->getBaseDamageWaiverAmount()) {
            $source = $this->getSource();
            $value  = $source->getDamageWaiverAmount();
            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'damage_waiver',
                'strong' => false,
                'label'  => Mage::helper('payperrentals')->__('Damage Waiver'),
                'value'  => $source instanceof Mage_Sales_Model_Order_Creditmemo ? - $value : $value
            )));
        }
        if ((float) $this->getOrder()->getBaseDepositpprAmount()) {
            $source = $this->getSource();
            $value  = $source->getDepositpprAmount();
            if(!($source instanceof Mage_Sales_Model_Order_Invoice)) {
            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'depositppr',
                'strong' => false,
                'label'  => Mage::helper('payperrentals')->__('Deposit'),
                'value'  => $source instanceof Mage_Sales_Model_Order_Creditmemo ? - $value : $value
            )));
            }
        }

        return $this;
    }
}
