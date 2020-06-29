<div class="wrap about-wrap">
<?php if (!defined('SEED_CSPV5_REMOVE_BRANDING')) { ?>
        <img style="display:block;width:200px;margin-bottom:10px" src="<?php echo SEED_CSPV5_PLUGIN_URL ?>admin/seedprod-logo-black.png">
        <h1>Coming Soon Page Pro</h1>
        <span class="version">Version <?php echo SEED_CSPV5_VERSION; ?></span>
        <div class="about-text">
            Thank you for choosing SeedProd's Coming Soon Pro - the best Coming Soon Page and Maintenance Mode plugin for WordPress.
        </div>
<?php } ?>
        <h2 class="nav-tab-wrapper">
            <a class="nav-tab nav-tab-active" href="javascript:void(0);">License</a>
        </h2>
        <p class="about-description">
            Enter your license key to enable all the features of the plugin. 
        </p>
        <input autocomplete='off' id='seed_cspv5_license_key' class='regular-text' type='password' value='<?php echo get_option('seed_cspv5_license_key') ?>' placeholder="License Key" />
        <button id='seed_cspv5_license_check' type='button' class='button-primary'><?php _e('Check License','seedprod') ?></button>
        <?php $api_nag = get_option('seed_cspv5_api_message'); ?>
        <div id='seed_cspv5_license_check_msg' style="background-color:#FCF8E3;margin:10px 0"><?php echo (!empty($api_nag)) ? $api_nag : '' ?></div>
        <p>Don't have a license key or need to renew? Visit <a href="https://www.seedprod.com/?utm_source=plugin&utm_medium=link&utm_campaign=no-license" target="_blank">http://seedprod.com</a></p>
        <br><br><br><br>
        <div class="feature-section two-col">   
            <div class="col">
                <h3>Getting Started</h3>
                <p>Watch our quick 60 second Getting Started video.</p>
                <p>Need more help Visit <a href="http://support.seedprod.com" target="_blank">http://support.seedprod.com</a></p>
                <p><a id="seed_cspv5_goto_settings" href="javascript::void(0)" target="_blank" class="button-primary">Go to the Settings Page</a></p>
            </div>
            <div class="col">
                <iframe width="420" height="315" src="https://www.youtube.com/embed/37mEhDccpQA?rel=0" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
</div>
<script type='text/javascript'>
<?php $ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_activate_license','seed_cspv5_activate_license')); ?>
var ajax_url = "<?php echo $ajax_url; ?>";
jQuery(document).ready(function($) {
    $('#seed_cspv5_license_check').click(function() {
        $('#seed_cspv5_license_check').prop("disabled", true);
        $('#seed_cspv5_license_check_msg').hide();
        license_key = $('#seed_cspv5_license_key').val();
        if(license_key != ''){
            $.get('<?php echo $ajax_url; ?>&apikey='+license_key, function(data) {
              var response = $.parseJSON(data);
              if(typeof response === 'object'){
                  
                  $('#seed_cspv5_license_check_msg').text(response.message);
                  //console.log(response);
                  $('#seed_cspv5_license_check_msg').fadeIn();
                  $('#seed_cspv5_license_check').prop("disabled", false);
              }else{
                  $('#seed_cspv5_license_check_msg').text(data);
                  //console.log(response);
                  $('#seed_cspv5_license_check_msg').fadeIn();
                  $('#seed_cspv5_license_check').prop("disabled", false);
              }
            });
        }else{
          $('#seed_cspv5_license_check_msg').text('Please enter your license key.').fadeIn();
          
          $('#seed_cspv5_license_check').prop("disabled", false);
        }
    }); 
    var warn_count = 0;
    $('#seed_cspv5_goto_settings').click(function(e) {
        e.preventDefault();
        license_key = $('#seed_cspv5_license_key').val();
        if(license_key == '' && warn_count < 2){
            if(warn_count == 0){
                alert('Enter your license key to enable all the features of the plugin.');
            }
            if(warn_count == 1){
                r =confirm("If you don't enter a valid license key, you will not be able to update SeedProd when important bug fixes and security enhancements are released. This can be a serious security risk for your site. Some aspects of the plugin that require the API may not work as well.");
                if(r){
                    window.location.href = '<?php echo admin_url() ?>options-general.php?page=seed_cspv5';
                }
            }
            warn_count = warn_count + 1;
        }else{
            window.location.href = '<?php echo admin_url() ?>options-general.php?page=seed_cspv5';
        }
        
        
    });
});
</script>
