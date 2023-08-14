<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Waitlist_Optin_Email' ) ) {
	/**
	 * Waitlist Optin Email
	 *
	 * An email sent to unregistered customers to optin to the waitlist
	 *
	 * @class    Pie_WCWL_Waitlist_Optin_Email
	 * @extends  WC_Email
	 */
	class Pie_WCWL_Waitlist_Optin_Email extends WC_Email {

		/**
		 * Language code to send the email out in
		 *
		 * @var string
		 */
		public $language = '';

		/**
		 * Hooks up the functions for Waitlist Mailout
		 *
		 * @access public
		 */
		public function __construct() {
			$this->customer_email = true;
			$this->wcwl_setup_mailout();
			add_action( 'wcwl_send_double_optin_email', array( $this, 'trigger' ), 10, 4 );
			parent::__construct();
		}

		/**
		 * Setup required variables for mailout class
		 *
		 * @access public
		 * @return void
		 */
		public function wcwl_setup_mailout() {
			$this->id                 = WCWL_SLUG . '_optin_email';
			$this->title              = __( 'Waitlist Double Opt-in Email', 'woocommerce-waitlist' );
			$this->description        = __( 'With double opt-in enabled unregistered customers will receive this email when signing up to a waitlist to confirm their email address.', 'woocommerce-waitlist' );
			$this->template_base      = WooCommerce_Waitlist_Plugin::$path . 'templates/';
			$this->template_html      = 'emails/waitlist-optin.php';
			$this->template_plain     = 'emails/plain/waitlist-optin.php';
			$this->subject            = $this->get_option( 'subject', $this->get_default_subject() );
			$this->heading            = $this->get_option( 'heading', $this->get_default_heading() );
			global $sitepress;
			if ( isset( $sitepress ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
				$this->language = ICL_LANGUAGE_CODE;
			}
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'Please confirm your email address', 'woocommerce-waitlist' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Please confirm your email address', 'woocommerce-waitlist' );
		}

		/**
		 * Trigger function for the mailout class
		 *
		 * @param int   $email      email of user to send the mail to
		 * @param int   $product_id ID of product that email refers to
		 * @param array $products   array of product IDs
		 * @param bool  $lang       customer site language
		 *
		 * @return bool success
		 *
		 * @access public
		 */
		public function trigger( $email, $product_id, $products, $lang ) {
			global $woocommerce_wpml;
			$product = wc_get_product( $product_id );
			if ( $woocommerce_wpml ) {
				$this->language = $lang;
				$this->setup_wpml_email( $this->language );
			}
			$this->products = $products;
			$this->setup_required_data( $product, $email );
			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return new WP_Error( 'woocommerce_waitlist', sprintf( __( 'Failed to send optin email on %s.' ), gmdate( 'd M, y' ) ) );
			}

			return $this->send_email();
		}

		/**
		 * Setup all required translations
		 *
		 * @param $wcml_emails
		 * @param $language
		 */
		protected function setup_wpml_email( $language ) {
			global $woocommerce_wpml, $sitepress, $woocommerce, $wpdb;
			if ( $language && $woocommerce_wpml && $sitepress ) {
				$strings     = new WCML_WC_Strings( $woocommerce_wpml, $sitepress, $wpdb );
				$wcml_emails = new WCML_Emails( $strings, $sitepress, $woocommerce, $wpdb );
				$wcml_emails->change_email_language( $language );
				$this->subject = apply_filters( 'wcwl_optin_email_subject_translated', 'Please confirm your email address', $language );
				$this->heading = apply_filters( 'wcwl_optin_email_heading_translated', 'Please confirm your email address', $language );
			}
		}

		/**
		 * Load generic data into the email class
		 *
		 * @param $product
		 * @param $email
		 */
		protected function setup_required_data( $product, $email ) {
			$this->object                          = $product;
			$this->placeholders['{product_title}'] = $product->get_title();
			$this->placeholders['{blogname}']      = $this->get_blogname();
			$this->recipient                       = $email;
		}

		/**
		 * Send the email and store the record in the archive if required
		 *
		 * @return bool success
		 */
		protected function send_email() {
			$subject = apply_filters( 'woocommerce_email_subject_' . $this->id, $this->get_translated_string( $this->subject, $this->language ) );
			$result  = $this->send( $this->get_recipient(), $subject, $this->get_content(), $this->get_headers(), $this->get_attachments() );

			return $result;
		}

		/**
		 * Translate the given string to the given language if a translation exists
		 *
		 * @param $string
		 * @param $language_code
		 *
		 * @return false|string
		 */
		protected function get_translated_string( $string, $language_code ) {
			if ( $language_code && function_exists( 'icl_get_string_id' ) ) {
				$string_id   = icl_get_string_id( $string, 'woocommerce-waitlist' );
				$translation = icl_get_string_by_id( $string_id, $language_code );
				if ( $translation ) {
					$string = $translation;
				}
			}

			return $this->format_string( $string );
		}

		/**
		 * Returns the html string needed to create an email to send out to user
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_html() {
			ob_start();
			$product_id = $this->object->get_id();
			$key        = hash_hmac( 'sha256', $this->recipient . '|' . $product_id, get_the_guid( $product_id ) . $this->recipient . 'woocommerce-waitlist-optin' );
			set_transient( $key, $key, apply_filters( 'wcwl_double_optin_valid_time', 86400 ) );
			wc_get_template(
				$this->template_html,
				array(
					'email_class'   => $this,
					'product_title' => get_the_title( $product_id ),
					'product_link'  => get_permalink( $product_id ),
					'key'           => $key,
					'email_heading' => apply_filters( 'woocommerce_email_heading_' . $this->id, $this->get_translated_string( $this->heading, $this->language ) ),
					'product_id'    => $product_id,
					'products'      => $this->products,
					'lang'          => $this->language,
					'email'         => $this->recipient,
				),
				false,
				$this->template_base
			);

			return ob_get_clean();
		}

		/**
		 * Returns the plain text needed to create an email to send out to user
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_plain() {
			ob_start();
			$product_id = $this->object->get_id();
			$key        = hash_hmac( 'sha256', $this->recipient . '|' . $product_id, get_the_guid( $product_id ) . $this->recipient . 'woocommerce-waitlist-optin' );
			set_transient( $key, $key, apply_filters( 'wcwl_double_optin_valid_time', 86400 ) );
			wc_get_template(
				$this->template_plain,
				array(
					'email_class'   => $this,
					'product_title' => get_the_title( $product_id ),
					'product_link'  => get_permalink( $product_id ),
					'key'           => $key,
					'email_heading' => apply_filters( 'woocommerce_email_heading_' . $this->id, $this->get_translated_string( $this->heading, $this->language ) ),
					'product_id'    => $product_id,
					'products'      => $this->products,
					'lang'          => $this->language,
					'email'         => $this->recipient,
				),
				false,
				$this->template_base
			);

			return ob_get_clean();
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'                => array(
					'title'   => __( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'woocommerce' ),
					'default' => 'yes',
				),
				'subject'                => array(
					'title'       => __( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' ),
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'                => array(
					'title'       => __( 'Email heading', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' ),
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'email_type'             => array(
					'title'       => __( 'Email type', 'woocommerce' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
	}
}
