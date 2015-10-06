<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()->addColumn($installer->getTable('simaintenance/items'),'specific_dates',array(
    'type'  =>  Varien_Db_Ddl_Table::TYPE_INTEGER,
    'comment'   =>  'If uses specific dates'));

$installer->endSetup();