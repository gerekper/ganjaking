<?php
namespace TheplusAddons;

use TheplusAddons\Controls\Theplus_Query as Query;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\QueryControl\Theplus_Module
 *
 */
class Theplus_Module {

	/**
	 * Displayed IDs
	 *
	 * @var    array
	 */
	public static $displayed_ids = [];

	/**
	 * Module constructor.
	 *
	 * @param array $args
	 */
	public function __construct() {
		
		$this->add_actions();
	}

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @return string
	 */
	public function get_name() {
		return 'query-control';
	}

	/**
	 * Add Actions
	 * 
	 * Registeres actions to Elementor hooks
	 *
	 * @return void
	 */
	protected function add_actions() {
		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
	}

	/**
	 * Load function depending on ajax query
	 *
	 * @return array
	 */
	public function plus_get_filter_autocomplete( array $data ) {

		if ( empty( $data['query_type'] ) || empty( $data['q'] ) ) {
			throw new \Exception( 'Bad Request' );
		}
		
		$results = call_user_func( [ $this, 'get_autocomplete_for_' . $data['query_type'] ], $data );

		return [
			'results' => $results,
		];
	}

	/**
	 * Get search values for 'posts' query
	 *
	 * @return array
	 */
	protected function get_autocomplete_for_posts( $data ) {
		$results = [];

		$query_params = [
			'post_type' 		=> $data['object_type'],
			's' 				=> $data['q'],
			'posts_per_page' 	=> -1,
		];

		if ( 'attachment' === $query_params['post_type'] ) {
			$query_params['post_status'] = 'inherit';
		}

		$query = new \WP_Query( $query_params );

		foreach ( $query->posts as $post ) {
			$results[] = [
				'id' 	=> $post->ID,
				'text' 	=> $post->post_title,
			];
		}

		return $results;
	}

	/**
	 * Get search values for taxonomy 'terms' query
	 *
	 * @return array
	 */
	protected function get_autocomplete_for_terms( $data ) {
		$results = [];

		$taxonomies = get_object_taxonomies('');

		$query_params = [
			'taxonomy' 		=> $taxonomies,
			'search' 		=> $data['q'],
			'hide_empty' 	=> false,
		];

		$terms = get_terms( $query_params );

		foreach ( $terms as $term ) {
			$taxonomy = get_taxonomy( $term->taxonomy );

			$results[] = [
				'id' 	=> $term->term_id,
				'text' 	=> $taxonomy->labels->singular_name . ': ' . $term->name,
			];
		}

		return $results;
	}

	/**
	 * Get search values for 'authors' query
	 *
	 * @return array
	 */
	protected function get_autocomplete_for_authors( $data ) {
		$results = [];

		$query_params = [
			'who' 					=> 'authors',
			'has_published_posts' 	=> true,
			'fields' 				=> [
				'ID',
				'display_name',
			],
			'search' 				=> '*' . $data['q'] . '*',
			'search_columns' 		=> [
				'user_login',
				'user_nicename',
			],
		];

		$user_query = new \WP_User_Query( $query_params );

		foreach ( $user_query->get_results() as $author ) {
			$results[] = [
				'id' 	=> $author->ID,
				'text' 	=> $author->display_name,
			];
		}

		return $results;
	}

	/**
	 * Get search values for 'posts' query
	 *
	 */
	protected function get_autocomplete_for_acf( $data ) {
		$results 	= [];
		$options 	= $data['query_options'];

		$query_params = [
			'post_type' 		=> 'acf-field',
			'post_status'		=> 'publish',
			'search_title_name' => $data['q'],
			'posts_per_page' 	=> -1,
		];

		$query = new \WP_Query( $query_params );

		foreach ( $query->posts as $post ) {

			$field_settings 	= unserialize( $post->post_content );
			$field_type 		= $field_settings['type'];

			if ( ! $this->check_valid_field_type( $options['field_type'], $field_type ) ) {
				continue;
			}

			$display_title			= $post->post_title;
			if (strpos(strtolower($display_title), strtolower($data['q'])) !== false) {
				$display_type 		= ( $options['show_type'] ) ? $this->get_title() : '';
				$display_field_type = ( $options['show_field_type'] ) ? $this->get_acf_field_check_label( $field_type ) : '';
				$display_field_group 		= ! empty( $options['show_group'] ) && $options['show_group'];
				$display_group 		= $display_field_group ? '(' . get_the_title( $post->post_parent ) . ')' : '';
				$display_title 			= ( $options['show_type'] || $options['show_field_type'] ) ? ': ' . $display_title : $display_title;

				$results[] = [
					'id' 	=> $post->post_name,
					'text' 	=> sprintf( '%1$s %2$s %3$s %4$s', $display_type, $display_field_type, $display_title , $display_group),
				];
			}
			
		}

		return $results;
	}
	
	protected function check_valid_field_type( $valid_checked, $type ) {
		if ( ! $valid_checked || ! $type ) {
			return false;
		}

		$field_types = $this->acf_get_fields_list();

		if ( is_array( $valid_checked ) ) {
			foreach ( $valid_checked as $valid_type ) {

				if ( is_array( $field_types[ $valid_type ] ) ) {
					if ( in_array( $type, $field_types[ $valid_type ] ) ) {
						return true;
					}
				} else {
					if ( $type === $field_types[ $valid_type ] ) {
						return true;
					}
				}
			}
		} else if ( in_array( $type, $field_types[ $valid_checked ] ) ) {
			return true;
		}

		return false;
	}
	
	public function acf_get_fields_list() {
		return [
			'textual' => [
				'text',
				'textarea',				
				'email',
				'url',
				'number',
				'password',
				'range',
			],
			'select' => [
				'select',
				'checkbox',
				'radio',
				'acfe_image_selector',
			],
			'button_group' => [				
				'button_group',
			],
			'date' => [
				'date_picker',
				'date_time_picker',
			],			
			'boolean' => [
				'true_false',
			],
			'post' => [
				'post_object',
				'relationship',
			],
			'taxonomy' => [
				'taxonomy',
			],
		];
	}
	
	/**
	 * Get search acf field by label
	 *
	 */
	public function get_acf_field_check_label( $field_type ) {
		if ( ! function_exists( 'acf_get_field_type' ) )
			return;

		$field_type_object = acf_get_field_type( $field_type );

		if ( $field_type_object )
			return $field_type_object->label;

		return false;
	}

	/**
	 * Load function to get value titles depending on ajax query
	 *
	 * @return array
	 */
	public function plus_get_control_value_titles( $request ) {
		$results = call_user_func( [ $this, 'get_value_titles_for_' . $request['query_type'] ], $request );

		return $results;
	}

	/**
	 * Get values for 'posts' query
	 *
	 */
	protected function get_value_titles_for_posts( $request ) {
		$ids = (array) $request['id'];
		$results = [];

		$query = new \WP_Query( [
			'post_type' 		=> 'any',
			'post__in' 			=> $ids,
			'posts_per_page' 	=> -1,
		] );

		foreach ( $query->posts as $post ) {
			$results[ $post->ID ] = $post->post_title;
		}

		return $results;
	}

	/**
	 * Get values for 'terms' query
	 *
	 */
	protected function get_value_titles_for_terms( $request ) {
		$ids = (array) $request['id'];
		$results = [];

		$query_params = [
			'include' 		=> $ids,
		];

		$terms = get_terms( $query_params );

		foreach ( $terms as $term ) {
			$results[ $term->term_id ] = $term->name;
		}

		return $results;
	}

	/**
	 * Get values for 'authors' query
	 *
	 */
	protected function get_value_titles_for_authors( $request ) {
		$ids = (array) $request['id'];
		$results = [];

		$query_params = [
			'who' 					=> 'authors',
			'has_published_posts' 	=> true,
			'fields' 				=> [
				'ID',
				'display_name',
			],
			'include' 				=> $ids,
		];

		$user_query = new \WP_User_Query( $query_params );

		foreach ( $user_query->get_results() as $author ) {
			$results[ $author->ID ] = $author->display_name;
		}

		return $results;
	}
	
	/**
	 * Get values for 'ACF' query
	 *
	 */
	protected function get_value_titles_for_acf( $request ) {
		$keys 		= (array) $request['id'];
		$results 	= [];
		$options 	= $request['query_options'];

		$query = new \WP_Query( [
			'post_type' 		=> 'acf-field',
			'post_name__in' 	=> $keys,
			'posts_per_page' 	=> -1,
		] );

		foreach ( $query->posts as $post ) {
			$field_settings 	= unserialize( $post->post_content );
			$field_type 		= $field_settings['type'];
			$display 			= $post->post_title;
			$display_type 		= ( !empty($options['show_type']) && isset($options['show_type']) ) ? $this->get_title() : '';
			$display_field_type = ( $options['show_field_type'] ) ? $this->get_acf_field_check_label( $field_type ) : '';
			$display 			= ( $options['show_type'] || $options['show_field_type'] ) ? ': ' . $display : $display;

			$results[ $post->post_name ] = sprintf( '%1$s %2$s %3$s', $display_type, $display_field_type, $display );
		}

		return $results;
	}
	/**
	 * Register Elementor Ajax Actions
	 *
	 */
	public function register_ajax_actions( $ajax_manager ) {
		$ajax_manager->register_ajax_action( 'plus_query_control_value_titles', [ $this, 'plus_get_control_value_titles' ] );
		$ajax_manager->register_ajax_action( 'plus_query_control_filter_autocomplete', [ $this, 'plus_get_filter_autocomplete' ] );
	}
}

new Theplus_Module();