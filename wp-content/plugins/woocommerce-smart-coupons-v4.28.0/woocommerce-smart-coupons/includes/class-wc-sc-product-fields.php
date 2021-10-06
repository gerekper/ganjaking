<?php
/**
 * Smart Coupons fields in products
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.2.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Product_Fields' ) ) {

	/**
	 * Class for handling Smart Coupons' field in products
	 */
	class WC_SC_Product_Fields {

		/**
		 * Variable to hold instance of WC_SC_Product_Fields
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'woocommerce_product_options_coupons' ) );
			add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'woocommerce_product_options_coupons_variable' ), 11, 3 );

			add_action( 'woocommerce_process_product_meta', array( $this, 'woocommerce_process_product_meta_coupons' ), 10, 2 );
			add_action( 'woocommerce_save_product_variation', array( $this, 'woocommerce_process_product_meta_coupons_variable' ), 10, 2 );

		}

		/**
		 * Get single instance of WC_SC_Product_Fields
		 *
		 * @return WC_SC_Product_Fields Singleton object of WC_SC_Product_Fields
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Function to provide area for entering coupon code
		 */
		public function woocommerce_product_options_coupons() {
			global $post;

			$product_type = WC_Product_Factory::get_product_type( $post->ID );

			$is_send_coupons_on_renewals = get_post_meta( $post->ID, 'send_coupons_on_renewals', true );

			echo '<div class="options_group smart-coupons-field">';

			$all_discount_types = wc_get_coupon_types();

			?>
			<p class="form-field smart-coupon-search post_<?php echo esc_attr( $post->ID ); ?>">
				<label for="_coupon_title"><?php echo esc_html__( 'Coupons', 'woocommerce-smart-coupons' ); ?></label>
				<select class="wc-coupon-search" style="width: 50%;" multiple="multiple" id="_coupon_title_<?php echo esc_attr( $post->ID ); ?>" name="_coupon_title[<?php echo esc_attr( $post->ID ); ?>][]" data-placeholder="<?php echo esc_attr__( 'Search for a coupon&hellip;', 'woocommerce-smart-coupons' ); ?>" data-action="sc_json_search_coupons" data-security="<?php echo esc_attr( wp_create_nonce( 'search-coupons' ) ); ?>" >
					<?php
					$coupon_titles = get_post_meta( $post->ID, '_coupon_title', true );

					if ( ! empty( $coupon_titles ) ) {

						foreach ( $coupon_titles as $coupon_title ) {

							$coupon = new WC_Coupon( $coupon_title );

							$discount_type = $coupon->get_discount_type();

							if ( ! empty( $discount_type ) ) {
								/* translators: 1. Discount type 2. Discount Type Label */
								$discount_type = sprintf( __( ' ( %1$s: %2$s )', 'woocommerce-smart-coupons' ), __( 'Type', 'woocommerce-smart-coupons' ), $all_discount_types[ $discount_type ] );
							}

							echo '<option value="' . esc_attr( $coupon_title ) . '"' . selected( true, true, false ) . '>' . esc_html( $coupon_title . $discount_type ) . '</option>';
						}
					}
					?>
				</select>
				<?php
				echo wc_help_tip( esc_html__( 'These coupon/s will be given to customers who buy this product. The coupon code will be automatically sent to their email address on purchase.', 'woocommerce-smart-coupons' ) ); // phpcs:ignore
				?>
			</p>
			<p class="form-field send_coupons_on_renewals_field post_<?php echo esc_attr( $post->ID ); ?>" style="display: none;">
				<label for="send_coupons_on_renewals"><?php echo esc_html__( 'Send coupons on renewals?', 'woocommerce-smart-coupons' ); ?></label>
				<input type="checkbox" class="checkbox" style="" name="send_coupons_on_renewals[<?php echo esc_attr( $post->ID ); ?>]" id="send_coupons_on_renewals_<?php echo esc_attr( $post->ID ); ?>" value="yes" <?php checked( $is_send_coupons_on_renewals, 'yes' ); ?>/>
				<?php echo wc_help_tip( esc_html__( 'Check this box to send above coupons on each renewal order.', 'woocommerce-smart-coupons' ) ); // phpcs:ignore ?>
			</p>
			<?php $this->product_options_admin_js(); ?>
			<?php

			echo '</div>';

		}

		/**
		 * Coupon fields for variation
		 *
		 * @param int     $loop           Position in the loop.
		 * @param array   $variation_data Variation data.
		 * @param WP_Post $variation Post data.
		 */
		public function woocommerce_product_options_coupons_variable( $loop = 0, $variation_data = array(), $variation = null ) {

			$variation_id = $variation->ID;

			$all_discount_types = wc_get_coupon_types();

			$is_send_coupons_on_renewals = get_post_meta( $variation_id, 'send_coupons_on_renewals', true );

			?>
			<div class="smart_coupons_product_options_variable smart-coupons-field">
				<p class="form-field smart-coupon-search _coupon_title_field post_<?php echo esc_attr( $variation_id ); ?> form-row form-row-full">
					<label for="_coupon_title_<?php echo esc_attr( $loop ); ?>"><?php echo esc_html__( 'Coupons', 'woocommerce-smart-coupons' ); ?></label>
					<?php echo wc_help_tip( esc_html__( 'These coupon/s will be given to customers who buy this product. The coupon code will be automatically sent to their email address on purchase.', 'woocommerce-smart-coupons' ) ); // phpcs:ignore ?>
					<select class="wc-coupon-search" style="width: 100% !important;" multiple="multiple" id="_coupon_title_<?php echo esc_attr( $variation_id ); ?>" name="_coupon_title[<?php echo esc_attr( $variation_id ); ?>][<?php echo esc_attr( $loop ); ?>][]" data-placeholder="<?php echo esc_attr__( 'Search for a coupon&hellip;', 'woocommerce-smart-coupons' ); ?>" data-action="sc_json_search_coupons" data-security="<?php echo esc_attr( wp_create_nonce( 'search-coupons' ) ); ?>" >
						<?php
						$coupon_titles = get_post_meta( $variation_id, '_coupon_title', true );

						if ( ! empty( $coupon_titles ) ) {

							foreach ( $coupon_titles as $coupon_title ) {

								$coupon = new WC_Coupon( $coupon_title );

								$discount_type = $coupon->get_discount_type();

								if ( ! empty( $discount_type ) ) {
									/* translators: 1. Discount type 2. Discount Type Label */
									$discount_type = sprintf( __( ' ( %1$s: %2$s )', 'woocommerce-smart-coupons' ), __( 'Type', 'woocommerce-smart-coupons' ), $all_discount_types[ $discount_type ] );
								}

								echo '<option value="' . esc_attr( $coupon_title ) . '"' . selected( true, true, false ) . '>' . esc_html( $coupon_title . $discount_type ) . '</option>';
							}
						}
						?>
					</select>
				</p>
				<p class="form-field send_coupons_on_renewals_field post_<?php echo esc_attr( $variation_id ); ?> form-row form-row-full">
					<label class="tips" data-tip="<?php echo esc_attr__( 'Check this box to send above coupons on each renewal order.', 'woocommerce-smart-coupons' ); ?>">
						<?php echo esc_html__( 'Send coupons on renewals?', 'woocommerce-smart-coupons' ); ?>
						<input type="checkbox" class="checkbox" id="send_coupons_on_renewals_<?php echo esc_attr( $variation_id ); ?>" name="send_coupons_on_renewals[<?php echo esc_attr( $variation_id ); ?>][<?php echo esc_attr( $loop ); ?>]" style="margin: 0.1em 0.5em 0.1em 0 !important;" value="yes" <?php checked( $is_send_coupons_on_renewals, 'yes' ); ?>/>
					</label>
				</p>
				<?php $this->product_options_admin_js(); ?>
			</div>
			<?php
		}

		/**
		 * Product options admin JS
		 */
		public function product_options_admin_js() {
			?>
			<script type="text/javascript">

				jQuery(function(){

					var updateSendCouponOnRenewals = function(theElement) {
						let element;
						if (typeof theElement == "undefined") {
							element = jQuery('.smart-coupons-field');
						} else {
							element = [theElement];
						}
						jQuery.each(element, function(index, value){
							var prodType = jQuery('select#product-type').find('option:selected').val();
							<?php if ( $this->is_wc_gte_30() ) { ?>
								var associatedCouponCount = jQuery(value).find('.smart-coupon-search span.select2-selection ul.select2-selection__rendered li.select2-selection__choice').length;
							<?php } else { ?>
								var associatedCouponCount = jQuery(value).find('.smart-coupon-search ul.select2-choices li.select2-search-choice').length;
							<?php } ?>
							if ( ( prodType == 'subscription' || prodType == 'variable-subscription' || prodType == 'subscription_variation' ) && associatedCouponCount > 0 ) {
								jQuery(value).find('p.send_coupons_on_renewals_field').show();
							} else {
								jQuery(value).find('p.send_coupons_on_renewals_field').hide();
							}
						});
					};

					setTimeout(function(){updateSendCouponOnRenewals();}, 100);

					jQuery('select#product-type').on('change', function() {

						var productType = jQuery(this).find('option:selected').val();

						if ( productType == 'simple' || productType == 'variable' || productType == 'subscription' || productType == 'variable-subscription' || productType == 'subscription_variation' ) {
							jQuery('.wc-coupon-search').show();
						} else {
							jQuery('.wc-coupon-search').hide();
						}

						updateSendCouponOnRenewals();

					});

					jQuery('.wc-coupon-search').on('change', function(){
						let theElement = jQuery(this).parent().parent();
						setTimeout( function() {
							updateSendCouponOnRenewals(theElement);
						}, 10 );
					});

					if ( typeof getEnhancedSelectFormatString == "undefined" ) {
						function getEnhancedSelectFormatString() {
							var formatString = {
								formatMatches: function( matches ) {
									if ( 1 === matches ) {
										return smart_coupons_select_params.i18n_matches_1;
									}

									return smart_coupons_select_params.i18n_matches_n.replace( '%qty%', matches );
								},
								formatNoMatches: function() {
									return smart_coupons_select_params.i18n_no_matches;
								},
								formatAjaxError: function( jqXHR, textStatus, errorThrown ) {
									return smart_coupons_select_params.i18n_ajax_error;
								},
								formatInputTooShort: function( input, min ) {
									var number = min - input.length;

									if ( 1 === number ) {
										return smart_coupons_select_params.i18n_input_too_short_1
									}

									return smart_coupons_select_params.i18n_input_too_short_n.replace( '%qty%', number );
								},
								formatInputTooLong: function( input, max ) {
									var number = input.length - max;

									if ( 1 === number ) {
										return smart_coupons_select_params.i18n_input_too_long_1
									}

									return smart_coupons_select_params.i18n_input_too_long_n.replace( '%qty%', number );
								},
								formatSelectionTooBig: function( limit ) {
									if ( 1 === limit ) {
										return smart_coupons_select_params.i18n_selection_too_long_1;
									}

									return smart_coupons_select_params.i18n_selection_too_long_n.replace( '%qty%', number );
								},
								formatLoadMore: function( pageNumber ) {
									return smart_coupons_select_params.i18n_load_more;
								},
								formatSearching: function() {
									return smart_coupons_select_params.i18n_searching;
								}
							};

							return formatString;
						}
					}

					<?php if ( $this->is_wc_gte_30() ) { // Ajax product search box. ?>

						jQuery( '[class= "wc-coupon-search"]' ).filter( ':not(.enhanced)' ).each( function() {
							var select2_args = {
								allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
								placeholder: jQuery( this ).data( 'placeholder' ),
								minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : '3',
								escapeMarkup: function( m ) {
									return m;
								},
								ajax: {
									url:         '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
									dataType:    'json',
									quietMillis: 250,
									data: function( params, page ) {
										return {
											term:     params.term,
											action:   jQuery( this ).data( 'action' ) || 'sc_json_search_coupons',
											security: jQuery( this ).data( 'security' )
										};
									},
									processResults: function( data, page ) {
										var terms = [];
										if ( data ) {
											jQuery.each( data, function( id, text ) {
												terms.push( { id: id, text: text } );
											});
										}
										return { results: terms };
									},
									cache: true
								}
							};

							select2_args = jQuery.extend( select2_args, getEnhancedSelectFormatString() );

							jQuery( this ).select2( select2_args );

						});

					<?php } else { ?>

						jQuery( ':input.wc-coupon-search' ).filter( ':not(.enhanced)' ).each( function() {
							var select2_args = {
								allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
								placeholder: jQuery( this ).data( 'placeholder' ),
								minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : '3',
								escapeMarkup: function( m ) {
									return m;
								},
								ajax: {
									url:         decodeURIComponent( '<?php echo rawurlencode( admin_url( 'admin-ajax.php' ) ); ?>' ),
									dataType:    'json',
									quietMillis: 250,
									data: function( term, page ) {
										return {
											term:     term,
											action:   jQuery( this ).data( 'action' ) || 'sc_json_search_coupons',
											security: '<?php echo esc_attr( wp_create_nonce( 'search-coupons' ) ); ?>'
										};
									},
									results: function( data, page ) {
										var terms = [];
										if ( data ) {
											jQuery.each( data, function( id, text ) {
												terms.push( { id: id, text: text } );
											});
										}
										return { results: terms };
									},
									cache: true
								}
							};

							if ( jQuery( this ).data( 'multiple' ) === true ) {
								select2_args.multiple = true;
								select2_args.initSelection = function( element, callback ) {
									var data     = jQuery.parseJSON( element.attr( 'data-selected' ) );
									var selected = [];

									jQuery( element.val().split( "," ) ).each( function( i, val ) {
										selected.push( { id: val, text: data[ val ] } );
									});
									return callback( selected );
								};
								select2_args.formatSelection = function( data ) {
									return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
								};
							} else {
								select2_args.multiple = false;
								select2_args.initSelection = function( element, callback ) {
									var data = {id: element.val(), text: element.attr( 'data-selected' )};
									return callback( data );
								};
							}

							select2_args = jQuery.extend( select2_args, getEnhancedSelectFormatString() );

							jQuery( this ).select2( select2_args ).addClass( 'enhanced' );

						});

					<?php } ?>

				});

			</script>
			<?php
		}

		/**
		 * Function to save coupon code to database
		 *
		 * @param int    $post_id The post id.
		 * @param object $post The post object.
		 */
		public function woocommerce_process_product_meta_coupons( $post_id, $post ) {
			$post_coupon_title = ( ! empty( $_POST['_coupon_title'][ $post_id ] ) ) ? wc_clean( wp_unslash( $_POST['_coupon_title'][ $post_id ] ) ) : ''; // phpcs:ignore

			if ( ! empty( $post_coupon_title ) ) {
				if ( $this->is_wc_gte_30() ) {
					$coupon_titles = $post_coupon_title;
				} else {
					$coupon_titles = array_filter( array_map( 'trim', explode( ',', $post_coupon_title ) ) );
				}
				update_post_meta( $post_id, '_coupon_title', $coupon_titles );
			} else {
				update_post_meta( $post_id, '_coupon_title', array() );
			}

			if ( isset( $_POST['send_coupons_on_renewals'][ $post_id ] ) ) { // phpcs:ignore
				update_post_meta( $post_id, 'send_coupons_on_renewals', wc_clean( wp_unslash( $_POST['send_coupons_on_renewals'][ $post_id ] ) ) ); // phpcs:ignore
			} else {
				update_post_meta( $post_id, 'send_coupons_on_renewals', 'no' );
			}
		}

		/**
		 * Function for saving coupon details in product meta
		 *
		 * @param  integer $variation_id Variation ID.
		 * @param  integer $i Loop ID.
		 */
		public function woocommerce_process_product_meta_coupons_variable( $variation_id, $i ) {

			if ( empty( $variation_id ) ) {
				return;
			}

			$post_coupon_title = ( ! empty( $_POST['_coupon_title'][ $variation_id ][ $i ] ) ) ? wc_clean( wp_unslash( $_POST['_coupon_title'][ $variation_id ][ $i ] ) ) : ''; // phpcs:ignore

			if ( ! empty( $post_coupon_title ) ) {
				if ( $this->is_wc_gte_30() ) {
					$coupon_titles = $post_coupon_title;
				} else {
					$coupon_titles = array_filter( array_map( 'trim', explode( ',', $post_coupon_title ) ) );
				}
				update_post_meta( $variation_id, '_coupon_title', $coupon_titles );
			} else {
				update_post_meta( $variation_id, '_coupon_title', array() );
			}

			if ( isset( $_POST['send_coupons_on_renewals'][ $variation_id ][ $i ] ) ) { // phpcs:ignore
				update_post_meta( $variation_id, 'send_coupons_on_renewals', wc_clean( wp_unslash( $_POST['send_coupons_on_renewals'][ $variation_id ][ $i ] ) ) ); // phpcs:ignore
			} else {
				update_post_meta( $variation_id, 'send_coupons_on_renewals', 'no' );
			}

		}

	}

}

WC_SC_Product_Fields::get_instance();
