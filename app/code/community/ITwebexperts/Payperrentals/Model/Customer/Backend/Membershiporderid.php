<?php

class ITwebexperts_Payperrentals_Model_Customer_Backend_Membershiporderid extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract {


    protected function _getCustomer() {
        return Mage::registry('customer');
    }

    public function validate($object) {

        //$sns = $object->getData($this->getAttribute()->getName());
        //if (empty($sns)) {
          //  return $this;
       // }


        return $this;
    }

    public function afterSave($object) {
		        //if the details are saved here I have to save a new order with the details, somehow the same thing must be done for upgrade/downgrade with some kind of flag.
				//for upgrade/downgrade/the order should be updated with the new details.

				//$sns = $object->getData($this->getAttribute()->getName());

				//Mage::getResourceSingleton('payperrentals/serialnumbers')->deleteByEntityId($object->getId());

				//foreach ($sns as $k=>$sn) {
					//if(!is_numeric($k)) continue;

				/*	$ex = Mage::getModel('payperrentals/serialnumbers')
						->setEntityId($this->_getProduct()->getId())
						->setSn($sn['sn'])
						->setStatus($sn['status'])
						->save();*/
				//}
        return $this;
    }
}
