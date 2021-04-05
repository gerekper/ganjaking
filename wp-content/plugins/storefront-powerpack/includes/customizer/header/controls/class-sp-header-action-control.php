<?php
/**
 * Class to create a Action Control.
 *
 * @package  Storefront_Powerpack
 * @author   Tiago Noronha
 * @since    1.0.0
 */
class SP_Header_Action_Control extends WP_Customize_Control {
	public function render_content() {
		?>

		<span class="customize-control-title"><?php _e( 'Header Customizer', 'storefront-powerpack' ) ?></span>

		<div style="padding: 10px; background-color: #fff; border: 1px solid #ccc; margin-bottom: 15px;">
			<span class="dashicons dashicons-info" style="color: #007cb2; float: right; margin-left: 1em;"></span>
			<?php _e( 'The Header Customizer allows you to toggle and rearrange the components in Storefront\'s header.', 'storefront-powerpack' ); ?>
		</div>

		<button class="button sp-header-open"><?php _e( 'Customize Header', 'storefront-powerpack' ); ?></button>
		<input type="hidden" <?php $this->input_attrs(); ?> value="" <?php echo $this->get_link(); ?> />
		<?php
	}
}