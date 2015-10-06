<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'use_global_dates');
$installer->endSetup();