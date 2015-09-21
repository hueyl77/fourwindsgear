// JavaScript Document
(function ($) {

	var defaults = {
		productsgrid: {},
		istable: 0,
		qtytable: 10,
		qtytableportrait: 10,
		qtymobile: 4,
		qtymobileportrait: 4,
		ismobile: 0,
		qtymobile: 4,
		qtymobileportrait: 4
	}

	var jmproduct = function (options) {
		this.elm = options.productsgrid;
		this.options = $.extend({}, defaults, options);
		this.initialize();
	}
	jmproduct.prototype = {

		initialize: function () {

			//Bind Ajax load more event
			if(typeof this.options.configs != "undefined"){
				if (parseInt(this.options.configs.ajaxloadmore)) {
					this.onAjaxLoadMore();
				}
			}
            
            //Bind ajax view action 
//            if(typeof this.options.configs != "undefined"){
//				if (parseInt(this.options.configs.ajaxview)) {
//					this.runAjaxView();
//				}
//			}

			if (this.options.istable) {
				$(window).resize($.proxy(function () {

					if ($(window).width() < 780 && this.options.qtytableportrait < this.elm.children("li.item").length) {
						diff = this.options.qtytableportrait - 1;
						this.elm.children("li.item:gt(" + diff + ")").css("display", "none");
					} else if ($(window).width() > 780 && this.options.qtytable < this.elm.children("li.item").length) {
						diff = this.options.qtytable - 1;
						this.elm.children("li.item:gt(" + diff + ")").css("display", "none");
					} else {
						this.elm.children("li.item").css("display", "block");
					}

				}, this));

			}

			if (this.options.ismobile) {

				$(window).resize($.proxy(function () {
					if ($(window).width() < 361 && this.options.qtymobileportrait < this.elm.children("li.item").length) {
						diff = this.options.qtymobileportrait - 1;
						this.elm.children("li.item:gt(" + diff + ")").css("display", "none");
					} else if ($(window).width() >= 361 && this.options.qtymobile < this.elm.children("li.item").length) {
						diff = this.options.qtymobile - 1;
						this.elm.children("li.item:gt(" + diff + ")").css("display", "none");
					} else {
						this.elm.children("li.item").css("display", "block");
					}

				}, this))
			}
			this.reloadtab();
		},

		onAjaxLoadMore: function () {
			//Bind ajax event to load more button
			var $container = $("#"+this.elm.attr("id"));
			var configs = this.options.configs;
			var finishedMessage = this.options.finished_message;
			var data = $.extend({}, {}, configs);

			$("#"+this.elm.attr("id") + "-item-more h3").on('click', function(e) {
				var $target = $(this);
				var url = $target.children('a').attr("data-href");
				var totalPage = parseInt($target.children('a').attr("data-total-page"));
				var nextPage = parseInt($target.children('a').attr("data-next-page"));

				if(nextPage <= totalPage){

					data.page = nextPage;
					if(url.length){
						$.ajax({
							type: 'POST',
							url: url,
							data: data,
							beforeSend: function(){
								$target.addClass('loading');
							},
							success: function(responseData){
								$target.removeClass('loading');
								$container.append(responseData);

                                //apply quick view for new products
                                if($().jmquickview){
                                    $container.jmquickview();
                                }

								if(nextPage == totalPage){
									$target.off('click');
									$target.html(finishedMessage);
								}else{
									//Update next page
									$target.children('a').attr("data-next-page", ++nextPage);
								}
							}
						});
					}
				}

			});
		},
        
//        runAjaxView: function () {
//			//Bind ajax event to load more button
//			var $container = $("#"+this.options.containerId+"_landing_item");
//			var configs = this.options.configs;
//            var url = this.options.ajaxViewUrl;
//            var data = $.extend({}, {}, configs);
//            var ajaxItems = $("#"+this.elm.attr("id")).find(this.options.itemSelector);
//            
//            if(ajaxItems.length){
//                
//                ajaxItems.each(function(i, item){
//                   var $target = $(item).find('img');
//                   if (url.length && $target.length){
//                       $target.on(configs.ajax_event_name, function(){
//                            data.product_id = $target.attr('data-id');
//                            //ajax request to get content
//                            if(!$(item).hasClass('active')){
//                                $.ajax({
//                                    type: 'POST',
//                                    url: url,
//                                    data: data,
//                                    beforeSend: function(){
//                                        $container.addClass('landing-item-loading');
//                                        ajaxItems.removeClass('active');
//                                        $(item).addClass('active');
//                                    },
//                                    success: function(rs){
//                                        $container.removeClass('landing-item-loading');
//                                        $container.fadeOut('fast', function(){
//                                            $container.html(rs).fadeIn();
//                                        });
//                                    }
//                                });
//                            }
//                        });
//                   }
//                });
//            }
//		},

		reloadtab: function () {
			if ($("ul.jm-tabs-title li.active")) {
				$("ul.jm-tabs-title li.active").trigger("click");
			}
		}

	}

	$.fn.jmproduct = function (options) {

		options.productsgrid = $(this);
		opotions = $.extend({}, options);
		new jmproduct(options);

	};

})(jQuery);

