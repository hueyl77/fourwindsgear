<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('payperrentals/reservationorders'),'dropoff',
    array(
        'nullable' => true,
        'primary' => false,
        'default'   =>  null,
        'type'     =>   Varien_Db_Ddl_Table::TYPE_DATETIME,
        'comment'  =>  'Dropoff Date'
    ));

$installer->getConnection()->addColumn($installer->getTable('payperrentals/reservationorders'),'pickup',
    array(
        'nullable' => true,
        'primary' => false,
        'default'   =>  null,
        'type'     =>   Varien_Db_Ddl_Table::TYPE_DATETIME,
        'comment'  =>  'Pickup Date'
    ));


$installer->endSetup();