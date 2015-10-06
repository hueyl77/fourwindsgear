<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Rentalqueue_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
	
	protected $_storeId = null;
	
	public function _construct(){
		parent::_construct();
		$this->_init('payperrentals/rentalqueue');
		if(!Mage::app()->isSingleStoreMode() && Mage::app()->getStore()->getId()){
			
			$this->setStoreId(Mage::app()->getStore()->getId());
		}
	}

    public function getNumberInQueue($customerid){
        $_queueCollection = Mage::getModel('payperrentals/rentalqueue')
            ->getCollection()
            ->addCustomerIdFilter($customerid)
            ->addSelectFilter('sendreturn_id = "0"');
        return count($_queueCollection);
    }

	public function addSelectFilter($select){
		$this->getSelect()->where($select);
		return $this;
	}

	public function addOrderFilter($orderBy){
		$this->getSelect()->order($orderBy);
		return $this;
	}

	public function addCustomerIdFilter($id){
		$this->getSelect()
			->where('main_table.customer_id=?', $id);
		return $this;	
	}

	public function addProductIdFilter($id){
		$this->getSelect()
				->where('main_table.product_id=?', $id);
		return $this;
	}

	public function addStoreIdFilter($id){
		$this->getSelect()
		->where('main_table.store_id="'.$id.'" OR main_table.store_id="0" OR main_table.store_id="1"');
			
		return $this;	
	}	
	
	public function load($printQuery = false, $logQuery = false){
		$this->_beforeLoad();
		return  parent::load($printQuery, $logQuery);
    }	
    
    protected function _beforeLoad(){
		if(($this->_storeId)){
			$this->addStoreIdFilter($this->_storeId);
		}
		return $this;
	}

	public function setStoreId($id){
		$this->_storeId = $id;
		return $this;
	}

    public function addProductAttributeToSelect($attributeCode)
    {
        $tableAlias = 'at_'.$attributeCode;
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attributeCode);

        $this->getSelect()
            ->joinLeft(
                array($tableAlias => $attribute->getBackend()->getTable()),
                'main_table.product_id = '.$tableAlias.'.entity_id',
                array('product_name' => 'value')
            )
            ->group('main_table.id');
        return $this;
    }
}
