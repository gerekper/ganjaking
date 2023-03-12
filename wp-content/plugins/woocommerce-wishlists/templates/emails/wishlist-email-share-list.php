<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php

$name = $name ? $name : get_post_meta( $wishlist->id, '_wishlist_first_name', true ) . ' ' . get_post_meta( $wishlist->id, '_wishlist_last_name', true );
$name = $name ? $name : __( 'Someone', 'wc_wishlist' );

?>

<?php
$name = $_POST['wishlist_email_from'];
?>

<p>
	<?php printf( __( '%s has a wishlist to share on <a href="%s">%s</a>', 'wc_wishlist' ), esc_html($name), get_site_url(), get_bloginfo( 'name' ) ); ?>
</p>

<?php $wishlist_items = WC_Wishlists_Wishlist_Item_Collection::get_items( $wishlist->id );

if ( sizeof( $wishlist_items ) > 0 ) : ?>

    <ul>
		<?php foreach ( $wishlist_items as $wishlist_item_key => $item ) :
			$_product = wc_get_product( $item['data'] ); ?>

            <li>
				<?php echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( is_array( $item['variation'] ) ? add_query_arg( $item['variation'], $_product->get_permalink() ) : $_product->get_permalink() ), $_product->get_name() ), $item, $wishlist_item_key ); ?>
				<?php $price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $item, $wishlist_item_key ); ?>
				<?php echo ', Price: ' . apply_filters( 'woocommerce_wishlist_list_item_price', $price, $item, $wishlist ); ?>
				<?php echo ' Quantity: ' . apply_filters( 'woocommerce_wishlist_list_item_quantity_value', esc_attr( $item['quantity'] ), $item, $wishlist ); ?>
            </li>
		<?php endforeach; ?>
    </ul>
<?php endif; ?>



<?php if ( $additional_content ): ?>
    <?php echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) ); ?>
<?php endif; ?>
<p>
	<?php printf( __( 'You can view this list by clicking on the link or copy and pasting it into your browser. <br/>View List: <a href="%s">%s</a>', 'wc_wishlist' ), $wishlist->get_the_url_view( $wishlist->id, true ), $wishlist->get_the_url_view( $wishlist->id, true ) ); ?>
</p>


<?php do_action( 'woocommerce_email_footer', $email_heading, $email ); ?>
