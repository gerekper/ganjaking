<?php
/**
 * LoginPress optout Content.
 * @package LoginPress
 * @version 1.1.14
 */

$loginpress_optout_nonce = wp_create_nonce('loginpress-optout-nonce');
?>
<style media="screen">
.loginpress-modal.active {
  display: block;
}
.loginpress-modal {
    position: fixed;
    overflow: auto;
    height: 100%;
    width: 100%;
    top: 0;
    z-index: 100000;
    display: none;
    background: rgba(0,0,0,0.6);
}
.loginpress-modal.active .loginpress-modal-dialog {
    top: 10%;
}
.loginpress-modal .loginpress-modal-dialog {
    background: transparent;
    position: absolute;
    left: 50%;
    margin-left: -298px;
    padding-bottom: 30px;
    top: -100%;
    z-index: 100001;
    width: 596px;
}
.loginpress-modal .loginpress-modal-header {
    border-bottom: #eeeeee solid 1px;
    background: #fbfbfb;
    padding: 15px 20px;
    position: relative;
    margin-bottom: -10px;
}
.loginpress-modal .loginpress-modal-body {
    border-bottom: 0;
}
.loginpress-modal .loginpress-modal-body, .loginpress-modal .loginpress-modal-footer {
    border: 0;
    background: #fefefe;
    padding: 20px;
}
.loginpress-modal .loginpress-modal-body>div {
    margin-top: 10px;
}
.loginpress-modal .loginpress-modal-body>div h2 {
    font-weight: bold;
    font-size: 20px;
    margin-top: 0;
}
.loginpress-modal .loginpress-modal-body p {
    font-size: 14px;
}
.loginpress-modal .loginpress-modal-footer {
    border-top: #eeeeee solid 1px;
    text-align: right;
}
.loginpress-modal .loginpress-modal-footer>.button:first-child {
    margin: 0;
}
.loginpress-modal .loginpress-modal-footer>.button {
    margin: 0 7px;
}
.loginpress-modal .loginpress-modal-body>div h2 {
    font-weight: bold;
    font-size: 20px;
    margin-top: 0;
}
.loginpress-modal .loginpress-modal-body h2 {
    font-size: 20px;
     line-height: 1.5em;
}
.loginpress-modal .loginpress-modal-header h4 {
    margin: 0;
    padding: 0;
    text-transform: uppercase;
    font-size: 1.2em;
    font-weight: bold;
    color: #cacaca;
    text-shadow: 1px 1px 1px #fff;
    letter-spacing: 0.6px;
    -webkit-font-smoothing: antialiased;
}

.loginpress-optout-spinner{
    display: none;
}
</style>


<div class="loginpress-modal loginpress-modal-opt-out">
  <div class="loginpress-modal-dialog">
    <div class="loginpress-modal-header">
      <h4><?php _e( 'Opt Out', 'loginpress' ); ?></h4>
    </div>
    <div class="loginpress-modal-body">
      <div class="loginpress-modal-panel active">
        <input type="hidden" class="loginpress_optout_nonce" name="loginpress_optout_nonce" value="<?php echo $loginpress_optout_nonce; ?>">
        <h2><?php _e( 'We appreciate your help in making the plugin better by letting us track some usage data.', 'loginpress' ); ?></h2>
        <div class="notice notice-error inline opt-out-error-message" style="display: none;">
          <p></p>
        </div>
        <p><?php echo sprintf( __( 'Usage tracking is done in the name of making %1$s LoginPress %2$s better. Making a better user experience, prioritizing new features, and more good things. We\'d really appreciate if you\'ll reconsider letting us continue with the tracking.', 'loginpress' ), '<strong>', '</strong>') ?></p>
        <p><?php echo sprintf( __( 'By clicking "Opt Out", we will no longer be sending any data to %1$s LoginPress%2$s.', 'loginpress' ), '<a href="https://wpbrigade.com" target="_blank">', '</a>' ); ?></p>
      </div>
    </div>
    <div class="loginpress-modal-footer">
      <form class="" action="<?php echo admin_url( 'plugins.php' ) ?>" method="post">
        <span class="loginpress-optout-spinner"><img src="<?php echo admin_url( '/images/spinner.gif' ); ?>" alt=""></span>
        <button type='submit' name='loginpress-submit-optout' id='loginpress_optout_button'  class="button button-secondary button-opt-out" tabindex="1"><?php _e( 'Opt Out', 'loginpress' ) ?></button>
        <button class="button button-primary button-close" tabindex="2"><?php _e( 'On second thought - I want to continue helping', 'loginpress' ); ?></button>
      </form>
    </div>
  </div>
</div>



<script type="text/javascript">

(function( $ ) {

  $(function() {
    var pluginSlug = 'loginpress';
    var optout_nonce = $('.loginpress_optout_nonce').val();
    // Code to fire when the DOM is ready.

    $(document).on('click', 'tr[data-slug="' + pluginSlug + '"] .opt-out', function(e){
        e.preventDefault();
        $('.loginpress-modal-opt-out').addClass('active');
    });

    $(document).on('click', '.button-close', function(event) {
      event.preventDefault();
      $('.loginpress-modal-opt-out').removeClass('active');
    });

    $(document).on('click','#loginpress_optout_button', function(event) {
      event.preventDefault();
      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
          action    : 'loginpress_optout_yes',
          security  : optout_nonce,
        },
        beforeSend: function(){
          $(".loginpress-optout-spinner").show();
          $(".loginpress-popup-allow-deactivate").attr("disabled", "disabled");
        }
      })
      .done(function() {
        $(".loginpress-optout-spinner").hide();
        $('.loginpress-modal-opt-out').removeClass('active');
        location.reload();
      });

    });

  });

})( jQuery ); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.
</script>
