<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Request A Quote Premium
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

use Dompdf\Dompdf;

/**
 * Implements the YITH_Request_Quote_Premium class.
 *
 * @class   YITH_Request_Quote_Premium
 * @package YITH
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_Request_Quote_Premium' ) ) {

	/**
	 * Class YITH_Request_Quote_Premium
	 */
	class YITH_Request_Quote_Premium extends YITH_Request_Quote {

		/**
		 * Locale
		 *
		 * @var bool
		 */
		private $locale = false;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_Request_Quote_Premium
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			parent::__construct();

			$this->_run();


			// register widget.
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );
			add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_emails' ) );

			if ( 'yes' === get_option( 'ywraq_enable_pdf', 'yes' ) && ( defined( 'DOING_CRON' ) || $this->is_admin() || ( isset( $_REQUEST['ywraq_checkout_quote'] ) && 'true' === $_REQUEST['ywraq_checkout_quote'] ) ) ) {//phpcs:ignore WordPress.Security.NonceVerification.Recommended
				add_action( 'create_pdf', array( $this, 'generate_pdf' ), 99 );
				add_action( 'yith_ywraq_quote_template_header', array( $this, 'pdf_header' ), 10, 1 );
				add_action( 'yith_ywraq_quote_template_footer', array( $this, 'pdf_footer' ), 10, 1 );
				add_action( 'yith_ywraq_quote_template_content', array( $this, 'pdf_content' ), 10, 1 );
				add_filter( 'plugin_locale', array( $this, 'set_locale_for_pdf' ), 10, 2 );
			}

			if ( $this->is_admin() ) {
				add_action( 'init', array( $this, 'set_plugin_requirements' ) );

				// register metabox to the product editor.
				add_action( 'admin_init', array( $this, 'add_metabox' ), 1 );
				add_action( 'save_post', array( $this, 'save_metabox_info' ), 1, 2 );
				add_filter( 'get_post_metadata', array( $this, 'get_exclusion_value' ), 10, 4 );


			} else {

				// show button in shop page.
				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_button_shop' ), 15 );
				add_filter( 'yith_ywraq_hide_price_template', array( $this, 'show_product_price' ), 0, 2 );

				if ( ! catalog_mode_plugin_enabled() ) {
					add_filter( 'woocommerce_get_price_html', array( $this, 'show_product_price' ), 0 );
					add_filter( 'woocommerce_get_variation_price_html', array( $this, 'show_product_price' ), 0 );
				}

				// check user type.
				add_filter( 'yith_ywraq_before_print_button', array( $this, 'must_be_showed' ), 10, 2 );
				add_filter( 'yith_ywraq_before_print_widget', array( $this, 'raq_page_check_user' ) );
				add_filter( 'yith_ywraq_before_print_my_account_my_quotes', array( $this, 'raq_page_check_user' ) );
				add_filter( 'yith_ywraq_before_print_raq_page', array( $this, 'raq_page_check_user' ) );
				add_filter( 'yith_ywraq_raq_page_deniend_access', array( $this, 'raq_page_denied_access' ) );
			}


			if ( 'yes' === get_option( 'ywraq_automate_send_quote' ) && '0' === get_option( 'ywraq_cron_time' ) ) {
				add_action( 'ywraq_after_create_order_from_checkout', array( $this, 'send_the_quote_automatically' ), 10, 2 );
				add_action( 'ywraq_after_create_order', array( $this, 'send_the_quote_automatically' ), 10, 2 );
			}
		}

		/**
		 * Include files and classes for the premium version.
		 *
		 * @since  2.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		private function _run() {

			// widget.
			require_once YITH_YWRAQ_INC . 'widgets/class.yith-ywraq-list-quote-widget.php';
			require_once YITH_YWRAQ_INC . 'widgets/class.yith-ywraq-mini-list-quote-widget.php';

			// Load class and functions of default form.
			require_once YITH_YWRAQ_INC . 'forms/default/class.yith-ywraq-default-form.php';
			require_once YITH_YWRAQ_INC . 'forms/default/functions.yith-ywraq-default-form.php';

			// privacy.
			require_once YITH_YWRAQ_INC . 'class.yith-request-quote-privacy.php';
			require_once YITH_YWRAQ_INC . 'class.yith-ywraq-order-request.php';


			if ( $this->is_admin() ) {
				require_once YITH_YWRAQ_INC . 'class.yith-ywraq-exclusions-handler.php';
				require_once YITH_YWRAQ_INC . 'admin/class.yith-ywraq-exclusions-prod-table.php';
				require_once YITH_YWRAQ_INC . 'admin/class.yith-ywraq-exclusions-cat-table.php';
				require_once YITH_YWRAQ_INC . 'admin/class.yith-ywraq-exclusions-tag-table.php';
				YITH_YWRAQ_Exclusions_Handler();
			}

			$this->_plugin_integrations();
			$this->_form_integrations();

		}

		/**
		 * Include the files and the classes if necessary.
		 *
		 * @since  2.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		private function _plugin_integrations() {

			if ( class_exists( 'YITH_Vendors' ) ) {
				require_once YITH_YWRAQ_INC . 'compatibility/yith-woocommerce-product-vendors.php';
			}

			if ( class_exists( 'YITH_WAPO' ) ) {
				require_once YITH_YWRAQ_INC . 'compatibility/yith-woocommerce-advanced-product-options.php';
			}

			if ( class_exists( 'YITH_WCP' ) ) {
				require_once YITH_YWRAQ_INC . 'compatibility/yith-woocommerce-composite-products.php';
			}

			if ( class_exists( 'YITH_WCDP' ) ) {
				require_once YITH_YWRAQ_INC . 'compatibility/yith-woocommerce-deposits-and-down-payments.php';
			}

		}

		/**
		 * Include the files and the classes if necessary.
		 *
		 * @since  2.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		private function _form_integrations() {

			$form_type = get_option( 'ywraq_inquiry_form_type', 'default' );

			if ( ywraq_cf7_form_installed() && ( is_admin() || 'contact-form-7' === $form_type ) ) {
				require_once YITH_YWRAQ_INC . 'forms/contact-form-7/class.yith-ywraq-contact-form-7.php';
				require_once YITH_YWRAQ_INC . 'forms/contact-form-7/functions.yith-ywraq-contact-form-7.php';
				YITH_YWRAQ_Contact_Form_7();
			}

			if ( ywraq_yit_contact_form_installed() && ( is_admin() || 'yit-contact-form' === $form_type ) ) {
				require_once YITH_YWRAQ_INC . 'forms/yit-contact-form/class.yith-ywraq-yit-contact-form.php';
				YITH_YWRAQ_YIT_Contact_Form();
			}

			if ( ywraq_gravity_form_installed() && ( is_admin() || 'gravity-forms' === $form_type ) ) {
				require_once YITH_YWRAQ_INC . 'forms/gravity-forms/ywraq-gravity-form-addons.php';
				YWRAQ_Gravity_Forms_Add_On();
			}

			if ( is_admin() || 'default' === $form_type ) {
				YITH_YWRAQ_Default_Form();
			}

			do_action( 'ywraq_form_integration' );
		}

		/**
		 * Add the quote button in other pages is the product is simple
		 *
		 * @return  boolean|void
		 * @author  Emanuela Castorina
		 * @since   1.0.0
		 */
		public function add_button_shop() {

			$show_button = apply_filters( 'yith_ywraq-btn_other_pages', true );

			global $product;

			if ( ! $product ) {
				return false;
			}

			$type_in_loop = apply_filters(
				'yith_ywraq_show_button_in_loop_product_type',
				array(
					'simple',
					'subscription',
					'external',
					'yith-composite',
				)
			);

			if ( ! yith_plugin_fw_is_true( $show_button ) || ! $product->is_type( $type_in_loop ) ) {
				return false;
			}

			if ( ! function_exists( 'YITH_YWRAQ_Frontend' ) ) {
				require_once YITH_YWRAQ_INC . 'class.yith-request-quote-frontend.php';
				YITH_YWRAQ_Frontend();
			}

			YITH_YWRAQ_Frontend()->print_button( $product );
		}

		/**
		 * Check for which users will not see the price
		 *
		 * @param      $price
		 * @param bool $product_id
		 *
		 * @return string
		 * @author  Emanuela Castorina
		 *
		 * @since   1.0.0
		 */
		public function show_product_price( $price, $product_id = false ) {

			$hide_price = get_option( 'ywraq_hide_price' ) === 'yes';

			if ( catalog_mode_plugin_enabled() ) {
				global $YITH_WC_Catalog_Mode;
				$hide_price = $YITH_WC_Catalog_Mode->check_product_price_single( true, $product_id );

				if ( $hide_price && '' !== get_option( 'ywctm_exclude_price_alternative_text' ) ) {
					$hide_price = false;
					$price      = get_option( 'ywctm_exclude_price_alternative_text' );
				}
			} elseif ( $hide_price && current_filter() === 'woocommerce_get_price_html' ) {
				$price = '';
			} elseif ( $hide_price && ! catalog_mode_plugin_enabled() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) && current_filter() !== 'woocommerce_get_price_html' ) {
				ob_start();

				$args = array(
					'.single_variation_wrap .single_variation',
				);

				$classes = implode( ', ', apply_filters( 'ywcraq_catalog_price_classes', $args ) );

				?>
				<style>
					<?php
						echo esc_attr( $classes );
					?>
					{
						display: none !important
					}

				</style>
				<?php
				echo ob_get_clean();
			}

			return ( $hide_price ) ? '' : $price;

		}

		/**
		 * Add metabox in the product editor
		 *
		 * @return  void
		 * @author  Emanuela Castorina
		 * @since   1.0.0
		 */
		public function add_metabox() {
			global $pagenow;
			$request = $_REQUEST; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$post = isset( $request['post'] ) ? $request['post'] : ( isset( $request['post_ID'] ) ? $request['post_ID'] : 0 );
			$post = get_post( $post );

			if ( ( $post && 'product' === $post->post_type ) || ( 'post-new.php' === $pagenow && isset( $_REQUEST['post_type'] ) && 'product' === $_REQUEST['post_type'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$args = require_once YITH_YWRAQ_DIR . 'plugin-options/metabox/ywraq-metabox.php';
				if ( ! function_exists( 'YIT_Metabox' ) ) {
					require_once YITH_YWRAQ_DIR . 'plugin-fw/yit-plugin.php';
				}
				$metabox = YIT_Metabox( 'yith-ywraq-metabox' );
				$metabox->init( $args );
			}

		}

		/**
		 * Check if the product is in the exclusion list
		 *
		 * @param string $value .
		 * @param int    $post_id .
		 * @param string $meta_key .
		 * @param bool   $single .
		 *
		 * @return mixed
		 * @author Alberto Ruggiero
		 */
		public function get_exclusion_value( $value, $post_id, $meta_key, $single ) {

			if ( '_ywraq_hide_quote_button' === $meta_key && 'product' === get_post_type( $post_id ) ) {

				$exclusion_prod = explode( ',', get_option( 'yith-ywraq-exclusions-prod-list', '' ) );
				$exclusion_prod = array_map( 'absint', $exclusion_prod );

				$value = in_array( $post_id, $exclusion_prod, true );
			}

			return $value;
		}

		/**
		 * Add or Remove the products in the exclusion list
		 *
		 * @param int     $post_id .
		 * @param WP_Post $post .
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function save_metabox_info( $post_id, $post ) {

			if ( ( $post && 'product' === $post->post_type ) ) {
				$exclusion_prod = explode( ',', get_option( 'yith-ywraq-exclusions-prod-list', '' ) );
				$exclusion_prod = array_map( 'absint', $exclusion_prod );
				if ( ! isset( $_REQUEST['yit_metaboxes']['_ywraq_hide_quote_button'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
					if ( in_array( $post_id, $exclusion_prod, true ) ) {
						$exclusion_prod = array_diff( $exclusion_prod, array( $post_id ) );
						update_option( 'yith-ywraq-exclusions-prod-list', implode( ',', $exclusion_prod ) );
					}
				} else {
					if ( ! in_array( $post_id, $exclusion_prod, true ) ) {
						$exclusion_prod = array_merge( $exclusion_prod, array( $post_id ) );
						update_option( 'yith-ywraq-exclusions-prod-list', implode( ',', $exclusion_prod ) );
					}
				}
			}
		}

		/**
		 * Register the widgets
		 *
		 * @return  void
		 * @author  Emanuela Castorina
		 * @since   1.0.0
		 */
		public function register_widgets() {
			register_widget( 'YITH_YWRAQ_List_Quote_Widget' );
			register_widget( 'YITH_YWRAQ_Mini_List_Quote_Widget' );
		}

		/**
		 * Refresh the quote list in the widget when a product is added or removed from the list
		 *
		 * @return  void
		 * @author  Emanuela Castorina
		 * @since   1.0.0
		 */
		public function ajax_refresh_quote_list() {
			$raq_content  = YITH_Request_Quote()->get_raq_return();
			$posted       = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$args         = array(
				'raq_content'       => $raq_content,
				'template_part'     => 'view',
				'title'             => isset( $posted['title'] ) ? $posted['title'] : '',
				'item_plural_name'  => isset( $posted['item_plural_name'] ) ? $posted['item_plural_name'] : '',
				'item_name'         => isset( $posted['item_name'] ) ? $posted['item_name'] : '',
				'button_label'      => isset( $posted['button_label'] ) ? $posted['button_label'] : '',
				'show_title_inside' => isset( $posted['show_title_inside'] ) ? $posted['show_title_inside'] : 1,
				'show_thumbnail'    => isset( $posted['show_thumbnail'] ) ? $posted['show_thumbnail'] : 1,
				'show_price'        => isset( $posted['show_price'] ) ? $posted['show_price'] : 1,
				'show_quantity'     => isset( $posted['show_quantity'] ) ? $posted['show_quantity'] : 1,
				'show_variations'   => isset( $posted['show_variations'] ) ? $posted['show_variations'] : 1,
				'widget_type'       => isset( $posted['widget_type'] ) ? $posted['widget_type'] : '',
			);
			$args['args'] = $args;

			wp_send_json(
				array(
					'large' => wc_get_template_html( 'widgets/quote-list.php', $args, '', YITH_YWRAQ_TEMPLATE_PATH . '/' ),
					'mini'  => wc_get_template_html( 'widgets/mini-quote-list.php', $args, '', YITH_YWRAQ_TEMPLATE_PATH . '/' ),
				)
			);

			die();
		}

		/**
		 * Refresh the number of items for the shortcode [yith_ywraq_number_items]
		 *
		 * @return  void
		 * @author  Emanuela Castorina
		 * @since   1.7.8
		 */
		public function ajax_refresh_number_items() {

			$posted = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$atts   = array(
				'show_url'         => $posted['show_url'],
				'item_name'        => $posted['item_name'],
				'item_plural_name' => $posted['item_plural_name'],
			);

			echo YITH_YWRAQ_Frontend()->shortcodes->ywraq_number_items( $atts );
		}

		/**
		 * Update the request a quote button to prevent caches
		 */
		public function ajax_update_ywraq_fragments() {

			$response = array(
				'error' => __( 'Error: Invalid request. Try again!', 'yith-woocommerce-request-a-quote' ),
			);

			$updated_fragments = array();
			$fragments         = wp_unslash( $_POST['fragments'] ); //phpcs:ignore
			if ( $fragments ) {
				foreach ( $fragments as $fragment ) {
					$updated_fragments[ $fragment ] = do_shortcode( '[yith_ywraq_button_quote product=' . $fragment . ']' );
				}

				$response = array(
					'success'   => true,
					'fragments' => $updated_fragments,
				);
			}

			wp_send_json( apply_filters( 'yith_ywraq_ajax_update_fragments_json', $response ) );
		}

		/**
		 * Loads the inquiry form
		 *
		 * @param array $args .
		 *
		 * @since 1.0
		 */
		public function get_inquiry_form( $args ) {

			$shortcode = '';

			switch ( get_option( 'ywraq_inquiry_form_type', 'default' ) ) {
				case 'yit-contact-form':
					$shortcode = '[contact_form name="' . get_option( 'ywraq_inquiry_yit_contact_form_id' ) . '"]';
					break;
				case 'contact-form-7':
					global $sitepress;
					if ( function_exists( 'icl_get_languages' ) && !is_null($sitepress) ) {
						$current_language = $sitepress->get_current_language();
						$cform7_id        = get_option( 'ywraq_inquiry_contact_form_7_id_' . $current_language );

					} else {
						$cform7_id = get_option( 'ywraq_inquiry_contact_form_7_id' );
					}

					$cform7_id = apply_filters( 'ywraq_inquiry_contact_form_7_id', $cform7_id );

					$shortcode = '[contact-form-7 id="' . $cform7_id . '"]';
					break;
				case 'gravity-forms':
					if ( ywraq_gravity_form_installed() ) {
						$gravity_form_id = YWRAQ_Gravity_Forms_Add_On()->get_selected_form_id();
						$gf_title_desc   = apply_filters( 'ywraq_gf_title_desc', 'title="true" description="true" ' );
						$shortcode       = '[gravityform id="' . $gravity_form_id . '" ' . $gf_title_desc . 'ajax="true"]';
					}
					break;
				case 'default':
					YITH_YWRAQ_Default_Form()->get_form_template( $args );
					break;
			}

			echo is_callable( 'apply_shortcodes' ) ? apply_shortcodes( $shortcode ) : do_shortcode( $shortcode );

		}

		/**
		 * Filters woocommerce available mails, to add wishlist related ones
		 *
		 * @param array $emails.
		 *
		 * @return array
		 * @since 1.0
		 */
		public function add_woocommerce_emails( $emails ) {
			$emails['YITH_YWRAQ_Send_Email_Request_Quote']          = include YITH_YWRAQ_INC . 'emails/class.yith-ywraq-send-email-request-quote.php';
			$emails['YITH_YWRAQ_Send_Email_Request_Quote_Customer'] = include YITH_YWRAQ_INC . 'emails/class.yith-ywraq-send-email-request-quote-customer.php';
			$emails['YITH_YWRAQ_Quote_Status']                      = include YITH_YWRAQ_INC . 'emails/class.yith-ywraq-quote-status.php';
			$emails['YITH_YWRAQ_Send_Quote']                        = include YITH_YWRAQ_INC . 'emails/class.yith-ywraq-send-quote.php';
			$emails['YITH_YWRAQ_Send_Quote_Reminder']               = include YITH_YWRAQ_INC . 'emails/class.yith-ywraq-send-quote-reminder.php';

			return $emails;
		}

		/**
		 * Loads WC Mailer when needed
		 *
		 * @return void
		 * @since 1.0
		 */
		public function load_wc_mailer() {
			add_action( 'send_raq_mail', array( 'WC_Emails', 'send_transactional_email' ), 10 );
			add_action( 'send_raq_customer_mail', array( 'WC_Emails', 'send_transactional_email' ), 10 );
			add_action( 'send_quote_mail', array( 'WC_Emails', 'send_transactional_email' ), 10 );
			add_action( 'change_status_mail', array( 'WC_Emails', 'send_transactional_email' ), 10 );
			add_action( 'send_reminder_quote_mail', array( 'WC_Emails', 'send_transactional_email' ), 10 );
		}

		/**
		 * Build wishlist page URL.
		 *
		 * @param string $action .
		 *
		 * @return string
		 * @since 1.0.0
		 */
		public function get_raq_url( $action = 'view' ) {
			$base_url    = '';
			$raq_page_id = get_option( 'ywraq_page_id' );

			if ( get_option( 'permalink_structure' ) ) {
				$raq_page          = get_post( $raq_page_id );
				$raq_page_slug     = $raq_page->post_name;
				$raq_page_relative = '/' . $raq_page_slug . '/' . $action . '/';

				$base_url = trailingslashit( home_url( $raq_page_relative ) );
			}

			return $base_url;

		}

		/**
		 * Check if the raq button can be showed
		 *
		 * @param boolean    $value Current filter value.
		 * @param WC_Product $product The WC Product object.
		 *
		 * @return bool
		 * @since 1.0.0
		 */
		public function must_be_showed( $value = true, $product = null ) {

			if ( is_null( $product ) ) {
				// if is null get global.
				global $product;
			}
			// if is null get post.
			if ( ! $product ) {
				global $post;
				if ( ! $post || ! is_object( $post ) || ! is_singular() ) {
					return false;
				}
				$product = wc_get_product( $post->ID );
			}


			if ( ! is_object( $product ) || ! $this->check_user_type() || ( ! ywraq_allow_raq_out_of_stock() && $product && ! $product->is_in_stock() ) || ( ywraq_show_btn_only_out_of_stock() && $product && $product->is_type( 'simple' ) && $product->is_in_stock() ) ) {
				return false;
			}

			if ( ywraq_is_in_exclusion( $product->get_id() ) ) {
				return false;
			}

			return $value;
		}

		/**
		 * Check user
		 *
		 * @return bool
		 * @since 1.0.0
		 */
		public function check_user() {

			global $product;

			if ( ! $product ) {
				global $post;
				if ( ! $post || ! is_object( $post ) || ! is_singular() ) {
					return false;
				}
				$product = wc_get_product( $post->ID );
			}

			if ( ! is_object( $product ) || ! $this->check_user_type() || ( ! ywraq_allow_raq_out_of_stock() && $product && ! $product->is_in_stock() ) || ( ywraq_show_btn_only_out_of_stock() && $product && $product->is_in_stock() ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Check if the raq button can be showed
		 *
		 * @return bool
		 * @since 1.0.0
		 */
		public function raq_page_check_user() {

			if ( ! $this->check_user_type() ) {
				return false;
			}

			return true;
		}

		/**
		 * Check if the current user is available to send requests
		 *
		 * @return bool
		 * @since 1.0.0
		 */
		public function check_user_type() {
			$user_type = get_option( 'ywraq_user_type' );
			$return    = false;

			if ( is_user_logged_in() && ( 'customers' === $user_type || 'all' === $user_type ) ) {
				$rules = (array) get_option( 'ywraq_user_role' );

				if ( empty( $rules ) || ! is_array( $rules ) ) {
					return false;
				}

				if ( in_array( 'all', $rules, true ) ) {
					return true;
				}

				$current_user = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : false;
				$intersect    = array();
				if ( $current_user instanceof WP_User ) {
					$intersect = array_intersect( $current_user->roles, $rules );
				}

				if ( ! empty( $intersect ) ) {
					return true;
				}
			} else {
				if ( ( ! is_user_logged_in() && 'guests' === $user_type ) || 'all' === $user_type ) {
					return true;
				}
			}

			return $return;
		}

		/**
		 * Raq page denied access
		 *
		 * @param string $message .
		 * @return string
		 */
		public function raq_page_denied_access( $message ) {
			$user_type = get_option( 'ywraq_user_type' );

			if ( 'customers' === $user_type ) {
				return __( 'You must be logged in to access this page', 'yith-woocommerce-request-a-quote' );
			}

			return $message;
		}

		/**
		 * Generate the template
		 *
		 * @param int $order_id .
		 *
		 * @return int
		 */
		public function generate_pdf( $order_id ) {

			ob_start();

			wc_get_template( 'pdf/quote.php', array( 'order_id' => $order_id ), '', YITH_YWRAQ_TEMPLATE_PATH . '/' );

			$html = ob_get_contents();
			ob_end_clean();

			require_once YITH_YWRAQ_DOMPDF_DIR . 'autoload.inc.php';

			$dompdf = new DOMPDF();

			$dompdf->setPaper( 'A4', apply_filters( 'ywraq_change_paper_orientation', 'portrait' ) );

			$dompdf->set_option( 'enable_html5_parser', apply_filters( 'ywraq_enable_html5_parser', true ) );

			$dompdf->loadHtml( $html );

			$dompdf->render();

			// The next call will store the entire PDF as a string in $pdf.
			$pdf = $dompdf->output();

			$file_path = $this->get_pdf_file_path( $order_id, true );

			if ( ! file_exists( $file_path ) ) {
				$file_path = $this->get_pdf_file_path( $order_id, false );
			} else {
				unlink( $file_path );
			}

			return file_put_contents( $file_path, $pdf );

		}

		/**
		 * Get Pdf File Url
		 *
		 * @param int $order_id .
		 * @return string
		 */
		public function get_pdf_file_url( $order_id ) {
			$path = $this->create_storing_folder( $order_id );
			$url  = YITH_YWRAQ_SAVE_QUOTE_URL . $path . $this->get_pdf_file_name( $order_id );

			return apply_filters( 'ywraq_pdf_file_url', $url );
		}

		/**
		 * Return the file of pdf
		 *
		 * @param int $order_id .
		 * @return string
		 */
		public function get_pdf_file_name( $order_id ) {

			$pdf_file_name = 'quote_' . $order_id . '.pdf';

			$order            = wc_get_order( $order_id );
			$_ywraq_pdf_crypt = apply_filters( 'ywraq_pdf_crypt_file_name', yit_get_prop( $order, '_ywraq_pdf_crypt', true ), $order_id );

			if ( ywraq_is_true( $_ywraq_pdf_crypt ) ) {
				$ywraq_customer_email = yit_get_prop( $order, 'ywraq_customer_email', true );
				$pdf_file_name        = 'quote_' . md5( $order_id . $ywraq_customer_email ) . '.pdf';
			}

			return apply_filters( 'ywraq_pdf_file_name', $pdf_file_name, $order_id );
		}

		/**
		 * Get Pdf File Path
		 *
		 * @param int  $order_id .
		 * @param bool $delete_file .
		 *
		 * @return string
		 */
		public function get_pdf_file_path( $order_id, $delete_file = false ) {
			$path = apply_filters( 'ywraq_pdf_file_path', $this->create_storing_folder( $order_id ), $order_id );
			$file = YITH_YWRAQ_DOCUMENT_SAVE_DIR . $path . $this->get_pdf_file_name( $order_id );
			// delete the document if exists.
			if ( file_exists( $file ) && $delete_file ) {
				@unlink( $file );
			}

			return $file;
		}

		/**
		 * Send the quote automatically after that the customer does the request.
		 *
		 * @param $raq
		 * @param WC_Order $order .
		 */
		public function send_the_quote_automatically( $raq, $order ) {

			if ( current_action() === 'ywraq_after_create_order' ) {
				$order = wc_get_order( $raq );
			}

			if ( $order instanceof WC_Order ) {
				do_action( 'create_pdf', $order->get_id() );
				do_action( 'send_quote_mail', $order->get_id() );
				$order->update_status( 'ywraq-pending' );
			}
		}

		/**
		 * Create Storing Folder
		 *
		 * @param int $order_id .
		 * @return mixed|string
		 */
		public static function create_storing_folder( $order_id ) {

			$order = wc_get_order( $order_id );
			/* Create folders for storing documents */
			$folder_pattern = '[year]/[month]/';

			$order_date = is_callable( array( $order, 'get_date_created' ) ) ? $order->get_date_created() : $order->order_date;

			$date = getdate( strtotime( $order_date ) );

			$folder_pattern = str_replace(
				array(
					'[year]',
					'[month]',
				),
				array(
					$date['year'],
					sprintf( '%02d', $date['mon'] ),
				),
				$folder_pattern
			);

			if ( ! file_exists( YITH_YWRAQ_DOCUMENT_SAVE_DIR . $folder_pattern ) ) {
				wp_mkdir_p( YITH_YWRAQ_DOCUMENT_SAVE_DIR . $folder_pattern );
			}

			return $folder_pattern;
		}

		/**
		 * PDF Content
		 *
		 * @param int $order_id .
		 */
		public function pdf_content( $order_id ) {
			$order    = wc_get_order( $order_id );
			$template = get_option( 'ywraq_pdf_template', 'table' );

			if ( 'table' === $template ) {
				wc_get_template( 'pdf/quote-table.php', array( 'order' => $order ), '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
			} else {
				wc_get_template( 'pdf/quote-table-div.php', array( 'order' => $order ), '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
			}

		}

		/**
		 * PDF Header
		 *
		 * @param int $order_id .
		 */
		public function pdf_header( $order_id ) {
			$order = wc_get_order( $order_id );
			wc_get_template( 'pdf/quote-header.php', array( 'order' => $order ), '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
		}

		/**
		 * PDF Footer
		 *
		 * @param int $order_id .
		 */
		public function pdf_footer( $order_id ) {
			$footer_content  = get_option( 'ywraq_pdf_footer_content' );
			$show_pagination = get_option( 'ywraq_pdf_pagination' );
			wc_get_template(
				'pdf/quote-footer.php',
				array(
					'footer'     => $footer_content,
					'pagination' => $show_pagination,
					'order_id'   => $order_id,
				),
				'',
				YITH_YWRAQ_TEMPLATE_PATH . '/'
			);
		}

		/**
		 * Change PDF Language
		 *
		 * @param string $lang .
		 */
		public function change_pdf_language( $lang ) {
			global $sitepress, $woocommerce;
			if ( is_object( $sitepress ) ) {
				$sitepress->switch_lang( $lang, true );
				$this->locale = $sitepress->get_locale( $lang );
				unload_textdomain( 'yith-woocommerce-request-a-quote' );
				unload_textdomain( 'woocommerce' );
				unload_textdomain( 'default' );

				load_plugin_textdomain( 'yith-woocommerce-request-a-quote', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
				$woocommerce->load_plugin_textdomain();
				load_default_textdomain();
			}
		}

		/**
		 * Set correct locale code for emails
		 *
		 * @param string $locale .
		 * @param string $domain .
		 *
		 * @return bool
		 */
		public function set_locale_for_pdf( $locale, $domain ) {

			if ( 'woocommerce' === $domain && $this->locale ) {
				$locale = $this->locale;
			}

			return $locale;
		}

		/**
		 * Set plugin requirement on System Info Panel
		 */
		public function set_plugin_requirements() {

			$plugin_name  = 'YITH WooCommerce Request a Quote';
			$requirements = array(
				'wp_cron_enabled'  => true,
				'mbstring_enabled' => true,
				'gd_enabled'       => true,
				'iconv_enabled'    => true,
				'imagick_version'  => '6.4.0',
			);
			yith_plugin_fw_add_requirements( $plugin_name, $requirements );
		}

	}

}

/**
 * Unique access to instance of YITH_Request_Quote_Premium class
 *
 * @return \YITH_Request_Quote_Premium
 */
function YITH_Request_Quote_Premium() {
	return YITH_Request_Quote_Premium::get_instance();
}

