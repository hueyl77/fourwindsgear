<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()->newTable($installer->getTable('simaintenance/status'))
    ->addColumn('status_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
        'unsigned'  =>  true,
        'nullable'  =>  false,
        'primary'   =>  true,
        'identity'  =>  true,
    ),'maintenance record id')
    ->addColumn('status',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'status')
    ->addColumn('status_system',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'status in system')
->addColumn('reserve_inventory',Varien_Db_Ddl_Table::TYPE_BOOLEAN,null,array(),'Reserve Inventory');

$installer->getConnection()->createTable($table);