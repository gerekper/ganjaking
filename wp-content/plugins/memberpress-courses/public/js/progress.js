(function($) {
  $(document).ready(function() {
    // alert('sdsd');
    $('.course-progress').each(function(i, e) {
      var progress_bar = $('.user-progress', e);
      var progress = 0;
      var interval = setInterval(expand_progress, 10);
      var target_progress = progress_bar.data('value');
      progress_bar.html(target_progress + '&#37;');
      function expand_progress() {
          if (progress >= target_progress) {
              clearInterval(interval);
          } else {
              progress++;
              progress_bar.width(progress + '%');
          }
      }
    });

    $('.mpcs-reset-course-progress').on('click', function(e) {
      e.preventDefault();
      if (confirm('Are you sure you want to reset course progress?')) {
        let user_progress = $(this).closest('tr').find('.course-progress');
        let params = {
          action:         'mpcs_reset_course_progress',
          course_id:      $(this).data('value'),
          user_id:        $(this).data('user'),
          nonce:          $(this).data('nonce'),
        };

        $.post(ajaxurl, params, function(res) {
          user_progress.html('<div class="user-progress" data-value="0">0%</div>')
        })
      }
    });


  });
})(jQuery);
