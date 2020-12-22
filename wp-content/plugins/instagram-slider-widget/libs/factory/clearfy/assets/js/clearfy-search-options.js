(function($) {
	'use strict';

	$(document).ready(function() {
		if( '' !== window.location.hash && window.location.hash.indexOf('factory-control-') ) {
			let controlClass = window.location.hash.replace('#', ''),
				controlEl = $('.' + controlClass);

			if( controlEl.closest('.factory-div').length && !controlEl.is(':visible') ) {
				controlEl.closest('.factory-div').fadeIn();
			}

			$([document.documentElement, document.body]).animate({
				scrollTop: controlEl.offset().top - 150
			}, 500, function() {

				controlEl.find('.control-label').css({
					color: '#ff5722',
					fontWeight: 'bold'
				});

				history.pushState("", document.title, window.location.pathname
					+ window.location.search);
			});
		}

		if( undefined === window.wfactory_clearfy_search_options ) {
			throw new Error('Global var {wfactory_clearfy_search_options} is not declared.');
		}

		$('#wbcr-factory-clearfy-228__autocomplete').wfactory_clearfy_autocomplete({
			lookup: wfactory_clearfy_search_options,
			onSelect: function(suggestion) {
				$('#wbcr-factory-clearfy-228__autocomplete').prop("disabled", true);
				window.location.href = suggestion.data.page_url;
			}
		});
	});

})(jQuery);