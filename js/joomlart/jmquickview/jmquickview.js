// JavaScript Document
(function ($) {

	var defaults = {
		listItem: {},
        itemClassToFind: ".link-compare",
		miniCartId: "#jm-mycart",
		loadingClass: ".jm-mycart-loading",
		showCartRightAway: true,
		quickViewText: "Quick view",
		quickViewTextTitle: "Product quick view",
		currentText: "Product {current} of {total}",
		previousText: "Preview",
		nextText: "Next",
        closeText: "Close",
        deleteConfirmMessage: "Are you sure you would like to remove this item from the shopping cart?",
        colorBoxTimeOut: null
	}

	var jmquickview = function (container, options) {

		this.options = $.extend({}, defaults, options);

		this.options.container = container;

		this.initialize();
	}

	jmquickview.prototype = {
        
		bindCartEvents: function () {

            //off event click on remove button
			$(this.options.miniCartId + " #cart-sidebar .btn-remove").off("click");
            $(this.options.miniCartId + " #cart-sidebar .btn-remove").attr('onclick', 'javascript:void(0);')

			//bind cart's buttons on the header
			$(this.options.miniCartId + " #cart-sidebar .btn-remove").on("click", $.proxy(function (e) {
                //console.log(e.target);
				e.stopPropagation();
				e.preventDefault();

				var $targetObj = $(e.target);
				if(!$targetObj.hasClass('btn-remove')){
					$targetObj = $(e.target).parent();
				}

				if (confirm(this.options.deleteConfirmMessage)) {

					//Ajax send request remove product from mini cart
					$.ajax({
						url: $targetObj.attr("href").replace("checkout/cart/delete", "quickview/index/deletecartsidebar"),
						dataType: 'json',
						beforeSend: $.proxy( function(){
							$(this.options.miniCartId).trigger("mouseenter");
							this.toggleLoading();
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
					this.toggleLoading();
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

                if($("#jm-cart-status").length){
                    $("#jm-cart-status").html(data);
                }

				$.post(baseurl + "quickview/links/sum", $.proxy(function (totalcart) {
					if ($(".totalcart").length)
                        //console.log(totalcart);
						$(".totalcart").html(totalcart);
				}, this));

                //Update mini cart content
				$.post(baseurl + "quickview/links/updatecart", $.proxy(function (datacart) {

					//Update content for mini cart
                    if ($(this.options.miniCartId).length){
                        $(this.options.miniCartId).replaceWith(datacart);
                        this.bindCartEvents();
                    }

					if(lastAction == 'delete'){

                        $.proxy(function(){
                            setTimeout(function(){
                                $(this.options.miniCartId).trigger("mouseleave");
                            }, 5000);
                        }, this);
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

		ajaxAddToCart: function (url) {
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

		toggleLoading: function () {
			if($(this.options.loadingClass).length){
				if ($(this.options.loadingClass).css("display") == "none") {
					$(this.options.loadingClass).show();
				} else {
					$(this.options.loadingClass).hide();
				}
			}
		},

        toggleColorBoxBorder: function(){
            if($("#cboxMiddleLeft").length){
                if($("#cboxMiddleLeft").css("display") == "none"){
                    $("#cboxMiddleLeft").show();
                    $("#cboxMiddleRight").show();
                    $("#cboxTopLeft").show();
                    $("#cboxTopCenter").show();
                    $("#cboxTopRight").show();
                    $("#cboxBottomLeft").show();
                    $("#cboxBottomCenter").show();
                    $("#cboxBottomRight").show();
                    $("#cboxClose").show();
                }else{
                    $("#cboxMiddleLeft").hide();
                    $("#cboxMiddleRight").hide();
                    $("#cboxTopLeft").hide();
                    $("#cboxTopCenter").hide();
                    $("#cboxTopRight").hide();
                    $("#cboxBottomLeft").hide();
                    $("#cboxBottomCenter").hide();
                    $("#cboxBottomRight").hide();
                    $("#cboxClose").hide();
                }
            }
        },

        bindCloseColorBox: function(){
            this.colorBoxTimeOut = window.setTimeout(function(){
                if($("#colorbox").length && $("#colorbox").css("display") == 'block'){
                    $("#cboxClose").trigger("click");
                }
            }, 5000);

            $("#cboxTitle").append("&nbsp;");
            //console.log("bind auto close colorbox");
        },

        offCloseColorBox: function(){
            clearTimeout(this.colorBoxTimeOut);
            //console.log("off auto close colorbox");
        },

		initialize: function () {
			$(this.options.miniCartId).data("", this);

			//Bind events needed to cart sidebar
			this.bindCartEvents();

			var productId = null;
            var productLink = null;

			$(this.options.container).find(this.options.itemClassToFind).each($.proxy(function (index, item) {

                //get product link
                productLink = $(item).attr("href");
                //console.log(productLink);

				if ((productLink != null) && (productLink != 'undefined')) {

                    var product = productLink.match(/product\/\d+/);
                    var $parentOfItem = $(item).parent().parent();

                    if(product && !$parentOfItem.siblings("#quickviewbox_" + product[0].replace("product/", "")).length)
                    {
                        productId = product[0].replace("product/", "");
                        var $quickViewTag = $("<a/>", {
                            "rel": "quickviewbox_" + $(this.options.container).attr("id"),
                            "href": baseurl + "quickview/index/index/id/" + productId,
                            "id": "quickviewbox_" + productId,
                            "title": this.options.quickViewTextTitle,
                            "class": "button jmquickview"
                        });
                        $quickViewTag.append('<span>' + this.options.quickViewText + '</span>');

                        //add quick view tag
                        $parentOfItem.before($quickViewTag);

                        //Bind colorbox event to quick view tag
                        $quickViewTag.colorbox({
                            className: "colorbox-jmquickview",
                            maxWidth: "1260",
                            initialWidth: "160",
                            initialHeight: "160",
                            current: this.options.currentText,
                            previous: this.options.previousText,
                            next: this.options.nextText,
                            close: this.options.closeText,
                            onLoad: $.proxy(function(){

                                //off auto close colorbox event
                                this.offCloseColorBox();

                                //hide colorbox border
                                this.toggleColorBoxBorder();
                            },this),

                            onComplete: $.proxy(function () {

                                //show colorbox border
                                this.toggleColorBoxBorder();

                                if (baseurl.indexOf("https") !== -1) {
                                    var action = $(".product_addtocart_form").attr("action").replace("http://", "https://");
                                    $(".product_addtocart_form").attr("action", action);
                                    $(".link-compare").attr("href", $(".link-compare").attr("href").replace("http://", "https://"));
                                    $(".link-wishlist").attr("href", $(".link-wishlist").attr("href").replace("http://", "https://"));
                                }

                                // Event to add product to wishlist
                                $("a.link-wishlist").on("click", function (e) {

                                    e.preventDefault();
                                    if (!productAddToCartForm.submitLight(this, $(this).attr("href"))) return false;

                                    var ulrwishlist = $(this).attr("href");
                                    ulrwishlist = ulrwishlist.replace("wishlist/index/add", "quickview/wishlist/addwishlist");
                                    var data = $('.product_addtocart_form').serialize();

                                    $.ajax({
                                        url: ulrwishlist,
                                        dataType: 'json',
                                        type: 'post',
                                        data: data,
                                        beforeSend: function(){
                                            $("#cboxLoadingGraphic").show();
                                        },
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

                            // Event to add product to compare
                            $("a.link-compare").on("click", function (e) {
                                e.preventDefault();
                                var urlcompare = $(this).attr("href");
                                urlcompare = urlcompare.replace("catalog/product_compare/add", "quickview/index/compare");

                                $.ajax({
                                    url: urlcompare,
                                    dataType: 'json',
                                    beforeSend: function(){
                                        $("#cboxLoadingGraphic").show();
                                    },
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
                                            bindCompare();
                                        }
                                    }
                                })
                            });

                            $(".quick-add-to-cart").on("click", $.proxy(function (e) {

                                if (!productAddToCartForm.submit($(this).children("button")[0])) return false;

                                e.preventDefault();

                                var urladdcart = $(".product_addtocart_form").attr("action");
                                urladdcart = urladdcart.replace("checkout/cart", "quickview/index");
                                var data = $('.product_addtocart_form').serialize();
                                data += '&isAjax=1';
                                urladdcart = urladdcart + "?" + data;

                                if (this.options.showCartRightAway) {
                                    $.colorbox({
                                        href: urladdcart,
                                        className: "colorbox-jmquickview",
                                        close: this.options.closeText,
                                        onComplete: $.proxy(function () {

                                        this.addComplete();
                                        //off auto close colorbox event
                                        this.bindCloseColorBox();
                                    }, this)});
                                } else {
                                    urladdcart = urladdcart + "&onlyadd=1";
                                    this.ajaxAddToCart(urladdcart)
                                    $("#cboxClose").trigger("click");
                                    this.toggleLoading();
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
		bindCompare();
	});

    bindCompare = function() {
		$("#compare-items").find(".btn-remove").off("click").on("click", function (e) {
			e.preventDefault();
			urlcompare = $(this).attr("href");
			urlcompare = urlcompare.replace("catalog/product_compare/remove", "quickview/index/remove");

			$.ajax({
				url: urlcompare,
				dataType: 'json',
                beforeSend: function(){
                    $("#cboxLoadingGraphic").show();
                },
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

			$.ajax({
				url: urlcompare,
				dataType: 'json',
                beforeSend: function(){
                    $("#cboxLoadingGraphic").show();
                },
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
