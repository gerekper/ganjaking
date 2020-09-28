<div class="wrap">
    <h2><?php _e( 'Wishlist Notification Settings', 'wc_wishlist' ); ?></h2>


    <form method="POST">
        <input type="hidden" name="wc-wishlist-admin-action" value="send-notifications"/>
		<?php echo WC_Wishlists_Plugin::nonce_field( 'send-notifications' ); ?>

        <input type="submit" value="Go"/>

    </form>

</div>