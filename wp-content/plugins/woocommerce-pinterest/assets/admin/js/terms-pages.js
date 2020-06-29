jQuery(function ($) {

	$('#woocommerce-pinterest-category-tags').selectWoo({
		width: 'auto',
		minimumInputLength: 2,
		placeholder: premmerceSettings.searchTagsTranslation,
		allowClear: true,
		ajax: {
			url: window.ajaxurl,
			delay: 200,
			method: 'GET',
			dataType: 'json',
			data: function (params) {
				return {
					action: premmerceSettings.get_terms_action,
					q: params.term,
					_wpnonce: premmerceSettings.get_terms_nonce
				};
			},
			processResults: function (response) {
				return {
					results: $.map(response, function(term){
						return {
							text: term.name,
							id: term.term_id
						}
					})
				};
			}
		}
	});

	$(document).ajaxSuccess(function(data, xhr, options){
		var url = new URL(location.protocol + '//' + location.host + '?' + options.data);
		if ('add-tag' === url.searchParams.get("action")) {
			var $select = $('select#woocommerce-pinterest-category-tags');
			$select.val(null).trigger('change');
		}
	});
});
