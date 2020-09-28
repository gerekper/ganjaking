<?php

function woocommerce_wishlists_get_template( $template_name, $args = array(), $template_path = '' ) {
	WC_Wishlist_Compatibility::wc_get_template( $template_name, $args, $template_path, WC_Wishlists_Plugin::plugin_path() . '/templates/' );
}

function woocommerce_wishlists_is_product_in_wishlist( $product_id, $wishlist_id = false ) {
	if ( $wishlist_id ) {
		$product_ids = WC_Wishlists_User::get_wishlist_product_ids();

		return isset( $product_ids[ $product_id ] ) && in_array( $wishlist_id, $product_ids[ $product_id ] );
	}

	return array_key_exists( $product_id, WC_Wishlists_User::get_wishlist_product_ids() );
}

function woocommerce_wishlists_get_wishlists_for_product( $product_id ) {
	$product_ids = WC_Wishlists_User::get_wishlist_product_ids();

	return isset( $product_ids[ $product_id ] ) ? $product_ids[ $product_id ] : false;
}

/*
 * Template Tags
 */

function woocommerce_wishlists_nav( $html_id ) {
	global $wp_query;

	$html_id = esc_attr( $html_id );

	if ( $wp_query->max_num_pages > 1 ) :
		?>
        <nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
            <div class="nav-previous alignleft"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older lists', 'wc_wishlist' ) ); ?></div>
            <div class="nav-next alignright"><?php previous_posts_link( __( 'Newer lists <span class="meta-nav">&rarr;</span>', 'wc_wishlist' ) ); ?></div>
        </nav><!-- #<?php echo $html_id; ?> .navigation -->
		<?php
	endif;
}

/*
 * URL Generation
 */

function woocommerce_wishlist_url_item_remove( $wishlist_id, $wishlist_item_key ) {
	return WC_Wishlists_Plugin::nonce_url( 'wishlists-remove-from-list', add_query_arg( array(
		'wlaction'          => 'wishlists-remove-from-list',
		'wlid'              => $wishlist_id,
		'wishlist-item-key' => $wishlist_item_key
	), WC_Wishlists_Pages::get_url_for( 'edit-my-list' ) ) );
}

function woocommerce_wishlist_url_item_add_to_cart( $wishlist_id, $wishlist_item_key, $wishlist_sharing_key = false, $wishlist_mode = false ) {

	if ( $wishlist_mode == 'edit' ) {
		return WC_Wishlists_Plugin::nonce_url( 'add-cart-item', add_query_arg( array(
			'wlkey'             => $wishlist_sharing_key,
			'wlaction'          => 'add-cart-item',
			'wlid'              => $wishlist_id,
			'wishlist-item-key' => $wishlist_item_key
		), WC_Wishlists_Wishlist::get_the_url_edit( $wishlist_id ) ) );
	} else {
		return WC_Wishlists_Plugin::nonce_url( 'add-cart-item', add_query_arg( array(
			'wlkey'             => $wishlist_sharing_key,
			'wlaction'          => 'add-cart-item',
			'wlid'              => $wishlist_id,
			'wishlist-item-key' => $wishlist_item_key
		), WC_Wishlists_Wishlist::get_the_url_view( $wishlist_id ) ) );
	}
}

function woocommerce_wishlist_use_add_to_cart_quantity_prompts( $wishlist_id, $wishlist_list_item ) {

	$prompt = get_option( 'wc_wishlist_add_to_cart_prompt_for_qty', false ) == 'yes';

	return apply_filters( 'woocommerce_wishlist_use_add_to_cart_quantity_prompts', $prompt, $wishlist_id, $wishlist_list_item );

}

function woocommerce_wishlist_url_add_all_to_cart( $wishlist_id, $wishlist_sharing_key = false, $wishlist_mode = false ) {

	if ( $wishlist_mode == 'edit' ) {
		$url = WC_Wishlists_Plugin::nonce_url( 'add-cart-items', add_query_arg( array(
			'wlaction' => 'add-cart-items',
			'wlkey'    => $wishlist_sharing_key,
			'wlid'     => $wishlist_id
		), WC_Wishlists_Wishlist::get_the_url_edit( $wishlist_id ) ) );
	} else {
		$url = WC_Wishlists_Plugin::nonce_url( 'add-cart-items', add_query_arg( array(
			'wlaction' => 'add-cart-items',
			'wlkey'    => $wishlist_sharing_key,
			'wlid'     => $wishlist_id
		), WC_Wishlists_Wishlist::get_the_url_view( $wishlist_id ) ) );
	}

	if ( isset( $_GET['preview'] ) ) {
		return add_query_arg( array( 'preview' => 'true' ), $url );
	} else {
		return $url;
	}
}

function _woocommerce_wishlist_sort_item_collection_date( $a, $b ) {
	if ( isset( $a['wl_date'] ) && isset( $b['wl_date'] ) ) {
		if ( $a['wl_date'] == $b['wl_date'] ) {
			return 0;
		}

		return ( $a['wl_date'] < $b['wl_date'] ) ? - 1 : 1;
	} else {
		return 0;
	}
}

function _woocommerce_wishlist_sort_item_collection_price_asc( $a, $b ) {
	$prod_a = $a['data'];
	$prod_b = $b['data'];

	$prod_a_price = get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' || WC()->customer->is_vat_exempt() ? wc_get_price_excluding_tax( $prod_a ) : $prod_a->get_price();
	$prod_b_price = get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' || WC()->customer->is_vat_exempt() ? wc_get_price_excluding_tax( $prod_b ) : $prod_b->get_price();

	if ( $prod_a_price == $prod_b_price ) {
		return 0;
	}

	return ( $prod_a_price > $prod_b_price ) ? - 1 : 1;
}

function _woocommerce_wishlist_sort_item_collection_price_desc( $a, $b ) {
	$prod_a = $a['data'];
	$prod_b = $b['data'];

	$prod_a_price = get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' || WC()->customer->is_vat_exempt() ? wc_get_price_excluding_tax( $prod_a ) : $prod_a->get_price();
	$prod_b_price = get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' || WC()->customer->is_vat_exempt() ? wc_get_price_excluding_tax( $prod_b ) : $prod_b->get_price();

	if ( $prod_a_price == $prod_b_price ) {
		return 0;
	}

	return ( $prod_a_price < $prod_b_price ) ? - 1 : 1;
}

function _woocommerce_wishlist_filter_item_collection_category( $item_collection, $category ) {
	if ( !is_array( $category ) ) {
		$category = array( $category );
	}

	$new_collection = array();

	foreach ( $item_collection as $key => $item ) {
		$product = $item['data'];
		$terms   = $product->get_category_ids();
		if ( $terms && !is_wp_error( $terms ) && count( array_intersect( $terms, $category ) ) > 0 ) {
			$new_collection[ $key ] = $item;
		}
	}

	return $new_collection;
}

function woocommerce_wishlist_register_email_form( $wishlist ) {
	global $email_forms;
	$email_forms[] = $wishlist;
}

add_action( 'wp_footer', 'woocommerce_wishlist_render_email_forms' );
if ( !function_exists( 'woocommerce_wishlist_render_email_forms' ) ) {

	function woocommerce_wishlist_render_email_forms() {
		global $email_forms;

		if ( $email_forms && !empty( $email_forms ) ) :

			foreach ( $email_forms as $wishlist ) {
				?>

                <div class="wl-modal" id="share-via-email-<?php echo $wishlist->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display:none;z-index:9999;">
                    <div class="wl-modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h1 id="myModalLabel"><?php _e( 'Share this list via e-mail ', 'wc_wishlist' ); ?></h1>
                    </div>
                    <div class="wl-modal-body">
                        <form id="share-via-email-<?php echo $wishlist->id; ?>-form" action="" method="POST">
                            <p class="form-row form-row-wide" class="wishlist_name">
                                <label for="wishlist_email_from"><?php _e( 'Your name:', 'wc_wishlist' ); ?></label>
                                <input type="text" class="input-text" name="wishlist_email_from" value="<?php echo esc_attr( get_post_meta( $wishlist->id, '_wishlist_first_name', true ) . ' ' . get_post_meta( $wishlist->id, '_wishlist_last_name', true ) ); ?>"/>
                            </p>
                            <p class="form-row form-row-wide">
                                <label for="wishlist_email_to"><?php _e( 'To:', 'wc_wishlist' ); ?></label>
                                <textarea class="wl-em-to" name="wishlist_email_to" rows="2" placeholder="<?php _e( 'Type in e-mail addresses: jo@example.com, jan@example.com.', 'wc_wishlist' ); ?>"></textarea>
                            </p>
                            <p class="form-row form-row-wide">
                                <label for="wishlist_content"><?php _e( 'Add a note:', 'wc_wishlist' ); ?></label>
                                <textarea class="wl-em-note" name="wishlist_content" rows="4"></textarea>
                            </p>
                            <div class="clear"></div>
                            <input type="hidden" name="wishlist_id" value="<?php echo esc_attr( $wishlist->id ); ?>"/>
                            <input type="hidden" name="wishlist-action" value="share-via-email"/>
							<?php echo WC_Wishlists_Plugin::nonce_field( 'share-via-email' ) ?>
                        </form>
                    </div>
                    <div class="wl-modal-footer">
                        <button class="button alt share-via-email-button" data-form="share-via-email-<?php echo $wishlist->id; ?>-form" aria-hidden="true"><?php _e( 'Send email', 'wc_wishlist' ); ?></button>
                    </div>
                </div>

				<?php
			}

		endif;
	}

}

function _woocommerce_wishlist_insert_return_url() {
	$redirect = add_query_arg( array( 'wlredirected' => 1 ) );
	echo '<input type="hidden" name="redirect" value="' . esc_url( $redirect ) . '" />';
}

add_filter( 'woocommerce_registration_redirect', '_woocommerce_wishlist_registration_redirect' );
function _woocommerce_wishlist_registration_redirect( $redirect ) {
	if ( isset( $_POST['redirect'] ) ) {
		if ( !empty( $_POST['redirect'] ) ) {
			$redirect = $_POST['redirect'];
		}
	}

	return $redirect;
}


add_action( 'woocommerce_wishlists_before_wrapper', 'wc_wishlists_disable_direct_template_access' );

function wc_wishlists_disable_direct_template_access() {
	if ( !defined( 'ABSPATH' ) ) {
		die; // Exit if accessed directly
	}
}
