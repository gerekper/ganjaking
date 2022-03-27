jQuery(function ($) {
	for (const fieldType in window.fieldSettings) {
		if (fieldType !== 'address') {
			continue;
		}

		if (window.fieldSettings.hasOwnProperty(fieldType)) {
			window.fieldSettings[fieldType] += ', .gpaa-field-setting';
		}
	}

	$(document).on('gform_load_field_settings', function (event, field, form) {
		$('#gpaa-enable').prop('checked', field['gpaaEnable'] == true);
	});
});
