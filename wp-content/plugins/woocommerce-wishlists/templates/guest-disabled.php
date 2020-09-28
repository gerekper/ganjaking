<?php do_action( 'woocommerce_wishlists_before_wrapper' ); ?>
    <div id="wl-wrapper" class="woocommerce">
		<?php echo WC_Wishlists_Settings::get_setting( 'wc_wishlist_guest_disabled_message', __( "Please logon or register for an account to create and manage wishlists" ) ); ?>
    </div>
<?php do_action( 'woocommerce_wishlists_after_wrapper' ); ?>