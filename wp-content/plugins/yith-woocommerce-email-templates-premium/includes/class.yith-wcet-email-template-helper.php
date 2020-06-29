<?php
/**
 * Email Template Helper
 *
 * @author  Yithemes
 * @package YITH WooCommerce Email Templates
 * @version 1.2.0
 */

defined( 'YITH_WCET' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCET_Email_Template_Helper' ) ) {
	/**
	 * YITH_WCET_Email_Template_Helper class.
	 * The class manage all the admin behaviors.
	 *
	 * @since    1.2.0
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCET_Email_Template_Helper {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCET_Email_Template_Helper
		 * @since 1.2.0
		 */
		protected static $instance;

		public $templates;

		public $current_email;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCET_Email_Template_Helper || YITH_WCET_Email_Template_Helper_Premium
		 * @since                                   1.2.0
		 */
		public static function get_instance() {
			$self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

			if ( is_null( $self::$instance ) ) {
				$self::$instance = new $self;
			}

			return $self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 */
		public function __construct() {
			$this->_init_templates();

			add_filter( 'wc_get_template', array( $this, 'custom_template' ), 999, 5 );

			add_action( 'woocommerce_email', array( $this, 'woocommerce_email' ) );

			add_filter( 'woocommerce_email_styles', array( $this, 'email_styles' ), 999 );
			add_filter( 'woocommerce_mail_content', array( $this, 'mail_content_styling' ) );

			add_action( 'admin_init', array( $this, 'preview_emails' ) );
		}

		/**
		 * change woocommerce_email_header with Email Templates Header
		 *
		 * @param WC_Emails $mailer
		 */
		public function woocommerce_email( $mailer ) {
			if ( $priority = has_action( 'woocommerce_email_header', array( $mailer, 'email_header' ) ) ) {
				remove_action( 'woocommerce_email_header', array( $mailer, 'email_header' ), $priority );
			}

			if ( $priority = has_action( 'woocommerce_email_footer', array( $mailer, 'email_footer' ) ) ) {
				remove_action( 'woocommerce_email_footer', array( $mailer, 'email_footer' ), $priority );
			}

			add_action( 'woocommerce_email_header', array( $this, 'email_header' ), 10, 2 );
			add_action( 'woocommerce_email_footer', array( $this, 'email_footer' ), 10, 2 );
		}

		protected function _init_templates() {
			$templates = array(
				'emails/email-footer.php',
				'emails/email-header.php',
				'emails/email-styles.php',
			);

			$this->templates = apply_filters( 'yith_wcet_templates', $templates );
		}

		/**
		 * get the default template path for email templates
		 *
		 * @return string
		 * @since 1.3.0
		 */
		public function get_default_template_email_path() {
			return YITH_WCET_TEMPLATE_EMAIL_PATH;
		}

		/**
		 * get the nearest template path for email templates based on WC version
		 *
		 * @return string
		 * @since 1.3.0
		 * @deprecated since 1.3.24 | use YITH_WCET_Email_Template_Helper::locate_template_in_plugin instead
		 */
		public function get_nearest_template_email_path() {
			return $this->get_default_template_email_path();
		}

		/**
		 * locate the template in the plugin based on WC version
		 *
		 * @param $template
		 *
		 * @return string
		 * @since 1.3.0
		 */
		public function locate_template_in_plugin( $template ) {
			$path               = $this->get_default_template_email_path() . '/' . $template;
			$wc_version         = WC()->version;
			$available_versions = array(
				'2.5',
				'3.0',
				'3.2',
				'3.4',
				'3.5',
				'3.7',
			);
			rsort( $available_versions );
			foreach ( $available_versions as $_version ) {
				if ( version_compare( $wc_version, $_version, '>=' ) ) {
					$new_path = YITH_WCET_TEMPLATE_PATH . '/emails/woocommerce' . $_version . '/' . $template;
					if ( file_exists( $new_path ) ) {
						$path = $new_path;
						break;
					}
				}
			}

			return $path;
		}

		/**
		 * Custom Template
		 * Filters wc_get_template for custom templates
		 *
		 * @return string
		 * @access   public
		 * @since    1.0.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function custom_template( $located, $template_name, $args, $template_path, $default_path ) {
			if ( in_array( $template_name, $this->templates ) ) {

				return $this->locate_template_in_plugin( $template_name );
			}

			return $located;
		}

		/**
		 * Woocommerce Email Styles
		 *
		 * @access   public
		 * @since    1.0.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function email_styles( $style ) {
			return '';
		}

		public function email_header( $email_heading, $email = '' ) {
			global $current_email;

			if ( empty( $current_email ) || ! ! $email ) {
				$current_email = $email;
			}

			wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading, 'email' => $current_email ) );
		}

		public function email_footer( $email = '', $args = array() ) {
			wc_get_template( 'emails/email-footer.php', array( 'args' => $args, 'email' => $email ) );
		}

		/**
		 * Mail Content Styling
		 * This func transforms css style of the mail in inline style; and return the content with the inline style
		 *
		 * @return string
		 * @access   public
		 * @since    1.0.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function mail_content_styling( $content ) {
			// get CSS styles
			ob_start();
			wc_get_template( 'emails/email-styles.php' );
			$css = ob_get_clean();

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

			$content = str_replace( 'yith-wccet-inline-style', 'style', $content ); // left for backward compatibility
			$content = str_replace( 'yith-wccet-style', 'style', $content );

			return $content;
		}

		/**
		 * Return if emogrifier library is supported.
		 *
		 * @return bool
		 * @since 1.3.11
		 */
		protected function supports_emogrifier() {
			return class_exists( 'DOMDocument' );
		}

		/**
		 * Return if emogrifier library is supported.
		 *
		 * @return string|bool
		 * @since 1.3.16
		 */
		protected function get_emogrifier_class() {
			$emogrifier_class = false;
			if ( $this->supports_emogrifier() ) {
				if ( ! class_exists( 'Emogrifier' ) && ! class_exists( '\\Pelago\\Emogrifier' ) ) {
					$paths = array(
						WC()->plugin_path() . '/includes/libraries/class-emogrifier.php',       // WC < 4.0
						WC()->plugin_path() . '/vendor/pelago/emogrifier/src/Emogrifier.php',   // WC >= 4.0
					);

					foreach ( $paths as $path ) {
						if ( file_exists( $path ) ) {
							include_once $path;
							break;
						}
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
		 * Preview email template
		 *
		 * @return string
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function preview_emails() {
			if ( isset( $_REQUEST['yith_wcet_preview_mail'] ) ) {

				if ( isset( $_REQUEST['template_id'] ) ) {
					global $current_email;
					$current_email = 'preview';
					$template_id   = $_REQUEST['template_id'];

					// load the mailer class
					$mailer = WC()->mailer();

					// get the preview email subject
					$email_heading = __( 'HTML Email Template', 'woocommerce' );

					// get the preview email content
					ob_start();
					wc_get_template( '/views/html-email-template-preview.php', array( 'template_id' => $template_id ), YITH_WCET_TEMPLATE_PATH, YITH_WCET_TEMPLATE_PATH );
					$message = ob_get_clean();

					// wrap the content with the email template and then add styles
					$message = $this->mail_content_styling( $this->wrap_message( $email_heading, $message, $current_email ) );

					// print the preview email
					echo $message;
					exit;
				}
			}
		}

		/**
		 * Wraps a message in the woocommerce mail template.
		 *
		 * @param mixed           $email_heading
		 * @param string          $message
		 * @param WC_Email|string $email
		 *
		 * @return string
		 */
		public function wrap_message( $email_heading, $message, $email = '' ) {
			// Buffer
			ob_start();

			do_action( 'woocommerce_email_header', $email_heading, $email );

			echo wpautop( wptexturize( $message ) );

			do_action( 'woocommerce_email_footer', $email );

			// Get contents
			$message = ob_get_clean();

			return $message;
		}

	}
}

/**
 * Unique access to instance of YITH_WCET_Email_Template_Helper class
 *
 * @return YITH_WCET_Email_Template_Helper | YITH_WCET_Email_Template_Helper_Premium
 * @since 1.2.0
 */
function YITH_WCET_Email_Template_Helper() {
	return YITH_WCET_Email_Template_Helper::get_instance();
}
