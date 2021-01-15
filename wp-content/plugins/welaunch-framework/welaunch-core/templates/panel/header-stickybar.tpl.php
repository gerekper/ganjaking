<?php
/**
 * The template for the header sticky bar.
 * Override this template by specifying the path where it is stored (templates_path) in your weLaunch config.
 *
 * @author        weLaunch Framework
 * @package       weLaunchFramework/Templates
 * @version:      4.0.0
 */

?>
<div id="welaunch-sticky">
	<div id="info_bar">
		<a href="javascript:void(0);"
			class="expand_options<?php echo esc_attr( ( $this->parent->args['open_expanded'] ) ? ' expanded' : '' ); ?>"<?php echo( true === $this->parent->args['hide_expand'] ? ' style="display: none;"' : '' ); ?>>
			<?php esc_attr_e( 'Expand', 'welaunch-framework' ); ?>
		</a>
		<div class="welaunch-action_bar">
			<span class="spinner"></span>
			<?php
			if ( false === $this->parent->args['hide_save'] ) {
				submit_button( esc_attr__( 'Save Changes', 'welaunch-framework' ), 'primary', 'welaunch_save', false, array( 'id' => 'welaunch_top_save' ) );
			}

			if ( false === $this->parent->args['hide_reset'] ) {
				submit_button( esc_attr__( 'Reset Section', 'welaunch-framework' ), 'secondary', $this->parent->args['opt_name'] . '[defaults-section]', false, array( 'id' => 'welaunch-defaults-section-top' ) );
				submit_button( esc_attr__( 'Reset All', 'welaunch-framework' ), 'secondary', $this->parent->args['opt_name'] . '[defaults]', false, array( 'id' => 'welaunch-defaults-top' ) );
			}
			?>
		</div>
		<div class="welaunch-ajax-loading" alt="<?php esc_attr_e( 'Working...', 'welaunch-framework' ); ?>">&nbsp;</div>
		<div class="clear"></div>
	</div>

	<!-- Notification bar -->
	<div id="welaunch_notification_bar">
		<?php $this->notification_bar(); ?>
	</div>
</div>
