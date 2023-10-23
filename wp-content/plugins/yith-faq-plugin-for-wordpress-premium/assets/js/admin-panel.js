/**
 * Admin panel scripts
 *
 * @package YITH\FAQPluginForWordPress\Assets\JS
 */

jQuery(
	function ( $ ) {

		init_select_style();

		function init_select_style() {
			$( '.yfwp-select' ).select2(
				{
					minimumResultsForSearch: Infinity
				}
			);

			$( window ).trigger( 'resize' );

		}

		var shortcode_dialog = $( document ).find( '.yith-faq-shortcodes-list-popup-wrapper' ),
			popupForm        = shortcode_dialog.find( 'form' ),
			openPopup        = function ( action, id ) {
				shortcode_dialog = $( document ).find( '.yith-faq-shortcodes-list-popup-wrapper' );
				var title        = 'insert' === action ? yfwp_admin.title_new : yfwp_admin.title_edit,
					button       = 'insert' === action ? yfwp_admin.create_btn_text : yfwp_admin.save_btn_text;
				popupForm        = shortcode_dialog.find( 'form' );

				popupForm.find( 'input[name="action"' ).val( action );

				if ( 'edit' === action ) {
					popupForm.find( '#yfwp_shortcode_shortcode_type' ).prop( 'disabled', true );
					popupForm.find( 'tr.shortcode_type' ).hide();
					popupForm.find( 'input[name="shortcode_id"' ).val( id );
				}
				shortcode_dialog.dialog(
					{
						closeText  : '',
						title      : title.replace( /&#039;/g, "'" ),
						width      : 600,
						modal      : true,
						dialogClass: 'yith-plugin-ui yith-faq-shortcodes-list-popup',
						buttons    : [{
							'text' : button.replace( /&#039;/g, "'" ),
							'click': function ( e ) {
								window.onbeforeunload = null;

								var title       = $( '#yfwp_shortcode_shortcode_title' ),
									select_cats = $( '#yfwp_shortcode_faq_to_show' ),
									type        = $( '#yfwp_shortcode_shortcode_type' ),
									page_id     = $( '#yfwp_shortcode_page_id' ),
									categories  = $( '#yfwp_shortcode_categories' );

								title.parent().removeClass( 'has-error' ).find( 'small' ).remove();
								categories.parent().removeClass( 'has-error' ).find( 'small' ).remove();
								page_id.parent().removeClass( 'has-error' ).find( 'small' ).remove();

								if ( title.val() === '' ) {
									e.preventDefault();
									title.parent().addClass( 'has-error' ).append( '<small class="field-error ">' + yfwp_admin.errors.missing_field + '</small>' );
									return false;
								} else if ( select_cats.val() !== 'all' && categories.find( 'option' ).length === 0 ) {
									e.preventDefault();
									categories.parent().addClass( 'has-error' ).append( '<small class="field-error ">' + yfwp_admin.errors.missing_category + '</small>' );
									return false;
								} else if ( type.val() === 'summary' && page_id.val() === '' ) {
									e.preventDefault();
									page_id.parent().addClass( 'has-error' ).append( '<small class="field-error ">' + yfwp_admin.errors.missing_page + '</small>' );
									return false;
								} else {
									popupForm.submit();
								}
							},
							'class': 'yith-save-button'
						}],
						position   : {
							my       : 'center center',
							at       : 'center center',
							of       : "#wpwrap",
							collision: 'fit'
						}
					}
				);

				$( document.body )
					.trigger( 'wc-enhanced-select-init' )
					.trigger( 'yith-framework-enhanced-select-init' )
					.trigger( 'yith_fields_init' );

				$( '#yfwp_shortcode_shortcode_type' ).on(
					'change',
					function () {
						if ( 'faqs' === $( this ).val() ) {
							$( 'tr.summary' ).hide();
							$( 'tr.faqs' ).show( 500 );
							$( '#yfwp_shortcode_style' ).trigger( 'change' );
							$( '#yfwp_shortcode_show_pagination' ).trigger( 'change' );

						} else {
							$( 'tr.faqs' ).hide();
							$( 'tr.summary' ).show( 500 );
						}
					}
				).trigger( 'change' );

				$( '#yfwp_shortcode_show_icon' ).on(
					'change',
					function () {
						if ( $( this ).val() === 'off' || $( '#yfwp_shortcode_style' ).val() === 'list' ) {
							$( 'tr.icon_size' ).hide();
							$( 'tr.icon' ).hide();
						} else {
							$( 'tr.icon_size' ).show( 500 );
							$( 'tr.icon' ).show( 500 );
						}
					}
				).trigger( 'change' );

				$( '#yfwp_shortcode_style' ).on(
					'change',
					function () {
						if ( $( this ).val() === 'list' ) {
							$( 'tr.expand_faq' ).hide();
							$( 'tr.show_icon' ).hide();
							$( 'tr.icon_size' ).hide();
							$( 'tr.icon' ).hide();
						} else {
							$( 'tr.expand_faq' ).show( 500 );
							$( 'tr.show_icon' ).show( 500 );
							$( '#yfwp_shortcode_show_icon' ).trigger( 'change' );
						}
					}
				).trigger( 'change' );

				$( '#yfwp_shortcode_faq_to_show' ).on(
					'change',
					function () {
						if ( $( this ).val() === 'all' ) {
							$( 'tr.categories' ).hide();
						} else {
							$( 'tr.categories' ).show( 500 );
						}
					}
				).trigger( 'change' );

				$( '#yfwp_shortcode_show_pagination' ).on(
					'change',
					function () {
						if ( $( this ).is( ':checked' ) && $( '#yfwp_shortcode_shortcode_type' ).val() === 'faqs' ) {
							$( 'tr.page_size' ).show( 500 );
						} else {
							$( 'tr.page_size' ).hide();
						}
					}
				).trigger( 'change' );

				init_select_style();
			};

		function load_popup( obj, action, id ) {
			var container = $( '.yith-faq-shortcodes' );

			container.addClass( 'processing' );
			container.block(
				{
					message   : null,
					overlayCSS: {
						background: '#fff',
						opacity   : 0.6
					}
				}
			);

			$.ajax(
				{
					url    : obj.attr( 'href' ),
					success: function ( response ) {
						if ( $( response ).find( '.yith-faq-shortcodes-list-popup-wrapper' ).length > 0 ) {
							$( '.yith-faq-shortcodes-list-popup-wrapper' ).html( '' ).html( $( response ).find( '.yith-faq-shortcodes-list-popup-wrapper' ).html() );
							openPopup( action, id );
						}
						container.unblock();
					}
				}
			);
		}

		$( document ).on(
			'click',
			'.yith-add-button',
			function ( e ) {
				e.preventDefault();
				load_popup( $( this ), 'insert', 0 );
			}
		);

		$( document ).on(
			'click',
			'.column-actions a.edit',
			function ( e ) {
				e.preventDefault();
				load_popup( $( this ), 'edit', $( this ).data( 'shortcode_id' ) );
			}
		);

		$( '.yith-faq-shortcodes-notice' ).on(
			'click',
			'.notice-dismiss',
			function () {
				$( this ).parent().hide();
			}
		);

		history.pushState( null, '', pruned_url( location.href ) );
		var next_button  = $( '.next-page' ),
			prev_button  = $( '.prev-page' ),
			first_button = $( '.first-page' ),
			last_button  = $( '.last-page' );

		if ( next_button.length > 0 ) {
			next_button.attr( 'href', pruned_url( next_button.attr( 'href' ) ) );
		}
		if ( prev_button.length > 0 ) {
			prev_button.attr( 'href', pruned_url( prev_button.attr( 'href' ) ) );
		}
		if ( first_button.length > 0 ) {
			first_button.attr( 'href', pruned_url( first_button.attr( 'href' ) ) );
		}
		if ( last_button.length > 0 ) {
			last_button.attr( 'href', pruned_url( last_button.attr( 'href' ) ) );
		}

		function pruned_url( url ) {
			url = url.replace( /&?action=([^&]$|[^&]*)/i, "" );
			return url;
		}

		$( window ).on(
			'resize',
			function () {

				var popup   = $( '.yith-faq-shortcodes-list-popup' ),
					wHeight = $( window ).height(),
					pHeight = popup.height();

				if ( wHeight < pHeight ) {
					popup.css(
						{
							'top'              : 0,
							'-webkit-transform': 'none',
							'transform'        : 'none'
						}
					);
				} else {
					popup.css(
						{
							'top'              : '50%',
							'-webkit-transform': 'translateY(-50%)',
							'transform'        : 'translateY(-50%)'
						}
					);
				}

			}
		);

	}
);
