<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$_installer = $this;
$_installer->startSetup();

$_installer->getConnection()
    ->addColumn($_installer->getTable('rshipping'), 'custom_shipping_amount_type', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length' => 1,
        'nullable' => false,
        'default' => '0',
        'after' => 'shipping_amount',
        'comment' => 'Custom Shipping Amount Type'
    ));

$_installer->endSetup();