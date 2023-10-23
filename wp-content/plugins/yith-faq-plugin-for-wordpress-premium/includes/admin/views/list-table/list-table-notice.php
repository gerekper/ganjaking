<?php
/**
 * List table notice template
 *
 * @package YITH\FAQPluginForWordPress\Admin\Views\ListTable
 * @var $message string The message.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="yith-faq-shortcodes-notice yith-plugin-fw-animate__appear-from-top ">
	<p><?php echo esc_attr( $message ); ?></p>
	<button type="button" class="notice-dismiss"></button>
</div>

