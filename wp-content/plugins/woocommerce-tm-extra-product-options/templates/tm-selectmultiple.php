<?php
/**
 * The template for displaying the select element for the builder/local modes
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-select.php
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
if ( isset( $class_label, $element_id, $fieldtype, $name, $options ) ) :
	$class_label = (string) $class_label;
	$element_id  = (string) $element_id;
	$fieldtype   = (string) $fieldtype;
	$name        = (string) $name;
	?>
<li class="tmcp-field-wrap"><div class="tmcp-field-wrap-inner">
	<label class="tc-col tm-epo-field-label<?php echo esc_attr( $class_label ); ?>" for="<?php echo esc_attr( $element_id ); ?>">
	<?php
	$select_array = [
		'class'    => $fieldtype . ' tm-epo-field tmcp-selectmultiple',
		'id'       => $element_id,
		'name'     => $name,
		'multiple' => 'multiple',
		'atts'     => [
			'data-price'          => '',
			'data-rules'          => '',
			'data-original-rules' => '',
		],
	];

	if ( isset( $required ) && ! empty( $required ) ) {
		$select_array['required'] = true;
	}
	if ( ! empty( $tax_obj ) ) {
		$select_array['atts']['data-tax-obj'] = $tax_obj;
	}
	if ( ! empty( $changes_product_image ) ) {
		$select_array['atts']['data-changes-product-image'] = $changes_product_image;
	}
	if ( isset( $element_data_attr ) && is_array( $element_data_attr ) ) {
		$select_array['atts'] = array_merge( $select_array['atts'], $element_data_attr );
	}
	if ( THEMECOMPLETE_EPO()->associated_per_product_pricing === 0 ) {
		$select_array['atts']['data-no-price'] = true;
	}

	$select_options = [];
	foreach ( $options as $option ) {
		$current_option = [
			'text'  => $option['text'],
			'value' => $option['value_to_show'],
			'atta'  => [],
		];

		if ( isset( $option['selected'] ) && isset( $option['current'] ) ) {
			$current_option['selected'] = $option['selected'];
		}
		if ( isset( $option['css_class'] ) ) {
			$current_option['class'] = 'tc-multiple-option tc-select-option' . $option['css_class'];
		}
		if ( isset( $option['data_price'] ) ) {
			$current_option['atts']['data-price'] = $option['data_price'];
		}
		if ( isset( $option['tm_tooltip_html'] ) && ! empty( $option['tm_tooltip_html'] ) ) {
			$current_option['atts']['data-tm-tooltip-html'] = $option['tm_tooltip_html'];
		}
		if ( isset( $option['image_variations'] ) ) {
			$current_option['atts']['data-image-variations'] = $option['image_variations'];
		}
		if ( isset( $option['data_rules'] ) ) {
			$current_option['atts']['data-rules'] = $option['data_rules'];
		}
		if ( isset( $option['data_original_rules'] ) ) {
			$current_option['atts']['data-original-rules'] = $option['data_original_rules'];
		}
		if ( isset( $option['data_rulestype'] ) ) {
			$current_option['atts']['data-rulestype'] = $option['data_rulestype'];
		}
		if ( isset( $option['data_text'] ) ) {
			$current_option['atts']['data-text'] = $option['data_text'];
		}
		if ( isset( $option['data_hide_amount'] ) ) {
			$current_option['atts']['data-hide-amount'] = $option['data_hide_amount'];
		}
		if ( ! empty( $tax_obj ) ) {
			$current_option['atts']['data-tax-obj'] = $tax_obj;
		}

		$select_options[] = $current_option;
	}

	$select_array = apply_filters(
		'wc_element_select_args',
		$select_array,
		isset( $tm_element_settings ) && isset( $tm_element_settings['type'] ) ? $tm_element_settings['type'] : '',
		isset( $args ) ? $args : [],
	);

	$select_options = apply_filters(
		'wc_element_select_option_args',
		$select_options,
		isset( $tm_element_settings ) && isset( $tm_element_settings['type'] ) ? $tm_element_settings['type'] : '',
		isset( $args ) ? $args : [],
	);

	THEMECOMPLETE_EPO_HTML()->create_dropdown( $select_array, $select_options, '/n', false, true );
	?>
	</label>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php'; ?>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity.php'; ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : [] ); ?>
</div><?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_choice_description.php'; ?></li>
	<?php
endif;
