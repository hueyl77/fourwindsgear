<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Catalog_Product_View
 */
class ITwebexperts_Payperrentals_Block_Catalog_Product_View_Rental extends ITwebexperts_Payperrentals_Block_Catalog_Product_View
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        if (!$this->getTemplate()) {
            $this->setTemplate('payperrentals/catalog/product/view_rental.phtml');
        }
    }

    /**
     * @return string
     */
    public function getCustomOptions()
    {
        $customOptions = array();
        $customOptionsColl = Mage::getModel('catalog/product_option')->getCollection();
        $customOptionsColl->getSelect()
            ->joinLeft(array('type_value' => $customOptionsColl->getTable('catalog/product_option_type_value')), 'main_table.option_id = type_value.option_id', array('option_type_id' => 'type_value.option_type_id'))
            ->joinLeft(array('type_price' => $customOptionsColl->getTable('catalog/product_option_type_price')), 'type_value.option_type_id = type_price.option_type_id', array('price' => 'type_price.price', 'price_type' => 'type_price.price_type'))
            ->where('main_table.product_id = ?', $this->getProduct()->getId());
        foreach ($customOptionsColl->getData() as $option) {
            $customOptions[$option['option_type_id']] = $option;
        }
        return Zend_Json::encode($customOptions);
    }
}
