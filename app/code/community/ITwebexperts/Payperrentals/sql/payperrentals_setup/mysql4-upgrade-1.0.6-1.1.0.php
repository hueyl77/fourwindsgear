<?php
/**
 * Start
 */
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

/**
 * Exclude dates
 */
$installer->run("
    ALTER TABLE {$this->getTable('payperrentals/excludeddates')}
      CHANGE `entity_id` `product_id` int(10) unsigned NOT NULL DEFAULT '0',
      MODIFY `store_id`  smallint(5) unsigned NOT NULL DEFAULT '0';

    ALTER TABLE {$this->getTable('payperrentals/excludeddates')}
      ADD CONSTRAINT `FK_PPR_EXCLUDEDATES_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES {$installer->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      ADD CONSTRAINT `FK_PPR_EXCLUDEDATES_PRODUCT_ID_CATALOG_PRODUCT_ID` FOREIGN KEY (`product_id`) REFERENCES {$installer->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

");




/**
 * End
 */
$installer->endSetup();