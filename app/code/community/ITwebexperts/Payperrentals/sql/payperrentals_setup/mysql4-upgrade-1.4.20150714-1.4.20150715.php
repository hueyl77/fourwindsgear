<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('sales/order'),'signature_image',
    array(
        'type'     =>   Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment'  =>  'Image Name'
    ));
$installer->endSetup();