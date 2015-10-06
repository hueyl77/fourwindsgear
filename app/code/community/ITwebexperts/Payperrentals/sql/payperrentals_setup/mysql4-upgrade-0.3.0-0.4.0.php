<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup = $this;

$configValuesMap = array(
	'payperrentals/notificationemails/return_reminder' =>
	'payperrentals_notification_reminder',
	'payperrentals/notificationemails/return_email' =>
	'payperrentals_notification_return',
	'payperrentals/notificationemails/send_email' =>
	'payperrentals_notification_send',
);

foreach ($configValuesMap as $configPath=>$configValue) {
	$installer->setConfigData($configPath, $configValue);
}

$installer->endSetup();
