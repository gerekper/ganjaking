<?php
/**
 * Request A Quote pages template; load template parts
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 2.2.7
 * @author  YITH
 *
 * @var $template_part string
 * @var $raq_content
 */

/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

global $wpdb, $woocommerce;

function_exists( 'wc_nocache_headers' ) && wc_nocache_headers();
$quote_wrapper_class = get_option( 'ywraq_page_list_layout_template', '' );
$quote_wrapper_class = count( $raq_content ) === 0 ? '' : $quote_wrapper_class;
?>

<div class="woocommerce ywraq-wrapper">

	<?php
	if ( ! apply_filters( 'yith_ywraq_before_print_raq_page', true ) ) :
		?>
		<div
			id="yith-ywraq-message"><?php echo wp_kses_post( apply_filters( 'yith_ywraq_raq_page_deniend_access', __( 'You do not have access to this page', 'yith-woocommerce-request-a-quote' ) ) ); ?></div>
		<?php
		return;
	endif;

	if ( function_exists( 'wc_print_notices' ) ) {
		if ( defined( 'YWMMQ_PREMIUM' ) && YWMMQ_PREMIUM ) {
			wc_print_notices();
		}

		$args['notices'] = yith_ywraq_check_notices();
		yith_ywraq_print_notices();
	}

	if ( ! isset( $_REQUEST['hidem'] ) ) :
		?>
		<div id="yith-ywraq-message"><?php do_action( 'ywraq_raq_message' ); ?></div>
		<?php
		if ( isset( $_GET['raq_nonce'] ) ) {
			return;
		}
		?>

		<?php
		if ( get_option( 'ywraq_show_return_to_shop' ) === 'yes' && count( $raq_content ) !== 0 ) :
			$shop_url             = apply_filters( 'yith_ywraq_return_to_shop_url', get_option( 'ywraq_return_to_shop_url' ) );
			$label_return_to_shop = apply_filters( 'yith_ywraq_return_to_shop_label', get_option( 'ywraq_return_to_shop_label' ) );
			?>
			<div class="yith-ywraq-before-table"><a class="button wc-backward"
			                                        href="<?php echo esc_url( apply_filters( 'yith_ywraq_return_to_shop_url', $shop_url ) ); ?>"><?php echo esc_html( $label_return_to_shop ); ?></a>
			</div>
		<?php endif ?>
		<div class="ywraq-form-table-wrapper <?php echo esc_attr( $quote_wrapper_class ); ?>">
			<?php wc_get_template( 'request-quote-' . $template_part . '.php', $args, '', YITH_YWRAQ_TEMPLATE_PATH . '/' ); ?>

			<?php if ( 'yes' == $args['show_form'] && count( $raq_content ) != 0 ) : ?>
				<?php if ( ! defined( 'YITH_YWRAQ_PREMIUM' ) ) : ?>
					<?php wc_get_template( 'request-quote-form.php', $args, '', YITH_YWRAQ_TEMPLATE_PATH . '/' ); ?>
				<?php else : ?>
					<?php YITH_Request_Quote_Premium()->get_inquiry_form( $args ); ?>
				<?php endif ?>
			<?php endif ?>
		</div>
	<?php endif ?>
</div>
