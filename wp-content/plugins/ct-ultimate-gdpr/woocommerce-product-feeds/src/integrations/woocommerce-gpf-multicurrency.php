<?php

use WOOMC\DAO\Factory;

class WoocommerceGpfMulticurrency {
	/**
	 * @var WoocommerceGpfTemplateLoader
	 */
	protected $template_loader;

	/**
	 * @var WoocommerceProductFeedsFeedConfigFactory
	 */
	protected $feed_config_factory;

	/**
	 * @var string
	 */
	private $currency = '';

	/**
	 * WoocommerceGpfMulticurrency constructor.
	 *
	 * @param WoocommerceProductFeedsFeedConfigFactory $feed_config_factory
	 * @param WoocommerceGpfTemplateLoader $template_loader
	 */
	public function __construct(
		WoocommerceProductFeedsFeedConfigFactory $feed_config_factory,
		WoocommerceGpfTemplateLoader $template_loader
	) {
		$this->template_loader     = $template_loader;
		$this->feed_config_factory = $feed_config_factory;
	}

	/**
	 * Capture the currency requested. Add hooks / filters.
	 */
	public function run() {
		// Allow a feed URL to be set to a specific currency.
		add_action( 'woocommerce_gpf_feed_edit_page', [ $this, 'render_feed_edit_page' ], 10, 2 );
		add_filter( 'woocommerce_gpf_feed_config_valid_extra_keys', [ $this, 'register_config_key' ] );
		add_filter( 'wp', [ $this, 'maybe_trigger_integration' ] );
		add_filter( 'woocommerce_gpf_feed_list_columns', [ $this, 'add_feed_list_column' ] );
		add_filter( 'woocommerce_gpf_feed_list_column_callback', [ $this, 'register_column_callback' ], 10, 2 );
	}

	/**
	 *
	 */
	public function maybe_trigger_integration() {
		$feed_config = $this->feed_config_factory->create_from_request();
		if ( is_null( $feed_config ) ) {
			return;
		}
		$this->currency = $_GET['currency'] ?? $feed_config->currency;
		if ( empty( $this->currency ) ) {
			return;
		}
		add_filter( 'woocommerce_multicurrency_override_currency', [ $this, 'override_forced_currency' ], 99 );
		add_filter( 'woocommerce_gpf_cache_name', array( $this, 'granularise_cache_name' ), 10, 1 );
		add_filter( 'woocommerce_gpf_feed_item', array( $this, 'add_currency_arg_to_product_permalinks' ), 10, 2 );
	}

	/**
	 * @param $currency
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function override_forced_currency( $currency ) {
		return $this->currency;
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public function granularise_cache_name( $name ) {
		return $name . '_' . $this->currency;
	}

	/**
	 * @param $feed_item
	 * @param $wc_product
	 *
	 * @return mixed
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function add_currency_arg_to_product_permalinks( $feed_item, $wc_product ) {
		$feed_item->purchase_link = add_query_arg(
			array(
				'currency' => $this->currency,
			),
			$feed_item->purchase_link
		);

		return $feed_item;
	}

	/**
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function add_feed_list_column( $columns ) {
		$pos = array_search( 'categories', array_keys( $columns ), true );
		return array_merge(
			array_slice( $columns, 0, $pos ),
			[
				'currency' => __( 'Currency', 'woocommerce_gpf' ),
			],
			array_slice( $columns, $pos, null )
		);
	}

	/**
	 * @param $callback
	 * @param $column
	 *
	 * @return array|null
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function register_column_callback( $callback, $column ) {
		if ( 'currency' === $column ) {
			return [ $this, 'column_currency' ];
		}
		return $callback;
	}

	/**
	 * @param $item
	 * @param $column_name
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function column_currency( $item, $column_name ) {
		$currency = $item->currency;
		if ( empty( $currency ) ) {
			$currency = '-';
		}
		echo esc_html( $currency );
	}

	/**
	 * Render the currency dropdown on the manage feed page for a feed.
	 *
	 * @param $feed
	 * @param $template_vars
	 */
	public function render_feed_edit_page( $feed, $template_vars ) {

		$template_vars['currency']   = $feed['currency'] ?? '';
		$template_vars['currencies'] = $this->get_currencies();
		$this->template_loader->output_template_with_variables(
			'woo-gpf',
			'admin-feed-edit-woocommerce-multicurrency',
			$template_vars
		);
		$base_dir = dirname( dirname( dirname( __FILE__ ) ) );
		wp_enqueue_script(
			'woo-gpf-admin-feed-edit-woocommerce-multicurrency',
			plugins_url( basename( $base_dir ) ) . '/js/admin-feed-edit-woocommerce-multicurrency.js',
			[ 'jquery' ],
			WOOCOMMERCE_GPF_VERSION,
			true
		);
	}

	/**
	 * @return array
	 */
	private function get_currencies() {
		$results = [];
		if ( ! is_callable( [ Factory::class, 'getDao' ] ) ) {
			return $results;
		}
		$wc_currencies    = \get_woocommerce_currencies();
		$default_currency = Factory::getDao()->getDefaultCurrency();
		$currencies       = Factory::getDao()->getEnabledCurrencies();
		// Put WooCommerce's default at the top of the list so it is the default.
		$results[ $default_currency ] = $wc_currencies[ $default_currency ] ?? $default_currency;

		// Add in the other enabled currencies.
		foreach ( $currencies as $currency_code ) {
			if ( $currency_code === $default_currency ) {
				continue;
			}
			$results[ $currency_code ] = $wc_currencies[ $currency_code ] ?? $currency_code;
		}

		return $results;
	}

	/**
	 * Register our key with the config class.
	 *
	 * @param $keys
	 *
	 * @return mixed
	 */
	public function register_config_key( $keys ) {
		$keys[] = 'currency';

		return $keys;
	}
}
