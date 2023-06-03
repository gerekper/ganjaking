<?php
/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @package WooCommerce Redsys Gateway
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */

/**
 * Bloque de imagen de visa y mastercard
 */
function redsys_create_block_bloques_redsys_block_init() {
	register_block_type(
		REDSYS_BLOCKS_PATH . 'build',
		array(
			'attributes' => array(
				'showBizum'      => array(
					'type'    => 'boolean',
					'default' => 1,
				),
				'bizumSize'      => array(
					'type'    => 'number',
					'default' => 50,
				),
				'showDiners'     => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'dinersSize'     => array(
					'type'    => 'number',
					'default' => 50,
				),
				'showDiscover'   => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'discoverSize'   => array(
					'type'    => 'number',
					'default' => 50,
				),
				'showMaestro'    => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'maestroSize'    => array(
					'type'    => 'number',
					'default' => 50,
				),
				'showMastercard' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'mastercardSize' => array(
					'type'    => 'number',
					'default' => 50,
				),
				'showUnionPay'   => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'unionPaySize'   => array(
					'type'    => 'number',
					'default' => 50,
				),
				'showVisa'       => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'visaSize'       => array(
					'type'    => 'number',
					'default' => 50,
				),
			),
		)
	);
}
add_action( 'init', 'redsys_create_block_bloques_redsys_block_init' );

/**
 * Enqueue block editor only JavaScript and CSS
 */
function redsys_imagen_visa_mastercard_enqueue() {
	wp_enqueue_script(
		'imagen-visa-mastercard-script',
		REDSYS_BLOCKS_PATH . 'build/index.js',
		array( 'wp-blocks', 'wp-element', 'wp-editor' ),
		filemtime( REDSYS_BLOCKS_PATH . 'build/index.js' ),
		true
	);

	wp_localize_script(
		'imagen-visa-mastercard-script',
		'imagenVisaMastercard',
		array( 'pluginUrl' => REDSYS_PLUGIN_URL_P )
	);

}
add_action( 'enqueue_block_editor_assets', 'redsys_imagen_visa_mastercard_enqueue' );
