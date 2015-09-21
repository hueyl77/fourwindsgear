<?php
class ITwebexperts_Payperrentals_Model_Custom_Column_Order_Enddate
    extends BL_CustomGrid_Model_Custom_Column_Simple_Table
{
    public function initConfig()
    {
        parent::initConfig();
        return $this;
    }

	public function getTableName()
	{
		return 'payperrentals/reservationorders';
	}

	public function getJoinConditionMainField()
	{
		return 'entity_id';
	}

	public function getJoinConditionTableField()
	{
		return 'order_id';
	}

	public function getTableFieldName()
	{
		return $this->getModelParam('enddate_field');
	}

	protected function _getStartDate($collection, $forFilter=false)
	{
		$helper    = $this->_getCollectionHelper();
		$mainAlias = $this->_getCollectionMainTableAlias($collection);
		$resAlias = $this->_getUniqueTableAlias('_resorders_');
		list($adapter, $qi) = $this->_getCollectionAdapter($collection, true);

		$select = $adapter->select()
				->joinLeft( array($resAlias =>  $collection->getTable('payperrentals/reservationorders')),$qi($resAlias.'.order_id').' = '.$qi($mainAlias.'.entity_id'), array('end_date'))
				;//->group($qi($mainAlias.'.order_id'));

		/*if (!$forFilter) {
			$select->group($cpAlias.'.product_id');
		}*/

		return $select;
	}

	public function addFieldToGridCollection($alias, $params, $block, $collection)
	{
		$startDateQuery = '('.$this->_getStartDate($collection).')';
        $collection->getSelect()->columns(array($alias => new Zend_Db_Expr($startDateQuery)));
        return $this;
	}

	public function getAdditionalJoinConditions($alias, $params, $block, $collection, $mainAlias, $tableAlias)
	{
		//list($adapter, $qi) = $this->_getCollectionAdapter($collection, true);
		//return array($adapter->quoteInto($qi($tableAlias.'.address_type').' = ?', $this->getAddressType()));

	}

}