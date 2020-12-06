
<?php
function eighth_presets() {
  ob_start();
  if ( version_compare( $GLOBALS['wp_version'], '5.3', '>=' ) ) : ?>
  <style media="screen"  id="loginpress-style-wp-5-3">
    .login form .input, .login input[type=text],.login form input[type=checkbox]{
        border-radius: 0;
    }
    input[type=checkbox]:checked:before {
        content: url('data:image/svg+xml;utf8,<svg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27><path%20d%3D%27M14.83%204.89l1.34.94-5.81%208.38H9.02L5.78%209.67l1.34-1.25%202.57%202.4z%27%20fill%3D%27%23fc6ec7%27%2F><%2Fsvg>');
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
        background-color: #fc6ec7;
        border: 0;
        margin-bottom: 8px;
        border-radius: 0;
    }
    .login form p.admin-email__details, .login h1.admin-email__heading{
        color: #444 !important;
    }
    .login form.admin-email-confirm-form{
        max-width: 360px;
        margin: 0 auto;
    }
    .login-action-confirm_admin_email #login{
        background: none;
        max-width: 460px;
    }
    .login-action-confirm_admin_email #backtoblog a {
        color: #444 !important;
    }
    .admin-email__actions-primary .button:first-child:hover{
        background-color: #ff8bd4;
    }
  </style>
  <?php else: ?>
    <style>
      input[type=checkbox]:checked:before {
        content: '\f147';
        color: #fc6ec7;
        font-size: 18px;
      }
    </style>
<?php endif; ?>
  <style media="screen" id="loginpress-style">

    body.login {
      /*background-image: url(<?php //echo plugins_url( 'img/bg3-girl.jpg', LOGINPRESS_PRO_PLUGIN_BASENAME )  ?>);
      background-size: cover;*/
      position: relative;
      min-height: 700px;
    }
    body.login:before{
      width: 50%;
      top: 0;
      left:0;
      height: 100%;
      background: #f9f9f9;
      content: '';
      position: absolute;
    }
    body.login:after{
      width: 50%;
      top: 0;
      right:0;
      height: 100%;
      background: url(<?php echo plugins_url( 'img/bg8.jpg', LOGINPRESS_PRO_PLUGIN_BASENAME )  ?>) no-repeat 0 0;
      -webkit-background-size: cover;
      background-size: cover;
      content: '';
      position: absolute;
    }
    .login label{
      margin-top: 0;
      display: block;
      color: #909090;
    }
    #login{
      position: relative;
      left: 25%;
      padding: 0;
      z-index: 12;
      max-height: 100%;
      width: 100%;
      max-width: 380px;
      padding-right: 15px;
      align-items: center;
      padding-left: 15px;
      margin: 0;
      /* justify-content: center; */
      transform: translateX(-50%);
      padding-bottom: 100px;
      padding-top: 100px;
    }
    #login form p + p:not(.forgetmenot), .user-pass-wrap{
      margin-top: 35px;
    }
    .login form .input, .login input[type=text]{
      background: none;
      display: block;
      color: #909090;
      font-size: 16px;
      width:100%;
      border:0;
      height: 45px;
      padding: 0 15px;
      border:1px solid #909090;
      box-shadow: none;
      background: #ffffff;
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
      background-color : #fc6ec7;
      font-weight: 700;
      font-size: 18px;
      color : #ffffff;
      height: 56px;
      border-radius: 0;
      border:0;
      box-shadow: none;
    }
    .wp-core-ui #login  .button-primary:hover{
      background-color: #ff8bd4;
    }
    .login form .forgetmenot label{
      font-size: 13px;
      /*font-family: 'Open Sans';*/
      color: #909090;
    }
    .login form input[type=checkbox]{
      background: none;
      border: 1px solid #909090;
      height: 13px;
      width: 13px;
      min-width: 13px;
      background: #fff;
    }
    .login #nav{
      font-size: 0;
      padding-left: 0;
      padding-right: 0;
    }
    .login #nav a, .login #backtoblog a{
      color: #909090;
      font-size: 13px;
    }
    .login #nav a:first-child{
      float: left;
    }
    .login #nav a:last-child{
      float: right;
    }
    .login #backtoblog{
      float: left;
      padding-left: 0;
      padding-right: 0;
    }
    .login #nav{
      width: 100%;
      float: right;
    }
    .login #backtoblog a:hover, .login #nav a:hover, .login h1 a:hover{
      color: #fc6ec7;
    }
    /* style two factor plugin */
    .login .backup-methods-wrap a, #login form p:not([class]){
      color: #909090;
    }
    .login .backup-methods-wrap a:hover{
      color: #fc6ec7;
    }
    /*End style two factor plugin */
  .footer-wrapper{
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 3;
  }
  .footer-cont{
    right: 0;
    bottom: 0;
    left: 0;
    text-align: center;
    width: 100vw;
    /* height: 40px; */
  }
  .copyRight{
    text-align: center;
    padding: 12px;
    background-color: #fc6ec7;
    color: #ffffff;
  }
  .loginpress-show-love{
    color: #fff;
  }
  .loginpress-show-love a{
    color: #fff;
  }
  .loginpress-show-love a:hover{
    color: #fc6ec7;
  }
  html[dir="rtl"] body.login {
    align-items: flex-end;
}
  @media screen and (max-width: 767px) {
      #login,html[dir="rtl"] #login{
      position: relative;
      -webkit-transform: translate(0);
      -ms-transform: translate(0);
      -o-transform: translate(0);
      transform: translate(0);
      margin: 0 auto;
      padding-top: 3%;
      left: 0;
      top: 0;
      padding-left: 15px !important;
      padding-right: 15px !important;
      right: 0;
      }
      #login:after{
        content: '';
        display: table;
        clear: both;
      }
      body.login:after{
      width: 100%;
      opacity: .7;
      }
      body.login:before{
        width: 100%;
      }
      #login form p + p:not(.forgetmenot){
        padding-top: 0;
      }
      .login .loginpress-show-love{
        position: relative;
        float: none;
        clear: both;
        text-align: center;
      }
      html[dir="rtl"] body.login:after,html[dir="rtl"] body.login:before{
        width: 100%;
      }
  }
  @media screen and (min-height: 701px) {
  #login{
    margin-left: 0;
  }
  #login:after{
    content: '';
    display: table;
    clear: both;
  }
  body.login{
    display: flex;
    justify-content: center;
    flex-direction: column;
    height: auto;
    min-height: 100vh;
  }
  html{
    height: auto;
  }

}
  @media screen and (max-height: 700px) {
    html body.login{
      height: 100%;
      position: static;
      min-height: unset;
    }
    html{
      min-height: 100%;
      position: relative;
      height: auto;
      padding-bottom: 70px;
    }
  }
  </style>
  <script>
  // (function($){
  // 	$(document).ready(function(){
  // 		if($(window).height()>700){
  // 			$('body').css('height',$('#login').outerHeight());
  // 		}else{
  // 			$('body').removeAttr('style');
  // 		}
  // 	});
  // 	$(window).on('resize load',function(){
  // 		if($(window).height()>700){
  // 			$('body').css('height',$('#login').outerHeight());
  // 		}else{
  // 			$('body').removeAttr('style');
  // 		}
  // 	});
  // }(jQuery));
      function temp8(){
        if(window.innerHeight>700){
            document.getElementsByTagName('body')[0].style.height  = document.querySelector('#login').clientHeight + 'px';
        }else{
          document.getElementsByTagName('body')[0].removeAttribute('style');
        }
      }
      window.addEventListener ? window.addEventListener( "load", temp8, false ) : window.attachEvent && window.attachEvent( "onload", temp8 );
      window.addEventListener ? window.addEventListener( "resize", temp8, false ) : window.attachEvent && window.attachEvent( "onresize", temp8 );
  </script>
  <?php
  $content = ob_get_clean();
  return $content;
}
echo eighth_presets();
