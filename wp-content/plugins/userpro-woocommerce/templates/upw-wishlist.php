<?php
    $upw_default_options = new UPWDefaultOptions();
    $activated_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
    if( in_array('yith-woocommerce-wishlist/init.php', $activated_plugins) || in_array('yith-woocommerce-wishlist-premium/init.php', $activated_plugins) || in_array('woocommerce-wishlists/woocommerce-wishlists.php', $activated_plugins) ) {
?>

<div class="userpro-section userpro-column userpro-collapsible-1 userpro-collapsed-0" >
    <?php _e( $upw_default_options->userpro_woocommerce_get_option( 'upw_wishlist_tab_text' ), 'userpro-woocommerce');?>
</div>
<div class='userpro-field userpro-field-all-media userpro-field-view'>
	<?php 
            
            if( in_array('yith-woocommerce-wishlist/init.php', $activated_plugins) || in_array('yith-woocommerce-wishlist-premium/init.php', $activated_plugins) ) {
                echo do_shortcode('[yith_wcwl_wishlist]');
            }
            elseif(in_array('woocommerce-wishlists/woocommerce-wishlists.php', $activated_plugins)){
                echo do_shortcode('[wc_wishlists_my_archive wc_wishlist_guest_enabled=enabled]');
            }
        ?>
</div>

    <?php } ?>