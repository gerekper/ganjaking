<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Waitlist_Left_Email' ) ) {
	/**
	 * Waitlist Left Email
	 *
	 * An email sent to the customer when they leave the waitlist for a product
	 *
	 * @class    Pie_WCWL_Waitlist_Left_Email
	 * @extends  WC_Email
	 */
	class Pie_WCWL_Waitlist_Left_Email extends WC_Email {

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
			add_action( 'wcwl_left_mailout_send_email', array( $this, 'trigger' ), 10, 3 );
			parent::__construct();
		}

		/**
		 * Setup required variables for mailout class
		 *
		 * @access public
		 * @return void
		 */
		public function wcwl_setup_mailout() {
			$this->id                 = WCWL_SLUG . '_left_email';
			$this->title              = __( 'Waitlist Left Notification', 'woocommerce-waitlist' );
			$this->description        = __( 'When a customer leaves a waitlist this email is sent to them with a link back to the product.', 'woocommerce-waitlist' );
			$this->template_base      = WooCommerce_Waitlist_Plugin::$path . 'templates/';
			$this->template_html      = 'emails/waitlist-left.php';
			$this->template_plain     = 'emails/plain/waitlist-left.php';
			$this->subject            = $this->get_option( 'subject', $this->get_default_subject() );
			$this->heading            = $this->get_option( 'heading', $this->get_default_heading() );
			$this->triggered_manually = false;
			global $sitepress;
			if ( isset( $sitepress ) ) {
				$this->language = wpml_get_default_language();
			}
		}

		/**
		 * Get email subject.
		 *
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'You have been removed from a waitlist.', 'woocommerce-waitlist' );
		}

		/**
		 * Get email heading.
		 *
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'You have been removed from the waitlist for {product_title} at {blogname}', 'woocommerce-waitlist' );
		}

		/**
		 * Trigger function for the mailout class
		 *
		 * @param int  $email      email of user to send the mail to
		 * @param int  $product_id ID of product that email refers to
		 * @param bool $manual     was the email triggered manually?
		 *
		 * @return bool success
		 *
		 * @access public
		 */
		public function trigger( $email, $product_id, $manual = false ) {
			global $woocommerce_wpml;
			$this->triggered_manually = $manual;
			$product                  = wc_get_product( $product_id );
			if ( $woocommerce_wpml ) {
				$this->language = wcwl_get_user_language( $email, $product_id );
				$product        = wc_get_product( wpml_object_id_filter( $product_id, 'product', true, $this->language ) );
				$this->setup_wpml_email( $this->language );
			}
			$this->setup_required_data( $product, $email );
			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return new WP_Error( 'woocommerce_waitlist', sprintf( __( 'Failed to send waitlist notification on %s. Is the email address valid and waitlist mailouts enabled?' ), gmdate( 'd M, y' ) ) );
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
				$this->subject = apply_filters( 'wcwl_left_mailout_subject_translated', 'You have been removed from a waitlist.', $language );
				$this->heading = apply_filters( 'wcwl_left_mailout_heading_translated', 'You have been removed from the waitlist for {product_title} at {blogname}', $language );
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
			$subject = apply_filters( 'woocommerce_email_subject_' . $this->id, $this->get_translated_string( $this->subject, $this->language ), $this->object, $this );
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
			if ( $language_code ) {
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
			wc_get_template(
				$this->template_html,
				array(
					'product_title' => get_the_title( $product_id ),
					'product_link'  => get_permalink( $product_id ),
					'email_heading' => apply_filters( 'woocommerce_email_heading_' . $this->id, $this->get_translated_string( $this->heading, $this->language ) ),
					'product_id'    => $product_id,
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
			wc_get_template(
				$this->template_plain,
				array(
					'product_title' => get_the_title( $product_id ),
					'product_link'  => get_permalink( $product_id ),
					'email_heading' => apply_filters( 'woocommerce_email_heading_' . $this->id, $this->get_translated_string( $this->heading, $this->language ) ),
					'product_id'    => $product_id,
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
					'default' => 'no',
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
