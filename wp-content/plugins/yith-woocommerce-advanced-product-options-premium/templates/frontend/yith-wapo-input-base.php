<?php
/**
 * Input field template
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Add-Ons Premium
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$class_container = 'ywapo_input_container_' . $type;
$input_classes = array( 'ywapo_input ywapo_input_' . $type, 'ywapo_price_' . esc_attr( $price_type ) );

$index = $key;

/* price position fix */

if ( $hidelabel ) {
	$before_label = $after_label = '';
}

if ( $type == 'radio' || $type == 'checkbox') {
    $after_label .= '<label for="' . $control_id . '" style="cursor: pointer;">' . $price_html . $yith_wapo_frontend->getTooltip( stripslashes( $tooltip ) ) . '</label>';
} else {
    $before_label .= '<label for="' . $control_id . '" style="cursor: pointer;">' . $price_html . $yith_wapo_frontend->getTooltip( stripslashes( $tooltip ) ) . '</label>';
}

/* value fix */
if ( $type == 'radio' ) {
    $value = $key;
    $key = '';
} else if ( $type == 'date' ){
    $input_classes[] = 'ywapo_datepicker';
    $type = 'text';
}

echo '<div class="ywapo_input_container ' . $class_container . '">';

echo sprintf( '%s<input id="%s" placeholder="%s" data-typeid="%s" data-price="%s" data-pricetype="%s" data-index="%s" type="%s" name="%s[%s]" value="%s" %s class="%s" %s %s %s %s %s/>%s',
	$before_label,
	$control_id,
	$placeholder,
	esc_attr( $type_id ),
	esc_attr( $price_calculated ),
	esc_attr( $price_type ),
	$index,
	esc_attr( $type ),
	esc_attr( $name ),
	$key,
	esc_attr( $value ),
	( $checked ? 'checked' : '' ),
	implode( ' ', $input_classes ),
	$min_html,
	$max_html,
	$max_length,
	$required ? 'required="required"' : '',
	$disabled,
	$after_label
);

if ( esc_attr( $type ) == 'file' ) {
	echo '<div><img class="preview" src="" style="max-width: 200px; display: none;" /></div>';
}

if ( $description != '' ) {
	echo '<p class="wapo_option_description">' . $description . '</p>';
}

echo '</div>';