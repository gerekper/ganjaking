(function ($) {
  $(document).ready(function() {
    function wafp_add_product_commission_level(amount) {
      var type = $('#wafp_commission_type').val();
      var currency = $('#wafp_commissions').attr('data-currency');
      var level = $('#wafp_commissions li').size() + 1; // Number of levels present
      var level_html = '';

      if( type == 'fixed' ) {
        level_html = '<li class="wafp_hidden">' + currency + ' <input type="text" value="' + amount + '" /></li>';
      }
      else {
        level_html = '<li class="wafp_hidden"><input type="text" value="' + amount + '" />%</li>';
      }

      $('#wafp_commissions').append(level_html);
      $('#wafp_commissions li:last-child').slideDown('fast');

      if($('#wafp_commissions li').size() > 1) {
        $('#wafp_remove_product_commission_level').show();
      }
    }

    function wafp_rebuild_commission_display() {
      $('#wafp_commissions').text(''); /// reset the commission display
      var commissions = $.parseJSON($('#wafp_commissions_json').text());
      var amount = '0.00';

      for(var i=0; i < commissions.length; i++) {
        amount = commissions[i];
        wafp_add_product_commission_level(amount);
      }
    }

    function wafp_rebuild_commission_json() {
      var commissions = [];

      $('#wafp_commissions li input').each( function() {
        commissions.push( $(this).val() );
      });

      $('#wafp_commissions_json').text(JSON.stringify(commissions));
    }

    $('#wafp_remove_product_commission_level').hide();
    wafp_rebuild_commission_display();

    if($('#wafp_enable_commission_group').is(':checked')) {
      $('#wafp_commission_group').show();
    }
    else {
      $('#wafp_commission_group').hide();
    }

    $('#wafp_enable_commission_group').click( function(e) {
      if($('#wafp_enable_commission_group').is(':checked')) {
        $('#wafp_commission_group').slideDown('fast');
      }
      else {
        $('#wafp_commission_group').slideUp('fast');
      }
    });

    $("#wafp_add_product_commission_level").click( function(e) {
      e.preventDefault();
      wafp_add_product_commission_level('0.00');
      wafp_rebuild_commission_json();
    });

    $("#wafp_remove_product_commission_level").click( function(e) {
      e.preventDefault();
      $('#wafp_commissions li:last-child').slideUp('fast', function() {
        $(this).remove();
        wafp_rebuild_commission_json();

        if($('#wafp_commissions li').size() <= 1) {
          $('#wafp_remove_product_commission_level').hide();
        }
      });
    });

    $('#wafp_commission_type').change( function(e) {
      e.preventDefault();
      wafp_rebuild_commission_display();
    });

    $('#wafp_commissions').on('blur', 'input', function(e) {
      var new_val = 0.00;
      if( $(this).val().match(/\d+(\.\d+)?/) ) {
        new_val = $(this).val();
      }

      if($('#wafp_commission_type').val() == 'percentage' && parseInt(new_val) >= 100) {
        new_val = 100.00;
      }

      new_val = parseFloat(new_val).toFixed(2)

      $(this).val(new_val);

      wafp_rebuild_commission_json();
    });
  });
})(jQuery);
