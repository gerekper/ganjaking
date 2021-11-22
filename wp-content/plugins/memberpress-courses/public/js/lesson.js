(function($) {
  $(document).ready(function() {
    if ($('body').hasClass('mpcs-sidebar-with-accordion')) {
      var headers = $('#mpcs-sidebar .mpcs-section-header');
      var current = $('#mpcs-sidebar .mpcs-lesson.current');
      if (current.length) {
        var header = current.closest('.mpcs-section').find('.mpcs-section-header');
        header.addClass('active');
        header.next('.mpcs-lessons').css('display', 'block');
      }
      $(headers).on('click', function () {
        var $this = $(this);
        $this.toggleClass('active');
        if ($this.hasClass('active')) {
          $this.next('.mpcs-lessons').css('display', 'block');
        } else {
          $this.next('.mpcs-lessons').css('display', 'none');
        }
      });
    }

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
