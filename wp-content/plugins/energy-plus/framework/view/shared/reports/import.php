<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>


<div class="__A__Reports_Import">
  <div class="__A__Reports_Import_Intro_Icon text-center">
    <span class="dashicons dashicons-chart-pie"></span>
  </div>

  <div class="__A__Reports_Import_Intro">
    <h2><?php esc_html_e('Import Tool', 'energyplus'); ?></h2>
    <p><?php esc_html_e('This tool allows you to export your past reports to Energy+.', 'energyplus'); ?>
      <br>
      <?php esc_html_e('Please start it when your store is least busy since it will run many queries in the background.', 'energyplus'); ?>
    </p>
    <p class="text-danger"><?php esc_html_e('Please do not close this window during import.', 'energyplus'); ?></a>
      <br><br>
    </div>

    <div class="__A__Reports_Import_Console d-none">
      <div class="__A__Reports_Import_Console_In"></div>
    </div>

    <div class="text-center">
      <button class="button-danger __A__Reports_Start_Import"><?php esc_html_e('Start import', 'energyplus'); ?></button>
    </div>
  </div>


  <script>
  jQuery(document).ready(function($) {
    "use strict";

    jQuery('.__A__Reports_Start_Import').on('click', function() {
      energyplusImport('<?php echo esc_attr(EnergyPlus_Helpers::strtotime('today', 'Y-m-01'))?>', 'import-date', '<?php echo esc_attr(EnergyPlus_Helpers::strtotime('today', 'Y-m-01'))?>');
      jQuery('.__A__Reports_Import_Intro_Icon').css('opacity', 0).slideUp();
      jQuery('.__A__Reports_Import_Intro').addClass('d-none');
      jQuery('.__A__Reports_Import_Console').removeClass('d-none');
      jQuery(this).text('Please wait...').css({'background-color':'#f5f5f5', 'border':'1px solid #ccc', 'color':'#ccc'}).attr('disabled', 'disabled');
    });

    function energyplusImport(id, type, last) {
      jQuery.post( EnergyPlusGlobal.ajax_url, {
        _wpnonce:         jQuery('input[name=_wpnonce]').val(),
        _asnonce:         EnergyPlusGlobal._asnonce,
        _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
        action:           "energyplus_ajax",
        segment:          'reports',
        do:               'import',
        sub:              type,
        range:            id,
        first:            '<?php echo esc_attr($first_order_date)?>'
      }, function(r) {
        if (r.status === 1) {

          jQuery('.__A__x').animate({opacity:0},200).remove();
          jQuery('.__A__Reports_Import_Console_In').append(r.message.det);
          jQuery('.__A__'+r.message.date).animate({opacity:1},500);

          energyplusImport(r.message.date, r.message.type, id);

          if (r.message.date === '-1') {
            jQuery('.__A__Reports_Start_Import').hide();
          }
        }
      }, 'json');
    }
  });
</script>
