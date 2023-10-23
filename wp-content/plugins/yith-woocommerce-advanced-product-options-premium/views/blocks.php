<?php
/**
 * Blocks Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$block_id = isset( $_REQUEST['block_id'] ) ? sanitize_key( $_REQUEST['block_id'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

if ( $block_id ) {
	yith_wapo_get_view(
		'block-editor/block-editor.php',
		array(
			'block_id' => $block_id,
		)
	);
} else {
	yith_wapo_get_view(
		'block-editor/blocks-table.php',
        array(),
        defined( 'YITH_WAPO_PREMIUM' ) && YITH_WAPO_PREMIUM ? 'premium/' : ''
	);
}


