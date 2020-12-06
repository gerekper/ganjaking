<!-- Style for activate Page -->
<style media="screen">

/*=============================================>>>>>
= Loginpress installation =
===============================================>>>>>*/
/**
*
* main container
*
*/
.loginpress-main-container{
  font-family: "Segoe UI", Frutiger, "Frutiger Linotype", "Dejavu Sans", "Helvetica Neue", Arial, sans-serif;
}
.loginpress-main-container *{
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}
.loginpress-plugin-info{
  text-align: center;
  padding: 5% 0;
  font-size: 30px;
  color: #23282d;
}
.loginpress-installation{
  background-color: #f1f1f1;
  border:1px solid #d8d8d8;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  -ms-border-radius: 4px;
  -o-border-radius: 4px;
  border-radius: 4px;
  max-width: 488px;
  min-height: 365px;
  text-align: center;
  margin: 0 auto;
  padding: 30px;
}
.loginpress-install{
  display: table-cell;
  vertical-align: middle;
  height: 305px;
  width: 428px;
}
.loginpress-install img{
  margin: 0 0 10px;
}
.loginpress-install p{
  font-family: "Segoe UI", Frutiger, "Frutiger Linotype", "Dejavu Sans", "Helvetica Neue", Arial, sans-serif;
  font-size: 14px;
  line-height: 18px;
  color: #aaa;
}
.loginpress-install .loginpress-copyright{
  font-size: 16px;
}
.loginpress-install .loginpress-copyright a{
  color: #7a7a7a;
  text-decoration: none;
}
.loginpress-btn{
  cursor: pointer;
  border: 1px solid rgb(0, 103, 153);
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  -ms-border-radius: 2px;
  -o-border-radius: 2px;
  border-radius: 2px;
  background-color: #0085ba;
  -webkit-box-shadow: 0px 2px 0px 0px rgba(0, 103, 153, 0.004);
  box-shadow: 0px 2px 0px 0px rgba(0, 103, 153, 0.004);
  width: 216px;
  height: 47px;
  z-index: 6;
  margin: 20px auto 0;
  display: block;
  color: #feffff;
  font-size: 21px;
  line-height: 40px;;
  text-decoration: none;
}
.loginpress-logo-container{
  position: relative;
  width: 185px;
  height: 185px;
  text-align: center;
  line-height: 185px;
  margin: 0 auto;
}
.loginpress-logo-container svg{
  position: absolute;
  left: 0;
  top: 0;
}
.loginpress-logo-container img{
  vertical-align: middle;
}
.loader-path {
  stroke-dasharray: 150,200;
  stroke-dashoffset: -10;
  -webkit-animation: dash 1.5s ease-in-out infinite, color 6s ease-in-out infinite;
  animation: dash 1.5s ease-in-out infinite, color 6s ease-in-out infinite;
  stroke-linecap: round;
}
.activating p{
  font-size: 20px;
}
.activated p{
  font-size: 20px;
  color: #00c853;
}
@-webkit-keyframes rotate {
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@keyframes rotate {
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-webkit-keyframes dash {
  0% {
    stroke-dasharray: 1,200;
    stroke-dashoffset: 0;
  }
  50% {
    stroke-dasharray: 89,200;
    stroke-dashoffset: -35;
  }
  100% {
    stroke-dasharray: 89,200;
    stroke-dashoffset: -124;
  }
}
@keyframes dash {
  0% {
    stroke-dasharray: 1,200;
    stroke-dashoffset: 0;
  }
  50% {
    stroke-dasharray: 89,200;
    stroke-dashoffset: -35;
  }
  100% {
    stroke-dasharray: 89,200;
    stroke-dashoffset: -124;
  }
}
@-webkit-keyframes color {
  0% {
    stroke: #d8d8d8;
  }
  40% {
    stroke: #d8d8d8;
  }
  66% {
    stroke: #d8d8d8;
  }
  80%, 90% {
    stroke: #d8d8d8;
  }
}
@keyframes color {
  0% {
    stroke: #d8d8d8;
  }
  40% {
    stroke: #d8d8d8;
  }
  66% {
    stroke: #d8d8d8;
  }
  80%, 90% {
    stroke: #d8d8d8;
  }
}



.circle-loader {
  margin: 0 0 30px 0;
  border: 2px solid rgba(0, 0, 0, 0.2);
  border-left-color: #00c853;
  animation-name: loader-spin;
  animation-duration: 1s;
  animation-iteration-count: infinite;
  animation-timing-function: linear;
  position: relative;
  display: inline-block;
  vertical-align: top;
}
.circle-loader, .circle-loader:after {
  border-radius: 50%;
  width: 148px;
  height: 148px;
}
.load-complete {
  -webkit-animation: none;
  animation: none;
  border-color: #00c853;
  transition: border 500ms ease-out;
}
.checkmark {
  display: none;
}
.checkmark.draw:after {
  animation-duration: 800ms;
  animation-timing-function: ease;
  animation-name: checkmark;
  transform: scaleX(-1) rotate(135deg);
}
.checkmark:after {
  opacity: 1;
  height: 4em;
  width: 2em;
  transform-origin: left top;
  border-right: 2px solid #00c853;
  border-top: 2px solid #00c853;
  content: '';
  left: 42px;
  top: 70px;
  position: absolute;
}
@keyframes loader-spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
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
    width: 2em;
    opacity: 1;
  }
  40% {
    height: 4em;
    width: 2em;
    opacity: 1;
  }
  100% {
    height: 4em;
    width: 2em;
    opacity: 1;
  }
}

/*= End of Loginpress installation =*/
/*=============================================<<<<<*/
</style>
<div class="loginpress-main-container">
  <p class="loginpress-plugin-info"><?php esc_html_e( 'LoginPress - Rebranding your boring WordPress Login pages', 'loginpress-pro' ); ?></p>
  <form action="#" method="post" class="loginpress-installation">
    <div id="loginpressInstallingFree" class="loginpress-install activating" style="display:none;">
      <div class="loginpress-logo-container">
        <img src="<?php echo plugins_url( '../img/loginpress-logo2.png', __FILE__ );?>" alt="loginpress">
        <svg class="circular-loader"viewBox="25 25 50 50" >
          <circle class="loader-path" cx="50" cy="50" r="20" fill="none" stroke="#d8d8d8" stroke-width="1" />
        </svg>
      </div>
      <p><?php esc_html_e( 'Downloading LoginPress...', 'loginpress-pro' ); ?></p>
    </div>
    <div id="loginpressActivatingFree" class="loginpress-install activating" style="display:none;">
      <div class="loginpress-logo-container">
        <img src="<?php echo plugins_url( '../img/loginpress-logo2.png', __FILE__ );?>" alt="loginpress">
        <svg class="circular-loader"viewBox="25 25 50 50" >
          <circle class="loader-path" cx="50" cy="50" r="20" fill="none" stroke="#d8d8d8" stroke-width="1" />
        </svg>
      </div>
      <p><?php esc_html_e( 'Activating LoginPress...', 'loginpress-pro' ); ?></p>
    </div>
    <!-- .loginpress-install activating-->
    <div id="loginpressActivatedFree" class="loginpress-install activated" style="display:none">

      <div class="circle-loader">
        <div class="checkmark draw"></div>
      </div>
      <p><?php esc_html_e( 'LoginPress Activated.', 'loginpress-pro' ); ?></p>
    </div>
    <!-- .loginpress-install activated-->
    <?php
    if ( ! file_exists( WP_PLUGIN_DIR . '/loginpress/loginpress.php' ) ) {
      add_action( 'admin_notices', 'lp_install_free' );
      ?>
      <div id="loginpressInstallFree" class="loginpress-install">
        <img src="<?php echo plugins_url( '../img/loginpress-logo.png', __FILE__ );?>" alt="loginpress">
        <?php echo sprintf( __( '%1$sI am innovating WordPress login page. I will help you to customize your boring login page into a stylish login landing page.%2$s%1$sLoginPress (Free) is essential for Pro version. %3$sJust click the install button and enjoy the Pro Features.%2$s%4$sCreated by %5$sWPBrigade%6$s%2$s', 'loginpress-pro' ), '<p>', '</p>', '<br />', '<p class="loginpress-copyright">', '<a href="http://wpbrigade.com">', '</a>' ); ?>

        <input type="hidden" name="loginpress_free_nonce" value="<?php echo wp_create_nonce( 'updates' ); ?>">
        <button type="submit"  class="loginpress-btn"><?php esc_html_e( 'Install', 'loginpress-pro' ) ?></button>
      </div>
      <!-- .loginpress-install -->
      <?php
      return;
    }

    if ( ! class_exists( 'LoginPress' ) ) {
      add_action( 'admin_notices', 'lp_activate_free_plugin' ); ?>
      <div id="loginpressActiveFree" class="loginpress-install active">
        <input type="hidden" name="loginpress_active_free_nonce" value="<?php echo wp_create_nonce( 'active_free' ); ?>">
        <img src="<?php echo plugins_url( '../img/loginpress-logo.png', __FILE__ );?>" alt="loginpress">
        <p><?php echo sprintf( __( 'LoginPress (Free) is essential for Pro version. %1$sJust click the Activate button and enjoy the Pro Features.', 'loginpress-pro' ), '<br />' ); ?></p>
        <button type="submit" href="#" class="loginpress-btn"><?php esc_html_e( 'Activate', 'loginpress-pro' ) ?></button>
      </div>
      <!-- .loginpress-install active-->
      <?php
      return;
    }
    ?>

  </form>

</div>
