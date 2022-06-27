
<?php
function tenth_presets() {
  ob_start();
  if ( version_compare( $GLOBALS['wp_version'], '5.3', '>=' ) ) : ?>
  <style media="screen"  id="loginpress-style-wp-5-3">
    .login form .input, .login input[type=text],.login form input[type=checkbox]{
        border-radius: 0;
    }
    input[type=checkbox]:checked:before {
        content: url('data:image/svg+xml;utf8,<svg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27><path%20d%3D%27M14.83%204.89l1.34.94-5.81%208.38H9.02L5.78%209.67l1.34-1.25%202.57%202.4z%27%20fill%3D%27%236856be%27%2F><%2Fsvg>');
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
        background-color: #6856be;
        border: 0;
        margin-bottom: 8px;
        border-radius: 3px;
    }
    .admin-email__actions-primary .button:first-child:hover{
        background-color: #4d5d95;
    }
    .login form p.admin-email__details, .login h1.admin-email__heading, .login #backtoblog a{
        color: #444 !important;
    }
    .login form.admin-email-confirm-form{
        max-width: 360px;
        margin: 0 auto;
    }
  </style>
  <?php else: ?>
    <style>
      input[type=checkbox]:checked:before {
        content: '\f147';
        color: #6856be;
        font-size: 18px;
      }
    </style>
<?php endif; ?>
  <style media="screen" id="loginpress-style">
    html, body.login {
      height: auto !important;
    }
    body.login {
      /*background-image: url(<?php echo plugins_url( 'img/bg3.jpg', LOGINPRESS_PRO_PLUGIN_BASENAME )  ?>);
      background-position: center center;*/
      background-color: #f5f7fa !important;
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
      color: #404040;
    }
    #login{
      background: #ffffff !important;
      padding: 5vh 30px 30px;
      max-width: calc(100vw - 120px) !important;
      width: calc(100vw - 120px) !important;
      border-radius: 10px 10px 10px 10px;
      box-shadow: 0px 5px 50px 0px rgba(0, 0, 0, 0.1);
      position: relative;
      margin: 60px auto;
      padding-right: calc(58vw - 60px);
      overflow: hidden;
      /*height: calc(100vh - 160px);*/
      min-height: 560px;
    }
    #login:after{
      content: '';
      width: calc(58vw - 90px);
      position: absolute;
      right: 0;
      top: 0;
      height: 100%;
      background-image: url(<?php echo plugins_url( 'img/bg10.jpg', LOGINPRESS_PRO_PLUGIN_BASENAME )  ?>);
      background-repeat: no-repeat;
      background-position: center center;
      background-size: cover;
      display: block !important;
    }
    .mobile #login{
      padding-left: 30px;
      padding-right: calc(58vw - 60px);
    }

    #loginform{
      margin: 0;
      padding: 0 !important;
    }
    #login form p + p:not(.forgetmenot), .user-pass-wrap{
    margin-top: 35px;
    }
    .login form .input, .login input[type=text]{
      background: #fff;
      display: block;
      color: #404040;
      font-size: 16px;
      width:100%;
      border:0;
      height: 50px;
      padding: 0 15px;
      box-shadow: none;
      border-bottom:1px solid #d9d9d9;
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
      background-color : #6856be;
      font-weight: 700;
      font-size: 18px;
      color : #ffffff;
      height: 56px;
      border-radius: 0;
      border:0;
      box-shadow: none;
    }
    .wp-core-ui #login  .button-primary:hover{
      background-color: #4d5d95;
    }
    .login form .forgetmenot label{
      font-size: 13px;
      color: #606060;
    }
    .login form input[type=checkbox]{
      background: none;
      border: 1px solid #d9d9d9;
      height: 13px;
      width: 13px;
      min-width: 13px;
    }
    .login #nav, .login #backtoblog {
      margin: 17px 0 0;
      padding: 0;
    color: #606060;
    }
    .login #nav{
      float: none;
      font-size: 0;
      max-width: 360px;
      width: 100%;
      margin-left: auto;
      margin-right: auto;
    }
    .login #nav:after{
      content: '';
      display: table;
      clear: both;
    }
    .login #nav a, .login #backtoblog a{
    color: #606060;
      font-size: 13px;
    }
    .login #nav a:first-child{
      float: left;
    }
    .login #nav a:last-child{
      float: right;
    }
    .login #backtoblog{
      float: none;
      max-width: 360px;
      width: 100%;
      margin-left: auto;
      margin-right: auto;
    }
    .login #backtoblog a:hover, .login #nav a:hover, .login h1 a:hover{
      color: #222;
    }
    /* style two factor plugin */
    .login .backup-methods-wrap a, #login form p:not([class]){
      color: #606060;
    }
    .login .backup-methods-wrap a:hover{
      color: #222;
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
      background-color: #a475c3;
      color: #ffffff;
    }
    #login form p + p:not(.forgetmenot){
    color: #606060;
    }
    .loginpress-show-love{
      color: #6856be;
    }
    .loginpress-show-love a{
      color: #5f4ca5;
    }
    .loginpress-show-love a:hover{
      color: #fff;
    }
    #login_error, .login .message, #loginform{
      max-width: 360px;
      width: 100%;
      margin-left: auto;
      margin-right: auto;
    }
    .login #login_error{
      margin-left: auto;
      margin-right: auto;
    }
    .mobile #login #backtoblog, .mobile #login #nav{
      margin-left: auto;
    }
    .login .custom-message{
      width: 360px;
      margin-left: auto !important;
      margin-right: auto !important;
    }
    @media screen and (min-width: 1240px) {
      .login #login{
        max-width: 1200px !important;
        width: 100% !important;
        margin-left: auto;
        margin-right: auto;
        padding-right: 600px;
      }
      #login:after{
        width: 600px;
      }
    }
    @media screen and (max-width: 767px) {
    #login:after{
      display: none;
    }
    #login{
      position: static;
      margin: 20px auto;
      padding-right: 15px;
      padding-left: 15px;
      max-width: 360px !important;
      width: 290px !important;
    }

    .mobile #login{
      padding: 15px 15px 0 15px;
      width: 100%;
    }
    #login:after{
      position: static;
      height: 200px;
      width: calc(100% + 30px);
      margin-left: -15px;
      margin-top: 20px;
      display: none !important;
    }
    .login .loginpress-show-love{
      position: relative;
      float: none;
      clear: both;
      text-align: center;
      padding: 3px 10px;
      margin-top: 10px;
    }

    }

    </style>

  <?php
  $content = ob_get_clean();
  return $content;
}
echo tenth_presets();
