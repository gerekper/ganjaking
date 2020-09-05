<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Rest.
 *
 * Here for any REST things.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Rest_Controller {

	const RETURN_TYPES_ALLOWED = array( 'exact', 'all', 'required', 'parents' );


	/**
	 * The instance.
	 *
	 * @var \wpbuddy\rich_snippets\Rest_Controller
	 *
	 * @since 2.0.0
	 */
	protected static $_instance = null;


	/**
	 * Get the singleton instance.
	 *
	 * Creates a new instance of the class if it does not exists.
	 *
	 * @return \wpbuddy\rich_snippets\Rest_Controller
	 *
	 * @since 2.0.0
	 */
	public static function instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}


	/**
	 * Magic function for cloning.
	 *
	 * Disallow cloning as this is a singleton class.
	 *
	 * @since 2.0.0
	 */
	protected function __clone() {
	}


	/**
	 * Magic method for setting upt the class.
	 *
	 * Disallow external instances.
	 *
	 * @since 2.0.0
	 */
	protected function __construct() {
	}


	/**
	 * Initializes admin stuff
	 *
	 * @since 2.0.0
	 */
	public static function init() {

		$instance = self::instance();

		# register routes
		$instance->register_routes();

		# load translations
		Admin_Controller::instance()->load_translations();
	}


	/**
	 * Registers the REST routes.
	 *
	 * @since 2.0.0
	 */
	protected function register_routes() {

		register_rest_route( 'wpbuddy/rich_snippets/v1', '/schemas/types', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'get_schema_types' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'q'    => array(
					'required'          => true,
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
				'page' => array(
					'sanitize_callback' => function ( $param ) {

						return absint( $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', '/schema', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'get_schema_type' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'type' => array(
					'required'          => true,
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', '/schema/examples', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'get_schema_type_examples' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'type' => array(
					'required'          => true,
					'sanitize_callback' => function ( $param ) {

						$v = sanitize_text_field( $param );
						if ( 1 !== preg_match( "#http(s)?:\/\/#", $v ) ) {
							$v = 'http://' . $v;
						}

						return $v;
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', '/schemas/recommended', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'get_recommended_schemas' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', '/schemas/properties', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'get_properties' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'schema_type' => array(
					'required'          => true,
					'sanitize_callback' => function ( $param ) {

						$v = sanitize_text_field( $param );
						if ( 1 !== preg_match( "#http(s)?:\/\/#", $v ) ) {
							$v = 'http://' . $v;
						}

						return $v;
					},
				),
				'return_type' => array(
					'required'          => true,
					'validate_callback' => function ( $param, $request, $key ) {

						return in_array( strtolower( $param ), self::RETURN_TYPES_ALLOWED );


					},
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( strtolower( $param ) );
					},
				),
				'q'           => array(
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v2', '/schemas/properties', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'get_properties_v2' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'schema_type'   => array(
					'required'          => true,
					'sanitize_callback' => function ( $param ) {

						$v = sanitize_text_field( $param );
						if ( 1 !== preg_match( "#http(s)?:\/\/#", $v ) ) {
							$v = 'http://' . $v;
						}

						return $v;
					},
				),
				'return_type'   => array(
					'required'          => true,
					'validate_callback' => function ( $param, $request, $key ) {

						return in_array( strtolower( $param ), self::RETURN_TYPES_ALLOWED );


					},
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( strtolower( $param ) );
					},
				),
				'q'             => array(
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
				'preconfigured' => array(
					'required'          => true,
					'default'           => false,
					'sanitize_callback' => function ( $param ) {

						return Helper_Model::instance()->string_to_bool( $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', '/schemas/property', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'get_property' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'property' => array(
					'required'          => true,
					'sanitize_callback' => function ( $param ) {

						$v = sanitize_text_field( $param );
						if ( 1 !== preg_match( "#http(s)?:\/\/#", $v ) ) {
							$v = 'http://' . $v;
						}

						return $v;
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', '/schemas/property/field-types', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'get_property_field_types' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'property' => array(
					'required'          => true,
					'sanitize_callback' => function ( $param ) {

						$v = sanitize_text_field( $param );
						if ( 1 !== preg_match( "#http(s)?:\/\/#", $v ) ) {
							$v = 'http://' . $v;
						}

						return $v;
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', '/schemas/properties/html', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( self::instance(), 'get_properties_html' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'properties'     => array(
					'validate_callback' => function ( $param, $request, $key ) {

						if ( ! is_array( $param ) ) {
							return new \WP_Error(
								'wpbuddy/rich_snippets/rest/param',
								_x( 'Please provide a list of properties.',
									'Thrown error on rest api when there was no list of properties found.',
									'rich-snippets-schema' )
							);
						}

						return $param;

					},
					'sanitize_callback' => function ( $param ) {

						return array_map( function ( $v ) {

							$v = sanitize_text_field( $v );
							if ( 1 !== preg_match( "#http(s)?:\/\/#", $v ) ) {
								$v = 'http://' . $v;
							}

							return $v;
						}, $param );
					},
				),
				'include_table'  => array(
					'sanitize_callback' => function ( $param ) {

						return filter_var( $param, FILTER_VALIDATE_BOOLEAN );
					},
				),
				'schema_type'    => array(
					'sanitize_callback' => function ( $param ) {

						$v = sanitize_text_field( $param );
						if ( 1 !== preg_match( "#http(s)?:\/\/#", $v ) ) {
							$v = 'http://' . $v;
						}

						return $v;
					},
				),
				'parent_type_id' => array(
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
				'post_id'        => array(
					'validate_callback' => function ( $param, $request, $key ) {

						# check if post exists
						return is_string( get_post_status( absint( $param ) ) );

					},
					'sanitize_callback' => function ( $param ) {

						return absint( $param );
					},
				),
				'snippet_id'     => array(
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
				'is_main_schema' => array(
					'sanitize_callback' => function ( $param ) {

						return Helper_Model::instance()->string_to_bool( $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'form_new', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'get_rich_snippets_form_new' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'post_id' => array(
					'validate_callback' => function ( $param, $request, $key ) {

						# check if post exists
						return is_string( get_post_status( absint( $param ) ) );

					},
					'sanitize_callback' => function ( $param ) {

						return absint( $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'snippets_forms', array(
			'methods'             => [ \WP_REST_Server::READABLE, \WP_REST_Server::CREATABLE ],
			'callback'            => array( self::instance(), 'get_rich_snippets_forms' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'post_id'   => array(
					'required'          => true,
					'validate_callback' => function ( $param, $request, $key ) {

						# check if post exists
						return is_string( get_post_status( absint( $param ) ) );

					},
					'sanitize_callback' => function ( $param ) {

						return absint( $param );
					},
				),
				'form_data' => array(
					'required'          => false,
					'default'           => [],
					'type'              => 'array',
					'validate_callback' => function ( $param, $request, $key ) {

						return is_array( $param );

					},
					'sanitize_callback' => function ( $param ) {

						$data = [];

						foreach ( $param as $key => $value ) {

							if ( ! ( isset( $value['id'], $value['properties'] ) ) ) {
								continue;
							}

							$data[ $key ] = [
								'id'   => sanitize_text_field( $value['id'] ),
								'loop' => isset( $value['loop'] ) ? sanitize_text_field( $value['loop'] ) : '',
							];

							foreach ( $value['properties'] as $prop ) {
								$data[ $key ]['properties'][] = [
									'id'                   => isset( $prop['id'] ) ? sanitize_text_field( $prop['id'] ) : '',
									'overridable'          => isset( $prop['overridable'] ) && $prop['overridable'],
									'overridable_multiple' => isset( $prop['overridable_multiple'] ) && $prop['overridable_multiple'],
									'ref'                  => isset( $prop['ref'] ) ? sanitize_text_field( $prop['ref'] ) : '',
									'subfield_select'      => isset( $prop['subfield_select'] ) ? sanitize_text_field( $prop['subfield_select'] ) : '',
									'textfield'            => isset( $prop['textfield'] ) ? sanitize_text_field( $prop['textfield'] ) : '',
								];
							}
						}

						return $data;
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'snippets_delete', array(
			'methods'             => \WP_REST_Server::DELETABLE,
			'callback'            => array( self::instance(), 'delete_snippets' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'post_id'     => array(
					'validate_callback' => function ( $param, $request, $key ) {

						# check if post exists
						return is_string( get_post_status( absint( $param ) ) );
					},
					'sanitize_callback' => function ( $param ) {

						return absint( $param );
					},
				),
				'snippet_ids' => array(
					'validate_callback' => function ( $param, $request, $key ) {

						return is_array( $param );
					},
					'sanitize_callback' => function ( $param ) {

						if ( ! is_array( $param ) ) {
							return array();
						}

						return array_map( function ( $v ) {

							if ( ! is_scalar( $v ) ) {
								return '';
							}

							return sanitize_text_field( $v );
						}, $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'clear_cache', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'clear_cache' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'faq', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'support_faq_search' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'q' => array(
					'required'          => true,
					'validate_callback' => function ( $param, $request, $key ) {

						$str = sanitize_text_field( $param );

						return ! empty( $str );
					},
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
			),
		) );

	}


	/**
	 * Performs a search on schema types
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.0.0
	 *
	 */
	public function get_schema_types( $request ) {

		$q = $request->get_param( 'q' );

		$types = Schemas_Model::get_types( $q );

		if ( is_wp_error( $types ) ) {
			return $types;
		}

		return rest_ensure_response( array( 'schema_types' => $types ) );
	}


	/**
	 * Fetches details about a single schema type.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.14.0
	 *
	 */
	public function get_schema_type( $request ) {

		$type = $request->get_param( 'type' );

		$type_details = Schemas_Model::get_type_details( $type );

		if ( is_wp_error( $type_details ) ) {
			return $type_details;
		}

		return rest_ensure_response( $type_details );
	}


	/**
	 * Performs a search on schema properties.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.0.0
	 *
	 */
	public function get_properties( $request ) {

		$properties = Schemas_Model::get_properties( array(
			'schema_type' => $request->get_param( 'schema_type' ),
			'return_type' => $request->get_param( 'return_type' ),
			'q'           => $request->get_param( 'q' ),
		) );

		if ( is_wp_error( $properties ) ) {
			return $properties;
		}

		$properties = wp_list_pluck( $properties, 'id' );

		return rest_ensure_response( array( 'properties' => $properties ) );
	}


	/**
	 * Performs a search on schema properties.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.14.0
	 *
	 */
	public function get_properties_v2( $request ) {

		$properties = Schemas_Model::get_properties( array(
			'schema_type'   => $request->get_param( 'schema_type' ),
			'return_type'   => $request->get_param( 'return_type' ),
			'q'             => $request->get_param( 'q' ),
			'preconfigured' => $request->get_param( 'preconfigured' )
		) );

		if ( is_wp_error( $properties ) ) {
			return $properties;
		}

		$properties = wp_list_pluck( $properties, 'suggested_value', 'id' );

		$properties = array_map( function ( $suggested_value ) {

			if ( false !== stripos( $suggested_value, '://schema.org/' ) ) {
				$label = Helper_Model::instance()->remove_schema_url( $suggested_value );
			} else {
				$label = Fields_Model::get_label( $suggested_value );
			}

			return [
				'value' => $suggested_value,
				'label' => $label
			];
		}, $properties );

		return rest_ensure_response( array( 'properties' => $properties ) );
	}

	/**
	 * Gets information about a single property.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.14.0
	 *
	 */
	public function get_property( $request ) {

		$property = $request->get_param( 'property' );
		$prop     = Schemas_Model::get_property_by_id( $property );

		if ( ! $prop instanceof Schema_Property ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/rest/schema/property',
				__( 'This schema property does not exist.', 'rich-snippets-schema' )
			);
		}

		return rest_ensure_response( $prop );
	}


	/**
	 * Builds a HTML form out of properties.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.0.0
	 *
	 */
	public function get_properties_html( $request ) {

		$prop_ids       = (array) $request->get_param( 'properties' );
		$include_table  = $request->get_param( 'include_table' );
		$schema_type    = $request->get_param( 'schema_type' );
		$post_id        = $request->get_param( 'post_id' );
		$snippet_id     = $request->get_param( 'snippet_id' );
		$is_main_schema = $request->get_param( 'is_main_schema' );

		$snippet = Snippets_Model::get_snippet( $snippet_id, (int) $post_id );

		if ( ! $snippet instanceof Rich_Snippet ) {
			$snippet = new Rich_Snippet( [
				'_is_main_snippet' => $is_main_schema,
			] );
			if ( ! empty( $snippet_id ) ) {
				$snippet->id = $snippet_id;
			}
			$snippet->type = Helper_Model::instance()->remove_schema_url( $schema_type );
		}

		if ( $include_table ) {
			$result = Admin_Snippets_Controller::instance()->get_property_table( $snippet, $prop_ids,
				get_post( $post_id ) );
		} else {
			$result = Admin_Snippets_Controller::instance()->get_property_table_elements( $snippet, $prop_ids,
				get_post( $post_id ) );
		}

		return rest_ensure_response( $result );
	}

	/**
	 * Returns the HTML form to create a rich snippet.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.0.0
	 *
	 */
	public function get_rich_snippets_form_new( $request ) {

		$post_id = $request->get_param( 'post_id' );

		return rest_ensure_response( array(
			'form' => $this->get_rich_snippets_form( $post_id ),
		) );
	}


	/**
	 * Get the HTML code for a snippets form.
	 *
	 * @param int $post_id
	 * @param string $snippet_id
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	private function get_rich_snippets_form( $post_id, $snippet_id = null ) {

		if ( is_null( $snippet_id ) ) {
			$snippet = new Rich_Snippet();
		} else {
			$snippet = Snippets_Model::get_snippet( $snippet_id, $post_id );
			if ( ! $snippet instanceof Rich_Snippet ) {
				$snippet = new Rich_Snippet();
			}
		}

		ob_start();
		View::admin_posts_snippet( get_post( $post_id ), $snippet );

		return ob_get_clean();
	}


	/**
	 * Returns the HTML forms from existing snippets.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.0.0
	 *
	 */
	public function get_rich_snippets_forms( $request ) {

		$post_id        = $request->get_param( 'post_id' );
		$form_data      = $request->get_param( 'form_data' );
		$snippet_export = $request->get_json_params();

		$forms = array();

		if ( count( $form_data ) > 0 ) {
			$snippets = Snippets_Model::generate_snippets( $form_data );
			Snippets_Model::update_snippets( $post_id, $snippets );
		} elseif ( is_array( $snippet_export ) ) {
			$snippet  = Snippets_Model::convert_from_json( $snippet_export );
			$snippets = [ $snippet ];
			Snippets_Model::update_snippets( $post_id, $snippets );
		} else {
			$snippets = Snippets_Model::get_snippets( $post_id );
		}

		if ( isset( $snippets ) ) {
			foreach ( $snippets as $snippet_id => $rich_snippet ) {
				$forms[] = $this->get_rich_snippets_form( $post_id, $snippet_id );
			}
		}

		return rest_ensure_response( array(
			'forms' => $forms,
		) );
	}


	/**
	 * Clears internal caches.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.0.0
	 *
	 */
	public function clear_cache( $request ) {

		$deleted = Cache_Model::clear_all_caches();

		return rest_ensure_response( array(
			'cache_cleared' => true,
			'cleared_items' => absint( $deleted ),
		) );
	}


	/**
	 * Deletes snippets from a post.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.0.0
	 *
	 */
	public function delete_snippets( $request ) {

		foreach ( $request->get_param( 'snippet_ids' ) as $snippet_id ) {
			$deleted = Snippets_Model::delete_snippet(
				$snippet_id,
				$request->get_param( 'post_id' )
			);

			if ( is_wp_error( $deleted ) ) {
				return $deleted;
			}

		}

		return rest_ensure_response( array(
			'deleted' => true,
		) );
	}


	/**
	 * Search rich-snippets.io for FAQ results.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 *
	 * @since 2.3.0
	 */
	public function support_faq_search( $request ) {

		$faq_posts = WPBuddy_Model::request(
			'/wp/v2/posts/?categories=10&per_page=20&search=' . urlencode( $request->get_param( 'q' ) )
		);

		if ( is_wp_error( $faq_posts ) ) {
			return $faq_posts;
		}

		ob_start();

		if ( count( $faq_posts ) <= 0 ) {
			printf( '<li>%s</li>',
				_x( 'Sorry, nothing matched your search query.', 'No FAQ entries found.', 'rich-snippets-schema' ) );
		} else {

			foreach ( $faq_posts as $faq_post ) {
				printf(
					'<li><a href="%s" target="_blank">%s</a><p class="description">%s</p></li>',
					esc_url( $faq_post->link ),
					strip_tags( $faq_post->title->rendered ),
					wp_trim_words( strip_tags( $faq_post->excerpt->rendered ) )
				);
			}

		}

		return rest_ensure_response( [ 'html' => ob_get_clean() ] );
	}


	/**
	 * Returns field type options.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|\WP_Error
	 * @since 2.14.0
	 *
	 */
	public function get_property_field_types( $request ) {
		$property_id = $request->get_param( 'property' );

		$property = Schemas_Model::get_property_by_id( $property_id );

		if ( ! $property instanceof Schema_Property ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/rest/schema/property',
				__( 'This schema property does not exist.', 'rich-snippets-schema' )
			);
		}

		new Fields_Model();

		$internal_values = call_user_func( function ( $range ) {

			$options = [];

			$values = Fields_Model::get_internal_values();

			foreach ( $values as $schema => $fields ) {

				if ( ! in_array( $schema, $range ) ) {
					continue;
				}

				foreach ( $fields as $field ) {
					$options[ $field['label'] ] = [
						'value'       => $field['id'],
						'label'       => $field['label'],
						'description' => isset( $field['description'] ) ? $field['description'] : ''
					];
				}

			}

			ksort( $options, SORT_NATURAL );

			return array_values( $options );
		}, $property->range_includes );

		$related_values = call_user_func( function ( $prop ) {
			$options = [];
			foreach ( Fields_Model::get_related_values( $prop ) as $schema ) {

				# filter primitive types
				if ( in_array( $schema, Schemas_Model::PRIMITIVE_TYPES, true ) ) {
					continue;
				}

				$options[] = [
					'value'       => $schema,
					'label'       => Helper_Model::instance()->remove_schema_url( $schema ),
					'description' => '',
				];
			}

			return $options;
		}, $property );

		$type_descendants_values = call_user_func( function ( $range ) {
			$options = [];
			foreach ( Schemas_Model::get_type_descendants( $range ) as $type ) {
				$options[] = [
					'value'       => 'descendant-' . $type,
					'label'       => Helper_Model::instance()->remove_schema_url( $type ),
					'description' => '',
				];
			}

			return $options;
		}, $property->range_includes );

		# Filter duplicates


		return rest_ensure_response( [
			'internal'    => $internal_values,
			'related'     => $related_values,
			'descendants' => $type_descendants_values
		] );
	}


	/**
	 * Sanitizes schemas.
	 *
	 * @param array $data
	 *
	 * @return array|\WP_Error
	 * @since 2.14.0
	 */
	public function sanitize_schema_field( $data ) {

		if ( ! is_array( $data ) ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/rest/sanitize_field_position',
				__( 'An array is needed.', 'rich-snippets-schema' )
			);
		}

		if ( ! isset( $data['schemas'] ) ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/rest/sanitize_field_position',
				__( 'An array with a key "schemas" is needed.', 'rich-snippets-schema' )
			);
		}

		$new_data = [];

		foreach ( $data['schemas'] as $schema_url => $properties ) {

			$s = Helper_Model::instance()->sanitize_schema_props( $properties );

			if ( count( $s[0] ) <= 0 ) {
				continue;
			}

			$uid = uniqid( 'snip-' );

			$new_data[ $uid ] = [
				'id'         => $schema_url,
				'properties' => $s[0]
			];

			if ( isset( $s[1] ) ) {
				$new_data = array_merge( $new_data, $s[1] );
			}
		}

		return $new_data;
	}


	/**
	 * Returns the schemas saved to a post.
	 *
	 * @param array $object
	 * @param string $field_name
	 * @param \WP_REST_Request $request
	 * @param string $object_type
	 *
	 * @return array
	 * @since 2.14.0
	 *
	 */
	public function get_schema_field( $object, $field_name, $request, $object_type ) {

		$meta = get_post_meta( $object['id'], '_wpb_rs_' . $field_name, true );

		return $meta;
	}


	/**
	 * Updates the schema field.
	 *
	 * @param                  $field_value
	 * @param \WP_Post $object
	 * @param string $field_name
	 * @param \WP_REST_Request $request
	 * @param string $object_type
	 *
	 * @return bool|\WP_Error
	 * @since 2.14.0
	 */
	public function update_schema_field( $field_value, $object, $field_name, $request, $object_type ) {

		$snippets = Snippets_Model::generate_snippets( $field_value );

		$updated = Snippets_Model::update_snippets( $object->ID, $snippets );

		if ( false === $updated ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/rest/get_field',
				__( 'An error occurred during the update.', 'rich-snippets-schema' ),
				[
					'value' => $field_value,
				]
			);
		}

		return true;
	}


	/**
	 * Sanitizes data before its processed.
	 *
	 * @param array $data
	 *
	 * @return array|\WP_Error
	 * @since 2.14.0
	 */
	public function sanitize_position_field( $data ) {

		$sanitized_data = [];

		if ( ! isset( $data['ruleGroups'] ) ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/rest/sanitize_field_position',
				__( 'An array with a "ruleGroups" key is needed.', 'rich-snippets-schema' )
			);
		}

		$i = - 1;
		foreach ( $data['ruleGroups'] as $rule_group ) {
			if ( ! isset( $rule_group['rules'] ) ) {
				continue;
			}

			$i ++;
			foreach ( $rule_group['rules'] as $rule ) {
				if ( ! isset( $rule['param'] ) ) {
					continue;
				}
				if ( ! isset( $rule['operator'] ) ) {
					continue;
				}
				if ( ! isset( $rule['value'] ) ) {
					continue;
				}

				$sanitized_data[ $i ][] = [
					'param'    => sanitize_text_field( $rule['param'] ),
					'operator' => sanitize_text_field( $rule['operator'] ),
					'value'    => sanitize_text_field( $rule['value'] ),
				];

			}
		}

		return $sanitized_data;
	}


	/**
	 * Updates the position field.
	 *
	 * @param                  $field_value
	 * @param \WP_Post $object
	 * @param string $field_name
	 * @param \WP_REST_Request $request
	 * @param string $object_type
	 *
	 * @return bool|\WP_Error
	 * @since 2.14.0
	 */
	public function update_position_field( $field_value, $object, $field_name, $request, $object_type ) {

		$ruleset = new Position_Ruleset();

		foreach ( $field_value as $rule_group ) {

			$new_rule_group = new Position_Rule_Group();

			foreach ( $rule_group['rules'] as $rule ) {
				if ( ! isset( $rule['param'] ) ) {
					continue;
				}
				if ( ! isset( $rule['operator'] ) ) {
					continue;
				}
				if ( ! isset( $rule['value'] ) ) {
					continue;
				}

				$new_rule           = new Position_Rule();
				$new_rule->param    = sanitize_text_field( $rule['param'] );
				$new_rule->operator = sanitize_text_field( $rule['operator'] );
				$new_rule->value    = sanitize_text_field( $rule['value'] );

				$new_rule_group->add_rule( $new_rule );
			}

			$ruleset->add_rulegroup( $new_rule_group );
		}

		$updated = Rules_Model::update_ruleset( $object->ID, $ruleset );

		if ( false === $updated ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/rest/get_field',
				__( 'An error occurred during the update.', 'rich-snippets-schema' ),
				[
					'value' => $field_value,
				]
			);
		}

		return true;
	}


	/**
	 * Get recommended schemas.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|\WP_Error
	 * @since 2.14.0
	 *
	 */
	public function get_recommended_schemas() {

		$recommendations = [];

		/**
		 * Check for WooCommerce
		 */
		if ( function_exists( 'WC' ) ) {
			$recommendations['product'] = [
				'detectedPlugin' => 'WooCommerce'
			];
		}

		/**
		 * Check if blog has blog posts
		 */
		$post_count = wp_count_posts( 'post' );

		if ( $post_count->publish > 0 ) {
			$recommendations['article'] = [
				'detectedPlugin' => 'WordPress Core'
			];
		}

		/**
		 * Check if blog has pages
		 */
		if ( ! isset( $recommendations['article'] ) ) {
			$page_count = wp_count_posts( 'page' );

			if ( $page_count->publish > 0 ) {
				$recommendations['article'] = [
					'detectedPlugin' => 'WordPress Core'
				];
			}
		}

		/**
		 * Check for WP Event Manager
		 * @see https://de.wordpress.org/plugins/wp-event-manager/
		 */
		if ( function_exists( '\WPEM' ) ) {
			$recommendations['event'] = [
				'detectedPlugin' => 'WP Event Manager'
			];
		}

		/**
		 * Check for The Events Calendar
		 * @see https://de.wordpress.org/plugins/the-events-calendar/
		 */
		if ( ! isset( $recommendations['event'] ) && defined( 'TRIBE_EVENTS_FILE' ) ) {
			$recommendations['event'] = [
				'detectedPlugin' => 'The Events Calendar'
			];
		}

		/**
		 * Check for WP Ultimate Recipe
		 * @see https://de.wordpress.org/plugins/wp-ultimate-recipe/
		 */
		if ( ! isset( $recommendations['event'] ) && ( class_exists( '\WPUltimateRecipe' ) || class_exists( '\WPUltimateRecipePremium' ) ) ) {
			$recommendations['event'] = [
				'detectedPlugin' => 'WP Ultimate Recipe'
			];
		}

		return rest_ensure_response( $recommendations );
	}


	/**
	 * Searches for SNIP examples.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.14.0
	 */
	public function get_schema_type_examples( $request ) {
		$schema = $request->get_param( 'type' );

		/**
		 * Get the TERM ID
		 */
		$response = WPBuddy_Model::request(
			'/wp/v2/schema?per_page=1&search=' . Helper_Model::instance()->remove_schema_url( $schema )
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! is_array( $response ) ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/rest/schema_examples',
				__( 'The WP-Buddy API returned an error while SNIP was searching for examples.', 'rich-snippets-schema' )
			);
		}

		if ( ! is_array( $response ) || ( is_array( $response ) && count( $response ) <= 0 ) ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/rest/schema_examples',
				__( 'The WP-Buddy API did not return any Term-IDs to request for examples.', 'rich-snippets-schema' )
			);
		}

		$tax_schema = reset( $response );

		$response = WPBuddy_Model::request(
			'/wp/v2/wpb-rs-sync?schema=' . $tax_schema->id
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! is_array( $response ) ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/rest/schema_examples',
				_x( 'The WP-Buddy API did not return any SNIPs.', 'Thrown error on rest api when there were no snip examples found.', 'rich-snippets-schema' )
			);
		}

		$ret = array_map( function ( $obj ) {

			$o            = new \stdClass();
			$o->id        = intval( $obj->id );
			$o->title     = sanitize_text_field( $obj->title->rendered );
			$o->link      = esc_url( $obj->link );
			$o->snip_code = $obj->snip_code;
			$o->excerpt   = sanitize_text_field( $obj->excerpt->rendered );

			return $o;

		}, $response );

		$ret = array_filter( $ret );

		return rest_ensure_response( $ret );
	}

}