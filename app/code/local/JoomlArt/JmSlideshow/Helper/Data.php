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

class JoomlArt_JmSlideshow_Helper_Data extends Mage_Core_Helper_Abstract
{
    var $defaultStoreCode = null;
    var $activeStoreCode = null;
    var $activeWebsiteCode = null;

    public static $_params = array(
        'show',
        'title',
        'loadjquery' ,
        'source',
        'sourceProductsMode',
        'catsid', // this for old theme
        'category_ids', // this for new theme
        'quanlity', // this for old theme
        'quantity', // this for new theme
        'folder',
        'readMoreText',
        'animation', 					//[slide, fade, slice], slide and fade for old compactible
        'mainWidth',					//width of main item
        'mainWidthtablet',
        'mainWidthtabletportrait',
        'mainWidthmobile',
        'mainWidthmobileportrait',
        'mainHeight',					//height of main item
        'duration',						//duration - time for animation
        'autoPlay',						//auto play
        'repeat',						//animation repeat or not
        'interval',						//interval - time for between animation
        'rtl',							//rtl
        'thumbType', 					//false - no thumb, other [number, thumb], thumb will animate
        'thumbImgMode',
        'useRatio',
        'thumbImgWidth',
        'thumbImgHeight',
        'thumbItems',					//number of thumb item will be show
        'thumbWidth',					//width of thumbnail item
        'thumbHeight',					//width of thumbnail item
        'thumbSpaces',					//space between thumbnails
        'thumbDirection',				//thumb orientation
        'thumbPosition',				//[0%, 50%, 100%]
        'thumbTrigger',					//thumb trigger event, [click, mouseenter]
        'show_thumb_desc',				//show thumbnail description or not
        'thumb_description',            // thumbnail description
        'controlBox',					//show navigation controller [next, prev, play, playback] - JM does not have a config
        'controlPosition',				//show navigation controller [next, prev, play, playback] - JM does not have a config
        'navButtons',					//main next/prev navigation buttons mode, [false, auto, fillspace]
        'showDesc',						//show description or not
        'descMaxLength',
        'descTrigger',					//[always, mouseover, load]
        'maskWidth',					//mask - a div over the the main item - used to hold descriptions
        'maskHeight',					//mask height
        'maskAnim',						//mask transition style [fade, slide, slide-fade], slide - will use the maskAlign to slide
        'maskOpacity',					//mask opacity
        'maskPosition',					//[0%, 50%, 100%]
        'description',
        'showProgress',					//show the progress bar
        'urls', 						// [] array of url of main items
        'targets' 						// [] same as urls, an array of target value such as, '_blank', 'parent', '' - default
    );

    public function __construct()
    {
        //initial default store code
        $this->defaultStoreCode = Mage::app()->getWebsite()->getDefaultStore()->getCode();

        //initial current store code
        if ($this->isAdmin()) {
            $this->activeStoreCode = Mage::app()->getRequest()->getParam('store', null);
            $this->activeWebsiteCode = Mage::app()->getRequest()->getParam('website', null);
        } else {
            $this->activeStoreCode = Mage::app()->getStore()->getCode();
            $this->activeWebsiteCode = Mage::app()->getWebsite()->getCode();
        }
    }

    public function get($var, $attributes = array(), $storeCode = null, $websiteCode = null)
    {
        $value = null;
        if(isset($attributes[$var])){
            $value = $attributes[$var];
        }else{
            $websiteCode = ($websiteCode) ? $websiteCode : $this->activeWebsiteCode;
            $storeCode = ($storeCode) ? $storeCode : $this->activeStoreCode;

            if ($storeCode){
                //$configGroups = Mage::getConfig()->getNode('joomlart_jmslideshow', 'stores', $storeCode);
                $configGroups = Mage::getStoreConfig("joomlart_jmslideshow", $storeCode);
            }
            else if ($websiteCode){
                //$configGroups = Mage::getConfig()->getNode('joomlart_jmslideshow', 'websites', $websiteCode);
                $configGroups = Mage::app()->getWebsite($websiteCode)->getConfig('joomlart_jmslideshow');
            }
            else{
                //$configGroups = Mage::getConfig()->getNode('joomlart_jmslideshow', 'default');
                $configGroups = Mage::getStoreConfig("joomlart_jmslideshow");
            }

            if ($configGroups){
                foreach($configGroups as $configs){
                    if (!is_object($configs)){
                        $configs = (object) $configs;
                    }
                    if(isset($configs->$var)){
                        $value = $configs->$var;
                        break;
                    }
                }
            }
        }

        return $value;
    }

    /**
     * Get data for the slideshow
     */
    public function getSlideShowData( $configs = array() )
    {
        $data = array();
        $items = array();
        $urls = array();
        $titles = array();
        $alts = array();
        $targets = array();

        if ($configs['source'] == 'products')
        {
            //Get products list
            $collection = $this->getProductCollection($configs);
            if ($collection)
            {
                $jmImage = Mage::helper('joomlart_jmslideshow/jmimage')->setConfig($configs['thumbImgMode'], $configs['useRatio']);
                $products = $collection->getItems();
                if($products){
                    $descMaxLength = isset($configs['descMaxLength']) ? $configs['descMaxLength'] : 60;
                    foreach ($products as $key => $_product) {
                        $items[$key] = array();

                        // product link
                        $urls[$key] = $_product->getProductUrl();

                        // minor thumbnail
                        $items[$key]['thumb'] = '<img align="left" src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $jmImage->resizeThumb('media/catalog/product' . $_product->getImage(), $configs['thumbImgWidth'], $configs['thumbImgHeight']) . '" alt="' . $this->escapeHtml($_product->getName()) . '" width="' . $configs['thumbImgWidth'] . '" height="' . $configs['thumbImgHeight'] . '" />';

                        // main thumbnail
                        $items[$key]['mainThumb'] = '<img align="left" src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $jmImage->resizeThumb('media/catalog/product' . $_product->getImage(), $configs['mainWidth'], $configs['mainHeight']) . '" alt="' . $this->escapeHtml($_product->getName()) . '" width="' . $configs['mainWidth'] . '" height="' . $configs['mainHeight'] . '" />';

                        // product title and short description
                        $items[$key]['caption'] = '<h3>' . $this->escapeHtml($_product->getName()) . '</h3><p>' . $this->subStrWords($this->escapeHtml($_product->getShortDescription()), $descMaxLength) . '</p>';
                    }
                }

            }
        }
        else if ($configs['source'] == 'categories'){

            $jmImage = Mage::helper('joomlart_jmslideshow/jmimage')->setConfig($configs['thumbImgMode'], $configs['useRatio']);

            //List categories
            $categories = $this->getCategories($configs);
            if($categories){
                $descMaxLength = isset($configs['descMaxLength']) ? $configs['descMaxLength'] : 60;
                foreach ($categories as $key => $category) {
                    $items[$key] = array();

                    // product link
                    $urls[$key] = $category->getUrl();

                    if($category->getImage()){
                        // minor thumbnail
                        $items[$key]['thumb'] = '<img align="left" src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $jmImage->resizeThumb('media/catalog/category/' . $category->getImage(), $configs['thumbImgWidth'], $configs['thumbImgHeight']) . '" alt="' . $this->escapeHtml($category->getName()) . '" width="' . $configs['thumbImgWidth'] . '" height="' . $configs['thumbImgHeight'] . '" />';
                        // main thumbnail
                        $items[$key]['mainThumb'] = '<img align="left" src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $jmImage->resizeThumb('media/catalog/category/' . $category->getImage(), $configs['mainWidth'], $configs['mainHeight']) . '" alt="' . $this->escapeHtml($category->getName()) . '" width="' . $configs['mainWidth'] . '" height="' . $configs['mainHeight'] . '" />';
                    }
                    else{
                        // minor thumbnail
                        $items[$key]['thumb'] = $this->__('No image.');
                       // main thumbnail
                        $items[$key]['mainThumb'] = $this->__('No image.');;
                    }

                    // product title and short description
                    $items[$key]['caption'] = '<h3>' . $this->escapeHtml($category->getName()) . '</h3><p>' . $this->subStrWords($this->escapeHtml($category->getDescription()), $descMaxLength) . '</p>';
                }
            }
        }
        else if($configs['source'] == 'images') // Get Images from folder path
        {
            $descriptionArray = $mainsThumbs = $imageArray = array();
            $captionsArray = array();
            $thumbArray = array();

            //Get images
            $listImages = $this->getImagesInDir($configs['folder']);
            
            $descriptionArray = $this->parseDescription($configs['description']);
            if (!count($descriptionArray)) { // This for old theme
                $descriptionArray = $this->parseDescOld($configs['description']);
            }

            if ($configs['show_thumb_desc']) {
                $thumbDescriptionArray = $this->parseDescription($configs['thumb_description']);
            }

            if ($listImages && is_array($listImages))
            {
                foreach ($listImages as $key => $img) {

                    $imageArray[] = $configs['folder'] . '/' . $img;

                    $captionsArray[$key] = (isset($descriptionArray) && isset($descriptionArray[$img]) && isset($descriptionArray[$img]['caption'])) ? str_replace("'", "\'", $descriptionArray[$img]['caption']) : '';

                    if ($configs['show_thumb_desc']) {
                        $thumbCaptionsArray[$key] = (isset($thumbDescriptionArray) && isset($thumbDescriptionArray[$img]) && isset($thumbDescriptionArray[$img]['caption'])) ? str_replace("'", "\'", $thumbDescriptionArray[$img]['caption']) : '';
                    }

                    $urls[$key] = (isset($descriptionArray[$img]) && isset($descriptionArray[$img]['url'])) ? $descriptionArray[$img]['url'] : '';
                    $titles[$key] = (isset($descriptionArray[$img]) && isset($descriptionArray[$img]['title'])) ? $descriptionArray[$img]['title'] : '';
                    $alts[$key] = (isset($descriptionArray[$img]) && isset($descriptionArray[$img]['alt'])) ? $descriptionArray[$img]['alt'] : '';
                    $targets[$key] = (isset($descriptionArray[$img]) && isset($descriptionArray[$img]['target'])) ? $descriptionArray[$img]['target'] : '_self';
                }

                $mainsThumbs = $this->buildThumbnail($imageArray, $configs['mainWidth'], $configs['mainHeight'], $configs['thumbImgMode'], $configs['useRatio']);

                //Build thumbnail
                if ($configs['thumbType'] == 'thumbs') {
                    if (function_exists('imagecreatetruecolor')) {
                        $thumbArray = $this->buildThumbnail($imageArray, $configs['thumbImgWidth'], $configs['thumbImgHeight'], $configs['thumbImgMode'], $configs['useRatio']);
                    }
                }else{
                    $thumbArray = $imageArray;
                }

                foreach ($listImages as $key => $image)
                {
                    $items[$key] = array();

                    if(isset($thumbArray[$key]) && $thumbArray[$key])
                        $thumbUrl= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $thumbArray[$key];
                    else $thumbUrl = '';

                    // minor thumbnail
                    if ($configs['thumbType'] == 'thumbs')
                        $items[$key]['thumb'] = '<img src="' . $thumbUrl . '" alt="Photo Thumb" width="' . $configs['thumbImgWidth'] . '" height="' . $configs['thumbImgHeight'] . '" />';
                    else
                        $items[$key]['thumb'] = '<img src="' . $thumbUrl . '" alt="Photo Thumb" width="100px" />';

                    if (isset($mainsThumbs[$key] )&& $mainsThumbs[$key])
                        $mainThumbUrl= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $mainsThumbs[$key];
                    else $mainThumbUrl='';

                    // main thumbnail
                    $items[$key]['mainThumb'] = '<img src="' . $mainThumbUrl . '" alt="'.$alts[$key].'" width="' . $configs['mainWidth'] . '" height="' . $configs['mainHeight'] . '"/>';

                    //title & description of images
                    if (isset($captionsArray[$key]) && $captionsArray[$key])
                        $items[$key]['caption'] = $captionsArray[$key];

                    //description of thumbnail
                    if (isset($thumbCaptionsArray[$key]) && $thumbCaptionsArray[$key])
                        $items[$key]['thumb_caption'] = $thumbCaptionsArray[$key];
                }
            }
        }

        $data['items'] = $items;
        $data['titles'] = $titles;
        $data['urls'] = $urls;
        $data['targets'] = $targets;
        if(isset($listImages))
            $data['images'] = $listImages;

        return $data;
    }

    /**
     * Get list image names from a folder
     *
     * @param null $folderPath
     * @return array|null
     */
    public function getImagesInDir($folderPath = null)
    {
        if (!$folderPath) {
            return null;
        }

        $imagePath = Mage::getBaseDir() . DIRECTORY_SEPARATOR . $folderPath;

        if (!is_dir($imagePath)) {
            return array();
        }

        $fileNames = $this->getFiles($imagePath);

        //We take image files only
        $list = array();
        foreach ($fileNames as $fileName) {
            $filePath = $imagePath . '/' . $fileName;
            if (preg_match("/bmp|gif|jpg|png|jpeg/", $fileName) && is_file($filePath)) {
                $list[] = $fileName;
            }
        }

        return $list;
    }

    /**
     * Build images thumbnail
     *
     * @param $imageArray
     * @param $twidth
     * @param $theight
     * @param $thumbImgMode
     * @param $aspect
     * @return array
     */
    public function buildThumbnail($images, $width, $height, $mode, $aspect)
    {
        if ($mode != 'none') {
            $thumbs = array();
            $jaImage = Mage::helper('joomlart_jmslideshow/jmimage');

            $crop = $mode == 'crop' ? true : false;
            foreach ($images as $image) {
                $thumbs[] = $jaImage->resize($image, $width, $height, $crop, $aspect);
            }
        } else {
            return $images;
        }

        return $thumbs;
    }

    /**
     * List all files from a folder
     *
     * @param $path
     * @param string $filter
     * @param bool $recurse
     * @param bool $fullPath
     * @param array $exclude
     * @return array
     */
    public function getFiles($path, $filter = '.', $recurse = false, $fullPath = false, $exclude = array('.svn', 'CVS'))
    {
        //Initialize variables
        $arr = array();

        // Is the path a folder?
        if (! is_dir($path)) {
            return array();
        }

        //Read the source directory
        $handle = opendir($path);
        while (($file = readdir($handle)) !== false) {
            $dir = $path . DIRECTORY_SEPARATOR . $file;
            $isDir = is_dir($dir);
            if (($file != '.') && ($file != '..') && (! in_array($file, $exclude))) {
                if ($isDir) {
                    if ($recurse) {
                        if (is_integer($recurse)) {
                            $recurse--;
                        }
                        $arr2 = $this->files($dir, $filter, $recurse, $fullPath);
                        $arr = array_merge($arr, $arr2);
                    }
                } else {
                    if (preg_match("/$filter/", $file)) {
                        if ($fullPath) {
                            $arr[] = $path . '/' . $file;
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

    public function parseDescOld($description)
    {
        $description = str_replace("<br />", "\n", $description);
        $description = explode("\n", $description);
        $descriptionArray = array();
        foreach ($description as $desc) {
            if ($desc) {
                $list = explode(":", $desc, 2);
                $list[1] = (count($list) > 1) ? explode("&", $list[1]) : array();
                $temp = array();
                for ($i = 0; $i < count($list[1]); ++$i) {
                    $l = explode("=", $list[1][$i]);
                    if (isset($l[1])) {
                        $temp[trim($l[0])] = trim($l[1]);
                    }
                }
                $descriptionArray[$list[0]] = $temp;
            }
        }

        return $descriptionArray;
    }

    //Format of description
    //[desc img="1.jpg" url="#"]
    //	<div class="desc-slide">
    //		<span class="text-title">Travel shopping</span>
    //		<span class="text-desc">Our online shop is the best place to buy bikes, accessories and other related products. </span>
    //		<a href="#" class="readmore">Shop now</a>
    //	</div>
    //[/desc]

    /**
     * Parse description to get attributes
     * @param $description
     * @return array attributes
     */
    public function parseDescription($description)
    {
        $descriptionArray = array();

        $regex = '#\[desc ([^\]]*)\]([^\[]*)\[/desc\]#m';
        preg_match_all($regex, $description, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $params = $this->parseParams($match[1]);
            if (is_array($params)) {
                $img = isset($params['img']) ? trim($params['img']) : null;
                if (!$img) {
                    continue;
                }

                $title = isset($params['title']) ? trim($params['title']) : '';
                $url = isset($params['url']) ? trim($params['url']) : '';
                $alt = isset($params['alt']) ? trim($params['alt']) : '';
                $target = isset($params['target']) ? trim($params['target']) : '_self';
                $descriptionArray[$img] = array('url' => $url, 'caption' => str_replace("\n", "<br />", trim($match[2])), 'title' => $title, 'alt' => $alt, 'target'=>$target);
            }
        }

        return $descriptionArray;
    }

    public function parseParams($params)
    {
        $params = html_entity_decode($params, ENT_QUOTES);
        $regex = "/\s*([^=\s]+)\s*=\s*('([^']*)'|\"([^\"]*)\"|([^\s]*))/";
        preg_match_all($regex, $params, $matches);
        $attributes = null;
        if (count($matches)) {
            $attributes = array();
            for ($i = 0; $i < count($matches[1]); $i++) {
                $key = $matches[1][$i];
                $val = $matches[3][$i] ? $matches[3][$i] : ($matches[4][$i] ? $matches[4][$i] : $matches[5][$i]);
                $attributes[$key] = $val;
            }
        }

        return $attributes;
    }

    /**
     * Get Product collection by configs
     *
     * @param null $configs
     * @return ProductCollection
     */
    public function getProductCollection($configs = null){
        $collection = null;
        if($configs)
        {
            $configs['mode'] = $configs['sourceProductsMode'];
            $limit = (isset($configs['quantity'])) ? $configs['quantity'] : (isset($configs['quanlity']) ? $configs['quanlity'] : null);
            $pageSize = $limit;

            $orderBy = (isset($configs['order_by'])) ? (int)$configs['order_by'] : 'updated_at';
            $orderDir = (isset($configs['order_dir'])) ? (int)$configs['order_dir'] : 'desc';
            $currentPage = (isset($configs['page'])) ? (int)$configs['page'] : 1;

            $storeId = Mage::app()->getStore()->getId();

            $resourceModel = 'catalog/product_collection';
            if($configs['mode'] == 'most_viewed' || $configs['mode'] == 'best_buy'){
                $resourceModel = 'reports/product_collection';
            }

            $collection = Mage::getResourceModel($resourceModel)
                ->setStoreId($storeId)
                ->addAttributeToSelect('*')
                ->addStoreFilter($storeId);

            $configs['category_ids'] = isset($configs['category_ids']) ? $configs['category_ids'] : (isset($configs['catsid']) ? $configs['catsid'] : null);
            if ($configs['category_ids']) {
                $this->addCategoryIdsFilter($collection, $configs['category_ids']);
            }

            $orderCondition = "e.{$orderBy} {$orderDir}";

            //Add more condition by mode
            if($configs['mode'] == 'top_rated' || $configs['mode'] == 'most_reviewed'){
                $collection->getSelect()->joinLeft(
                    array( 'reviewed_table' => Mage::getConfig()->getTablePrefix().'review_entity_summary' ),
                    "reviewed_table.store_id = {$storeId} AND reviewed_table.entity_pk_value = e.entity_id",
                    array()
                );

                if($configs['mode'] == 'top_rated'){
                    $orderBy = 'rating_summary';
                }

                if($configs['mode'] == 'most_reviewed'){
                    $orderBy = 'reviews_count';
                }

                $orderCondition = "reviewed_table.{$orderBy} {$orderDir}";
            }
            else if($configs['mode'] == 'attribute'){
                $collection->addAttributeToSelect('featured');
                $collection->addFieldToFilter(array(
                    array('attribute' => 'featured', 'eq' => '1')
                ));
            }
            else if($configs['mode'] == 'most_viewed'){
                $collection->addViewsCount();
                $orderCondition = null;
            }
            else if($configs['mode'] == 'best_buy'){
                $collection->addOrderedQty();
                $collection->setOrder('ordered_qty', 'desc');
                $orderCondition = null;
            }

            //Check for Flat mode
            $productFlatData = Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
            if($productFlatData == "1")
            {
                if( in_array( $configs['mode'], array('best_buy', 'most_viewed') ) ){
                    $collection->getSelect()->joinLeft(
                        array('flat' => Mage::getConfig()->getTablePrefix().'catalog_product_flat_'.$storeId),
                        "(e.entity_id = flat.entity_id ) ",
                        array(
                            'flat.name AS name','flat.small_image AS small_image', 'flat.thumbnail AS thumbnail', 'flat.price AS price','flat.special_price as special_price','flat.special_from_date AS special_from_date','flat.special_to_date AS special_to_date'
                        )
                    );
                }
            }

            //Only get enabled products
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);

            //Check in/out stock
            if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
            }

            //set random order
            if (isset($configs['random']) && $configs['random'] && is_object($collection))
            {
                $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
            }

            if($orderCondition){
                $collection->getSelect()->order($orderCondition);
            }

            //Set limit
            if(method_exists($collection, 'setMaxSize')){
                $collection->setMaxSize($limit);
            }else{
                $collection->getSelect()->limit($limit);
            }

            if(!isset($configs['get_total'])){
                //set page size
                $collection->setPageSize($pageSize)->setCurPage($currentPage);
            }
            //echo $collection->getSelect()->assemble();die();
        }

        return $collection;
    }

    /**
     * Add condition to filter by category
     *
     * @param $collection
     * @param null $categoryIds
     */
    public function addCategoryIdsFilter(&$collection, $categoryIds = null, $matchType = 'OR')
    {
        $categoryIds = explode(",", $categoryIds);
        if($categoryIds){
            if(sizeof($categoryIds) == 1 || $matchType == 'AND')
            {
                foreach ($categoryIds as $catId) {
                    $categoryModel = Mage::getModel('catalog/category')->load($catId);
                    $collection->addCategoryFilter($categoryModel); //category filter
                }
            }
            else if($matchType == 'OR'){
                $ctf = array();
                foreach ($categoryIds as $catId) {
                    $ctf[]['finset'] = $catId;
                }
                if($ctf){
                    $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                        ->addAttributeToFilter('category_id',array($ctf));
                    //->addAttributeToFilter('category_id',array($ctf))->groupByAttribute('entity_id');
                }
            }
        }
    }

    /**
     * List categories by categories id
     *
     * @param array $configs
     * @return array|null
     */
    public function getCategories($configs = array()){
        if (empty($configs['category_ids'])) return null;

        $categories = array();
        $categoryIds = explode(',', $configs['category_ids']);
        foreach ($categoryIds as $catId) {
            $catId = (int)trim($catId);
            $category = Mage::getModel('catalog/category')->load($catId);
            if ($category) {
                $categories[] = $category;
            }
        }

        return $categories;
    }

    /**
     * Get sub string from string with limit length
     *
     * @param $text
     * @param $maxchar
     * @param string $end
     * @return string
     */
    public static function subStrWords($text, $maxchar, $end = '...') {
        if (strlen($text) > $maxchar) {
            $words = explode(" ", $text);
            $output = '';
            $i = 0;
            while (1) {
                $length = (strlen($output) + strlen($words[$i]));
                if ($length > $maxchar) {
                    break;
                } else {
                    $output = $output . " " . $words[$i];
                    ++$i;
                };
            };
        } else {
            $output = $text;
        }

        return $output . $end;
    }

    public function isAdmin()
    {
        if(Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'adminhtml'){
            return true;
        }

        return false;
    }

}
