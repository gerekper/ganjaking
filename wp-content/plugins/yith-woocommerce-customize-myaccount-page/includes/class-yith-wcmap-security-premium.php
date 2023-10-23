<?php
/**
 * Security class premium
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 3.12.0
 */

defined( 'YITH_WCMAP' ) || exit;  // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP_Security_Premium', false ) ) {
	/**
	 * Security class premium.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 2.5.0
	 */
	class YITH_WCMAP_Security_Premium extends YITH_WCMAP_Security {

		/**
		 * The reCaptcha public key
		 *
		 * @since 2.5.0
		 * @var string
		 */
		private $recaptcha_public = '';

		/**
		 * The reCaptcha private key
		 *
		 * @since 2.5.0
		 * @var string
		 */
		private $recaptcha_private = '';

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  2.5.0
		 */
		public function __construct() {
			parent::__construct();

			add_action( 'init', array( $this, 'handle_recaptcha' ) );
			add_filter( 'woocommerce_registration_errors', array( $this, 'handle_email_domain_error' ), 15, 3 );
		}

		/**
		 * Handle recaptcha actions
		 *
		 * @since  2.5.7
		 * @return void
		 */
		public function handle_recaptcha() {
			if ( $this->enabled_recaptcha() ) {
				add_action( 'wp_head', array( $this, 'add_recaptcha_script' ), 100 );
				add_action( 'woocommerce_register_form', array( $this, 'add_recaptcha_form' ) );
				add_filter( 'woocommerce_registration_errors', array( $this, 'handle_recaptcha_error' ), 10, 3 );
			}
		}

		/**
		 * Check if recaptcha is enabled
		 *
		 * @since  2.5.0
		 * @return boolean
		 */
		public function enabled_recaptcha() {

			$enable_recaptcha = true;

			if ( is_user_logged_in() || 'yes' !== get_option( 'yith-wcmap-enable-recaptcha', 'no' ) ) {
				$enable_recaptcha = false;
			} else {
				$this->recaptcha_public  = get_option( 'yith-wcmap-public-recaptcha', '' );
				$this->recaptcha_private = get_option( 'yith-wcmap-private-recaptcha', '' );

				if ( ! $this->recaptcha_public || ! $this->recaptcha_private ) {
					$enable_recaptcha = false;
				}
			}

			/**
			 * APPLY_FILTERS: yith_wcmap_enabled_recaptcha
			 *
			 * Filters whether reCaptcha is enabled.
			 *
			 * @param bool $enable_recaptcha Whether reCaptcha is enabled or not.
			 *
			 * @return bool
			 */
			return apply_filters( 'yith_wcmap_enabled_recaptcha', $enable_recaptcha );
		}

		/**
		 * Validate reCaptcha
		 *
		 * @since  2.5.0
		 * @param string $response The recaptcha response.
		 * @return boolean
		 */
		protected function is_recaptcha_valid( $response ) {
			// phpcs:disable
			if ( ! $response || ! $this->recaptcha_private ) {
				return false;
			}

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify' );
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt(
				$ch,
				CURLOPT_POSTFIELDS,
				array(
					'secret'   => $this->recaptcha_private,
					'response' => $response,
				)
			);
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			$output = curl_exec( $ch );

			curl_close( $ch );

			$output = json_decode( $output );

			return ! empty( $output->success ) ? $output->success : false;
			// phpcs:enable
		}

		/**
		 * Add reCaptcha script to site head if is my account
		 *
		 * @since  2.5.0
		 */
		public function add_recaptcha_script() {
			$my_current_lang = apply_filters( 'wpml_current_language', null );
			?>
			<script src="https://www.google.com/recaptcha/api.js?hl=<?php echo esc_attr( $my_current_lang ); ?>" async defer></script>
			<?php
		}

		/**
		 * Add reCaptcha block to WooCommerce registration form
		 *
		 * @since  2.5.0
		 */
		public function add_recaptcha_form() {
			if ( ! is_page( wc_get_page_id( 'myaccount' ) ) ) {
				return;
			}
			?>
			<input type="hidden" name="ywcmap-recaptcha" value="yes">
			<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $this->recaptcha_public ); ?>"></div>
			<?php
		}

		/**
		 * Handle registration recaptcha error
		 *
		 * @since  2.5.0
		 * @param WP_Error $errors   Registration errors.
		 * @param string   $username The customer username.
		 * @param string   $email    The customer email.
		 * @return WP_Error
		 */
		public function handle_recaptcha_error( $errors, $username, $email ) {
			if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) && ! empty( $_REQUEST['ywcmap-recaptcha'] ) && ( empty( $_REQUEST['g-recaptcha-response'] ) || ! $this->is_recaptcha_valid( sanitize_text_field( wp_unslash( $_REQUEST['g-recaptcha-response'] ) ) ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$errors->add( 'registration-error-invalid-recaptcha', __( 'ReCaptcha validation error. Please try again.', 'yith-woocommerce-customize-myaccount-page' ) );
			}

			return $errors;
		}

		/**
		 * Handle email domain registration error
		 *
		 * @since  2.5.0
		 * @param WP_Error $errors   Registration errors.
		 * @param string   $username The customer username.
		 * @param string   $email    The customer email.
		 * @return WP_Error
		 */
		public function handle_email_domain_error( $errors, $username, $email ) {
			$blocked_domains = get_option( 'yith-wcmap-email-domain-blocked', '' );
			if ( empty( $blocked_domains ) ) {
				return $errors;
			}

			$blocked_domains        = explode( ',', $blocked_domains );
			$blocked_domains        = array_map( 'trim', $blocked_domains );
			list( $local, $domain ) = explode( '@', $email, 2 );

			if ( ! empty( $blocked_domains ) && in_array( $domain, $blocked_domains, true ) ) {
				$errors->add( 'registration-error-invalid-email', __( 'Email domain is not valid! Please use a different one and try again.', 'yith-woocommerce-customize-myaccount-page' ) );
			}

			return $errors;
		}
	}
}
