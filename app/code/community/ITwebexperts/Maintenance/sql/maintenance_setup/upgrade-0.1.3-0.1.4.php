<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()->addColumn($installer->getTable('simaintenance/periodic'),'maintainer_id',array(
    'type'  =>  Varien_Db_Ddl_Table::TYPE_INTEGER,
    'comment'   =>  'Maintainer Id'));

$installer->endSetup();