<?php
/**
 * Class to create a Notice Control.
 *
 * @package  Storefront_Powerpack
 * @author   Tiago Noronha
 * @since    1.0.0
 */
class SP_Designer_Notice_Control extends WP_Customize_Control {
	/**
	 * @access public
	 * @var string
	 */
	public $settings = 'blogname';

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		?>
		<div class="sp-designer-notice">
			<p>
				<span class="dashicons dashicons-info"></span>
				<?php _e( 'Select an editable element on your page to the right to adjust its properties.', 'storefront-powerpack' ); ?>
			</p>
		</div>
		<?php
	}
}