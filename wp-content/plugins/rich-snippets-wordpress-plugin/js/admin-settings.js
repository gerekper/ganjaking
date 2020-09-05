var rich_snippets_admin_settings;

(
  function () {
    'use strict'

    rich_snippets_admin_settings = function () {

      this.init = function () {
        var self = this

        jQuery(document).on('click', '.wpb-rs-clear-cache', function (e) {
          e.preventDefault()
          self.clear_cache(jQuery(this))
        })
      }

      this.before_ajax_send = function ($clicked_el) {
        $clicked_el.addClass('install-now updating-message')
      }

      this.ajax_complete = function ($clicked_el) {
        $clicked_el.removeClass('install-now').removeClass('updating-message')
        $clicked_el.addClass('dashicons-before dashicons-yes wpb-rs-ajax-finished')
        setTimeout(function () {
          $clicked_el.removeClass('dashicons-before').removeClass('dashicons-yes').removeClass('wpb-rs-ajax-finished')
        }, 5000)
      }

      this.clear_cache = function ($clicked_el) {
        var self = this

        jQuery.ajax({
          'url':        WPB_RS_SETTINGS.rest_url + '/clear_cache/',
          'dataType':   'json',
          'beforeSend': function (xhr) {
            xhr.setRequestHeader('X-WP-Nonce', WPB_RS_SETTINGS.nonce)
            self.before_ajax_send($clicked_el)
          },
          'complete':   function () {
            self.ajax_complete($clicked_el)
          },
          'data':       {},
          'method':     'GET',
          'success':    function (data) {

          },
        })
      }

    }

    jQuery(document).ready(function () {
      new rich_snippets_admin_settings().init()
    })

  }
)()



