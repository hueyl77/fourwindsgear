<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup = $this;

$fieldList = array('is_reservation', 'tax_class_id','payperrentals_quantity','payperrentals_min_number','payperrentals_min_type','payperrentals_max_number','payperrentals_max_type','payperrentals_avail_number','payperrentals_avail_type','payperrentals_avail_numberb','payperrentals_avail_typeb','payperrentals_deposit','payperrentals_has_shipping','payperrentals_has_multiply','payperrentals_pricingtype','payperrentals_use_serials','payperrentals_use_send_return','payperrentals_use_times','res_excluded_dates','res_prices','payperrentals_padding_days','res_serialnumbers','disabled_with_message','res_excluded_daysweek','allow_overbooking','global_min_period','global_max_period','global_turnover_after','global_turnover_before','global_excludedays');
foreach ($fieldList as $field) {
	$applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));

	if (!in_array('bundle', $applyTo)) {
		$applyTo[] = 'bundle';
	}
	$installer->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
}

try {
	$setup->removeAttribute('catalog_product', 'bundle_pricingtype');

}catch(Exception $E) {

}


$setup->addAttribute('catalog_product', 'bundle_pricingtype', array(
	'backend'       => '',
	'source'        => 'payperrentals/product_bundlepricingtype',
	'group'			=> 'Payperrentals',
	'label'         => 'Bundle Pricing Type',
	'input'         => 'select',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'bundle',
	'visible_on_front' => false,
	'position'      =>  10,
));

$installer->endSetup();
