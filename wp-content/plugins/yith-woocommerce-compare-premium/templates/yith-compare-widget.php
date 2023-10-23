<?php
/**
 * YITH Woocommerce Compare widget template
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Compare
 * @version 2.5.0
 */

defined( 'YITH_WOOCOMPARE' ) || exit; // Exit if accessed directly.

global $yith_woocompare;

?>

<div class="yith-woocompare-widget-content" data-lang="<?php echo esc_attr( $lang ); ?>" <?php echo ! empty( $hide_empty ) ? 'data-hide="1"' : ''; ?>>
	<?php if ( ! empty( $products_list ) ) : ?>
		<ul class="products-list">
			<?php
			foreach ( $products_list as $product_id ) :
				$product = wc_get_product( $product_id );

				if ( ! $product ) {
					continue;
				}

				wc_get_template(
					'yith-compare-widget-item.php',
					array(
						'product'    => $product,
						'product_id' => $product_id,
					),
					'',
					YITH_WOOCOMPARE_TEMPLATE_PATH . '/'
				);

			endforeach;
			?>
		</ul>

		<a href="<?php echo esc_url( $remove_url ); ?>" data-product_id="all" class="clear-all" rel="nofollow"><?php esc_html_e( 'Clear all', 'yith-woocommerce-compare' ); ?></a>
		<?php
		/**
		 * APPLY_FILTERS: yith_woocompare_widget_view_table_button
		 *
		 * Filters the text of the button to view the comparison table in the widget.
		 *
		 * @param string $text Button text.
		 *
		 * @return string
		 */
		?>
		<a href="<?php echo esc_url( $view_url ); ?>" class="compare-widget button" rel="nofollow"><?php echo esc_html( apply_filters( 'yith_woocompare_widget_view_table_button', __( 'Compare', 'yith-woocommerce-compare' ) ) ); ?></a>
	<?php else : ?>
		<span class="list_empty"><?php echo esc_html__( 'No products to compare', 'yith-woocommerce-compare' ); ?></span>
	<?php endif; ?>
</div>
