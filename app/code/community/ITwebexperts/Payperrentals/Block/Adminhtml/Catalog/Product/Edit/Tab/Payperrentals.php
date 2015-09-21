<?php

/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Catalog_Product_Edit_Tab_Payperrentals
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Catalog_Product_Edit_Tab_Payperrentals extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @var null
     */
    protected $_product = null;

    /**
     * @var null
     */
    protected $_config = null;

    /**
     *
     */
    public function _construct()
    {
        
//        $this->setSkipGenerateContent(true);
        $this->setTemplate('payperrentals/product/edit/history.phtml');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('payperrentals')->__('Reservation History');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('payperrentals')->__('Reservation History');
    }

    /**
     * @return bool
     */
    public function canShowTab(){
        return ($this->getProduct()->getTypeId() == ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE);
    }

    /**
     * @return bool
     */
    public function isHidden(){
        return !$this->canShowTab();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return parent::_toHtml();
    }

    /**
     * @return mixed
     */
    public function getProduct(){
	if(!$this->getData('product')){
	    $this->setData('product', Mage::registry('product'));
	}
	return $this->getData('product');
    }

}
