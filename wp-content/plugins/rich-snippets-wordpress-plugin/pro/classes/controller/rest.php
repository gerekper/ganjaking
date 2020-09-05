<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Fields_Model;
use wpbuddy\rich_snippets\Rich_Snippet;
use wpbuddy\rich_snippets\Snippets_Model;
use wpbuddy\rich_snippets\View;
use wpbuddy\rich_snippets\WPBuddy_Model;
use function wpbuddy\rich_snippets\rich_snippets;

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
 * @since   2.19.0
 */
class Rest_Controller extends \wpbuddy\rich_snippets\Rest_Controller {

	/**
	 * Get the singleton instance.
	 *
	 * Creates a new instance of the class if it does not exists.
	 *
	 * @return Rest_Controller
	 *
	 * @since 2.19.0
	 */
	public static function instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Initializes admin stuff
	 *
	 * @since 2.19.0
	 */
	public static function init() {
		$instance = self::instance();

		parent::init();

		# register REST fields
		$instance ->register_rest_fields();
	}


	/**
	 * Registers the REST fields needed.
	 *
	 * @since 2.14.0
	 */
	protected function register_rest_fields() {
		register_rest_field( 'wpb-rs-global', 'schemas', [
			'get_callback'    => [ $this, 'get_schema_field' ],
			'schema'          => [
				'type'        => 'object',
				'description' => __( 'Schemas saved on a post.', 'rich-snippets-schema' ),
				'context'     => [ 'view', 'edit' ],
				'arg_options' => array(
					'sanitize_callback' => [ $this, 'sanitize_schema_field' ],
				),
			],
			'update_callback' => [ $this, 'update_schema_field' ],
		] );

		register_rest_field( 'wpb-rs-global', 'position', [
			'get_callback'    => [ $this, 'get_position_field' ],
			'schema'          => [
				'type'        => 'object',
				'description' => __( 'The position of the schema.', 'rich-snippets-schema' ),
				'context'     => [ 'view', 'edit' ],
				'arg_options' => array(
					'sanitize_callback' => [ $this, 'sanitize_position_field' ],
				),
			],
			'update_callback' => [ $this, 'update_position_field' ],
		] );
	}


	/**
	 * Returns the position saved to a post.
	 *
	 * @param array $object
	 * @param string $field_name
	 * @param \WP_REST_Request $request
	 * @param string $object_type
	 *
	 * @return string JSON encoded representation of the ruleset.
	 * @since 2.14.0
	 */
	public function get_position_field( $object, $field_name, $request, $object_type ) {

		return Rules_Model::get_ruleset( $object['id'] )->__toString();
	}

	/**
	 * Registers the REST routes.
	 *
	 * @since 2.0.0
	 */
	protected function register_routes() {
		parent::register_routes();

		register_rest_route( 'wpbuddy/rich_snippets/v1', '/admin/verify', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'activate_plugin' ),
			'permission_callback' => function ( $request ) {

				/**
				 * REST Permission filter.
				 *
				 * Allows to modify the capability for the REST access.
				 *
				 * @hook  wpbuddy/rich_snippets/rest/permission
				 *
				 * @param {string} $capability The capability for the REST access. Default: manage_options.
				 * @param {WP_Rest_Request} $request
				 *
				 * @returns {string} The capability for the REST persmission.
				 *
				 * @since 2.0.0
				 */
				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'purchase_code' => array(
					'required'          => true,
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'positions/value-select', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'load_position_value_select' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'param' => array(
					'validate_callback' => function ( $param, $request, $key ) {

						$param_groups = Admin_Position_Controller::instance()->get_params();

						foreach ( $param_groups as $param_list ) {
							if ( isset( $param_list['params'][ $param ] ) ) {
								return true;
							}
						}

						return false;

					},
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v2', 'positions/value-select', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'load_position_value_select_options' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'param' => array(
					'validate_callback' => function ( $param, $request, $key ) {

						$param_groups = Admin_Position_Controller::instance()->get_params();

						foreach ( $param_groups as $param_list ) {
							if ( isset( $param_list['params'][ $param ] ) ) {
								return true;
							}
						}

						return false;

					},
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'positions/value-possibilities', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'load_position_value_possibilities' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'q'     => array(
					'required'          => true,
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
				'page'  => array(
					'sanitize_callback' => function ( $param ) {

						return absint( $param );
					},
				),
				'param' => array(
					'validate_callback' => function ( $param, $request, $key ) {

						$param_groups = Admin_Position_Controller::instance()->get_params();

						foreach ( $param_groups as $param_list ) {
							if ( isset( $param_list['params'][ $param ] ) ) {
								return true;
							}
						}

						return false;

					},
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'overwrite_form', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( self::instance(), 'get_overwrite_form' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'post_id'         => array(
					'required'          => true,
					'validate_callback' => function ( $param, $request, $key ) {

						# check if post exists
						return is_string( get_post_status( absint( $param ) ) );
					},
					'sanitize_callback' => function ( $param ) {

						return absint( $param );
					},
				),
				'snippet_post_id' => array(
					'required'          => true,
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

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'overwrite', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( self::instance(), 'overwrite_snippet_values' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'post_id'              => array(
					'required'          => true,
					'validate_callback' => function ( $param, $request, $key ) {

						# check if post exists
						return is_string( get_post_status( absint( $param ) ) );
					},
					'sanitize_callback' => function ( $param ) {

						return absint( $param );
					},
				),
				'main_snippet_post_id' => array(
					'required'          => true,
					'validate_callback' => function ( $param, $request, $key ) {
						return is_string( get_post_status( intval( $param ) ) );
					},
					'sanitize_callback' => function ( $param ) {

						return intval( $param );
					},
				),
				'main_snippet_id'      => array(
					'required'          => true,
					'validate_callback' => function ( $param, $request, $key ) {

						# check if post exists
						return is_string( $param );
					},
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'feature-request/vote', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( self::instance(), 'support_feature_vote' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'comment_id' => array(
					'required'          => true,
					'validate_callback' => function ( $param, $request, $key ) {

						$comment_id = intval( $param );

						return ! empty( $comment_id );
					},
					'sanitize_callback' => function ( $param ) {

						return intval( $param );
					},
				),
				'direction'  => array(
					'required'          => true,
					'validate_callback' => function ( $param, $request, $key ) {

						return in_array( $param, array( 'up', 'down' ) );
					},
					'sanitize_callback' => function ( $param ) {

						return sanitize_text_field( $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'feature-request', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( self::instance(), 'support_feature_add' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'content' => array(
					'required'          => true,
					'sanitize_callback' => function ( $param ) {

						return sanitize_textarea_field( $param );
					},
				),
			),
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'deactivate-license', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( self::instance(), 'deactivate_license' ),
			'permission_callback' => function ( $request ) {

				/**
				 * REST deactivate license permission filter.
				 *
				 * Allows to modify the capability for the REST access on deactivating the license.
				 *
				 * @hook  wpbuddy/rich_snippets/rest/permission/deactivate_license
				 *
				 * @param {string} $capability The capability for the REST access. Default: manage_options.
				 * @param {WP_Rest_Request} $request
				 *
				 * @returns {string} The capability for the REST persmission.
				 *
				 * @since 2.0.0
				 */
				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission/deactivate_license',
					current_user_can( 'manage_options' ),
					$request
				);
			},
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'rating/dismiss', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( self::instance(), 'rating_dismiss' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission/rating/dismiss',
					current_user_can( 'manage_options' ),
					$request
				);
			},
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'schema/export', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( self::instance(), 'schema_export' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
		) );

		register_rest_route( 'wpbuddy/rich_snippets/v1', 'snips/import', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( self::instance(), 'snips_import' ),
			'permission_callback' => function ( $request ) {

				return apply_filters(
					'wpbuddy/rich_snippets/rest/permission',
					current_user_can( 'manage_options' ),
					$request
				);
			},
			'args'                => array(
				'snips' => array(
					'required' => true,
					'type'     => 'array',
					'items'    => [
						'type'    => 'integer',
						'minimum' => '1',
						'require' => true,
					],
				),
			),
		) );
	}


	/**
	 * Verifies a purchase.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.0.0
	 *
	 */
	public function activate_plugin( $request ) {

		$purchase_code = $request->get_param( 'purchase_code' );

		update_option( 'wpb_rs/purchase_code', $purchase_code, false );

		$response = WPBuddy_Model::request(
			'/wpbuddy/rich_snippets_manager/v1/validate',
			array(
				'method'  => 'POST',
				'body'    => array(
					'purchase_code' => $purchase_code,
				),
				'timeout' => 20,
			),
			false,
			true
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$verified = true;

		update_option( base64_decode( 'd3BiX3JzL3ZlcmlmaWVk' ), $verified, true );
		update_option( 'd3BiX3JzL3ZlcmlmaWVk', $verified, true );

		return rest_ensure_response( array( 'verified' => $verified ) );
	}

	/**
	 * Loads a position value select box (HTML code).
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.0.0
	 */
	public function load_position_value_select( $request ) {

		$rule        = new Position_Rule();
		$rule->param = $request->get_param( 'param' );

		ob_start();
		Admin_Position_Controller::instance()->print_value_select( $rule );

		return rest_ensure_response( array(
			'select_html' => ob_get_clean(),
		) );
	}

	/**
	 * Returns the positions value select options.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.14.0
	 *
	 */
	public function load_position_value_select_options( $request ) {

		$rule        = new Position_Rule();
		$rule->param = $request->get_param( 'param' );

		$options = Admin_Position_Controller::instance()->get_value_select_options( $rule );

		return rest_ensure_response( $options );
	}


	/**
	 * Loads values for a position value select2 box.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.0.0
	 *
	 */
	public function load_position_value_possibilities( $request ) {

		$q     = $request->get_param( 'q' );
		$page  = $request->get_param( 'page' );
		$param = $request->get_param( 'param' );

		global $wpdb;

		$like = sprintf( '%%%s%%', $wpdb->esc_like( $q ) );

		$sql = $wpdb->prepare(
			"SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE (post_title LIKE '%s' OR ID = %d) AND post_status = 'publish'",
			esc_sql( $like ),
			$q
		);

		if ( 'page_parent' === $param ) {
			$sql .= ' AND post_type = "page"';
		}

		$posts = $wpdb->get_results( $sql );

		if ( ! is_array( $posts ) ) {
			return rest_ensure_response( array(
				'values' => array(),
			) );
		}

		$values = array();

		$i18n = _x(
			'%1$s (%2$s, %3$d)',
			'value possibilities: %1$s is the post title, %2$s is the post type, %3$d is the post ID',
			'rich-snippets-schema'
		);

		foreach ( $posts as $post ) {
			$post_title = empty( $post->post_title ) ? __( '(No post title)', 'rich-snippets-schema' ) : $post->post_title;

			$values[ $post->ID ] = sprintf(
				$i18n,
				esc_attr( $post_title ),
				esc_attr( $post->post_type ),
				$post->ID
			);
		}

		/**
		 * Position value possibilities filter.
		 *
		 * Allows to modify the list of possible values for positions.
		 *
		 * @hook  wpbuddy/rich_snippets/position/value_possibilities
		 *
		 * @param {array} $values The possible values.
		 * @param {string} $q The search term.
		 * @param {int} $page The page number.
		 * @param {string} $param
		 *
		 * @returns {array} A list of possible position values.
		 *
		 * @since 2.0.0
		 */
		$values = apply_filters( 'wpbuddy/rich_snippets/position/value_possibilities', $values, $q, $page, $param );

		return rest_ensure_response( array(
			'values' => $values,
		) );
	}


	/**
	 * Outputs the overwrite-form for a snippet.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 *
	 * @since 2.2.0
	 */
	public function get_overwrite_form( $request ) {

		$snippet_post_id = $request->get_param( 'snippet_post_id' );
		$post_id         = $request->get_param( 'post_id' );

		$rich_snippets = Snippets_Model::get_snippets( $snippet_post_id );

		if ( count( $rich_snippets ) <= 0 ) {
			return new \WP_Error( 'get_overwrite_form',
				__( 'Could not find snippets on this post.', 'rich-snippets-schema' ) );
		}

		$rich_snippet = array_values( $rich_snippets )[0];

		new Fields_Model();

		ob_start();
		View::admin_snippets_overwrite_form( get_post( $snippet_post_id ), $rich_snippet, $post_id );

		return rest_ensure_response( array(
			'form' => ob_get_clean(),
		) );
	}


	/**
	 * Saves overwrite-data to a post.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 *
	 * @since 2.2.0
	 */
	public function overwrite_snippet_values( $request ) {

		$post_id         = $request->get_param( 'post_id' );
		$snippet_post_id = $request->get_param( 'main_snippet_post_id' );
		$main_snippet_id = $request->get_param( 'main_snippet_id' );

		/**
		 * Back Compat
		 */
		$data = get_post_meta( $post_id, '_wpb_rs_overwrite_data', true );
		if ( is_array( $data ) && array_key_exists( $main_snippet_id, $data ) ) {
			unset( $data[ $main_snippet_id ] );
			update_post_meta( $post_id, '_wpb_rs_overwrite_data', $data );
		}

		/**
		 * New Format
		 */
		global $wpdb;

		# delete all old values
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key LIKE %s",
			$post_id,
			'%' . $wpdb->esc_like( 'snippet_' . $snippet_post_id ) . '%'
		) );

		$snippet = call_user_func( function ( $params, $pid ) {

			$allowed_html = [
				'h1'     => [],
				'h2'     => [],
				'h3'     => [],
				'h4'     => [],
				'h5'     => [],
				'h6'     => [],
				'br'     => [],
				'ol'     => [],
				'ul'     => [],
				'li'     => [],
				'a'      => [
					'href' => array(),
				],
				'p'      => [],
				'div'    => [],
				'b'      => [],
				'strong' => [],
				'i'      => [],
				'em'     => [],
			];

			$r = [];
			foreach ( $params as $param_name => $param_value ) {
				if ( 0 !== stripos( $param_name, 'snippet_' . $pid ) ) {
					continue;
				}

				/**
				 * Allowed HTML filter for overwritten input fields.
				 *
				 * Allows to change what HTML types are allowed on input fields.
				 *
				 * @hook  wpbuddy/rich_snippets/rest/overwrite_field/allowed_html
				 *
				 * @param {array} $allowed_Html Array of allowed HTML tags. @see wp_kses() function in WordPress.
				 * @param {string} $param_name The HTML name of the current field.
				 * @param {string} $param_value The value of the current field.
				 * @param {array} $params Parameters for this process (post-iD, snippet ID, etc.)
				 *
				 * @returns {array} Array of allowed HTML tags. @see wp_kses() function in WordPress.
				 *
				 * @since 2.17.0
				 */
				$a_html = apply_filters( 'wpbuddy/rich_snippets/rest/overwrite_field/allowed_html', $allowed_html, $param_name, $param_value, $params );

				$r[ sanitize_text_field( $param_name ) ] = wp_kses(
					$param_value,
					$a_html
				);
			}

			return $r;
		}, $request->get_params(), $snippet_post_id );

		# save all new values
		foreach ( $snippet as $prop_name => $prop_value ) {
			add_post_meta( $post_id, $prop_name, $prop_value, true );
		}

		return rest_ensure_response( true );
	}


	/**
	 * Vote for a feature.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.3.0
	 *
	 */
	public function support_feature_vote( $request ) {

		$response = WPBuddy_Model::request(
			'/wpbuddy/rich_snippets_manager/v1/support/feature/vote',
			[
				'method' => 'POST',
				'body'   => $request->get_params(),
			],
			false,
			true
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return rest_ensure_response( [ 'success' => true ] );
	}


	/**
	 * Add a new feature.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.3.0
	 *
	 */
	public function support_feature_add( $request ) {

		$response = WPBuddy_Model::request(
			'/wp/v2/comments',
			[
				'method' => 'POST',
				'body'   => [
					'content' => $request->get_param( 'content' ),
					'post'    => defined( 'WPB_RS_REMOTE' ) ? 1 : 443,
				],
			],
			false,
			true
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return rest_ensure_response( [ 'success' => true ] );
	}


	/**
	 * Deactivates a license.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 * @since 2.5.0
	 *
	 */
	public function deactivate_license( $request ) {

		$response = WPBuddy_Model::request(
			'/wpbuddy/rich_snippets_manager/v1/deactivate-license',
			[ 'method' => 'POST' ],
			false,
			true
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$deactivated = isset( $response->deactivated ) ? $response->deactivated : false;

		$redirect_url = '';

		if ( $deactivated ) {
			if ( ! function_exists( '\deactivate_plugins' ) && is_file( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
				include ABSPATH . 'wp-admin/includes/plugin.php';
			}

			deactivate_plugins( rich_snippets()->get_plugin_file() );

			$redirect_url = admin_url( 'index.php' );
		}

		return rest_ensure_response( [
			'deactivated'  => $deactivated,
			'redirect_url' => $redirect_url
		] );
	}

	/**
	 * Dismisses the rating notice.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 * @since 2.9.0
	 *
	 */
	public function rating_dismiss( $request ) {

		return rest_ensure_response( [
			'updated' => update_option( 'wpb_rs/rating_dismissed_timestamp', time(), true ),
		] );
	}


	/**
	 * Returns a SNIP in JSON format for exporting.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|\WP_Error
	 * @since 2.13.0
	 *
	 */
	public function schema_export( $request ) {

		$form_data = $request->get_body_params();

		if ( ! isset( $form_data['snippets'] ) ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/rest/export',
				__( 'Missing field "snippets".', 'rich-snippets-schema' )
			);
		}

		if ( ! is_array( $form_data['snippets'] ) ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/rest/export',
				__( 'Field "snippets" should be an array of values.', 'rich-snippets-schema' )
			);
		}

		$snippets = Snippets_Model::generate_snippets( $form_data['snippets'] );

		$snippets = array_map( function ( $snippet ) {
			/**
			 * @var Rich_Snippet $snippet
			 */
			$snippet->_is_export = true;
			$snippet->prepare_for_export();

			return $snippet;
		}, $snippets );

		if ( isset( $form_data['wpb_rs_position_rule'] ) && is_array( $form_data['wpb_rs_position_rule'] ) ) {
			$ruleset = Rules_Model::convert_to_ruleset( $form_data['wpb_rs_position_rule'] );
			if ( $ruleset->has_rulegroups() ) {
				foreach ( $snippets as $snip_id => $snippet ) {
					$rules = $ruleset->__toString();

					$snippets[ $snip_id ]->{'@ruleset'} = json_decode( $rules );
				}
			}
		}

		return rest_ensure_response( $snippets );
	}


	/**
	 * Imports SNIPs that were found on rich-snippets.io
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @since 2.14.0
	 */
	public function snips_import( $request ) {
		global $wpdb;
		$snipIds = $request->get_param( 'snips' );

		$response = WPBuddy_Model::request(
			'/wp/v2/wpb-rs-sync?include=' . implode( ',', $snipIds )
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! is_array( $response ) ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/rest/snips_import',
				_x( 'The WP-Buddy API did not return any SNIPs.', 'Thrown error on rest api when there were no snip examples found.', 'rich-snippets-schema' )
			);
		}

		$messages = [];

		foreach ( $response as $snip ) {
			$snip_id     = intval( $snip->id );
			$query       = $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} as pm LEFT JOIN {$wpdb->posts} as p ON (pm.post_id = p.ID) WHERE pm.meta_key = '_wpb_rs_sync_id' AND pm.meta_value = %d AND p.post_status = 'publish' LIMIT 1", $snip_id );
			$post_id     = $wpdb->get_var( $query );
			$is_new_post = false;

			# Create a new Global Snippet if it does not exist
			if ( is_null( $post_id ) ) {
				$post_id = wp_insert_post( [
					'post_type'   => 'wpb-rs-global',
					'post_title'  => sanitize_text_field( $snip->title->rendered ),
					'post_status' => 'publish',
					'meta_input'  => [
						'_wpb_rs_sync_id' => intval( $snip_id )
					]
				] );

				if ( is_wp_error( $post_id ) ) {
					$messages[ $snip_id ] = new \WP_Error(
						'wpbuddy/rich_snippets/rest/snips_import',
						sprintf(
							__( 'Could not create global snippet during SNIP import. Got error: %s', 'rich-snippets-schema' ),
							$post_id->get_error_message()
						)
					);
					continue;
				}

				$is_new_post = true;
			}

			$post_id = intval( $post_id );

			if ( is_null( $snip->snip_code ) ) {
				if ( $is_new_post ) {
					# delete previously created post
					wp_delete_post( $post_id, true );
				}

				$messages[ $snip_id ] = new \WP_Error(
					'wpbuddy/rich_snippets/rest/snips_import',
					__( 'Could not decode SNIP. No Global Snippet was created.', 'rich-snippets-schema' )
				);

				continue;
			}

			/**
			 * Now write all the post meta
			 */

			$snippet = json_decode( json_encode( $snip->snip_code ), true );

			if ( isset( $snippet['@ruleset'] ) ) {
				$rules_array = $snippet['@ruleset'];
				unset( $snippet['@ruleset'] );
			} else {
				$rules_array = [];
			}

			# the snippet
			$snippet = Snippets_Model::convert_from_json( $snippet );
			Snippets_Model::update_snippets( $post_id, [ $snippet ] );

			if ( ! is_array( $rules_array ) ) {
				continue;
			}

			$rules_array = array_filter( $rules_array );

			if ( count( $rules_array ) <= 0 ) {
				continue;
			}

			$ruleset = Rules_Model::convert_to_ruleset( $rules_array );
			Rules_Model::update_ruleset( $post_id, $ruleset );

			if ( $is_new_post ) {
				$messages[ $snip_id ] = sprintf(
					__( 'Created Global Snippet "%s".', 'rich-snippets-schema' ),
					sanitize_text_field( $snip->title->rendered )
				);
			} else {
				$messages[ $snip_id ] = sprintf(
					__( 'Updated Global Snippet "%s".', 'rich-snippets-schema' ),
					get_the_title( $post_id )
				);
			}

		}

		return rest_ensure_response( $messages );
	}
}