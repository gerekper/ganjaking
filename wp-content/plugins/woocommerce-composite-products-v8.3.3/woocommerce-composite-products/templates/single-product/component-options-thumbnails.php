<?php
/**
 * Component Options - Thumbnails template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/component-options-thumbnails.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @since    3.6.0
 * @version  3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div id="component_option_thumbnails_<?php echo $component_id; ?>" class="component_option_thumbnails columns-<?php echo esc_attr( $thumbnail_columns ); ?>" data-component_option_columns="<?php echo esc_attr( $thumbnail_columns ); ?>"></div>
