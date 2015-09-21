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

$attr[] = 'warehousequantity'; //attribute code to remove
$attr[] = 'warehousesupplier'; //attribute code to remove

$setup = Mage::getResourceModel('catalog/setup', 'core_setup');
try {
    $setup->startSetup();
    foreach($attr as $iAttr) {
        $setup->removeAttribute('catalog_product', $iAttr);
    }
    $setup->endSetup();
    echo $attr . " attribute is removed";
} catch (Mage_Core_Exception $e) {
    print_r($e->getMessage());
}
