/**
 * URL shortener Admin Script Doc Comment
 *
 * @category Script
 * @package  Yith Custom Thank You Page for Woocommerce
 * @author    Armando Liccardo
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * @link http://www.yithemes.com
 */

jQuery(
	function ( $ ) {
		$( document ).ready(
			function () {
				'use strict'

				/* URL shortening select change event */
				$( '#ctpw_url_shortening' ).change(
					function () {

						let option = $( 'option:selected', this ).val(),
								google = $( '#ctpw_google_api_key' ),
								bitly  = $( '#ctpw_bitly_access_token' )

						switch ( option ) {
							/* select bitly service */
							case 'bitly':
								bitly.parent().parent().show()
								bitly.prop( 'required', true )
								google.parent().parent().hide()
								google.prop( 'required', false )
								break
							/* select google service */
							case 'google':
								bitly.parent().parent().hide()
								bitly.prop( 'required', false )
								google.parent().parent().show()
								google.prop( 'required', true )
								break

							default:
								bitly.parent().parent().hide()
								bitly.prop( 'required', false )
								google.parent().parent().hide()
								google.prop( 'required', false )

						}
					}
				).change()

				/* change event on Select Custom page or Custom Url */
				$( '#yith_ctpw_general_page_or_url' ).change(
					function () {
						let option    = $( 'option:selected', this ).val(),
								ctwp_page = $( '#yith_ctpw_general_page' ),
								ctpw_url  = $( '#yith_ctpw_general_page_url' )

						switch ( option ) {
							case 'ctpw_page':
								ctwp_page.parent().parent().show()
								ctpw_url.parent().parent().hide()
								break
							case 'ctpw_url':
								ctpw_url.parent().parent().show()
								ctwp_page.parent().parent().hide()
								break
							default:
								ctwp_page.parent().parent().hide()
								ctpw_url.parent().parent().hide()
						}

					}
				).change()

				/* edit\new category select change event */
				$( '#yith_ctpw_or_url_product_cat_thankyou_page' ).change(
					function () {
						let option    = $( 'option:selected', this ).val(),
								ctwp_page = $( '#yith_ctpw_product_cat_thankyou_page' ),
								ctpw_url  = $( '#yith_ctpw_url_product_cat_thankyou_page' )

						switch ( option ) {
							case 'ctpw_page':
								ctwp_page.parent().parent().show()
								ctpw_url.parent().parent().hide()
								break
							case 'ctpw_url':
								ctpw_url.parent().parent().show()
								ctwp_page.parent().parent().hide()
								break
							default:
								ctwp_page.parent().parent().hide()
								ctpw_url.parent().parent().hide()
						}
					}
				).change()

				// product page tab.
				$( '#ctpw_tab_data #yith_ctpw_product_thankyou_page_url' ).change(
					function () {
						let option    = $( 'option:selected', this ).val(),
								ctwp_page = $( '#yith_product_thankyou_page' ),
								ctpw_url  = $( '#yith_ctpw_product_thankyou_url' )

						switch ( option ) {
							case 'ctpw_page':
								ctwp_page.parent().show()
								ctpw_url.parent().hide()
								break
							case 'ctpw_url':
								ctpw_url.parent().show()
								ctwp_page.parent().hide()
								break
							default:
								ctwp_page.parent().hide()
								ctpw_url.parent().hide()
						}
					}
				).change()

				$( '#woocommerce-product-data' ).on(
					'woocommerce_variations_loaded',
					function () {
						// product variation options.
						$( '.woocommerce_variation .yith_ctpw .yith_ctpw_product_thankyou_page_url' ).change(
							function () {
								let item_class = '.' + $( this ).parent().attr( 'ctpw_item' )
								let option    = $( 'option:selected', this ).val(),
										ctwp_page = $( item_class + ' .yith_ctpw_product_thankyou_page' ),
										ctpw_url  = $( item_class + ' .yith_ctpw_product_thankyou_url' )

								switch ( option ) {
									case 'ctpw_page':
										ctwp_page.show()
										ctpw_url.hide()
										break
									case 'ctpw_url':
										ctpw_url.show()
										ctwp_page.hide()
										break
									default:
										ctwp_page.hide()
										ctpw_url.hide()
								}

							}
						).change()
					}
				)

				// manage field selection in Payment Gateways Tab.
				$( '#yith_ctpw_panel_payment_gateways .yith_ctpw_general_page_or_url' ).change(
					function () {
						var item_name   = $( this ).attr( 'ctpw_pg_id' ),
							option        = $( 'option:selected', this ).val(),
							ctwp_page_sel = '#yith_ctpw_page_for_' + item_name,
							ctpw_url_sel  = '#yith_ctpw_url_for_' + item_name,
							ctpw_page     = $( ctwp_page_sel ),
							ctpw_url      = $( ctpw_url_sel )

						switch ( option ) {
							case 'ctpw_page':
								ctpw_page.parent().parent().show()
								ctpw_url.parent().parent().hide()
								break
							case 'ctpw_url':
								ctpw_url.parent().parent().show()
								ctpw_page.parent().parent().hide()
								break
							default:
								ctpw_page.parent().parent().hide()
								ctpw_url.parent().parent().hide()
						}

					}
				).change()

				// add an edit link to the selected page in Main Admin settings, product category settings, single product settings.
				function get_page_edit_url ( ctpw_id, where ) {
					var data = {
						'action': 'yith_ctpw_get_edit_page_url',
						'ctpw_id': ctpw_id
					}

					if ( where === 'main_settings' ) {
						$( '.single_select_page .yith_ctpw_edit_page_url_link' ).remove()
						$.post(
							ajaxurl,
							data,
							function ( resp ) {
								if ( resp !== false ) {
									$( 'tr.single_select_page td .select2 .selection' ).append( '<a style="text-decoration: none; position: absolute; top: 8px; right: 40px;" class="yith_ctpw_edit_page_url_link" target="_blank" href="' + resp + '">Edit</a>' )
								}
							}
						)
					} else if ( where === 'product_cat_settings' ) {
						$( 'tr.yith_ctpw_cat_page .yith_ctpw_edit_page_url_link' ).remove()
						$.post(
							ajaxurl,
							data,
							function ( resp ) {
								if ( resp !== false ) {
									$( 'tr.yith_ctpw_cat_page td' ).append( '<a style="text-decoration: none;" class="yith_ctpw_edit_page_url_link" target="_blank" href="' + resp + '">Edit</a>' )
								}
							}
						)
					} else if ( where === 'single_p_tab_settings' ) {
						$( '#ctpw_tab_data .yith_ctpw_product_thankyou_page_id .yith_ctpw_edit_page_url_link' ).remove()
						$.post(
							ajaxurl,
							data,
							function ( resp ) {
								if ( resp !== false ) {
									$( '#ctpw_tab_data .yith_ctpw_product_thankyou_page_id' ).append( '<a style="text-decoration: none; margin-left: 4px;" class="yith_ctpw_edit_page_url_link" target="_blank" href="' + resp + '">Edit</a>' )
								}
							}
						)
					}

				}

				if ( $( 'select#yith_ctpw_general_page' ).val() !== '' ) {
					get_page_edit_url( $( 'select#yith_ctpw_general_page' ).val(), 'main_settings' )
				}

				$( 'select#yith_ctpw_general_page' ).change(
					function () {
						get_page_edit_url( $( this ).val(), 'main_settings' )
					}
				)

				if ( $( 'select#yith_ctpw_product_cat_thankyou_page' ).val() !== 0 ) {
					get_page_edit_url( $( 'select#yith_ctpw_product_cat_thankyou_page' ).val(), 'product_cat_settings' )
				}

				$( 'select#yith_ctpw_product_cat_thankyou_page' ).change(
					function () {
						get_page_edit_url( $( this ).val(), 'product_cat_settings' )
					}
				)

				if ( $( '#ctpw_tab_data .yith_ctpw_product_thankyou_page_id select' ).val() !== '0' ) {
					get_page_edit_url( $( '#ctpw_tab_data .yith_ctpw_product_thankyou_page_id select' ).val(), 'single_p_tab_settings' )
				}

				$( '#ctpw_tab_data .yith_ctpw_product_thankyou_page_id select' ).change(
					function () {
						get_page_edit_url( $( this ).val(), 'single_p_tab_settings' )
					}
				)

				// end add edit page link.

				/* Backend PDF Preview Button */
				var yctpw_pdf_preview_backend = $( '.yctpw_pdf_preview_backend' )
				// check if the PDF section is enabled; if not we remove the button.
				if ( $( '#yith_ctpw_enable_pdf' ).val() === 'no' && $( '#yith_ctpw_enable_pdf_as_shortcode' ).val() === 'no' ) {
					yctpw_pdf_preview_backend.remove()
				}

				yctpw_pdf_preview_backend.on(
					'click',
					function () {
						// check if we have a valid order id to test otherwise we cannot proceed.
						if ( yith_ctpw_ajax.order_id.trim() !== '' ) {

							console.log( 'YCTPW Starting Preview process' )
							// disable button to avoid click.
							$( this ).css( 'pointer-events', 'none' )
							// adding a loading gif.
							$( this ).after( '<img class="yith_ctpw_loading" src="' + yith_ctpw_ajax.loading_gif + '" />' )

							const show_order_header_table  = $( '#yith_ctpw_pdf_show_order_header' ).val()
							const show_order_details_table = $( '#yith_ctpw_pdf_show_order_details_table' ).val()
							const show_customer_details    = $( '#yith_ctpw_pdf_show_customer_details' ).val()
							const show_logo                = $( '#yith_ctpw_pdf_show_logo' ).val()
							const logo_image_url           = $( '#yith_ctpw_pdf_custom_logo' ).val()
							const footer_text              = $( '#yith_ctpw_pdf_footer_text' ).val()

							const preview_settings = {
								show_logo: show_logo,
								logo_image_url: logo_image_url,
								show_order_header_table: show_order_header_table,
								show_order_details_table: show_order_details_table,
								show_customer_details: show_customer_details,
								footer_text: footer_text
							}

							$.ajax(
								{
									type: 'POST',
									url: yith_ctpw_ajax.ajaxurl,
									data: {
										'action': 'yith_ctpw_get_pdf',
										'order_id': yith_ctpw_ajax.order_id,
										'backend_preview': true,
										'preview_settings': preview_settings
									},
									dataType: 'json',
									success: function ( result, textStatus, jqXHR ) {
										if ( result['status'] && result['file'] !== '' ) {
											console.log( 'YCTPW Preview status: ' + result['status'] )

											var html        = result['file'],
													max_height  = $( window ).height(),
													max_width   = $( window ).width(),
													height      = max_height - 200,
													width       = ( max_width - 200 > 600 ) ? 600 : max_width - 200,
													margin_left = width / 2,
													margin_top  = height / 2

											$( 'body' ).append( '<div id="yctpw_pdf_preview_overlay"></div><div id="yctpw_pdf_preview" style="width:' + width + 'px; height:' + height + 'px; top: 50%; left: 50%; margin-top: -' + margin_top + 'px; margin-left: -' + margin_left + 'px;"><div id="yctpw_pdf_preview_close">X</div><iframe id="yctpw_pdf_preview_frame"></iframe></div>' )

											// wrap the adding contents function in a timeout because on firefox he cannot apply after the append.
											setTimeout(
												function () {
													$( '#yctpw_pdf_preview_frame' ).contents().find( 'html' ).html( html )
												},
												1000
											)

											$( '#yctpw_pdf_preview_close' ).on(
												'click',
												function () {
													$( this ).parent().remove()
													$( '#yctpw_pdf_preview_overlay' ).remove()
												}
											)

											// create the pdf iframe to start the download.
											setTimeout(
												function () {
													// remove the loading gif.
													$( '.yith_ctpw_loading' ).remove()
													// enabling the button again.
													$( '.yctpw_pdf_preview_backend' ).css( 'pointer-events', 'all' )
												},
												500
											)
										}
									},
									error: function ( jqXHR ) {
										console.log( 'yith_ctpw_pdf_preview_error' )
									}
								}
							)
						} else {
							alert( yith_ctpw_ajax.no_valid_order_message )
						}
					}
				)

				/* Rules Tab */
				const rules_tab = $('.yctpw-rules');
				const item_type = rules_tab.find('select#item_type');
				item_type.on('change', function(){
					switch ( $(this).val() ) {
						case 'product':
							rules_tab.find('.product_id').show();
							rules_tab.find('.category_id').hide();
							rules_tab.find('.payment_method').hide();
							break;
						case 'product_category':
							rules_tab.find('.category_id').show();
							rules_tab.find('.product_id').hide();
							rules_tab.find('.payment_method').hide();
							break;
						case 'payment_method':
							rules_tab.find('.category_id').hide();
							rules_tab.find('.product_id').hide();
							rules_tab.find('.payment_method').show();
							break;

					}

				});
				item_type.trigger( 'change' );

				const page_url = rules_tab.find('#yith_ctpw_general_page_or_url');
				page_url.on( 'change', function() {
					switch ( $(this).val() ) {
						case 'ctpw_page':
							rules_tab.find('.yith_thankyou_page').show();
							rules_tab.find('.yith_thankyou_url').hide();

							break;
						case 'ctpw_url':
							rules_tab.find('.yith_thankyou_url').show();
							rules_tab.find('.yith_thankyou_page').hide();
							break;

					}
				});
				page_url.trigger('change');

				/* save button */
				const save_button = $('.yctpw-rules #yctpw_save_button');
				save_button.on('click', function(e) {
					var error = false;
					const item_type = $('.yctpw-rules #item_type').val();
					e.preventDefault();

					const product_id = $('.yctpw-rules #product_id');
					if ( 'product' === item_type && ( '' === product_id.val() || null === product_id.val() ) ) {
						error = true;
						product_id.next().addClass('yctpw_error');
					} else {
						product_id.next().removeClass('yctpw_error');
					}

					const category_id = $('.yctpw-rules #category_id');
					if ( 'category' === item_type && ( '' === category_id.val() || null === category_id.val() ) ) {
						error = true;
						category_id.next().addClass('yctpw_error');
					} else {
						category_id.next().removeClass('yctpw_error');
					}

					const rule_type = $('.yctpw-rules #yith_ctpw_general_page_or_url').val();
					const page = $('.yctpw-rules #yith_thankyou_page');

					if ( 'ctpw_page' === rule_type && ( 0 === page.val() || '0' === page.val() ) ) {
						page.addClass('yctpw_error');
						error = true;
					} else {
						page.removeClass('yctpw_error');
					}

					const url = $('.yctpw-rules #yith_thankyou_url');
					if ( 'ctpw_url' === rule_type && '' === url.val() ) {
						url.addClass('yctpw_error');
						error = true;
					} else {
						url.removeClass('yctpw_error');
					}

					if ( error === false ) {
						$('.yctpw-rules form#form').submit();
					}

				});
			}
		) // end document ready.
	}
)