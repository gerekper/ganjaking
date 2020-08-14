<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WC_Catalog_Mode_Premium' ) ) {

	/**
	 * Implements features of YITH WooCommerce Catalog Mode plugin
	 *
	 * @class   YITH_WC_Catalog_Mode
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 * @package Yithemes
	 */
	class YITH_WC_Catalog_Mode_Premium extends YITH_WC_Catalog_Mode {

		/**
		 * @var array
		 */
		protected $_user_geolocation = array();

		public $_yit_contact_form = false;
		public $_contact_form_7 = false;
		public $_gravity_forms = false;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Catalog_Mode_Premium
		 * @since 1.3.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self;

			}

			return self::$instance;

		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			parent::__construct();

			$this->includes();

			add_action( 'init', array( $this, 'init_inquiry_forms' ) );
			add_action( 'init', array( $this, 'init_multivendor_integration' ), 20 );

			if ( is_admin() ) {

				add_action( 'ywctm_exclusions', array( YWCTM_Exclusions_Table(), 'output' ) );
				add_action( 'ywctm_custom_url', array( YWCTM_Custom_Url_Table(), 'output' ) );
				add_action( 'ywctm_alternative_text', array( YWCTM_Alternative_Text_Table(), 'output' ) );
				add_action( 'ywctm_vendor_exclusions', array( YWCTM_Vendors_Table(), 'output' ) );

				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_premium_scripts_admin' ) );
				add_action( 'woocommerce_admin_field_icon', 'YITH_Icon_List::output' );

				add_action( 'product_cat_edit_form_fields', array( $this, 'write_taxonomy_options' ), 99 );
				add_action( 'product_tag_edit_form_fields', array( $this, 'write_taxonomy_options' ), 99 );
				add_action( 'edited_product_cat', array( $this, 'save_taxonomy_options' ) );
				add_action( 'edited_product_tag', array( $this, 'save_taxonomy_options' ) );

			}

			if ( get_option( 'ywctm_enable_plugin' ) == 'yes' && $this->check_user_admin_enable() ) {

				if ( ! is_admin() || $this->is_quick_view() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

					add_filter( 'ywctm_get_exclusion', array( $this, 'get_exclusion' ), 10, 3 );
					add_filter( 'woocommerce_product_tabs', array( $this, 'add_inquiry_form_tab' ) );
					add_filter( 'woocommerce_product_tabs', array( $this, 'disable_reviews_tab' ), 98 );
					add_filter( 'woocommerce_product_get_price', array( $this, 'show_product_price' ), 10, 2 );
					add_filter( 'woocommerce_get_price_html', array( $this, 'show_product_price' ), 12, 2 );
					add_filter( 'woocommerce_get_variation_price_html', array( $this, 'show_product_price' ), 12, 2 );
					add_filter( 'yith_ywraq_hide_price_template', array( $this, 'show_product_price' ), 12, 2 );

					add_filter( 'ywctm_check_price_hidden', array( $this, 'check_price_hidden' ), 10, 2 );
					add_filter( 'woocommerce_product_is_on_sale', array( $this, 'hide_on_sale' ), 10, 2 );

					add_action( 'woocommerce_single_product_summary', array( $this, 'show_custom_button' ), 20 );
					add_action( 'woocommerce_after_shop_loop_item', array( $this, 'show_custom_button_loop' ), 20 );
					add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_premium_styles' ) );
					add_action( 'wp_head', array( $this, 'custom_button_css' ) );
					add_action( 'init', array( $this, 'geolocate_user' ) );

					add_filter( 'ywctm_css_classes', array( $this, 'hide_price_single_page' ) );

					add_action( 'woocommerce_before_single_product', array( $this, 'add_inquiry_form_page' ), 5 );

				}

			}

			add_filter( 'yit_get_contact_forms', array( $this, 'yit_get_contact_forms' ) );
			add_filter( 'wpcf7_get_contact_forms', array( $this, 'wpcf7_get_contact_forms' ) );
			add_filter( 'gravity_get_contact_forms', array( $this, 'gravity_get_contact_forms' ) );
			add_filter( 'wpcf7_mail_components', array( $this, 'wpcf7_mail_components' ), 10, 2 );
			add_filter( 'gform_pre_send_email', array( $this, 'gform_pre_send_email' ), 10, 2 );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// compatibility with quick view
			add_action( 'yith_wcqv_product_summary', array( $this, 'check_quick_view' ) );
			add_action( 'after_setup_theme', array( $this, 'flatsome_integration' ) );

		}

		/**
		 * Files inclusion
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		private function includes() {

			include_once( 'includes/class-yith-icon.php' );
			include_once( 'includes/functions-ywctm-ip.php' );

			if ( is_admin() ) {
				include_once( 'includes/admin/class-yith-custom-table.php' );
				include_once( 'includes/admin/meta-boxes/class-ywctm-meta-box.php' );
				include_once( 'templates/admin/exclusions-table.php' );
				include_once( 'templates/admin/alternative-text-table.php' );
				include_once( 'templates/admin/custom-url-table.php' );
				include_once( 'templates/admin/vendors-table.php' );
				include_once( 'templates/admin/icon-list.php' );
				include_once( 'templates/admin/class-yith-wc-custom-country-select.php' );
				include_once( 'templates/admin/class-ywctm-languages-form-table.php' );
				include_once( 'templates/admin/class-ywctm-languages-url-table.php' );
			}

		}

		/**
		 * FORM PLUGINS FUNCTIONS
		 */

		/**
		 * Set active form plugins
		 *
		 * @return  void
		 * @since   1.5.0
		 * @author  Alberto Ruggiero
		 */
		public function init_inquiry_forms() {

			$this->_yit_contact_form = function_exists( 'YIT_Contact_Form' );
			$this->_contact_form_7   = function_exists( 'wpcf7_contact_form' );
			$this->_gravity_forms    = function_exists( 'gravity_form' );

		}

		/**
		 * Check if at least a form plugin is active
		 *
		 * @return  boolean
		 * @since   1.5.0
		 * @author  Alberto Ruggiero
		 */
		public function exists_inquiry_forms() {

			$form_plugins = $this->get_active_form_plugins();
			unset( $form_plugins['none'] );

			return ( ! empty( $form_plugins ) );

		}

		/**
		 * Get active form plugins
		 *
		 * @return  array
		 * @since   1.5.0
		 * @author  Alberto Ruggiero
		 */
		public function get_active_form_plugins() {

			$active_plugins = array(
				'none' => __( 'None', 'yith-woocommerce-catalog-mode' ),
			);

			if ( YITH_WCTM()->_yit_contact_form ) {
				$active_plugins['yit-contact-form'] = __( 'YIT Contact Form', 'yith-woocommerce-catalog-mode' );
			}

			if ( YITH_WCTM()->_contact_form_7 ) {
				$active_plugins['contact-form-7'] = __( 'Contact Form 7', 'yith-woocommerce-catalog-mode' );
			}

			if ( YITH_WCTM()->_gravity_forms ) {
				$active_plugins['gravity-forms'] = __( 'Gravity Forms', 'yith-woocommerce-catalog-mode' );
			}

			return $active_plugins;
		}

		/**
		 * Get list of forms by YIT Contact Form plugin
		 *
		 * @param   $array
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function yit_get_contact_forms( $array = array() ) {

			if ( ! $this->_yit_contact_form ) {
				return array( '' => __( 'Plugin not activated or not installed', 'yith-woocommerce-catalog-mode' ) );
			}

			$forms = get_posts( array( 'post_type' => YIT_Contact_Form()->contact_form_post_type ) );

			foreach ( $forms as $form ) {
				$array[ $form->post_name ] = $form->post_title;
			}

			if ( $array == array() ) {
				return array( '' => __( 'No contact form found', 'yith-woocommerce-catalog-mode' ) );
			}

			return $array;

		}

		/**
		 * Get list of forms by Contact Form 7 plugin
		 *
		 * @param   $array
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function wpcf7_get_contact_forms( $array = array() ) {

			if ( ! $this->_contact_form_7 ) {
				return array( '' => __( 'Plugin not activated or not installed', 'yith-woocommerce-catalog-mode' ) );
			}

			$forms = WPCF7_ContactForm::find();

			foreach ( $forms as $form ) {
				$array[ $form->id() ] = $form->title();
			}

			if ( $array == array() ) {
				return array( '' => __( 'No contact form found', 'yith-woocommerce-catalog-mode' ) );
			}

			return $array;

		}

		/**
		 * Get list of forms by Gravity Forms plugin
		 *
		 * @param   $array
		 *
		 * @return  array
		 * @since   1.0.7
		 *
		 * @author  Alberto Ruggiero
		 */
		public function gravity_get_contact_forms( $array = array() ) {

			if ( ! $this->_gravity_forms ) {
				return array( '' => __( 'Plugin not activated or not installed', 'yith-woocommerce-catalog-mode' ) );
			}

			$forms = RGFormsModel::get_forms( null, 'title' );

			foreach ( $forms as $form ) {
				$array[ $form->id ] = $form->title;
			}

			if ( $array == array() ) {
				return array( '' => __( 'No contact form found', 'yith-woocommerce-catalog-mode' ) );
			}

			return $array;

		}

		/**
		 * Append Product page permalink to mail body (WPCF7)
		 *
		 * @param   $components
		 * @param   $contact_form
		 *
		 * @return  mixed
		 * @since   1.0.8
		 *
		 * @author  Alberto Ruggiero
		 */
		public function wpcf7_mail_components( $components, WPCF7_ContactForm $contact_form ) {

			if ( isset( $_REQUEST['ywctm-product-id'] ) ) {

				$post_id = $_REQUEST['ywctm-product-id'];

				if ( class_exists( 'SitePress' ) ) {

					$contact_form_7 = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_contact_form_7_id_wpml' ), $post_id, 'ywctm_inquiry_contact_form_7_id_wpml' );
					$page_language  = wpml_get_language_information( null, $post_id );
					$contact_form_7 = ( isset( $contact_form_7[ $page_language['language_code'] ] ) ? $contact_form_7[ $page_language['language_code'] ] : '' );

				} else {

					$contact_form_7 = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_contact_form_7_id' ), $post_id, 'ywctm_inquiry_contact_form_7_id' );

				}

				if ( $contact_form->id() == $contact_form_7 ) {

					if ( apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_product_permalink' ), $post_id, 'ywctm_inquiry_product_permalink' ) == 'yes' ) {

						$form_atts    = $contact_form->get_properties();
						$field_label  = __( 'Related product', 'yith-woocommerce-catalog-mode' );
						$product      = wc_get_product( $post_id );
						$product_link = $product->get_permalink();
						$product_name = $product->get_formatted_name();

						if ( ! $form_atts['mail']['use_html'] ) {

							$field_data = "{$field_label}: {$product_name} - {$product_link}\n\n";

						} else {

							ob_start(); ?>

                            <p>
								<?php echo $field_label; ?>: <a
                                    href="<?php echo $product_link ?>"><?php echo $product_name ?></a>
                            </p>

							<?php $field_data = ob_get_clean();

						}

						$components['body'] = $field_data . $components['body'];

					}

				}
			}

			return $components;

		}

		/**
		 * Append Product page permalink to mail body (Gravity Forms)
		 *
		 * @param   $components
		 * @param   $mail_format
		 *
		 * @return  mixed
		 * @since   1.0.8
		 *
		 * @author  Alberto Ruggiero
		 */
		public function gform_pre_send_email( $components, $mail_format ) {

			if ( isset( $_REQUEST['ywctm-product-id'] ) ) {

				$post_id = $_REQUEST['ywctm-product-id'];

				if ( apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_product_permalink' ), $post_id, 'ywctm_inquiry_product_permalink' ) == 'yes' ) {

					$field        = '';
					$lead         = '';
					$field_label  = __( 'Related product', 'yith-woocommerce-catalog-mode' );
					$product      = wc_get_product( $post_id );
					$product_link = $product->get_permalink();
					$product_name = $product->get_formatted_name();

					if ( $mail_format != 'html' ) {

						$field_data = "{$field_label}: {$product_name} - {$product_link}\n\n";

					} else {

						ob_start(); ?>

                        <table width="99%" border="0" cellpadding="1" cellspacing="0" bgcolor="#EAEAEA">
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF">
                                        <tr bgcolor="<?php echo apply_filters( 'gform_email_background_color_label', '#EAF2FA', $field, $lead ); ?>">
                                            <td colspan="2">
                                                <font
                                                    style="font-family: sans-serif; font-size:12px;"><strong><?php echo $field_label ?></strong></font>
                                            </td>
                                        </tr>
                                        <tr bgcolor="<?php echo apply_filters( 'gform_email_background_color_data', '#FFFFFF', $field, $lead ); ?>">
                                            <td width="20">&nbsp;</td>
                                            <td>
                                                <a href="<?php echo $product_link ?>"
                                                   style="font-family: sans-serif; font-size:12px;"><?php echo $product_name ?></a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <br />

						<?php $field_data = ob_get_clean();

					}

					$components['message'] = $field_data . $components['message'];

				}


			}

			return $components;

		}

		/**
		 * MULTIVENDOR FUNCTIONS
		 */

		/**
		 * Add YITH WooCommerce Multi Vendor integration
		 *
		 * @return  void
		 * @since   1.3.0
		 * @author  Alberto Ruggiero
		 */
		public function init_multivendor_integration() {

			if ( $this->is_multivendor_active() ) {

				include_once( 'includes/class-ywctm-multivendor.php' );

			}

		}

		/**
		 * Check if YITH WooCommerce Multi Vendor is active
		 *
		 * @return  bool
		 * @since   1.3.0
		 * @author  Alberto Ruggiero
		 */
		public function is_multivendor_active() {

			return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM;

		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Enqueue script file
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function enqueue_premium_scripts_admin() {

			if ( ! empty( $_GET['page'] ) && ( $_GET['page'] == $this->_panel_page || $_GET['page'] == 'yith_vendor_ctm_settings' ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_script( 'ywctm-admin', YWCTM_ASSETS_URL . 'js/ywctm-admin' . $suffix . '.js', array( 'jquery' ) );

				$args = array(
					'vendor_id' => '0',
				);

				if ( $this->is_multivendor_active() ) {

					if ( YWCTM_MultiVendor()->check_ywctm_vendor_enabled() ) {

						$vendor            = yith_get_vendor( 'current', 'user' );
						$args['vendor_id'] = $vendor->id;

					}

				}

				wp_localize_script( 'ywctm-admin', 'ywctm', $args );

				wp_enqueue_style( 'ywctm-style', YWCTM_ASSETS_URL . 'css/yith-catalog-mode-premium-admin' . $suffix . '.css' );
			}

		}

		/**
		 * Add YWCTM fields in category/tag edit page
		 *
		 * @param   $taxonomy
		 *
		 * @return  void
		 * @since   1.3.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function write_taxonomy_options( $taxonomy ) {

			if ( get_option( 'ywctm_hide_add_to_cart_single' ) == 'yes' || get_option( 'ywctm_hide_add_to_cart_loop' ) == 'yes' || get_option( 'ywctm_hide_price' ) == 'yes' ) {

				$atc_field   = __( 'Show "Add to cart" button anyway', 'yith-woocommerce-catalog-mode' );
				$price_field = __( 'Show price anyway', 'yith-woocommerce-catalog-mode' );

				if ( get_option( 'ywctm_exclude_hide_price' ) == 'yes' || get_option( 'ywctm_exclude_hide_add_to_cart' ) == 'yes' ) {

					if ( get_option( 'ywctm_exclude_hide_add_to_cart_reverse' ) == 'yes' ) {
						$atc_field = __( 'Hide "Add to cart" button anyway', 'yith-woocommerce-catalog-mode' );
					}

					if ( get_option( 'ywctm_exclude_hide_price_reverse' ) == 'yes' ) {
						$price_field = __( 'Hide price anyway', 'yith-woocommerce-catalog-mode' );
					}

				}


				$add_to_cart      = get_term_meta( $taxonomy->term_id, '_ywctm_exclude_catalog_mode', true ) == 'yes' ? 'checked' : '';
				$price            = get_term_meta( $taxonomy->term_id, '_ywctm_exclude_hide_price', true ) == 'yes' ? 'checked' : '';
				$alternative_text = get_term_meta( $taxonomy->term_id, '_ywctm_alternative_text', true );


				?>
                <tr>
                    <th colspan="2">
                        <h3><?php _e( 'Catalog Mode Options', 'yith-woocommerce-catalog-mode' ) ?></h3>
                    </th>
                </tr>
                <tr class="form-field">
                    <th>
                        <label for="_ywctm_exclude_catalog_mode"><?php echo $atc_field; ?></label>
                    </th>
                    <td>
                        <input id="_ywctm_exclude_catalog_mode" name="_ywctm_exclude_catalog_mode" type="checkbox" <?php echo $add_to_cart; ?> />
                    </td>
                </tr>
                <tr class="form-field">
                    <th>
                        <label for="_ywctm_exclude_hide_price"><?php echo $price_field; ?></label>
                    </th>
                    <td>
                        <input id="_ywctm_exclude_hide_price" name="_ywctm_exclude_hide_price" type="checkbox" <?php echo $price; ?> />
                    </td>
                </tr>
                <tr class="form-field">
                    <th>
                        <label for="_ywctm_alternative_text"><?php echo __( 'Text replacing price', 'yith-woocommerce-catalog-mode' ); ?></label>
                    </th>
                    <td>
                        <input id="_ywctm_alternative_text" name="_ywctm_alternative_text" type="text" <?php echo $alternative_text; ?> />
                    </td>
                </tr>

				<?php if ( get_option( 'ywctm_custom_button' ) == 'yes' || get_option( 'ywctm_custom_button_loop' ) == 'yes' ) : ?>

					<?php

					$button    = get_term_meta( $taxonomy->term_id, '_ywctm_custom_url_enabled', true ) == 'yes' ? 'checked' : '';
					$no_button = get_term_meta( $taxonomy->term_id, '_ywctm_exclude_button', true ) == 'yes' ? 'checked' : '';
					$protocol  = get_term_meta( $taxonomy->term_id, '_ywctm_custom_url_protocol', true );
					$link      = get_term_meta( $taxonomy->term_id, '_ywctm_custom_url_link', true );
					$target    = get_term_meta( $taxonomy->term_id, '_ywctm_custom_url_link_target', true );

					?>

                    <tr class="form-field">
                        <th>
                            <label
                                for="_ywctm_exclude_button"><?php _e( 'Exclude products from custom button', 'yith-woocommerce-catalog-mode' ); ?></label>
                        </th>
                        <td>
                            <input id="_ywctm_exclude_button" name="_ywctm_exclude_button"
                                   type="checkbox" <?php echo $no_button; ?> />
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th>
                            <label
                                for="_ywctm_custom_url_enabled"><?php _e( 'Enable custom button URL override', 'yith-woocommerce-catalog-mode' ); ?></label>
                        </th>
                        <td>
                            <input id="_ywctm_custom_url_enabled" name="_ywctm_custom_url_enabled"
                                   type="checkbox" <?php echo $button; ?> />
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th>
                            <label
                                for="_ywctm_custom_url_protocol"><?php _e( 'URL protocol type', 'yith-woocommerce-catalog-mode' ); ?></label>
                        </th>
                        <td>
                            <select id="_ywctm_custom_url_protocol" name="_ywctm_custom_url_protocol">
                                <option
                                    value="generic" <?php selected( $protocol, 'generic' ); ?>><?php _e( 'Generic URL', 'yith-woocommerce-catalog-mode' ); ?></option>
                                <option
                                    value="mailto" <?php selected( $protocol, 'mailto' ); ?>><?php _e( 'E-mail address', 'yith-woocommerce-catalog-mode' ); ?></option>
                                <option
                                    value="tel" <?php selected( $protocol, 'tel' ); ?>><?php _e( 'Phone number', 'yith-woocommerce-catalog-mode' ); ?></option>
                                <option
                                    value="skype" <?php selected( $protocol, 'skype' ); ?>><?php _e( 'Skype contact', 'yith-woocommerce-catalog-mode' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th>
                            <label
                                for="_ywctm_custom_url_link"><?php _e( 'URL Link', 'yith-woocommerce-catalog-mode' ); ?></label>
                        </th>
                        <td>
                            <input id="_ywctm_custom_url_link" name="_ywctm_custom_url_link" type="text" value="<?php echo $link; ?>" />
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th>
                            <label
                                for="_ywctm_custom_url_link_target"><?php _e( 'Open link in new tab (Only for Generic URL)', 'yith-woocommerce-catalog-mode' ); ?></label>
                        </th>
                        <td>
                            <input id="_ywctm_custom_url_link_target" name="_ywctm_custom_url_link_target"
                                   type="checkbox" <?php echo $target; ?> />
                        </td>
                    </tr>

				<?php endif; ?>

				<?php

			}

		}

		/**
		 * Save YWCTM category/tag options
		 *
		 * @param   $taxonomy_id
		 *
		 * @return  void
		 * @since   1.3.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function save_taxonomy_options( $taxonomy_id ) {

			global $pagenow;

			if ( ! $taxonomy_id || $pagenow != 'edit-tags.php' ) {
				return;
			}

			if ( get_option( 'ywctm_hide_add_to_cart_single' ) == 'yes' || get_option( 'ywctm_hide_add_to_cart_loop' ) == 'yes' || get_option( 'ywctm_hide_price' ) == 'yes' ) {

				$add_to_cart      = isset( $_POST['_ywctm_exclude_catalog_mode'] ) ? 'yes' : 'no';
				$price            = isset( $_POST['_ywctm_exclude_hide_price'] ) ? 'yes' : 'no';
				$alternative_text = $_POST['_ywctm_alternative_text'];

				update_term_meta( $taxonomy_id, '_ywctm_exclude_catalog_mode', $add_to_cart );
				update_term_meta( $taxonomy_id, '_ywctm_exclude_hide_price', $price );
				update_term_meta( $taxonomy_id, '_ywctm_alternative_text', $alternative_text );

				if ( get_option( 'ywctm_custom_button' ) == 'yes' || get_option( 'ywctm_custom_button_loop' ) == 'yes' ) {

					$button    = isset( $_POST['_ywctm_custom_url_enabled'] ) ? 'yes' : 'no';
					$no_button = isset( $_POST['_ywctm_exclude_button'] ) ? 'yes' : 'no';
					$protocol  = $_POST['_ywctm_custom_url_protocol'];
					$link      = $_POST['_ywctm_custom_url_link'];
					$target    = isset( $_POST['_ywctm_custom_url_link_target'] ) ? 'yes' : 'no';

					update_term_meta( $taxonomy_id, '_ywctm_custom_url_enabled', $button );
					update_term_meta( $taxonomy_id, '_ywctm_exclude_button', $no_button );
					update_term_meta( $taxonomy_id, '_ywctm_custom_url_protocol', $protocol );
					update_term_meta( $taxonomy_id, '_ywctm_custom_url_link', $link );
					update_term_meta( $taxonomy_id, '_ywctm_custom_url_link_target', $target );

				}

			}

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Get user IP Address
		 *
		 * @return  void
		 * @since   1.3.4
		 * @author  Alberto Ruggiero
		 */
		public function geolocate_user() {

			if ( get_option( 'ywctm_hide_price_users' ) == 'country' ) {

				$ip_address = ywctm_get_ip_address();
				$request    = wp_remote_get( 'http://ip-api.com/json/' . $ip_address );
				$response   = json_decode( wp_remote_retrieve_body( $request ) );

				if ( ! $response || $response->status == 'fail' ) {
					$wc_geo_ip               = WC_Geolocation::geolocate_ip( $ip_address );
					$this->_user_geolocation = $wc_geo_ip['country'];
				} else {
					$this->_user_geolocation = $response->countryCode;
				}

			}

		}

		/**
		 * Check if country has catalog mode active
		 *
		 * @param   $post_id
		 *
		 * @return  boolean
		 * @since   1.3.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function country_check( $post_id ) {

			$countries = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_countries' ), $post_id, 'ywctm_hide_countries' );
			$reverse   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_countries_reverse' ), $post_id, 'ywctm_hide_countries_reverse' );
			$result    = false;
			$countries = ( ! is_array( $countries ) ) ? explode( ',', $countries ) : $countries;

			if ( in_array( $this->_user_geolocation, $countries ) ) {
				$result = true;
			}

			return ( ( $reverse == 'yes' ) ? ! $result : $result );

		}

		/**
		 * Get exclusion
		 *
		 * @param   $value
		 * @param   $post_id
		 * @param   $option
		 *
		 * @return  string
		 * @since   1.3.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function get_exclusion( $value, $post_id, $option ) {

			$product = wc_get_product( $post_id );

			if ( ! $product ) {
				return 'no';
			}

			if ( apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( $option ), $post_id, $option ) == 'yes' ) {
				return 'yes';
			}

			$product_cats = wp_get_object_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );
			foreach ( $product_cats as $cat_id ) {

				if ( apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, $option, true ), $post_id, $cat_id, $option ) == 'yes' ) {
					return 'yes';
				}

			}

			$product_tags = wp_get_object_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
			foreach ( $product_tags as $tag_id ) {

				if ( apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, $option, true ), $post_id, $tag_id, $option ) == 'yes' ) {
					return 'yes';
				}

			}

			return 'no';

		}

		/**
		 * Enqueue css file
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function enqueue_premium_styles() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_style( 'ywctm-premium-style', YWCTM_ASSETS_URL . 'css/yith-catalog-mode-premium' . $suffix . '.css' );

			global $post;

			if ( ! isset( $post ) ) {
				return;
			}

			if ( ! wc_get_product( $post->ID ) ) {
				return;
			}

			$form_type = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_type' ), $post->ID, 'ywctm_inquiry_form_type' );

			if ( $form_type != 'none' && ( $this->exists_inquiry_forms() ) ) {

				wp_enqueue_script( 'ywctm-frontend', YWCTM_ASSETS_URL . 'js/ywctm-frontend-premium' . $suffix . '.js', array( 'jquery' ) );
				wp_localize_script( 'ywctm-frontend', 'ywctm', array(
					'form_type'  => $form_type,
					'product_id' => $post->ID
				) );

			}

		}

		/**
		 * Removes reviews tab from single page product
		 *
		 * @param   $tabs
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function disable_reviews_tab( $tabs ) {

			global $post;

			$disable_review = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_disable_review' ), $post->ID, 'ywctm_disable_review' );

			if ( ( $disable_review == 'unregistered' && ! is_user_logged_in() ) || $disable_review == 'all' ) {
				unset( $tabs['reviews'] );
			}

			return $tabs;

		}

		/**
		 * Add inquiry form tab to single product page
		 *
		 * @param   $tabs
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function add_inquiry_form_tab( $tabs = array() ) {

			global $post;

			$priority = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_where_show', 'tab' ), $post->ID, 'ywctm_inquiry_form_where_show' );

			if ( $priority == 'tab' ) {

				$active_form = $this->get_active_inquiry_form( $post->ID );

				if ( ! empty( $active_form ) ) {

					$tab_title = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_tab_title' ), $post->ID, 'ywctm_inquiry_form_tab_title' );

					$tabs['inquiry_form'] = array(
						'title'            => apply_filters( 'ywctm_inquiry_form_title', $tab_title ),
						'priority'         => 40,
						'callback'         => array( $this, 'get_inquiry_form' ),
						'form_type'        => $active_form['form_type'],
						'contact_form_7'   => $active_form['contact_form_7'],
						'gravity_forms'    => $active_form['gravity_forms'],
						'yit_contact_form' => $active_form['yit_contact_form'],
					);

				}

			}

			return $tabs;

		}

		/**
		 * Get active inquiry form
		 *
		 * @param $post_id
		 *
		 * @return  array
		 * @since   1.5.1
		 *
		 * @author  Alberto ruggiero
		 */
		public function get_active_inquiry_form( $post_id ) {

			$active_form = array();
			$form_type   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_type' ), $post_id, 'ywctm_inquiry_form_type' );

			if ( $form_type != 'none' && ( $this->exists_inquiry_forms() ) ) {

				if ( class_exists( 'SitePress' ) ) {

					$contact_form_7   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_contact_form_7_id_wpml' ), $post_id, 'ywctm_inquiry_contact_form_7_id_wpml' );
					$gravity_forms    = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_gravity_forms_id_wpml' ), $post_id, 'ywctm_inquiry_gravity_forms_id_wpml' );
					$yit_contact_form = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_yit_contact_form_id_wpml' ), $post_id, 'ywctm_inquiry_yit_contact_form_id_wpml' );

					$page_language = wpml_get_language_information( null, $post_id );

					$contact_form_7   = ( isset( $contact_form_7[ $page_language['language_code'] ] ) ? $contact_form_7[ $page_language['language_code'] ] : '' );
					$gravity_forms    = ( isset( $gravity_forms[ $page_language['language_code'] ] ) ? $gravity_forms[ $page_language['language_code'] ] : '' );
					$yit_contact_form = ( isset( $yit_contact_form[ $page_language['language_code'] ] ) ? $yit_contact_form[ $page_language['language_code'] ] : '' );

				} else {

					$contact_form_7   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_contact_form_7_id' ), $post_id, 'ywctm_inquiry_contact_form_7_id' );
					$gravity_forms    = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_gravity_forms_id' ), $post_id, 'ywctm_inquiry_gravity_forms_id' );
					$yit_contact_form = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_yit_contact_form_id' ), $post_id, 'ywctm_inquiry_yit_contact_form_id' );

				}

				$show_yit_contact_form = ( $form_type == 'yit-contact-form' && $yit_contact_form != '' );
				$show_contact_form_7   = ( $form_type == 'contact-form-7' && $contact_form_7 != '' );
				$show_gravity_forms    = ( $form_type == 'gravity-forms' && $gravity_forms != '' );

				if ( $show_yit_contact_form || $show_contact_form_7 || $show_gravity_forms ) {

					$active_form = array(
						'form_type'        => $form_type,
						'contact_form_7'   => $contact_form_7,
						'gravity_forms'    => $gravity_forms,
						'yit_contact_form' => $yit_contact_form,
					);

				}


			}

			return $active_form;

		}

		/**
		 * Add inquiry form directly to single product page
		 *
		 * @return  void
		 * @since   1.5.1
		 * @author  Alberto ruggiero
		 */
		public function add_inquiry_form_page() {

			global $post;
			$priority = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_where_show', 'tab' ), $post->ID, 'ywctm_inquiry_form_where_show' );

			if ( $priority != 'tab' ) {

				$hook     = apply_filters( 'ywctm_inquiry_form_hook', 'woocommerce_single_product_summary' );
				$priority = apply_filters( 'ywctm_inquiry_form_priority', $priority );

				add_action( $hook, array( $this, 'print_inquiry_form_shortcode' ), $priority );

			}

		}

		/**
		 * Print Inquiry form on product page
		 *
		 * @return  void
		 * @since   1.5.1
		 * @author  Alberto ruggiero
		 */
		public function print_inquiry_form_shortcode() {

			global $post;

			$active_form = $this->get_active_inquiry_form( $post->ID );

			if ( ! empty( $active_form ) ) {

				$tab_title = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_tab_title' ), $post->ID, 'ywctm_inquiry_form_tab_title' );

				?>
                <div class="ywctm-inquiry-form-wrapper">

					<span class="ywctm-form-title">
						<?php echo apply_filters( 'ywctm_inquiry_form_title', $tab_title ); ?>
					</span>

					<?php

					$this->get_inquiry_form( '', $active_form );

					?>
                </div>
				<?php
			}

		}

		/**
		 * Inquiry form tab template
		 *
		 * @param   $key
		 * @param   $tab
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto ruggiero
		 */
		public function get_inquiry_form( $key, $tab ) {

			global $product;

			$shortcode = '';

			switch ( $tab['form_type'] ) {
				case 'yit-contact-form':
					$shortcode = '[contact_form name="' . $tab['yit_contact_form'] . '"]';
					break;
				case 'contact-form-7':
					$shortcode = '[contact-form-7 id="' . $tab['contact_form_7'] . '"]';
					break;
				case 'gravity-forms':
					$shortcode = '[gravityform  id=' . $tab['gravity_forms'] . apply_filters( 'ywctm_gravity_ajax', ' ajax=true' ) . ']';
					break;
			}

			do_action( 'ywctm_before_inquiry_form', $product );
			echo do_shortcode( $shortcode );
			do_action( 'ywctm_after_inquiry_form', $product );


		}

		/**
		 * Add a custom button in product details page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function show_custom_button() {

			global $post;

			if ( apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_custom_button' ), $post->ID, 'ywctm_custom_button' ) == 'yes' && $this->check_add_to_cart_single() ) {

				$this->get_custom_button_template();

			}

		}

		/**
		 * Add a custom button in loop
		 *
		 * @return  void
		 * @since   1.0.4
		 * @author  Alberto Ruggiero
		 */
		public function show_custom_button_loop() {

			global $post;

			if ( ! isset( $post ) ) {
				return;
			}

			if ( apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_custom_button_loop' ), $post->ID, 'ywctm_custom_button_loop' ) == 'yes' && $this->check_hide_add_cart_loop() ) {

				$this->get_custom_button_template();

			}

		}

		/**
		 * Get custom button template
		 *
		 * @return  void
		 * @since   1.0.4
		 * @author  Alberto Ruggiero
		 */
		public function get_custom_button_template() {

			global $post;

			if ( ! isset( $post ) ) {
				return;
			}

			$button_text = '';
			$protocol    = '';
			$link        = '';
			$target      = '';
			$post_id     = $post->ID;
			$product     = wc_get_product( $post->ID );

			if ( ! $product ) {
				return;
			}

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
				$post_id = yit_wpml_object_id( $post_id, 'product', true, wpml_get_default_language() );
			}

			if ( apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_exclude_button' ), $post_id, '_ywctm_exclude_button' ) == 'yes' ) {
				return;
			}

			if ( apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_custom_url_enabled' ), $post_id, '_ywctm_custom_url_enabled' ) == 'yes' ) {

				$button_text = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_button_text' ), $post_id, '_ywctm_button_text' );
				$protocol    = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_custom_url_protocol' ), $post_id, '_ywctm_custom_url_protocol' );
				$link        = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_custom_url_link' ), $post_id, '_ywctm_custom_url_link' );
				$target      = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_custom_url_link_target' ), $post_id, '_ywctm_custom_url_link_target' );

			} else {

				$product_cats = wp_get_object_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );
				foreach ( $product_cats as $cat_id ) {

					if ( apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_exclude_button', true ), $post_id, $cat_id, '_ywctm_exclude_button' ) == 'yes' ) {
						return;
					}

					if ( apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_custom_url_enabled', true ), $post_id, $cat_id, '_ywctm_custom_url_enabled' ) == 'yes' ) {
						if ( $button_text == '' ) {
							$button_text = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_button_text', true ), $post_id, $cat_id, '_ywctm_button_text' );
						}

						$protocol    = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_custom_url_protocol', true ), $post_id, $cat_id, '_ywctm_custom_url_protocol' );
						$link        = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_custom_url_link', true ), $post_id, $cat_id, '_ywctm_custom_url_link' );
						$target      = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_custom_url_link_target', true ), $post_id, $cat_id, '_ywctm_custom_url_link_target' );

					}

				}

				if ( $protocol == '' ) {

					$product_tags = wp_get_object_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
					foreach ( $product_tags as $tag_id ) {

						if ( apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_exclude_button', true ), $post_id, $tag_id, '_ywctm_exclude_button' ) == 'yes' ) {
							return;
						}

						if ( apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_custom_url_enabled', true ), $post_id, $tag_id, '_ywctm_custom_url_enabled' ) == 'yes' ) {
							if ( $button_text == '' ) {
								$button_text = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_button_text', true ), $post_id, $tag_id, '_ywctm_button_text' );
							}

							$protocol    = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_custom_url_protocol', true ), $post_id, $tag_id, '_ywctm_custom_url_protocol' );
							$link        = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_custom_url_link', true ), $post_id, $tag_id, '_ywctm_custom_url_link' );
							$target      = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_custom_url_link_target', true ), $post_id, $tag_id, '_ywctm_custom_url_link_target' );

						}

					}

				}

				if ( $protocol == '' ) {

					$protocol = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_url_type' ), $post_id, 'ywctm_button_url_type' );
					$link     = apply_filters( 'ywctm_get_vendor_option', get_option( ( class_exists( 'SitePress' ) ? 'ywctm_button_url_wpml' : 'ywctm_button_url' ) ), $post_id, ( class_exists( 'SitePress' ) ? 'ywctm_button_url_wpml' : 'ywctm_button_url' ) );
					$target   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_url_target' ), $post_id, 'ywctm_button_url_target' );

				}

			}
			if ( $button_text == '' ) {
				$button_text = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_text' ), $post_id, 'ywctm_button_text' );
			}

			$button_url_type = $protocol == 'generic' ? '' : $protocol . ':';

			if ( class_exists( 'SitePress' ) && is_array( $link ) ) {

				$page_language = wpml_get_language_information( null, $post_id );
				$link          = $link[ $page_language['language_code'] ];

			}

			$button_url = $link == '' ? '#' : $link;
			$newtab     = ( ( $protocol == 'generic' && $target == 'yes' ) ? ' target="_blank" ' : '' );

			$icon = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_icon' ), $post_id, 'ywctm_button_icon' );

			?>
            <div class="ywctm-custom-button-container">
                <p>
                    <a class="button ywctm-custom-button <?php echo apply_filters( 'ywctm_custom_button_additional_classes', '' ); ?>" href="<?php printf( '%s%s', $button_url_type, $button_url ); ?>" <?php echo $newtab; ?>>
						<?php
						switch ( $icon['select'] ) :
							case 'icon':
								?>
                                <span
                                    class="ywctm-icon-form" <?php echo YITH_Icon()->get_icon_data( $icon['icon'] ) ?>></span>
								<?php break;
							case 'custom':
								?>
                                <span class="custom-icon"><img src="<?php echo esc_url( $icon['custom'] ); ?>"></span>
								<?php break;
						endswitch; ?>
                        <span class="ywctm-inquiry-title"><?php echo $button_text; ?></span>
                    </a>
                </p>
            </div>
			<?php

		}

		/**
		 * Set custom css for custom button
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function custom_button_css() {

			global $post;

			if ( empty( $post ) ) {
				return;
			}

			$post_id = $post->ID;

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
				$post_id = yit_wpml_object_id( $post_id, 'product', true, wpml_get_default_language() );
			}

			$button_color          = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_color' ), $post_id, 'ywctm_button_color' );
			$button_hover_color    = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_hover' ), $post_id, 'ywctm_button_hover' );
			$button_bg_color       = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_bg_color' ), $post_id, 'ywctm_button_bg_color' );
			$button_bg_hover_color = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_bg_hover' ), $post_id, 'ywctm_button_bg_hover' );

			if ( $button_color != '' || $button_hover_color != '' ) : ?>

                <style type="text/css">
                    <?php if ( $button_color != '' ) : ?>
                    a.ywctm-custom-button {
                        color: <?php echo $button_color; ?> !important;
                        background-color: <?php echo $button_bg_color; ?> !important;
                    }

                    <?php endif;
					if ( $button_hover_color != '') :?>
                    a.ywctm-custom-button:hover {
                        color: <?php echo $button_hover_color; ?> !important;
                        background-color: <?php echo $button_bg_hover_color; ?> !important;
                    }

                    <?php endif; ?>
                </style>

			<?php endif;

		}

		/**
		 * Hides product price from single product page
		 *
		 * @param   $classes
		 *
		 * @return  string
		 * @since   1.4.4
		 *
		 * @author  Alberto Ruggiero
		 */
		public function hide_price_single_page( $classes ) {

			if ( $this->check_product_price_single( true ) ) {

				$args = array(
					'.woocommerce-variation-price'
				);

				$classes = array_merge( $classes, apply_filters( 'ywctm_catalog_price_classes', $args ) );

			}

			return $classes;

		}

		/**
		 * Checks if product price needs to be hidden
		 *
		 * @param   $priority
		 * @param   $product_id
		 *
		 * @return  bool
		 * @since   1.0.2
		 *
		 * @author  Alberto Ruggiero
		 */
		public function check_product_price_single( $priority = true, $product_id = false ) {

			global $post;

			if ( empty( $post ) && ! $product_id ) {
				return false;
			}

			$post_id = ( $product_id ) ? $product_id : $post->ID;

			return apply_filters( 'ywctm_check_price_hidden', false, $post_id );

		}

		/**
		 * Hides on-sale badge if price is hidden
		 *
		 * @param   $is_on_sale
		 * @param   $product WC_Product
		 *
		 * @return  bool
		 * @since   1.5.5
		 *
		 * @author  Alberto Ruggiero
		 */
		public function hide_on_sale( $is_on_sale, $product ) {

			if ( apply_filters( 'ywctm_check_price_hidden', false, $product->get_id() ) ) {

				$is_on_sale = false;

			}

			return $is_on_sale;

		}

		/**
		 * Check if price is hidden
		 *
		 * @param   $hide
		 * @param   $post_id
		 *
		 * @return  bool
		 * @since   1.4.4
		 *
		 * @author  Alberto Ruggiero
		 */
		public function check_price_hidden( $hide, $post_id ) {

			$hide = false;

			$hide_price = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_price' ), $post_id, 'ywctm_hide_price' );

			if ( get_option( 'ywctm_enable_plugin' ) == 'yes' && $this->check_user_admin_enable() && $hide_price == 'yes' ) {

				if ( $this->apply_catalog_mode( $post_id ) ) {

					$product = wc_get_product( $post_id );

					if ( ! $product ) {
						return $hide;
					}

					global $sitepress;
					$has_wpml = ! empty( $sitepress ) ? true : false;

					if ( $has_wpml && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
						$post_id = yit_wpml_object_id( $post_id, 'product', true, wpml_get_default_language() );
						$product = wc_get_product( $post_id );
					}


					$enable_exclusion = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_exclude_hide_price' ), $post_id, 'ywctm_exclude_hide_price' );
					$exclude_catalog  = apply_filters( 'ywctm_get_exclusion', $product->get_meta( '_ywctm_exclude_hide_price' ), $post_id, '_ywctm_exclude_hide_price' );

					$hide = ( $enable_exclusion != 'yes' ? true : ( $exclude_catalog != 'yes' ? true : false ) );

					$reverse_criteria = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_exclude_hide_price_reverse' ), $post_id, 'ywctm_exclude_hide_price_reverse' );

					if ( $enable_exclusion == 'yes' && $reverse_criteria == 'yes' ) {

						$hide = ! $hide;

					}

				}

			}

			return $hide;

		}

		/**
		 * Check for which users will not see the price
		 *
		 * @param   $price
		 * @param   $product
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function show_product_price( $price, $product ) {

			if ( defined( 'WOOCOMMERCE_CHECKOUT' ) || defined( 'WOOCOMMERCE_CART' ) || apply_filters( 'ywctm_ajax_admin_check', is_admin(), $product ) || ( apply_filters( 'ywctm_prices_only_on_cart', false ) && ( current_filter() == 'woocommerce_get_price' || current_filter() == 'woocommerce_product_get_price' ) ) ) {
				return $price;
			}

			if ( $product instanceof WC_Product ) {
				$product_id = yit_get_base_product_id( $product );
			} else {
				$product_id = $product;
				$product    = wc_get_product( $product_id );

			}

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
				$product    = wc_get_product( $product_id );
			}

			$hide_price = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_price' ), $product_id, 'ywctm_hide_price' );

			if ( $hide_price == 'yes' && $this->apply_catalog_mode( $product_id ) ) {

				$price = $this->set_price_label( $price, $product );

				if ( $price == $this->get_alternative_text( $product, $product_id ) ) {

					if ( ( current_filter() == 'woocommerce_get_price' || current_filter() == 'woocommerce_product_get_price' ) ) {

						if ( ( class_exists( 'YITH_Request_Quote_Premium' ) && get_option( 'ywraq_show_button_near_add_to_cart' ) == 'yes' ) || is_account_page() ) {
							$value = 0;
						} else {
							$value = '';
						}

						$price = apply_filters( 'ywctm_hidden_price_meta', $value );

					} elseif ( current_filter() == 'yith_ywraq_hide_price_template' ) {
						$price = '';
					}

				}

			}

			$price = apply_filters( 'ywctm_hide_price_anyway', $price, $product_id );

			return $price;

		}

		/**
		 * Gets alternative text if set
		 *
		 * @param   $product    WC_Product
		 * @param   $product_id integer
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function get_alternative_text( $product, $product_id ) {
			$alternative_text = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_alternative_text' ), $product_id, '_ywctm_alternative_text' );

			if ( $alternative_text == '' ) {
				$product_cats = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );
				foreach ( $product_cats as $cat_id ) {
					$alternative_text = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_alternative_text', true ), $product_id, $cat_id, '_ywctm_alternative_text' );
				}
			}

			if ( $alternative_text == '' ) {
				$product_tags = wp_get_object_terms( $product_id, 'product_tag', array( 'fields' => 'ids' ) );
				foreach ( $product_tags as $tag_id ) {
					$alternative_text = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_alternative_text', true ), $product_id, $tag_id, '_ywctm_alternative_text' );
				}
			}

			if ( $alternative_text == '' ) {
				$alternative_text = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_exclude_price_alternative_text' ), $product_id, 'ywctm_exclude_price_alternative_text' );
			}

			return $alternative_text;
		}

		/**
		 * Hides price, if not excluded, and shows alternative text if set
		 *
		 * @param   $price
		 * @param   $product WC_Product
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function set_price_label( $price, $product ) {

			$product_id       = yit_get_base_product_id( $product );
			$alternative_text = $this->get_alternative_text( $product, $product_id );
			$enable_exclusion = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_exclude_hide_price' ), $product_id, 'ywctm_exclude_hide_price' );
			$exclude_catalog  = apply_filters( 'ywctm_get_exclusion', $product->get_meta( '_ywctm_exclude_hide_price' ), $product_id, '_ywctm_exclude_hide_price' );

			$remove = ( $enable_exclusion != 'yes' ? true : ( $exclude_catalog != 'yes' ? true : false ) );

			$reverse_criteria = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_exclude_hide_price_reverse' ), $product_id, 'ywctm_exclude_hide_price_reverse' );

			if ( $enable_exclusion == 'yes' && $reverse_criteria == 'yes' ) {

				$remove = ! $remove;

			}

			return ( $remove ? ( $alternative_text != '' ? $alternative_text : '' ) : $price );

		}

		/**
		 * Hides product price and add to cart in YITH Quick View
		 *
		 * @return  void
		 * @since   1.0.7
		 * @author  Francesco Licandro
		 */
		public function check_quick_view() {
			/*add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'hide_add_to_cart_quick_view' ) );
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'hide_price_quick_view' ) );*/
			if ( $this->is_quick_view() ) {
				$this->hide_add_to_cart_quick_view();
				$this->hide_price_quick_view();
			}
		}

		/**
		 * Flatsome Integration
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function flatsome_integration() {
			$wp_theme   = wp_get_theme();
			$theme_name = is_child_theme() ? $wp_theme->get_template() : strtolower( $wp_theme->Name );

			if ( 'flatsome' == $theme_name ) {
				add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'hide_add_to_cart_quick_view' ) );
				add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'hide_price_quick_view' ) );
				add_filter( 'ywctm_ajax_admin_check', '__return_false' );
			}

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
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWCTM_INIT' ) {
			$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
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
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, true );

			return $links;
		}

		/**
		 * DEPRECATED FUNCTIONS
		 */

		/**
		 * Hides product price from single product page
		 *
		 * @param   $action
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function hide_product_price_single( $action = 'woocommerce_single_product_summary' ) {

			/*if ( $action == '' ) {
				$action = 'woocommerce_single_product_summary';
			}

			$priority = has_action( $action, 'woocommerce_template_single_price' );

			if ( $this->check_product_price_single( $priority ) ) {

				remove_action( $action, 'woocommerce_template_single_price', $priority );
				add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'hide_price_quick_view' ) );

			}*/
			return;

		}

		/**
		 * Hide price for product in quick view
		 *
		 * @return  mixed
		 * @since   1.0.7
		 * @author  Francesco Licandro
		 */
		public function hide_price_quick_view() {

			if ( $this->check_product_price_single( true ) ) {

				ob_start();

				$args = array(
					'.single_variation_wrap .single_variation',
					'.yith-quick-view .price',
					'.price-wrapper',
				);

				$classes = implode( ', ', apply_filters( 'ywctm_catalog_price_classes', $args ) );

				?>

                <style>

                    <?php echo $classes; ?>
                    {
                        display: none !important
                    }

                </style>

				<?php

				echo ob_get_clean();

			}

		}

	}

}