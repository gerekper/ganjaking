<?php
/**
 * Validation Message template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/js/validation-message.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><script type="text/template" id="tmpl-wc_cp_validation_message">
	<div class="validation_message woocommerce-info">
		<ul style="list-style:none">
			<# for ( var index = 0; index <= data.length - 1; index++ ) { #>
				<li>{{{ data[ index ] }}}</li>
			<# } #>
		</ul>
	</div>
</script>
