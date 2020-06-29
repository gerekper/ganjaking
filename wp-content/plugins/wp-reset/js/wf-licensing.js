/**
 * WebFactory Licensing Manager
 * (c) WebFactory Ltd
 * www.webfactoryltd.com
 */


function wf_licensing_verify_licence_ajax(prefix, license_key, button) {
  data = window['wf_licensing_' + prefix];
  if (!data) {
    alert('Licensing data is missing. Please reload the page and try again.');
    return;
  }

  jQuery(button).addClass('loading');

  jQuery
    .post(
      ajaxurl,
      {
        action: 'wf_licensing_' + prefix + '_validate',
        license_key: license_key,
        _ajax_nonce: data.nonce,
        _rand: Math.floor(Math.random() * 9999) + 1
      },
      function (response) {
        if (data.debug) {
          console.log('Validate license, first try: ', response);
        }
        if (response.success) {
          location.reload();
        } else {
          alert('Unable to contact licensing server. Please try again in a few moments or contact support.');
        }
      }
    )
    .fail(function () {
      alert('Undocumented error. Please reload the page and try again.');
    })
    .always(function () {
      jQuery(button).removeClass('loading');
      jQuery(window).trigger('wf_licensing:ajax_always');
    });
} // wf_licensing_verify_licence_ajax


function wf_licensing_deactivate_licence_ajax(prefix, license_key, button) {
  data = window['wf_licensing_' + prefix];
  if (!data) {
    alert('Licensing data is missing. Please reload the page and try again.');
    return;
  }

  jQuery(button).addClass('loading');

  jQuery
    .post(
      ajaxurl,
      {
        action: 'wf_licensing_' + prefix + '_deactivate',
        license_key: license_key,
        _ajax_nonce: data.nonce,
        _rand: Math.floor(Math.random() * 9999) + 1
      },
      function (response) {
        if (data.debug) {
          console.log('Deactivate license, first try: ', response);
        }
        if (response.success) {
          location.reload();
        } else {
          alert('Unable to contact licensing server. Please try again in a few moments or contact support.');
        }
      }
    )
    .fail(function () {
      alert('Undocumented error. Please reload the page and try again.');
    })
    .always(function () {
      jQuery(button).removeClass('loading');
      jQuery(window).trigger('wf_licensing:ajax_always');
    });
} // wf_licensing_deactivate_licence_ajax
