<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Email' ) ) {
	/**
	 * Class YITH_WCMBS_Email
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCMBS_Email extends WC_Email {

		/**
		 * @var YITH_WCMBS_Membership
		 */
		public $object;

		/**
		 * @var string
		 */
		public $custom_message = '';

		/**
		 * @var int
		 */
		public $user_id;

		public function __construct() {
			$this->placeholders = array_merge(
				array(
					'{firstname}'              => '',
					'{lastname}'               => '',
					'{membership_name}'        => '',
					'{membership_expire_date}' => '',
				),
				$this->placeholders
			);

			parent::__construct();
		}

		/**
		 * Trigger.
		 *
		 * @param array $args
		 */
		function trigger( $args ) {

			if ( ! $this->is_enabled() ) {
				return;
			}

			if ( $args ) {
				/**
				 * @var int                          $user_id
				 * @var YITH_WCMBS_Membership | bool $membership
				 */
				$default = array(
					'user_id'    => 0,
					'membership' => false,
				);

				$args = wp_parse_args( $args, $default );
				extract( $args );

				if ( $membership instanceof YITH_WCMBS_Membership ) {
					$this->object  = $membership;
					$this->user_id = $user_id;
					$user          = get_user_by( 'id', $user_id );
					$order         = isset( $membership->order_id ) ? wc_get_order( $membership->order_id ) : false;

					$this->placeholders['{firstname}']              = ! ! $user ? $user->user_firstname : '';
					$this->placeholders['{lastname}']               = ! ! $user ? $user->user_lastname : '';
					$this->placeholders['{membership_name}']        = $membership->get_plan_title();
					$this->placeholders['{membership_expire_date}'] = apply_filters( 'yith_wcmbs_email_membership_status_expiration_date', $membership->get_formatted_date( 'end_date' ), $membership, $this );

					$user_email = ! ! $order ? $order->get_billing_email() : '';
					if ( ! $user_email ) {
						$user_email = ! ! $user ? $user->user_email : '';
					}
					$this->recipient = $user_email;

					if ( $this->get_recipient() ) {
						if ( apply_filters( 'yith_wcmbs_maybe_send_email_membership', true, $this ) ) {
							$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
						}
					}
				}
			}
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			$params = array_merge( array(
									   'membership'     => $this->object,
									   'email_heading'  => $this->get_heading(),
									   'custom_message' => $this->get_custom_message(),
									   'user_id'        => $this->user_id,
									   'plain_text'     => false,
									   'email'          => $this,
								   ),
								   $this->get_extra_content_params()
			);

			return wc_get_template_html( $this->template_html, $params, '', $this->template_base );
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			$params = array_merge( array(
									   'membership'     => $this->object,
									   'email_heading'  => $this->get_heading(),
									   'custom_message' => $this->get_custom_message(),
									   'user_id'        => $this->user_id,
									   'plain_text'     => true,
									   'email'          => $this,
								   ),
								   $this->get_extra_content_params()
			);

			return wc_get_template_html( $this->template_plain, $params, '', $this->template_base );
		}

		/**
		 * do you need extra content params? If so, override me!
		 *
		 * @return array
		 */
		public function get_extra_content_params() {
			return array();
		}

		/**
		 * Get email subject.
		 *
		 * @return string
		 */
		public function get_default_subject() {
			return $this->subject;
		}

		/**
		 * Get email heading.
		 *
		 * @return string
		 */
		public function get_default_heading() {
			return $this->heading;
		}

		/**
		 * Get email custom message.
		 *
		 * @return string
		 */
		public function get_default_custom_message() {
			return $this->custom_message;
		}

		/**
		 * Get email custom message.
		 *
		 * @return string
		 */
		public function get_custom_message() {
			return $this->format_string( $this->get_option( 'custom_message', $this->get_default_custom_message() ) );
		}

		/**
		 * Initialise Settings Form Fields - these are generic email options most will use.
		 */
		public function init_form_fields() {
			// translators: %s is a comma-separated list of available placeholders
			$placeholder_text = sprintf( __( 'Available placeholders: %s', 'yith-woocommerce-membership' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );

			parent::init_form_fields();

			if ( isset( $this->form_fields['additional_content'] ) ) {
				unset( $this->form_fields['additional_content'] );
			}

			$email_type = $this->form_fields['email_type'];
			unset( $this->form_fields['email_type'] );

			$this->form_fields['custom_message'] = array(
				'title'       => __( 'Custom Message', 'yith-woocommerce-membership' ),
				'type'        => 'textarea',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => '',
				'default'     => $this->get_default_custom_message(),
			);

			$this->form_fields['email_type'] = $email_type;
		}

	}

}
