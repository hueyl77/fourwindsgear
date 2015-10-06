<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Excludeddates_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
	
	protected $_storeId = null;
	
	public function _construct(){
		parent::_construct();
		$this->_init('payperrentals/excludeddates');
	}

	public function addProductIdFilter($id){
		$this->getSelect()
			->where('product_id=?', $id);
		return $this;	
	}

	public function addSelectFilter($select){
		$this->getSelect()->where($select);
		return $this;
	}

    /**
     * Filters by store id but if collection is blank filters by default store id
     * @param int $productId
     * @param int $storeId
     */

    public function addProductStoreFilter($productId, $storeId){
        $countSelect = $this->getSelectCountSql();
        $countSelect->where('product_id="'.$productId.'" AND store_id="'.$storeId.'"');
        if ($countSelect->query()->fetchColumn() > 0) {
            $this->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('store_id', $storeId);
        } else {
            $this->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('store_id', '0');
        }
        return $this;
    }

}
