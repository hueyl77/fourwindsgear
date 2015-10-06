<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$_installer = $this;
$_installer->startSetup();

$_installer->getConnection()
    ->addColumn($_installer->getTable('rshipping'), 'shipping_cutoff_time', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'default' => '',
        'comment' => 'Shipping Cutoff Time'
    ));

$_installer->endSetup();