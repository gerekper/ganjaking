<?php
/**
 * The template for displaying the time element for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-time.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( isset( $class_label, $element_id, $get_default_value, $input_type, $name, $button_style, $fieldtype, $rules, $original_rules, $time_mask, $time_placeholder, $min_time, $max_time, $time_format, $custom_time_format, $time_theme, $time_theme_size, $time_theme_position, $translation_hour, $translation_minute, $translation_second ) ) :
	$class_label         = (string) $class_label;
	$element_id          = (string) $element_id;
	$get_default_value   = (string) $get_default_value;
	$input_type          = (string) $input_type;
	$name                = (string) $name;
	$button_style        = (string) $button_style;
	$fieldtype           = (string) $fieldtype;
	$rules               = (string) $rules;
	$original_rules      = (string) $original_rules;
	$time_mask           = (string) $time_mask;
	$time_placeholder    = (string) $time_placeholder;
	$min_time            = (string) $min_time;
	$max_time            = (string) $max_time;
	$time_format         = (string) $time_format;
	$custom_time_format  = (string) $custom_time_format;
	$time_theme          = (string) $time_theme;
	$time_theme_size     = (string) $time_theme_size;
	$time_theme_position = (string) $time_theme_position;
	$translation_hour    = (string) $translation_hour;
	$translation_minute  = (string) $translation_minute;
	$translation_second  = (string) $translation_second;
	?>
<li class="tmcp-field-wrap"><div class="tmcp-field-wrap-inner">
	<label for="<?php echo esc_attr( $element_id ); ?>" class="tc-col tm-epo-field-label tm-epo-timepicker-label-container<?php echo esc_attr( $class_label ); ?>">
	<?php
	$input_args = [
		'nodiv'      => 1,
		'default'    => $get_default_value,
		'type'       => 'input',
		'input_type' => $input_type,
		'tags'       => [
			'id'                       => $element_id,
			'name'                     => $name,
			'class'                    => $fieldtype . $button_style . ' tm-epo-field tmcp-time',
			'data-price'               => '',
			'data-rules'               => $rules,
			'data-original-rules'      => $original_rules,
			'data-mask'                => $time_mask,
			'data-mask-placeholder'    => $time_placeholder,
			'data-min-time'            => $min_time,
			'data-max-time'            => $max_time,
			'data-time-format'         => $time_format,
			'data-custom-time-format'  => $custom_time_format,
			'data-time-theme'          => $time_theme,
			'data-time-theme-size'     => $time_theme_size,
			'data-time-theme-position' => $time_theme_position,
			'data-tranlation-hour'     => $translation_hour,
			'data-tranlation-minute'   => $translation_minute,
			'data-tranlation-second'   => $translation_second,
			'inputmode'                => 'none',
		],
	];
	if ( 'time' === $input_type ) {
		unset( $input_args['tags']['data-mask'] );
		unset( $input_args['tags']['data-mask-placeholder'] );
	}
	if ( isset( $required ) && ! empty( $required ) ) {
		$input_args['tags']['required'] = true;
	}
	if ( ! empty( $tax_obj ) ) {
		$input_args['tags']['data-tax-obj'] = $tax_obj;
	}
	if ( THEMECOMPLETE_EPO()->associated_per_product_pricing === 0 ) {
		$input_args['tags']['data-no-price'] = true;
	}

	$input_args = apply_filters(
		'wc_element_input_args',
		$input_args,
		isset( $tm_element_settings ) && isset( $tm_element_settings['type'] ) ? $tm_element_settings['type'] : '',
		isset( $args ) ? $args : [],
	);

	THEMECOMPLETE_EPO_HTML()->create_field( $input_args, true );
	?>
	</label>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php'; ?>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity.php'; ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : [] ); ?>
</div></li>
	<?php
endif;
