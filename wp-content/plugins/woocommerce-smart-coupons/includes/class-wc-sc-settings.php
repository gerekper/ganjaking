<?php
/**
 * Smart Coupons Storewide Settings
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.9.1
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Settings' ) ) {

	/**
	 * Class for handling storewide settings for Smart Coupons
	 */
	class WC_SC_Settings {

		/**
		 * The WooCommerce settings tab name
		 *
		 * @since 3.4.0
		 * @var string
		 */
		public static $tab_slug = 'wc-smart-coupons';

		/**
		 * Variable to hold instance of WC_SC_Settings
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {
			add_action( 'admin_init', array( $this, 'add_delete_credit_after_usage_notice' ) );

			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_smart_coupon_settings_tab' ), 50 );
			add_action( 'woocommerce_settings_tabs_' . self::$tab_slug, array( $this, 'smart_coupon_settings_page' ) );
			add_action( 'woocommerce_update_options_' . self::$tab_slug, array( $this, 'save_smart_coupon_admin_settings' ) );

			add_action( 'woocommerce_admin_field_wc_sc_radio_with_html', array( $this, 'radio_with_html' ) );
		}

		/**
		 * Get single instance of WC_SC_Settings
		 *
		 * @return WC_SC_Settings Singleton object of WC_SC_Settings
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
		 * @param string $function_name Function to call.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return mixed Result of function call.
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
		 * Function to add setting tab for Smart Coupons
		 *
		 * @param array $settings_tabs Existing tabs.
		 * @return array
		 */
		public function add_smart_coupon_settings_tab( $settings_tabs ) {

			$settings_tabs[ self::$tab_slug ] = __( 'Smart Coupons', 'woocommerce-smart-coupons' );

			return $settings_tabs;
		}

		/**
		 * Function to add styles and script for Smart Coupons settings page
		 */
		public function sc_settings_page_styles_scripts() {

			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}

			if ( ! wp_style_is( 'smart-coupon' ) ) {
				wp_enqueue_style( 'smart-coupon' );
			}

			$design           = get_option( 'wc_sc_setting_coupon_design', 'basic' );
			$background_color = get_option( 'wc_sc_setting_coupon_background_color', '#39cccc' );
			$foreground_color = get_option( 'wc_sc_setting_coupon_foreground_color', '#30050b' );
			$third_color      = get_option( 'wc_sc_setting_coupon_third_color', '#39cccc' );
			?>
			<script type="text/javascript">
				jQuery(function(){
					let root = document.documentElement;

					if ( typeof getEnhancedSelectFormatString == "undefined" ) {
						function getEnhancedSelectFormatString() {
							return {
								'language': {
									errorLoading: function() {
										// Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
										return wc_enhanced_select_params.i18n_searching;
									},
									inputTooLong: function( args ) {
										var overChars = args.input.length - args.maximum;

										if ( 1 === overChars ) {
											return wc_enhanced_select_params.i18n_input_too_long_1;
										}

										return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
									},
									inputTooShort: function( args ) {
										var remainingChars = args.minimum - args.input.length;

										if ( 1 === remainingChars ) {
											return wc_enhanced_select_params.i18n_input_too_short_1;
										}

										return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
									},
									loadingMore: function() {
										return wc_enhanced_select_params.i18n_load_more;
									},
									maximumSelected: function( args ) {
										if ( args.maximum === 1 ) {
											return wc_enhanced_select_params.i18n_selection_too_long_1;
										}

										return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
									},
									noResults: function() {
										return wc_enhanced_select_params.i18n_no_matches;
									},
									searching: function() {
										return wc_enhanced_select_params.i18n_searching;
									}
								}
							};
						}
					}

					// Ajax coupon search box
					jQuery( ':input.wc-sc-storewide-coupon-search' ).filter( ':not(.enhanced)' ).each( function() {
						var select2_args = {
							allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
							placeholder: jQuery( this ).data( 'placeholder' ),
							minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : '3',
							escapeMarkup: function( m ) {
								return m;
							},
							ajax: {
								url:         wc_enhanced_select_params.ajax_url,
								dataType:    'json',
								delay:       250,
								data:        function( params ) {
									return {
										term         : params.term,
										action       : jQuery( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
										security     : jQuery( this ).data( 'security' ) || wc_enhanced_select_params.search_products_nonce
									};
								},
								processResults: function( data ) {
									var terms = [];
									if ( data ) {
										jQuery.each( data, function( id, text ) {
											terms.push( { id: id, text: text } );
										});
									}
									return {
										results: terms
									};
								},
								cache: true
							}
						};

						select2_args = jQuery.extend( select2_args, getEnhancedSelectFormatString() );

						jQuery( this ).selectWoo( select2_args ).addClass( 'enhanced' );

					});

					jQuery('select#smart_coupons_storewide_offer_coupon_code').parent().append( '&nbsp;<a class="thickbox smart_coupons_storewide_offer_coupon_code" href="https://woocommerce.com/wp-content/uploads/2012/08/smart-coupons-enable-store-notice-for-coupon.png?TB_iframe=true"><small>[<?php echo esc_html__( 'Preview', 'woocommerce-smart-coupons' ); ?>]</small></a>' );

					jQuery('select#smart_coupons_storewide_offer_coupon_code').parent().append( '&nbsp;<a target="_blank" class="smart_coupons_storewide_offer_coupon_code" href="https://woocommerce.com/document/smart-coupons/how-to-display-a-coupon-code-notice-sitewide/#section-4"><small>[<?php echo esc_html__( 'See coupon search limitations', 'woocommerce-smart-coupons' ); ?>]</small></a>' );

					jQuery('body .forminp-wc_sc_radio_with_html').on('click', '.wc_sc_setting_coupon_design_colors li', function(){
						let color_string = jQuery(this).find('input[type="radio"]').val();
						if ('custom' !== color_string) {
							let colors = color_string.split('-');
							jQuery.each(jQuery('.forminp-wc_sc_radio_with_html .wc_sc_setting_coupon_design_colors.custom li span'), function(index, value){
								jQuery(value).css('background-color', '#' + colors[index]);
								jQuery(value).find('input').val('#' + colors[index]).trigger('change');
							});
						}
					});

					jQuery('body .forminp-wc_sc_radio_with_html').on('click', '.wc_sc_setting_coupon_design li, .wc_sc_setting_coupon_design_colors li', function(){
						jQuery(this).find('input[type="radio"]').prop('checked', true);
					});

					jQuery('.forminp-wc_sc_radio_with_html .wc_sc_setting_coupon_design_colors.custom li').on('change', 'input[type="color"]', function(){
						let element = jQuery(this);
						let color = element.val();
						let index = element.parent().index() - 1;
						element.parent().css('background-color', color);
						root.style.setProperty('--sc-color' + (index + 1), color);
					});

				});
			</script>
			<style type="text/css">
				#TB_window img#TB_Image {
					border: none !important;
				}
				.form-table th {
					width: 25% !important;
				}
				span.select2-results {
					width: 100%;
				}
				span.select2-results ul {
					overflow: hidden;
					margin: 0;
					padding: 0;
				}
				span.select2-results ul#select2-wc_sc_setting_coupon_design-results li {
					list-style: none;
					float: left;
					text-align: center;
					width: 50%;
					box-sizing: border-box;
					background-color: transparent !important; 
				}
				a.smart_coupons_storewide_offer_coupon_code small {
					vertical-align: sub;
				}
			</style>
			<style type="text/css">
				:root {
					--sc-color1: <?php echo esc_html( $background_color ); ?>;
					--sc-color2: <?php echo esc_html( $foreground_color ); ?>;
					--sc-color3: <?php echo esc_html( $third_color ); ?>;
				}
			</style>
			<style type="text/css"><?php echo esc_html( wp_strip_all_tags( $this->get_coupon_styles(), true ) ); // phpcs:ignore ?></style>
			<style type="text/css"><?php echo esc_html( wp_strip_all_tags( $this->get_coupon_styles( 'email-coupon' ), true ) ); // phpcs:ignore ?></style>
			<?php
		}

		/**
		 * Function to display Smart Coupons settings
		 */
		public function smart_coupon_settings_page() {
			add_thickbox();

			$sc_settings = $this->get_settings();
			if ( ! is_array( $sc_settings ) || empty( $sc_settings ) ) {
				return;
			}

			woocommerce_admin_fields( $sc_settings );
			wp_nonce_field( 'wc_smart_coupons_settings', 'sc_security', false );
			$this->sc_settings_page_styles_scripts();
		}

		/**
		 * Function to get Smart Coupons admin settings
		 *
		 * @return array $sc_settings Smart Coupons admin settings.
		 */
		public function get_settings() {
			global $store_credit_label;

			$singular = ( ! empty( $store_credit_label['singular'] ) ) ? $store_credit_label['singular'] : __( 'store credit', 'woocommerce-smart-coupons' );
			$plural   = ( ! empty( $store_credit_label['plural'] ) ) ? $store_credit_label['plural'] : __( 'store credits', 'woocommerce-smart-coupons' );

			$valid_order_statuses = get_option( 'wc_sc_valid_order_statuses_for_coupon_auto_generation' );

			$paid_statuses = wc_get_is_paid_statuses();

			if ( false === $valid_order_statuses ) {
				add_option( 'wc_sc_valid_order_statuses_for_coupon_auto_generation', $paid_statuses, '', 'no' );
			} elseif ( ! is_array( $valid_order_statuses ) ) {
				update_option( 'wc_sc_valid_order_statuses_for_coupon_auto_generation', $paid_statuses, 'no' );
			}

			$all_order_statuses      = wc_get_order_statuses();
			$all_paid_order_statuses = array();

			foreach ( $paid_statuses as $paid_status ) {
				$wc_paid_status = 'wc-' . $paid_status;
				if ( array_key_exists( $wc_paid_status, $all_order_statuses ) ) {
					$all_paid_order_statuses[ $paid_status ] = $all_order_statuses[ $wc_paid_status ];
				}
			}

			$all_discount_types = wc_get_coupon_types();

			$storewide_offer_coupon_option = array();
			$storewide_offer_coupon_code   = get_option( 'smart_coupons_storewide_offer_coupon_code' );
			if ( ! empty( $storewide_offer_coupon_code ) ) {
				$coupon        = new WC_Coupon( $storewide_offer_coupon_code );
				$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : 'percent';
				/* translators: 1. The coupon code, 2. The discount type */
				$storewide_offer_coupon_option[ $storewide_offer_coupon_code ] = sprintf( __( '%1$s (Type: %2$s)', 'woocommerce-smart-coupons' ), $storewide_offer_coupon_code, ( ( array_key_exists( $discount_type, $all_discount_types ) ) ? $all_discount_types[ $discount_type ] : $discount_type ) );
			}

			$valid_designs = $this->get_valid_coupon_designs();

			$coupon_design         = get_option( 'wc_sc_setting_coupon_design' );
			$default_coupon_design = 'basic';
			if ( ! empty( $coupon_design ) && 'custom-design' === $coupon_design ) {
				$default_coupon_design = $coupon_design;
			}

			$sc_settings = array(
				array(
					'title' => __( 'Smart Coupons Settings', 'woocommerce-smart-coupons' ),
					'type'  => 'title',
					'desc'  => __( 'Set up Smart Coupons the way you like. Use these options to configure/change the way Smart Coupons works.', 'woocommerce-smart-coupons' ),
					'id'    => 'sc_display_coupon_settings',
				),
				array(
					'title'    => __( 'Colors', 'woocommerce-smart-coupons' ),
					'id'       => 'wc_sc_setting_coupon_design_colors',
					'default'  => '2b2d42-edf2f4-d90429',
					'type'     => 'wc_sc_radio_with_html',
					'desc_tip' => __( 'Choose a color scheme for coupons.', 'woocommerce-smart-coupons' ),
					'class'    => 'wc_sc_setting_coupon_design_colors',
					'autoload' => false,
					'options'  => array(
						'2b2d42-edf2f4-d90429' => __( 'Amaranth red', 'woocommerce-smart-coupons' ),
						'003459-ffffff-00a8e8' => __( 'Carolina Blue', 'woocommerce-smart-coupons' ),
						'334752-fffdf5-46b39d' => __( 'Keppel', 'woocommerce-smart-coupons' ),
						'edf2f4-bb2538-fcbf49' => __( 'McDonald', 'woocommerce-smart-coupons' ),
						'd6b56d-231f20-ffe09c' => __( 'Gold', 'woocommerce-smart-coupons' ),
						'362f78-f8f9fa-5950ec' => __( 'Majorelle Blue', 'woocommerce-smart-coupons' ),
						'a82f82-f5f8ff-f45dc4' => __( 'Rose Pink', 'woocommerce-smart-coupons' ),
						'2c5f72-f3d2b3-f16a6c' => __( 'Vintage', 'woocommerce-smart-coupons' ),
						'e37332-fefefe-de4f3c' => __( 'Spanish Orange', 'woocommerce-smart-coupons' ),
						'8e6e5d-f2f2f2-333333' => __( 'Chocolate', 'woocommerce-smart-coupons' ),
						'a7e7ff-418fde-ffffff' => __( 'Ocean', 'woocommerce-smart-coupons' ),
					),
					'args'     => array(
						'html_callback' => array( $this, 'color_scheme_html' ),
					),
				),
				array(
					'title'    => __( 'Customize colors', 'woocommerce-smart-coupons' ),
					'id'       => 'wc_sc_setting_coupon_design_colors',
					'default'  => 'custom',
					'type'     => 'wc_sc_radio_with_html',
					'desc_tip' => __( 'Customize color scheme for coupons.', 'woocommerce-smart-coupons' ),
					'class'    => 'wc_sc_setting_coupon_design_colors custom',
					'autoload' => false,
					'options'  => array(
						'custom' => __( 'Custom colors', 'woocommerce-smart-coupons' ),
					),
					'args'     => array(
						'html_callback' => array( $this, 'color_scheme_html' ),
					),
				),
				array(
					'title'    => __( 'Styles', 'woocommerce-smart-coupons' ),
					'id'       => 'wc_sc_setting_coupon_design',
					'default'  => $default_coupon_design,
					'type'     => 'wc_sc_radio_with_html',
					'desc_tip' => __( 'Choose a style for coupon on the website.', 'woocommerce-smart-coupons' ),
					'class'    => 'sc-coupons-list wc_sc_setting_coupon_design',
					'autoload' => false,
					'options'  => array(
						'flat'      => __( 'Flat', 'woocommerce-smart-coupons' ),
						'promotion' => __( 'Promotion', 'woocommerce-smart-coupons' ),
						'ticket'    => __( 'Ticket', 'woocommerce-smart-coupons' ),
						'festive'   => __( 'Festive', 'woocommerce-smart-coupons' ),
						'special'   => __( 'Special', 'woocommerce-smart-coupons' ),
						'shipment'  => __( 'Shipment', 'woocommerce-smart-coupons' ),
						'cutout'    => __( 'Cutout', 'woocommerce-smart-coupons' ),
						'deliver'   => __( 'Deliver', 'woocommerce-smart-coupons' ),
						'clipper'   => __( 'Clipper', 'woocommerce-smart-coupons' ),
						'basic'     => __( 'Basic', 'woocommerce-smart-coupons' ),
						'deal'      => __( 'Deal', 'woocommerce-smart-coupons' ),
					),
					'args'     => array(
						'html_callback' => array( $this, 'coupon_design_html' ),
					),
				),
				array(
					'title'    => __( 'Style for email', 'woocommerce-smart-coupons' ),
					'id'       => 'wc_sc_setting_coupon_design_for_email',
					'default'  => 'email-coupon',
					'type'     => 'wc_sc_radio_with_html',
					'desc_tip' => __( 'Style for coupon in email.', 'woocommerce-smart-coupons' ),
					'class'    => 'sc-coupons-list wc_sc_setting_coupon_design',
					'autoload' => false,
					'options'  => array(
						'email-coupon' => __( 'Email coupon', 'woocommerce-smart-coupons' ),
					),
					'args'     => array(
						'html_callback' => array( $this, 'coupon_design_html' ),
					),
				),
				array(
					'name'     => __( 'Number of coupons to show', 'woocommerce-smart-coupons' ),
					'desc'     => __( 'How many coupons (at max) should be shown on cart, checkout & my account page? If set to 0 (zero) then coupons will not be displayed at all on the website.', 'woocommerce-smart-coupons' ),
					'id'       => 'wc_sc_setting_max_coupon_to_show',
					'type'     => 'number',
					'desc_tip' => true,
					'css'      => 'min-width:300px;',
					'autoload' => false,
				),
				array(
					'name'              => __( 'Number of characters in auto-generated coupon code', 'woocommerce-smart-coupons' ),
					'desc'              => __( 'Number of characters in auto-generated coupon code will be restricted to this number excluding prefix and/or suffix. The default length will be 13. It is recommended to keep this number between 10 to 15 to avoid coupon code duplication.', 'woocommerce-smart-coupons' ),
					'id'                => 'wc_sc_coupon_code_length',
					'type'              => 'number',
					'desc_tip'          => true,
					'placeholder'       => '13',
					'css'               => 'min-width:300px;',
					'custom_attributes' => array(
						'min'  => 7,
						'step' => 1,
						'max'  => 20,
					),
					'autoload'          => false,
				),
				array(
					'name'              => __( 'Valid order status for auto-generating coupon', 'woocommerce-smart-coupons' ),
					'desc'              => __( 'Choose order status which will trigger the auto-generation of coupon, if the order contains product which will generate the coupon.', 'woocommerce-smart-coupons' ),
					'id'                => 'wc_sc_valid_order_statuses_for_coupon_auto_generation',
					'default'           => $paid_statuses,
					'type'              => 'multiselect',
					'class'             => 'wc-enhanced-select',
					'css'               => 'min-width: 350px;',
					'desc_tip'          => true,
					'options'           => $all_paid_order_statuses,
					'custom_attributes' => array(
						'data-placeholder' => __( 'Select order status&hellip;', 'woocommerce-smart-coupons' ),
					),
					'autoload'          => false,
				),
				array(
					'name'              => __( 'Enable store notice for the coupon', 'woocommerce-smart-coupons' ),
					'id'                => 'smart_coupons_storewide_offer_coupon_code',
					'type'              => 'select',
					'default'           => '',
					'desc'              => __( 'Search & select a coupon which you want to display as store notice. The selected coupon\'s description will be displayed along with the coupon code (if it is set) otherwise, a description will be generated automatically. To disable the feature, keep this field empty.', 'woocommerce-smart-coupons' ),
					'desc_tip'          => true,
					'class'             => 'wc-sc-storewide-coupon-search',
					'css'               => 'min-width:300px;',
					'autoload'          => false,
					'custom_attributes' => array(
						'data-placeholder' => __( 'Search for a coupon...', 'woocommerce-smart-coupons' ),
						'data-action'      => 'sc_json_search_storewide_coupons',
						'data-security'    => wp_create_nonce( 'search-coupons' ),
						'data-allow_clear' => true,
					),
					'options'           => $storewide_offer_coupon_option,
				),
				array(
					/* translators: %s: Label for store credit */
					'name'          => sprintf( __( 'Generated %s amount', 'woocommerce-smart-coupons' ), strtolower( $singular ) ),
					/* translators: %s: Label for store credit */
					'desc'          => sprintf( __( 'Include tax in the amount of the generated %s', 'woocommerce-smart-coupons' ), strtolower( $singular ) ),
					'id'            => 'wc_sc_generated_store_credit_includes_tax',
					'type'          => 'checkbox',
					'default'       => 'no',
					'checkboxgroup' => 'start',
					'autoload'      => false,
				),
				array(
					'name'          => __( 'Displaying coupons', 'woocommerce-smart-coupons' ),
					/* translators: %s: Preview link */
					'desc'          => sprintf( __( 'Include coupon details on product\'s page, for products that issue coupons %s', 'woocommerce-smart-coupons' ), '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/sc-associated-coupons.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a>' ),
					'id'            => 'smart_coupons_is_show_associated_coupons',
					'type'          => 'checkbox',
					'default'       => 'no',
					'checkboxgroup' => 'start',
					'autoload'      => false,
				),
				array(
					/* translators: %s: Preview link */
					'desc'          => sprintf( __( 'Show coupons available to customers on their My Account > Coupons page %s', 'woocommerce-smart-coupons' ), '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/sc-myaccount.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a>' ),
					'id'            => 'woocommerce_smart_coupon_show_my_account',
					'type'          => 'checkbox',
					'default'       => 'yes',
					'checkboxgroup' => '',
					'autoload'      => false,
				),
				array(
					/* translators: %s: Preview link */
					'desc'          => sprintf( __( 'Include coupons received from other people on My Account > Coupons page %s', 'woocommerce-smart-coupons' ), '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/sc-coupon-received.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a>' ),
					'id'            => 'show_coupon_received_on_my_account',
					'type'          => 'checkbox',
					'default'       => 'no',
					'checkboxgroup' => '',
					'autoload'      => false,
				),
				array(
					/* translators: %s: Preview link */
					'desc'          => sprintf( __( 'Show invalid or used coupons in My Account > Coupons %s', 'woocommerce-smart-coupons' ), '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/sc-invalid-used-coupons.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a>' ),
					'id'            => 'smart_coupons_show_invalid_coupons_on_myaccount',
					'type'          => 'checkbox',
					'default'       => 'no',
					'checkboxgroup' => '',
					'autoload'      => false,
				),
				array(
					/* translators: %s: Preview link */
					'desc'          => sprintf( __( 'Display coupon description along with coupon code (on site as well as in emails) %s', 'woocommerce-smart-coupons' ), '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/sc-coupon-description.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a>' ),
					'id'            => 'smart_coupons_show_coupon_description',
					'type'          => 'checkbox',
					'default'       => 'no',
					'checkboxgroup' => 'end',
					'autoload'      => false,
				),
				array(
					'name'          => __( 'Automatic deletion', 'woocommerce-smart-coupons' ),
					/* translators: %s: Note for admin */
					'desc'          => sprintf( __( 'Delete the %1$s when entire credit amount is used up %2$s', 'woocommerce-smart-coupons' ), strtolower( $singular ), '<small>' . __( '(Note: It\'s recommended to keep it Disabled)', 'woocommerce-smart-coupons' ) . '</small>' ),
					'id'            => 'woocommerce_delete_smart_coupon_after_usage',
					'type'          => 'checkbox',
					'default'       => 'no',
					'checkboxgroup' => 'start',
					'autoload'      => false,
				),
				array(
					'name'          => __( 'Coupon emails', 'woocommerce-smart-coupons' ),
					'desc'          => __( 'Email auto generated coupons to recipients', 'woocommerce-smart-coupons' ),
					'id'            => 'smart_coupons_is_send_email',
					'type'          => 'checkbox',
					'default'       => 'yes',
					'checkboxgroup' => 'start',
					'autoload'      => false,
				),
				array(
					'name'          => __( 'Printing coupons', 'woocommerce-smart-coupons' ),
					'desc'          => __( 'Enable feature to allow printing of coupons', 'woocommerce-smart-coupons' ) . ' <a href="https://woocommerce.com/document/smart-coupons/how-to-print-coupons/" target="_blank"><small>' . __( '[Read More]', 'woocommerce-smart-coupons' ) . '</small></a>',
					'id'            => 'smart_coupons_is_print_coupon',
					'type'          => 'checkbox',
					'default'       => 'yes',
					'checkboxgroup' => 'start',
					'autoload'      => false,
				),
				array(
					/* translators: %s: Label for store credit */
					'name'          => sprintf( __( 'Sell %s at less price?', 'woocommerce-smart-coupons' ), strtolower( $plural ) ),
					/* translators: %s: Label for store credit, 1: : Label for store credit, 2: Label for store credit, 3: Label for store credit */
					'desc'          => sprintf( __( 'Allow selling %s at discounted price', 'woocommerce-smart-coupons' ), strtolower( $plural ) ) . ' <a href="https://woocommerce.com/document/smart-coupons/how-to-sell-gift-card-at-less-price/" target="_blank"><small>' . __( '[Read More]', 'woocommerce-smart-coupons' ) . '</small></a><span class="woocommerce-help-tip" data-tip="' . esc_attr( sprintf( __( 'When selling %1$s, if Regular and Sale price is found for the product, then coupon will be created with product\'s Regular Price but customer will pay product\'s Sale price. This setting will also make sure if any discount coupon is applied on the %2$s while purchasing, then customer will get %3$s in their picked price', 'woocommerce-smart-coupons' ), strtolower( $singular ), strtolower( $singular ), strtolower( $singular ) ) ) . '"></span>',
					'id'            => 'smart_coupons_sell_store_credit_at_less_price',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
					'default'       => 'no',
					'autoload'      => false,
					'desc_tip'      => '',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'sc_display_coupon_settings',
				),
				array(
					'title' => __( 'Labels', 'woocommerce-smart-coupons' ),
					'type'  => 'title',
					'desc'  => __( 'Call it something else! Use these to quickly change text labels through your store. <a href="https://woocommerce.com/document/smart-coupons/how-to-translate-smart-coupons/" target="_blank">Use translations</a> for complete control.', 'woocommerce-smart-coupons' ),
					'id'    => 'sc_setting_labels',
				),
				array(
					'name'        => __( 'Store Credit / Gift Certificate', 'woocommerce-smart-coupons' ),
					'desc'        => '<a href="https://woocommerce.com/document/smart-coupons/how-to-rename-store-credit/" target="_blank"><small>' . __( '[Read More]', 'woocommerce-smart-coupons' ) . '</small></a>',
					'id'          => 'sc_store_credit_singular_text',
					'type'        => 'text',
					'placeholder' => __( 'Singular name', 'woocommerce-smart-coupons' ),
					'desc_tip'    => __( 'Give alternate singular name to Store Credit / Gift Certficate. This label will only rename Store Credit / Gift Certficate used in the Smart Coupons plugin.', 'woocommerce-smart-coupons' ),
					'css'         => 'min-width:300px;',
					'autoload'    => false,
				),
				array(
					'id'          => 'sc_store_credit_plural_text',
					'type'        => 'text',
					'desc_tip'    => __( 'Give plural name for the above singular name.', 'woocommerce-smart-coupons' ),
					'placeholder' => __( 'Plural name', 'woocommerce-smart-coupons' ),
					'css'         => 'min-width:300px;',
					'autoload'    => false,
				),
				array(
					/* translators: %s: Label for store credit */
					'name'        => sprintf( __( '%s product CTA', 'woocommerce-smart-coupons' ), ucfirst( $singular ) ),
					'desc'        => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/sc-purchase-credit-shop-text.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a>',
					'id'          => 'sc_gift_certificate_shop_loop_button_text',
					'type'        => 'text',
					/* translators: %s: Label for store credit */
					'desc_tip'    => sprintf( __( 'This is what will be shown instead of "Add to Cart" for products that sell %s.', 'woocommerce-smart-coupons' ), strtolower( $plural ) ),
					'placeholder' => __( 'Select options', 'woocommerce-smart-coupons' ),
					'css'         => 'min-width:300px;',
					'autoload'    => false,
				),
				array(
					/* translators: %s: Label for store credit */
					'name'        => sprintf( __( 'While purchasing %s', 'woocommerce-smart-coupons' ), strtolower( $plural ) ),
					'desc'        => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/sc-purchase-credit-product-page-text.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a>',
					'id'          => 'smart_coupon_store_gift_page_text',
					'type'        => 'text',
					/* translators: %s: Label for store credit */
					'desc_tip'    => sprintf( __( 'When you opt to allow people to buy %s of any amount, this label will be used.', 'woocommerce-smart-coupons' ), strtolower( $plural ) ),
					'placeholder' => __( 'Purchase credit worth', 'woocommerce-smart-coupons' ),
					'css'         => 'min-width:300px;',
					'autoload'    => false,
				),
				array(
					'name'        => __( '"Coupons with Product" description', 'woocommerce-smart-coupons' ),
					'desc'        => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/sc-associated-coupon-description-front.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a>',
					'id'          => 'smart_coupon_product_page_text',
					'type'        => 'text',
					'desc_tip'    => __( 'This is the heading above coupon details displayed on products that issue coupons.', 'woocommerce-smart-coupons' ),
					'placeholder' => __( 'You will get following coupon(s) when you buy this item', 'woocommerce-smart-coupons' ),
					'css'         => 'min-width:300px;',
					'autoload'    => false,
				),
				array(
					'name'        => __( 'On Cart/Checkout pages', 'woocommerce-smart-coupons' ),
					'desc'        => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/sc-coupon-cart-checkout-title.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a>',
					'id'          => 'smart_coupon_cart_page_text',
					'type'        => 'text',
					'desc_tip'    => __( 'This is the title for the list of available coupons, shown on Cart and Checkout pages.', 'woocommerce-smart-coupons' ),
					'placeholder' => __( 'Available Coupons (click on a coupon to use it)', 'woocommerce-smart-coupons' ),
					'css'         => 'min-width:300px;',
					'autoload'    => false,
				),
				array(
					'name'        => __( 'My Account page', 'woocommerce-smart-coupons' ),
					'desc'        => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/sc-myaccount-title.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a>',
					'id'          => 'smart_coupon_myaccount_page_text',
					'type'        => 'text',
					'desc_tip'    => __( 'Title of available coupons list on My Account page.', 'woocommerce-smart-coupons' ),
					/* translators: %s: Label for store credit */
					'placeholder' => sprintf( __( 'Available Coupons & %s', 'woocommerce-smart-coupons' ), ucwords( $plural ) ),
					'css'         => 'min-width:300px;',
					'autoload'    => false,
				),
				array(
					'type' => 'sectionend',
					'id'   => 'sc_setting_labels',
				),
				array(
					'title' => __( 'Coupon Receiver Details during Checkout', 'woocommerce-smart-coupons' ),
					'type'  => 'title',
					'desc'  => __( 'Buyers can send purchased coupons to anyone – right while they\'re checking out.', 'woocommerce-smart-coupons' ),
					'id'    => 'sc_coupon_receiver_settings',
				),
				array(
					'name'     => __( 'Allow sending of coupons to others', 'woocommerce-smart-coupons' ),
					'desc'     => __( 'Allow the buyer to send coupons to someone else.', 'woocommerce-smart-coupons' ) . ' <a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/sc-coupon-receiver-details-form.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a>',
					'id'       => 'smart_coupons_display_coupon_receiver_details_form',
					'type'     => 'checkbox',
					'default'  => 'yes',
					'autoload' => false,
					'desc_tip' => '',
				),
				array(
					'name'        => __( 'Title', 'woocommerce-smart-coupons' ),
					'desc'        => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/sc-title-coupon-receiver-form.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a>',
					'id'          => 'smart_coupon_gift_certificate_form_page_text',
					'type'        => 'text',
					'desc_tip'    => __( 'The title for coupon receiver details block.', 'woocommerce-smart-coupons' ),
					'placeholder' => __( 'Send Coupons to...', 'woocommerce-smart-coupons' ),
					'css'         => 'min-width:300px;',
					'autoload'    => false,
				),
				array(
					'name'     => __( 'Description', 'woocommerce-smart-coupons' ),
					'desc'     => '<a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/sc-coupon-receiver-form-description.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a>',
					'id'       => 'smart_coupon_gift_certificate_form_details_text',
					'type'     => 'text',
					'desc_tip' => __( 'Additional text below the title.', 'woocommerce-smart-coupons' ),
					'css'      => 'min-width:300px;',
					'autoload' => false,
				),
				array(
					'name'          => __( 'Allow schedule sending of coupons?', 'woocommerce-smart-coupons' ),
					'desc'          => __( 'Enable this to allow buyers to select date & time for delivering the coupon.', 'woocommerce-smart-coupons' ) . ' <a class="thickbox" href="' . add_query_arg( array( 'TB_iframe' => 'true' ), 'https://woocommerce.com/wp-content/uploads/2012/08/schedule-delivery-of-coupon.png' ) . '"><small>' . __( '[Preview]', 'woocommerce-smart-coupons' ) . '</small></a><span class="woocommerce-help-tip" data-tip="' . esc_attr__( 'The coupons will be sent to the recipients via email on the selected date & time', 'woocommerce-smart-coupons' ) . '"></span>',
					'id'            => 'smart_coupons_schedule_store_credit',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
					'default'       => 'no',
					'autoload'      => false,
					'desc_tip'      => '',
				),
				array(
					'name'     => __( 'Combine emails', 'woocommerce-smart-coupons' ),
					'desc'     => __( 'Send only one email instead of multiple emails when multiple coupons are generated for same recipient', 'woocommerce-smart-coupons' ),
					'id'       => 'smart_coupons_combine_emails',
					'type'     => 'checkbox',
					'default'  => 'no',
					'autoload' => false,
				),
				array(
					'type' => 'sectionend',
					'id'   => 'sc_coupon_receiver_settings',
				),
			);

			if ( $this->is_wc_gte_30() && wc_tax_enabled() ) {
				$before_tax_option[] = array(
					'name'            => __( 'Apply before tax', 'woocommerce-smart-coupons' ),
					'desc'            => __( 'Deduct credit/gift before doing tax calculations', 'woocommerce-smart-coupons' ),
					'id'              => 'woocommerce_smart_coupon_apply_before_tax',
					'type'            => 'checkbox',
					'default'         => 'no',
					'checkboxgroup'   => 'start',
					'autoload'        => false,
					'show_if_checked' => 'option',
				);

				$prices_include_tax = wc_prices_include_tax();

				if ( true === $prices_include_tax ) {
					$before_tax_option[] = array(
						/* translators: %s: Label for store credit */
						'name'            => sprintf( __( '%s include tax?', 'woocommerce-smart-coupons' ), ucfirst( $singular ) ),
						/* translators: %s: Label for store credit */
						'desc'            => sprintf( __( '%s discount is inclusive of tax', 'woocommerce-smart-coupons' ), ucfirst( $singular ) ),
						'id'              => 'woocommerce_smart_coupon_include_tax',
						'type'            => 'checkbox',
						'default'         => 'no',
						'checkboxgroup'   => 'end',
						'autoload'        => false,
						'show_if_checked' => 'yes',
					);
				}

				array_splice( $sc_settings, 15, 0, $before_tax_option );
			}

			return apply_filters( 'wc_smart_coupons_settings', $sc_settings );

		}

		/**
		 * Function for saving settings for Gift Certificate
		 */
		public function save_smart_coupon_admin_settings() {
			if ( empty( $_POST['sc_security'] ) || ! wp_verify_nonce( wp_unslash( $_POST['sc_security'] ), 'wc_smart_coupons_settings' ) ) { // phpcs:ignore
				return;
			}

			$sc_settings = $this->get_settings();
			if ( ! is_array( $sc_settings ) || empty( $sc_settings ) ) {
				return;
			}

			woocommerce_update_options( $sc_settings );

			// Update WC Email settings when SC admin settings are updated.
			$is_send_email  = get_option( 'smart_coupons_is_send_email', 'yes' );
			$combine_emails = get_option( 'smart_coupons_combine_emails', 'no' );

			$email_settings = get_option( 'woocommerce_wc_sc_email_coupon_settings', array() );
			if ( is_array( $email_settings ) ) {
				$email_settings['enabled'] = $is_send_email;
				update_option( 'woocommerce_wc_sc_email_coupon_settings', $email_settings, 'no' );
			}

			$combine_email_settings = get_option( 'woocommerce_wc_sc_combined_email_coupon_settings', array() );
			if ( is_array( $combine_email_settings ) ) {
				$combine_email_settings['enabled'] = $combine_emails;
				update_option( 'woocommerce_wc_sc_combined_email_coupon_settings', $combine_email_settings, 'no' );
			}

			$predefined_colors = array(
				'2b2d42-edf2f4-d90429',
				'003459-ffffff-00a8e8',
				'334752-fffdf5-46b39d',
				'edf2f4-bb2538-fcbf49',
				'd6b56d-231f20-ffe09c',
				'362f78-f8f9fa-5950ec',
				'a82f82-f5f8ff-f45dc4',
				'2c5f72-f3d2b3-f16a6c',
				'e37332-fefefe-de4f3c',
				'8e6e5d-f2f2f2-333333',
				'a7e7ff-418fde-ffffff',
			);

			$color_options = array(
				'wc_sc_setting_coupon_background_color',
				'wc_sc_setting_coupon_foreground_color',
				'wc_sc_setting_coupon_third_color',
			);
			$colors        = array();
			foreach ( $color_options as $option ) {
				$post_option = ( isset( $_POST[ $option ] ) ) ? wc_clean( wp_unslash( $_POST[ $option ] ) ) : ''; // phpcs:ignore
				if ( ! empty( $post_option ) ) {
					$colors[] = $post_option;
					update_option( $option, $post_option, 'no' );
				}
			}
			$color_scheme = implode( '-', $colors );
			$color_scheme = str_replace( '#', '', $color_scheme );
			if ( in_array( $color_scheme, $predefined_colors, true ) ) {
				update_option( 'wc_sc_setting_coupon_design_colors', $color_scheme, 'no' );
			}

			$old_storewide_offer_coupon_code  = get_option( 'smart_coupons_storewide_offer_coupon_code' );
			$post_storewide_offer_coupon_code = ( ! empty( $_POST['smart_coupons_storewide_offer_coupon_code'] ) ) ? wc_clean( wp_unslash( $_POST['smart_coupons_storewide_offer_coupon_code'] ) ) : ''; // phpcs:ignore
			if ( $old_storewide_offer_coupon_code !== $post_storewide_offer_coupon_code ) {
				update_option( 'smart_coupons_storewide_offer_coupon_code', $post_storewide_offer_coupon_code, 'no' );
				if ( ! empty( $post_storewide_offer_coupon_code ) ) {
					$coupon_id     = wc_get_coupon_id_by_code( $post_storewide_offer_coupon_code );
					$coupon_status = get_post_status( $coupon_id );
					if ( 'publish' === $coupon_status ) {
						update_option( 'woocommerce_demo_store', 'yes' );
					} else {
						update_option( 'woocommerce_demo_store', 'no' );
					}
					update_option( 'woocommerce_demo_store_notice', '', 'no' );
					$notice = get_option( 'woocommerce_demo_store_notice' );
					if ( empty( $notice ) ) {
						$coupon = new WC_Coupon( $post_storewide_offer_coupon_code );
						$notice = $this->generate_storewide_offer_coupon_description( array( 'coupon_object' => $coupon ) );
						update_option( 'woocommerce_demo_store_notice', wp_filter_post_kses( $notice ), 'no' );
					}
				} else {
					update_option( 'woocommerce_demo_store', 'no' );
					update_option( 'woocommerce_demo_store_notice', '', 'no' );
				}
			}

		}

		/**
		 * Function to Add Delete Credit After Usage Notice
		 */
		public function add_delete_credit_after_usage_notice() {

			$is_delete_smart_coupon_after_usage = get_option( 'woocommerce_delete_smart_coupon_after_usage' );

			if ( 'yes' !== $is_delete_smart_coupon_after_usage ) {
				return;
			}

			$admin_email = get_option( 'admin_email' );

			$user = get_user_by( 'email', $admin_email );

			$current_user_id = get_current_user_id();

			if ( ! empty( $current_user_id ) && ! empty( $user->ID ) && $current_user_id === $user->ID ) {
				add_action( 'admin_notices', array( $this, 'delete_credit_after_usage_notice' ) );
				add_action( 'admin_footer', array( $this, 'ignore_delete_credit_after_usage_notice' ) );
			}

		}

		/**
		 * Function to Delete Credit After Usage Notice
		 */
		public function delete_credit_after_usage_notice() {
			global $store_credit_label;
			$plural                            = ( ! empty( $store_credit_label['plural'] ) ) ? $store_credit_label['plural'] : __( 'store credits', 'woocommerce-smart-coupons' );
			$current_user_id                   = get_current_user_id();
			$is_hide_delete_after_usage_notice = get_user_meta( $current_user_id, 'hide_delete_credit_after_usage_notice', true );                                                      // @codingStandardsIgnoreLine
			if ( 'yes' !== $is_hide_delete_after_usage_notice ) {
				echo '<div class="error"><p>';
				if ( ! empty( $_GET['page'] ) && 'wc-settings' === $_GET['page'] && empty( $_GET['tab'] ) ) { // phpcs:ignore
					/* translators: 1: plugin name 2: page based text 3: Label for store credit 4: Hide notice text */
					echo sprintf( esc_html__( '%1$s: %2$s to avoid issues related to missing data for %3$s. %4$s', 'woocommerce-smart-coupons' ), '<strong>' . esc_html__( 'WooCommerce Smart Coupons', 'woocommerce-smart-coupons' ) . '</strong>', esc_html__( 'Uncheck', 'woocommerce-smart-coupons' ) . ' &quot;<strong>' . esc_html__( 'Delete Gift / Credit, when credit is used up', 'woocommerce-smart-coupons' ) . '</strong>&quot;', esc_html( strtolower( $plural ) ), '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=wc-smart-coupons' ) ) . '">' . esc_html__( 'Setting', 'woocommerce-smart-coupons' ) . '</a>' ) . ' <button type="button" class="button" id="hide_notice_delete_credit_after_usage">' . esc_html__( 'Hide this notice', 'woocommerce-smart-coupons' ) . '</button>'; // phpcs ignore.
				} else {
					/* translators: 1: plugin name 2: page based text 3: Label for store credit 4: Hide notice text */
					echo sprintf( esc_html__( '%1$s: %2$s to avoid issues related to missing data for %3$s. %4$s', 'woocommerce-smart-coupons' ), '<strong>' . esc_html__( 'WooCommerce Smart Coupons', 'woocommerce-smart-coupons' ) . '</strong>', '<strong>' . esc_html__( 'Important setting', 'woocommerce-smart-coupons' ) . '</strong>', esc_html( strtolower( $plural ) ), '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=wc-smart-coupons' ) ) . '">' . esc_html__( 'Setting', 'woocommerce-smart-coupons' ) . '</a>' ) . ' <button type="button" class="button" id="hide_notice_delete_credit_after_usage">' . esc_html__( 'Hide this notice', 'woocommerce-smart-coupons' ) . '</button>'; // phpcs ignore.
				}
				echo '</p></div>';
			}

		}

		/**
		 * Function to Ignore Delete Credit After Usage Notice
		 */
		public function ignore_delete_credit_after_usage_notice() {

			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}

			?>
			<script type="text/javascript">
				jQuery(function(){
					jQuery('body').on('click', 'button#hide_notice_delete_credit_after_usage', function(){
						jQuery.ajax({
							url: decodeURIComponent( '<?php echo rawurlencode( admin_url( 'admin-ajax.php' ) ); ?>' ),
							type: 'post',
							dataType: 'json',
							data: {
								action: 'hide_notice_delete_after_usage',
								security: '<?php echo esc_html( wp_create_nonce( 'hide-smart-coupons-notice' ) ); ?>'
							},
							success: function( response ) {
								if ( response.message == 'success' ) {
									jQuery('button#hide_notice_delete_credit_after_usage').parent().parent().remove();
								}
							}
						});
					});
				});
			</script>
			<?php

		}

		/**
		 * Draw form field for radio with HTML
		 *
		 * @param array $value Field arguments.
		 */
		public function radio_with_html( $value = array() ) {
			$custom_attributes = array();

			if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
				foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
					$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
				}
			}

			$field_description = WC_Admin_Settings::get_field_description( $value );
			$description       = $field_description['description'];
			$tooltip_html      = $field_description['tooltip_html'];

			$option_value = $value['value'];
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); // phpcs:ignore ?></label>
				</th>
				<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
					<fieldset id="sc-cc">
						<?php echo $description; // phpcs:ignore ?>
						<ul class="<?php echo esc_attr( $value['class'] ); ?>">
						<?php
						foreach ( $value['options'] as $key => $val ) {
							?>
							<li class="<?php echo ( $key === $option_value ) ? 'selected' : ''; ?>">
								<input
								name="<?php echo esc_attr( $value['id'] ); ?>"
								value="<?php echo esc_attr( $key ); ?>"
								type="radio"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								<?php echo implode( ' ', $custom_attributes ); // phpcs:ignore ?>
								<?php checked( $key, $option_value ); ?>
								/>
								<?php
								if ( ! empty( $value['args']['html_callback'] ) ) {
									call_user_func_array( $value['args']['html_callback'], array( $key ) );
								}
								?>
							</li>
							<?php
						}
						?>
						</ul>
					</fieldset>
				</td>
			</tr>
			<?php

		}

		/**
		 * Draw color scheme
		 *
		 * @param string $colors Colors.
		 */
		public function color_scheme_html( $colors = '' ) {
			if ( empty( $colors ) ) {
				return;
			}
			if ( 'custom' === $colors ) {
				$color_codes   = array();
				$color_options = array(
					'wc_sc_setting_coupon_background_color' => '#39cccc',
					'wc_sc_setting_coupon_foreground_color' => '#30050b',
					'wc_sc_setting_coupon_third_color' => '#39cccc',
				);
				foreach ( $color_options as $option => $default ) {
					$color_code = get_option( $option, $default );
					?>
					<span style="background-color: <?php echo esc_attr( $color_code ); ?>">
						<input type="color" id="<?php echo esc_attr( $option ); ?>" name="<?php echo esc_attr( $option ); ?>" value="<?php echo esc_attr( $color_code ); ?>">
					</span>
					<?php
				}
			} else {
				$color_codes = explode( '-', $colors );
				foreach ( $color_codes as $color_code ) {
					?>
					<span style="background-color: #<?php echo esc_attr( $color_code ); ?>"></span>
					<?php
				}
			}
		}

		/**
		 * Draw coupon design HTML
		 *
		 * @param string $design The design.
		 * @return void
		 */
		public function coupon_design_html( $design = '' ) {
			if ( empty( $design ) ) {
				return;
			}
			$args = array(
				'coupon_amount'      => 10,
				'amount_symbol'      => get_woocommerce_currency_symbol(),
				'discount_type'      => __( 'Discount', 'woocommerce-smart-coupons' ),
				'coupon_description' => __( 'Hurry. Going fast! On the entire range of products.', 'woocommerce-smart-coupons' ),
				'coupon_code'        => 'sample-code',
				'coupon_expiry'      => $this->get_expiration_format( strtotime( 'Dec 31' ) ),
				'thumbnail_src'      => $this->get_coupon_design_thumbnail_src(),
				'classes'            => '',
				'template_id'        => $design,
				'is_percent'         => false,
			);
			?>
			<div style="display: inline-block;">
				<?php wc_get_template( 'coupon-design/' . $design . '.php', $args, '', plugin_dir_path( WC_SC_PLUGIN_FILE ) . 'templates/' ); ?>
			</div>
			<?php
		}

	}

}

WC_SC_Settings::get_instance();
