<?php

namespace wpbuddy\rich_snippets\pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @var \WP_Post $post
 */
$post = $this->arguments[0];
#$controller = Admin_Position_Controller::instance();

?>

<p class="description"><?php _e( 'Use this textarea field to import from plain JSON-LD code or from SNIP examples that you can find around the web. You can also export a Global Snippet to use it on another site. <a href="https://rich-snippets.io/import-export/" target="_blank">Read more about this here.</a>',
		'rich-snippets-schema' ); ?></p>

<textarea class="large-text code wpb-rs-importexport-code" rows="10"></textarea>

<button class="button wpb-rs-import button-disabled">
    <svg data-icon="file-export" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
        <path fill="currentColor"
              d="M384 121.9c0-6.3-2.5-12.4-7-16.9L279.1 7c-4.5-4.5-10.6-7-17-7H256v128h128zM571 308l-95.7-96.4c-10.1-10.1-27.4-3-27.4 11.3V288h-64v64h64v65.2c0 14.3 17.3 21.4 27.4 11.3L571 332c6.6-6.6 6.6-17.4 0-24zm-379 28v-32c0-8.8 7.2-16 16-16h176V160H248c-13.2 0-24-10.8-24-24V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V352H208c-8.8 0-16-7.2-16-16z"/>
    </svg>
	<?php _e( 'Import', 'rich-snippets-schema' ); ?>
</button>
<button class="button wpb-rs-export">
    <svg data-icon="file-import" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
        <path fill="currentColor"
              d="M16 288c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h112v-64zm489-183L407.1 7c-4.5-4.5-10.6-7-17-7H384v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-153 31V0H152c-13.3 0-24 10.7-24 24v264h128v-65.2c0-14.3 17.3-21.4 27.4-11.3L379 308c6.6 6.7 6.6 17.4 0 24l-95.7 96.4c-10.1 10.1-27.4 3-27.4-11.3V352H128v136c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H376c-13.2 0-24-10.8-24-24z"/>
    </svg>
	<?php _e( 'Export', 'rich-snippets-schema' ); ?>
</button>