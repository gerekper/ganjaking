<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the translation of the cart.
 *
 * @since 1.0
 */
class PLLWC_Frontend_Cart {
	/**
	 * Controls if the cart translation if enabled.
	 *
	 * @var bool
	 */
	protected $enable_cart_translation;

	/**
	 * Product language data store.
	 *
	 * @var PLLWC_Product_Language_CPT
	 */
	protected $data_store;

	/**
	 * Constructor.
	 * Setups filters and actions.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		/**
		 * Filters if the cart translation is enabled.
		 *
		 * It can be useful to disable the translation of the products in the cart in case
		 * a third party plugin adds information to the cart that we are not able to translate.
		 *
		 * @since 1.6
		 *
		 * @param bool $enable True if the cart translation is enabled, false otherwise.
		 */
		$this->enable_cart_translation = apply_filters( 'pllwc_enable_cart_translation', true );

		if ( did_action( 'pll_language_defined' ) ) {
			$this->init();
		} else {
			add_action( 'pll_language_defined', array( $this, 'init' ), 1 );
			add_filter( 'option_woocommerce_cart_page_id', array( $this, 'translate_add_to_cart_page_id' ) );
		}

		if ( $this->enable_cart_translation ) {
			$this->data_store = PLLWC_Data_Store::load( 'product_language' );

			add_filter( 'pll_set_language_from_query', array( $this, 'pll_set_language_from_query' ), 5 ); // Before Polylang.

			add_filter( 'woocommerce_cart_hash', array( $this, 'cart_hash' ), 10, 2 ); // Hash should be language independent.
			add_filter( 'woocommerce_cart_item_data_to_validate', array( $this, 'cart_item_data_to_validate' ), 10, 2 ); // Since WC 3.4.
		}
	}

	/**
	 * Setups actions and filters once the language is defined.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function init() {
		/*
		 * Resets the cart when switching the language, even when the cart translation is disabled,
		 * to translate the mini cart the theme.
		 */
		if ( isset( $_COOKIE[ PLL_COOKIE ] ) && pll_current_language() !== $_COOKIE[ PLL_COOKIE ] ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) ); // After WooCommerce load_scripts().
		}

		if ( $this->enable_cart_translation ) {
			// Translates the products in the cart.
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'woocommerce_cart_loaded_from_session' ) );
		}
	}

	/**
	 * Reloads the cart when the language is set from the content.
	 *
	 * @since 0.3.2
	 *
	 * @param PLL_Language|false $lang False or language object.
	 * @return PLL_Language|false
	 */
	public function pll_set_language_from_query( $lang ) {
		if ( ! PLL()->options['force_lang'] ) {
			if ( did_action( 'pll_language_defined' ) ) {
				/*
				 * Handle a specific case for the Site home (when the language code is hidden for the default language).
				 * Done here and not in the 'pll_language_defined' action to avoid a notice with WooCommerce Dynamic pricing which calls is_shop().
				 */
				WC()->cart->get_cart_from_session();
			} else {
				add_action( 'pll_language_defined', array( WC()->cart, 'get_cart_from_session' ) );
			}
		}

		return $lang;
	}

	/**
	 * Resets the cached data when switching the language.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		// Reset shipping methods (required since WC 2.6).
		WC()->shipping()->calculate_shipping( WC()->cart->get_shipping_packages() );

		$cart_hash_key = apply_filters( 'woocommerce_cart_hash_key', 'wc_cart_hash_' . md5( get_current_blog_id() . '_' . get_site_url( get_current_blog_id(), '/' ) . get_template() ) );
		$fragment_name = apply_filters( 'woocommerce_cart_fragment_name', 'wc_fragments_' . md5( get_current_blog_id() . '_' . get_site_url( get_current_blog_id(), '/' ) . get_template() ) );

		// Add js to reset the cart.
		wp_add_inline_script( // Since WP 4.5.
			'wc-cart-fragments',
			sprintf(
				'(function( $ ){
						sessionStorage.removeItem( "%s" );
						sessionStorage.removeItem( "%s" );
					}
				)();',
				esc_js( $cart_hash_key ),
				esc_js( $fragment_name )
			),
			'before'
		);
	}

	/**
	 * Translates the product attributes in the cart.
	 *
	 * @since 1.1
	 *
	 * @param string[] $attributes Selected attributes.
	 * @param string   $lang       Target language.
	 * @param string   $orig_lang  Source language.
	 * @return string[]
	 */
	public function translate_attributes_in_cart( $attributes, $lang, $orig_lang ) {
		foreach ( $attributes as $name => $value ) {
			if ( '' === $value ) {
				continue;
			}

			$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

			if ( taxonomy_exists( $taxonomy ) ) {
				// Don't use get_term_by( 'slug' ) which is filtered in the current language by Polylang Pro.
				$terms = get_terms( $taxonomy, array( 'slug' => $value, 'lang' => $orig_lang ) );

				if ( ! empty( $terms ) && is_array( $terms ) ) {
					$term = reset( $terms );
					if ( $term instanceof WP_Term && $term_id = pll_get_term( $term->term_id, $lang ) ) {
						$term = get_term( $term_id, $taxonomy );
						if ( $term instanceof WP_Term ) {
							$attributes[ $name ] = $term->slug;
						}
					}
				}
			}
		}

		return $attributes;
	}

	/**
	 * Translates the products in the cart.
	 *
	 * @since 0.3.5
	 *
	 * @param array  $item Cart item.
	 * @param string $lang Language code.
	 * @return array
	 */
	protected function translate_cart_item( $item, $lang ) {
		$orig_lang = $this->data_store->get_language( $item['product_id'] );

		if ( ! $orig_lang ) {
			return $item;
		}

		$item['product_id'] = $this->data_store->get( $item['product_id'], $lang );

		// Variable product.
		if ( $item['variation_id'] && $tr_id = $this->data_store->get( $item['variation_id'], $lang ) ) {
			$item['variation_id'] = $tr_id;
			if ( ! empty( $item['data'] ) ) {
				$item['data'] = wc_get_product( $item['variation_id'] );
			}

			// Variations attributes.
			if ( ! empty( $item['variation'] ) ) {
				$item['variation'] = $this->translate_attributes_in_cart( $item['variation'], $lang, $orig_lang );
			}
		} elseif ( ! empty( $item['data'] ) ) {
			// Simple product.
			$item['data'] = wc_get_product( $item['product_id'] );
		}

		/**
		 * Filters a cart item when it is translated.
		 *
		 * @since 0.6
		 *
		 * @param array  $item Cart item.
		 * @param string $lang Language code.
		 */
		$item = apply_filters( 'pllwc_translate_cart_item', $item, $lang );

		/**
		 * Filters the cart item data.
		 * This filters aims to replace the filter 'woocommerce_add_cart_item_data',
		 * which can't be used here as it conflicts with WooCommerce Bookings,
		 * which uses the filter to create new bookings and not only to filter the cart item data.
		 *
		 * @since 0.7.4
		 *
		 * @param array $cart_item_data Cart item data.
		 * @param array $item           Cart item.
		 */
		$cart_item_data = (array) apply_filters( 'pllwc_add_cart_item_data', array(), $item );
		$item['key'] = WC()->cart->generate_cart_id( $item['product_id'], $item['variation_id'], $item['variation'], $cart_item_data );

		return $item;
	}

	/**
	 * Translates the cart contents.
	 *
	 * @since 0.3.5
	 *
	 * @param array  $contents Cart contents.
	 * @param string $lang     Language code.
	 * @return array
	 */
	protected function translate_cart_contents( $contents, $lang = '' ) {
		if ( empty( $lang ) ) {
			$lang = pll_current_language();
		}

		foreach ( $contents as $key => $item ) {
			if ( $item['product_id'] && ( $tr_id = $this->data_store->get( $item['product_id'], $lang ) ) && $tr_id !== $item['product_id'] ) {
				unset( $contents[ $key ] );
				$item = $this->translate_cart_item( $item, $lang );
				$contents[ $item['key'] ] = $item;

				/**
				 * Fires after a cart item has been translated.
				 *
				 * @since 1.1
				 *
				 * @param array  $item Cart item.
				 * @param string $key  Previous cart item key. The new key can be found in $item['key'].
				 */
				do_action( 'pllwc_translated_cart_item', $item, $key );
			}
		}

		/**
		 * Filters the cart contents after all cart items have been translated.
		 *
		 * @since 1.1
		 *
		 * @param array  $contents Cart contents.
		 * @param string $lang     Language code.
		 */
		$contents = apply_filters( 'pllwc_translate_cart_contents', $contents, $lang );

		return $contents;
	}

	/**
	 * Translates the products and removed products in the cart.
	 *
	 * @since 0.3.5
	 *
	 * @return void
	 */
	public function woocommerce_cart_loaded_from_session() {
		WC()->cart->cart_contents = $this->translate_cart_contents( WC()->cart->cart_contents );
		WC()->cart->removed_cart_contents = $this->translate_cart_contents( WC()->cart->removed_cart_contents );
	}

	/**
	 * Makes the cart hash language independent by relying on products in default language.
	 *
	 * @since 0.9.4
	 *
	 * @param string $hash         Cart hash.
	 * @param array  $cart_session Cart session.
	 * @return string Modified cart hash.
	 */
	public function cart_hash( $hash, $cart_session ) {
		if ( ! empty( $cart_session ) ) {
			$cart_session = $this->translate_cart_contents( $cart_session, pll_default_language() );
			$hash = md5( wp_json_encode( $cart_session ) . WC()->cart->get_total( 'edit' ) );
		}
		return $hash;
	}

	/**
	 * Makes the cart item hash language independent by relying on attributes in default language
	 *
	 * @since 1.0
	 *
	 * @param array      $data    Data to validate in the hash.
	 * @param WC_Product $product Product in the cart item.
	 * @return array
	 */
	public function cart_item_data_to_validate( $data, $product ) {
		if ( ! empty( $data['attributes'] ) ) {
			$tr_product_id = $this->data_store->get( $product->get_id(), pll_default_language() );
			$tr_product = wc_get_product( $tr_product_id );
			if ( $tr_product && method_exists( $tr_product, 'get_variation_attributes' ) ) {
				$data['attributes'] = $tr_product->get_variation_attributes();
			}
		}
		return $data;
	}

	/**
	 * Translates the cart page id in the Add to cart action.
	 *
	 * @since 1.6
	 *
	 * @param int $page_id Cart page id.
	 * @return int
	 */
	public function translate_add_to_cart_page_id( $page_id ) {
		if ( empty( $_REQUEST['add-to-cart'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $page_id;
		}

		$product_id = absint( wp_unslash( $_REQUEST['add-to-cart'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$language = $this->data_store->get_language( $product_id );

		if ( ! empty( $language ) ) {
			$page_id = pll_get_post( $page_id, $language );
		}
		return $page_id;
	}
}
