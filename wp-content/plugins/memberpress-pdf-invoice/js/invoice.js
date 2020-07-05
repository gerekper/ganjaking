jQuery(document).ready(function ($) {
  $('body').on('click', '#mepr-biz-logo-remove', function (e) {
    e.preventDefault();
    $('input[name="mepr_biz_logo_remove"]').val('1');
    $(this).closest('form').submit();
  });

  // Add Color Picker to all inputs that have 'color-field' class
  $('.mepr-color-picker').wpColorPicker();

});
