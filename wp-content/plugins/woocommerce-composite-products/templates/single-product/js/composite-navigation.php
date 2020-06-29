<?php
/**
 * Composite Navigation template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/js/composite-navigation.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><script type="text/template" id="tmpl-wc_cp_composite_navigation">
	<div class="composite_navigation_inner">
		<a class="page_button prev {{ data.prev_btn.btn_classes }}" href="{{ data.prev_btn.btn_link }}" rel="nofollow" aria-label="{{ data.prev_btn.btn_label }}">{{ data.prev_btn.btn_text }}</a>
		<a class="page_button next {{ data.next_btn.btn_classes }}" href="{{ data.next_btn.btn_link }}" rel="nofollow" aria-label="{{ data.next_btn.btn_label }}">{{ data.next_btn.btn_text }}</a>
	</div>
</script>
