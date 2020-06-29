<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Modify custom post type arguments.
		add_filter( 'event_ticket_register_args', array( $this, 'ticket_post_type_args' ), 10, 1 );
		add_filter( 'event_ticket_email_register_args', array( $this, 'ticket_email_post_type_args' ), 10, 1 );

		// Register post types.
		$this->_register_post_types();
	}

	/**
	 * Change settings for ticket post type.
	 *
	 * @param  array $args Default args
	 * @return array       Modified args
	 */
	public function ticket_post_type_args( $args = array() ) {
		$args['public']              = false;
		$args['publicly_queryable']  = false;
		$args['exclude_from_search'] = true;
		$args['rewrite']             = false;
		$args['show_in_nav_menus']   = false;
		$args['menu_position']       = 58;
		$args['menu_icon']           = 'dashicons-tickets-alt';
		$args['supports']            = array( 'title' );

		return $args;
	}

	/**
	 * Change settings for ticket email post type.
	 *
	 * @param  array $args Default args
	 * @return array       Modified args
	 */
	public function ticket_email_post_type_args( $args = array() ) {
		$args['publicly_queryable']  = false;
		$args['exclude_from_search'] = true;
		$args['show_in_menu']        = false;
		$args['show_in_nav_menus']   = false;
		$args['supports']            = array( 'title' );

		return $args;
	}

	/**
	 * Register post types.
	 *
	 * @return void
	 */
	private function _register_post_types() {
		$this->_register_post_type( 'event_ticket', __( 'Tickets', 'woocommerce-box-office' ), __( 'Ticket', 'woocommerce-box-office' ) );
		$this->_register_post_type( 'event_ticket_email', __( 'Emails', 'woocommerce-box-office' ), __( 'Email', 'woocommerce-box-office' ) );
	}

	/**
	 * Wrapper function to register a new post type.
	 *
	 * @param  string $post_type   Post type name
	 * @param  string $plural      Post type item plural name
	 * @param  string $single      Post type item single name
	 * @param  string $description Description of post type
	 * @return object              Post type class object
	 */
	private function _register_post_type( $post_type = '', $plural = '', $single = '', $description = '' ) {
		if ( ! $post_type || ! $plural || ! $single ) {
			return;
		}

		require_once( WCBO()->dir . 'includes/lib/class-wc-box-office-post-type.php' );
		$post_type = new WC_Box_Office_Post_Type( $post_type, $plural, $single, $description );

		return $post_type;
	}
}
