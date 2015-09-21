<?php

/** @var $this Mage_Core_Model_Resource_Setup */
$_installer = $this;
$_installer->startSetup();

$_installer->getConnection()
    ->addColumn($_installer->getTable('rshipping'), 'use_custom_shipping_amount', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length' => 1,
        'nullable' => false,
        'default' => '0',
        'after' => 'shipping_method',
        'comment' => 'Custom Shipping Amount Flag'
    ));
$_installer->getConnection()
    ->addColumn($_installer->getTable('rshipping'), 'shipping_amount', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length' => '12,4',
        'nullable' => true,
        'default' => null,
        'after' => 'use_custom_shipping_amount',
        'comment' => 'Shipping Amount Value'
    ));

$_installer->endSetup();