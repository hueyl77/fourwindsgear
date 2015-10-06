<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup = $this;

try {
	$setup->removeAttribute('catalog_product', 'autoselect_enddate');

}catch(Exception $E) {

}


$setup->addAttribute('catalog_product', 'autoselect_enddate', array(
	'backend'       => '',
	'source'  => 'eav/entity_attribute_source_boolean',
	'group'			=> 'Payperrentals',
	'label'         => 'Autoselect End Date',
	'input'         => 'select',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> '0',
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'reservation',
	'visible_on_front' => false,
	'position'      =>  10,
));

$entityTypeId = $installer->getEntityTypeId('catalog_product');

/*$idAttributeOldSelect = $this->getAttribute($entityTypeId, 'myold_attribute', 'attribute_id');
$installer->updateAttribute($entityTypeId, $idAttributeOldSelect, array(
	'input' => 'multiselect'
));*/

$fieldList = array('is_reservation', 'tax_class_id','payperrentals_quantity','payperrentals_min_number','payperrentals_min_type','payperrentals_max_number','payperrentals_max_type','payperrentals_avail_number','payperrentals_avail_type','payperrentals_avail_numberb','payperrentals_avail_typeb','payperrentals_deposit','payperrentals_has_shipping','payperrentals_has_multiply','payperrentals_pricingtype','payperrentals_use_serials','payperrentals_use_send_return','payperrentals_use_times','res_excluded_dates','res_prices','payperrentals_padding_days','res_serialnumbers','disabled_with_message','res_excluded_daysweek','allow_overbooking','global_min_period','global_max_period','global_turnover_after','global_turnover_before','global_excludedays','bundle_pricingtype');
$applyTo = array();
foreach ($fieldList as $field) {
	$applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));

	if (!in_array('bundle', $applyTo)) {
		$applyTo[] = 'bundle';
	}
	$installer->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
}

$fieldList = array('is_reservation', 'tax_class_id','payperrentals_quantity','payperrentals_min_number','payperrentals_min_type','payperrentals_max_number','payperrentals_max_type','payperrentals_avail_number','payperrentals_avail_type','payperrentals_avail_numberb','payperrentals_avail_typeb','payperrentals_deposit','payperrentals_has_shipping','payperrentals_has_multiply','payperrentals_pricingtype','payperrentals_use_serials','payperrentals_use_send_return','payperrentals_use_times','res_excluded_dates','res_prices','payperrentals_padding_days','res_serialnumbers','disabled_with_message','res_excluded_daysweek','allow_overbooking','global_min_period','global_max_period','global_turnover_after','global_turnover_before','global_excludedays','autoselect_enddate');
$applyTo = array();
foreach ($fieldList as $field) {
	$applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
	if (!in_array('grouped', $applyTo)) {
		$applyTo[] = 'grouped';
		//$installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
	}
	if (!in_array('configurable', $applyTo)) {
		$applyTo[] = 'configurable';
		//$installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
	}
	if (!in_array('reservation', $applyTo)) {
		$applyTo[] = 'reservation';
	}
	$installer->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
	//set up attribute to scope view. I am not sure about the implications so this needs some testing
	$installer->updateAttribute('catalog_product', $field, 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
}

$installer->endSetup();
