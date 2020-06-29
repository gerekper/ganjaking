(function($) {
  $(document).ready(function() {
    $('body').on('click', '.admin-tooltip', function() {
      var tooltip_title = $(this).find('.data-title').html();
      var tooltip_info = $(this).find('.data-info').html();
      $(this).pointer({ 'content':  '<h3>' + tooltip_title + '</h3><p>' + tooltip_info + '</p>',
                        'position': {'edge':'left','align':'center'},
                      })
      .pointer('open');
    });
  });
})(jQuery);
