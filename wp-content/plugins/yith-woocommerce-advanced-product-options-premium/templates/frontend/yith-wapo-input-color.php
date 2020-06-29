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

$class_container = 'ywapo_input_container_'.$type;
$input_classes = array( 'ywapo_input ywapo_input_'.$type , 'ywapo_price_'.esc_attr( $price_type ) );

$index = $key;

/* price position fix */

$after_label .= $price_html . $yith_wapo_frontend->getTooltip( stripslashes( $tooltip ) );

/* value fix */
$input_classes[] = 'ywapo_colorpicker';
$type = 'hidden';

echo '<div class="ywapo_input_container '.$class_container.'">';

echo sprintf( '<input data-typeid="%s" data-price="%s" data-pricetype="%s" data-index="%s" type="%s" name="%s[%s]" value="%s" %s class="%s" %s %s %s/>',
	esc_attr( $type_id ),
	esc_attr( $price_calculated ),
	esc_attr( $price_type ),
	$index,
	esc_attr( $type ),
	esc_attr( $name ),
	$key,
	esc_attr( $value ),
	($checked ? 'checked' : ''),
	implode( ' ' , $input_classes ),
	$min_html,
	$max_html,
	$disabled
);

echo sprintf( '%s<input type="text" class="wp-color-picker" />%s',$before_label, $after_label );

if ( $description != '' ) {
	echo '<p class="wapo_option_description">' . $description . '</p>';
}

echo '</div>';