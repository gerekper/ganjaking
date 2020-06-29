<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FUE_Query {

	/**
	 * @var array The query vars to add to WP
	 */
	public $query_vars = array();

	/**
	 * Class constructor to hook in methods
	 */
	public function __construct() {
		add_action( 'init', array($this, 'add_endpoints') );

		if (! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars'), 0 );
			add_action( 'parse_request', array( $this, 'parse_request'), 0 );
			add_action( 'template_redirect', array( $this, 'load_template' ), 0 );
		}

		$this->init_query_vars();
	}

	/**
	 * Define the query vars the FUE uses
	 */
	public function init_query_vars() {
		$endpoints = $this->get_endpoints();

		$this->query_vars = array(
			$endpoints['unsubscribe']                           => $endpoints['unsubscribe'],
			'email-unsubscribe'                                 => 'email-unsubscribe',
			'my-account/'. $endpoints['email_subscriptions']    => 'my-account/'. $endpoints['email_subscriptions'],
			$endpoints['email_subscriptions']                   => $endpoints['email_subscriptions'],
			$endpoints['email_preferences']                     => $endpoints['email_preferences']
		);
	}

	/**
	 * Register the endpoints
	 */
	public function add_endpoints() {
		foreach ( $this->query_vars as $key => $var ) {
			add_rewrite_endpoint( $var, EP_ROOT | EP_PAGES );
		}
	}

	/**
	 * add_query_vars function.
	 *
	 * @access public
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->query_vars as $key => $var )
			$vars[] = $key;

		return $vars;
	}

	/**
	 * Get query vars
	 *
	 * @return array
	 */
	public function get_query_vars() {
		return $this->query_vars;
	}

	/**
	 * Parse the request and look for query vars - endpoints may not be supported
	 */
	public function parse_request() {
		global $wp;

		// Map query vars to their keys, or get them if endpoints are not supported
		foreach ( $this->query_vars as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$wp->query_vars[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $var ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			} elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}

	/**
	 * Load FUE endpoint templates
	 */
	public function load_template() {
		global $wp;

		$endpoints = $this->get_endpoints();

		// redirect old query vars for backwards-compatibility
		if ( isset( $wp->query_vars['email-unsubscribe'] ) && $endpoints['unsubscribe'] != 'email-unsubscribe' ) {
			wp_safe_redirect( site_url( '/'. $endpoints['unsubscribe'] ) );
			exit;
		}

		if ( isset( $wp->query_vars[ $endpoints['unsubscribe'] ] ) ) {
			fue_get_template( 'email-unsubscribe.php', array(), 'follow-up-emails', trailingslashit( FUE_TEMPLATES_DIR ) );
			exit;
		} elseif ( isset( $wp->query_vars[ $endpoints['email_subscriptions'] ] ) || isset( $wp->query_vars['my-account/'. $endpoints['email_subscriptions'] ] ) ) {
			fue_get_template( 'email-subscriptions.php', array(), 'follow-up-emails', trailingslashit( FUE_TEMPLATES_DIR ) );
			exit;
		} elseif ( isset( $wp->query_vars[ $endpoints['email_preferences'] ] ) ) {
			fue_get_template( 'myaccount/email-preferences.php', array(), 'follow-up-emails', trailingslashit( FUE_TEMPLATES_DIR ) );
			exit;
		}

	}

	private function get_endpoints() {
		$unsubscribe            = get_option( 'fue_unsubscribe_endpoint', 'unsubscribe' );
		$email_subscriptions    = get_option( 'fue_email_subscriptions_endpoint', 'email-subscriptions' );
		$email_preferences      = get_option( 'fue_email_preferences_endpoint', 'email-preferences' );

		return apply_filters('fue_query_endpoints', array(
			'unsubscribe'           => $unsubscribe,
			'email_subscriptions'   => $email_subscriptions,
			'email_preferences'     => $email_preferences
		) );
	}

}
