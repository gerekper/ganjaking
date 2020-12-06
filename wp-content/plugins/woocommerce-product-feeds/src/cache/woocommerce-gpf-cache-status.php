<?php

/**
 * Class WoocommerceGpfCacheStatus
 *
 * Handles generation of information relating to the cache for the status report.
 */
class WoocommerceGpfCacheStatus {

	/**
	 * @var WoocommerceGpfCache
	 */
	protected $cache;

	/**
	 * @var WoocommerceGpfCommon
	 */
	protected $common;
	/**
	 * @var WoocommerceGpfTemplateLoader
	 */
	protected $template_loader;

	/**
	 * WoocommerceGpfCacheStatus constructor.
	 *
	 * @param WoocommerceGpfCommon $woocommerce_gpf_common
	 * @param WoocommerceGpfCache $woocommerce_gpf_cache
	 * @param WoocommerceGpfTemplateLoader $woocommerce_gpf_template_loader
	 */
	public function __construct(
		WoocommerceGpfCommon $woocommerce_gpf_common,
		WoocommerceGpfCache $woocommerce_gpf_cache,
		WoocommerceGpfTemplateLoader $woocommerce_gpf_template_loader
	) {
		$this->cache           = $woocommerce_gpf_cache;
		$this->common          = $woocommerce_gpf_common;
		$this->template_loader = $woocommerce_gpf_template_loader;
		add_filter( 'woocommerce_gpf_cache_status', [ $this, 'generate_status_output' ], 10, 2 );
	}

	/**
	 * @param $output
	 * @param $settings_url
	 *
	 * @return string
	 */
	public function generate_status_output( $output, $settings_url ) {

		global $wpdb, $table_prefix;

		if ( ! $this->cache->is_enabled() ) {
			return $output;
		}
		$feed_types = $this->common->get_feed_types();
		// Work out how many products we have cached per-feed type.
		$status  = array_fill_keys( array_keys( $feed_types ), 0 );
		$results = $wpdb->get_results(
			"SELECT `name`,
			        COUNT(DISTINCT(post_id)) AS total
			   FROM {$table_prefix}wc_gpf_render_cache
		   GROUP BY `name`",
			OBJECT_K
		);
		$results = wp_list_pluck( $results, 'total' );
		$status  = array_merge( $status, $results );

		// Work out the total number of eligible products.
		$args = array(
			'status'   => array( 'publish' ),
			'type'     => array( 'simple', 'variable', 'bundle' ),
			'limit'    => 1,
			'offset'   => 0,
			'return'   => 'ids',
			'paginate' => true,
		);

		$results     = wc_get_products(
			apply_filters( 'woocommerce_gpf_wc_get_products_args', $args, 'status' )
		);
		$total_cache = $results->total;
		$rebuild_url = wp_nonce_url(
			add_query_arg(
				array(
					'gpf_action' => 'rebuild_cache',
				),
				$settings_url
			),
			'gpf_rebuild_cache'
		);

		$status_items = '';
		foreach ( $feed_types as $feed_id => $feed_type ) {
			// Do not show feeds that aren't enabled.
			if ( ! $this->common->is_feed_enabled( $feed_id ) ) {
				continue;
			}
			$status_items .= $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'admin-cache-status-item',
				array(
					'name'   => $feed_type['name'],
					// Translators: Placeholders represent the number of items processed, and the total to be generated, e.g. 5 / 10
					'status' => sprintf( __( '<strong>%1$d</strong> / <strong>%2$d</strong> generated', 'woocommerce_gpf' ), $status[ $feed_id ], $total_cache ),
					'total'  => $total_cache,
				)
			);
		}
		$msg = '';
		if ( function_exists( 'as_next_scheduled_action' ) &&
			 (
				 as_next_scheduled_action( 'woocommerce_product_feeds_cache_clear_all' ) !== false ||
				 as_next_scheduled_action( 'woocommerce_product_feeds_cache_rebuild_all' ) !== false
			 )
		) {
			$msg = $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'admin-cache-rebuild-scheduled',
				[
					'msg' => esc_html( __( '** Cache rebuild scheduled **', 'woocommerce_gpf' ) ),
				]
			);
		}
		$cache_status_variables = array(
			'status_items' => $status_items,
			'rebuild_url'  => $rebuild_url,
			'settings_url' => $settings_url,
			'msg'          => $msg,
		);

		return $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'admin-cache-status',
			$cache_status_variables
		);
	}
}
