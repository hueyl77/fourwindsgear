<?php

class Wavethemes_Jmbasetheme_Model_Config_Profile
{
	public function toOptionArray()
	{
        $websiteCode = Mage::app()->getRequest()->getParam("website", null);
        $storeCode = Mage::app()->getRequest()->getParam("store", null);
        $profiles = Mage::helper("jmbasetheme")->getProfiles($storeCode, 'backend', $websiteCode);
		$profiles = array_keys($profiles);
		$options = array();
        if ($profiles){
            foreach ($profiles as $f) {
                $options[] = array(
                    'value' => $f,
                    'label' => $f,
                );
            }
        }

		return $options;
	}

}
