<?php
/**
 * Add date attribute to quote and order item
 */

$_installer = new Mage_Sales_Model_Resource_Setup('core_setup');
$_entities = array(
    'quote_item',
    'order_item'
);
$_options = array(
    'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
    'visible' => true,
    'required' => false,
);
$_optionsForSerialize = array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'visible' => true,
    'required' => false,
);
foreach ($_entities as $_entity) {
    $_installer->addAttribute($_entity, 'start_turnover_before', $_options);
    $_installer->addAttribute($_entity, 'end_turnover_after', $_options);
    $_installer->addAttribute($_entity, 'item_booked_serialize', $_optionsForSerialize);
}


$_installer->endSetup();

$_installer = $this;
$_installer->startSetup();
$_installer->run("ALTER TABLE {$this->getTable('payperrentals/reservationorders')} ADD `start_turnover_before` DATETIME NOT NULL");
$_installer->run("ALTER TABLE {$this->getTable('payperrentals/reservationorders')} ADD `end_turnover_after` DATETIME NOT NULL");
$_installer->run("ALTER TABLE {$this->getTable('payperrentals/reservationorders')} ADD `item_booked_serialize` TEXT NOT NULL");
$_installer->endSetup();