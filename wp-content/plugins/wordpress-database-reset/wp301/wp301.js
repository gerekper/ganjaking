/**
 * Campaign for WP 301 Redirects PRO
 * (c) WebFactory Ltd, 2020
 */

jQuery(document).ready(function ($) {
  $('#wp301promo_dismiss').on('click', function (e) {
    e.preventDefault();

    var slug = $(this).data('plugin-slug');

    $.get({
      url: ajaxurl,
      data: {
        action: 'wp301_promo_dismiss',
        slug: slug,
        _ajax_nonce: wp301_promo.nonce_wp301_promo_dismiss,
      },
    })
      .always(function (data) {})
      .done(function (data) {
        if (data.success) {
          if (slug == 'dashboard') {
            $('#wp301promo_widget').hide();
          } else {
            $('#wp301-dialog').dialog('close');
          }
        } else {
          alert('Sorry, something is not right. Please reload the page and try again.');
        }
      })
      .fail(function (data) {
        alert('Sorry, something is not right. Please reload the page and try again.');
      });
  }); // dismiss


  $('#wp301promo_submit').on('click', function (e) {
    e.preventDefault();

    var btn = $('#wp301promo_submit');
    var name = $('#wp301promo_name').val();
    var email = $('#wp301promo_email').val();
    var plugin = $('#wp301promo_plugin').val();
    var position = $('#wp301promo_position').val();
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

    if (name.length < 2 || name.length > 128) {
      $('#wp301promo_name').focus();
      alert('Please enter your name. Thank you 👍');
      return false;
    }
    if (!regex.test(email) || email.lenght > 128) {
      $('#wp301promo_email').focus();
      alert('Please enter a valid email address. Thank you 👍');
      return false;
    }

    $(btn).addClass('disabled');
    $.get({
      url: ajaxurl,
      data: {
        action: 'wp301_promo_submit',
        _ajax_nonce: wp301_promo.nonce_wp301_promo_submit,
        name: name,
        email: email,
        position: position,
        plugin: plugin,
      },
    })
      .always(function (data) {
        $(btn).removeClass('disabled');
      })
      .done(function (data) {
        if (data.success) {
          alert(data.data);

          if (position == 'dashboard') {
            $('#wp301promo_widget').hide();
          } else {
            $('#wp301-dialog').dialog('close');
          }
        } else {
          alert(data.data);
        }
      })
      .fail(function (data) {
        alert('Sorry, something is not right. Please reload the page and try again.');
      });
  });


  $('#wp301promo_name, #wp301promo_email').on('keypress', function (e) {
    if (e.which == 13) {
      $('#wp301promo_submit').trigger('click');
    }
  }); // on enter


  if (wp301_promo.open_popup && $('#wp301-dialog').length == 1) {
    $('#wp301-dialog').dialog({
      dialogClass: 'wp-dialog wp301-dialog',
      modal: true,
      resizable: false,
      width: 550,
      height: 'auto',
      show: 'fade',
      hide: 'fade',
      close: function (event, ui) {},
      open: function (event, ui) {
        $(this)
          .siblings()
          .find('span.ui-dialog-title')
          .html('Get a WP 301 Redirects PRO license for FREE <del>$158</del>');
        wp301_fix_dialog_close(event, ui);
      },
      autoOpen: true,
      closeOnEscape: false,
    });
  } // open dialog
}); // jQuery ready


function wp301_fix_dialog_close(event, ui) {
  jQuery('.ui-widget-overlay').bind('click', function () {
    jQuery('#' + event.target.id).dialog('close');
  });
} // wp301_fix_dialog_close
