<?php



class ITwebexperts_Payperrentals_Model_Mysql4_Reservationorders_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected $_isGrouped = false;

    protected $_quoteFilterApplied = false;

    protected $_includeCanceled = 0;

    public function _construct()
    {
        parent::_construct();
        $this->_init('payperrentals/reservationorders');
        $this->addFilterToMap('product_id', 'main_table.product_id');
    }

    /**
     * @param $select
     * @return $this
     */
    public function addSelectFilter($select)
    {
        $this->getSelect()->where($select);
        return $this;
    }

    /**
     * @param $select
     * @return $this
     */
    public function addHavingFilter($select)
    {
        $this->getSelect()->having($select);
        return $this;
    }
    /**
     * Add send/return id filter
     * @param string $_sendReturnId
     * @return $this
     */
    public function addSendReturnFilter($_sendReturnId = '0')
    {
        $this->getSelect()->where('main_table.sendreturn_id=?', $_sendReturnId);
        return $this;
    }

    /**
     * Group by order id
     * @return $this
     */
    public function groupByOrder()
    {
        $this->getSelect()->group('order_id');
        return $this;
    }

    /**
     * Add filter where method argument between start and end dates
     * @param $start_date
     * @return $this
     */
    public function addBetweenDatesFilter($start_date)
    {
        $this->getSelect()->where('date(main_table.start_date) <= date("' . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                $start_date
            ) . '")')
            ->where('date(main_table.end_date) >= date("' . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                    $start_date
                ) . '")');
        return $this;
    }

    /**
     * Add product filter
     * @param int $_productId
     * @return $this
     */
    public function addProductIdFilter($_productId)
    {
        $this->getSelect()->where('main_table.product_id=?', $_productId);
        return $this;
    }

    /**
     * Add collection filter for many product ids
     * @param array $_productIds
     * @return $this
     */
    public function addProductIdsFilter($_productIds)
    {
        $this->addFieldToFilter('main_table.product_id', array('in' => $_productIds));
        return $this;
    }

    /**
     * Add collection filter for many order ids
     * @param array $_orderIds
     * @return $this
     * */
    public function addOrderIdsFilter($_orderIds)
    {
        $this->addFieldToFilter('main_table.order_id', array('in' => $_orderIds));
    }

    /**
     * Filter for only one order
     * @param int $_orderId
     * @return $this
     */
    public function addOrderIdFilter($_orderId)
    {
        $this->getSelect()->where('main_table.order_id=?', $_orderId);
        return $this;
    }

    /**
     * @return $this
     */
    public function groupByOrderId()
    {
        $this->getSelect()
            ->from(null, 'COUNT(id) as total_items')
            ->group('order_id');

        return $this;
    }

    /**
     * @return $this
     */
    public function orderByOrderId()
    {
        $this->getSelect()
            ->order('main_table.order_id DESC');

        return $this;
    }

    /**
     * Allow return cancelled orders
     * @return $this
     */
    public function dropCanceledFilter()
    {
        $this->_includeCanceled = true;
        return $this;
    }

    /**
     * Rewrite _beforeLoad method
     * @return $this|Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _beforeLoad()
    {
        if (!$this->_includeCanceled) {
            $this->getSelect()->where('main_table.qty > main_table.qty_cancel');
        }

        return $this;
    }

    /**
     * Rewrite Varien_Data_Collection_Db method
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        if (!$this->_isGrouped) {
            return parent::getSelectCountSql();
        }
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        return $countSelect->reset()->from($this->getSelect(), array())->columns('COUNT(*)');
    }

    public function getToSendReturnCollection($startDatefrom,$startDateto,$endDatefrom,$endDateto,$forStore,$sendreturn = 'send')
    {
        $resource = Mage::getSingleton('core/resource');
        $this->getSelect()->joinLeft(array('sendreturn'=>$resource->getTableName('payperrentals/sendreturn')),'main_table.sendreturn_id = sendreturn.id',array('send_date','return_date','qtysendreturn'=>'sendreturn.qty','sn','sendreturnid'=>'sendreturn.id'));
        $this->addSelectFilter("start_date >= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($startDatefrom) . "' AND start_date <= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                $startDateto
            ) . "'")
            ->addSelectFilter("end_date >= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate($endDatefrom) . "' AND end_date <= '" . ITwebexperts_Payperrentals_Helper_Date::toDbDate(
                    $endDateto
                ) . "'")
            ->addSelectFilter("product_type = '" . ITwebexperts_Payperrentals_Helper_Data::PRODUCT_TYPE . "'")
            ->addFieldToFilter('main_table.order_id',array('neq'=>0))
            ->orderByOrderId();
        if($sendreturn == 'send') {
            $this->addFieldToFilter('sendreturn.send_date', array('null' => true));
        } else if($sendreturn == 'return'){
            $this->addFieldToFilter('sendreturn.send_date',array('notnull'=>true))
                ->addFieldToFilter('sendreturn.return_date',array(
                    array('eq'=>'1970-01-01 00:00:00'),
                    array('eq'=>'0000-00-00 00:00:00'),
                    ));
    }
        if ($forStore) {
            $this->getSelect()->joinLeft(array('so' => Mage::getSingleton('core/resource')->getTableName('sales_flat_order')), 'main_table.order_id = ' . 'so.entity_id', array('so.store_id as store_id'));
            $this->getSelect()->where('so.store_id=?', $this->getRequest()->getParam('forStore'));
        }
        //echo $this->getSelect();
        return $this;
    }

}

