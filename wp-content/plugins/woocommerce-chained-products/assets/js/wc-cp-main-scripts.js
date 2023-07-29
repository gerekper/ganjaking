/* phpcs:ignoreFile */
/**
 * Chained Products JavaScript
 *
 * @package woocommerce-chained-products/assets/js
 */

(function ($, ajaxVars) {
	"use strict";

	let saCPVariations = {
		parentSection: "",
		init: function () {

			this.parentSection = $( ".tab-included-products.chained_items_container" );
			let self           = saCPVariations;

			if ( $( 'input[name=variation_id]' ).length > 0 && parseInt( $( 'input[name=variation_id]' ).val() ) ) {
				self.showProducts();
			}

			$( 'input[name=variation_id]' ).on(
				'change',
				function() {
					self.parentSection.html('');
					$('a#wc_cp_load_more').hide();
					let val = $(this).val() || 0;
					if ( parseInt( val ) > 0 ) {
						$( 'span.price, div.single_variation p.stock' ).css( 'visibility', 'hidden' );
						$( "#wc_cp_page_number" ).val( '' );
						self.showProducts();
					}
				}
			);

			$( 'a#wc_cp_load_more' ).on(
				'click',
				function( e ) {
					e.preventDefault();
					self.showProducts();
				}
			);
		},
		triggerLoadMore(){
			this.showProducts();
		},
		async showProducts() {
			let self     = saCPVariations;
			this.showLoader();
			let data = await this.getProducts();
			self.renderProducts( data || '' )
		},
		renderProducts(html = '') {

			$( 'span.price, div.single_variation p.stock' ).css( 'visibility', 'visible' )
			this.showLoader( false )

			if( ! html || 0 === html.length ) {
				return;
			}

			( 0 < parseInt( this.parentSection.find( 'ul.products' ).length ) ) ? this.parentSection.find( 'ul.products' ).append( $(html).find( 'li' ) ) : this.parentSection.append( html )

			this.parentSection.find( 'ul.products li.product' ).removeClass('first').addClass('last')

			let loopPageNumber = parseInt( $(html).find('div.wccp-page-no').data('page-number') || 0 )
	
			if ( 0 < loopPageNumber ) {
				$( 'a#wc_cp_load_more' ).show();
				$( "#wc_cp_page_number" ).val( loopPageNumber )
			} else {
				$( 'a#wc_cp_load_more' ).hide();
			}
				
			let stock = parseInt( $(html).find('div.wccp-stock').data('stock') || 0 )

			if( isNaN( stock ) ) {
				return;
			}

			if ( 0 >= stock ) {
				$( "div.single_variation p.stock" ).text( $( 'div.single_variation p.stock' ).text() || '' );
			} else {
				if ( 0 < stock ) {
					$( "div.single_variation p.stock" ).text( "" );
				} else {
					$( ".variations_form" )
						.find( ".single_add_to_cart_button" )
						.removeClass( "wc-variation-selection-needed" )
						.addClass( "disabled wc-variation-is-unavailable" );
					$( ".variations_form" )
						.find( ".woocommerce-variation-add-to-cart" )
						.removeClass( "woocommerce-variation-add-to-cart-enabled" )
						.addClass( "woocommerce-variation-add-to-cart-disabled" );
					$( "div.single_variation p.stock" ).text( "" );
				}
				$( "input[name=quantity]" ).attr( "max", stock );
				$( "input[name=quantity]" ).attr( "data-max", stock );
			}
		},
		getProducts() {
			let form_data = this.getProductData()

			if (! form_data.variable_id) {
				return '';
			}

			return this.sendRequest( "get_chained_products_html_view", form_data )
		},
		sendRequest( action = "", form_value = {} ) {
			return new Promise(
				function (resolve ) {
					$.ajax(
						{
							url: ajaxVars.ajaxURL || '',
							type: "POST",
							data: { form_value, action, security: ajaxVars.security || '' },
							dataType: "html",
							success( result ) {
								resolve( result );
							},
							error( err ) {
								resolve( err );
							},
						}
					)
				}
			)
		},
		getProductData() {
			return {
				variable_id: parseInt( $( "input[name=variation_id]" ).val() || 0 ),
				price: $( "#show_price" ).val() || '',
				quantity: $( "#show_quantity" ).val() || '',
				style: $( "#select_style" ).val() || '',
				post_per_page : parseInt( ajaxVars.postPerPage || 5 ),
				page: parseInt( $( "#wc_cp_page_number" ).val() || 0),
			};
		},
		showLoader( show = true ){
			$( 'img#wc_cp_load_more' ).toggle( show )
		}
	};

	saCPVariations.init();
})( jQuery, cpVariationParams || {} );
