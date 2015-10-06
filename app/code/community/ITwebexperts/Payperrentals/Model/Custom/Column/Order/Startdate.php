<?php
class ITwebexperts_Payperrentals_Model_Custom_Column_Order_Startdate
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
		return $this->getModelParam('startdate_field');
	}


}