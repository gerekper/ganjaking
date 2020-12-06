(function($) {
  $(document).ready(function() {
    $('#wafp-edge-updates').click( function(e) {
      e.preventDefault();
      var wpnonce = $(this).attr('data-nonce');

      $('#wafp-edge-updates-wrap .wafp_loader').show();
      $(this).prop('disabled',true);

      var data = {
        action: 'wafp_edge_updates',
        edge: $(this).is(':checked'),
        wpnonce: wpnonce
      };

      var bigthis = this;

      $.post(ajaxurl, data, function(obj) {
        $('#wafp-edge-updates-wrap .wafp_loader').hide();
        $(bigthis).prop('disabled',false);

        if('error' in obj)
          alert(obj.error);
        else {
          $(bigthis).prop('checked',(obj.state=='true'));
        }
      }, 'json');
    });
  });
})(jQuery);
