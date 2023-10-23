<?php
/**
 * Price Range template
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
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>


<div
	class="yith-wcan-filter <?php echo esc_attr( $filter->get_additional_classes() ); ?>"
	id="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>"
	data-filter-type="<?php echo esc_attr( $filter->get_type() ); ?>"
	data-filter-id="<?php echo esc_attr( $filter->get_id() ); ?>"
	data-multiple="<?php echo esc_attr( $filter->is_multiple_allowed() ? 'yes' : 'no' ); ?>"
>
	<?php echo $filter->render_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<div class="filter-content">
		<?php echo $filter->render_start(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<?php
		foreach ( $filter->get_formatted_ranges() as $range ) :
			echo $filter->render_item( $range ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		endforeach;
		?>

		<?php echo $filter->render_end(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</div>
