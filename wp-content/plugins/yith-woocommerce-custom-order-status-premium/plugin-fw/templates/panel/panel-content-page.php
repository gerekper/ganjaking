<?php
/**
 * The Template for displaying the YIT panel form.
 *
 * @var YIT_Plugin_Panel $panel
 * @var string           $panel_content_class
 * @var string           $form_method
 * @var string           $option_key
 * @package    YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$reset_warning = __( 'If you continue with this action, you will reset all options in this page.', 'yith-plugin-fw' ) . '\n' . __( 'Are you sure?', 'yith-plugin-fw' );

?>
<div id="wrap" class="yith-plugin-fw plugin-option yit-admin-panel-container">
	<?php $panel->message(); ?>
	<div class="<?php echo esc_attr( $panel_content_class ); ?>">
		<h2><?php echo wp_kses_post( $panel->get_tab_title() ); ?></h2>
		<?php if ( $panel->is_show_form() ) : ?>
			<form id="yith-plugin-fw-panel" method="<?php echo esc_attr( $form_method ); ?>" action="options.php">
				<?php do_settings_sections( 'yit' ); ?>
				<p>&nbsp;</p>
				<?php settings_fields( 'yit_' . $panel->settings['parent'] . '_options' ); ?>
				<input type="hidden" name="<?php echo esc_attr( $panel->get_name_field( 'option_key' ) ); ?>"
						value="<?php echo esc_attr( $option_key ); ?>"/>
				<input type="submit" class="button-primary"
						value="<?php esc_attr_e( 'Save Changes', 'yith-plugin-fw' ); ?>"
						style="float:left;margin-right:10px;"/>
				<input type="hidden" name="page" value="<?php echo esc_attr( $panel->settings['page'] ); ?>"/>
				<input type="hidden" name="tab" value="<?php echo esc_attr( $panel->get_current_tab() ); ?>"/>
				<input type="hidden" name="sub_tab" value="<?php echo esc_attr( $panel->get_current_sub_tab() ); ?>"/>
			</form>
			<form method="post">
				<input type="hidden" name="yit-action" value="reset"/>
				<input type="submit" name="yit-reset" class="button-secondary"
						value="<?php esc_attr_e( 'Reset to default', 'yith-plugin-fw' ); ?>"
						onclick="return confirm('<?php echo esc_attr( $reset_warning ); ?>');"/>
			</form>
			<p>&nbsp;</p>
		<?php endif ?>
	</div>
</div>
