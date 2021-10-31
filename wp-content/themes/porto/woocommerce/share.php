<?php
/**
 * Share template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */


global $porto_settings;

$share         = porto_get_meta_value( 'page_share' );
$share_enabled = $porto_settings['share-enable'] && 'no' !== $share && ( 'yes' === $share || ( 'yes' !== $share && $porto_settings['page-share'] ) );

do_action( 'yith_wcwl_before_wishlist_share', $wishlist );
if ( ! $share_enabled && is_page() && get_option( 'yith_wcwl_wishlist_page_id' ) == get_the_ID() ) :
	$porto_settings['share-facebook']   = '';
	$porto_settings['share-twitter']    = '';
	$porto_settings['share-linkedin']   = '';
	$porto_settings['share-googleplus'] = '';
	$porto_settings['share-pinterest']  = '';
	$porto_settings['share-email']      = '';
	$porto_settings['share-vk']         = '';
	$porto_settings['share-xing']       = '';
	$porto_settings['share-tumblr']     = '';
	$porto_settings['share-reddit']     = '';
	$porto_settings['share-whatsapp']   = '';

	if ( $share_facebook_enabled ) {
		$porto_settings['share-facebook'] = '1';
	}
	if ( $share_twitter_enabled ) {
		$porto_settings['share-twitter'] = '1';
	}
	if ( $share_pinterest_enabled ) {
		$porto_settings['share-pinterest'] = '1';
	}
	if ( $share_email_enabled ) {
		$porto_settings['share-email'] = '1';
	}
	if ( $share_whatsapp_enabled ) {
		$porto_settings['share-whatsapp'] = '1';
	}

	do_action( 'yith_wcwl_before_wishlist_share', $wishlist );
	?>
<div class="page-share wishlist-share">
		<h3 class="yith-wcwl-share-title"><i class="fas fa-share"></i><?php echo esc_html( $share_title ); ?></h3>
		<?php
			porto_get_template_part(
				'share',
				null,
				array(
					'share_link_url' => $share_link_url,
				)
			);
		?>

		<?php do_action( 'yith_wcwl_after_share_buttons', $share_link_url, $share_title, $share_link_title ); ?>
	</div>
	<?php if ( $share_url_enabled ) : ?>
		<div class="yith-wcwl-after-share-section">
			<input class="copy-target" readonly="readonly" type="url" name="yith_wcwl_share_url" id="yith_wcwl_share_url" value="<?php echo esc_attr( $share_link_url ); ?>"/>
			<?php echo ( ! empty( $share_link_url ) ) ? sprintf( '<small>%s <span class="copy-trigger">%s</span> %s</small>', esc_html__( '(Now', 'yith-woocommerce-wishlist' ), esc_html__( 'copy', 'yith-woocommerce-wishlist' ), esc_html__( 'this wishlist link and share it anywhere)', 'yith-woocommerce-wishlist' ) ) : ''; ?>
</div>
		<?php
endif;
	do_action( 'yith_wcwl_after_wishlist_share', $wishlist );
endif;
