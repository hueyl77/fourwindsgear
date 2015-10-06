<?php

$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

/**
 * Fixes all indexes
 */
$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/reservationorders'),
        'IDX_start_end',
        array('start_date','end_date'));

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/reservationorders'),
        'IDX_send_return_id',
        'sendreturn_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/reservationorders'),
        'IDX_turnover_start_end',
        array('start_turnover_before','end_turnover_after'));

$installer->getConnection()
    ->dropKey(
        $installer->getTable('payperrentals/reservationprices'),
        'entity_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/reservationprices'),
        'IDX_entity_id',
        'entity_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/reservationprices'),
        'IDX_store_id',
        'store_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/reservationprices'),
        'IDX_date_from_to',
        array('date_from','date_to'));

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/reservationprices'),
        'IDX_ptype',
        'ptype');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/reservationprices'),
        'IDX_numberof',
        'numberof');

$installer->getConnection()
    ->dropKey(
        $installer->getTable('payperrentals/sendreturn'),
        'order_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/sendreturn'),
        'IDX_order_id',
        'order_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/sendreturn'),
        'IDX_order_id',
        'order_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/sendreturn'),
        'IDX_res_start_end',
        array('res_startdate','res_enddate'));

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/sendreturn'),
        'IDX_send_return',
        array('send_date','return_date'));

$installer->getConnection()
    ->dropKey(
        $installer->getTable('payperrentals/serialnumbers'),
        'entity_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/serialnumbers'),
        'IDX_entity_id',
        'entity_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/serialnumbers'),
        'IDX_status',
        'status');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/ordertodates'),
        'IDX_event_date',
        'event_date');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/rentalqueue'),
        'IDX_customer_id',
        'customer_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/rentalqueue'),
        'IDX_sendreturn_id',
        'sendreturn_id');

$installer->getConnection()
    ->dropKey(
        $installer->getTable('payperrentals/rentalqueue'),
        'product_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/rentalqueue'),
        'IDX_product_id',
        'product_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/reservationquotes'),
        'IDX_quote_item_id',
        'quote_item_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/reservationquotes'),
        'IDX_quote_id',
        'quote_id');

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/reservationquotes'),
        'IDX_start_end',
        array('start_date','end_date'));

$installer->getConnection()
    ->addKey(
        $installer->getTable('payperrentals/reservationquotes'),
        'IDX_sendreturn',
        'sendreturn_id');
/**
 * End
 */
$installer->endSetup();