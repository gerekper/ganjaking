/**
 * YITH WooCommerce Multi Step Checkout
 * @version 2.0.0
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
(function ($) {
  var $body = $('body');

  $.yith_wcmv_deps = function (option, dep_to, value, type, equal_to) {
    var
      $current_field = $(option),
      $current_container = null,
      event = null,
      $dep_to = null,
      $check = false;

    if (type === 'toggle-element-fixed') {
      $current_container = $current_field.closest('div.yith-toggle-content-row');
    } else {
      $current_container = $current_field.closest('tr');
    }

    if (type === 'select-images') {
      event = 'click';
      $dep_to = $(dep_to + '-wrapper').find('li.yith-plugin-fw-select-images__item');
    } else {
      event = 'change';
      $dep_to = $(dep_to);
    }

    $(document).on('yith_wcms_deps_init', function () {
      var current_orientation_style = $('input[name=yith_wcms_timeline_display]:checked');
      change_images_preview(current_orientation_style);
    });
  
    $dep_to.on(event, function () {
      var val = '',
        t = $(this);

      if (type === 'select-images') {
        val = t.data('key');
      } else {
        val = t.val();
      }
      if( option == '#yith_wcms_text_step_separator_onoff' ){
        console.log( val );
      }
      if (equal_to === true) {
        $check = val !== value;
      } else {
        $check = val === value;
      }

      if ($check) {
        if (type === 'toggle-element-fixed' || type === 'onoff-animated') {
          if (!$current_container.hasClass('fade-in')) {
            $current_container.hide();
            $current_container.css({ 'opacity': '0' });
          } else {
            $current_container.fadeTo("slow", 0, function () {
              $(this).hide().removeClass('fade-in');
            });
          }
        } else {
          $current_container.addClass('yith-wcms-disabled');
        }
      } else {

        if (type === 'toggle-element-fixed' || type === 'onoff-animated') {
          $current_container.show();
          $current_container.fadeTo("slow", 1).addClass('fade-in');
        } else {
          $current_container.removeClass('yith-wcms-disabled');
        }
      }
    });
    
    if (type === 'select-images') {
      $dep_to = $(dep_to + '-wrapper').find('li.yith-plugin-fw-select-images__item--selected');
    }
    
    $dep_to.trigger(event);
  };

  /**
   * Change image for select images
   */
  var change_images_preview = function (elem) {
    var
      current = elem.val(),
      img_wrapper = $('#yith_wcms_timeline_template-wrapper');
    img_wrapper.find('li').each(function () {
      var li = $(this);
      li.find('img.yith-plugin-fw-select-images_src').attr('src', li.data(current));
      if (current === 'vertical') {
        li.addClass('vertical').removeClass('horizontal');
      } else {
        li.removeClass('vertical').addClass('horizontal');
      }
    });
  }

  if ($body.hasClass('yith-plugin-fw-panel')) {

    if (yith_wcms_admin.current_tab === 'steps') {
      /* Deps */
      $.yith_wcmv_deps('#yith_wcms_text_step_separator_onoff', '#yith_wcms_timeline_template', 'text', 'select-images', true);
      $.yith_wcmv_deps('#yith_wcms_text_step_separator', '#yith_wcms_timeline_template', 'text', 'select-images', true);
      $.yith_wcmv_deps('#yith_wcms_show_step_number', '#yith_wcms_timeline_template', 'text', 'select-images', false);

      /* Login Toggle Deps */
      $.yith_wcmv_deps('#yith_wcmv_login_settings_yith_wcms_form_checkout_login_message', '#yith_wcmv_login_settings_woocommerce_enable_checkout_login_reminder', 'yes', 'toggle-element-fixed', true);
      $.yith_wcmv_deps('#yith_wcmv_login_settings_yith_wcms_timeline_use_my_account_in_login_step', '#yith_wcmv_login_settings_woocommerce_enable_checkout_login_reminder', 'yes', 'toggle-element-fixed', true);
      $.yith_wcmv_deps('#yith_wcmv_login_settings_woocommerce_enable_myaccount_registration', '#yith_wcmv_login_settings_woocommerce_enable_checkout_login_reminder', 'yes', 'toggle-element-fixed', true);

      /* Login Icons */
      $.yith_wcmv_deps('#yith_wcmv_login_settings_yith_wcms_timeline_options_default_icon_login', '#yith_wcmv_login_settings_yith_wcms_use_icon_login', 'default-icon', 'toggle-element-fixed', true);
      $.yith_wcmv_deps('#yith_wcmv_login_settings_yith_wcms_timeline_options_icon_login', '#yith_wcmv_login_settings_yith_wcms_use_icon_login', 'custom-icon', 'toggle-element-fixed', true);

      /* Billing Icons */
      $.yith_wcmv_deps('#yith_wcmv_billing_settings_yith_wcms_timeline_options_default_icon_billing', '#yith_wcmv_billing_settings_yith_wcms_use_icon_billing', 'default-icon', 'toggle-element-fixed', true);
      $.yith_wcmv_deps('#yith_wcmv_billing_settings_yith_wcms_timeline_options_icon_billing', '#yith_wcmv_billing_settings_yith_wcms_use_icon_billing', 'custom-icon', 'toggle-element-fixed', true);

      /* Shipping Icons */
      $.yith_wcmv_deps('#yith_wcmv_shipping_settings_yith_wcms_timeline_options_default_icon_shipping', '#yith_wcmv_shipping_settings_yith_wcms_use_icon_shipping', 'default-icon', 'toggle-element-fixed', true);
      $.yith_wcmv_deps('#yith_wcmv_shipping_settings_yith_wcms_timeline_options_icon_shipping', '#yith_wcmv_shipping_settings_yith_wcms_use_icon_shipping', 'custom-icon', 'toggle-element-fixed', true);

      /* Payment Icons */
      $.yith_wcmv_deps('#yith_wcmv_order_info_settings_yith_wcms_timeline_options_default_icon_order', '#yith_wcmv_order_info_settings_yith_wcms_use_icon_order', 'default-icon', 'toggle-element-fixed', true);
      $.yith_wcmv_deps('#yith_wcmv_order_info_settings_yith_wcms_timeline_options_icon_order', '#yith_wcmv_order_info_settings_yith_wcms_use_icon_order', 'custom-icon', 'toggle-element-fixed', true);

      /* Payment Icons */
      $.yith_wcmv_deps('#yith_wcmv_payment_settings_yith_wcms_timeline_options_default_icon_payment', '#yith_wcmv_payment_settings_yith_wcms_use_icon_payment', 'default-icon', 'toggle-element-fixed', true);
      $.yith_wcmv_deps('#yith_wcmv_payment_settings_yith_wcms_timeline_options_icon_payment', '#yith_wcmv_payment_settings_yith_wcms_use_icon_payment', 'custom-icon', 'toggle-element-fixed', true);

      /* Step Merging */
      $.yith_wcmv_deps('#yith_wcmv_shipping_settings', '#yith_wcmv_billing_settings_yith_wcms_timeline_options_merge_billing_and_shipping_step', 'yes', 'onoff-animated', false);
      $.yith_wcmv_deps('#yith_wcmv_payment_settings', '#yith_wcmv_order_info_settings_yith_wcms_timeline_options_merge_order_and_payment_step', 'yes', 'onoff-animated', false);

      $(document).on('change', 'input[name=yith_wcms_timeline_display]', function (e) {
        change_images_preview($(this));
      });

      /**
       * Add preview in select icons
       */
      var select_icons = [
        '#yith_wcmv_login_settings_yith_wcms_timeline_options_default_icon_login',
        '#yith_wcmv_billing_settings_yith_wcms_timeline_options_default_icon_billing',
        '#yith_wcmv_shipping_settings_yith_wcms_timeline_options_default_icon_shipping',
        '#yith_wcmv_order_info_settings_yith_wcms_timeline_options_default_icon_order',
        '#yith_wcmv_payment_settings_yith_wcms_timeline_options_default_icon_payment'
      ];

      $.each(select_icons, function (index, value) {
        var icons_list = $(value);

        icons_list.each(function () {
          var t = $(this),
            renderOptions = function (state) {
              if (!state.id) {
                return state.text;
              }

              return $('<span class="yith-wcms-default-icon-wrapper"><img class="yith-wcms-default-icon-item" width="15px" height="15px" src="' + yith_wcms_admin.icons_url + state.element.value.toLowerCase() + '.svg">' + state.text + '</span>');
            };

          t.select2({
            templateResult: renderOptions
          });
        });

        yith_wcms_admin.icons_path;

      });
    }

    if (yith_wcms_admin.current_tab === 'buttons') {
      $.yith_wcmv_deps('#yith_wcms_navigation_buttons_background_colors', '#yith_wcms_nav_buttons_enabled', 'yes', 'onoff', true);
      $.yith_wcmv_deps('#yith_wcms_navigation_buttons_text_colors', '#yith_wcms_nav_buttons_enabled', 'yes', 'onoff', true);
      $.yith_wcmv_deps('#yith_wcms_back_to_cart_button_background_colors', '#yith_wcms_nav_enable_back_to_cart_button', 'yes', 'onoff', true);
      $.yith_wcmv_deps('#yith_wcms_back_to_cart_button_text_colors', '#yith_wcms_nav_enable_back_to_cart_button', 'yes', 'onoff', true);
    }

    /**
     * Deps Init
     */
    $(document).trigger('yith_wcms_deps_init');
  }

})(jQuery);
