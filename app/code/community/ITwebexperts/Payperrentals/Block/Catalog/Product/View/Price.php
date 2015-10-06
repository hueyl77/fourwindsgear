<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Catalog_Product_View_Price
 */
class ITwebexperts_Payperrentals_Block_Catalog_Product_View_Price extends Mage_Catalog_Block_Product_View_Price
{
    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        if (!$this->getTemplate()) {
            $this->setTemplate('catalog/product/price.phtml');
        }
    }

    /**
     * @param int $product_id
     * @return mixed
     */
    public function getProduct($product_id = 0)
    {
        if (!$this->getData('product')) {
            $model = Mage::getSingleton('catalog/product');
            if ($product_id instanceof $model) {
                $model = $product_id;
            } elseif (Mage::registry('product')) {
                $model = Mage::registry('product');
            } elseif ($product_id || ($product_id = $this->getRequest()->getParam('id'))) {
                $model->load($product_id);
            }
            $this->setData('product', $model);
        }
        return $this->getData('product');
    }


    /**
     * 1.8 FIX
     *
     * @param string|integer|Mage_Core_Model_Config_Element $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Abstract || false
     */
    public function getProductAttribute($attribute)
    {
        $attributeModel = $this->getProduct()->getResource()->getAttribute($attribute);
        if (!$attributeModel && is_string($attribute) && $attribute == 'special_price') {
            $attributeModel = new Varien_Object();
            $attributeModel->getStoreLabel('');
        }
        return $attributeModel;
    }

}
