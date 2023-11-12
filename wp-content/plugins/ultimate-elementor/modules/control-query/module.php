<?php
/**
 * Search ACF field by select2 control query.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\ControlQuery;

use UltimateElementor\Base\Module_Base;
use UltimateElementor\Modules\ControlQuery\Types\Uae_Control_Query;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * \Modules\QueryControl\Theplus_Module
 */
class Module extends Module_Base {

	/**
	 * Control ID.
	 */
	const QUERY_CONTROL_ID = 'uael-control-query';

	/**
	 * Module should load or not.
	 *
	 * @since 1.35.1
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_enable() {
		return true;
	}

	/**
	 * Module constructor.
	 *
	 * @since 1.35.1
	 * @access public
	 */
	public function __construct() {
		parent::__construct();

		$this->add_actions();
	}

	/**
	 * Get Name
	 *
	 * Get the name of the module
	 *
	 * @return string
	 * @since 1.35.1
	 */
	public function get_name() {
		return 'control-query';
	}

	/**
	 * Title of the field.
	 *
	 * @return string|void
	 * @since 1.35.1
	 */
	public function get_title() {
		return __( 'ACF', 'uael' );
	}

	/**
	 * Load function depending on ajax query
	 *
	 * @param array $data Search query.
	 *
	 * @return array
	 * @throws \Exception Exception.
	 * @since 1.35.1
	 */
	public function uael_get_filter_autocomplete( array $data ) {

		if ( empty( $data['query_type'] ) || empty( $data['q'] ) ) {
			throw new \Exception( 'Bad Request' );
		}

		$results = call_user_func( array( $this, 'get_autocomplete_for_' . $data['query_type'] ), $data );

		return array(
			'results' => $results,
		);
	}

	/**
	 * Get search values for 'posts' query
	 *
	 * @param array $data Control data.
	 *
	 * @return array
	 * @since 1.35.1
	 */
	protected function get_autocomplete_for_acf( $data ) {
		$results = array();
		$options = $data['query_options'];

		$query_params = array(
			'post_type'         => 'acf-field',
			'post_status'       => 'publish',
			'search_title_name' => $data['q'],
			'posts_per_page'    => -1,
		);

		$query = new \WP_Query( $query_params );

		foreach ( $query->posts as $post ) {

			$field_settings = unserialize( $post->post_content ); // phpcs:ignore
			$field_type     = $field_settings['type'];

			if ( ! $this->check_valid_field_type( $options['field_type'], $field_type ) ) {
				continue;
			}

			$display_title      = $post->post_title;
			$display_type       = ( $options['show_type'] ) ? $this->get_title() : '';
			$display_field_type = ( $options['show_field_type'] ) ? $this->get_acf_field_check_label( $field_type ) : '';
			$display_title      = ( $options['show_type'] || $options['show_field_type'] ) ? ': ' . $display_title : $display_title;

			$results[] = array(
				'id'   => $post->post_name,
				'text' => sprintf( '%1$s %2$s %3$s', $display_type, $display_field_type, $display_title ),
			);
		}

		return $results;
	}

	/**
	 * Checks if given control field types match
	 * component field types
	 *
	 * @since  1.35.1
	 * @param  array $valid_checked Sets of valid control field types.
	 * @param  array $type Component field type to check against.
	 * @return bool
	 */
	protected function check_valid_field_type( $valid_checked, $type ) {
		if ( ! $valid_checked || ! $type ) {
			return false;
		}

		$field_types = $this->acf_get_fields_list();

		if ( is_array( $valid_checked ) ) {
			foreach ( $valid_checked as $valid_type ) {

				if ( is_array( $field_types[ $valid_type ] ) ) {
					if ( in_array( $type, $field_types[ $valid_type ], true ) ) {
						return true;
					}
				} else {
					if ( $type === $field_types[ $valid_type ] ) {
						return true;
					}
				}
			}
		} elseif ( in_array( $type, $field_types[ $valid_checked ], true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns array of component field types organized
	 * based on categories
	 *
	 * @return array
	 * @since  1.35.1
	 */
	public function acf_get_fields_list() {
		return array(
			'textual'  => array(
				'text',
				'textarea',
				'email',
				'url',
				'number',
				'password',
				'range',
			),
			'select'   => array(
				'select',
				'checkbox',
				'radio',
			),
			'date'     => array(
				'date_picker',
				'date_time_picker',
			),
			'boolean'  => array(
				'true_false',
			),
			'post'     => array(
				'post_object',
				'relationship',
			),
			'taxonomy' => array(
				'taxonomy',
			),
		);
	}

	/**
	 * Get search acf field by label.
	 *
	 * @param array $field_type Field types.
	 *
	 * @return false|void
	 * @since 1.35.1
	 */
	public function get_acf_field_check_label( $field_type ) {
		if ( ! function_exists( 'acf_get_field_type' ) ) {
			return;
		}

		$field_type_object = acf_get_field_type( $field_type );

		if ( $field_type_object ) {
			return $field_type_object->label;
		}

		return false;
	}

	/**
	 * Load function to get value titles depending on ajax query
	 *
	 * @param mixed $request Ajax request.
	 * @since 1.35.1
	 * @return array
	 */
	public function uael_get_control_value_titles( $request ) {

		$results = call_user_func( array( $this, 'get_value_titles_for_' . $request['query_type'] ), $request );

		return $results;
	}

	/**
	 * Get values for 'ACF' query
	 *
	 * @param mixed $request Ajax request.
	 * @since 1.35.1
	 */
	protected function get_value_titles_for_acf( $request ) {
		$keys    = (array) $request['id'];
		$results = array();
		$options = $request['query_options'];

		$query = new \WP_Query(
			array(
				'post_type'      => 'acf-field',
				'post_name__in'  => $keys,
				'posts_per_page' => -1,
			)
		);

		foreach ( $query->posts as $post ) {
			$field_settings     = unserialize( $post->post_content ); // phpcs:ignore
			$field_type         = $field_settings['type'];
			$display            = $post->post_title;
			$display_type       = ( $options['show_type'] ) ? $this->get_title() : '';
			$display_field_type = ( $options['show_field_type'] ) ? $this->get_acf_field_check_label( $field_type ) : '';
			$display            = ( $options['show_type'] || $options['show_field_type'] ) ? ': ' . $display : $display;

			$results[ $post->post_name ] = sprintf( '%1$s %2$s %3$s', $display_type, $display_field_type, $display );
		}

		return $results;
	}



	/**
	 * Register Elementor Ajax Actions
	 *
	 * @param mixed $ajax_manager Elementor ajax manager.
	 * @since 1.35.1
	 */
	public function register_ajax_actions( $ajax_manager ) {
		$ajax_manager->register_ajax_action( 'uael_query_control_value_titles', array( $this, 'uael_get_control_value_titles' ) );
		$ajax_manager->register_ajax_action( 'uael_query_control_filter_autocomplete', array( $this, 'uael_get_filter_autocomplete' ) );
	}

	/**
	 * Add initial actions.
	 *
	 * @since 1.35.1
	 */
	protected function add_actions() {
		add_action( 'elementor/ajax/register_actions', array( $this, 'register_ajax_actions' ) );

	}

	/**
	 * Register the control query.
	 *
	 * @since 1.35.1
	 */
	public function register_controls() {
		$controls_manager = \Elementor\Plugin::$instance->controls_manager;
		$controls_manager->register( new Uae_Control_Query() );

	}
}

