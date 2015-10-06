<?php

class Wavethemes_Jmbasetheme_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $defaultStoreCode = null;
    protected $activeWebsiteCode = null;
	protected $activeStoreCode = null;
	protected $cookieExp = null;
    
	public function __construct()
	{
		//set cookie expire fo active color
		$this->cookieExp = time() + 60 * 60 * 24 * 355; // one year

		//initial default store code of current website
		$this->defaultStoreCode = Mage::app()->getWebsite()->getDefaultStore()->getCode();

		//initial current active store code
		if(isset($_GET["___store"])){
			$this->activeStoreCode = $_GET["___store"];
			//Set cookie for later
            $timeLife = time() + 60 * 60 * 24; // 1 days
			setcookie("active_store", $this->activeStoreCode, $timeLife, "/");
		}
		else if(isset($_COOKIE["active_store"])){
			$this->activeStoreCode = $_COOKIE["active_store"];
        }else{
            $this->activeStoreCode = Mage::app()->getStore()->getCode();
        }

        //make cookie website for other context
        if(!isset($_COOKIE["active_website"])){
            $this->activeWebsiteCode = Mage::app()->getWebsite()->getCode();
            //Set cookie for later
            $timeLife = time() + 60 * 60 * 24; // 1 days
            setcookie("active_website", $this->activeWebsiteCode, $timeLife, "/");
        } else {
            $this->activeWebsiteCode = $_COOKIE["active_website"];
        }

		//set active color if has from request
		if(isset($_GET['jmcolor'])){
			//Set cookie for later
			setcookie($this->getDefaultTheme()."_active_color", $_GET['jmcolor'], $this->cookieExp, "/");
		}
	}

	public function getSkinPath($storeCode = null, $location = 'frontend', $websiteCode = null)
	{
        $storeCode = ($storeCode) ? $storeCode :$this->getCurrentStoreCode($location);
		$designPackage = $this->getDesignPackage($storeCode, $location, $websiteCode);
		$defaultTheme = $this->getDefaultTheme($storeCode, $location, $websiteCode);

		$skinPath = Mage::getBaseDir("skin") . DS . "frontend" . DS . $designPackage . DS . $defaultTheme;
		if (!is_dir($skinPath . DS . "wavethemes". DS . "jmbasetheme")) {
			mkdir($skinPath . DS . "wavethemes" . DS . "jmbasetheme", 0775, true);
		}
		$skinPath = $skinPath . DS . "wavethemes" . DS . "jmbasetheme";

		return $skinPath;
	}

	public function getCssPath($storeCode = null)
	{
		$skinPath = $this->getSkinPath($storeCode);
		$cssPath = $skinPath . DS . "css" . DS;
		if (!is_dir($cssPath)) {
			mkdir($cssPath, 0775);
		}

		return $cssPath;
	}

	public function getSkinProfilePath($storeCode = null, $location = 'frontend', $websiteCode = null)
	{
		$skinPath = $this->getSkinPath($storeCode, $location, $websiteCode);
		$skinProfilePath = $skinPath . DS . "profiles" . DS;
		if (!is_dir($skinProfilePath)) {
			mkdir($skinProfilePath, 0775);
		}

		return $skinProfilePath;
	}

	public function getDesignPath($storeCode = null, $location = 'frontend', $websiteCode = null)
	{
		$designPackage = $this->getDesignPackage($storeCode, $location, $websiteCode);
		$defaultTheme = $this->getDefaultTheme($storeCode, $location, $websiteCode);
		$designPath = Mage::getBaseDir("design") . DS . "frontend" . DS . $designPackage . DS . $defaultTheme;

		return $designPath;
	}

	public function getProfilePath($storeCode = null, $location = 'frontend', $websiteCode = null)
	{
		$designPath = $this->getDesignPath($storeCode, $location, $websiteCode);
		if (!is_dir($designPath . DS . "profiles" . DS . "core")) {
			mkdir($designPath . DS . "profiles" . DS . "core", 0775, true);
		}
		if (!is_dir($designPath . DS . "profiles" . DS . "local")) {
			mkdir($designPath . DS . "profiles" . DS . "local", 0775, true);
		}
		$profilePath = $designPath . DS . "profiles" . DS;

		return $profilePath;
	}

	public function getProfileContent($location = 'frontend', $websiteCode = null)
    {
		$storeCode = $this->getCurrentStoreCode($location);
        if (!$storeCode){
            if ($location == 'backend'){
                $websiteCode = Mage::app()->getRequest()->getParam("website", null);
            }else{ // frontend
                $websiteCode = ($websiteCode) ? $websiteCode : $this->activeWebsiteCode;
            }
        }

		$profileName = $this->getProfileName($storeCode, $location, $websiteCode);
		$profilePath = $this->getProfilePath($storeCode, $location, $websiteCode);

		if (file_exists($profilePath . "local" . DS . $profileName . ".ini")) {
			$configs = parse_ini_file($profilePath . "local" . DS . $profileName . ".ini");
		} else {
            if (file_exists($profilePath . "core" . DS . $profileName . ".ini")) {
                $configs = parse_ini_file($profilePath . "core" . DS . $profileName . ".ini");
            } else {
                $configs = array();
            }
		}

		//Get the correct device fields
		$device = Mage::helper('jmbasetheme/mobiledetect');
		if ($device->isTablet()) {
			$configs["productlistdeslenght"] = isset($configs["productlistdeslenghttablet"]) && $configs["productlistdeslenghttablet"] ? $configs["productlistdeslenghttablet"] : $configs["productlistdeslenght"];
			$configs["showlabel"] = isset($configs["showlabeltablet"]) && $configs["showlabeltablet"] ? $configs["showlabeltablet"] : $configs["showlabel"];
			$configs["productgridnumbercolumn"] = isset($configs["productgridnumbercolumntablet"]) && $configs["productgridnumbercolumntablet"] ? $configs["productgridnumbercolumntablet"] : $configs["productgridnumbercolumn"];
			$configs["quanlityperpage"] = isset($configs["quanlityperpagetablet"]) && $configs["quanlityperpagetablet"] ? $configs["quanlityperpagetablet"] : "";
			$configs["productlimageheight"] = isset($configs["productlimageheighttablet"]) && $configs["productlimageheighttablet"] ? $configs["productlimageheighttablet"] : $configs["productlimageheight"];
			$configs["productlimageheightportrait"] = isset($configs["productlimageheighttabletportrait"]) && $configs["productlimageheighttabletportrait"] ? $configs["productlimageheighttabletportrait"] : $configs["productlimageheight"];
			if ($configs["productlimageheightportrait"] > $configs["productlimageheight"]) $configs["productlimageheight"] = $configs["productlimageheightportrait"];
			$configs["productlimagewidth"] = isset($configs["productlimagewidthtablet"]) && $configs["productlimagewidthtablet"] ? $configs["productlimagewidthtablet"] : $configs["productlimagewidth"];
			$configs["productlimagewidthportrait"] = isset($configs["productlimagewidthtabletportrait"]) && $configs["productlimagewidthtabletportrait"] ? $configs["productlimagewidthtabletportrait"] : $configs["productlimagewidth"];
			if ($configs["productlimagewidthportrait"] > $configs["productlimagewidth"]) $configs["productlimagewidth"] = $configs["productlimagewidthportrait"];

		} else if ($device->isMobile()) {
			$configs["productlistdeslenght"] = isset($configs["productlistdeslenghttmobile"]) && $configs["productlistdeslenghttmobile"] ? $configs["productlistdeslenghtmobile"] : $configs["productlistdeslenght"];
			$configs["showlabel"] = isset($configs["showlabelmobile"]) && $configs["showlabelmobile"] ? $configs["showlabelmobile"] : $configs["showlabel"];
			$configs["productgridnumbercolumn"] = isset($configs["productgridnumbercolumnmobile"]) && $configs["productgridnumbercolumnmobile"] ? $configs["productgridnumbercolumnmobile"] : $configs["productgridnumbercolumn"];
			$configs["quanlityperpage"] = isset($configs["quanlityperpagemobile"]) && $configs["quanlityperpagemobile"] ? $configs["quanlityperpagemobile"] : "";
			$configs["productlimageheight"] = isset($configs["productlimageheightmobile"]) && $configs["productlimageheightmobile"] ? $configs["productlimageheightmobile"] : $configs["productlimageheight"];
			$configs["productlimagewidth"] = isset($configs["productlimagewidthmobile"]) && $configs["productlimagewidthmobile"] ? $configs["productlimagewidthmobile"] : $configs["productlimagewidth"];
		}

		return $configs;
	}

	//This function for old themes & extensions
	public function getactiveprofile()
	{
		return $this->getProfileContent();
	}

    public function getDesignPackage($storeCode = null, $location = "frontend", $websiteCode = null)
    {
        $websiteCode = ($websiteCode) ? $websiteCode : Mage::app()->getRequest()->getParam("website", null);
        $storeCode = ($storeCode) ? $storeCode : $this->getCurrentStoreCode($location);
        if ($storeCode) {
            //$package = Mage::getStoreConfig('design/package/name', $storeCode);
            $package = Mage::getConfig()->getNode('design/package/name','stores', $storeCode);
        }else if ($websiteCode){
            //$package = Mage::app()->getWebsite($websiteCode)->getConfig('design/package/name');
            $package = Mage::getConfig()->getNode('design/package/name','websites', $websiteCode);
        }else {
            //$package = (Mage::getStoreConfig('design/package/name')) ? Mage::getStoreConfig('design/package/name') : "default";
            $package = Mage::getConfig()->getNode('design/package/name','default');
        }

        return ($package) ? trim($package) : 'default';
    }

	public function getDefaultTheme($storeCode = null, $location = "frontend", $websiteCode = null)
	{
        $storeCode = ($storeCode) ? $storeCode : $this->getCurrentStoreCode($location);
        $websiteCode = ($websiteCode) ? $websiteCode : Mage::app()->getRequest()->getParam("website", null);
        if ($storeCode) {
            //$theme = Mage::getStoreConfig('design/theme/default', $storeCode);
            $theme = Mage::getConfig()->getNode('design/theme/default', 'stores', $storeCode);
        }else if ($websiteCode) {
            //$theme = Mage::app()->getWebsite($websiteCode)->getConfig('design/theme/default');
            $theme = Mage::getConfig()->getNode('design/theme/default', 'websites', $websiteCode);
        }else {
            $theme = Mage::getConfig()->getNode('design/theme/default', 'default');
        }

		return ($theme) ? trim($theme) : 'default';
	}

	//This function for old themes & extensions
	public function gettheme()
	{
		return $this->getDefaultTheme();
	}

	/**
	 * Get Profile Name
	 *
	 * @return string profile name
	 */
	public function getProfileName($storeCode = null, $location = 'frontend', $websiteCode = null)
    {
        $websiteCode = ($websiteCode) ? $websiteCode : Mage::app()->getRequest()->getParam("website", null);
        $storeCode = ($storeCode) ? $storeCode : $this->getCurrentStoreCode($location);

		$cookieName = $this->getDefaultTheme()."_active_color";
        if (isset($_GET['jmcolor'])) {
			$profileName = $_GET['jmcolor'];
		}
        else if ($location == 'frontend' AND (isset($_COOKIE[$cookieName]) AND $_COOKIE[$cookieName])) {
			$profileName = $_COOKIE[$cookieName];
		} 
        else {
            if ($storeCode){
                $profileName = Mage::getConfig()->getNode('wavethemes_jmbasetheme/jmbasethemegeneral/profile', 'stores', $storeCode);
            }else if ($websiteCode) {
                $profileName = Mage::getConfig()->getNode('wavethemes_jmbasetheme/jmbasethemegeneral/profile', 'websites', $websiteCode);
            } else {
                $profileName = Mage::getConfig()->getNode('wavethemes_jmbasetheme/jmbasethemegeneral/profile', 'default');
            }
		}
    
		return $profileName;
	}

	//This function for old themes & extensions
	public function getprofile()
	{
		return $this->getProfileName();
	}

	public function getProfiles($storeCode = null, $location = 'frontend', $websiteCode = null)
	{
		$profiles = array();
		$coreProfiles = array();
		$localProfiles = array();

		$profiles["default"] = new stdclass();
		$profilePath = $this->getProfilePath($storeCode, $location, $websiteCode);

		$coreFiles = $this->files($profilePath . "core", '\.ini');

		if ($coreFiles) {
			foreach ($coreFiles as $file) {
				$coreProfiles[strtolower(substr($file, 0, -4))] = parse_ini_file($profilePath . "core" . DS . $file);
			}
		}
		$localFiles = $this->files($profilePath . DS . "local", '\.ini');
		if ($localFiles) {
			foreach ($localFiles as $file) {
				$localProfiles[strtolower(substr($file, 0, -4))] = parse_ini_file($profilePath . "local" . DS . $file);
			}
		}

		$profiles = array_merge($coreProfiles, $localProfiles);

		if (empty($profiles)) {
			$profiles["default"] = new stdclass();
		}
		foreach ($profiles as $profile => $settings) {
			if (file_exists($profilePath . "core" . DS . $profile . ".ini")) {
				$profiles[$profile]["iscore"] = true;
			}
		}

		return $profiles;
	}

	public function files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS'))
	{
		// Initialize variables
		$arr = array();

		// Is the path a folder?
		if (!is_dir($path)) {
			return false;
		}

		// read the source directory
		$handle = opendir($path);
		while (($file = readdir($handle)) !== false) {
			if (($file != '.') && ($file != '..') && (!in_array($file, $exclude))) {
				$dir = $path . DS . $file;
				$isDir = is_dir($dir);
				if ($isDir) {
					if ($recurse) {
						if (is_integer($recurse)) {
							$arr2 = $this->files($dir, $filter, $recurse - 1, $fullpath);
						} else {
							$arr2 = $this->files($dir, $filter, $recurse, $fullpath);
						}

						$arr = array_merge($arr, $arr2);
					}
				} else {
					if (preg_match("/$filter/", $file)) {
						if ($fullpath) {
							$arr[] = $path . DS . $file;
						} else {
							$arr[] = $file;
						}
					}
				}
			}
		}
		closedir($handle);

		asort($arr);
		return $arr;
	}

	public function write_ini_file($assoc_arr, $path)
	{
		$content = "\n";
		foreach ($assoc_arr as $key => $elem) {
			if (is_array($elem)) {
				for ($i = 0; $i < count($elem); $i++) {
					$content .= $key . "[] = \"" . $elem[$i] . "\"\r\n";
				}
			} else if ($elem == "") $content .= $key . " = \r\n";
			else $content .= $key . " = \"" . $elem . "\"\r\n";
		}

		if (!$handle = fopen($path, 'w')) {
			return false;
		}
		if (!fwrite($handle, $content)) {
			return false;
		}
		fclose($handle);
		return true;
	}

	public function saveProfile($profileName, $assocArr, $storeCode = null, $location = 'backend', $websiteCode = null)
	{
		return $this->write_ini_file($assocArr, $this->getProfilePath($storeCode, $location, $websiteCode) . "local" . DS . $profileName);
	}

	public function writeToFile($content, $path)
	{
		if (!$handle = fopen($path, 'w')) {
			return false;
		}
		if (!fwrite($handle, $content)) {
			return false;
		}
		fclose($handle);
		return true;
	}

	public function removeDir($dir)
    {
		$this->emptyDirectory($dir, true);
	}

	public function emptyDirectory($dirname, $self_delete = false)
    {
		if (is_dir($dirname))
			$dir_handle = opendir($dirname);
		if (!$dir_handle){
			return false;
		}
		while ($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				if (!is_dir($dirname . DIRECTORY_SEPARATOR . $file))
					@unlink($dirname . DIRECTORY_SEPARATOR . $file);
				else
					self::emptyDirectory($dirname . DIRECTORY_SEPARATOR . $file, true);
			}
		}
		closedir($dir_handle);

		if ($self_delete){
			@rmdir($dirname);
		}

		return true;
	}

	public function getCurrentStoreCode($location = 'frontend')
    {
		if($location == 'frontend'){
			$storeCode = ($this->activeStoreCode) ? $this->activeStoreCode : $this->defaultStoreCode;
		}else if($location == 'backend'){
			$storeCode = Mage::app()->getRequest()->getParam('store', null);
		}

		return $storeCode;
	}

    public function getBaseUrl($storeCode = null, $location = 'frontend')
    {
        $storeCode = ($storeCode) ? $storeCode : $this->getCurrentStoreCode($location);
        if (Mage::getStoreConfig("web/secure/use_in_adminhtml")) {
            $baseUrl = Mage::getStoreConfig("web/secure/base_url", $storeCode);
        } else {
            $baseUrl = Mage::getBaseUrl();
        }

        return $baseUrl;
    }

    public function getAssignedProfiles($storeCode = null, $location = 'backend', $websiteCode = null)
    {
        $profiles = $this->getProfiles($storeCode, $location, $websiteCode);

        //Get assigned profiles
        $profileNames = array_keys($profiles);
        $assignedProfiles = array();
        $stores = Mage::app()->getStores();
        foreach ($stores as $store){
            $assignProfile = Mage::getStoreConfig("wavethemes_jmbasetheme/jmbasethemegeneral/profile", $store->getCode());
            if(in_array($assignProfile, $profileNames)){
                $assignedProfiles[$store->getId()] = $assignProfile;
            }
        }

        return $assignedProfiles;
    }

    public function listDirectories($path, $fullPath = false)
    {
        $result = array();
        $dir = opendir($path);
        if ($dir) {
            while ($entry = readdir($dir)) {
                if (substr($entry, 0, 1) == '.' || !is_dir($path . DS . $entry)){
                    continue;
                }
                if ($fullPath) {
                    $entry = $path . DS . $entry;
                }
                $result[] = $entry;
            }
            unset($entry);
            closedir($dir);
        }

        return $result;
    }

    public function listFiles($path, $fullPath = false)
    {
        $result = array();
        if (is_dir($path)){
            $dir = opendir($path);
            if ($dir) {
                while ($entry = readdir($dir)) {
                    if (substr($entry, 0, 1) == '.' || is_dir($path . DS . $entry)){
                        continue;
                    }
                    if ($fullPath) {
                        $entry = $path . DS . $entry;
                    }
                    $result[] = $entry;
                }
                unset($entry);
                closedir($dir);
            }
        }

        return $result;
    }

    public function saveConfig($configName, $value, $store = null, $website = null)
    {
        if ($configName){
            if (strlen($store)) // store level
            {
                $store_id = Mage::getModel('core/store')->load($store)->getId();
                $scope = "stores";
                $scope_id = $store_id;
            }
            elseif (strlen($website)) // website level
            {
                $website_id = Mage::getModel('core/website')->load($website)->getId();
                //$store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
                $scope = "websites";
                $scope_id = $website_id;
            }
            else // default level
            {
                $scope = "default";
                $scope_id = 0;
                //$store_id = 0;
            }

            $config = Mage::getConfig();
            $config->saveConfig($configName, $value, $scope, $scope_id);
            $config->cleanCache();
        }
    }
    
    public function getCSSProfileUrl()
    {
        $url = null;
        $activeProfile = null;
        $websiteCode = null;
        $location = 'frontend';
        $storeCode = $this->getCurrentStoreCode($location);
        if (!$storeCode) {
            //get current website code
            $websiteCode = Mage::app()->getWebsite()->getCode();
        }
        if (isset($_GET['jmcolor'])) {
			$activeProfile = $_GET['jmcolor'];
		}
		if (!$activeProfile) {
			$activeProfile = $this->getProfileName($storeCode, $location, $websiteCode);
		}

        $cssFileTemplate = $this->getSkinProfilePath($storeCode, $location, $websiteCode) . $activeProfile . DS . $activeProfile . ".css.php";
        if (file_exists($cssFileTemplate)) {
            $cssFile = $this->getSkinProfilePath($storeCode, $location, $websiteCode) . $activeProfile . DS . $activeProfile . ".css";
            if (!file_exists($cssFile)) {
                //generate
                $this->generateProfileCSS();
            }
            if (file_exists($cssFile)){
                $url = "wavethemes/jmbasetheme/profiles/{$activeProfile}/{$activeProfile}.css";
            }
        }
        
        return $url;
    }
    
    public function generateProfileCSS($location = 'frontend', $storeCode = null, $websiteCode = null)
    {
        $activeProfile = $this->getProfileName($storeCode, $location, $websiteCode);
        $skinPath = $this->getSkinProfilePath($storeCode, $location, $websiteCode);
        $cssTemplateFilePath = $skinPath . $activeProfile . DS . $activeProfile . ".css.php";
        if (file_exists($cssTemplateFilePath)) {
            try{
                $cssContent = $this->_generateCSSContent($location, $cssTemplateFilePath);
                $cssFilePath = str_replace('.php', '', $cssTemplateFilePath);
                //write to file.css
                $file = new Varien_Io_File(); 
                $file->setAllowCreateFolders(true); 
                $file->open(array( 'path' => $skinPath . $activeProfile )); 
                $file->streamOpen($cssFilePath, 'w+'); 
                $file->streamLock(true); 
                $file->streamWrite($cssContent); 
                $file->streamUnlock(); 
                $file->streamClose(); 
            }catch (Exception $e){ 
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jmbasetheme')->__('Failed generating CSS file: %s', $cssFilePath). '<br/>Message: ' . $e->getMessage());
                Mage::logException($e);
            }
        }
    }
    
    protected function _generateCSSContent($location, $cssTmpl = null)
    {
        $result = null;
        if ($cssTmpl){
            ob_start();
            try {
                //pre-process for old themes (make before 06/16/2015)
                $this->_updateCSSTemplate($cssTmpl);
                $baseconfig = $this->getProfileContent($location);
                include $cssTmpl;
            }catch (Exception $e) {
                ob_get_clean();
                throw $e;
            }
            $result = ob_get_clean();
        }
        
        return $result;
    }
    
    private function _updateCSSTemplate($tmlPath)
    {
        if($tmlPath){
            $content = file_get_contents($tmlPath);
            if (strpos($content, "mageFilename")){ //if not replace yet
                //comment not needed code (old code in old themes make before 06/16/2015)
                $content = preg_replace('/(\<\?php).*?(\?\>)/si', '', $content, 1);
                file_put_contents($tmlPath, $content);
            }
        }
    }
}
	 