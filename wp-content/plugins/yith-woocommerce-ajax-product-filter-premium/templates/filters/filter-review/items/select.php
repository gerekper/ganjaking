<?php
/**
 * Filters Review Item - Select option
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
 * @var $show_count bool
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<option class="filter-item select <?php echo esc_attr( $rate['additional_classes'] ); ?>" value="<?php echo esc_attr( $rate['rate'] ); ?>" <?php selected( $filter->is_review_rate_active( $rate['rate'] ) ); ?> data-template="<?php echo esc_attr( yith_wcan_get_rating_html( $rate['rate'] ) ); ?>" data-count="<?php echo esc_attr( $filter->render_review_rate_count( $rate ) ); ?>" >
	<?php echo esc_html( yith_wcan_get_rating_label( $rate['rate'] ) ); ?>
</option>
