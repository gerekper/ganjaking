<?php
/**
 * Data Store: Product Catalog.
 *
 * @package WC_Instagram/Data_Stores
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Instagram_Data_Store', false ) ) {
	include_once WC_INSTAGRAM_PATH . 'includes/abstracts/abstract-wc-instagram-data-store.php';
}

/**
 * Class WC_Instagram_Data_Store_Product_Catalog_CPT.
 */
class WC_Instagram_Data_Store_Product_Catalog_CPT extends WC_Instagram_Data_Store implements WC_Object_Data_Store_Interface {

	/**
	 * Maps the metadata with the object properties.
	 *
	 * @since 4.0.0
	 *
	 * @var array An array of pairs [meta_key => property_key].
	 */
	protected $meta_key_to_props = array(
		'filter_by'                   => 'filter_by',
		'products_option'             => 'products_option',
		'product_cats_option'         => 'product_cats_option',
		'product_cats'                => 'product_cats',
		'product_types_option'        => 'product_types_option',
		'product_types'               => 'product_types',
		'virtual_products'            => 'virtual_products',
		'downloadable_products'       => 'downloadable_products',
		'stock_status'                => 'stock_status',
		'include_product_ids'         => 'include_product_ids',
		'exclude_product_ids'         => 'exclude_product_ids',
		'id_format'                   => 'id_format',
		'group_id_format'             => 'group_id_format',
		'mpn_format'                  => 'mpn_format',
		'brand'                       => 'brand',
		'google_product_category'     => 'google_product_category',
		'condition'                   => 'condition',
		'images_option'               => 'images_option',
		'include_variations'          => 'include_variations',
		'include_currency'            => 'include_currency',
		'description_field'           => 'description_field',
		'variation_description_field' => 'variation_description_field',
		'default_description'         => 'default_description',
		'include_tax'                 => 'include_tax',
		'tax_location'                => 'tax_location',
		'include_stock'               => 'include_stock',
		'stock_quantity'              => 'stock_quantity',
		'backorder_stock_quantity'    => 'backorder_stock_quantity',
	);

	/**
	 * Maps the type of each meta key for sanitizing the values in the BD.
	 *
	 * Include only the meta keys that need sanitization (dates, booleans, etc.).
	 *
	 * @var array
	 */
	protected $meta_key_types = array(
		'include_variations' => 'bool',
		'include_currency'   => 'bool',
		'include_tax'        => 'bool',
		'include_stock'      => 'bool',
	);

	/**
	 * Constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		$this->internal_meta_keys = array_keys( $this->meta_key_to_props );
	}

	/**
	 * Creates a new product catalog in the database.
	 *
	 * @since 4.0.0
	 *
	 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
	 */
	public function create( &$product_catalog ) {
		$data = array(
			'post_type'   => 'wc_instagram_catalog',
			'post_status' => 'publish',
			'post_title'  => $product_catalog->get_title(),
			'post_name'   => $product_catalog->get_slug(),
			'post_author' => 1,
		);

		/**
		 * Filters the product catalog data before creating it.
		 *
		 * @since 4.0.0
		 *
		 * @param array $data An array with the product catalog data.
		 */
		$data = apply_filters( 'wc_instagram_new_product_catalog_data', $data );

		$catalog_id = wp_insert_post( $data, true );

		if ( ! $catalog_id || is_wp_error( $catalog_id ) ) {
			return;
		}

		$product_catalog->set_id( $catalog_id );
		$product_catalog->set_status( 'publish' );

		$this->update_post_meta( $product_catalog );

		$product_catalog->save_meta_data();
		$product_catalog->apply_changes();

		/**
		 * Fires after creating a product catalog.
		 *
		 * @since 4.0.0
		 *
		 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
		 */
		do_action( 'wc_instagram_product_catalog_created', $product_catalog );
	}

	/**
	 * Reads a product catalog from the database.
	 *
	 * @since 4.0.0
	 *
	 * @throws Exception If the product catalog is invalid.
	 *
	 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
	 */
	public function read( &$product_catalog ) {
		$product_catalog->set_defaults();

		$catalog_id  = $product_catalog->get_id();
		$post_object = ( $catalog_id ? get_post( $catalog_id ) : null );

		if ( ! $post_object || 'wc_instagram_catalog' !== $post_object->post_type ) {
			throw new Exception( 'Invalid product catalog.' );
		}

		$product_catalog->set_props(
			array(
				'title'  => $post_object->post_title,
				'slug'   => $post_object->post_name,
				'status' => $post_object->post_status,
			)
		);

		$this->read_post_meta( $product_catalog );

		$product_catalog->read_meta_data();
		$product_catalog->set_object_read();

		/**
		 * Fires after loading the product catalog data.
		 *
		 * @since 1.0.0
		 *
		 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
		 */
		do_action( 'wc_instagram_product_catalog_loaded', $product_catalog );
	}

	/**
	 * Updates a product catalog in the database.
	 *
	 * @since 4.0.0
	 *
	 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
	 */
	public function update( &$product_catalog ) {
		$product_catalog->save_meta_data();

		$changes = $product_catalog->get_changes();

		// Only update when the post data changes.
		if ( array_intersect( array( 'title', 'slug', 'status' ), array_keys( $changes ) ) ) {
			$post_data = array(
				'post_title'        => $product_catalog->get_title(),
				'post_name'         => $product_catalog->get_slug(),
				'post_status'       => $product_catalog->get_status(),
				'post_modified'     => current_time( 'mysql' ),
				'post_modified_gmt' => current_time( 'mysql', 1 ),
			);

			/**
			 * When updating this object, to prevent infinite loops, use $wpdb
			 * to update data, since wp_update_post spawns more calls to the
			 * save_post action.
			 *
			 * This ensures hooks are fired by either WP itself (admin screen save),
			 * or an update purely from CRUD.
			 */
			if ( doing_action( 'save_post' ) ) {
				$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $product_catalog->get_id() ) );
				clean_post_cache( $product_catalog->get_id() );
			} else {
				wp_update_post( array_merge( array( 'ID' => $product_catalog->get_id() ), $post_data ) );
			}

			// Refresh internal metadata, in case things were hooked into `save_post` or another WP hook.
			$product_catalog->read_meta_data( true );
		}

		$this->update_post_meta( $product_catalog );

		$product_catalog->apply_changes();

		/**
		 * Fires after updating the product catalog.
		 *
		 * @since 4.0.0
		 *
		 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
		 */
		do_action( 'wc_instagram_product_catalog_updated', $product_catalog );
	}

	/**
	 * Deletes a product catalog from the database.
	 *
	 * @since 4.0.0
	 *
	 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
	 * @param array                        $args            Optional. Additional arguments. Default empty.
	 * @return bool
	 */
	public function delete( &$product_catalog, $args = array() ) {
		$catalog_id = $product_catalog->get_id();

		if ( ! $catalog_id ) {
			return false;
		}

		$args = wp_parse_args(
			$args,
			array(
				'force_delete' => false,
			)
		);

		/**
		 * Fires before deleting a product catalog.
		 *
		 * @since 4.0.0
		 *
		 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
		 * @param array                        $args            Arguments passed to the delete method.
		 */
		do_action( 'wc_before_delete_instagram_product_catalog', $product_catalog, $args );

		if ( $args['force_delete'] ) {
			wp_delete_post( $catalog_id, true );
			$product_catalog->set_id( 0 );

			/**
			 * Fires after deleting a product catalog.
			 *
			 * @since 4.0.0
			 *
			 * @param int $catalog_id Product catalog ID.
			 */
			do_action( 'wc_instagram_product_catalog_deleted', $catalog_id );
		} else {
			wp_trash_post( $catalog_id );
			$product_catalog->set_status( 'trash' );

			/**
			 * Fires after trashing a product catalog.
			 *
			 * @since 4.0.0
			 *
			 * @param int $catalog_id Product catalog ID.
			 */
			do_action( 'wc_instagram_product_catalog_trashed', $catalog_id );
		}

		return true;
	}
}
