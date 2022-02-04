var MpcsQuizEditor = (function ($) {
  var quizEditor,
    working = false;

  quizEditor = {
    initialize: function () {
      if(MpcsQuizEditorL10n.hasAttempts) {
        vex.dialog.open({
          unsafeMessage: MpcsQuizEditorL10n.quizLockedMessage,
          className: 'vex-theme-plain mpcs-quiz-locked-vex',
          buttons: [
            {
              type: 'button',
              text: MpcsQuizEditorL10n.delete,
              className: "vex-dialog-button-secondary",
              click: quizEditor.deleteAllAttempts
            },
            {
              type: 'button',
              text: MpcsQuizEditorL10n.cancel,
              className: "vex-dialog-button-primary",
              click: function () {
                window.location.href = MpcsQuizEditorL10n.courseUrl;
              }
            },
          ],
          showCloseButton: false,
          escapeButtonCloses: false,
          overlayClosesOnClick: false,
        });
      }
    },

    deleteAllAttempts: function (e) {
      if(!confirm(MpcsQuizEditorL10n.confirmDeleteAllQuizAttempts)) {
        return;
      }

      if (working) {
        return;
      }

      working = true;

      var $button = $(e.target),
        original_button_html = $button.html(),
        original_button_width = $button.width();

      $button.html('<i class="mpcs-spinner mpcs-animate-spin"></i>').width(original_button_width);

      $.ajax({
        method: 'POST',
        url: MpcsQuizEditorL10n.ajaxUrl,
        data: {
          action: 'mpcs_delete_all_attempts',
          _ajax_nonce: MpcsQuizEditorL10n.deleteAllAttemptsNonce,
          quiz_id: MpcsQuizEditorL10n.quizId,
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            $button.html('<i class="mpcs-option-check"></i>');
            window.location.reload();
            return;
          }
          else {
            quizEditor.deleteAllAttemptsError(response.data);
          }
        }
        else {
          quizEditor.deleteAllAttemptsError('Invalid response');
        }

        $button.html(original_button_html).width('auto');
      })
      .fail(function () {
        $button.html(original_button_html).width('auto');
        quizEditor.deleteAllAttemptsError('Request failed');
      })
      .always(function () {
        working = false;
      });
    },

    deleteAllAttemptsError: function (message) {
      alert('Failed to delete the attempts. Check the browser console for more information if this issue persists.');
      console.log(message);
    }
  };

  $(quizEditor.initialize);

  return quizEditor;
})(jQuery);
