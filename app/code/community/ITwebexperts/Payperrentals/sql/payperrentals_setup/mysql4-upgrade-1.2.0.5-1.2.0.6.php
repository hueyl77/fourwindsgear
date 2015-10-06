<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
// Product attributes
try {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('payperrentals/reservationpricesdates'))
        ->addColumn(
            'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'identity' => true,
                'nullable' => false,
                'primary'  => true,
            ), 'id'
        )
        ->addColumn(
            'description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'unsigned' => true,
                'nullable' => true,
            ), 'id'
        )
        ->addColumn(
            'store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 100, array(
                'nullable' => true,
                'default'  => null,
            ), 'Product Id'
        )
        ->addColumn(
            'disabled_type', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => true,
                'default'  => null,
            ), 'Disabled Type'
        )
        ->addColumn(
            'date_from', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
                'nullable' => true,
                'default'  => null,
            ), 'From'
        )
        ->addColumn(
            'date_to', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
                'nullable' => true,
                'default'  => null,
            ), 'To'
        );

    $installer->getConnection()->addColumn(
        $installer->getTable('payperrentals/reservationprices'), 'reservationpricesdates_id',
        array(
            'nullable' => true,
            'primary'  => false,
            'default'  => null,
            'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'comment'  => 'Reservationpricesdates FK'
        )
    );

    $installer->getConnection()->createTable($table);
}catch(Exception $e){

}

$installer->endSetup();