<?php
/**
* Class for Radio Button Control.
*
* @since  1.0.23
* @access public
*/
class LoginPress_Misc_Control extends WP_Customize_Control {

	/**
	* The type of customize control being rendered.
	*
	* @since  1.0.23
	* @access public
	* @var    string
	*/
	public $type = '';

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since 1.0.23
   * @access public
   * @return void
	 */
	public function enqueue() {

		// wp_enqueue_script( 'loginpress-miscellaneous-control-js', LOGINPRESS_DIR_URL . 'js/controls/loginpress-miscellaneous-control.js', array( 'jquery' ), LOGINPRESS_VERSION, true );
		// wp_enqueue_style( 'loginpress-miscellaneous-control-css', LOGINPRESS_DIR_URL . 'css/controls/loginpress-miscellaneous-control.css', array(), LOGINPRESS_VERSION );

	}

	/**
  * Displays the control content.
  *
  * @since  1.0.23
  * @access public
  * @return void
  */
	public function render_content() {

		switch ( $this->type ) {
            default:

            case 'hr' :
                echo '<hr />';
                break;
        }
	}
}
