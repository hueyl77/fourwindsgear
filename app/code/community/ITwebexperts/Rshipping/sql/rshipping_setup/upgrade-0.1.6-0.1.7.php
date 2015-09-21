<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$_installer = $this;
$_installer->startSetup();

$_installer->getConnection()
    ->addColumn($_installer->getTable('rshipping'), 'is_local_pickup', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length' => 1,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Is Local Pickup'
    ));
$_installer->getConnection()
    ->addColumn($_installer->getTable('rshipping'), 'is_default_method', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length' => 1,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Is Default'
    ));

$_installer->endSetup();