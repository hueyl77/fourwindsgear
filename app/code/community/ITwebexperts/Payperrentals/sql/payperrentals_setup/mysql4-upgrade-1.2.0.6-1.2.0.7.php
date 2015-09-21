<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup = $this;

$installer->run("
    ALTER TABLE {$this->getTable('catalog_product_entity_text')} CHANGE `value` `value` LONGTEXT;
	");

$installer->endSetup();
