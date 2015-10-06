

/* Base settings */
body#bd {
	<?php if (isset($baseconfig["bgolor"]) && ($baseconfig["bgolor"])): ?>
		background-color: <?php echo $baseconfig["bgolor"] ?>;
	<?php endif;?>
	<?php if (isset($baseconfig["bgimage"]) && ($baseconfig["bgimage"])): ?>
		background-image:url("images/<?php echo $baseconfig["bgimage"]; ?>");
	<?php endif;?>
}

#jm-wrapper {
	<?php if (isset($baseconfig["wrappercolor"]) && ($baseconfig["wrappercolor"])): ?>
		background-color: <?php echo $baseconfig["wrappercolor"] ?>;
	<?php endif;?>
	<?php if (isset($baseconfig["wrapperbg"]) && ($baseconfig["wrapperbg"])): ?>
		background-image:url("images/<?php echo $baseconfig["wrapperbg"]; ?>");
	<?php endif;?>
}

#jm-header {
	<?php if (isset($baseconfig["headerimage"]) && ($baseconfig["headerimage"])): ?>
		background-image:url("images/<?php echo $baseconfig["headerimage"]; ?>");
	<?php endif;?>
	<?php if (isset($baseconfig["headerolor"]) && ($baseconfig["headerolor"])): ?>
		background-color: <?php echo $baseconfig["headerolor"] ?>;
	<?php endif;?>
}

.jm-megamenu ul.level0 li.mega a.mega {
	border-color: <?php echo $baseconfig["headerolor"] ?>;
}

#jm-bots3,
#jm-footer {
	<?php if (isset($baseconfig["footerimage"]) && ($baseconfig["footerimage"])): ?>
		background-image:url("images/<?php echo $baseconfig["footerimage"]; ?>");
	<?php endif;?>
	<?php if (isset($baseconfig["footerolor"]) && ($baseconfig["footerolor"])): ?>
		background-color: <?php echo $baseconfig["footerolor"] ?>;
	<?php endif;?>
}

a:active,
a:focus,
a:hover,
.subtitle,
.sub-title,
.btn-toggle.active,
.btn-toggle:hover,
.btn-toggle:active,
.btn-toggle:focus,
.pages ol li.current,
.pages ol li a.active,
.pages ol li a:focus,
.pages ol li a:hover,
.breadcrumbs li a:active,
.breadcrumbs li a:focus,
.breadcrumbs li a:hover,
.data-table .remove-item:active,
.data-table .remove-item:focus,
.data-table .remove-item:hover,
.form-search button:hover,
.form-language a:active,
.form-language a:focus,
.form-language a:hover,
.form-language a.active,
.form-currency li.active a,
.shop-access ul li a:active,
.shop-access ul li a:focus,
.shop-access ul li a:hover,
.jm-product-lemmon .prev:hover,
.jm-product-lemmon .next:hover,
.jm-product-deals .products-grid .deals-info li .item-count-days,
.jm-product-deals .products-grid li:hover .product-name a,
.block.block-cart .mycart-toggle a,
.block-account .block-content li a:active,
.block-account .block-content li a:focus,
.block-account .block-content li a:hover,
.block-account .block-content li.current,
.block-compare ol#compare-items  li a:active,
.block-compare ol#compare-items  li a:focus,
.block-compare ol#compare-items  li a:hover,
.compare-table tr.first td .product-name a:active,
.compare-table tr.first td .product-name a:focus,
.compare-table tr.first td .product-name a:hover,
.block-tags .block-content a:active,
.block-tags .block-content a:hover,
.block-tags .block-content a:focus,
.jm-product-list .products-grid li:hover .product-name,
.products-list .product-name a:active,
.products-list .product-name a:focus, 
.products-list .product-name a:hover,
.ratings .rating-links a:active,
.ratings .rating-links a:focus,
.ratings .rating-links a:hover,
.availability.out-of-stock span,
.availability-only strong,
.availability-only-details tr.odd td.last,
span.price,
.minimal-price-link .label,
.minimal-price-link .price,
.tier-prices-grouped li,
.more-views li:focus a i,
.more-views li:hover a i,
.more-views li.active a i,
.jm-product-price .availability span,
.product-name:active,
.product-name:focus,
.product-name:hover,
.tags-list li a:active,
.tags-list li a:focus,
.tags-list li a:hover,
.advanced-search-summary strong,
.page-sitemap .sitemap a:active,
.page-sitemap .sitemap a:focus,
.page-sitemap .sitemap a:hover,
.page-sitemap .sitemap li.level-0 a,
.cart-table .item-msg,
.cart .totals .checkout-types a,
.block-layered-nav dd li:focus,
.block-layered-nav dd li:hover,
.block-layered-nav dd li a:active .price,
.block-layered-nav dd li a:focus .price,
.block-layered-nav dd li a:hover .price,
.block-layered-nav dd li:focus .price,
.block-layered-nav dd li:hover .price,
.block-layered-nav dd li:focus a,
.block-layered-nav dd li:hover a,
.block-layered-nav dd li a:active,
.block-layered-nav dd li a:focus,
.block-layered-nav dd li a:hover,
.jm-megamenu ul.level0 li.mega a.mega.active,
.jm-megamenu ul.level0 li.mega a.mega:hover, 
.jm-megamenu ul.level0 li.mega:hover > a.mega,
.jm-megamenu ul.level1 li.mega div.group-title a.mega,
.jm-megamenu ul.level2 li.mega.active a.mega, 
.jm-megamenu ul.level2 li.mega a.mega:active, 
.jm-megamenu ul.level2 li.mega a.mega:focus, 
.jm-megamenu ul.level2 li.mega a.mega:hover,
.block-cart .product-details .product-name a:hover,
.jm-product-onedeal .products-grid .deals-info li .item-count-days,
.block-cart.product-details .edit:active,
.block-cart .product-details .edit:focus,
.block-cart .product-details .edit:hover,
.block-cart .product-details .remove:active,
.block-cart .product-details .remove:focus,
.block-cart .product-details .remove:hover,
.gift-messages h3,
.gift-messages-form h4,
.info-set h3,
.info-set h4,
.info-set .box h2,
.info-set .data-table .product-name,
.opc-block-progress dt.complete a, 
.opc-block-progress dt.complete .separator,
#opc-login .col-2 .buttons-set a:active,
#opc-login .col-2 .buttons-set a:focus,
#opc-login .col-2 .buttons-set a:hover,
.checkout-progress li.active,
.account-login .buttons-set a,
.addresses-list h2,
.news-view-more:active,
.news-view-more:focus,
.news-view-more:hover,
#jm-bots3 ul li a:active,
#jm-bots3 ul li a:focus,
#jm-bots3 ul li a:hover,
.jm-legal a,
.best-selling h3,
.info-inner2 ul.list-info li a,
.jm-error-page a:active,
.jm-error-page a:focus,
.jm-error-page a:hover,
.jm-error-page h1{
	color: <?php echo $baseconfig["color"] ?>;
}

#button-btt,
.contactinfo-inner .shop-now,
.dashboard .box-reviews .number,
.dashboard .box-tags .number,
.jm-product-deals .products-grid .view-details:active,
.jm-product-deals .products-grid .view-details:focus,
.jm-product-deals .products-grid .view-details:hover,
.jm-product-onedeal .products-grid .view-details:active,
.jm-product-onedeal .products-grid .view-details:focus,
.jm-product-onedeal .products-grid .view-details:hover,
#jm-mycart .btn-toggle,
.jm-mask-desc .jm-slide-desc .readmore,
.from-category:visited,
.from-category,
#cboxClose,
button.button { 
	background-color: <?php echo $baseconfig["color"] ?>;
}

.block-cart .summary a{
	color: <?php echo $baseconfig["color"] ?> !important;
}

.more-views li.active a, 
.more-views li a:active, 
.more-views li a:focus, 
.more-views li a:hover, 
.ja-tab-navigator li.active a,
.checkout-progress li.active {
	border-color: <?php echo $baseconfig["color"] ?>;
}


<?php if (isset($baseconfig["logo"]) && ($baseconfig["logo"])): ?>
#logo a {
		background-image:url("images/<?php echo $baseconfig["logo"]; ?>")  !important;
}
<?php endif;?>

<?php if (isset($baseconfig["flogo"]) && ($baseconfig["flogo"])): ?>
#flogo a {
		background-image:url("images/<?php echo $baseconfig["flogo"]; ?>") !important;
}
<?php endif;?>

<?php if (isset($baseconfig["megaseparator"]) && ($baseconfig["megaseparator"])): ?>
.jm-megamenu ul.level0 li.mega {
		background-image:url("images/<?php echo $baseconfig["megaseparator"]; ?>");
}
<?php endif;?>

<?php if (isset($baseconfig["nextpre"]) && ($baseconfig["nextpre"])): ?>
.jm-slide-buttons span {
		background-image:url("images/<?php echo $baseconfig["nextpre"]; ?>");
}
<?php endif;?>

<?php if (isset($baseconfig["quickviewpre"]) && ($baseconfig["quickviewpre"])): ?>
#cboxPrevious {
		background-image:url("images/<?php echo $baseconfig["quickviewpre"]; ?>");
}
<?php endif;?>

<?php if (isset($baseconfig["quickviewnext"]) && ($baseconfig["quickviewnext"])): ?>
#cboxNext {
		background-image:url("images/<?php echo $baseconfig["quickviewnext"]; ?>");
}
<?php endif;?>

#jm-tops1 {
	<?php if (isset($baseconfig["slideshowimage"]) && ($baseconfig["slideshowimage"])): ?>
		background-image:url("images/<?php echo $baseconfig["slideshowimage"]; ?>");
	<?php endif;?>
	<?php if (isset($baseconfig["slideshowcolor"]) && ($baseconfig["slideshowcolor"])): ?>
		background-color: <?php echo $baseconfig["slideshowcolor"] ?>;
	<?php endif;?>
}

#jm-tops2 {
	<?php if (isset($baseconfig["sliderimage"]) && ($baseconfig["sliderimage"])): ?>
		background-image:url("images/<?php echo $baseconfig["sliderimage"]; ?>");
	<?php endif;?>
	<?php if (isset($baseconfig["slidercolor"]) && ($baseconfig["slidercolor"])): ?>
		background-color: <?php echo $baseconfig["slidercolor"] ?>;
	<?php endif;?>
}

#jm-mass-top{
	<?php if (isset($baseconfig["bannerimage"]) && ($baseconfig["bannerimage"])): ?>
		background-image:url("images/<?php echo $baseconfig["bannerimage"]; ?>");
	<?php endif;?>
	<?php if (isset($baseconfig["bannercolor"]) && ($baseconfig["bannercolor"])): ?>
		background-color: <?php echo $baseconfig["bannercolor"] ?>;
	<?php endif;?>
}

#jm-mass-bottom{
	<?php if (isset($baseconfig["productimage"]) && ($baseconfig["productimage"])): ?>
		background-image:url("images/<?php echo $baseconfig["productimage"]; ?>");
	<?php endif;?>
	<?php if (isset($baseconfig["productcolor"]) && ($baseconfig["productcolor"])): ?>
		background-color: <?php echo $baseconfig["productcolor"] ?>;
	<?php endif;?>
}

#jm-bots1{
	<?php if (isset($baseconfig["lastestnewsimage"]) && ($baseconfig["lastestnewsimage"])): ?>
		background-image:url("images/<?php echo $baseconfig["lastestnewsimage"]; ?>");
	<?php endif;?>
	<?php if (isset($baseconfig["lastestnewscolor"]) && ($baseconfig["lastestnewscolor"])): ?>
		background-color: <?php echo $baseconfig["lastestnewscolor"] ?>;
	<?php endif;?>
}

#jm-bots2{
	<?php if (isset($baseconfig["brandimage"]) && ($baseconfig["brandimage"])): ?>
		background-image:url("images/<?php echo $baseconfig["brandimage"]; ?>");
	<?php endif;?>
	<?php if (isset($baseconfig["brandcolor"]) && ($baseconfig["brandcolor"])): ?>
		background-color: <?php echo $baseconfig["brandcolor"] ?>;
	<?php endif;?>
}