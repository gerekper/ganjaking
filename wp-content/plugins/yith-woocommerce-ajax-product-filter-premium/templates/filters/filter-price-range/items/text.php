<?php
/**
 * Filters Price Range Item - Text
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Filters
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $preset YITH_WCAN_Preset
 * @var $filter YITH_WCAN_Filter_Price_Range
 * @var $range array
 * @var $item_id string
 * @var $item_name string
 * @var $show_count bool
 * @var $additional_classes string
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<li class="filter-item text <?php echo $filter->is_range_active( $range ) ? 'active' : ''; ?> <?php echo esc_attr( $additional_classes ); ?>">
	<a href="<?php echo esc_url( $filter->get_filter_url( $range ) ); ?>" <?php yith_wcan_add_rel_nofollow_to_url( true, true ); ?> role="button" data-range-min="<?php echo esc_attr( $range['min'] ); ?>" data-range-max="<?php echo $range['unlimited'] ? '' : esc_attr( $range['max'] ); ?>" class="price-range" >
		<?php echo $filter->render_formatted_range( $range ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo $filter->render_range_count( $range ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a>
</li>
