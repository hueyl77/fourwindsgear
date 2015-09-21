<?php

 $mageFilename = "../../../../../../../app/Mage.php";
 require_once $mageFilename; 
 umask(0);
 Mage::app();
 $profile = $_GET["profile"];
 
 if($profile = $_GET["profile"]){
     $activeprofile = $profile;
 }else{
     $activeprofile = Mage::helper("jmbasetheme")->getprofile();
 }
 
 $defaultheme = Mage::helper("jmbasetheme")->getDefaultTheme();
 $baseconfig = Mage::helper("jmbasetheme")->getactiveprofile($activeprofile);
 header("Content-type: text/css; charset: UTF-8");
?>

.colors-setting a.active span{border:1px solid #fff;}

/* Base settings */
body#bd {
   background-color: <?php echo $baseconfig["bgolor"]; ?>;
	<?php if (isset($baseconfig["bgimage"]) && ($baseconfig["bgimage"])): ?>
		background-image:url("../../../../<?php echo $defaultheme; ?>/wavethemes/jmbasetheme/profiles/<?php echo $activeprofile ?>/images/<?php echo $baseconfig["bgimage"]; ?>");
	<?php endif;?>
}

@media only screen and (min-width:986px) and (max-width: 1235px) {
	
}

/* Grid product list tablet portrait settings */
@media only screen and (min-width:720px) and (max-width: 985px){
    <?php if(isset($baseconfig["productgridnumbercolumntabletportrait"])&&$baseconfig["productgridnumbercolumntabletportrait"]) { ?> 
   .category-products .products-grid li.item {
      width: <?php echo 100/$baseconfig["productgridnumbercolumntabletportrait"]."% !important;" ?> 
   }
   <?php } ?>
 
}

