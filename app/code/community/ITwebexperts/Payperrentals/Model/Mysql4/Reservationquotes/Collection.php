<?php



class ITwebexperts_Payperrentals_Model_Mysql4_Reservationquotes_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected $_isGrouped = false;

    protected $_quoteFilterApplied = false;

    protected $_includeCanceled = 0;

    public function _construct()
    {
        parent::_construct();
        $this->_init('payperrentals/reservationquotes');
    }

    public function addSelectFilter($select)
    {
        $this->getSelect()->where($select);
        return $this;
    }


    public function addSendReturnFilter($sr = '0')
    {
        //not send 0
        $this->getSelect()->where("sendreturn_id='$sr'");
        return $this;
    }

    public function groupByQuoteItem()
    {
        $this->getSelect()->group('quote_item_id');
        return $this;
    }

    public function addQuoteIdFilter($id)
    {
        $this->getSelect()->where("quote_id='" . intval($id) . "'");
        //$this->_quoteFilterApplied = true;
        return $this;
    }

    public function addBetweenDatesFilter($start_date)
    {
        $this->getSelect()->where('date(start_date) <= date("' . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                $start_date
            ) . '")')
            ->where('date(end_date) >= date("' . ITwebexperts_Payperrentals_Helper_Date::toDbDate($start_date) . '")');
        return $this;
    }

    public function addQuoteItemIdFilterNot($id)
    {
        $this->getSelect()->where("quote_item_id<>?", intval($id));
        //$this->_quoteFilterApplied = true;
        return $this;
    }

    public function addProductIdFilter($id)
    {
        $this->getSelect()->where('product_id=?', $id);
        return $this;
    }

    /**
     * Add collection filter for many product ids
     * */
    public function addProductIdsFilter($_productIds)
    {
        $_queryStringAr = array();
        foreach ($_productIds as $_productId) {
            $_queryStringAr[] = 'product_id="' . $_productId . '"';
        }
        $this->getSelect()->where(implode(' OR ', $_queryStringAr));
        return $this;
    }

    /**
     * Add collection filter for many order ids
     * */
    public function addOrderIdsFilter($_orderIds)
    {
        $_queryStringAr = array();
        foreach ($_orderIds as $_orderId) {
            $_queryStringAr[] = 'order_id="' . $_orderId . '"';
        }
        $this->getSelect()->where(implode(' OR ', $_queryStringAr));
        return $this;
    }

    public function addOrderIdFilter($id)
    {
        $this->getSelect()->where('order_id="' . $id . '"');
        return $this;
    }

    public function groupByOrderId()
    {
        $this->getSelect()
            ->from(null, 'COUNT(id) as total_items')
            ->group('order_id');

        return $this;
    }

    public function orderByOrderId()
    {
        $this->getSelect()
            ->order('order_id DESC');

        return $this;
    }


    public function load($printQuery = false, $logQuery = false)
    {
        $this->_beforeLoad();
        return parent::load($printQuery, $logQuery);
    }

    public function dropCanceledFilter()
    {
        $this->_includeCanceled = true;
        return $this;
    }

    protected function _beforeLoad()
    {
        if (!$this->_includeCanceled) {
            $this->getSelect()->where('qty_cancel=0');
        }

        return $this;
    }

    public function getSelectCountSql()
    {
        if (!$this->_isGrouped) {
            return parent::getSelectCountSql();
        }
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        return $countSelect->reset()->from($this->getSelect(), array())->columns('COUNT(*)');
    }

}
