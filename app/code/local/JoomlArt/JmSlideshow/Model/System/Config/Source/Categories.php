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


class JoomlArt_JmSlideshow_Model_System_Config_Source_Categories
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray(){
		return $this->getOptions();
	}

	protected function getOptions(){
		$options = array();

		//The root category id of default store
		$storeCode = Mage::app()->getRequest()->getParam('store');
		if($storeCode){
			$store = self::getStoreByCode($storeCode);
		}else{
			$store = Mage::app()->getWebsite(true)->getDefaultStore();
		}
		if($store)
			$rootCategoryId = $store->getRootCategoryId();

		/**
		 * Check if parent node of the store still exists
		 */
		$category = Mage::getModel('catalog/category');
		/* @var $category Mage_Catalog_Model_Category */
		if (!$category->checkId($rootCategoryId)) {
			return array();
		}

		$recursionLevel = max(0, (int)Mage::app()->getWebsite(true)->getDefaultStore()->getConfig('catalog/navigation/max_depth'));
		$storeCategories = $category->getCategories($rootCategoryId, $recursionLevel, false, true, true);
		$storeCategories = $storeCategories->load()->addAttributeToSelect("*");

		//categories list
		$catList = $this->getOutPutList($rootCategoryId, $storeCategories, "name", "entity_id", "parent_id", true, true);
		foreach ($catList as $id => $label) {
			$options[] = array(
				'value' => $id, 'label'=> $label
			);
		}

		return $options;
	}

	protected function treeCategories($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $label, $key, $parent, $shownumberproduct = false)
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

				$list[$id] = $v;
				$list[$id]->$label = "$indent$txt";
				if($shownumberproduct)
					$list[$id]->$label .= " (".$v->getProductCount().")";
				$list[$id]->children = count(@$children[$id]);
				$list = $this->treeCategories($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $label, $key, $parent, $shownumberproduct);
			}
		}

		return $list;
	}


	protected function getOutPutList($root = 0, $collections, $labelfield = "title", $keyfield = "id", $parentfield = "parent", $addtop = false, $shownumberproduct = false)
	{
		@$children = array();
		foreach ($collections as $collection) {
			$pt = $collection->$parentfield;
			$list = (isset($children[$pt]) && $children[$pt]) ? $children[$pt] : array();
			array_push($list, $collection);
			$children[$pt] = $list;
		}

		$lists = $this->treeCategories($root, '', array(), $children, 9999, 0, $labelfield, $keyfield, $parentfield, $shownumberproduct);

		if ($addtop) {
			$outputs = array('0' => "All");
		}
		foreach ($lists as $id => $list) {
			$lists[$id]->$labelfield = "--" . $lists[$id]->$labelfield;
			$outputs[$lists[$id]->$keyfield] = $lists[$id]->$labelfield;
		}

		return $outputs;
	}

	protected static function getStoreByCode($storeCode)
	{
		$store = null;
		if($storeCode){
			$stores = Mage::app()->getStores();
			foreach($stores as $store){
				if($store->getCode() == $storeCode) {
					return $store;
				}
			}
		}

		return null; // if not found
	}
}
