jQuery(
	function ( $ ) {

		function render_icons( text ) {

			var regex = /{{(((\w+-?)*) ((\w+\d*-?)*))}}/gm;

			return text.replace( regex, '<i class="$1"></i>' );

		}

		$( window ).resize(
			function () {
				if ( 1440 > $( window ).width() ) {
					$( 'body' ).addClass( 'folded' );
				} else {
					$( 'body' ).removeClass( 'folded' );
				}
			}
		);

		var selected_icon_row           = $( 'tr.ywctm_selected_icon' ),
			selected_icon_size_row      = $( 'tr.ywctm_selected_icon_size' ),
			selected_icon_alignment_row = $( 'tr.ywctm_selected_icon_alignment' ),
			icon_color_row              = $( 'tr.ywctm_icon_color' ),
			custom_icon_row             = $( 'tr.ywctm_custom_icon' );

		$( document ).ready(
			function () {

				if ( 1440 > $( window ).width() ) {
					$( 'body' ).addClass( 'folded' );
				}

				$( '.ywctm-settings-wrapper .group-title' ).each(
					function () {
						$( this ).click(
							function () {

								var row = $( this ).parent();

								if ( row.hasClass( 'is-opened' ) ) {

									row.find( '.form-table' ).fadeTo(
										'slow',
										0,
										function () {
											row.removeClass( 'is-opened' );
										}
									);

								} else {
									row.addClass( 'is-opened' );
									row.find( '.form-table' ).fadeTo( 'slow', 1 );
								}

							}
						);
					}
				);

				var button = $( 'span.ywctm-custom-button' );

				$( document ).on(
					'tinymce-editor-setup',
					function ( event, editor ) {

						editor.on(
							'init',
							function ( e ) {
								e.target.editorCommands.execCommand( 'fontName', false, 'inherit' );
								e.target.editorCommands.execCommand( 'fontSize', false, '16px' );
							}
						);

						editor.on(
							'ExecCommand',
							function ( e ) {
								if ( '' !== e.target.getContent() ) {
									button.find( '.ywctm-inquiry-title' ).html( render_icons( e.target.getContent() ) );
								}
							}
						);

						editor.on(
							'SetContent',
							function ( e ) {
								if ( '' !== e.content ) {
									button.find( '.ywctm-inquiry-title' ).html( render_icons( e.content ) )
								}
							}
						);

						editor.on(
							'KeyUp',
							function ( e ) {
								button.find( '.ywctm-inquiry-title' ).html( render_icons( e.target.innerHTML ) )
							}
						);

					}
				);

				$( '#ywctm-button-label-metabox input, #ywctm-button-label-metabox select, #ywctm-button-label-metabox .yit-icons-manager-wrapper, #ywctm-button-label-metabox .ui-slider-horizontal, .wp-editor-area' ).on(
					'change keyup input keydown keypress click mousemove',
					function () {

						var current_element = $( this ),
							button_text     = button.find( '.ywctm-inquiry-title' ).html(),
							icon,
							icon_data,
							icon_class      = '',
							image           = '';

						if ( $( this ).hasClass( 'wp-editor-area' ) ) {
							if ( '' === $( this ).val() ) {
								if ( button_text.indexOf( 'btn-placeholder' ) === -1 ) {
									button_text = $( this ).val();
								}
							} else {
								button_text = $( this ).val();
							}
						}

						if ( '' !== current_element && 'ywctm_icon_type' === current_element.attr( 'id' ) ) {

							switch ( current_element.val() ) {
								case 'icon':
									icon_color_row.show( 500 );
									selected_icon_row.show( 500 );
									selected_icon_size_row.show( 500 );
									selected_icon_alignment_row.show( 500 );
									custom_icon_row.hide();
									break;

								case 'custom':
									icon_color_row.hide();
									selected_icon_row.hide();
									selected_icon_size_row.hide();
									selected_icon_alignment_row.show( 500 );
									custom_icon_row.show( 500 );
									break;

								default:
									icon_color_row.hide();
									selected_icon_row.hide();
									selected_icon_size_row.hide();
									selected_icon_alignment_row.hide();
									custom_icon_row.hide();

							}
						}

						var text_color              = $( '#ywctm_text_color_default' ).val(),
							hover_text_color        = $( '#ywctm_text_color_hover' ).val(),
							background_color        = $( '#ywctm_background_color_default' ).val(),
							hover_background_color  = $( '#ywctm_background_color_hover' ).val(),
							border_color            = $( '#ywctm_border_color_default' ).val(),
							hover_color_color       = $( '#ywctm_border_color_hover' ).val(),
							border_thickness        = $( '#ywctm_border_style_thickness' ).val(),
							border_radius           = $( '#ywctm_border_style_radius' ).val(),
							margin_top              = $( '#ywctm_margin_settings_top' ).val(),
							margin_right            = $( '#ywctm_margin_settings_right' ).val(),
							margin_bottom           = $( '#ywctm_margin_settings_bottom' ).val(),
							margin_left             = $( '#ywctm_margin_settings_left' ).val(),
							padding_top             = $( '#ywctm_padding_settings_top' ).val(),
							padding_right           = $( '#ywctm_padding_settings_right' ).val(),
							padding_bottom          = $( '#ywctm_padding_settings_bottom' ).val(),
							padding_left            = $( '#ywctm_padding_settings_left' ).val(),
							width_amount            = $( '#ywctm_width_settings_width' ).val(),
							width_unit              = $( '#ywctm_width_settings_unit' ).val(),
							selected_icon           = $( '#ywctm_selected_icon' ).val(),
							icon_color              = $( '#ywctm_icon_color_default' ).val(),
							hover_icon_color        = $( '#ywctm_icon_color_hover' ).val(),
							selected_icon_size      = $( '#ywctm_selected_icon_size' ).val(),
							selected_icon_alignment = $( '#ywctm_selected_icon_alignment' ).val(),
							custom_icon             = $( '#ywctm_custom_icon' ).val();

						switch ( $( '#ywctm_icon_type' ).val() ) {
							case 'icon':

								icon_data = selected_icon.split( ':' );

								switch ( icon_data[ 0 ] ) {
									case 'FontAwesome':
										icon_class = 'fa fa-' + icon_data[ 1 ];
										break;
									case 'Dashicons':
										icon_class = 'dashicons dashicons-' + icon_data[ 1 ];
										break;
									case 'retinaicon-font':
										icon_class = 'retinaicon-font ' + icon_data[ 1 ];
										break;
									default:
								}

								icon = '<span class="ywctm-icon-form ' + icon_class + '"></span>';
								break;
							case 'custom':
								image = ( '' !== custom_icon ? '<img src="' + custom_icon + '" />' : '' );
								icon  = '<span class="custom-icon">' + image + '</span>';
								break;
							default:
								icon = '';
						}

						button
							.css( 'color', text_color )
							.css( 'background-color', background_color )
							.css( 'border-color', border_color )
							.css( 'border-width', border_thickness )
							.css( 'border-radius', border_radius + 'px' )
							.css( 'margin-top', margin_top + 'px' )
							.css( 'margin-right', margin_right + 'px' )
							.css( 'margin-bottom', margin_bottom + 'px' )
							.css( 'margin-left', margin_left + 'px' )
							.css( 'padding-top', padding_top + 'px' )
							.css( 'padding-right', padding_right + 'px' )
							.css( 'padding-bottom', padding_bottom + 'px' )
							.css( 'padding-left', padding_left + 'px' )
							.css( 'width', width_amount + width_unit )
							.html( icon + '<span class="ywctm-inquiry-title">' + render_icons( button_text ) + '</span>' )
							.off( 'mouseenter mouseleave' )
							.hover(
								function () {

									$( this )
										.css( 'color', hover_text_color )
										.css( 'background-color', hover_background_color )
										.css( 'border-color', hover_color_color );

									$( this )
										.find( '.ywctm-icon-form' )
										.css( 'color', hover_icon_color );
								},
								function () {

									$( this )
										.css( 'color', text_color )
										.css( 'background-color', background_color )
										.css( 'border-color', border_color );

									$( this )
										.find( '.ywctm-icon-form' )
										.css( 'color', icon_color );
								}
							);

						button
							.find( '.ywctm-icon-form' )
							.css( 'color', icon_color )
							.css( 'font-size', selected_icon_size + 'px' )
							.css( 'align-self', selected_icon_alignment );

						button
							.find( '.custom-icon' )
							.css( 'align-self', selected_icon_alignment );

					}
				).change();

				$( window ).scroll(
					function () {

						var container = $( '.ywctm-button-preview' );

						if ( undefined === container || $( '.is-opened' ).length < 1 ) {
							return;
						}

						if ( container.offset().top < $( window ).scrollTop() + 32 ) {
							container.find( 'div.sticky' ).css(
								{
									position: 'fixed',
									top     : '3rem',
									width   : container.width()
								}
							);
						} else {
							container.find( 'div.sticky' ).css(
								{
									position: 'relative',
									top     : 'initial',
									width   : '100%'
								}
							);
						}
					}
				);

			}
		);

		$( 'input[name^=ywctm_button_url_type]' ).change(
			function () {
				if ( $( this ).is( ':checked' ) && 'custom' === $( this ).val() ) {
					$( '#ywctm_button_url' ).parent().parent().parent().show( 500 );
				} else {
					$( '#ywctm_button_url' ).parent().parent().parent().hide();
				}
			}
		).change();

	}
);
