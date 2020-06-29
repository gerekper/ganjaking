<?php
/**
 * Wishlist page template
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist              \YITH_WCWL_Wishlist Current wishlist
 * @var $wishlist_items        array Array of items to show for current page
 * @var $page_title            string Page title
 * @var $show_price            bool Whether to show price column
 * @var $show_dateadded        bool Whether to show item date of addition
 * @var $show_stock_status     bool Whether to show product stock status
 * @var $show_price_variations bool Whether to show price variation over time
 * @var $show_variation        bool Whether to show variation attributes when possible
 * @var $show_quantity         bool Whether to show input quantity or not
 * @var $css_url               string Url to css file
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> >

<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<title><?php echo esc_html( $page_title ); ?></title>

	<link rel="stylesheet" href="<?php echo esc_url( $css_url ); ?>"/>
</head>

<body>
<div class="heading">
	<div id="logo">
		<h1><?php echo esc_html( get_option( 'blogname' ) ); ?></h1>
	</div>
	<div id="tagline"><?php echo esc_html( get_option( 'blogdescription' ) ); ?></div>
</div>

<!-- TITLE -->
<?php
do_action( 'yith_wcwl_pdf_before_wishlist_title', $wishlist );

if ( ! empty( $page_title ) ) :
	?>
	<div class="wishlist-title">
		<?php echo apply_filters( 'yith_wcwl_wishlist_title', '<h2>' . esc_html( $page_title ) . '</h2>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
<?php
endif;

do_action( 'yith_wcwl_pdf_before_wishlist', $wishlist );
?>

<!-- WISHLIST TABLE -->
<table class="shop_table cart wishlist_table">

	<?php $column_count = 2; ?>

	<thead>
	<tr>

		<th class="product-thumbnail"></th>

		<th class="product-name">
			<span class="nobr">
				<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_view_name_heading', __( 'Product Name', 'yith-woocommerce-wishlist' ) ) ); ?>
			</span>
		</th>

		<?php
		if ( $show_price || $show_price_variations ) :
			$column_count ++;
		?>
			<th class="product-price">
				<span class="nobr">
					<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_view_price_heading', __( 'Unit Price', 'yith-woocommerce-wishlist' ) ) ); ?>
				</span>
			</th>
		<?php endif; ?>

		<?php
		if ( $show_quantity ) :
			$column_count ++;
		?>
			<th class="product-quantity">
				<span class="nobr">
					<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_view_quantity_heading', __( 'Quantity', 'yith-woocommerce-wishlist' ) ) ); ?>
				</span>
			</th>
		<?php endif; ?>

		<?php
		if ( $show_stock_status ) :
			$column_count ++;
		?>
			<th class="product-stock-status">
				<span class="nobr">
					<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_view_stock_heading', __( 'Stock status', 'yith-woocommerce-wishlist' ) ) ); ?>
				</span>
			</th>
		<?php endif; ?>

		<?php
		if ( $show_dateadded ) :
			$column_count ++;
		?>
			<th class="product-add-to-cart"></th>
		<?php endif; ?>
	</tr>
	</thead>

	<tbody>
	<?php
	if ( count( $wishlist_items ) > 0 ) :
		foreach ( $wishlist_items as $item ) :
			/**
			 * @var $item \YITH_WCWL_Wishlist_Item
			 */
			global $product;

			$product      = $item->get_product();
			$availability = $product->get_availability();
			$stock_status = isset( $availability['class'] ) ? $availability['class'] : false;

			if ( $product && $product->exists() ) :
				?>
				<tr id="yith-wcwl-row-<?php echo esc_attr( $item->get_product_id() ); ?>" data-row-id="<?php echo esc_attr( $item->get_product_id() ); ?>">

					<td class="product-thumbnail">
						<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item->get_product_id() ) ) ); ?>">
							<?php echo YITH_WCWL_Frontend()->get_product_image_with_path( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</td>

					<td class="product-name">
						<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item->get_product_id() ) ) ); ?>"><?php echo esc_html( apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ); ?></a>

						<?php
						if ( $show_variation && $product->is_type( 'variation' ) ) {
							/**
							 * @var $product \WC_Product_Variation
							 */
							echo wc_get_formatted_variation( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>

						<?php do_action( 'yith_wcwl_table_after_product_name', $item ); ?>
					</td>

					<?php if ( $show_price || $show_price_variations ) : ?>
						<td class="product-price">
							<?php
							if ( $show_price ) {
								echo $item->get_formatted_product_price(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}

							if ( $show_price_variations ) {
								echo $item->get_price_variation(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</td>
					<?php endif ?>

					<?php if ( $show_quantity ) : ?>
						<td class="product-quantity">
							<?php echo esc_html( $item->get_quantity() ); ?>
						</td>
					<?php endif; ?>

					<?php if ( $show_stock_status ) : ?>
						<td class="product-stock-status">
							<?php echo $stock_status === 'out-of-stock' ? '<span class="wishlist-out-of-stock">' . esc_html__( 'Out of stock', 'yith-woocommerce-wishlist' ) . '</span>' : '<span class="wishlist-in-stock">' . esc_html__( 'In Stock', 'yith-woocommerce-wishlist' ) . '</span>'; ?>
						</td>
					<?php endif ?>

					<?php if ( $show_dateadded ): ?>
						<td class="product-add-to-cart">
							<!-- Date added -->
							<?php
							if ( $show_dateadded && isset( $item['dateadded'] ) ):
								echo '<span class="dateadded">' . esc_html( sprintf( __( 'Added on: %s', 'yith-woocommerce-wishlist' ), date_i18n( get_option( 'date_format' ), strtotime( $item['dateadded'] ) ) ) ) . '</span>';
							endif;
							?>
						</td>
					<?php endif; ?>
				</tr>
			<?php
			endif;
		endforeach;
	else: ?>
		<tr>
			<td colspan="<?php echo esc_attr( $column_count ) ?>" class="wishlist-empty"><?php echo esc_html( apply_filters( 'yith_wcwl_no_product_to_remove_message', __( 'No products added to the wishlist', 'yith-woocommerce-wishlist' ) ) ); ?></td>
		</tr>
	<?php endif; ?>
	</tbody>

</table>

<?php do_action( 'yith_wcwl_pdf_after_wishlist', $wishlist ); ?>
</body>
</html>
