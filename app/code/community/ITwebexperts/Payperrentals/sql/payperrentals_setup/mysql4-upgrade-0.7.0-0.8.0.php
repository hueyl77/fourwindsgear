<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup = $this;
try {
	$setup->removeAttribute('catalog_product', 'res_excluded_membership');


}catch(Exception $E) {

}

$configValuesMap = array(
	'payperrentals/notificationemails/return_reminder_auto' =>
	'payperrentals_notification_return_reminder',
	'payperrentals/notificationemails/tobebilled_email' =>
	'payperrentals_notification_tobebilled_reminder',
);
foreach ($configValuesMap as $configPath=>$configValue) {
	$installer->setConfigData($configPath, $configValue);
}

$setup->addAttribute('catalog_product', 'res_excluded_membership', array(
	'backend'       => 'eav/entity_attribute_backend_array',
	'source'        => 'payperrentals/product_excludemembership',
	'group'			=> 'Payperrentals',
	'label'         => 'Exclude Memberships',
	'input'         => 'multiselect',
	'type'          => 'text',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  30,
));

$installer->endSetup();