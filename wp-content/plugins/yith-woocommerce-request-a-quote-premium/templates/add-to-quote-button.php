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
 * @var $wpnonce    string
 * @var $label      string
 */


?>

<a href="#" class="<?php echo $class; ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>"
   data-wp_nonce="<?php echo esc_attr( $wpnonce ); ?>">
	<?php echo esc_html( $label ); ?>
</a>
