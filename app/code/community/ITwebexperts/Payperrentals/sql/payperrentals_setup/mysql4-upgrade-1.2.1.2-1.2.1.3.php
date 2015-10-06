<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->getConnection()->addColumn($installer->getTable('payperrentals/serialnumbers'),'date_acquired',
    array(
        'nullable' => true,
        'primary' => false,
        'default'   =>  null,
        'type'     =>   Varien_Db_Ddl_Table::TYPE_DATETIME,
        'comment'  =>  'Date Inventory Was Acquired'
    ));

$installer->endSetup();