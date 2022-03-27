<?php
/**
 * Options for YITH Plugin Panel WooCommerce
 *
 * @package YITH Plugin Framework
 */

return array(
	'ajax-customers' => array(
		'type'              => 'ajax-customers',
		'value'             => 1,
		'php_unit_expected' => 1,
	),

	'ajax-customers-multiple' => array(
		'type'              => 'ajax-customers',
		'multiple'          => true,
		'value'             => array( 1, 2, 3 ),
		'php_unit_expected' => array( 1, 2, 3 ),
	),

	'ajax-posts' => array(
		'type'              => 'ajax-posts',
		'value'             => 1,
		'php_unit_expected' => 1,
	),

	'ajax-posts-multiple' => array(
		'type'              => 'ajax-posts',
		'multiple'          => true,
		'value'             => array( 1, 2, 3 ),
		'php_unit_expected' => array( 1, 2, 3 ),
	),

	'ajax-products' => array(
		'type'              => 'ajax-products',
		'value'             => 1,
		'php_unit_expected' => 1,
	),

	'ajax-products-multiple' => array(
		'type'              => 'ajax-products',
		'multiple'          => true,
		'value'             => array( 1, 2, 3 ),
		'php_unit_expected' => array( 1, 2, 3 ),
	),

	'ajax-terms' => array(
		'type'              => 'ajax-terms',
		'value'             => 1,
		'php_unit_expected' => 1,
	),

	'ajax-terms-multiple' => array(
		'type'              => 'ajax-terms',
		'multiple'          => true,
		'value'             => array( 1, 2, 3 ),
		'php_unit_expected' => array( 1, 2, 3 ),
	),

	'checkbox' => array(
		'type'              => 'checkbox',
		'value'             => 1,
		'php_unit_expected' => 'yes',
	),

	'checkbox-off' => array(
		'type'              => 'checkbox',
		'value'             => 0,
		'php_unit_expected' => 'no',
	),

	'checkbox-array' => array(
		'type'              => 'checkbox-array',
		'value'             => array( 'one', 'two', 'three' ),
		'php_unit_expected' => array( 'one', 'two', 'three' ),
	),

	'colorpicker' => array(
		'type'              => 'colorpicker',
		'value'             => '#123456',
		'php_unit_expected' => '#123456',
	),

	'country-select' => array(
		'type'              => 'country-select',
		'value'             => 'US:NY',
		'php_unit_expected' => 'US:NY',
	),

	'date-format' => array(
		'type'              => 'date-format',
		'value'             => 'Y-m-d',
		'php_unit_expected' => 'Y-m-d',
	),

	'datepicker' => array(
		'type'              => 'datepicker',
		'value'             => '2020-12-25',
		'php_unit_expected' => '2020-12-25',
	),

	'dimensions' => array(
		'type'              => 'dimensions',
		'value'             => array(
			'unit'       => 'px',
			'dimensions' => array(
				'top'    => 10,
				'right'  => 20,
				'bottom' => 10,
				'left'   => 20,
			),
			'linked'     => 'yes',
		),
		'php_unit_expected' => array(
			'unit'       => 'px',
			'dimensions' => array(
				'top'    => 10,
				'right'  => 20,
				'bottom' => 10,
				'left'   => 20,
			),
			'linked'     => 'yes',
		),
	),

	'hidden' => array(
		'type'              => 'hidden',
		'value'             => 'This is a dummy test!',
		'php_unit_expected' => 'This is a dummy test!',
	),

	'icons' => array(
		'type'              => 'icons',
		'value'             => 'FontAwesome:music',
		'php_unit_expected' => 'FontAwesome:music',
	),

	'image-gallery' => array(
		'type'              => 'image-gallery',
		'value'             => '1,2,3',
		'php_unit_expected' => '1,2,3',
	),

	'multi-colorpicker' => array(
		'type'              => 'multi-colorpicker',
		'value'             => array( '#ffffff', '#000000' ),
		'php_unit_expected' => array( '#ffffff', '#000000' ),
	),

	'multi-select' => array(
		'type'              => 'multi-select',
		'value'             => array( 'one', 'two' ),
		'php_unit_expected' => array( 'one', 'two' ),
	),

	'number' => array(
		'type'              => 'number',
		'value'             => 10,
		'php_unit_expected' => 10,
	),

	'onoff' => array(
		'type'              => 'onoff',
		'value'             => 1,
		'php_unit_expected' => 'yes',
	),

	'onoff-off' => array(
		'type'              => 'onoff',
		'value'             => 0,
		'php_unit_expected' => 'no',
	),

	'password' => array(
		'type'              => 'password',
		'value'             => 'password',
		'php_unit_expected' => 'password',
	),

	'radio' => array(
		'type'              => 'radio',
		'value'             => 'one',
		'php_unit_expected' => 'one',
	),

	'select' => array(
		'type'              => 'select',
		'value'             => 'one',
		'php_unit_expected' => 'one',
	),

	'select-images' => array(
		'type'              => 'select-images',
		'value'             => 'one',
		'php_unit_expected' => 'one',
	),

	'slider' => array(
		'type'              => 'slider',
		'value'             => 50,
		'php_unit_expected' => 50,
	),

	'text' => array(
		'type'              => 'text',
		'value'             => 'This is a dummy test!',
		'php_unit_expected' => 'This is a dummy test!',
	),

	'text-array' => array(
		'type'              => 'text-array',
		'value'             => array('one', 'two', "Let's testing quotes and double quotes \""),
		'php_unit_expected' => array('one', 'two', "Let's testing quotes and double quotes \""),
	),

	'textarea' => array(
		'type'              => 'textarea',
		'value'             => "Let's testing quotes, double quotes \" and <h1>HTML tags</h1>",
		'php_unit_expected' => "Let's testing quotes, double quotes \" and <h1>HTML tags</h1>",
	),

	'textarea-codemirror' => array(
		'type'              => 'textarea-codemirror',
		'value'             => "Let's testing quotes, double quotes \" and <h1>HTML tags</h1>",
		'php_unit_expected' => "Let's testing quotes, double quotes \" and <h1>HTML tags</h1>",
	),

	'textarea-editor' => array(
		'type'              => 'textarea-editor',
		'value'             => "Let's testing quotes, double quotes \" and <h1>HTML tags</h1>",
		'php_unit_expected' => "Let's testing quotes, double quotes \" and <h1>HTML tags</h1>",
	),

	'upload' => array(
		'type'              => 'upload',
		'value'             => 'http://example.com/image.jpg',
		'php_unit_expected' => 'http://example.com/image.jpg',
	),
);
