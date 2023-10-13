<?php
/**
 * YITH_WCBK_Search_Form Class
 *
 * @package YITH\Booking\Classes
 * @author  YITH <plugins@yithemes.com>
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Search_Form' ) ) {
	/**
	 * Class YITH_WCBK_Search_Form
	 */
	class YITH_WCBK_Search_Form extends YITH_WCBK_Data {

		/**
		 * The object type
		 *
		 * @var string
		 */
		protected $object_type = 'search_form';

		/**
		 * The data store.
		 *
		 * @var YITH_WCBK_Search_Form_Data_Store
		 */
		protected $data_store;

		/**
		 * Instance number.
		 *
		 * @var int
		 */
		protected static $instance_number = 0;

		/**
		 * Current instance number;
		 *
		 * @var int
		 */
		private $current_instance_number;

		/**
		 * Data.
		 *
		 * @var array
		 */
		protected $data = array(
			'name'                        => '',
			'fields'                      => array(
				'search'     => array(
					'enabled' => 'no',
					'label'   => '',
				),
				'location'   => array(
					'enabled'       => 'yes',
					'default_range' => 30,
					'show_range'    => 'yes',
				),
				'categories' => array(
					'enabled' => 'no',
				),
				'tags'       => array(
					'enabled' => 'no',
				),
				'date'       => array(
					'enabled' => 'yes',
					'type'    => '',
				),
				'persons'    => array(
					'enabled' => 'yes',
					'type'    => 'persons',
				),
				'services'   => array(
					'enabled' => 'yes',
					'type'    => '',
				),
			),
			'layout'                      => 'vertical',
			'colors'                      => array(
				'background' => 'transparent',
				'text'       => '#333333',
			),
			'search_button_colors'        => array(
				'background'       => '#3b4b56',
				'text'             => '#ffffff',
				'background-hover' => '#2e627c',
				'text-hover'       => '#ffffff',
			),
			'search_button_border_radius' => array(
				'dimensions' => array(
					'top-left'     => 5,
					'top-right'    => 5,
					'bottom-right' => 5,
					'bottom-left'  => 5,
				),
				'unit'       => 'px',
				'linked'     => 'yes',
			),
			'show_results'                => 'popup',
		);

		/**
		 * Meta to prop map.
		 *
		 * @var array
		 * @since 3.0.0
		 */
		private $meta_to_prop = array(
			'_yith_wcbk_admin_search_form_fields' => 'fields',
			'_layout'                             => 'layout',
			'_colors'                             => 'colors',
			'_search-button-colors'               => 'search_button_colors',
			'_show-results'                       => 'show_results',
			'_search_button_border_radius'        => 'search_button_border_radius',
		);

		/**
		 * YITH_WCBK_Search_Form constructor.
		 *
		 * @param int|YITH_WCBK_Search_Form|WP_Post $search_form The object.
		 *
		 * @throws Exception If passed search form is invalid.
		 */
		public function __construct( $search_form ) {
			parent::__construct( $search_form );

			$this->data_store = WC_Data_Store::load( 'yith-booking-search-form' );

			if ( is_numeric( $search_form ) && $search_form > 0 ) {
				$this->set_id( $search_form );
			} elseif ( $search_form instanceof self ) {
				$this->set_id( absint( $search_form->get_id() ) );
			} elseif ( ! empty( $search_form->ID ) ) {
				$this->set_id( absint( $search_form->ID ) );
			} else {
				$this->set_object_read( true );
			}

			if ( $this->get_id() > 0 ) {
				$this->data_store->read( $this );
			}

			self::$instance_number ++;
			$this->current_instance_number = self::$instance_number;
		}

		/**
		 * Magic Getter for backward compatibility.
		 *
		 * @param string $key The key.
		 *
		 * @return mixed
		 */
		public function __get( $key ) {
			yith_wcbk_doing_it_wrong( $key, 'Search form properties should not be accessed directly.', '3.0.0' );

			if ( 'id' === $key ) {
				return $this->get_id();
			}

			return null;
		}

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		|
		| Methods for getting data from the product object.
		*/

		/**
		 * Return the name
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_name( $context = 'view' ) {
			return $this->get_prop( 'name', $context );
		}

		/**
		 * Get fields
		 *
		 * @return array
		 */
		public function get_fields() {
			return $this->get_prop( 'fields' );
		}

		/**
		 * Get colors
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function get_colors() {
			return $this->get_prop( 'colors' );
		}

		/**
		 * Get search_button_colors
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function get_search_button_colors() {
			return $this->get_prop( 'search_button_colors' );
		}

		/**
		 * Get search_button_border_radius
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function get_search_button_border_radius() {
			return $this->get_prop( 'search_button_border_radius' );
		}

		/**
		 * Get show_results
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_show_results() {
			return $this->get_prop( 'show_results' );
		}

		/**
		 * Get layout
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_layout() {
			return $this->get_prop( 'layout' );
		}

		/*
		|--------------------------------------------------------------------------
		| Setters
		|--------------------------------------------------------------------------
		|
		| Functions for setting product data.
		*/

		/**
		 * Set the name
		 *
		 * @param string $value The value to set.
		 *
		 * @since 4.0.0
		 */
		public function set_name( $value ) {
			$this->set_prop( 'name', $value );
		}

		/**
		 * Set fields
		 *
		 * @param array $value The fields.
		 *
		 * @since 3.0.0
		 */
		public function set_fields( $value ) {
			$value = is_array( $value ) ? $value : array();

			// Fill the array with default fields, if missing. Don't use wp_parse_args/array_merge since fields are custom-sorted.
			foreach ( $this->default_data['fields'] as $key => $field ) {
				if ( ! isset( $value[ $key ] ) ) {
					$field['enabled'] = 'no'; // If not exist, it'll be disabled.
					$value[ $key ]    = $field;
				}
			}

			// Parse fields based on active modules.
			if ( ! yith_wcbk_is_services_module_active() ) {
				unset( $value['services'] );
			}

			if ( ! yith_wcbk_is_people_module_active() ) {
				unset( $value['persons'] );
			}

			if ( ! yith_wcbk_is_google_maps_module_active() ) {
				unset( $value['location'] );
			}

			$this->set_prop( 'fields', $value );
		}

		/**
		 * Set colors
		 *
		 * @param array $value The colors.
		 */
		public function set_colors( $value ) {
			$value = is_array( $value ) ? $value : array();
			$value = wp_parse_args( $value, $this->default_data['colors'] );

			$this->set_prop( 'colors', $value );
		}

		/**
		 * Set search_button_colors
		 *
		 * @param array $value The search button colors.
		 */
		public function set_search_button_colors( $value ) {
			$value = is_array( $value ) ? $value : array();
			$value = wp_parse_args( $value, $this->default_data['search_button_colors'] );

			$this->set_prop( 'search_button_colors', $value );
		}

		/**
		 * Set search_button_border_radius
		 *
		 * @param array $value The search button border radius.
		 */
		public function set_search_button_border_radius( $value ) {
			$value = is_array( $value ) ? $value : array();
			$value = wp_parse_args( $value, $this->default_data['search_button_border_radius'] );

			$this->set_prop( 'search_button_border_radius', $value );
		}

		/**
		 * Set show_results
		 *
		 * @param array $value The show-results value.
		 */
		public function set_show_results( $value ) {
			$allowed = array( 'popup', 'shop' );
			$value   = in_array( $value, $allowed, true ) ? $value : $this->default_data['show_results'];
			$this->set_prop( 'show_results', $value );
		}

		/**
		 * Set layout
		 *
		 * @param string $value The layout.
		 */
		public function set_layout( $value ) {
			$allowed = array( 'vertical', 'horizontal' );
			$value   = in_array( $value, $allowed, true ) ? $value : $this->default_data['layout'];
			$this->set_prop( 'layout', $value );
		}

		/*
		|--------------------------------------------------------------------------
		| Non CRUD Methods
		|--------------------------------------------------------------------------
		|
		*/

		/**
		 * Return the current instance number.
		 *
		 * @return int
		 */
		public function get_current_instance_number() {
			return $this->current_instance_number;
		}

		/**
		 * Return an unique identifier
		 * id-current_instance_number
		 *
		 * @return string
		 */
		public function get_unique_id() {
			return $this->get_id() . '-' . $this->get_current_instance_number();
		}

		/**
		 * Get style settings.
		 *
		 * @return array
		 */
		public function get_styles() {
			return array(
				'style'                => 'default', // Deprecated 3.0.0. Kept for backward compatibility.
				'colors'               => $this->get_colors(),
				'search-button-colors' => $this->get_search_button_colors(),
			);
		}

		/**
		 * Retrieve the CSS custom style.
		 *
		 * @return string
		 */
		public function get_css_style() {
			$form_id   = $this->get_id();
			$selectors = array(
				'form'                => '.yith-wcbk-booking-search-form-' . esc_attr( $form_id ),
				'widget'              => '.yith_wcbk_booking_search_form_widget-' . esc_attr( $form_id ),
				'search_button'       => '.yith-wcbk-booking-search-form-submit',
				'search_button:hover' => '.yith-wcbk-booking-search-form-submit:hover',
			);

			$colors               = $this->get_colors();
			$search_colors        = $this->get_search_button_colors();
			$search_border_radius = $this->get_search_button_border_radius();
			$search_border_radius = yith_plugin_fw_parse_dimensions( $search_border_radius );
			$search_border_radius = implode( ' ', $search_border_radius );

			$styles = array(
				array(
					'parents'   => array( $selectors['form'], $selectors['widget'] ),
					'selector'  => null,
					'styles'    => array(
						'background' => $colors['background'],
						'color'      => $colors['text'],
					),
					'important' => true,
				),
				array(
					'parents'   => array( $selectors['form'] ),
					'selector'  => $selectors['search_button'],
					'styles'    => array(
						'background'    => $search_colors['background'],
						'color'         => $search_colors['text'],
						'border-radius' => $search_border_radius,
					),
					'important' => true,
				),
				array(
					'parents'   => array( $selectors['form'] ),
					'selector'  => $selectors['search_button:hover'],
					'styles'    => array(
						'background' => $search_colors['background-hover'],
						'color'      => $search_colors['text-hover'],
					),
					'important' => true,
				),
			);

			return yith_wcbk_css( $styles );
		}

		/**
		 * Print the search form
		 *
		 * @param array $args Arguments.
		 */
		public function output( $args = array() ) {
			static $printed_css = array();

			if ( ! in_array( $this->get_id(), $printed_css, true ) ) {
				echo '<style>' . $this->get_css_style() . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$printed_css[] = $this->get_id();
			}

			$defaults            = array(
				'cat' => '',
			);
			$args                = wp_parse_args( $args, $defaults );
			$args['search_form'] = $this;

			yith_wcbk_get_module_template( 'search-forms', 'booking-search-form.php', $args, 'booking/search-form/' );
		}

		/*
		|--------------------------------------------------------------------------
		| Deprecated Methods
		|--------------------------------------------------------------------------
		|
		*/

		/**
		 * Is this valid?
		 *
		 * @return bool
		 * @deprecated 4.0.0
		 */
		public function is_valid() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Search_Form::is_valid', '4.0.0' );

			return ! empty( $this->get_id() ) && get_post_type( $this->get_id() ) === YITH_WCBK_Post_Types::SEARCH_FORM;
		}

		/**
		 * Retrieve the post object.
		 *
		 * @return null|WP_Post
		 * @deprecated 3.0.0
		 */
		public function get_post_data() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Search_Form::get_post_data', '3.0.0', 'get_post' );

			return get_post( $this->get_id() );
		}

		/**
		 * Get options.
		 *
		 * @return array
		 * @deprecated 3.0.0
		 */
		public function get_options() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Search_Form::get_options', '3.0.0', 'specific getters' );

			return array(
				'show-results' => $this->get_show_results(),
			);
		}
	}
}
