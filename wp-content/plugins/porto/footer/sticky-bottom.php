<?php

global $porto_settings;
$icon_count = empty( $porto_settings['show-icon-menus-mobile'] ) ? 0 : count( $porto_settings['show-icon-menus-mobile'] );
if ( is_customize_preview() && $icon_count < 1 ) {
	echo '<div class="porto-sticky-navbar d-none"></div>';
}
if ( ! defined( 'ABSPATH' ) || $icon_count < 1 ) {
	return;
}
$sticky_icon = $porto_settings['show-icon-menus-mobile'];
$woo         = defined( 'WOOCOMMERCE_VERSION' );
$wishlist    = defined( 'YITH_WCWL' );
?>
<div class="porto-sticky-navbar has-ccols ccols-<?php echo esc_attr( $icon_count ); ?> d-sm-none">
	<?php if ( is_numeric( array_search( 'home', $sticky_icon ) ) ) : ?>
		<div class="sticky-icon link-home">
			<a href="<?php echo esc_url( get_home_url() ); ?>">
				<i class="<?php echo esc_attr( $porto_settings['sticky-icon-home'] ); ?>"></i>
				<span class="label"><?php esc_html_e( 'home', 'porto' ); ?></span>
			</a>
		</div>
	<?php endif; ?>
	<?php if ( is_numeric( array_search( 'blog', $sticky_icon ) ) ) : ?>
		<div class="sticky-icon link-blog">
			<a href="<?php echo esc_url( get_post_permalink( get_option( 'page_for_posts' ) ) ); ?>">
				<i class="<?php echo esc_attr( $porto_settings['sticky-icon-blog'] ); ?>"></i>
				<span class="label"><?php esc_html_e( 'blog', 'porto' ); ?></span>
			</a>
		</div>
	<?php endif; ?>
	<?php if ( is_numeric( array_search( 'shop', $sticky_icon ) ) & $woo ) : ?>
		<div class="sticky-icon link-shop">
			<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">
				<i class="<?php echo esc_attr( $porto_settings['sticky-icon-shop'] ); ?>"></i>
				<span class="label"><?php esc_html_e( 'categories', 'porto' ); ?></span>
			</a>
		</div>
	<?php endif; ?>
	<?php if ( is_numeric( array_search( 'wishlist', $sticky_icon ) ) & $wishlist & $woo ) : ?>
		<div class="sticky-icon link-wishlist">
			<a href="<?php echo esc_url( YITH_WCWL()->get_wishlist_url() ); ?>">
				<i class="<?php echo esc_attr( $porto_settings['sticky-icon-wishlist'] ); ?>"></i>
				<span class="label"><?php esc_html_e( 'wishlist', 'porto' ); ?></span>
			</a>
		</div>
	<?php endif; ?>
	<?php if ( is_numeric( array_search( 'account', $sticky_icon ) ) & $woo ) : ?>
		<div class="sticky-icon link-account">
			<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>">
				<i class="<?php echo esc_attr( $porto_settings['sticky-icon-account'] ); ?>"></i>
				<span class="label"><?php esc_html_e( 'account', 'porto' ); ?></span>
			</a>
		</div>
	<?php endif; ?>
	<?php if ( is_numeric( array_search( 'cart', $sticky_icon ) ) & $woo ) : ?>
		<div class="sticky-icon link-cart">
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
				<span class="cart-icon">
					<i class="<?php echo esc_attr( $porto_settings['sticky-icon-cart'] ); ?>"></i>
					<span class="cart-items"><?php echo intval( wc()->cart->get_cart_contents_count() ); ?></span>
				</span>
				<span class="label"><?php esc_html_e( 'cart', 'porto' ); ?></span>
			</a>
		</div>
	<?php endif; ?>
</div>
