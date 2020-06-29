<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements the YITH_YWRAQ_Contact_Form_7 class.
 *
 * @class   YITH_YWRAQ_Contact_Form_7
 * @package YITH
 * @since   2.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_YWRAQ_Contact_Form_7' ) ) {

	/**
	 * Class YITH_YWRAQ_Contact_Form_7
	 */
	class YITH_YWRAQ_Contact_Form_7 {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_YWRAQ_Contact_Form_7
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_YWRAQ_Contact_Form_7
		 * @since 2.0.0
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
		 * Initialize form and registers actions and filters to be used
		 *
		 * @since  2.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			add_action( 'before_woocommerce_init', array( $this, 'avoid_rest_api_check' ) );


			if ( is_admin() ) {
				add_filter( 'wpcf7_get_contact_forms', 'yith_ywraq_wpcf7_get_contact_forms' );
				add_filter( 'wpcf7_collect_mail_tags', array( $this, 'add_tags_to_contact_form7' ) );
				add_filter( 'ywraq_form_type_list', array( $this, 'add_to_option_list' ) );
				add_filter( 'ywraq_additional_form_options', array( $this, 'add_option' ), 10, 3 );
			}

			add_filter( 'wpcf7_special_mail_tags', 'yith_ywraq_email_custom_tags', 10, 3 );


			if ( get_option( 'ywraq_inquiry_form_type' ) == 'contact-form-7' ) {
				add_filter( 'yith_ywraq_frontend_localize', array( $this, 'frontend_localize' ) );
				add_action( 'wpcf7_mail_sent', array( $this, 'redirect_after_submission_mail_contact_form' ) );

				if ( get_option( 'ywraq_enable_order_creation', 'yes' ) == 'yes' ) {
					add_action( 'wpcf7_before_send_mail', array( $this, 'create_order_before_mail_cf7' ) );
					add_filter( 'ywraq_ajax_create_order_args', array( $this, 'create_order_args' ), 10, 2 );
					add_filter( 'ywraq_order_meta_list', array( $this, 'add_order_metas' ), 10, 3 );
					add_action( 'shutdown', array( $this, 'fix_contact_form_7' ), - 1 );
				}
			}
		}

		/**
		 * Skip the rest api check for avoid sending issues with WC 3.6.0
		 *
		 * @return void
		 * @since 2.1.8
		 */
		public function avoid_rest_api_check() {

			$form_id = ywraq_get_current_contact_form_7();

			if ( $form_id != '' && ( false !== strpos( $_SERVER['REQUEST_URI'], $form_id ) ) ) {

				add_filter( 'woocommerce_is_rest_api_request', '__return_false' );
				//include cart functions
				include_once WC_ABSPATH . 'includes/wc-cart-functions.php';

			}

		}

		/**
		 * Add the tags [yith-request-a-quote-list] to the contact form 7 legend
		 *
		 * @params $tags
		 * @param $tags
		 *
		 * @return array
		 * @since 1.4.9
		 */
		public function add_tags_to_contact_form7( $tags ) {
			$tags[] = 'yith-request-a-quote-list';

			return $tags;
		}

		/**
		 * Add current CF7 Form to javascript frontend localization.
		 *
		 * @param array $localize
		 *
		 * @since  2.0
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function frontend_localize( $localize ) {
			$localize['cform7_id'] = apply_filters( 'ywraq_inquiry_contact_form_7_id', ywraq_get_current_contact_form_7() );

			return $localize;
		}

		/**
		 * @param $list
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_to_option_list( $list ) {
			$list['contact-form-7'] = __( 'Contact Form 7', 'yith-woocommerce-request-a-quote' );

			return $list;
		}

		/**
		 * @param $options
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_option( $options ) {
			if ( function_exists( 'wpml_get_active_languages_filter' )  ) {
				$langs = wpml_get_active_languages_filter( '', 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );

				if ( is_array( $langs ) ) {
					foreach ( $langs as $key => $lang ) {
						$contact_form_7[ 'contact_form_7_' . $key ] = array(
							'name'      => sprintf( '%s:', $lang['native_name'] ),
							'type'      => 'yith-field',
							'yith-type' => 'select',
							'desc'      => __( 'Choose the form to display', 'yith-woocommerce-request-a-quote' ),
							'options'   => apply_filters( 'wpcf7_get_contact_forms', array() ),
							'id'        => 'ywraq_inquiry_contact_form_7_id_' . $key,
							'class'     => 'wc-enhanced-select',
							'deps'      => array(
								'id'    => 'ywraq_inquiry_form_type',
								'value' => 'contact-form-7',
								'type'  => 'hide'
							),
							'class'     => 'contact-form-7'
						);
					}
				}

			} else {
				$contact_form_7 = array(
					'name'      => '',
					'type'      => 'yith-field',
					'yith-type' => 'select',
					'desc'      => sprintf( '%s. <a href="%s" class="ywraq_cf7_link">%s<a>', __( 'Choose the form to display', 'yith-woocommerce-request-a-quote' ), esc_url( add_query_arg( array( 'page' => 'wpcf7' ), admin_url() ) ), __( 'Edit form', 'yith-woocommerce-request-a-quote' ) ),
					'options'   => apply_filters( 'wpcf7_get_contact_forms', array() ),
					'id'        => 'ywraq_inquiry_contact_form_7_id',
					'deps'      => array(
						'id'    => 'ywraq_inquiry_form_type',
						'value' => 'contact-form-7',
						'type'  => 'hide'
					),
					'class'     => 'contact-form-7'
				);

			}

			if ( ! empty( $contact_form_7 ) ) {
				foreach ( $contact_form_7 as $k => $cf ) {
					if ( ! is_array( $cf ) ) {
						$options['contact_form_7'] = $contact_form_7;
						break;
					}
					$options[ $k ] = $cf;
				}
			}

			return $options;
		}

		/**
		 *
		 *
		 * @since  1.1.8
		 * @author Emanuela Castorina
		 */
		public function fix_contact_form_7() {
			if ( isset( $_POST['_wpcf7_is_ajax_call'] ) ) {
				die();
			}
		}

		/**
		 * Create the order after that contact form email is sent.
		 *
		 * @param $cf WPCF7_ContactForm
		 *
		 * @throws Exception
		 */
		public function create_order_before_mail_cf7( $cf ) {

			$form_id = ywraq_get_current_contact_form_7();

			if ( isset( $_REQUEST['_wpcf7'] ) && $_REQUEST['_wpcf7'] == $form_id ) {
				YITH_YWRAQ_Order_Request()->ajax_create_order( false );
			}
		}

		/**
		 * Do the redirect after that email is sent with contact form 7 without ajax enabled
		 *
		 * @param $contact_form WPCF7_ContactForm
		 *
		 * @return bool
		 */
		public function redirect_after_submission_mail_contact_form( $contact_form ) {
			if ( ! function_exists( 'wpcf7_load_js' ) ) {
				return false;
			}

			$wpcf7_load_js = wpcf7_load_js();

			if ( $wpcf7_load_js && get_option( 'ywraq_enable_order_creation' ) == 'yes' ) {
				return false;
			}

			$contact_form_id = ywraq_get_current_contact_form_7();
			$cf7_id          = $contact_form->id();


			if ( $contact_form_id == $cf7_id ) {
				if ( apply_filters( 'ywraq_clear_list_after_send_quote', true ) ) {
					YITH_Request_Quote()->clear_raq_list();
				}
			}
		}


		/**
		 * Add argument with fields sent from CF7 form before create the order.
		 *
		 * @param $args
		 * @param $posted
		 *
		 * @return array
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function create_order_args( $args, $posted ) {
			$other_email_content = '';
			$other_fields        = array();
			$exclude_params      = ywraq_cf7_get_fields_excluded();
			$supported_fields    = ywraq_cf7_supported_woocommerce_fields();

			if ( ! empty( $posted ) ) {
				foreach ( $posted as $key => $value ) {
					if ( ! in_array( $key, $exclude_params ) ) {
						$value                = is_array( $value ) ? implode( ', ', $value ) : $value;
						$other_email_content  .= sprintf( '<strong>%s</strong>: %s<br>', $key, $value );
						$key                  = apply_filters( 'ywraq_other_email_content_key', $key, $value );
						$other_fields[ $key ] = $value;
					}
				}
			}

			if ( ! isset( $posted['your-name'] ) ) {
				$first_name = isset( $posted['billing-first-name'] ) ? sanitize_text_field( $posted['billing-first-name'] ) : '';
				$last_name  = isset( $posted['billing-last-name'] ) ? sanitize_text_field( $posted['billing-last-name'] ) : '';
				$name       = $first_name . ' ' . $last_name;
			} else {
				$name = sanitize_text_field( $posted['your-name'] );
			}

			$args_cf7 = array(
				'user_name'           => $name,
				'user_email'          => sanitize_email( $posted['your-email'] ),
				'user_message'        => sanitize_textarea_field( $posted['your-message'] ),
				'other_email_fields'  => $other_fields,
				'other_email_content' => $other_email_content,
			);

			if ( $supported_fields ) {
				foreach ( $supported_fields as $supported_field ) {
					if ( isset( $posted[ $supported_field ] ) ) {
						$args_cf7[ $supported_field ] = $posted[ $supported_field ];
					}
				}
			}


			if ( isset( $posted['billing-vat'] ) ) {
				$args_cf7['billing-vat'] = sanitize_text_field( $posted['billing-vat'] );
			}
			if ( isset( $posted['billing-address'] ) ) {
				$args_cf7['billing-address-1'] = sanitize_text_field( $posted['billing-address'] );
			}

			if ( isset( $posted['lang'] ) ) {
				$args_cf7['lang'] = sanitize_text_field( $posted['lang'] );
			}

			if ( ! isset( $args['lang'] ) && isset( $posted['_wpcf7_locale'] ) ) {
				$lang             = explode( '_', $posted['_wpcf7_locale'] );
				$args_cf7['lang'] = $lang[0];
			}

			return array_merge( $args, $args_cf7 );

		}

		/**
		 * Add order meta from request.
		 *
		 * @param $attr
		 * @param $order_id
		 * @param $raq
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_order_metas( $attr, $order_id, $raq ) {

			//default fields
			$attr['ywraq_customer_name']    = $raq['user_name'];
			$attr['ywraq_customer_message'] = $raq['user_message'];
			$attr['ywraq_customer_email']   = $raq['user_email'];
			$attr['_raq_request']           = $raq;

			if ( isset( $raq['billing-phone'] ) ) {
				$attr['ywraq_billing_phone'] = $raq['billing-phone'];
			}

			if ( isset( $raq['other_email_content'] ) ) {
				$attr['ywraq_other_email_content'] = $raq['other_email_content'];
			}

			if ( isset( $raq['other_email_fields'] ) ) {
				$attr['ywraq_other_email_fields'] = $raq['other_email_fields'];
			}

			if ( isset( $raq['billing-vat'] ) ) {
				$attr['ywraq_billing_vat'] = $raq['billing-vat'];
			}


			$ov_field = apply_filters( 'ywraq_override_order_billing_fields', true );
			if ( $ov_field ) {
				$supported_fields       = ywraq_cf7_supported_woocommerce_fields();
				$attr['_billing_email'] = $raq['user_email'];
				foreach ( $supported_fields as $field ) {
					if ( isset( $raq[ $field ] ) && ! empty( $raq[ $field ] ) ) {
						$name          = '_' . str_replace( '-', '_', $field );
						$attr[ $name ] = $raq[ $field ];
					}
				}
			}

			return $attr;
		}

	}

	/**
	 * Unique access to instance of YITH_YWRAQ_Contact_Form_7 class
	 *
	 * @return \YITH_YWRAQ_Contact_Form_7
	 */
	function YITH_YWRAQ_Contact_Form_7() {
		return YITH_YWRAQ_Contact_Form_7::get_instance();
	}
}