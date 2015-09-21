<?php
if (isset($_SERVER['REQUEST_METHOD'])) {
    die('This script cannot be run from Browser. This is the shell script.');
}
$_sapiType = php_sapi_name();
/*if (substr($_sapiType, 0, 3) != 'cli') {
    echo "Script can be executed only from cli";
    return;
}*/
$workingDir = dirname(__FILE__);
if(strpos($workingDir,'.modman') > 0) {
    require_once $workingDir . '/../../../app/Mage.php';
}else{
    require_once $workingDir . '/../app/Mage.php';
}

Mage::init('admin');

/** Remove all reservation products */
$_productsCollection = Mage::getResourceModel('catalog/product_collection')
    ->addAttributeToFilter('type_id', array(
        array('eq' => 'reservation'),
        array('eq' => 'membershippackage')
    ));
try {
    $_productsCollection->delete();
} catch (Exception $_e) {
    Mage::logException($_e);
}
/**************/

/** Drop tables */
$_tablesArray = array(
    'payperrentals' => array('excludeddates', 'mailinglog', 'ordertodates', 'rentalqueue', 'reservationorders', 'reservationprices', 'reservationquotes', 'sendreturn', 'serialnumbers', 'serialnumbersdetails'),
    'rshipping' => array('products', 'rshipping')
);
$_setupModel = new Mage_Core_Model_Resource_Setup('core_setup');
foreach ($_tablesArray as $_modelPrefix => $_tableArray) {
    foreach ($_tableArray as $_table) {
        try {
            $_setupModel->getConnection()->dropTable($_setupModel->getTable($_modelPrefix . '/' . $_table));
        } catch (Exception $_e) {
            Mage::logException($_e);
        }
    }
}
/**************/
/** Drop added columns */
$_columnsArray = array(
    'sales/quote' => array(
        'start_datetime',
        'end_datetime',
        'return_datetime',
        'send_datetime',
        'estimate_send',
        'estimate_return',
        'event_id',
        'event_name',
        'gate_name',
        'damage_waiver_amount',
        'base_damage_waiver_amount',
        'damage_waiver_amount_invoiced',
        'base_damage_waiver_amount_invoiced',
        'damage_waiver_amount_refunded',
        'base_damage_waiver_amount_refunded',
        'depositppr_amount',
        'base_depositppr_amount',
        'depositppr_amount_invoiced',
        'base_depositppr_amount_invoiced',
        'depositppr_amount_refunded',
        'base_depositppr_amount_refunded'
    ),
    'sales/order' => array(
        'start_datetime',
        'end_datetime',
        'return_datetime',
        'send_datetime',
        'estimate_send',
        'estimate_return',
        'event_id',
        'event_name',
        'gate_name',
        'damage_waiver_amount',
        'base_damage_waiver_amount',
        'damage_waiver_amount_invoiced',
        'base_damage_waiver_amount_invoiced',
        'damage_waiver_amount_refunded',
        'base_damage_waiver_amount_refunded',
        'depositppr_amount',
        'base_depositppr_amount',
        'depositppr_amount_invoiced',
        'base_depositppr_amount_invoiced',
        'depositppr_amount_refunded',
        'base_depositppr_amount_refunded'
    ),
    'sales/quote_item' => array(
        'start_turnover_before',
        'end_turnover_after',
        'item_booked_serialize'
    ),
    'sales/order_item' => array(
        'start_turnover_before',
        'end_turnover_after',
        'item_booked_serialize'
    )
);
foreach ($_columnsArray as $_table => $_columnArray) {
    foreach ($_columnArray as $_column) {
        try {
            $_setupModel->getConnection()->dropColumn($_setupModel->getTable($_table), $_column);
        } catch (Exception $_e) {
            Mage::logException($_e);
        }
    }
}
/**************/

/** Remove Attributes */
$_attributesArray = array(
    'catalog_product' => array(
        'payperrentals_quantity',
        'payperrentals_min_number',
        'payperrentals_min_type',
        'global_min_period',
        'payperrentals_max_number',
        'payperrentals_max_type',
        'global_max_period',
        'payperrentals_avail_number',
        'payperrentals_avail_type',
        'global_turnover_after',
        'payperrentals_avail_numberb',
        'payperrentals_avail_typeb',
        'global_turnover_before',
        'payperrentals_deposit',
        'payperrentals_has_shipping',
        'payperrentals_has_multiply',
        'payperrentals_pricingtype',
        'payperrentals_use_send_return',
        'payperrentals_use_times',
        'payperrentals_padding_days',
        'disabled_with_message',
        'res_excluded_daysweek',
        'global_excludedays',
        'allow_overbooking',
        'res_excluded_dates',
        'res_prices',
        'payperrentals_use_serials',
        'res_serialnumbers',
        'is_reservation',
        'number_items',
        'bundle_pricingtype',
        'autoselect_enddate',
        'res_excluded_membership',
        'hide_end_date',
        'show_time_grid',
        'use_global_exclude_dates',
        'payperrentals_buyoutprice',
        'payperrentals_enable_extend',
        'payperrentals_enable_buyout',
        'payperrentals_buyout_onproduct',
        'use_global_dates',
        'reservation_cost',
        'allow_shipping',
        'shipping_method',
        'enable_extend_order',
        'maintenance_quantity'
    ),
    'customer' => array(
        'membershippackage_name',
        'membershippackage_cc',
        'membershippackage_payment',
        'membershippackage_enabled',
        'membershippackage_day',
        'membershippackage_month',
        'membershippackage_year'
    ),
    'order' => array(
        'start_datetime',
        'end_datetime',
        'return_datetime',
        'send_datetime',
        'estimate_send',
        'estimate_return',
        'event_id',
        'event_name',
        'gate_name',
        'damage_waiver_amount',
        'base_damage_waiver_amount',
        'damage_waiver_amount_invoiced',
        'base_damage_waiver_amount_invoiced',
        'damage_waiver_amount_refunded',
        'base_damage_waiver_amount_refunded'
    )
);

$_attributeModel = new Mage_Sales_Model_Resource_Setup('sales_setup');
foreach ($_attributesArray as $_attributeType => $_attributeArray) {
    foreach ($_attributeArray as $_attributeCode) {
        try {
            $_attributeModel->removeAttribute($_attributeType, $_attributeCode);
        } catch (Exception $_e) {
            Mage::logException($_e);
        }
    }
}
/**************/

/** Remove resource data*/
$_setupModel = new Mage_Core_Model_Resource_Setup('core_setup');
$_setupModel->deleteTableRow($_setupModel->getTable('core/resource'), 'code', 'payperrentals_setup');
$_setupModel->deleteTableRow($_setupModel->getTable('core/resource'), 'code', 'rshipping_setup');
/**************/