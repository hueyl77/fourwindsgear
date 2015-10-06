<?php
class ITwebexperts_Payperrentals_Model_Mysql4_Reservationorders extends Mage_Core_Model_Mysql4_Abstract {
    protected function _construct() {
        $this->_init('payperrentals/reservationorders', 'id');
    }


    public function cancelByOrderId($id, $qty) {
        $this->_getWriteAdapter()->update($this->getMainTable(), array('qty_cancel' => $qty),'order_id="'.($id).'"');
        return $this;
    }

	public function updateShippedQty($id, $qtyShipped) {
		$this->_getWriteAdapter()->update($this->getMainTable(), array('qty_shipped' => $qtyShipped),'id='.($id));
		return $this;
	}

    public function updateReturnedQty($id, $qtyReturned) {
        $this->_getWriteAdapter()->update($this->getMainTable(), array('qty_returned' => $qtyReturned),'id='.($id));
        return $this;
    }

    public function updateStartEndByOrderId($order_id, $start, $end) {
        $this->_getWriteAdapter()->update($this->getMainTable(), array('start_date' => $start, 'end_date' => $end),'order_id="'.($order_id).'"');
        return $this;
    }

    public function updatePickupDropoffByOrderId($order_id, $start, $end) {//dropoff send date
        $this->_getWriteAdapter()->update($this->getMainTable(), array('dropoff' => $start, 'pickup' => $end),'order_id="'.($order_id).'"');
        return $this;
    }

    public function cancelByOrderItemId($oid, $id, $qty=0) {
        $Adapter = $this->_getWriteAdapter();

        $sql = "UPDATE "
             . $Adapter->quoteIdentifier($this->getMainTable(), true)
             . ' SET `qty_cancel`=`qty_cancel`+'.$qty
             . ' WHERE `order_item_id`='.$id
             . ' AND `order_id`="'.$oid.'"';
            //might be a bug when on the same order is the same product with different dates
        $Adapter->query($sql);
        
        return $this;
    }


    public function deleteByQuoteItem(Mage_Sales_Model_Quote_Item $QuoteItem) {
        $condition =   "otype='".ITwebexperts_Payperrentals_Model_Reservationorders::TYPE_QUOTE."' AND order_id=".intval($QuoteItem->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }


    public function deleteByQuoteItemId($id) {
        $condition =   "otype='".ITwebexperts_Payperrentals_Model_Reservationorders::TYPE_QUOTE."' AND order_id=".intval($id);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }
    public function deleteByQuoteId($id) {
        $condition =   "otype='".ITwebexperts_Payperrentals_Model_Reservationorders::TYPE_QUOTE."' AND quote_id=".intval($id);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }
}
