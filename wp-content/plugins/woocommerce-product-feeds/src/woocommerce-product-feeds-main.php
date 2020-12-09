<?php

use Pimple\Container;

class WoocommerceProductFeedsMain {

	/********************************************************
	 * Dependencies.
	 *******************************************************/

	/**
	 * @var WoocommerceGpfImportExportIntegration
	 */
	protected $import_export_integration;

	/**
	 * @var WoocommerceGpfStructuredData
	 */
	protected $structured_data;

	/**
	 * @var WoocommerceGpfCache
	 */
	protected $cache;

	/**
	 * @var WoocommerceProductFeedsIntegrationManager
	 */
	protected $integration_manager;

	/**
	 * @var WoocommerceGpfCommon
	 */
	protected $common;

	/**
	 * @var Container
	 */
	protected $container;

	/********************************************************
	 * Admin side dependencies.
	 *******************************************************/

	/**
	 * @var WoocommerceGpfAdmin
	 */
	protected $gpf_admin;

	/**
	 * @var WoocommercePrfAdmin
	 */
	protected $prf_admin;

	/**
	 * @var WoocommerceGpfStatusReport
	 */
	protected $status_report;

	/********************************************************
	 * Used if we are generating a feed
	 *******************************************************/

	/**
	 * @var WoocommerceGpfFrontend
	 */
	protected $frontend;

	/**
	 * @var WoocommercePrfGoogleReviewFeed
	 */
	protected $google_review_feed;

	/**
	 * @var WoocommerceGpfRestApi
	 */
	private $rest_api;

	/**
	 * @var WoocommerceProductFeedsExpandedStructuredData
	 */
	private $expanded_structured_data;

	/**
	 * @var WoocommerceProductFeedsExpandedStructuredDataCacheInvalidator
	 */
	private $expanded_structured_data_cache_invalidator;

	/**
	 * WoocommerceProductFeedsMain constructor.
	 *
	 * @param WoocommerceGpfCommon $woocommerce_gpf_common
	 * @param WoocommerceGpfCache $woocommerce_gpf_cache
	 * @param WoocommerceProductFeedsIntegrationManager $integration_manager
	 * @param Container $container
	 */
	public function __construct(
		WoocommerceGpfCommon $woocommerce_gpf_common,
		WoocommerceGpfCache $woocommerce_gpf_cache,
		WoocommerceProductFeedsIntegrationManager $integration_manager,
		Container $container
	) {
		$this->common              = $woocommerce_gpf_common;
		$this->cache               = $woocommerce_gpf_cache;
		$this->integration_manager = $integration_manager;
		$this->container           = $container;
	}

	public function run() {

		$settings            = get_option( 'woocommerce_gpf_config', [] );
		$use_expanded_schema = isset( $settings['expanded_schema'] ) && 'on' === $settings['expanded_schema'];
		if ( is_admin() ) {
			$this->gpf_admin                 = $this->container['WoocommerceGpfAdmin'];
			$this->gpf_db_manager            = $this->container['WoocommerceProductFeedsDbManager'];
			$this->status_report             = $this->container['WoocommerceGpfStatusReport'];
			$this->prf_admin                 = $this->container['WoocommercePrfAdmin'];
			$this->import_export_integration = $this->container['WoocommerceGpfImportExportIntegration'];
			$this->gpf_admin->initialise();
			$this->gpf_db_manager->initialise();
			$this->status_report->initialise();
			$this->prf_admin->initialise();
			$this->import_export_integration->initialise();
		} else {
			if ( $use_expanded_schema ) {
				$this->expanded_structured_data = $this->container['WoocommerceProductFeedsExpandedStructuredData'];
				$this->expanded_structured_data->initialise();
			} else {
				$this->structured_data = $this->container['WoocommerceGpfStructuredData'];
				$this->structured_data->initialise();
			}
		}
		if ( $use_expanded_schema ) {
			$this->expanded_structured_data_cache_invalidator =
				$this->container['WoocommerceProductFeedsExpandedStructuredDataCacheInvalidator'];
			$this->expanded_structured_data_cache_invalidator->initialise();
		}

		$this->rest_api = $this->container['WoocommerceGpfRestApi'];
		$this->rest_api->initialise();

		add_action( 'plugins_loaded', [ $this->common, 'initialise' ], 1 );
		add_action( 'plugins_loaded', [ $this->integration_manager, 'initialise' ] );
		add_action( 'plugins_loaded', [ $this, 'block_wordpress_gzip_compression' ] );
		add_action( 'init', [ $this, 'register_endpoints' ] );
		add_action( 'template_redirect', [ $this, 'trigger_feeds' ] );
		add_filter(
			'woocommerce_customer_default_location_array',
			[ $this, 'set_customer_default_location' ]
		);
		add_filter( 'http_request_args', [ $this, 'prevent_wporg_update_check' ], 10, 2 );
	}

	/**
	 * Block wordpress.org plugins with similar names overwriting through WordPress' update mechanism.
	 *
	 * @param $request
	 * @param $url
	 *
	 * @return mixed
	 */
	public function prevent_wporg_update_check( $request, $url ) {
		if ( 0 === strpos( $url, 'https://api.wordpress.org/plugins/update-check/' ) ) {
			$my_plugin = plugin_basename( __FILE__ );
			$plugins   = @json_decode( $request['body']['plugins'], true );
			if ( null === $plugins ) {
				return $request;
			}
			// Freemius updater creates a request without the active array set.
			if ( isset( $plugins['active'] ) && is_array( $plugins['active'] ) ) {
				unset( $plugins['active'][ array_search( $my_plugin, $plugins['active'], true ) ] );
			}
			unset( $plugins['plugins'][ $my_plugin ] );
			$request['body']['plugins'] = wp_json_encode( $plugins );
		}

		return $request;
	}

	/**
	 * Disable attempts to GZIP the feed output to avoid memory issues.
	 */
	public function block_wordpress_gzip_compression() {
		if ( isset( $_GET['woocommerce_gpf'] ) ) {
			remove_action( 'init', 'ezgz_buffer' );
		}
	}

	/**
	 * Override the default customer address.
	 */
	public function set_customer_default_location( $location ) {
		if ( woocommerce_gpf_is_generating_feed() ) {
			return wc_format_country_state_string( get_option( 'woocommerce_default_country' ) );
		} else {
			return $location;
		}
	}

	/**
	 * Bodge for WPEngine.com users - provide the feed at a URL that doesn't
	 * rely on query arguments as WPEngine don't support URLs with query args
	 * if the requestor is a googlebot. #broken
	 */
	public function register_endpoints() {
		add_rewrite_tag( '%woocommerce_gpf%', '([^/]+)' );
		add_rewrite_tag( '%gpf_start%', '([0-9]{1,})' );
		add_rewrite_tag( '%gpf_limit%', '([0-9]{1,})' );
		add_rewrite_tag( '%gpf_categories%', '^(\d+(,\d+)*)?$' );
		add_rewrite_rule( 'woocommerce_gpf/([^/]+)/gpf_start/([0-9]{1,})/gpf_limit/([0-9]{1,})/gpf_categories/(\d+(,\d+)*)', 'index.php?woocommerce_gpf=$matches[1]&gpf_start=$matches[2]&gpf_limit=$matches[3]&gpf_categories=$matches[4]', 'top' );
		add_rewrite_rule( 'woocommerce_gpf/([^/]+)/gpf_start/([0-9]{1,})/gpf_limit/([0-9]{1,})', 'index.php?woocommerce_gpf=$matches[1]&gpf_start=$matches[2]&gpf_limit=$matches[3]', 'top' );
		add_rewrite_rule( 'woocommerce_gpf/([^/]+)/gpf_start/([0-9]{1,})', 'index.php?woocommerce_gpf=$matches[1]&gpf_start=$matches[2]', 'top' );
		add_rewrite_rule( 'woocommerce_gpf/([^/]+)/gpf_categories/(\d+(,\d+)*)', 'index.php?woocommerce_gpf=$matches[1]&gpf_categories=$matches[2]', 'top' );
		add_rewrite_rule( 'woocommerce_gpf/([^/]+)', 'index.php?woocommerce_gpf=$matches[1]', 'top' );
	}

	/**
	 * Instantiate the relevant classes dependant on the feed request type.
	 */
	public function trigger_feeds() {

		global $wp_query;

		// Parsing for legacy URLs.
		if ( isset( $_REQUEST['action'] ) && 'woocommerce_gpf' === $_REQUEST['action'] ) {
			if ( isset( $_REQUEST['feed_format'] ) ) {
				$wp_query->query_vars['woocommerce_gpf'] = $_REQUEST['feed_format'];
			} else {
				$wp_query->query_vars['woocommerce_gpf'] = 'google';
			}
		}

		if ( isset( $wp_query->query_vars['woocommerce_gpf'] ) ) {
			if ( 'googlereview' === $wp_query->query_vars['woocommerce_gpf'] ) {
				$this->google_review_feed = $this->container['WoocommercePrfGoogleReviewFeed'];
				$this->google_review_feed->initialise();
			} else {
				$this->frontend = $this->container['WoocommerceGpfFrontend'];
				$this->frontend->initialise();
			}
		}
	}
}
