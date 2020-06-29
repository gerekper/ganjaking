<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * @version     3.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}

global $porto_layout, $porto_settings, $porto_product_layout, $product;

$post_class  = join( ' ', wc_get_product_class( '', $product ) );
$post_class .= ' product-layout-' . esc_attr( $porto_product_layout );

$skeleton_lazyload = apply_filters( 'porto_skeleton_lazyload', ! empty( $porto_settings['show-skeleton-screen'] ) && in_array( 'product', $porto_settings['show-skeleton-screen'] ) && ! porto_is_ajax(), 'single-product' );
if ( $skeleton_lazyload && ( ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) || porto_is_elementor_preview() ) ) {
	$skeleton_lazyload = false;
}
?>

<div id="product-<?php the_ID(); ?>" class="<?php echo esc_attr( $post_class ), ! $skeleton_lazyload ? '' : ' skeleton-loading'; ?>">
<?php
if ( $skeleton_lazyload ) {
	ob_start();
	$porto_settings['skeleton_lazyload'] = true;
}
?>

<?php
if ( 'builder' == $porto_product_layout ) :
	if ( isset( $porto_settings['product-single-content-builder'] ) && $porto_settings['product-single-content-builder'] ) {
		global $wpdb;

		echo do_shortcode( '[porto_block name="' . $porto_settings['product-single-content-builder'] . '" post_type="product_layout"]' );

		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			the_content();
		}
	} else {
		echo '<p class="lead">Please create a product layout in <strong>Product Layouts/Add New</strong>. You need to select product layout in Theme Options -> WooCommerce -> Single Product -> Custom Product Layout.</p>';
	}
else :
	$summary_before_classes = array( 'summary-before' );
	$summary_classes        = array( 'summary', 'entry-summary' );
	$summary2_classes       = false;
	if ( 'full_width' == $porto_product_layout ) {
		$summary_before_classes[] = 'col-lg-6';
		$summary_classes[]        = 'col-lg-6';
	} elseif ( 'transparent' == $porto_product_layout ) {
		$summary_before_classes[] = 'col-lg-7';
		$summary_classes[]        = 'col-lg-5';
	} elseif ( 'grid' == $porto_product_layout ) {
		$summary_before_classes[] = 'col-lg-8';
		$summary_classes[]        = 'col-lg-4';
	} elseif ( 'sticky_both_info' == $porto_product_layout || 'centered_vertical_zoom' == $porto_product_layout ) {
		$summary_before_classes[] = 'col-lg-6';
		$summary_classes[]        = 'col-lg-3';
		$summary2_classes         = $summary_classes;
	} elseif ( 'extended' != $porto_product_layout ) {
		if ( 'fullwidth' == $porto_layout || 'widewidth' == $porto_layout ) {
			$summary_before_classes[] = 'col-md-5';
			$summary_classes[]        = 'col-md-7';
		} else {
			$summary_before_classes[] = 'col-md-6';
			$summary_classes[]        = 'col-md-6';
		}
	}
	?>
	<div class="product-summary-wrap">
	<?php if ( 'extended' !== $porto_product_layout ) : ?>
		<?php if ( 'sticky_both_info' === $porto_product_layout ) : ?>
			<div class="porto-woocommerce-summary-before">
				<?php
					/**
					 * porto_woocommerce_before_single_product_summary hook.
					 */
					do_action( 'porto_woocommerce_before_single_product_summary' );
				?>
			</div>
		<?php endif; ?>
		<div class="row">
	<?php endif; ?>
			<div class="<?php echo implode( ' ', $summary_before_classes ); ?>">
			<?php if ( 'full_width' == $porto_product_layout ) : ?>
				<div class="product-media" data-plugin-sticky data-plugin-options="<?php echo esc_attr( '{"autoInit": true, "minWidth": 991, "containerSelector": ".product-summary-wrap","paddingOffsetTop":0}' ); ?>">
			<?php endif; ?>
				<?php
					/**
					 * woocommerce_before_single_product_summary hook.
					 *
					 * @hooked woocommerce_show_product_sale_flash - 10
					 * @hooked woocommerce_show_product_images - 20
					 */
					do_action( 'woocommerce_before_single_product_summary' );
				?>
			<?php if ( 'full_width' == $porto_product_layout ) : ?>
				</div>
			<?php endif; ?>
			</div>

			<div class="<?php echo implode( ' ', $summary_classes ); ?>">
			<?php
			if ( 'sticky_info' == $porto_product_layout || 'sticky_both_info' == $porto_product_layout ) :
					$sticky_min_width = 'sticky_info' == $porto_product_layout ? '767' : '991';
				?>
				<div data-plugin-sticky data-plugin-options="<?php echo esc_attr( '{"autoInit": true, "minWidth": ' . $sticky_min_width . ', "containerSelector": ".entry-summary","paddingOffsetTop":' . ( $porto_settings['grid-gutter-width'] - 5 ) . '}' ); ?>">
			<?php endif; ?>
				<?php
					/**
					 * Hook: woocommerce_single_product_summary.
					 *
					 * @hooked woocommerce_template_single_title - 5
					 * @hooked woocommerce_template_single_rating - 10
					 * @hooked woocommerce_template_single_price - 10
					 * @hooked woocommerce_template_single_excerpt - 20
					 * @hooked woocommerce_template_single_add_to_cart - 30
					 * @hooked woocommerce_template_single_meta - 40
					 * @hooked woocommerce_template_single_sharing - 50
					 */

					do_action( 'woocommerce_single_product_summary' );
				?>
			<?php if ( 'sticky_info' == $porto_product_layout || 'sticky_both_info' == $porto_product_layout ) : ?>
				</div>
			<?php endif; ?>
			</div>

		<?php if ( isset( $summary2_classes ) && $summary2_classes ) : ?>
			<div class="<?php echo implode( ' ', $summary2_classes ); ?>">
			<?php if ( 'sticky_both_info' == $porto_product_layout ) : ?>
				<div data-plugin-sticky data-plugin-options="<?php echo esc_attr( '{"autoInit": true, "minWidth": 991, "containerSelector": ".entry-summary","paddingOffsetTop":' . ( $porto_settings['grid-gutter-width'] - 5 ) . '}' ); ?>">
			<?php endif; ?>
				<?php
					/**
					* porto_woocommerce_single_product_summary2 hook.
					*/

					do_action( 'porto_woocommerce_single_product_summary2' );
				?>
			<?php if ( 'sticky_both_info' == $porto_product_layout ) : ?>
				</div>
			<?php endif; ?>
			</div>
		<?php endif; ?>
	<?php if ( 'extended' !== $porto_product_layout ) : ?>
		</div><!-- .summary -->
	<?php endif; ?>
	</div>

	<?php
		/**
		 * woocommerce_after_single_product_summary hook.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>

<?php endif; ?>

<?php
if ( $skeleton_lazyload ) :
	$post_content = ob_get_clean();
	?>
	<script type="text/template"><?php echo json_encode( $post_content ); ?></script>
<?php endif; ?>
</div><!-- #product-<?php the_ID(); ?> -->
<?php if ( $skeleton_lazyload ) : ?>
	<?php
	if ( ! isset( $summary_before_classes ) ) {
		$summary_before_classes = array( 'summary-before', 'col-md-5' );
		$summary_classes        = array( 'summary', 'entry-summary', 'col-md-7' );
	}
	if ( 1 === count( $summary_before_classes ) ) {
		$summary_before_classes[] = 'col-12';
	}
	if ( 2 === count( $summary_classes ) ) {
		$summary_classes[] = 'col-12';
	}
	?>
<div class="<?php echo esc_attr( $post_class ); ?> skeleton-body">
	<div class="row">
		<div class="<?php echo implode( ' ', $summary_before_classes ); ?>"></div>
		<div class="<?php echo implode( ' ', $summary_classes ); ?>"></div>
	<?php if ( isset( $summary2_classes ) && $summary2_classes ) : ?>
		<div class="<?php echo implode( ' ', $summary2_classes ); ?>"></div>
	<?php endif; ?>
		<div class="tab-content col-lg-12"></div>
	</div>
</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_single_product' ); ?>
