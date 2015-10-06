<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup = $this;

$fieldList = array('is_recurring','recurring_profile','price','tax_class_id');
foreach ($fieldList as $field) {
	$applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
	if (!in_array('membershippackage', $applyTo)) {
		$applyTo[] = 'membershippackage';
		$installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
	}
}

$fieldList = array('is_reservation');
foreach ($fieldList as $field) {
	$applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
	if (!in_array('reservation', $applyTo)) {
		$applyTo[] = 'reservation';
		$installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
	}
}

try {
	$setup->removeAttribute('catalog_product', 'number_items');
	$setup->removeAttribute('customer', 'membershippackage_nametest2');
	$setup->removeAttribute('customer', 'membershippackage_name');
	$setup->removeAttribute('customer', 'membershippackage_cc');
	$setup->removeAttribute('customer', 'membershippackage_payment');
	$setup->removeAttribute('customer', 'membershippackage_enabled');
	$setup->removeAttribute('customer', 'membershippackage_day');
	$setup->removeAttribute('customer', 'membershippackage_month');
	$setup->removeAttribute('customer', 'membershippackage_year');
	$setup->removeAttribute('customer', 'membershippackage_orderid');

}catch(Exception $E) {

}


$setup->addAttribute('catalog_product', 'number_items', array(
	'backend'       => '',
	'source'        => '',
	'group'			=> 'Recurring Profile',
	'label'         => 'Number of Items',
	'input'         => 'text',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'membershippackage',
	'visible_on_front' => false,
	'position'      =>  10,
));

//$setup = Mage::getModel('customer/entity_setup', 'core_setup');
$setup->addAttribute('customer', 'membershippackage_name', array(
	'type' => 'int',
	'input' => 'select',
	'group'	=> 'Membership',
	'label' => 'Package Name',
	'global' => 1,
	'visible' => 1,
	'required' => 0,
	'user_defined' => 1,
	'default' => '0',
	'visible_on_front' => 1,
	'source'=> 'payperrentals/customer_membershippackage_name',
));
if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
	$customer = Mage::getModel('customer/customer');
	$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
	$setup->addAttributeToSet('customer', $attrSetId, 'Membership', 'membershippackage_name');
}
if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
	Mage::getSingleton('eav/config')
			->getAttribute('customer', 'membershippackage_name')
			->setData('used_in_forms', array('adminhtml_customer_membership'/*,'customer_account_create','customer_account_edit','checkout_register'*/))
			->save();
}

/*$entityTypeId = $installer->getEntityTypeId('customer');
$attributeId  = $installer->getAttributeId('customer', 'membershippackage_nametest1');

$attributeSets = $installer->getConnection()->fetchAll('select * from '.$this->getTable('eav/attribute_set').' where entity_type_id=?', $entityTypeId);
foreach ($attributeSets as $attributeSet) {
	$setId = $attributeSet['attribute_set_id'];
	$installer->addAttributeGroup($entityTypeId, $setId, 'Membership');
	$groupId = $installer->getAttributeGroupId($entityTypeId, $setId, 'Membership');
	$installer->addAttributeToGroup($entityTypeId, $setId, $groupId, $attributeId);
}
*/



$setup->addAttribute('customer', 'membershippackage_cc', array(
	'type' => 'text',
	'input' => 'text',
	'group'	=> 'Membership',
	'label' => 'Credit Card',
	'global' => 1,
	'visible' => 1,
	'required' => 0,
	'user_defined' => 1,
	'default' => '0',
	'visible_on_front' => 1,
	'source'=> 'payperrentals/customer_membershippackage_cc',
));
if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
	$customer = Mage::getModel('customer/customer');
	$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
	$setup->addAttributeToSet('customer', $attrSetId, 'Membership', 'membershippackage_cc');
}
if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
	Mage::getSingleton('eav/config')
			->getAttribute('customer', 'membershippackage_cc')
			->setData('used_in_forms', array('adminhtml_customer_membership'/*,'customer_account_create','customer_account_edit','checkout_register'*/))
			->save();
}


$setup->addAttribute('customer', 'membershippackage_payment', array(
	'type' => 'int',
	'input' => 'select',
	'group' => 'Membership',
	'label' => 'Payment Type',
	'global' => 1,
	'visible' => 1,
	'required' => 0,
	'user_defined' => 1,
	'default' => '0',
	'visible_on_front' => 1,
	'source'=> 'payperrentals/customer_membershippackage_payment',
));
if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
	$customer = Mage::getModel('customer/customer');
	$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
	$setup->addAttributeToSet('customer', $attrSetId, 'Membership', 'membershippackage_payment');
}
if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
	Mage::getSingleton('eav/config')
			->getAttribute('customer', 'membershippackage_payment')
			->setData('used_in_forms', array('adminhtml_customer_membership'/*,'customer_account_create','customer_account_edit','checkout_register'*/))
			->save();
}

$setup->addAttribute('customer', 'membershippackage_enabled', array(
	'type' => 'int',
	'input' => 'select',
	'group' => 'Membership',
	'label' => 'Account Activated',
	'global' => 1,
	'visible' => 1,
	'required' => 0,
	'user_defined' => 1,
	'default' => '0',
	'visible_on_front' => 1,
	'source'=> 'payperrentals/customer_membershippackage_enabled',
));
if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
	$customer = Mage::getModel('customer/customer');
	$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
	$setup->addAttributeToSet('customer', $attrSetId, 'Membership', 'membershippackage_enabled');
}
if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
	Mage::getSingleton('eav/config')
			->getAttribute('customer', 'membershippackage_enabled')
			->setData('used_in_forms', array('adminhtml_customer_membership'/*,'customer_account_create','customer_account_edit','checkout_register'*/))
			->save();
}

$setup->addAttribute('customer', 'membershippackage_day', array(
	'type' => 'int',
	'input' => 'select',
	'group' => 'Membership',
	'label' => 'Day',
	'global' => 1,
	'visible' => 1,
	'required' => 0,
	'user_defined' => 1,
	'default' => '0',
	'visible_on_front' => 1,
	'source'=> 'payperrentals/customer_membershippackage_day',
));
if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
	$customer = Mage::getModel('customer/customer');
	$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
	$setup->addAttributeToSet('customer', $attrSetId, 'Membership', 'membershippackage_day');
}
if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
	Mage::getSingleton('eav/config')
			->getAttribute('customer', 'membershippackage_day')
			->setData('used_in_forms', array('adminhtml_customer_membership'/*,'customer_account_create','customer_account_edit','checkout_register'*/))
			->save();
}

$setup->addAttribute('customer', 'membershippackage_month', array(
	'type' => 'int',
	'input' => 'select',
	'group' => 'Membership',
	'label' => 'Month',
	'global' => 1,
	'visible' => 1,
	'required' => 0,
	'user_defined' => 1,
	'default' => '0',
	'visible_on_front' => 1,
	'source'=> 'payperrentals/customer_membershippackage_month',
));
if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
	$customer = Mage::getModel('customer/customer');
	$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
	$setup->addAttributeToSet('customer', $attrSetId, 'Membership', 'membershippackage_month');
}
if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
	Mage::getSingleton('eav/config')
			->getAttribute('customer', 'membershippackage_month')
			->setData('used_in_forms', array('adminhtml_customer_membership'/*,'customer_account_create','customer_account_edit','checkout_register'*/))
			->save();
}

$setup->addAttribute('customer', 'membershippackage_year', array(
	'type' => 'int',
	'input' => 'select',
	'group' => 'Membership',
	'label' => 'Year',
	'global' => 1,
	'visible' => 1,
	'required' => 0,
	'user_defined' => 1,
	'default' => '0',
	'visible_on_front' => 1,
	'source'=> 'payperrentals/customer_membershippackage_year',
));
if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
	$customer = Mage::getModel('customer/customer');
	$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
	$setup->addAttributeToSet('customer', $attrSetId, 'Membership', 'membershippackage_year');
}
if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
	Mage::getSingleton('eav/config')
			->getAttribute('customer', 'membershippackage_year')
			->setData('used_in_forms', array('adminhtml_customer_membership'/*,'customer_account_create','customer_account_edit','checkout_register'*/))
			->save();
}

/*$setup->addAttribute('customer', 'membershippackage_orderid', array(
	'type' => 'text',
	'input' => 'text',
	'label' => 'Membership',
	'global' => 1,
	'visible' => 0,
	'required' => 0,
	'user_defined' => 1,
	'default' => '0',
	'visible_on_front' => 1,
	'source'=> '',
	'backend'       => 'payperrentals/customer_backend_membershiporderid',
));
if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
	$customer = Mage::getModel('customer/customer');
	$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
	$setup->addAttributeToSet('customer', $attrSetId, 'Membership', 'membershippackage_orderid');
}
if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
	Mage::getSingleton('eav/config')
			->getAttribute('customer', 'membershippackage_orderid')
			->save();
}
*/

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('payperrentals/rentalqueue')};

	CREATE TABLE {$this->getTable('payperrentals/rentalqueue')} (
		`id` INT( 11 ) NOT NULL auto_increment,
		`product_id` INT( 11 ) NOT NULL ,
		`customer_id` INT( 11 ) NOT NULL ,
		`store_id` INT( 11 ) NOT NULL ,
		`sort_order` INT( 11 ) NOT NULL DEFAULT '0',
		`date_added` DATETIME NOT NULL ,
		`custom_options` TEXT NOT NULL DEFAULT '',
		`attributes` TEXT NOT NULL DEFAULT '',
		`sendreturn_id` INT( 11 ) NOT NULL DEFAULT '0',
		PRIMARY KEY ( `id` ) ,
		KEY `store_id` (`store_id`),
		INDEX ( `product_id` , `customer_id` , `sendreturn_id` )

	) DEFAULT CHARSET utf8 ENGINE = InnoDB;

	");

$installer->endSetup();
