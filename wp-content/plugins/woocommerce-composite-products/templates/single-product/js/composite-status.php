<?php
/**
 * Composite Status Messages template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/js/composite-status.php'.
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

?><script type="text/template" id="tmpl-wc_cp_composite_status">
	<ul class="messages" style="list-style:none">
		<# for ( var index = 0; index < data.length; index++ ) { #>
			<li class="message <# if ( false === data[ index ].is_old ) { #>current<# } #>">
				<span class="content">{{{ data[ index ].message_content }}}</span>
			</li>
		<# } #>
	</ul>
</script>
