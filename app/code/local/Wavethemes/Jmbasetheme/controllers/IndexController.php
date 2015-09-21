<?php

class Wavethemes_Jmbasetheme_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Frontend basetheme"));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home Page"),
            "title" => $this->__("Home Page"),
            "link" => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("frontend basetheme", array(
            "label" => $this->__("Frontend basetheme"),
            "title" => $this->__("Frontend basetheme")
        ));

        $this->renderLayout();
    }

    public function createProfileAction() {
        $result = array();
        $requests = $this->getRequest()->getParams();
        $profileName = $requests["profile"];
        $settings = $requests["settings"];
        $storeCode = isset($requests["store_code"]) ? $requests["store_code"] : null;
        $websiteCode = isset($requests["website_code"]) ? $requests["website_code"] : null;
        $location = 'backend';

        if (Mage::helper("jmbasetheme")->saveProfile($profileName . ".ini", $settings, $storeCode, $location, $websiteCode)) {
            $result['message'] = $this->__("Profile %s was successfully created!", "<span class='profile-name'>" . $profileName . "</span>");
            $result['profile'] = $profileName;
            $result['settings'] = $settings;
            $result['type'] = "new";
            //clone the core profile's params
            //clone the profile's params
            $profilePath = Mage::helper("jmbasetheme")->getProfilePath($storeCode, $location, $websiteCode);
            if (file_exists($profilePath . "core" . DS . "core.xml")) {
                $xmlContent = file_get_contents($profilePath . "core" . DS . "core.xml");
                $xmlContent = str_replace("jmbasethemecore", "jmbasetheme" . $profileName, $xmlContent);
                $xmlContent = preg_replace("/settings\s*for\s*core\s*profile/", "settings for " . $profileName . " profile", $xmlContent);
                if (!Mage::helper("jmbasetheme")->writeToFile($xmlContent, $profilePath . "local" . DS . $profileName . ".xml")) {
                    $result['error'] = "Could not save the " . $profileName . " profile settings !";
                }
            }
            //create new skin profile folder if it was not there
            $skinProfilePath = Mage::helper("jmbasetheme")->getSkinProfilePath($storeCode, $location, $websiteCode);
            if (!is_dir($skinProfilePath . $profileName)) {
                mkdir($skinProfilePath . $profileName, '0775');
            }
            //Clone the frontend css file from core
            if (file_exists($skinProfilePath . "core" . DS . "core" . ".css.php")) {
                $cssContent = trim(file_get_contents($skinProfilePath . "core" . DS . "core" . ".css.php"));
                Mage::helper("jmbasetheme")->writeToFile($cssContent, $skinProfilePath . $profileName . DS . $profileName . ".css.php");
                chmod($skinProfilePath . $profileName . DS . $profileName . ".css.php", 0775);
            }
            //clone the profile image folder
            if (is_dir($skinProfilePath . "core" . DS . "images")) {
                $src = $skinProfilePath . "core" . DS . "images";
                $dst = $skinProfilePath . $profileName . DS . "images";
                $this->rcopy($src, $dst);
            }
        } else {
            $result['error'] = $this->__("An error occurred while creating the profile!");
        }
        echo json_encode($result);
    }

    public function cloneProfileAction() {
        $result = array();
        $requests = $this->getRequest()->getParams();
        $oldProfile = $requests["old_profile"];
        $profileName = $requests["profile"];
        $settings = $requests["settings"];
        $storeCode = isset($requests["store_code"]) ? $requests["store_code"] : null;
        $websiteCode = isset($requests["website_code"]) ? $requests["website_code"] : null;
        $location = 'backend';

        //clone the profile's params
        $profilePath = Mage::helper("jmbasetheme")->getProfilePath($storeCode, $location, $websiteCode);
        if (file_exists($profilePath . "core" . DS . $oldProfile . ".xml")) {
            $xmlContent = file_get_contents($profilePath . "core" . DS . $oldProfile . ".xml");
            $xmlContent = str_replace("jmbasetheme" . $oldProfile, "jmbasetheme" . $profileName, $xmlContent);

            $xmlContent = preg_replace("/settings\s*for\s*" . $oldProfile . "\s*profile/", "settings for " . $profileName . " profile", $xmlContent);

            if (!Mage::helper("jmbasetheme")->writeToFile($xmlContent, $profilePath . "local" . DS . $profileName . ".xml")) {
                $result['error'] = $this->__("Could not save the %s profile settings!", "<span class='profile-name'>" . $profileName . "</span>");
            }
        } else if (file_exists($profilePath . "local" . DS . $oldProfile . ".xml")) {
            $xmlContent = file_get_contents($profilePath . "local" . DS . $oldProfile . ".xml");
            $xmlContent = str_replace("jmbasetheme" . $oldProfile, "jmbasetheme" . $profileName, $xmlContent);

            $xmlContent = preg_replace("/settings\s*for\s*" . $oldProfile . "\s*profile/", "settings for " . $profileName . " profile", $xmlContent);

            if (!Mage::helper("jmbasetheme")->writeToFile($xmlContent, $profilePath . "local" . DS . $profileName . ".xml")) {
                $result['error'] = $this->__("Could not save the %s profile settings!", "<span class='profile-name'>" . $profileName . "</span>");
            }
        }
        //create new skin profile folder if it was not there
        $skinProfilePath = Mage::helper("jmbasetheme")->getSkinProfilePath($storeCode, $location, $websiteCode);
        if (!is_dir($skinProfilePath . $profileName)) {
            mkdir($skinProfilePath . $profileName, 0775, true);
        }

        //Clone the frontend css file $profile.css.php
        if (file_exists($skinProfilePath . $oldProfile . DS . $oldProfile . ".css.php")) {
            $cssContent = file_get_contents($skinProfilePath . $oldProfile . DS . $oldProfile . ".css.php");
            Mage::helper("jmbasetheme")->writeToFile($cssContent, $skinProfilePath . $profileName . DS . $profileName . ".css.php");
        }
        //clone the profile image folder
        if (is_dir($skinProfilePath . $oldProfile . DS . "images")) {
            $src = $skinProfilePath . $oldProfile . DS . "images";
            $dst = $skinProfilePath . $profileName . DS . "images";
            $this->rCopy($src, $dst);
        }

        //clone the setting values
        if (Mage::helper("jmbasetheme")->saveProfile($profileName . ".ini", $settings, $storeCode, $location, $websiteCode)) {
            $result['message'] = $this->__("Profile %s was successfully cloned!", "<span class='profile-name'>" . $profileName . "</span>");
            $result['profile'] = $profileName;
            $result['old_profile'] = $oldProfile;
            $result['settings'] = $settings;
            $result['type'] = "clone";
        } else {
            $result['error'] = $this->__("An error occurred while creating the profile!");
        }
        echo json_encode($result);
    }

    public function saveProfileAction() {
        $result = array();
        $requests = $this->getRequest()->getParams();
        $profileName = $requests["profile"];
        $settings = $requests["settings"];
        $storeCode = isset($requests["store_code"]) ? $requests["store_code"] : null;
        $websiteCode = isset($requests["website_code"]) ? $requests["website_code"] : null;
        $location = 'backend';

        //check to see if this is a core profile
        $isCore = false;
        if (file_exists(Mage::helper("jmbasetheme")->getProfilePath($storeCode, $location, $websiteCode) . "core" . DS . $profileName . ".ini")) {
            $isCore = true;
        }

        if (isset($settings["deleteimages"]) && $settings["deleteimages"] !== "") {
            $deleteImages = explode(",", $settings["deleteimages"]);
            foreach ($deleteImages as $image) {
                $skinProfilePath = Mage::helper("jmbasetheme")->getSkinProfilePath($storeCode, $location, $websiteCode);
                $isDefaultImage = false;
                $delete = false;

                if (substr($image, 0, 7) == 'default') {
                    $isDefaultImage = true;
                }
                if (!$isCore || ($isCore && !$isDefaultImage)) {
                    $delete = true;
                }

                if ($delete && file_exists($skinProfilePath . $profileName . DS . "images" . DS . $image)) {
                    @unlink($skinProfilePath . $profileName . DS . "images" . DS . $image);
                }
            }
        }
        if (Mage::helper("jmbasetheme")->saveProfile($profileName . ".ini", $settings, $storeCode, $location, $websiteCode)) {
            $result['message'] = $this->__("Profile %s was successfully saved! Please wait to saving the profile for current configuration scope.", "<span class='profile-name'>" . $profileName . "</span>");
            $result['profile'] = $profileName;
            $result['settings'] = $settings;
            $result['type'] = "saveProfile";
        } else {
            $result['error'] = $this->__("An error occurred while saving the profile!");
        }

        echo json_encode($result);
    }

    public function restoreProfileAction() {
        $result = array();
        $requests = $this->getRequest()->getParams();
        $profileName = $requests["profile"];
        $storeCode = isset($requests["store_code"]) ? $requests["store_code"] : null;
        $websiteCode = isset($requests["website_code"]) ? $requests["website_code"] : null;
        $location = 'backend';

        $profilePath = Mage::helper("jmbasetheme")->getProfilePath($storeCode, $location, $websiteCode);
        if (file_exists($profilePath . "core" . DS . $profileName . ".ini")) {
            $settings = parse_ini_file($profilePath . "core" . DS . $profileName . ".ini");
            $settings["iscore"] = true;
            Mage::helper("jmbasetheme")->saveProfile($profileName . ".ini", $settings, $storeCode, $location, $websiteCode);
            $result['message'] = $this->__("Profile %s was successfully restored to default!", "<span class='profile-name'>" . $profileName . "</span>");
            $result['profile'] = $profileName;
            $result['settings'] = $settings;
            $result['type'] = "restore";
        } else {
            $result['error'] = $this->__("This is not a core profile so you can't restore it!");
        }

        echo json_encode($result);
    }

    public function deleteProfileAction() {
        $result = array();
        $requests = $this->getRequest()->getParams();
        $profileName = $requests["profile"];
        $storeCode = isset($requests["store_code"]) ? $requests["store_code"] : null;
        $websiteCode = isset($requests["website_code"]) ? $requests["website_code"] : null;
        $location = 'backend';

        $profilePath = Mage::helper("jmbasetheme")->getProfilePath($storeCode, $location, $websiteCode);
        //delete ini file
        if (file_exists($profilePath . "local" . DS . $profileName . ".ini")) {
            @unlink($profilePath . "local" . DS . $profileName . ".ini");

            //delete xml file
            if (file_exists($profilePath . "local" . DS . $profileName . ".xml")) {
                @unlink($profilePath . "local" . DS . $profileName . ".xml");
            }

            //Delete assets from skin profile
            $skinProfilePath = Mage::helper("jmbasetheme")->getSkinProfilePath($storeCode, $location, $websiteCode);
            Mage::helper('jmbasetheme')->removeDir($skinProfilePath . $profileName);

            //reset profile to default
            if ($storeCode) {
                Mage::getConfig()->saveConfig("wavethemes_jmbasetheme/jmbasethemegeneral/profile", "default", "stores", Mage::app()->getStore($storeCode)->getId());
            } else if ($websiteCode) {
                Mage::getConfig()->saveConfig("wavethemes_jmbasetheme/jmbasethemegeneral/profile", "default", "websites", Mage::app()->getWebsite($websiteCode)->getId());
            } else {
                Mage::getConfig()->saveConfig("wavethemes_jmbasetheme/jmbasethemegeneral/profile", "default");
            }

            //built result
            $result['message'] = $this->__("Profile %s was successfully deleted!", "<span class='profile-name'>" . $profileName . "</span>");
            $result['profile'] = $profileName;
            $result['type'] = "delete";
        } else {
            $result['error'] = $this->__("The profile does not exists!");
        }

        echo json_encode($result);
    }

    public function rCopy($src, $dst) {
        if (file_exists($dst))
            rmdir($dst);
        if (is_dir($src)) {
            mkdir($dst, 0775, true);
            $files = scandir($src);
            foreach ($files as $file)
                if ($file != "." && $file != "..")
                    $this->rCopy("$src/$file", "$dst/$file");
        } else if (file_exists($src))
            copy($src, $dst);
    }

}
