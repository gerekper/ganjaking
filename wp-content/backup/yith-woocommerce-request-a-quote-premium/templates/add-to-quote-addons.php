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
 * @var $product_id int
 * @var $variations string
 * @var $label      string
 * @var $exists     bool
 */

$data_variations = ( isset( $variations ) && ! empty( $variations ) ) ? ' data-variation="' . $variations . '" ' : '';

?>

<div
	class="yith-ywraq-add-to-quote add-to-quote-addons-<?php echo esc_attr( $product_id ); ?>" <?php echo esc_attr( $data_variations ); ?>>
	<a class="add-request-quote-button-addons button" style="display:<?php echo ( $exists ) ? 'none' : 'block'; ?>"
	   href="<?php echo esc_url( get_the_permalink( $product_id ) ); ?>">
		<?php echo esc_html( $label ); ?>
	</a>
</div>

<div class="clear"></div>
