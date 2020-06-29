<?php

class WoocommerceGpfCacheInvalidator {

	/**
	 * Instance of the cache class.
	 *
	 * @var WoocommerceGpfCache
	 */
	private $cache;

	/**
	 * Keep track of post parents when posts are deleted so we can cache clean
	 * appropriately.
	 *
	 * @var array
	 */
	private $parent_map;

	/**
	 * Constructor.
	 *
	 * Hook all the events we are interested in.
	 */
	public function __construct( $cache ) {

		if ( ! $cache->is_enabled() ) {
			return;
		}

		// Store the cache instance.
		$this->cache = $cache;

		// Initialise the parent map.
		$this->parent_map = array();

		// When a product is added / updated, rebuild the cache for that
		// product.
		add_action( 'woocommerce_new_product', array( $this, 'save_product' ), 90 );
		add_action( 'woocommerce_update_product', array( $this, 'save_product' ), 90 );

		// If we don't attach to these we miss product gallery updates.
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product' ), 90, 2 );

		// When a product is trashed / removed drop its cache.
		add_action( 'trashed_post', array( $this, 'save_product' ), 90 );
		add_action( 'deleted_post', array( $this, 'save_product' ), 90 );

		// When a product is restored from the trash, build its cache.
		add_action( 'untrashed_post', array( $this, 'save_product' ), 90 );

		// When a variant is added / updated, rebuild the cache for the parent
		// product.
		add_action( 'woocommerce_new_product_variation', array( $this, 'save_variation' ), 90 );
		add_action( 'woocommerce_update_product_variation', array( $this, 'save_variation' ), 90 );

		// When a variant is removed, rebuild the cache for the parent product.
		add_action( 'wp_trash_post', array( $this, 'pre_delete_post' ), 90 );
		add_action( 'delete_post', array( $this, 'pre_delete_post' ), 90 );
		add_action( 'woocommerce_delete_product_variation', array( $this, 'remove_variation' ), 90 );
		add_action( 'woocommerce_trash_product_variation', array( $this, 'remove_variation' ), 90 );

		// When category / term / attribute updated, rebuild cache for affected products.
		add_action( 'edited_term', array( $this, 'rebuild_term' ), 90, 3 );

		// When a category / term / attribute is removed, rebuild cache for affected products.
		add_action( 'delete_term', array( $this, 'rebuild_term_objects' ), 90, 5 );

		// When plugin settings are changed, rebuild full cache.
		add_action( 'woocommerce_update_options_gpf', array( $this, 'rebuild_all' ), 90 );
	}

	/**
	 * Handle saving of a product.
	 *
	 * @param  int  $product_id  The product ID of the product being saved.
	 *
	 * @return void
	 */
	public function save_product( $product_id ) {
		$this->rebuild_product( $product_id );
	}


	/**
	 * Make a note of the post parent if there is one prior to deleting a
	 * variant.
	 */
	public function pre_delete_post( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post ||
			 'product_variation' !== $post->post_type ||
			 empty( $post->post_parent ) ) {
			return;
		}
		$this->parent_map[ $post_id ] = $post->post_parent;
	}

	/**
	 * Rebuild a cache for a variant.
	 *
	 * Triggers a rebuild of the main product since the minimum cache unit is
	 * the parent product, not individual variations.
	 *
	 * @param  int $product_id  The main product ID.
	 *
	 * @return void
	 */
	public function remove_variation( $product_id ) {
		$parent_id = isset( $this->parent_map[ $product_id ] ) ? $this->parent_map[ $product_id ] : null;
		if ( ! $parent_id ) {
			return;
		}
		$this->rebuild_product( $parent_id );
	}

	/**
	 * Rebuild a cache for a variant.
	 *
	 * Triggers a rebuild of the main product since the minimum cache unit is
	 * the parent product, not individual variations.
	 *
	 * @param  int $product_id  The main product ID.
	 * @param  int $idx         The index of the variation being updated.
	 *
	 * @return void
	 */
	public function save_variation( $product_id ) {
		$product   = wc_get_product( $product_id );
		$parent_id = $product->get_parent_id();
		$this->rebuild_product( $parent_id );
	}

	/**
	 * Rebuild the cache for a product.
	 *
	 * @param  int $product_id  The product ID of the product to rebuild.
	 *
	 * @return void
	 */
	public function rebuild_product( $product_id ) {
		$this->cache->flush_product( $product_id );
	}

	/**
	 * Rebuild the cache for all products attached to a term.
	 *
	 * @param  int $term_id  The term ID to refresh.
	 *
	 * @return void
	 */
	public function rebuild_term( $term_id, $tt_id, $taxonomy ) {
		$this->cache->flush_term( $term_id, $tt_id, $taxonomy );
	}

	/**
	 * Handle term deletion. Rebuild for all affected products.
	 *
	 * $object_ids is a list of all objects related to the term that has been
	 * deleted. They *may* not all be products so the background job needs to
	 * validate them before rebuilding.
	 */
	public function rebuild_term_objects( $term, $tt_id, $taxonomy, $deleted_term, $object_ids ) {
		$this->cache->flush_objects( $object_ids );
	}

	/**
	 * Rebuild the cache for all products.
	 *
	 * @return void
	 */
	public function rebuild_all() {
		$this->cache->flush_all();
	}
}
