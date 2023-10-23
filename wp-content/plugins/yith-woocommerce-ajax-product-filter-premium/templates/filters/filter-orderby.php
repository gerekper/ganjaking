<?php
/**
 * OrderBy template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Filters
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $preset YITH_WCAN_Preset
 * @var $filter YITH_WCAN_Filter_Orderby
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php if ( $filter->get_order_options() ) : ?>
<div class="yith-wcan-filter <?php echo esc_attr( $filter->get_additional_classes() ); ?>" id="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>" data-filter-type="<?php echo esc_attr( $filter->get_type() ); ?>" data-filter-id="<?php echo esc_attr( $filter->get_id() ); ?>">
	<?php echo $filter->render_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<div class="filter-content">
		<select class="filter-order-by filter-dropdown filter-items <?php echo esc_attr( $filter->get_items_container_classes() ); ?>" name="filter[<?php echo esc_attr( $preset->get_id() ); ?>][<?php echo esc_attr( $filter->get_id() ); ?>]" id="filter_<?php echo esc_attr( $preset->get_id() ); ?>_<?php echo esc_attr( $filter->get_id() ); ?>">
			<?php foreach ( $filter->get_formatted_order_options() as $sorting_order => $label ) : ?>
				<option class="filter-item" value="<?php echo esc_attr( $sorting_order ); ?>" <?php selected( $filter->is_order_active( $sorting_order ) ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
</div>
<?php endif; ?>
