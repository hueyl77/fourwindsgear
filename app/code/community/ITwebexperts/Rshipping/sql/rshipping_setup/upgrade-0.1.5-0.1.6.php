<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$_installer = $this;
$_installer->startSetup();

$_installer->getConnection()
    ->addColumn($_installer->getTable('rshipping'), 'ignore_turnover_day', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'default' => '',
        'comment' => 'Ignored Turnover Days'
    ));

$_installer->endSetup();