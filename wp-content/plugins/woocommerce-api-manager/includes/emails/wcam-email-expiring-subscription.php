<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager Expiring Subscriptions Email Class
 *
 * An email sent to the customer when a subscription will expire soon.
 *
 * @since       3.0
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Subscriptions Email
 * @extends WC_Email
 */

if ( class_exists( 'WC_Email' ) ) {
	class WCAM_Email_Expiring_Subscription extends WC_Email {

		private $api_resource;

		/**
		 * Create an instance of the class.
		 *
		 * @since 3.0
		 */
		function __construct() {

			$this->id             = 'wc_am_expiring_subscription';
			$this->title          = __( 'Expiring Subscription', 'woocommerce-api-manager' );
			$this->description    = __( 'Expiring API Manager Subscription emails are sent when a customer\'s subscription will expire soon, and provide a renewal link.', 'woocommerce-api-manager' );
			$this->template_html  = 'emails/expiring-subscription.php';
			$this->template_plain = 'emails/plain/expiring-subscription.php';
			$this->template_base  = WCAM()->plugin_path() . '/templates/';
			$this->customer_email = true;
			$this->placeholders   = array(
				'{order_date}'   => '',
				'{order_number}' => '',
			);

			parent::__construct();

			$this->manual = true;
		}

		/**
		 * Get the default email subject.
		 *
		 * @since 3.0
		 *
		 * @param bool $is_expired              Whether the Subscription has expired yet.
		 * @param bool $is_expired_grace_period Whether the Subscription grace period has expired yet.
		 *
		 * @return string
		 */
		public function get_default_subject( $is_expired = true, $is_expired_grace_period = true ) {
			if ( $is_expired === false && $is_expired_grace_period === false ) {
				return __( 'Subscription on order #{order_number} is expiring soon on {site_title}', 'woocommerce-api-manager' );
			} elseif ( $is_expired === true && $is_expired_grace_period === false ) {
				return __( 'Subscription on order #{order_number} has expired but is renewable on {site_title}', 'woocommerce-api-manager' );
			}

			return __( 'Subscription on order #{order_number} has expired on {site_title}', 'woocommerce-api-manager' );
		}

		/**
		 * Get the default email heading.
		 *
		 * @since 3.0
		 *
		 * @param bool $is_expired              Whether the Subscription has expired yet.
		 * @param bool $is_expired_grace_period Whether the Subscription grace period has expired yet.
		 *
		 * @return string
		 */
		public function get_default_heading( $is_expired = true, $is_expired_grace_period = true ) {
			if ( $is_expired === false && $is_expired_grace_period === false ) {
				return __( 'Subscription on order #{order_number} is expiring soon on {site_title}', 'woocommerce-api-manager' );
			} elseif ( $is_expired === true && $is_expired_grace_period === false ) {
				return __( 'Subscription on order #{order_number} has expired but is renewable on {site_title}', 'woocommerce-api-manager' );
			}

			return __( 'Subscription on order #{order_number} has expired on {site_title}', 'woocommerce-api-manager' );
		}

		/**
		 * Get email subject.
		 *
		 * @since 3.0
		 *
		 * @return string
		 */
		public function get_subject() {
			$is_expired              = WC_AM_ORDER_DATA_STORE()->is_time_expired( $this->api_resource->access_expires );
			$is_expired_grace_period = WC_AM_GRACE_PERIOD()->is_expired( $this->api_resource->api_resource_id );

			if ( ! $is_expired && ! $is_expired_grace_period ) {
				$subject = $this->get_option( 'subject_expiring', $this->get_default_subject( false, false ) );

				return apply_filters( 'wc_am_email_subject_customer_subscription_not_expired', $this->format_string( $subject ), $this->api_resource, $this );
			} elseif ( $is_expired && ! $is_expired_grace_period ) {
				$subject = $this->get_option( 'subject_still_renewable', $this->get_default_subject( true, false ) );

				return apply_filters( 'wc_am_email_subject_customer_subscription_expired', $this->format_string( $subject ), $this->api_resource, $this );
			}

			$subject = $this->get_option( 'subject_expired', $this->get_default_subject() );

			return apply_filters( 'wc_am_email_subject_customer_subscription_expired', $this->format_string( $subject ), $this->api_resource, $this );
		}

		/**
		 * Get email heading.
		 *
		 * @since 3.0
		 *
		 * @return string
		 */
		public function get_heading() {
			$is_expired              = WC_AM_ORDER_DATA_STORE()->is_time_expired( $this->api_resource->access_expires );
			$is_expired_grace_period = WC_AM_GRACE_PERIOD()->is_expired( $this->api_resource->api_resource_id );

			if ( ! $is_expired && ! $is_expired_grace_period ) {
				$heading = $this->get_option( 'heading_expiring', $this->get_default_heading( false, false ) );

				return apply_filters( 'wc_am_email_heading_customer_subscription_not_expired', $this->format_string( $heading ), $this->api_resource, $this );
			} elseif ( $is_expired && ! $is_expired_grace_period ) {
				$heading = $this->get_option( 'heading_still_renewable', $this->get_default_heading( true, false ) );

				return apply_filters( 'woocommerce_email_heading_customer_subscription_expired', $this->format_string( $heading ), $this->api_resource, $this );
			}

			$heading = $this->get_option( 'heading_expired', $this->get_default_heading() );

			return apply_filters( 'woocommerce_email_heading_customer_subscription_expired', $this->format_string( $heading ), $this->api_resource, $this );
		}

		/**
		 * Default content to show below main email content.
		 *
		 * @since 3.0
		 *
		 * @return string
		 */
		public function get_default_additional_content() {
			return __( 'Thanks for using {site_url}!', 'woocommerce-api-manager' );
		}

		/**
		 * Trigger function.
		 *
		 * @since 3.0
		 *
		 * @param int $api_resource_id
		 *
		 * @return void
		 */
		function trigger( $api_resource_id ) {
			if ( ! empty( $api_resource_id ) ) {
				$this->setup_locale();

				$this->api_resource = WC_AM_API_RESOURCE_DATA_STORE()->get_resources_by_api_resource_id( $api_resource_id );

				try {
					if ( ! WC_AM_FORMAT()->empty( $this->api_resource ) && $this->api_resource->sub_id == 0 ) {
						$order = WC_AM_ORDER_DATA_STORE()->get_order_object( $this->api_resource->order_id );

						if ( is_a( $order, 'WC_Order' ) ) {
							$this->object                           = $order;
							$this->recipient                        = $this->object->get_billing_email();
							$this->placeholders[ '{order_date}' ]   = wc_format_datetime( $this->object->get_date_created() );
							$this->placeholders[ '{order_number}' ] = $this->object->get_order_number();
						}
					}

					if ( $this->get_recipient() ) {
						$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
					}
				} catch ( Exception $e ) {
					WC_AM_LOG()->log_error( esc_html__( 'WCAM_Email_Expiring_Subscription()->trigger() error: ', 'woocommerce-api-manager' ) . $e, 'email-trigger' );
				}

				$this->restore_locale();
			}
		}

		/**
		 * Get content HTML.
		 *
		 * @since 3.0
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html( $this->template_html, array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => false,
				'plain_text'         => false,
				'api_resource'       => $this->api_resource,
				'email'              => $this,
			),                           '', $this->template_base );
		}

		/**
		 * Get content plain.
		 *
		 * @since 3.0
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html( $this->template_plain, array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => false,
				'plain_text'         => true,
				'api_resource'       => $this->api_resource,
				'email'              => $this,
			),                           '', $this->template_base );
		}

		/**
		 * Initialise settings form fields.
		 *
		 * @since 3.0
		 */
		public function init_form_fields() {
			/* translators: %s: list of placeholders */
			$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'woocommerce-api-manager' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
			$this->form_fields = array(
				'subject_expiring'        => array(
					'title'       => __( 'Subject expiring', 'woocommerce-api-manager' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject( false, false ),
					'default'     => '',
				),
				'heading_expiring'        => array(
					'title'       => __( 'Email heading expiring', 'woocommerce-api-manager' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading( false, false ),
					'default'     => '',
				),
				'subject_still_renewable' => array(
					'title'       => __( 'Subject still renewable', 'woocommerce-api-manager' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject( true, false ),
					'default'     => '',
				),
				'heading_still_renewable' => array(
					'title'       => __( 'Email heading still renewable', 'woocommerce-api-manager' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading( true, false ),
					'default'     => '',
				),
				'subject_expired'         => array(
					'title'       => __( 'Subject expired', 'woocommerce-api-manager' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading_expired'         => array(
					'title'       => __( 'Email heading expired', 'woocommerce-api-manager' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading( true ),
					'default'     => '',
				),
				'additional_content'      => array(
					'title'       => __( 'Additional content', 'woocommerce-api-manager' ),
					'description' => __( 'Text to appear below the main email content.', 'woocommerce-api-manager' ) . ' ' . $placeholder_text,
					'css'         => 'width:400px; height: 75px;',
					'placeholder' => __( 'N/A', 'woocommerce-api-manager' ),
					'type'        => 'textarea',
					'default'     => $this->get_default_additional_content(),
					'desc_tip'    => true,
				),
				'email_type'              => array(
					'title'       => __( 'Email type', 'woocommerce-api-manager' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce-api-manager' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
	}
}

return new WCAM_Email_Expiring_Subscription();