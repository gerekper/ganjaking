<?php

/**
 * WooCommerce plugins integration
 */
class Permalink_Manager_WooCommerce {

	public function __construct() {
		add_action( 'init', array( $this, 'init_hooks' ), 99 );
		add_action( 'plugins_loaded', array( $this, 'init_early_hooks' ), 99 );
	}

	/**
	 * Add support for SEO plugins using their hooks
	 */
	function init_hooks() {
		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'permalink_manager_filter_query', array( $this, 'woocommerce_detect' ), 8, 5 );
			add_filter( 'template_redirect', array( $this, 'woocommerce_checkout_fix' ), 9 );

			if ( class_exists( 'Permalink_Manager_Pro_Functions' ) ) {
				if ( empty( $permalink_manager_options['general']['partial_disable']['post_types'] ) || ! in_array( 'shop_coupon', $permalink_manager_options['general']['partial_disable']['post_types'] ) ) {
					if ( is_admin() ) {
						add_filter( 'woocommerce_coupon_data_tabs', 'Permalink_Manager_Pro_Functions::woocommerce_coupon_tabs' );
						add_action( 'woocommerce_coupon_data_panels', 'Permalink_Manager_Pro_Functions::woocommerce_coupon_panel' );
						add_action( 'woocommerce_coupon_options_save', 'Permalink_Manager_Pro_Functions::woocommerce_save_coupon_uri', 9, 2 );
					}

					add_filter( 'request', 'Permalink_Manager_Pro_Functions::woocommerce_detect_coupon_code', 1, 1 );
					add_filter( 'permalink_manager_disabled_post_types', 'Permalink_Manager_Pro_Functions::woocommerce_coupon_uris', 9, 1 );
				}
			}

			// WooCommerce Import/Export
			add_filter( 'woocommerce_product_export_product_default_columns', array( $this, 'woocommerce_csv_custom_uri_column' ), 9 );
			add_filter( 'woocommerce_product_export_product_column_custom_uri', array( $this, 'woocommerce_export_custom_uri_value' ), 9, 3 );

			add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'woocommerce_csv_custom_uri_column' ), 9 );
			add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( $this, 'woocommerce_csv_custom_uri_column' ), 9 );
			add_action( 'woocommerce_product_import_inserted_product_object', array( $this, 'woocommerce_csv_import_custom_uri' ), 9, 2 );

			add_action( 'woocommerce_product_duplicate', array( $this, 'woocommerce_generate_permalinks_after_duplicate' ), 9, 2 );
			add_filter( 'permalink_manager_filter_default_post_uri', array( $this, 'woocommerce_product_attributes' ), 5, 5 );

			if ( wp_doing_ajax() && class_exists( 'SitePress' ) ) {
				add_filter( 'permalink_manager_filter_final_post_permalink', array( $this, 'woocommerce_translate_ajax_fragments_urls' ), 9999, 3 );
			}
		}

		// WooCommerce Wishlist Plugin
		if ( function_exists( 'tinv_get_option' ) ) {
			add_filter( 'permalink_manager_detect_uri', array( $this, 'ti_woocommerce_wishlist_uris' ), 15, 3 );
		}

		// WooCommerce Subscriptions
		if ( class_exists( 'WC_Subscriptions' ) ) {
			add_filter( 'permalink_manager_filter_final_post_permalink', array( $this, 'wcs_fix_subscription_links' ), 10, 3 );
		}
	}

	/**
	 * Some hooks must be called shortly after all the plugins are loaded
	 */
	public function init_early_hooks() {
		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'woocommerce_get_endpoint_url', array( 'Permalink_Manager_Core_Functions', 'control_trailing_slashes' ), 9 );
			add_action( 'before_woocommerce_init', array( $this, 'woocommerce_declare_compatibility' ) );
		}
	}

	/**
	 * Fix query on WooCommerce shop page & disable the canonical redirect if WooCommerce query variables are set
	 */
	function woocommerce_detect( $query, $old_query, $uri_parts, $pm_query, $content_type ) {
		global $woocommerce, $pm_query;

		$shop_page_id = get_option( 'woocommerce_shop_page_id' );

		// WPML - translate shop page id
		$shop_page_id = apply_filters( 'wpml_object_id', $shop_page_id, 'page', true );

		// Fix shop page
		if ( get_theme_support( 'woocommerce' ) && ! empty( $pm_query['id'] ) && is_numeric( $pm_query['id'] ) && $shop_page_id == $pm_query['id'] ) {
			$query['post_type'] = 'product';
			unset( $query['pagename'] );
		}

		// Fix WooCommerce pages
		if ( ! empty( $woocommerce->query->query_vars ) ) {
			$query_vars = $woocommerce->query->query_vars;

			foreach ( $query_vars as $key => $val ) {
				if ( isset( $query[ $key ] ) ) {
					$query['do_not_redirect'] = 1;
					break;
				}
			}
		}

		return $query;
	}

	/**
	 * Redirects the user from the Shop archive to the Shop page if the user is not searching for anything
	 * Disable canonical redirect on "thank you" & another WooCommerce pages
	 */
	function woocommerce_checkout_fix() {
		global $wp_query, $pm_query, $permalink_manager_options;

		// Redirect from Shop archive to selected page
		if ( is_shop() && empty( $pm_query['id'] ) ) {
			$redirect_mode = ( ! empty( $permalink_manager_options['general']['redirect'] ) ) ? $permalink_manager_options['general']['redirect'] : false;
			$redirect_shop = apply_filters( 'permalink_manager_redirect_shop_archive', false );
			$shop_page     = get_option( 'woocommerce_shop_page_id' );

			if ( $redirect_mode && $redirect_shop && $shop_page && empty( $wp_query->query_vars['s'] ) ) {
				$shop_url = get_permalink( $shop_page );
				wp_safe_redirect( $shop_url, $redirect_mode );
				exit();
			}
		}

		if ( is_checkout() || ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url() ) ) {
			$wp_query->query_vars['do_not_redirect'] = 1;
		}
	}

	/**
	 * Generate a new custom permalink for duplicated product
	 *
	 * @param WC_Product $new_product The new product object.
	 * @param WC_Product $old_product The product that was duplicated.
	 */
	function woocommerce_generate_permalinks_after_duplicate( $new_product, $old_product ) {
		if ( ! empty( $new_product ) ) {
			$product_id = $new_product->get_id();

			// Ignore variations
			if ( $new_product->get_type() === 'variation' || Permalink_Manager_Helper_Functions::is_post_excluded( $product_id, true ) ) {
				return;
			}

			$custom_uri = Permalink_Manager_URI_Functions_Post::get_default_post_uri( $product_id, false, true );
			Permalink_Manager_URI_Functions::save_single_uri( $product_id, $custom_uri, false, true );
		}
	}

	/**
	 * If the URI contains %pa_attribute_name% tag, replace it with the value of the attribute
	 *
	 * @param string $default_uri The default custom permalink that WordPress would use for the post.
	 * @param string $slug The post slug.
	 * @param WP_Post $post The post object.
	 * @param string $post_name The post slug.
	 * @param bool $native_uri true if the URI is a native URI, false if it's a custom URI
	 *
	 * @return string The default custom permalink
	 */
	function woocommerce_product_attributes( $default_uri, $slug, $post, $post_name, $native_uri ) {
		// Do not affect native URIs
		if ( $native_uri ) {
			return $default_uri;
		}

		// Use only for products
		if ( empty( $post->post_type ) || $post->post_type !== 'product' ) {
			return $default_uri;
		}

		preg_match_all( "/%pa_(.[^\%]+)%/", $default_uri, $custom_fields );

		if ( ! empty( $custom_fields[1] ) ) {
			$product = wc_get_product( $post->ID );

			foreach ( $custom_fields[1] as $i => $custom_field ) {
				$attribute_name  = sanitize_title( $custom_field );
				$attribute_value = $product->get_attribute( $attribute_name );

				$default_uri = str_replace( $custom_fields[0][ $i ], Permalink_Manager_Helper_Functions::sanitize_title( $attribute_value ), $default_uri );
			}
		}

		return $default_uri;
	}

	/**
	 * Check the current request is a WooCommerce AJAX request. If it is, check the translated page's URL should be returned
	 *
	 * @param string $permalink The full URL of the post
	 * @param WP_Post $post The post object
	 * @param string $old_permalink The original URL of the post.
	 *
	 * @return string The permalink is being returned.
	 */
	function woocommerce_translate_ajax_fragments_urls( $permalink, $post, $old_permalink ) {
		// Use it only if the permalinks are different
		if ( $permalink == $old_permalink || $post->post_type !== 'page' ) {
			return $permalink;
		}

		// A. Native WooCommerce AJAX events
		if ( ! empty( $_REQUEST['wc-ajax'] ) ) {
			$action = sanitize_title( $_REQUEST['wc-ajax'] );
		} // B. Shoptimizer theme
		else if ( ! empty( $_REQUEST['action'] ) ) {
			$action = sanitize_title( $_REQUEST['action'] );
		}

		// Allowed action names
		$allowed_actions = array( 'shoptimizer_pdp_ajax_atc', 'get_refreshed_fragments' );

		if ( ! empty( $action ) && in_array( $action, $allowed_actions ) ) {
			$translated_post_id = apply_filters( 'wpml_object_id', $post->ID, 'page' );
			$permalink          = ( $translated_post_id !== $post->ID ) ? get_permalink( $translated_post_id ) : $permalink;
		}

		return $permalink;
	}

	/**
	 * 4FA. Add a new column to the WooCommerce CSV Import/Export tool
	 *
	 * @param array $columns The array of columns to be displayed.
	 *
	 * @return array The $columns array.
	 */
	function woocommerce_csv_custom_uri_column( $columns ) {
		if ( ! is_array( $columns ) ) {
			return $columns;
		}

		$label = __( 'Custom URI', 'permalink-manager' );
		$key   = 'custom_uri';

		if ( current_filter() == 'woocommerce_csv_product_import_mapping_default_columns' ) {
			$columns[ $label ] = $key;
		} else {
			$columns[ $key ] = $label;
		}

		return $columns;
	}

	/**
	 * 4FB. Return the custom permalink of the product if it exists, otherwise return the default URI
	 *
	 * @param string $value The value of the column.
	 * @param WC_Product $product The product object.
	 * @param mixed $column_id The column ID.
	 *
	 * @return string The custom permalink or default permalink
	 */
	function woocommerce_export_custom_uri_value( $value, $product, $column_id ) {
		if ( empty( $value ) && ! empty( $product ) ) {
			$product_id = $product->get_id();

			// Get custom permalink or default permalink
			$value = Permalink_Manager_URI_Functions_Post::get_post_uri( $product_id );
		}

		return $value;
	}

	/**
	 * 4FC. Set the custom URI for the product using the value from CSV file, if not set use the default permalink
	 *
	 * @param WC_Product $product The product object.
	 * @param array $data The data array for the current row being imported.
	 */
	function woocommerce_csv_import_custom_uri( $product, $data ) {
		global $permalink_manager_uris;

		if ( ! empty( $product ) ) {
			$product_id = $product->get_id();

			// Ignore variations
			if ( $product->get_type() == 'variation' ) {
				return;
			}

			// A. Use default permalink if "Custom URI" is not set and did not exist before
			if ( empty( $permalink_manager_uris[ $product_id ] ) && empty( $data['custom_uri'] ) ) {
				$custom_uri = Permalink_Manager_URI_Functions_Post::get_default_post_uri( $product_id, false, true );
			} else if ( ! empty( $data['custom_uri'] ) ) {
				$custom_uri = Permalink_Manager_Helper_Functions::sanitize_title( $data['custom_uri'] );
			} else {
				return;
			}

			Permalink_Manager_URI_Functions::save_single_uri( $product_id, $custom_uri, false, true );
		}
	}

	/**
	 * Declare support for 'High-Performance order storage (COT)' and other features in WooCommerce
	 */
	function woocommerce_declare_compatibility() {
		$features_util_class = '\Automattic\WooCommerce\Utilities\FeaturesUtil';

		if ( class_exists( $features_util_class ) && method_exists( $features_util_class, 'declare_compatibility' ) ) {
			$features    = method_exists( $features_util_class, 'get_features' ) ? $features_util_class::get_features( true ) : array();

			foreach ( array_keys( $features ) as $feature ) {
				$features_util_class::declare_compatibility( $feature, PERMALINK_MANAGER_BASENAME );
			}
		}
	}

	/**
	 * Extract the Wishlist ID from the URI and add it to the $uri_parts array (WooCommerce Wishlist Plugin)
	 *
	 * @param array $uri_parts An array of the URI parts.
	 * @param string $request_url The URL that was requested.
	 * @param array $endpoints An array of all the endpoints that are currently registered.
	 *
	 * @return array The URI parts.
	 */
	function ti_woocommerce_wishlist_uris( $uri_parts, $request_url, $endpoints ) {
		global $permalink_manager_uris;

		$wishlist_pid = function_exists( 'tinv_get_option' ) ? tinv_get_option( 'general', 'page_wishlist' ) : '';

		// Find the Wishlist page URI
		if ( is_numeric( $wishlist_pid ) && ! empty( $permalink_manager_uris[ $wishlist_pid ] ) ) {
			$wishlist_uri = preg_quote( $permalink_manager_uris[ $wishlist_pid ], '/' );

			// Extract the Wishlist ID
			preg_match( "/^({$wishlist_uri})\/([^\/]+)\/?$/", $uri_parts['uri'], $output_array );

			if ( ! empty( $output_array[2] ) ) {
				$uri_parts['uri']            = $output_array[1];
				$uri_parts['endpoint']       = 'tinvwlID';
				$uri_parts['endpoint_value'] = $output_array[2];
			}
		}

		return $uri_parts;
	}

	/**
	 * Keep the query strings appended to the product permalinks by WooCommerce Subscriptions
	 *
	 * @param string $permalink
	 * @param WP_Post $post
	 * @param string $old_permalink
	 *
	 * @return string
	 */
	function wcs_fix_subscription_links( $permalink, $post, $old_permalink ) {
		if ( ! empty( $post->post_type ) && $post->post_type == 'product' && strpos( $old_permalink, 'switch-subscription=' ) !== false ) {
			$query_arg = parse_url( $old_permalink, PHP_URL_QUERY );
			$permalink = "{$permalink}?{$query_arg}";
		}

		return $permalink;
	}
}