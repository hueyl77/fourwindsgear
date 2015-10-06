<?php

abstract class ITwebexperts_Payperrentals_Model_Mysql4_Reports_Product_Report_Abstract
{
    public static function addReportFilter($_collection, $_filterField, $_filterValue)
    {
        $_condition = $_collection->getConnection()->prepareSqlCondition($_filterField, $_filterValue);
        $_collection->getSelect()->having($_condition);
    }
}