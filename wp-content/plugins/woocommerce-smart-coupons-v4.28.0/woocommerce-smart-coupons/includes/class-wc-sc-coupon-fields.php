<?php
/**
 * Smart Coupons fields in coupons
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.4.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupon_Fields' ) ) {

	/**
	 * Class for handling Smart Coupons' field in coupons
	 */
	class WC_SC_Coupon_Fields {

		/**
		 * Variable to hold instance of WC_SC_Coupon_Fields
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'add_meta_boxes', array( $this, 'woocommerce_smart_coupons_add_meta_box' ) );
			add_action( 'woocommerce_coupon_options', array( $this, 'woocommerce_smart_coupon_options' ), 10, 2 );
			add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'sc_woocommerce_coupon_options_usage_restriction' ), 10, 2 );
			add_filter( 'woocommerce_coupon_discount_types', array( $this, 'add_smart_coupon_discount_type' ) );
			add_action( 'save_post', array( $this, 'woocommerce_process_smart_coupon_meta' ), 10, 2 );

			add_filter( 'woocommerce_coupon_data_tabs', array( $this, 'smart_coupons_data_tabs' ) );
			add_action( 'woocommerce_coupon_data_panels', array( $this, 'smart_coupons_data_panels' ), 10, 2 );

			add_action( 'wc_sc_enhanced_select_script_start', array( $this, 'enhanced_select_script_start' ) );
			add_action( 'wc_sc_enhanced_select_script_end', array( $this, 'enhanced_select_script_end' ) );

			add_action( 'wp_ajax_wc_sc_json_search_products_and_variations', array( $this, 'wc_sc_json_search_products_and_variations' ) );
			add_filter( 'woocommerce_json_search_found_products', array( $this, 'exclude_variation_parent' ) );

			add_action( 'admin_footer', array( $this, 'enqueue_styles_scripts' ) );

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
		 * Get single instance of WC_SC_Coupon_Fields
		 *
		 * @return WC_SC_Coupon_Fields Singleton object of WC_SC_Coupon_Fields
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Add coupon shareable link metabox on coupon edit page.
		 */
		public function woocommerce_smart_coupons_add_meta_box() {
			global $pagenow, $typenow;

			if ( 'post.php' === $pagenow && 'shop_coupon' === $typenow ) {
				add_meta_box( 'sc-share-link', __( 'Coupon shareable link', 'woocommerce-smart-coupons' ), array( $this, 'wc_sc_shareable_link' ), 'shop_coupon', 'side', 'default' );
			}
		}

		/**
		 * Content for coupon shareable link metabox on coupon edit page.
		 */
		public function wc_sc_shareable_link() {
			global $post;

			if ( empty( $post->post_title ) ) {
				return;
			}

			$shop_page_id = get_option( 'woocommerce_shop_page_id', 0 );

			if ( ! empty( $shop_page_id ) ) {
				$shop_page_id = 'shop';
			} else {
				$home_url     = home_url();
				$shop_page_id = ( function_exists( 'wpcom_vip_url_to_postid' ) ) ? wpcom_vip_url_to_postid( $home_url ) : url_to_postid( $home_url ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.url_to_postid_url_to_postid
			}

			if ( empty( $shop_page_id ) ) {
				$shop_page_id = 'cart';
			}

			$coupon_share_url = add_query_arg(
				array(
					'coupon-code' => $post->post_title,
					'sc-page'     => $shop_page_id,
				),
				home_url( '/' )
			);

			?>
			<h2 style="padding: unset;">
				<?php
					echo esc_html__( 'Copy the following link and share it to apply this coupon via URL.', 'woocommerce-smart-coupons' );
				?>
			</h2><br>
			<div class="sc-shareable-link">
				<textarea id="coupon-link" readonly="readonly" rows="1" cols="25"><?php echo esc_html( $coupon_share_url ); ?></textarea>
				<br><br>
				<div class="copy-button" style="float: right;">
					<a class="button button-primary sc-click-to-copy-btn" id="sc-click-to-copy-btn" onclick="sc_copy_coupon_link_to_clipboard()" data-clipboard-action="copy" data-clipboard-target="#coupon-link"><?php echo esc_html__( 'Click to copy', 'woocommerce-smart-coupons' ); ?></a>
				</div>
				<br><br>
				<div class="sc-multiple-coupons">
					<p><?php echo esc_html__( 'You can also apply multiple coupon codes via a single URL. For example:', 'woocommerce-smart-coupons' ); ?></p>
					<p>
						<code>
						<?php
							echo esc_html(
								add_query_arg(
									array(
										'coupon-code' => 'coupon1,coupon2,coupon3',
										'sc-page'     => $shop_page_id,
									),
									home_url( '/' )
								)
							);
						?>
						</code>
					</p>
				</div>
			</div>
			<?php
		}

		/**
		 * Function to display the coupon data meta box.
		 *
		 * @param  int       $coupon_id Coupon ID.
		 * @param  WC_Coupon $coupon Coupon Object.
		 */
		public function woocommerce_smart_coupon_options( $coupon_id = 0, $coupon = null ) {
			global $post;

			$is_page_bulk_generate = false;

			$get_page = ( ! empty( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore

			if ( 'wc-smart-coupons' === $get_page ) {
				$is_page_bulk_generate = true;
			}

			?>
			<script type="text/javascript">
				jQuery(function(){
					var customerEmails;
					var showHideSmartCouponsOptions = function() {
						let discount_type = jQuery('select#discount_type').val();
						if ( 'smart_coupon' === discount_type ) {
							jQuery('input#is_pick_price_of_product').parent('p').show();
							jQuery('input#auto_generate_coupon').attr('checked', 'checked');
							jQuery('div#for_prefix_suffix').show();
							jQuery('div#sc_is_visible_storewide').hide();
							jQuery("p.auto_generate_coupon_field").hide();
							jQuery( '.wc_sc_max_discount_field' ).hide();
							jQuery('p.sc_coupon_validity').show();
						} else {
							jQuery('input#is_pick_price_of_product').parent('p').hide();
							jQuery('div#sc_is_visible_storewide').show();
							customerEmails = jQuery('input#customer_email').val();
							if ( customerEmails != undefined || customerEmails != '' ) {
								customerEmails = customerEmails.trim();
								if ( customerEmails == '' ) {
									jQuery('input#sc_is_visible_storewide').parent('p').show();
								} else {
									jQuery('input#sc_is_visible_storewide').parent('p').hide();
								}
							}
							jQuery("p.auto_generate_coupon_field").show();
							if (jQuery("input#auto_generate_coupon").is(":checked")){
								jQuery('p.sc_coupon_validity').show();
							} else {
								jQuery('p.sc_coupon_validity').hide();
							}
							if( 'percent' === discount_type ) {
								jQuery( '.wc_sc_max_discount_field' ).show();
							} else {
								jQuery( '.wc_sc_max_discount_field' ).hide();
							}
						}
					};

					var showHidePrefixSuffix = function() {
						<?php if ( ! $is_page_bulk_generate ) { ?>
							if (jQuery("#auto_generate_coupon").is(":checked")){
								// show the hidden div.
								jQuery("div#for_prefix_suffix").show("slow");
								jQuery("div#sc_is_visible_storewide").hide("slow");
								jQuery('p.sc_coupon_validity').show("slow");
							} else {
								// otherwise, hide it.
								jQuery("div#for_prefix_suffix").hide("slow");
								jQuery("div#sc_is_visible_storewide").show("slow");
								jQuery('p.sc_coupon_validity').hide("slow");
							}
						<?php } ?>
					}

					setTimeout(function(){
						showHideSmartCouponsOptions();
						showHidePrefixSuffix();
					}, 100);

					jQuery("#auto_generate_coupon").on('change', function(){
						showHidePrefixSuffix();
					});

					jQuery('select#discount_type').on('change', function(){
						showHideSmartCouponsOptions();
						showHidePrefixSuffix();
					});

					jQuery('input#customer_email').on('keyup', function(){
						showHideSmartCouponsOptions();
					});

					<?php if ( $this->is_wc_gte_32() ) { ?>
					let wc_sc_expiry_time = parseInt( jQuery('#wc_sc_expiry_time').val() );
					let expiry_time_string = '';
					if( Number.isInteger( wc_sc_expiry_time ) ) {
						let expiry_minutes = wc_sc_expiry_time / 60; // Expiry time is stored in seconds.
						let expiry_hours = Math.floor( expiry_minutes / 60 ); // Get total hours from total seconds.
						expiry_minutes = expiry_minutes % 60; // Get remaining minutes after removing hours from total seconds.
						expiry_hours = expiry_hours < 10 ? '0' + expiry_hours : expiry_hours; // Add leading zero to hours to avoid timepicker time not preselected issue when hours < 10.
						expiry_minutes = expiry_minutes < 10 ? '0' + expiry_minutes : expiry_minutes; // Add leading zero to minutes to avoid timepicker time not preselected issue when minutes < 10.
						expiry_time_string = expiry_hours + ':' + expiry_minutes;
					}

					jQuery('#wc_sc_expiry_time_picker').timepicker({
						timeInput: true,
					}).val(expiry_time_string);

					jQuery('#wc_sc_expiry_time_picker').on('change', function(){
						let expiry_time = jQuery(this).val();
						if( expiry_time !== '' && expiry_time.indexOf(':') > 0 ) {
							expiry_time = expiry_time.split(':');
							let expiry_hours = parseInt( expiry_time[0] );
							let expiry_minutes = parseInt( expiry_time[1] );
							if( Number.isInteger( expiry_hours ) && Number.isInteger( expiry_minutes ) ) {
								expiry_time = expiry_hours * 60 * 60 + expiry_minutes * 60;
							}
						}
						jQuery('#wc_sc_expiry_time').val( expiry_time );
					});
					<?php } ?>
				});
			</script>
			<div class="options_group smart-coupons-field" style="border-top: 1px solid #eee;">
				<?php
				// Coupon expiry time feature is compatible with WooCommerce 3.2.0 and above.
				if ( $this->is_wc_gte_32() ) {
					woocommerce_wp_hidden_input(
						array(
							'id' => 'wc_sc_expiry_time',
						)
					);

					woocommerce_wp_text_input(
						array(
							'id'                => 'wc_sc_expiry_time_picker',
							'label'             => __( 'Coupon expiry time', 'woocommerce-smart-coupons' ),
							'placeholder'       => esc_attr__( 'HH:MM', 'woocommerce-smart-coupons' ),
							'description'       => __( 'Time after which coupon will be expired. This will work in conjunction with Coupon expiry date.', 'woocommerce-smart-coupons' ),
							'type'              => 'text',
							'desc_tip'          => true,
							'custom_attributes' => array(
								'autocomplete' => 'off',
							),
						)
					);
				}

				// Max discount field for percentage type coupon.
				woocommerce_wp_text_input(
					array(
						'id'                => 'wc_sc_max_discount',
						'label'             => __( 'Max discount', 'woocommerce-smart-coupons' ),
						'placeholder'       => esc_attr__( 'Unlimited discount', 'woocommerce-smart-coupons' ),
						'description'       => __( 'The maximum discount this coupon can give on a cart.', 'woocommerce-smart-coupons' ),
						'type'              => 'number',
						'desc_tip'          => true,
						'custom_attributes' => array(
							'step' => 'any',
							'min'  => 0,
						),
					)
				);

				woocommerce_wp_checkbox(
					array(
						'id'          => 'sc_restrict_to_new_user',
						'label'       => __( 'For new user only?', 'woocommerce-smart-coupons' ),
						'description' => __( 'When checked, this coupon will be valid for the user\'s first order on the store.', 'woocommerce-smart-coupons' ),
					)
				);

				$sc_coupon_validity = get_post_meta( $post->ID, 'sc_coupon_validity', true );
				$validity_suffix    = get_post_meta( $post->ID, 'validity_suffix', true );
				?>
				<p class="form-field sc_coupon_validity ">
					<label for="sc_coupon_validity"><?php echo esc_html__( 'Valid for', 'woocommerce-smart-coupons' ); ?></label>
					<input type="number" class="short" style="width: 15%;" name="sc_coupon_validity" id="sc_coupon_validity" value="<?php echo esc_attr( $sc_coupon_validity ); ?>" placeholder="0" min="1">&nbsp;
					<select name="validity_suffix" style="float: none;">
						<option value="days" <?php selected( $validity_suffix, 'days' ); ?>><?php echo esc_html__( 'Days', 'woocommerce-smart-coupons' ); ?></option>
						<option value="weeks" <?php selected( $validity_suffix, 'weeks' ); ?>><?php echo esc_html__( 'Weeks', 'woocommerce-smart-coupons' ); ?></option>
						<option value="months" <?php selected( $validity_suffix, 'months' ); ?>><?php echo esc_html__( 'Months', 'woocommerce-smart-coupons' ); ?></option>
						<option value="years" <?php selected( $validity_suffix, 'years' ); ?>><?php echo esc_html__( 'Years', 'woocommerce-smart-coupons' ); ?></option>
					</select>
					<span class="description"><?php echo esc_html__( '(Used only for auto-generated coupons)', 'woocommerce-smart-coupons' ); ?></span>
				</p>
				<?php
				if ( ! $is_page_bulk_generate ) {
					woocommerce_wp_checkbox(
						array(
							'id'          => 'is_pick_price_of_product',
							'label'       => __( 'Coupon value same as product\'s price?', 'woocommerce-smart-coupons' ),
							'description' => __( 'When checked, generated coupon\'s value will be same as product\'s price', 'woocommerce-smart-coupons' ),
						)
					);
					woocommerce_wp_checkbox(
						array(
							'id'          => 'auto_generate_coupon',
							'label'       => __( 'Auto generate new coupons with each item', 'woocommerce-smart-coupons' ),
							'description' => __( 'Generate exact copy of this coupon with unique coupon code for each purchased product (needs this coupon to be linked with that product)', 'woocommerce-smart-coupons' ),
						)
					);
				}

				echo '<div id="for_prefix_suffix">';

				?>
				<p class="form-field coupon_title_prefix_suffix_field ">
					<?php
					$coupon_title_prefix = '';
					$coupon_title_suffix = '';
					if ( ! empty( $post->ID ) ) {
						$coupon_title_prefix = get_post_meta( $post->ID, 'coupon_title_prefix', true );
						$coupon_title_suffix = get_post_meta( $post->ID, 'coupon_title_suffix', true );
					}
					?>
					<label for="coupon_title_prefix"><?php echo esc_html__( 'Coupon code format', 'woocommerce-smart-coupons' ); ?></label>
					<input type="text" class="short" style="width: 15%;" name="coupon_title_prefix" id="coupon_title_prefix" value="<?php echo esc_attr( $coupon_title_prefix ); ?>" placeholder="<?php echo esc_attr__( 'Prefix', 'woocommerce-smart-coupons' ); ?>">&nbsp;
					<code>coupon_code</code>&nbsp;
					<input type="text" class="short" style="float: initial; width: 15%;" name="coupon_title_suffix" id="coupon_title_suffix" value="<?php echo esc_attr( $coupon_title_suffix ); ?>" placeholder="<?php echo esc_attr__( 'Suffix', 'woocommerce-smart-coupons' ); ?>">
					<span class="description"><?php echo esc_html__( '(We recommend up to three letters for prefix/suffix)', 'woocommerce-smart-coupons' ); ?></span>
				</p>
				<?php

				echo '</div>';

				if ( ! $is_page_bulk_generate ) {

					echo '<div id="sc_is_visible_storewide">';
					// for disabling e-mail restriction.
					woocommerce_wp_checkbox(
						array(
							'id'          => 'sc_is_visible_storewide',
							'label'       => __( 'Show on cart, checkout', 'woocommerce-smart-coupons' ) . '<br>' . __( 'and my account?', 'woocommerce-smart-coupons' ),
							'description' => __( 'When checked, this coupon will be visible on cart/checkout page for everyone', 'woocommerce-smart-coupons' ),
						)
					);

					echo '</div>';

				}
				?>
			</div>
			<?php

		}

		/**
		 * Function add additional field to disable email restriction
		 *
		 * @param integer   $coupon_id The coupon id.
		 * @param WC_Coupon $coupon    The coupon object.
		 */
		public function sc_woocommerce_coupon_options_usage_restriction( $coupon_id = 0, $coupon = null ) {
			?>
			<div class="options_group smart-coupons-field">
				<?php
					woocommerce_wp_checkbox(
						array(
							'id'          => 'sc_disable_email_restriction',
							'label'       => __( 'Disable email restriction?', 'woocommerce-smart-coupons' ),
							'description' => __( 'Do not restrict auto-generated coupons to buyer/receiver email, anyone with coupon code can use it', 'woocommerce-smart-coupons' ),
						)
					);
				?>
			</div>
			<?php
		}

		/**
		 * Function to process smart coupon meta
		 *
		 * @param int    $post_id The post id.
		 * @param object $post The post object.
		 */
		public function woocommerce_process_smart_coupon_meta( $post_id, $post ) {
			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( is_int( wp_is_post_revision( $post ) ) ) {
				return;
			}
			if ( is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}
			if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( wc_clean( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) { // phpcs:ignore
				return;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			if ( 'shop_coupon' !== $post->post_type ) {
				return;
			}

			$post_sc_restrict_to_new_user       = ( isset( $_POST['sc_restrict_to_new_user'] ) ) ? wc_clean( wp_unslash( $_POST['sc_restrict_to_new_user'] ) ) : 'no';               // phpcs:ignore
			$post_auto_generate_coupon          = ( isset( $_POST['auto_generate_coupon'] ) ) ? wc_clean( wp_unslash( $_POST['auto_generate_coupon'] ) ) : 'no';                     // phpcs:ignore
			$post_usage_limit_per_user          = ( isset( $_POST['usage_limit_per_user'] ) ) ? wc_clean( wp_unslash( $_POST['usage_limit_per_user'] ) ) : '';                       // phpcs:ignore
			$post_limit_usage_to_x_items        = ( isset( $_POST['limit_usage_to_x_items'] ) ) ? wc_clean( wp_unslash( $_POST['limit_usage_to_x_items'] ) ) : '';                   // phpcs:ignore
			$post_coupon_title_prefix           = ( isset( $_POST['coupon_title_prefix'] ) ) ? wc_clean( wp_unslash( $_POST['coupon_title_prefix'] ) ) : '';                         // phpcs:ignore
			$post_coupon_title_suffix           = ( isset( $_POST['coupon_title_suffix'] ) ) ? wc_clean( wp_unslash( $_POST['coupon_title_suffix'] ) ) : '';                         // phpcs:ignore
			$post_sc_coupon_validity            = ( isset( $_POST['sc_coupon_validity'] ) ) ? wc_clean( wp_unslash( $_POST['sc_coupon_validity'] ) ) : '';                           // phpcs:ignore
			$post_validity_suffix               = ( isset( $_POST['validity_suffix'] ) ) ? wc_clean( wp_unslash( $_POST['validity_suffix'] ) ) : 'days';                             // phpcs:ignore
			$post_sc_is_visible_storewide       = ( isset( $_POST['sc_is_visible_storewide'] ) ) ? wc_clean( wp_unslash( $_POST['sc_is_visible_storewide'] ) ) : 'no';               // phpcs:ignore
			$post_sc_disable_email_restriction  = ( isset( $_POST['sc_disable_email_restriction'] ) ) ? wc_clean( wp_unslash( $_POST['sc_disable_email_restriction'] ) ) : 'no';     // phpcs:ignore
			$post_is_pick_price_of_product      = ( isset( $_POST['is_pick_price_of_product'] ) ) ? wc_clean( wp_unslash( $_POST['is_pick_price_of_product'] ) ) : 'no';             // phpcs:ignore
			$post_wc_sc_add_product_ids         = ( isset( $_POST['wc_sc_add_product_ids'] ) ) ? wc_clean( wp_unslash( $_POST['wc_sc_add_product_ids'] ) ) : array();                // phpcs:ignore
			$post_wc_sc_add_product_qty         = ( isset( $_POST['wc_sc_add_product_qty'] ) ) ? wc_clean( wp_unslash( $_POST['wc_sc_add_product_qty'] ) ) : 1;                      // phpcs:ignore
			$post_wc_sc_product_discount_amount = ( isset( $_POST['wc_sc_product_discount_amount'] ) ) ? wc_clean( wp_unslash( $_POST['wc_sc_product_discount_amount'] ) ) : '';     // phpcs:ignore
			$post_wc_sc_product_discount_type   = ( isset( $_POST['wc_sc_product_discount_type'] ) ) ? wc_clean( wp_unslash( $_POST['wc_sc_product_discount_type'] ) ) : 'percent';  // phpcs:ignore
			$post_original_post_status          = ( isset( $_POST['original_post_status'] ) ) ? wc_clean( wp_unslash( $_POST['original_post_status'] ) ) : '';                       // phpcs:ignore
			$post_post_status                   = ( isset( $_POST['post_status'] ) ) ? wc_clean( wp_unslash( $_POST['post_status'] ) ) : '';                                         // phpcs:ignore
			$post_discount_type                 = ( isset( $_POST['discount_type'] ) ) ? wc_clean( wp_unslash( $_POST['discount_type'] ) ) : '';                                     // phpcs:ignore
			$post_coupon_amount                 = ( isset( $_POST['coupon_amount'] ) ) ? wc_clean( wp_unslash( $_POST['coupon_amount'] ) ) : 0;                                      // phpcs:ignore

			if ( isset( $_POST['sc_restrict_to_new_user'] ) ) { // phpcs:ignore
				update_post_meta( $post_id, 'sc_restrict_to_new_user', $post_sc_restrict_to_new_user );
			} else {
				update_post_meta( $post_id, 'sc_restrict_to_new_user', 'no' );
			}

			if ( isset( $_POST['auto_generate_coupon'] ) ) { // phpcs:ignore
				update_post_meta( $post_id, 'auto_generate_coupon', $post_auto_generate_coupon );
			} else {
				if ( get_post_meta( $post_id, 'discount_type', true ) === 'smart_coupon' ) {
					update_post_meta( $post_id, 'auto_generate_coupon', 'yes' );
				} else {
					update_post_meta( $post_id, 'auto_generate_coupon', 'no' );
				}
			}

			if ( isset( $_POST['usage_limit_per_user'] ) ) { // phpcs:ignore
				update_post_meta( $post_id, 'usage_limit_per_user', $post_usage_limit_per_user );
			}

			if ( isset( $_POST['limit_usage_to_x_items'] ) ) { // phpcs:ignore
				update_post_meta( $post_id, 'limit_usage_to_x_items', $post_limit_usage_to_x_items );
			}

			if ( get_post_meta( $post_id, 'discount_type', true ) === 'smart_coupon' ) {
				update_post_meta( $post_id, 'apply_before_tax', 'no' );
			}

			if ( isset( $_POST['coupon_title_prefix'] ) ) { // phpcs:ignore
				update_post_meta( $post_id, 'coupon_title_prefix', $post_coupon_title_prefix );
			}

			if ( isset( $_POST['coupon_title_suffix'] ) ) { // phpcs:ignore
				update_post_meta( $post_id, 'coupon_title_suffix', $post_coupon_title_suffix );
			}

			if ( isset( $_POST['sc_coupon_validity'] ) ) { // phpcs:ignore
				update_post_meta( $post_id, 'sc_coupon_validity', $post_sc_coupon_validity );
				update_post_meta( $post_id, 'validity_suffix', $post_validity_suffix );
			}

			if ( isset( $_POST['sc_is_visible_storewide'] ) ) { // phpcs:ignore
				update_post_meta( $post_id, 'sc_is_visible_storewide', $post_sc_is_visible_storewide );
			} else {
				update_post_meta( $post_id, 'sc_is_visible_storewide', 'no' );
			}

			if ( isset( $_POST['sc_disable_email_restriction'] ) ) { // phpcs:ignore
				update_post_meta( $post_id, 'sc_disable_email_restriction', $post_sc_disable_email_restriction );
			} else {
				update_post_meta( $post_id, 'sc_disable_email_restriction', 'no' );
			}

			if ( isset( $_POST['is_pick_price_of_product'] ) ) { // phpcs:ignore
				update_post_meta( $post_id, 'is_pick_price_of_product', $post_is_pick_price_of_product );
			} else {
				update_post_meta( $post_id, 'is_pick_price_of_product', 'no' );
			}

			if ( isset( $_POST['wc_sc_add_product_ids'] ) ) { // phpcs:ignore
				if ( $this->is_wc_gte_30() ) {
					$product_ids = $post_wc_sc_add_product_ids;
				} else {
					$product_ids = array_filter( array_map( 'trim', explode( ',', $post_wc_sc_add_product_ids ) ) );
				}
				$add_product_details = array();
				if ( ! empty( $product_ids ) ) {
					$quantity        = $post_wc_sc_add_product_qty;
					$discount_amount = $post_wc_sc_product_discount_amount;
					$discount_type   = $post_wc_sc_product_discount_type;
					foreach ( $product_ids as $id ) {
						$data                    = array();
						$data['product_id']      = $id;
						$data['quantity']        = $quantity;
						$data['discount_amount'] = $discount_amount;
						$data['discount_type']   = $discount_type;
						$add_product_details[]   = $data;
					}
				}
				update_post_meta( $post_id, 'wc_sc_add_product_details', $add_product_details );
			} else {
				update_post_meta( $post_id, 'wc_sc_add_product_details', array() );
			}

			if( isset( $_POST['wc_sc_max_discount'] ) ) { // phpcs:ignore
				$max_discount = wc_clean( wp_unslash( $_POST['wc_sc_max_discount'] ) ); // phpcs:ignore
				update_post_meta( $post_id, 'wc_sc_max_discount', $max_discount );
			}

			if( isset( $_POST['wc_sc_expiry_time'] ) ) { // phpcs:ignore
				$expiry_time = wc_clean( wp_unslash( $_POST['wc_sc_expiry_time'] ) ); // phpcs:ignore
				update_post_meta( $post_id, 'wc_sc_expiry_time', $expiry_time );
			}

			if ( ! empty( $post_discount_type ) && 'smart_coupon' === $post_discount_type && ! empty( $post_original_post_status ) && 'auto-draft' === $post_original_post_status && ! empty( $post_coupon_amount ) ) {
				update_post_meta( $post_id, 'wc_sc_original_amount', $post_coupon_amount );
			}

		}

		/**
		 * Add a tab in coupon data metabox
		 *
		 * @param  array $tabs Existing tabs.
		 * @return array $tabs With additional tab
		 */
		public function smart_coupons_data_tabs( $tabs = array() ) {

			$tabs['wc_sc_actions'] = array(
				'label'  => __( 'Actions', 'woocommerce-smart-coupons' ),
				'target' => 'wc_smart_coupons_actions',
				'class'  => '',
			);

			return $tabs;
		}

		/**
		 * Panel for Smart Coupons additional data fields
		 *
		 * @param integer   $coupon_id The coupon id.
		 * @param WC_Coupon $coupon The coupon object.
		 */
		public function smart_coupons_data_panels( $coupon_id = 0, $coupon = null ) {

			$add_product_details = get_post_meta( $coupon_id, 'wc_sc_add_product_details', true );
			$add_product_qty     = ( isset( $add_product_details[0]['quantity'] ) ) ? $add_product_details[0]['quantity'] : 1;
			$discount_amount     = ( isset( $add_product_details[0]['discount_amount'] ) && '' !== $add_product_details[0]['discount_amount'] ) ? $add_product_details[0]['discount_amount'] : '';
			$discount_type       = ( ! empty( $add_product_details[0]['discount_type'] ) ) ? $add_product_details[0]['discount_type'] : 'percent';

			$is_js_started = did_action( 'wc_sc_enhanced_select_script_start' );
			if ( 0 === $is_js_started ) {
				do_action( 'wc_sc_enhanced_select_script_start' );
			}
			?>
			<div id="wc_smart_coupons_actions" class="panel woocommerce_options_panel">
				<div class="options_group smart-coupons-field">
					<p><strong><?php echo esc_html__( 'After applying the coupon do these also', 'woocommerce-smart-coupons' ); ?></strong></p>
					<p class="form-field">
						<label><?php echo esc_html__( 'Add products to cart', 'woocommerce-smart-coupons' ); ?></label>
						<?php $product_ids = ( ! empty( $add_product_details ) ) ? wp_list_pluck( $add_product_details, 'product_id' ) : array(); ?>
						<?php if ( $this->is_wc_gte_30() ) { ?>
							<select class="select2_search_products_coupons" style="width: 50%;" multiple="multiple" id="wc_sc_add_product_ids" name="wc_sc_add_product_ids[]" data-placeholder="<?php echo esc_attr__( 'Search for a product&hellip;', 'woocommerce-smart-coupons' ); ?>" data-action="wc_sc_json_search_products_and_variations" data-security="<?php echo esc_attr( wp_create_nonce( 'search-products' ) ); ?>" >
								<?php
								if ( ! empty( $product_ids ) ) {
									$product_ids = array_filter( array_map( 'trim', $product_ids ) );
									foreach ( $product_ids as $product_id ) {
										$product = wc_get_product( $product_id );
										if ( is_object( $product ) ) {
											echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
										}
									}
								}
								?>
							</select>
						<?php } else { ?>
							<?php
								$json_products = array();

							if ( ! empty( $product_ids ) ) {
								$product_ids = array_filter( array_map( 'trim', $product_ids ) );
								foreach ( $product_ids as $product_id ) {
									$product = wc_get_product( $product_id );
									if ( is_object( $product ) ) {
										$json_products[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
									}
								}
							}
							?>
							<input type="hidden" class="select2_search_products_coupons" style="width: 50%;" id="wc_sc_add_product_ids" name="wc_sc_add_product_ids[]" data-placeholder="<?php echo esc_attr__( 'Search for a product&hellip;', 'woocommerce-smart-coupons' ); ?>" data-action="wc_sc_json_search_products_and_variations" data-multiple="true" data-selected="<?php echo esc_attr( wp_json_encode( $json_products ) ); ?>" value="<?php echo esc_attr( implode( ',', array_keys( $json_products ) ) ); // phpcs:ignore ?>" data-security="<?php echo esc_attr( wp_create_nonce( 'search-products' ) ); ?>"/>
						<?php } ?>
					</p>
					<p class="form-field">
						<label><?php echo esc_html__( 'each with quantity', 'woocommerce-smart-coupons' ); ?></label>
						<input type="number" step="1" name="wc_sc_add_product_qty" value="<?php echo ( '' !== $add_product_qty ) ? esc_attr( $add_product_qty ) : 1; ?>" placeholder="<?php echo esc_attr__( '1', 'woocommerce-smart-coupons' ); ?>" style="width: 5em;">
						<?php echo wc_help_tip( esc_html__( 'This much quantity of each product, selected above, will be added to cart.', 'woocommerce-smart-coupons' ) ); // phpcs:ignore ?>
					</p>
					<p class="form-field">
						<label><?php echo esc_html__( 'with discount of', 'woocommerce-smart-coupons' ); ?></label>
						<input type="number" step="0.01" name="wc_sc_product_discount_amount" value="<?php echo ( '' !== $discount_amount ) ? esc_attr( $discount_amount ) : ''; ?>" placeholder="<?php echo esc_attr__( '0.00', 'woocommerce-smart-coupons' ); ?>" style="width: 5em;">
						<select name="wc_sc_product_discount_type">
							<option value="percent" <?php selected( $discount_type, 'percent' ); ?>><?php echo esc_html__( '%', 'woocommerce-smart-coupons' ); ?></option>
							<option value="flat" <?php selected( $discount_type, 'flat' ); ?>><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></option>
						</select>
						<?php echo wc_help_tip( esc_html__( 'When this coupon will be applied, selected products will be added to cart with set discount. If discount is not set, this coupon\'s discount will be applied to these products.', 'woocommerce-smart-coupons' ) ); // phpcs:ignore ?>
					</p>
				</div>
				<?php do_action( 'wc_smart_coupons_actions', $coupon_id, $coupon ); ?>
			</div>
			<?php
			$is_js_ended = did_action( 'wc_sc_enhanced_select_script_end' );
			if ( 0 === $is_js_ended ) {
				do_action( 'wc_sc_enhanced_select_script_end' );
			}
		}

		/**
		 * Enhanced select script start
		 */
		public function enhanced_select_script_start() {

			$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

			// Register scripts.
			if ( ! wp_script_is( 'woocommerce_admin', 'registered' ) ) {
				wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), WC()->version, false );
			}

			if ( $this->is_wc_gte_32() ) {
				wp_register_script( 'wc-sc-select2', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . $suffix . '.js', array( 'jquery' ), WC()->version, false );
			} else {
				wp_register_script( 'wc-sc-select2', WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), WC()->version, false );
			}

			if ( ! wp_script_is( 'wc-enhanced-select', 'registered' ) ) {
				wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'wc-sc-select2' ), WC()->version, false );
			}
			$wc_sc_select_params = array(
				'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce-smart-coupons' ),
				'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce-smart-coupons' ),
				'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce-smart-coupons' ),
				'i18n_ajax_error'           => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce-smart-coupons' ),
				'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce-smart-coupons' ),
				'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce-smart-coupons' ),
				'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce-smart-coupons' ),
				'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce-smart-coupons' ),
				'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce-smart-coupons' ),
				'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce-smart-coupons' ),
				'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce-smart-coupons' ),
				'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce-smart-coupons' ),
				'ajax_url'                  => admin_url( 'admin-ajax.php' ),
				'search_products_nonce'     => wp_create_nonce( 'search-products' ),
				'search_customers_nonce'    => wp_create_nonce( 'search-customers' ),
			);

			$params = array(
				'strings' => array(
					'import_products' => '',
					'export_products' => '',
				),
				'urls'    => array(
					'import_products' => '',
					'export_products' => '',
				),
			);

			wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
			wp_localize_script( 'wc-sc-select2', 'wc_enhanced_select_params', $wc_sc_select_params );

			wp_enqueue_script( 'woocommerce_admin' );
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC()->version );

			wp_enqueue_script( 'wc-sc-select2' );
			wp_enqueue_script( 'wc-enhanced-select' );
			wp_enqueue_style( 'wc-sc-select2', $assets_path . 'css/select2.css', array(), WC()->version );

		}

		/**
		 * Enhanced select script end
		 */
		public function enhanced_select_script_end() {
			?>
			<script type="text/javascript">

				jQuery(function(){

					<?php if ( $this->is_wc_gte_30() ) { ?>

						if ( typeof getEnhancedSelectFormatString == "undefined" ) {
							function getEnhancedSelectFormatString() {
								var formatString = {
									noResults: function() {
										return wc_enhanced_select_params.i18n_no_matches;
									},
									errorLoading: function() {
										return wc_enhanced_select_params.i18n_ajax_error;
									},
									inputTooShort: function( args ) {
										var remainingChars = args.minimum - args.input.length;

										if ( 1 === remainingChars ) {
											return wc_enhanced_select_params.i18n_input_too_short_1;
										}

										return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
									},
									inputTooLong: function( args ) {
										var overChars = args.input.length - args.maximum;

										if ( 1 === overChars ) {
											return wc_enhanced_select_params.i18n_input_too_long_1;
										}

										return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
									},
									maximumSelected: function( args ) {
										if ( args.maximum === 1 ) {
											return wc_enhanced_select_params.i18n_selection_too_long_1;
										}

										return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
									},
									loadingMore: function() {
										return wc_enhanced_select_params.i18n_load_more;
									},
									searching: function() {
										return wc_enhanced_select_params.i18n_searching;
									}
								};

								var language = { 'language' : formatString };

								return language;
							}
						}

						jQuery( '[class= "select2_search_products_coupons"]' ).each( function() {

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
											action:   jQuery( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
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

						function getEnhancedSelectFormatString() {
							var formatString = {
								formatMatches: function( matches ) {
									if ( 1 === matches ) {
										return wc_enhanced_select_params.i18n_matches_1;
									}

									return wc_enhanced_select_params.i18n_matches_n.replace( '%qty%', matches );
								},
								formatNoMatches: function() {
									return wc_enhanced_select_params.i18n_no_matches;
								},
								formatAjaxError: function( jqXHR, textStatus, errorThrown ) {
									return wc_enhanced_select_params.i18n_ajax_error;
								},
								formatInputTooShort: function( input, min ) {
									var number = min - input.length;

									if ( 1 === number ) {
										return wc_enhanced_select_params.i18n_input_too_short_1
									}

									return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', number );
								},
								formatInputTooLong: function( input, max ) {
									var number = input.length - max;

									if ( 1 === number ) {
										return wc_enhanced_select_params.i18n_input_too_long_1
									}

									return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', number );
								},
								formatSelectionTooBig: function( limit ) {
									if ( 1 === limit ) {
										return wc_enhanced_select_params.i18n_selection_too_long_1;
									}

									return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', number );
								},
								formatLoadMore: function( pageNumber ) {
									return wc_enhanced_select_params.i18n_load_more;
								},
								formatSearching: function() {
									return wc_enhanced_select_params.i18n_searching;
								}
							};

							return formatString;
						}

						jQuery( '[class= "select2_search_products_coupons"]' ).each( function() {

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
									data: function( term, page ) {
										return {
											term:     term,
											action:   jQuery( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
											security: jQuery( this ).data( 'security' )
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
									return '<div class=\"selected-option\" data-id=\"' + data.id + '\">' + data.text + '</div>';
								};
							} else {
								select2_args.multiple = false;
								select2_args.initSelection = function( element, callback ) {
									var data = {id: element.val(), text: element.attr( 'data-selected' )};
									return callback( data );
								};
							}

							select2_args = jQuery.extend( select2_args, getEnhancedSelectFormatString() );

							jQuery( this ).select2( select2_args );
						});

					<?php } ?>

				});

			</script>
			<?php
		}

		/**
		 * Search products & only variations
		 */
		public function wc_sc_json_search_products_and_variations() {

			if ( ! class_exists( 'WC_AJAX' ) ) {
				if ( ! defined( 'WC_PLUGIN_FILE' ) ) {
					wp_send_json(
						array(
							'success' => 'false',
							'message' => __( 'Could not locate WooCommerce', 'woocommerce-smart-coupons' ),
						)
					);
				}
				include_once dirname( WC_PLUGIN_FILE ) . '/includes/class-wc-ajax.php';
			}

			$term = (string) urldecode( sanitize_text_field( wp_unslash( $_GET['term'] ) ) ); // phpcs:ignore

			WC_AJAX::json_search_products( $term, true );

		}

		/**
		 * Remove variation parent from search result
		 *
		 * @param  array $products Array of product ids with product name.
		 * @return array $products Array of product ids with product name after removing variation parent
		 */
		public function exclude_variation_parent( $products = null ) {

			$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ); // phpcs:ignore

			if ( 'wc_sc_json_search_products_and_variations' === $backtrace[5]['function'] ) {
				if ( ! empty( $products ) ) {
					$product_ids = array_keys( $products );
					$parent_ids  = array_map( 'wp_get_post_parent_id', $product_ids );
					$parent_ids  = array_filter( array_unique( $parent_ids ) );
					foreach ( $parent_ids as $parent ) {
						unset( $products[ $parent ] );
					}
				}
			}

			return $products;

		}

		/**
		 * Function to add new discount type 'smart_coupon'
		 *
		 * @param array $discount_types Existing discount types.
		 * @return array $discount_types Including smart coupon discount type.
		 */
		public function add_smart_coupon_discount_type( $discount_types ) {
			global $store_credit_label;

			$discount_types['smart_coupon'] = ! empty( $store_credit_label['singular'] ) ? ucfirst( $store_credit_label['singular'] ) : __( 'Store Credit / Gift Certificate', 'woocommerce-smart-coupons' );

			return $discount_types;
		}

		/**
		 * Function to enqueue required styles and scripts for Smart Coupons' custom fields
		 */
		public function enqueue_styles_scripts() {
			global $post;

			if ( ! empty( $post->post_type ) && 'shop_coupon' === $post->post_type && ! empty( $post->ID ) ) {

				// Compatibility between expiry_date & date_expires meta field.
				if ( $this->is_wc_gte_30() ) {
					$date_expires = intval( get_post_meta( $post->ID, 'date_expires', true ) );
					$expiry_date  = get_post_meta( $post->ID, 'expiry_date', true );

					if ( ! empty( $expiry_date ) && empty( $date_expires ) ) {
						$date_expires = strtotime( $expiry_date );
						if ( false !== $date_expires ) {
							update_post_meta( $post->ID, 'date_expires', $date_expires );
							delete_post_meta( $post->ID, 'expiry_date' );
						}
						$js = "jQuery('input#expiry_date').val('" . $expiry_date . "');";
						wc_enqueue_js( $js );
					}
				}

				if ( is_callable( 'WC_Smart_Coupons::get_smart_coupons_plugin_data' ) ) {
					$plugin_data = WC_Smart_Coupons::get_smart_coupons_plugin_data();
					$version     = $plugin_data['Version'];
				} else {
					$version = '';
				}

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				if ( ! wp_style_is( 'jquery-ui-style', 'registered' ) ) {
					wp_register_style( 'jquery-ui-style', WC()->plugin_url() . '/assets/css/jquery-ui/jquery-ui' . $suffix . '.css', array(), WC()->version );
				}

				if ( ! wp_style_is( 'jquery-ui-timepicker', 'registered' ) ) {
					wp_register_style( 'jquery-ui-timepicker', untrailingslashit( plugins_url( '/', WC_SC_PLUGIN_FILE ) ) . '/assets/css/jquery-ui-timepicker-addon' . $suffix . '.css', array( 'jquery-ui-style' ), $version );
				}

				if ( ! wp_style_is( 'jquery-ui-timepicker' ) ) {
					wp_enqueue_style( 'jquery-ui-timepicker' );
				}

				if ( ! wp_script_is( 'jquery-ui-timepicker', 'registered' ) ) {
					wp_register_script( 'jquery-ui-timepicker', untrailingslashit( plugins_url( '/', WC_SC_PLUGIN_FILE ) ) . '/assets/js/jquery-ui-timepicker-addon' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ), $version, true );
				}

				if ( ! wp_script_is( 'jquery-ui-timepicker' ) ) {
					wp_enqueue_script( 'jquery-ui-timepicker' );
				}

				$screen = get_current_screen();
				if ( 'shop_coupon' === $screen->id ) {
					?>
					<script type="text/javascript" class="sc-copy">
						function sc_copy_coupon_link_to_clipboard() {
							var copyText = document.getElementById("coupon-link");
							jQuery.trim(copyText);
							copyText.select();
							document.execCommand("copy");
							document.getElementById("sc-click-to-copy-btn").innerHTML = '<?php echo esc_html__( 'Copied!', 'woocommerce-smart-coupons' ); ?>';
							setTimeout(function(){
								document.getElementById("sc-click-to-copy-btn").innerHTML = '<?php echo esc_html__( 'Click To Copy', 'woocommerce-smart-coupons' ); ?>';
							}, 1000);
						}
					</script>
					<?php
				}
			}

		}

	}

}

WC_SC_Coupon_Fields::get_instance();
