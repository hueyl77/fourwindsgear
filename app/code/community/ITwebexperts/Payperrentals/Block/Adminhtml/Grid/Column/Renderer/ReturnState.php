<?php


/**
 *
 * @author Enrique Piatti
 */
class ITwebexperts_Payperrentals_Block_Adminhtml_Grid_Column_Renderer_ReturnState extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_totalQtyReturned = 0;
    protected $_totalQtyShipped = 0;
    protected $_hasShipmentInDb = false;
    /**
     * @param Varien_Object $_row
     * @return string
     */
    public function render(Varien_Object $_row)
    {
        $this->setOrder($_row);
        $_returnCollection = Mage::getResourceModel('payperrentals/sendreturn_collection')
            ->addFieldToFilter('order_id', array('in' => array($_row->getEntityId(), $_row->getIncrementId())));
        $_totalQtyReturned = 0;
        $_returnedDate = '';
        $_returnedUnixtimeDate = 0;
        $this->_hasShipmentInDb = false;
        if(count($_returnCollection)) $this->_hasShipmentInDb = true;
        foreach($_returnCollection as $_returnItem) {
            if($_returnItem->getReturnDate() != '0000-00-00 00:00:00' && $_returnItem->getReturnDate() != '1970-01-01 00:00:00')
            {
                $_totalQtyReturned += $_returnItem->getQty();
                if ($_returnedUnixtimeDate < strtotime($_returnItem->getReturnDate())) {
                    $_returnedDate = $_returnItem->getReturnDate();
                    $_returnedUnixtimeDate = strtotime($_returnItem->getReturnDate());
                }
            }
        }
        $callingBlock = $this->getColumn()->getGrid()->getId();
        if ($callingBlock == "sales_returnlate_grid"){
        /* START code below is for when return is used WITHOUT the ship render on late returns page */
        $_shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
            ->addFieldToFilter('order_id', array('eq' => $_row->getEntityId()));
        $_totalQtyShipped = 0;
        $_shippedDate = '';
        $_shippedUnixtimeDate = 0;
        foreach($_shipmentCollection as $_shipmentItem) {
            $_totalQtyShipped += $_shipmentItem->getTotalQty();
            if ($_shippedUnixtimeDate < strtotime($_shipmentItem->getCreatedAt())) {
                $_shippedDate = $_shipmentItem->getCreatedAt();
                $_shippedUnixtimeDate = strtotime($_shipmentItem->getCreatedAt());
            }
        }
        Mage::unregister('total_qty_shipped');
        Mage::register('total_qty_shipped', $_totalQtyShipped);
        }
        /* END code below is for when return is used WITHOUT the ship render on late returns page */
        $_totalQtyShipped = (Mage::registry('total_qty_shipped')) ? Mage::registry('total_qty_shipped') : 0;
        $this->_totalQtyShipped = $_totalQtyShipped;
        $this->_totalQtyReturned = $_totalQtyReturned;
        $_buttonHtml = $this->_getReturnButtonHtml();
        if (!$_totalQtyReturned) {
            return Mage::helper('payperrentals')->__('Not Returned') . '<br/>' . $_buttonHtml;
        } elseif ($_totalQtyReturned < $_totalQtyShipped) {
            return Mage::helper('payperrentals')->__('Partially Returned') . '<br/>' . $_buttonHtml;
        } else {
            return ITwebexperts_Payperrentals_Helper_Date::formatDbDate($_returnedDate, true, true);
        }
    }


    /**
     * @return string
     */
    protected function _getReturnButtonHtml()
    {
        $_buttonHtml = '';
        if ($this->_isAllowedToReturn()) {
            $_buttonHtml = '<a href="' . $this->_getReturnUrl() . '"><button title="Return" type="button" class="scalable go" onclick="setLocation(\'' . $this->_getReturnUrl() . '\'); return false;" style="">
			<span><span><span>'.Mage::helper('payperrentals')->__('Return').'</span></span></span></button></a>';
        }
        return $_buttonHtml;
    }

    /**
     * @return bool
     */
    protected function _isAllowedToReturn()
    {
        return (bool)$this->_totalQtyShipped && $this->_hasShipmentInDb && $this->_totalQtyShipped != $this->_totalQtyReturned;
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
    protected function _getReturnUrl()
    {
        $callingBlock = $this->getColumn()->getGrid()->getId();
        if ($callingBlock == "sales_returnlate_grid"){
            return $this->getUrl('adminhtml/sales_order_return/new', array('order_id' => $this->_getOrder()->getId()));
        }
        return $this->getUrl('*/sales_order_return/new', array('order_id' => $this->_getOrder()->getId()));
    }
}