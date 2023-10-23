<?php
/**
 * Preset filter - Term edit
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Admin
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $filter_id    int
 * @var $range        array
 * @var $range_id     int
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<div id="range_<?php echo esc_attr( $filter_id ); ?>_<?php echo esc_attr( $range_id ); ?>" class="range-box" data-range_id="<?php echo esc_attr( $range_id ); ?>">

	<a href="#" role="button" class="range-remove">&times;</a>

	<p class="yith-plugin-fw-field-wrapper yith-plugin-fw-text-field-wrapper min">
		<label for="filters_<?php echo esc_attr( $filter_id ); ?>_price_ranges_<?php echo esc_attr( $range_id ); ?>_min"><?php echo esc_html_x( 'Min', '[Admin] Range min (preset edit page)', 'yith-woocommerce-ajax-navigation' ); ?></label>
		<input type="number" name="filters[<?php echo esc_attr( $filter_id ); ?>][price_ranges][<?php echo esc_attr( $range_id ); ?>][min]" id="filters_<?php echo esc_attr( $filter_id ); ?>_price_ranges_<?php echo esc_attr( $range_id ); ?>_min" value="<?php echo ! empty( $range['min'] ) ? esc_attr( $range['min'] ) : '0'; ?>"/>
	</p>

	<div class="yith-plugin-fw-field-wrapper yith-plugin-fw-onoff-field-wrapper unlimited">
		<label for="filters_<?php echo esc_attr( $filter_id ); ?>_price_ranges_<?php echo esc_attr( $range_id ); ?>_unlimited"><?php echo esc_html_x( 'Show "& Above" in last range', '[Admin] Range option that allows to skip max selection (preset edit page)', 'yith-woocommerce-ajax-navigation' ); ?></label>
		<?php
		yith_plugin_fw_get_field(
			array(
				'id'    => "filters_{$filter_id}_price_ranges_{$range_id}_unlimited",
				'name'  => "filters[$filter_id][price_ranges][{$range_id}][unlimited]",
				'type'  => 'onoff',
				'value' => isset( $range['unlimited'] ) ? $range['unlimited'] : false,
			),
			true
		);
		?>
	</div>

	<p class="yith-plugin-fw-field-wrapper yith-plugin-fw-text-field-wrapper max">
		<label for="filters_<?php echo esc_attr( $filter_id ); ?>_price_ranges_<?php echo esc_attr( $range_id ); ?>_max"><?php echo esc_html_x( 'Max', '[Admin] Range max (preset edit page)', 'yith-woocommerce-ajax-navigation' ); ?></label>
		<input type="number" name="filters[<?php echo esc_attr( $filter_id ); ?>][price_ranges][<?php echo esc_attr( $range_id ); ?>][max]" id="filters_<?php echo esc_attr( $filter_id ); ?>_price_ranges_<?php echo esc_attr( $range_id ); ?>_max" value="<?php echo ! empty( $range['max'] ) ? esc_attr( $range['max'] ) : '0'; ?>"/>
	</p>

</div>
