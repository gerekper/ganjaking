jQuery( function ( $ ) {
	var type           = $( '#yith-wcbm-badge-type' ).data( 'type' ),
		tab_container  = $( ".tab-container" ),
		preview_badge  = $( '#preview-badge' ),
		btn_text       = $( '#btn-text' ),
		btn_image      = $( '#btn-image' ),
		image_url      = $( "#yith-wcbm-image-url" ),
		half_left      = $( ".half-left" ),
		half_right     = $( ".half-right" ),
		url_for_images = $( '#yith-wcbm-url-for-images' ).val(),
		flag           = 0,
		correct_height = function () {
			half_left.removeAttr( "style" );
			half_right.removeAttr( "style" );
			half_left.css( { 'min-height': '450px' } );
			if ( half_right.height() > half_left.height() ) {
				half_left.height( half_right.height() );
			} else {
				half_right.height( half_left.height() );
			}
		},
		preview_render = function () {
			if ( type !== 'image' ) {
				preview_badge.html( $( "#yith-wcbm-text" ).val() );
				preview_badge.css(
					{
						"color"           : $( "#yith-wcbm-txt-color" ).val(),
						"background-color": $( "#yith-wcbm-bg-color" ).val(),
						"width"           : $( "#yith-wcbm-width" ).val() + "px",
						"height"          : $( "#yith-wcbm-height" ).val() + "px",
						"line-height"     : $( "#yith-wcbm-height" ).val() + "px"
					}
				);

			} else {
				preview_badge.removeAttr( "style" );
				var image_badge = image_url.val();
				preview_badge.html( '<img src="' + url_for_images + image_badge + '" />' );
			}

			var position = $( "#yith-wcbm-position" ).val();
			switch ( position ) {
				case 'top-right':
					preview_badge.css( { 'top': '0', 'bottom': 'auto', 'left': 'auto', 'right': '0' } );
					break;
				case 'bottom-left':
					preview_badge.css( { 'top': 'auto', 'bottom': '0', 'left': '0', 'right': 'auto' } );
					break;
				case 'bottom-right':
					preview_badge.css( { 'top': 'auto', 'bottom': '0', 'left': 'auto', 'right': '0' } );
					break;
				default:
					preview_badge.css( { 'top': '0', 'bottom': 'auto', 'left': '0', 'right': 'auto' } );
			}
		};

	preview_render();
	$( "input.update-preview" ).on( "change paste keyup input focus", function () {
		preview_render();
	} );
	$( "select.update-preview" ).on( "change focus", function () {
		preview_render();
	} );
	$( '.yith-wcbm-color-picker' ).wpColorPicker( {
													  change: preview_render
												  } );
	$( '.iris-palette' ).on( 'click', function () {
		setTimeout( preview_render, 1 );
	} );

	/*** Button Control ***/
	var selected_class      = 'yith-wcbm-button-selected',
		input_type          = $( "#yith-wcbm-badge-type" ),
		button_select_image = $( ".yith-wcbm-select-image-btn" );

	btn_text.on( 'click', function () {
		input_type.val( 'text' );
		type = 'text';
		correct_height();
		preview_render();
	} );
	btn_image.on( 'click', function () {
		input_type.val( 'image' );
		type = 'image';
		correct_height();
		preview_render();
	} );

	tab_container.tabs();
	switch ( type ) {
		case 'image':
			tab_container.tabs( 'option', 'active', 1 );
			break;
		default:
	}

	button_select_image.on( 'click', function ( e ) {
		var badge_image_url = $( this ).data( 'badge_image_url' );
		image_url.val( badge_image_url );
		preview_render();
		button_select_image.removeClass( "yith-wcbm-select-image-btn-selected" );
		$( this ).addClass( "yith-wcbm-select-image-btn-selected" );
	} );

	//add selected css class to the selected image button
	button_select_image.each( function () {
		if ( $( this ).data( 'badge_image_url' ) === image_url.val() ) {
			$( this ).addClass( "yith-wcbm-select-image-btn-selected" );
			flag = 1;
		}
	} );
	if ( !flag && !image_url.val() ) {
		button_select_image.first().trigger( 'click' );
	}
	correct_height();

	// Hide the "view badge" button
	$( '#view-post-btn' ).hide();

} );