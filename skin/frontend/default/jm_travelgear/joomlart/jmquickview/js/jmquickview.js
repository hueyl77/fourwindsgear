// JavaScript Document
(function ($) {

	var defaults = {
		listitem: {},
		btnCart: ".btn-cart",
		miniCartId: "#jm-mycart",
		btnCompare: ".btn-compare",
		loadingClass: ".jm-mycart-loading",
		showcartrightaway: true,
		quickviewtext: "Quick view",
		quickviewtexttitle: "Product quick view",
		currenttext: "Product quick view",
		previoustext: "Preview",
		nexttext: "Next"
	}

	var jmquickview = function (container, options) {

		this.options = $.extend({}, defaults, options);

		this.options.container = container;

		this.initialize();
	}

	jmquickview.prototype = {
		bindcartEvents: function () {

			$(this.options.miniCartId + " #cart-sidebar .remove").off("click");
			$(this.options.miniCartId + " #cart-sidebar .remove").attr('onclick', 'javascript:void(0);')
			//bind cart's buttons on the header
			$(this.options.miniCartId + " #cart-sidebar .remove").on("click", $.proxy(function (e) {
				e.stopPropagation();
				e.preventDefault();

				var $targetObj = $(e.target);
				if(!$targetObj.hasClass('remove')){
					$targetObj = $(e.target).parent();
				}

				if (confirm($targetObj.attr("data-confirm"))) {
					var urldelete = $targetObj.attr("href");
					urldelete = urldelete.replace("checkout/cart/delete", "quickview/index/deletecartsidebar");

					//Ajax send request delete product from mini cart
					$.ajax({
						url: urldelete,
						dataType: 'json',
						beforeSend: $.proxy( function(){
							$(this.options.miniCartId).trigger("mouseenter");
							this.toggleloading();
						}, this),
						success: $.proxy(function (data) {
							if (data.status == 'ERROR') {
								alert(data.message);
							} else {
								this.deleteComplete();
							}

						}, this)

					});
				}

			}, this));

			if($(".jmquickview_cart_form button.btn-update,.jmquickview_cart_form button.btn-empty").length){

				$(".jmquickview_cart_form button.btn-update,.jmquickview_cart_form button.btn-empty").off("click");
				$(".jmquickview_cart_form button.btn-update,.jmquickview_cart_form button.btn-empty").on("click", $.proxy(function (e) {

					e.preventDefault();
					//loading();
					form = $(e.target).parents(".jmquickview_cart_form");
					urlcart = form.attr('action');
					urlcart = urlcart.replace("checkout/cart", "quickview/index");
					var datacart = form.serialize();
					datacart = datacart + "&update_cart_action=" + $(e.target).attr("value");
					urlcart = urlcart + "?" + datacart
					this.toggleloading();
					$.post(urlcart, $.proxy(function (data) {
						this.addComplete();
					}, this));

				}, this));
			}

		},

		updateCart: function(lastAction){
			$.post(baseurl + "quickview/links/index", $.proxy(function (data) {
				if ($(".top-link-cart").length)
					$(".top-link-cart").html(data);

				$.post(baseurl + "quickview/links/sum", $.proxy(function (totalcart) {
					if ($(".totalcart").length)
						$(".totalcart").html(totalcart);
				}, this));

				$.post(baseurl + "quickview/links/updatecart", $.proxy(function (datacart) {
					//Update content for mini cart
					if ($(this.options.miniCartId + " .inner-toggle").length) {
						$(this.options.miniCartId + " .inner-toggle").html(datacart);
						this.bindcartEvents();
					}

					if(lastAction == 'delete'){
						this.toggleloading();
						setTimeout(function(){
							$(this.options.miniCartId).trigger("mouseleave");
						}, 5000);
					}

				}, this));

			}, this));
		},

		addComplete: function () {
			this.updateCart('add');
		},

		deleteComplete: function () {
			this.updateCart('delete');
		},

		ajaxaddtocart: function (url) {
			$.ajax({
				url: url,
				dataType: 'json',
				success: $.proxy(function (data) {

					if (data.status == 'ERROR') {
						alert(data.message);
					} else {
						this.addComplete();
					}
				}, this)

			});
		},

		toggleloading: function () {
			if($(this.options.loadingClass).length){
				if ($(this.options.loadingClass).css("display") == "none") {
					$(this.options.loadingClass).show();
				} else {
					$(this.options.loadingClass).hide();
				}
			}
		},

		initialize: function () {
			$(this.options.miniCartId).data("mycartobj", this);

			//Bind events needed to cart sidebar
			this.bindcartEvents();

			//options = this.options;
			$(this.options.container).find(this.options.btnCompare).each($.proxy(function (index, bcart) {
				if($(bcart).siblings("ul.add-to-links").length){
					productlink = $(bcart).siblings("ul.add-to-links").find("li a.link-compare").attr("href");
				}
				else{ //for JM product slider markup
					productlink = $(bcart).attr('onclick');
				}
				var productid = null;
				bcartparent = $(bcart).parent();
				if ((productlink != null) && (productlink != 'undefined') && (product = productlink.match(/product\/\d+/))) {
					productid = product[0].replace("product/", "");
					if(productid){
						var $quickviewtag = $("<a/>", {
							"rel": "quickviewbox",
							"href":  baseurl + "quickview/index/index/id/" + productid,
							"id": "quickviewbox" + productid,
							"title": this.options.quickviewtexttitle
						});
						$quickviewtag.append('<button type="button" class="form-button jmquickview"><span>' + this.options.quickviewtext + '</span></button>');

						if(bcartparent.children("#quickviewbox" + product[0].replace("product/", "")).length){
							//console.log("Run quick view again: #"+"quickviewbox" + productid);
							bcartparent.children("#quickviewbox" + product[0].replace("product/", "")).replaceWith($quickviewtag);
						}else{
							$(bcart).before($quickviewtag);
						}

						$quickviewtag.colorbox({current: this.options.currenttext, onComplete: $.proxy(function () {
							if (baseurl.indexOf("https") !== -1) {
								action = $(".product_addtocart_form").attr("action");
								action = action.replace("http://", "https://");
								$(".product_addtocart_form").attr("action", action);
								$(".link-compare").attr("href", $(".link-compare").attr("href").replace("http://", "https://"));
								$(".link-wishlist").attr("href", $(".link-wishlist").attr("href").replace("http://", "https://"));
							}

							//Start bind events to elements on quick view popup
							// add product to wishlist
							$("a.link-wishlist").on("click", function (e) {
								e.preventDefault();
								if (!productAddToCartForm.submitLight(this, $(this).attr("href"))) return false;
								ulrwishlist = $(this).attr("href");
								ulrwishlist = ulrwishlist.replace("wishlist/index/add", "quickview/wishlist/addwishlist");
								var data = $('.product_addtocart_form').serialize();
								$("#cboxLoadingGraphic").show();
								$.ajax({
									url: ulrwishlist,
									dataType: 'json',
									type: 'post',
									data: data,
									success: function (data) {
										$("#cboxLoadingGraphic").hide();
										if (data.status == 'ERROR') {
											alert(data.message);
										} else {
											alert(data.message);
											if ($('.block-wishlist').length) {
												$('.block-wishlist').replaceWith(data.sidebar);
											} else {
												if ($('.col-right').length) {
													$('.col-right').prepend(data.sidebar);
												}
											}
											if ($('.header .links').length) {
												$('.header .links').replaceWith(data.toplink);
											}
										}
									}
								});
							});

							// add product to compare
							$("a.link-compare").on("click", function (e) {
								e.preventDefault();
								urlcompare = $(this).attr("href");
								urlcompare = urlcompare.replace("catalog/product_compare/add", "quickview/index/compare");
								$("#cboxLoadingGraphic").show();
								$.ajax({
									url: urlcompare,
									dataType: 'json',
									success: function (data) {
										$("#cboxLoadingGraphic").hide();
										if (data.status == 'ERROR') {
											alert(data.message);
										} else {
											alert(data.message);
											if ($('.block-compare').length) {
												$('.block-compare').replaceWith(data.sidebar);
											} else {
												if ($('.col-right').length) {
													$('.col-right').prepend(data.sidebar);
												}
											}
											comparebinding();

										}
									}
								})
							});

							//Bind event to add cart on quick view popup

							//off prevent default
							if($("a.optionsboxadd").length){
								$("a.optionsboxadd").attr("href", "javascript:void(0);");
							}

							$("a.optionsboxadd").on("click", $.proxy(function (e) {

								if (!productAddToCartForm.submit($(this).children("button")[0])) return false;

								e.preventDefault();

								var urladdcart = $(".product_addtocart_form").attr("action");
								urladdcart = urladdcart.replace("checkout/cart", "quickview/index");
								var data = $('.product_addtocart_form').serialize();
								data += '&isAjax=1';
								urladdcart = urladdcart + "?" + data;

								if (this.options.showcartrightaway) {
									$.colorbox({href: urladdcart, onComplete: $.proxy(function () {
										this.addComplete();
									}, this)});
								} else {
									urladdcart = urladdcart + "&onlyadd=1";
									this.ajaxaddtocart(urladdcart)
									$("#cboxClose").trigger("click");
									this.toggleloading();
								}

							}, this));

						}, this)});
					}
				}
			}, this));
		}
	}

	$.fn.jmquickview = function (options) {
		new jmquickview(this, options);

	};

	$(document).ready(function () {
		comparebinding();
	});

	function comparebinding() {
		$("#compare-items").find(".btn-remove").off("click").on("click", function (e) {
			e.preventDefault();
			urlcompare = $(this).attr("href");
			urlcompare = urlcompare.replace("catalog/product_compare/remove", "quickview/index/remove");
			$("#cboxLoadingGraphic").show();
			$.ajax({
				url: urlcompare,
				dataType: 'json',
				success: function (data) {
					$("#cboxLoadingGraphic").hide();
					if (data.status == 'ERROR') {
						alert(data.message);
					} else {
						alert(data.message);
						if ($('.block-compare').length) {
							$('.block-compare').replaceWith(data.sidebar);
						} else {
							if ($('.col-right').length) {
								$('.col-right').prepend(data.sidebar);
							}
						}
					}
				}
			})
		});

		$(".block-compare").find(".actions").children("a").off("click").on("click", function (e) {

			e.preventDefault();
			urlcompare = $(this).attr("href");
			urlcompare = urlcompare.replace("catalog/product_compare/clear", "quickview/index/clear");
			$("#cboxLoadingGraphic").show();
			$.ajax({
				url: urlcompare,
				dataType: 'json',
				success: function (data) {
					$("#cboxLoadingGraphic").hide();
					if (data.status == 'ERROR') {
						alert(data.message);
					} else {
						alert(data.message);
						if ($('.block-compare').length) {
							$('.block-compare').replaceWith(data.sidebar);
						} else {
							if ($('.col-right').length) {
								$('.col-right').prepend(data.sidebar);
							}
						}
					}
				}
			})
		});
	}

})(jQuery)
