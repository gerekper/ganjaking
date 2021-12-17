<?php if (isset($_GET['setup_wizard'])) : ?>
<div id="setup-wizard-notify" class="panel panel-success small-padding margin-top">   
    <div class="panel-heading">
    
    <div class="container-flex flex-between"> 
            <div class="container-flex">
              
                <span class="display-block"><strong><?php echo esc_html_x('Setup Wizard has applied preferred settings!', 'Wizard', 'ali2woo'); ?></strong></span>
            </div>
            <div class="container-flex">
              
                <a href="#" class="btn-link small chrome-notify-close" alt="<?php _e('Close'); ?>">
                    <svg class="icon-small-cross"> 
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-small-cross"></use>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <script>(function ($) {
            $('.setup-wizard-notify').click(function () {$(this).closest('.panel').remove();return false;});
        })(jQuery);</script>
</div>
<?php endif; ?>
