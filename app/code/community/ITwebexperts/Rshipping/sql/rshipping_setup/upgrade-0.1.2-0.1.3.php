<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$_installer = $this;
$_installer->startSetup();

$_installer->getConnection()
    ->addColumn($_installer->getTable('rshipping'), 'start_disabled_days', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'default' => '',
        'comment' => 'Start Disabled Days'
    ));
$_installer->getConnection()
    ->addColumn($_installer->getTable('rshipping'), 'end_disabled_days', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'default' => '',
        'comment' => 'End Disabled Days'
    ));

$_installer->endSetup();