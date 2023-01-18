<?php
/**
 * LoginPress reCAPTCHA.
 *
 * @since 1.0.1
 * @version 2.5.2
 */
if ( ! class_exists( 'LoginPress_Recaptcha' ) ) {

	class LoginPress_Recaptcha {

		/**
	  * Variable that Check for LoginPress settings.
	  *
	  * @var string
	  * @since 2.0.1
	  */
	  public $loginpress_settings;

		/* * * * * * * * * *
		* Class constructor
		* * * * * * * * * */
		function __construct() {

			$this->loginpress_settings = get_option( 'loginpress_setting' );
			$this->_hooks();
		}

		/**
		 * _hooks call WP Hook
		 */
		private function _hooks() {

			add_filter( 'loginpress_pro_settings', array( $this, 'loginpress_pro_settings_array' ), 10, 1 );

			$cap_permission = isset( $this->loginpress_settings['enable_repatcha'] ) ? $this->loginpress_settings['enable_repatcha'] : 'off';

			// return from reCaptcha if woocommerce login or registration nonce set.
			if ( isset( $_POST['woocommerce-login-nonce'] ) || isset( $_POST['woocommerce-register-nonce'] ) ) {
				return;
			}

			if ( 'off' == $cap_permission ) {
				return;
			}

			$cap_login    = isset( $this->loginpress_settings['captcha_enable']['login_form'] ) ? $this->loginpress_settings['captcha_enable']['login_form'] : false;

			$cap_lost     = isset( $this->loginpress_settings['captcha_enable']['lostpassword_form'] ) ? $this->loginpress_settings['captcha_enable']['lostpassword_form'] : false;

			$cap_register = isset( $this->loginpress_settings['captcha_enable']['register_form'] ) ? $this->loginpress_settings['captcha_enable']['register_form'] : false;

			/* Add reCAPTCHA on login form */
			if ( $cap_login ) {
				add_action( 'login_form', array( $this, 'loginpress_recaptcha_field' ) );
			}

			/* Add reCAPTCHA on Lost password form */
			if ( $cap_lost ) {
				add_action( 'lostpassword_form', array( $this, 'loginpress_recaptcha_field' ) );
			}

			/* Add reCAPTCHA on registration form */
			if ( $cap_register ) {
				add_action( 'register_form', array( $this, 'loginpress_recaptcha_field' ), 99 );
			}

			/* Authentication reCAPTCHA on login form */
			if ( ! isset( $_GET['customize_changeset_uuid'] ) && $cap_login ) {
				add_filter( 'authenticate', array( $this, 'loginpress_recaptcha_auth' ), 99, 3 );
			}

			/* Authentication reCAPTCHA on lostpassword form */
			if ( ! isset( $_GET['customize_changeset_uuid'] ) && $cap_lost ) {
				add_filter( 'allow_password_reset', array( $this, 'loginpress_recaptcha_lostpassword_auth' ) );
			}

			/**
			 * Authentication reCAPTCHA on registration form && if register action is performed.
			 *
			 * @version 2.5.3
			 */
			if ( ! isset( $_GET['customize_changeset_uuid'] ) && $cap_register ) {
				add_filter( 'registration_errors', array( $this, 'loginpress_recaptcha_registration_auth' ), 10, 3 );
			}

			add_action( 'login_enqueue_scripts', array( $this, 'loginpress_recaptcha_script' ) );
		}

		/**
		 * [loginpress_pro_settings_array Setting Fields for reCAPTCHA.]
		 *
		 * @param  [array] $setting_array [ settings fields of free version ]
		 * @return [array]                [ recaptcha settings fields ]
		 * @version 2.5.0
		 */
		public function loginpress_pro_settings_array( $setting_array ) {

			$_new_settings = array(
				array(
					'name'  => 'force_login',
					'label' => __( 'Force Login', 'loginpress-pro' ),
					'desc'  => __( 'Force user to login before viewing the site?', 'loginpress-pro' ),
					'type'  => 'checkbox',
				),
				array(
					'name'  => 'enable_repatcha',
					'label' => __( 'Enable reCAPTCHA', 'loginpress-pro' ),
					'desc'  => __( 'Enable LoginPress reCaptcha', 'loginpress-pro' ),
					'type'  => 'checkbox',
					// 'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'name'    => 'recaptcha_type',
					'label'   => __( 'Recaptcha type', 'loginpress-pro' ),
					'desc'    => __( 'Select the type of reCAPTCHA', 'loginpress-pro' ),
					'type'    => 'select',
					'default' => 'v2-robot',
					'options' => array(
						'v2-robot'     => __( 'V2 I\'m not robot.', 'loginpress-pro' ),
						'v2-invisible' => __( 'V2 invisible', 'loginpress-pro' ),
						'v3'           => __( 'V3', 'loginpress-pro' ),
					),
				),
				array(
					'name'              => 'site_key',
					'label'             => __( 'Site Key', 'loginpress-pro' ),
					'desc'              => __( 'Get <a href="https://www.google.com/recaptcha/admin" target="_blank"> reCaptcha</a> site key.<br> <span class="alert-note">Make sure you are adding right  site key for this domain. If it\'s incorrect may be you\'r not able to access your website.</span>', 'loginpress-pro' ),
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'name'              => 'secret_key',
					'label'             => __( 'Secret Key', 'loginpress-pro' ),
					'desc'              => __( 'Get <a href="https://www.google.com/recaptcha/admin" target="_blank"> reCaptcha</a> secret key. <br> <span class="alert-note">Make sure you are adding right  secret key for this domain. If it\'s incorrect may be you\'r not able to access your website.</span>', 'loginpress-pro' ),
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'name'              => 'site_key_v2_invisible',
					'label'             => __( 'Site Key', 'loginpress-pro' ),
					'desc'              => __( 'Get <a href="https://www.google.com/recaptcha/admin" target="_blank"> reCaptcha</a> site key.<br> <span class="alert-note">Make sure you are adding right  site key for this domain. If it\'s incorrect may be you\'r not able to access your website.</span>', 'loginpress-pro' ),
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'name'              => 'secret_key_v2_invisible',
					'label'             => __( 'Secret Key', 'loginpress-pro' ),
					'desc'              => __( 'Get <a href="https://www.google.com/recaptcha/admin" target="_blank"> reCaptcha</a> secret key. <br> <span class="alert-note">Make sure you are adding right  secret key for this domain. If it\'s incorrect may be you\'r not able to access your website.</span>', 'loginpress-pro' ),
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'name'              => 'site_key_v3',
					'label'             => __( 'Site Key', 'loginpress-pro' ),
					'desc'              => __( 'Get <a href="https://www.google.com/recaptcha/admin" target="_blank"> reCaptcha</a> site key.<br> <span class="alert-note">Make sure you are adding right  site key for this domain. If it\'s incorrect may be you\'r not able to access your website.</span>', 'loginpress-pro' ),
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'name'              => 'secret_key_v3',
					'label'             => __( 'Secret Key', 'loginpress-pro' ),
					'desc'              => __( 'Get <a href="https://www.google.com/recaptcha/admin" target="_blank"> reCaptcha</a> secret key. <br> <span class="alert-note">Make sure you are adding right  secret key for this domain. If it\'s incorrect may be you\'r not able to access your website.</span>', 'loginpress-pro' ),
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'name'    => 'good_score',
					'label'   => __( 'Select reCaptcha score', 'loginpress-pro' ),
					'desc'    => __( 'Minimum level of score if some one less than it will show error message.', 'loginpress-pro' ),
					'type'    => 'select',
					'default' => '0.5',
					'options' => array(
						'0.1' => '0.1',
						'0.2' => '0.2',
						'0.3' => '0.3',
						'0.4' => '0.4',
						'0.5' => '0.5',
						'0.6' => '0.6',
						'0.7' => '0.7',
						'0.8' => '0.8',
						'0.9' => '0.9',
						'1.0' => '1.0',
					),
				),
				array(
					'name'    => 'captcha_theme',
					'label'   => __( 'Choose theme', 'loginpress-pro' ),
					'desc'    => __( 'Select a theme for reCAPTCHA', 'loginpress-pro' ),
					'type'    => 'select',
					'default' => 'light',
					'options' => array(
						'light' => 'Light',
						'dark'  => 'Dark',
					),
				),
				array(
					'name'    => 'captcha_language',
					'label'   => __( 'Choose language', 'loginpress-pro' ),
					'desc'    => __( 'Select a language for reCAPTCHA', 'loginpress-pro' ),
					'type'    => 'select',
					'default' => 'en',
					'options' => array(
						'ar'     => 'Arabic',
						'af'     => 'Afrikaans',
    				'am'     => 'Amharic',
				    'hy'     => 'Armenian',
				    'az'     => 'Azerbaijani' ,
				    'eu'     => 'Basque',
				    'bn'     => 'Bengali',
						'bg'     => 'Bulgarian',
						'ca'     => 'Catalan',
						'zh-HK'  => 'Chinese (HongKong)',
						'zh-CN'  => 'Chinese (Simplified)',
						'zh-TW'  => 'Chinese (Traditional)',
						'hr'     => 'Croatian',
						'cs'     => 'Czech',
						'da'     => 'Danish',
						'nl'     => 'Dutch',
						'en-GB'  => 'English (UK)',
						'en'     => 'English (US)',
						'fil'    => 'Filipino',
						'fi'     => 'Finnish',
						'fr'     => 'French',
						'fr-CA'  => 'French (Canadian)',
						'gl'     => 'Galician',
    				'ka'     => 'Georgian',
						'de'     => 'German',
						'de-AT'  => 'German (Austria)',
						'de-CH'  => 'German (Switzerland)',
						'el'     => 'Greek',
						'gu' 	   => 'Gujarati',
						'iw'     => 'Hebrew',
						'hi'     => 'Hindi',
						'hu'     => 'Hungarain',
						'is'     => 'Icelandic',
						'id'     => 'Indonesian',
						'it'     => 'Italian',
						'ja'     => 'Japanese',
						'kn'     => 'Kannada',
						'ko'     => 'Korean',
						'lo'     => 'Laothian',
						'lv'     => 'Latvian',
						'lt'     => 'Lithuanian',
						'ms'     => 'Malay',
    				'ml'     => 'Malayalam',
    				'mr'     => 'Marathi',
    				'mn'     => 'Mongolian',
						'no'     => 'Norwegian',
						'fa'     => 'Persian',
						'pl'     => 'Polish',
						'pt'     => 'Portuguese',
						'pt-BR'  => 'Portuguese (Brazil)',
						'pt-PT'  => 'Portuguese (Portugal)',
						'ro'     => 'Romanian',
						'ru'     => 'Russian',
						'sr'     => 'Serbian',
						'si'     => 'Sinhalese',
						'sk'     => 'Slovak',
						'sl'     => 'Slovenian',
						'es'     => 'Spanish',
						'es-419' => 'Spanish (Latin America)',
						'sw'     => 'Swahili',
						'sv'     => 'Swedish',
						'ta'     => 'Tamil',
						'te'     => 'Telugu',
						'th'     => 'Thai',
						'tr'     => 'Turkish',
						'ur'     => 'Urdu',
						'uk'     => 'Ukrainian',
						'ur'		 => 'Urdu',
						'vi'     => 'Vietnamese',
						'zu'		 => 'Zulu',
					),
				),
				array(
					'name'    => 'captcha_enable',
					'label'   => __( 'Enable reCAPTCHA on', 'loginpress-pro' ),
					'desc'    => __( 'Choose the form to enable google recaptcha on that', 'loginpress-pro' ),
					'type'    => 'multicheck',
					'default' => array( 'login_form' => 'login_form' ),
					'options' => array(
						'login_form'        => __( 'Login Form', 'loginpress-pro' ),
						'lostpassword_form' => __( 'Lost Password Form', 'loginpress-pro' ),
						'register_form'     => __( 'Register Form', 'loginpress-pro' ),
					),
				),
			);

			return( array_merge( $_new_settings, $setting_array ) );
		}

		/**
		 * [loginpress_recaptcha_script recaptcha style]
		 *
		 * @since 1.0.1
		 * @version 2.5.0
		 */
		public function loginpress_recaptcha_script() {

			$cap_type      = isset( $this->loginpress_settings['recaptcha_type'] ) ? $this->loginpress_settings['recaptcha_type'] : 'v2-robot';

			$cap_site      = isset( $this->loginpress_settings['site_key'] ) ? $this->loginpress_settings['site_key'] : '';
			$cap_secret    = isset( $this->loginpress_settings['secret_key'] ) ? $this->loginpress_settings['secret_key'] : '';

			$cap_site_v2   = isset( $this->loginpress_settings['site_key_v2_invisible'] ) ? $this->loginpress_settings['site_key_v2_invisible'] : '';
			$cap_secret_v2 = isset( $this->loginpress_settings['secret_key_v2_invisible'] ) ? $this->loginpress_settings['secret_key_v2_invisible'] : '';

			$cap_site_v3   = isset( $this->loginpress_settings['site_key_v3'] ) ? $this->loginpress_settings['site_key_v3'] : '';
			$cap_secret_v3 = isset( $this->loginpress_settings['secret_key_v3'] ) ? $this->loginpress_settings['secret_key_v3'] : '';

			/**
			 * Enqueue Google reCaptcha V2 "I'm not robot" script.
			 * @since 1.0.1
			 */
			if ( 'v2-robot' == $cap_type ) {

				$cap_language    = isset( $this->loginpress_settings['captcha_language'] ) ? $this->loginpress_settings['captcha_language'] : 'en';
				$recaptcha_size  = get_option( 'loginpress_customization' );
				$_recaptcha_size = ! empty( $recaptcha_size['recaptcha_size'] ) ? $recaptcha_size['recaptcha_size'] : 1; ?>

				<script src="https://www.google.com/recaptcha/api.js?hl=<?php echo $cap_language; ?>" async defer></script>
			  <style type="text/css">
				.loginpress_recaptcha_wrapper{
				  text-align: center;
				}
				.loginpress_recaptcha_wrapper .g-recaptcha{
				  display: inline-block;
				  transform-origin: top left;
				  transform: scale(<?php echo $_recaptcha_size; ?>);
				}
				html[dir="rtl"] .g-recaptcha{
				  transform-origin: top right;
				}
			  </style>
				<?php
			}

			/**
			 * Enqueue Google reCaptcha V2 invisible script.
			 * @since 2.5.0
			 */
			if ( 'v2-invisible' == $cap_type ) {

				if ( ! empty( $cap_site_v2 ) && ! empty( $cap_secret_v2 ) ) :
					?>

					<script src="https://www.google.com/recaptcha/api.js?onload=onloadV2Callback&render=explicit" async defer></script>
					<script type="text/javascript">
						var onSubmit = function(token) {
							console.log('success!' + token);
							document.getElementById("loginform").submit();
						};

						var onloadV2Callback = function() {
							grecaptcha.render('wp-submit', {
								'sitekey' : '<?php echo $cap_site_v2; ?>',
								'callback' : onSubmit
							});
						};
					</script>
					<?php
				endif;// check $cap_site_v2 && $cap_secret_v2.
			}

			/**
			 * Enqueue Google reCaptcha V3 script.
			 * @since 2.5.0
			 */
			if ( 'v3' == $cap_type ) {

				if ( ! empty( $cap_site_v3 ) && ! empty( $cap_secret_v3 ) ) :
					?>

					<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $cap_site_v3; ?>"></script>
				  <script>
							grecaptcha.ready(function() {
							grecaptcha.execute('<?php echo $cap_site_v3; ?>', {action: 'loginpage'}).then(function(token) {
								jQuery('#loginform').prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');

								} );
						  } );
					</script>
					<?php
				endif;// check $cap_site_v3 && $cap_secret_v3.
			}
		}

		/**
		 * [loginpress_recaptcha_field Google reCaptcha Callback]
		 *
		 * @return [string] [recaptcha layout]
		 * @version 2.1.2
		 */
		public function loginpress_recaptcha_field() {

			global $recaptcha;

			$cap_site            = isset( $this->loginpress_settings['site_key'] ) ? $this->loginpress_settings['site_key'] : '';
			$cap_secret          = isset( $this->loginpress_settings['secret_key'] ) ? $this->loginpress_settings['secret_key'] : '';
      $cap_type            = isset( $this->loginpress_settings['recaptcha_type'] ) ? $this->loginpress_settings['recaptcha_type'] : 'v2-robot';

			if ( 'v2-robot' == $cap_type ) {
				$cap_theme       = isset( $this->loginpress_settings['captcha_theme'] ) ? $this->loginpress_settings['captcha_theme'] : 'light';
				$captcha_preview = '';
				if ( ! empty( $cap_site ) && ! empty( $cap_secret ) ) :
					$captcha_preview .= '<div class="loginpress_recaptcha_wrapper">';
					$captcha_preview .= '<div class="g-recaptcha" data-sitekey="' . htmlentities( trim( $cap_site ) ) . '" data-theme="' . $cap_theme . '"></div>';
					$captcha_preview .= '</div>';
				endif; // check $cap_site && $cap_secret.

				echo $captcha_preview;
			}

		}

		/**
		 * reCAPTCHA Login Authentication.
		 *
		 * @param  [object] $user
		 * @param  [string] $username
		 * @param  [string] $password
		 *
		 * @version 2.1.2
		 */
		function loginpress_recaptcha_auth( $user, $username, $password ) {

      $cap_type = isset( $this->loginpress_settings['recaptcha_type'] ) ? $this->loginpress_settings['recaptcha_type'] : 'v2-robot';

			if ( isset( $_POST['g-recaptcha-response'] ) ) {

				if( 'v3' == $cap_type ){
					$good_score = $this->loginpress_settings['good_score'];
					$score = $this->loginpress_v3_recaptcha_verifier();
					if ( $score < $good_score ) {
						return new WP_Error( 'recaptcha_error', $this->loginpress_recaptcha_error() );
					}
				} else {
					$response = $this->loginpress_recaptcha_verifier();
					if ( ! $response->isSuccess() ) {
						return new WP_Error( 'recaptcha_error', $this->loginpress_recaptcha_error() );
					}
				}
			}
			return $user;
		}

		/**
		 * Google reCaptcha V2 server side verification.
		 *
		 * @since 2.1.2
		 * @version 2.5.0
		 */
		function loginpress_recaptcha_verifier() {

			$cap_type = isset( $this->loginpress_settings['recaptcha_type'] ) ? $this->loginpress_settings['recaptcha_type'] : 'v2-robot';

			if ( 'v2-invisible' == $cap_type ) {
				$secret = isset( $this->loginpress_settings['secret_key_v2_invisible'] ) ? $this->loginpress_settings['secret_key_v2_invisible'] : false;
			} else {
				$secret = isset( $this->loginpress_settings['secret_key'] ) ? $this->loginpress_settings['secret_key'] : false;
			}

			include LOGINPRESS_PRO_ROOT_PATH . '/lib/recaptcha/src/autoload.php';

			if( ini_get('allow_url_fopen') ) {
				$reCaptcha = new \ReCaptcha\ReCaptcha( $secret );
			} else {
				$reCaptcha = new \ReCaptcha\ReCaptcha( $secret , new \ReCaptcha\RequestMethod\CurlPost() );
			}

			$response  = $reCaptcha->verify( wp_unslash( $_POST['g-recaptcha-response'] ), $this->loginpress_get_remote_ip() );

			return $response;
		}

		/**
		 * Google reCaptcha V3 server side verification.
		 *
		 * @since 2.1.2
		 * @version 2.5.0
		 */
		function loginpress_v3_recaptcha_verifier() {

			if ( isset( $_POST['g-recaptcha-response'] ) ) {

				$v3_secret = isset( $this->loginpress_settings['secret_key_v3'] ) ? $this->loginpress_settings['secret_key_v3'] : false;

				// Build POST request:
				$recaptcha_url      = 'https://www.google.com/recaptcha/api/siteverify';
				$recaptcha_response = $_POST['g-recaptcha-response'];

				// Make and decode POST request:
				$recaptcha = file_get_contents( $recaptcha_url . '?secret=' . $v3_secret . '&response=' . $recaptcha_response );
				$response  = json_decode( $recaptcha );

				// Take action based on the score returned:
				if ( isset( $response->score ) && $response->score ) {
					return $response->score;
				}
			}
			// otherwise, let the spammer think that they got their message through
			return 0;
		}



		/**
		 * [loginpress_recaptcha_lostpassword_auth reCAPTCHA Lost Password Authentication.]
		 *
		 * @param  [array] $errors
		 * @return [array] $errors
		 *
		 * @version 2.1.2
		 */
		function loginpress_recaptcha_lostpassword_auth( $errors ) {

      $cap_type = isset( $this->loginpress_settings['recaptcha_type'] ) ? $this->loginpress_settings['recaptcha_type'] : 'v2-robot';

			if ( isset( $_POST['g-recaptcha-response'] ) ) {

				if ( 'v3' == $cap_type ) {

					$good_score = $this->loginpress_settings['good_score'];
					$score      = $this->loginpress_v3_recaptcha_verifier();
					if ( $score < $good_score ) {
						return new WP_Error( 'recaptcha_error', $this->loginpress_recaptcha_error() );
					}
				} else {

					$response = $this->loginpress_recaptcha_verifier();
					if ( ! $response->isSuccess() ) {

						return new WP_Error( 'recaptcha_error', $this->loginpress_recaptcha_error() );
					}
				}
			}

			return $errors;
		}


		/**
		 * [loginpress_recaptcha_registration_auth reCAPTCHA Registration Authentication.]
		 *
		 * @param  [array]  $errors
		 * @param  [string] $sanitized_user_login
		 * @param  [string] $user_email
		 * @return [array] $errors
		 *
		 * @version 2.1.2
		 */
		public function loginpress_recaptcha_registration_auth( $errors, $sanitized_user_login, $user_email ) {

      $cap_type = isset( $this->loginpress_settings['recaptcha_type'] ) ? $this->loginpress_settings['recaptcha_type'] : 'v2-robot';

			if ( isset( $_POST['g-recaptcha-response'] ) ) {

				if ( 'v3' == $cap_type ) {

					$good_score = $this->loginpress_settings['good_score'];
					$score      = $this->loginpress_v3_recaptcha_verifier();
					if ( $score < $good_score ) {
						return new WP_Error( 'recaptcha_error', $this->loginpress_recaptcha_error() );
					}
				} else {
					$response = $this->loginpress_recaptcha_verifier();
					if ( ! $response->isSuccess() ) {
						return new WP_Error( 'recaptcha_error', $this->loginpress_recaptcha_error() );
					}
				}
			}
			return $errors;
		}

		/**
		 * [loginpress_get_remote_ip]
		 *
		 * @return [string] [remote address]
		 */
		public function loginpress_get_remote_ip() {

			return $_SERVER['REMOTE_ADDR'];
		}

		/**
		 * [loginpress_recaptcha_error recaptcha error message]
		 *
		 * @return [string] [Custom error message]
		 * @version 2.1.2
		 */
		public function loginpress_recaptcha_error() {

			$loginpress_settings = get_option( 'loginpress_customization' );
			$recaptcha_message   = isset( $loginpress_settings['recaptcha_error_message'] ) ? $loginpress_settings['recaptcha_error_message'] : __( '<strong>ERROR:</strong> Please verify reCAPTCHA', 'loginpress-pro' );

			$allowed_html = array(
				'a'      => array(),
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
				'i'      => array(),
			);
			return wp_kses( $recaptcha_message, $allowed_html );
		}
	}
}
?>
