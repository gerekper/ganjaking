<?php

class GP_Inventory_Resources {

	const RESOURCE_POST_TYPE = 'gpi_resource';

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		$this->register_post_type();
		$this->init_ajax();
	}

	public function init_ajax() {
		add_action( 'wp_ajax_gpi_add_resource', array( $this, 'ajax_add_resource' ) );
		add_action( 'wp_ajax_gpi_edit_resource', array( $this, 'ajax_edit_resource' ) );
		add_action( 'wp_ajax_gpi_delete_resource', array( $this, 'ajax_delete_resource' ) );
		add_action( 'wp_ajax_gpi_get_resource_claimed_inventory', array( $this, 'ajax_get_resource_claimed_inventory' ) );
	}

	public function ajax_add_resource() {
		// @todo resource-specific capabilities
		if ( ! GFCommon::current_user_can_any( array( 'gravityforms_edit_forms' ) ) ) {
			wp_die( -1 );
		}

		check_ajax_referer( 'gp-inventory', 'security' );

		$resource_id = wp_insert_post( array(
			'post_type'   => self::RESOURCE_POST_TYPE,
			'post_title'  => rgpost( 'resource_name' ),
			'post_status' => 'publish',
			'meta_input'  => array(
				'gpi_inventory_limit' => rgpost( 'inventory_limit' ),
				'gpi_choice_based'    => rgpost( 'choice_based' ),
				'gpi_properties'      => rgpost( 'properties' ),
			),
		) );

		wp_send_json( array(
			'resource_id' => $resource_id,
		) );
	}

	public function ajax_edit_resource() {
		// @todo resource-specific capabilities
		if ( ! GFCommon::current_user_can_any( array( 'gravityforms_edit_forms' ) ) ) {
			wp_die( -1 );
		}

		check_ajax_referer( 'gp-inventory', 'security' );

		$resource_id = wp_update_post( array(
			'ID'         => rgpost( 'resource_id' ),
			'post_title' => rgpost( 'resource_name' ),
			'meta_input' => array(
				'gpi_inventory_limit' => rgpost( 'inventory_limit' ),
				'gpi_properties'      => rgpost( 'properties' ),
			),
		) );

		wp_send_json( array(
			'resource_id' => $resource_id,
		) );
	}

	public function ajax_delete_resource() {
		// @todo resource-specific capabilities
		if ( ! GFCommon::current_user_can_any( array( 'gravityforms_edit_forms' ) ) ) {
			wp_die( -1 );
		}

		check_ajax_referer( 'gp-inventory', 'security' );

		$resource_id = rgpost( 'resource_id' );
		$resource    = get_post( $resource_id );

		if ( ! $resource || $resource->post_type !== self::RESOURCE_POST_TYPE ) {
			status_header( 400 );
			wp_send_json_error( new WP_Error( 'resource_not_found' ) );
			return;
		}

		$deleted = wp_delete_post( $resource_id, true );

		if ( $deleted ) {
			wp_send_json_success();
			return;
		}

		status_header( 400 );
		wp_send_json_error( new WP_Error( 'resource_could_not_be_deleted' ) );
	}

	public function ajax_get_resource_claimed_inventory() {
		if ( ! GFCommon::current_user_can_any( array( 'gravityforms_edit_forms' ) ) ) {
			wp_die( - 1 );
		}

		check_ajax_referer( 'gp-inventory', 'security' );

		$resource_id = rgpost( 'resource_id' );
		$resource    = get_post( $resource_id );

		if ( ! $resource || $resource->post_type !== self::RESOURCE_POST_TYPE ) {
			status_header( 400 );
			wp_send_json_error( new WP_Error( 'resource_not_found' ) );
			return;
		}

		// Use first resource field to calculate the claimed inventory
		$resource_fields = gp_inventory_type_advanced()->get_resource_fields( $resource_id );

		if ( empty( $resource_fields ) ) {
			die();
		}

		$resource_field = $resource_fields[0];

		if ( gp_inventory_type_advanced()->is_using_properties( $resource_field[0] ) ) {
			die();
		}

		wp_send_json( gp_inventory_type_advanced()->get_claimed_inventory( $resource_fields[0] ) );
	}

	public function get_resources() {
		$resources_posts = get_posts( array(
			'post_type'   => self::RESOURCE_POST_TYPE,
			'numberposts' => 9999,
		) );

		$resources = array();

		foreach ( $resources_posts as $resource_post ) {
			$resources[ $resource_post->ID ] = array(
				'id'              => $resource_post->ID,
				'name'            => $resource_post->post_title,
				'properties'      => $this->get_resource_properties( $resource_post->ID ),
				'inventory_limit' => $this->get_resource_inventory_limit( $resource_post->ID ),
				'choice_based'    => $this->is_resource_choice_based( $resource_post->ID ),
			);
		}

		return $resources;
	}

	public function is_resource_choice_based( $resource_id ) {
		return wp_validate_boolean( get_post_meta( $resource_id, 'gpi_choice_based', true ) );
	}

	public function get_resource_inventory_limit( $resource_id ) {
		return (int) get_post_meta( $resource_id, 'gpi_inventory_limit', true );
	}

	public function get_resource_properties( $resource_id ) {
		$meta = get_post_meta( $resource_id, 'gpi_properties', true );

		if ( ! $meta || ! is_array( $meta ) ) {
			return array();
		}

		return $meta;
	}

	public function register_post_type() {
		/**
		 * Modify the arguments used for registering the Resource post type.
		 *
		 * @since 1.0-beta-1.0
		 *
		 * @param array  $args  Post type arguments. See https://developer.wordpress.org/reference/functions/register_post_type/#parameters
		 */
		$resource_post_type_args = apply_filters( 'gpi_resource_post_type_args', array(
			'labels'              => array(
				'name'                  => _x( 'Resources', 'Post Type General Name', 'gp-inventory' ),
				'singular_name'         => _x( 'Resource', 'Post Type Singular Name', 'gp-inventory' ),
				'add_new_item'          => __( 'Add New Resource', 'gp-inventory' ),
				'add_new'               => __( 'Add New', 'gp-inventory' ),
				'new_item'              => __( 'New Resource', 'gp-inventory' ),
				'edit_item'             => __( 'Edit Resource', 'gp-inventory' ),
				'update_item'           => __( 'Update Resource', 'gp-inventory' ),
				'view_item'             => __( 'View Resource', 'gp-inventory' ),
				'view_items'            => __( 'View Resources', 'gp-inventory' ),
				'search_items'          => __( 'Search Resources', 'gp-inventory' ),
				'not_found'             => __( 'Not found', 'gp-inventory' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'gp-inventory' ),
				'items_list'            => __( 'Resources list', 'gp-inventory' ),
				'items_list_navigation' => __( 'Resources list navigation', 'gp-inventory' ),
				'filter_items_list'     => __( 'Filter resources list', 'gp-inventory' ),
			),
			'supports'            => array( 'title', 'custom-fields' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
		) );

		register_post_type( self::RESOURCE_POST_TYPE, $resource_post_type_args );
	}

}

function gp_inventory_resources() {
	return GP_Inventory_Resources::get_instance();
}
