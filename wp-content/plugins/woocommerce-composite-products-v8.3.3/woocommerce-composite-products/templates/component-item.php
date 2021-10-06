<?php
/**
 * Component item data template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/component-item.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version  2.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><dl class="component"><?php

	$key = sanitize_text_field( $component_data[ 'key' ] );

	?><dt class="component-<?php echo sanitize_html_class( $key ); ?>"><?php echo wp_kses_post( $component_data[ 'key' ] ); ?>:</dt>
	<dd class="component-<?php echo sanitize_html_class( $key ); echo empty( $component_data[ 'value' ] ) ? ' component-hidden' : ''; ?>"><?php echo wp_kses_post( wpautop( $component_data[ 'value' ] ) ); ?></dd>
</dl>
