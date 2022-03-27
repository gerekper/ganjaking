var MpcsQuiz = (function ($) {

  var quiz,
    submitting = false,
    $submit_button,
    $submit_button_bottom,
    next_page_url;

  quiz = {

    initialize: function () {
      if(MpcsQuizL10n.attempt_complete) {
        $(MpcsQuizL10n.attempt_score).insertBefore($('.mpcs-quiz-question').first());

        $('#mpcs-quiz-continue, #mpcs-quiz-continue-bottom').on('click', quiz.goToNextLesson);
      }
      else {
        $submit_button = $('#mpcs-quiz-submit').on('click', quiz.submitQuiz);
        $submit_button_bottom = $('#mpcs-quiz-submit-bottom').on('click', quiz.submitQuiz);
        next_page_url = $submit_button.length ? $submit_button.data('next-page-url') : $submit_button_bottom.data('next-page-url');
        quiz.setupAutoSave();
      }

      quiz.setupCharacterCounters();
    },

    submitQuiz: function () {
      if(submitting) {
        return;
      }

      submitting = true;

      var submit_button_html = $submit_button.html(),
        submit_button_bottom_html = $submit_button_bottom.html(),
        submit_button_bottom_width = $submit_button_bottom.width(),
        form_data = new FormData();

      form_data.append('action', 'mpcs_submit_quiz');
      form_data.append('post_id', MpcsQuizL10n.post_id);
      form_data.append('attempt_id', MpcsQuizL10n.attempt_id);
      form_data.append('_ajax_nonce', MpcsQuizL10n.submit_quiz_nonce);

      $.each($('.mpcs-quiz-question').find('input, textarea, select').serializeArray(), function (i, o) {
        form_data.append(o.name, o.value);
      });

      $('.mpcs-quiz-submission-error').remove();
      $('.mpcs-quiz-question-error').remove();

      $submit_button.add($submit_button_bottom).html('<i class="mpcs-spinner mpcs-animate-spin"></i>');
      $submit_button_bottom.width(submit_button_bottom_width);

      $.ajax({
        method: 'POST',
        url: MpcsQuizL10n.ajax_url,
        data: form_data,
        processData: false,
        contentType: false
      }).done(function (response) {
        if(response && typeof response.success == 'boolean') {
          if(response.success) {
            if(!response.data.show_results) {
              window.location.href = next_page_url;
            }
            else {
              window.location.reload();
            }
          }
          else {
            if(typeof response.data == 'string') {
              quiz.displayQuizSubmissionError(response.data);
            }
            else if (typeof response.data == 'object' && response.data !== null && typeof response.data.errors == 'object') {
              var $first_invalid_question = null;

              $.each(response.data.errors, function (i, error) {
                if(!$first_invalid_question) {
                  $first_invalid_question = $('#mpcs-quiz-question-' + error.id);
                }

                quiz.displayQuizFieldError(error.id, error.message);
              });

              if($first_invalid_question) {
                quiz.scrollTo($first_invalid_question);
              }
            }
            else {
              quiz.displayQuizSubmissionError('Invalid response');
            }
          }
        }
      }).fail(function () {
        quiz.displayQuizSubmissionError('Request failed');
        console.log(arguments);
      }).always(function () {
        $submit_button.html(submit_button_html);
        $submit_button_bottom.html(submit_button_bottom_html).width('auto');
        submitting = false;
      });
    },

    displayQuizFieldError: function (id, message) {
      var $question = $('#mpcs-quiz-question-' + id);

      if($question.length) {
        var $error = $('<div class="mpcs-quiz-question-error mpcs-clearfix">');

        $('<div class="mpcs-quiz-question-error-inner">').text(message).appendTo($error)
        $question.find('.mpcs-quiz-question-input').after($error);
        quiz.animateShow($error);
      }
    },

    displayQuizSubmissionError: function (message) {
      var $error = $('<div class="mpcs-quiz-submission-error">');

      $error.append($('<div class="mpcs-quiz-submission-error-inner">').text(MpcsQuizL10n.error_submitting_quiz.replace('%s', message)));
      $('.mpcs-quiz-question').first().before($error);
      quiz.animateShow($error);
      quiz.scrollTo($error);
    },

    animateShow: function ($element) {
      $element.animate({
        height: 'show',
        opacity: 'show',
        marginTop: 'show',
        marginBottom: 'show',
        paddingTop: 'show',
        paddingBottom: 'show'
      }, { duration: 400 });
    },

    setupCharacterCounters: function () {
      if(!Array.from) {
        return;
      }

      $('.mpcs-quiz-character-counter').each(function () {
        var $counter = $(this),
          $textarea = $counter.closest('.mpcs-quiz-question').find('textarea');

        $textarea.on('keyup blur', function () {
          var value = $(this).val(),
            count = 0;

          if(value) {
            count = Array.from(value).length;
          }

          $counter.text(MpcsQuizL10n.character_count.replace('%d', count));
        });

        if($textarea.val()) {
          $textarea.triggerHandler('keyup');
        }
      });
    },

    scrollTo: function ($target) {
      if(!MpcsQuizL10n.scroll_enabled || !$.scrollTo || !$target || !$target.length) {
        return;
      }

      if(quiz.isScrolledIntoView($target, MpcsQuizL10n.scroll_offset)) {
        return; // already in view
      }

      $.scrollTo($target, MpcsQuizL10n.scroll_speed, {
        axis: 'y',
        offset: MpcsQuizL10n.scroll_offset
      });
    },

    isScrolledIntoView: function ($element, offset) {
      var doc_view_top = $(window).scrollTop(),
        doc_view_bottom = doc_view_top + $(window).height(),
        elem_top = $element.offset().top,
        elem_bottom = elem_top + $element.height();

      if(offset) {
        elem_top += offset;
      }

      return ((elem_bottom >= doc_view_top) && (elem_top <= doc_view_bottom) && (elem_bottom <= doc_view_bottom) && (elem_top >= doc_view_top));
    },

    setupAutoSave: function () {
      if(!MpcsQuizL10n.auto_save_enabled) {
        return;
      }

      $('.mpcs-quiz-question').each(function () {
        var $fields = $(this).find('.mpcs-quiz-question-field');

        $fields.each(function () {
          var $field = $(this),
            event;

          if($field.is('input[type="checkbox"], input[type="radio"]')) {
            event = 'change.mpcs';
          }
          else if($field.is('textarea, input[type="text"]')) {
            event = 'blur.mpcs';
          }

          if(event) {
            $field.on(event, function () {
              var form_data = new FormData();
              form_data.append('action', 'mpcs_auto_save_question');
              form_data.append('_ajax_nonce', MpcsQuizL10n.auto_save_question_nonce);
              form_data.append('attempt_id', MpcsQuizL10n.attempt_id);
              form_data.append('question_id', $field.data('question-id'));

              $.each($fields.serializeArray(), function (i, o) {
                form_data.append(o.name, o.value);
              });

              $.ajax({
                method: 'POST',
                url: MpcsQuizL10n.ajax_url,
                data: form_data,
                processData: false,
                contentType: false
              });
            });
          }
        });
      });
    },

    goToNextLesson: function () {
      if(submitting) {
        return;
      }

      submitting = true;

      var href = $(this).data('href');

      $.ajax({
        method: 'POST',
        url: MpcsQuizL10n.ajax_url,
        data: {
          action: 'mpcs_record_lesson_progress',
          progress_nonce: MpcsQuizL10n.progress_nonce,
          lesson_id: MpcsQuizL10n.post_id
        }
      }).always(function () {
        submitting = false;
        window.location.href = href;
      });
    }

  };

  $(quiz.initialize);

  return quiz;
})(jQuery);
