<?php

use Pimple\Container;

/**
 * Class WoocommerceGpfCache
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class WoocommerceGpfCache {

	/**
	 * @var array
	 */
	private static $jobs = [];

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * Whether to use the render cache.
	 *
	 * @var boolean
	 */
	private $cache_enabled = false;

	/**
	 * @var WoocommerceGpfCacheInvalidator
	 */
	private $cache_invalidator;

	/**
	 * Constructor.
	 *
	 * Work out if the cache is enabled or not. Trigger initialisation of worker
	 * processes.
	 *
	 * @param Container $container
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
		add_action( 'plugins_loaded', [ $this, 'enable_cache' ] );
		add_action( 'init', [ $this, 'init_workers' ], 9 );

	}

	/**
	 * Enable the cache via a filter if required.
	 */
	public function enable_cache() {
		// Cache is disabled by default. It can be enabled via a filter.
		$this->cache_enabled = apply_filters( 'woocommerce_gpf_render_cache_enabled', $this->cache_enabled );
		if ( $this->cache_enabled ) {
			$this->cache_invalidator = $this->container['WoocommerceGpfCacheInvalidator'];
			$this->cache_invalidator->initialise();
		}
	}

	/**
	 * Init instances for all cache jobs.
	 */
	public function init_workers() {
		// Bail if we've already created instances.
		if ( ! empty( self::$jobs ) || ! $this->cache_enabled ) {
			return;
		}
		// Instantiate worker queues.
		$job_types = [
			'WoocommerceGpfClearAllJob',
			'WoocommerceGpfClearProductJob',
			'WoocommerceGpfRebuildSimpleJob',
			'WoocommerceGpfRebuildComplexJob',
			'WoocommerceGpfRebuildProductJob',
		];
		foreach ( $job_types as $job_type ) {
			self::$jobs[ $job_type ] = $this->container[ $job_type ];
		}
	}

	/**
	 * Allow external classes to see if the cache is enabled.
	 *
	 * @return boolean  True if the cache is enabled. False otherwise.
	 */
	public function is_enabled() {
		return $this->cache_enabled;
	}

	/**
	 * Fetch multiple items from the cache.
	 *
	 * @param array $post_ids Array of post IDs
	 * @param string $name The cache name to get for these items.
	 *
	 * @return array                 Array of post_id => cached_value for all matched items.
	 */
	public function fetch_multi( $post_ids, $name ) {
		global $wpdb, $table_prefix;

		if ( ! $this->cache_enabled ) {
			return array();
		}

		$cache_name = apply_filters( 'woocommerce_gpf_cache_name', $name );

		$placeholders = array_fill( 0, count( $post_ids ), '%d' );
		$placeholders = implode( ', ', $placeholders );
		$sql          = "SELECT `post_id`, `value`
		          FROM {$table_prefix}wc_gpf_render_cache
				 WHERE `post_id` IN ($placeholders)
				   AND `name` = %s";
		$post_ids[]   = $cache_name;
		$results      = $wpdb->get_results( $wpdb->prepare( $sql, $post_ids ), OBJECT_K );
		$results      = wp_list_pluck( $results, 'value', 'post_id' );

		return $results;
	}

	/**
	 * Fetch an item from the cache.
	 *
	 * @param int $post_id The post ID that this item is attached to.
	 * @param string $name The cache name to get for this item.
	 *
	 * @return string|null           Cached value, or null.
	 */
	public function fetch( $post_id, $name ) {
		global $wpdb, $table_prefix;

		if ( ! $this->cache_enabled ) {
			return null;
		}
		$cache_name = apply_filters( 'woocommerce_gpf_cache_name', $name );
		$sql        = "SELECT `value`
		          FROM {$table_prefix}wc_gpf_render_cache
				 WHERE `post_id` = %d
				   AND `name` = %s";

		return $wpdb->get_var( $wpdb->prepare( $sql, $post_id, $cache_name ) );
	}

	/**
	 * Store / update an item in the cache.
	 *
	 * @param int $post_id The post ID that this item is attached to.
	 * @param string $name The cache name to get for this item.
	 * @param string $value The value to store.
	 */
	public function store( $post_id, $name, $value ) {
		global $wpdb, $table_prefix;
		if ( ! $this->cache_enabled ) {
			return;
		}
		$cache_name = apply_filters( 'woocommerce_gpf_cache_name', $name );
		$cache_id   = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT `id`
				   FROM {$table_prefix}wc_gpf_render_cache
				  WHERE `post_id` = %d
				    AND `name` = %s",
				$post_id,
				$cache_name
			)
		);
		if ( is_null( $cache_id ) ) {
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$table_prefix}wc_gpf_render_cache
					             (`post_id`, `name`, `value`)
						  VALUES ( %d, %s, %s )",
					$post_id,
					$cache_name,
					$value
				)
			);
		} else {
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$table_prefix}wc_gpf_render_cache
					    SET `value` = %s
					  WHERE id = %d",
					$value,
					$cache_id
				)
			);
		}
	}

	/**
	 * Drop a specific product's data from the cache, and request a rebuild for it.
	 *
	 * @param int $post_id The product's post ID to be cleared down.
	 *
	 * @return
	 */
	public function flush_product( $post_id ) {
		if ( ! $this->cache_enabled ) {
			return;
		}
		$pending = as_get_scheduled_actions(
			[
				'hook'   => 'woocommerce_product_feeds_cache_rebuild_product',
				'args'   => [ $post_id ],
				'status' => \ActionScheduler_Store::STATUS_PENDING,
			]
		);
		if ( empty( $pending ) ) {
			as_schedule_single_action(
				null,
				'woocommerce_product_feeds_cache_rebuild_product',
				[
					$post_id,
				],
				'woocommerce-product-feeds'
			);
		}
	}

	public function clear_product( $post_id ) {
		if ( ! $this->cache_enabled ) {
			return;
		}
		$pending = as_get_scheduled_actions(
			[
				'hook'   => 'woocommerce_product_feeds_cache_clear_product',
				'args'   => [ $post_id ],
				'status' => \ActionScheduler_Store::STATUS_PENDING,
			]
		);
		if ( empty( $pending ) ) {
			as_schedule_single_action(
				null,
				'woocommerce_product_feeds_cache_clear_product',
				[
					$post_id,
				],
				'woocommerce-product-feeds'
			);
		}
	}

	/**
	 * Drop objects from the cache, and request a rebuild for them.
	 *
	 * We queue a RebuildProductJob. That will validate that the object is
	 * indeed a product before acting, and ignore it if not.
	 *
	 * @param array $post_ids The object IDs to be cleared down.
	 *
	 * @return
	 */
	public function flush_objects( $post_ids ) {
		if ( ! $this->cache_enabled ) {
			return;
		}
		foreach ( $post_ids as $post_id ) {
			as_schedule_single_action(
				null,
				'woocommerce_product_feeds_cache_rebuild_product',
				[
					$post_id,
				],
				'woocommerce-product-feeds'
			);
		}
	}

	/**
	 * Flush any products with a specific term, and rebuild them.
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function flush_term( $term_id, $tt_id, $taxonomy ) {
		if ( ! $this->cache_enabled ) {
			return;
		}
		as_schedule_single_action(
			null,
			'woocommerce_product_feeds_cache_rebuild_simple',
			[
				0,
				apply_filters( 'woocommerce_product_feeds_rebuild_chunk_limit_simple', 30 ),
				[
					'taxonomy' => $taxonomy,
					'term_id'  => $term_id,
				],
			],
			'woocommerce-product-feeds'
		);
		as_schedule_single_action(
			null,
			'woocommerce_product_feeds_cache_rebuild_complex',
			[
				0,
				apply_filters( 'woocommerce_product_feeds_rebuild_chunk_limit_complex', 1 ),
				[
					'taxonomy' => $taxonomy,
					'term_id'  => $term_id,
				],
			],
			'woocommerce-product-feeds'
		);
	}

	/**
	 * Clear the cache, and trigger a rebuild.
	 *
	 * @SuppressWarnings(PHPMD.UndefinedVariable)
	 */
	public function flush_all() {
		if ( ! $this->cache_enabled || empty( self::$jobs ) ) {
			return;
		}
		// Clear the job queues to abort any in-progress rebuild, then trigger a clear and rebuild.
		self::$jobs['WoocommerceGpfClearAllJob']->cancel_all();
		self::$jobs['WoocommerceGpfClearProductJob']->cancel_all();
		self::$jobs['WoocommerceGpfRebuildSimpleJob']->cancel_all();
		self::$jobs['WoocommerceGpfRebuildComplexJob']->cancel_all();
		self::$jobs['WoocommerceGpfRebuildProductJob']->cancel_all();
		as_schedule_single_action(
			null,
			'woocommerce_product_feeds_cache_clear_all',
			[],
			'woocommerce-product-feeds'
		);
	}
}
