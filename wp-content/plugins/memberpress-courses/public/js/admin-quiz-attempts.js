var MpcsQuizAttempts = (function ($) {
  var attempts;

  attempts = {
    initialize: function () {
      $('.mpcs-quiz-attempt-view').on('click', function (e) {
        e.preventDefault();
        attempts.viewQuizAttempt($(this).data('id'));
      });

      $('.mpcs-quiz-attempt-delete').on('click', function (e) {
        e.preventDefault();
        attempts.deleteQuizAttempt($(this).data('id'));
      });

      $('#doaction, #doaction2').on('click', function (e) {
        var action = $('#bulk-action-selector-top').val();

        if(action === 'delete' && !confirm(MpcsQuizAttemptsL10n.quiz_attempt_delete_bulk_confirm)) {
          e.preventDefault();
        }
      });
    },

    viewQuizAttempt: function (id) {
      if(!window.vex) {
        attempts.viewQuizAttemptError('Popup script missing');
        return;
      }

      $.ajax({
        method: 'GET',
        url: MpcsQuizAttemptsL10n.ajax_url,
        data: {
          action: 'mpcs_quiz_attempt_view',
          id: id
        }
      })
      .done(function (response) {
        if(response && typeof response.success == 'boolean') {
          if(response.success) {
            vex.dialog.open({
              unsafeMessage: response.data,
              className: 'vex-theme-plain mpcs-quiz-attempt-vex',
              buttons: [],
              showCloseButton: true
            });
          }
          else {
            attempts.viewQuizAttemptError(response.data);
          }
        } else {
          attempts.viewQuizAttemptError('Invalid response');
        }
      })
      .fail(function () {
        attempts.viewQuizAttemptError('Request failed');
      });
    },

    viewQuizAttemptError: function(message) {
      alert('Failed to view the attempt. Check the browser console for more information if this issue persists.');
      console.log(message);
    },

    deleteQuizAttempt: function (id) {
      if(!confirm(MpcsQuizAttemptsL10n.quiz_attempt_delete_confirm)) {
        return;
      }

      $.ajax({
        method: 'POST',
        url: MpcsQuizAttemptsL10n.ajax_url,
        data: {
          action: 'mpcs_quiz_attempt_delete',
          _ajax_nonce: MpcsQuizAttemptsL10n.quiz_attempt_delete_nonce,
          id: id
        }
      })
      .done(function (response) {
        if(response && typeof response.success == 'boolean') {
          if(response.success) {
            $('#mpcs-attempt-row-' + id).fadeOut(function () {
              $(this).remove();
            });
          }
          else {
            attempts.deleteQuizAttemptError(response.data);
          }
        } else {
          attempts.deleteQuizAttemptError('Invalid response');
        }
      })
      .fail(function () {
        attempts.deleteQuizAttemptError('Request failed');
      });
    },

    deleteQuizAttemptError: function (message) {
      alert('Failed to delete the attempt. Check the browser console for more information if this issue persists.');
      console.log(message);
    }
  };

  $(attempts.initialize);

  return attempts;
})(jQuery);
