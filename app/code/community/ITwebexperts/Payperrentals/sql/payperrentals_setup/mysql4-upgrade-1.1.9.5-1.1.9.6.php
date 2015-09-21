<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();

$installer->run("
	DROP TABLE IF EXISTS {$installer->getTable('payperrentals/ordertodates')};");
$table = $installer->getConnection()
    ->newTable($installer->getTable('payperrentals/ordertodates'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'id')
    ->addColumn('orders_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 100, array(
        'nullable' => true,
        'default'  => null,
    ), 'Order Id')
    ->addColumn('event_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => true,
        'default'  => null,
    ), 'Event Date')
    ->addIndex($installer->getIdxName(
            $installer->getTable('payperrentals/ordertodates'),
            array('orders_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('orders_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )->addForeignKey(
        'FK_ITEMS_DATES_ORDERS_RELATION_ITEM1',
        'orders_id',
        $installer->getTable('sales/order'),
        'entity_id',
        'cascade',
        'cascade'
    );

$installer->getConnection()->createTable($table);

$installer->endSetup();