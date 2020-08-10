jQuery(document).ready(function() {
  "use strict";

  // Solves the conflict with jQuery UI and Bootstrap
  jQuery.noConflict();


  // Check if Bootstrap is enabled
  if ((typeof jQuery().emulateTransitionEnd == 'function')) {
    jQuery('[data-toggle="tooltip"]').tooltip();
  } else {
    jQuery('body').on('click', '.__A__Item.btnA', function(e) {
      jQuery(this).toggleClass('collapsed');
      jQuery(this).find('> div:not(.liste)').toggleClass('collapse');
    });
  }

  jQuery('.__A__MainMenu, .__A__MainMenu ul').css('overflow', 'unset');
  jQuery('.energyplus-admin-dashboard #energyplus-wp-notices:not(.__A__WP_Notices_Container)').show();

  jQuery('body').on('click', '.__A__StopPropagation', function(e) {
    e.stopPropagation();
  });

  jQuery('body').on('click', '.btnA a', function(e) {
    e.stopPropagation();
  });

  window.isMobile = false; //initiate as false

  // device detection
  if (jQuery(window).width()<820) {
    window.isMobile = true;
  }


  jQuery('body').on('click', '.__A__Mobile_Actions', function() {
    var prnt;

    if (jQuery(this).hasClass("__A__M21")) {
      prnt = jQuery(this).parent().parent();
    }  else if (jQuery(this).hasClass("__A__M1-A")) {
      prnt = jQuery(this).parent().parent().parent();
    }  else {
      prnt = jQuery(this).parent();
    }

    jQuery(this).toggleClass('__A__Rotate');
    prnt.toggleClass('collapsed');
    jQuery(".__A__Col_3, .__A__Col_3N", prnt ).toggleClass("__A__Col_3S");
  });

  /* Submenu position */
  jQuery(document).on( 'mouseover', '.__A__MainMenuV > li, .__A__MainMenuV li ul li', function() {

    var $document = jQuery( document ),
    $window = jQuery( window ),
    $body = jQuery( document.body ),
    $wpwrap = jQuery( '#wpwrap' ),
    $menuItem = jQuery(this),
    bottomOffset, pageHeight, adjustment, theFold, menutop, wintop, maxtop,
    $submenu = $menuItem.find( '> .energyplus-header-submenu' );

    menutop = $menuItem.offset().top;
    wintop = $window.scrollTop();
    maxtop = menutop - wintop - 30; // max = make the top of the sub almost touch admin bar

    bottomOffset = menutop + $submenu.height() + 1; // Bottom offset of the menu
    pageHeight = $wpwrap.height(); // Height of the entire page
    adjustment = 60 + bottomOffset - pageHeight;
    theFold = $window.height() + wintop - 10; // The fold

    if ( theFold < ( bottomOffset - adjustment ) ) {
      adjustment = bottomOffset - theFold;
    }

    if ( adjustment > maxtop ) {
      adjustment = maxtop;
    }

    if ( adjustment > 1 ) {
      $submenu.css( 'margin-top', '-' + adjustment + 'px' );
    } else {
      $submenu.css( 'margin-top', '' );
    }

  });

  /* Overflow Menu */

  window.onresize = navigationResize;
  navigationResize();

  function navigationResize() {
    if (self === top) {

      jQuery('.__A__MainMenuH li.more').before(jQuery('#overflow > li'));
      jQuery('.__A__MainMenuV li.more').before(jQuery('#overflow > li'));


      var $navItemMore = jQuery('.__A__MainMenuH > li.more'),
      $navItems = jQuery('.__A__MainMenuH > li:not(.more)'),
      navItemMoreWidth = $navItemMore.outerWidth(),
      navItemWidth = $navItemMore.outerWidth(),
      windowWidth = jQuery('.__A__MainMenuH').width(),
      navItemMoreLeft, offset, navOverflowWidth;

      var $navItemMoreV = jQuery('.__A__MainMenuV > li.more'),
      $navItemsV = jQuery('.__A__MainMenuV > li:not(.more)'),
      navItemMoreWidthV = $navItemMoreV.outerHeight(),
      navItemWidthV = $navItemMoreV.outerHeight(),
      windowWidthV = jQuery('.__A__MainMenuV').height(),
      navItemMoreLeftV, offsetV, navOverflowWidthV;

      $navItems.each(function() {
        navItemWidth += jQuery(this).outerWidth(true);
      });

      $navItemsV.each(function() {
        navItemWidthV += jQuery(this).outerHeight(true);
      });

      if (navItemWidthV > 0) {
        navItemWidthV += 30;
      }

      if (navItemWidth > windowWidth) {
        $navItemMore.show();
      } else {
        $navItemMore.hide();
      }

      if (navItemWidthV > windowWidthV) {
        $navItemMoreV.show();
      } else {
        $navItemMoreV.hide();
      }

      var i = 0;
      windowWidth -= 30;
      while (navItemWidth > windowWidth && i<30) {
        navItemWidth -= $navItems.last().outerWidth(true);

        $navItems.last().prependTo('#overflow');
        $navItems.splice(-1,1);
        ++i;
      }

      while (navItemWidthV > windowWidthV) {

        navItemWidthV -= $navItemsV.last().outerHeight();
        $navItemsV.last().prependTo('#overflow');
        $navItemsV.splice(-1,1);
      }

      jQuery('#overflow').addClass('energyplus-header-submenu');
      jQuery('.__A__MainMenu').removeClass('overflow-hidden');
      jQuery('.__A__MainMenu .more').removeClass('d-none');

      if (jQuery('#overflow').height() > (jQuery(window).height()-100)) {
        jQuery('#overflow').addClass('energyplus-more-double');
      }

      jQuery('.__A__MainMenu > ul > li').each(function(){
        if (jQuery(this).find('> ul').length>0) {
          if (true === window.isMobile) {
            jQuery(this).find('> a').attr('href', '#');
          } else {
            jQuery(this).find('> a').attr('href', jQuery(this).find('> ul > li:first-child > a').attr('href'));
          }
        }
      });

      jQuery('.__A__MainMenu > ul > li.more > ul > li').each(function(){
        if (jQuery(this).find('> ul').length>0) {
          if (true === window.isMobile) {
            jQuery(this).find('> a').attr('href', '#');
          } else {
            jQuery(this).find('> a').attr('href', jQuery(this).find('> ul > li:first-child > a').attr('href'));
          }
        }
      });
    }
  }

  /* List 1 - Checbox */

  jQuery('.__A__List_M1 .__A__Checkbox').on( "click", function() {
    jQuery('.__A__Checkbox_Hidden').show();
    jQuery(".__A__Item.btnA").addClass('collapsed').attr('aria-expanded', false);
    jQuery(".__A__Item.btnA .collapse").removeClass('show');
    jQuery('.__A__Checkbox_Hidden').show();
  });


  /* Ajax notifications */

  var EnergyPlusAjaxCounter;

  window.EnergyPlusAjax = function(type, message) {
    type = typeof type !== 'undefined' ? type : "clear";
    message = typeof message !== 'undefined' ? message : EnergyPlusGlobal.i18n.wait;
    var an = jQuery('#__A__Ajax_Notification');

    an.stop();

    if (message) {
      an.find('.__A__Text').text(message);

    }

    if ('clear' === type ) {

      clearTimeout(EnergyPlusAjaxCounter);
      an.find('.__A__Ajax_Notification_Container').removeClass().addClass('__A__Ajax_Notification_Container badge badge-pill badge-warning');
      an.find('.__A__Loading').removeClass().addClass('__A__Loading');
      an.find('.__A__Error').removeClass().addClass('__A__Error d-none');
      an.find('.__A__OK').removeClass().addClass('__A__OK  d-none');
    }

    if ('success' === type) {
      an.find('.__A__Ajax_Notification_Container').removeClass().addClass('__A__Ajax_Notification_Container badge badge-pill badge-success');
      an.find('.__A__Loading').removeClass().addClass('__A__Loading d-none');
      an.find('.__A__Error').removeClass().addClass('__A__Error d-none');
      an.find('.__A__OK').removeClass().addClass('__A__OK');
      clearTimeout(EnergyPlusAjaxCounter);
      EnergyPlusAjaxCounter = setTimeout(function() {
        EnergyPlusAjax('hide', message);
      }, 4000);
    }

    if ('error' === type) {
      an.find('.__A__Ajax_Notification_Container').removeClass().addClass('__A__Ajax_Notification_Container badge badge-pill badge-danger');
      an.find('.__A__Loading').removeClass().addClass('__A__Loading d-none');
      an.find('.__A__Error').removeClass().addClass('__A__Error');
      an.find('.__A__OK').removeClass().addClass('__A__OK d-none');
      clearTimeout(EnergyPlusAjaxCounter);

      EnergyPlusAjaxCounter = setTimeout(function() {
        EnergyPlusAjax('hide');
      }, 20000);
    }

    if ('hide' === type ) {
      an.removeClass().addClass('animated slideOutDown');
      clearTimeout(EnergyPlusAjaxCounter);

      EnergyPlusAjaxCounter = setTimeout(function() {
        an.find('.__A__Ajax_Notification_Container').removeClass().addClass('__A__Ajax_Notification_Container badge badge-pill badge-warning');
        an.find('.__A__Loading').removeClass().addClass('__A__Loading');
        an.find('.__A__Error').removeClass().addClass('__A__Error d-none');
        an.find('.__A__OK').removeClass().addClass('__A__OK  d-none');
      }, 800);
    } else {
      an.removeClass('d-none').addClass('animated slideInUp');
    }
  };

  /*
  Get params from current url
  */

  window.getUrlVars = function() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
      vars[key] = value;
    });
    return vars;
  };

  window.getUrlParam = function (parameter, defaultvalue){
    var urlparameter = defaultvalue;
    if(window.location.href.indexOf(parameter) > -1){
      urlparameter = getUrlVars()[parameter].replace(/#-/,'');
    }
    return urlparameter;
  };


  /*
  Search for segments
  */

  window.searchMe = function(extra) {

    extra = typeof extra !== 'undefined' ? extra : "";

    jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce,
      action: "energyplus_ajax",
      segment: getUrlParam('segment', ''),
      do: 'search',
      q: jQuery(".__A__Search_Input").val(),
      status: jQuery(".__A__Input_Status").val(),
      extra: extra
    }, function(r) {
      jQuery('.__A__Search_Input').removeClass('loading');
      jQuery(".__A__Container").html(r).addClass('__A__Ajax_Response');
    });
  };

  jQuery(".__A__Search_Button").on( "click", function() {
    jQuery('.__A__Searching').toggleClass('closed');
    jQuery('.__A__Search_Input').focus();
  });

  jQuery(".__A__Search_Input").doWithDelay("keyup", function(e) {
    jQuery(this).addClass('loading');
    window.searchMe(jQuery(this).data('status'));
  }, 200);

  jQuery(".__A__Select_All").on( "click",function() {
    var doit = true;

    if ('select' === jQuery(this).attr('data-state')) {
      jQuery(this).attr('data-state', 'unselect');
    } else {
      doit = false;
      jQuery(this).attr('data-state', 'select');
    }

    jQuery('.__A__Checkbox').each(function () {
      jQuery(this).prop('checked', doit);
    });


  });

  /* Desktop App */

  jQuery('body').on('click', '.__A__Desktop_Control', function(e) {
    e.preventDefault();
    jQuery('title').text(jQuery('title').text() + 'energy-asterisk-' + jQuery(this).data('do'));
  });


  /* Global */
  var openNotifications = 0;
  var notificationsId = 0;
  window.refreshOnClose = 0;

  jQuery("#slider, #slider2, #notifications").removeClass('d-none');

  /* Search */
  jQuery(".__A__Left_Search, .dashicons-search").on( "click", function() {

    window.slider2_global.slideReveal("hide");

    jQuery("body").css({overflow:'hidden'});

    jQuery(".energyplus-search-1--overlay").addClass('energyplus-search-1--overlay-show');

    setTimeout(function() {   jQuery(".energyplus-search-input").focus(); }, 500);


  });

  jQuery("#energyplus-search-1--close-button").on( "click", function() {
    jQuery("body").css({overflow:'auto'});
    jQuery(".energyplus-search-1--overlay").removeClass('energyplus-search-1--overlay-show');
    jQuery(".energyplus-search-input").val('');
  });


  jQuery(".energyplus-search-input").on("keyup", function(e) {
    jQuery(".__A__Search_Container_Searching").removeClass("hidden");
    jQuery(".__A__Search_Complete").removeClass("__A__Search_Complete");

    if (1 === jQuery(this).data('close-on-empty') && '' === jQuery(this).val()) {
      jQuery(".energyplus-search-1--overlay").removeClass('energyplus-search-1--overlay-show');
    }

  });

  var xhr = null;

  jQuery(".energyplus-search-input").doWithDelay("keyup", function(e) {

    if (!jQuery(".energyplus-search-1--overlay").hasClass('energyplus-search-1--overlay-show')) {
      jQuery("body").css({overflow:'hidden'});

      jQuery(".energyplus-search-1--overlay").addClass('energyplus-search-1--overlay-show');
    }
    if (1 === jQuery(this).data('close-on-empty') && '' === jQuery(this).val()) {
      jQuery("body").css({overflow:'auto'});

      jQuery(".energyplus-search-1--overlay").removeClass('energyplus-search-1--overlay-show');
    }


    if(xhr !== null){
      xhr.abort();
      xhr = null;
    }

    xhr = jQuery.post( EnergyPlusGlobal.ajax_url, {
      _wpnonce: jQuery('input[name=_wpnonce]').val(),
      _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
      _asnonce: EnergyPlusGlobal._asnonce_search,

      action: "energyplus_ajax",
      segment: 'search',
      do: 'search',
      q: jQuery(this).val(),
      mode: 98,
      status: ''
    }, function(r) {

      jQuery(".__A__Search_Container").html(r);
    });
  }, 500);


  /* Notifications */


  function notifyMe(title, body, link) {

    title = typeof title !== 'undefined' ? title : "";
    body = typeof body !== 'undefined' ? body : "";
    link = typeof link !== 'undefined' ? link : "";


    if (!("Notification" in window)) {
      console.log("This browser does not support desktop notification");
    }

    else if (Notification.permission === "granted") {
      var notification = new Notification(title, {
        body: body
      });
      notification.onclick = function(e) {
        e.preventDefault();
        window.focus();
        //  window.location.href= link;
      };
    }

    else if (Notification.permission !== "denied") {
      Notification.requestPermission(function (permission) {
        if (permission === "granted") {
          var notification = new Notification(title, {
            body: body
          });
          notification.onclick = function(e) {
            e.preventDefault();
            window.focus();
            //  window.location.href= link;
          };
        }
      });
    }
  }

  if ('Notification' in window) {
    Notification.requestPermission();
  }
  //  Notification.requestPermission();
  function spawnNotification(theBody,theIcon,theTitle) {
    var options = {
      body: theBody,
      icon: theIcon
    };
    var n = new Notification(theTitle,options);
  }

  var last;
  notifications(-2);

  if (EnergyPlusGlobal.refresh>9000) {
    setInterval(function(last) {
      notifications();
    }, EnergyPlusGlobal.refresh);
  }


  function notifications(lasttime) {
    if (!lasttime) {
      lasttime = last;
    }
    jQuery.ajax({
      type: 'POST',
      dataType: 'json',
      url: EnergyPlusGlobal.ajax_url,
      data: {
        _wpnonce: jQuery('input[name=_wpnonce]').val(),
        _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
        _asnonce: EnergyPlusGlobal._asnonce_notifications,
        action: 'energyplus_ajax',
        segment: 'notifications',
        lasttime: lasttime,
        opened: openNotifications,
        lastid: notificationsId
      },
      cache: false,
      headers: {
        'cache-control': 'no-cache'
      },
      success: function(response) {

        var cls = "badge-silent";

        last = response.time;
        if (response.lastid > 0) {
          notificationsId = response.lastid;
        }

        if (0 < response.count) {
          cls = "badge-danger";
        }

        jQuery(".__A__DisplayNotifications").animate({opacity: 1}, 0).removeClass('badge-danger').addClass(cls).text(response.count);

        if (response.result !== "") {
          jQuery("#notifications > .__A__Notifications_Content").prepend(response.result);
          jQuery("#notifications .__A__EmptyTable").remove();

        }

        jQuery('#odometer').html(response.today_sales);


        jQuery.each(response.alerts, function(i, item) {
          notifyMe(item.title, item.body, item.link);
        });

        jQuery.each(response.sounds, function(i, item) {
          if (item !== '') {
            var  audio = new Audio(item);

            audio.preload = 'auto';
            // Just to protect your ears :)
            audio.volume = 0.2;
            audio.play();

          }
        });

        if (response.title !== jQuery("title").text()) {
          jQuery("title").text(response.title);
        }
      }
    }, "json");
  }


  function slideRevelWidth() {
    window.device_width = EnergyPlusGlobal.reactors_tweaks_window_size; // 770
    window.device_width_notific = "450px"; // 770

    jQuery('#inbrowser').css({
      width: '100%'
    });

    if (jQuery(window).width()<1025) {
      window.device_width = '100%';
      window.device_width_notific = '100%';
      jQuery('#inbrowser').css({
        width: jQuery(window).width()+'px'
      });
    }
  }

  slideRevelWidth();


  var notific =  jQuery('#notifications').slideReveal({
    position: "right",
    push: false,
    overlay: true,
    zIndex: 1048,
    width: window.device_width_notific,
    shown: function(slider, trigger){
      openNotifications = 1;
      jQuery("html, body").css({
        'overflow': 'hidden'
      });
    },
    hidden: function(slider, trigger){
      openNotifications = 0;
      jQuery("html, body").css({
        'overflow': 'inherit'
      });
    }
  });

  var trig =  jQuery('#slider').slideReveal({
    position: "right",
    push: false,
    overlay: true,
    width: window.device_width,
    shown: function(slider, trigger){
      jQuery('body').css({'overflow':'hidden'});

      jQuery("#inbrowser--loading").addClass('d-flex').removeClass('d-none');
      jQuery(".__A__Trig_Close").removeClass('d-none');


    },
    hidden: function(slider, trigger){
      jQuery('body').css({'overflow':'auto'});

      window.location.hash = '#-';

      jQuery("#inbrowser--loading").addClass('d-flex').removeClass('d-none');
      jQuery(".__A__Trig_Close").addClass('d-none');
      jQuery("#inbrowser").attr('src', 'about:blank');

      if (1 === window.refreshOnClose) {
        location.reload(true);
      }
    }
  });

  var slider2 = jQuery('#slider2').slideReveal({
    position: "left",
    push: true,
    overlay: false,
    width: '200px',
    trigger: jQuery("#trig2")
  });

  window.trigGlobal = trig;
  window.slider2_global = slider2;
  window.notific_global = notific;

  window.addEventListener('resize',function(e) {
    window.notific_global.slideReveal('hide');
    window.trigGlobal.slideReveal('hide');

    slideRevelWidth();
  }, false);



  jQuery(".__A__DisplayNotifications, .__A__X").on( "click", function() {
    notifications(-1);
    jQuery(".__A__DisplayNotifications").removeClass('badge-danger').text("0");
    notific.slideReveal("toggle");
  });


  jQuery( "body" ).on( "click", '.__A__Trig_CloseButton' , function() {
    window.trigGlobal.slideReveal('hide');
    jQuery("#inbrowser--loading").addClass('d-flex').removeClass('d-none');
    jQuery(".__A__Trig_Close").addClass('d-none');
    jQuery("#inbrowser").attr('src', 'about:blank');
  });

  jQuery( "body" ).on( "click", '.__A__Trig_BackButton', function() {
    document.getElementById('inbrowser').contentWindow.history.back(-1);
    jQuery(this).removeClass('__A__Trig_BackButton').addClass('__A__Trig_CloseButton').html('<span class="dashicons dashicons-arrow-left-alt"></span>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;');
  });

  jQuery( "body" ).on( "click", '.trig', function(e) {
    if (!isMobile) {
      e.preventDefault();

      if (jQuery(this).hasClass('trig-close')) {
        refreshOnClose = 1;
      } else {
        refreshOnClose = 0;
      }

      if (jQuery(this).attr('data-hash')) {
        window.location.hash = jQuery(this).attr('data-hash');
      }

      jQuery("#inbrowser--loading").addClass('d-flex').removeClass('d-none');
      jQuery(".__A__Trig_Close").addClass('d-none');

      jQuery("#inbrowser").attr("src", jQuery(this).attr('href'));

      trig.slideReveal("show");

      jQuery('#inbrowser').on("load", function() {
        jQuery("#inbrowser").show();
        jQuery("#inbrowser--loading").removeClass('d-flex').addClass('d-none');
        jQuery(".__A__Trig_Close").removeClass('d-none');
      });
    } else {
      if (jQuery('body').hasClass('energyplus-half')) {
        window.location = EnergyPlusGlobal._admin_url + 'admin.php?page=energyplus&segment=frame&in=' + encodeURIComponent(jQuery(this).attr('href')) + "&_asnonce="+ EnergyPlusGlobal._asnonce_notifications;
        return false;
      }
    }

  });

  jQuery('#inbrowser, #energyplus-frame').on('load', function() {
    jQuery("#inbrowser").show();
    jQuery("#inbrowser--loading").removeClass('d-flex').addClass('d-none');
    jQuery(".__A__Trig_Close").removeClass('d-none');
  });

  /* Url with hash */

  window.detectHash = function(url) {

    var hash = window.location.hash.substr(1);

    if (hash && hash !== '-') {
      window.trigGlobal.slideReveal("show");
      jQuery("#inbrowser").attr("src", url.replace(/HASH/, hash));
      jQuery('#inbrowser').on("load", function() {
        jQuery("#inbrowser--loading").removeClass('d-flex').addClass('d-none');
        jQuery(".__A__Trig_Close").removeClass('d-none');
        jQuery("#inbrowser").show();
      });
    }
  };

  // Show adminbar when press A key from keyboard
  if ("1" === EnergyPlusGlobal.reactors_tweaks_adminbar_hotkey) {
    jQuery(document).on('keydown', function(event) {

      var excludeInputs = [
        "text", "password", "number", "email", "url", "range", "date", "month", "week", "time", "datetime",
        "datetime-local", "search", "color", "tel", "textarea"];

        if (this !== event.target && (event.target.isContentEditable || jQuery.inArray(event.target.type, excludeInputs) > -1)) {
          return;
        }

        if(event.which == 65) {
          if (jQuery('#wpadminbar').is(":visible")) {
            jQuery('#wpadminbar').hide();
          } else {
            jQuery('#wpadminbar').attr("style", "height:50px; opacity:0;padding-top:10px; display: inline !important").animate({
              opacity: 1
            });
          }
          event.preventDefault();
          return false;
        }
      }
    );
  }

});
