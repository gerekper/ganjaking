jQuery(function ($) {

	$(document).on('click', '[data-action-delete]', function (e) {
		if (!confirm($(this).data('confirmation-massage'))) {
			e.preventDefault();
		}
	});

});
