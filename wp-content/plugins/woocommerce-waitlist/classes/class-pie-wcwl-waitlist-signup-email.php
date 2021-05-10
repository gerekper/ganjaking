<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Waitlist_Signup_Email' ) ) {
	/**
	 * Waitlist Signup Email
	 *
	 * An email sent to the admin when a user signs up to a waitlist
	 *
	 * @class    Pie_WCWL_Waitlist_Signup_Email
	 * @extends  WC_Email
	 */
	class Pie_WCWL_Waitlist_Signup_Email extends WC_Email {

		/**
		 * Hooks up the functions
		 *
		 * @access public
		 */
		public function __construct() {
			$this->customer_email = false;
			$this->setup_mailout();
			add_action( 'wcwl_new_signup_send_admin_email', array( $this, 'trigger' ), 10, 2 );
			parent::__construct();
		}

		/**
		 * Setup required variables
		 *
		 * @access public
		 * @return void
		 */
		public function setup_mailout() {
			$this->id             = WCWL_SLUG . '_signup_email';
			$this->title          = __( 'Waitlist Admin Sign-up Email', 'woocommerce-waitlist' );
			$this->description    = __( 'When a user signs up to a waitlist this email is sent to the defined admin email address', 'woocommerce-waitlist' );
			$this->template_base  = WooCommerce_Waitlist_Plugin::$path . 'templates/';
			$this->template_html  = 'emails/waitlist-new-signup.php';
			$this->template_plain = 'emails/plain/waitlist-new-signup.php';
			$this->subject        = $this->get_option( 'subject', $this->get_default_subject() );
			$this->heading        = $this->get_option( 'heading', $this->get_default_heading() );
			$this->recipient      = apply_filters( 'wcwl_admin_email_recipients', $this->get_option( 'recipient', $this->get_admin_email() ) );
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'A user has just joined a waitlist!', 'woocommerce-waitlist' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'A user has just joined a waitlist!', 'woocommerce-waitlist' );
		}

		/**
		 * Get the admin email address either from waitlist settings or fallback to WordPress settings
		 */
		public function get_admin_email() {
			$options = get_option( 'woocommerce_woocommerce_waitlist_signup_email_settings' );
			if ( isset( $options['notification_email'] ) && is_email( $options['notification_email'] ) ) {
				$email = $options['notification_email'];
			} else {
				$email = get_option( 'woocommerce_waitlist_admin_email' );
				if ( ! is_email( $email ) ) {
					$email = get_option( 'admin_email' );
				}
			}

			return $email;
		}

		/**
		 * Check whether the notification should be enabled or not based on previous setups
		 */
		public function get_enabled_status() {
			$options = get_option( 'woocommerce_woocommerce_waitlist_signup_email_settings' );
			if ( isset( $options['enabled'] ) ) {
				return $options['enabled'];
			} else {
				$enabled = get_option( 'woocommerce_waitlist_notify_admin' );
				if ( $enabled ) {
					return $enabled;
				}
			}

			return 'yes';
		}

		/**
		 * Trigger function for the mailout class
		 *
		 * @param int $email    email of user to send the mail to
		 * @param int $product_id ID of product that email refers to
		 *
		 * @return bool success
		 *
		 * @access public
		 */
		public function trigger( $email, $product_id ) {
			$product          = wc_get_product( $product_id );
			$this->object     = $product;
			$this->user_email = $email;
			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return false;
			}

			return $this->send_email();
		}

		/**
		 * Send the email and store the record in the archive if required
		 *
		 * @return bool success
		 */
		protected function send_email() {
			$subject = apply_filters( 'woocommerce_email_subject_' . $this->id, $this->subject, $this->object, $this );
			$result  = $this->send( $this->get_recipient(), $subject, $this->get_content(), $this->get_headers(), $this->get_attachments() );

			return $result;
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
			$post_id    = WooCommerce_Waitlist_Plugin::is_variation( $this->object ) ? $this->object->get_parent_id() : $product_id;
			wc_get_template(
				$this->template_html,
				array(
					'product_id'    => $product_id,
					'product_title' => get_the_title( $product_id ),
					'product_link'  => admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
					'email_heading' => apply_filters( 'woocommerce_email_heading_' . $this->id, $this->heading ),
					'user_email'    => $this->user_email,
					'admin_email'   => $this->get_admin_email(),
					'count'         => get_post_meta( $post_id, '_' . WCWL_SLUG . '_count', true ),
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
			$post_id    = WooCommerce_Waitlist_Plugin::is_variation( $this->object ) ? $this->object->get_parent_id() : $product_id;
			wc_get_template(
				$this->template_plain,
				array(
					'product_id'    => $product_id,
					'product_title' => get_the_title( $product_id ),
					'product_link'  => admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
					'email_heading' => apply_filters( 'woocommerce_email_heading_' . $this->id, $this->heading ),
					'user_email'    => $this->user_email,
					'admin_email'   => $this->get_admin_email(),
					'count'         => get_post_meta( $post_id, '_' . WCWL_SLUG . '_count', true ),
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
				'enabled'            => array(
					'title'   => __( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'woocommerce' ),
					'default' => $this->get_enabled_status(),
				),
				'notification_email' => array(
					'title'       => __( 'Email Address', 'woocommerce-waitlist' ),
					'description' => __( 'The email address to send these notifications to (defaults to your set WordPress Admin email found at Settings->General)', 'woocommerce' ),
					'desc_tip'    => true,
					'type'        => 'email',
					'placeholder' => $this->get_admin_email(),
				),
				'subject'            => array(
					'title'       => __( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'            => array(
					'title'       => __( 'Email heading', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'email_type'         => array(
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
