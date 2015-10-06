<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('payperrentals/reservationorders'),'fixeddate_id',
    array(
        'nullable' => false,
        'primary' => false,
        'default'   =>  0,
        'type'     =>   Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'  =>  'Fixed Date Id'
    ));

$installer->endSetup();