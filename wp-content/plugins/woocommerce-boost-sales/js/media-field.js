'use strict';
jQuery(document).ready(function ($) {
	/**
	 * Media Uploader
	 * Dependencies     : jquery, wp media uploader
	 * Feature added by : Smartik - http://smartik.ws/
	 * Date             : 05.28.2013
	 */
	function optionsframework_add_file(event, selector) {

		var upload = $(".uploaded-file"), frame;
		var $el = $(this);

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if (frame) {
			frame.open();
			return;
		}

		// Create the media frame.
		frame = wp.media({
			// Set the title of the modal.
			title : $el.data('choose'),
			// Customize the submit button.
			button: {
				// Set the text of the button.
				text : $el.data('update'),
				// Tell the button not to close the modal, since we're
				// going to refresh the page when the image is selected.
				close: false
			}
		});

		// When an image is selected, run a callback.
		frame.on('select', function () {
			// Grab the selected attachment.
			var attachment = frame.state().get('selection').first();
			frame.close();
			selector.find('.upload').val(attachment.attributes.id);

			if (attachment.attributes.type == 'image') {
				if (attachment.attributes.width < 150) {
					selector.find('.screenshot').empty().hide().append('<img style="max-width:100%" class="of-option-image" src="' + attachment.attributes.sizes.full.url + '">').slideDown('fast');
				} else {
					selector.find('.screenshot').empty().hide().append('<img style="max-width:100%" class="of-option-image" src="' + attachment.attributes.sizes.thumbnail.url + '">').slideDown('fast');
				}
			}
			selector.find('.media_upload_button').unbind();
			selector.find('.remove-image').show().removeClass('hide');//show "Remove" button
			selector.find('.of-background-properties').slideDown();
			optionsframework_file_bindings();
		});

		// Finally, open the modal.
		frame.open();
	}

	function optionsframework_remove_file(selector) {
		selector.find('.remove-image').hide().addClass('hide');//hide "Remove" button
		selector.find('.upload').val('');
		selector.find('.screenshot').slideUp();
		selector.find('.remove-file').unbind();
		// We don't display the upload button if .upload-notice is present
		// This means the user doesn't have the WordPress 3.5 Media Library Support
		if ($('.section-upload .upload-notice').length > 0) {
			$('.media_upload_button').remove();
		}
		optionsframework_file_bindings();
	}

	function optionsframework_file_bindings() {
		$('.remove-image, .remove-file').on('click', function () {
			optionsframework_remove_file($(this).parents('.vi-media-field'));
		});

		$('.media_upload_button').unbind('click').click(function (event) {
			optionsframework_add_file(event, $(this).parents('.vi-media-field'));
		});
	}

	optionsframework_file_bindings();
});