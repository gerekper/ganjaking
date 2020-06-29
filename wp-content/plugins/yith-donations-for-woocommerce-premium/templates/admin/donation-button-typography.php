<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



$font_size_field = array(
	'id' => $field['id'].'_size',
	'name' => $field['id'].'[size]',
	'value' => !empty( $field['value']['size'] ) ? $field['value']['size'] : $field['default']['size'],
	'type' => 'number',
	'min' => 1,
	'custom_attributes' => 'style="width:20%;"',
);

yith_plugin_fw_get_field( $font_size_field, true );

$unit_font_field = array(
	'id' => $field['id'].'_unit',
	'name' => $field['id'].'[unit]',
	'value' => !empty( $field['value']['unit'] ) ? $field['value']['unit'] : $field['default']['unit'],
	'type' => 'select',
	'custom_attributes' => 'style="width:20%;"',
	'options' => array(
			'px' => 'px',
			'em' => 'em',
			'pt' => 'pt',
			'rem' => 'rem'
	)
);
yith_plugin_fw_get_field( $unit_font_field, true );

$font_style_field = array(
	'id' => $field['id'].'_style',
	'name' => $field['id'].'[style]',
	'value' => !empty( $field['value']['style'] ) ? $field['value']['style'] : $field['default']['style'],
	'type' => 'select',
	'custom_attributes' => 'style="width:20%;"',
	'options' => array(
		'regular' => _x( 'Regular', 'Font style type','yith-donations-for-woocommerce'),
		'bold' => _x( 'Bold', 'Font style type','yith-donations-for-woocommerce'),
		'extra-bold' => _x( 'Extra bold', 'Font style type','yith-donations-for-woocommerce'),
		'italic' => _x( 'Italic', 'Font style type','yith-donations-for-woocommerce'),
		'bold-italic'=> _x( 'Italic bold', 'Font style type','yith-donations-for-woocommerce')
	)
);
yith_plugin_fw_get_field( $font_style_field, true );


$font_transform_field = array(
	'id' => $field['id'].'_transform',
	'name' => $field['id'].'[transform]',
	'value' => !empty( $field['value']['transform'] ) ? $field['value']['transform'] : $field['default']['transform'],
	'type' => 'select',
	'custom_attributes' => 'style="width:20%;"',
	'options' => array(
		'none'          =>  _x( 'None', 'Font transform type','yith-donations-for-woocommerce'),
		'lowercase'     =>  _x( 'Lowercase', 'Font transform type','yith-donations-for-woocommerce'),
		'uppercase'     =>  _x( 'Uppercase', 'Font transform type','yith-donations-for-woocommerce'),
		'capitalize'    =>  _x( 'Capitalize', 'Font transform type','yith-donations-for-woocommerce')

	)
);

yith_plugin_fw_get_field( $font_transform_field, true );