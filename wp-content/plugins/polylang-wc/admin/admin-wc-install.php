<?php
/**
 * @package Polylang-WC
 */

/**
 * Allows to automatically install the translations of the WooCommerce default pages.
 * Manages the installation of the default product category.
 *
 * @since 0.1
 */
class PLLWC_Admin_WC_Install {
	/**
	 * List of WooCommerce pages in all languages.
	 *
	 * @var array<string, array<string, array<string, string>>>
	 */
	private $pages = array();

	/**
	 * Locale used to translate WooCommerce pages title.
	 *
	 * @var string
	 */
	private $locale;

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		// Add post state for translations of the shop, cart, etc...
		add_filter( 'display_post_states', array( $this, 'display_post_states' ), 10, 2 );

		// Allow WC to install the default pages and their translations through status page.
		add_filter( 'woocommerce_debug_tools', array( $this, 'debug_tools' ) );

		// Make sure to load only on setup wizard as initializing translated pages is expensive.
		if ( isset( $_GET['page'], $_GET['path'] ) && 'wc-admin' === $_GET['page'] && '/setup-wizard' === $_GET['path'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			$this->init_translated_pages();

			if ( ! empty( $this->pages ) ) {
				add_action( 'init', array( $this, 'translate_default_wc_pages' ) ); // $wp_rewrite is not available yet and is required when wp_unique_post_slug() is called, so we need to wait for init.
			}
		}

		// Add default product category when adding a new language.
		add_action( 'pll_add_language', array( $this, 'add_language' ) );
	}

	/**
	 * Translates the default WooCommerce pages in all existing languages.
	 *
	 * @since 1.7
	 *
	 * @return void
	 */
	public function translate_default_wc_pages() {
		foreach ( pll_languages_list() as $lang ) {
			foreach ( array_keys( $this->pages[ $lang ] ) as $key ) {
				$this->translate_page( $key, $lang );
			}
		}
	}

	/**
	 * Add post states for the translations of the shop, cart, checkout, account and terms pages.
	 *
	 * @since 0.9
	 *
	 * @param string[] $post_states List of post display states.
	 * @param WP_Post  $post        The post object.
	 * @return string[]
	 */
	public function display_post_states( $post_states, $post ) {
		if ( in_array( $post->ID, pll_get_post_translations( wc_get_page_id( 'shop' ) ) ) ) {
			$post_states['wc_page_for_shop'] = __( 'Shop Page', 'polylang-wc' );
		}

		if ( in_array( $post->ID, pll_get_post_translations( wc_get_page_id( 'cart' ) ) ) ) {
			$post_states['wc_page_for_cart'] = __( 'Cart Page', 'polylang-wc' );
		}

		if ( in_array( $post->ID, pll_get_post_translations( wc_get_page_id( 'checkout' ) ) ) ) {
			$post_states['wc_page_for_checkout'] = __( 'Checkout Page', 'polylang-wc' );
		}

		if ( in_array( $post->ID, pll_get_post_translations( wc_get_page_id( 'myaccount' ) ) ) ) {
			$post_states['wc_page_for_myaccount'] = __( 'My Account Page', 'polylang-wc' );
		}

		if ( in_array( $post->ID, pll_get_post_translations( wc_get_page_id( 'terms' ) ) ) ) {
			$post_states['wc_page_for_terms'] = __( 'Terms and Conditions Page', 'polylang-wc' );
		}

		return $post_states;
	}

	/**
	 * Replaces the Install WooCommerce Pages tool by our own to be able to create translations.
	 *
	 * @since 0.1
	 *
	 * @param array $tools List of available tools.
	 * @return array
	 */
	public function debug_tools( $tools ) {
		$n = array_search( 'install_pages', array_keys( $tools ) );
		$end = array_slice( $tools, $n + 1 );
		$tools = array_slice( $tools, 0, $n );

		$tools['pll_install_pages'] = array(
			'name'     => __( 'Install WooCommerce pages', 'polylang-wc' ),
			'button'   => __( 'Install pages', 'polylang-wc' ),
			'desc'     => sprintf(
				'<strong class="red">%1$s</strong> %2$s',
				__( 'Note:', 'polylang-wc' ),
				__( 'This tool will install all the missing WooCommerce pages. Pages already defined and set up will not be replaced.', 'polylang-wc' )
			),
			'callback' => array( $this, 'install_pages' ),
		);

		return array_merge( $tools, $end );
	}

	/**
	 * Filters the locale when creating WooCommerce translated pages.
	 *
	 * @since 0.1
	 *
	 * @param string $locale The plugin's current locale.
	 * @param string $domain Text domain.
	 * @return string
	 */
	public function plugin_locale( $locale, $domain ) {
		return 'polylang-wc' === $domain ? $this->locale : $locale;
	}

	/**
	 * Initializes the page names and content in all available languages.
	 * The method does not actually create the pages.
	 *
	 * It implements its own locale switch rather than using switch_to_locale()
	 * for performance reasons as we only need our own translations.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function init_translated_pages() {
		add_filter( 'plugin_locale', array( $this, 'plugin_locale' ), 10, 2 );

		/** @var PLL_Language $language */
		foreach ( pll_languages_list( array( 'fields' => '' ) ) as $language ) {
			// Load our text domain in the new language.
			$this->locale = $language->locale;
			unload_textdomain( 'polylang-wc' );
			load_plugin_textdomain( 'polylang-wc' );

			/*
			 * Partly copy paste of WC_Install::create_pages that we can't use it directly
			 * because Woocommerce checks for the unicity of each page.
			 */
			$this->pages[ $language->slug ] = apply_filters(
				'woocommerce_create_pages',
				array(
					'shop'      => array(
						'name'    => _x( 'shop', 'Page slug', 'polylang-wc' ),
						'title'   => _x( 'Shop', 'Page title', 'polylang-wc' ),
						'content' => '',
					),
					'cart'      => array(
						'name'    => _x( 'cart', 'Page slug', 'polylang-wc' ),
						'title'   => _x( 'Cart', 'Page title', 'polylang-wc' ),
						'content' => '[' . apply_filters( 'woocommerce_cart_shortcode_tag', 'woocommerce_cart' ) . ']',
					),
					'checkout'  => array(
						'name'    => _x( 'checkout', 'Page slug', 'polylang-wc' ),
						'title'   => _x( 'Checkout', 'Page title', 'polylang-wc' ),
						'content' => '[' . apply_filters( 'woocommerce_checkout_shortcode_tag', 'woocommerce_checkout' ) . ']',
					),
					'myaccount' => array(
						'name'    => _x( 'my-account', 'Page slug', 'polylang-wc' ),
						'title'   => _x( 'My account', 'Page title', 'polylang-wc' ),
						'content' => '[' . apply_filters( 'woocommerce_my_account_shortcode_tag', 'woocommerce_my_account' ) . ']',
					),
				)
			);
		}

		// Reloads the current text domain.
		remove_filter( 'plugin_locale', array( $this, 'plugin_locale' ) );
		unload_textdomain( 'polylang-wc' );
		load_plugin_textdomain( 'polylang-wc' );
	}

	/**
	 * Install pages from the WooCommerce status tools when using the "Install pages" button.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function install_pages() {
		// Let WooCommerce create the pages in the default language.
		WC_Install::create_pages();

		$this->init_translated_pages();

		$default_language = pll_default_language();

		// In case pages were installed before Polylang, the pages may have no language. We must assign one.
		foreach ( array_keys( $this->pages[ $default_language ] ) as $key ) {
			$post_id = wc_get_page_id( $key );
			if ( ! pll_get_post_language( $post_id ) ) {
				pll_set_post_language( $post_id, $default_language );
			}
		}

		// Then translate them.
		$this->translate_default_wc_pages();

		return __( 'All missing WooCommerce pages successfully installed', 'polylang-wc' );
	}

	/**
	 * Creates a page translation.
	 *
	 * @since 0.1
	 *
	 * @param string $page WooCommerce Page slug.
	 * @param string $lang Language slug.
	 * @return void
	 */
	public function translate_page( $page, $lang ) {
		$post_id = wc_get_page_id( $page );

		if ( $post_id < 0 ) {
			// The given page doesn't exist.
			return;
		}

		$translations = pll_get_post_translations( $post_id );

		// Create the translation only if it doesn't exist yet.
		if ( empty( $translations[ $lang ] ) ) {
			$post = get_post( $post_id, ARRAY_A );
			unset( $post['ID'] );
			// FIXME post parent?
			$post['post_title'] = $this->pages[ $lang ][ $page ]['title'];
			$post['post_status'] = 'draft'; // Keep it draft before we set the language, to correctly handle auto added pages to menu.
			$tr_id = wp_insert_post( $post );

			if ( ! is_wp_error( $tr_id ) ) {
				// Assign the language and translations.
				pll_set_post_language( $tr_id, $lang );
				$translations[ $lang ] = $tr_id;
				pll_save_post_translations( $translations );

				$tr_post = get_post( $tr_id );

				/*
				 * We can now publish the page which will also add it to menus if auto add pages to menu is checked
				 * and attempt to share the slug if needed ( to do after the language has been set ).
				 */
				if ( ! empty( $tr_post ) ) {
					$tr_post->post_name = $this->pages[ $lang ][ $page ]['name'];
					$tr_post->post_status = 'publish';
					wp_update_post( $tr_post );
				}
			}
		}
	}

	/**
	 * Creates a default product category for a language.
	 *
	 * @since 0.9.3
	 *
	 * @param string $lang Language code.
	 * @return void
	 */
	protected static function create_default_product_cat( $lang ) {
		$default = get_option( 'default_product_cat' );

		if ( $default && ! pll_get_term( $default, $lang ) ) {
			$name = _x( 'Uncategorized', 'Default category slug', 'polylang-wc' );
			$slug = sanitize_title( $name . '-' . $lang );
			$cat = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug ) );

			// Bail in case of database error, but continue if we got a term.
			if ( is_wp_error( $cat ) && array_key_exists( 'term_exists', $cat->errors ) && isset( $cat->error_data['term_exists'] ) ) {
				$cat_id = $cat->error_data['term_exists'];
			} elseif ( is_array( $cat ) ) {
				$cat_id = $cat['term_id'];
			}

			if ( ! empty( $cat_id ) ) {
				// Assign the language and translations.
				pll_set_term_language( (int) $cat_id, $lang );
				$translations = pll_get_term_translations( $default );
				$translations[ $lang ] = $cat_id;
				pll_save_term_translations( $translations );
			}
		}
	}

	/**
	 * Creates a default product category when adding a language.
	 *
	 * @since 0.9.3
	 *
	 * @param array $args New language arguments.
	 * @return void
	 */
	public function add_language( $args ) {
		if ( $default = get_option( 'default_product_cat' ) ) {
			$default_cat_lang = pll_get_term_language( $default );

			// Assign a default language to the default product category.
			if ( ! $default_cat_lang ) {
				pll_set_term_language( (int) $default, pll_default_language() );
			} else {
				self::create_default_product_cat( $args['slug'] );
			}
		}
	}

	/**
	 * Assigns the default language to the default product category
	 * and creates translated default categories.
	 *
	 * @since 0.9.3
	 *
	 * @return void
	 */
	public static function create_default_product_cats() {
		if ( $default = get_option( 'default_product_cat' ) ) {
			$default_cat_lang = pll_get_term_language( $default );

			// Assign a default language to default product category.
			if ( ! $default_cat_lang ) {
				pll_set_term_language( (int) $default, pll_default_language() );
			}

			foreach ( pll_languages_list() as $language ) {
				if ( $language !== $default_cat_lang && ! pll_get_term( $default, $language ) ) {
					self::create_default_product_cat( $language );
				}
			}
		}
	}

	/**
	 * Replaces the Uncategorized product cat in default language by the correct translation.
	 *
	 * @since 0.9.3
	 *
	 * @return void
	 */
	public static function replace_default_product_cats() {
		global $wpdb;

		if ( $default = get_option( 'default_product_cat' ) ) {
			$default_category = get_term( $default, 'product_cat' );

			if ( $default_category instanceof WP_Term ) {
				foreach ( PLL()->model->get_languages_list() as $language ) {
					if ( pll_default_language() !== $language->slug ) {
						$tr_cat = pll_get_term( $default_category->term_id, $language->slug );
						if ( $tr_cat ) {
							$tr_cat = get_term( $tr_cat, 'product_cat' );

							if ( $tr_cat instanceof WP_Term ) {
								$wpdb->query(
									$wpdb->prepare(
										"UPDATE {$wpdb->term_relationships} as tr1
										JOIN {$wpdb->term_relationships} as tr2 ON tr1.object_id = tr2.object_id
										AND tr2.term_taxonomy_id = %d
										SET tr1.term_taxonomy_id = %d
										WHERE tr1.term_taxonomy_id = %d",
										$language->term_taxonomy_id,
										$tr_cat->term_taxonomy_id,
										$default_category->term_taxonomy_id
									)
								);
							}
						}
					}
				}

				wp_cache_flush();
				delete_transient( 'wc_term_counts' );
				wp_update_term_count_now( pll_get_term_translations( $default_category->term_id ), 'product_cat' );
			}
		}
	}

	/**
	 * Updates the default product categories after update to WooCommerce 3.3.
	 *
	 * @since 0.9.3
	 *
	 * @param string $option Option name.
	 * @param string $value  WooCommerce DB version.
	 * @return void
	 */
	public static function update_330_wc_db_version( $option, $value ) {
		if ( version_compare( $value, '3.3.0', '>=' ) ) {
			self::replace_default_product_cats();
		}
	}
}
