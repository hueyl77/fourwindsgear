<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('payperrentals/reservationpricesdates'),'repeat_days',
    array(
            'nullable' => false,
            'primary' => false,
            'default'   =>  '',
            'type'     =>   Varien_Db_Ddl_Table::TYPE_TEXT,
            'comment'  =>  'Repeat days'
    ));



$installer->endSetup();