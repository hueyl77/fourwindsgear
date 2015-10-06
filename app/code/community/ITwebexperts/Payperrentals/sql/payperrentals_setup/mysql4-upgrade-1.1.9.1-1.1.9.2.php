<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup = $this;
try {

    $installer->run(
        "

	ALTER TABLE  `" . $this->getTable('sales/order') . "` ADD  `damage_waiver_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `" . $this->getTable('sales/order') . "` ADD  `base_damage_waiver_amount` DECIMAL( 10, 2 ) NOT NULL;

    "
    );

    $installer->run(
        "

		ALTER TABLE  `" . $this->getTable('sales/quote_address') . "` ADD  `damage_waiver_amount` DECIMAL( 10, 2 ) NOT NULL;
		ALTER TABLE  `" . $this->getTable('sales/quote_address') . "` ADD  `base_damage_waiver_amount` DECIMAL( 10, 2 ) NOT NULL;

		"
    );

    $installer->run(
        "

		ALTER TABLE  `" . $this->getTable('sales/order') . "` ADD  `damage_waiver_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;
		ALTER TABLE  `" . $this->getTable('sales/order') . "` ADD  `base_damage_waiver_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;

		"
    );

    $installer->run(
        "

		ALTER TABLE  `" . $this->getTable('sales/invoice') . "` ADD  `damage_waiver_amount` DECIMAL( 10, 2 ) NOT NULL;
		ALTER TABLE  `" . $this->getTable('sales/invoice') . "` ADD  `base_damage_waiver_amount` DECIMAL( 10, 2 ) NOT NULL;
		"
    );

    $installer->run(
        "

		ALTER TABLE  `" . $this->getTable('sales/order') . "` ADD  `damage_waiver_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;
		ALTER TABLE  `" . $this->getTable('sales/order') . "` ADD  `base_damage_waiver_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;

		ALTER TABLE  `" . $this->getTable('sales/creditmemo') . "` ADD  `damage_waiver_amount` DECIMAL( 10, 2 ) NOT NULL;
		ALTER TABLE  `" . $this->getTable('sales/creditmemo') . "` ADD  `base_damage_waiver_amount` DECIMAL( 10, 2 ) NOT NULL;

		"
    );

}catch(Exception $e){

}
$installer->endSetup();
