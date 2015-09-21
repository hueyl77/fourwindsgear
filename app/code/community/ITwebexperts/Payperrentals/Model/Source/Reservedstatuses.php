<?php

class ITwebexperts_Payperrentals_Model_Source_Reservedstatuses
{

	public function toOptionArray()
	{
        $retStatus = array();
        $statuses = Mage::getModel('sales/order_status')
            ->getCollection()
            ->addFieldToSelect('status')
            ->addFieldToSelect('label');
        foreach($statuses as $iStatus){
            $myStat['value'] = $iStatus['status'];
            $myStat['label'] = $iStatus['label'];
            $retStatus[] = $myStat;
        }
        return $retStatus;
	}

}
