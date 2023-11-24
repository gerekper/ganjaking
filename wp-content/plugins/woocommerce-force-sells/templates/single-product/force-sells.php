<?php
/**
 * Template: Force Sells single product page.
 *
 * @package WC_Force_Sells/Templates
 * @version 1.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var WC_Product[] $force_sells The force sells products.
 * @var bool         $show_price  Whether to show the price.
 */
?>

<div class="clear"></div>
<div class="wc-force-sells">
	<p> <?php echo esc_html__( 'This will also add the following products to your cart:', 'woocommerce-force-sells' ); ?> </p>
	<ul>
		<?php foreach ( $force_sells as $force_sell ) : ?>
			<li>
				<?php
				echo esc_html( $force_sell->get_title() );
				if ( $show_price ) :
					echo ' - ' . wp_kses_post( wc_price( $force_sell->get_price() ) );
				endif;
				?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
