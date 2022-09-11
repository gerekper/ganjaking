<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with Mix and Match Products.
 * Version tested: 2.0.0.
 *
 * It handles the synchronization of products metas
 * and the translation of the cart when the language is switched.
 *
 * @since 1.1
 * @since 1.7 Added support for version 2.0+. Thanks @helgatheviking for bringing it.
 */
class PLLWC_Mix_Match {

	/**
	 * Using 2.0-style MNM tables.
	 *
	 * @var bool
	 */
	private $has_custom_db;

	/**
	 * An array of translated cart keys.
	 *
	 * @var array
	 */
	private $translated_cart_keys = array();

	/**
	 * Constructor.
	 * Setup filters.
	 *
	 * @since 1.1
	 */
	public function __construct() {
		$this->has_custom_db = class_exists( 'WC_MNM_Compatibility' ) && WC_MNM_Compatibility::is_db_version_gte( '2.0' );

		// Product synchronization.
		add_filter( 'pllwc_copy_post_metas', array( $this, 'copy_product_metas' ) );
		add_filter( 'pllwc_translate_product_meta', array( $this, 'translate_product_meta' ), 10, 3 );

		if ( $this->has_custom_db ) {
			add_action( 'pllwc_copy_product', array( $this, 'copy_product' ), 10, 4 );
		}

		// Cart.
		add_filter( 'pllwc_translate_cart_item', array( $this, 'translate_cart_item' ), 10, 2 );
		add_filter( 'pllwc_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_action( 'pllwc_translated_cart_item', array( $this, 'translated_cart_item' ), 10, 2 );
		add_filter( 'pllwc_translate_cart_contents', array( $this, 'translate_cart_contents' ) );
		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_loaded_from_session' ), 20 ); // After PLLWC_Frontend_Cart.
	}

	/**
	 * Adds metas to synchronize when saving a product.
	 * Hooked to the filter 'pllwc_copy_post_metas'.
	 *
	 * @since 1.1
	 *
	 * @param string[] $metas List of custom fields names.
	 * @return string[]
	 */
	public function copy_product_metas( $metas ) {
		if ( $this->has_custom_db ) {
			return array_merge(
				$metas,
				array(
					'_mnm_base_price'                => '_mnm_base_price',
					'_mnm_base_regular_price'        => '_mnm_base_regular_price',
					'_mnm_base_sale_price'           => '_mnm_base_sale_price',
					'_mnm_max_container_size'        => '_mnm_max_container_size',
					'_mnm_min_container_size'        => '_mnm_min_container_size',
					'_mnm_per_product_pricing'       => '_mnm_per_product_pricing',
					// Only M&M >= 2.0.
					'_mnm_add_to_cart_form_location' => '_mnm_add_to_cart_form_location',
					'_mnm_child_category_ids'        => '_mnm_child_category_ids',
					'_mnm_content_source'            => '_mnm_content_source',
					'_mnm_layout_override'           => '_mnm_layout_override',
					'_mnm_layout_style'              => '_mnm_layout_style',
					'_mnm_packing_mode'              => '_mnm_packing_mode',
					'_mnm_per_product_discount'      => '_mnm_per_product_discount',
					'_mnm_weight_cumulative'         => '_mnm_weight_cumulative',
				)
			);
		} else {
			return array_merge(
				$metas,
				array(
					'_mnm_base_price'           => '_mnm_base_price',
					'_mnm_base_regular_price'   => '_mnm_base_regular_price',
					'_mnm_base_sale_price'      => '_mnm_base_sale_price',
					'_mnm_max_container_size'   => '_mnm_max_container_size',
					'_mnm_min_container_size'   => '_mnm_min_container_size',
					'_mnm_per_product_pricing'  => '_mnm_per_product_pricing',
					// Only M&M < 2.0.
					'_mnm_data'                 => '_mnm_data',
					'_mnm_per_product_shipping' => '_mnm_per_product_shipping',
				)
			);
		}
	}

	/**
	 * Translates the Mix and Match contents.
	 * Hooked to the filter 'pllwc_translate_product_meta'.
	 *
	 * @since 1.1
	 *
	 * @param  mixed  $value Meta value.
	 * @param  string $key   Meta key.
	 * @param  string $lang  Language of target.
	 * @return mixed
	 */
	public function translate_product_meta( $value, $key, $lang ) {
		switch ( $key ) {
			case '_mnm_child_category_ids':
				// For MNM 2.x category contents.
				if ( empty( $value ) || ! is_array( $value ) ) {
					// An array of IDs is expected.
					return array();
				}

				$out = array();

				foreach ( $value as $category_id ) {
					if ( ! is_numeric( $category_id ) || $category_id <= 0 ) {
						continue;
					}

					$out[] = pll_get_term( (int) $category_id, $lang );
				}

				$value = array_filter( $out );
				break;

			case '_mnm_data':
				/**
				 * Backward compatibility for MNM 1.x. As of 2.0 child items are stored in custom DB table.
				 *
				 * @see: PLLWC_Mix_Match::copy_product()
				 */
				if ( empty( $value ) || ! is_array( $value ) ) {
					// An array of IDs is expected.
					return array();
				}

				$out        = array();
				$data_store = PLLWC_Data_Store::load( 'product_language' );

				foreach ( $value as $post_id => $data ) {
					if ( ! is_numeric( $post_id ) || $post_id <= 0 ) {
						continue;
					}

					$tr_id = $data_store->get( $post_id, $lang );

					if ( empty( $tr_id ) ) {
						$out[ $post_id ] = $data;
						continue;
					}

					$tr_product_id   = $tr_id;
					$tr_variation_id = 0;

					// If a variation, need to also translate the parent ID.
					if ( ! empty( $data['product_id'] ) && ! empty( $data['variation_id'] ) ) {
						$tr_product_id   = $data_store->get( $data['product_id'], $lang );
						$tr_variation_id = $tr_id;
					}

					$out[ $tr_id ] = array(
						'child_id'     => $tr_id,
						'product_id'   => $tr_product_id,
						'variation_id' => $tr_variation_id,
					);
				}

				$value = $out;
				break;
		}

		return $value;
	}

	/**
	 * Copies or synchronizes the bundled items.
	 * Hooked to the action 'pllwc_copy_product'.
	 *
	 * @since 1.7
	 *
	 * @param int    $from Id of the post from which we copy informations.
	 * @param int    $to   Id of the post to which we paste informations.
	 * @param string $lang language slug.
	 * @return void
	 */
	public function copy_product( $from, $to, $lang ) {
		/**
		 * Used to prevent an infinite loop.
		 *
		 * @var array<int,int> Post IDs as array keys. 1 as values
		 */
		static $copying_products = array();

		if ( isset( $copying_products[ $from ] ) ) {
			// Prevent an infinite loop.
			return;
		}

		/** @var WC_Product_Mix_and_Match|null|false $from_product */
		$from_product = wc_get_product( $from );

		if ( empty( $from_product ) || ! $from_product->is_type( 'mix-and-match' ) ) {
			return;
		}

		$to_product = new WC_Product_Mix_and_Match( $to );

		if ( empty( $to_product ) ) {
			return;
		}

		$copying_products[ $from ] = 1;

		/** @var PLLWC_Product_Language_CPT $data_store */
		$data_store = PLLWC_Data_Store::load( 'product_language' );
		$tr_items   = array();

		foreach ( $from_product->get_child_items() as $item ) {
			$tr_product_id = $data_store->get( $item->get_product_id(), $lang );

			if ( $item->get_variation_id() ) {
				$tr_variation_id = $data_store->get( $item->get_variation_id(), $lang );

				if ( $tr_product_id && $tr_variation_id ) {
					$tr_items[] = array(
						'product_id'   => $tr_product_id,
						'variation_id' => $tr_variation_id,
					);
				}
			} elseif ( $tr_product_id ) {
				$tr_items[] = array(
					'product_id'   => $tr_product_id,
					'variation_id' => 0,
				);
			}
		}

		if ( ! empty( $tr_items ) ) {
			$to_product->set_child_items( $tr_items );
			$to_product->save();
		}

		unset( $copying_products[ $from ] );
	}

	/**
	 * Translates items in the cart.
	 * Hooked to the filter 'pllwc_translate_cart_item'.
	 *
	 * @since 1.1
	 *
	 * @param array  $item Cart item.
	 * @param string $lang Language code.
	 * @return array
	 */
	public function translate_cart_item( $item, $lang = '' ) {
		// `wc_mnm_is_container_cart_item()` and `wc_mnm_maybe_is_child_cart_item()` were introoduced in M&M 1.7.
		if ( ! function_exists( 'wc_mnm_is_container_cart_item' ) ) {
			return $item;
		}

		if ( wc_mnm_is_container_cart_item( $item ) ) {
			$item['mnm_config'] = $this->translate_config( $item['mnm_config'], $lang );

			if ( isset( $item['mnm_contents'] ) ) {
				// Stash the content keys for later. Cannot translate now as the child products have not yet been translated.
				$item['mnm_contents_tr'] = $item['mnm_contents'];
				$item['mnm_contents']    = array();
			}
		} elseif ( wc_mnm_maybe_is_child_cart_item( $item ) ) {
			if ( isset( $item['mnm_container'], $this->translated_cart_keys[ $item['mnm_container'] ] ) ) {
				$item['mnm_container'] = $this->translated_cart_keys[ $item['mnm_container'] ];
			}
		}

		return $item;
	}

	/**
	 * Translates the config in the cart item.
	 *
	 * @since 1.7
	 *
	 * @param  array  $config Config.
	 * @param  string $lang   Language code.
	 * @return array<int<1,max>,array{
	 *     product_id: int<1,max>,
	 *     variation_id?: int<1,max>,
	 *     variation?: array<string,string>
	 * }>
	 */
	protected function translate_config( $config, $lang ) {
		/** @var PLLWC_Product_Language_CPT $data_store */
		$data_store = PLLWC_Data_Store::load( 'product_language' );
		$tr_config  = array();

		foreach ( $config as $row ) {
			$row['product_id'] = $data_store->get( $row['product_id'], $lang );

			if ( empty( $row['product_id'] ) ) {
				continue;
			}

			// Variations.
			if ( ! empty( $row['variation_id'] ) ) {
				$row['variation_id'] = $data_store->get( $row['variation_id'], $lang );

			}

			// Variations attributes.
			if ( ! empty( $row['variation'] ) ) {
				$orig_lang = $data_store->get_language( $row['product_id'] );

				if ( ! empty( $orig_lang ) ) {
					$row['variation'] = PLLWC()->cart->translate_attributes_in_cart( $row['variation'], $lang, $orig_lang );
				}
			}

			$tr_config[ $row['product_id'] ] = $row;
		}

		return $tr_config;
	}

	/**
	 * Adds Mix and Match Product informations to the cart item data when translated.
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
			'mnm_config',
			'mnm_contents',
			'mnm_container',
		);
		return array_merge( $cart_item_data, array_intersect_key( $item, array_flip( $keys ) ) );
	}

	/**
	 * Stores new cart keys as function of previous values.
	 * Later needed to restore the relationship between the Mix and Match product and contained products.
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
	 * Assigns correct mnm_contents values to the Mix and Match product
	 * once the contained cart items have been translated.
	 * Hooked to the filter pllwc_translate_cart_contents.
	 *
	 * @since 1.1
	 *
	 * @param array $contents Cart contents.
	 * @return array
	 */
	public function translate_cart_contents( $contents ) {
		if ( empty( $this->translated_cart_keys ) ) {
			return $contents;
		}

		foreach ( $contents as $key => $cart_item ) {
			if ( ! wc_mnm_is_container_cart_item( $cart_item ) || empty( $cart_item['mnm_contents_tr'] ) ) {
				continue;
			}

			$contents[ $key ]['mnm_contents'] = array_unique(
				array_keys(
					array_intersect(
						array_flip( $this->translated_cart_keys ),
						$cart_item['mnm_contents_tr']
					)
				)
			);
			unset( $contents[ $key ]['mnm_contents_tr'] );
		}

		return $contents;
	}

	/**
	 * Allows WooCommerce Mix and Match to filter the cart prices after the cart has been translated.
	 * We need to do it here as WooCommerce Mix and Match directly access to WC()->cart->cart_contents.
	 * Hooked to the action 'woocommerce_cart_loaded_from_session'.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function cart_loaded_from_session() {
		$mnm_cart = WC_Mix_and_Match_Cart::get_instance();

		foreach ( WC()->cart->cart_contents as $cart_key => $item ) {
			if ( empty( $item['data'] ) ) {
				continue;
			}

			WC()->cart->cart_contents[ $cart_key ] = $mnm_cart->add_cart_item_filter( $item, $cart_key );
		}
	}
}
