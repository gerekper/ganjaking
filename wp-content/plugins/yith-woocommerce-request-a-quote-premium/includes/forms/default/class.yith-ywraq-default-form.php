<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements the YITH_YWRAQ_Default_Form class.
 *
 * @class   YITH_YWRAQ_Default_Form
 * @since   2.0.0
 * @author  YITH
 * @package YITH
 */
if ( ! class_exists( 'YITH_YWRAQ_Default_Form' ) ) {

	/**
	 * Class YITH_YWRAQ_Default_Form
	 */
	class YITH_YWRAQ_Default_Form {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_YWRAQ_Default_Form
		 */
		protected static $instance;

		protected $attachments = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_YWRAQ_Default_Form
		 * @since 2.0.0
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
		 * Initialize form and registers actions and filters to be used
		 *
		 * @since  2.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 15 );

			add_filter( 'woocommerce_form_field_ywraq_multiselect', array( $this, 'multiselect_type' ), 10, 4 );
			add_filter( 'woocommerce_form_field_ywraq_datepicker', array( $this, 'datepicker_type' ), 10, 4 );
			add_filter( 'woocommerce_form_field_ywraq_heading', array( $this, 'heading_type' ), 10, 4 );
			add_filter( 'woocommerce_form_field_ywraq_timepicker', array( $this, 'timepicker_type' ), 10, 4 );
			add_filter( 'woocommerce_form_field_ywraq_upload', array( $this, 'upload_type' ), 10, 4 );
			add_filter( 'woocommerce_form_field_ywraq_acceptance', array( $this, 'acceptance_type' ), 10, 4 );

			add_action( 'wc_ajax_ywraq_submit_default_form', array( $this, 'submit_default_form' ) );
			add_filter( 'ywraq_order_meta_list', array( $this, 'add_order_metas' ), 10, 3 );
			add_filter( 'ywraq_form_fields', array( $this, 'filter_wpml_strings' ), 999, 1 );

			// Form options
			add_action( 'ywraq_admin_tabs', array( $this, 'add_admin_tab' ) );
			add_action( 'yith_ywraq_form_table', array( $this, 'raqform_table' ) );
			add_action( 'admin_footer', array( $this, 'print_add_edit_fields_form' ) );
			add_action( 'wp_ajax_ywraq_save_default_form', array( $this, 'save_options' ) );
			add_filter( 'script_loader_tag', array( $this, 'add_async_attribute' ), 10, 2 );
		}

		/**
		 * Add async and defer to recaptcha script
		 *
		 * @param $tag
		 * @param $handle
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_async_attribute( $tag, $handle ) {
			if ( 'yith_ywraq_recaptcha' !== $handle ) {
				return $tag;
			}

			return str_replace( ' src', ' async="async" defer="defer" src', $tag );
		}

		/**
		 * Add the tab of default form in the plugin settings
		 *
		 * @param $admin_tabs
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_admin_tab( $admin_tabs ) {
			if ( ( isset( $_POST['ywraq_inquiry_form_type'] ) && $_POST['ywraq_inquiry_form_type'] == 'default' ) || get_option( 'ywraq_inquiry_form_type', 'default' ) == 'default' ) {
				$admin_tabs['raqform'] = __( 'Default Form', 'yith-woocommerce-request-a-quote' ); //@since 1.4.5
			}
			if ( ( isset( $_POST['ywraq_inquiry_form_type'] ) && $_POST['ywraq_inquiry_form_type'] != 'default' ) ) {
				unset( $admin_tabs['raqform'] );
			}

			return $admin_tabs;
		}

		/**
		 * Add default form to request a quote
		 *
		 * @throws Exception
		 * @since  2.0.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function raqform_table() {
			if ( is_null( WC()->customer ) ) {
				WC()->customer = new WC_Customer( get_current_user_id(), false );
			}
			if ( isset( $_GET['page'] ) && $_GET['page'] == YITH_YWRAQ_Admin()->_panel_page && isset( $_GET['tab'] ) && $_GET['tab'] == 'raqform' && file_exists( YITH_YWRAQ_TEMPLATE_PATH . '/admin/ywraq-raqform-table.php' ) ) {

				$fields             = ywraq_get_form_fields( true );
				$default_fields_key = ywraq_get_default_form_fields_keys();

				include_once( YITH_YWRAQ_TEMPLATE_PATH . '/admin/ywraq-raqform-table.php' );

			}
		}

		/**
		 * Print edit form fields
		 *
		 * @since  2.0.0
		 * @author Francesco Licandro
		 */
		public function print_add_edit_fields_form() {
			if ( isset( $_GET['page'] ) && $_GET['page'] == YITH_YWRAQ_Admin()->_panel_page && isset( $_GET['tab'] ) && $_GET['tab'] == 'raqform' && file_exists( YITH_YWRAQ_TEMPLATE_PATH . '/admin/ywraq-fields-edit.php' ) ) {

				// define variables
				$positions         = ywraq_get_array_positions_form_field();
				$validation        = ywraq_get_array_validation_form_field();
				$field_types       = ywraq_get_form_field_type();
				$connect_to_fields = ywraq_get_connect_fields();

				include_once( YITH_YWRAQ_TEMPLATE_PATH . '/admin/ywraq-fields-edit.php' );
			}
		}

		/**
		 * Save options fields
		 *
		 * @since  2.0.0
		 * @author Francesco Licandro
		 */
		public function save_options() {

			$names = isset( $_POST['field_name'] ) ? $_POST['field_name'] : array();
			if ( empty( $names ) ) {
				return;
			}

			// get max number
			$max        = max( array_map( 'absint', array_keys( $names ) ) );
			$new_fields = array();

			for ( $i = 0; $i <= $max; $i++ ) {

				// get name
				$name = wc_clean( stripslashes( $names[ $i ] ) );
				$name = str_replace( ' ', '_', $name );

				if ( ! empty( $_POST['field_deleted'][ $i ] ) ) {
					$this->save_ordermeta( $name );
					continue;
				}

				$new_fields[ $name ] = array();

				$new_fields[ $name ]['type']                      = ! empty( $_POST['field_type'][ $i ] ) ? $_POST['field_type'][ $i ] : 'text';
				$new_fields[ $name ]['label']                     = ! empty( $_POST['field_label'][ $i ] ) ? stripslashes( $_POST['field_label'][ $i ] ) : '';
				$new_fields[ $name ]['placeholder']               = ! empty( $_POST['field_placeholder'][ $i ] ) ? stripslashes( $_POST['field_placeholder'][ $i ] ) : '';
				$new_fields[ $name ]['options']                   = ! empty( $_POST['field_options'][ $i ] ) ? $this->create_options_array( $_POST['field_options'][ $i ], $new_fields[ $name ]['type'] ) : array();
				$new_fields[ $name ]['class']                     = ! empty( $_POST['field_class'][ $i ] ) ? array_map( 'wc_clean', explode( ',', $_POST['field_class'][ $i ] ) ) : array();
				$new_fields[ $name ]['label_class']               = ! empty( $_POST['field_label_class'][ $i ] ) ? array_map( 'wc_clean', explode( ',', $_POST['field_label_class'][ $i ] ) ) : '';
				$new_fields[ $name ]['validate']                  = ! empty( $_POST['field_validate'][ $i ] ) ? explode( ',', $_POST['field_validate'][ $i ] ) : '';
				$new_fields[ $name ]['connect_to_field']          = ! empty( $_POST['field_connect_to_field'][ $i ] ) ? $_POST['field_connect_to_field'][ $i ] : '';
				$new_fields[ $name ]['required']                  = ! empty( $_POST['field_required'][ $i ] ) && $new_fields[ $name ]['type'] != 'ywraq_heading';
				$new_fields[ $name ]['default']                   = ( ! empty( $_POST['field_checked'][ $i ] ) && $new_fields[ $name ]['type'] == 'checkbox' ) ? 1 : '';
				$new_fields[ $name ]['upload_allowed_extensions'] = ( ! empty( $_POST['field_upload_allowed_extensions'][ $i ] ) && $new_fields[ $name ]['type'] != 'ywraq_heading' ) ? $_POST['field_upload_allowed_extensions'][ $i ] : '';
				$new_fields[ $name ]['description']               = ( ! empty( $_POST['field_description'][ $i ] ) && $new_fields[ $name ]['type'] != 'ywraq_heading' ) ? $_POST['field_description'][ $i ] : '';
				$new_fields[ $name ]['max_filesize']              = ( ! empty( $_POST['field_max_filesize'][ $i ] ) && $new_fields[ $name ]['type'] != 'ywraq_heading' ) ? $_POST['field_max_filesize'][ $i ] : '';
				$new_fields[ $name ]['enabled']                   = ! empty( $_POST['field_enabled'][ $i ] );
				$new_fields[ $name ] ['id']                       = ( ! empty( $_POST['field_id'][ $i ] ) && 'state' == $new_fields[ $name ]['type'] ) ? $_POST['field_id'][ $i ] : $name;
				// check also in bulk action
				if ( ( $_POST['bulk_action'] || $_POST['bulk_action_bottom'] ) && isset( $_POST['select_field'][ $i ] ) ) {
					$new_fields[ $name ]['enabled'] = $_POST['bulk_action'] == 'enable' || $_POST['bulk_action_bottom'] == 'enable';
				}

				$new_fields[ $name ]['custom_attributes'] = array();
				if ( ! empty( $_POST['field_position'][ $i ] ) ) {
					array_push( $new_fields[ $name ]['class'], $_POST['field_position'][ $i ] );
				}

			}

			if ( ! empty( $new_fields ) ) {
				// save option
				update_option( 'ywraq_fields_form_options', $new_fields );
			}
		}

		/**
		 * Create options array for field
		 *
		 * @access protected
		 *
		 * @param string $options
		 * @param string $type
		 *
		 * @return array
		 * @since  2.0.0
		 * @author Francesco Licandro
		 *
		 */
		protected function create_options_array( $options, $type = '' ) {

			$options_array = array();

			$options = array_map( 'wc_clean', explode( '|', $options ) ); // create array from string
			$options = array_unique( $options );                          // remove double entries

			// first of all add empty options for placeholder if type is option
			if ( $type == 'select' ) {
				$options_array[''] = '';
			}

			foreach ( $options as $option ) {
				$has_key = strpos( $option, '::' );
				if ( $has_key ) {
					list( $key, $option ) = explode( '::', $option );
				} else {
					$key = $option;
				}

				// create key
				$key                   = sanitize_title_with_dashes( $key );
				$options_array[ $key ] = stripslashes( $option );
			}

			return $options_array;
		}

		/**
		 * Create order meta for prevent losing information if a fields was deleted
		 *
		 * @access protected
		 *
		 * @param string $field The field name to convert
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 *
		 */
		protected function save_ordermeta( $field ) {
			global $wpdb;

			$query = $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_key = %s WHERE meta_key LIKE %s", $field, '_' . $field );
			$wpdb->query( $query );
		}

		/**
		 * Enqueue Scripts and Styles
		 *
		 * @return void
		 * @since  2.0.0
		 * @author Emanuela Castorina
		 */
		public function enqueue_styles_scripts() {

			global $post;

			$raq_page_id = YITH_Request_Quote()->get_raq_page_id();
			$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			//Styles and scripts in request a quote page
			if ( ( $post && $post->ID == $raq_page_id ) || ( $post && $post->ID == wc_get_page_id( 'myaccount' ) ) || defined( 'YITH_WACP_PREMIUM' ) ) {

				if ( ! wp_script_is( 'selectWoo' ) ) {
					wp_enqueue_script( 'selectWoo' );
					wp_enqueue_style( 'select2' );
					wp_enqueue_script( 'wc-enhanced-select' );
				}
				wp_enqueue_script( 'wc-country-select' );
				wp_enqueue_script( 'wc-address-i18n' );
				wp_enqueue_script( 'ywraq-default-form-js', YITH_YWRAQ_ASSETS_URL . '/js/ywraq-default-form' . $suffix . '.js', array(
					'jquery',
					'jquery-ui-datepicker',
				), YITH_YWRAQ_VERSION, true );
				wp_enqueue_style( 'ywraq-default-form', YITH_YWRAQ_ASSETS_URL . '/css/ywraq-default-form.css' );

				if ( ywraq_check_recaptcha_options() && ( ! class_exists( 'WP_reCaptcha' ) || ( class_exists( 'WP_reCaptcha' ) && is_user_logged_in() ) ) ) {
					wp_enqueue_script( 'yith_ywraq_recaptcha', '//www.google.com/recaptcha/api.js?onload=ywraq_recaptcha&render=explicit', array( 'ywraq-default-form-js' ), YITH_YWRAQ_VERSION, false );
				}

				$form_localize_args = array(
					'ajaxurl'                   => WC_AJAX::get_endpoint( "%%endpoint%%" ),
					'validation_enabled'        => get_option( 'ywraq-enable-js-error-check' ) == 'yes',
					'err_msg'                   => __( 'This is a required field.', 'yith-woocommerce-request-a-quote' ),
					'err_msg_mail'              => __( 'The mail you have entered seems to be wrong.', 'yith-woocommerce-request-a-quote' ),
					'err_msg_upload_filesize'   => __( 'The file is greater than ', 'yith-woocommerce-request-a-quote' ),
					'err_msg_allowed_extension' => __( 'This file type is unsupported. Valid extensions: ', 'yith-woocommerce-request-a-quote' ),
					'time_format'               => get_option( 'ywraq-time-format-datepicker' ) == '12',
					'block_loader'              => get_option( 'ywraq_loader_image', ywraq_get_ajax_default_loader() ),
				);

				wp_localize_script( 'ywraq-default-form-js', 'ywraq_form', apply_filters( 'yith_ywraq_form_localize', $form_localize_args ) );

			}

		}

		/**
		 * Multiselect fields type
		 *
		 * @param string $field
		 * @param string $key
		 * @param array  $args
		 * @param string $value
		 *
		 * @return string
		 * @since  2.0.0
		 *
		 * @author Francesco Licandro
		 */
		public function multiselect_type( $field, $key, $args, $value ) {

			$required = $args['required'] ? ' <abbr class="required" title="' . esc_attr__( 'required', 'yith-woocommerce-request-a-quote' ) . '">*</abbr>' : '';
			// get value as array
			$value = is_string( $value ) ? explode( ', ', $value ) : $value;

			ob_start();
			?>

			<label for="<?php esc_attr( $args['id'] ) ?>"
				class="<?php echo esc_attr( implode( ' ', $args['label_class'] ) ) ?>">
				<?php echo esc_html( $args['label'] ) . $required ?>
			</label>
			<select name="<?php echo esc_attr( $key ) ?>[]" id="<?php echo esc_attr( $args['id'] ) ?>"
				class="ywraq-multiselect-type wc-enhanced-select" multiple="multiple"
				data-placeholder="<?php echo esc_attr( $args['placeholder'] ) ?>">
				<?php foreach ( $args['options'] as $key => $option ) : ?>
					<option
						value="<?php echo $key ?>" <?php echo in_array( $key, $value ) ? 'selected=selected' : ''; ?>><?php echo $option ?></option>
				<?php endforeach; ?>
			</select>

			<?php
			$field = ob_get_clean();

			return $this->wrap_field( $field, $args );

		}

		/**
		 * Datepicker fields type
		 *
		 * @param string $field
		 * @param string $key
		 * @param array  $args
		 * @param string $value
		 *
		 * @return string
		 * @since  2.0.0
		 *
		 * @author Francesco Licandro
		 */
		public function datepicker_type( $field, $key, $args, $value ) {

			$required = $args['required'] ? ' <abbr class="required" title="' . esc_attr__( 'required', 'yith-woocommerce-request-a-quote' ) . '">*</abbr>' : '';
			$format   = get_option( 'ywraq-date-format-datepicker', 'mm/dd/yy' );

			ob_start();
			?>

			<label for="<?php esc_attr( $args['id'] ) ?>"
				class="<?php echo esc_attr( implode( ' ', $args['label_class'] ) ) ?>">
				<?php echo esc_html( $args['label'] ) . $required ?>
			</label>
			<input name="<?php echo esc_attr( $key ) ?>" id="<?php echo esc_attr( $args['id'] ) ?>" type="text"
				class="ywraq-datepicker-type"
				value="<?php echo $value ?>" placeholder="<?php echo esc_attr( $args['placeholder'] ) ?>"
				data-format="<?php echo $format ?>">

			<?php
			$field = ob_get_clean();

			return $this->wrap_field( $field, $args );
		}

		/**
		 * Timepicker fields type
		 *
		 * @param string $field
		 * @param string $key
		 * @param array  $args
		 * @param string $value
		 *
		 * @return string
		 * @since  2.0.0
		 *
		 * @author Francesco Licandro
		 */
		public function timepicker_type( $field, $key, $args, $value ) {

			$required = $args['required'] ? ' <abbr class="required" title="' . esc_attr__( 'required', 'yith-woocommerce-request-a-quote' ) . '">*</abbr>' : '';

			ob_start();
			?>

			<label for="<?php esc_attr( $args['id'] ) ?>"
				class="<?php echo esc_attr( implode( ' ', $args['label_class'] ) ) ?>">
				<?php echo esc_html( $args['label'] ) . $required ?>
			</label>
			<input name="<?php echo esc_attr( $key ) ?>" id="<?php echo esc_attr( $args['id'] ) ?>" type="text"
				class="ywraq-timepicker-type" value="<?php echo $value ?>"
				placeholder="<?php echo esc_attr( $args['placeholder'] ) ?>">

			<?php
			$field = ob_get_clean();

			return $this->wrap_field( $field, $args );
		}

		/**
		 * Upload fields type
		 *
		 * @param string $field
		 * @param string $key
		 * @param array  $args
		 * @param string $value
		 *
		 * @return string
		 * @since  2.0.0
		 *
		 * @author Emanuela Castorina
		 */
		public function upload_type( $field, $key, $args, $value ) {

			$required = $args['required'] ? ' <abbr class="required" title="' . esc_attr__( 'required', 'yith-woocommerce-request-a-quote' ) . '">*</abbr>' : '';

			ob_start();
			?>

			<label for="<?php esc_attr( $args['id'] ) ?>"
				class="<?php echo esc_attr( implode( ' ', $args['label_class'] ) ) ?>">
				<?php echo esc_html( $args['label'] ) . $required ?>
			</label>
			<input name="<?php echo esc_attr( $key ) ?>" id="<?php echo esc_attr( $args['id'] ) ?>" type="file"
				class="ywraq-upload-type input-text input-upload"
				placeholder="<?php echo esc_attr( $args['placeholder'] ) ?>"
				data-max-size="<?php echo $args['max_filesize'] ?>"
				data-allowed="<?php echo $args['upload_allowed_extensions'] ?>">

			<?php
			$field = ob_get_clean();

			return $this->wrap_field( $field, $args );
		}

		/**
		 * @param $field
		 * @param $key
		 * @param $args
		 * @param $value
		 *
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function acceptance_type( $field, $key, $args, $value ) {
			$required = $args['required'] ? ' <abbr class="required" title="' . esc_attr__( 'required', 'yith-woocommerce-request-a-quote' ) . '">*</abbr>' : '';

			ob_start();
			?>

			<span
				class="ywraq_acceptance_description"><?php echo ywraq_replace_policy_page_link_placeholders( $args['description'] ) ?></span>
			<input type="checkbox" name="<?php echo esc_attr( $key ) ?>"
				id="<?php echo esc_attr( $args['id'] ) ?>" <?php echo $args['required'] ? 'required' : '' ?>>
			<label for="<?php echo esc_attr( $key ) ?>"
				class="ywraq_acceptance_label <?php echo esc_attr( implode( ' ', $args['label_class'] ) ) ?>">
				<?php echo esc_html( $args['label'] ) . $required ?></label>
			<?php
			$field = ob_get_clean();

			return $this->wrap_field( $field, $args );
		}

		/**
		 * Heading fields type
		 *
		 * @param string $field
		 * @param string $key
		 * @param array  $args
		 * @param string $value
		 *
		 * @return string
		 * @since  2.0.0
		 *
		 * @author Francesco Licandro
		 */
		public function heading_type( $field, $key, $args, $value ) {

			$container_class = ! empty( $args['class'] ) ? 'form-row ' . esc_attr( implode( ' ', $args['class'] ) ) : '';

			$field = '<h3 class="' . $container_class . '">' . $args['label'] . '</h3>';

			return $field;
		}

		/**
		 * Wrap field
		 *
		 * @param string $content
		 * @param array  $args
		 *
		 * @return string
		 * @since  2.0.0
		 *
		 * @author Francesco Licandro
		 */
		public function wrap_field( $content, $args ) {
			// set id
			$container_id = esc_attr( $args['id'] ) . '_field';
			// set class
			$container_class = ! empty( $args['class'] ) ? 'form-row ' . esc_attr( implode( ' ', $args['class'] ) ) : '';
			// set clear
			$after = ! empty( $args['clear'] ) ? '<div class="clear"></div>' : '';

			return '<p class="' . $container_class . '" id="' . $container_id . '">' . $content . '</p>' . $after;
		}

		/**
		 * Check the form validation and trigger the email message
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function submit_default_form() {

			//check if the default form was submitted
			if ( ! isset( $_REQUEST['ywraq_mail_wpnonce'] ) ) {
				return;
			}

			$posted               = apply_filters( 'ywraq_default_form_posted_request', $_REQUEST );
			$errors               = array();
			$form_fields          = ywraq_get_form_fields();
			$filled_form_fields   = array();
			$registration_enabled = ! is_user_logged_in() && 'yes' == get_option( 'ywraq_add_user_registration_check', 'no' );
			$force_registration   = get_option( 'ywraq_force_user_to_register', 'no' ) == 'yes';
			$account_fields       = array();

			if ( $registration_enabled && isset( $posted['createaccount'] ) ) {
				$checkout       = WC_Checkout::instance();
				$account_fields = $checkout->get_checkout_fields( 'account' );
				foreach ( $account_fields as $key => $account_field ) {
					$account_fields[ $key ]['enabled'] = 1;
				}
				$form_fields = array_merge( $form_fields, $account_fields );
			}

			if ( YITH_Request_Quote()->is_empty() ) {
				$errors[] = ywraq_get_list_empty_message();
			}

			//validating fields.
			foreach ( $form_fields as $name => $form_field ) {

				if ( ! $form_field['enabled'] ) {
					continue;
				}

				if ( ! isset( $account_fields[ $name ] ) ) {
					$filled_form_fields[ $name ] = array(
						'id'               => $form_field['id'],
						'type'             => $form_field['type'],
						'label'            => $form_field['label'],
						'connect_to_field' => isset( $form_field['connect_to_field'] ) ? $form_field['connect_to_field'] : '',
						'value'            => '',
					);

				}

				$error = $this->validate_field( $posted, $name, $form_field );

				if ( $error ) {
					$errors[] = $error;
				} elseif ( isset( $filled_form_fields[ $name ] ) ) {
					if ( 'ywraq_upload' == $form_field['type'] ) {
						$filled_form_fields[ $name ]['value'] = isset( $this->attachments[ $name ] ) ? $this->attachments[ $name ] : array();
					} else {
						$value = isset( $posted[ $name ] ) ? $posted[ $name ] : '';
						if ( $value && in_array( $form_field['type'], array( 'text', 'state', 'ywraq_heading' ) ) ) {
							$value = sanitize_text_field( $posted[ $name ] );
						} elseif ( $value && 'email' == $form_field['type'] ) {
							$value = sanitize_email( $value );
						} elseif ( $value && 'textarea' == $form_field['type'] ) {
							$value = sanitize_textarea_field( $value );
						} elseif ( $value && 'select' == $form_field['type'] ) {
							$value =  $form_field['options'][$value];
						}

						$filled_form_fields[ $name ]['value'] = $value;

						if ( $form_field['type'] == 'country' ) {
							$filled_form_fields['user_country'] = $value;
						}
					}
				}
			}
			remove_all_filters( 'woocommerce_registration_errors' );

			//validating recaptcha.
			if ( ywraq_check_recaptcha_options() ) {
				$captcha_error_string = sprintf( '<p>%s</p>', __( 'Please check the the captcha form.', 'yith-woocommerce-request-a-quote' ) );
				if ( isset( $posted['g-recaptcha-response'] ) ) {
					$captcha = $posted['g-recaptcha-response'];
				}
				if ( ! $captcha ) {
					$errors[] = $captcha_error_string;
				} else {
					$secretKey = get_option( 'ywraq_reCAPTCHA_secretkey' );
					$response  = wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret=" . $secretKey . "&response=" . $captcha );
					if ( is_wp_error( $response ) || ! isset( $response['body'] ) ) {
						$errors[] = $captcha_error_string;
					} else {
						$responseKeys = json_decode( $response['body'], true );
						if ( intval( $responseKeys["success"] ) !== 1 ) {
							$errors[] = $captcha_error_string;
						}
					}
				}
			}

			$errors = apply_filters( 'ywraq_request_validate_fields', $errors, $posted );

			if ( $errors ) {
				$results = array(
					'result'   => 'failure',
					'messages' => $this->get_errors( $errors ),
				);
			} else {
				try {

					//Add customer id
					$filled_form_fields['customer_id'] = $this->get_customer_id( $posted, $registration_enabled, $force_registration, $filled_form_fields );

					//Get language
					if ( isset( $posted['lang'] ) ) {
						$filled_form_fields['lang'] = sanitize_text_field( $posted['lang'] );
					}

					$filled_form_fields['raq_content'] = YITH_Request_Quote()->get_raq_return();

					$username = '';
					//retro compatibility
					if ( isset( $posted['first_name'] ) ) {
						$username = sanitize_text_field( $posted['first_name'] );
					}

					if ( isset( $posted['last_name'] ) ) {
						$username .= ' ' . sanitize_text_field( $posted['last_name'] );
					}

					$filled_form_fields['user_name']    = $username ? trim( $username ) : '';
					$filled_form_fields['user_email']   = isset( $posted['email'] ) ? sanitize_text_field( $posted['email'] ) : '';
					$filled_form_fields['user_message'] = isset( $posted['message'] ) ? sanitize_text_field( $posted['message'] ) : '';

					if ( get_option( 'ywraq_enable_order_creation', 'yes' ) == 'yes' ) {
						do_action( 'ywraq_process', $filled_form_fields );
					}

					do_action( 'send_raq_mail', $filled_form_fields );
					do_action( 'send_raq_customer_mail', $filled_form_fields );
					apply_filters( 'ywraq_email_filled_form_fields', $filled_form_fields );

					$results = array(
						'result'   => 'success',
						'redirect' => YITH_Request_Quote()->get_redirect_page_url(),
					);

				} catch ( Exception $e ) {
					$results = array(
						'result'   => 'failure',
						'messages' => $e->getMessage(),
					);
				}
			}
			wp_send_json( $results );
			exit();
		}

		/**
		 * Return the customer id that is sending the quote.
		 *
		 * If the customer must be created and there's an error an exception is triggered.
		 *
		 * @param $posted
		 * @param $registration_enabled
		 * @param $force_registration
		 *
		 * @param $filled_form_fields
		 *
		 * @return int|WP_Error
		 * @throws Exception
		 * @since  2.0.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		private function get_customer_id( $posted, $registration_enabled, $force_registration, $filled_form_fields ) {
			$customer_id      = 0;
			$email            = isset( $posted['email'] ) ? sanitize_email( $posted['email'] ) : '';
			$current_customer = $email ? get_user_by( 'email', $email ) : false;
			if ( is_user_logged_in() ) {
				$customer_id = get_current_user_id();
			} elseif ( is_object( $current_customer ) && apply_filters( 'ywraq_link_to_registered', false ) ) {
				$customer_id = $current_customer->ID;
			} elseif ( $registration_enabled && ( $force_registration || ! empty( $posted['createaccount'] ) ) ) {
				$username    = ! empty( $posted['account_username'] ) ? sanitize_text_field( $posted['account_username'] ) : '';
				$password    = ! empty( $posted['account_password'] ) ? sanitize_text_field( $posted['account_password'] ) : '';
				$customer_id = wc_create_new_customer( $email, $username, $password );

				if ( is_wp_error( $customer_id ) ) {
					throw new Exception( $customer_id->get_error_message() );
				}

				wp_set_current_user( $customer_id );
				wc_set_customer_auth_cookie( $customer_id );

				// Add customer info from other fields.
				if ( $customer_id && apply_filters( 'ywraq_default_form_update_customer_data', true ) ) {
					$customer                = new WC_Customer( $customer_id );
					$filled_form_fields_keys = array_keys( $filled_form_fields );

					$search_index_key   = array_search( 'billing_first_name', array_column( $filled_form_fields, 'connect_to_field' ) );
					$billing_first_name = ( false !== $search_index_key ) ? $filled_form_fields[ $filled_form_fields_keys[ $search_index_key ] ]['value'] : '';

					$search_index_key  = array_search( 'billing_last_name', array_column( $filled_form_fields, 'connect_to_field' ) );
					$billing_last_name = ( false !== $search_index_key ) ? $filled_form_fields[ $filled_form_fields_keys[ $search_index_key ] ]['value'] : '';

					if ( ! empty( $billing_first_name ) ) {
						$customer->set_first_name( $billing_first_name );
					}

					if ( ! empty( $billing_last_name ) ) {
						$customer->set_last_name( $billing_last_name );
					}

					// If the display name is an email, update to the user's full name.
					if ( is_email( $customer->get_display_name() ) ) {
						$customer->set_display_name( $billing_first_name . ' ' . $billing_last_name );
					}

					foreach ( $filled_form_fields as $key => $value ) {
						// Use setters where available.
						$connected = $value['connect_to_field'];

						if ( is_callable( array( $customer, "set_{$connected}" ) ) ) {
							$customer->{"set_{$connected}"}( $value['value'] );

							// Store custom fields prefixed with wither shipping_ or billing_.
						} elseif ( 0 === stripos( $connected, 'billing_' ) || 0 === stripos( $connected, 'shipping_' ) ) {
							$customer->update_meta_data( $connected, $value['value'] );
						}
					}

					/**
					 * Action hook to adjust customer before save.
					 *
					 * @since 3.0.0
					 */
					do_action( 'ywraq_checkout_update_customer', $customer, $filled_form_fields );

					$customer->save();

				}

			}

			if ( $customer_id && is_multisite() && is_user_logged_in() && ! is_user_member_of_blog() ) {
				add_user_to_blog( get_current_blog_id(), $customer_id, 'customer' );
			}

			return $customer_id;
		}

		/**
		 * Custom validation for fields
		 *
		 * @param array  $posted Array of posted params
		 *
		 * @param        $key
		 * @param        $field
		 *
		 * @return string
		 * @since  2.0.0
		 * @access public
		 * @author Emanuela Castorina
		 *
		 */
		public function validate_field( $posted, $key, $field ) {
			$message            = '';
			$force_registration = get_option( 'ywraq_force_user_to_register', 'no' ) == 'yes';
			if ( $field['required'] && ( ( ( 'ywraq_upload' != $field['type'] && 'state' != $field['type'] ) && ( ! isset( $posted[ $key ] ) || $posted[ $key ] == '' ) ) || ( 'ywraq_upload' == $field['type'] && $field['required'] && ! isset( $_FILES[ $key ]['name'] ) && $_FILES[ $key ] == '' ) ) && ( 'account_password' != $key || ( ( 'account_password' == $key ) && ( $force_registration || ! empty( $posted['createaccount'] ) ) ) ) ) {
				$message .= sprintf( __( '%s is required.', 'yith-woocommerce-request-a-quote' ), '<strong>' . $field['label'] . '</strong>' );
			}


			if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
				foreach ( $field['validate'] as $rule ) {
					switch ( $rule ) {
						case 'email':
							$email = sanitize_email( strtolower( $posted[ $key ] ) );
							if ( ! is_email( $email ) ) {
								$message .= sprintf( __( ' %s is not a valid email address.', 'yith-woocommerce-request-a-quote' ), '<strong>' . $field['label'] . '</strong>' );
							}
							break;
						case 'phone' :
							if ( ! WC_Validation::is_phone( $posted[ $key ] ) ) {
								$message .= sprintf( __( ' %s is not a valid phone number.', 'yith-woocommerce-request-a-quote' ), '<strong>' . $field['label'] . '</strong>' );
							}
							break;
						case 'file':
							if ( isset( $_FILES[ $key ]['name'] ) ) {
								if ( ! empty( $field['max_filesize'] ) && $field['max_filesize'] * 1048576 < $_FILES[ $key ]['size'] ) {
									$message .= sprintf( __( ' %s is greater than %s.', 'yith-woocommerce-request-a-quote' ), '<strong>' . $field['label'] . '</strong>', $field['max_filesize'] . 'MB' );
								}

								if ( ! function_exists( 'wp_handle_upload' ) ) {
									require_once( ABSPATH . 'wp-admin/includes/file.php' );
								}

								$mime_type = ywraq_get_upload_mime_types( $field['upload_allowed_extensions'] );

								$upload_overrides = array( 'test_form' => false, 'mimes' => $mime_type );
								$moved_file       = wp_handle_upload( $_FILES[ $key ], $upload_overrides );

								if ( $moved_file && ! isset( $moved_file['error'] ) ) {
									$this->attachments[ $key ] = $moved_file;
								} else {
									$message .= ' ' . $field['label'] . ' ' . $moved_file['error'];
								}
							}

							break;
						default:
							$message .= apply_filters( 'ywraq_default_form_validate_field', '', $posted, $key, $field );
							break;
					}
				}
			} else {
				if ( 'ywraq_upload' == $field['type'] ) {
					if ( isset( $_FILES[ $key ]['name'] ) ) {
						if ( ! function_exists( 'wp_handle_upload' ) ) {
							require_once( ABSPATH . 'wp-admin/includes/file.php' );
						}

						$upload_overrides = array( 'test_form' => false );
						$moved_file       = wp_handle_upload( $_FILES[ $key ], $upload_overrides );

						if ( $moved_file && ! isset( $moved_file['error'] ) ) {
							$this->attachments[ $key ] = $moved_file;
						} else {
							$message .= ' ' . $field['label'] . ' ' . $moved_file['error'];
						}
					}
				} elseif ( 'state' == $field['type'] ) {

					$form_fields = ywraq_get_form_fields();
					$country     = '';

					foreach ( $form_fields as $field_key => $field_args ) {
						if ( $field_args['type'] == 'country' ) {
							$country = $posted[ $field_key ];
							break;
						}
					}

					if ( $country != '' ) {
						$valid_states = WC()->countries->get_states( $country );


						if ( ! empty( $valid_states ) && is_array( $valid_states ) && count( $valid_states ) > 0 ) {
							$valid_state_values = array_map( 'wc_strtoupper', array_flip( array_map( 'wc_strtoupper', $valid_states ) ) );
							$posted[ $key ]     = wc_strtoupper( $posted[ $key ] );

							if ( isset( $valid_state_values[ $posted[ $key ] ] ) ) {
								// With this part we consider state value to be valid as well, convert it to the state key for the valid_states check below.
								$posted[ $key ] = $valid_state_values[ $posted[ $key ] ];
							}

							if ( ! in_array( $posted[ $key ], $valid_state_values, true ) ) {
								$message .= sprintf( __( '%s is required.', 'yith-woocommerce-request-a-quote' ), '<strong>' . $field['label'] . '</strong>' );
							}
						}

					}


				}


			}

			return ltrim( $message );
		}

		/**
		 * Get all errors in HTML mode or simple string.
		 *
		 * @param      $errors
		 * @param bool $html
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public function get_errors( $errors, $html = true ) {
			return implode( ( $html ? '<br />' : ', ' ), $errors );
		}

		/**
		 * Call the template to show the form
		 *
		 * @param $args
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_form_template( $args ) {
			$default_args = array(
				'fields'                  => ywraq_get_form_fields(),
				'registration_is_enabled' => get_option( 'ywraq_add_user_registration_check', 'no' ),
				'force_registration'      => get_option( 'ywraq_force_user_to_register', 'no' ),
			);

			if ( 'yes' == $default_args['registration_is_enabled'] ) {
				$checkout                       = WC_Checkout::instance();
				$default_args['account_fields'] = $checkout->get_checkout_fields( 'account' );
			}

			$args = array_merge( $args, $default_args );

			wc_get_template( 'request-quote-default-form.php', $args, '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
		}

		/**
		 * Gets the value either from the posted data, or from the users meta data.
		 *
		 * @param        $key
		 * @param        $field
		 *
		 * @param string $default
		 * @return string
		 * @throws Exception
		 */
		public function get_value( $key, $field, $default = '' ) {
			$value = $default;

			if ( ! empty( $_POST[ $key ] ) ) {
				return wc_clean( $_POST[ $key ] );
			} else {
				if ( 'yes' == get_option( 'ywraq_autocomplete_default_form', 'no' ) ) {
					$input = isset( $field['connect_to_field'] ) ? $field['connect_to_field'] : '';

					if ( is_null( WC()->customer ) ) {
						WC()->customer = new WC_Customer( get_current_user_id(), false );
					}

					if ( is_callable( array( WC()->customer, "get_$input" ) ) ) {
						$value = WC()->customer->{"get_$input"}() ? WC()->customer->{"get_$input"}() : null;
					} elseif ( WC()->customer->meta_exists( $input ) ) {
						$value = WC()->customer->get_meta( $input, true );
					}

					$input = isset( $field['connect_to_field'] ) ? $field['connect_to_field'] : '';
					$value = '';
					if ( is_callable( array( WC()->customer, "get_$input" ) ) ) {
						$value = WC()->customer->{"get_$input"}() ? WC()->customer->{"get_$input"}() : null;
					} elseif ( WC()->customer->meta_exists( $input ) ) {
						$value = WC()->customer->get_meta( $input, true );
					}
				}
			}

			return apply_filters( 'ywraq_get_default_form_field_' . $key, $value, $key, $field );
		}

		/**
		 * Add order meta from the request.
		 *
		 * @param $attr
		 * @param $order_id
		 * @param $raq
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_order_metas( $attr, $order_id, $raq ) {

			if ( ! isset( $raq['user_name'] ) ) {
				return;
			}
			$other_email_content = '';
			$other_fields        = array();
			$order               = wc_get_order( $order_id );

			//default fields
			$attr['ywraq_customer_name']    = wc_clean( wp_unslash( $raq['user_name'] ) );
			$attr['ywraq_customer_message'] = isset( $raq['message'] ) ? wc_sanitize_textarea( wp_unslash( $raq['message']['value'] ) ) : '';
			$attr['ywraq_customer_email'] = isset( $raq['email']['value'] ) ? sanitize_email( wp_unslash( $raq['email']['value'] ) ) : '';
			$attr['_raq_request']         = $raq;

			foreach ( $raq as $name => $item ) {
				if ( isset( $item['connect_to_field'] ) && ! empty( $item['connect_to_field'] ) ) {
					$attr[ '_' . $item['connect_to_field'] ] = stripslashes( $item['value'] );
				} elseif ( isset( $item['value'] ) && isset( $item['type'] ) && 'ywraq_heading' != $item['type'] && 'message' != $name && 'ywraq_upload' != $item['type'] ) {
					$key   = apply_filters( 'ywraq_other_email_content_key', isset( $item['label'] ) ? $item['label'] : $name );
					$value = is_array( $item['value'] ) ? implode( ', ', $item['value'] ) : $item['value'];

					if ( $item['type'] == 'ywraq_acceptance' ) {
						$value = ( $value == 'on' ? __( 'Accepted', 'yith-woocommerce-request-a-quote' ) : __( 'Not Accepted', 'yith-woocommerce-request-a-quote' ) );
					}
					$value                = wc_clean( wp_unslash( $value ) );
					$other_email_content  .= sprintf( '<strong>%s</strong>: %s<br>', $key, $value );
					$other_fields[ $key ] = $value;

				}
			}

			$attachments = ywraq_get_default_form_attachment( $raq, 'url' );
			if ( ! empty( $attachments ) ) {
				$attr['ywraq_customer_attachment'] = $attachments;
			}

			$attr['ywraq_other_email_fields']  = $other_fields;
			$attr['ywraq_other_email_content'] = $other_email_content;

			return $attr;
		}

		/**
		 * Wpml string translation.
		 *
		 * @param $fields
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function filter_wpml_strings( $fields ) {

			if ( $fields ) {

				foreach ( $fields as $key => &$single ) {
					$single = ywraq_field_filter_wpml_strings( $key, $single );
				}
			}

			return $fields;
		}

	}

	/**
	 * Unique access to instance of YITH_YWRAQ_Default_Form class
	 *
	 * @return YITH_YWRAQ_Default_Form
	 */
	function YITH_YWRAQ_Default_Form() {
		return YITH_YWRAQ_Default_Form::get_instance();
	}
}
