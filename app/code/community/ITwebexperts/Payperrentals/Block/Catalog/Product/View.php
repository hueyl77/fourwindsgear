<?php


class ITwebexperts_Payperrentals_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    private $_isAjax = false;

    /**
     * Constructor
     */
    public function _construct()
    {
        parent::_construct();
        if (!$this->getTemplate()) {
            $this->setTemplate('payperrentals/catalog/product/view.phtml');
        }
    }

    public function setIsAjax($_isAjax = true)
    {
        $this->_isAjax = $_isAjax;
    }

    public function getIsAjax()
    {
        return $this->_isAjax;
    }

    /**
     * Get postfix for js functions
     * @return string
     */
    public function getJsFunctionPostfix()
    {
        return ($this->_isAjax) ? 'Ajax' : '';
    }

    /**
     * Get form id
     * @return string
     */
    public function getJsContainerPostfix()
    {
        return ($this->_isAjax) ? '#ajax_product_addtocart_form' : '#product_addtocart_form';
    }
}