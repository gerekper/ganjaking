<?php

use PHPUnit\Framework\ExpectationFailedException;

class WCML_WC_Memberships implements \IWPML_Action {

	const SAVED_POST_PARENT = 'wcml_memberships_post_parent';
	/**
	 * @var WPML_WP_API
	 */
	private $wp_api;

	/**
	 * @param WPML_WP_API $wp_api
	 */
	public function __construct( WPML_WP_API $wp_api ) {
		$this->wp_api = $wp_api;
	}

	public function add_hooks() {
		add_filter( 'wcml_register_endpoints_query_vars', [ $this, 'register_endpoints_query_vars' ], 10, 3 );
		add_filter( 'parse_request', [ $this, 'adjust_query_vars' ] );
		add_filter( 'wcml_endpoint_permalink_filter', [ $this, 'endpoint_permalink_filter' ], 10, 2 );
		add_filter( 'wc_memberships_members_area_my-memberships_actions', [ $this, 'filter_actions_links' ] );
		add_filter( 'wpml_pre_parse_query', [ $this, 'save_post_parent' ] );
		add_filter( 'wpml_post_parse_query', [ $this, 'restore_post_parent' ] );
		add_filter( 'wc_memberships_rule_object_ids', [ $this, 'add_translated_object_ids' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_assets' ] );
	}

	/**
	 * @param array          $query_vars
	 * @param array          $wc_vars
	 * @param WCML_Endpoints $object
	 *
	 * @return array
	 */
	public function register_endpoints_query_vars( $query_vars, $wc_vars, $object ) {
		$query_vars['members_area'] = $this->get_translated_endpoint( $object );

		return $query_vars;
	}

	/**
	 * @param WCML_Endpoints $object
	 *
	 * @return string
	 */
	public function get_translated_endpoint( $object ) {
		$translation = $object->get_endpoint_translation(
			'members_area',
			get_option( 'woocommerce_myaccount_members_area_endpoint', 'members-area' )
		);

		return $translation;
	}

	/**
	 * @param WP $q
	 *
	 * @return WP
	 */
	public function adjust_query_vars( $q ) {
		if ( ! isset( $q->query_vars['members-area'] ) && isset( $q->query_vars['members_area'] ) ) {
			$q->query_vars['members-area'] = $q->query_vars['members_area'];
		}

		return $q;
	}

	/**
	 * @param string $endpoint
	 * @param string $key
	 *
	 * @return string
	 */
	public function endpoint_permalink_filter( $endpoint, $key ) {
		if ( 'members_area' === $key ) {
			$endpoint = get_option( 'woocommerce_myaccount_members_area_endpoint', 'members-area' );
		}

		return $endpoint;
	}

	/**
	 * @param array $actions
	 * @return array
	 */
	public function filter_actions_links( $actions ) {
		foreach ( $actions as $key => $action ) {
			if ( 'view' === $key ) {
				$membership_endpoints   = $this->get_members_area_endpoint();
				$actions[ $key ]['url'] = str_replace( $membership_endpoints['original'], $membership_endpoints['translated'], $action['url'] );
			}
		}

		return $actions;
	}

	/**
	 * @param WP_Query $q
	 *
	 * @return WP_Query
	 */
	public function save_post_parent( $q ) {
		if ( isset( $q->query_vars['post_type'] )
			&& in_array( 'wc_user_membership', (array) $q->query_vars['post_type'], true )
			&& ! empty( $q->query_vars['post_parent'] ) ) {
			$q->query_vars[ self::SAVED_POST_PARENT ] = $q->query_vars['post_parent'];
		}

		return $q;
	}

	/**
	 * @param WP_Query $q
	 *
	 * @return WP_Query
	 */
	public function restore_post_parent( $q ) {
		if ( isset( $q->query_vars[ self::SAVED_POST_PARENT ] ) ) {
			$q->query_vars['post_parent'] = $q->query_vars[ self::SAVED_POST_PARENT ];
			unset( $q->query_vars[ self::SAVED_POST_PARENT ] );
		}

		return $q;
	}

	/**
	 * @param array $object_ids
	 *
	 * @return array
	 */
	public function add_translated_object_ids( $object_ids ) {
		$result = [];
		foreach ( $object_ids as $object_id ) {
			$type         = apply_filters( 'wpml_element_type', get_post_type( $object_id ) );
			$trid         = apply_filters( 'wpml_element_trid', null, $object_id, $type );
			$translations = array_values( wp_list_pluck(
				apply_filters( 'wpml_get_element_translations', [], $trid, $type ),
				'element_id'
			) );

			$result = array_merge( $result, $translations );
		}

		return $result;
	}

	public function load_assets() {
		if ( wc_get_page_id( 'myaccount' ) === get_the_ID() ) {
			$wcml_plugin_url = $this->wp_api->constant( 'WCML_PLUGIN_URL' );
			$wcml_version    = $this->wp_api->constant( 'WCML_VERSION' );
			wp_register_script( 'wcml-members-js', $wcml_plugin_url . '/compatibility/res/js/wcml-members.js', [ 'jquery' ], $wcml_version, true );
			wp_enqueue_script( 'wcml-members-js' );
			wp_localize_script( 'wcml-members-js', 'wc_memberships_memebers_area_endpoint', $this->get_members_area_endpoint() );
		}
	}

	/**
	 * @return array
	 */
	private function get_members_area_endpoint() {
		$endpoint            = get_option( 'woocommerce_myaccount_members_area_endpoint', 'members-area' );
		$string_context      = class_exists( 'WPML_Endpoints_Support' ) ? WPML_Endpoints_Support::STRING_CONTEXT : 'WooCommerce Endpoints';
		$translated_endpoint = apply_filters( 'wpml_translate_single_string', $endpoint, $string_context, 'members_area' );

		return [
			'original'   => $endpoint,
			'translated' => $translated_endpoint,
		];
	}

}
