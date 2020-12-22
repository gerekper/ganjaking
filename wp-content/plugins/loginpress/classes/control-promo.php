<?php

/**
* Class for Promotion.
*
* @since  1.0.17
* @access public
*/
class LoginPress_Promo extends WP_Customize_Control {

  /**
  * The type of customize control being rendered.
  *
  * @since  1.0.17
  * @access public
  * @var    string
  */
  public $type = 'promotion';

  /**
  * The type of customize control being rendered.
  *
  * @since  1.0.17
  * @access public
  * @var    string
  */
  public $thumbnail;

  /**
  * Promotion text for <the>  Controler</the>.
  *
  * @since  1.0.17
  * @access public
  * @var    string
  */
  public $promo_text;

  /**
  * Promotion link for the Controler
  *
  * @since  1.0.17
  * @access public
  * @var    string
  */
  public $link;

  /**
  * Enqueue scripts/styles.
  *
  * @since  1.0.17
  * @access public
  * @return void
  */
  public function enqueue() {
    // wp_enqueue_script( 'jt-customize-controls', plugins_url(  '/customize-controls.js' , __FILE__  ), array( 'jquery' ) );
    // wp_enqueue_script( 'jquery-ui-button' );
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

    <span class="customize-control-title">
      <?php echo esc_attr( $this->label ); ?>
      <?php if ( ! empty( $this->description ) ) : ?>
        <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
      <?php endif; ?>
    </span>


    <div id="input_<?php echo $this->id; ?>" class="image">

        <div class="loginpress_promo_thumbnail">
          <a href="<?php echo esc_url( $this->link );?>" target="_blank">
            <div class="customizer-promo-overlay">
            <span class="customizer-promo-text"><?php echo esc_html( $this->promo_text ); ?></span>
            </div>
            <img src="<?php echo esc_url( $this->thumbnail ); ?>" alt="<?php echo esc_attr( $this->id ); ?>" title="<?php echo esc_attr( $this->id ); ?>">
          </a>
        </div> <!--  .loginpress_promo_thumbnail -->

    </div>

  <?php }


}
function loginpress_promo_control_css() {
  ?>
  <style>
  .loginpress_promo_thumbnail a{
    display: inline-block;
    position: relative;
    border:5px solid transparent;
  }
  .loginpress_promo_thumbnail a .customizer-promo-overlay{
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(204, 204, 204, 0.8);
      content: '';
      -webkit-transition: all 0.2s ease-in-out;
      -moz-transition: all 0.2s ease-in-out;
      -ms-transition: all 0.2s ease-in-out;
      transition: all 0.2s ease-in-out;
      opacity: 0;
      visibility: hidden;
      -webkit-transform: scale(.5);
      -moz-transform: scale(.5);
      -ms-transform: scale(.5);
      transform: scale(.5);
    }
    .customizer-promo-text{
      line-height:1.2;
      position: absolute;
      top: 50%;
      left: 50%;
      -webkit-transform:translate(-50%, -50%);
      transform:translate(-50%, -50%);
      width: 100%;
      font-size: 25px;
      color: #000;
      z-index: 100;
      text-align: center;
      opacity: 0;
    }
    .loginpress_promo_thumbnail a:hover{
      border-color: #ccc;
    }
    .loginpress_promo_thumbnail a:hover .customizer-promo-text{
      opacity: 1;
    }
    .loginpress_promo_thumbnail a:hover .customizer-promo-overlay{
      opacity: 1;
      visibility: visible;
      -webkit-transform: scale(1);
      -moz-transform: scale(1);
      -ms-transform: scale(1);
      transform: scale(1);
    }
  </style>
  <?php
}
add_action( 'customize_controls_print_styles', 'loginpress_promo_control_css' );
?>
