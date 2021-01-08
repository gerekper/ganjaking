(function($) {
  $(document).ready(function() {
    // Adjust column width
    $('body th#col_approval').css("width", "100px"); //Could get away with 60px, but 100px gives room for Approve | Reject on one line

    // Ajax
    function mpmma_approve_reject_member_call(user_id, action, silent = 0) {
      var data = {
        action: action,
        user_id: user_id,
        silent: silent
      };

      $.post(ajaxurl, data, function(response) {
        $('span#mpmma_spinner_' + user_id).hide();
        $('span#mpmma_status_wrap_' + user_id).html(response.toString().trim()).fadeIn();
      });
    }

    // Approve
    $('.mpmma_approve').click(function(e) {
      e.preventDefault();
      var user_id = $(this).data('userid');
      $('span#mpmma_status_wrap_' + user_id).hide();
      $('span#mpmma_reject_wrap_' + user_id).hide();
      $('span#mpmma_reject_silent_wrap_' + user_id).hide();
      $('span#mpmma_approve_wrap_' + user_id).hide();
      $('span#mpmma_approve_silent_wrap_' + user_id).hide();
      $('span#mpmma_spinner_' + user_id).fadeIn();
      mpmma_approve_reject_member_call(user_id, 'mpmma_approve_member');
    });

    // Approve Silent
    $('.mpmma_approve_silent').click(function(e) {
      e.preventDefault();
      var user_id = $(this).data('userid');
      $('span#mpmma_status_wrap_' + user_id).hide();
      $('span#mpmma_reject_wrap_' + user_id).hide();
      $('span#mpmma_reject_silent_wrap_' + user_id).hide();
      $('span#mpmma_approve_wrap_' + user_id).hide();
      $('span#mpmma_approve_silent_wrap_' + user_id).hide();
      $('span#mpmma_spinner_' + user_id).fadeIn();
      mpmma_approve_reject_member_call(user_id, 'mpmma_approve_member', 1);
    });

    // Reject
    $('.mpmma_reject').click(function(e) {
      e.preventDefault();
      var user_id = $(this).data('userid');
      $('span#mpmma_status_wrap_' + user_id).hide();
      $('span#mpmma_reject_wrap_' + user_id).hide();
      $('span#mpmma_reject_silent_wrap_' + user_id).hide();
      $('span#mpmma_approve_wrap_' + user_id).hide();
      $('span#mpmma_approve_silent_wrap_' + user_id).hide();
      $('span#mpmma_spinner_' + user_id).fadeIn();
      mpmma_approve_reject_member_call(user_id, 'mpmma_reject_member');
    });

    // Reject Silently
    $('.mpmma_reject_silent').click(function(e) {
      e.preventDefault();
      var user_id = $(this).data('userid');
      $('span#mpmma_status_wrap_' + user_id).hide();
      $('span#mpmma_reject_wrap_' + user_id).hide();
      $('span#mpmma_reject_silent_wrap_' + user_id).hide();
      $('span#mpmma_approve_wrap_' + user_id).hide();
      $('span#mpmma_approve_silent_wrap_' + user_id).hide();
      $('span#mpmma_spinner_' + user_id).fadeIn();
      mpmma_approve_reject_member_call(user_id, 'mpmma_reject_member', 1);
    });
    
  });
})(jQuery);
