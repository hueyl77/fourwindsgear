<?php

/** Update the use_global_future_limit to yes for all existing products  */

$productIds = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter('type_id', array('in' => array('reservation','bundle','configurable','grouped')))->getAllIds();

// Now create an array of attribute_code => values
$attributeData = array("use_global_future_limit" =>"1");

// Set the store to affect. I used admin to change all default values
$storeId = 0;

// Now update the attribute for the given products.
Mage::getSingleton('catalog/product_action')->updateAttributes($productIds, $attributeData, $storeId);
