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

if ( ! class_exists( 'YWRR_Request_Mail' ) ) {

	/**
	 * Implements Request Mail for YWRR plugin
	 *
	 * @class   YWRR_Request_Mail
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 * @extends WC_Email
	 *
	 * @package Yithemes
	 */
	class YWRR_Request_Mail extends WC_Email {

		/**
		 * @var integer $days_ago number of days after order completion
		 */
		var $days_ago;

		/**
		 * @var array $item_list list of item to review
		 */
		var $item_list;

		/**
		 * @var string $review_list processed list of items in HTML or Plain mode
		 */
		var $review_list;

		/**
		 * @var integer $template the template of the email
		 */
		var $template_type;

		/**
		 * @var string $lang the language of the email
		 */
		var $lang;

		/**
		 * @var string $lang the language of the email
		 */
		var $mail_body;

		/**
		 * Constructor
		 *
		 * Initialize email type and set templates paths
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->title          = esc_html__( 'Review Reminder', 'yith-woocommerce-review-reminder' );
			$this->id             = 'yith-review-reminder';
			$this->description    = esc_html__( 'Send a review reminder to the customers over WooCommerce.', 'yith-woocommerce-review-reminder' );
			$this->customer_email = true;
			$this->template_base  = YWRR_TEMPLATE_PATH;
			$this->template_html  = 'emails/review-request.php';
			$this->template_plain = 'emails/plain/review-request.php';

			global $woocommerce_wpml;

			$is_wpml_configured = apply_filters( 'wpml_setting', false, 'setup_complete' );
			if ( $is_wpml_configured && defined( 'WCML_VERSION' ) && $woocommerce_wpml ) {
				add_filter( 'send_ywrr_mail_notification', array( $this, 'refresh_email_lang' ), 10, 1 );
				add_filter( 'wcml_send_email_order_id', array( $this, 'send_email_order_id' ), 10, 1 );
			}

			add_filter( 'send_ywrr_mail_notification', array( $this, 'trigger' ), 15, 1 );

			parent::__construct();
			$this->plugin_id = '';

		}

		/**
		 * Get order id
		 *
		 * @param   $order_id integer
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function send_email_order_id( $order_id ) {

			if ( isset( $_REQUEST['order_id'] ) ) {
				$order_id = $_REQUEST['order_id'];
			}

			return $order_id;

		}

		/**
		 * Refresh email language
		 *
		 * @param   $args array
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function refresh_email_lang( $args ) {

			if ( isset( $args['order_id'] ) ) {
				$order_id = $args['order_id'];
			} else {
				return $args;
			}

			if ( $order_id ) {

				$order = wc_get_order( $order_id );
				$lang  = $order->get_meta( 'wpml_language' );

				if ( ! empty( $lang ) ) {

					global $sitepress;

					$sitepress->switch_lang( $lang, true );

				}

			}

			return $args;

		}

		/**
		 * Trigger email send
		 *
		 * @param   $args array
		 *
		 * @return  boolean
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function trigger( $args ) {

			$this->object                     = ( $args['order_id'] ) ? wc_get_order( $args['order_id'] ) : 0;
			$this->lang                       = ( $args['order_id'] ) ? $this->object->get_meta( 'wpml_language' ) : '';
			$this->heading                    = apply_filters( 'wpml_translate_single_string', $this->get_subject(), 'admin_texts_ywrr_mail_subject', 'ywrr_mail_subject', $this->lang );
			$this->subject                    = apply_filters( 'wpml_translate_single_string', $this->get_heading(), 'admin_texts_ywrr_mail_subject', 'ywrr_mail_subject', $this->lang );
			$this->days_ago                   = $args['days_ago'];
			$this->item_list                  = $args['item_list'];
			$this->template_type              = ( ! $args['template'] ) ? get_option( 'ywrr_mail_template' ) : $args['template'];
			$this->placeholders['{order_id}'] = $args['order_id'];
			$this->recipient                  = ( $args['order_id'] ) ? $this->object->get_billing_email() : $args['test_email'];
			$this->mail_body                  = ( $args['type'] == 'booking' ) ? $this->get_option( 'ywrr_mail_body_booking' ) : $this->get_option( 'ywrr_mail_body' );

			if ( ! $this->get_recipient() ) {
				return false;
			}

			return $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

		}

		/**
		 * Apply inline styles to dynamic content.
		 *
		 * @param string|null $content
		 *
		 * @return  string
		 * @since   1.1.8
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function style_inline( $content ) {

			// make sure we only inline CSS for html emails
			if ( in_array( $this->get_content_type(), array( 'text/html', 'multipart/alternative' ) ) && class_exists( 'DOMDocument' ) ) {

				ob_start();

				$templates = ywrr_get_templates();

				if ( in_array( $this->template_type, $templates ) ) {
					wc_get_template( 'emails/' . $this->template_type . '/email-styles.php', array(), false, YWRR_TEMPLATE_PATH );
				} else {
					wc_get_template( 'emails/email-styles.php' );
				}

				$css = apply_filters( 'woocommerce_email_styles', ob_get_clean(), $this );

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

		/**
		 * Get emogrifier class.
		 *
		 * @return  boolean
		 * @since   1.4.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
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
		 * @return  boolean
		 * @since   1.4.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		protected function supports_emogrifier() {
			return class_exists( 'DOMDocument' ) && version_compare( PHP_VERSION, '5.5', '>=' );
		}

		/**
		 * Get HTML content
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_content_html() {
			ob_start();
			wc_get_template( $this->template_html, array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'mail_body'     => $this->mail_body,
				'days_ago'      => $this->days_ago,
				'item_list'     => $this->item_list,
				'review_list'   => $this->review_list,
				'template_type' => $this->template_type,
				'lang'          => $this->lang,
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
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_content_plain() {
			ob_start();
			wc_get_template( $this->template_plain, array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'mail_body'     => $this->mail_body,
				'days_ago'      => $this->days_ago,
				'item_list'     => $this->item_list,
				'review_list'   => $this->review_list,
				'lang'          => $this->lang,
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			), false, $this->template_base );

			return ob_get_clean();
		}

		/**
		 * Checks if this email is enabled and will be sent.
		 *
		 * @return  boolean
		 * @since   1.1.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function is_enabled() {
			return true;
		}

		/**
		 * Get email type.
		 *
		 * @return  string
		 * @since   1.6.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_email_type() {
			return $this->get_option( 'ywrr_mail_type' );
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
		 * @since   1.6.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_subject() {
			return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $this->get_option( 'ywrr_mail_subject', $this->get_default_subject() ) ), $this->object );
		}

		/**
		 * Get email heading.
		 *
		 * @return  string
		 * @since   1.6.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_heading() {
			return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->get_option( 'ywrr_mail_subject', $this->get_default_subject() ) ), $this->object );
		}

		/**
		 * Initialise Settings Form Fields
		 *
		 * @return  void
		 * @since   1.6.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init_form_fields() {

			if ( ! function_exists( 'ywrr_mail_options' ) ) {
				include( YWRR_DIR . 'includes/ywrr-functions.php' );
			}

			$this->form_fields = ywrr_mail_options( true );

		}

	}

}

return new YWRR_Request_Mail();