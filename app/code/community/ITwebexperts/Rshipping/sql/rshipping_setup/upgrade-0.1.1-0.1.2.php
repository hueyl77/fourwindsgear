<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$_installer = $this;
$_installer->startSetup();

$_installer->getConnection()
    ->addColumn($_installer->getTable('rshipping'), 'min_rental_period', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Min Rental Period'
    ));
$_installer->getConnection()
    ->addColumn($_installer->getTable('rshipping'), 'min_rental_type', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'default' => '3',
        'comment' => 'Min Rental Type'
    ));

$_installer->endSetup();