<?php
/**
 * Keywords template
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>
<# if ( ! _.isEmpty( keywords ) ) { #>
<select class="bdt-elementpack-library-keywords">
	<option value=""><?php esc_html_e( 'Any Topic', 'bdthemes-element-pack' ); ?></option>
	<# _.each( keywords, function( title, term_slug ) { #>
	<option value="{{ term_slug }}">{{ title }}</option>
	<# } ); #>
</select>
<# } #>