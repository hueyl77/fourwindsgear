<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup = $this;

function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") rrmdir($dir . "/" . $object); else unlink($dir . "/" . $object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}
try {
    $app = Mage::getBaseDir('app') . '/code/local/';
    if (is_dir($app . 'Excellence')) {
        rrmdir($app . 'Excellence');
    }
    if (is_dir($app . 'Itweb')) {
        rrmdir($app . 'Itweb');
    }
    if (is_dir($app . 'Kenneth')) {
        rrmdir($app . 'Kenneth');
    }
    if (is_dir($app . 'Mage/Sales/Block/Adminhtml/Recurring/Profile/Renderer')) {
        rrmdir($app . 'Mage/Sales/Block/Adminhtml/Recurring/Profile/Renderer');
    }

    if (file_exists($app . 'Mage/Catalog/Block/Product.php')) {
        unlink($app . 'Mage/Catalog/Block/Product.php');
    }
    if (file_exists($app . 'Mage/Catalog/Block/Product/Abstract.php')) {
        unlink($app . 'Mage/Catalog/Block/Product/Abstract.php');
    }
    if (file_exists($app . 'Mage/Paygate/Model/Authorizenet.php')) {
        unlink($app . 'Mage/Paygate/Model/Authorizenet.php');
    }
    if (file_exists($app . 'Mage/Paypal/Model/Express/Checkout.php')) {
        unlink($app . 'Mage/Paypal/Model/Express/Checkout.php');
    }
    if (file_exists($app . 'Mage/Sales/Model/Order/Payment.php')) {
        unlink($app . 'Mage/Sales/Model/Order/Payment.php');
    }
    if (file_exists($app . 'Mage/Sales/Model/Order.php')) {
        unlink($app . 'Mage/Sales/Model/Order.php');
    }
    if (file_exists($app . 'Mage/Sales/Block/Adminhtml/Recurring/Profile.php')) {
        unlink($app . 'Mage/Sales/Block/Adminhtml/Recurring/Profile.php');
    }
    if (file_exists($app . 'Mage/Sales/Block/Adminhtml/Recurring/Profile/Grid.php')) {
        unlink($app . 'Mage/Sales/Block/Adminhtml/Recurring/Profile/Grid.php');
    }
}catch(Exception $e){

}
$installer->endSetup();
