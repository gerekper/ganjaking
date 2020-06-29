<?php
/**
 * Share template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 1.1.5
 */

global $yith_wcwl;

/*if( get_option( 'yith_wcwl_share_fb' ) == 'yes' || get_option( 'yith_wcwl_share_twitter' ) == 'yes' || get_option( 'yith_wcwl_share_pinterest' ) == 'yes'  || get_option( 'yith_wcwl_share_email' ) == 'yes') {
	$share_url  = $yith_wcwl->get_wishlist_url();
	$share_url .= get_option( 'permalink-structure' ) != '' ? '&amp;user_id=' : '?user_id=';
	$share_url .= get_current_user_id();
	echo YITH_WCWL_UI::get_share_links( $share_url );
}*/

global $porto_settings;

$share = porto_get_meta_value( 'page_share' );
if ( $porto_settings['share-enable'] && 'no' !== $share && ( 'yes' === $share || ( 'yes' !== $share && $porto_settings['page-share'] ) ) ) : ?>
<div class="page-share wishlist-share">
	<h3><i class="fas fa-share"></i><?php esc_html_e( 'Share on', 'porto' ); ?></h3>
	<?php get_template_part( 'share' ); ?>
</div>
	<?php
endif;
