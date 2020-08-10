jQuery( document ).ready(function() {
  "use strict";

  /* General settings */

  function changeLogo () {
    var  logo = jQuery(".custom-img-id");

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      action: "energyplus_ajax",
      segment: logo.attr('data-segment'),
      section: logo.attr('data-section'),
      feature: logo.attr('data-feature'),
      state: false,
      val: logo.val()
    }, function() {
      EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
    }
  );
}

jQuery(".__A__OnOff").on( "change",function() {
  EnergyPlusAjax();

  var feature = jQuery(this).attr('data-feature');

  jQuery.post( EnergyPlusGlobal.ajax_url, {
    _wpnonce: jQuery('input[name=_wpnonce]').val(),
    _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
    _asnonce: EnergyPlusGlobal._asnonce,
    action: "energyplus_ajax",
    segment: jQuery(this).attr('data-segment'),
    section: jQuery(this).attr('data-section'),
    feature: jQuery(this).attr('data-feature'),
    state: jQuery(this).prop('checked')
  }, function() {
    if (true === jQuery('.__A__OnOff[data-feature="use-shop_manager"]').prop('checked') && true === jQuery('.__A__OnOff[data-feature="use-administrator"]').prop('checked')) {
      jQuery('.__A__Item_ForceToUse').slideUp('fast');
    } else {
      jQuery('.__A__Item_ForceToUse').slideDown('fast');

    }

    EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
  }
);
});

jQuery(".__A__Options_Badge").on( "change",function() {
  EnergyPlusAjax();

  jQuery.post( EnergyPlusGlobal.ajax_url, {
    _wpnonce: jQuery('input[name=_wpnonce]').val(),
    _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
    _asnonce: EnergyPlusGlobal._asnonce,
    action: "energyplus_ajax",
    segment: jQuery(this).attr('data-segment'),
    section: jQuery(this).attr('data-section'),
    feature: jQuery(this).attr('data-feature'),
    val: jQuery('.__A__Options_Badge:checked').val()
  }, function() {
    EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
  }
);
});

jQuery(".__A__Settings_Input").doWithDelay("keyup", function(e) {

  EnergyPlusAjax();

  jQuery.post( EnergyPlusGlobal.ajax_url, {
    _wpnonce: jQuery('input[name=_wpnonce]').val(),
    _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
    _asnonce: EnergyPlusGlobal._asnonce,
    action: "energyplus_ajax",
    segment: jQuery(this).attr('data-segment'),
    section: jQuery(this).attr('data-section'),
    feature: jQuery(this).attr('data-feature'),
    val: jQuery(this).val()
  }, function() {
    EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
  }
);

}, 500);


jQuery(".energyplus-settigns-general--themes-change").on( "click", function() {
  EnergyPlusAjax();

  jQuery.post( EnergyPlusGlobal.ajax_url, {
    _wpnonce: jQuery('input[name=_wpnonce]').val(),
    _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
    _asnonce: EnergyPlusGlobal._asnonce,
    action: "energyplus_settings",
    section: 'themes',
    theme: jQuery(this).attr('data-theme')
  }, function() {
    EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
    location.reload(true);
  });
});

// LOGO
var frame,
metaBox = jQuery('#energyplus-user-logo'),
addImgLink = metaBox.find('.upload-custom-img'),
imgContainer = jQuery(".__A__Settings_Logo"),
imgIdInput = metaBox.find( '.custom-img-id' );

addImgLink.on( 'click', function( event ){

  event.preventDefault();

  if ( frame ) {
    frame.open();
    return;
  }

  frame = wp.media({
    title: 'Select or upload media',
    button: {
      text: 'Use this media'
    },
    multiple: false
  });

  frame.on( 'select', function() {
    var attachment = frame.state().get('selection').first().toJSON();
    imgContainer.html( '<img src="'+attachment.url+'" alt="" class="__A__Settings_Logo_New" />' );
    imgIdInput.val( attachment.id );
    changeLogo();
  });

  frame.open();
});

/* Panels */

jQuery(".energyplus-modes").on( "click",function() {
  EnergyPlusAjax();

  jQuery.post( EnergyPlusGlobal.ajax_url, {
    _wpnonce: jQuery('input[name=_wpnonce]').val(),
    _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
    _asnonce: EnergyPlusGlobal._asnonce,
    action: "energyplus_ajax",
    segment: 'settings',
    section: 'modes',
    panel: jQuery(this).attr('data-panel'),
    state: jQuery(this).val()
  }, function(r) {
    EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
  }
);
});

jQuery(".energyplus-panel-item-onoff ").on( "click",function() {
  EnergyPlusAjax();
  jQuery.post( EnergyPlusGlobal.ajax_url, {
    _wpnonce: jQuery('input[name=_wpnonce]').val(),
    _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
    _asnonce: EnergyPlusGlobal._asnonce,
    action: "energyplus_settings_panels",
    panel: jQuery(this).attr("data-id"),
    for: jQuery(this).attr("data-for"),
    state:  jQuery(this).prop("checked")
  }, function(r) {
    if (1 === r.status) {
      EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
    } else {
      EnergyPlusAjax('error', r.error);
    }
  }, "json");
});


jQuery(".energyplus-panel-item--delete").on( "click",function() {
  EnergyPlusAjax();
  jQuery.post( EnergyPlusGlobal.ajax_url, {
    _wpnonce: jQuery('input[name=_wpnonce]').val(),
    _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
    _asnonce: EnergyPlusGlobal._asnonce,
    action: "energyplus_settings_panels",
    panel: jQuery(this).attr("data-id"),
    state: -2
  }, function(r) {
    EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
    location.reload();
  });
});

jQuery("#energyplus-panel-item--new-save").on( "click",function() {

  jQuery(this).val("Please wait").attr('disabled', true);

  EnergyPlusAjax();

  jQuery.post( EnergyPlusGlobal.ajax_url, {
    _wpnonce: jQuery('input[name=_wpnonce]').val(),
    _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
    _asnonce: EnergyPlusGlobal._asnonce,
    action: "energyplus_settings_panels",
    panel: '-1',
    parent: jQuery("#energyplus-panel-item--new-parent").val(),
    title: jQuery("#energyplus-panel-item--new-title").val(),
    url: jQuery("#energyplus-panel-item--new-url").val()
  }, function (r) {
    if (r.return === 'success')
    {
      EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      window.location.href = window.location.href + "&r="+(+new Date())+"#" + r.anchor;
      location.reload(true);
    } else {
      EnergyPlusAjax('error', r.error);
      jQuery(this).val("Please wait").attr('disabled', false);

    }
  }, "json");
});



jQuery("#energyplus-reset-menu").on( "click",function() {

  EnergyPlusAjax();

  jQuery.post( EnergyPlusGlobal.ajax_url, {
    _wpnonce: jQuery('input[name=_wpnonce]').val(),
    _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
    _asnonce: EnergyPlusGlobal._asnonce,
    action: "energyplus_ajax",
    segment: 'settings',
    section: 'reset-menu'
  }, function (r) {
    if (r.return === 'success')
    {
      EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
      location.reload(true);
    } else {
      EnergyPlusAjax('error', r.error);
    }
  }, "json");
});

jQuery(".__A__Settings_Color").on( "mouseover", function() {
  var colors = jQuery(this).data('colors');

  if (!jQuery(this).hasClass('__A__Settings_Color_Own')) {
    jQuery(".__A__Settings_Color_Own_Div").addClass('d-none');
  }
  jQuery.each( colors, function( key, value ) {
    document.documentElement.style.setProperty("--"+key, value);
  });
});

jQuery(".__A__Settings_Color_Own").on( "mouseover", function(e) {
  e.preventDefault();

  var colors = jQuery(this).data('colors');
  var colors_temp = colors;

  jQuery(".__A__Settings_Color_Own_Div").removeClass('d-none');
  jQuery('.energyplus-header-top').css({'border-bottom': '0px'});

  jQuery.each( colors, function( key, value ) {
    jQuery('.__A__Settings_Color_Own-'+key).wpColorPicker({
      width:160,
      change: function(event, ui){
        document.documentElement.style.setProperty("--"+key, ui.color.toString());
        colors_temp[key] = ui.color.toString();
        jQuery(".__A__Settings_Color_Own").attr('data-colors', JSON.stringify(colors_temp));
        if ('header-background' === key) {
          document.documentElement.style.setProperty("--header-more", LightenDarkenColor(ui.color.toString(), -60));
        } else if ('header-icons' === key) {
          document.documentElement.style.setProperty("--header-text", ui.color.toString());
        }

      }});

      jQuery('.__A__Settings_Color_Own-'+key).wpColorPicker('color',value);
    });

  });

  jQuery(".__A__Settings_Color, .__A__Settings_Color_Own_Save").on( "click", function(e) {
    var colors;

    jQuery('.__A__Settings_Color_Selected').removeClass('__A__Settings_Color_Selected');
    jQuery(this).addClass('__A__Settings_Color_Selected');

    if (jQuery(this).hasClass('__A__Settings_Color_Own_Save')) {
      colors = jQuery(".__A__Settings_Color_Own").data('colors');
    } else {
      colors = jQuery(this).data('colors');
    }

    EnergyPlusAjax();

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      action: "energyplus_ajax",
      segment: 'settings',
      section: 'colors',
      val: colors
    }, function() {
      EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
    }
  );
});


// Reorder menu items
var currentlyScrolling = false;

var SCROLL_AREA_HEIGHT = 80; // Distance from window's top and bottom edge.

if (jQuery('.energyplus-settings-panels').length > 0) {
  var ns = jQuery('.energyplus-settings-panels').sortable({
    axis: "y",
    handle: ".__A__Products_Hand",
    scroll: true,
    sort: function(event, ui) {

      if (currentlyScrolling) {
        return;
      }

      var windowHeight   = jQuery(window).height();
      var mouseYPosition = event.clientY;

      if (mouseYPosition < SCROLL_AREA_HEIGHT) {
        currentlyScrolling = true;

        jQuery('html, body').animate({
          scrollTop: "-=" + windowHeight / 2 + "px" // Scroll up half of window height.
        },
        400, // 400ms animation.
        function() {
          currentlyScrolling = false;
        });

      } else if (mouseYPosition > (windowHeight - SCROLL_AREA_HEIGHT)) {

        currentlyScrolling = true;

        jQuery('html, body').animate({
          scrollTop: "+=" + windowHeight / 2 + "px" // Scroll down half of window height.
        },
        400, // 400ms animation.
        function() {
          currentlyScrolling = false;
        });

      }
    },
    start: function(event, ui) {
      jQuery('.energyplus-settings-panels .__A__Description').hide();
      jQuery('.energyplus-settings-panels li').addClass('__A___Settings_Drags');
      ui.placeholder.height(120);
      ui.helper.height(120);

    },
    stop: function(event, ui) {
      var arr = jQuery('.energyplus-settings-panels').sortable('toArray');


      EnergyPlusAjax();

      jQuery.post( EnergyPlusGlobal.ajax_url, {
        _wpnonce: jQuery('input[name=_wpnonce]').val(),
        _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
        _asnonce: EnergyPlusGlobal._asnonce,
        action: "energyplus_ajax",
        segment: 'settings',
        section: 'reorder',
        ids: arr
      }, function(r) {
        if ('success' === r.status) {
          EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);
        } else {
          EnergyPlusAjax('error', r.error);
        }

        jQuery('.energyplus-settings-panels .__A__Description').show();
        jQuery('.energyplus-settings-panels li').removeClass('__A___Settings_Drags');


      }, 'json');
    }
  });
}

jQuery('.__A__Settings_Change_Icon').iconpicker()
.on('change', function(e){

  var id = jQuery(this).attr('data-id');
  var icon = e.icon;

  EnergyPlusAjax();
  jQuery.post( EnergyPlusGlobal.ajax_url, {
    _wpnonce: jQuery('input[name=_wpnonce]').val(),
    _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
    _asnonce: EnergyPlusGlobal._asnonce,
    action: "energyplus_settings",
    section: 'icon',
    panel: id,
    icon: icon
  }, function(r) {
    if (1 === r.status) {
      EnergyPlusAjax('success', EnergyPlusGlobal.i18n.done);

      jQuery('#energyplus-menu-'+id+' .__A__Item .__A__Item_Icon').removeClass().addClass('__A__Item_Icon ' + icon);
      jQuery('#energyplus-'+id+' .__A__Custom_Icon_Container').removeClass().text('').addClass('__A__Custom_Icon_Container energyplus-custom-icon ' + icon);


    } else {
      EnergyPlusAjax('error', r.error);
    }
  }, "json");


});

jQuery('.__A__Settings_Change_Icon .empty').text(' Change icon');


function LightenDarkenColor(color, percent) {

  var R = parseInt(color.substring(1,3),16);
  var G = parseInt(color.substring(3,5),16);
  var B = parseInt(color.substring(5,7),16);

  R = parseInt(R * (100 + percent) / 100);
  G = parseInt(G * (100 + percent) / 100);
  B = parseInt(B * (100 + percent) / 100);

  R = (R<255)?R:255;
  G = (G<255)?G:255;
  B = (B<255)?B:255;

  var RR = ((R.toString(16).length===1)?"0"+R.toString(16):R.toString(16));
  var GG = ((G.toString(16).length===1)?"0"+G.toString(16):G.toString(16));
  var BB = ((B.toString(16).length===1)?"0"+B.toString(16):B.toString(16));

  return "#"+RR+GG+BB;

}

});
