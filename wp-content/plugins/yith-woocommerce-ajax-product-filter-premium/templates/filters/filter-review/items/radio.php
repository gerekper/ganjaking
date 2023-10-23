<?php
/**
 * Filters Review Item - Radio
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Filters
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $preset YITH_WCAN_Preset
 * @var $filter YITH_WCAN_Filter_Review
 * @var $rate array
 * @var $item_id string
 * @var $item_name string
 * @var $show_count bool
 * @var $additional_classes string
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<li class="filter-item radio <?php echo $filter->is_review_rate_active( $rate['rate'] ) ? 'active' : ''; ?> <?php echo esc_attr( $additional_classes ); ?>">
	<label for="<?php echo esc_attr( $item_id ); ?>">
		<input type="radio" id="<?php echo esc_attr( $item_id ); ?>" name="<?php echo esc_attr( $item_name ); ?>" value="<?php echo esc_attr( $rate['rate'] ); ?>" <?php checked( $filter->is_review_rate_active( $rate['rate'] ) ); ?> />

		<a href="<?php echo esc_url( $filter->get_filter_url( $rate['rate'] ) ); ?>" <?php yith_wcan_add_rel_nofollow_to_url( true, true ); ?> class="term-label" >
			<?php echo yith_wcan_get_rating_html( $rate['rate'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $filter->render_review_rate_count( $rate ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
	</label>
</li>
