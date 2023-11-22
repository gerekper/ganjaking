(function ($) {

	"use strict";

	var settings = premiumProAddonsSettings.settings;

	$('#pa-white-label input').on('change input', function (e) {

		e.preventDefault();

		handleSaveSettings();

	});

	function handleSaveSettings() {

		if ('valid' === settings.status) {

			$.ajax({
				url: settings.ajaxurl,
				type: 'POST',
				data: {
					action: 'pa_wht_lbl_save_settings',
					security: settings.nonce,
					fields: $('form#pa-white-label').serialize()
				},
				success: function (response) {

					console.log("saved");

				},
				error: function (err) {

					console.log(err);

					Swal.fire({
						type: 'error',
						title: 'Oops...',
						text: 'Something went wrong!'
					});
				}
			});

		} else {
			Swal.fire({
				type: 'warning',
				html: 'Please activate <a href="' + settings.adminurl + "/admin.php?page=premium-addons#tab=license" + '">Premium Addons License</a> to use white labeling option',
			});
		}

	}

})(jQuery);