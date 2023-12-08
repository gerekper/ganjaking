<?php
/**
 * Smart Coupons Display
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     4.0.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Display_Coupons' ) ) {

	/**
	 * Class for handling display feature for coupons
	 */
	class WC_SC_Display_Coupons {

		/**
		 * Variable to hold instance of WC_SC_Display_Coupons
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Custom endpoint name.
		 *
		 * @var string
		 */
		public static $endpoint;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'wp_ajax_sc_get_available_coupons', array( $this, 'get_available_coupons_html' ) );
			add_action( 'wp_ajax_nopriv_sc_get_available_coupons', array( $this, 'get_available_coupons_html' ) );

			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'show_attached_gift_certificates' ) );
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'override_add_to_cart_button' ), 10, 3 );

			add_action( 'woocommerce_after_cart_table', array( $this, 'show_available_coupons_after_cart_table' ) );
			add_action( 'woocommerce_before_checkout_form', array( $this, 'show_available_coupons_before_checkout_form' ), 11 );

			add_filter( 'wc_sc_show_as_valid', array( $this, 'show_as_valid' ), 10, 2 );

			add_action( 'wp_loaded', array( $this, 'myaccount_display_coupons' ) );

			add_action( 'add_meta_boxes', array( $this, 'add_generated_coupon_details' ) );
			add_action( 'woocommerce_view_order', array( $this, 'generated_coupon_details_view_order' ) );
			add_action( 'woocommerce_email_after_order_table', array( $this, 'generated_coupon_details_after_order_table' ), 10, 3 );

			add_action( 'wp_footer', array( $this, 'frontend_styles_and_scripts' ) );

			add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'woocommerce_update_order_review_fragments' ) );

			add_action( 'init', array( $this, 'endpoint_hooks' ) );

			add_filter( 'woocommerce_available_variation', array( $this, 'modify_available_variation' ), 10, 3 );

			add_action( 'wp_loaded', array( $this, 'maybe_enable_store_notice_for_coupon' ) );

			add_action( 'wc_ajax_wc_sc_get_attached_coupons', array( $this, 'ajax_get_attached_coupons' ) );

			add_filter( 'safecss_filter_attr_allow_css', array( $this, 'check_safecss' ), 20, 2 );

			add_filter( 'woocommerce_demo_store', array( $this, 'maybe_change_store_notice' ), 10, 2 );

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
		 * Get single instance of WC_SC_Display_Coupons
		 *
		 * @return WC_SC_Display_Coupons Singleton object of WC_SC_Display_Coupons
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Function to show available coupons on Cart & Checkout page
		 *
		 * @param string $available_coupons_heading Existing heading.
		 * @param string $page The page.
		 * @param array  $args Additional arguments.
		 */
		public function show_available_coupons( $available_coupons_heading = '', $page = 'checkout', $args = array() ) {

			$max_coupon_to_show = get_option( 'wc_sc_setting_max_coupon_to_show', 5 );
			$max_coupon_to_show = apply_filters( 'wc_sc_max_coupon_to_show', $max_coupon_to_show, array( 'source' => $this ) );
			$max_coupon_to_show = absint( $max_coupon_to_show );

			$coupons = array();
			if ( $max_coupon_to_show > 0 ) {
				$coupons = $this->sc_get_available_coupons_list( array(), $args );
			}

			if ( empty( $coupons ) ) {
				do_action(
					'wc_sc_no_available_coupons',
					array(
						'available_coupons_heading' => $available_coupons_heading,
						'page'                      => $page,
					)
				);
				return false;
			}

			if ( ! wp_style_is( 'smart-coupon' ) ) {
				wp_enqueue_style( 'smart-coupon' );
			}

			$design           = get_option( 'wc_sc_setting_coupon_design', 'basic' );
			$background_color = get_option( 'wc_sc_setting_coupon_background_color', '#39cccc' );
			$foreground_color = get_option( 'wc_sc_setting_coupon_foreground_color', '#30050b' );
			$third_color      = get_option( 'wc_sc_setting_coupon_third_color', '#39cccc' );

			$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );

			$valid_designs = $this->get_valid_coupon_designs();

			if ( ! in_array( $design, $valid_designs, true ) ) {
				$design = 'basic';
			}

			?>
			<div id="coupons_list" style="display: none;">
				<?php
				if ( 'custom-design' !== $design ) {
					if ( ! wp_style_is( 'smart-coupon-designs' ) ) {
						wp_enqueue_style( 'smart-coupon-designs' );
					}
					?>
						<style type="text/css">
							:root {
								--sc-color1: <?php echo esc_html( $background_color ); ?>;
								--sc-color2: <?php echo esc_html( $foreground_color ); ?>;
								--sc-color3: <?php echo esc_html( $third_color ); ?>;
							}
						</style>
						<?php
				}
				?>
				<h3><?php echo esc_html__( stripslashes( $available_coupons_heading ), 'woocommerce-smart-coupons' ); // phpcs:ignore ?></h3>
					<div id="sc-cc">
						<div id="all_coupon_container" class="sc-coupons-list">
				<?php

				$coupons_applied = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_applied_coupons' ) ) ) ? WC()->cart->get_applied_coupons() : array();

				foreach ( $coupons as $code ) {

					if ( in_array( strtolower( $code->post_title ), array_map( 'strtolower', $coupons_applied ), true ) ) {
						continue;
					}

					$coupon_id = wc_get_coupon_id_by_code( $code->post_title );

					if ( empty( $coupon_id ) ) {
						continue;
					}

					if ( $max_coupon_to_show <= 0 ) {
						break;
					}

					$coupon = new WC_Coupon( $code->post_title );

					if ( 'woocommerce_before_my_account' !== current_filter() && ! $this->is_valid( $coupon ) ) {

						// Filter to allow third party developers to show coupons which are invalid due to cart requirements like minimum order total or products.
						$wc_sc_force_show_coupon = apply_filters( 'wc_sc_force_show_invalid_coupon', false, array( 'coupon' => $coupon ) );
						if ( false === $wc_sc_force_show_coupon ) {
							continue;
						}
					}

					if ( $this->is_wc_gte_30() ) {
						if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
							continue;
						}
						$coupon_id = $coupon->get_id();
						if ( empty( $coupon_id ) ) {
							continue;
						}
						$is_free_shipping = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
						$discount_type    = $coupon->get_discount_type();
						$expiry_date      = $coupon->get_date_expires();
						$coupon_code      = $coupon->get_code();
					} else {
						$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
						$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
						$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						$expiry_date      = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
						$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
					}

					$coupon_amount = $this->get_amount( $coupon, true );

					$is_show_zero_amount_coupon = true;

					if ( ( empty( $coupon_amount ) ) && ( ( ! empty( $discount_type ) && ! in_array( $discount_type, array( 'free_gift', 'smart_coupon' ), true ) ) || ( 'yes' !== $is_free_shipping ) ) ) {
						if ( 'yes' !== $is_free_shipping ) {
							$is_show_zero_amount_coupon = false;
						}
					}

					$is_show_zero_amount_coupon = apply_filters( 'show_zero_amount_coupon', $is_show_zero_amount_coupon, array( 'coupon' => $coupon ) );

					if ( false === $is_show_zero_amount_coupon ) {
						continue;
					}

					if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
						$expiry_date = ( is_callable( array( $expiry_date, 'getTimestamp' ) ) ) ? $expiry_date->getTimestamp() : null;
					} elseif ( ! is_int( $expiry_date ) ) {
						$expiry_date = $this->strtotime( $expiry_date );
					}

					if ( ! empty( $expiry_date ) && is_int( $expiry_date ) ) {
						$expiry_time = ( $this->is_callable( $coupon, 'get_meta' ) ) ? (int) $coupon->get_meta( 'wc_sc_expiry_time' ) : (int) get_post_meta( $coupon_id, 'wc_sc_expiry_time', true );
						if ( ! empty( $expiry_time ) ) {
							$expiry_date += $expiry_time; // Adding expiry time to expiry date.
						}
					}

					if ( empty( $discount_type ) || ( ! empty( $expiry_date ) && time() > $expiry_date ) ) {
						continue;
					}

					$coupon_post = get_post( $coupon_id );

					$coupon_data = $this->get_coupon_meta_data( $coupon );

					$coupon_type = ( ! empty( $coupon_data['coupon_type'] ) ) ? $coupon_data['coupon_type'] : '';

					if ( 'yes' === $is_free_shipping ) {
						if ( ! empty( $coupon_type ) ) {
							$coupon_type .= __( ' & ', 'woocommerce-smart-coupons' );
						}
						$coupon_type .= __( 'Free Shipping', 'woocommerce-smart-coupons' );
					}

					$coupon_description = '';
					if ( ! empty( $coupon_post->post_excerpt ) && 'yes' === $show_coupon_description ) {
						$coupon_description = $coupon_post->post_excerpt;
					}

					$is_percent = $this->is_percent_coupon( array( 'coupon_object' => $coupon ) );

					$args = array(
						'coupon_object'      => $coupon,
						'coupon_amount'      => $coupon_amount,
						'amount_symbol'      => ( true === $is_percent ) ? '%' : get_woocommerce_currency_symbol(),
						'discount_type'      => wp_strip_all_tags( $coupon_type ),
						'coupon_description' => ( ! empty( $coupon_description ) ) ? $coupon_description : wp_strip_all_tags( $this->generate_coupon_description( array( 'coupon_object' => $coupon ) ) ),
						'coupon_code'        => $coupon_code,
						'coupon_expiry'      => ( ! empty( $expiry_date ) ) ? $this->get_expiration_format( $expiry_date ) : __( 'Never expires', 'woocommerce-smart-coupons' ),
						'thumbnail_src'      => $this->get_coupon_design_thumbnail_src(
							array(
								'design'        => $design,
								'coupon_object' => $coupon,
							)
						),
						'classes'            => 'apply_coupons_credits',
						'template_id'        => $design,
						'is_percent'         => $is_percent,
					);

					wc_get_template( 'coupon-design/' . $design . '.php', $args, '', plugin_dir_path( WC_SC_PLUGIN_FILE ) . 'templates/' );

					$max_coupon_to_show--;

				}

				?>
				</div>
				<?php

				if ( did_action( 'wc_smart_coupons_frontend_styles_and_scripts' ) <= 0 || ! defined( 'DOING_AJAX' ) || DOING_AJAX !== true ) {
					$this->frontend_styles_and_scripts( array( 'page' => $page ) );
				}
				?>
			</div></div>
			<?php

		}

		/**
		 * Get available coupon's HTML
		 */
		public function get_available_coupons_html() {
			check_ajax_referer( 'sc-get-available-coupons', 'security' );
			$this->show_available_coupons_before_checkout_form();
			die();
		}

		/**
		 * Function to show available coupons before checkout form
		 */
		public function show_available_coupons_before_checkout_form() {

			$smart_coupon_cart_page_text = get_option( 'smart_coupon_cart_page_text' );
			$smart_coupon_cart_page_text = ( ! empty( $smart_coupon_cart_page_text ) ) ? $smart_coupon_cart_page_text : __( 'Available Coupons (click on a coupon to use it)', 'woocommerce-smart-coupons' );
			$this->show_available_coupons( $smart_coupon_cart_page_text, 'checkout' );

		}

		/**
		 * Check if store credit is valid based on amount
		 *
		 * @param  boolean $is_valid Validity.
		 * @param  array   $args     Additional arguments.
		 * @return boolean           Validity.
		 */
		public function show_as_valid( $is_valid = false, $args = array() ) {

			$coupon = ( ! empty( $args['coupon_obj'] ) ) ? $args['coupon_obj'] : false;

			if ( empty( $coupon ) ) {
				return $is_valid;
			}

			if ( $this->is_wc_gte_30() ) {
				$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
			} else {
				$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
			}

			$coupon_amount = $this->get_amount( $coupon, true );

			if ( true === $is_valid && 'smart_coupon' === $discount_type && empty( $coupon_amount ) ) {
				return false;
			}

			return $is_valid;
		}

		/**
		 * Hooks for handling display of coupons on My Account page
		 */
		public function myaccount_display_coupons() {

			$is_show_on_my_account = get_option( 'woocommerce_smart_coupon_show_my_account', 'yes' );

			if ( 'yes' !== $is_show_on_my_account ) {
				return;
			}

			if ( $this->is_wc_gte_26() ) {
				add_filter( 'woocommerce_get_query_vars', array( $this, 'sc_add_query_vars' ) );
				// Change the My Account page title.
				add_filter( 'the_title', array( $this, 'sc_endpoint_title' ) );
				// Inserting our new tab/page into the My Account page.
				add_filter( 'woocommerce_account_menu_items', array( $this, 'sc_new_menu_items' ) );
				add_action( 'woocommerce_account_' . self::$endpoint . '_endpoint', array( $this, 'sc_endpoint_content' ) );
			} else {
				add_action( 'woocommerce_before_my_account', array( $this, 'show_smart_coupon_balance' ) );
				add_action( 'woocommerce_before_my_account', array( $this, 'generated_coupon_details_before_my_account' ) );
			}

		}

		/**
		 * Function to show gift certificates that are attached with the product
		 */
		public function show_attached_gift_certificates() {
			global $post;

			if ( empty( $post->ID ) ) {
				return;
			}

			$is_show_associated_coupons = get_option( 'smart_coupons_is_show_associated_coupons', 'no' );

			if ( 'yes' !== $is_show_associated_coupons ) {
				return;
			}

			add_action( 'wp_footer', array( $this, 'attached_coupons_styles_and_scripts' ) );

			?>
			<div class="clear"></div>
			<div class="gift-certificates" style="display: none;"></div>
			<?php

		}

		/**
		 * Attached coupons styles and scripts
		 */
		public function attached_coupons_styles_and_scripts() {
			global $post;
			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}
			$product_type = WC_Product_Factory::get_product_type( $post->ID );
			?>
			<script type="text/javascript">
				jQuery(function(){
					let couponDetails = {};
					let product_type = decodeURIComponent( '<?php echo rawurlencode( (string) $product_type ); ?>' );

					function reload_attached_coupons( product_id ) {
						if ( product_id != '' && product_id != undefined ) {
							jQuery('div.gift-certificates').empty().hide();

							if ( product_id in couponDetails ) {
								jQuery('div.gift-certificates').replaceWith( couponDetails[product_id] );
								return;
							}

							jQuery.ajax({
								url: '?wc-ajax=wc_sc_get_attached_coupons',
								type: 'POST',
								dataType: 'html',
								data: {
									product_id: product_id,
									security: decodeURIComponent( '<?php echo rawurlencode( wp_create_nonce( 'wc-sc-get-attached-coupons' ) ); ?>' )
								},
								success: function( response ) {
									if ( null !== response && typeof 'undefined' !== response && '' !== response ) {
										couponDetails[ product_id ] = response;
										jQuery('div.gift-certificates').replaceWith( response );
									}
								}
							});
						}
					}

					setTimeout(function(){
						let product_types = [ 'variable', 'variable-subscription', 'subscription_variation' ];
						if ( product_types.includes( product_type ) ) {
							var default_variation_id = jQuery('input[name=variation_id]').val();
							if ( default_variation_id != '' && default_variation_id != undefined ) {
								jQuery('input[name=variation_id]').val( default_variation_id ).trigger( 'change' );
							}
						} else {
							reload_attached_coupons( decodeURIComponent( '<?php echo rawurlencode( $post->ID ); ?>' ) );
						}
					}, 10);

					jQuery('input[name=variation_id]').on('change', function(){
						let variation_id = jQuery('input[name=variation_id]').val();
						reload_attached_coupons( variation_id );
					});

					jQuery('a.reset_variations').on('click', function(){
						jQuery('div.gift-certificates').hide();
					});
				});
			</script>
			<?php
		}

		/**
		 * Get attached coupon's details via AJAX
		 */
		public function ajax_get_attached_coupons() {

			check_ajax_referer( 'wc-sc-get-attached-coupons', 'security' );

			global $store_credit_label;

			$response = '';

			$post_product_id = ( ! empty( $_POST['product_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : 0; // phpcs:ignore

			if ( ! empty( $post_product_id ) ) {

				$_product = wc_get_product( $post_product_id );

				$coupon_titles = $this->get_coupon_titles( array( 'product_object' => $_product ) );

				if ( $this->is_wc_gte_30() ) {
					$product_type = ( is_object( $_product ) && is_callable( array( $_product, 'get_type' ) ) ) ? $_product->get_type() : '';
				} else {
					$product_type = ( ! empty( $_product->product_type ) ) ? $_product->product_type : '';
				}

				$sell_sc_at_less_price         = get_option( 'smart_coupons_sell_store_credit_at_less_price', 'no' );
				$generated_credit_includes_tax = $this->is_generated_store_credit_includes_tax();

				if ( 'yes' === $sell_sc_at_less_price ) {
					if ( is_a( $_product, 'WC_Product_Variable' ) ) {
						$price = ( is_object( $_product ) && is_callable( array( $_product, 'get_variation_regular_price' ) ) ) ? $_product->get_variation_regular_price( 'max' ) : 0;
					} else {
						$price = ( is_object( $_product ) && is_callable( array( $_product, 'get_regular_price' ) ) ) ? $_product->get_regular_price() : 0;
					}
				} else {
					if ( is_a( $_product, 'WC_Product_Variable' ) ) {
						$price = ( is_object( $_product ) && is_callable( array( $_product, 'get_variation_price' ) ) ) ? $_product->get_variation_price( 'max' ) : 0;
					} else {
						$price = ( is_object( $_product ) && is_callable( array( $_product, 'get_price' ) ) ) ? $_product->get_price() : 0;
					}
				}

				if ( $coupon_titles && count( $coupon_titles ) > 0 && ! empty( $price ) ) {

					$all_discount_types              = wc_get_coupon_types();
					$smart_coupons_product_page_text = get_option( 'smart_coupon_product_page_text' );
					$smart_coupons_product_page_text = ( ! empty( $smart_coupons_product_page_text ) ) ? $smart_coupons_product_page_text : __( 'You will get following coupon(s) when you buy this item:', 'woocommerce-smart-coupons' );

					$list_started = true;

					ob_start();

					foreach ( $coupon_titles as $coupon_title ) {

						$coupon = new WC_Coupon( $coupon_title );

						$is_callable_coupon_get_meta = $this->is_callable( $coupon, 'get_meta' );

						if ( $this->is_wc_gte_30() ) {
							if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
								continue;
							}
							$coupon_id = $coupon->get_id();
							if ( empty( $coupon_id ) ) {
								continue;
							}
							$discount_type               = $coupon->get_discount_type();
							$is_free_shipping            = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
							$product_ids                 = $coupon->get_product_ids();
							$excluded_product_ids        = $coupon->get_excluded_product_ids();
							$product_categories          = $coupon->get_product_categories();
							$excluded_product_categories = $coupon->get_excluded_product_categories();
						} else {
							$coupon_id                   = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
							$discount_type               = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
							$is_free_shipping            = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
							$product_ids                 = ( ! empty( $coupon->product_ids ) ) ? $coupon->product_ids : array();
							$excluded_product_ids        = ( ! empty( $coupon->exclude_product_ids ) ) ? $coupon->exclude_product_ids : array();
							$product_categories          = ( ! empty( $coupon->product_categories ) ) ? $coupon->product_categories : array();
							$excluded_product_categories = ( ! empty( $coupon->exclude_product_categories ) ) ? $coupon->exclude_product_categories : array();
						}

						$coupon_amount = $this->get_amount( $coupon, true );

						$is_pick_price_of_product = ( true === $is_callable_coupon_get_meta ) ? $coupon->get_meta( 'is_pick_price_of_product' ) : get_post_meta( $coupon_id, 'is_pick_price_of_product', true );

						if ( $list_started && ! empty( $discount_type ) ) {
							echo '<div class="gift-certificates">';
							echo '<br /><p>' . esc_html( wp_unslash( $smart_coupons_product_page_text ) ) . '';
							echo '<ul>';
							$list_started = false;
						}

						switch ( $discount_type ) {

							case 'smart_coupon':
								/* translators: %s: singular name for store credit */
								$credit_label = ! empty( $store_credit_label['singular'] ) ? sprintf( __( '%s of ', 'woocommerce-smart-coupons' ), esc_html( ucwords( $store_credit_label['singular'] ) ) ) : __( 'Store Credit of ', 'woocommerce-smart-coupons' );
								if ( 'yes' === $is_pick_price_of_product ) {
									$amount = ( $price > 0 ) ? $credit_label . wc_price( $price ) : '';
								} else {
									$amount = ( ! empty( $coupon_amount ) ) ? $credit_label . wc_price( $coupon_amount ) : '';
								}

								break;

							case 'fixed_cart':
								$amount = wc_price( $coupon_amount ) . esc_html__( ' discount on your entire purchase', 'woocommerce-smart-coupons' );
								break;

							case 'fixed_product':
								if ( ! empty( $product_ids ) || ! empty( $excluded_product_ids ) || ! empty( $product_categories ) || ! empty( $excluded_product_categories ) ) {
									$discount_on_text = esc_html__( 'some products', 'woocommerce-smart-coupons' );
								} else {
									$discount_on_text = esc_html__( 'all products', 'woocommerce-smart-coupons' );
								}
								$amount = wc_price( $coupon_amount ) . esc_html__( ' discount on ', 'woocommerce-smart-coupons' ) . $discount_on_text;
								break;

							case 'percent_product':
								if ( ! empty( $product_ids ) || ! empty( $excluded_product_ids ) || ! empty( $product_categories ) || ! empty( $excluded_product_categories ) ) {
									$discount_on_text = esc_html__( 'some products', 'woocommerce-smart-coupons' );
								} else {
									$discount_on_text = esc_html__( 'all products', 'woocommerce-smart-coupons' );
								}
								$amount = $coupon_amount . '%' . esc_html__( ' discount on ', 'woocommerce-smart-coupons' ) . $discount_on_text;
								break;

							case 'percent':
								if ( ! empty( $product_ids ) || ! empty( $excluded_product_ids ) || ! empty( $product_categories ) || ! empty( $excluded_product_categories ) ) {
									$discount_on_text = esc_html__( 'some products', 'woocommerce-smart-coupons' );
								} else {
									$discount_on_text = esc_html__( 'your entire purchase', 'woocommerce-smart-coupons' );
								}
								$max_discount_text = '';
								$max_discount      = $this->get_post_meta( $coupon_id, 'wc_sc_max_discount', true, true );
								if ( ! empty( $max_discount ) && is_numeric( $max_discount ) ) {
									/* translators: %s: Maximum coupon discount amount */
									$max_discount_text = sprintf( __( ' upto %s', 'woocommerce-smart-coupons' ), wc_price( $max_discount ) );
								}
								$amount = $coupon_amount . '%' . esc_html__( ' discount', 'woocommerce-smart-coupons' ) . $max_discount_text . esc_html__( ' on ', 'woocommerce-smart-coupons' ) . $discount_on_text;
								break;

							default:
								$default_coupon_type = ( ! empty( $all_discount_types[ $discount_type ] ) ) ? $all_discount_types[ $discount_type ] : ucwords( str_replace( array( '_', '-' ), ' ', $discount_type ) );
								$coupon_amount       = apply_filters( 'wc_sc_coupon_amount', $coupon_amount, $coupon );
								/* translators: 1. Discount type 2. Discount amount */
								$amount = sprintf( esc_html__( '%1$s coupon of %2$s', 'woocommerce-smart-coupons' ), $default_coupon_type, $coupon_amount );
								$amount = apply_filters( 'wc_sc_coupon_description', $amount, $coupon );
								break;

						}

						if ( 'yes' === $is_free_shipping && in_array( $discount_type, array( 'fixed_cart', 'fixed_product', 'percent_product', 'percent' ), true ) ) {
							/* translators: Add more detail to coupon description */
							$amount = sprintf( esc_html__( '%s Free Shipping', 'woocommerce-smart-coupons' ), ( ( ! empty( $coupon_amount ) ) ? $amount . esc_html__( ' &', 'woocommerce-smart-coupons' ) : '' ) );
						}

						if ( ! empty( $amount ) ) {
							// Allow third party developers to modify the text being shown for the linked coupons.
							$amount = apply_filters( 'wc_sc_linked_coupon_text', $amount, array( 'coupon' => $coupon ) );

							// Mostly escaped earlier hence not escaping because it might have some HTML code.
							echo '<li>' . $amount . '</li>'; // phpcs:ignore
						}
					}

					if ( ! $list_started ) {
						echo '</ul></p></div>';
					}

					$response = ob_get_clean();

				}
			}

			echo wp_kses_post( $response );

			die();
		}

		/**
		 * Function to allow CSS from this plugin
		 *
		 * @param boolean $allow_css Whether the CSS in the test string is considered safe.
		 * @param string  $css_test_string The CSS string to test.
		 * @return boolean
		 */
		public function check_safecss( $allow_css = false, $css_test_string = '' ) {
			$backtrace           = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );  // phpcs:ignore
			$backtrace_functions = wp_list_pluck( $backtrace, 'function' );
			$allowed_css         = array(
				'sc-color',
			);
			if ( false === $allow_css && in_array( 'sc_endpoint_content', $backtrace_functions, true ) ) {
				foreach ( $allowed_css as $css ) {
					if ( false !== strpos( $css_test_string, $css ) ) {
						$allow_css = true;
						break;
					}
				}
			}
			return $allow_css;
		}

		/**
		 * Replace Add to cart button with Select Option button for products which are created for purchasing credit, on shop page
		 *
		 * @param string           $html Original add to cart button HTML.
		 * @param mixed|WC_Product $product The product object.
		 * @param array            $args Additional arguments.
		 * @return string HTML of add to cart button
		 */
		public function override_add_to_cart_button( $html = '', $product = null, $args = array() ) {

			if ( is_null( $product ) || empty( $product ) || ! $product instanceof WC_Product ) {
				return $html;
			}
			$coupons = $this->get_coupon_titles( array( 'product_object' => $product ) );
			if ( empty( $coupons ) ) {
				return $html;
			}
			if ( $this->is_coupon_amount_pick_from_product_price( $coupons ) && ! ( $product->get_price() > 0 ) ) {

				$html = sprintf(
					'<a href="%s" class="%s">%s</a>',
					esc_url( $product->get_permalink() ),
					esc_attr( ! empty( $args['class'] ) ? $args['class'] : 'button' ),
					esc_html( get_option( 'sc_gift_certificate_shop_loop_button_text', __( 'Select options', 'woocommerce-smart-coupons' ) ) )
				);

			}
			return $html;
		}

		/**
		 * Function to show available coupons after cart table
		 */
		public function show_available_coupons_after_cart_table() {

			$smart_coupon_cart_page_text = get_option( 'smart_coupon_cart_page_text' );
			$smart_coupon_cart_page_text = ( ! empty( $smart_coupon_cart_page_text ) ) ? $smart_coupon_cart_page_text : __( 'Available Coupons (click on a coupon to use it)', 'woocommerce-smart-coupons' );
			$this->show_available_coupons( $smart_coupon_cart_page_text, 'cart' );

		}

		/**
		 * Function to display current balance associated with Gift Certificate
		 */
		public function show_smart_coupon_balance() {
			global $store_credit_label;

			$smart_coupon_myaccount_page_text = get_option( 'smart_coupon_myaccount_page_text' );

			/* translators: %s: plural name for store credit */
			$smart_coupons_myaccount_page_text = ( ! empty( $smart_coupon_myaccount_page_text ) ) ? $smart_coupon_myaccount_page_text : ( ! empty( $store_credit_label['plural'] ) ? sprintf( __( 'Available Coupons & %s', 'woocommerce-smart-coupons' ), ucwords( $store_credit_label['plural'] ) ) : __( 'Available Coupons & Store Credits', 'woocommerce-smart-coupons' ) );
			$this->show_available_coupons( $smart_coupons_myaccount_page_text, 'myaccount' );

		}

		/**
		 * Display generated coupon's details on My Account page
		 */
		public function generated_coupon_details_before_my_account() {
			$show_coupon_received_on_my_account = get_option( 'show_coupon_received_on_my_account', 'no' );

			if ( is_user_logged_in() && 'yes' === $show_coupon_received_on_my_account ) {
				$user_id = get_current_user_id();
				$this->get_generated_coupon_data( '', $user_id, true, true );
			}
		}

		/**
		 * Add new query var.
		 *
		 * @param array $vars The query vars.
		 * @return array
		 */
		public function sc_add_query_vars( $vars = array() ) {

			$vars[ self::$endpoint ] = self::$endpoint;
			return $vars;
		}

		/**
		 * Set endpoint title.
		 *
		 * @param string $title The title of coupon page.
		 * @return string
		 */
		public function sc_endpoint_title( $title ) {
			global $wp_query;

			$is_endpoint = isset( $wp_query->query_vars[ self::$endpoint ] );

			if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
				// New page title.
				$title = __( 'Coupons', 'woocommerce-smart-coupons' );
				remove_filter( 'the_title', array( $this, 'sc_endpoint_title' ) );
			}

			return $title;
		}

		/**
		 * Insert the new endpoint into the My Account menu.
		 *
		 * @param array $items Existing menu items.
		 * @return array
		 */
		public function sc_new_menu_items( $items ) {

			// Remove the menu items.
			if ( isset( $items['edit-address'] ) ) {
				$edit_address = $items['edit-address'];
				unset( $items['edit-address'] );
			}

			if ( isset( $items['payment-methods'] ) ) {
				$payment_methods = $items['payment-methods'];
				unset( $items['payment-methods'] );
			}

			if ( isset( $items['edit-account'] ) ) {
				$edit_account = $items['edit-account'];
				unset( $items['edit-account'] );
			}

			if ( isset( $items['customer-logout'] ) ) {
				$logout = $items['customer-logout'];
				unset( $items['customer-logout'] );
			}

			// Insert our custom endpoint.
			$items[ self::$endpoint ] = __( 'Coupons', 'woocommerce-smart-coupons' );

			// Insert back the items.
			if ( ! empty( $edit_address ) ) {
				$items['edit-address'] = $edit_address;
			}
			if ( ! empty( $payment_methods ) ) {
				$items['payment-methods'] = $payment_methods;
			}
			if ( ! empty( $edit_account ) ) {
				$items['edit-account'] = $edit_account;
			}
			if ( ! empty( $logout ) ) {
				$items['customer-logout'] = $logout;
			}

			return $items;
		}

		/**
		 * Get coupon HTML
		 *
		 * @param  array $coupon_data the coupon data.
		 * @return string Coupon's HTML
		 */
		public function get_coupon_html( $coupon_data = array() ) {

			ob_start();

			$design                  = ( ! empty( $coupon_data['design'] ) ) ? $coupon_data['design'] : null;
			$coupon                  = ( ! empty( $coupon_data['coupon_object'] ) ) ? $coupon_data['coupon_object'] : null;
			$is_free_shipping        = ( ! empty( $coupon_data['is_free_shipping'] ) ) ? $coupon_data['is_free_shipping'] : null;
			$show_coupon_description = ( ! empty( $coupon_data['show_coupon_description'] ) ) ? $coupon_data['show_coupon_description'] : null;
			$coupon_description      = ( ! empty( $coupon_data['coupon_description'] ) ) ? $coupon_data['coupon_description'] : null;

			$coupon_amount = $this->get_amount( $coupon, true );

			$coupon_type = ( ! empty( $coupon_data['coupon_type'] ) ) ? $coupon_data['coupon_type'] : '';

			if ( 'yes' === $is_free_shipping ) {
				if ( ! empty( $coupon_type ) ) {
					$coupon_type .= __( ' & ', 'woocommerce-smart-coupons' );
				}
				$coupon_type .= __( 'Free Shipping', 'woocommerce-smart-coupons' );
			}

			$coupon_description = ( 'yes' === $show_coupon_description ) ? $coupon_description : '';

			$is_percent = $this->is_percent_coupon( array( 'coupon_object' => $coupon ) );

			$args = array(
				'coupon_object'      => $coupon,
				'coupon_amount'      => $coupon_amount,
				'amount_symbol'      => ( true === $is_percent ) ? '%' : get_woocommerce_currency_symbol(),
				'discount_type'      => wp_strip_all_tags( $coupon_type ),
				'coupon_description' => ( ! empty( $coupon_description ) ) ? $coupon_description : wp_strip_all_tags( $this->generate_coupon_description( array( 'coupon_object' => $coupon ) ) ),
				'coupon_code'        => $coupon_data['coupon_code'],
				'coupon_expiry'      => ( ! empty( $coupon_data['expiry_date'] ) ) ? $coupon_data['expiry_date'] : __( 'Never expires', 'woocommerce-smart-coupons' ),
				'thumbnail_src'      => $this->get_coupon_design_thumbnail_src(
					array(
						'design'        => $design,
						'coupon_object' => $coupon,
					)
				),
				'classes'            => 'apply_coupons_credits',
				'template_id'        => $design,
				'is_percent'         => $is_percent,
			);

			wc_get_template( 'coupon-design/' . $design . '.php', $args, '', plugin_dir_path( WC_SC_PLUGIN_FILE ) . 'templates/' );

			$html = ob_get_clean();

			return $html;

		}

		/**
		 * Endpoint HTML content.
		 * To show available coupons on My Account page
		 */
		public function sc_endpoint_content() {
			global $store_credit_label, $woocommerce_smart_coupon;

			$max_coupon_to_show = get_option( 'wc_sc_setting_max_coupon_to_show', 5 );
			$max_coupon_to_show = get_option( 'wc_sc_setting_max_coupon_to_show_on_myaccount', ( $max_coupon_to_show * 10 ) );
			$max_coupon_to_show = apply_filters( 'wc_sc_max_coupon_to_show_on_myaccount', $max_coupon_to_show, array( 'source' => $this ) );
			$max_coupon_to_show = absint( $max_coupon_to_show );

			$coupons = array();
			if ( $max_coupon_to_show > 0 ) {
				$coupons = $this->sc_get_available_coupons_list( array() );
			}

			if ( empty( $coupons ) ) {
				?>
				<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
					<?php echo esc_html__( 'Sorry, No coupons available for you.', 'woocommerce-smart-coupons' ); ?>
				</div>
				<?php
				return false;
			}

			if ( ! wp_style_is( 'smart-coupon' ) ) {
				wp_enqueue_style( 'smart-coupon' );
			}

			$coupons_applied = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_applied_coupons' ) ) ) ? WC()->cart->get_applied_coupons() : array();

			$available_coupons_heading = get_option( 'smart_coupon_myaccount_page_text' );

			/* translators: %s: plural name for store credit */
			$available_coupons_heading = ( ! empty( $available_coupons_heading ) ) ? $available_coupons_heading : ( ! empty( $store_credit_label['plural'] ) ? sprintf( __( 'Available Coupons & %s', 'woocommerce-smart-coupons' ), ucwords( $store_credit_label['plural'] ) ) : __( 'Available Coupons & Store Credits', 'woocommerce-smart-coupons' ) );

			$design                  = get_option( 'wc_sc_setting_coupon_design', 'basic' );
			$background_color        = get_option( 'wc_sc_setting_coupon_background_color', '#39cccc' );
			$foreground_color        = get_option( 'wc_sc_setting_coupon_foreground_color', '#30050b' );
			$third_color             = get_option( 'wc_sc_setting_coupon_third_color', '#39cccc' );
			$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );

			$valid_designs = $this->get_valid_coupon_designs();

			if ( ! in_array( $design, $valid_designs, true ) ) {
				$design = 'basic';
			}

			?>
			<style type="text/css"><?php echo esc_html( wp_strip_all_tags( $this->get_coupon_styles( $design ), true ) ); // phpcs:ignore ?></style>
			<?php
			if ( 'custom-design' !== $design ) {
				if ( ! wp_style_is( 'smart-coupon-designs' ) ) {
					wp_enqueue_style( 'smart-coupon-designs' );
				}
				?>
					<style type="text/css">
						:root {
							--sc-color1: <?php echo esc_html( $background_color ); ?>;
							--sc-color2: <?php echo esc_html( $foreground_color ); ?>;
							--sc-color3: <?php echo esc_html( $third_color ); ?>;
						}
					</style>
					<?php
			}
			?>
			<style type="text/css">
				.wc_sc_coupon_actions_wrapper {
					float: right;
				}
			</style>
			<h2><?php echo esc_html__( stripslashes( $available_coupons_heading ), 'woocommerce-smart-coupons' ); // phpcs:ignore ?></h2>
			<p><?php echo esc_html__( 'List of coupons which are valid & available for use. Click on the coupon to use it. The coupon discount will be visible only when at least one product is present in the cart.', 'woocommerce-smart-coupons' ); ?></p>

			<div class="woocommerce-Message woocommerce-Message--info woocommerce-info" style="display:none;">
				<?php echo esc_html__( 'Sorry, No coupons available for you.', 'woocommerce-smart-coupons' ); ?>
			</div>

			<?php

			$coupon_block_data = array(
				'smart_coupons'   => array(
					'html' => array(),
				),
				'valid_coupons'   => array(
					'html' => array(),
				),
				'invalid_coupons' => array(
					'html' => array(),
				),
			);

			$total_store_credit = 0;
			$coupons_to_print   = array();

			foreach ( $coupons as $code ) {

				if ( in_array( $code->post_title, $coupons_applied, true ) ) {
					continue;
				}

				$coupon_id = wc_get_coupon_id_by_code( $code->post_title );

				if ( empty( $coupon_id ) ) {
					continue;
				}

				if ( $max_coupon_to_show <= 0 ) {
					break;
				}

				$coupon = new WC_Coupon( $code->post_title );

				if ( $this->is_wc_gte_30() ) {
					if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
						continue;
					}
					$coupon_id = $coupon->get_id();
					if ( empty( $coupon_id ) ) {
						continue;
					}
					$is_free_shipping = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
					$discount_type    = $coupon->get_discount_type();
					$expiry_date      = $coupon->get_date_expires();
					$coupon_code      = $coupon->get_code();
				} else {
					$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
					$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
					$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					$expiry_date      = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
					$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
				}

				$coupon_amount = $this->get_amount( $coupon, true );

				if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
					$expiry_date = ( is_callable( array( $expiry_date, 'getTimestamp' ) ) ) ? $expiry_date->getTimestamp() : null;
				} elseif ( ! is_int( $expiry_date ) ) {
					$expiry_date = $this->strtotime( $expiry_date );
				}

				if ( ! empty( $expiry_date ) && is_int( $expiry_date ) ) {
					$expiry_time = ( $this->is_callable( $coupon, 'get_meta' ) ) ? (int) $coupon->get_meta( 'wc_sc_expiry_time' ) : (int) get_post_meta( $coupon_id, 'wc_sc_expiry_time', true );
					if ( ! empty( $expiry_time ) ) {
						$expiry_date += $expiry_time; // Adding expiry time to expiry date.
					}
				}

				if ( empty( $discount_type ) ) {
					continue;
				}

				$coupon_post = get_post( $coupon_id );

				$coupon_data = $this->get_coupon_meta_data( $coupon );

				$block_data                            = array();
				$block_data['coupon_code']             = $coupon_code;
				$block_data['coupon_amount']           = $coupon_data['coupon_amount'];
				$block_data['coupon_type']             = $coupon_data['coupon_type'];
				$block_data['is_free_shipping']        = $is_free_shipping;
				$block_data['coupon_object']           = $coupon;
				$block_data['design']                  = $design;
				$block_data['show_coupon_description'] = $show_coupon_description;

				if ( ! empty( $coupon_post->post_excerpt ) && 'yes' === $show_coupon_description ) {
					$block_data['coupon_description'] = $coupon_post->post_excerpt;
				}

				if ( ! empty( $expiry_date ) ) {
					$block_data['expiry_date'] = $this->get_expiration_format( $expiry_date );
				} else {
					$block_data['expiry_date'] = '';
				}

				$show_as_valid = apply_filters( 'wc_sc_show_as_valid', $this->is_valid( $coupon ), array( 'coupon_obj' => $coupon ) );

				if ( true === $show_as_valid ) {
					$coupons_to_print[] = $block_data['coupon_code'];
					$html               = $this->get_coupon_html( $block_data );
					if ( 'smart_coupon' === $discount_type ) {
						$total_store_credit                          += $coupon_amount;
						$coupon_block_data['smart_coupons']['html'][] = $html;
					} else {
						$coupon_block_data['valid_coupons']['html'][] = $html;
					}
				} else {
					$block_data['is_invalid'] = 'yes';
					$html                     = $this->get_coupon_html( $block_data );
					$coupon_block_data['invalid_coupons']['html'][] = $html;
				}

				$max_coupon_to_show--;

			}

			$coupon_block_data['smart_coupons']['total'] = $total_store_credit;

			$is_print = get_option( 'smart_coupons_is_print_coupon', 'yes' );
			$is_print = apply_filters( 'wc_sc_myaccount_show_print_button', wc_string_to_bool( $is_print ), array( 'source' => $woocommerce_smart_coupon ) );

			if ( true === $is_print && ! empty( $coupons_to_print ) ) {
				$print_url = add_query_arg(
					array(
						'print-coupons' => 'yes',
						'source'        => 'wc-smart-coupons',
						'coupon-codes'  => implode(
							',',
							$coupons_to_print
						),
					)
				);
				?>
				<span class="wc_sc_coupon_actions_wrapper">
					<a target="_blank" href="<?php echo esc_url( $print_url ); ?>" class="button"><?php echo esc_html( _n( 'Print coupon', 'Print coupons', count( $coupons_to_print ), 'woocommerce-smart-coupons' ) ); ?></a>
				</span>
				<?php
			}
			?>
			<?php

			$allowed_html = wp_kses_allowed_html( 'post' );

			$additional_allowed_html = array(
				'svg'    => array(
					'fill'                => true,
					'viewbox'             => true,
					'stroke'              => true,
					'class'               => true,
					'style'               => true,
					'xmlns'               => true,
					'preserveaspectratio' => true,
				),
				'path'   => array(
					'stroke-linecap'  => true,
					'stroke-linejoin' => true,
					'stroke-width'    => true,
					'd'               => true,
					'fill'            => true,
					'class'           => true,
				),
				'g'      => array(
					'stroke-miterlimit' => true,
					'stroke-linecap'    => true,
					'stroke-linejoin'   => true,
					'stroke-width'      => true,
				),
				'circle' => array(
					'cx' => true,
					'cy' => true,
					'r'  => true,
				),
				'defs'   => array(),
				'style'  => array(),
			);

			$additional_allowed_html = apply_filters( 'wc_sc_kses_allowed_html_for_coupon', $additional_allowed_html, array( 'source' => $this ) );

			if ( ! empty( $additional_allowed_html ) ) {
				foreach ( $additional_allowed_html as $tag => $attributes ) {
					if ( ! empty( $attributes ) && array_key_exists( $tag, $allowed_html ) ) {
						$allowed_html[ $tag ] = array_merge( $allowed_html[ $tag ], $attributes );
					} else {
						$allowed_html[ $tag ] = $attributes;
					}
				}
			}

			$smart_coupons_block = '';

			if ( ! empty( $coupon_block_data['smart_coupons']['html'] ) ) {
				$smart_coupons_block = implode( '', $coupon_block_data['smart_coupons']['html'] );
			}

			$smart_coupons_block = trim( $smart_coupons_block );

			if ( ! empty( $smart_coupons_block ) ) {
				?>
					<div id='sc_coupons_list'>
						<h4><?php echo ! empty( $store_credit_label['plural'] ) ? esc_html( ucwords( $store_credit_label['plural'] ) ) : esc_html__( 'Store Credits', 'woocommerce-smart-coupons' ); ?></h4>
						<div id="sc-cc">
							<div id="all_coupon_container" class="sc-coupons-list">
							<?php echo wp_kses( $smart_coupons_block, $allowed_html ); // phpcs:ignore ?>
							</div>
						</div>
					<?php
					if ( ! empty( $coupon_block_data['smart_coupons']['total'] ) && 0 !== $coupon_block_data['smart_coupons']['total'] ) {
						?>
						<div class="wc_sc_total_available_store_credit"><?php echo esc_html__( 'Total Credit Amount', 'woocommerce-smart-coupons' ) . ': ' . wc_price( $coupon_block_data['smart_coupons']['total'] ); // phpcs:ignore ?></div>
						<?php
					}
					?>
					<br>
				</div>
			<?php } ?>
			<?php
			$valid_coupons_block = '';

			if ( ! empty( $coupon_block_data['valid_coupons']['html'] ) ) {
				$valid_coupons_block = implode( '', $coupon_block_data['valid_coupons']['html'] );
			}

			$valid_coupons_block = trim( $valid_coupons_block );

			if ( ! empty( $valid_coupons_block ) ) {
				?>
					<div id='coupons_list'>
						<h4><?php echo esc_html__( 'Discount Coupons', 'woocommerce-smart-coupons' ); ?></h4>
						<div id="sc-cc">
							<div id="all_coupon_container" class="sc-coupons-list">
							<?php echo wp_kses( $valid_coupons_block, $allowed_html ); // phpcs:ignore ?>
							</div>
						</div>
					</div>
					<br>
			<?php } ?>
			<?php
			// to show user specific coupons on My Account.
			$this->generated_coupon_details_before_my_account();

			$is_show_invalid_coupons = get_option( 'smart_coupons_show_invalid_coupons_on_myaccount', 'no' );
			if ( 'yes' === $is_show_invalid_coupons ) {
				$invalid_coupons_block = '';

				if ( ! empty( $coupon_block_data['invalid_coupons']['html'] ) ) {
					$invalid_coupons_block = implode( '', $coupon_block_data['invalid_coupons']['html'] );
				}

				$invalid_coupons_block = trim( $invalid_coupons_block );

				if ( ! empty( $invalid_coupons_block ) ) {
					?>
					<br>
					<div id='invalid_coupons_list'>
						<h2>
						<?php
						echo wp_kses(
							apply_filters(
								'wc_sc_invalid_coupons_list_title',
								__( 'Invalid / Used Coupons', 'woocommerce-smart-coupons' ),
								array(
									'source'          => $this,
									'all_coupon_data' => $coupon_block_data,
								)
							),
							$allowed_html
						);
						?>
							</h2>
						<p>
						<?php
						echo wp_kses(
							apply_filters(
								'wc_sc_invalid_coupons_list_description',
								__( 'List of coupons which can not be used. The reason can be based on its usage restrictions, usage limits, expiry date.', 'woocommerce-smart-coupons' ),
								array(
									'source'          => $this,
									'all_coupon_data' => $coupon_block_data,
								)
							),
							$allowed_html
						);
						?>
							</p>
						<div id="sc-cc">
							<div id="all_coupon_container" class="sc-coupons-list">
								<?php echo wp_kses( $invalid_coupons_block, $allowed_html ); // phpcs:ignore ?>
							</div>
						</div>
					</div>
					<?php
				}
			}

			if ( did_action( 'wc_smart_coupons_frontend_styles_and_scripts' ) <= 0 || ! defined( 'DOING_AJAX' ) || DOING_AJAX !== true ) {
				$this->frontend_styles_and_scripts( array( 'page' => 'myaccount' ) );
			}

			$js = "var total_store_credit = '" . $total_store_credit . "';
					if ( total_store_credit == 0 ) {
						jQuery('#sc_coupons_list').hide();
					}

					if( jQuery('div#all_coupon_container').children().length == 0 ) {
						jQuery('#coupons_list').hide();
					}

					if( jQuery('div.woocommerce-MyAccount-content').children().length == 0 ) {
						jQuery('.woocommerce-MyAccount-content').append(jQuery('.woocommerce-Message.woocommerce-Message--info.woocommerce-info'));
						jQuery('.woocommerce-Message.woocommerce-Message--info.woocommerce-info').show();
					}

					/* to show scroll bar for core coupons */
					var coupons_list = jQuery('#coupons_list');
					var coupons_list_height = coupons_list.height();

					if ( coupons_list_height > 400 ) {
						coupons_list.css('height', '400px');
						coupons_list.css('overflow-y', 'scroll');
					} else {
						coupons_list.css('height', '');
						coupons_list.css('overflow-y', '');
					}
			";

			wc_enqueue_js( $js );

		}

		/**
		 * Function to get available coupons list
		 *
		 * @param array $coupons The coupons.
		 * @param array $args Additional arguments.
		 * @return array Modified coupons.
		 */
		public function sc_get_available_coupons_list( $coupons = array(), $args = array() ) {

			global $wpdb;

			$global_coupons = array();

			$wpdb->query( $wpdb->prepare( 'SET SESSION group_concat_max_len=%d', 999999 ) ); // phpcs:ignore

			$global_coupons = wp_cache_get( 'wc_sc_global_coupons', 'woocommerce_smart_coupons' );

			if ( false === $global_coupons ) {
				$global_coupons_ids = get_option( 'sc_display_global_coupons' );

				if ( ! empty( $global_coupons_ids ) ) {
					$global_coupons = $wpdb->get_results( // phpcs:ignore
						$wpdb->prepare(
							"SELECT *
								FROM {$wpdb->prefix}posts
								WHERE FIND_IN_SET (ID, (SELECT GROUP_CONCAT(option_value SEPARATOR ',') FROM {$wpdb->prefix}options WHERE option_name = %s)) > 0
									AND post_type = %s
								GROUP BY ID
								ORDER BY post_date DESC",
							'sc_display_global_coupons',
							'shop_coupon'
						)
					);
					wp_cache_set( 'wc_sc_global_coupons', $global_coupons, 'woocommerce_smart_coupons' );
					$this->maybe_add_cache_key( 'wc_sc_global_coupons' );
				}
			}

			if ( is_scalar( $global_coupons ) ) {
				$global_coupons = array();
			}

			$global_coupons = apply_filters( 'wc_smart_coupons_global_coupons', $global_coupons );

			$args['global_coupons'] = $global_coupons;

			if ( is_user_logged_in() ) {

				global $current_user;

				if ( ! empty( $current_user->user_email ) && ! empty( $current_user->ID ) ) {

					$count_option_current_user = wp_cache_get( 'wc_sc_current_users_option_name_' . $current_user->ID, 'woocommerce_smart_coupons' );

					if ( false === $count_option_current_user ) {
						$count_option_current_user = $wpdb->get_col( // phpcs:ignore
							$wpdb->prepare(
								"SELECT option_name
									FROM {$wpdb->prefix}options
									WHERE option_name LIKE %s
									ORDER BY option_id DESC",
								$wpdb->esc_like( 'sc_display_custom_credit_' . $current_user->ID . '_' ) . '%'
							)
						);
						wp_cache_set( 'wc_sc_current_users_option_name_' . $current_user->ID, $count_option_current_user, 'woocommerce_smart_coupons' );
						$this->maybe_add_cache_key( 'wc_sc_current_users_option_name_' . $current_user->ID );
					}

					if ( count( $count_option_current_user ) > 0 ) {
						$count_option_current_user = substr( strrchr( $count_option_current_user[0], '_' ), 1 );
						$count_option_current_user = ( ! empty( $count_option_current_user ) ) ? $count_option_current_user + 2 : 1;
					} else {
						$count_option_current_user = 1;
					}

					$coupon_ids = array();
					$coupon_ids = apply_filters( 'wc_sc_available_coupon_ids', $coupon_ids, $args );

					$option_nm = 'sc_display_custom_credit_' . $current_user->ID . '_' . $count_option_current_user;
					$wpdb->query( $wpdb->prepare( 'SET SESSION group_concat_max_len=%d', 999999 ) ); // phpcs:ignore
					$wpdb->delete( $wpdb->prefix . 'options', array( 'option_name' => $option_nm ) ); // WPCS: db call ok.
					$query_string = "REPLACE INTO {$wpdb->prefix}options (option_name, option_value, autoload)
										SELECT %s,
											IFNULL(GROUP_CONCAT(DISTINCT p.id SEPARATOR ','), ''),
											%s
										FROM {$wpdb->prefix}posts AS p
											JOIN {$wpdb->prefix}postmeta AS pm
												ON(pm.post_id = p.ID
													AND p.post_type = %s
													AND p.post_status = %s
													AND pm.meta_key = %s
													AND pm.meta_value LIKE %s";

					$query_args = array(
						$option_nm,
						'no',
						'shop_coupon',
						'publish',
						'customer_email',
						'%' . $wpdb->esc_like( '"' . $current_user->user_email . '"' ) . '%',
					);

					if ( ! empty( $coupon_ids ) ) {
						$how_many      = count( $coupon_ids );
						$placeholders  = array_fill( 0, $how_many, '%d' );
						$query_string .= ' AND p.ID IN (' . implode( ',', $placeholders ) . ')';
						$query_args    = array_merge( $query_args, $coupon_ids );
					}

					$query_string .= ')';

					$wpdb->query( // phpcs:ignore
						$wpdb->prepare( $query_string, $query_args ) // phpcs:ignore
					);

					$customer_coupon_ids = get_option( $option_nm );

					// Only execute rest of the queries if coupons found.
					if ( ! empty( $customer_coupon_ids ) ) {
						$wpdb->query( // phpcs:ignore
							$wpdb->prepare(
								"UPDATE {$wpdb->prefix}options
									SET option_value = (SELECT IFNULL(GROUP_CONCAT(post_id SEPARATOR ','), '')
														FROM {$wpdb->prefix}postmeta
														WHERE meta_key = %s
															AND CAST(meta_value AS SIGNED) >= '0'
															AND FIND_IN_SET(post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = %s) as temp )) > 0 )
									WHERE option_name = %s",
								'coupon_amount',
								$option_nm,
								$option_nm
							)
						);
					}
					$coupons = wp_cache_get( 'wc_sc_all_coupon_id_for_user_' . $current_user->ID, 'woocommerce_smart_coupons' );

					if ( false === $coupons ) {
						$coupons = $wpdb->get_results( // phpcs:ignore
							$wpdb->prepare(
								"SELECT *
									FROM {$wpdb->prefix}posts
									WHERE FIND_IN_SET (ID, (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = %s)) > 0
										AND post_status = %s
										AND post_type = %s
									GROUP BY ID
									ORDER BY post_date DESC",
								$option_nm,
								'publish',
								'shop_coupon'
							)
						);
						wp_cache_set( 'wc_sc_all_coupon_id_for_user_' . $current_user->ID, $coupons, 'woocommerce_smart_coupons' );
						$this->maybe_add_cache_key( 'wc_sc_all_coupon_id_for_user_' . $current_user->ID );
					}

					$wpdb->query( // phpcs:ignore
						$wpdb->prepare(
							"DELETE FROM {$wpdb->prefix}options WHERE option_name = %s",
							$option_nm
						)
					);
				}
			}

			$coupons = array_merge( $coupons, $global_coupons );

			$unique_id_to_code = array_unique( array_reverse( wp_list_pluck( $coupons, 'post_title', 'ID' ), true ) );

			$unique_ids = array_map( 'absint', array_keys( $unique_id_to_code ) );

			foreach ( $coupons as $index => $coupon ) {
				if ( empty( $coupon->ID ) || ! in_array( absint( $coupon->ID ), $unique_ids, true ) ) {
					unset( $coupons[ $index ] );
				}
			}

			return $coupons;

		}

		/**
		 * Include frontend styles & scripts
		 *
		 * @param array $args Arguments.
		 */
		public function frontend_styles_and_scripts( $args = array() ) {

			if ( is_account_page() ) {
				$show_myaccount_menu_icon = apply_filters(
					'wc_sc_show_myaccount_menu_icon',
					$this->sc_get_option( 'wc_sc_show_myaccount_menu_icon' ),
					array(
						'source' => $this,
						'args'   => $args,
					)
				);

				if ( 'yes' === $show_myaccount_menu_icon ) {
					if ( empty( self::$endpoint ) ) {
						self::$endpoint = self::get_endpoint();
					}
					?>
					<style type="text/css">
						.woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--<?php echo esc_html( self::$endpoint ); ?> a:before {
							font-family: "dashicons";
							content: "\f524";
						}
					</style>
					<?php
				}
			}

			if ( empty( $args['page'] ) ) {
				return;
			}

			if ( apply_filters( 'wc_sc_apply_coupon_via_ajax', true, array( 'source' => $this ) ) ) {
				$url               = WC_AJAX::get_endpoint( 'apply_coupon' );
				$wp_security_nonce = wp_create_nonce( 'apply-coupon' );
				$js                = "function wc_sc_apply_coupon_js() {
										let coupon_code = jQuery(this).data('coupon_code');
										if( coupon_code != '' && coupon_code != undefined ) {
											jQuery(this).css('opacity', '0.5');";

				if ( is_cart() ) {
					$js .= "block_cart_elements( jQuery( '.woocommerce-cart-form' ) );
							block_cart_elements( jQuery( 'div.cart_totals' ) );
							block_cart_elements( jQuery( '.sc-coupons-list' ) ); ";
				} else {
					$js .= "block_cart_elements( jQuery( '.sc-coupons-list' ) ); ";
				}
				$js .= "var data = {
									coupon_code: coupon_code, 
									security: '" . $wp_security_nonce . "'
							};
										
							jQuery.ajax( {
									type:     'POST',
									url:      '" . $url . "',
									data:     data,
									dataType: 'html',";
				if ( is_cart() ) {
					$js .= "success: function( response ) {
										jQuery( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
										jQuery( '.woocommerce-notices-wrapper:first' ).prepend( response );                               
									},
									complete: function() {
									   update_cart();
									   jQuery( '.woocommerce-cart-form' ).removeClass( 'processing' ).unblock();
									   jQuery( 'div.cart_totals' ).removeClass( 'processing' ).unblock();
									   jQuery( '.sc-coupons-list' ).removeClass( 'processing' ).unblock();
									}";
				} elseif ( is_checkout() ) {
					$js .= "success: function( response ) {             
										if ( response ) {    
											jQuery( '.woocommerce-error, .woocommerce-message' ).remove();                             
											jQuery( 'form.woocommerce-checkout' ).before( response );               

											$( document.body ).trigger( 'update_checkout', { update_shipping_method: false } );                          
										}                          
									},
									complete: function() {
										jQuery( '.sc-coupons-list' ).removeClass( 'processing' ).unblock();
									}";
				} else {
					$js .= "success: function( response ) {
										jQuery( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
									   
										if( jQuery('body #sc_coupons_list').length ){
											jQuery( '#sc_coupons_list' ).before( response );    
										}else{
											 jQuery( '#coupons_list' ).before( response );   
										}
										scroll_to_notices( jQuery( '[role=\"alert\"]' ) );
									},
									complete: function() {
										jQuery( '.sc-coupons-list' ).removeClass( 'processing' ).unblock();
									}";
				}

				$js .= ' } );
		
						}
					}';
			} else {
				$js = "function wc_sc_apply_coupon_js() {
					let coupon_code = jQuery(this).data('coupon_code');
	
					if( coupon_code != '' && coupon_code != undefined ) {
	
						jQuery(this).css('opacity', '0.5');
						var url = '" . trailingslashit( home_url() ) . ( ( strpos( home_url(), '?' ) === false ) ? '?' : '&' ) . ( ( ! empty( $args['page'] ) ) ? 'sc-page=' . $args['page'] : '' ) . "&coupon-code='+coupon_code;
						jQuery(location).attr('href', url);
	
					}
				}";
			}

			$js .= "var show_hide_coupon_list = function() {
						if ( jQuery('div#coupons_list').find('div.sc-coupon, div.coupon-container').length > 0 ) {
							jQuery('div#coupons_list').slideDown(800);
						} else {
							jQuery('div#coupons_list').hide();
						}
					};

					var coupon_container_height = jQuery('#all_coupon_container').height();
					if ( coupon_container_height > 400 ) {
						jQuery('#all_coupon_container').css('height', '400px');
						jQuery('#all_coupon_container').css('overflow-y', 'scroll');
					} else {
						jQuery('#all_coupon_container').css('height', '');
						jQuery('#all_coupon_container').css('overflow-y', '');
					}
		
					jQuery( document.body ).on('click', '.apply_coupons_credits', wc_sc_apply_coupon_js);
		
					jQuery('.checkout_coupon').next('#coupons_list').hide();
		
					jQuery('a.showcoupon').on('click', function() {
						show_hide_coupon_list();
					});
		
					jQuery('div#invalid_coupons_list div#all_coupon_container .sc-coupon').removeClass('apply_coupons_credits');
		
					function wc_sc_update_checkout() {
						jQuery( document.body ).trigger('update_checkout');
					}

					 function scroll_to_notices( scrollElement ){
						if ( scrollElement.length ) {
							jQuery( 'html, body' ).animate( {
								scrollTop: ( scrollElement.offset().top - 100 )
							}, 1000 );
						}
					 }
					
					function block_cart_elements( node ){
						if ( ! ( node.is( '.processing' ) || node.parents( '.processing' ).length ) ) {
							node.addClass( 'processing' ).block( {
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							} );
						}
					}
					
					function update_cart()
					{
						var cart_form = jQuery( '.woocommerce-cart-form' );
						block_cart_elements( cart_form );
						block_cart_elements( jQuery( 'div.cart_totals' ) );
						block_cart_elements( jQuery( '.sc-coupons-list' ) );
						
						// Make call to actual form post URL.
						jQuery.ajax( {
							type:     cart_form.attr( 'method' ),
							url:      cart_form.attr( 'action' ),
							data:     cart_form.serialize(),
							dataType: 'html',
							success:  function( response ) {
								var response_html  = jQuery.parseHTML( response );
								var updated_cart_form  = jQuery( '.woocommerce-cart-form', response_html );
								var updated_totals     = jQuery( '.cart_totals', response_html );
								jQuery( '.woocommerce-cart-form' ).replaceWith( updated_cart_form );
								jQuery( '.woocommerce-cart-form' ).find( ':input[name=\"update_cart\"]' ).prop( 'disabled', true ).attr( 'aria-disabled', true );
					   
								jQuery( '.cart_totals' ).replaceWith( updated_totals );
								jQuery( document.body ).trigger( 'updated_cart_totals' );
								
							}, 
							complete: function() {
							   scroll_to_notices( jQuery( '[role=\"alert\"]' ) ); 
							   cart_form.removeClass( 'processing' ).unblock();
							   jQuery( 'div.cart_totals' ).removeClass( 'processing' ).unblock();
							   jQuery( '.sc-coupons-list' ).removeClass( 'processing' ).unblock();                          
							   
							}
						} );
					}";

			if ( is_checkout() ) {
				$is_wc_session_get = function_exists( 'WC' ) && isset( WC()->session ) && is_object( WC()->session ) && is_callable( array( WC()->session, 'get' ) );
				if ( true === $is_wc_session_get ) {
					$is_reload = WC()->session->get( 'wc_sc_reload_payment_method' );
					if ( 'yes' === $is_reload ) {
						$js .= "
								jQuery(document.body).off('change', 'input[name=payment_method]', wc_sc_update_checkout);
								jQuery(document.body).on('change', 'input[name=payment_method]', wc_sc_update_checkout);
								";
					} else {
						$js .= "
								jQuery(document.body).off('change', 'input[name=payment_method]', wc_sc_update_checkout);
								";
					}
				}
				$js .= "
						jQuery(document.body).on('applied_coupon_in_checkout', function( e, data ){
							if ( jQuery('.woocommerce .woocommerce-message').length ) {
								jQuery(document.body).off('change', 'input[name=payment_method]', wc_sc_update_checkout);
							} else {
								jQuery(document.body).on('change', 'input[name=payment_method]', wc_sc_update_checkout);
							}
						});
						jQuery(document.body).on('updated_checkout', function( e, data ){
							try {
								if ( data.fragments.wc_sc_available_coupons ) {
									jQuery('div#coupons_list').replaceWith( data.fragments.wc_sc_available_coupons );
									jQuery( document.body ).on('click', '.apply_coupons_credits', wc_sc_apply_coupon_js);
								}
							} catch(e) {}
							show_hide_coupon_list();
						});
						";
			} else {
				$js .= '
						show_hide_coupon_list();
						';
			}

			if ( $this->is_wc_gte_26() ) {
				$js .= "
						if (typeof sc_available_coupons_ajax === 'undefined') {
							var sc_available_coupons_ajax = null;
						}
						jQuery(document.body).on('updated_cart_totals update_checkout', function(){
							clearTimeout( sc_available_coupons_ajax );
							sc_available_coupons_ajax = setTimeout(function(){
								jQuery('div#coupons_list').css('opacity', '0.5');
								jQuery.ajax({
									url: '" . admin_url( 'admin-ajax.php' ) . "',
									type: 'post',
									dataType: 'html',
									data: {
										action: 'sc_get_available_coupons',
										security: '" . wp_create_nonce( 'sc-get-available-coupons' ) . "'
									},
									success: function( response ) {
										if ( response != undefined && response != '' ) {
											jQuery('div#coupons_list').replaceWith( response );
										}
										show_hide_coupon_list();
										jQuery('div#coupons_list').css('opacity', '1');
									}
								});
							}, 200);
						});";
			} else {
				$js .= "
						jQuery('body').on( 'update_checkout', function( e ){
							var coupon_code = jQuery('.woocommerce-remove-coupon').data( 'coupon' );
							if ( coupon_code != undefined && coupon_code != '' ) {
								jQuery('div[data-coupon_code=\"'+coupon_code+'\"].apply_coupons_credits').show();
							}
						});";
			}

			wc_enqueue_js( $js );

			do_action( 'wc_smart_coupons_frontend_styles_and_scripts' );

		}

		/**
		 * Generate & add available coupons fragments
		 *
		 * @param  array $fragments Existing fragments.
		 * @return array $fragments
		 */
		public function woocommerce_update_order_review_fragments( $fragments = array() ) {

			if ( ! empty( $_POST['post_data'] ) ) { // phpcs:ignore
				wp_parse_str( $_POST['post_data'], $posted_data ); // phpcs:ignore
				if ( empty( $_REQUEST['billing_email'] ) && ! empty( $posted_data['billing_email'] ) ) { // phpcs:ignore
					$_REQUEST['billing_email'] = $posted_data['billing_email'];
				}
			}

			ob_start();
			$this->show_available_coupons_before_checkout_form();
			$fragments['wc_sc_available_coupons'] = ob_get_clean();

			return $fragments;
		}

		/**
		 * Get endpoint
		 *
		 * @return string The endpoint
		 */
		public static function get_endpoint() {
			self::$endpoint = get_option( 'woocommerce_myaccount_wc_sc_dashboard_endpoint', 'wc-smart-coupons' );
			return self::$endpoint;
		}

		/**
		 * Hooks for handle endpoint
		 */
		public function endpoint_hooks() {
			if ( empty( self::$endpoint ) ) {
				self::$endpoint = self::get_endpoint();
			}
			if ( $this->is_wc_gte_34() ) {
				add_filter( 'woocommerce_get_settings_advanced', array( $this, 'add_endpoint_account_settings' ) );
			} else {
				add_filter( 'woocommerce_account_settings', array( $this, 'add_endpoint_account_settings' ) );
			}
		}

		/**
		 * Add UI option for changing Smart Coupons endpoints in WC settings
		 *
		 * @param mixed $settings Existing settings.
		 * @return mixed $settings
		 */
		public function add_endpoint_account_settings( $settings ) {

			$sc_endpoint_setting = array(
				'title'    => __( 'Coupons', 'woocommerce-smart-coupons' ),
				'desc'     => __( 'Endpoint for the My Account &rarr; Coupons page', 'woocommerce-smart-coupons' ),
				'id'       => 'woocommerce_myaccount_wc_sc_dashboard_endpoint',
				'type'     => 'text',
				'default'  => 'wc-smart-coupons',
				'desc_tip' => true,
			);

			$after_key = 'woocommerce_myaccount_view_order_endpoint';

			$after_key = apply_filters(
				'wc_sc_endpoint_account_settings_after_key',
				$after_key,
				array(
					'settings' => $settings,
					'source'   => $this,
				)
			);

			WC_Smart_Coupons::insert_setting_after( $settings, $after_key, $sc_endpoint_setting );

			return $settings;
		}

		/**
		 * Fetch generated coupon's details
		 *
		 * Either order_ids or user_ids required
		 *
		 * @param array|int $order_ids Order IDs.
		 * @param array|int $user_ids User IDs.
		 * @param boolean   $html Whether to return only data or html code, optional, default:false.
		 * @param boolean   $header Whether to add a header above the list of generated coupon details, optional, default:false.
		 * @param string    $layout Possible values 'box' or 'table' layout to show generated coupons details, optional, default:box.
		 *
		 * @return array $generated_coupon_data associative array containing generated coupon's details
		 */
		public function get_generated_coupon_data( $order_ids = '', $user_ids = '', $html = false, $header = false, $layout = 'box' ) {
			global $wpdb;

			if ( ! is_array( $order_ids ) ) {
				$order_ids = ( ! empty( $order_ids ) ) ? array( $order_ids ) : array();
			}

			if ( ! is_array( $user_ids ) ) {
				$user_ids = ( ! empty( $user_ids ) ) ? array( $user_ids ) : array();
			}

			$user_order_ids = array();

			if ( ! empty( $user_ids ) ) {

				if ( $this->is_hpos() ) {
					$user_order_ids_query = $wpdb->prepare(
						"SELECT DISTINCT id FROM {$wpdb->prefix}wc_orders
							WHERE %d
								AND customer_id",
						1
					);
				} else {
					$user_order_ids_query = $wpdb->prepare(
						"SELECT DISTINCT post_id FROM {$wpdb->prefix}postmeta
							WHERE %d
								AND meta_value",
						1
					);
				}

				if ( count( $user_ids ) === 1 ) {
					$user_order_ids_query .= $wpdb->prepare( ' = %d', current( $user_ids ) );
				} else {
					$how_many              = count( $user_ids );
					$placeholders          = array_fill( 0, $how_many, '%d' );
					$user_order_ids_query .= $wpdb->prepare( ' IN ( ' . implode( ',', $placeholders ) . ' )', $user_ids ); // phpcs:ignore
				}

				if ( ! $this->is_hpos() ) {
					$user_order_ids_query .= $wpdb->prepare( ' AND meta_key = %s', '_customer_user' );
				}

				$unique_user_ids = array_unique( $user_ids );

				$user_order_ids = wp_cache_get( 'wc_sc_order_ids_by_user_id_' . implode( '_', $unique_user_ids ), 'woocommerce_smart_coupons' );

				if ( false === $user_order_ids ) {
					$user_order_ids = $wpdb->get_col( $user_order_ids_query ); // phpcs:ignore
					wp_cache_set( 'wc_sc_order_ids_by_user_id_' . implode( '_', $unique_user_ids ), $user_order_ids, 'woocommerce_smart_coupons' );
					$this->maybe_add_cache_key( 'wc_sc_order_ids_by_user_id_' . implode( '_', $unique_user_ids ) );
				}
			}

			$new_order_ids = array_unique( array_merge( $user_order_ids, $order_ids ) );

			$generated_coupon_data = array();
			foreach ( $new_order_ids as $id ) {
				$order                      = function_exists( 'wc_get_order' ) ? wc_get_order( $id ) : null;
				$is_callable_order_get_meta = $this->is_callable( $order, 'get_meta' );
				$data                       = ( true === $is_callable_order_get_meta ) ? $order->get_meta( 'sc_coupon_receiver_details' ) : get_post_meta( $id, 'sc_coupon_receiver_details', true );
				if ( empty( $data ) ) {
					continue;
				}
				$from = $this->is_callable( $order, 'get_billing_email' ) ? $order->get_billing_email() : get_post_meta( $id, '_billing_email', true );
				if ( empty( $generated_coupon_data[ $from ] ) ) {
					$generated_coupon_data[ $from ] = array();
				}
				$generated_coupon_data[ $from ] = array_merge( $generated_coupon_data[ $from ], $data );
			}

			$backtrace           = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );  // phpcs:ignore
			$backtrace_functions = wp_list_pluck( $backtrace, 'function' );
			if ( in_array( 'sc_generated_coupon_data_metabox', $backtrace_functions, true ) ) {
				reset( $order_ids );
				$order_id   = current( $order_ids );
				$from       = $this->get_post_meta( $order_id, '_billing_email', true );
				$coupon_ids = $wpdb->get_col( // phpcs:ignore
					$wpdb->prepare(
						"SELECT DISTINCT p.ID
							FROM {$wpdb->posts} AS p
								LEFT JOIN {$wpdb->postmeta} AS pm
									ON (p.ID = pm.post_id)
							WHERE p.post_type = %s
								AND p.post_status = %s
								AND pm.meta_key = %s
								AND (pm.meta_value = %s
										OR pm.meta_value = %d )",
						'shop_coupon',
						'future',
						'generated_from_order_id',
						$order_id,
						$order_id
					)
				);
				if ( ! empty( $coupon_ids ) && is_array( $coupon_ids ) ) {
					foreach ( $coupon_ids as $coupon_id ) {
						$coupon_receiver_details = $this->get_post_meta( $coupon_id, 'wc_sc_coupon_receiver_details', true );
						$from                    = ( ! empty( $coupon_receiver_details['gift_certificate_sender_email'] ) ) ? $coupon_receiver_details['gift_certificate_sender_email'] : $from;
						if ( empty( $generated_coupon_data[ $from ] ) || ! is_array( $generated_coupon_data[ $from ] ) ) {
							$generated_coupon_data[ $from ] = array();
						}
						$generated_coupon_data[ $from ][] = array(
							'code'     => ( ! empty( $coupon_receiver_details['coupon_details']['code'] ) ) ? $coupon_receiver_details['coupon_details']['code'] : '',
							'amount'   => ( ! empty( $coupon_receiver_details['coupon_details']['amount'] ) ) ? $coupon_receiver_details['coupon_details']['amount'] : 0,
							'email'    => ( ! empty( $coupon_receiver_details['gift_certificate_receiver_email'] ) ) ? $coupon_receiver_details['gift_certificate_receiver_email'] : '',
							'message'  => ( ! empty( $coupon_receiver_details['message_from_sender'] ) ) ? $coupon_receiver_details['message_from_sender'] : '',
							'order_id' => ( ! empty( $order_id ) ) ? $order_id : 0,
						);
					}
				}
				if ( empty( $generated_coupon_data ) ) {
					$order         = ( ! empty( $order_id ) ) ? wc_get_order( $order_id ) : null;
					$order_actions = array();
					if ( ! class_exists( 'WC_SC_Coupon_Process' ) ) {
						include_once 'class-wc-sc-coupon-process.php';
					}
					$wc_sc_coupon_process = ( is_callable( 'WC_SC_Coupon_Process::get_instance' ) ) ? WC_SC_Coupon_Process::get_instance() : null;
					if ( $this->is_callable( $wc_sc_coupon_process, 'order_actions' ) ) {
						$order_actions = $wc_sc_coupon_process->order_actions( array(), $order );
					}
					?>
					<p>
						<?php
							/* translators: 1. Link to jump to 'Order actions' metabox 2. Text 'Order actions' 3. Text 'arrow' */
							echo sprintf( esc_html_x( 'Coupons are not generated for this order. You can regenerate it from %1$s. Select an appropriate action from the %2$s dropdown menu and hit the %3$s button next to it.', 'Generated coupons metabox - Order edit admin page', 'woocommerce-smart-coupons' ), '<strong><a href="#woocommerce-order-actions">' . esc_html_x( 'Order actions', 'Generated coupons metabox - Order edit admin page', 'woocommerce-smart-coupons' ) . '</a></strong>', '<strong>' . esc_html_x( 'Order actions', 'Generated coupons metabox - Order edit admin page', 'woocommerce-smart-coupons' ) . '</strong>', '<strong>' . esc_html_x( 'arrow', 'Generated coupons metabox - Order edit admin page', 'woocommerce-smart-coupons' ) . '</strong>' );
						?>
					</p>
					<?php
					if ( ! empty( $order_actions ) ) {
						$target_actions   = array( 'wc_sc_regenerate_coupons', 'wc_sc_regenerate_resend_coupons' );
						$all_actions      = array_keys( $order_actions );
						$action_intersect = array_intersect( $target_actions, $all_actions );
						if ( ! empty( $action_intersect ) ) {
							?>
							<div>
								<ul class="ul-disc">
									<?php
									foreach ( $order_actions as $action => $label ) {
										switch ( $action ) {
											case 'wc_sc_regenerate_coupons':
												?>
												<li>
													<?php
														/* translators: 1. Label for Order action for regenerating coupons */
														echo sprintf( esc_html_x( 'Select %s to only regenerate coupons. No email will be sent for this.', 'Generated coupons metabox - Order edit admin page', 'woocommerce-smart-coupons' ), '<strong>' . esc_html( $label ) . '</strong>' );
													?>
												</li>
												<?php
												break;
											case 'wc_sc_regenerate_resend_coupons':
												?>
												<li>
													<?php
														/* translators: 1. Label for Order action for regenerating & resending coupons */
														echo sprintf( esc_html_x( 'Select %s to regenerate as well as resend coupons to the recipients via email.', 'Generated coupons metabox - Order edit admin page', 'woocommerce-smart-coupons' ), '<strong>' . esc_html( $label ) . '</strong>' );
													?>
												</li>
												<?php
												break;
										}
									}
									?>
								</ul>
							</div>
							<?php
						}
					}
				}
			}

			if ( empty( $generated_coupon_data ) ) {
				return;
			}

			if ( $html ) {

				ob_start();
				if ( 'table' === $layout ) {
					$this->get_generated_coupon_data_table( $generated_coupon_data );
				} else {
					$this->get_generated_coupon_data_box( $generated_coupon_data );
				}
				$coupon_details_html_content = ob_get_clean();

				$found_coupon = ( 'table' === $layout ) ? ( strpos( $coupon_details_html_content, 'coupon_received_row' ) !== false ) : ( strpos( $coupon_details_html_content, '<details' ) !== false );

				if ( $found_coupon && ! empty( $coupon_details_html_content ) ) {

					echo '<div id="generated_coupon_data_container" style="padding: 2em 0 2em;">';

					if ( $header ) {
						echo '<h2>' . esc_html__( 'Coupon Received', 'woocommerce-smart-coupons' ) . '</h2>';
						echo '<p>' . esc_html__( 'List of coupons & their details which you have received from the store. Click on the coupon to see the details.', 'woocommerce-smart-coupons' ) . '</p>';
					}

					echo $coupon_details_html_content; // phpcs:ignore

					echo '</div>';

				}

				return;

			}

			return $generated_coupon_data;
		}

		/**
		 * HTML code to display generated coupon's data in box layout
		 *
		 * @param array $generated_coupon_data Associative array containing generated coupon's details.
		 */
		public function get_generated_coupon_data_box( $generated_coupon_data = array() ) {
			if ( empty( $generated_coupon_data ) ) {
				return;
			}

			$design           = get_option( 'wc_sc_setting_coupon_design', 'basic' );
			$background_color = get_option( 'wc_sc_setting_coupon_background_color', '#39cccc' );
			$foreground_color = get_option( 'wc_sc_setting_coupon_foreground_color', '#30050b' );
			$third_color      = get_option( 'wc_sc_setting_coupon_third_color', '#39cccc' );

			$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );

			$valid_designs = $this->get_valid_coupon_designs();

			if ( ! in_array( $design, $valid_designs, true ) ) {
				$design = 'basic';
			}

			$current_filter = current_filter();
			if ( 'woocommerce_email_after_order_table' === $current_filter ) {
				$design = 'email-coupon';
			}

			$email = $this->get_current_user_email();
			$js    = "
					var switchMoreLess = function() {
						var total = jQuery('details').length;
						var open = jQuery('details[open]').length;
						if ( open == total ) {
							jQuery('a#more_less').text('" . __( 'Less details', 'woocommerce-smart-coupons' ) . "');
						} else {
							jQuery('a#more_less').text('" . __( 'More details', 'woocommerce-smart-coupons' ) . "');
						}
					};
					switchMoreLess();

					jQuery('a#more_less').on('click', function(){
						var current = jQuery('details').attr('open');
						if ( current == '' || current == undefined ) {
							jQuery('details').attr('open', 'open');
							jQuery('a#more_less').text('" . __( 'Less details', 'woocommerce-smart-coupons' ) . "');
						} else {
							jQuery('details').removeAttr('open');
							jQuery('a#more_less').text('" . __( 'More details', 'woocommerce-smart-coupons' ) . "');
						}
					});

					jQuery('summary.generated_coupon_summary').on('mouseup', function(){
						setTimeout( switchMoreLess, 10 );
					});

					jQuery('span.expand_collapse').show();

					var generated_coupon_element = jQuery('#all_generated_coupon');
					var generated_coupon_container_height = generated_coupon_element.height();
					if ( generated_coupon_container_height > 400 ) {
						generated_coupon_element.css('height', '400px');
						generated_coupon_element.css('overflow-y', 'scroll');
					} else {
						generated_coupon_element.css('height', '');
						generated_coupon_element.css('overflow-y', '');
					}

					jQuery('#all_generated_coupon').on('click', '.coupon-container', function(){
						setTimeout(function(){
							var current_element = jQuery(this).find('details');
							var is_open = current_element.attr('open');
							if ( is_open == '' || is_open == undefined ) {
									current_element.attr('open', 'open');
							} else {
								current_element.removeAttr('open');
							}
						}, 1);
					});

				";

			wc_enqueue_js( $js );

			?>
			<style type="text/css">
				.coupon-container {
					margin: .2em;
					box-shadow: 0 0 5px #e0e0e0;
					display: inline-table;
					text-align: center;
					cursor: pointer;
					max-width: 49%;
					padding: .55em;
					line-height: 1.4em;
				}
				.coupon-container.previews { cursor: inherit }

				.coupon-content {
					padding: 0.2em 1.2em;
				}

				.coupon-content .code {
					font-family: monospace;
					font-size: 1.2em;
					font-weight:700;
				}

				.coupon-content .coupon-expire,
				.coupon-content .discount-info {
					font-family: Helvetica, Arial, sans-serif;
					font-size: 1em;
				}
				.coupon-content .discount-description {
					font: .7em/1 Helvetica, Arial, sans-serif;
					width: 250px;
					margin: 10px inherit;
					display: inline-block;
				}

				.generated_coupon_details { padding: 0.6em 1em 0.4em 1em; text-align: left; }
				.generated_coupon_data { border: solid 1px lightgrey; margin-bottom: 5px; margin-right: 5px; width: 50%; }
				.generated_coupon_details p { margin: 0; }
				span.expand_collapse { text-align: right; display: block; margin-bottom: 1em; cursor: pointer; }
				.float_right_block { float: right; }
				summary::-webkit-details-marker { display: none; }
				details[open] summary::-webkit-details-marker { display: none; }
				span.wc_sc_coupon_actions_wrapper {
					display: block;
					text-align: right;
				}
			</style>
			<style type="text/css"><?php echo esc_html( wp_strip_all_tags( $this->get_coupon_styles( $design ), true ) ); // phpcs:ignore ?></style>
			<?php
			if ( 'custom-design' !== $design ) {
				if ( ! wp_style_is( 'smart-coupon-designs' ) ) {
					wp_enqueue_style( 'smart-coupon-designs' );
				}
				?>
					<style type="text/css">
						:root {
							--sc-color1: <?php echo esc_html( $background_color ); ?>;
							--sc-color2: <?php echo esc_html( $foreground_color ); ?>;
							--sc-color3: <?php echo esc_html( $third_color ); ?>;
						}
					</style>
					<?php
			}
			?>
			<div class="generated_coupon_data_wrapper">
				<span class="expand_collapse" style="display: none;">
					<a id="more_less"><?php echo esc_html__( 'More details', 'woocommerce-smart-coupons' ); ?></a>
				</span>
				<div id="sc-cc">
					<div id="all_generated_coupon" class="sc-coupons-list">
					<?php
					$is_meta_box         = false;
					$backtrace           = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );  // phpcs:ignore
					$backtrace_functions = wp_list_pluck( $backtrace, 'function' );
					if ( in_array( 'sc_generated_coupon_data_metabox', $backtrace_functions, true ) ) {
						$is_meta_box = true;
					}
					foreach ( $generated_coupon_data as $from => $data ) {
						$order        = null;
						$coupon_codes = wp_list_pluck( $data, 'code' );
						$posts        = $this->get_post_by_title( $coupon_codes, ARRAY_A, 'shop_coupon' );
						foreach ( $data as $coupon_data ) {

							if ( ! is_admin() && ! empty( $coupon_data['email'] ) && ! empty( $email ) && $coupon_data['email'] !== $email ) {
								continue;
							}

							if ( empty( $this->sc_coupon_exists( $coupon_data['code'] ) ) ) {
								continue;
							}

							$coupon   = new WC_Coupon( $coupon_data['code'] );
							$order_id = ( ! empty( $coupon_data['order_id'] ) ) ? absint( $coupon_data['order_id'] ) : 0;
							$order    = ( ! is_a( $order, 'WC_Order' ) && ! empty( $order_id ) ) ? wc_get_order( $order_id ) : $order;

							if ( $this->is_wc_gte_30() ) {
								if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
									continue;
								}
								$coupon_id = $coupon->get_id();
								if ( empty( $coupon_id ) ) {
									if ( true === $is_meta_box ) {
										$coupon_code           = ( ! empty( $coupon_data['code'] ) ) ? $coupon_data['code'] : '';
										$sanitized_coupon_code = sanitize_title( $coupon_code ); // The generated string will be checked in an array key to locate post object.
										$coupon_post_obj       = ( ! empty( $posts[ $sanitized_coupon_code ] ) ) ? $posts[ $sanitized_coupon_code ] : null;
										if ( ! empty( $coupon_post['ID'] ) ) {
											$coupon    = new WC_Coupon( $coupon_post['ID'] );
											$coupon_id = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
										} else {
											continue;
										}
									} else {
										continue;
									}
								}
								$is_free_shipping = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
								$discount_type    = $coupon->get_discount_type();
								$expiry_date      = $coupon->get_date_expires();
								$coupon_code      = $coupon->get_code();
							} else {
								$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
								$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
								$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
								$expiry_date      = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
								$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
							}

							$coupon_amount = $this->get_amount( $coupon, true, $order );

							if ( empty( $coupon_id ) || empty( $discount_type ) ) {
								continue;
							}

							$coupon_post = get_post( $coupon_id );

							$coupon_meta = $this->get_coupon_meta_data( $coupon );

							$coupon_type = ( ! empty( $coupon_meta['coupon_type'] ) ) ? $coupon_meta['coupon_type'] : '';

							if ( 'yes' === $is_free_shipping ) {
								if ( ! empty( $coupon_type ) ) {
									$coupon_type .= __( ' & ', 'woocommerce-smart-coupons' );
								}
								$coupon_type .= __( 'Free Shipping', 'woocommerce-smart-coupons' );
							}

							if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
								$expiry_date = ( is_callable( array( $expiry_date, 'getTimestamp' ) ) ) ? $expiry_date->getTimestamp() : null;
							} elseif ( ! is_int( $expiry_date ) ) {
								$expiry_date = $this->strtotime( $expiry_date );
							}

							if ( ! empty( $expiry_date ) && is_int( $expiry_date ) ) {
								$expiry_time = ( $this->is_callable( $coupon, 'get_meta' ) ) ? (int) $coupon->get_meta( 'wc_sc_expiry_time' ) : (int) get_post_meta( $coupon_id, 'wc_sc_expiry_time', true );
								if ( ! empty( $expiry_time ) ) {
									$expiry_date += $expiry_time; // Adding expiry time to expiry date.
								}
							}

							$coupon_description = '';
							if ( ! empty( $coupon_post->post_excerpt ) && 'yes' === $show_coupon_description ) {
								$coupon_description = $coupon_post->post_excerpt;
							}

							$is_percent = $this->is_percent_coupon( array( 'coupon_object' => $coupon ) );

							$args = array(
								'coupon_object'      => $coupon,
								'coupon_amount'      => $coupon_amount,
								'amount_symbol'      => ( true === $is_percent ) ? '%' : get_woocommerce_currency_symbol(),
								'discount_type'      => wp_strip_all_tags( $coupon_type ),
								'coupon_description' => ( ! empty( $coupon_description ) ) ? $coupon_description : wp_strip_all_tags( $this->generate_coupon_description( array( 'coupon_object' => $coupon ) ) ),
								'coupon_code'        => $coupon_code,
								'coupon_expiry'      => ( ! empty( $expiry_date ) ) ? $this->get_expiration_format( $expiry_date ) : __( 'Never expires', 'woocommerce-smart-coupons' ),
								'thumbnail_src'      => $this->get_coupon_design_thumbnail_src(
									array(
										'design'        => $design,
										'coupon_object' => $coupon,
									)
								),
								'classes'            => '',
								'template_id'        => $design,
								'is_percent'         => $is_percent,
							);

							?>
							<div>
								<details>
									<summary class="generated_coupon_summary">
										<?php wc_get_template( 'coupon-design/' . $design . '.php', $args, '', plugin_dir_path( WC_SC_PLUGIN_FILE ) . 'templates/' ); ?>
									</summary>
									<div class="generated_coupon_details">
										<p><strong><?php echo esc_html__( 'Sender', 'woocommerce-smart-coupons' ); ?>:</strong> <?php echo esc_html( $from ); ?></p>
										<p><strong><?php echo esc_html__( 'Receiver', 'woocommerce-smart-coupons' ); ?>:</strong> <?php echo esc_html( $coupon_data['email'] ); ?></p>
										<?php if ( ! empty( $coupon_data['message'] ) ) { ?>
											<p><strong><?php echo esc_html__( 'Message', 'woocommerce-smart-coupons' ); ?>:</strong> <?php echo esc_html( $coupon_data['message'] ); ?></p>
										<?php } ?>
									</div>
								</details>
							</div>
							<?php
						}
					}
					?>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * HTML code to display generated coupon's details is table layout
		 *
		 * @param array $generated_coupon_data Associative array of generated coupon's details.
		 */
		public function get_generated_coupon_data_table( $generated_coupon_data = array() ) {
			if ( empty( $generated_coupon_data ) ) {
				return;
			}
			$email = $this->get_current_user_email();
			?>
				<div class="woocommerce_order_items_wrapper">
					<table class="woocommerce_order_items">
						<thead>
							<tr>
								<th><?php echo esc_html__( 'Code', 'woocommerce-smart-coupons' ); ?></th>
								<th><?php echo esc_html__( 'Amount', 'woocommerce-smart-coupons' ); ?></th>
								<th><?php echo esc_html__( 'Receiver', 'woocommerce-smart-coupons' ); ?></th>
								<th><?php echo esc_html__( 'Message', 'woocommerce-smart-coupons' ); ?></th>
								<th><?php echo esc_html__( 'Sender', 'woocommerce-smart-coupons' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ( $generated_coupon_data as $from => $data ) {
								$email = ( ! empty( $email ) ) ? $email : $from;
								foreach ( $data as $coupon_data ) {
									if ( ! is_admin() && ! empty( $coupon_data['email'] ) && $coupon_data['email'] !== $email ) {
										continue;
									}
									echo '<tr class="coupon_received_row">';
									echo '<td>' . esc_html( $coupon_data['code'] ) . '</td>';
									echo '<td>' . wc_price( $coupon_data['amount'] ) . '</td>'; // phpcs:ignore
									echo '<td>' . esc_html( $coupon_data['email'] ) . '</td>';
									echo '<td>' . esc_html( $coupon_data['message'] ) . '</td>';
									echo '<td>' . esc_html( $from ) . '</td>';
									echo '</tr>';
								}
							}
							?>
						</tbody>
					</table>
				</div>
			<?php
		}

		/**
		 * Get current user's email
		 *
		 * @return string $email
		 */
		public function get_current_user_email() {
			$current_user = wp_get_current_user();
			if ( ! $current_user instanceof WP_User ) {
				return;
			}
			$billing_email = get_user_meta( $current_user->ID, 'billing_email', true );
			$email         = ( ! empty( $billing_email ) ) ? $billing_email : $current_user->user_email;
			return $email;
		}

		/**
		 * Display generated coupons details after Order table
		 *
		 * @param  WC_Order $order         The order.
		 * @param  boolean  $sent_to_admin Whether sent to admin.
		 * @param  boolean  $plain_text    Whether a plain text email.
		 */
		public function generated_coupon_details_after_order_table( $order = false, $sent_to_admin = false, $plain_text = false ) {

			if ( $this->is_wc_gte_30() ) {
				$order_id      = ( is_object( $order ) && is_callable( array( $order, 'get_id' ) ) ) ? $order->get_id() : 0;
				$order_refunds = ( ! empty( $order ) && is_callable( array( $order, 'get_refunds' ) ) ) ? $order->get_refunds() : array();
			} else {
				$order_id      = ( ! empty( $order->id ) ) ? $order->id : 0;
				$order_refunds = ( ! empty( $order->refunds ) ) ? $order->refunds : array();
			}

			if ( ! empty( $order_refunds ) ) {
				return;
			}

			if ( ! empty( $order_id ) ) {
				$this->get_generated_coupon_data( $order_id, '', true, true );
			}
		}

		/**
		 * Display generated coupons details on View Order page
		 *
		 * @param int $order_id The order id.
		 */
		public function generated_coupon_details_view_order( $order_id = 0 ) {
			if ( ! empty( $order_id ) ) {
				$this->get_generated_coupon_data( $order_id, '', true, true );
			}
		}

		/**
		 * Metabox on Order Edit Admin page to show generated coupons during the order
		 */
		public function add_generated_coupon_details() {
			global $post, $theorder;

			if ( is_a( $theorder, 'WC_Order' ) ) {
				$post_type = 'shop_order';
			} elseif ( ! empty( $post->ID ) ) {
				$post_type = ( ! empty( $post->ID ) ) ? $this->get_post_type( $post->ID ) : $this->get_post_type();
			}

			if ( empty( $post_type ) || 'shop_order' !== $post_type ) {
				return;
			}

			$screen = ( $this->is_hpos() ) ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';

			add_meta_box( 'sc-generated-coupon-data', __( 'Generated coupons', 'woocommerce-smart-coupons' ), array( $this, 'sc_generated_coupon_data_metabox' ), $screen, 'normal' );
		}

		/**
		 * Metabox content (Generated coupon's details)
		 */
		public function sc_generated_coupon_data_metabox() {
			global $post, $theorder;
			$order_id = ( is_object( $theorder ) && is_a( $theorder, 'WC_Order' ) && $this->is_callable( $theorder, 'get_id' ) ) ? $theorder->get_id() : ( ! empty( $post->ID ) ? $post->ID : 0 );
			if ( ! empty( $order_id ) ) {
				$this->get_generated_coupon_data( $order_id, '', true, false );
			}
		}

		/**
		 * Modify available variation
		 *
		 * @param array                $found_variation The found variation.
		 * @param WC_Product_Variable  $product The variable product object.
		 * @param WC_Product_Variation $variation The variation object.
		 * @return array
		 */
		public function modify_available_variation( $found_variation = array(), $product = null, $variation = null ) {
			if ( is_a( $product, 'WC_Product_Variable' ) ) {
				if ( $this->is_wc_gte_30() ) {
					$product_id = ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
				} else {
					$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
				}

				$coupons = $this->get_coupon_titles( array( 'product_object' => $variation ) );

				if ( ! empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) ) {
					if ( is_a( $variation, 'WC_Product_Variation' ) ) {
						$found_variation['price_including_tax']      = wc_round_discount( wc_get_price_including_tax( $variation ), 2 );
						$found_variation['price_including_tax_html'] = wc_price( $found_variation['price_including_tax'] );
						$found_variation['price_excluding_tax']      = wc_round_discount( wc_get_price_excluding_tax( $variation ), 2 );
						$found_variation['price_excluding_tax_html'] = wc_price( $found_variation['price_excluding_tax'] );
						if ( is_callable( array( $variation, 'get_regular_price' ) ) ) {
							$regular_price                                       = $variation->get_regular_price();
							$found_variation['regular_price_including_tax']      = wc_round_discount( wc_get_price_including_tax( $variation, array( 'price' => $regular_price ) ), 2 );
							$found_variation['regular_price_including_tax_html'] = wc_price( $found_variation['regular_price_including_tax'] );
							$found_variation['regular_price_excluding_tax']      = wc_round_discount( wc_get_price_excluding_tax( $variation, array( 'price' => $regular_price ) ), 2 );
							$found_variation['regular_price_excluding_tax_html'] = wc_price( $found_variation['regular_price_excluding_tax'] );
						}
					}
				}
			}
			return $found_variation;
		}

		/**
		 * Function to check & enable store notice for coupon
		 */
		public function maybe_enable_store_notice_for_coupon() {
			$coupon_code = get_option( 'smart_coupons_storewide_offer_coupon_code' );
			if ( ! empty( $coupon_code ) ) {
				$coupon_id     = wc_get_coupon_id_by_code( $coupon_code );
				$coupon        = new WC_Coupon( $coupon_id );
				$coupon_status = ( $this->is_wc_greater_than( '6.1.2' ) && $this->is_callable( $coupon, 'get_status' ) ) ? $coupon->get_status() : get_post_status( $coupon_id );
				if ( 'publish' === $coupon_status && ! is_store_notice_showing() ) {
					update_option( 'woocommerce_demo_store', 'yes' );
				} elseif ( 'publish' !== $coupon_status && is_store_notice_showing() ) {
					update_option( 'woocommerce_demo_store', 'no' );
				}
			}
		}

		/**
		 * Check if the store notice needs any change & apply the change if required
		 *
		 * @param string $displayed_notice The notice to be displayed.
		 * @param string $raw_notice The raw notice.
		 * @return string
		 */
		public function maybe_change_store_notice( $displayed_notice = '', $raw_notice = '' ) {
			$current_currency = get_woocommerce_currency();
			$base_currency    = get_option( 'woocommerce_currency' );
			if ( $base_currency !== $current_currency ) {
				$storewide_offer_coupon_code = get_option( 'smart_coupons_storewide_offer_coupon_code' );
				if ( ! empty( $storewide_offer_coupon_code ) && false !== stripos( $raw_notice, '<code>' . $storewide_offer_coupon_code . '</code>' ) ) {
					if ( ! empty( $this->sc_coupon_exists( $storewide_offer_coupon_code ) ) ) {
						$coupon     = new WC_Coupon( $storewide_offer_coupon_code );
						$is_percent = $this->is_percent_coupon( array( 'coupon_object' => $coupon ) );
						if ( false === $is_percent ) {
							$coupon_amount    = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_amount' ) ) ) ? $coupon->get_amount( 'edit' ) : 0;
							$search           = get_woocommerce_currency_symbol( $base_currency ) . $coupon_amount;
							$replace          = get_woocommerce_currency_symbol( $current_currency ) . $this->convert_price( $coupon_amount, $current_currency, $base_currency );
							$displayed_notice = str_replace( $search, $replace, $displayed_notice );
						}
					}
				}
			}
			return $displayed_notice;
		}

	}

}

WC_SC_Display_Coupons::get_instance();
