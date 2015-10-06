<?php


/**
 * Class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Productname
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Html_Renderer_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return mixed
     */
    public function render(Varien_Object $row){
        return '<a href="'. Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getOrderId())).'">'.$row->getIncrementsId().'</a>';
	}

}
