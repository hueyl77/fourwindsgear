<?php
$templateRecord = array(
    'title'     =>  'General Maintenance',
    'snippet'   =>  'General review of product condition'
);
Mage::getModel('simaintenance/snippets')->setData($templateRecord)->save();