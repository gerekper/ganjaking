<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php if (!isset($ajax)) { ?>
  <div class="__A__Widget_Options">
    <h1 class="float-left">
      <?php esc_html_e('Last Activities', 'energyplus'); ?>
    </h1>
    <div class="__A__Widget_Options_AutoHide float-left">
      <ul>
        <li><a class="__A__Widget_Settings_<?php echo esc_attr($args['id'])?>_Range<?php if ('online' === $args['range']) echo ' __A__Selected';?>" data-range='online' href="javascript:;"><?php esc_html_e('Online', 'energyplus'); ?></a></li>
          <li><a class="__A__Widget_Settings_<?php echo esc_attr($args['id'])?>_Range<?php if ('all' === $args['range']) echo ' __A__Selected';?>" data-range='all' href="javascript:;"><?php esc_html_e('All', 'energyplus'); ?></a></li>
          </ul>
        </div>
        <div class="__A__Clear_Both"></div>
      </div>
      <div class="__A__Widget_Lastactivity_container __A__Range_<?php echo esc_attr($args['range'])?>">
        <div class="__A__EmptyTable <?php if (0 < count($result)) {echo ' d-none'; } else { echo 'd-flex'; }?> align-items-center justify-content-center text-center">
          <div><span class="dashicons dashicons-marker"></span><br><?php esc_html_e('No visitors online', 'energyplus'); ?></div>
        </div>
      <?php } ?>
      <?php foreach ($result AS $session) { ?>
        <div class="__A__Widget_Lastactivity_row animated fadeInUp d-flex align-items-center __A__Time_<?php echo date("Hi", strtotime($session['date'])) ?> __A__Widget_Lastactivity_Sess_<?php echo esc_attr($session['id']) ?>" id="__A__Widget_Lastactivity_Sess_<?php echo esc_attr($session['id']) ?>">
          <div class="__A__I1">
            <?php if (300 > (EnergyPlus_Helpers::strtotime('now', 'U')-EnergyPlus_Helpers::strtotime($session['date'], 'U'))) { ?>
              <span class="badge badge-success __A__online" data-time="<?php echo date("H:i", strtotime($session['date'])) ?>">ON</span>
            <?php } else { ?>
              <span class=""><?php echo date("H:i", strtotime($session['date'])) ?></span>
            <?php } ?>
          </div>
          <div class="__A__I1 __A__I2">
            <?php echo wp_kses_post($session['visitor']) ?>
          </div>

          <div class="__A__I">
            <div class="__A__I_Overflow">
              <?php foreach ($session['views'] AS $views_key => $r) { ?>
                <div class="__A__I_O_Container">
                  <?php  if (1 === $r['type']) { ?>
                    <img src="<?php echo get_the_post_thumbnail_url($r['details']['id']); ?>" class="__A__Product_Image"  data-toggle="tooltip" data-placement="bottom" title="<?php echo esc_html($r['details']['name'])?> (<?php echo esc_html( strip_tags ( wc_price($r['details']['price'] ) ) )?>)">
                  <?php } elseif (2 === $r['type']) {  ?>
                    <div class="__A__Widget_Lastactivity_T2"  data-toggle="tooltip" data-placement="bottom" title="<?php echo esc_html($r['details']['name'])?>"><?php echo esc_html(strtoupper($r['details']['name']{0}))?></div>
                  <?php } elseif (4 === $r['type']) {  ?>
                    <img src="<?php echo get_the_post_thumbnail_url($r['details']['id']); ?>" class="__A__Product_Image">
                    <div class="__A__Widget_Lastactivity_T4"  data-toggle="tooltip" data-placement="bottom" title="<?php echo esc_html(sprintf( esc_html__( '%s (%s) has been added to cart' , 'energyplus') , $r['details']['name'], strip_tags ( wc_price($r['details']['price'] ) ) )) ?>">+</div>
                  <?php } elseif (5 === $r['type']) {  ?>
                    <img src="<?php echo get_the_post_thumbnail_url($r['details']['id']); ?>" class="__A__Product_Image" >
                    <div class="__A__Widget_Lastactivity_T5"   data-toggle="tooltip" data-placement="bottom" title="<?php echo esc_html( sprintf( esc_html__( '%s (%s) has been removed from cart' , 'energyplus') , $r['details']['name'], strip_tags ( wc_price($r['details']['price'] ) ) ) ) ?>">-</div>
                  <?php } elseif (6 === $r['type']) {  ?>
                    <div class="__A__Widget_Lastactivity_T6"  data-toggle="tooltip" data-placement="bottom" title="<?php esc_html_e('Checkout', 'energyplus'); ?>"><?php echo esc_html( get_woocommerce_currency_symbol() ) ?></div>
                  <?php } elseif (7 === $r['type']) {  ?>
                    <div class="__A__Widget_Lastactivity_T7"   data-toggle="tooltip" data-placement="bottom" title="<?php esc_html_e('Homepage', 'energyplus'); ?>" ><span class="dashicons dashicons-admin-site"></span></div>
                  <?php } elseif (10 === $r['type']) {  ?>
                    <div class="__A__Widget_Lastactivity_T10"  data-toggle="tooltip" data-placement="bottom" title="<?php esc_html_e('Search:', 'energyplus'); ?> <?php echo esc_html(implode(", ", $r['details']['term']))?>" ><span class="dashicons dashicons-search"></span></div>
                  <?php } elseif (17 === $r['type']) {  ?>
                    <div class="__A__Widget_Lastactivity_T2"  data-toggle="tooltip" data-placement="bottom" title="<?php echo esc_html($r['details']['term'])?>"><?php echo esc_html(strtoupper($r['details']['term']{0}))?></div>
                  <?php } ?>
                  <?php if (1 < $r['details']['cnt']) { ?>
                    <span class="__A__Widget_Lastactivity_T0 badge badge-warning"><?php echo esc_html($r['details']['cnt'])?></span>
                  <?php } ?>
                </div>
              <?php } ?>
              <div class="__A__Clear_Both">
              </div>
            </div>
          </div>
          <div class="__A__Clear_Both"></div>
        </div>

      <?php } ?>
      <?php if (!isset($ajax)) { ?>
      </div>

      <script>
      jQuery(function () {
        "use strict";

        jQuery(".__A__Widget_Settings_<?php echo esc_attr($args['id'])?>_Range").on( "click", function() {
          jQuery(".__A__Widget_Settings_<?php echo esc_attr($args['id'])?>_Range").removeClass("__A__Selected");
          jQuery(".__A__Widget_Lastactivity_container")
          .removeClass("__A__Range_online")
          .removeClass("__A__Range_all")
          .addClass("__A__Range_"+jQuery(this).attr("data-range"));

          jQuery('.__A__Widget_Lastactivity_container').css('opacity', '0.3');

          jQuery(this).addClass("__A__Selected");
          jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url("admin-ajax.php")?>',
            data: {
              action: 'energyplus_widgets',
              a: 'settings',
              id: '<?php echo esc_attr($args['id'])?>',
              set_id: 'range',
              s: jQuery(this).attr("data-range")
            },
            cache: false,
            headers: {
              'cache-control': 'no-cache'
            },
            success: function(response) {
              window.reload_widgets(-2);
              jQuery('.__A__Widget_Lastactivity_container').css('opacity', '1');

            }
          }, 'json');
        });
      });
      </script>

    <?php }?>
