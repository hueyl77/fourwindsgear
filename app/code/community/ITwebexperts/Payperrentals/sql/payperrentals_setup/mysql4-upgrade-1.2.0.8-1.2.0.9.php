<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
// Product attributes
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$configValuesMap = array(
    'payperrentals/notificationemails/extend_reminder_email' =>
        'payperrentals_notification_extend_reminder'
);
foreach ($configValuesMap as $configPath=>$configValue) {
    $installer->setConfigData($configPath, $configValue);
}

$installer->endSetup();