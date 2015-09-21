/* http://keith-wood.name/datepick.html
   Dutch localisation for jQuery Datepicker.
   Written by Sander Kwantes (info@wildatheart.eu). */
(function($jppr) {
	$jppr.datepick.regional['nl'] = {
		monthNames: ['januari','februari','maart','april','mei','juni',
		'juli','augustus','september','oktober','november','december'],
		monthNamesShort: ['jan','feb','mrt','apr','mei','jun',
		'jul','aug','sep','okt','nov','dec'],
		dayNames: ['zondag','maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag'],
		dayNamesShort: ['zo','ma','di','wo','do','vr','za'],
		dayNamesMin: ['zo','ma','di','wo','do','vr','za'],
		//dateFormat: 'mm/dd/yy',
		firstDay: 1,
		renderer: $jppr.datepick.defaultRenderer,
		prevText: '&#x3c;', prevStatus: 'Vorige maand',
		prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: '',
		nextText: '&#x3e;', nextStatus: 'Volgende maand',
		nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: '',
		currentText: 'Huidige maand', currentStatus: '',
		todayText: 'Vandaag', todayStatus: '',
		clearText: 'Wissen', clearStatus: 'Huidige datum wissen',
		closeText: 'Sluiten', closeStatus: 'Zonder wijzigingen sluiten',
		yearStatus: 'Ander jaar kiezen', monthStatus: 'Andere maand kiezen',
		weekText: 'Wk', weekStatus: 'Week van de maand',
		dateStatus: 'Kies DD d MM', defaultStatus: 'Kies een datum',
		initStatus: 'Kies een datum',
		isRTL: false
	};
	$jppr.datepick.setDefaults($jppr.datepick.regional['nl']);
})(jQuery);
