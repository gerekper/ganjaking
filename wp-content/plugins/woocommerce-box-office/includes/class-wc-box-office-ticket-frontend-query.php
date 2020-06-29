<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that handles frontend page query.
 *
 * @since 1.0.2
 */
class WC_Box_Office_Ticket_Frontend_Query extends WC_Query {

	public function __construct() {
		add_action( 'init', array( $this, 'add_endpoints' ) );
		add_filter( 'the_title', array( $this, 'change_endpoint_title' ), 11, 1 );

		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
			add_filter( 'woocommerce_get_breadcrumb', array( $this, 'add_breadcrumb' ), 10 );
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 11 );

			// Inserting your new tab/page into the My Account page.
			add_filter( 'woocommerce_account_menu_items', array( $this, 'add_menu_items' ) );
			add_action( 'woocommerce_account_my-tickets_endpoint', array( $this, 'endpoint_content' ) );
		}

		$this->init_query_vars();
	}

	/**
	 * Init query vars by loading options.
	 */
	public function init_query_vars() {
		$this->query_vars = array(
			'my-tickets' => get_option( 'woocommerce_myaccount_my_tickets_endpoint', 'my-tickets' ),
		);
	}

	/**
	 * Adds endpoint breadcrumb when viewing my tickets.
	 *
	 * @param  array $crumbs already assembled breadcrumb data
	 * @return array $crumbs if we're on a my tickets page, then augmented breadcrumb data
	 */
	public function add_breadcrumb( $crumbs ) {
		global $wp;

		foreach ( $this->query_vars as $key => $query_var ) {
			if ( $this->is_query( $query_var ) ) {
				$crumbs[] = array( $this->get_endpoint_title( $key ) );
			}
		}

		return $crumbs;
	}

	/**
	 * Check if the current query is for a type we want to override.
	 *
	 * @param  string $query_var the string for a query to check for
	 * @return bool
	 */
	protected function is_query( $query_var ) {
		global $wp;

		$is_ticket_query = false;
		if ( is_main_query() && is_page() && isset( $wp->query_vars[ $query_var ] ) ) {
			$is_ticket_query = true;
		}

		return $is_ticket_query;
	}

	/**
	 * Get endpoint title.
	 *
	 * @param  string $endpoint Endpoint name
	 * @return string           Endpoint title
	 */
	public function get_endpoint_title( $endpoint ) {
		$title = '';
		if ( 'my-tickets' === $endpoint ) {
			$title = __( 'My Tickets', 'woocommerce-box-office' );
		}

		return $title;
	}

	/**
	 * Changes page title on my tickets page.
	 *
	 * @param  string $title original title
	 * @return string        changed title
	 */
	public function change_endpoint_title( $title ) {
		global $wp;

		if ( in_the_loop() ) {
			foreach ( $this->query_vars as $key => $query_var ) {
				if ( $this->is_query( $query_var ) && in_the_loop() ) {
					$title = $this->get_endpoint_title( $key );

					// unhook after we've returned our title to prevent it from overriding others
					remove_filter( 'the_title', array( $this, __FUNCTION__ ), 11 );
				}
			}
		}
		return $title;
	}


	/**
	 * Insert the new endpoint into the My Account menu.
	 *
	 * @param array $items
	 * @return array
	 */
	public function add_menu_items( $menu_items ) {
		// Try insert after orders.
		if ( isset( $menu_items['orders'] ) ) {
			$new_menu_items = array();
			foreach ( $menu_items as $key => $menu ) {
				$new_menu_items[ $key ] = $menu;
				if ( 'orders' === $key ) {
					$new_menu_items['my-tickets'] = __( 'My Tickets', 'woocommerce-box-office' );
				}
			}
			$menu_items = $new_menu_items;
		} else {
			$menu_items['my-tickets'] = __( 'My Tickets', 'woocommerce-box-office' );
		}

		return $menu_items;
	}

	/**
	 * Endpoint HTML content.
	 */
	public function endpoint_content() {
		wc_get_template( 'myaccount/my-tickets.php', array(), 'woocommerce-box-office', WCBO()->dir . 'templates/' );
	}

	/**
	 * Fix for endpoints on the homepage
	 *
	 * Based on WC_Query->pre_get_posts(), but only applies the fix for endpoints on the homepage from it
	 * instead of duplicating all the code to handle the main product query.
	 *
	 * @param mixed $q query object
	 */
	public function pre_get_posts( $q ) {
		// We only want to affect the main query
		if ( ! $q->is_main_query() ) {
			return;
		}

		if ( $q->is_home() && 'page' === get_option( 'show_on_front' ) && absint( get_option( 'page_on_front' ) ) !== absint( $q->get( 'page_id' ) ) ) {
			$_query = wp_parse_args( $q->query );
			if ( ! empty( $_query ) && array_intersect( array_keys( $_query ), array_keys( $this->query_vars ) ) ) {
				$q->is_page     = true;
				$q->is_home     = false;
				$q->is_singular = true;
				$q->set( 'page_id', (int) get_option( 'page_on_front' ) );
				add_filter( 'redirect_canonical', '__return_false' );
			}
		}
	}
}

