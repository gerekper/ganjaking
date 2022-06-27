
<?php
function second_presets() {
  ob_start();
  if ( version_compare( $GLOBALS['wp_version'], '5.3', '>=' ) ) : ?>
  <style media="screen"  id="loginpress-style-wp-5-3">
    .login form .input, .login input[type=text],.login form input[type=checkbox]{
      border-radius: 0;
    }
    .language-switcher{
      clear: both;
      position: relative;
      z-index: 1;
    }
    input[type=checkbox]:checked:before {
      content: url('data:image/svg+xml;utf8,<svg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27><path%20d%3D%27M14.83%204.89l1.34.94-5.81%208.38H9.02L5.78%209.67l1.34-1.25%202.57%202.4z%27%20fill%3D%27%23f78f1e%27%2F><%2Fsvg>');
    }
    .login-action-confirm_admin_email #login{
        background-color: transparent;
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
        background-color: #f78f1e;
        border: 0;
        margin-bottom: 8px;
        border-radius: 0;
    }
    .admin-email__actions-primary .button:first-child:hover{
        background-color: #fff;
        color: #f78f1e;
    }
    .login form p.admin-email__details, .login h1.admin-email__heading, .login #backtoblog a{
        color: #fff !important;
    }
    .login form.admin-email-confirm-form{
        max-width: 360px;
        margin: 0 auto;
    }
    .admin-email__actions-secondary a, .login .admin-email__details a{
        color: #d5d5d5;
    }
  </style>
  <?php else: ?>
    <style>
      input[type=checkbox]:checked:before {
        content: '\f147';
        margin: -5px 0 0 -6px;
        color: #f78f1e;
      }
    </style>
<?php endif; ?>
  <style media="screen" id="loginpress-style">
    html, body.login {
      height: auto !important;
    }
    body.login {
      background-image: url(<?php echo plugins_url( 'img/bg2.jpg', LOGINPRESS_PRO_PLUGIN_BASENAME )  ?>);
      background-size: cover;
      display: table  !important;
      min-height: 100vh;
      width: 100%;
      padding: 0;
    }
    body.login.login-action-login{
      display: table  !important;
    }
    body.login.login-action-login.firefox{
      height: 1px !important;
    }
    .login label{
    font-size:0;
    line-height:0;
    margin-top: 0;
    display: block;
    margin-bottom:
    }

    .login label{
      margin: 0;
    }
    .login .forgetmenot label{
    display: inline-block;
}
    #login form .user-pass-wrap{
    margin-top: 35px;
    }
    .login form .input, .login input[type=text]{
      background: rgba(255,255,255,.2);
      display: block;
      color: #fff;
      font-size: 16px;
      width:100%;
      border:0;
      height: 50px;
      padding: 0 15px;
    }
    .login form{
      background: none;
      padding: 0;
      box-shadow: none;
    }
    .login form br{
    display: none;
    }
    #login{
      width: calc(100% - 30px) !important;
      max-width: 360px !important;
    }
    #login form p.submit{
      clear: both;
      padding-top: 35px;
    }
    .wp-core-ui #login  .button-primary{
      width:100% !important;
      display: block;
      float: none;
      background-color : #f78f1e;
      font-weight: 700;
      font-size: 18px;
      /*font-family: "Roboto", sans-serif;*/
      color : #ffffff;
      height: 56px;
      border-radius: 0;
      border:0;
      box-shadow: none;
    }
    .wp-core-ui #login  .button-primary:hover{
      background-color: #fff;
      color : #f78f1e;
    }
    .login form .forgetmenot label{
      font-size: 13px;
      color: #d5d5d5;
    }
    .login form input[type=checkbox]{
      background: none;
      border: 1px solid #d5d5d5;
      height: 13px;
      width: 13px;
      min-width: 13px;
      border-radius: 0;
    }
    .login #nav, .login #backtoblog {
      margin: 17px 0 0;
      padding: 0;
      font-size: 14px;
      color: #d5d5d5;
    }
    .login #nav a, .login #backtoblog a{
      font-size: 14px;
      color: #d5d5d5;
    }
    .login #nav {
      font-size: 0;
      float: right;
      width: 100%;
      padding: 0 24px;
    }
    .login #nav a:last-child {
      float: right;
    }
    .login #nav a:first-child {
      float: left;
    }
    .login #backtoblog{
      float: left;
      padding: 0 24px;
    }
    .login #backtoblog a:hover, .login #nav a:hover, .login h1 a:hover{
      color: #fff;
    }
    /* style two factor plugin */
    .login .backup-methods-wrap a, #login form p:not([class]){
      color: #d5d5d5;
    }
    .login .backup-methods-wrap a:hover{
        color: #fff;
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
      background-color: #303030;
      color: #fff;
    }
    #login form p + p:not(.forgetmenot){
    color: #d5d5d5;
    }
    .loginpress-show-love{
      color: #fff;
    }
    .loginpress-show-love a{
      color: #fff;
    }
    .loginpress-show-love a:hover{
      color: #f78f1e;
    }
    @media screen and (max-width: 767px) {
      .login #nav{
        text-align: center;
        width: 100%;
        float: none;
      }
      .login #backtoblog{
        text-align: center;
        width: 100%;
        float: none;
        clear: both;
        padding-top: 11px;
      }
      .login .loginpress-show-love{
        position: static;
        text-align: center;
        float: none;
        background: rgba(255,255,255, .5);
        margin-top: 11px;
        padding-bottom: 0;
        padding: 3px;
      }
      #login{
        width: 290px !important;
      }
    }
    </style>

  <?php
  $content = ob_get_clean();
  return $content;
}
echo second_presets();
