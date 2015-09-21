function setupMassAction(o) {
	var massActionGrid = new varienGridMassaction(
		o.htmlId,
		o.gridJsObject,
		o.selectedJson,
		o.formFieldNameInternal,
		o.formFieldName
	);

	massActionGrid.setItems(o.itemsJson);
	massActionGrid.setGridIds(o.gridIdsJson);
	massActionGrid.setUseAjax(true);
	massActionGrid.setUseSelectAll(true);
	massActionGrid.errorText = o.errorText;

	massActionGrid.apply = function () {
		// $('loading-mask').show();
		var printer = new jQuery.fn.labelPrinterStandalone({
			labelTypes : ['8160-s'],
			printUrl   : o.printUrl,
			getData    : function () {

				if(varienStringArray.count(massActionGrid.checkedString) == 0) {
					alert(massActionGrid.errorText);
					return;
				}

				var item = massActionGrid.getSelectedItem();
				if(!item) {
					massActionGrid.validator.validate();
					return;
				}
				massActionGrid.currentItem = item;
				var fieldName = (item.field ? item.field : massActionGrid.formFieldName);
				var fieldsHtml = '';

				if(massActionGrid.currentItem.confirm && !window.confirm(massActionGrid.currentItem.confirm)) {
					return;
				}

				massActionGrid.formHiddens.update('');
				new Insertion.Bottom(massActionGrid.formHiddens, massActionGrid.fieldTemplate.evaluate({name: fieldName, value: massActionGrid.checkedString}));
				new Insertion.Bottom(massActionGrid.formHiddens, massActionGrid.fieldTemplate.evaluate({name: 'massaction_prepare_key', value: fieldName}));

				if(!massActionGrid.validator.validate()) {
					return;
				}

				return massActionGrid.form.serialize(false);
			},
			beforeShow : function () {
				return true;
			}
		});
		printer.create();
	}

	window[o.jsObjectName] = massActionGrid;
}