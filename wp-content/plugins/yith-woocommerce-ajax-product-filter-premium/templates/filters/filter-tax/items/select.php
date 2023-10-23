<?php
/**
 * Filters Tax Item - Select option
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

<option class="filter-item select <?php echo $filter->is_term_active( $term ) ? 'active' : ''; ?> <?php echo esc_attr( $additional_classes ); ?>" value="<?php echo esc_attr( yith_wcan_esc_term_slug( $term->slug ) ); ?>" <?php echo selected( $filter->is_term_active( $term ) ); ?> <?php echo ! empty( $tooltip ) ? 'data-title="' . esc_attr( $tooltip ) . '"' : ''; ?> data-filter_url="<?php echo esc_url( $filter->get_term_url( $term ) ); ?>" data-count="<?php echo esc_attr( $filter->render_term_count( $term, $count ) ); ?>" >
	<?php echo esc_html( ! empty( $label ) ? $label : $term->name ); ?>
</option>

<?php
if ( isset( $children ) ) {
	echo $filter->render_hierarchy( $children ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
?>
