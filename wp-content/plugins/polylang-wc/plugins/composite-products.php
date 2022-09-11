<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with WooCommerce Composite Products.
 * Version tested: 3.13.11.
 *
 * It handles the synchronization of products metas
 * and the translation of the cart when the language is switched.
 *
 * @since 1.3
 */
class PLLWC_Composite_Products {
	/**
	 * Array of cart keys with original as key and translation as value.
	 *
	 * @var array
	 */
	protected $translated_cart_keys;

	/**
	 * Constructor.
	 * Setups actions and filters.
	 *
	 * @since 1.3
	 */
	public function __construct() {
		/*
		 * Copy and synchronization.
		 * FIXME Clicking on "Save configuration" doesn't fire the sync.
		 */
		add_filter( 'pllwc_copy_post_metas', array( $this, 'copy_product_metas' ) );
		add_filter( 'pllwc_translate_product_meta', array( $this, 'translate_product_meta' ), 10, 5 );

		// Cart translation.
		add_filter( 'pllwc_translate_cart_item', array( $this, 'translate_cart_item' ) );
		add_filter( 'pllwc_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_action( 'pllwc_translated_cart_item', array( $this, 'translated_cart_item' ), 10, 2 );
		add_filter( 'pllwc_translate_cart_contents', array( $this, 'translate_cart_contents' ) );
		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_loaded_from_session' ), 20 ); // After PLLWC_Frontend_Cart.

	}

	/**
	 * Adds the metas to synchronize when saving a composite product.
	 * Hooked to the filter 'pllwc_copy_post_metas'.
	 *
	 * @since 1.3
	 *
	 * @param string[] $metas List of custom fields names.
	 * @return string[]
	 */
	public function copy_product_metas( $metas ) {
		$to_sync = array(
			'_bto_base_regular_price',
			'_bto_base_sale_price',
			'_bto_style',
			'_bto_add_to_cart_form_location',
			'_bto_shop_price_calc',
			'_bto_edit_in_cart',
			'_bto_sold_individually',
			'_bto_data',
			'_bto_scenario_data',
			'_bto_base_price',
		);

		return array_merge( $metas, array_combine( $to_sync, $to_sync ) );
	}


	/**
	 * Keeps the text untouched in synchronized complex metas.
	 *
	 * @since 1.3
	 *
	 * @param array  $value      Meta value.
	 * @param string $key        Meta key.
	 * @param int    $product_id Target product id.
	 * @return array
	 */
	private function maybe_keep_text_in_meta( $value, $key, $product_id ) {
		$product = wc_get_product( $product_id );

		if ( $product && $product instanceof WC_Product_Composite && is_array( $value ) ) {
			switch ( $key ) {
				case '_bto_data':
					$data = $product->get_composite_data();
					break;
				case '_bto_scenario_data':
					$data = $product->get_scenario_data();
					break;
			}

			foreach ( array_keys( $value ) as $component_id ) {
				if ( ! empty( $data[ $component_id ]['title'] ) ) {
					$value[ $component_id ]['title'] = $data[ $component_id ]['title'];
				}

				if ( ! empty( $data[ $component_id ]['description'] ) ) {
					$value[ $component_id ]['description'] = $data[ $component_id ]['description'];
				}
			}
		}

		return $value;
	}

	/**
	 * Adjust values before synchronizing metas.
	 * Hooked to the filter 'pllwc_translate_product_meta'.
	 *
	 * @since 1.3
	 *
	 * @param array  $value Meta value.
	 * @param string $key   Meta key.
	 * @param string $lang  Target language.
	 * @param int    $from  Source product id.
	 * @param int    $to    Target product id.
	 * @return array
	 */
	public function translate_product_meta( $value, $key, $lang, $from, $to ) {
		switch ( $key ) {
			case '_bto_data':
				$data_store = PLLWC_Data_Store::load( 'product_language' );
				$value      = $this->maybe_keep_text_in_meta( $value, $key, $to );

				foreach ( $value as $component_id => $component ) {
					if ( isset( $component['assigned_ids'] ) ) {
						foreach ( $component['assigned_ids'] as $k => $product_id ) {
							$value[ $component_id ]['assigned_ids'][ $k ] = $data_store->get( $product_id, $lang );
						}
					}

					if ( isset( $component['assigned_category_ids'] ) ) {
						foreach ( $component['assigned_category_ids'] as $k => $category_id ) {
							$value[ $component_id ]['assigned_category_ids'][ $k ] = pll_get_term( $category_id, $lang );
						}
					}

					if ( isset( $component['default_id'] ) ) {
						$value[ $component_id ]['default_id'] = $data_store->get( $component['default_id'], $lang );
					}
				}
				break;
			case '_bto_scenario_data':
				$data_store = PLLWC_Data_Store::load( 'product_language' );
				$value      = $this->maybe_keep_text_in_meta( $value, $key, $to );

				foreach ( $value as $scenario_id => $scenario ) {
					foreach ( $scenario['component_data'] as $component_id => $products_in_scenario ) {
						foreach ( $products_in_scenario as $k => $product_id ) {
							if ( ! empty( $product_id ) ) {
								$value[ $scenario_id ]['component_data'][ $component_id ][ $k ] = $data_store->get( $product_id, $lang );
							}
						}
					}
				}
				break;
		}
		return $value;
	}

	/**
	 * Translates items in the cart.
	 * Hooked to the filter 'pllwc_translate_cart_item'.
	 *
	 * @since 1.3
	 *
	 * @param array $item Cart item.
	 * @return array
	 */
	public function translate_cart_item( $item ) {
		if ( ! empty( $item['composite_data'] ) ) {
			$composite  = &$item['composite_data'];
			$data_store = PLLWC_Data_Store::load( 'product_language' );

			foreach ( array_keys( $composite ) as $key ) {
				$composite[ $key ]['product_id']   = $data_store->get( $composite[ $key ]['product_id'] );
				$composite[ $key ]['composite_id'] = $data_store->get( $composite[ $key ]['composite_id'] );

				$product = wc_get_product( $composite[ $key ]['composite_id'] );
				if ( $product && $product instanceof WC_Product_Composite ) {
					$data = $product->get_composite_data();
					$composite[ $key ]['title'] = $data[ $key ]['title'];
				}
			}
		}

		if ( isset( $item['composite_parent'], $this->translated_cart_keys[ $item['composite_parent'] ] ) ) {
			$item['composite_parent'] = $this->translated_cart_keys[ $item['composite_parent'] ];
		}

		return $item;
	}

	/**
	 * Adds Composite products informations to the cart item data when translated.
	 * Hooked to the filter 'pllwc_add_cart_item_data'.
	 *
	 * @since 1.3
	 *
	 * @param array $cart_item_data Cart item data.
	 * @param array $item           Cart item.
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_data, $item ) {
		$keys = array(
			'composite_children',
			'composite_data',
			'composite_item',
			'composite_parent',
		);

		if ( isset( $item['composite_children'] ) ) {
			$item['composite_children'] = array();
		}

		return array_merge( $cart_item_data, array_intersect_key( $item, array_flip( $keys ) ) );
	}

	/**
	 * Stores new cart keys as a function of previous values.
	 * Later needed to restore the relationship with the composite parent.
	 * Hooked to the action 'pllwc_translated_cart_item'.
	 *
	 * @since 1.3
	 *
	 * @param array  $item Cart item.
	 * @param string $key  Previous cart item key. The new key can be found in $item['key'].
	 * @return void
	 */
	public function translated_cart_item( $item, $key ) {
		$this->translated_cart_keys[ $key ] = $item['key'];
	}

	/**
	 * Assigns the correct composite_children values to the composite parent
	 * once the composite children cart items have been translated.
	 * Hooked to the filter 'pllwc_translate_cart_contents'.
	 *
	 * @since 1.3
	 *
	 * @param array $contents Cart contents.
	 * @return array
	 */
	public function translate_cart_contents( $contents ) {
		$parents = array();

		foreach ( $contents as $cart_key => $item ) {
			if ( isset( $item['composite_parent'] ) ) {
				$parents[ $cart_key ] = $item['composite_parent'];
			}
		}

		if ( ! empty( $parents ) ) {
			foreach ( $contents as $cart_key => $item ) {
				if ( isset( $item['composite_children'] ) ) {
					$contents[ $cart_key ]['composite_children'] = array_keys( $parents, $item['key'] );
				}
			}
		}

		return $contents;
	}

	/**
	 * Allows Composite Products to filter the cart prices after the cart has been translated.
	 * Hooked to the action 'woocommerce_cart_loaded_from_session'.
	 *
	 * @since 1.3
	 *
	 * @return void
	 */
	public function cart_loaded_from_session() {
		foreach ( WC()->cart->cart_contents as $cart_key => $item ) {
			if ( ! empty( $item['data'] ) ) {
				WC()->cart->cart_contents[ $cart_key ] = WC_CP_Cart::instance()->add_cart_item_filter( $item, $cart_key );
			}
		}
	}
}
