(function ($) {

  'use strict';
  $(function () {
    $(this)
      .find('.gm-theme-migrate__button')
      .on('click',
        function (e) {
          e.preventDefault();
          var self = $(this);

          if (self.hasClass('disabled')) {
            return;
          }

          self.addClass('disabled');

          $.ajax({
            type: 'POST',
            url: ajaxurl,
            dataType: 'json',
            data: {
              action: 'gm_ajax_start_migrate'
            },
            error: function (result) {
              self.removeClass('disabled');
              $('.gm-theme-migrate__notice-wrapper')
                .append(result.message);
            },
            success: function (result) {
              if (result.code === 'none') {
                $('.gm-theme-migrate__notice-wrapper')
                  .remove();
                alert(result.message);
              }

              if (result.code === 'background') {
                $('.gm-theme-migrate__notice-wrapper')
                  .html(result.message);
              }

            }.bind(this)
          });

        }
      );

    $(this)
      .on('click', '.gm-theme-migrate__notice-info .notice-dismiss', function () {
        $.ajax(
          ajaxurl,
          {
            type: 'POST',
            data: {
              action: 'gm_dismissed_migration_notice_info'
            }
          });
      });


    // Migration debug page actions.
    $(this)
      .on('click', '.gm-migrate-debug-action-btn', function () {

        var actionValue = $(this).attr('data-action');
        var versionValue = $(this).attr('data-version');

        $.ajax({
          type: 'POST',
          url: ajaxurl,
          dataType: 'json',
          data: {
            action: actionValue,
            version: versionValue
          },
          error: function (result) {
            console.log('ajax error or action not implemented: ' + actionValue);
            alert('ajax error');
          },
          success: function (result) {
            if (actionValue === 'gm_migrate_log') {
              $('.gm-debug-log-block-wrapper').removeClass('gm-debug-log-hidden');
              $('#gm-debug-log-block .gm-debug-log-block-title span').html(versionValue);
              $('#gm-debug-log-block .gm-debug-log-block-content').html(result.message);
            } else {
              window.location.reload(false);
            }
          }.bind(this)
        });

      });


  });
})(jQuery);