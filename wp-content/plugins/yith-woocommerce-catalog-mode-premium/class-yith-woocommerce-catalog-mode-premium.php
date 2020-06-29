<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WooCommerce_Catalog_Mode_Premium' ) ) {

	/**
	 * Implements features of YITH WooCommerce Catalog Mode plugin
	 *
	 * @class   YITH_WooCommerce_Catalog_Mode_Premium
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 * @package Yithemes
	 */
	class YITH_WooCommerce_Catalog_Mode_Premium extends YITH_WooCommerce_Catalog_Mode {

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WooCommerce_Catalog_Mode_Premium
		 * @since 1.3.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self;

			}

			return self::$instance;

		}

		/**
		 * @var array User geolocation info
		 */
		protected $_user_geolocation = null;

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			parent::__construct();

			add_action( 'init', array( $this, 'geolocate_user' ) );
			add_action( 'init', array( $this, 'init_multivendor_integration' ), 20 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_premium_scripts_admin' ), 15 );
			add_action( 'product_cat_edit_form', array( $this, 'write_taxonomy_options' ), 99 );
			add_action( 'product_tag_edit_form', array( $this, 'write_taxonomy_options' ), 99 );
			add_action( 'edited_product_cat', array( $this, 'save_taxonomy_options' ) );
			add_action( 'edited_product_tag', array( $this, 'save_taxonomy_options' ) );

			if ( ! is_admin() || $this->is_quick_view() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_premium_styles' ) );
				add_filter( 'woocommerce_product_tabs', array( $this, 'add_inquiry_form_tab' ) );
				add_filter( 'woocommerce_product_tabs', array( $this, 'disable_reviews_tab' ), 98 );
				add_action( 'woocommerce_before_single_product', array( $this, 'add_inquiry_form_page' ), 5 );
				add_action( 'woocommerce_before_single_product', array( $this, 'show_wapo_if_hidden' ), 5 );
				add_action( 'woocommerce_single_product_summary', array( $this, 'show_custom_button' ), 20 );
				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'show_custom_button' ), 20 );
				add_filter( 'ywctm_get_exclusion', array( $this, 'get_exclusion' ), 10, 4 );
				add_filter( 'woocommerce_product_get_price', array( $this, 'show_product_price' ), 10, 2 );
				add_filter( 'woocommerce_get_price_html', array( $this, 'show_product_price' ), 12, 2 );
				add_filter( 'yith_ywraq_hide_price_template', array( $this, 'show_product_price' ), 12, 2 );
				add_filter( 'ywctm_check_price_hidden', array( $this, 'check_price_hidden' ), 10, 2 );
				add_filter( 'woocommerce_product_is_on_sale', array( $this, 'hide_on_sale' ), 10, 2 );
				add_filter( 'ywctm_css_classes', array( $this, 'hide_price_single_page' ) );
				// remove discount table from product (YITH WooCommerce Dynamic Discount Product)
				add_filter( 'ywdpd_exclude_products_from_discount', array( $this, 'hide_discount_quantity_table' ), 10, 2 );
			}

			// compatibility with quick view
			add_action( 'yith_wcqv_product_summary', array( $this, 'check_quick_view' ) );
			add_shortcode( 'ywctm-button', array( $this, 'print_custom_button_shortcode' ) );
			add_shortcode( 'ywctm-inquiry-form', array( $this, 'print_inquiry_form_shortcode' ) );

			add_action( 'after_setup_theme', array( $this, 'themes_integration' ) );

			if ( is_admin() ) {
				// register plugin to licence/update system
				add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
				add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			}

		}

		/**
		 * Premium files inclusion
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function include_files() {

			parent::include_files();

			include_once( 'includes/ywctm-functions-premium.php' );
			include_once( 'includes/class-ywctm-button-label-post-type.php' );

			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				include_once( 'includes/integrations/class-ywctm-elementor.php' );
			}

			if ( is_admin() ) {

				include_once( 'includes/admin/class-yith-custom-table.php' );
				include_once( 'includes/admin/meta-boxes/class-ywctm-product-metabox.php' );
				include_once( 'includes/admin/tables/class-ywctm-exclusions-table.php' );

				if ( ywctm_is_multivendor_active() && ywctm_is_multivendor_integration_active() ) {
					include_once( 'includes/admin/tables/class-ywctm-vendors-table.php' );
				}
			}

		}

		/**
		 * Check if country has catalog mode active
		 *
		 * @param   $apply   boolean
		 * @param   $post_id integer
		 *
		 * @return  boolean
		 * @since   1.3.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function country_check( $apply, $post_id ) {

			if ( 'yes' === get_option( 'ywctm_enable_geolocation', 'no' ) ) {
				$geolocation   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_geolocation_settings' ), $post_id, 'ywctm_geolocation_settings' );
				$countries     = maybe_unserialize( $geolocation['countries'] );
				$users_match   = 'all' === $geolocation['users'] || ! is_user_logged_in();
				$country_match = in_array( $this->_user_geolocation, $countries, true );

				$apply = $users_match && $country_match;

				if ( 'disable' === $geolocation['action'] ) {
					$apply = ! $apply;
				}
			}

			return $apply;

		}

		/**
		 * Get user country from IP Address
		 *
		 * @return  void
		 * @since   1.3.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function geolocate_user() {

			if ( 'yes' === get_option( 'ywctm_enable_geolocation', 'no' ) ) {
				$ip_address = ywctm_get_ip_address();
				$request    = wp_remote_get( "https://freegeoip.app/json/$ip_address" );
				$response   = json_decode( wp_remote_retrieve_body( $request ) );

				if ( ! $response || '' === $response->country_code ) {
					$wc_geo_ip   = WC_Geolocation::geolocate_ip( $ip_address );
					$geolocation = $wc_geo_ip['country'];
				} else {
					$geolocation = $response->country_code;
				}

				if ( '' === $geolocation ) {
					$geolocation = wc_get_base_location()['country'];
				}

				$this->_user_geolocation = $geolocation;
			}
		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Multi Vendor integration init function
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init_multivendor_integration() {
			if ( ywctm_is_multivendor_active() ) {
				include_once( 'includes/integrations/class-ywctm-multi-vendor.php' );
			}
		}

		/**
		 * Enqueue script file
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function enqueue_premium_scripts_admin() {

			wp_register_style( 'ywctm-admin-premium', yit_load_css_file( YWCTM_ASSETS_URL . 'css/admin-premium.css' ), array(), YWCTM_VERSION );
			wp_register_script( 'ywctm-admin-premium', yit_load_css_file( YWCTM_ASSETS_URL . 'js/admin-premium.js' ), array( 'jquery' ), YWCTM_VERSION );

			$args = array(
				'vendor_id'          => ywctm_get_vendor_id( true ),
				'error_messages'     => array(
					'product'  => esc_html__( 'Select at least one product', 'yith-woocommerce-catalog-mode' ),
					'category' => esc_html__( 'Select at least one category', 'yith-woocommerce-catalog-mode' ),
					'tag'      => esc_html__( 'Select at least one tag', 'yith-woocommerce-catalog-mode' ),
				),
				'buttons_custom_url' => ywctm_buttons_id_with_custom_url(),
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
			);
			wp_localize_script( 'ywctm-admin-premium', 'ywctm', $args );

			if ( ! empty( $_GET['page'] ) && ( $_GET['page'] === $this->_panel_page || 'yith_vendor_ctm_settings' === $_GET['page'] ) ) {

				wp_enqueue_script( 'ywctm-admin-premium' );
				wp_enqueue_style( 'ywctm-admin-premium' );

				if ( ! ywctm_is_multivendor_active() ) {
					$css = '.yith-plugin-fw-sub-tabs-nav{display: none}';
					wp_add_inline_style( 'ywctm-admin-premium', $css );
				}
			}

			if ( ! empty( $_GET['taxonomy'] ) && ( 'product_cat' === $_GET['taxonomy'] || 'product_tag' === $_GET['taxonomy'] ) ) {
				wp_enqueue_style( 'yith-plugin-fw-fields' );
				wp_enqueue_script( 'yith-plugin-fw-fields' );
				wp_enqueue_style( 'ywctm-admin-premium' );
				wp_enqueue_script( 'ywctm-admin-premium' );
			}

		}

		/**
		 * Add YWCTM fields in category/tag edit page
		 *
		 * @param   $taxonomy
		 *
		 * @return  void
		 * @since   1.3.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function write_taxonomy_options( $taxonomy ) {

			$item          = get_term_meta( $taxonomy->term_id, '_ywctm_exclusion_settings' . ywctm_get_vendor_id(), true );
			$has_exclusion = 'yes';

			if ( ! $item ) {
				$atc_global         = get_option( 'ywctm_hide_add_to_cart_settings' . ywctm_get_vendor_id() );
				$button_global      = get_option( 'ywctm_custom_button_settings' . ywctm_get_vendor_id() );
				$button_loop_global = get_option( 'ywctm_custom_button_settings_loop' . ywctm_get_vendor_id() );
				$price_global       = get_option( 'ywctm_hide_price_settings' . ywctm_get_vendor_id() );
				$label_global       = get_option( 'ywctm_custom_price_text_settings' . ywctm_get_vendor_id() );
				$has_exclusion      = 'no';

				$item = array(
					'enable_inquiry_form'         => 'yes',
					'enable_atc_custom_options'   => 'no',
					'atc_status'                  => $atc_global['action'],
					'custom_button'               => $button_global,
					'custom_button_loop'          => $button_loop_global,
					'enable_price_custom_options' => 'no',
					'price_status'                => $price_global['action'],
					'custom_price_text'           => $label_global,
				);
			}

			$fields  = array_merge(
				array(
					array(
						'id'    => 'ywctm_has_exclusion',
						'name'  => 'ywctm_has_exclusion',
						'type'  => 'onoff',
						'title' => esc_html__( 'Add to exclusion list', 'yith-woocommerce-catalog-mode' ),
						'value' => $has_exclusion,
					),
				),
				ywctm_get_exclusion_fields( $item )
			);
			$enabled = get_option( 'ywctm_inquiry_form_enabled' . ywctm_get_vendor_id(), 'hidden' );

			?>
			<div class="ywctm-taxonomy-panel yith-plugin-ui woocommerce">
				<h2><?php esc_html_e( 'Catalog Mode Options', 'yith-woocommerce-catalog-mode' ); ?></h2>
				<table class="form-table <?php echo( 'hidden' !== $enabled && ywctm_exists_inquiry_forms() ? '' : 'no-active-form' ); ?>">
					<tbody>
					<?php foreach ( $fields as $field ) : ?>
						<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo $field['type']; ?> <?php echo $field['name']; ?>">
							<th scope="row" class="titledesc">
								<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
							</th>
							<td class="forminp forminp-<?php echo $field['type']; ?>">
								<?php yith_plugin_fw_get_field( $field, true ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php

		}

		/**
		 * Save YWCTM category/tag options
		 *
		 * @param   $taxonomy_id
		 *
		 * @return  void
		 * @since   1.3.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function save_taxonomy_options( $taxonomy_id ) {

			global $pagenow;

			if ( ! $taxonomy_id || 'edit-tags.php' !== $pagenow ) {
				return;
			}

			if ( isset( $_POST['ywctm_has_exclusion'] ) ) {

				$exclusion_data = array(
					'enable_inquiry_form'         => isset( $_POST['ywctm_enable_inquiry_form'] ) ? 'yes' : 'no',
					'enable_atc_custom_options'   => isset( $_POST['ywctm_enable_atc_custom_options'] ) ? 'yes' : 'no',
					'atc_status'                  => $_POST['ywctm_atc_status'],
					'custom_button'               => $_POST['ywctm_custom_button'],
					'custom_button_url'           => $_POST['ywctm_custom_button_url'],
					'custom_button_loop'          => $_POST['ywctm_custom_button_loop'],
					'custom_button_loop_url'      => $_POST['ywctm_custom_button_loop_url'],
					'enable_price_custom_options' => isset( $_POST['ywctm_enable_price_custom_options'] ) ? 'yes' : 'no',
					'price_status'                => $_POST['ywctm_price_status'],
					'custom_price_text'           => $_POST['ywctm_custom_price_text'],
					'custom_price_text_url'       => $_POST['ywctm_custom_price_text_url'],
				);

				update_term_meta( $taxonomy_id, '_ywctm_exclusion_settings' . ywctm_get_vendor_id(), $exclusion_data );
			} else {
				delete_term_meta( $taxonomy_id, '_ywctm_exclusion_settings' . ywctm_get_vendor_id() );
			}

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Get exclusion
		 *
		 * @param   $value         string
		 * @param   $post_id       integer
		 * @param   $option        string
		 * @param   $global_value  string
		 *
		 * @return  mixed
		 * @since   1.3.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_exclusion( $value, $post_id, $option, $global_value = '' ) {

			$product = wc_get_product( $post_id );

			if ( ! $product ) {
				return $value;
			}

			switch ( $option ) {
				case 'atc':
				case 'price':
					$product_exclusion = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_exclusion_settings' ), $post_id, '_ywctm_exclusion_settings' );

					if ( $product_exclusion ) {

						if ( 'yes' === $product_exclusion[ 'enable_' . $option . '_custom_options' ] ) {

							return $product_exclusion[ $option . '_status' ];
						} else {
							return $global_value;
						}
					}

					$product_cats = wp_get_object_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );
					foreach ( $product_cats as $cat_id ) {

						$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_exclusion_settings', true ), $post_id, $cat_id, '_ywctm_exclusion_settings' );
						if ( $product_exclusion ) {

							if ( 'yes' === $product_exclusion[ 'enable_' . $option . '_custom_options' ] ) {

								return $product_exclusion[ $option . '_status' ];
							} else {
								return $global_value;
							}
						}
					}

					$product_tags = wp_get_object_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
					foreach ( $product_tags as $tag_id ) {

						$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_exclusion_settings', true ), $post_id, $tag_id, '_ywctm_exclusion_settings' );
						if ( $product_exclusion ) {

							if ( 'yes' === $product_exclusion[ 'enable_' . $option . '_custom_options' ] ) {

								return $product_exclusion[ $option . '_status' ];
							} else {
								return $global_value;
							}
						}
					}

					return $value;

					break;
				case 'inquiry_form':
					$product_exclusion = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_exclusion_settings' ), $post_id, '_ywctm_exclusion_settings' );

					if ( $product_exclusion ) {
						return 'yes' === $product_exclusion['enable_inquiry_form'];
					}

					$product_cats = wp_get_object_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );
					foreach ( $product_cats as $cat_id ) {

						$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_exclusion_settings', true ), $post_id, $cat_id, '_ywctm_exclusion_settings' );
						if ( $product_exclusion ) {
							return 'yes' === $product_exclusion['enable_inquiry_form'];
						}
					}

					$product_tags = wp_get_object_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
					foreach ( $product_tags as $tag_id ) {

						$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_exclusion_settings', true ), $post_id, $tag_id, '_ywctm_exclusion_settings' );
						if ( $product_exclusion ) {
							return 'yes' === $product_exclusion['enable_inquiry_form'];
						}
					}

					return $value;

					break;
				case 'custom_button':
				case 'custom_button_loop':
					$product_exclusion = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_exclusion_settings' ), $post_id, '_ywctm_exclusion_settings' );

					if ( $product_exclusion && 'yes' === $product_exclusion['enable_atc_custom_options'] ) {
						return $product_exclusion[ $option ];
					}

					$product_cats = wp_get_object_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );
					foreach ( $product_cats as $cat_id ) {

						$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_exclusion_settings', true ), $post_id, $cat_id, '_ywctm_exclusion_settings' );
						if ( $product_exclusion && 'yes' === $product_exclusion['enable_atc_custom_options'] ) {
							return $product_exclusion[ $option ];
						}
					}

					$product_tags = wp_get_object_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
					foreach ( $product_tags as $tag_id ) {

						$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_exclusion_settings', true ), $post_id, $tag_id, '_ywctm_exclusion_settings' );
						if ( $product_exclusion && 'yes' === $product_exclusion['enable_atc_custom_options'] ) {
							return $product_exclusion[ $option ];
						}
					}

					break;
				case 'price_label':
					$product_exclusion = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_exclusion_settings' ), $post_id, '_ywctm_exclusion_settings' );

					if ( $product_exclusion && 'yes' === $product_exclusion['enable_price_custom_options'] ) {
						return $product_exclusion['custom_price_text'];
					}

					$product_cats = wp_get_object_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );
					foreach ( $product_cats as $cat_id ) {

						$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_exclusion_settings', true ), $post_id, $cat_id, '_ywctm_exclusion_settings' );
						if ( $product_exclusion && 'yes' === $product_exclusion['enable_price_custom_options'] ) {
							return $product_exclusion['custom_price_text'];
						}
					}

					$product_tags = wp_get_object_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
					foreach ( $product_tags as $tag_id ) {

						$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_exclusion_settings', true ), $post_id, $tag_id, '_ywctm_exclusion_settings' );
						if ( $product_exclusion && 'yes' === $product_exclusion['enable_price_custom_options'] ) {
							return $product_exclusion['custom_price_text'];
						}
					}

					break;
				default:
					return $value;
			}

			return $value;
		}

		/**
		 * Enqueue css file
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function enqueue_premium_styles() {

			if ( is_product() ) {
				$product      = wc_get_product();
				$form_enabled = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_enabled', 'hidden' ), $product->get_id(), 'ywctm_inquiry_form_enabled' );

				if ( 'hidden' !== $form_enabled && ( ywctm_exists_inquiry_forms() ) ) {

					$form_custom_css = '';
					$form_type       = 'none';

					//Add styles for inquiry form
					if ( 'hidden' !== $form_enabled ) {

						$in_desc   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_where_show', 'tab' ), $product->get_id(), 'ywctm_inquiry_form_where_show' );
						$style     = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_style', 'classic' ), $product->get_id(), 'ywctm_inquiry_form_style' );
						$form_type = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_type' ), $product->get_id(), 'ywctm_inquiry_form_type' );

						if ( 'desc' === $in_desc && 'toggle' === $style ) {

							$tg_text_color = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_toggle_button_text_color' ), $product->get_id(), 'ywctm_toggle_button_text_color' );
							$tg_back_color = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_toggle_button_background_color' ), $product->get_id(), 'ywctm_toggle_button_background_color' );

							$form_custom_css .= '.ywctm-inquiry-form-wrapper.has-toggle .ywctm-toggle-button{ color:' . $tg_text_color['default'] . '; background-color:' . $tg_back_color['default'] . ';}';
							$form_custom_css .= '.ywctm-inquiry-form-wrapper.has-toggle .ywctm-toggle-button:hover{ color:' . $tg_text_color['hover'] . '; background-color:' . $tg_back_color['hover'] . ';}';

						}
					}

					wp_enqueue_script( 'ywctm-inquiry-form', yit_load_js_file( YWCTM_ASSETS_URL . 'js/inquiry-form.js' ), array( 'jquery' ), YWCTM_VERSION );
					wp_localize_script(
						'ywctm-inquiry-form',
						'ywctm',
						array(
							'form_type'  => $form_type,
							'product_id' => $product->get_id(),
						)
					);

					wp_enqueue_style( 'ywctm-inquiry-form', yit_load_css_file( YWCTM_ASSETS_URL . 'css/inquiry-form.css' ), array(), YWCTM_VERSION );
					wp_add_inline_style( 'ywctm-inquiry-form', $form_custom_css );

				}
			}

			//Add styles for custom button replacing add to cart or price
			$buttons = ywctm_get_active_buttons_id();

			if ( $buttons ) {

				$button_custom_css = '';
				$icon_sets         = array();
				$google_fonts      = array();

				wp_enqueue_style( 'ywctm-button-label', yit_load_css_file( YWCTM_ASSETS_URL . 'css/button-label.css' ), array(), YWCTM_VERSION );
				wp_enqueue_script( 'ywctm-button-label', yit_load_js_file( YWCTM_ASSETS_URL . 'js/button-label-frontend.js' ), array( 'jquery' ), YWCTM_VERSION );

				foreach ( $buttons as $button ) {

					$button_settings = ywctm_get_button_label_settings( $button );
					$used_icons      = get_post_meta( $button, 'ywctm_used_icons', true );
					$icon_sets       = array_unique( array_merge( $icon_sets, ( '' === $used_icons ? array() : $used_icons ) ) );
					$used_fonts      = get_post_meta( $button, 'ywctm_used_fonts', true );
					$google_fonts    = array_unique( array_merge( $google_fonts, ( '' === $used_fonts ? array() : $used_fonts ) ) );

					if ( $button_settings ) {

						$button_custom_css .= '.ywctm-button-' . $button . ' .ywctm-custom-button {';
						$button_custom_css .= 'color:' . $button_settings['text_color']['default'] . ';';
						$button_custom_css .= 'background-color:' . $button_settings['background_color']['default'] . ';';
						$button_custom_css .= 'border-style: solid;';
						$button_custom_css .= 'border-color:' . $button_settings['border_color']['default'] . ';';

						if ( '' !== $button_settings['border_style']['thickness'] ) {
							$button_custom_css .= 'border-width:' . $button_settings['border_style']['thickness'] . 'px;';
						}
						if ( '' !== $button_settings['border_style']['radius'] ) {
							$button_custom_css .= 'border-radius:' . $button_settings['border_style']['radius'] . 'px;';
						}
						if ( '' !== $button_settings['margin_settings']['top'] ) {
							$button_custom_css .= 'margin-top:' . $button_settings['margin_settings']['top'] . 'px;';
						}
						if ( '' !== $button_settings['margin_settings']['bottom'] ) {
							$button_custom_css .= 'margin-bottom:' . $button_settings['margin_settings']['bottom'] . 'px;';
						}
						if ( '' !== $button_settings['margin_settings']['left'] ) {
							$button_custom_css .= 'margin-left:' . $button_settings['margin_settings']['left'] . 'px;';
						}
						if ( '' !== $button_settings['margin_settings']['right'] ) {
							$button_custom_css .= 'margin-right:' . $button_settings['margin_settings']['right'] . 'px;';
						}
						if ( '' !== $button_settings['padding_settings']['top'] ) {
							$button_custom_css .= 'padding-top:' . $button_settings['padding_settings']['top'] . 'px;';
						}
						if ( '' !== $button_settings['padding_settings']['bottom'] ) {
							$button_custom_css .= 'padding-bottom:' . $button_settings['padding_settings']['bottom'] . 'px;';
						}
						if ( '' !== $button_settings['padding_settings']['left'] ) {
							$button_custom_css .= 'padding-left:' . $button_settings['padding_settings']['left'] . 'px;';
						}
						if ( '' !== $button_settings['padding_settings']['right'] ) {
							$button_custom_css .= 'padding-right:' . $button_settings['padding_settings']['right'] . 'px;';
						}
						if ( '' !== $button_settings['width_settings']['width'] ) {
							$button_custom_css .= 'width:' . $button_settings['width_settings']['width'] . ( '%' !== $button_settings['width_settings']['unit'] ? 'px' : '%' ) . ';';
						}

						$button_custom_css .= '}';
						$button_custom_css .= '.ywctm-button-' . $button . ' .ywctm-custom-button:hover {';
						$button_custom_css .= 'color:' . $button_settings['text_color']['hover'] . ';';
						$button_custom_css .= 'background-color:' . $button_settings['background_color']['hover'] . ';';
						$button_custom_css .= 'border-color:' . $button_settings['border_color']['hover'] . ';';
						$button_custom_css .= '}';

						if ( 'none' !== $button_settings['icon_type'] ) {
							$button_custom_css .= '.ywctm-button-' . $button . ' .ywctm-custom-button .ywctm-icon-form, .ywctm-button-' . $button . ' .ywctm-custom-button .custom-icon {';
							if ( ! empty( $button_settings['selected_icon_size'] ) && 'icon' === $button_settings['icon_type'] ) {
								$button_custom_css .= 'font-size:' . $button_settings['selected_icon_size'] . 'px;';
								$button_custom_css .= 'color:' . $button_settings['icon_color']['default'] . ';';
							}
							$button_custom_css .= 'align-self:' . $button_settings['selected_icon_alignment'] . ';';
							$button_custom_css .= '}';
							$button_custom_css .= '.ywctm-button-' . $button . ' .ywctm-custom-button:hover .ywctm-icon-form {';
							$button_custom_css .= 'color:' . $button_settings['icon_color']['hover'] . ';';
							$button_custom_css .= '}';

						}
					}
				}

				if ( ! empty( $icon_sets ) ) {
					foreach ( $icon_sets as $icon_set ) {
						switch ( $icon_set ) {
							case 'fontawesome':
								wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0' );
								break;
							case 'dashicons':
								wp_enqueue_style( 'dashicons' );
								break;
							case 'retinaicon-font':
								wp_enqueue_style( 'ywctm-retinaicon-font', yit_load_css_file( YWCTM_ASSETS_URL . 'css/retinaicon-font.css' ), array(), YWCTM_VERSION );
								break;
						}
					}
				}

				if ( ! empty( $google_fonts ) ) {
					$font_names = array();
					foreach ( $google_fonts as $google_font ) {
						$font_names[] = str_replace( ' ', '+', $google_font ) . ':400,400i,700,700i';
					}
					wp_enqueue_style( 'ywctm-google-fonts', 'https://fonts.googleapis.com/css?family=' . implode( '|', $font_names ) . '&display=swap' );
				}

				wp_add_inline_style( 'ywctm-button-label', $button_custom_css );

			}

		}

		/**
		 * Removes reviews tab from single page product
		 *
		 * @param   $tabs array
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function disable_reviews_tab( $tabs ) {

			global $post;

			$disable_review = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_disable_review' ), $post->ID, 'ywctm_disable_review' );

			if ( 'yes' === $disable_review && ! is_user_logged_in() ) {
				unset( $tabs['reviews'] );
			}

			return $tabs;

		}

		/**
		 * Add inquiry form tab to single product page
		 *
		 * @param   $tabs array
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_inquiry_form_tab( $tabs ) {

			global $post;

			$enabled = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_enabled', 'hidden' ), $post->ID, 'ywctm_inquiry_form_enabled' );
			$in_tab  = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_where_show', 'tab' ), $post->ID, 'ywctm_inquiry_form_where_show' );

			if ( 'hidden' !== $enabled && 'tab' === $in_tab ) {

				$show_form = apply_filters( 'ywctm_get_exclusion', ( 'exclusion' !== $enabled ), $post->ID, 'inquiry_form' );

				if ( ! $show_form ) {
					return $tabs;
				}

				$active_form = $this->get_active_inquiry_form( $post->ID );

				if ( ! empty( $active_form ) && '' !== $active_form['form_id'] ) {

					$tab_title = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_tab_title' ), $post->ID, 'ywctm_inquiry_form_tab_title' );

					//APPLY_FILTERS: ywctm_inquiry_form_title: last chance to change the Form tab title
					$tab_title            = apply_filters( 'ywctm_inquiry_form_title', $tab_title );
					$tabs['inquiry_form'] = array(
						'title'     => $tab_title,
						'priority'  => 40,
						'callback'  => array( $this, 'get_inquiry_form' ),
						'form_type' => $active_form['form_type'],
						'form_id'   => $active_form['form_id'],
					);

				}
			}

			return $tabs;

		}

		/**
		 * Get active inquiry form
		 *
		 * @param $post_id integer
		 *
		 * @return  array
		 * @since   1.5.1
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_active_inquiry_form( $post_id ) {

			$active_form = array();
			$form_type   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_type' ), $post_id, 'ywctm_inquiry_form_type' );

			if ( 'none' !== $form_type && ( ywctm_exists_inquiry_forms() ) ) {

				$active_form = array(
					'form_type' => $form_type,
					'form_id'   => ywctm_get_localized_form( $form_type, $post_id ),
				);

			}

			return $active_form;

		}

		/**
		 * Check if YITH WooCommerce Add-ons options should be printed
		 *
		 * @return  void
		 * @since   2.0.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function show_wapo_if_hidden() {

			global $post, $Product_Addon_Display;//phpcs:ignore

			/* Show YITH WooCommerce Product Add-Ons*/
			if ( function_exists( 'YITH_WAPO' ) && $this->check_price_hidden( false, $post->ID ) ) {
				$priority = apply_filters( 'ywctm_wapo_position', 15 );
				add_action( 'woocommerce_single_product_summary', array( $this, 'show_wapo_options' ), $priority );
			}

			/* Show WooCommerce Product Add-Ons*/
			if ( $Product_Addon_Display && $this->check_price_hidden( false, $post->ID ) ) {                        //phpcs:ignore
				add_action( 'woocommerce_single_product_summary', array( $Product_Addon_Display, 'display' ), 20 ); //phpcs:ignore
			}

		}

		/**
		 * Print YITH WooCommerce Add-ons options
		 *
		 * @return  void
		 * @since   2.0.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function show_wapo_options() {
			echo do_shortcode( '[yith_wapo_show_options]' );
		}

		/**
		 * Add inquiry form directly to single product page
		 *
		 * @return  void
		 * @since   1.5.1
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_inquiry_form_page() {

			global $post;

			$enabled = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_enabled', 'hidden' ), $post->ID, 'ywctm_inquiry_form_enabled' );
			$in_desc = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_where_show', 'tab' ), $post->ID, 'ywctm_inquiry_form_where_show' );

			if ( 'hidden' !== $enabled && 'desc' === $in_desc ) {

				$show_form = apply_filters( 'ywctm_get_exclusion', ( 'exclusion' !== $enabled ), $post->ID, 'inquiry_form' );

				if ( ! $show_form ) {
					return;
				}

				$priority = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_position', '15' ), $post->ID, 'ywctm_inquiry_form_position' );
				//APPLY_FILTERS: ywctm_inquiry_form_hook: hook where print the inquiry form
				$hook = apply_filters( 'ywctm_inquiry_form_hook', 'woocommerce_single_product_summary' );
				//APPLY_FILTERS: ywctm_inquiry_form_priority: priority to apply to the function
				$priority = apply_filters( 'ywctm_inquiry_form_priority', $priority );

				add_action( $hook, array( $this, 'inquiry_form_shortcode' ), $priority );

			}

		}

		/**
		 * Print Inquiry form on product page
		 *
		 * @return  void
		 * @since   1.5.1
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function inquiry_form_shortcode() {

			global $post;

			$active_form = $this->get_active_inquiry_form( $post->ID );

			if ( ! empty( $active_form ) && '' !== $active_form['form_id'] ) {

				$tab_title   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_tab_title' ), $post->ID, 'ywctm_inquiry_form_tab_title' );
				$button_text = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_toggle_button_text' ), $post->ID, 'ywctm_toggle_button_text' );
				$form_style  = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_style' ), $post->ID, 'ywctm_inquiry_form_style' );

				//APPLY_FILTERS: ywctm_inquiry_form_title: last chance to change the Form tab title
				$tab_title = apply_filters( 'ywctm_inquiry_form_title', $tab_title );
				//APPLY_FILTERS: ywctm_inquiry_form_title_wrapper: the wrapper of the form title
				$title_wrapper = apply_filters( 'ywctm_inquiry_form_title_wrapper', 'h3' );
				?>
				<div class="ywctm-inquiry-form-wrapper <?php echo ( 'toggle' === $form_style ) ? 'has-toggle' : ''; ?>">
					<?php
					if ( 'toggle' === $form_style ) {
						?>
						<div class="ywctm-toggle-button"><?php echo $button_text; ?></div>
						<?php
					} else {
						echo sprintf( '<%1$s class="ywctm-form-title">%2$s</%1$s>', $title_wrapper, $tab_title );
					}
					?>
					<div class="ywctm-toggle-content">
						<?php $this->get_inquiry_form( 'inquiry_form', $active_form ); ?>
					</div>
				</div>
				<?php
			}

		}

		/**
		 * Inquiry form tab template
		 *
		 * @param   $key integer
		 * @param   $tab array
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_inquiry_form( $key, $tab ) {

			if ( 'inquiry_form' !== $key ) {
				return;
			}

			global $product;

			$product_id = $product ? $product->get_id() : 0;

			$shortcode = '';

			switch ( $tab['form_type'] ) {
				case 'yit-contact-form':
					$shortcode = '[contact_form name="' . $tab['form_id'] . '"]';
					break;
				case 'contact-form-7':
					$shortcode = '[contact-form-7 id="' . $tab['form_id'] . '"]';
					break;
				case 'ninja-forms':
					$shortcode = '[ninja_form  id=' . $tab['form_id'] . ']';
					break;
				case 'formidable-forms':
					$shortcode = '[formidable  id=' . $tab['form_id'] . ']';
					break;
				case 'gravity-forms':
					$shortcode = '[gravityform  id=' . $tab['form_id'] . apply_filters( 'ywctm_gravity_ajax', ' ajax=true' ) . ']';
					break;
			}

			//DO_ACTION: ywctm_before_inquiry_form: execute code before printing the inquiry form
			do_action( 'ywctm_before_inquiry_form', $product );

			echo apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_text_before_form' ), $product_id, 'ywctm_text_before_form' );
			echo do_shortcode( $shortcode );

			//DO_ACTION: ywctm_after_inquiry_form: execute code after printing the inquiry form
			do_action( 'ywctm_after_inquiry_form', $product );

		}

		/**
		 * Add a custom button into a shortcode
		 *
		 * @return  string
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function print_custom_button_shortcode() {

			ob_start();
			$this->show_custom_button( true );

			return ob_get_clean();
		}

		/**
		 * Add inquiry form into a shortcode
		 *
		 * @return  string
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function print_inquiry_form_shortcode() {

			ob_start();
			$this->inquiry_form_shortcode();

			return ob_get_clean();
		}

		/**
		 * Add a custom button in product details and shop page
		 *
		 * @param   $in_shortcode boolean
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function show_custom_button( $in_shortcode = false ) {

			global $post;

			if ( ! isset( $post ) ) {
				return;
			}

			//APPLY_FILTERS: ywctm_allowed_page_hooks: hooks enabled for single product page
			$page_actions = apply_filters( 'ywctm_allowed_page_hooks', array( 'woocommerce_single_product_summary' ) );
			//APPLY_FILTERS: ywctm_allowed_shop_hooks: hooks enabled for shop page
			$loop_actions = apply_filters( 'ywctm_allowed_shop_hooks', array( 'woocommerce_after_shop_loop_item' ) );
			$is_loop      = in_array( current_action(), $loop_actions, true );
			$is_page      = in_array( current_action(), $page_actions, true ) || $in_shortcode;
			$button_id    = 'none';

			if ( $is_page && $this->check_hide_add_cart( true ) ) {
				$button_id = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_custom_button_settings' ), $post->ID, 'ywctm_custom_button_settings' );
				$button_id = apply_filters( 'ywctm_get_exclusion', $button_id, $post->ID, 'custom_button' );
			}

			if ( $is_loop && $this->check_hide_add_cart( false, false, true ) ) {
				$button_id = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_custom_button_settings_loop' ), $post->ID, 'ywctm_custom_button_settings_loop' );
				$button_id = apply_filters( 'ywctm_get_exclusion', $button_id, $post->ID, 'custom_button_loop' );
			}

			if ( ywctm_is_wpml_active() ) {
				$button_id = yit_wpml_object_id( $button_id, 'ywctm-button-label', true, wpml_get_current_language() );
			}

			if ( $this->apply_catalog_mode( $post->ID ) && 'none' !== $button_id ) {
				$this->get_custom_button_template( $button_id, 'atc', $is_loop );
			}

		}

		/**
		 * Get custom button template
		 *
		 * @param   $button_id integer|boolean
		 * @param   $replaces  string
		 * @param   $is_loop   boolean
		 *
		 * @return  void
		 * @since   1.0.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_custom_button_template( $button_id = false, $replaces = 'atc', $is_loop = false ) {

			global $post, $product;

			if ( ! isset( $post ) || ! $product ) {
				return;
			}

			if ( false === $button_id ) {

				if ( 'price' === $replaces ) {
					$button_id = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_custom_price_text_settings' ), $post->ID, 'ywctm_custom_price_text_settings' );
				} else {
					$button_id = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_custom_button_settings' ), $post->ID, 'ywctm_custom_button_settings' );
				}
			}

			$button_settings = ywctm_get_button_label_settings( $button_id );
			$is_published    = 'publish' === get_post_status( $button_id );

			if ( ! $button_settings || ( ! $is_published && 'legacy' !== $button_id ) ) {
				return;
			}

			//APPLY_FILTERS: ywctm_custom_button_additional_classes: additional classes for custom button
			$custom_classes = apply_filters( 'ywctm_custom_button_additional_classes', '', $button_id );
			$classes        = array( 'ywctm-custom-button', $custom_classes );

			switch ( $button_settings['button_url_type'] ) {
				case 'custom':
					$custom_url  = ywctm_get_custom_button_url_override( $product, $replaces, $is_loop );
					$button_type = 'a';
					$button_url  = 'href="' . ( '' === $custom_url ? $button_settings['button_url'] : $custom_url ) . '"';
					break;
				case 'product':
					$button_type = 'a';
					$button_url  = 'href="' . $product->get_permalink() . '"';
					break;
				default:
					$button_type = 'span';
					$button_url  = '';
			}
			//APPLY_FILTERS: ywctm_custom_button_open_new_page: check if button link opens in new page
			if ( apply_filters( 'ywctm_custom_button_open_new_page', false, $button_id ) && 'none' !== $button_settings['button_url_type'] ) {
				$button_url .= ' target="_blank"';
			}

			$button_text = '<span class="ywctm-inquiry-title">' . ywctm_parse_icons( $button_settings['label_text'] ) . '</span>';

			switch ( $button_settings['icon_type'] ) {
				case 'icon':
					$button_icon = '<span class="ywctm-icon-form ' . ywctm_get_icon_class( $button_settings['selected_icon'] ) . '"></span>';
					break;
				case 'custom':
					$button_icon = '<span class="custom-icon"><img src="' . $button_settings['custom_icon'] . '"></span>';
					break;
				default:
					$button_icon = '';
			}

			?>
			<div class="ywctm-custom-button-container ywctm-button-<?php echo $button_id; ?>" data-product_id="<?php echo $product->get_id(); ?>">
				<?php echo sprintf( '<%1$s class="%2$s" %3$s>%4$s%5$s</%1$s>', $button_type, implode( ' ', $classes ), $button_url, $button_icon, $button_text ); ?>
			</div>
			<?php

		}

		/**
		 * Hides product price from single product page
		 *
		 * @param   $classes array
		 *
		 * @return  array
		 * @since   1.4.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function hide_price_single_page( $classes ) {

			if ( $this->check_hide_price() ) {

				$args = array(
					'.woocommerce-variation-price',
				);

				//APPLY_FILTERS: ywctm_catalog_price_classes: CSS classes of price element
				$classes = array_merge( $classes, apply_filters( 'ywctm_catalog_price_classes', $args ) );

			}

			return $classes;

		}

		/**
		 * Hides on-sale badge if price is hidden
		 *
		 * @param   $is_on_sale boolean
		 * @param   $product    WC_Product
		 *
		 * @return  boolean
		 * @since   1.5.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function hide_on_sale( $is_on_sale, $product ) {

			if ( $this->check_hide_price( $product->get_id() ) ) {
				$is_on_sale = false;
			}

			return $is_on_sale;

		}

		/**
		 * Check if price is hidden
		 *
		 * @param   $hide       boolean
		 * @param   $product_id integer
		 *
		 * @return  boolean
		 * @since   1.4.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_price_hidden( $hide, $product_id ) {

			if ( $this->check_hide_price( $product_id ) && $this->apply_catalog_mode( $product_id ) ) {
				$hide = true;
			}

			return $hide;

		}

		/**
		 * Check if price is hidden
		 *
		 * @param   $product_id integer|boolean
		 *
		 * @return  boolean
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_hide_price( $product_id = false ) {

			global $post;

			if ( ! $product_id && ! isset( $post ) ) {
				return false;
			}

			$product_id = ( $product_id ) ? $product_id : $post->ID;

			if ( ywctm_is_wpml_active() && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
			}

			$product = wc_get_product( $product_id );

			if ( ! $product ) {
				return false;
			}

			$price_settings_general = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_price_settings' ), $product_id, 'ywctm_hide_price_settings' );
			$behavior               = $price_settings_general['action'];

			if ( 'all' !== $price_settings_general['items'] ) {
				$behavior = apply_filters( 'ywctm_get_exclusion', ( 'hide' === $behavior ? 'show' : 'hide' ), $product_id, 'price', $behavior );
			}

			return ( 'hide' === $behavior && $this->apply_catalog_mode( $product_id ) );

		}

		/**
		 * Check for which users will not see the price
		 *
		 * @param   $price   string
		 * @param   $product integer
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function show_product_price( $price, $product ) {

			if ( defined( 'WOOCOMMERCE_CHECKOUT' ) || defined( 'WOOCOMMERCE_CART' ) || apply_filters( 'ywctm_ajax_admin_check', is_admin(), $product ) || ( apply_filters( 'ywctm_prices_only_on_cart', false ) && ( current_filter() === 'woocommerce_get_price' || current_filter() === 'woocommerce_product_get_price' ) ) ) {
				return $price;
			}

			if ( $product instanceof WC_Product ) {
				$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
			} else {
				$product_id = $product;
			}

			if ( $this->check_hide_price( $product_id ) && $this->apply_catalog_mode( $product_id ) ) {

				if ( ( current_filter() === 'woocommerce_get_price' || current_filter() === 'woocommerce_product_get_price' ) ) {

					if ( ( class_exists( 'YITH_Request_Quote_Premium' ) && get_option( 'ywraq_show_button_near_add_to_cart' ) === 'yes' ) || is_account_page() ) {
						$value = 0;
					} else {
						$value = '';
					}

					$price = apply_filters( 'ywctm_hidden_price_meta', $value );

				} elseif ( current_filter() === 'yith_ywraq_hide_price_template' ) {
					$price = '';
				} else {

					$label_id = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_custom_price_text_settings' ), $product_id, 'ywctm_custom_price_text_settings' );
					$label_id = apply_filters( 'ywctm_get_exclusion', $label_id, $product_id, 'price_label' );

					if ( ywctm_is_wpml_active() ) {
						$label_id = yit_wpml_object_id( $label_id, 'ywctm-button-label', true, wpml_get_current_language() );
					}

					if ( 'none' !== $label_id ) {
						ob_start();
						$this->get_custom_button_template( $label_id, 'price' );
						$price = ob_get_clean();
					} else {
						$price = '';
					}
				}
			}

			return apply_filters( 'ywctm_hide_price_anyway', $price, $product_id );

		}

		/**
		 * Hide discount quantity table from YITH WooCommerce Dynamic Pricing Discount id the catalog mode is active
		 *
		 * @param   $value   boolean
		 * @param   $product WC_Product
		 *
		 * @return boolean
		 * @since  2.0.0
		 * @author Francesco Licandro
		 */
		public function hide_discount_quantity_table( $value, $product ) {
			return $product && $this->check_hide_add_cart( true, $product->get_id() );
		}

		/**
		 * Hides product price and add to cart in YITH Quick View
		 *
		 * @return  void
		 * @since   1.0.7
		 * @author  Francesco Licandro
		 */
		public function check_quick_view() {
			if ( $this->is_quick_view() ) {
				$this->hide_add_to_cart_quick_view();
				$this->hide_price_quick_view();
			}
		}

		/**
		 * Hide price for product in quick view
		 *
		 * @return  void
		 * @since   1.0.7
		 * @author  Francesco Licandro
		 */
		public function hide_price_quick_view() {

			if ( $this->check_hide_price() ) {

				$args = array(
					'.single_variation_wrap .single_variation',
					'.yith-quick-view .price',
					'.price-wrapper',
				);

				//APPLY_FILTERS: ywctm_catalog_price_classes: CSS classes of price element
				$classes = implode( ', ', apply_filters( 'ywctm_catalog_price_classes', $args ) );
				ob_start();

				?>
				<style type="text/css">
					<?php echo $classes; ?>
					{
						display: none !important
					;
					}
				</style>
				<?php

				echo ob_get_clean();

			}

		}

		/**
		 * Hide add to cart button in quick view
		 *
		 * @return  void
		 * @since   1.0.7
		 * @author  Francesco Licandro
		 */
		public function hide_add_to_cart_quick_view() {

			if ( $this->check_hide_add_cart( true ) ) {

				$hide_variations = get_option( 'ywctm_hide_variations' );
				$args            = array(
					'form.cart button.single_add_to_cart_button',
				);

				$theme_name = ywctm_get_theme_name();

				if ( 'oceanwp' === $theme_name ) {
					$args[] = 'form.cart';
				}

				if ( ! class_exists( 'YITH_YWRAQ_Frontend' ) || ( ( class_exists( 'YITH_Request_Quote_Premium' ) ) && ! YITH_Request_Quote_Premium()->check_user_type() ) ) {
					$args[] = 'form.cart .quantity';
				}

				if ( 'yes' === $hide_variations ) {

					$args[] = 'table.variations';
					$args[] = 'form.variations_form';
					$args[] = '.single_variation_wrap .variations_button';

				}

				//APPLY_FILTERS: ywctm_cart_widget_classes: CSS selector of add to cart buttons
				$classes = implode( ', ', apply_filters( 'ywctm_catalog_classes', $args ) );

				ob_start();
				?>
				<style type="text/css">
					<?php echo $classes; ?>
					{
						display: none !important
					}
				</style>
				<?php
				echo ob_get_clean();
			}

		}

		/**
		 * Themes Integration
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function themes_integration() {

			$theme_name = strtolower( ywctm_get_theme_name() );

			switch ( $theme_name ) {
				case 'flatsome':
					add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'hide_add_to_cart_quick_view' ) );
					add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'hide_price_quick_view' ) );
					add_filter( 'ywctm_ajax_admin_check', '__return_false' );
					add_filter( 'ywctm_modify_woocommerce_after_shop_loop_item', '__return_false' );
					break;
				case 'oceanwp':
					add_action( 'ocean_woo_quick_view_product_content', array( $this, 'hide_add_to_cart_quick_view' ) );
					add_action( 'ocean_woo_quick_view_product_content', array( $this, 'hide_price_quick_view' ) );
					add_filter( 'ywctm_modify_woocommerce_after_shop_loop_item', '__return_false' );
					break;
				case 'astra':
					add_action( 'astra_woo_quick_view_product_summary', array( $this, 'hide_add_to_cart_quick_view' ) );
					add_action( 'astra_woo_quick_view_product_summary', array( $this, 'hide_price_quick_view' ) );
					add_filter( 'ywctm_modify_woocommerce_after_shop_loop_item', '__return_false' );
					break;
				case 'avada':
					add_filter( 'ywctm_modify_woocommerce_after_shop_loop_item', '__return_false' );
					break;
			}

		}

		/**
		 * Checks if product price needs to be hidden
		 *
		 * @param   $x          boolean
		 * @param   $product_id integer|boolean
		 *
		 * @return  boolean
		 * @since   1.0.2
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_product_price_single( $x = true, $product_id = false ) {
			return $this->check_hide_price( $product_id );
		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Register plugins for activation tab
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YWCTM_INIT, YWCTM_SECRET_KEY, YWCTM_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YWCTM_SLUG, YWCTM_INIT );
		}

		/**
		 * Plugin row meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $new_row_meta_args
		 * @param   $plugin_meta
		 * @param   $plugin_file
		 * @param   $plugin_data
		 * @param   $status
		 * @param   $init_file
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWCTM_INIT' ) {
			$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $links | links plugin array
		 *
		 * @return  mixed
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, true );

			return $links;
		}

	}

}
