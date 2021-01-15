<?php
/**
 * The template for the panel footer area.
 * Override this template by specifying the path where it is stored (templates_path) in your weLaunch config.
 *
 * @author        weLaunch Framework
 * @package       weLaunchFramework/Templates
 * @version:      4.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<div id="welaunch-sticky-padder" style="display: none;">&nbsp;</div>
<div id="welaunch-footer-sticky">
	<div id="welaunch-footer">
		<?php
		if ( isset( $this->parent->args['share_icons'] ) ) {
			$skip_icons = false;

			if ( ! $this->parent->args['dev_mode'] && $this->parent->args_class->omit_icons ) {
				$skip_icons = true;
			}
			?>
			<div id="welaunch-share">
				<?php
				foreach ( $this->parent->args['share_icons'] as $links ) {
					if ( $skip_icons ) {
						continue;
					}
					// SHIM, use URL now.
					if ( isset( $links['link'] ) && ! empty( $links['link'] ) ) {
						$links['url'] = $links['link'];
						unset( $links['link'] );
					}
					if ( isset( $links['icon'] ) && ! empty( $links['icon'] ) ) {
						if ( strpos( $links['icon'], 'el-icon' ) !== false && strpos( $links['icon'], 'el ' ) === false ) {
							$links['icon'] = 'el ' . $links['icon'];
						}
					}
					?>
					<a href="<?php echo esc_url( $links['url'] ); ?>" title="<?php echo esc_attr( $links['title'] ); ?>" target="_blank">
						<?php if ( isset( $links['icon'] ) && ! empty( $links['icon'] ) ) { ?>
							<i class="<?php echo esc_attr( $links['icon'] ); ?>"></i>
						<?php } else { ?>
							<img src="<?php echo esc_url( $links['img'] ); ?>"/>
						<?php } ?>
					</a>
				<?php } ?>
			</div>
		<?php } ?>

		<div class="welaunch-action_bar">
			<span class="spinner"></span>
			<?php
			if ( false === $this->parent->args['hide_save'] ) {
				submit_button( esc_html__( 'Save Changes', 'welaunch-framework' ), 'primary', 'welaunch_save', false, array( 'id' => 'welaunch_bottom_save' ) );
			}

			if ( false === $this->parent->args['hide_reset'] ) {
				submit_button( esc_html__( 'Reset Section', 'welaunch-framework' ), 'secondary', $this->parent->args['opt_name'] . '[defaults-section]', false, array( 'id' => 'welaunch-defaults-section-bottom' ) );
				submit_button( esc_html__( 'Reset All', 'welaunch-framework' ), 'secondary', $this->parent->args['opt_name'] . '[defaults]', false, array( 'id' => 'welaunch-defaults-bottom' ) );
			}
			?>
		</div>
		<div class="welaunch-ajax-loading" alt="<?php esc_html_e( 'Working...', 'welaunch-framework' ); ?>">&nbsp;</div>
		<div class="clear"></div>
	</div>
</div>
