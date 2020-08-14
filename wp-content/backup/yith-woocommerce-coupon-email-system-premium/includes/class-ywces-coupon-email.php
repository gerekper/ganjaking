<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YWCES_Coupon_Mail' ) ) {

	/**
	 * Implements Coupon Mail for YWCES plugin
	 *
	 * @class   YWCES_Coupon_Mail
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 * @extends WC_Email
	 *
	 * @package Yithemes
	 */
	class YWCES_Coupon_Mail extends WC_Email {

		/**
		 * @var int $mail_body content of the email
		 */
		var $mail_body;

		/**
		 * @var int $template the template of the email
		 */
		var $template_type;

		/**
		 * Constructor
		 *
		 * Initialize email type and set templates paths
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->id             = 'yith-coupon-email-system';
			$this->customer_email = true;
			$this->description    = esc_html__( 'YITH WooCommerce Coupon Email System offers an automatic way to send a coupon to your users according to specific events.', 'yith-woocommerce-coupon-email-system' );
			$this->title          = esc_html__( 'Coupon Email System', 'yith-woocommerce-coupon-email-system' );
			$this->template_base  = YWCES_TEMPLATE_PATH . '/';
			$this->template_html  = 'emails/coupon-email.php';
			$this->template_plain = 'emails/plain/coupon-email.php';
			$this->enabled        = 'yes';
			$this->lang           = '';

			add_filter( 'send_ywces_mail_notification', array( $this, 'trigger' ), 15, 1 );

			parent::__construct();

		}

		/**
		 * Trigger email send
		 *
		 * @param   $mail_args
		 *
		 * @return  bool
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function trigger( $mail_args ) {

			$this->heading       = $mail_args['mail_subject'];
			$this->subject       = $mail_args['mail_subject'];
			$this->mail_body     = $mail_args['mail_body'];
			$this->template_type = ( ! $mail_args['template'] ) ? get_option( 'ywces_mail_template' ) : $mail_args['template'];
			$this->recipient     = $mail_args['mail_address'];
			$this->email_type    = get_option( 'ywces_mail_type' );

			if ( ! $this->get_recipient() ) {
				return false;
			}

			return $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), "" );

		}

		/**
		 * Send the email.
		 *
		 * @param string $to
		 * @param string $subject
		 * @param string $message
		 * @param string $headers
		 * @param string $attachments
		 *
		 * @return  bool
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function send( $to, $subject, $message, $headers, $attachments ) {

			add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
			add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
			add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

			$message = apply_filters( 'woocommerce_mail_content', $this->style_inline( $message ) );

			if ( defined( 'YWCES_PREMIUM' ) && get_option( 'ywces_mandrill_enable' ) == 'yes' ) {

				$return = YWCES_Mandrill()->send_email( $to, $subject, $message, $headers, $attachments );

			} else {

				$return = wp_mail( $to, $subject, $message, $headers, $attachments );

			}

			remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
			remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
			remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

			return $return;

		}

		/**
		 * Apply inline styles to dynamic content.
		 *
		 * @param string|null $content
		 *
		 * @return  string
		 * @since   1.1.4
		 *
		 * @author  Alberto Ruggiero
		 */
		public function style_inline( $content ) {

			// make sure we only inline CSS for html emails
			if ( in_array( $this->get_content_type(), array( 'text/html', 'multipart/alternative' ) ) && class_exists( 'DOMDocument' ) ) {

				ob_start();

				if ( array_key_exists( $this->template_type, YITH_WCES()->_email_templates ) ) {

					$path   = YITH_WCES()->_email_templates[ $this->template_type ]['path'];
					$folder = YITH_WCES()->_email_templates[ $this->template_type ]['folder'];

					wc_get_template( $folder . '/email-styles.php', array(), '', $path );
					$css = apply_filters( 'ywces_email_styles', ob_get_clean() );

				} else {

					wc_get_template( 'emails/email-styles.php' );
					wc_get_template( '/emails/email-styles.php', array(), '', YWCES_TEMPLATE_PATH );
					$css = apply_filters( 'woocommerce_email_styles', ob_get_clean() );

				}

				if ( $emogrifier_class = $this->get_emogrifier_class() ) {

					try {
						$emogrifier = new $emogrifier_class( $content, $css );
						if ( method_exists( $emogrifier, 'disableStyleBlocksParsing' ) ) {
							$emogrifier->disableStyleBlocksParsing();
						}
						$content = $emogrifier->emogrify();
					} catch ( Exception $e ) {
						$logger = wc_get_logger();
						$logger->error( $e->getMessage(), array( 'source' => 'emogrifier' ) );
					}
				} else {
					$content = '<style type="text/css">' . $css . '</style>' . $content;
				}

			}

			return $content;

		}

		public function get_emogrifier_class() {
			$emogrifier_class = false;
			if ( $this->supports_emogrifier() ) {
				if ( ! class_exists( 'Emogrifier' ) || ! class_exists( '\\Pelago\\Emogrifier' ) ) {

					if ( version_compare( WC_VERSION, '4.0.0', '>=' ) ) {
						include_once WC()->plugin_path() . '/vendor/pelago/emogrifier/src/Emogrifier.php';
					} else {
						include_once WC()->plugin_path() . '/includes/libraries/class-emogrifier.php';
					}

				}

				if ( class_exists( 'Emogrifier' ) ) {
					$emogrifier_class = 'Emogrifier';
				} elseif ( class_exists( '\\Pelago\\Emogrifier' ) ) {
					$emogrifier_class = '\\Pelago\\Emogrifier';
				}

			}

			return $emogrifier_class;
		}

		/**
		 * Return if emogrifier library is supported.
		 *
		 * @return bool
		 * @since 3.5.0
		 */
		protected function supports_emogrifier() {
			return class_exists( 'DOMDocument' ) && version_compare( PHP_VERSION, '5.5', '>=' );
		}

		/**
		 * Get HTML content
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_content_html() {

			ob_start();

			wc_get_template( $this->template_html, array(
				'email_heading' => $this->get_heading(),
				'mail_body'     => $this->mail_body,
				'template_type' => $this->template_type,
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this,
			), false, $this->template_base );

			return ob_get_clean();

		}

		/**
		 * Get Plain content
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_content_plain() {

			ob_start();

			wc_get_template( $this->template_plain, array(
				'email_heading' => $this->get_heading(),
				'mail_body'     => $this->mail_body,
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			), false, $this->template_base );

			return ob_get_clean();

		}

		/**
		 * Get email content type.
		 *
		 * @param $default
		 *
		 * @return  string
		 * @since   1.0.9
		 * @author  Alberto Ruggiero
		 */
		public function get_content_type( $default = '' ) {
			switch ( get_option( 'ywces_mail_type' ) ) {
				case 'html' :
					return 'text/html';
				default :
					return 'text/plain';
			}
		}

		/**
		 * Checks if this email is enabled and will be sent.
		 *
		 * @return  bool
		 * @since   1.0.9
		 * @author  Alberto Ruggiero
		 */
		public function is_enabled() {
			return true;
		}

		/**
		 * Initialise Settings Form Fields
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function init_form_fields() {

		}

	}

}

return new YWCES_Coupon_Mail();