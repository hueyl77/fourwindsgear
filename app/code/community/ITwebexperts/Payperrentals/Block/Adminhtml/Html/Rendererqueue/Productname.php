<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Productname
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Rendererqueue_Productname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return mixed
     */
    public function render(Varien_Object $row){
		$data = $row->getData($this->getColumn()->getIndex());
		$product = Mage::getModel('catalog/product')->load($data);
		$productName = $product->getName();
		return $productName;
	}

}
