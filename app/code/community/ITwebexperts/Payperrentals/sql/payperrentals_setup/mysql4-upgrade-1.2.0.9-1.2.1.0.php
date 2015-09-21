<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('payperrentals/reservationorders'),'comments',
    array(
        'nullable' => true,
        'primary' => false,
        'default'   =>  null,
        'type'     =>   Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment'  =>  'comments'
    ));

$installer->endSetup();