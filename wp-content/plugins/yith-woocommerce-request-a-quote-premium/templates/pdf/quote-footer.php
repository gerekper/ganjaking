<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @var $footer string
 * @var $pagination string
 * @var $order_id int
 */

if ( function_exists( 'icl_get_languages' ) ) {
	global $sitepress;
	$lang = get_post_meta( $order_id, 'wpml_language', true );
	YITH_Request_Quote_Premium()->change_pdf_language( $lang );
}
?>
<div class="footer">
	<?php if ( '' !== $footer ) : ?>
		<div class="footer-content"><?php echo wp_kses_post( $footer ); ?></div>
	<?php endif; ?>
	<?php

	if ( 'yes' === $pagination ) :
		?>
		<div class="page"><?php echo esc_html( __( 'Page', 'yith-woocommerce-request-a-quote' ) ); ?> <span class="pagenum"></span>
		</div>
	<?php endif ?>
</div>
<?php
if ( function_exists( 'wc_restore_locale' ) ) {
	wc_restore_locale();
}
?>
