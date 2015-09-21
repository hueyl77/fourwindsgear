<?php

class ITwebexperts_Payperrentals_Block_Adminhtml_Catalog_Product_Edit_Tab_Payperrentals_Serialnumbers extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface
{
    public function __construct()
    {
        $this->setTemplate('payperrentals/product/edit/tab/payperrentals/serialnumbers.phtml');

    }

    public function getProduct()
    {
        return Mage::registry('product');
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }


    protected function _prepareLayout()
    {

        $this->setChild('serialform',
            $this->getLayout()
                ->createBlock('payperrentals/adminhtml_catalog_product_edit_tab_payperrentals_serialform')
                ->setTemplate('payperrentals/product/edit/tab/payperrentals/serialform.phtml'));

        return parent::_prepareLayout();
    }

    public function js_string_escape($data)
    {
        $safe = "";
        for ($i = 0; $i < strlen($data); $i++) {
            if (ctype_alnum($data[$i]))
                $safe .= $data[$i];
            else
                $safe .= sprintf("\\x%02X", ord($data[$i]));
        }
        return $safe;
    }

    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    public function getElement()
    {
        return $this->_element;
    }

    public function getValues()
    {
        return Mage::getModel('payperrentals/serialnumbers')->getCollection()
            ->addEntityIdFilter($this->getProduct()->getId())
            ->getItems();
    }

    public function getSerialUrl()
    {
        if (Mage::helper('itwebcommon')->isVendorAdmin()) {
            return Mage::getUrl("vendors/ajax/generateserials/", array('form_key' => Mage::getSingleton('core/session')->getFormKey()));
        } else {
            return Mage::getUrl("payperrentals_admin/adminhtml_ajax/generateserials/", array('form_key' => Mage::getSingleton('core/session')->getFormKey()));
        }
    }
}
