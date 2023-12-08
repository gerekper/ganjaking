<?php
/**
 * The template for displaying the product element dropdown for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/products/template-dropdown.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates/Products
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( isset( $layout_mode, $class_label, $element_id, $name, $fieldtype, $placeholder, $priced_individually, $options ) ) :
	$layout_mode         = (string) $layout_mode;
	$class_label         = (string) $class_label;
	$element_id          = (string) $element_id;
	$name                = (string) $name;
	$fieldtype           = (string) $fieldtype;
	$placeholder         = (string) $placeholder;
	$priced_individually = (bool) $priced_individually;


	$checked_option = [];
	?>
<li class="tmcp-field-wrap tc-epo-element-product-holder tc-epo-element-product-<?php echo esc_attr( $layout_mode ); ?>"><div class="tmcp-field-wrap-inner">
	<label class="tc-col tm-epo-field-label<?php echo esc_attr( $class_label ); ?>" for="<?php echo esc_attr( $element_id ); ?>">
	<?php
	$select_array = [
		'class' => $fieldtype . ' tc-epo-field-product tm-epo-field tmcp-select',
		'id'    => $element_id,
		'name'  => $name,
		'atts'  => [
			'data-price'           => '',
			'data-rules'           => '',
			'data-original-rules'  => '',
			'data-placeholder'     => $placeholder,
			'data-no-price-change' => '1',
			'data-no-price'        => ( ! $priced_individually ),
		],
	];

	if ( isset( $required ) && ! empty( $required ) ) {
		$select_array['required'] = true;
	}
	if ( isset( $element_data_attr ) && is_array( $element_data_attr ) ) {
		$select_array['atts'] = array_merge( $select_array['atts'], $element_data_attr );
	}

	$select_options = [];
	if ( is_array( $options ) ) {
		foreach ( $options as $option ) {
			$current_option = [
				'text'  => $option['text'],
				'value' => $option['value_to_show'],
				'attS'  => [],
			];

			if ( isset( $option['selected'] ) && isset( $option['current'] ) ) {
				$current_option['selected'] = $option['selected'];
				if ( $option['selected'] === $option['current'] ) {
					$checked_option = $option;
				}
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
			if ( isset( $option['data_type'] ) ) {
				$current_option['atts']['data-type'] = $option['data_type'];
			}
			if ( isset( $option['data_hide_amount'] ) ) {
				$current_option['atts']['data-hide-amount'] = $option['data_hide_amount'];
			}
			$current_option['atts']['data-no-price'] = ( ! $priced_individually );

			$select_options[] = $current_option;
		}
		if ( ! empty( $checked_option ) ) {
			$option = $checked_option;
		}
	}

	THEMECOMPLETE_EPO_HTML()->create_dropdown( $select_array, $select_options, '/n', false, true );
	?>
	</label>
	<?php
	require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php';
	require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity-hidden.php';
	?>
</div></li>
<li class="tc-epo-element-product-li-container tm-hidden">
	<?php
	require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-variation.php';
	require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-container.php';
	?>
</li>
	<?php
endif;
