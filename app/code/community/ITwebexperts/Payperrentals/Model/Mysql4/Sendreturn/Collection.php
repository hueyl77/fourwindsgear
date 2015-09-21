<?php

class ITwebexperts_Payperrentals_Model_Mysql4_Sendreturn_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
	
	protected $_storeId = null;
	
	public function _construct(){
		parent::_construct();
		$this->_init('payperrentals/sendreturn');

	}

	public function orderByOrderId(){
		$this->getSelect()
				->order('order_id DESC');

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

	public function orderByCustomerId(){
		$this->getSelect()
				->order('customer_id DESC');

		return $this;
	}

	public function addOrderIdFilter($id){
		$this->getSelect()
			->where('order_id=?', $id);
		return $this;	
	}

	public function addSelectFilter($select){
		$this->getSelect()->where($select);
		return $this;
	}

	public function load($printQuery = false, $logQuery = false){
		//$this->_beforeLoad();
		return  parent::load($printQuery, $logQuery);
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
