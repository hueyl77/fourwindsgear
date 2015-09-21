<?php


/**
 *
 * @author Enrique Piatti
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Grid_Column_Renderer_ShippingState extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $_row
     * @return string
     */
    public function render(Varien_Object $_row)
    {
        $this->setOrder($_row);
        $_shipmentCollectionAll = Mage::getResourceModel('sales/order_shipment_collection')
            ->addFieldToFilter('order_id', array('eq' => $_row->getEntityId()));
        $_shipmentCollection = Mage::getResourceModel('payperrentals/sendreturn_collection')
            ->addFieldToFilter('order_id', array('in' => array($_row->getEntityId(), $_row->getIncrementId())));
        $_totalQtyShipped = 0;
        $_shippedDate = '';
        $_shippedUnixtimeDate = 0;
        foreach($_shipmentCollection as $_shipmentItem) {
            if($_shipmentItem->getSendDate() != '0000-00-00 00:00:00' && $_shipmentItem->getSendDate() != '1970-01-01 00:00:00') {
                $_totalQtyShipped += $_shipmentItem->getQty();

            }
        }
        $_totalQtyShippedAll = 0;
        foreach($_shipmentCollectionAll as $_shipmentItem) {
            $_totalQtyShippedAll += $_shipmentItem->getTotalQty();
            if ($_shippedUnixtimeDate < strtotime($_shipmentItem->getCreatedAt())) {
                $_shippedDate = $_shipmentItem->getCreatedAt();
                $_shippedUnixtimeDate = strtotime($_shipmentItem->getCreatedAt());
            }
        }

        Mage::unregister('total_qty_shipped');
        Mage::register('total_qty_shipped', $_totalQtyShipped);
        /*$_totalQtyShipped = $this->_getValue($_row);*/
        $_realOrder = Mage::getModel('sales/order')->load($_row->getId());
        $_totalQtyOrdered = (int)$_realOrder->getTotalQtyOrdered();
        $_shipButtonHtml = $this->_getShipButtonHtml();
        if (!$_totalQtyShippedAll) {
            return Mage::helper('payperrentals')->__('Not Shipped') . '<br/>' . $_shipButtonHtml;
        } elseif ($_totalQtyShippedAll < $_totalQtyOrdered) {
            return Mage::helper('payperrentals')->__('Partially Shipped') . '<br/>' . $_shipButtonHtml;
        } else {
            return ITwebexperts_Payperrentals_Helper_Date::formatDbDate($_shippedDate, true, true);
        }
    }


    /**
     * @return string
     */
    protected function _getShipButtonHtml()
    {
        $_buttonHtml = '';
        $_order = Mage::getModel('sales/order')->load($this->_getOrder()->getOrderId());
        if ($this->_isAllowedToShip() && $_order->canShip() && !$_order->getForcedDoShipmentWithInvoice()) {
            $_buttonHtml = '<a><button title="Ship" type="button" class="scalable go" onclick="setLocation(\'' . $this->_getShipUrl() . '\'); return false;" style="">
			<span><span><span>'.Mage::helper('payperrentals')->__('Ship').'</span></span></span></button></a>';
        }
        return $_buttonHtml;
    }

    /**
     * @return mixed
     */
    protected function _isAllowedToShip()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/ship');
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        return $this->getData('order');
    }

    /**
     * @return string
     */
    protected function _getShipUrl()
    {
        return $this->getUrl('*/sales_order_shipment/start', array('order_id' => $this->_getOrder()->getId()));
    }
}

