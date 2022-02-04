jQuery( function( $ ) {

	var style = Object.assign(document.createElement('style'), {id: 'label-style'});
	document.head.appendChild(style);

	var label = document.querySelector('.product-label'),
		labelWrap = document.querySelector('.label-wrap'),
		typeField = document.querySelector('#_wapl_label_type'),
		textField = document.querySelector('#_wapl_label_text'),
		colorField = document.querySelector('#_wapl_label_style'),
		alignField = document.querySelector('#_wapl_label_align'),
		leftField = document.querySelector('#wapl-custom-position-left'),
		topField = document.querySelector('#wapl-custom-position-top'),
		metaBox = document.querySelector('.wapl-meta-box, #woocommerce_advanced_product_labels')

	// On type update
	function onTypeUpdate() {
		var types = wapl.types.map(i => 'type-' + i);
		var newType = typeField.value;

		metaBox.classList.remove(...types);
		metaBox.classList.add('type-' + newType);

		labelWrap.classList.remove(...wapl.types.map(i => 'wapl-' + i))
		labelWrap.classList.add('wapl-' + newType)

		if (newType === 'custom') {
			textField.value = '';
			colorField.value = 'red';
			onTextUpdate();
			onColorUpdate();
		}
	}
	typeField.addEventListener('change', onTypeUpdate)
	onTypeUpdate();

	function onColorUpdate(e) {
		label.classList.remove(...(wapl.colors.map(i => 'label-' + i)))
		label.classList.add('label-' + colorField.value);
		labelWrap.classList.remove(...(wapl.colors.map(i => 'label-' + i)))
		labelWrap.classList.add('label-' + colorField.value);
		metaBox.classList.remove(...(wapl.colors.map(i => 'label-color-' + i)))
		metaBox.classList.add('label-color-' + colorField.value);

		if (colorField.value !== 'custom') {
			label.style.backgroundColor = '';
			label.style.color = '';
		}

		if (colorField.value === 'custom' && 'rgba(0, 0, 0, 0)' == getStyle(label, 'background-color')) {
			label.style.backgroundColor = document.querySelector('#wapl-custom-background').value;
			label.style.color = document.querySelector('#wapl-custom-text').value;
			document.querySelector('#label-style').innerHTML = '.label-wrap .product-label:after { border-color: ' + $('#wapl-custom-background').val() + '; }';
		}
	}
	colorField.addEventListener('input', onColorUpdate)
	onColorUpdate();

	function getStyle(el, styleProp) {
		if (el.currentStyle)
			return el.currentStyle[styleProp];

		return document.defaultView.getComputedStyle(el, null).getPropertyValue(styleProp);
	}

	function onTextUpdate() {
		document.querySelector('.wapl-label-text').innerHTML = textField.value;
	}
	textField.addEventListener('input', onTextUpdate)

	function onAlignUpdate(e) {
		if (alignField.value === 'custom') {
			metaBox.classList.add('align-custom');
			label.style.top = topField.value + 'px';
			label.style.left = leftField.value + 'px';
			makeLabelDraggable();
		} else {
			metaBox.classList.remove('align-custom');
			label.style.top = null;
			label.style.left = null;
		}

		labelWrap.classList.remove('wapl-alignleft', 'wapl-alignright', 'wapl-aligncenter', 'wapl-aligncustom');
		labelWrap.classList.add('wapl-align' + alignField.value);
	}
	alignField.addEventListener('change', onAlignUpdate)
	onAlignUpdate();

	function updateCustomPosition() {
		var left = Math.round(label.style.left.replace('px', '')),
			top = Math.round(label.style.top.replace('px', ''));

		leftField.value = left;
		topField.value = top;
	}
	updateCustomPosition();

	function onCustomPositionUpdate() {
		setLabelPosition(leftField.value, topField.value);
	}
	leftField.addEventListener('change', onCustomPositionUpdate)
	topField.addEventListener('change', onCustomPositionUpdate)

	function setLabelPosition(left = null, top = null) {
		if (left !== null) {
			label.style.left = left + 'px';
			leftField.value = left;
		}

		if (top !== null) {
			label.style.top = top + 'px';
			topField.value = top;
		}
	}

	function makeLabelDraggable() {
		$('.product-label').draggable({
			create: updateCustomPosition,
			stop: updateCustomPosition,
			drag: updateCustomPosition
		});
	}

	// Keyboard controls label position
	document.body.addEventListener('keydown', (e) => {
		var activeElement = document.activeElement,
			inputs = ['input', 'select', 'button', 'textarea'],
			distance = e.shiftKey ? 10 : 1,
			left = label.style.left || label.offsetLeft,
			top = label.style.top || label.offsetTop;

		if (activeElement && inputs.indexOf(activeElement.tagName.toLowerCase()) !== -1) {
			return true;
		}

		// Reposition if one of the directional keys is pressed
		switch (e.keyCode || e.which) {
			case 37:
				setLabelPosition(parseInt(left, 10) - distance);
				break; // Left
			case 38:
				setLabelPosition(null, parseInt(top, 10) - distance);
				break; // Up
			case 39:
				setLabelPosition(parseInt(left, 10) + distance);
				break; // Right
			case 40:
				setLabelPosition(null, parseInt(top, 10) + distance);
				break; // Down
			default:
				return true; // Exit and bubble
		}

		e.preventDefault();
	})

	// Background color picker
	$('#wapl-custom-background').wpColorPicker({
// 		palettes: ['#D9534F', '#3498db', '#39A539', '#ffe312', '#ffA608', '#999', '#444', '#fff'],
		color: '#D9534F',
		palettes: false,
		change: function( event, ui ) {
			label.style.backgroundColor = ui.color.toString();
			document.querySelector('#label-style').innerHTML = '.label-wrap .product-label:after { border-color: ' + ui.color.toString() + '; }';
		},
	});
	$('#wapl-custom-text').wpColorPicker({
// 		palettes: ['#D9534F', '#3498db', '#39A539', '#ffe312', '#ffA608', '#999', '#444', '#fff'],
		color: '#fff',
		palettes: false,
		change: function( event, ui ) {
			label.style.color = ui.color.toString();
		},
	});



	// Uploading files
	var file_frame,
		wp_media_post_id = wp.media.model.settings.post.id, // Store the old id
		set_to_post_id = 0;

	$('#upload_image_button').on('click', function (event) {

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if (file_frame) {
			file_frame.uploader.uploader.param('post_id', set_to_post_id); // Set the post ID to what we want
			file_frame.open(); // Open frame
			return;
		} else {
			// Set the wp.media post id so the uploader grabs the ID we want when initialised
			wp.media.model.settings.post.id = set_to_post_id;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Select a image to upload',
			button: { text: 'Use this image' },
			multiple: false	// Set to true to allow multiple files to be selected
		});

		// When an image is selected, run a callback.
		file_frame.on('select', function () {
			// We set multiple to false so only get one image from the uploader
			var attachment = file_frame.state().get('selection').first().toJSON();

			// Do something with attachment.id and/or attachment.url here
			label.style.backgroundImage = 'url("' + attachment.url + '")';
			label.style.width = attachment.width + 'px';
			label.style.height = attachment.height + 'px';
			$('#custom-image-url').attr('value', attachment.url);
			$('#wapl-custom-image').val(attachment.id);

			makeLabelDraggable();

			// Restore the main post ID
			wp.media.model.settings.post.id = wp_media_post_id;
		});

		// Finally, open the modal
		file_frame.open();
	});

	// Restore the main ID when the add media button is pressed
	$('a.add_media').on('click', function () {
		wp.media.model.settings.post.id = wp_media_post_id;
	});

});
