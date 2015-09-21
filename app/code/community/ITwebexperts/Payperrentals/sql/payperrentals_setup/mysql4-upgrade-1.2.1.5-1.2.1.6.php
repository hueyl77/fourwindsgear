<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('payperrentals/excludeddates'),'exclude_dates_from',
    array(
        'nullable' => false,
        'primary' => false,
        'default'   =>  0,
        'type'     =>   Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'comment'  =>  'Exclude From'
    ));



$installer->endSetup();