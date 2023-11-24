jQuery(document).ready(function ($) {
  // Handle notice dismiss & postpone actions
  $(document).on('click', '.uc-admin-notice [data-action="dismiss"], .uc-admin-notice [data-action="postpone"]', function (event) {
    event.preventDefault();

    var $action = $(this);
    var $root = $action.closest('.uc-admin-notice');
    var url = $action.attr('data-ajax-url');

    $root.slideUp(200, function () {
      $root.remove();
    });

    $.post(url);
  });
});
