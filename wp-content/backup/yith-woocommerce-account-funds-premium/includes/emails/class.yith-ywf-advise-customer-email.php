<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_YWF_Advise_Customer_Email' ) ) {

	class YITH_YWF_Advise_Customer_Email extends WC_Email {

		public function __construct() {
			$this->id             = 'yith_advise_user_funds_email';
			$this->customer_email = true;
			$this->title          = __( 'Customer funds note', 'yith-woocommerce-account-funds' );
			$this->description    = __( 'This email is sent to customers when the administrator changes the amount of their available funds', 'yith-woocommerce-account-funds' );

			$this->subject = $this->get_option( 'ywf_mail_admin_change_fund_subject' );
			$this->heading = $this->get_option( 'ywf_user_change_fund_heading' );

			$this->template_base  = YITH_FUNDS_TEMPLATE_PATH;
			$this->template_html  = 'emails/email-advise-user-funds.php';
			$this->template_plain = 'emails/plain/email-advise-user-funds.php';

			$this->placeholders = array(
				'{site_title}'     => '',
				'{before_funds}'   => '',
				'{after_funds}'    => '',
				'{customer_email}' => '',
				'{customer_name}'  => '',
				'{log_date}'       => '',
				'{change_reason}'  => ''
			);

			add_action( 'ywf_send_advise_user_fund_email_notification', array( $this, 'trigger' ), 10, 1 );
			parent::__construct();
		}


		public function trigger( $args ) {

			if ( empty( $args ) ) {
				return;
			}

			$this->setup_locale();
			if ( apply_filters( 'ywf_send_email', true, $args['user_id'] ) ) {
				/**@var WP_User $user */
				$user            = get_user_by( 'id', $args['user_id'] );
				$this->recipient = $user->user_email;
				$order_id        = ywf_get_user_currency( $args['user_id'] );
				$order           = wc_get_order( $order_id );

				if ( $order ) {
					$currency = $order->get_currency();
				} else {
					$currency = get_woocommerce_currency();

				}
				$user_name     = $user->display_name;
				$log_date      = $args['log_date'];
				$change_reason = $args['change_reason'];
				$before_funds  = apply_filters( 'yith_fund_into_customer_email', $args['before_funds'], $currency );
				$after_funds   = apply_filters( 'yith_fund_into_customer_email', $args['after_funds'], $currency );

				$this->object = $user;
				$this->placeholders['{customer_name}']  = $user_name;
				$this->placeholders['{customer_email}'] = $this->recipient;
				$this->placeholders['{log_date}']       = $log_date;
				$this->placeholders['{change_reason}']  = $change_reason;
				$this->placeholders['{before_funds}']   = wc_price( $before_funds, array( 'currency' => $currency ) );
				$this->placeholders['{after_funds}']    = wc_price( $after_funds, array( 'currency' => $currency ) );;
			}
			if ( $this->is_enabled() && $this->get_recipient() ) {

				$this->send( $this->get_recipient(), $this->get_subject(), $this->format_string( $this->get_content() ), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		public function get_content_html() {
			return wc_get_template_html( $this->template_html, array(
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this,
			),
				false, YITH_FUNDS_TEMPLATE_PATH );
		}

		public function get_content_plain() {
			return wc_get_template_html( $this->template_plain, array(
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			),
				false, YITH_FUNDS_TEMPLATE_PATH );
		}


		/**
		 * get_headers function.
		 *
		 * @access public
		 * @return string
		 */
		public function get_headers() {

			$headers = "Content-Type: " . $this->get_content_type() . "\r\n";

			return apply_filters( 'woocommerce_email_headers', $headers, $this->id, $this->object, $this );
		}

		/**
		 * check if this email is enabled
		 * @return bool
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		public function is_enabled() {
			$enabled = $this->get_option( 'ywf_mail_admin_change_fund_enabled' );

			return apply_filters( 'woocommerce_email_enabled_' . $this->id, 'yes' == $enabled, $this->object, $this );
		}

		/**
		 * Admin Panel Options Processing - Saves the options to the DB
		 *
		 * @return  void
		 * @since   1.6.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function process_admin_options() {
			woocommerce_update_options( $this->form_fields );
		}

		/**
		 * Override option key.
		 *
		 * @param   $key string
		 *
		 * @return  string
		 * @since   1.6.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_field_key( $key ) {
			return $key;
		}

		/**
		 * Get plugin option.
		 *
		 * @param $key         string
		 * @param $empty_value mixed
		 *
		 * @return  mixed
		 * @since   1.6.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_option( $key, $empty_value = null ) {

			$setting = get_option( $key );

			// Get option default if unset.
			if ( ! $setting ) {
				$form_fields = $this->get_form_fields();
				$setting     = isset( $form_fields[ $key ] ) ? $this->get_field_default( $form_fields[ $key ] ) : '';
			}

			return $setting;

		}

		/**
		 * Get email subject.
		 *
		 * @return  string
		 * @since   1.4.0
		 * @author  YITH
		 */
		public function get_subject() {
			return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $this->get_option( 'ywf_mail_admin_change_fund_subject', $this->get_default_subject() ) ), $this->object, $this );
		}

		public function get_heading() {

			return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->get_option( 'ywf_user_change_fund_heading', $this->get_default_heading() ) ), $this->object, $this );

		}

		/**
		 * Initialise Settings Form Fields
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function init_form_fields() {
			$options = include( YITH_FUNDS_DIR . '/plugin-options/emails-multi-tab/edit-funds-email-options.php' );
			$options = $options['emails-multi-tab-edit-funds-email'];

			foreach ( $options as $key => $option ) {

				if ( isset( $option['type'] ) && ! in_array( $option['type'], array( 'title', 'sectionend' ) ) ) {

					$new_option = $option;

					$new_option['type']        = $option['yith-type'] == 'onoff' ? 'checkbox' : $option['yith-type'];
					$new_option['title']       = $option['name'];
					$new_option['description'] = ! empty( $option['desc'] ) ? $option['desc'] : '';

					if ( 'number' == $new_option['type'] ) {

						$new_option['custom_attributes'] = array(
							'min'  => $new_option['min'],
							'step' => $new_option['step'],
						);
					}

					unset( $new_option['yith-type'] );
					unset( $new_option['name'] );
					unset( $new_option['desc'] );
					$this->form_fields[ $new_option['id'] ] = $new_option;
				}
			}
		}

		public function get_email_type() {

			return $this->get_option( 'ywf_mail_admin_change_fund_type' );
		}

	}
}

return new YITH_YWF_Advise_Customer_Email();