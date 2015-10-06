<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()->newTable($installer->getTable('simaintenance/items'))
    ->addColumn('item_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
        'unsigned'  =>  true,
        'nullable'  =>  false,
        'primary'   =>  true,
        'identity'  =>  true,
    ),'maintenance record id')
    ->addColumn('date_added',Varien_Db_Ddl_Table::TYPE_DATETIME,null,array(
    ),'Date Added')
    ->addColumn('product_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
    ))
    ->addColumn('quantity',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(),'Quantity')
    ->addColumn('serials',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'Serials')
    ->addColumn('cost',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'Cost of Maintenance')
    ->addColumn('summary',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'Summary')
    ->addColumn('description',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'Description')
    ->addColumn('comments',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'Comments')
    ->addColumn('maintainer_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(),'Maintainer')
    ->addColumn('start_date',Varien_Db_Ddl_Table::TYPE_DATETIME,null,array(),'Start Date')
    ->addColumn('end_date',Varien_Db_Ddl_Table::TYPE_DATETIME,null,array(),'End Date')
    ->addColumn('status',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'Status')
    ->addColumn('added_from',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'Added From');

$installer->getConnection()->createTable($table);

$table = $installer->getConnection()->newTable($installer->getTable('simaintenance/maintainers'))
    ->addColumn('maintainer_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
        'unsigned'  =>  true,
        'nullable'  =>  false,
        'primary'   =>  true,
        'identity'  =>  true,
    ),'maintenance record id')
    ->addColumn('admin_id',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'admin user id');

$installer->getConnection()->createTable($table);

$table = $installer->getConnection()->newTable($installer->getTable('simaintenance/snippets'))
    ->addColumn('snippet_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
        'unsigned'  =>  true,
        'nullable'  =>  false,
        'primary'   =>  true,
        'identity'  =>  true,
    ),'maintenance record id')
    ->addColumn('title',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'snippet title')
    ->addColumn('snippet',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'snippet description')
    ->addColumn('store_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(),'store id');

$installer->getConnection()->createTable($table);

$table = $installer->getConnection()->newTable($installer->getTable('simaintenance/periodic'))
    ->addColumn('periodic_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
        'unsigned'  =>  true,
        'nullable'  =>  false,
        'primary'   =>  true,
        'identity'  =>  true,
    ),'maintenance record id')
    ->addColumn('product_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(),'product_id')
    ->addColumn('frequency_quantity',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(),'frequency quantity')
    ->addColumn('frequency_type',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(),'frequency period type')
    ->addColumn('snippet_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(),'fk snippet id')
    ->addColumn('start_date',Varien_Db_Ddl_Table::TYPE_DATETIME,null,array(),'start date');

$installer->getConnection()->createTable($table);

$installer->endSetup();