<?php
/**
 *
 * @author Enrique Piatti
 */
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();
$installer->endSetup();

$installer2 = new Mage_Sales_Model_Mysql4_Setup('sales_setup');
$installer2->startSetup();


$installer2->addAttribute('order', 'event_id', array(
    'type' => 'int',
    'grid' => true,
    'unsigned'  => true
));


$installer2->addAttribute('quote', 'event_id', array(
    'type' => 'int',
    'grid' => true,
    'unsigned'  => true
));

$installer2->addAttribute('order', 'event_name', array(
    'type' => 'text',
    'grid' => true
));


$installer2->addAttribute('quote', 'event_name', array(
    'type' => 'text',
    'grid' => true
));


$installer2->addAttribute('order', 'gate_name', array(
    'type' => 'text',
    'grid' => true
));


$installer2->addAttribute('quote', 'gate_name', array(
    'type' => 'text',
    'grid' => true
));


$installer2->getConnection()->addColumn($installer2->getTable('sales/quote'), 'gate_name', 'TEXT');
$installer2->getConnection()->addColumn($installer2->getTable('sales/order'), 'gate_name', 'TEXT');

$installer2->getConnection()->addColumn($installer2->getTable('sales/quote'), 'event_name', 'TEXT');
$installer2->getConnection()->addColumn($installer2->getTable('sales/order'), 'event_name', 'TEXT');

$installer2->getConnection()->addColumn($installer2->getTable('sales/quote'), 'event_id', 'INT');
$installer2->getConnection()->addColumn($installer2->getTable('sales/order'), 'event_id', 'INT');


$installer2->endSetup();

