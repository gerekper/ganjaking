<?php

class WoocommerceGpfCache {

	/**
	 * Worker classes.
	 *
	 * @var array
	 */
	private static $jobs;

	/**
	 * Whether to use the render cache.
	 *
	 * @var boolean
	 */
	private $cache_enabled;

	/**
	 * Constructor.
	 *
	 * Work out if the cache is enabled or not. Trigger initialisation of worker
	 * processes.
	 */
	public function __construct() {

		global $wp_version;

		// Cache is disabled by default. It can be enabled via a filter.
		// Requires WooCommerce 3.0.0+ and WP 4.5.0+
		$this->cache_enabled = apply_filters( 'woocommerce_gpf_render_cache_enabled', false );
		add_action( 'init', array( $this, 'init_workers' ), 9 );
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
	 * Initialise queue workers.
	 */
	public function init_workers() {
		// Do nothing if the cache is disabled, or we've already hooked up
		// the workers.
		if ( ! $this->cache_enabled || ! empty( self::$jobs ) ) {
			return;
		}
		// Instantiate worker queues.
		$job_types = array(
			'WoocommerceGpfRebuildProductJob',
			'WoocommerceGpfRebuildTermJob',
			'WoocommerceGpfRebuildAllJob',
		);
		foreach ( $job_types as $job_type ) {
			self::$jobs[ $job_type ] = new $job_type();
		}
		// Register a shutdown function in this instance only to dispatch the
		// workers.
		add_action( 'shutdown', array( $this, 'dispatch_workers' ) );
	}

	/**
	 * Fetch multiple items from the cache.
	 *
	 * @param  array    $post_ids    Array of post IDs
	 * @param  string   $name         The cache name to get for these items.
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
		$results      = $wpdb->get_results(
			$wpdb->prepare(
				$sql,
				$post_ids
			),
			OBJECT_K
		);
		$results      = wp_list_pluck( $results, 'value', 'post_id' );
		return $results;
	}

	/**
	* Fetch an item from the cache.
	*
	* @param  int    $post_id       The post ID that this item is attached to.
	* @param  string $name          The cache name to get for this item.
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
		return $wpdb->get_var(
			$wpdb->prepare(
				$sql,
				$post_id,
				$cache_name
			)
		);
	}

	/**
	* Store / update an item in the cache.
	*
	* @param  int    $post_id       The post ID that this item is attached to.
	* @param  string $name           The cache name to get for this item.
	* @param  string $value         The value to store.
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
	 * @param  int  $post_id    The product's post ID to be cleared down.
	 *
	 * @return
	 */
	public function flush_product( $post_id ) {
		if ( ! $this->cache_enabled ) {
			return;
		}
		self::$jobs['WoocommerceGpfRebuildProductJob']
			->push_to_queue( array( 'post_id' => $post_id ) );
	}

	/**
	 * Drop objects from the cache, and request a rebuild for them.
	 *
	 * We queue a RebuildProductJob. That will validate that the object is
	 * indeed a product before acting, and ignore it if not.
	 *
	 * @param  array  $object_ids    The object IDs to be cleared down.
	 *
	 * @return
	 */
	public function flush_objects( $object_ids ) {
		if ( ! $this->cache_enabled ) {
			return;
		}
		foreach ( $object_ids as $object_id ) {
			self::$jobs['WoocommerceGpfRebuildProductJob']
				->push_to_queue( array( 'post_id' => $object_id ) );
		}
	}

	/**
	 * Flush any products with a specific term, and rebuild them.
	 */
	public function flush_term( $term_id, $tt_id, $taxonomy ) {
		if ( ! $this->cache_enabled ) {
			return;
		}
		self::$jobs['WoocommerceGpfRebuildTermJob']
			->push_to_queue(
				array(
					'term_id'  => $term_id,
					'taxonomy' => $taxonomy,
				)
			);
	}

	/**
	 * Clear the cache, and trigger a rebuild.
	 */
	public function flush_all() {
		if ( ! $this->cache_enabled ) {
			return;
		}
		// Clear the rebuild all queue to abort any in-progress rebuild.
		self::$jobs['WoocommerceGpfRebuildAllJob']->cancel_all();
		self::$jobs['WoocommerceGpfRebuildProductJob']->cancel_all();
		self::$jobs['WoocommerceGpfRebuildTermJob']->cancel_all();
		self::$jobs['WoocommerceGpfRebuildAllJob']
			->push_to_queue( array( 'task' => 'clear-all' ) );
	}

	/**
	 * Iterate all jobs and save & dispatch any that are dirty.
	 */
	public function dispatch_workers() {
		foreach ( self::$jobs as $idx => $job ) {
			$job->dispatch_if_dirty();
		}
	}

	/**
	 * Get debug information about the current status of the cache queues.
	 */
	public function get_debug() {
		$content = '<hr><h3>Queue status</h3>';
		foreach ( self::$jobs as $k => $job ) {
			$content .= '<h4>' . $k . '</h4>';
			$tasks    = $job->get_all_batches();
			if ( empty( $tasks ) ) {
				$content .= '<p>No tasks in queue.</p>';
			} else {
				foreach ( $tasks as $task ) {
					$content     .= '<ul class="ul-disc">';
					$task_details = unserialize( $task );
					foreach ( $task_details as $task_detail ) {
						$content .= '<li>';
						$idx      = 0;
						foreach ( $task_detail as $k => $v ) {
							if ( $idx++ ) {
								$content .= ', ';
							}
							$content .= '<strong>' . ucfirst( esc_html( $k ) ) . ': </strong>' . esc_html( $v );
						}
						$content .= '</li>';
					}
					$content .= '</ul>';
				}
			}
		}
		return $content;
	}
}
