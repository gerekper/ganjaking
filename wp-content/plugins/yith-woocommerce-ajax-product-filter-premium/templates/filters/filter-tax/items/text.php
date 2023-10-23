<?php
/**
 * Filters Tax Item - Text
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
 * @var $children array
 * @var $count int
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<li class="filter-item text <?php echo $filter->is_term_active( $term ) ? 'active' : ''; ?> <?php echo esc_attr( $additional_classes ); ?>">
	<a href="<?php echo esc_url( $filter->get_term_url( $term ) ); ?>" <?php yith_wcan_add_rel_nofollow_to_url( true, true ); ?> role="button" data-term-id="<?php echo esc_attr( $term->term_id ); ?>" data-term-slug="<?php echo esc_attr( yith_wcan_esc_term_slug( $term->slug ) ); ?>" class="term-label" <?php echo ! empty( $tooltip ) ? 'data-title="' . esc_attr( $tooltip ) . '"' : ''; ?>>
		<?php echo esc_html( ! empty( $label ) ? $label : $term->name ); ?>
		<?php echo $filter->render_term_count( $term, $count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a>

	<?php
	if ( isset( $children ) ) {
		echo $filter->render_hierarchy( $children ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
</li>
