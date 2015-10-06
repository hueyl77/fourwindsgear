<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('sales/order'),'signature_date',
    array(
        'type'     =>   Varien_Db_Ddl_Table::TYPE_DATETIME,
        'comment'  =>  'Signed date'
    ));
$installer->getConnection()->addColumn($installer->getTable('sales/order'),'signature_text',
    array(
        'type'     =>   Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment'  =>  'Signed text'
    ));
$installer->getConnection()->addColumn($installer->getTable('sales/order'),'signature_xynative',
    array(
        'type'     =>   Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 65535,
        'comment'  =>  'Native signature xy format'
    ));
$installer->endSetup();