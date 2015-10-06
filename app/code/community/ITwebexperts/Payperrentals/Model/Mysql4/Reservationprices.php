<?php


class ITwebexperts_Payperrentals_Model_Mysql4_Reservationprices extends Mage_Core_Model_Mysql4_Abstract{

   	protected function _construct(){
		$this->_init('payperrentals/reservationprices', 'id');
	}
		
	public function deleteByEntityId($id, $storeId = null){
		$condition =   'entity_id='.intval($id);
		if(!is_null($storeId)){
			$condition .= ' AND store_id='.intval($storeId);
		}
		$this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
		 
		return $this;
	}

    public function updateDateFrom($id) {
        $Adapter = $this->_getWriteAdapter();
        $sql = "UPDATE IGNORE"
            . $Adapter->quoteIdentifier($this->getMainTable(), true)
            . ' SET date_from="0000-00-00 00:00:00"'
            . 'WHERE `id`='.$id;
        $Adapter->query($sql);
        return $this;
    }
    public function updateDateTo($id) {
        $Adapter = $this->_getWriteAdapter();
        $sql = "UPDATE IGNORE"
            . $Adapter->quoteIdentifier($this->getMainTable(), true)
            . ' SET date_to="0000-00-00 00:00:00"'
            . 'WHERE `id`='.$id;
        $Adapter->query($sql);
        return $this;
    }
	
}
