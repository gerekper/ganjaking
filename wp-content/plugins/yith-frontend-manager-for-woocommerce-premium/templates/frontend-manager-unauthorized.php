<?php
/**
 * Unahthorized template
 *
 * @package YITH\FrontendManager\Templates
 */

?>
<div id="yith-wcfm-unauthorized">
	<h3 class="<?php echo esc_attr( $alert_title_class ); ?>">
		<?php echo wp_kses_post( $alert_title ); ?>
	</h3>

	<p><?php echo wp_kses_post( $alert_message ); ?></p>
</div><!-- .page-content -->
