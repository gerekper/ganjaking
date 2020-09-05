<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


printf(
	'<p class="description">%s</p>',
	__( 'CodeCanyon allows only one license per domain. If you mistakenly activated the plugin on this domain, just deactivate it here and you will then be able to activate it on the new domain. This process can be repeated up to three times per month. Please note that after hitting this button, the plugin will be deactivated on this site.', 'rich-snippets-schema' )
);
?>

<a href="#"
   class="button wpb-rs-support-deactivate-license"><?php _e( 'Deactivate license on this site', 'rich-snippets-schema' ); ?></a>

<p class="wpb-rs-support-deactivate-license-error"></p>
