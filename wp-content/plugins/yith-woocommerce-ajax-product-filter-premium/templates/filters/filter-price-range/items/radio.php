<?php
/**
 * Filters Price Range Item - Radio
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
 * @var $formatted_range string
 * @var $item_id string
 * @var $item_name string
 * @var $show_count bool
 * @var $additional_classes string
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<li class="filter-item radio <?php echo $filter->is_range_active( $range ) ? 'active' : ''; ?> <?php echo esc_attr( $additional_classes ); ?>">
	<label for="<?php echo esc_attr( $item_id ); ?>" data-range-min="<?php echo esc_attr( $range['min'] ); ?>" data-range-max="<?php echo $range['unlimited'] ? '' : esc_attr( $range['max'] ); ?>">
		<input type="radio" id="<?php echo esc_attr( $item_id ); ?>" name="<?php echo esc_attr( $item_name ); ?>" value="<?php echo esc_attr( $formatted_range ); ?>" <?php checked( $filter->is_range_active( $range ) ); ?> />

		<a href="<?php echo esc_url( $filter->get_filter_url( $range ) ); ?>" <?php yith_wcan_add_rel_nofollow_to_url( true, true ); ?> role="button" class="price-range" >
			<?php echo $filter->render_formatted_range( $range ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $filter->render_range_count( $range ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
	</label>
</li>
