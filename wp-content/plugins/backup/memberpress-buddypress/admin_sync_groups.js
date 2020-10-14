jQuery(function ($) {
  var $button = $('#mepr-bb-sync-groups'),
    $status = $('#mepr-bb-sync-groups-status');

  $button.click(function () {
    $button.prop('disabled', true);
    $status.html('<i class="mp-icon mp-icon-spinner animate-spin" aria-hidden="true"></i>').css('display', 'inline-block');

    $.ajax({
      type: 'POST',
      url: MeprBuddyPressSyncGroups.ajax_url,
      dataType: 'json',
      data: {
        action: 'mepr_bp_sync_groups',
        _ajax_nonce: MeprBuddyPressSyncGroups.nonce,
        user_id: $(this).data('user-id')
      }
    }).done(function (response) {
      if(response && typeof response == 'object' && response.success) {
        $status.html('<i class="mp-icon mp-icon-ok" style="color:#4d8c2e;" aria-hidden="true"></i>');
      }
      else {
        $status.html('<i class="mp-icon mp-icon-cancel" style="color:#d40022;" aria-hidden="true"></i>');
      }
    }).fail(function () {
      $status.html('<i class="mp-icon mp-icon-cancel" style="color:#d40022;" aria-hidden="true"></i>');
    }).always(function () {
      $button.prop('disabled', false);
    });
  });
});
