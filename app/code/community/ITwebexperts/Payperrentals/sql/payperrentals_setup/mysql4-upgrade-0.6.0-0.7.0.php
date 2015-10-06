<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup = $this;

$installer->run("
    ALTER TABLE {$this->getTable('payperrentals/reservationorders')} CHANGE `order_id` `order_id` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
	");

$installer->endSetup();
