<?php
/** @var $this Mage_Core_Model_Resource_Setup */
$_installer = $this;
$_installer->startSetup();

$_setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$_installer->getConnection()
    ->addColumn($_installer->getTable('rshipping'), 'turnover_before_type', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'default' => '3',
        'comment' => 'Turnover Before Type'
    ));

$_installer->endSetup();