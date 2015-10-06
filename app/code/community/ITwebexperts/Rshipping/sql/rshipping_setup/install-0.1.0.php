<?php

/** @var $this Mage_Core_Model_Resource_Setup */
$_installer = $this;
$_installer->startSetup();

$_table = $_installer->getConnection()
    ->newTable($_installer->getTable('rshipping'))
    ->addColumn('rshipping_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'ID')
    ->addColumn('shipping_title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
        'default' => ''
    ), 'Shipping Title')
    ->addColumn('shipping_method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
        'default' => ''
    ), 'Shipping Method')
    ->addColumn('turnover_before_period', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
        'default' => ''
    ), 'Turnover Before Period')
    ->addColumn('turnover_before_period', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'default' => '3'
    ), 'Turnover Before Type')
    ->addColumn('turnover_after_period', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
        'default' => ''
    ), 'Turnover After Period')
    ->addColumn('turnover_after_type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'default' => '3'
    ), 'Turnover After Type')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, 6, array(
        'nullable' => false,
        'default' => '0'
    ), 'Status')
    ->addColumn('created_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => true,
    ), 'Create Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => true,
    ), 'Update Time');
$_installer->getConnection()->dropTable($_installer->getTable('rshipping'));
$_installer->getConnection()->createTable($_table);

$_table = $_installer->getConnection()
    ->newTable($_installer->getTable('rshipping/products'))
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'ID')
    ->addColumn('rshipping_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'unsigned' => true
    ), 'Shipping Method ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'unsigned' => true
    ), 'Product ID')
    ->addIndex(
        $_installer->getIdxName(
        'rshipping/products',
            array('rshipping_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('rshipping_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $_installer->getIdxName(
        'rshipping/products',
            array('product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('product_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey($_installer->getFkName('rshipping/products', 'rshipping_id', 'rshipping', 'rshipping_id'),
        'rshipping_id', $_installer->getTable('rshipping'), 'rshipping_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($_installer->getFkName('rshipping/products', 'product_id', 'catalog/product', 'entity_id'),
        'rshipping_id', $_installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
$_installer->getConnection()->dropTable($_installer->getTable('rshipping/products'));
$_installer->getConnection()->createTable($_table);

$_installer->endSetup();
