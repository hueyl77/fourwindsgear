<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup = $this;
$installer->run("
    ALTER TABLE {$this->getTable('payperrentals/sendreturn')} CHANGE `order_id` `order_id` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
	");
try{
$installer->run("

	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `depositppr_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_depositppr_amount` DECIMAL( 10, 2 ) NOT NULL;

    ");

$installer->run("

		ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `depositppr_amount` DECIMAL( 10, 2 ) NOT NULL;
		ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `base_depositppr_amount` DECIMAL( 10, 2 ) NOT NULL;

		");

$installer->run("

		ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `depositppr_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;
		ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_depositppr_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;

		");

$installer->run("

		ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `depositppr_amount` DECIMAL( 10, 2 ) NOT NULL;
		ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `base_depositppr_amount` DECIMAL( 10, 2 ) NOT NULL;
		");

$installer->run("

		ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `depositppr_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;
		ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_depositppr_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;

		ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `depositppr_amount` DECIMAL( 10, 2 ) NOT NULL;
		ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `base_depositppr_amount` DECIMAL( 10, 2 ) NOT NULL;

		");

}catch(Exception $e){

}

$installer->endSetup();
