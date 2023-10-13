<?php
/**
 * Class YITH_WCBK_Email
 * Email class.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Email' ) ) {
	/**
	 * Class YITH_WCBK_Email
	 */
	abstract class YITH_WCBK_Email extends WC_Email {
		/**
		 * The booking object.
		 *
		 * @var YITH_WCBK_Booking
		 */
		public $object;

		/**
		 * Default custom message.
		 *
		 * @var string
		 */
		public $custom_message = '';

		/**
		 * Are actions initialized?
		 *
		 * @var bool
		 */
		public static $actions_initialized = false;

		/**
		 * Is it sending including iCal?
		 *
		 * @var bool
		 */
		public static $sending_booking_email_with_ical = false;

		/**
		 * Placeholders having a specific behavior for plain text emails.
		 * They'll be converted to the plain version
		 * Example: {booking_details} -> {booking_details:plain}
		 *
		 * @var string[]
		 */
		private $plain_text_placeholders = array(
			'{booking_details}' => '{booking_details:plain}',
		);

		/**
		 * YITH_WCBK_Email constructor.
		 */
		public function __construct() {
			$this->placeholders = array_merge(
				array(
					'{booking_id}'      => '',
					'{booking_id_link}' => '',
					'{booking_details}' => '',
					'{product_name}'    => '',
					'{status}'          => '',
					'{customer_name}'   => '',
				),
				$this->placeholders
			);

			$this->set_default_params();

			parent::__construct();

			$this->maybe_init_static_actions();
		}

		/**
		 * Override it to set default params.
		 *
		 * @since 3.5.0
		 */
		public function set_default_params() {

		}

		/**
		 * Trigger.
		 *
		 * @param int $booking_id The booking ID.
		 */
		public function trigger( $booking_id ) {
			if ( $booking_id ) {
				$this->object = yith_get_booking( $booking_id );

				$this->prepare_and_send();
			}
		}

		/**
		 * Prepare the email and send it.
		 *
		 * @since 3.5.0
		 */
		protected function prepare_and_send() {
			if ( $this->object && $this->object->is_valid() ) {
				$this->maybe_set_booking_recipient();

				if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
					return;
				}

				do_action( 'yith_wcbk_email_before_sending', $this, $this->customer_email );

				$this->init_placeholders_before_sending();
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

				do_action( 'yith_wcbk_email_after_sending', $this, $this->customer_email );
			}
		}

		/**
		 * Initialize placeholders before sending.
		 */
		protected function init_placeholders_before_sending() {
			if ( $this->object && $this->object->is_valid() ) {
				$this->placeholders['{booking_id}']    = $this->object->get_id();
				$this->placeholders['{status}']        = $this->object->get_status_text();
				$this->placeholders['{customer_name}'] = yith_wcbk_get_user_name( $this->object->get_user() );

				$product = $this->object->get_product();
				if ( $product ) {
					$this->placeholders['{product_name}'] = sprintf(
						'<a href="%s">%s</a>',
						esc_url( $product->get_permalink() ),
						esc_html( $product->get_name() )
					);
				}

				$this->placeholders['{booking_id_link}'] = sprintf(
					'<a href="%s">%s</a>',
					esc_url( $this->is_customer_email() ? $this->object->get_view_booking_url() : get_edit_post_link( $this->object->get_id() ) ),
					'#' . esc_html( $this->object->get_id() )
				);

				ob_start();
				do_action( 'yith_wcbk_email_booking_details', $this->object, ! $this->is_customer_email(), false, $this );
				$this->placeholders['{booking_details}'] = ob_get_clean();

				ob_start();
				do_action( 'yith_wcbk_email_booking_details', $this->object, ! $this->is_customer_email(), true, $this );
				$this->placeholders['{booking_details:plain}'] = ob_get_clean();

				$this->placeholders = apply_filters( 'yith_wcbk_email_placeholders', $this->placeholders, $this );
			}
		}

		/**
		 * Maybe set booking recipient email.
		 */
		protected function maybe_set_booking_recipient() {
			if ( $this->customer_email && $this->object ) {
				$this->recipient = $this->object->get_user_email();
			}
		}

		/**
		 * Initialize static actions that will be executed one time only.
		 */
		public function maybe_init_static_actions() {
			if ( ! self::$actions_initialized ) {
				add_action( 'phpmailer_init', array( __CLASS__, 'reset_sending_booking_email_with_ical' ), 0 );
				add_action( 'phpmailer_init', array( __CLASS__, 'remove_ical_if_not_booking_email' ), 999 );
				self::$actions_initialized = true;
			}
		}

		/**
		 * Set the $sending_booking_email_with_ical attribute to false.
		 */
		public static function reset_sending_booking_email_with_ical() {
			self::$sending_booking_email_with_ical = false;
		}

		/**
		 * Remove the iCal for emails not sent by Booking.
		 *
		 * @param PHPMailer $mailer The mailer.
		 */
		public static function remove_ical_if_not_booking_email( $mailer ) {
			// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( ! self::$sending_booking_email_with_ical && ! empty( $mailer->Ical ) && strpos( $mailer->Ical, 'YITH Booking and Appointment for WooCommerce' ) ) {
				$mailer->Ical = '';
			}
			// phpcs:enable
		}


		/**
		 * Handle multipart mail.
		 *
		 * @param PHPMailer $mailer The mailer.
		 *
		 * @return PHPMailer
		 */
		public function handle_multipart( $mailer ) {
			if ( $this->sending ) {
				self::$sending_booking_email_with_ical = true;
			}
			$include_ical = apply_filters( 'yith_wcbk_email_include_ical', $this->customer_email, $this );
			if ( $include_ical && $this->sending && 'multipart' === $this->get_email_type() && $this->object && $this->object->get_id() ) {
				$ical         = yith_wcbk()->exporter->get_ics( $this->object->get_id(), false, true );
				$mailer->Ical = $ical; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}

			return parent::handle_multipart( $mailer );
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			$params = array_merge(
				array(
					'booking'        => $this->object,
					'email_heading'  => $this->get_heading(),
					'sent_to_admin'  => ! $this->is_customer_email(),
					'plain_text'     => true,
					'email'          => $this,
					'custom_message' => $this->get_custom_message( true ),
				),
				$this->get_extra_content_params()
			);

			return wc_get_template_html( $this->template_plain, $params, '', $this->template_base );
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			$params = array_merge(
				array(
					'booking'        => $this->object,
					'email_heading'  => $this->get_heading(),
					'sent_to_admin'  => ! $this->is_customer_email(),
					'plain_text'     => false,
					'email'          => $this,
					'custom_message' => $this->get_custom_message( false ),
				),
				$this->get_extra_content_params()
			);

			return wc_get_template_html( $this->template_html, $params, '', $this->template_base );
		}

		/**
		 * Do you need extra content params? If so, override me!
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
		 * @since  2.0.0
		 */
		public function get_default_subject() {
			return $this->subject;
		}

		/**
		 * Get email heading.
		 *
		 * @return string
		 * @since  2.0.0
		 */
		public function get_default_heading() {
			return $this->heading;
		}

		/**
		 * Default content to show below main email content.
		 *
		 * @since 3.0.0
		 */
		public function get_default_custom_message() {
			return $this->custom_message;
		}

		/**
		 * Return content from the custom_message field.
		 *
		 * @param bool $plain_text Plain text flag.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_custom_message( $plain_text = false ) {
			$custom_message = $this->get_option( 'custom_message', $this->get_default_custom_message() );
			if ( $plain_text ) {
				$custom_message = strtr( $custom_message, $this->plain_text_placeholders );
			}

			return $this->format_string( $custom_message );
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'        => array(
					'title'   => esc_html__( 'Enable/Disable', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Enable this email notification', 'yith-booking-for-woocommerce' ),
					'default' => 'yes',
				),
				'subject'        => array(
					'title'       => esc_html__( 'Subject', 'yith-booking-for-woocommerce' ),
					'type'        => 'text',
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'        => array(
					'title'       => esc_html__( 'Email Heading', 'yith-booking-for-woocommerce' ),
					'type'        => 'text',
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'custom_message' => array(
					'title'                   => _x( 'Message', 'Email message title', 'yith-booking-for-woocommerce' ),
					'type'                    => 'yith_wcbk_field',
					'yith_wcbk_field_type'    => 'textarea-editor',
					'default'                 => '',
					'teeny'                   => true,
					'media_buttons'           => false,
					'default_content_options' => array(
						'content'          => $this->get_default_custom_message(),
						'edit_text'        => __( 'Edit message', 'yith-booking-for-woocommerce' ),
						'use_default_text' => __( 'Restore default message', 'yith-booking-for-woocommerce' ),
					),
					'description'             =>
						'<span class="yith-wcbk-emails__email__placeholders">' .
						'<span class="yith-wcbk-emails__email__placeholders__label">' . esc_html__( 'Available placeholders:', 'yith-booking-for-woocommerce' ) . '</span>' .
						'<code>' . implode( '</code> <code>', array_keys( $this->placeholders ) ) . '</code>' .
						'</span>',
					'desc_tip'                => false,
				),
				'email_type'     => array(
					'title'   => __( 'Email type', 'yith-booking-for-woocommerce' ),
					'type'    => 'select',
					'default' => 'html',
					'class'   => 'email_type wc-enhanced-select',
					'options' => $this->get_email_type_options(),
				),
			);
		}

		/**
		 * Email type options.
		 * Allow only HTML and Multipart
		 *
		 * @return array
		 */
		public function get_email_type_options() {
			$types = parent::get_email_type_options();

			if ( isset( $types['plain'] ) ) {
				unset( $types['plain'] );
			}

			return $types;
		}

		/**
		 * Return email type.
		 * Allow only HTML and Multipart
		 *
		 * @return string
		 */
		public function get_email_type() {
			$type = parent::get_email_type();

			return 'plain' !== $type ? $type : 'html';
		}

		/**
		 * Return the recipient to be shown in settings list.
		 *
		 * @return string
		 */
		public function get_recipient_to_show_in_settings_list() {
			return $this->is_customer_email() ? __( 'Customer', 'yith-booking-for-woocommerce' ) : $this->get_recipient();
		}

		/**
		 * Generate custom fields by using YITH framework fields.
		 *
		 * @param string $key  The key of the field.
		 * @param array  $data The attributes of the field as an associative array.
		 *
		 * @return string
		 */
		public function generate_yith_wcbk_field_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$value     = $this->get_option( $key );
			$defaults  = array(
				'title'                   => '',
				'label'                   => '',
				'yith_wcbk_field_type'    => 'text',
				'description'             => '',
				'desc_tip'                => false,
				'default_content_options' => array(),
			);

			wp_enqueue_script( 'yith-plugin-fw-fields' );
			wp_enqueue_style( 'yith-plugin-fw-fields' );

			$data = wp_parse_args( $data, $defaults );

			$default_content_options                     = $data['default_content_options'];
			$default_content_options['content']          = $default_content_options['content'] ?? '';
			$default_content_options['edit_text']        = $default_content_options['edit_text'] ?? __( 'Edit', 'yith-booking-for-woocommerce' );
			$default_content_options['use_default_text'] = $default_content_options['use_default_text'] ?? __( 'Restore default message', 'yith-booking-for-woocommerce' );
			$default_content                             = $default_content_options['content'];

			$field          = $data;
			$field['type']  = $data['yith_wcbk_field_type'];
			$field['name']  = $field_key;
			$field['value'] = $value;
			$private_keys   = array( 'label', 'title', 'description', 'yith_wcbk_field_type', 'default_content_options' );

			foreach ( $private_keys as $private_key ) {
				unset( $field[ $private_key ] );
			}

			$row_classes = implode(
				' ',
				array_filter(
					array(
						'yith-wcbk-email-field__row',
						! ! $value ? '' : 'yith-wcbk-email-field__row--empty-value',
					)
				)
			);

			ob_start();
			?>
			<tr valign="top" class="<?php echo esc_attr( $row_classes ); ?>" data-default-content="<?php echo esc_attr( $default_content ); ?>">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				</th>
				<td class="forminp yith-plugin-ui">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<?php if ( $default_content && 'textarea-editor' === $field['type'] ) : ?>
							<div class="yith-wcbk-email-field__default-content">
								<?php
								echo wp_kses_post( nl2br( $default_content ) );
								yith_plugin_fw_get_component(
									array(
										'class'  => 'yith-wcbk-email-field__edit',
										'type'   => 'action-button',
										'action' => 'edit',
										'icon'   => 'edit',
										'title'  => $default_content_options['edit_text'],
										'url'    => '#',
									),
									true
								);
								?>
							</div>
							<div class="yith-wcbk-email-field__field">
								<?php yith_plugin_fw_get_field( $field, true, true ); ?>
							</div>
						<?php else : ?>
							<?php yith_plugin_fw_get_field( $field, true, true ); ?>
						<?php endif; ?>
						<?php echo wp_kses_post( $this->get_description_html( $data ) ); ?>
						<?php if ( $default_content && 'textarea-editor' === $field['type'] ) : ?>
							<span class="yith-wcbk-email-field__use-default">
								<?php echo esc_html( $default_content_options['use_default_text'] ); ?>
							</span>
						<?php endif; ?>
					</fieldset>
				</td>
			</tr>
			<?php

			return ob_get_clean();
		}
	}
}
