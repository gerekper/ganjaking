<?php
/**
 * Filters Price Range Item - Select option
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
 * @var $show_count bool
 * @var $additional_classes string
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<option class="filter-item select <?php echo esc_attr( $additional_classes ); ?>" value="<?php echo esc_attr( $formatted_range ); ?>" <?php selected( $filter->is_range_active( $range ) ); ?>data-count="<?php echo esc_attr( $filter->render_range_count( $range ) ); ?>" >
	<?php echo $filter->render_formatted_range( $range ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</option>
