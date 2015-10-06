<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute('customer','membershippackage_day');
$setup->removeAttribute('customer','membershippackage_month');
$setup->removeAttribute('customer','membershippackage_year');
$setup->removeAttribute('customer','membershippackage_payment');

/** fix sort orders */

$setup->updateAttribute('customer','membershippackage_name',array(
   'position'   =>  10
));

$setup->updateAttribute('customer','membershippackage_enabled',array(
    'position'   =>  20
));

$setup->updateAttribute('customer','membershippackage_cc',array(
    'position'   =>  30
));

/** Add membershippackage_ccexpiremonth, membershippackage_ccexpireyear, membershippackage_otherpayment, membershippackage_start, membershippackage_lastbilled, membershippackage_notes  */

$setup->addAttribute('customer', 'membershippackage_ccmonth', array(
    'type' => 'text',
    'input' => 'select',
    'group' => 'Membership',
    'label' => 'CC Expires Month',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'position'  =>  31,
    'user_defined' => 1,
    'default' => '0',
    'visible_on_front' => 0,
    'source'=> 'payperrentals/customer_membershippackage_ccmonth',
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'membershippackage_ccmonth')
        ->setData('used_in_forms', array('adminhtml_customer_membership'))
        ->save();
}

$setup->addAttribute('customer', 'membershippackage_ccyear', array(
    'type' => 'text',
    'input' => 'select',
    'group' => 'Membership',
    'label' => 'CC Expires Year',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'position'  =>  32,
    'user_defined' => 1,
    'default' => '0',
    'visible_on_front' => 0,
    'source'=> 'payperrentals/customer_membershippackage_ccyear',
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'membershippackage_ccyear')
        ->setData('used_in_forms', array('adminhtml_customer_membership'))
        ->save();
}

$setup->addAttribute('customer', 'membershippackage_otherpayment', array(
    'type' => 'varchar',
    'input' => 'textarea',
    'group' => 'Membership',
    'label' => 'Other Payment Method Details',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'position'  =>  40,
    'user_defined' => 1,
    'default' => '0',
    'visible_on_front' => 0,
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'membershippackage_otherpayment')
        ->setData('used_in_forms', array('adminhtml_customer_membership'))
        ->save();
}

$setup->addAttribute('customer', 'membershippackage_start', array(
    'type' => 'datetime',
    'input' => 'datetime',
    'group' => 'Membership',
    'label' => 'Membership Start Date',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'position'  =>  50,
    'user_defined' => 1,
    'default' => '0',
    'visible_on_front' => 0
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'membershippackage_start')
        ->setData('used_in_forms', array('adminhtml_customer_membership'))
        ->save();
}

$setup->addAttribute('customer', 'membershippackage_lastbilled', array(
    'type' => 'datetime',
    'input' => 'datetime',
    'group' => 'Membership',
    'label' => 'Membership Last Billed Date',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'position'  =>  60,
    'user_defined' => 1,
    'default' => '0',
    'visible_on_front' => 0
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'membershippackage_lastbilled')
        ->setData('used_in_forms', array('adminhtml_customer_membership'))
        ->save();
}

$setup->addAttribute('customer', 'membershippackage_notes', array(
    'type' => 'varchar',
    'input' => 'textarea',
    'group' => 'Membership',
    'label' => 'Membership Notes',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'position'  =>  70,
    'user_defined' => 1,
    'default' => '0',
    'visible_on_front' => 0
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'membershippackage_notes')
        ->setData('used_in_forms', array('adminhtml_customer_membership'))
        ->save();
}

$installer->endSetup();