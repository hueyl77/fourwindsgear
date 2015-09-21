<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Reservationprices_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected $_storeId = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('payperrentals/reservationprices');

    }

    public function addSelectFilter($select)
    {
        $this->getSelect()->where($select);
        return $this;
    }

    public function addOrderFilter($orderBy)
    {
        $this->getSelect()->order($orderBy);
        return $this;
    }

    /**
     * Filters by store id but if collection is blank filters by default store id
     * @param int $entityId
     * @param int $storeId
     */

    public function addEntityStoreFilter($entityId, $storeId){
        $countSelect = $this->getSelectCountSql();
        $countSelect->where('entity_id="'.$entityId.'" AND store_id="'.$storeId.'"');
        if ($countSelect->query()->fetchColumn() > 0) {
            $this->addFieldToFilter('entity_id', $entityId)
                ->addFieldToFilter('main_table.store_id', $storeId);
        } else {
            $this->addFieldToFilter('entity_id', $entityId)
                ->addFieldToFilter('main_table.store_id', '0');
        }
        return $this;
    }

}
