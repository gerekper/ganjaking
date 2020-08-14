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
 * Add to Quote button template
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 2.2.7
 * @author  YITH
 *
 * @var $product_id    int
 * @var $variations    string
 * @var $exists        bool
 * @var $template_part string
 * @var $rqa_url       string
 * @var $label_browse  string
 */

$data_variations = ( isset( $variations ) && ! empty( $variations ) ) ? ' data-variation="' . $variations . '" ' : '';

?>

<div
	class="yith-ywraq-add-to-quote add-to-quote-<?php echo esc_attr( $product_id ); ?>" <?php echo esc_attr( $data_variations ); ?>>
	<?php
	if ( ! is_product() && apply_filters( 'yith_ywraq_quantity_loop', false ) ) {
		woocommerce_quantity_input();//@phpcs:ignore
	}
	?>
	<div class="yith-ywraq-add-button <?php echo ( $exists ) ? 'hide' : 'show'; ?>"
		 style="display:<?php echo ( $exists ) ? 'none' : 'block'; ?>"
		 data-product_id="<?php echo esc_attr( $product_id ); ?>">
		<?php wc_get_template( 'add-to-quote-' . $template_part . '.php', $args, '', YITH_YWRAQ_TEMPLATE_PATH . '/' ); ?>
	</div>
	<div
		class="yith_ywraq_add_item_product-response-<?php echo esc_attr( $product_id ); ?> yith_ywraq_add_item_product_message hide hide-when-removed"
		style="display:none" data-product_id="<?php echo esc_attr( $product_id ); ?>"></div>
	<div
		class="yith_ywraq_add_item_response-<?php echo esc_attr( $product_id ); ?> yith_ywraq_add_item_response_message <?php echo esc_attr( ( ! $exists ) ? 'hide' : 'show' ); ?> hide-when-removed"
		data-product_id="<?php echo esc_attr( $product_id ); ?>"
		style="display:<?php echo ( ! $exists ) ? 'none' : 'block'; ?>"><?php echo esc_html( ywraq_get_label( 'already_in_quote' ) ); ?></div>
	<div
		class="yith_ywraq_add_item_browse-list-<?php echo esc_attr( $product_id ); ?> yith_ywraq_add_item_browse_message  <?php echo esc_attr( ( ! $exists ) ? 'hide' : 'show' ); ?> hide-when-removed"
		style="display:<?php echo esc_attr( ( ! $exists ) ? 'none' : 'block' ); ?>"
		data-product_id="<?php echo esc_attr( $product_id ); ?>"><a
			href="<?php echo esc_url( $rqa_url ); ?>"><?php echo esc_html( $label_browse ); ?></a></div>

</div>

<div class="clear"></div>
