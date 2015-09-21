<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();

$write = Mage::getSingleton('core/resource')->getConnection('core_write');
$write->query("update ".Mage::getSingleton('core/resource')->getTableName('payperrentals_reservationorders')." set item_booked_serialize=?", array(''));
$write->query("update ".Mage::getSingleton('core/resource')->getTableName('payperrentals_reservationquotes')." set item_booked_serialize=?", array(''));

$installer->endSetup();