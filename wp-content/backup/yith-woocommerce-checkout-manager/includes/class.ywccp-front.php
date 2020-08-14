<?php
/**
 * Frontend class
 *
 * @author  YITH
 * @package YITH WooCommerce Checkout Manager
 * @version 1.0.0
 */

if ( ! defined( 'YWCCP' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! class_exists( 'YWCCP_Front' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YWCCP_Front {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YWCCP_Front
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YWCCP_VERSION;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YWCCP_Front
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 9 );

			// Multiselect form fields type.
			add_filter( 'woocommerce_form_field_multiselect', array( $this, 'multiselect_type' ), 10, 4 );
			// Datepicker form fields type.
			add_filter( 'woocommerce_form_field_datepicker', array( $this, 'datepicker_type' ), 10, 4 );
			// Heading form fields type.
			add_filter( 'woocommerce_form_field_heading', array( $this, 'heading_type' ), 10, 4 );
			// Timepicker form fields type.
			add_filter( 'woocommerce_form_field_timepicker', array( $this, 'timepicker_type' ), 10, 4 );

			// Add additional table on order view.
			add_action( 'woocommerce_order_details_after_order_table', array( $this, 'additional_info_table' ), 10, 1 );

			// Validate fields.
			add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate_fields' ), 10, 1 );

			// Filter locale array.
			add_filter( 'woocommerce_get_script_data', array( $this, 'filter_locale_array' ), 10, 2 );
		}

		/**
		 * Enqueue scripts and styles
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function enqueue_scripts() {

			global $wp_scripts;

			$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_register_style( 'ywccp-front-style', YWCCP_ASSETS_URL . '/css/ywccp-frontend.css', array(), $this->version, 'all' );
			wp_register_script( 'ywccp-external-script', YWCCP_ASSETS_URL . '/js/ywccp-external.min.js', array( 'jquery' ), $this->version, true );
			wp_register_script( 'ywccp-front-script', YWCCP_ASSETS_URL . '/js/ywccp-frontend' . $min . '.js', array( 'jquery', 'ywccp-external-script' ), $this->version, true );

			if ( is_checkout() || $this->check_myaccount() ) {
				wp_enqueue_script( 'ywccp-external-script' );
				wp_enqueue_script( 'ywccp-front-script' );
				wp_enqueue_style( 'ywccp-front-style' );
				wp_enqueue_script( 'jquery-ui-datepicker' );

				wp_register_script( 'wc-address-i18n', YWCCP_ASSETS_URL . '/js/ywccp-address-i18n' . $min . '.js', array( 'jquery', 'ywccp-front-script' ), $this->version, true );

				wp_localize_script(
					'ywccp-front-script',
					'ywccp_front',
					array(
						'validation_enabled'      => get_option( 'ywccp-enable-js-error-check', 'no' ) === 'yes',
						'vat_validation_enabled'  => get_option( 'ywccp-enable-js-vat-check', 'no' ) === 'yes',
						'err_msg'                 => esc_html__( 'This is a required field.', 'yith-woocommerce-checkout-manager' ),
						'err_msg_vat'             => esc_html__( 'The VAT number you have entered seems to be wrong.', 'yith-woocommerce-checkout-manager' ),
						'err_msg_mail'            => esc_html__( 'The mail you have entered seems to be wrong.', 'yith-woocommerce-checkout-manager' ),
						'time_format'             => get_option( 'ywccp-time-format-datepicker', '12' ) === '12',
						'datepicker_change_year'  => apply_filters( 'ywccp_datepicker_change_year', false ),
						'datepicker_change_month' => apply_filters( 'ywccp_datepicker_change_month', false ),
						'datepicker_year_range'   => apply_filters( 'ywccp_datepicker_year_range', 'c-10:c+10' ),
						'datepicker_min_date'     => apply_filters( 'ywccp_datepicker_min_date', false ),
						'conditions'              => wp_json_encode( ywccp_get_all_conditions() ),
					)
				);

				$inline_style = ywccp_add_custom_style();
				if ( $inline_style ) {
					wp_add_inline_style( 'ywccp-front-style', $inline_style );
				}
			}
			do_action( 'ywccp_scripts_registered' );
		}

		/**
		 * Check if is page my-account and set class variable
		 *
		 * @access protected
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		protected function check_myaccount() {
			global $post;

			if ( ! is_null( $post ) && strpos( $post->post_content, '[woocommerce_my_account' ) !== false && is_user_logged_in() ) {
				return true;
			}

			return false;
		}

		/**
		 * Multiselect fields type
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param string $field
		 * @param string $key
		 * @param array  $args
		 * @param string $value
		 * @return string
		 */
		public function multiselect_type( $field, $key, $args, $value ) {

			$required = $args['required'] ? ' <abbr class="required" title="' . esc_attr__( 'required', 'yith-woocommerce-checkout-manager' ) . '">*</abbr>' : '';
			// Get value as array.
			$value = is_string( $value ) ? explode( ', ', $value ) : $value;

			ob_start();
			?>

			<label for="<?php esc_attr( $args['id'] ); ?>"
				class="<?php echo esc_attr( implode( ' ', $args['label_class'] ) ); ?>">
				<?php echo esc_html( $args['label'] ) . $required; ?>
			</label>
			<select name="<?php echo esc_attr( $key ); ?>[]" id="<?php echo esc_attr( $args['id'] ); ?>"
				class="ywccp-multiselect-type" multiple="multiple"
				data-placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>">
				<?php foreach ( $args['options'] as $key => $option ) : ?>
					<option
						value="<?php echo esc_attr( $key ); ?>" <?php echo in_array( $key, $value ) ? 'selected=selected' : ''; ?>>
						<?php echo esc_html( $option ); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<?php
			$field = ob_get_clean();

			return $this->wrap_field( $field, $args );
		}

		/**
		 * Datepicker fields type
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param string $field
		 * @param string $key
		 * @param array  $args
		 * @param string $value
		 * @return string
		 */
		public function datepicker_type( $field, $key, $args, $value ) {

			$required = $args['required'] ? ' <abbr class="required" title="' . esc_attr__( 'required', 'yith-woocommerce-checkout-manager' ) . '">*</abbr>' : '';
			$format   = get_option( 'ywccp-date-format-datepicker', 'mm/dd/yy' );

			ob_start();
			?>

			<label for="<?php esc_attr( $args['id'] ); ?>"
				class="<?php echo esc_attr( implode( ' ', $args['label_class'] ) ); ?>">
				<?php echo esc_html( $args['label'] ) . $required; ?>
			</label>
			<input name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $args['id'] ); ?>" type="text"
				class="ywccp-datepicker-type"
				value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
				data-format="<?php echo esc_attr( $format ); ?>">

			<?php
			$field = ob_get_clean();

			return $this->wrap_field( $field, $args );
		}

		/**
		 * Timepicker fields type
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param string $field
		 * @param string $key
		 * @param array  $args
		 * @param string $value
		 * @return string
		 */
		public function timepicker_type( $field, $key, $args, $value ) {

			$required = $args['required'] ? ' <abbr class="required" title="' . esc_attr__( 'required', 'yith-woocommerce-checkout-manager' ) . '">*</abbr>' : '';

			ob_start();
			?>

			<label for="<?php esc_attr( $args['id'] ); ?>"
				class="<?php echo esc_attr( implode( ' ', $args['label_class'] ) ); ?>">
				<?php echo esc_html( $args['label'] ) . $required; ?>
			</label>
			<input name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $args['id'] ); ?>" type="text"
				class="ywccp-timepicker-type" value="<?php echo esc_attr( $value ); ?>"
				placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>">

			<?php
			$field = ob_get_clean();

			return $this->wrap_field( $field, $args );
		}

		/**
		 * Heading fields type
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param string $field
		 * @param string $key
		 * @param array  $args
		 * @param string $value
		 * @return string
		 */
		public function heading_type( $field, $key, $args, $value ) {
			// Build class.
			$class = ! empty( $args['class'] ) ? implode( ' ', $args['class'] ) : '';
			$field = '<div class="clear"></div><h3 class="' . esc_attr( $class ) . '">' . wp_kses_post( $args['label'] ) . '</h3>';

			return $field;
		}

		/**
		 * Wrap field
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param string $content
		 * @param array  $args
		 * @return string
		 */
		public function wrap_field( $content, $args ) {
			// Set id.
			$container_id = esc_attr( $args['id'] ) . '_field';
			// Set class.
			$container_class = ! empty( $args['class'] ) ? 'form-row ' . esc_attr( implode( ' ', $args['class'] ) ) : '';
			// Set clear.
			$after = ! empty( $args['clear'] ) ? '<div class="clear"></div>' : '';

			return '<p class="' . $container_class . '" id="' . $container_id . '">' . $content . '</p>' . $after;
		}

		/**
		 * Add additional field table on view order
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param object $order
		 */
		public function additional_info_table( $order ) {

			$fields = ywccp_get_custom_fields( 'additional' );

			// Build template content.
			$content = array();
			foreach ( $fields as $key => $field ) {
				// Check if value exists for order.
				$value = yit_get_prop( $order, $key, true );
				// Get translated field if needed.
				$field = ywccp_multilingual_single_field( $key, $field );

				if ( $value && $field['show_in_order'] ) {

					$content[ $key ] = array(
						'label' => $field['label'],
						'value' => isset( $field['options'][ $value ] ) ? $field['options'][ $value ] : $value,
					);
				}
			}

			if ( empty( $content ) ) {
				return;
			}

			wc_get_template(
				'ywccp-additional-fields-table.php',
				array(
					'fields' => $content,
				),
				'',
				YWCCP_TEMPLATE_PATH . '/'
			);
		}

		/**
		 * Custom validation for fields
		 *
		 * @since  1.0.0
		 * @access public
		 * @author Francesco Licandro
		 * @param array $posted Array of posted params.
		 */
		public function validate_fields( $posted ) {

			$checkout_fields = WC()->checkout->checkout_fields;

			foreach ( $checkout_fields as $fieldset_key => $fieldset ) {

				if ( 'shipping' === $fieldset_key && ( ! $posted['ship_to_different_address'] || ! WC()->cart->needs_shipping_address() ) ) {
					continue;
				}

				foreach ( $fieldset as $key => $field ) {
					if ( isset( $posted[ $key ] ) ) {
						// Validation rules.
						if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) && $posted[ $key ] !== '' ) {
							foreach ( $field['validate'] as $rule ) {
								switch ( $rule ) {
									case 'vat':
										// Get country.
										$country = isset( $posted[ $fieldset_key . '_country' ] ) ? $posted[ $fieldset_key . '_country' ] : '';
										// Validate vat.
										$this->validate_vat_field( $posted[ $key ], $country );
										break;
									default:
										if ( $rule ) {
											do_action( 'ywccp_validation_field_' . $rule, $key, $field, $fieldset_key, $posted );
										}
										break;
								}
							}
						}
					}
				}
			}
		}


		/**
		 * Validate vat number using vatValidation.class
		 *
		 * @author Francesco Licandro
		 * @param string $country The country of VAT.
		 * @param string $vat     The vat to check.
		 */
		public function validate_vat_field( $vat, $country ) {

			// Check for european vat.
			switch ( $country ) {
				case 'AT':
					$regex = '/^(AT){0,1}U[0-9]{8}$/i';
					break;
				case 'BE':
					$regex = '/^(BE){0,1}[0]{0,1}[0-9]{9}$/i';
					break;
				case 'BG':
					$regex = '/^(BG){0,1}[0-9]{9,10}$/i';
					break;
				case 'CY':
					$regex = '/^(CY){0,1}[0-9]{8}[A-Z]$/i';
					break;
				case 'CZ':
					$regex = '/^(CZ){0,1}[0-9]{8,10}$/i';
					break;
				case 'DK':
					$regex = '/^(DK){0,1}([0-9]{2}[\ ]{0,1}){3}[0-9]{2}$/i';
					break;
				case 'EE':
				case 'EL':
				case 'PT':
				case 'DE':
					$regex = '/^(EE|EL|DE|PT){0,1}[0-9]{9}$/i';
					break;
				case 'FR':
					$regex = '/^(FR){0,1}[0-9A-Z]{2}[\ ]{0,1}[0-9]{9}$/i';
					break;
				case 'FI':
				case 'HU':
				case 'LU':
				case 'MT':
				case 'SI':
					$regex = '/^(FI|HU|LU|MT|SI){0,1}[0-9]{8}$/i';
					break;
				case 'IE':
					$regex = '/^(IE){0,1}[0-9][0-9A-Z\+\*][0-9]{5}[A-Z]$/i';
					break;
				case 'IT':
				case 'LV':
					$regex = '/^(IT|LV){0,1}[0-9]{11}$/i';
					break;
				case 'LT':
					$regex = '/^(LT){0,1}([0-9]{9}|[0-9]{12})$/i';
					break;
				case 'NL':
					$regex = '/^(NL){0,1}[0-9]{9}B[0-9]{2}$/i';
					break;
				case 'PL':
				case 'SK':
					$regex = '/^(PL|SK){0,1}[0-9]{10}$/i';
					break;
				case 'RO':
					$regex = '/^(RO){0,1}[0-9]{2,10}$/i';
					break;
				case 'SE':
					$regex = '/^(SE){0,1}[0-9]{12}$/i';
					break;
				case 'ES':
					$regex = '/^(ES){0,1}([0-9A-Z][0-9]{7}[A-Z])|([A-Z][0-9]{7}[0-9A-Z])$/i';
					break;
				case 'GB':
					$regex = '/^(GB){0,1}([1-9][0-9]{2}[\ ]{0,1}[0-9]{4}[\ ]{0,1}[0-9]{2})|([1-9][0-9]{2}[\ ]{0,1}[0-9]{4}[\ ]{0,1}[0-9]{2}[\ ]{0,1}[0-9]{3})|((GD|HA)[0-9]{3})$/i';
					break;
				default:
					$regex = false;
					break;
			}

			$error = false;

			// Remove empty spaces and + char.
			$vat = trim( preg_replace( '/\+|\s/', '', $vat ) );
			if ( ! $regex ) {
				$res = preg_match_all( '/[0-9]/', $vat );
				if ( $res < 4 ) {
					$error = true;
				}
			} else {
				$res = preg_match( $regex, $vat );
				if ( ! $res ) {
					$error = true;
				}
			}

			if ( $error ) {
				wc_add_notice( __( 'The VAT number you have entered seems to be wrong. Please, check it.', 'yith-woocommerce-checkout-manager' ), 'error' );
			}
		}

		/**
		 * Get customized locale default
		 *
		 * @since  1.3.8
		 * @author Francesco Licandro
		 * @return array
		 */
		public function get_locale_default() {

			$new_locale = array();
			$billing    = ywccp_get_checkout_fields( 'billing' );
			$shipping   = ywccp_get_checkout_fields( 'shipping' );

			foreach ( $billing as $key => $value ) {
				// Check for translations.
				$value = ywccp_field_filter_wpml_strings( $key, $value );
				$key   = str_replace( 'billing_', '', $key );

				$new_locale['billing'][ $key ] = array(
					'required'    => isset( $value['required'] ) ? $value['required'] : false,
					'label'       => isset( $value['label'] ) ? $value['label'] : '',
					'placeholder' => isset( $value['placeholder'] ) ? $value['placeholder'] : '',
				);
			}
			foreach ( $shipping as $key => $value ) {
				// Check for translations.
				$value = ywccp_field_filter_wpml_strings( $key, $value );
				$key   = str_replace( 'shipping_', '', $key );

				$new_locale['shipping'][ $key ] = array(
					'required'    => isset( $value['required'] ) ? $value['required'] : false,
					'label'       => isset( $value['label'] ) ? $value['label'] : '',
					'placeholder' => isset( $value['placeholder'] ) ? $value['placeholder'] : '',
				);
			}

			return $new_locale;
		}

		/**
		 * Filter locale array for ywccp_address_i18n
		 *
		 * @since  1.3.8
		 * @author Francesco Licandro
		 * @param array  $params The locale array.
		 * @param string $handle The script handle.
		 * @return array
		 */
		public function filter_locale_array( $params, $handle ) {
			if ( 'wc-address-i18n' !== $handle ) {
				return $params;
			}

			foreach ( $params as $key => &$locale ) {

				if ( 'locale' !== $key ) {
					continue;
				}

				$locale = json_decode( $locale, true );

				foreach ( $locale as $country => &$fields ) {
					if ( 'default' === $country ) {
						$fields = $this->get_locale_default();
					} else {
						foreach ( $fields as $key => &$field ) {
							unset( $field['label'], $field['placeholder'], $field['class'], $field['priority'], $field['required'] );
						}
						$fields = array_filter( $fields );
					}
				}

				$locale = array_filter( $locale );
				$locale = wp_json_encode( $locale );
			}

			return $params;
		}
	}
}
/**
 * Unique access to instance of YWCCP_Front class
 *
 * @since 1.0.0
 * @return YWCCP_Front
 */
function YWCCP_Front() { // phpcs:ignore
	return YWCCP_Front::get_instance();
}
