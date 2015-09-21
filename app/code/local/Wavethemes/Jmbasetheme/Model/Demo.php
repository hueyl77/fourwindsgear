<?php

class Wavethemes_Jmbasetheme_Model_Demo extends Mage_Core_Model_Abstract
{
	protected $_importSrc;
    protected $_styleSrc;
	
	public function __construct()
    {
        parent::__construct();

        $websiteCode = Mage::app()->getRequest()->getParam('website', null);
        $storeCode   = Mage::app()->getRequest()->getParam('store', null);
        $designPackage = Mage::helper('jmbasetheme')->getDesignPackage($storeCode, 'backend', $websiteCode);

		$this->_importSrc = Mage::getBaseDir("design") . DS . "frontend" . DS . $designPackage . DS . 'demo' . DS . 'data' .DS;
        $this->_styleSrc = Mage::getBaseDir("design") . DS . "frontend" . DS . $designPackage . DS . 'demo' . DS . 'styles' .DS;

        if (!is_dir($this->_importSrc)) {
            mkdir($this->_importSrc, 0775, true);
        }

        if (!is_dir($this->_styleSrc)) {
            mkdir($this->_styleSrc, 0775, true);
        }
    }

	public function importCms($modelString, $itemContainerNodeString, $overwrite = false)
    {
		try
		{
			$xmlPath = $this->_importSrc . $itemContainerNodeString . '.xml';
			if (!is_readable($xmlPath))
			{
				throw new Exception(
					Mage::helper('jmbasetheme')->__("Can't get the data file for import cms blocks/pages: %s", $xmlPath)
                );
			}
			$xmlObj = new Varien_Simplexml_Config($xmlPath);
			
			$conflictingOldItems = array();
			$i = 0;
			foreach ($xmlObj->getNode($itemContainerNodeString)->children() as $b)
			{
                $store_ids = $this->_parseStoreIds($b->stores);

				$oldBlocks = Mage::getModel($modelString)->getCollection()
					->addFieldToFilter('identifier', $b->identifier)
                    ->addStoreFilter($store_ids, false)
					->load();

				if ($overwrite)
				{
					if (count($oldBlocks) > 0)
					{
						$conflictingOldItems[] = $b->identifier;
						foreach ($oldBlocks as $old){
                            $old->delete();
                        }
					}
				}
				else
				{
					if (count($oldBlocks) > 0)
					{
						$conflictingOldItems[] = $b->identifier;
						continue;
					}
				}

				if($modelString == 'cms/page'){
                    Mage::getModel($modelString)
                        ->setTitle($b->title)
                        ->setContent($b->content)
                        ->setIdentifier($b->identifier)
                        ->setIsActive($b->is_active)
                        ->setStores($store_ids)
                        ->setRootTemplate($b->root_template)
                        ->setLayoutUpdateXml($b->layout_update_xml)
                        ->save();
                    
                }else {
				    Mage::getModel($modelString)
					    ->setTitle($b->title)
					    ->setContent($b->content)
					    ->setIdentifier($b->identifier)
					    ->setIsActive($b->is_active)
					    ->setStores($store_ids)
					    ->save();
                }

				$i++;
			}
			
			if ($i)
			{
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('jmbasetheme')->__('%s item(s) were imported successfully.', $i)
				);
			}
			else
			{
				Mage::getSingleton('adminhtml/session')->addNotice(
					Mage::helper('jmbasetheme')->__('No items were imported')
				);
			}
			
			if ($overwrite)
			{
				if ($conflictingOldItems)
					Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('jmbasetheme')
						->__('Items (%s) with the following identifiers were overwritten:<br />%s', count($conflictingOldItems), implode(', ', $conflictingOldItems))
					);
			}
			else
			{
				if ($conflictingOldItems)
					Mage::getSingleton('adminhtml/session')->addNotice(
						Mage::helper('jmbasetheme')
						->__('Unable to import items (%s) with the following identifiers (they already exist in the database):<br />%s', count($conflictingOldItems), implode(', ', $conflictingOldItems))
					);
			}
		}
		catch (Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			Mage::logException($e);
		}
    }

    public function importMenu()
    {
        try
        {
            $xmlPath = $this->_importSrc . 'menus.xml';
            if (!is_readable($xmlPath))
            {
                throw new Exception(
                    Mage::helper('jmbasetheme')->__("Can't get the data file for Menu: %s", $xmlPath)
                );
            }
            $xmlObj = new Varien_Simplexml_Config($xmlPath);

            $conflictingOldItems1 = $conflictingOldItems2 = array();
            $i = $j = 0;
            //import Menu Groups
            foreach ($xmlObj->getNode('groups')->children() as $b)
            {
                $overwrite = true;
                $oldModels = Mage::getModel('jmmegamenu/jmmegamenugroup')->getCollection()
                    ->addFieldToFilter('menutype', $b->menutype)
                    ->addFieldToFilter('storeid', $b->storeid)
                    ->load();

                if ($overwrite)
                {
                    if (count($oldModels) > 0)
                    {
                        $conflictingOldItems1[] = $b->id;
                        foreach ($oldModels as $old){
                            $old->delete();
                        }
                    }
                }
                else
                {
                    if (count($oldModels) > 0)
                    {
                        $conflictingOldItems1[] = $b->id;
                        continue;
                    }
                }

                $model = Mage::getModel('jmmegamenu/jmmegamenugroup');
                $data = array(
                    'id' => $b->id,
                    'menutype' => $b->menutype,
                    'title' => $b->title,
                    'description' => $b->description,
                    'storeid' => $b->storeid
                );
                $model->addData($data);
                $model->save();
                $i++;
            }

            //import Menu Items
            foreach ($xmlObj->getNode('items')->children() as $b)
            {
                $overwrite = true;
                $oldModels = Mage::getModel('jmmegamenu/jmmegamenu')->getCollection()
                    ->addFieldToFilter('menu_id', $b->menu_id)
                    ->load();

                if ($overwrite)
                {
                    if (count($oldModels) > 0)
                    {
                        $conflictingOldItems2[] = $b->menu_id;
                        foreach ($oldModels as $old){
                            $old->delete();
                        }
                    }
                }
                else
                {
                    if (count($oldModels) > 0)
                    {
                        $conflictingOldItems1[] = $b->menu_id;
                        continue;
                    }
                }

                $model = Mage::getModel('jmmegamenu/jmmegamenu');
                $data = array(
                    'menu_id' => $b->menu_id,
                    'title' => $b->title,
                    'link' => $b->link,
                    'url' => $b->url,
                    'catid' => $b->catid,
                    'menualias' => $b->menualias,
                    'menutype' => $b->menutype,
                    'category' => $b->category,
                    'cms' => $b->cms,
                    'parent' => $b->parent,
                    'lft' => $b->lft,
                    'rgt' => $b->rgt,
                    'mega_cols' => $b->mega_cols,
                    'mega_group' => $b->mega_group,
                    'mega_class' => $b->mega_class,
                    'status' => $b->status,
                    'ordering' => $b->ordering,
                    'showtitle' => $b->showtitle,
                    'menugroup' => $b->menugroup,
                    'created_time' => $b->created_time,
                    'update_time' => $b->update_time,
                    'static_block' => $b->static_block,
                    'mega_subcontent' => $b->mega_subcontent,
                    'mega_width' => $b->mega_width,
                    'mega_colw' => $b->mega_colw,
                    'mega_colxw' => $b->mega_colxw,
                    'desc' => $b->desc,
                    'browserNav' => $b->browserNav,
                    'contentxml' => $b->contentxml,
                    'image' => $b->image,
                    'description' => $b->description,
                    'shownumproduct' => $b->shownumproduct
                );
                $model->addData($data);
                $model->save();
                $j++;
            }

            if ($i)
            {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('jmbasetheme')->__('%s MenuGroup(s) were imported successfully.', $i)
                );
                if ($j){
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('jmbasetheme')->__('%s Menu Item(s) were imported successfully.', $j)
                    );
                }
            }
            else
            {
                Mage::getSingleton('adminhtml/session')->addNotice(
                    Mage::helper('jmbasetheme')->__('No items were imported')
                );
            }

            if ($overwrite)
            {
                if ($conflictingOldItems1){
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('jmbasetheme')
                            ->__('Menu Groups with the following ids were overwritten(%s):<br />%s', count($conflictingOldItems1), implode(', ', $conflictingOldItems1))
                    );
                }
                if ($conflictingOldItems2){
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('jmbasetheme')
                            ->__('Menu Items with the following ids were overwritten(%s):<br />%s', count($conflictingOldItems2), implode(', ', $conflictingOldItems2))
                    );
                }
            }
            else
            {
                if ($conflictingOldItems1){
                    Mage::getSingleton('adminhtml/session')->addNotice(
                        Mage::helper('jmbasetheme')
                            ->__('Unable to import Menu Group (%s) with the following ids (they already exist in the database):<br />%s', count($conflictingOldItems1), implode(', ', $conflictingOldItems1))
                    );
                }

                if ($conflictingOldItems2){
                    Mage::getSingleton('adminhtml/session')->addNotice(
                        Mage::helper('jmbasetheme')
                            ->__('Unable to import Menu Item (%s) with the following ids (they already exist in the database):<br />%s', count($conflictingOldItems2), implode(', ', $conflictingOldItems2))
                    );
                }
            }
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::logException($e);
        }
    }

    public function setDemo($style, $store = null, $website = null)
    {
        try
        {
            //Unset color cookie in jmbasetheme
            $cookieName = Mage::helper("jmbasetheme")->getDefaultTheme($store, 'backend', $website)."_active_color";
            if (isset($_COOKIE[$cookieName])){
                setcookie($cookieName, null, time() - 3600, "/");
            }
            
            $xmlPath = $this->_styleSrc . $style . '.xml';
            if (!is_readable($xmlPath))
            {
                throw new Exception(
                    Mage::helper('jmbasetheme')->__("Can't get the data file for set demo style: %s", $xmlPath)
                );
            }

            $xmlObj = new Varien_Simplexml_Config($xmlPath);

            if (strlen($store)) // store level
            {
                $store_id = Mage::getModel('core/store')->load($store)->getId();
                $scope = "stores";
                $scope_id = $store_id;
            }
            elseif (strlen($website)) // website level
            {
                $website_id = Mage::getModel('core/website')->load($website)->getId();
                $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
                $scope = "websites";
                $scope_id = $website_id;
            }
            else // default level
            {
                $scope = "default";
                $scope_id = 0;
                $store_id = 0;
            }

            $config = Mage::getConfig();
            foreach ($xmlObj->getNode("config")->children() as $b_name => $b)
            {
                foreach ($b->children() as $c_name => $c){
                    foreach ($c->children() as $d_name => $d){
                        $config->saveConfig($b_name.'/'.$c_name.'/'.$d_name,$c->$d_name,$scope,$scope_id);
                    }
                }
            }
            
            //clean config cache
            $config->cleanCache();
			//flush & clean Magento cache
			Mage::app()->getCacheInstance()->flush();
			Mage::app()->getCache()->clean();

            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('jmbasetheme')
                    ->__('%s was applied for this configuration scope.', ucfirst($style))
            );
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::logException($e);
        }
    }

    public function exportCMSData($modelString, $itemContainerNodeString)
    {
        try
        {
            //$xmlPath = $this->_importSrc . $itemContainerNodeString . '.xml';
            //export blocks
            $data = Mage::getModel($modelString)->getCollection()
                ->addFieldToFilter('is_active', 1)
                ->load();
            if ($data){
                $rsModel = Mage::getResourceModel($modelString);
                $i = 0;
                $items = $data->getItems();

                $content = "<root>";
                $content .= "<{$itemContainerNodeString}>";
                foreach ($items as $item){
                    $content .= "<cms_block>";
                    $content .= "<title><![CDATA[".$item->getData('title')."]]></title>";
                    $content .= "<identifier>".$item->getData('identifier')."</identifier>";
                    $strContent = str_replace(array('<![CDATA[', ']]>'), array('', ''), $item->getData('content'));
                    
                    //replace category param
                    $strContent = preg_replace('/(category_ids=\").*?(\")/si', 'category_ids="all"', $strContent);
                    $strContent = preg_replace('/(catsid=\").*?(\")/si', 'catsid="all"', $strContent);
                    
                    $content .= "<content><![CDATA[".$strContent."]]></content>";
                    $content .= "<is_active>".$item->getData('is_active')."</is_active>";
                    //get store ids
                    $store_ids = $rsModel->lookupStoreIds($item->getId());
                    if ($store_ids){
                        $content .= "<stores>";
                        foreach ($store_ids as $store_id){
                            $content .= "<id>{$store_id}</id>";
                        }
                        $content .= "</stores>";
                    }
                    if ($itemContainerNodeString == 'pages'){
                        $content .= "<root_template>".$item->getData('root_template')."</root_template>";
                        $content .= "<layout_update_xml><![CDATA[".$item->getData('layout_update_xml')."]]></layout_update_xml>";
                    }
                    $content .= "</cms_block>";

                    $i++;
                }
                $content .= "</{$itemContainerNodeString}>";
                $content .= "</root>";

                //write to file
                $file = new Varien_Io_File();
                $file->setAllowCreateFolders(true);
                $file->open(array( 'path' => $this->_importSrc ));
                $file->streamOpen($this->_importSrc . $itemContainerNodeString . '.xml', 'w+');
                $file->streamLock(true);
                $file->streamWrite($content);
                $file->streamUnlock();
                $file->streamClose();

                if ($i)
                {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('jmbasetheme')->__("%s {$itemContainerNodeString} item(s) were exported successfully.", $i)
                    );
                }
                else
                {
                    Mage::getSingleton('adminhtml/session')->addNotice(
                        Mage::helper('jmbasetheme')->__('No items were imported')
                    );
                }
            }

        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::logException($e);
        }
    }

    public function exportMenuData(){
        try
        {
            //get all menu group data
            $groups = Mage::getModel('jmmegamenu/jmmegamenugroup')->getCollection()->load()->getItems();
            $items = Mage::getModel('jmmegamenu/jmmegamenu')->getCollection()->load()->getItems();
            $i = $j = 0;
            if ($groups && $items){
                $content = "<root>";
                //Make menu groups nodes
                $content .= "<groups>";
                foreach ($groups as $group){
                    $content .= "<group>";
                    $data = $group->getData();
                    foreach ($data as $key => $value){
                        if (in_array($key, array('title', 'description'))){
                            $value = "<![CDATA[{$value}]]>";
                        }
                        $content .= "<{$key}>".$value."</{$key}>";
                    }
                    $content .= "</group>";
                    $i++;
                }
                $content .= "</groups>";

                //Make menu items nodes
                $content .= "<items>";
                foreach ($items as $item){
                    $content .= "<item>";
                    if ($item->getData('menutype') == '0'){// is category menu link
                        $item->setData('link', '#');
                        $item->setData('url', '#');
                        $item->setData('catid', null);
                        $item->setData('category', null);
                    }
                    $data = $item->getData();
                    foreach ($data as $key => $value){
                        if (in_array($key, array('title', 'link', 'url', 'mega_colxw', 'desc', 'image', 'description', 'contentxml', 'mega_class'))){
                            $value = "<![CDATA[{$value}]]>";
                        }
                        $content .= "<{$key}>".$value."</{$key}>";
                    }
                    $content .= "</item>";
                    $j++;
                }

                $content .= "</items>";
                $content .= "</root>";

                //write to file
                $file = new Varien_Io_File();
                $file->setAllowCreateFolders(true);
                $file->open(array( 'path' => $this->_importSrc ));
                $file->streamOpen($this->_importSrc . 'menus.xml', 'w+');
                $file->streamLock(true);
                $file->streamWrite($content);
                $file->streamUnlock();
                $file->streamClose();

                if ($i)
                {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('jmbasetheme')->__("%s Menu Group(s) were exported successfully.", $i)
                    );
                    if ($j)
                    {
                        Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('jmbasetheme')->__("%s Menu Item(s) were exported successfully.", $j)
                        );
                    }
                }
                else
                {
                    Mage::getSingleton('adminhtml/session')->addNotice(
                        Mage::helper('jmbasetheme')->__('No items were imported')
                    );
                }
            }
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::logException($e);
        }
    }

    protected function _parseStoreIds($obj){
        $data = (array)$obj;
        $storeIds = isset($data['id']) ? $data['id'] : array(0);
        return $storeIds;
    }
    
    public function isSupported($itemContainerNodeString)
    {
        $xmlPath = $this->_importSrc . $itemContainerNodeString . '.xml';
        return file_exists($xmlPath);
    }
    
    public function hasStyleDefined()
    {
        $files = Mage::helper('jmbasetheme')->listFiles($this->_styleSrc);
        return (count($files) > 0) ? true : false;
    }
}
