<?php

class ITwebexperts_Rshipping_Model_Source_ShippingAmountType
{
    const PER_CART_TYPE = 0;
    const PER_PRODUCT_TYPE = 1;

    private $_options = array(
        self::PER_CART_TYPE => 'Per cart',
        self::PER_PRODUCT_TYPE => 'Per product'
    );

    /**
     * Get shipping amount price type
     * @return array
     */
    public function toOptionArray()
    {
        $returnOptions = array();
        $options = $this->_options;
        $helper = Mage::helper('rshipping');
        foreach ($options as $value => $label) {
            $returnOptions[] = array(
                'value' => $value,
                'label' => $helper->__($label)
            );
        }

        return $returnOptions;
    }

    /**
     * Get Cart Amount Type
     * @return int
     */
    public function getCartAmountType()
    {
        return self::PER_CART_TYPE;
    }

    /**
     * Get Product Amount Type
     * @return int
     */
    public function getProductAmountType()
    {
        return self::PER_PRODUCT_TYPE;
    }
}