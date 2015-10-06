<?php

/*------------------------------------------------------------------------
# $JA#PRODUCT_NAME$ - Version $JA#VERSION$ - Licence Owner $JA#OWNER$
# ------------------------------------------------------------------------
# Copyright (C) 2004-2009 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: J.O.O.M Solutions Co., Ltd
# Websites: http://www.joomlart.com - http://www.joomlancers.com
# This file may not be redistributed in whole or significant part.
-------------------------------------------------------------------------*/

class Wavethemes_Jmmegamenu_Adminhtml_JmmegamenuController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('joomlart/groups')->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Manager'),
            Mage::helper('adminhtml')->__('Menu Manager'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function groupAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $id= $this->getRequest()->getParam('id');

        $menuModel = Mage::getModel('jmmegamenu/jmmegamenu')->load($id);

        if ($menuModel->getId() || $id == 0) {

            if ($id == 0) {
                $groupId = $this->getRequest()->getParam('groupid', null);
                if ($groupId){
                    $menuModel->setData("menugroup", $groupId);
                }
                $menuModel->setData("menutype", 2);
                $menuModel->setData("mega_subcontent", 1);
                $menuModel->setData("mega_group", 0);
                $menuModel->setData("mega_cols", 1);
                $menuModel->setData("showtitle", 1);
            }

            if ($menuModel->getData('menugroup')){
                $groupModel = Mage::getModel('jmmegamenu/jmmegamenugroup')->load($menuModel->getData('menugroup'));
                Mage::register('jmmegamenugroup_data', $groupModel);
            }

            Mage::register('jmmegamenu_data', $menuModel);

            $this->loadLayout();
            $this->_setActiveMenu('jmmegamenu/items');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Manager'), Mage::helper('adminhtml')->__('Menu Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Description'), Mage::helper('adminhtml')->__('Menu Description'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('jmmegamenu/adminhtml_jmmegamenu_edit'))
                ->_addLeft($this->getLayout()->createBlock('jmmegamenu/adminhtml_jmmegamenu_edit_tabs'));

            $this->renderLayout();

        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jmmegamenu')->__('Menu Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editgroupAction()
    {
        $id = $this->getRequest()->getParam('id');

        $groupModel = Mage::getModel('jmmegamenu/jmmegamenugroup')->load($id);

        if ($groupModel->getId() || $id == 0) {

            Mage::register('jmmegamenugroup_data', $groupModel);
            $this->loadLayout();
            $this->_setActiveMenu('jmmegamenu/group');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Group Configuration'), Mage::helper('adminhtml')->__('Menu Group Configuration'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Group Configuration'), Mage::helper('adminhtml')->__('Menu Group Configuration'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('jmmegamenu/adminhtml_jmmegamenugroup_edit'))
                ->_addLeft($this->getLayout()->createBlock('jmmegamenu/adminhtml_jmmegamenugroup_edit_tabs'));

            $this->renderLayout();

        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jmmegamenu')->__('Menu Group does not exist'));
            $this->_redirect('*/*/');
        }
    }


    public function newgroupAction()
    {
        $this->_forward('editgroup');
    }

    protected function getNextorder($where)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $menutable = $resource->getTableName('jmmegamenu');
        $sqlquery = 'SELECT MAX(ordering) From ' . $menutable . ' where ' . $where;
        $rows = $read->fetchAll($sqlquery);
        if ($rows) {
            return $rows[0]['MAX(ordering)'] + 1;
        }

    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('image');
                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);
                    // Set the file upload mode
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);
                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS . "jmmegamenu" . DS;
                    $uploader->save($path, $_FILES['image']['name']);
                } catch (Exception $e) {
                }
                //this way the name is saved in DB
                $image = "jmmegamenu/" . $_FILES['image']['name'];
            }
            try {
                $postData = $this->getRequest()->getPost();
                if (isset($postData['image']['delete']) && $postData['image']['delete'] == 1) {
                    $path = Mage::getBaseDir('media') . DS . $postData['image']['value'];
                    $path = preg_replace("/\//", "\\", $path);
                    unlink($path);
                    $postData['image'] = "";
                } else {
                    if (isset($image)) $postData['image'] = $image;
                    else unset($postData['image']);
                }
                $resource = Mage::getSingleton('core/resource');
                $read = $resource->getConnection('core_read');

                //Check exists menu item alias
                $menutable = $resource->getTableName('jmmegamenu');
                if (!empty($postData["menualias"])) {
                    $query = 'SELECT tblmega.menu_id'
                        . ' FROM ' . $menutable
                        . ' as tblmega WHERE tblmega.menualias = "' . $postData["menualias"]
                        . '" and tblmega.menu_id != "' . $this->getRequest()->getParam('id')
                        . '"  ORDER BY tblmega.menu_id';
                    $rows = $read->fetchAll($query);
                    if (count($rows)) {
                        throw new Exception('The alias already used by another menu item.');
                    }
                }

                $postData['link'] = str_replace(Mage::getBaseUrl(), "", $postData['link']);
                $postData['category'] = str_replace(Mage::getBaseUrl(), "", $postData['category']);
                $postData['cms'] = str_replace(Mage::getBaseUrl(), "", $postData['cms']);

                $menuModel = Mage::getModel('jmmegamenu/jmmegamenu');
                $helper = Mage::helper('jmmegamenu');
                if ($this->getRequest()->getParam('id') <= 0) { // if add new
                    $menuModel->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
                    $where = " status >= 0 AND parent = " . (int)$postData['parent'];
                    $postData['ordering'] = $this->getNextorder($where);
                }

                //Update category id
                if(!$postData['menutype']){ // is category menu item type
                    if(!$postData['catid']){
                        $category = $helper->getCategoryByUrl($postData['category']);
                        if($category){
                            $postData['catid'] = $category->getId();
                        }
                    }
                }
                $menuModel
                    ->addData($postData)
                    ->setUpdateTime(Mage::getSingleton('core/date')->gmtDate())
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();

                $helper->reorder(' parent= ' . (int)$postData['parent']);

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Menu Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setJmMegamenuData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $menuModel->getId()));
                    return;
                }
                $this->_redirect('*/*/index/groupid/' . $postData['menugroup']);
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setJmMegamenuData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function savegroupAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $postData = $this->getRequest()->getPost();
                $groupModel = Mage::getModel('jmmegamenu/jmmegamenugroup');

                $id = $this->getRequest()->getParam('id');
                if ($id <= 0) { // if add new
                    $groupModel->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
                }

                //Check exits menu key on this store
                $resource = Mage::getSingleton('core/resource');
                $read= $resource->getConnection('core_read');
                $query = 'SELECT id'
                    . ' FROM '.$resource->getTableName('jmmegamenu_types')
                    . ' WHERE storeid = '.$postData['storeid']
                    . ' AND menutype = "'.$postData['menutype'].'"';
                if($id > 0){
                    $query .= ' AND id <> '.$id;
                }
                $query .= ' ORDER BY id';
                $menu = $read->fetchRow($query);
                if($menu){
                    throw new Exception('The Menu key already used by this store.');
                }

                $groupModel
                    ->addData($postData)
                    ->setUpdateTime(Mage::getSingleton('core/date')->gmtDate())
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Menu Group was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setJmMegamenugroupData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/editgroup', array('id' => $groupModel->getId()));
                    return;
                }
                $this->_redirect('*/*/group');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setJmMegamenugroupData($this->getRequest()->getPost());
                $this->_redirect('*/*/editgroup', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }


    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {

                $this->deleteitems($this->getRequest()->getParam('id'));
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Brand was successfully deleted'));
                $groupid = $this->getRequest()->getParam('group');
                $this->_redirect('*/*/index', array('groupid' => $groupid));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        //$this->_redirect('*/*/');
    }

    public function deletegroupAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            $groupid = $this->getRequest()->getParam('id');
            //$storeid = $this->getRequest()->getParam('storeid');
            $resource = Mage::getSingleton('core/resource');
            //$read = $resource->getConnection('core_read');
            $write = $resource->getConnection('core_write');
            try {
                $groupModel = Mage::getModel('jmmegamenu/jmmegamenugroup');
                $groupModel->setId($groupid)->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The menu group was successfully deleted'));
                $this->_forward('group');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/editgroup', array('id' => $this->getRequest()->getParam('id')));
            }
        }

    }

    protected function deleteitems($id)
    {
        if ($id) {
            $menuModel = Mage::getModel('jmmegamenu/jmmegamenu');
            $menuModel->setId($id)->delete();
            $collections = $menuModel->getCollection()->addFilter("parent", $id, "eq");
            foreach ($collections as $collection) {
                $this->deleteitems($collection->menu_id);
            }
        }
    }


    public function ajaxAction()
    {

        $groupid = $this->getRequest()->getParam('menugroup');
        $activecat = $this->getRequest()->getParam('activecat');
        $activecms = $this->getRequest()->getParam('activecms');
        $helper = Mage::helper('jmmegamenu');

        $groupModel = Mage::getModel('jmmegamenu/jmmegamenugroup')->load($groupid);
        $storeid = $groupModel->getData("storeid");
        $store = Mage::app()->getStore($storeid);
        //The root category id of this store
        $parent = $store->getRootCategoryId();
        /**
         * Check if parent node of the store still exists
         */
        $category = Mage::getModel('catalog/category');
        /* @var $category Mage_Catalog_Model_Category */
        if (!$category->checkId($parent)) {
            return array();
        }

        $recursionLevel = max(0, $store->getConfig('catalog/navigation/max_depth'));
        $storeCategories = $category->getCategories($parent, $recursionLevel, false, true, true);

        //$storeCategories = $storeCategories->load()->addAttributeToSelect("*");
        $list = array();
        foreach($storeCategories as $category){
            $list[] = $category->setStoreId($store->getId())->load($category->getId);
        }

        //categories list
        $catlist = $helper->getoutputList($parent, $list, "name", "entity_id", "parent_id");
        $clist = array();
        foreach ($catlist as $id => $cat) {
            $category = Mage::getModel('catalog/category')->setStoreId($store->getId())->load($id);
            $url = str_replace(Mage::getBaseUrl(), "", $category->getUrl());
            $clist[$url] = $cat;
        }
        $response = array();
        $categories = '<select class=" select" name="category" id="category">';

        foreach ($clist as $url => $catname) {
            if ($activecat == $url) {
                $categories .= '<option selected value="' . $url . '">' . $catname . '</option>';
            } else {
                $categories .= '<option value="' . $url . '">' . $catname . '</option>';
            }
        }
        $categories .= '</select>';
        $response["category"] = $categories;

        //cmspages list
        $cmspages = array();
        $cmspages = '<select class=" select" name="cms" id="cms">';
        if (Mage::getStoreConfig("web/secure/use_in_adminhtml")) {
            $baseurl = Mage::getStoreConfig("web/secure/base_url");
        } else {
            $baseurl = Mage::getBaseUrl();
        }
        if (!strpos($baseurl, "index.php")) $baseurl .= "index.php/";
        foreach ($helper->getListcms($storeid) as $page) {
            $url = $baseurl . $page;
            if ($page == $activecms) {
                $cmspages .= '<option selected value="' . $url . '">' . $page . '</option>';
            } else {
                $cmspages .= '<option value="' . $url . '">' . $page . '</option>';
            }

        }

        $cmspages .= "</select>";
        $response["cmspage"] = $cmspages;

        //get collections of all menu item filter by the requested menu group
        if ($this->getRequest()->getParam('menuid')) {
            $menumodle = Mage::getModel('jmmegamenu/jmmegamenu')->load($this->getRequest()->getParam('menuid'));
            $parentid = $menumodle->getData("parent");
            $collections = Mage::getModel('jmmegamenu/jmmegamenu')->getCollection()->addFieldToFilter("menu_id", array("neq" => $this->getRequest()->getParam('menuid')))
                ->addFieldToFilter("menugroup", array('eq' => $groupid));
        } else {
            $collections = Mage::getModel('jmmegamenu/jmmegamenu')->getCollection()->addFieldToFilter("menugroup", array('eq' => $groupid));
        }

        $parent = '<select class=" select" name="parent" id="parent">';
        //Get the parent list
        $parents = $helper->getoutputList(0, $collections, "title", "menu_id", "parent", true);
        foreach ($parents as $paid => $palabel) {
            $parent .= '<option value="' . $paid;
            if (isset($parentid) && ($paid == $parentid)) {
                $parent .= '" selected="selected"';
            } else {
                $parent .= '" ';
            }
            $parent .= ' >' . $palabel . '</option>';
        }
        $parent .= '</select>';
        $response['parent'] = $parent;

        echo json_encode($response);

    }

    public function massDuplicateAction()
    {
        $menuIds = $this->getRequest()->getParam('jmmegamenu');
        if (!is_array($menuIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                $menuModel = Mage::getModel('jmmegamenu/jmmegamenu');
                foreach ($menuIds as $menuId) {
                    $menuModel->load($menuId);
                    $data = $menuModel->getData();
                    if (!isset($groupid)) $groupid = $data['menugroup'];
                    unset($data['menu_id']);
                    unset($data['menualias']);
                    $data['parent'] = '0';
                    $menuModel->setData($data);
                    $menuModel->setCreatedTime(now())->setUpdateTime(now());
                    $menuModel->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully duplicate', count($menuIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index', array('groupid' => $groupid));
    }

    public function massDeleteAction()
    {
        $menuIds = $this->getRequest()->getParam('jmmegamenu');
        if (!is_array($menuIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                $menuModel = Mage::getModel('jmmegamenu/jmmegamenu');
                foreach ($menuIds as $menuId) {
                    if (!isset($groupid)) {
                        $menuModel->load($menuId);
                        $groupid = $menuModel->getData('menugroup');
                    }
                    $this->deleteitems($menuId);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully delete', count($menuIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index', array('groupid' => $groupid));
    }

    public function massStatusAction()
    {
        $menuIds = $this->getRequest()->getParam('jmmegamenu');
        if (!is_array($menuIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                $menuModel = Mage::getModel('jmmegamenu/jmmegamenu');
                foreach ($menuIds as $menuId) {
                    $menuModel->load($menuId);
                    if (!isset($groupid)) {
                        $groupid = $menuModel->getData('menugroup');
                    }
                    $menuModel->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully updated', count($menuIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index', array('groupid' => $groupid));
    }

    public function massDuplicateGroupAction()
    {
        $groupIds = $this->getRequest()->getParam('jmmegamenu');
        $to_groupId = $this->getRequest()->getParam('duplicate_to');
        if (!is_array($groupIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($groupIds as $groupId) {
                    $this->findDuplicate(0, $groupId, 0, $to_groupId);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully duplicate', count($groupIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/group');
    }

    private function findDuplicate($parent, $idgroup, $parentNew, $idgroupNew)
    {
        $menuModel = Mage::getModel('jmmegamenu/jmmegamenu');
        $collections = $menuModel->getCollection()
            ->addFieldToFilter("menugroup", array('eq' => $idgroup))
            ->addFieldToFilter("parent", array('eq' => $parent));
        foreach ($collections as $collection) {
            $data = $collection->getData();
            $id = $data['menu_id'];
            $idNew = $this->addDuplicate($data, $parentNew, $idgroupNew);
            $this->findDuplicate($id, $idgroup, $idNew, $idgroupNew);
        }
    }

    private function addDuplicate($data, $parentNew, $idgroupNew)
    {
        $menuModel = Mage::getModel('jmmegamenu/jmmegamenu');
        unset($data['menu_id']);
        unset($data['menualias']);
        $data['menugroup'] = $idgroupNew;
        $data['parent'] = $parentNew;
        $menuModel->setData($data);
        $menuModel->setCreatedTime(now())->setUpdateTime(now());
        $menuModel->save();
        return $menuModel->getId();
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'group':
            case 'index':
            default:
                return Mage::getSingleton('admin/session')->isAllowed( 'joomlart/jmmegamenu/groups');
                break;
        }
    }
} 