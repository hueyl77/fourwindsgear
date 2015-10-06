<?php


class ITwebexperts_Payperrentals_Model_Mysql4_Reservationquotes extends Mage_Core_Model_Mysql4_Abstract {
    protected function _construct() {
        $this->_init('payperrentals/reservationquotes', 'id');
    }

    public function deleteByQuoteItem(Mage_Sales_Model_Quote_Item $QuoteItem) {
        $condition =   "quote_item_id=".intval($QuoteItem->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }

    public function deleteByQuoteItemAndDates(Mage_Sales_Model_Quote_Item $QuoteItem, $start_date, $end_date) {
        $condition =   "quote_item_id=".intval($QuoteItem->getId()). ' AND start_date="'. ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                $start_date
            ) .'" AND end_date="'. ITwebexperts_Payperrentals_Helper_Date::toDbDate($end_date) .'"';
        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }

    public function deleteByQuoteItemId($id) {
        $condition =   "quote_item_id=".intval($id);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }

    public function deleteByQuoteId($id) {
        $condition =   "quote_id=".intval($id);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }

}
