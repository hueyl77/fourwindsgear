<?php


class ITwebexperts_Payperrentals_Model_Mysql4_Rentalqueue extends Mage_Core_Model_Mysql4_Abstract{

   	protected function _construct(){
		$this->_init('payperrentals/rentalqueue', 'id');
	}
		
	public function deleteByCustomerId($id, $storeId = null){
		$condition =   'customer_id='.intval($id);
		if(!is_null($storeId)){
			$condition .= ' AND store_id='.intval($storeId);
		}
		$this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
		 
		return $this;
	}

	public function updateSendReturnById($id, $sr) {
		$this->_getWriteAdapter()->update($this->getMainTable(), array('sendreturn_id' => $sr),'id='.($id));
		return $this;
	}

	public function updateSortOrder($neworder, $customer_id, $store_id, $product_id){
		$where = array();
		$where[] = $this->_getWriteAdapter()->quoteInto('customer_id = ?', $customer_id);
		$where[] = $this->_getWriteAdapter()->quoteInto('store_id = ?', $store_id);
		$where[] = $this->_getWriteAdapter()->quoteInto('product_id = ?', $product_id);
		$this->_getWriteAdapter()->update($this->getMainTable(),array('sort_order' => $neworder), $where);
	}

	public function deleteByProductList($customer_id, $store_id, $product_id_list){
		$where = array();
		$where[] = $this->_getWriteAdapter()->quoteInto('customer_id = ?', $customer_id);
		$where[] = $this->_getWriteAdapter()->quoteInto('store_id = ?', $store_id);
		$where[] = $this->_getWriteAdapter()->quoteInto('product_id IN (?)', $product_id_list);
		$this->_getWriteAdapter()->delete($this->getMainTable(), $where);
	}

	public function deleteByNotInProductList($customer_id, $store_id, $product_id_list){
		$where = array();
		$where[] = $this->_getWriteAdapter()->quoteInto('customer_id = ?', $customer_id);
		$where[] = $this->_getWriteAdapter()->quoteInto('store_id = ?', $store_id);
		$where[] = $this->_getWriteAdapter()->quoteInto('product_id NOT IN (?)', $product_id_list);
		$this->_getWriteAdapter()->delete($this->getMainTable(), $where);
	}
	
}
