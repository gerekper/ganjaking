<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with WooCommerce Product Bundles.
 * Version tested: 5.8.1.
 *
 * It handles the copy or synchronization of products metas
 * and the translation of the cart when the language is switched.
 *
 * @since 1.1
 */
class PLLWC_Product_Bundles {
	/**
	 * Array of cart keys with original as key and translation as value.
	 *
	 * @var array
	 */
	protected $translated_cart_keys;

	/**
	 * An array of cart item stamp.
	 *
	 * @var array
	 */
	private $stamps;

	/**
	 * Constructor.
	 * Setups actions and filters.
	 *
	 * @since 1.1
	 */
	public function __construct() {
		// Copy and synchronization.
		add_action( 'pllwc_copy_product', array( $this, 'copy_product' ), 10, 4 );
		add_filter( 'pllwc_copy_post_metas', array( $this, 'copy_product_metas' ) );

		// Cart translation.
		add_filter( 'pllwc_translate_cart_item', array( $this, 'translate_cart_item' ), 10, 2 );
		add_filter( 'pllwc_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_action( 'pllwc_translated_cart_item', array( $this, 'translated_cart_item' ), 10, 2 );
		add_filter( 'pllwc_translate_cart_contents', array( $this, 'translate_cart_contents' ) );
		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_loaded_from_session' ), 20 ); // After PLLWC_Frontend_Cart.
	}

	/**
	 * Copies or synchronizes the bundled items.
	 * Hooked to the action 'pllwc_copy_product'.
	 *
	 * @since 1.1
	 *
	 * @param int    $from id of the post from which we copy informations.
	 * @param int    $to   id of the post to which we paste informations.
	 * @param string $lang language slug.
	 * @param bool   $sync true if it is synchronization, false if it is a copy, defaults to false.
	 * @return void
	 */
	public function copy_product( $from, $to, $lang, $sync = false ) {
		$from = wc_get_product( $from );

		if ( ! $from || 'bundle' !== $from->get_type() ) {
			return;
		}

		$data_store = PLLWC_Data_Store::load( 'product_language' );

		// Remember the current items in translated bundle to synchronize the deleted items.
		$to = new WC_Product_Bundle( $to );
		$_translated_bundle_item_ids = array();
		foreach ( $to->get_bundled_data_items() as $item ) {
			$_translated_bundle_item_ids[ $item->get_product_id() ] = $item->get_bundled_item_id();
		}

		$tr_ids = array();

		// Invalidate the bundled data items in case a bundled item has been deleted.
		$cache_key = WC_Cache_Helper::get_cache_prefix( 'bundled_data_items' ) . $from->get_id();
		wp_cache_delete( $cache_key, 'bundled_data_items' );

		foreach ( $from->get_bundled_data_items() as $item ) {
			$tr_id = $data_store->get( $item->get_product_id(), $lang );
			if ( $tr_id ) {
				$tr_ids[] = $tr_id;

				// Meta data.
				$meta_data = $item->get_meta_data();
				$meta_data['bundled_id'] = $to->get_id();

				if ( ! empty( $meta_data['allowed_variations'] ) ) {
					foreach ( $meta_data['allowed_variations'] as $k => $variation_id ) {
						$meta_data['allowed_variations'][ $k ] = $data_store->get( $variation_id, $lang );
					}
				}

				if ( ! empty( $meta_data['default_variation_attributes'] ) ) {
					// FIXME Copy paste of PLLWC_Admin_Products::copy_default_attributes().
					foreach ( $meta_data['default_variation_attributes'] as $k => $v ) {
						if ( taxonomy_exists( $k ) ) {
							$terms = get_terms( $k, array( 'slug' => $v, 'lang' => '' ) ); // Don't use get_term_by filtered by language since WP 4.7.
							if ( is_array( $terms ) && ( $term = reset( $terms ) ) && $tr_id = pll_get_term( $term->term_id, $lang ) ) {
								$term = get_term( $tr_id, $k );
								$meta_data['default_variation_attributes'][ $k ] = $term->slug;
							}
						}
					}
				}

				if ( $sync ) {
					$meta_data = array_diff_key( $meta_data, array_flip( array( 'title', 'description' ) ) ); // Copy, don't sync.
				}

				// Copy or update.
				if ( isset( $_translated_bundle_item_ids[ $tr_id ] ) ) {
					WC_PB_DB::update_bundled_item(
						$_translated_bundle_item_ids[ $tr_id ],
						array(
							'menu_order' => $item->get_menu_order(),
							'meta_data'  => $meta_data,
						)
					);
				} else {
					WC_PB_DB::add_bundled_item(
						array(
							'bundle_id'  => $to->get_id(),
							'product_id' => $tr_id,
							'menu_order' => $item->get_menu_order(),
							'meta_data'  => $meta_data,
						)
					);
				}
			}
		}

		// Synchronize deleted items.
		if ( $sync ) {
			foreach ( $_translated_bundle_item_ids as $tr_id => $item ) {
				if ( ! in_array( $tr_id, $tr_ids ) ) {
					WC_PB_DB::delete_bundled_item( $item );
				}
			}
		}
	}

	/**
	 * Adds metas to synchronize when saving a bundled product.
	 * Hooked to the filter 'pllwc_copy_post_metas'.
	 *
	 * @since 1.1
	 *
	 * @param string[] $metas List of custom fields names.
	 * @return string[]
	 */
	public function copy_product_metas( $metas ) {
		$to_sync = array(
			'_wc_pb_edit_in_cart',
			'_wc_pb_base_regular_price',
			'_wc_pb_base_sale_price',
			'_wc_pb_base_price',
			'_wc_pb_layout_style',
			'_wc_pb_group_mode',
			'_wc_pb_sold_individually_context',
			'_wc_pb_add_to_cart_form_location',
		);

		return array_merge( $metas, array_combine( $to_sync, $to_sync ) );
	}

	/**
	 * Translates the stamp in the cart item.
	 *
	 * @since 1.1
	 *
	 * @param array  $item Cart item.
	 * @param string $lang Language code.
	 * @return array
	 */
	protected function translate_stamp( $item, $lang ) {
		$product_ids = array();
		$tr_stamp    = array();
		$data_store  = PLLWC_Data_Store::load( 'product_language' );

		if ( isset( $item['bundled_by'], $this->stamps[ $item['bundled_by'] ] ) ) {
			return $this->stamps[ $item['bundled_by'] ];
		}

		foreach ( wp_list_pluck( $item['stamp'], 'product_id' ) as $product_id ) {
			$product_ids[] = $data_store->get( $product_id, $lang );
		}

		$args = array(
			'product_id' => $product_ids,
			'bundle_id'  => $data_store->get( $item['product_id'], $lang ),
			'return'     => 'id=>product_id',
		);

		$bundled_items = WC_PB_DB::query_bundled_items( $args );
		$bundled_items = array_flip( $bundled_items );

		foreach ( $item['stamp'] as $s ) {
			$orig_lang = $data_store->get_language( $s['product_id'] );
			$tr_id = $data_store->get( $s['product_id'], $lang );

			if ( $tr_id && ! empty( $bundled_items[ $tr_id ] ) ) {
				$bundled_item = $bundled_items[ $tr_id ];

				$s['product_id'] = $tr_id;

				// Variations.
				if ( ! empty( $s['variation_id'] ) ) {
					$tr_var_id = $data_store->get( $s['variation_id'], $lang );
					$s['variation_id'] = $tr_var_id;
				}

				// Variations attributes.
				if ( ! empty( $s['attributes'] ) ) {
					$s['attributes'] = PLLWC()->cart->translate_attributes_in_cart( $s['attributes'], $lang, $orig_lang );
				}

				// Overriden title.
				if ( $title = WC_PB_DB::get_bundled_item_meta( $bundled_item, 'title' ) ) {
					$s['title'] = $title;
				}

				$tr_stamp[ $bundled_item ] = $s;
			}
		}

		$tr_stamp = empty( $tr_stamp ) ? $item['stamp'] : $tr_stamp;
		$this->stamps[ $item['key'] ] = $tr_stamp;

		return $tr_stamp;
	}

	/**
	 * Translates the items the in cart.
	 * Hooked to the filter 'pllwc_translate_cart_item'.
	 *
	 * @since 1.1
	 *
	 * @param array  $item Cart item.
	 * @param string $lang Language code.
	 * @return array
	 */
	public function translate_cart_item( $item, $lang ) {

		if ( isset( $item['stamp'] ) ) {
			$item['stamp'] = $this->translate_stamp( $item, $lang );
		}

		if ( isset( $item['bundled_items'] ) ) {
			$item['bundled_items'] = array();
		}

		if ( isset( $item['bundled_by'], $this->translated_cart_keys[ $item['bundled_by'] ] ) ) {
			$item['bundled_by'] = $this->translated_cart_keys[ $item['bundled_by'] ];
		}

		if ( isset( $item['bundled_item_id'] ) ) {
			$product_ids = wp_list_pluck( $item['stamp'], 'product_id' );
			$item['bundled_item_id'] = array_search( $item['product_id'], $product_ids );
		}

		return $item;
	}

	/**
	 * Adds the Product bundles informations to the cart item data when translated.
	 * Hooked to the filter 'pllwc_add_cart_item_data'.
	 *
	 * @since 1.1
	 *
	 * @param array $cart_item_data Cart item data.
	 * @param array $item           Cart item.
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_data, $item ) {
		$keys = array(
			'bundled_by',
			'bundled_items',
			'bundled_item_id',
			'stamp',
		);
		return array_merge( $cart_item_data, array_intersect_key( $item, array_flip( $keys ) ) );
	}

	/**
	 * Stores the new cart keys as function of the previous values.
	 * Later needed to restore the relationship between the bundle and bundled products.
	 * Hooked to the action 'pllwc_translated_cart_item'.
	 *
	 * @since 1.1
	 *
	 * @param array  $item Cart item.
	 * @param string $key  Previous cart item key. The new key can be found in $item['key'].
	 * @return void
	 */
	public function translated_cart_item( $item, $key ) {
		$this->translated_cart_keys[ $key ] = $item['key'];
	}

	/**
	 * Assigns the correct bundled_items values to the bundle
	 * once the bundled cart items have been translated.
	 * Hooked to the filter 'pllwc_translate_cart_contents'.
	 *
	 * @since 1.1
	 *
	 * @param array $contents Cart contents.
	 * @return array
	 */
	public function translate_cart_contents( $contents ) {
		$bundled_by = array();

		foreach ( $contents as $cart_key => $item ) {
			if ( isset( $item['bundled_by'] ) ) {
				$bundled_by[ $cart_key ] = $item['bundled_by'];
			}
		}

		if ( ! empty( $bundled_by ) ) {
			foreach ( $contents as $cart_key => $item ) {
				if ( isset( $item['bundled_items'] ) ) {
					$contents[ $cart_key ]['bundled_items'] = array_keys( $bundled_by, $item['key'] );
				}
			}
		}

		return $contents;
	}

	/**
	 * Allows Product Bundles to filter the cart prices after the cart has been translated.
	 * Needed for example when the bundled products are not individually priced.
	 * We need to do it here as Product Bundles directly accesses to WC()->cart->cart_contents
	 * in a function used by WC_PB_Cart::add_cart_item_filter().
	 * Hooked to the action 'woocommerce_cart_loaded_from_session'.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function cart_loaded_from_session() {
		foreach ( WC()->cart->cart_contents as $cart_key => $item ) {
			if ( ! empty( $item['data'] ) ) {
				WC()->cart->cart_contents[ $cart_key ] = WC_PB_Cart::instance()->add_cart_item_filter( $item, $cart_key );
			}
		}
	}
}
