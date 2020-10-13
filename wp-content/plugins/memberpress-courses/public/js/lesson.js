(function($) {
  $(document).ready(function() {
    $('#previous_lesson_link').on('click', function(e) {
      e.preventDefault();
      this.disabled = true;
      location.href = $(this).data('href');
    });

    $('#next_lesson_link').on('click', function(e) {
      e.preventDefault();
      this.disabled = true;
      var follow_link = $(this).data('href');
      var params = {
        action:         'mpcs_record_lesson_progress',
        lesson_id:      $(this).data('value'),
        progress_nonce: locals.progress_nonce,
      };

      $.post(locals.ajaxurl, params, function(res) {
        location.href = follow_link;
      })
      .fail(function(res) {
        console.log('Request Failed: Progress was not recorded.', res);
        location.href = follow_link;
      });
    });
  });
})(jQuery);
