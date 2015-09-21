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

class Wavethemes_Jmmegamenu_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $defaultStoreCode = null;
    protected $activeStoreCode = null;

    public function __construct()
    {
        //initial default store code
        $this->defaultStoreCode = Mage::app()->getWebsite(true)->getDefaultStore()->getCode();

        //initial current store code
        $this->activeStoreCode = Mage::app()->getStore()->getCode();
    }

    public function get($var, $attributes = array())
    {
        $value = null;
        if (isset($attributes[$var])) {
            $value = $attributes[$var];
        } else {
            $storeCode = ($this->activeStoreCode) ? $this->activeStoreCode : $this->defaultStoreCode;
            $configGroups = Mage::getStoreConfig("wavethemes_jmmegamenu", $storeCode);
            foreach ($configGroups as $configs) {
                if (isset($configs[$var])) {
                    $value = $configs[$var];
                    break;
                }
            }
        }

        return $value;
    }

    public function treemenu($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $label, $key, $parent)
    {

        if (@$children[$id] && $level <= $maxlevel) {
            foreach ($children[$id] as $v) {

                $id = $v->$key;
                $pre = '- ';
                $spacer = '---';
                if ($v->$parent == 0) {
                    $txt = $v->$label;
                } else {
                    $txt = $pre . $v->$label;
                }
                $pt = $v->$parent;
                $list[$id] = $v;
                $list[$id]->$label = "$indent$txt";
                $list[$id]->children = count(@$children[$id]);
                $list = $this->treemenu($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $label, $key, $parent);
            }
        }
        return $list;

    }


    public function getoutputList($root = 0, $collections, $labelfield = "title", $keyfield = "id", $parentfield = "parent", $addtop = false)
    {
        @$children = array();
        foreach ($collections as $collection) {
            $pt = $collection->$parentfield;
            $list = isset($children[$pt]) ? $children[$pt] : array();
            array_push($list, $collection);
            $children[$pt] = $list;
        }

        $lists = $this->treemenu($root, '', array(), $children, 9999, 0, $labelfield, $keyfield, $parentfield);
        if ($addtop) {
            $outputs = array('0' => "Top");
        }
        foreach ($lists as $id => $list) {

            $lists[$id]->$labelfield = "--" . $lists[$id]->$labelfield;
            $outputs[$lists[$id]->$keyfield] = $lists[$id]->$labelfield;

        }

        return $outputs;
    }


    public function prepareGridCollection($root = 0, &$collections, $labelfield = "title", $keyfield = "id", $parentfield = "parent", $addtop = false)
    {
        @$children = array();
        foreach ($collections as $collection) {
            $pt = $collection->$parentfield;
            $list = (isset($children[$pt]) && $children[$pt]) ? $children[$pt] : array();
            array_push($list, $collection);
            $children[$pt] = $list;
        }

        $lists = $this->treemenu($root, '', array(), $children, 9999, 0, $labelfield, $keyfield, $parentfield);
        if ($addtop) {
            $outputs = array('0' => "Top");
        }
        foreach ($lists as $id => $list) {

            $lists[$id]->$labelfield = $lists[$id]->$labelfield;

        }

        return $lists;
    }

    public function getActivemenu($collections)
    {
        $baseurl = Mage::getBaseUrl();
        $currenturl = Mage::helper('core/url')->getCurrentUrl();
        $alias = "";
        $currenturls = explode("?alias=", $currenturl);
        $currenturl = $currenturls[0];
        if (isset($currenturls[1])) {
            $alias = $currenturls[1];
        }

        $websiteId = Mage::app()->getStore()->getWebsiteId();
        $CurrentPage = Mage::app()->getWebsite($websiteId)->getConfig('web/default/cms_home_page');

        //homepage with or without index.php
        if ($currenturl == $baseurl || $currenturl . "index.php" . DS == $baseurl) $currenturl = $baseurl . $CurrentPage;

        //find a menu item whose link match curent url
        foreach ($collections as $collection) {

            if ($collection->menutype == 2) {
                if ($collection->link == $currenturl) {
                    return $collection;
                }
            } else {
                if (strpos($currenturl, $baseurl . $collection->url) !== false && $collection->menualias == $alias) {
                    return $collection;
                }
            }
        }


        // search for a category menu item that match current category
        $catcollection = false;
        foreach ($collections as $collection) {
            //check categories items only
            if (!$collection->menutype) {
                if ($this->isCategoryActive($collection->catid)) {
                    $catcollection = $collection;
                }
            }
        }
        return $catcollection;
    }

    public function isCategoryActive($catid)
    {
        if (Mage::getSingleton('catalog/layer')) {
            $currentcat = Mage::getSingleton('catalog/layer')->getCurrentCategory();
            return in_array($catid, $currentcat->getPathIds());
        }

        return false;

    }

    public function getListcms($storeid = null)
    {

        if ($storeid == null) {
            $storeid = Mage::app()->getWebsite(true)->getDefaultStore()->getId();
        }

        $links = array();
        $cms_pages = Mage::getModel('cms/page')->getCollection()->addStoreFilter($storeid);
        $cms_pages->load();
        foreach ($cms_pages as $_page) {
            $data = $_page->getData();
            if ($data['identifier'] == 'no-route')
                continue;
            $links[$data['identifier']] = $data['identifier'];
        }

        return $links;


    }

    public function getorders($id)
    {

        $item = Mage::getModel('jmmegamenu/jmmegamenu')->load($id);
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $menutable = $resource->getTableName('jmmegamenu');
        $query = 'SELECT ordering AS value, title AS label'
            . ' FROM ' . $menutable
            . ' WHERE parent = ' . (int)$item->parent

            . ' AND status >= 0'
            . ' ORDER BY ordering';
        $rows = $read->fetchAll($query);
        $rows = array_values($rows);
        $rows[] = array('value' => count($rows) + 1, "label" => "Last");
        array_unshift($rows, array('value' => 0, "label" => "First"));
        foreach ($rows as $k => $v) {
            $rows[$k]['label'] = $rows[$k]['value'] . " " . $rows[$k]['label'];
        }
        return $rows;
    }

    public function reorder($where = '')
    {
        $k = "menu_id";
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $write = $resource->getConnection('core_write');
        $menutable = $resource->getTableName('jmmegamenu');
        $query = 'SELECT menu_id, ordering'
            . ' FROM ' . $menutable
            . ' WHERE ordering >= 0' . ($where ? ' AND ' . $where : '')
            . ' ORDER BY ordering';
        if (!($orders = $read->fetchAll($query))) {
            return false;
        }
        // compact the ordering numbers
        for ($i = 0, $n = count($orders); $i < $n; $i++) {
            if ($orders[$i]['ordering'] >= 0) {
                if ($orders[$i]['ordering'] != $i + 1) {

                    $orders[$i]['ordering'] = $i + 1;
                    $query = 'UPDATE ' . $menutable
                        . ' SET ordering = ' . (int)$orders[$i]['ordering']
                        . ' WHERE ' . $k . ' = \'' . $orders[$i][$k] . '\'';

                    $write->query($query);
                }
            }
        }
        return true;
    }

    public function getMenuId($menuType = null)
    {
        $menuId = null;
        //get menu group id
        $storeId = Mage::app()->getStore()->getStoreId();
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $menuTable = $resource->getTableName('jmmegamenu_types');
        
        if ($menuType){ //new themes
            $query = "SELECT id, storeid FROM {$menuTable} WHERE menutype='{$menuType}' ORDER BY id ASC";
            $menus = $read->fetchAll($query);
            if ($menus){
                foreach ($menus as $menu){
                    if ($menu['storeid'] == $storeId){
                        $menuId = $menu['id'];
                    }
                }
                if (!$menuId){
                    $menuId = $menus[0]['id'];
                }
            }
        } else { //old themes
            $query = "SELECT id, storeid FROM {$menuTable} WHERE storeid={$storeId} ORDER BY id ASC";
            $menus = $read->fetchAll($query);
            if ($menus){
                $menuId = $menus[0]['id'];
            }
        }

        return $menuId;
    }

    public function getCategoryByUrl($url = null){
        $category = null;
        if($url){
            $url = str_replace(".html", "", $url);
            $pos = strrpos($url, "/");
            $start = ($pos) ? ($pos+1) : 0;
            $url_key = substr($url, $start);
            $category = Mage::getModel('catalog/category')->loadByAttribute('url_key', $url_key);
        }

        return $category;
    }

}
