<?php
$statusDefaults = array(
    'status'    =>  'New',
    'status_system' =>  'New',
    'reserve_inventory' =>  1
);
Mage::getModel('simaintenance/status')->setData($statusDefaults)->save();
$statusDefaults = array(
    'status'    =>  'In Progress',
    'status_system' =>  'In Progress',
    'reserve_inventory' =>  1
);
Mage::getModel('simaintenance/status')->setData($statusDefaults)->save();
$statusDefaults = array(
    'status'    =>  'Complete',
    'status_system' =>  'Complete',
    'reserve_inventory' =>  0
);
Mage::getModel('simaintenance/status')->setData($statusDefaults)->save();
$statusDefaults = array(
    'status'    =>  'Awaiting Parts',
    'status_system' =>  'Awaiting Parts',
    'reserve_inventory' =>  1
);
Mage::getModel('simaintenance/status')->setData($statusDefaults)->save();