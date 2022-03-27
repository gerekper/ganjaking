( function( $ ) {
	// replace formula merge tags for List Fields with the count of rows
	gform.addFilter( 'gform_merge_tag_value_pre_calculation', function( value, match, isVisible, formulaField, formId ) {
		if (typeof match[3] === 'undefined' || match[3].indexOf(':count') === -1) {
			return value;
		}

		var inputId = match[1];
		var fieldId = parseInt(inputId,10);

		return $('#gform_' + formId + ' #field_' + formId + '_' + fieldId)
			.find('.gfield_list_group')
			.length;
	} );

	gform.addAction('gform_list_post_item_add', function( $clone, $container ) {
		var formId = $container.closest('.gform_wrapper').attr('id').replace('gform_wrapper_', '');

		if (!formId || typeof window.gf_global.gfcalc[formId] === 'undefined') {
			return;
		}

		var calcObject = window.gf_global.gfcalc[ formId ];

		calcObject.runCalcs(formId, calcObject.formulaFields)
	});

	gform.addAction('gform_list_post_item_delete', function( $container ) {
		var formId = $container.closest('.gform_wrapper').attr('id').replace('gform_wrapper_', '');

		if (!formId || typeof window.gf_global.gfcalc[formId] === 'undefined') {
			return;
		}

		var calcObject = window.gf_global.gfcalc[ formId ];

		calcObject.runCalcs(formId, calcObject.formulaFields)
	});

})(jQuery);
