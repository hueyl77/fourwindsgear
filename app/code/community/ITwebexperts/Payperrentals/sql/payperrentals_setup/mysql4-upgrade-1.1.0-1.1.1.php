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
    UPDATE {$this->getTable('payperrentals/reservationorders')} AS `reservation`
      INNER JOIN {$this->getTable('sales/order')} AS `order` ON (`reservation`.`order_id` = `order`.`increment_id`)
      SET  `reservation`.`order_id` = `order`.`entity_id`
    WHERE `otype`='order';

    CREATE TABLE {$this->getTable('payperrentals/reservationquotes')} LIKE {$this->getTable('payperrentals/reservationorders')};

    INSERT INTO {$this->getTable('payperrentals/reservationquotes')} (SELECT * FROM {$this->getTable('payperrentals/reservationorders')} WHERE `otype`='quote');

    DELETE FROM {$this->getTable('payperrentals/reservationorders')} WHERE `otype`='quote';

    ALTER TABLE {$this->getTable('payperrentals/reservationorders')}
      DROP COLUMN `otype`,
      DROP COLUMN `quote_id`,
      MODIFY `order_id`  int(10) unsigned NOT NULL,
      MODIFY `order_item_id` int(10) unsigned NOT NULL;

    ALTER TABLE {$this->getTable('payperrentals/reservationquotes')}
      DROP COLUMN `otype`,
      DROP COLUMN `order_item_id`,
      CHANGE `order_id` `quote_item_id`  int(10) unsigned NOT NULL,
      MODIFY `quote_id` int(10) unsigned NOT NULL;

     ALTER TABLE {$this->getTable('payperrentals/reservationquotes')}
      ADD CONSTRAINT `FK_PPR_QUOTES_QUOTE_ID_SALES_QUOTE_ENTITY_ID` FOREIGN KEY (`quote_id`) REFERENCES {$installer->getTable('sales/quote')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      ADD CONSTRAINT `FK_PPR_QUOTES_ORDER_ID_SALES_QUOTE_ITEM_ID` FOREIGN KEY (`quote_item_id`) REFERENCES {$installer->getTable('sales/quote_item')} (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");




/**
 * End
 */
$installer->endSetup();