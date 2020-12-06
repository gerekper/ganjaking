// Easy Affiliate Options JS
jQuery(document).ready(function($) {
  jQuery('#wafp_add_commission_level').click( function() {
    var data = {
      action: 'add_commission_level',
      level: jQuery('#wafp_commission_levels').children().length + 1
    };

    jQuery.post(ajaxurl, data, function(response) {
      jQuery('#wafp_commission_levels').append(response);
      jQuery('#wafp_commission_levels li:last').slideDown('fast');
      jQuery('#wafp_remove_commission_level').show();
      wafp_set_commission_type();
    });
  });

  if( jQuery('#wafp_commission_levels' ).children().length > 1 ) {
    jQuery('#wafp_remove_commission_level').show();
  }

  jQuery('#wafp_remove_commission_level').click( function() {
    jQuery('#wafp_commission_levels li:last').slideUp('fast',function() {
      jQuery('#wafp_commission_levels li:last').remove();

      if( jQuery('#wafp_commission_levels' ).children().length < 2 ) {
        jQuery('#wafp_remove_commission_level').hide();
      }
    });
  });
});

