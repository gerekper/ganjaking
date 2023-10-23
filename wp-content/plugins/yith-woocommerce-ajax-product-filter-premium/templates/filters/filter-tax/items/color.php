<?php
/**
 * Filters Tax Item - Color
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Filters
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $preset YITH_WCAN_Preset
 * @var $filter YITH_WCAN_Filter_Tax
 * @var $term WP_Term
 * @var $item_id string
 * @var $label string
 * @var $tooltip string
 * @var $show_count bool
 * @var $additional_classes string
 * @var $mode string
 * @var $color_1 string
 * @var $color_2 string
 * @var $image int
 * @var $children array
 * @var $count int
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<li class="filter-item color <?php echo $filter->is_term_active( $term ) ? 'active' : ''; ?> <?php echo esc_attr( $additional_classes ); ?>">
	<a href="<?php echo esc_url( $filter->get_term_url( $term ) ); ?>" <?php yith_wcan_add_rel_nofollow_to_url( true, true ); ?> role="button" data-term-id="<?php echo esc_attr( $term->term_id ); ?>" data-term-slug="<?php echo esc_attr( yith_wcan_esc_term_slug( $term->slug ) ); ?>" <?php echo ! empty( $tooltip ) ? 'data-title="' . esc_attr( $tooltip ) . '"' : ''; ?>>
		<?php if ( 'image' === $mode && ! empty( $image ) ) : ?>
			<span class="term-image">
				<?php echo wp_get_attachment_image( $image, apply_filters( 'yith_wcan_filter_tax_color_image_size', 'thumbnail', $filter ), false, apply_filters( 'yith_wcan_filter_tax_color_image_attr', array(), $filter ) ); ?>
			</span>
		<?php endif; ?>

		<?php if ( 'color' === $mode && ! empty( $color_1 ) ) : ?>
			<span class="term-color <?php echo ! empty( $color_2 ) ? 'bi-color' : 'single-color'; ?>">
				<span class="color-swatch" style="background-color: <?php echo esc_attr( $color_1 ); ?>; border-color: <?php echo esc_attr( ! empty( $color_2 ) ? $color_2 : $color_1 ); ?>"></span>
			</span>
		<?php endif; ?>

		<?php if ( ! empty( $label ) ) : ?>
			<span class="term-label">
				<?php echo esc_html( $label ); ?>
				<?php echo $filter->render_term_count( $term, $count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
		<?php endif; ?>
	</a>

	<?php
	if ( isset( $children ) ) {
		echo $filter->render_hierarchy( $children ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
</li>
