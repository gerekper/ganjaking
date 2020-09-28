<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php

$name = $name ? $name : get_post_meta( $wishlist->id, '_wishlist_first_name', true ) . ' ' . get_post_meta( $wishlist->id, '_wishlist_last_name', true );
$name = $name ? $name : __( 'Someone', 'wc_wishlist' );

?>

<?php
$name = $_POST['wishlist_email_from'];
?>

<p>
	<?php printf( __( '%s has a wishlist to share on <a href="%s">%s</a>', 'wc_wishlist' ), $name, get_site_url(), get_bloginfo( 'name' ) ); ?>
</p>

<?php if ( $additional_content ): ?>
    <p>
		<?php echo esc_html( $additional_content ); ?>
    </p>
<?php endif; ?>
<p>
	<?php printf( __( 'You can view this list by clicking on the link or copy and pasting it into your browser. <br/>View List: <a href="%s">%s</a>', 'wc_wishlist' ), $wishlist->get_the_url_view( $wishlist->id, true ), $wishlist->get_the_url_view( $wishlist->id, true ) ); ?>
</p>


<?php do_action( 'woocommerce_email_footer', $email_heading, $email ); ?>
