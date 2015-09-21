<?php

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();

/** Remove Attributes */
$_attributesArray = array(
    'catalog_product' => array(
        'payperrentals_buyoutprice',
        'payperrentals_enable_extend',
        'payperrentals_enable_buyout',
        'payperrentals_buyout_onproduct'
    ),
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

$installer->endSetup();