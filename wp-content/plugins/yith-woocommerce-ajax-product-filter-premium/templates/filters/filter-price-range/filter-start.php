<?php
/**
 * Filters Price Range Start
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Filters
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $preset    YITH_WCAN_Preset
 * @var $filter    YITH_WCAN_Filter_Price_Range
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php if ( 'select' === $filter->get_filter_design() ) : ?>
	<select class="filter-items filter-dropdown <?php echo esc_attr( $filter->get_items_container_classes() ); ?>" name="filter[<?php echo esc_attr( $preset->get_id() ); ?>][<?php echo esc_attr( $filter->get_id() ); ?>]" id="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>" <?php echo $filter->is_multiple_allowed() ? 'multiple="multiple"' : ''; ?> >
		<?php if ( ! $filter->is_multiple_allowed() ) : ?>
			<option class="filter-item select" value=""><?php echo esc_html_x( 'Any price', '[FRONTEND] General option for reviews dropdown', 'yith-woocommerce-ajax-navigation' ); ?></option>
		<?php endif; ?>
<?php else : ?>
	<ul class="filter-items <?php echo esc_attr( $filter->get_items_container_classes() ); ?>">
<?php endif; ?>
