<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    coupon-referral-program
 * @subpackage coupon-referral-program/includes
 */

/**
 * The Onboarding-specific functionality of the plugin admin side.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    coupon-referral-program
 * @subpackage coupon-referral-program/includes
 * @author     makewebbetter <ticket@makewebbetter.com>
 */
if ( class_exists( 'Makewebbetter_Onboarding_Helper' ) ) {
	return;
}

/**
 * Helper module for MakeWebBetter plugins.
 */
class Makewebbetter_Onboarding_Helper {

	/**
	 * The single instance of the class.
	 *
	 * @since   1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Base url of hubspot api.
	 *
	 * @since 1.0.0
	 * @var string base url of API.
	 */
	private $base_url = 'https://api.hsforms.com/';

	/**
	 * Portal id of hubspot api.
	 *
	 * @since 1.0.0
	 * @var string Portal id.
	 */
	private static $portal_id = '6493626';

	/**
	 * Form id of hubspot api.
	 *
	 * @since 1.0.0
	 * @var string Form id.
	 */
	private static $onboarding_form_id = 'd94dcb10-c9c1-4155-a9ad-35354f2c3b52';
	private static $deactivation_form_id = '329ffc7a-0e8c-4e11-8b41-960815c31f8d';


	/**
	 * Plugin Name.
	 *
	 * @since 1.0.0
	 */
	
	private static $plugin_name;
	private static $store_name;
	private static $store_url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		self::$store_name = get_bloginfo( 'name' );
		self::$store_url = home_url();

		if ( defined( 'ONBOARD_PLUGIN_NAME' ) ) {
			self::$plugin_name = ONBOARD_PLUGIN_NAME;
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		add_action( 'admin_footer', array( $this, 'add_deactivation_popup_screen' ) );
		add_filter( 'mwb_deactivation_form_fields', array( $this, 'add_deactivation_form_fields' ) );

		// Ajax to send data.
		add_action( 'wp_ajax_send_onboarding_data', array( $this, 'send_onboarding_data' ) );
		add_action( 'wp_ajax_nopriv_send_onboarding_data', array( $this, 'send_onboarding_data' ) );

		// Ajax to Skip popup.
		add_action( 'wp_ajax_skip_onboarding_popup', array( $this, 'skip_onboarding_popup' ) );
		add_action( 'wp_ajax_nopriv_skip_onboarding_popup', array( $this, 'skip_onboarding_popup' ) );
	}

	/**
	 * Main HubWooConnectionMananager Instance.
	 *
	 * Ensures only one instance of HubWooConnectionMananager is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return HubWooConnectionMananager - Main instance.
	 */
	public static function get_instance() {

		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Makewebbetter_Onboarding_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Makewebbetter_Onboarding_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( $this->is_valid_page_screen() ) {

			wp_enqueue_style( 'makewebbetter-onboarding-style', COUPON_REFERRAL_PROGRAM_DIR_URL . 'admin/css/makewebbetter-onboarding-admin.css', array(), '1.0.0', 'all' );
			wp_enqueue_style( 'select2' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Makewebbetter_Onboarding_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Makewebbetter_Onboarding_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( $this->is_valid_page_screen() ) {

			wp_enqueue_script( 'makewebbetter-onboarding-scripts', COUPON_REFERRAL_PROGRAM_DIR_URL . 'admin/js/makewebbetter-onboarding-admin.js', array( 'jquery', 'select2' ), '1.0.0', true );

			global $pagenow;
			$current_slug = ! empty( explode( '/', plugin_basename( __FILE__ ) ) ) ? explode( '/', plugin_basename( __FILE__ ) )[0] : '';
			wp_localize_script(
				'makewebbetter-onboarding-scripts',
				'mwb',
				array(
					'ajaxurl'       => admin_url( 'admin-ajax.php' ),
					'auth_nonce'    => wp_create_nonce( 'mwb_onboarding_nonce' ),
					'current_screen'    => $pagenow,
					'current_supported_slug'    => apply_filters( 'mwb_deactivation_supported_slug', array() ),
				)
			);
		}
	}


	/**
	 * Get all valid screens to add scripts and templates.
	 *
	 * @since    1.0.0
	 */
	public function add_deactivation_popup_screen() {

		global $pagenow;
		if ( ! empty( $pagenow ) && 'plugins.php' == $pagenow ) {
			require_once COUPON_REFERRAL_PROGRAM_DIR_PATH . 'includes/extra-templates/makewebbetter-deactivation-template-display.php';
		}
	}


	/**
	 * Validate current screen.
	 *
	 * @since    1.0.0
	 */
	public function is_valid_page_screen() {

		global $pagenow;
		$screen = get_current_screen();

		$is_valid = false;

		if ( ! empty( $screen->id ) ) {

			$is_valid = in_array( $screen->id, apply_filters( 'mwb_helper_valid_frontend_screens', array() ) ) && $this->add_mwb_additional_validation();
		}

		if ( empty( $is_valid ) && 'plugins.php' == $pagenow ) {
			$is_valid = true;
		}

		return $is_valid;
	}

	/**
	 * Validate the popup to be shown on screen.
	 *
	 * @since    1.0.0
	 */
	public function can_show_onboarding_popup() {

		$is_already_sent = get_option( 'onboarding-data-sent', false );

		// Already submitted the data.
		if ( ! empty( $is_already_sent ) && 'sent' == $is_already_sent ) {
			return false;
		}

		$get_skipped_timstamp = get_option( 'onboarding-data-skipped', false );

		if ( ! empty( $get_skipped_timstamp ) ) {

			$next_show = strtotime( '+2 days', $get_skipped_timstamp );

			$current_time = time();

			$time_diff = $next_show - $current_time;

			if ( 0 < $time_diff ) {

				return false;
			}
		}

		// By default Show.
		return true;
	}


	/**
	 * Add your deactivation form fields.
	 *
	 * @since    1.0.0
	 */
	public function add_deactivation_form_fields() {

		$current_user = wp_get_current_user();
		if ( ! empty( $current_user ) ) {
			$current_user_email = $current_user->user_email ? $current_user->user_email : '';
		}

		$store_name = get_bloginfo( 'name' );
		$store_url = get_home_url();

		/**
		 * Do not repeat id index.
		 */

		$fields = array(

			/**
			 * Input field with label.
			 * Radio field with label ( select only one ).
			 * Radio field with label ( select multiple one ).
			 * Checkbox radio with label ( select only one ).
			 * Checkbox field with label ( select multiple one ).
			 * Only Label ( select multiple one ).
			 * Select field with label ( select only one ).
			 * Select2 field with label ( select multiple one ).
			 * Email field with label. ( auto filled with admin email )
			 */

			rand() => array(
				'id' => 'deactivation-reason',
				'label' => '',
				'type' => 'radio',
				'name' => 'plugin_deactivation_reason',
				'value' => '',
				'multiple' => 'no',
				'required' => 'yes',
				'extra-class' => '',
				'options' => array(
					'temporary-deactivation-for-debug'      => __( 'It is a temporary deactivation. I am just debugging an issue.', 'coupon-referral-program' ),
					'site-layout-broke'         => __( 'The plugin broke my layout or some functionality.', 'coupon-referral-program' ),
					'complicated-configuration'         => __( 'The plugin is too complicated to configure.', 'coupon-referral-program' ),
					'no-longer-need'        => __( 'I no longer need the plugin', 'coupon-referral-program' ),
					'found-better-plugin'       => __( 'I found a better plugin', 'coupon-referral-program' ),
					'other'         => __( 'Other', 'coupon-referral-program' ),
				),
			),

			rand() => array(
				'id' => 'deactivation-reason-text',
				'label' => 'Let us know why you are deactivating {plugin-name} so we can improve the plugin',
				'type' => 'textarea',
				'name' => 'deactivation_reason_text',
				'value' => '',
				'required' => '',
				'extra-class' => 'mwb-keep-hidden',
			),

			rand() => array(
				'id' => 'admin-email',
				'label' => '',
				'type' => 'hidden',
				'name' => 'email',
				'value' => $current_user_email,
				'required' => '',
				'extra-class' => '',
			),

			rand() => array(
				'id' => 'store-name',
				'label' => '',
				'type' => 'hidden',
				'name' => 'company',
				'value' => $store_name,
				'required' => '',
				'extra-class' => '',
			),

			rand() => array(
				'id' => 'store-url',
				'label' => '',
				'type' => 'hidden',
				'name' => 'website',
				'value' => $store_url,
				'required' => '',
				'extra-class' => '',
			),

			rand() => array(
				'id' => 'plugin-name',
				'label' => '',
				'type' => 'hidden',
				'name' => 'org_plugin_name',
				'value' => '',
				'required' => '',
				'extra-class' => '',
			),
		);

		return $fields;
	}

	/**
	 * Returns form fields html.
	 *
	 * @since       1.0.0
	 * @param       array  $attr               The attributes of this field.
	 * @param       string $base_class         The basic class for the label.
	 */
	public function render_field_html( $attr = array(), $base_class = 'on-boarding' ) {
		$id     = ! empty( $attr['id'] ) ? $attr['id'] : '';
		$name   = ! empty( $attr['name'] ) ? $attr['name'] : '';
		$label  = ! empty( $attr['label'] ) ? $attr['label'] : '';
		$type   = ! empty( $attr['type'] ) ? $attr['type'] : '';
		$class  = ! empty( $attr['extra-class'] ) ? $attr['extra-class'] : '';
		$value  = ! empty( $attr['value'] ) ? $attr['value'] : '';
		$options    = ! empty( $attr['options'] ) ? $attr['options'] : array();
		$multiple   = ! empty( $attr['multiple'] ) && 'yes' == $attr['multiple'] ? 'yes' : 'no';
		$required   = ! empty( $attr['required'] ) ? 'required="required"' : '';

		$html = '';

		if ( 'hidden' != $type ) : ?>
			<div class ="mwb-form-single-field">
			<?php
		endif;

		switch ( $type ) {

			case 'radio':
				// If field requires multiple answers.
				if ( ! empty( $options ) && is_array( $options ) ) :
					?>
					<label class="on-boarding-label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_attr( $label ); ?></label>

					<?php
					$is_multiple = ! empty( $multiple ) && 'yes' != $multiple ? 'name = "' . $name . '"' : '';

					foreach ( $options as $option_value => $option_label ) :
						?>
						<div class="mwb-<?php echo esc_html( $base_class ); ?>-radio-wrapper">
							<input type="<?php echo esc_attr( $type ); ?>" class="on-boarding-<?php echo esc_attr( $type ); ?>-field <?php echo esc_attr( $class ); ?>" value="<?php echo esc_attr( $option_value ); ?>" id="<?php echo esc_attr( $option_value ); ?>" <?php echo esc_html( $required ); ?> <?php echo esc_attr( $is_multiple ); ?>>
							<label class="on-boarding-field-label" for="<?php echo esc_html( $option_value ); ?>"><?php echo esc_html( $option_label ); ?></label>
						</div>
					<?php endforeach; ?>

					<?php
				 endif;

				break;

			case 'checkbox':
				// If field requires multiple answers.
				if ( ! empty( $options ) && is_array( $options ) ) :
					?>

					<label class="on-boarding-label" for="<?php echo esc_attr( $id ); ?>'"><?php echo esc_attr( $label ); ?></label>
					
					<?php foreach ( $options as $option_id => $option_label ) : ?>
						
						   <div class="mwb-<?php echo esc_html( $base_class ); ?>-checkbox-wrapper">
						<input type="<?php echo esc_html( $type ); ?>" class="on-boarding-<?php echo esc_html( $type ); ?>-field <?php echo esc_html( $class ); ?>" value="<?php echo esc_html( $value ); ?>" id="<?php echo esc_html( $option_id ); ?>">
						<label class="on-boarding-field-label" for="<?php echo esc_html( $option_id ); ?>"><?php echo esc_html( $option_label ); ?></label>
						</div>

					<?php endforeach; ?>
					<?php
				endif;

				break;

			case 'select':
			case 'select2':
				// If field requires multiple answers.
				if ( ! empty( $options ) && is_array( $options ) ) {

					$is_multiple = 'yes' == $multiple ? 'multiple' : '';
					$select2 = ( 'yes' == $multiple && 'select' == $type ) || 'select2' == $type ? 'on-boarding-select2 ' : '';
					?>

					<label class="on-boarding-label"  for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
					<select class="on-boarding-select-field <?php echo esc_html( $select2 ); ?> <?php echo esc_html( $class ); ?>" id="<?php echo esc_html( $id ); ?>" name="<?php echo esc_html( $name ); ?>[]" <?php echo esc_html( $required ); ?> <?php echo esc_html( $is_multiple ); ?>>

						<?php if ( 'select' == $type ) : ?>	
							<option class="on-boarding-options" value=""><?php esc_html_e( 'Select Any One Option...', 'coupon-referral-program' ); ?></option>
						<?php endif; ?>

						<?php foreach ( $options as $option_value => $option_label ) : ?>	
						
							<option class="on-boarding-options" value="<?php echo esc_attr( $option_value ); ?>"><?php echo esc_html( $option_label ); ?></option>

						<?php endforeach; ?>
					</select>

					<?php
				}

				break;

			case 'label':
				/**
				 * Only a text in label.
				 */
				?>
				<label class="on-boarding-label <?php echo( esc_html( $class ) ); ?>" for="<?php echo( esc_attr( $id ) ); ?>"><?php echo( esc_html( $label ) ); ?></label>
				<?php
				break;

			case 'textarea':
				/**
				 * Text Area Field.
				 */
				?>
				<textarea rows="3" cols="50" class="<?php echo( esc_html( $base_class ) ); ?>-textarea-field <?php echo( esc_html( $class ) ); ?>" placeholder="<?php echo( esc_attr( $label ) ); ?>" id="<?php echo( esc_attr( $id ) ); ?>" name="<?php echo( esc_attr( $name ) ); ?>"><?php echo( esc_attr( $value ) ); ?></textarea>

				<?php
				break;

			default:
				/**
				 * Text/ Password/ Email.
				 */
				?>
				<label class="on-boarding-label" for="<?php echo( esc_attr( $id ) ); ?>"><?php echo( esc_html( $label ) ); ?></label>
				<input type="<?php echo( esc_attr( $type ) ); ?>" class="on-boarding-<?php echo( esc_attr( $type ) ); ?>-field <?php echo( esc_attr( $class ) ); ?>" value="<?php echo( esc_attr( $value ) ); ?>"  name="<?php echo( esc_attr( $name ) ); ?>" id="<?php echo( esc_attr( $id ) ); ?>" <?php echo( esc_html( $required ) ); ?>>

				<?php
		}

		if ( 'hidden' != $type ) :
			?>
			</div>
			<?php
		endif;
	}


	/**
	 * Send the data to MWB server.
	 *
	 * @since    1.0.0
	 */
	public function send_onboarding_data() {
		
		check_ajax_referer( 'mwb_onboarding_nonce', 'nonce' );

		$form_data = ! empty( $_POST['form_data'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['form_data'] ) ) ) : '';

		$formatted_data = array();

		if ( ! empty( $form_data ) && is_array( $form_data ) ) {

			foreach ( $form_data as $key => $input ) {

				if ( 'show-counter' == $input->name ) {
					continue;
				}

				if ( false !== strrpos( $input->name, '[]' ) ) {

					$new_key = str_replace( '[]', '', $input->name );
					$new_key = str_replace( '"', '', $new_key );

					array_push(
						$formatted_data,
						array(
							'name'  => $new_key,
							'value' => $input->value,
						)
					);

				} else {

					$input->name = str_replace( '"', '', $input->name );

					array_push(
						$formatted_data,
						array(
							'name'  => $input->name,
							'value' => $input->value,
						)
					);
				}
			}
		}

		try {

			$found = current(
				array_filter(
					$formatted_data,
					function( $item ) {
						return isset( $item['name'] ) && 'plugin_deactivation_reason' == $item['name'];
					}
				)
			);

			if ( ! empty( $found ) ) {
				$action_type = 'deactivation';
			} else {
				$action_type = 'onboarding';
			}
			
			if ( ! empty( $formatted_data ) && is_array( $formatted_data ) ) {

				unset( $formatted_data['show-counter'] );

				$this->handle_form_submission_for_hubspot( $formatted_data, $action_type );
			}
		} catch ( Exception $e ) {

			echo json_encode( $e->getMessage() );
			wp_die();
		}

		if ( ! empty( $action_type ) && 'onboarding' == $action_type ) {
			$get_skipped_timstamp = update_option( 'onboarding-data-sent', 'sent' );
		}

		echo json_encode( $formatted_data );
		wp_die();
	}


	/**
	 * Covert array to html.
	 *
	 * @param      array $formatted_data       The parsed data submitted vai form.
	 * @since      1.0.0
	 */
	public function render_form_data_into_table( $formatted_data = array() ) {

		$email_body = '<table border="1" style="text-align:center;"><tr><th>Data</th><th>Value</th></tr>';
		foreach ( $formatted_data as $key => $value ) {

			$key = ucwords( str_replace( '_', ' ', $key ) );
			$key = ucwords( str_replace( '-', ' ', $key ) );

			if ( is_array( $value ) ) {

				$email_body .= '<tr><td>' . $key . '</td><td>';

				foreach ( $value as $k => $v ) {
					$email_body .= ucwords( $v ) . '<br>';
				}

				$email_body .= '</td></tr>';
			} else {

				$email_body .= '  <tr><td>' . $key . '</td><td>' . ucwords( $value ) . '</td></tr>';
			}
		}

		$email_body .= '</table>';

		return $email_body;
	}

	/**
	 * Skip the popup for some days.
	 *
	 * @since    1.0.0
	 */
	public function skip_onboarding_popup() {

		$get_skipped_timstamp = update_option( 'onboarding-data-skipped', time() );
		echo json_encode( 'true' );
		wp_die();
	}

	/**
	 * Add additional validations to onboard screen.
	 *
	 * @param      string $result       The result of this validation.
	 * @since    1.0.0
	 */
	public function add_mwb_additional_validation( $result = true ) {

		if ( ! empty( $_GET['tab'] ) && 'general-setting' !== $_GET['tab'] ) {

			$result = false;
		}

		return $result;
	}

	/**
	 * Handle Hubspot form submission.
	 *
	 * @param      string $result       The result of this validation.
	 * @since    1.0.0
	 */
	protected function handle_form_submission_for_hubspot( $submission = false, $action_type = 'onboarding' ) {

		if ( 'onboarding' == $action_type ) {
			array_push(
				$submission,
				array(
					'name'  => 'currency',
					'value' => get_woocommerce_currency(),
				)
			);
		}

		$result = $this->hubwoo_submit_form( $submission, $action_type );

		if ( true == $result['success'] ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Handle Hubspot GET api calls.
	 *
	 * @since    1.0.0
	 */
	private function hic_get( $endpoint, $headers ) {

		$url = $this->base_url . $endpoint;

		$ch = @curl_init();
		@curl_setopt( $ch, CURLOPT_POST, false );
		@curl_setopt( $ch, CURLOPT_URL, $url );
		@curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		@curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		@curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		@curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
		$response = @curl_exec( $ch );
		$status_code = @curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		$curl_errors = curl_error( $ch );
		@curl_close( $ch );

		return array(
			'status_code' => $status_code,
			'response' => $response,
			'errors' => $curl_errors,
		);
	}


	/**
	 * Handle Hubspot POST api calls.
	 *
	 * @since    1.0.0
	 */
	private function hic_post( $endpoint, $post_params, $headers ) {

		$url = $this->base_url . $endpoint;

		$ch = @curl_init();
		@curl_setopt( $ch, CURLOPT_POST, true );
		@curl_setopt( $ch, CURLOPT_URL, $url );
		@curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_params );
		@curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		@curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		@curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		@curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
		$response = @curl_exec( $ch );
		$status_code = @curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		$curl_errors = curl_error( $ch );
		@curl_close( $ch );

		return array(
			'status_code' => $status_code,
			'response' => $response,
			'errors' => $curl_errors,
		);
	}

	/**
	 *  Hubwoo Onboarding Submission :: Get a form.
	 *
	 * @param           $form_id    form ID.
	 * @since       1.0.0
	 */
	protected function hubwoo_submit_form( $form_data = array(), $action_type = 'onboarding' ) {

		if ( 'onboarding' == $action_type ) {
			$form_id = self::$onboarding_form_id;
		} else {
			$form_id = self::$deactivation_form_id;
		}

		$url = 'submissions/v3/integration/submit/' . self::$portal_id . '/' . $form_id;

		$headers = array(
			'Content-Type: application/json',
		);

		$form_data = json_encode(
			array(
				'fields' => $form_data,
				'context'  => array(
					'pageUri' => self::$store_url,
					'pageName' => self::$store_name,
					'ipAddress' => $this->get_client_ip(),
				),
			)
		);

		$response = $this->hic_post( $url, $form_data, $headers );

		if ( $response['status_code'] == 200 ) {
			$result = json_decode( $response['response'], true );
			$result['success'] = true;
		} else {

			$result = $response;
		}

		return $result;
	}


	// Function to get the client IP address
	function get_client_ip() {
		$ipaddress = '';
		if ( getenv( 'HTTP_CLIENT_IP' ) ) {
			$ipaddress = getenv( 'HTTP_CLIENT_IP' );
		} else if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$ipaddress = getenv( 'HTTP_X_FORWARDED_FOR' );
		} else if ( getenv( 'HTTP_X_FORWARDED' ) ) {
			$ipaddress = getenv( 'HTTP_X_FORWARDED' );
		} else if ( getenv( 'HTTP_FORWARDED_FOR' ) ) {
			$ipaddress = getenv( 'HTTP_FORWARDED_FOR' );
		} else if ( getenv( 'HTTP_FORWARDED' ) ) {
			$ipaddress = getenv( 'HTTP_FORWARDED' );
		} else if ( getenv( 'REMOTE_ADDR' ) ) {
			$ipaddress = getenv( 'REMOTE_ADDR' );
		} else {
			$ipaddress = 'UNKNOWN';
		}
		return $ipaddress;
	}

	// End of Class.
}
