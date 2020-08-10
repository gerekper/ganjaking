<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="__A__Widget_Options">
  <h1 class="float-left">
    <?php esc_html_e('Visitors', 'energyplus'); ?>
  </h1>
  <div class="__A__Widget_Options_AutoHide float-left">
    <ul>
      <li><a class="__A__Widget_Settings_<?php echo esc_attr($args['id']) ?>_Range<?php if ('hourly' === $args['range']) echo ' __A__Selected';?>" data-range='hourly' href="javascript:;"><?php esc_html_e('Hourly', 'energyplus'); ?></a></li>
        <li><a class="__A__Widget_Settings_<?php echo esc_attr($args['id']) ?>_Range<?php if ('daily' === $args['range']) echo ' __A__Selected';?>" data-range='daily' href="javascript:;"><?php esc_html_e('Daily', 'energyplus'); ?></a></li>
          <li><a class="__A__Widget_Settings_<?php echo esc_attr($args['id']) ?>_Range<?php if ('monthly' === $args['range']) echo ' __A__Selected';?>" data-range='monthly' href="javascript:;"><?php esc_html_e('Monthly', 'energyplus'); ?></a></li>
          </ul>
        </div>
      </div>

      <div class="chart-container __A__Widget_Hourly_Chart">
        <canvas id="__A__Chart_<?php echo esc_attr($args['id']) ?>" width="120"></canvas>
      </div>
      <script>
      jQuery(document).ready(function() {
        "use strict";

        var ctx = document.getElementById("__A__Chart_<?php echo esc_attr($args['id']) ?>").getContext('2d');
        ctx.height = 100;
        var myChart = new Chart(ctx, {
          type: 'bar',
          maintainAspectRatio: false,

          options: {
            <?php if (isset($args['ajax'])) { ?>
            animation: false,
            <?php } ?>
            responsive: true,
            maintainAspectRatio: false,
            legend: {
              display: false,
            },
            scales: {
              xAxes: [{
                gridLines: {
                  display:false,

                }
              }],
              yAxes: [{
                display: false,
                ticks: {
                  min: 0,
                  max: <?php echo intval($max+(ceil($max*0.3))) ?>,
                  stepSize: 1
                },
                gridLines: {
                  display:false
                }
              }]
            }
          },
          data: {
            labels: [<?php echo implode(",", $labels); ?>],
            datasets: [{
              <?php if ('one-shadow' === EnergyPlus::$theme) {?>
              backgroundColor: '#8bbed9',
              <?php }?>
              label: '',
              data: [<?php echo implode(",", $results); ?>],
              borderWidth: 1
            }]
          },

        });
        <?php if (!isset($args['ajax'])) { ?>
        Chart.plugins.register({
          afterDatasetsDraw: function(chart) {
            var ctx = chart.ctx;

            chart.data.datasets.forEach(function(dataset, i) {
              var meta = chart.getDatasetMeta(i);
              if (!meta.hidden) {
                meta.data.forEach(function(element, index) {
                  // Draw the text in black, with the specified font
                  <?php if ('one-shadow' === EnergyPlus::$theme) { ?>
                  ctx.fillStyle = '#666';
                  <?php } else { ?>
                  ctx.fillStyle = '#ccc';
                  <?php }?>

                  var fontSize = 13;
                  var fontStyle = 'normal';
                  var fontFamily = 'Arial';
                  ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);

                  // Just naively convert to string for now
                  var dataString = dataset.data[index].toString();

                  // Make sure alignment settings are correct
                  ctx.textAlign = 'center';
                  ctx.textBaseline = 'middle';

                  var padding = 5;
                  var position = element.tooltipPosition();
                  if (dataString > 0) {
                    ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                  }
                });
              }
            });
          }
        });
        <?php } ?>
      });

      jQuery(function () {
        jQuery(".__A__Widget_Settings_<?php echo esc_attr($args['id']) ?>_Range").on( "click", function() {
          jQuery(".__A__Widget_Settings_<?php echo esc_attr($args['id']) ?>_Range").removeClass("__A__Selected");
          jQuery(this).addClass("__A__Selected");

          jQuery('#__A__Chart_3').css('opacity', '0.3');

          jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url("admin-ajax.php")?>',
            data: {
              action: 'energyplus_widgets',
              a: 'settings',
              id: '<?php echo esc_attr($args['id']) ?>',
              set_id: 'range',
              s: jQuery(this).attr("data-range")
            },
            cache: false,
            headers: {
              'cache-control': 'no-cache'
            },
            success: function(response) {
              reload_widgets();
            }
          }, 'json');
        });
      });

      </script>
