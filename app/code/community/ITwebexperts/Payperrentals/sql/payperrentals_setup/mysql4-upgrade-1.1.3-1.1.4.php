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
    UPDATE {$this->getTable('payperrentals/sendreturn')} AS `sendreturn`
      INNER JOIN {$this->getTable('sales/order')} AS `order` ON (`sendreturn`.`order_id` = `order`.`increment_id`)
      SET  `sendreturn`.`order_id` = `order`.`entity_id`;

    ALTER TABLE {$this->getTable('payperrentals/sendreturn')}
      MODIFY `order_id`  int(10) unsigned NOT NULL;

");




/**
 * End
 */
$installer->endSetup();