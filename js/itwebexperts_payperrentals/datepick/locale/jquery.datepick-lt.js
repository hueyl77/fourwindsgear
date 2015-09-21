/* http://keith-wood.name/datepick.html
   Lithuanian localisation for jQuery Datepicker.
   Written by Arturas Paleicikas <arturas@avalon.lt> */
(function($jppr) {
	$jppr.datepick.regional['lt'] = {
		monthNames: ['Sausis','Vasaris','Kovas','Balandis','Gegužė','Birželis',
		'Liepa','Rugpjūtis','Rugsėjis','Spalis','Lapkritis','Gruodis'],
		monthNamesShort: ['Sau','Vas','Kov','Bal','Geg','Bir',
		'Lie','Rugp','Rugs','Spa','Lap','Gru'],
		dayNames: ['sekmadienis','pirmadienis','antradienis','trečiadienis','ketvirtadienis','penktadienis','šeštadienis'],
		dayNamesShort: ['sek','pir','ant','tre','ket','pen','šeš'],
		dayNamesMin: ['Se','Pr','An','Tr','Ke','Pe','Še'],
		/*dateFormat: 'dd/mm/yyyy',*/ firstDay: 1,
		renderer: $jppr.datepick.defaultRenderer,
		prevText: '&#x3c;Atgal',  prevStatus: '',
		prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: '',
		nextText: 'Pirmyn&#x3e;', nextStatus: '',
		nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: '',
		currentText: 'Šiandien', currentStatus: '',
		todayText: 'Šiandien', todayStatus: '',
		clearText: 'Išvalyti', clearStatus: '',
		closeText: 'Uždaryti', closeStatus: '',
		yearStatus: '', monthStatus: '',
		weekText: 'Wk', weekStatus: '',
		dayStatus: 'D, M d', defaultStatus: '',
		isRTL: false
	};
	$jppr.datepick.setDefaults($jppr.datepick.regional['lt']);
})(jQuery);
