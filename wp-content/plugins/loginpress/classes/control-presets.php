<?php

/**
* Class for Presets.
*
* @since  1.0.9
* @access public
*/
class LoginPress_Presets extends WP_Customize_Control {

  /**
  * The type of customize control being rendered.
  *
  * @since  1.0.9
  * @access public
  * @var    string
  */
  public $type = 'checkbox-multiple';

  /**
  * Enqueue scripts/styles.
  *
  * @since  1.0.9
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
  * @since  1.0.9
  * @access public
  * @return void
  */
  public function render_content() {


    if ( empty( $this->choices ) )
    return;
    $name = 'loginpress_preset-' . $this->id; ?>

    <span class="customize-control-title">
      <?php echo esc_attr( $this->label ); ?>
      <?php if ( ! empty( $this->description ) ) : ?>
        <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
      <?php endif; ?>
    </span>

    <?php // $multi_values = !is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value(); ?>


    <div id="input_<?php echo $this->id; ?>" class="image">

      <?php foreach ( $this->choices as $val ) : ?>

        <?php $_disbaled = isset( $val['pro'] ) ? 'disabled' : ''; ?>
        <?php $_disbaled_link = isset( $val['link'] ) ? 'disabled' : ''; ?>
        <?php $disable_for_pro = $_disbaled == 'disabled'  ? $_disbaled : $_disbaled_link; ?>
        <div class="loginpress_thumbnail">
          <input <?php echo $disable_for_pro; ?> class="image-select" type="radio" value="<?php echo esc_attr( $val['id'] ); ?>" id="<?php echo $this->id . $val['id']; ?>" name="<?php echo esc_attr( $name ); ?>" <?php  checked( $this->value(), $val['id'] ); ?> />
          <label for="<?php echo $this->id . $val['id']; ?>">
            <div class="loginpress_thumbnail_img">
              <img src="<?php echo $val['thumbnail']; ?>" alt="<?php echo esc_attr( $val['id'] ); ?>" title="<?php echo esc_attr( $val['id'] ); ?>">
            </div> <!--  .img -->
            <h3><?php echo $val['name'] ?></h3>
          </label>
          <?php if ( isset( $val['pro'] ) ) : ?>
            <a href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=themes&utm_campaign=pro-upgrade" target="_blank" class="no-available">
              <span><?php _e( 'Unlock Premium Feature', 'loginpress' ); ?></span>
            </a>
          <?php elseif ( isset( $val['link'] ) ) : ?>
            <a href="mailto:support@wpbrigade.com?subject=I want Custom Design for my login page." class="no-available">
              <span><?php _e( 'Contact us for Custom Design', 'loginpress' ); ?></span>
            </a>
          <?php endif; ?>


        <!-- </input> -->
      </div> <!--  .loginpress_thumbnail -->

    <?php endforeach; ?>
  </div>

  <input name='presets_hidden' type="hidden" <?php $this->link(); ?> value="<?php echo  $this->value(); ?>" />
  <?php }


}

function loginpress_presets_control_css() {
  ?>
  <style>
  #customize-theme-controls #accordion-section-customize_presets .accordion-section-title{
    background-color: #FFD700;
  }
  #customize-controls #accordion-section-customize_presets:hover>.accordion-section-title{
    background-color: #FFD700;
  }
  .customize-control-checkbox-multiple .image.ui-buttonset input[type=radio] {
  height: auto;
}
.customize-control-checkbox-multiple .image.ui-buttonset label {
  display: inline-block;
  margin-right: 5px;
  margin-bottom: 5px;
}
.customize-control-checkbox-multiple .image.ui-buttonset label.ui-state-active {
  background: none;
}
.customize-control-checkbox-multiple .customize-control-radio-buttonset label {
  padding: 5px 10px;
  background: #f7f7f7;
  border-left: 1px solid #dedede;
  line-height: 35px;
}
.customize-control-checkbox-multiple label img {
  border: 1px solid #bbb;
  opacity: 0.5;
}
#customize-controls .customize-control-checkbox-multiple label img {
  max-width: 250px;
  height: auto;
  width: 100%;
  margin-bottom: 0;
  border: 0;
  display: block;
}
.customize-control-checkbox-multiple label.ui-state-active img {
  background: #dedede;
  border-color: #000;
  opacity: 1;
  margin-bottom: 0;
}
.customize-control-checkbox-multiple label.ui-state-hover img {
  opacity: 0.9;
  border-color: #999;
}
.customize-control-radio-buttonset label.ui-corner-left {
  border-radius: 3px 0 0 3px;
  border-left: 0;
}
.customize-control-radio-buttonset label.ui-corner-right {
  border-radius: 0 3px 3px 0;
}
#customize-control-customize_presets_settings input[type=radio]{
  display: none;
}
#customize-control-customize_presets_settings label{
  display: block;
  position: relative;
  width: 100%;
}
#customize-control-customize_presets_settings .loginpress_thumbnail{
  width: calc(50% - 10px);
  margin-bottom: 10px;
  position: relative;
  border: 5px solid transparent;
  -webkit-transition:all 0.2s ease-in-out;
  -moz-transition:all 0.2s ease-in-out;
  -ms-transition:all 0.2s ease-in-out;
  transition:all 0.2s ease-in-out;
}
#customize-control-customize_presets_settings .loginpress_thumbnail:nth-child(odd){
  float: left;
}
#customize-control-customize_presets_settings .loginpress_thumbnail:nth-child(even){
  float: right;
}
#customize-control-customize_presets_settings .image:after{
  content: '';
  display: table;
  clear: both;
}
#customize-control-customize_presets_settings h3{
  margin: 0;
  font: 400 14px 'Open Sans', Arial, Helvetica, sans-serif;
  line-height: 1.1;
  padding: 3px;
  text-align: center;
  background: #eee;
  color: #777777;
}
#customize-control-customize_presets_settings label .loginpress_thumbnail_img:after {
  content: '';
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #2EB150;
  position: absolute;
  top: -5px;
  left: -5px;
  border-radius: 50%;
  visibility: hidden;
}
#customize-control-customize_presets_settings label .loginpress_thumbnail_img:before {
  height: 6px;
  width: 3px;
  -webkit-transform-origin: left top;
  -moz-transform-origin: left top;
  -ms-transform-origin: left top;
  -o-transform-origin: left top;
  transform-origin: left top;
  border-right: 3px solid white;
  border-top: 3px solid white;
  border-radius: 2.5px !important;
  content: '';
  position: absolute;
  z-index: 2;
  opacity: 0;
  margin-top: 0px;
  margin-left: -7px;
  top: 5px;
  left: 4px;
}
#customize-control-customize_presets_settings .loginpress_thumbnail_img{
  display: block;
  position: relative;
}
#customize-control-customize_presets_settings input[type="radio"]:checked + label .loginpress_thumbnail_img:before {
  -webkit-animation-delay: 100ms;
  -moz-animation-delay: 100ms;
  animation-delay: 100ms;
  -webkit-animation-duration: 1s;
  -moz-animation-duration: 1s;
  animation-duration: 1s;
  -webkit-animation-timing-function: ease;
  -moz-animation-timing-function: ease;
  animation-timing-function: ease;
  -webkit-animation-name: checkmark;
  -moz-animation-name: checkmark;
  animation-name: checkmark;
  -webkit-transform: scaleX(-1) rotate(135deg);
  -moz-transform: scaleX(-1) rotate(135deg);
  -ms-transform: scaleX(-1) rotate(135deg);
  -o-transform: scaleX(-1) rotate(135deg);
  transform: scaleX(-1) rotate(135deg);
  -webkit-animation-fill-mode: forwards;
  -moz-animation-fill-mode: forwards;
  animation-fill-mode: forwards;
  z-index: 2;
}
#customize-control-customize_presets_settings input[type="radio"]:checked + label .loginpress_thumbnail_img:after{
  visibility: visible;
}
/*#customize-control-customize_presets_settings input[type="radio"]:disabled + label .loginpress_thumbnail_img:before{
  visibility: hidden;
}
#customize-control-customize_presets_settings input[type="radio"]:disabled + label .loginpress_thumbnail_img:after{
  visibility: hidden;
}*/
#customize-control-customize_presets_settings img{
  margin-bottom: 0;
}
#customize-control-customize_presets_settings input[type="radio"]:checked + label img{
  opacity: 1;
}
.no-available{
  top: 0;
  left: 0;
  background: rgba(204, 204, 204, 0.8);
  content: '';
  position: absolute;
  bottom: 0;
  right: 0;
  z-index: 100;
  padding: 20px;
  text-align: center;
  font-weight: bold;
  color: #000;
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
  text-decoration: none !important;
}
#customize-control-customize_presets_settings .loginpress_thumbnail:hover input[type="radio"]:disabled ~ .no-available{
  opacity: 1;
  visibility: visible;
  color: #000;
  -webkit-transform: scale(1);
  -moz-transform: scale(1);
  -ms-transform: scale(1);
  transform: scale(1);
}
#customize-control-customize_presets_settings .loginpress_thumbnail:hover{
  border-color: #ccc;
}

@-webkit-keyframes checkmark {
  0% {
    height: 0;
    width: 0;
    opacity: 1;
  }
  20% {
    height: 0;
    width: 5px;
    opacity: 1;
  }
  40% {
    height: 10px;
    width: 5px;
    opacity: 1;
  }
  100% {
    height: 10px;
    width: 5px;
    opacity: 1;
  }
}
@-moz-keyframes checkmark {
  0% {
    height: 0;
    width: 0;
    opacity: 1;
  }
  20% {
    height: 0;
    width: 5px;
    opacity: 1;
  }
  40% {
    height: 10px;
    width: 5px;
    opacity: 1;
  }
  100% {
    height: 10px;
    width: 5px;
    opacity: 1;
  }
}
@keyframes checkmark {
  0% {
    height: 0;
    width: 0;
    opacity: 1;
  }
  20% {
    height: 0;
    width: 5px;
    opacity: 1;
  }
  40% {
    height: 10px;
    width: 5px;
    opacity: 1;
  }
  100% {
    height: 10px;
    width: 5px;
    opacity: 1;
  }
}

  </style>
  <?php
}
add_action( 'customize_controls_print_styles', 'loginpress_presets_control_css' );
?>
