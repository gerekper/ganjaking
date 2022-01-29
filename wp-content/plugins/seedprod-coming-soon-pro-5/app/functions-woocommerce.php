<?php



/**
 * Render WooCommerce Custom Products Grid Shortcode for Builder Preview
 */
function seedprod_pro_render_shortcode_wc_custom_products_grid() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$cols           = isset( $_GET['cols'] ) ? sanitize_text_field( wp_unslash( $_GET['cols'] ) ) : '';
		$paginate       = isset( $_GET['paginate'] ) ? sanitize_text_field( wp_unslash( $_GET['paginate'] ) ) : '';
		$limit          = isset( $_GET['limit'] ) ? sanitize_text_field( wp_unslash( $_GET['limit'] ) ) : '';
		$order_by       = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';
		$order          = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
		$skus           = isset( $_GET['skus'] ) ? sanitize_text_field( wp_unslash( $_GET['skus'] ) ) : '';
		$ids            = isset( $_GET['ids'] ) ? sanitize_text_field( wp_unslash( $_GET['ids'] ) ) : '';
		$category       = isset( $_GET['category'] ) ? sanitize_text_field( wp_unslash( $_GET['category'] ) ) : '';
		$tag            = isset( $_GET['tag'] ) ? sanitize_text_field( wp_unslash( $_GET['tag'] ) ) : '';
		$product_group  = isset( $_GET['product_group'] ) ? sanitize_text_field( wp_unslash( $_GET['product_group'] ) ) : ''; // Options - on_sale, best_selling, top_rated.
		$attribute      = isset( $_GET['attribute'] ) ? sanitize_text_field( wp_unslash( $_GET['attribute'] ) ) : '';
		$terms          = isset( $_GET['terms'] ) ? sanitize_text_field( wp_unslash( $_GET['terms'] ) ) : '';
		$terms_operator = isset( $_GET['terms_operator'] ) ? sanitize_text_field( wp_unslash( $_GET['terms_operator'] ) ) : '';
		$tag_operator   = isset( $_GET['tag_operator'] ) ? sanitize_text_field( wp_unslash( $_GET['tag_operator'] ) ) : '';
		$cat_operator   = isset( $_GET['cat_operator'] ) ? sanitize_text_field( wp_unslash( $_GET['cat_operator'] ) ) : '';
		$visibility     = isset( $_GET['visibility'] ) ? sanitize_text_field( wp_unslash( $_GET['visibility'] ) ) : '';
		$source         = isset( $_GET['source'] ) ? sanitize_text_field( wp_unslash( $_GET['source'] ) ) : '';
		$group_attr     = '';

		if ( '' !== $product_group ) {
			$group_attr = $product_group . "='true'";
		}

		if ( 'all_products' === $source ) {
			echo do_shortcode( "[products paginate='$paginate' limit='$limit' columns='$cols' orderby='$order_by' order='$order' ]" );
		}

		if ( 'featured_products' === $source ) {
			echo do_shortcode( "[products visibility='featured' paginate='$paginate' limit='$limit' columns='$cols' ]" );
		}

		if ( 'sale_products' === $source ) {
			echo do_shortcode( "[products orderby='popularity' on_sale='true' paginate='$paginate' limit='$limit' columns='$cols' ]" );
		}

		if ( 'best_selling_products' === $source ) {
			echo do_shortcode( "[products best_selling='true' paginate='$paginate' limit='$limit' columns='$cols' ]" );
		}

		if ( 'recent_products' === $source ) {
			echo do_shortcode( "[products orderby='id' order='DESC' visibility='visible' paginate='$paginate' limit='$limit' columns='$cols']" );
		}

		if ( 'top_rated_products' === $source ) {
			echo do_shortcode( "[products orderby='popularity' top_rated='true' paginate='$paginate' limit='$limit' columns='$cols']" );
		}

		if ( 'custom_query' === $source ) {
			echo do_shortcode( "[products columns='$cols' paginate='$paginate' limit='$limit' orderby='$order_by' order='$order' ids='$ids' skus='$skus' category='$category' tag='$tag' $group_attr attribute='$attribute' terms='$terms' terms_operator='$terms_operator' tag_operator='$tag_operator' cat_operator='$cat_operator' visibility='$visibility']" );
		}

		exit;
	}
}

/**
 * Get WooCommerce Products.
 *
 * @return JSON object.
 */
function seedprod_pro_get_woocommerce_products() {
	$products = array();

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		// Check if Woocommmerce is installed and active.
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			// Fetch Products.
			$args = array(
				'status' => 'publish',
			);

			$p = wc_get_products( $args );

			foreach ( $p as $product ) {
				$products[] = $product->get_data();
			}
		}
	}

	wp_send_json( $products );
}

/**
 * Get product taxonomy.
 *
 * @return JSON object.
 */
function seedprod_pro_get_woocommerce_product_taxonomy() {
	$taxonomy = array();

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		// Check if Woocommmerce is installed and active.
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			// Fetch taxonomy.
			$args = array(
				'taxonomy'   => isset( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : '',
				'hide_empty' => false,
			);

			$taxonomy = get_terms( $args );
		}
	}

	wp_send_json( $taxonomy );
}

/**
 * Get list of product attributes
 *
 * @return JSON object.
 */
function seedprod_pro_get_woocommerce_product_attributes() {
	$attributes = array();

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		// Check if Woocommmerce is installed and active.
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$attributes = wc_get_attribute_taxonomies();
		}
	}

	wp_send_json( $attributes );
}

/**
 * Get list of product attribute terms.
 *
 * @return JSON object.
 */
function seedprod_pro_get_woocommerce_product_attribute_terms() {
	$attribute_terms = array();

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		// Check if Woocommmerce is installed and active.
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$attribute = isset( $_GET['attribute'] ) ? sanitize_text_field( wp_unslash( $_GET['attribute'] ) ) : '';

			// Get attribute terms
			if ( $attribute ) {
				$attribute_terms = get_terms(
					array(
						'taxonomy'   => 'pa_' . $attribute,
						'hide_empty' => false,
					)
				);
			}
		}
	}

	wp_send_json( $attribute_terms );
}

/**
 * Render WooCommerce Checkout Shortcode for Builder Preview
 */
function seedprod_pro_render_shortcode_wc_checkout() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		// Prevent redirect if cart is empty for preview
		add_filter( 'woocommerce_checkout_redirect_empty_cart', '__return_false' );
		add_filter( 'woocommerce_checkout_update_order_review_expired', '__return_false' );

		echo do_shortcode( '[woocommerce_checkout]' );
		exit;
	}
}


/**
 * Render WooCommerce Cart Shortcode for Builder Preview
 */
function seedprod_pro_render_shortcode_wc_cart() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		echo do_shortcode( '[woocommerce_cart]' );
		exit;
	}
}


