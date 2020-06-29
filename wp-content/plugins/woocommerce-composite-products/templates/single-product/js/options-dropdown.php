<?php
/**
 * Dropdown Options template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/js/options-dropdown.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 3.12.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><script type="text/template" id="tmpl-wc_cp_options_dropdown">
	<# for ( var index = 0; index <= data.length - 1; index++ ) { #>
		<# if ( false === data[ index ].is_hidden ) { #>
			<option value="{{ data[ index ].option_id }}" <# if ( data[ index ].is_disabled ) { #>disabled="disabled"<# } #> <# if ( data[ index ].is_selected ) { #>selected="selected"<# } #>>{{{ data[ index ].option_display_title }}}</option>
		<# } #>
	<# } #>
</script>
