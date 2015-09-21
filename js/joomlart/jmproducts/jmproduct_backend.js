// JavaScript Document
(function($){
	$(document).ready(function(){
		$('tr[id*="row_joomlart_jmproducts_joomlart_jmproducts_setting"]').find('td.scope-label').html('&nbsp;');
		$('tr[id*="row_joomlart_jmproducts_joomlart_jmproducts_title"]').css({"background-color" : "#ccc", "font-weight":"bold"}).find('td.scope-label').html('&nbsp;');

		//Do something more if need here...
	})
})(jQuery);


