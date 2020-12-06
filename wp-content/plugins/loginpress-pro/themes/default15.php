
<?php
function fifteenth_presets() {
  ob_start();
  if ( version_compare( $GLOBALS['wp_version'], '5.3', '>=' ) ) : ?>
  <style media="screen"  id="loginpress-style-wp-5-3">
    .login form .input, .login input[type=text],.login form input[type=checkbox]{
      border-radius: 0;
    }
    input[type=checkbox]:checked:before {
      content: url('data:image/svg+xml;utf8,<svg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27><path%20d%3D%27M14.83%204.89l1.34.94-5.81%208.38H9.02L5.78%209.67l1.34-1.25%202.57%202.4z%27%20fill%3D%27%23ff7d69%27%2F><%2Fsvg>');
    }
    .login label{
      display: block;
      position: static;
    }
    .login label:after{
      bottom: 0;
      z-index: 1;
    }
    #login form p:not([class]){
      position: relative;
    }
    .user-pass-wrap{
      position: relative;
    }
    .login .button.wp-hide-pw{
      z-index: 1;
    }
    .user-pass-wrap{
        position: relative;
        margin-top: 35px;
    }
    .login form .forgetmenot{
        float: left !important;
    }
    .wp-core-ui #login .button-primary{
        margin-left: 0;
    }
    .admin-email__actions-primary .button:first-child{
        font: 700 18px "Roboto", sans-serif;
        color: #fff;
        height: auto;
        line-height: 20px !important;
        padding: 13px;
        padding-top: 13px;
        padding-bottom: 13px;
        width: 100%;
        text-align: center;
        background-color: #ff7d69;
        border: 0;
        margin-bottom: 8px;
        border-radius: 3px;
    }
    .admin-email__actions-primary .button:first-child:hover{
        background-color: #f98b7a;
    }
    .login form p.admin-email__details, .login h1.admin-email__heading, .login #backtoblog a{
        color: #fff !important;
    }
    .login form.admin-email-confirm-form{
        max-width: 360px;
        margin: 0 auto;
    }
    .admin-email__actions-secondary a, .login .admin-email__details a{
        color: #fff;
    }
  </style>
  <?php else: ?>
    <style>
      input[type=checkbox]:checked:before {
        content: '\f147';
        color: #ff7d69;
        font-size: 18px;
      }
      .login label{
        position: relative;
      }
    </style>
<?php endif; ?>
  <style media="screen" id="loginpress-style">

    html, body.login {
      height: auto !important;
    }
    body.login {
      background-image: url(<?php echo plugins_url( 'img/bg15.jpg', LOGINPRESS_PRO_PLUGIN_BASENAME )  ?>);
      background-position: left bottom !important;
      /*background-color: #f1f1f1 !important;*/
      background-size: cover;
      display: table;
      min-height: 100vh;
      width: 100%;
      padding: 0;
      position: relative;
    }
    body.login.login-action-login{
      display: table  !important;

    }
    body.login.login-action-login.firefox{
      height: 1px !important;
    }

    /*body.login:after{
      width: 100%;
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      height: 60%;
      background: #263466;
    }*/
    /*.login label{
    font-size:0;
    line-height:0;
    margin-top: 0;
    display: block;
    margin-bottom:
    }*/
    .login label{
      font-size: 16px;
      color: #fff;
      display: block;
    }
    #login{
      /*background: url(img/form_bg.jpg) no-repeat 0 0 !important;*/
      background: none !important;
      max-width: 350px !important;
      width: 100% !important;
      border-radius: 10px;
      margin-top: 8%;
      padding: 40px 20px 50px;
    }
    #loginform{
      margin: 0 auto;
      padding: 30px 0 0 !important;
    }
    #login:after{
      content: '';
      display: table;
      clear: both;
    }
    #login form p + p:not(.forgetmenot){
      margin-top: 35px;
    }
    .login form .input, .login input[type=text]{
      display: block;
      color: #606060;
      font-size: 16px;
      width:100%;
      border:0;
      height: 45px;
      padding: 0 15px;
      border-radius: 3px;
      -webkit-box-shadow: none;
      box-shadow: none;
      padding-right: 80px;
      background-color: #fff;
      margin-top: 10px !important;
      border: 1px solid #e4e4e3;
    }
    input:-webkit-autofill{
      transition: all 100000s ease-in-out 0s !important;
      transition-property: background-color, color !important;
    }
    .login form{
      background: none;
      padding: 0;
      box-shadow: none;
    }
    .login form br{
    display: none;
    }
    #login form p.submit{
      clear: both;
      padding-top: 35px;
    }
    .wp-core-ui #login  .button-primary{
      width:100% !important;
      display: block;
      float: none;
      background-color : #ff7d69;
      font-weight: 700;
      font-size: 18px;
      color : #ffffff;
      height: 56px;
      border:0;
      box-shadow: none;
      border-radius: 3px;
      box-shadow: 0px 5px 20px 0px rgb( 255, 255, 255 , .20);
    }
    .login label:after{
      width: 60px;
      height: 45px;
      background-color: #fff;
      position: absolute;
      bottom: 0;
      right: 0;
      border-radius:0 5px 5px 0;
      content: '';
    }
    .login label[for="user_login"]:after,
    .login label[for="user_pass"]:after{
      background-image: url(<?php echo plugins_url( 'img/login-field-icons.svg', LOGINPRESS_PLUGIN_BASENAME )  ?>);
    }
    .login label[for="user_login"]:after{
      background-repeat: no-repeat;
      background-position: 0 center;
    }
    .login label[for="user_pass"]:after{
      background-repeat: no-repeat;
      background-position: -60px center;
    }
    .login form .forgetmenot label:after{
      visibility: hidden;
    }

    .wp-core-ui #login  .button-primary:hover{
      background-color: #f98b7a;
    }
    .login form .forgetmenot label{
      font-size: 13px;
      color: #ffffff;
    }
    .login form input[type=checkbox]{
      border: 1px solid #ffffff;
      background: #ffffff;
      height: 13px;
      width: 13px;
      min-width: 13px;
    }
    .login #nav{
      font-size: 0;
      float: right;
      width: 100%;
    }
    .login #nav, .login #backtoblog {
      margin: 17px 0 0;
      padding: 0;
      color: #ffffff;
    }
    .login #nav a, .login #backtoblog a{
      font-size: 13px;
      color: #ffffff;
    }
    .login #nav a:first-child{
      float: left;
    }
    .login #nav a:last-child{
      float: right;
    }
    .login #backtoblog{
      float: left;
    }
    .login #backtoblog a:hover, .login #nav a:hover, .login h1 a:hover{
      color: #eae8e8;
    }
    /* style two factor plugin */
    .login .backup-methods-wrap a, #login form p:not([class]){
      color: #fff;
    }
    .login .backup-methods-wrap a:hover{
      color: #eae8e8;
    }
    /*End style two factor plugin */
    .footer-wrapper{
    	display: table-footer-group;
    }
    .footer-cont{
    	right: 0;
    	bottom: 0;
    	left: 0;
    	text-align: center;
    	display: table-cell;
    	vertical-align: bottom;
    	height: 100px;
      width: 100vw;
    }
    .copyRight{
    	text-align: center;
      padding: 12px;
      background-color: #ff7d69;
      color: #ffffff;
    }
    #login form p + p:not(.forgetmenot){
    color: #d5d5d5;
    }
    .loginpress-show-love{
      color: #fff;
    }
    .loginpress-show-love a{
      color: #eae8e8;
    }
    .loginpress-show-love a:hover{
      color: #fff;
    }
    .mobile #login{
      padding: 15px;
    }
    @media screen and (max-width: 768px) {
       #login{
        padding: 15px;
        float: none;
        margin: 20px auto;
        width: 290px !important;
      }
      .login .loginpress-show-love{
        position: static;
        padding: 3px 15px;
        text-align: center;
        float: none;
      }
      .mobile #login{
        padding: 15px;
        float: none;
        margin: 20px auto;
      }
    }

    </style>

  <?php
  $content = ob_get_clean();
  return $content;
}
echo fifteenth_presets();
