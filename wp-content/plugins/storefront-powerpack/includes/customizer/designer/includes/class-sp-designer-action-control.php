<?php
/**
 * Class to create a Action Control.
 *
 * @package  Storefront_Powerpack
 * @author   Tiago Noronha
 * @since    1.0.0
 */
class SP_Designer_Action_Control extends WP_Customize_Control {
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
		<button class="button sp-designer-point-click-toggle sp-add-new-style"><?php _e( 'Add a Style', 'storefront-powerpack' ); ?></button>
		<?php
	}
}