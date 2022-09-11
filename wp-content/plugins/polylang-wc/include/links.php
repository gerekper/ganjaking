<?php
/**
 * @package Polylang-WC
 */

/**
 * Translates links.
 *
 * @since 0.1
 */
class PLLWC_Links {

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		// Rewrite rules.
		add_filter( 'pre_option_rewrite_rules', array( $this, 'prepare_rewrite_rules' ), 5 ); // Before Polylang.
		add_filter( 'pll_modify_rewrite_rule', array( $this, 'fix_rewrite_rules' ), 10, 4 );

		// Translation of the current url.
		add_filter( 'pll_translation_url', array( $this, 'pll_translation_url' ), 10, 2 );

		// Breadcrumb.
		add_filter( 'woocommerce_breadcrumb_home_url', 'pll_home_url', 10, 0 );

		if ( PLL() instanceof PLL_Frontend ) {
			add_filter( 'option_woocommerce_permalinks', array( $this, 'option_woocommerce_permalinks' ) );
		}

		add_filter( 'woocommerce_get_checkout_order_received_url', array( $this, 'checkout_order_received_url' ), 10, 2 );
	}

	/**
	 * Prepares rewrite rules filters for the shop.
	 * Hooked to the filter 'pre_option_rewrite_rules'.
	 *
	 * @since 0.1
	 *
	 * @param string[] $pre Not used.
	 * @return string[] Unmodified $pre.
	 */
	public function prepare_rewrite_rules( $pre ) {
		if ( ! has_filter( 'rewrite_rules_array', array( $this, 'rewrite_shop_rules' ) ) ) {
			add_filter( 'rewrite_rules_array', array( $this, 'rewrite_shop_rules' ), 5 ); // Before Polylang.
			add_filter( 'rewrite_rules_array', array( $this, 'rewrite_shop_subpages_rules' ), 20 ); // After wc_fix_rewrite_rules().
		}
		return $pre;
	}

	/**
	 * Get the shop pages slugs in all languages.
	 *
	 * @since 0.3.6
	 *
	 * @return string[]
	 */
	protected function get_all_shop_page_slugs() {
		$slugs = array();
		$id = wc_get_page_id( 'shop' );
		$translations = pll_get_post_translations( $id );

		foreach ( $translations as $lang => $id ) {
			$slugs[ $lang ] = get_page_uri( $id );
		}

		return array_filter( $slugs );
	}

	/**
	 * Modifies the product archive rewrite rules
	 * to get the slugs directly from all the shop page translations.
	 * It must be done after WooCommerce for the shop rules to stay on top.
	 * Hooked to the filter 'rewrite_rules_array'.
	 *
	 * @since 0.1
	 *
	 * @param string[] $rules Rewrite rules.
	 * @return string[] Modified rewrite rules.
	 */
	public function rewrite_shop_rules( $rules ) {
		$new_rules = array();
		$id = wc_get_page_id( 'shop' );

		if ( $id ) {
			$uri = get_page_uri( $id ) . '/'; // The page uri got from the WooCommerce option.
			$translations = $this->get_all_shop_page_slugs();

			if ( count( $translations ) > 1 ) {
				if ( PLL()->options['force_lang'] > 0 ) {
					// The language is set from the directory, subdomain or domain.
					$translations = array_unique( $translations );
					$new_uri = '(' . implode( '|', $translations ) . ')/';

					foreach ( $rules as $key => $rule ) {
						if ( 0 === strpos( $key, $uri ) ) { // OK if we are acting before Polylang.
							$new_rules[ str_replace( $uri, $new_uri, $key ) ] = str_replace(
								array( '[8]', '[7]', '[6]', '[5]', '[4]', '[3]', '[2]', '[1]' ),
								array( '[9]', '[8]', '[7]', '[6]', '[5]', '[4]', '[3]', '[2]' ),
								$rule
							); // Hopefully it is sufficient!

							unset( $rules[ $key ] ); // Now useless.
						}
					}
				} else {
					/*
					 * When the language is set from the content, we need to explicitely set one rewrite rule per language
					 * and make sure to avoid a conflict with the product rewrite rules when the shop base matches the shop page slug.
					 */
					foreach ( $rules as $key => $rule ) {
						if ( 0 === strpos( $key, $uri ) && false !== strpos( $rule, 'post_type=product' ) ) {
							foreach ( $translations as $lang => $new_uri ) {
								$new_rules[ str_replace( $uri, $new_uri . '/', $key ) ] = str_replace( '?', "?lang=$lang&", $rule );
							}

							unset( $rules[ $key ] ); // Now useless.
						}
					}
				}
			}
		}

		return $new_rules + $rules;
	}

	/**
	 * Add rewrite rules for the shop subpages.
	 * It must be done after WooCommerce to remove the rules created by WooCommerce.
	 *
	 * @since 0.9.5
	 *
	 * @param string[] $rules Rewrite rules.
	 * @return string[] Modified rewrite rules.
	 */
	public function rewrite_shop_subpages_rules( $rules ) {
		global $wp_rewrite;

		$permalinks = wc_get_permalink_structure();
		$page_rewrite_rules = array();

		if ( $permalinks['use_verbose_page_rules'] && $id = wc_get_page_id( 'shop' ) ) {
			foreach ( pll_get_post_translations( $id ) as $lang => $shop_page_id ) {
				$subpages = wc_get_page_children( $shop_page_id );

				foreach ( $subpages as $subpage ) {
					$uri = get_page_uri( $subpage );

					// Remove rules added by WooCommerce as it is easier to add our own rather than modifying them separately.
					foreach ( $rules as $key => $rule ) {
						if ( false !== strpos( $rule, 'pagename=' . $uri ) ) {
							unset( $rules[ $key ] );
						}
					}

					if ( PLL()->options['hide_default'] && PLL()->options['default_lang'] === $lang ) {
						$slug = $uri;
					} else {
						$slug = $lang . '/' . $uri;
					}

					/*
					 * Inspired by WooCommerce wc_fix_rewrite_rules().
					 * Code last checked: WC 4.0
					 */
					$page_rewrite_rules[ $slug . '/?$' ] = 'index.php?pagename=' . $uri;
					$wp_generated_rewrite_rules = $wp_rewrite->generate_rewrite_rules( $slug, EP_PAGES, true, true, false, false );
					foreach ( $wp_generated_rewrite_rules as $key => $value ) {
						$wp_generated_rewrite_rules[ $key ] = $value . '&pagename=' . $uri;
					}
					$page_rewrite_rules = array_merge( $page_rewrite_rules, $wp_generated_rewrite_rules );
				}
			}
		}
		return $page_rewrite_rules + $rules;
	}

	/**
	 * Prevents Polylang from modifying some rewrite rules.
	 *
	 * @since 0.1
	 *
	 * @param bool        $modify  Whether to modify or not the rule, defaults to true.
	 * @param string[]    $rule    Original rewrite rule.
	 * @param string      $filter  Current set of rules being modified.
	 * @param string|bool $archive Custom post post type archive name or false if it is not a cpt archive.
	 * @return bool
	 */
	public function fix_rewrite_rules( $modify, $rule, $filter, $archive ) {
		if ( 'root' === $filter && false !== strpos( reset( $rule ), 'wc-api=$matches[2]' ) ) {
			return false;
		}

		if ( ! PLL()->options['force_lang'] && 'rewrite_rules_array' === $filter && 'product' === $archive ) {
			return false;
		}

		return $modify;
	}

	/**
	 * Returns the translation of the current url.
	 * Hooked to the filter 'pll_translation_url'.
	 *
	 * @since 0.1
	 *
	 * @param string $url  Translation url.
	 * @param string $lang Language slug.
	 * @return string
	 */
	public function pll_translation_url( $url, $lang ) {
		global $wp;

		// Shop.
		if ( is_shop() && ! is_search() ) {
			$url = '';
			$tr_shop_id = pll_get_post( wc_get_page_id( 'shop' ), $lang );

			if ( $tr_shop_id ) {
				$url = get_permalink( $tr_shop_id );

				// Layered nav.
				foreach ( wc_get_attribute_taxonomies() as $tax ) {
					$name = 'filter_' . $tax->attribute_name;

					if ( ! empty( $_GET[ $name ] ) && $tr_id = pll_get_term( (int) $_GET[ $name ], $lang ) ) { // phpcs:ignore WordPress.Security.NonceVerification
						$url = add_query_arg( array( $name => $tr_id ), $url );
					}
				}
			}
		}

		// Endpoints.
		if ( $endpoint = WC()->query->get_current_endpoint() ) {
			$value = wc_edit_address_i18n( $wp->query_vars[ $endpoint ], true ); // Address.
			$url = wc_get_endpoint_url( $endpoint, $value, $url );
			if ( PLL()->links_model->using_permalinks ) {
				$url = trailingslashit( $url ); // Needed for address.
			}

			if ( 'order-received' === $endpoint ) {
				$order = wc_get_order( $value );
				if ( $order instanceof WC_Order ) {
					$url = add_query_arg( 'key', $order->get_order_key(), $url );
				}
			}

			if ( 'order-pay' === $endpoint ) {
				$order = wc_get_order( $value );
				if ( $order instanceof WC_Order ) {
					$url = add_query_arg(
						array(
							'pay_for_order' => 'true',
							'key'           => $order->get_order_key(),
						),
						$url
					);
				}
			}
		}

		return $url;
	}

	/**
	 * Fixes the "Shop" link in the breadcrumb.
	 * Note that WooCommerce uses the presence of the shop page slug in permalink product base to display it.
	 * Hooked to the filter 'option_woocommerce_permalinks'.
	 *
	 * @since 0.3.6
	 *
	 * @param string[] $permalinks WooCommerce permalinks options.
	 * @return string[]
	 */
	public function option_woocommerce_permalinks( $permalinks ) {
		if ( isset( $permalinks['product_base'] ) && did_action( 'pll_language_defined' ) ) {
			$slugs = $this->get_all_shop_page_slugs();
			$lang  = pll_current_language();

			if ( count( $slugs ) > 1 && ! empty( $slugs[ $lang ] ) ) {
				$pattern = '#(' . implode( '|', $slugs ) . ')#';
				$permalinks['product_base'] = (string) preg_replace( $pattern, $slugs[ $lang ], $permalinks['product_base'] );
			}
		}

		return $permalinks;
	}

	/**
	 * Sets the home urls when using plain permalinks and the shop is on front.
	 *
	 * @since 0.5
	 *
	 * @param PLL_Language[] $languages Array of PLL_Language objects.
	 * @return PLL_Language[]
	 */
	public static function set_home_urls( $languages ) {
		// Test that wc_get_page_id() exists as the filter is applied before we check if WooCommerce is activated.
		if ( ! get_option( 'permalink_structure' ) && 'page' === get_option( 'show_on_front' ) && function_exists( 'wc_get_page_id' ) && in_array( wc_get_page_id( 'shop' ), wp_list_pluck( $languages, 'page_on_front' ) ) ) {
			$options = get_option( 'polylang' );
			foreach ( $languages as $k => $lang ) {
				if ( ! $options['hide_default'] || $lang->slug !== $options['default_lang'] ) {
					$languages[ $k ]->home_url = home_url( '/?post_type=product&lang=' . $lang->slug );
				}
			}
		}

		return $languages;
	}

	/**
	 * Sets the home urls when not cached and using plain permalinks and the shop is on front.
	 *
	 * @since 0.5
	 *
	 * @param PLL_Language[] $languages Array of PLL_Language objects.
	 * @return PLL_Language[]
	 */
	public static function pll_after_languages_cache( $languages ) {
		if ( ( defined( 'PLL_CACHE_LANGUAGES' ) && ! PLL_CACHE_LANGUAGES ) || ( defined( 'PLL_CACHE_HOME_URL' ) && ! PLL_CACHE_HOME_URL ) ) {
			return self::set_home_urls( $languages );
		}
		return $languages;
	}

	/**
	 * Makes sure that the order received url is in the same language as the order.
	 * This is especially useful when evaluating the return url for gateways, which
	 * evaluate the return url on an api endpoint.
	 *
	 * @since 1.5.1
	 *
	 * @param string   $url   Order received url.
	 * @param WC_Order $order WC_Order object.
	 * @return string
	 */
	public function checkout_order_received_url( $url, $order ) {
		static $avoid_recursion = false;

		if ( $avoid_recursion ) {
			return $url;
		}

		/** @var PLLWC_Order_Language_CPT */
		$data_store = PLLWC_Data_Store::load( 'order_language' );
		$lang = $data_store->get_language( $order->get_id() );

		if ( $lang ) {
			$avoid_recursion = true;
			$saved_curlang = PLL()->curlang;

			PLL()->curlang = PLL()->model->get_language( $lang );
			$url = $order->get_checkout_order_received_url();

			$avoid_recursion = false;
			PLL()->curlang = $saved_curlang;
		}
		return $url;
	}
}
