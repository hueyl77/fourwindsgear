<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Checkout_Cart_Item_Renderer
 */
class ITwebexperts_Payperrentals_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer{

    /**
     * Return booking options array
     * @return array
     */
    protected function _getOptions(){

        $product = $this->getProduct();
        return Mage::helper('payperrentals/rendercart')->renderDates(null, $this->getItem(), $product, true);
    }

    /**
     * Accept option value and return its formatted view
     *
     * @param mixed $optionValue
     * Method works well with these $optionValue format:
     *      1. String
     *      2. Indexed array e.g. array(val1, val2, ...)
     *      3. Associative array, containing additional option info, including option value, e.g.
     *          array
     *          (
     *              [label] => ...,
     *              [value] => ...,
     *              [print_value] => ...,
     *              [option_id] => ...,
     *              [option_type] => ...,
     *              [custom_view] =>...,
     *          )
     *
     * @return array
     */

    public function getFormatedOptionValue($optionValue)
    {
        /* @var $helper Mage_Catalog_Helper_Product_Configuration */
        $helper = Mage::helper('catalog/product_configuration');
        $params = array(
            'max_length' => 1000,
            'cut_replacer' => ' <a href="#" class="dots" onclick="return false">...</a>'
        );
        return $helper->getFormattedOptionValue($optionValue, $params);
    }

    /**
     * Return merged options array
     * This array consist of standard Magento options and booking
     * @return array
     */
    public function getOptionList(){
        return array_merge($this->_getOptions(), parent::getOptionList());
    }
}