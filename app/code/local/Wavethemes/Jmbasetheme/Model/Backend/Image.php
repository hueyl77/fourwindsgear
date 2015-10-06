<?php
/*------------------------------------------------------------------------
# $JM#PRODUCT_NAME$ - Version $JM#VERSION$ - Licence Owner $JM#OWNER$
# ------------------------------------------------------------------------
# Copyright (C) 2004-2009 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: J.O.O.M Solutions Co., Ltd
# Websites: http://www.ubertheme.com/ - http://www.joomlancers.com
# This file may not be redistributed in whole or significant part.
-------------------------------------------------------------------------*/

class Wavethemes_Jmbasetheme_Model_Backend_Image extends Mage_Adminhtml_Model_System_Config_Backend_File
{
    protected $_maxFileSize = 0;

    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return array('jpg', 'jpeg', 'gif', 'png');
    }

    protected function setMaxUploadFileSize($maxSize){
        if($maxSize){
            $this->_maxFileSize = $maxSize;
        }
    }

    /**
     * Return the root part of directory path for uploading
     *
     * @var string
     * @return string
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();

        if ($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'])
        {
            $uploadDir = $this->_getUploadDir();
            try {
                $file = array();
                $tmpName = $_FILES['groups']['tmp_name'];
                $file['tmp_name'] = $tmpName[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $name = $_FILES['groups']['name'];
                $file['name'] = $name[$this->getGroupId()]['fields'][$this->getField()]['value'];

                //Check exists
                if(file_exists($uploadDir.DS.$file['name']))
                {
                    $msg = "+ " . Mage::helper('jmbasetheme')->__("Image with named %s was existed", $file['name']);
                    Mage::getSingleton('core/session')->addNotice($msg);
                    return $this;
                }
                else
                {
                    $uploader = new Mage_Core_Model_File_Uploader($file);
                    $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                    $uploader->setAllowRenameFiles(true);

                    $maxImageSize = 2 * 1024; //2MB
                    $this->setMaxUploadFileSize($maxImageSize);
                    $uploader->addValidateCallback('size', $this, 'validateMaxSize');

                    $result = $uploader->save($uploadDir);
                }
            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
                return $this;
            }

            $filename = $result['file'];
            if ($filename) {
                if ($this->_addWhetherScopeInfo()) {
                    $filename = $this->_prependScopeInfo($filename);
                }
                $this->setValue($filename);
            }
        } else {
            if (is_array($value) && !empty($value['delete'])) {
                $this->setValue('');
            } else {
                $this->unsValue();
            }
        }
        return $this;
    }

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throw Mage_Core_Exception
     */
    protected function _getUploadDir()
    {
        $fieldConfig = $this->getFieldConfig();
        /* @var $fieldConfig Varien_Simplexml_Element */

        if (empty($fieldConfig->upload_dir)) {
            Mage::throwException(Mage::helper('catalog')->__('The base directory to upload file is not specified.'));
        }

        $uploadDir = (string)$fieldConfig->upload_dir;

        $el = $fieldConfig->descend('upload_dir');

        /**
         * Add scope info
         */
        if (!empty($el['scope_info'])) {
            $uploadDir = $this->_appendScopeInfo($uploadDir);
        }

        /**
         * Take root from config
         */
        if (!empty($el['config'])) {
            $uploadRoot = $this->_getUploadRoot((string)$el['config']);
            $uploadDir = $uploadRoot .DS. $uploadDir;
        }

        return $uploadDir;
    }

    /**
     * Prepend path with scope info
     *
     * E.g. 'stores/2/path' , 'websites/3/path', 'default/path'
     *
     * @param string $path
     * @return string
     */
    protected function _prependScopeInfo($path)
    {
        $scopeInfo = $this->getScope();
        if ('default' != $this->getScope()) {
            $scopeInfo .= DS . $this->getScopeId();
        }
        return $scopeInfo . DS . $path;
    }

    /**
     * Add scope info to path
     *
     * E.g. 'path/stores/2' , 'path/websites/3', 'path/default'
     *
     * @param string $path
     * @return string
     */
    protected function _appendScopeInfo($path)
    {
        $path .= DS . $this->getScope();
        if ('default' != $this->getScope()) {
            $path .= DS . $this->getScopeId();
        }
        return $path;
    }

    protected function _getUploadRoot($token)
    {
        $helper = Mage::helper("jmbasetheme");
        $groups = Mage::app()->getRequest()->getPost('groups');
        $profile = $groups['jmbasethemegeneral']['fields']['profile']['value'];
        if (!$profile) {
            $profile = $helper->getProfileName(null, 'backend');
        }
        $skinProfilePath = $helper->getSkinProfilePath(null, 'backend');

        return $skinProfilePath.DS.$profile.DS;
    }
}
