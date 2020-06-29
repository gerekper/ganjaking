<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_YWF_Vendor_Redeem_Email' ) ) {

	class  YITH_YWF_Vendor_Redeem_Email extends WC_Email {

		protected $redeem_transactions;
		public function __construct() {

			$this->id             = 'ywf_vendor_redeem_email';
			$this->customer_email = false;
			$this->title          = _x( 'Vendor Redeem Funds','Is the email title', 'yith-woocommerce-account-funds' );
			$this->description    = __( 'This email is sent to administrator when one or more vendor redeem funds', 'yith-woocommerce-account-funds' );

			$this->template_base  = YITH_FUNDS_TEMPLATE_PATH;
			$this->template_html  = 'emails/vendor-redeem-funds.php';
			$this->template_plain = 'emails/plain/vendor-redeem-funds.php';
			$this->recipient      = $this->get_option( 'ywf_redeem_email_recipient' );
			$this->subject        = $this->get_option( 'ywf_redeem_email_subject' );
			$this->email_type     = $this->get_option( 'ywf_redeem_email_type' );
			$this->redeem_transactions = array();
			add_action( $this->id . '_notification', array( $this, 'trigger' ), 10, 1 );

			parent::__construct();
		}

		/**
		 * @param $args
		 *
		 * @author  YITH
		 * @since   1.4.0
		 */
		public function trigger( $args ) {

			$this->setup_locale();

			if ( $this->is_enabled() && ! empty( $args ) && $this->get_recipient() ) {

				$this->placeholders['{date}'] = wc_format_datetime( new WC_DateTime( date( 'Y-m-d' ), new DateTimeZone( 'UTC' ) ) );
				$this->redeem_transactions = $args;
				$this->object = false;
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}
			$this->restore_locale();
		}

		/**
		 * check if the email is enabled
		 * @return bool
		 * @since 1.4.0
		 * @author YITH
		 */
		public function is_enabled() {

			return apply_filters( 'woocommerce_email_enabled_' . $this->id, 'yes' == $this->get_option( 'ywf_redeem_email_enabled' ) && ( defined( 'YITH_WPV_PREMIUM' ) && defined( 'YITH_WPV_VERSION' ) && version_compare( YITH_WPV_VERSION, '3.5.3', '>=' ) ), $this->object, $this );
		}

		/**
		 * Admin Panel Options Processing - Saves the options to the DB
		 *
		 * @return  void
		 * @since   1.4.0
		 * @author  YITH
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
		 * @since   1.4.0
		 * @author  YITH
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
		 * @since   1.4.0
		 * @author  YITH
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
		 * Initialise Settings Form Fields
		 *
		 * @return  void
		 * @since   1.4.0
		 * @author  YITH
		 */
		public function init_form_fields() {
			$options = include( YITH_FUNDS_DIR . '/plugin-options/emails-multi-tab/redeem-email-options.php' );
			$options = $options['emails-multi-tab-redeem-email'];

			foreach ( $options as $key => $option ) {

				if ( isset( $option['type'] ) && ! in_array( $option['type'], array( 'title', 'sectionend' ) ) ) {

					if( 'html' !== $option['yith-type'] ) {


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
		}

		public function get_email_type() {
			return $this->get_option('ywf_redeem_email_type');
		}

		/**
		 * Get email subject.
		 *
		 * @return  string
		 * @since   1.4.0
		 * @author  YITH
		 */
		public function get_subject() {
			return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $this->get_option( 'ywf_redeem_email_subject', $this->get_default_subject() ) ), $this->object, $this );
		}

		public function get_heading() {

			return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->get_option('ywf_redeem_email_heading' , $this->get_default_heading() ) ), $this->object , $this);

		}

		public function get_content_html() {

			return wc_get_template_html(
				$this->template_html,
				array(
					'email_heading' => $this->get_heading(),
					'sent_to_admin'      => true,
					'plain_text'         => false,
					'email'              => $this,
					'redeem_transactions' => $this->redeem_transactions
				),
				false,
				$this->template_base
			);
		}

		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				array(
					'email_heading' => $this->get_heading(),
					'sent_to_admin'      => true,
					'plain_text'         => false,
					'email'              => $this,
					'redeem_transactions' => $this->redeem_transactions
				),
				false,
				$this->template_base
			);
		}

	}
}

return new YITH_YWF_Vendor_Redeem_Email();