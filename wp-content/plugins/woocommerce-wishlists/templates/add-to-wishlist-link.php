<?php global $woocommerce_wishlist, $add_to_wishlist_args, $product; ?>
<?php $class_name = woocommerce_wishlists_get_wishlists_for_product( $product->get_id() ) ? 'wl-button-already-in' : ''; ?>
<input type="hidden" name="wlid" id="wlid"/>
<input type="hidden" name="add-to-wishlist-type" value="<?php echo $product->get_type(); ?>"/>
<input type="hidden" name="wl_from_single_product" value="<?php echo is_product() ? '1' : '0'; ?>"/>

<div id="wl-wrapper" class="woocommerce wl-button-wrap wl-row wl-clear <?php echo $class_name; ?>">
	<?php if ( woocommerce_wishlists_get_wishlists_for_product( $product->get_id() ) ) : ?>
        <div class="wl-already-in">
			<?php  echo apply_filters( 'woocommerce_wishlist_already_in_list_text', __( 'This item is already in one of your wishlists', 'wc_wishlist' )); ?>
            <ul>
				<?php foreach ( woocommerce_wishlists_get_wishlists_for_product( $product->get_id() ) as $list_id ) : ?>
                    <li>
                        <a href="<?php echo WC_Wishlists_Wishlist::get_the_url_edit( $list_id ); ?>" title="<?php echo get_the_title( $list_id ); ?>"><?php echo get_the_title( $list_id ); ?></a>
                    </li>
				<?php endforeach; ?>
            </ul>
        </div>
	<?php endif; ?>

    <a rel="nofollow" href="" data-productid="<?php echo $product->get_id(); ?>" data-listid="<?php echo $add_to_wishlist_args['single_id']; ?>" class="<?php echo implode( ' ', $add_to_wishlist_args['btn_class'] ); ?>">
		<?php echo apply_filters( 'woocommerce_wishlist_add_to_wishlist_text', WC_Wishlists_Settings::get_setting( 'wc_wishlist_button_text', __( 'Add to wishlist', 'wc_wishlist' ) ), $product->get_type() ); ?>
    </a>
</div>

<?php if ( $product->is_type( 'external' ) ) : ?>
    <script type="text/javascript">
        window.woocommerce_wishlist_add_to_wishlist_url = "<?php echo esc_url( add_query_arg( array( 'add-to-wishlist-itemid' => $product->get_id() ), $product->add_to_cart_url() ) ); ?>";
    </script>
<?php else : ?>
    <script type="text/javascript">
        //<![CDATA[
        window.woocommerce_wishlist_add_to_wishlist_url = "<?php echo esc_url( add_query_arg( array( 'add-to-wishlist-itemid' => $product->get_id() ) ) ); ?>";
        //]]>
    </script>
<?php endif; ?>
