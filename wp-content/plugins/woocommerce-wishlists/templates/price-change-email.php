<?php do_action( 'woocommerce_email_header', $email_heading, null ); ?>

    <p><?php printf( __( "Price drop alert from %s. Items below have been reduced in price:", 'wc_wishlist' ), get_option( 'blogname' ) ); ?></p>


<?php foreach ( $changes as $change ) : ?>
    <h2><a href="<?php echo $change['url']; ?>"><?php printf(__('New Low Price For: %s', 'wc_wishlist'), $change['title']); ?></h2>
<?php endforeach; ?>


<?php do_action( 'woocommerce_email_footer', null ); ?>
