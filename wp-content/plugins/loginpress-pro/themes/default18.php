
<?php
function eleventh_presets() {
  ob_start();
  if ( version_compare( $GLOBALS['wp_version'], '5.3', '>=' ) ) : ?>
    <style media="screen"  id="loginpress-style-wp-5-3">
      .login form .input, .login input[type=text],.login form input[type=checkbox]{
          border-radius: 0;
      }
      input[type=checkbox]:checked:before {
          content: url('data:image/svg+xml;utf8,<svg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27><path%20d%3D%27M14.83%204.89l1.34.94-5.81%208.38H9.02L5.78%209.67l1.34-1.25%202.57%202.4z%27%20fill%3D%27%23174cc3%27%2F><%2Fsvg>');
      }
      .login .button.wp-hide-pw{
          display: none;
      }
      .user-pass-wrap{
          position: relative;
      }
      #loginpress_showPasswordWrapper{
          position: absolute;
          bottom: 0;
          right: 0;
          top: auto;
          width: 45px;
          height: 45px;
          display: flex;
          justify-content: center;
          align-content: center;
          align-items: center;
      }
      .login .button.wp-hide-pw{
          display: none !important;
      }
      #loginpress_showPasswordWrapper #loginpress_showPasswordCheck:checked + #loginpress_showPasswordToggle .dashicons:before {
          content: "\f530";
      }
      #loginpress_showPasswordToggle{
          color: #0071a1;
      }
      #loginpress_showPasswordWrapper #loginpress_showPasswordCheck{
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          opacity: 0;
          z-index: 1000;
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
          background-color: #174cc3;
          border: 0;
          margin-bottom: 8px;
          border-radius: 0;
      }
      .admin-email__actions-primary .button:first-child:hover{
          background-color: #3871f1;
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
        color: #174cc3;
        font-size: 18px;
      }
      #loginpress_showPasswordWrapper{
        position: absolute;
        top: 0;
        right: 0;
        text-align: right;
      }
    </style>
<?php endif; ?>
  <style media="screen" id="loginpress-style">

  html, body.login {
    height: auto !important;
  }
    body.login {
      background-image: url(<?php echo plugins_url( 'img/bg18.jpg', LOGINPRESS_PRO_PLUGIN_BASENAME )  ?>);
      background-position: center center;
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
    }
    label[for="user_pass"]{
      display: block;
      position: relative;
    }
    #loginpress_showPasswordToggle{
      font-size: 13px;
    }
    #loginpress_showPasswordWrapper input[type="checkbox"]{
      /* margin: 0; */
      margin: 1px 6px 0 0;
    }
    #login{
      /*background: url(img/form_bg.jpg) no-repeat 0 0 !important;*/
      background: none;
      max-width: 350px !important;
      width: 100% !important;
      border-radius: 10px;
      margin-top: 8%;
      padding: 40px 20px 50px;
      margin-right: auto;
      margin-left: auto;
    }
    #login h1 a{
      width: 200px !important;
      height: 200px !important;
      text-indent: 0;
      border-radius: 50%;
      border:solid 2.5px #217093;
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
      background: none;
      display: block;
      color: #fff;
      font-size: 16px;
      width:100%;
      border:0;
      height: 45px;
      padding: 0 15px;
      border-bottom:1px solid #716e6b;
      -webkit-box-shadow: none;
      box-shadow: none;
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
      background-color : #174cc3;
      font-weight: 700;
      font-size: 18px;
      color : #ffffff;
      height: 56px;
      border-radius: 0;
      border:0;
      box-shadow: none;
      border-radius: 0;
      box-shadow: 0px 5px 20px 0px rgb( 255, 255, 255 , .20);
    }

    .wp-core-ui #login  .button-primary:hover{
      background-color: #3871f1;
    }
    .login form .forgetmenot label{
      font-size: 13px;
      color: #fff;
    }
    .login form input[type=checkbox]{
      background: none;
      border: 1px solid #fff;
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
      color: #fff;
    }
    .login #nav a, .login #backtoblog a{
      font-size: 13px;
      color: #fff;
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
      color: #a9ddf3;
    }
    /* style two factor plugin */
    .login .backup-methods-wrap a, #login form p:not([class]){
      color: #929292;
    }
    .login .backup-methods-wrap a:hover{
      color: #a9ddf3;
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
      background-color: #174cc3;
      color: #ffffff;
    }
    #login form p + p:not(.forgetmenot){
    color: #d5d5d5;
    }
    .loginpress-show-love{
      color: #929292;
    }
    .loginpress-show-love a{
      color: #fff;
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
      .mobile #login{
        padding: 15px;
        float: none;
        margin: 20px auto;
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
echo eleventh_presets();
