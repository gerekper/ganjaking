<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>
<script>
jQuery(document).ready(function() {
  "use strict";

  jQuery.post( "<?php echo admin_url("admin-ajax.php")?>", {
    <?php echo EnergyPlus_Helpers::ajax_nonce() ?>,
    _asnonce: '<?php echo wp_create_nonce( 'energyplus-segment--customers')?>',
    action: "energyplus_ajax",
    segment: 'customers',
    do: 'search',
    q: '<?php echo esc_attr($term) ?>',
    mode: 98,
    status: ''
  }, function(r) {

    jQuery('.__A__Search_Container_Searching').hide();

    if ('\n' === r) {
      jQuery('.__A__Search_Container_No').addClass('__A__No3');
    } else {
      jQuery('.__A__Search_Container_No').removeClass('__A__No3');
    }
    jQuery(".__A__Search_Customers").addClass("__A__Search_Complete").html(r);
  });



  jQuery.post( "<?php echo admin_url("admin-ajax.php")?>", {
    <?php echo EnergyPlus_Helpers::ajax_nonce() ?>,
    _asnonce: '<?php echo wp_create_nonce( 'energyplus-segment--products')?>',

    action: "energyplus_ajax",
    segment: 'products',
    do: 'search',
    q: '<?php echo esc_attr($term) ?>',
    mode: 98,
    status: ''
  }, function(r) {

    jQuery('.__A__Search_Container_Searching').hide();

    if ('\n' === r) {
      jQuery('.__A__Search_Container_No').addClass('__A__No2');
    } else {
      jQuery('.__A__Search_Container_No').removeClass('__A__No2');
    }
    jQuery(".__A__Search_Products").addClass("__A__Search_Complete").html(r);
  });

  jQuery.post( "<?php echo admin_url("admin-ajax.php")?>", {
    <?php echo EnergyPlus_Helpers::ajax_nonce() ?>,
    _asnonce: '<?php echo wp_create_nonce( 'energyplus-segment--orders')?>',
    action: "energyplus_ajax",
    segment: 'orders',
    do: 'search',
    q: '<?php echo esc_attr($term) ?>',
    mode: 98,
    status: ''
  }, function(r) {

    jQuery('.__A__Search_Container_Searching').hide();

    if ('\n' === r) {
      jQuery('.__A__Search_Container_No').addClass('__A__No1');
    } else {
      jQuery('.__A__Search_Container_No').removeClass('__A__No1');
    }
    jQuery(".__A__Search_Orders").addClass("__A__Search_Complete").html(r);
  });

});

</script>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
