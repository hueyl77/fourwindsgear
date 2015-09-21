<?php

class Wavethemes_Jmbasetheme_Model_Observer
{
	/**
	 * Extend extra configs from etc folder of module on system init configs
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function extendConfig($observer)
	{
		$storeCode = Mage::helper("jmbasetheme")->getCurrentStoreCode("backend");
		$profiles = array_keys(Mage::helper("jmbasetheme")->getProfiles($storeCode));
		$mergeObject = new Mage_Core_Model_Config_Base();
		$profilePath =  Mage::helper("jmbasetheme")->getProfilePath($storeCode);
		foreach ($profiles as $profile) {
			if (file_exists($profilePath . "core" . DS . $profile . ".xml")) {
				$mergeObject->loadFile($profilePath . "core" . DS . $profile . ".xml");
			} else {
				$mergeObject->loadFile($profilePath . "local" . DS . $profile . ".xml");
			}
			$observer->config->extend($mergeObject, false);
		}

		if (file_exists($profilePath . "core" . DS . "core.xml")) {
			$mergeObject->loadFile($profilePath . "core" . DS . "core.xml");
			$observer->config->extend($mergeObject, false);
		}

		//extend tablet settings
		$mergeObject->loadFile(Mage::getModuleDir('etc', 'Wavethemes_Jmbasetheme') . "/device.xml");
		$observer->config->extend($mergeObject, false);

		//extend mobile settings
		$mergeObject->loadFile(Mage::getModuleDir('etc', 'Wavethemes_Jmbasetheme') . "/mobile.xml");
		$observer->config->extend($mergeObject, false);
	}
    
    public function generateProfileCSS($observer)
    {
        if($observer->getData('section') == 'wavethemes_jmbasetheme'){
            Mage::helper("jmbasetheme")->generateProfileCSS('backend');
            
            //Unset color cookie in jmbasetheme
            $storeCode = Mage::helper("jmbasetheme")->getCurrentStoreCode("backend");
            $cookieName = Mage::helper("jmbasetheme")->getDefaultTheme($storeCode, 'backend')."_active_color";
            if (isset($_COOKIE[$cookieName])){
                setcookie($cookieName, null, time() - 3600, "/");
            }
        }
    }

	/**
	 * Update the Toolbar content after layout generate blocks when on mobile mode.
	 *
	 * @param Varien_Event_Observer $observer
	 *
	 */
	public function resetToolbar($observer)
	{
		if (!Mage::app()->getStore()->isAdmin()) {
			$toolbar = $observer['layout']->getBlock("product_list_toolbar");
			$devicedetect = Mage::helper('jmbasetheme/mobiledetect');
			$baseconfig = Mage::helper("jmbasetheme")->getProfileContent();
			if (Mage::registry('current_category') && $toolbar && $devicedetect->isMobile() && $baseconfig['quanlityperpage'] != "") {
				$toolbar->addPagerLimit("grid", $baseconfig["quanlityperpage"]);
				$toolbar->addPagerLimit("list", 8);
			}
		}
	}

}
	
	

