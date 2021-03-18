jQuery(document).ready(function ($) {

	function jq(selector) {
		return selector.replace(/[!"#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~]/g, "\\\\$&");
	}




	if ($('#_swatch_type').val() == 'pickers') {
		$('#_swatch_type_options').show();
	} else {
		$('#_swatch_type_options').hide();
	}

	$("#_swatch_type").change(function () {
		if ($(this).val() == 'pickers') {
			$('#_swatch_type_options').show();
		} else {
			$('#_swatch_type_options').hide();
		}
	});



	// add edit button functionality
	$('#swatches a.wcsap_edit_field').click(function (event) {
		event.preventDefault();

		var field = $(this).closest('.field');

		if (field.hasClass('form_open'))
		{
			field.removeClass('form_open');
		}
		else
		{
			field.addClass('form_open');
		}

		field.children('.field_form_mask').animate({
			'height': 'toggle'
		}, 500);

		return false;

	});

	$('.wcsap_field_meta').click(function (event) {
		event.preventDefault();
		event.stopPropagation();

		var field = $(this).closest('.field');

		if (field.hasClass('form_open'))
		{
			field.removeClass('form_open');
		}
		else
		{
			field.addClass('form_open');
		}

		field.children('.field_form_mask').animate({
			'height': 'toggle'
		}, 500);

		return false;
	});


	$('.attribute_swatch_preview').delegate('a', 'click', function (event) {
		event.preventDefault();

		var field = $(this).closest('.field');

		if (field.hasClass('form_open'))
		{
			field.removeClass('form_open');
		}
		else
		{
			field.addClass('form_open');
		}

		field.children('.field_form_mask').animate({
			'height': 'toggle'
		}, 500);

		return false;

	})


	$('.section-color-swatch').each(function () {

		var option_id = $(this).find('.woo-color').attr('id');
		var color = $(this).find('.woo-color').val();
		var preview_id = option_id + '_preview_swatch';
		var picker_id = option_id += '_picker';



		$('#' + picker_id).children('div').css('backgroundColor', color);
		$('#' + picker_id).ColorPicker({
			color: color,
			onShow: function (colpkr) {
				jQuery(colpkr).fadeIn(200);
				return false;
			},
			onHide: function (colpkr) {
				jQuery(colpkr).fadeOut(200);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#' + picker_id).children('div').css('backgroundColor', '#' + hex);
				$('#' + preview_id).css('backgroundColor', '#' + hex);
				$('#' + picker_id).next('input').attr('value', '#' + hex);

			}
		});
	});

	$('select._swatch_type_options_attribute_type').change(function () {
		var $parent = $(this).closest('table.wcsap_input');

		$parent.find('.field_option').hide();
		$parent.find('.field_option_' + $(this).val()).show();


		var $preview = $(this).closest('div.sub_field').find('.swatch-wrapper');

		if ($(this).val() == 'image') {
			$(this).closest('div.sub_field').find('td.attribute_swatch_type').html('Image');
			$('a.swatch', $preview).hide();
			$('a.image', $preview).show();

		} else {
			$(this).closest('div.sub_field').find('td.attribute_swatch_type').html('Color Swatch');
			$('a.image', $preview).hide();
			$('a.swatch', $preview).show();

		}
	});

	$('select._swatch_type_options_type').change(function () {
		var $parent = $(this).closest('tbody', 'table.wcsap_input');

		$parent.children('.field_option').hide();
		$parent.find('.field_option_' + $(this).val()).show();

	});




	var _custom_media = false;
	var _orig_send_attachment = wp.media.editor.send.attachment;

	$('.remove_swatch_image_button').click(function (e) {
		e.preventDefault();

		$(this).parent().find('.upload_image_id').val('');
		$(this).closest('.sub_field').find('img').attr('src', wc_swatches_params.placeholder_img_src);
	});

	var frame = null;

	$('.upload_swatch_image_button').click(function (e) {
		e.preventDefault();
		$button = $(this);
		
		// If the media frame already exists, reopen it.
		if (frame) {
			frame.open();
			return;
		}

		// Create a new media frame
		frame = wp.media({
			title: 'Select or Upload an Image',
			button: {
				text: 'Use this media'
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});


		// When an image is selected in the media frame...
		frame.on('select', function () {

			// Get media attachment details from the frame state
			var attachment = frame.state().get('selection').first().toJSON();

			var $input = $button.parent().find('.upload_image_id');

			$input.val(attachment.id);
			$button.closest('.sub_field').find('img').attr('src', attachment.url);
		});

		// Finally, open the modal on click
		frame.open();
		return false;
	});
});