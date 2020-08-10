<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<h1><?php esc_html_e('Online Visitors', 'energyplus'); ?></h1>
<div id='__A__Widget_<?php echo esc_attr($args['id']) ?>_Current' class="__A__Widget_onlineusers_Current"><?php echo esc_html($result); ?></div>
<canvas id="__A__Widget_<?php echo esc_attr($args['id']) ?>_Canvas" class="__A__Widget_onlineusers_Canvas"></canvas>
<div id="bp-energyplus-wdg-ov--min"><?php echo esc_html($min); ?></div>
<div id="bp-energyplus-wdg-ov--max"><?php echo esc_html($max); ?></div>

<script>
jQuery(document).ready(function() {
  "use strict";

  var opts = {
    lines: 12, // The number of lines to draw
    angle: 0.06, // The span of the gauge arc
    lineWidth: 0.5, // The line thickness
    pointer: {
      length: 0.75, // The radius of the inner circle
      strokeWidth: 0.035, // The thickness
      <?php if ('dark' === EnergyPlus::$theme) {?>
      color: '#777' // Fill color
      <?php } else { ?>
      color: '#000' // Fill color
      <?php } ?>
    },
    limitMax: false,
    <?php if ('dark' === EnergyPlus::$theme) {?>  // If true, the pointer will not go past the end of the gauge
    colorStart: '#777',   // Colors
    colorStop: '#777',
    strokeColor: '#323232',
    <?php } else { ?>
    colorStart: '#6FADCF',   // Colors
    colorStop: '#8FC0DA',
    strokeColor: '#E0E0E0',
    <?php } ?>
    generateGradient: true,
    highDpiSupport: true
  };
  setGaugeMax('<?php echo esc_attr($args['id'])?>', <?php echo esc_attr(absint($result)); ?>,<?php echo esc_attr(absint($max)) ?>,"", opts);
});
</script>
