<?php

/**
* Class for Group.
*
* @since  1.1.3
* @access public
*/
class LoginPress_Group_Control extends WP_Customize_Control {

  /**
  * The type of customize control being rendered.
  *
  * @since  1.1.3
  * @access public
  * @var    string
  */
  public $type = 'group';

  /**
  * Information text for the Group.
  *
  * @since  1.1.3
  * @access public
  * @var    string
  */
  public $info_text;

  /**
  * Enqueue scripts/styles.
  *
  * @since  1.0.17
  * @access public
  * @return void
  */
  public function enqueue() {
		wp_enqueue_style( 'loginpress-group-control-css', LOGINPRESS_DIR_URL . 'css/controls/loginpress-group-control.css', array(), LOGINPRESS_VERSION );
  }

  /**
  * Displays the control content.
  *
  * @since  1.0.17
  * @access public
  * @return void
  */
  public function render_content() {
    ?>

    <div id="input_<?php echo $this->id; ?>" class="loginpress-group-wrapper">
      <h3 class="loginpress-group-heading"><?php echo esc_attr( $this->label ); ?></h3>
      <div class="loginpress-group-info">
        <p><span class="loginpress-group-badge badges"><?php esc_html_e( 'Info:', 'loginpress' ) ?></span><?php echo esc_html( $this->info_text ); ?></p>
      </div>
    </div>


  <?php }


}
?>
