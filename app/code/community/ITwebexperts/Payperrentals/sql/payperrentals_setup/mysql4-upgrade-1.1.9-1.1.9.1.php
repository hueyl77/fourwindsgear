<?php
/**
 * Start
 */
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

/**
 * Reservation orders and reservation quotes separation
 */
$installer->run("
    ALTER TABLE {$this->getTable('payperrentals/sendreturn')} ADD INDEX ( `product_id` );
    ALTER TABLE {$this->getTable('payperrentals/reservationorders')} ADD INDEX ( `product_id` );
    ALTER TABLE {$this->getTable('payperrentals/reservationquotes')} ADD INDEX ( `product_id` );
    ALTER TABLE {$this->getTable('payperrentals/reservationprices')}
    ADD COLUMN  `damage_waiver` int(11) NOT NULL DEFAULT '0' AFTER `price`;
");




/**
 * End
 */
$installer->endSetup();