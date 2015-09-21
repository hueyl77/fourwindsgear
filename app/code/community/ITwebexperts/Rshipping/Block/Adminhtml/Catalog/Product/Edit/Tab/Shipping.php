<?php

/**
 * Product Shipping tab
 *
 * @category   Itwebexperts
 * @package    Itwebexperts_Rshipping
 */
class ITwebexperts_Rshipping_Block_Adminhtml_Catalog_Product_Edit_Tab_Shipping extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Retrieve registered product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Itwebexperts_Rshipping_Block_Adminhtml_Catalog_Product_Edit_Tab_Shipping
     */
    protected function _prepareForm()
    {
        $product = $this->getProduct();
        $helper = $this->getWarehouseHelper();
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('product_');
        $form->setFieldNameSuffix('product');
        $fieldset = $form->addFieldset('shelves_info', array('legend' => $helper->__('Shelves'),));
        $shelvesElement = $fieldset->addField('shelves', 'text', array(
            'name' => 'shelves',
            'label' => $helper->__('Shelves'),
            'title' => $helper->__('Shelves'),
            'required' => false,
            'value' => $product->getShelves(),
        ));
        $shelvesElement->setRenderer(
            $this->getLayout()->createBlock('warehouse/adminhtml_catalog_product_edit_tab_shelf_renderer')
        );
        $this->setForm($form);
        return parent::_prepareForm();
    }
}