<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$_installer = $this;
$_installer->startSetup();

$_installer->getConnection()
    ->addColumn($_installer->getTable('rshipping'), 'use_live_ups_api', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length' => 1,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Use UPS Api'
    ));

$_installer->endSetup();