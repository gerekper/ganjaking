"use strict";

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/*! js-cookie v3.0.0-rc.0 | MIT */
!function (e, t) {
  "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) && "undefined" != typeof module ? module.exports = t() : "function" == typeof define && define.amd ? define(t) : (e = e || self, function () {
    var r = e.Cookies,
        n = e.Cookies = t();

    n.noConflict = function () {
      return e.Cookies = r, n;
    };
  }());
}(void 0, function () {
  "use strict";

  function e(e) {
    for (var t = 1; t < arguments.length; t++) {
      var r = arguments[t];

      for (var n in r) {
        e[n] = r[n];
      }
    }

    return e;
  }

  var t = {
    read: function read(e) {
      return e.replace(/%3B/g, ";");
    },
    write: function write(e) {
      return e.replace(/;/g, "%3B");
    }
  };
  return function r(n, i) {
    function o(r, o, u) {
      if ("undefined" != typeof document) {
        "number" == typeof (u = e({}, i, u)).expires && (u.expires = new Date(Date.now() + 864e5 * u.expires)), u.expires && (u.expires = u.expires.toUTCString()), r = t.write(r).replace(/=/g, "%3D"), o = n.write(String(o), r);
        var c = "";

        for (var f in u) {
          u[f] && (c += "; " + f, !0 !== u[f] && (c += "=" + u[f].split(";")[0]));
        }

        return document.cookie = r + "=" + o + c;
      }
    }

    return Object.create({
      set: o,
      get: function get(e) {
        if ("undefined" != typeof document && (!arguments.length || e)) {
          for (var r = document.cookie ? document.cookie.split("; ") : [], i = {}, o = 0; o < r.length; o++) {
            var u = r[o].split("="),
                c = u.slice(1).join("="),
                f = t.read(u[0]).replace(/%3D/g, "=");
            if (i[f] = n.read(c, f), e === f) break;
          }

          return e ? i[e] : i;
        }
      },
      remove: function remove(t, r) {
        o(t, "", e({}, r, {
          expires: -1
        }));
      },
      withAttributes: function withAttributes(t) {
        return r(this.converter, e({}, this.attributes, t));
      },
      withConverter: function withConverter(t) {
        return r(e({}, this.converter, t), this.attributes);
      }
    }, {
      attributes: {
        value: Object.freeze(i)
      },
      converter: {
        value: Object.freeze(n)
      }
    });
  }(t, {
    path: "/"
  });
});
var seedprodCookies = Cookies.noConflict(); // optin form

var sp_emplacementRecaptcha = [];
var sp_option_id = "";
jQuery("form[id^=sp-optin-form]").submit(function (e) {
  e.preventDefault();
  var form_id = jQuery(this).attr("id");
  var id = form_id.replace("sp-optin-form-", "");

  if (seeprod_enable_recaptcha === 1) {
    grecaptcha.execute(sp_emplacementRecaptcha[id]);
  } else {
    var token = "";
    sp_send_request(token, id);
  }
});

var sp_CaptchaCallback = function sp_CaptchaCallback() {
  jQuery("div[id^=recaptcha-]").each(function (index, el) {
    sp_option_id = el.id.replace("recaptcha-", "");
    sp_emplacementRecaptcha[sp_option_id] = grecaptcha.render(el, {
      sitekey: "6LdfOukUAAAAAMCOEFEZ9WOSKyoYrxJcgXsf66Xr",
      badge: "bottomright",
      type: "image",
      size: "invisible",
      callback: function callback(token) {
        sp_send_request(token, sp_option_id);
      }
    });
  });
};

function sp_send_request(token, id) {
  var data = jQuery("#sp-optin-form-" + id).serialize();
  var j1 = jQuery.ajax({
    url: seedprod_api_url + "subscribers",
    type: "post",
    dataType: "json",
    timeout: 5000,
    data: data
  }); // add ajax class

  jQuery("#sp-optin-form-" + id + ' .sp-optin-submit').addClass('sp-ajax-striped sp-ajax-animated'); //var j2 = jQuery.ajax( "/" );

  var j2 = jQuery.ajax({
    url: sp_subscriber_callback_url,
    type: 'post',
    timeout: 30000,
    data: data
  });
  jQuery.when(j1, j2).done(function (a1, a2) {
    // take next action
    var action = jQuery("#sp-optin-form-" + id + " input[name^='seedprod_action']").val(); // show success message

    if (action == "1") {
      jQuery("#sp-optin-form-" + id).hide();
      jQuery("#sp-optin-success-" + id).show();
    } // redirect


    if (action === "2") {
      var redirect = jQuery("#sp-optin-form-" + id + " input[name^='redirect_url']").val();
      window.location.href = redirect;
    }

    jQuery("#sp-optin-form-" + id + ' .sp-optin-submit').removeClass('sp-ajax-striped sp-ajax-animated'); // alert( "We got what we came for!" );
  }).fail(function (jqXHR, textStatus, errorThrown) {
    jQuery("#sp-optin-form-" + id + ' .sp-optin-submit').removeClass('sp-ajax-striped sp-ajax-animated');

    if (seeprod_enable_recaptcha === 1) {
      grecaptcha.reset(sp_emplacementRecaptcha[id]);
    } // var response = JSON.parse(j1.responseText);
    // var errorString  = '';
    // jQuery.each( response.errors, function( key, value) {
    //     errorString +=  value ;
    // });
    // alert(errorString);
    // console.log(j1);
    // console.log(j2);

  });
  return;
} // countdown


var x = [];

function countdown(type, ts, id, action, redirect) {
  var now = new Date().getTime();

  if (type == 'vt') {
    ts = ts + now;
    var seedprod_enddate = seedprodCookies.get('seedprod_enddate_' + id);

    if (seedprod_enddate != undefined) {
      ts = seedprod_enddate;
    } else {
      seedprodCookies.set('seedprod_enddate_' + id, ts, {
        expires: 360
      });
    }
  } // Update the count down every 1 second


  x[id] = setInterval(function () {
    var now = new Date().getTime();
    var distance = ts - now;
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor(distance % (1000 * 60 * 60 * 24) / (1000 * 60 * 60));
    var minutes = Math.floor(distance % (1000 * 60 * 60) / (1000 * 60));
    var seconds = Math.floor(distance % (1000 * 60) / 1000);

    if (seconds == -1) {
      seconds = 0;
      minutes = 0;
      hours = 0;
      days = 0;
    }

    if (days == 0) {
      jQuery("#sp-cd-days-" + id).hide();
    } else {
      jQuery("#sp-cd-days-" + id + " .sp-cd-amount").html(pad(days, 2));
    }

    jQuery("#sp-cd-hours-" + id + " .sp-cd-amount").html(pad(hours, 2));
    jQuery("#sp-cd-minutes-" + id + " .sp-cd-amount").html(pad(minutes, 2));
    jQuery("#sp-cd-seconds-" + id + " .sp-cd-amount").html(pad(seconds, 2)); //   document.getElementById(id).innerHTML = days + "d " + pad(hours,2) + "h "
    //   + pad(minutes,2) + "m " + pad(seconds,2) + "s ";
    // If the count down is finished, write some text

    if (distance < 0) {
      clearInterval(x[id]); // show success message

      if (action == "1") {
        jQuery("#sp-countdown-" + id + " .sp-countdown-group").hide();
        jQuery("#sp-countdown-expired-" + id).show();
      } // redirect


      if (action == "2") {
        jQuery("#sp-countdown-" + id + " .sp-countdown-group").hide();
        window.location.href = redirect;
      } // restart


      if (action == "3") {
        //console.log('remove' + id);
        seedprodCookies.remove('seedprod_enddate_' + id); //location.reload();
      }
    }
  }, 1000);
}

function seedprod_animatedheadline(blockId, infiniteLoop, animationDuration, animationDelay) {
  //let animatewrapper = jQuery("#sp-animated-head-"+blockId+" .sp-title-highlight .sp-highlighted-text-wrapper");
  if (infiniteLoop == "true") {
    window.setInterval(function () {
      jQuery('#sp-animated-head-' + blockId + ' .sp-title-highlight .sp-title--headline.sp-animated').addClass('sp-hide-highlight'); //jQuery("#sp-animated-head-"+blockId+" .sp-title-highlight .sp-highlighted-text-wrapper").addClass("sp-highlighted-hide-text-wrapper");

      setTimeout(function () {
        jQuery('#sp-animated-head-' + blockId + ' .sp-title-highlight .sp-title--headline.sp-animated').removeClass('sp-hide-highlight'); //jQuery("#sp-animated-head-"+blockId+" .sp-title-highlight .sp-highlighted-text-wrapper").removeClass("sp-highlighted-hide-text-wrapper");
      }, 200);
    }, animationDelay);
  }
}

function seedprod_rotateheadline(blockId, continueLoop, animationDuration) {
  jQuery("#sp-animated-head-" + blockId + ' .preview-sp-title').seedprod_responsive_title_shortcode();
}
/* end of rotate js code */


function pad(n, width, z) {
  z = z || "0";
  n = n + "";
  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
} // remove any theme css


jQuery(document).ready(function ($) {
  $('link[href*="/wp-content/themes/"]').remove();
}); // Dynamic Text

jQuery(document).ready(function ($) {
  var default_format = "{MM}/{dd}/{yyyy}";
  var html = $("body").html();
  var newTxt = html.split("[#");

  for (var i = 1; i < newTxt.length; i++) {
    var format = default_format;
    var tag = newTxt[i].split("]")[0];
    var parts = tag.split(":");

    if (parts.length > 1) {
      format = parts[1];
    } else {
      format = default_format;
    }

    var d = Date.create(parts[0]);
    var regex = "\\[#" + tag + "]";
    var re = new RegExp(regex, "g");
    $("body *").replaceText(re, d.format(format));
  }

  $(".sp-dynamic-text").contents().unwrap();
}); // Dynamic Query Parameter

jQuery(document).ready(function ($) {
  var default_value = "";
  var html = $("body").html();
  var newTxt = html.split("[q:");

  for (var i = 1; i < newTxt.length; i++) {
    var def_val = default_value;
    var tag = newTxt[i].split("]")[0];
    var parts = tag.split("=");

    if (parts.length > 1) {
      def_val = parts[1];
    } else {
      def_val = default_value;
    }

    var d = parts[0]; //Date.create(parts[0]);

    var regex = "\\[q:" + tag + "]";
    var re = new RegExp(regex, "g");
    var searchParams = new URLSearchParams(window.location.search);
    var paramdata = searchParams.get(d);

    if (paramdata != null) {
      def_val = paramdata;
    } // console.log(re);
    // console.log(def_val);
    //  console.log(d);
    //  console.log(def_val);
    //  console.log(paramdata);
    //$("body *").replaceText(re,seedprod_escapeHtml(def_val));


    var replaced = $("body").html().replace(re, seedprod_escapeHtml(def_val));
    $("body").html(replaced);
  }

  $(".sp-dynamic-text").contents().unwrap();
});

function seedprod_escapeHtml(unsafe) {
  return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
/** 
 * SeedProd Tab Block javascript
*/


function seedprod_tabbedlayout(blockId) {
  jQuery("#sp-" + blockId + ' ul.sp-tabbedlayout-wrapper li a').click(function () {
    jQuery("#sp-" + blockId + ' ul.sp-tabbedlayout-wrapper li a').removeClass('sp-active-tab');
    var sp_tab = jQuery(this).attr('data-tab');
    jQuery("#sp-" + blockId + ' ul.sp-tabbedlayout-wrapper li a.sp-tab-section-' + sp_tab).addClass('sp-active-tab');
    jQuery("#sp-" + blockId + ' div.tab-content-box').addClass('sp-hidden');
    jQuery("#sp-" + blockId + ' div.sp-tab-content-section-' + sp_tab).removeClass('sp-hidden');
  });
}
/*!-----------------------------------------------------------------------------
 * seedprod_bg_slideshow()
 * ----------------------------------------------------------------------------
 * Example:
 * seedprod_bg_slideshow('body', ['IMG_URL', 'IMG_URL', 'IMG_URL'], 3000);
 * --------------------------------------------------------------------------*/


function seedprod_bg_slideshow(selector, slides) {
  var delay = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 5000;
  var transition_timing = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'ease-in';
  var transition_duration = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 500;
  document.querySelector(selector).style.backgroundSize = "cover";
  document.querySelector(selector).style.backgroundRepeat = "no-repeat";
  document.querySelector(selector).style.backgroundPosition = "center center"; // Set transitions

  var transition = "all " + transition_duration + 'ms ' + transition_timing;
  document.querySelector(selector).style.WebkitTransition = transition;
  document.querySelector(selector).style.MozTransition = transition;
  document.querySelector(selector).style.MsTransition = transition;
  document.querySelector(selector).style.OTransition = transition;
  document.querySelector(selector).style.transition = transition;
  var currentSlideIndex = 0; // Load first slide

  document.querySelector(selector).style.backgroundImage = "url('" + slides[currentSlideIndex] + "')";
  currentSlideIndex++; // Load next slide every interval

  setInterval(function () {
    document.querySelector(selector).style.backgroundImage = "url('" + slides[currentSlideIndex] + "')";
    currentSlideIndex++; // Reset counter

    if (currentSlideIndex >= slides.length) {
      currentSlideIndex = 0;
    }
  }, delay); // Preload slideshow images

  var preloadImages = new Array();
  slides.forEach(function (val, i) {
    preloadImages[i] = new Image();
    preloadImages[i].src = val;
  });
}

jQuery('.sp-testimonial-nav button').click(function () {
  var currentId = '#' + jQuery(this).parents('.sp-testimonials-wrapper').attr('id');
  var currentButtonIndex = jQuery(currentId + ' .sp-testimonial-nav button').index(this);
  var currentIndex = 0;
  var testimonials = jQuery('.sp-testimonial-wrapper', jQuery(this).parents(currentId));
  var slideshowmax = jQuery(this).parents('.sp-testimonials-wrapper').attr('data-slidetoshow');

  if (slideshowmax == undefined) {
    slideshowmax = 1;
  }
  /*
  jQuery(testimonials).each(function (index) {
  	var o = jQuery(this).css('opacity');
  	if (o == 1) {
  		currentIndex = index;
  	}
  })
  */


  var slider_length = Math.ceil(testimonials.length / parseInt(slideshowmax));

  for (var customindexdata = 0; customindexdata < slider_length; customindexdata++) {
    var opa = jQuery(currentId + ' .sp-testimonial-nav button[data-index="' + customindexdata + '"]').css('opacity');

    if (opa >= 0.5) {
      //console.log("customindexdata is =" + customindexdata);
      currentIndex = customindexdata;
    }
  }

  var buttonsLength = jQuery(currentId + ' .sp-testimonial-nav button').length - 1;
  var currentButtonIndexData = jQuery(currentId + ' .sp-testimonial-nav button').eq(currentButtonIndex).attr('data-index'); // check for previous button click

  if (currentButtonIndex == 0) {
    if (0 == currentIndex) {
      currentIndex = Math.ceil(testimonials.length / parseInt(slideshowmax)) - 1;
    } else {
      currentIndex--;
    }
  } // check for next button click


  if (currentButtonIndex == buttonsLength) {
    if (Math.ceil(testimonials.length / parseInt(slideshowmax)) - 1 == currentIndex) {
      currentIndex = 0;
    } else {
      currentIndex++;
    }
  } // reset states


  testimonials.css({
    'opacity': 0,
    'height': '0',
    'position': 'absolute'
  });
  jQuery(currentId + ' .sp-testimonial-nav button[data-index]').css({
    'opacity': 0.25
  });
  var startindex = parseInt(currentIndex * parseInt(slideshowmax));
  var endindex = parseInt(startindex + parseInt(slideshowmax)); // select testimonial and button

  if (currentButtonIndexData !== undefined) {
    currentIndex = currentButtonIndexData;
    startindex = parseInt(currentIndex * parseInt(slideshowmax));
    endindex = parseInt(startindex + parseInt(slideshowmax));

    for (var i = startindex; i < endindex; i++) {
      jQuery(testimonials).eq(i).css({
        'opacity': 1,
        'height': 'auto',
        'position': 'initial'
      });
    } //jQuery(testimonials).eq(currentIndex).css({ 'opacity': 1, 'height': 'auto', 'position': 'initial' });


    jQuery(currentId + ' .sp-testimonial-nav button').eq(currentButtonIndex).css({
      'opacity': 1
    });
  } else {
    startindex = parseInt(currentIndex * parseInt(slideshowmax));
    endindex = parseInt(startindex + parseInt(slideshowmax));

    for (var _i = startindex; _i < endindex; _i++) {
      jQuery(testimonials).eq(_i).css({
        'opacity': 1,
        'height': 'auto',
        'position': 'initial'
      }); //jQuery(currentId + ' .sp-imagecarousel-nav button').eq(currentButtonIndex).css({ 'opacity': 1 })	
    } //jQuery(testimonials).eq(currentIndex).css({ 'opacity': 1, 'height': 'auto', 'position': 'initial' });


    jQuery(currentId + ' .sp-testimonial-nav button').eq(currentIndex + 1).css({
      'opacity': 1
    });
  }
});
var testimonial_timers = {};
jQuery(".sp-testimonials-wrapper").each(function (index) {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    testimonial_timers[currentId] = setInterval(function () {
      jQuery(currentId + ' .sp-testimonial-nav button:last-child').trigger('click');
    }, speed);
  }
});
jQuery(".sp-testimonials-wrapper").hover(function () {
  var id = '#' + jQuery(this).attr('id');
  clearInterval(testimonial_timers[id]);
});
jQuery(".sp-testimonials-wrapper").mouseleave(function () {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    testimonial_timers[currentId] = setInterval(function () {
      jQuery(currentId + ' .sp-testimonial-nav button:last-child').trigger('click');
    }, speed);
  }
});
/* start  of twitter timline js code */

function seedprod_twitterembedtimeline(blockId, timelineid, showReplies, width, height, chrome, align, borderColors, colorScheme, lang) {
  //jQuery("#sp-animated-head-"+blockId+' .preview-sp-title' ).seedprod_responsive_title_shortcode();
  twttr.ready(function (twttr) {
    window.twttr.widgets.createTimeline({
      sourceType: "profile",
      screenName: timelineid
    }, document.getElementById('sp-twitterembedtimeline-preview-' + blockId), {
      showReplies: showReplies,
      width: width,
      height: height,
      chrome: chrome,
      align: align,
      borderColor: borderColors,
      theme: colorScheme,
      lang: lang
    }).then(function (el) {//console.log('Tweet added.'); 
    });
  });
}

function seedprod_twittertweetbutton(blockId, tweetUrl, buttonSize, tweetText, tweetHashTag, viaHandle, relatedTweet, lang) {
  twttr.ready(function (twttr) {
    window.twttr.widgets.createShareButton(tweetUrl, document.getElementById('sp-twittertweetbutton-preview-' + blockId), {
      size: buttonSize,
      text: tweetText,
      hashtags: tweetHashTag,
      via: viaHandle,
      related: relatedTweet,
      lang: lang
    });
  });
}
/* end of twitter timline js code */

/* this is image carousel block code */


jQuery('.sp-imagecarousel-nav button').click(function () {
  var currentId = '#' + jQuery(this).parents('.sp-imagecarousels-wrapper').attr('id');
  var currentButtonIndex = jQuery(currentId + ' .sp-imagecarousel-nav button').index(this);
  var currentIndex = 0;
  var currentIndexOfNav = 0;
  var imagecarousels = jQuery('.sp-imagecarousel-wrapper', jQuery(this).parents(currentId));
  var slideshowmax = jQuery(this).parents('.sp-imagecarousels-wrapper').attr('data-slidetoshow');

  if (slideshowmax == undefined) {
    slideshowmax = 1;
  } //console.log("new slidershow value = " + slideshowmax);

  /*
  jQuery(imagecarousels).each(function (index) {
  	var o = jQuery(this).css('opacity');
  	if (o == 1) {
  		currentIndex = index;
  	}
  })
  */


  var slider_length = Math.ceil(imagecarousels.length / parseInt(slideshowmax));

  for (var customindexdata = 0; customindexdata < slider_length; customindexdata++) {
    var opa = jQuery(currentId + ' .sp-imagecarousel-nav button[data-index="' + customindexdata + '"]').css('opacity');

    if (opa >= 0.5) {
      //console.log("customindexdata is =" + customindexdata);
      currentIndex = customindexdata;
    }
  }

  var buttonsLength = jQuery(currentId + ' .sp-imagecarousel-nav button').length - 1;
  var currentButtonIndexData = jQuery(currentId + ' .sp-imagecarousel-nav button').eq(currentButtonIndex).attr('data-index'); // check for previous button click

  if (currentButtonIndex == 0) {
    if (0 == currentIndex) {
      currentIndex = Math.ceil(imagecarousels.length / parseInt(slideshowmax)) - 1;
    } else {
      currentIndex--;
    }
  } // check for next button click


  if (currentButtonIndex == buttonsLength) {
    if (Math.ceil(imagecarousels.length / parseInt(slideshowmax)) - 1 == currentIndex) {
      currentIndex = 0;
    } else {
      currentIndex++;
    }
  }

  var startindex = parseInt(currentIndex * parseInt(slideshowmax));
  var endindex = parseInt(startindex + parseInt(slideshowmax)); // reset states

  imagecarousels.css({
    'opacity': 0,
    'height': '0',
    'position': 'absolute'
  });
  jQuery(currentId + ' .sp-imagecarousel-nav button[data-index]').css({
    'opacity': 0.25
  }); // select imagecarousel and button

  if (currentButtonIndexData !== undefined) {
    currentIndex = currentButtonIndexData;
    startindex = parseInt(currentIndex * parseInt(slideshowmax));
    endindex = parseInt(startindex + parseInt(slideshowmax));

    for (var i = startindex; i < endindex; i++) {
      jQuery(imagecarousels).eq(i).css({
        'opacity': 1,
        'height': 'auto',
        'position': 'initial'
      });
    } //jQuery(imagecarousels).eq(currentIndex).css({ 'opacity': 1, 'height': 'auto', 'position': 'initial' });


    jQuery(currentId + ' .sp-imagecarousel-nav button').eq(currentButtonIndex).css({
      'opacity': 1
    });
  } else {
    startindex = parseInt(currentIndex * parseInt(slideshowmax));
    endindex = parseInt(startindex + parseInt(slideshowmax));

    for (var _i2 = startindex; _i2 < endindex; _i2++) {
      jQuery(imagecarousels).eq(_i2).css({
        'opacity': 1,
        'height': 'auto',
        'position': 'initial'
      }); //jQuery(currentId + ' .sp-imagecarousel-nav button').eq(currentButtonIndex).css({ 'opacity': 1 })	
    } //jQuery(imagecarousels).eq(currentIndex).css({ 'opacity': 1, 'height': 'auto', 'position': 'initial' });


    jQuery(currentId + ' .sp-imagecarousel-nav button').eq(currentIndex + 1).css({
      'opacity': 1
    });
  }
});
var imagecarousel_timers = {};
jQuery(".sp-imagecarousels-wrapper").each(function (index) {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    imagecarousel_timers[currentId] = setInterval(function () {
      jQuery(currentId + ' .sp-imagecarousel-nav button:last-child').trigger('click');
    }, speed);
  }
});
jQuery(".sp-imagecarousels-wrapper").hover(function () {
  var id = '#' + jQuery(this).attr('id');
  clearInterval(imagecarousel_timers[id]);
});
jQuery(".sp-imagecarousels-wrapper").mouseleave(function () {
  var currentId = '#' + jQuery(this).attr('id');
  var autoPlay = jQuery(this).attr('data-autoplay');
  var speed = jQuery(this).attr('data-speed');

  if (speed === '') {
    speed = 5000;
  } else {
    speed = parseInt(speed) * 1000;
  }

  if (autoPlay !== undefined) {
    imagecarousel_timers[currentId] = setInterval(function () {
      jQuery(currentId + ' .sp-imagecarousel-nav button:last-child').trigger('click');
    }, speed);
  }
});

function PureDropdown(dropdownParent) {
  var PREFIX = 'seedprod-',
      ACTIVE_CLASS_NAME = PREFIX + 'menu-active',
      ARIA_ROLE = 'role',
      ARIA_HIDDEN = 'aria-hidden',
      MENU_OPEN = 0,
      MENU_CLOSED = 1,
      MENU_ACTIVE_SELECTOR = '.menu-item-active',
      MENU_LINK_SELECTOR = '.menu-item a',
      MENU_SELECTOR = '.sub-menu',
      DISMISS_EVENT = window.hasOwnProperty && window.hasOwnProperty('ontouchstart') ? 'touchstart' : 'mousedown',
      ARROW_KEYS_ENABLED = true,
      ddm = this; // drop down menu

  this._state = MENU_CLOSED;

  this.show = function () {
    if (this._state !== MENU_OPEN) {
      this._dropdownParent.classList.add(ACTIVE_CLASS_NAME);

      this._menu.setAttribute(ARIA_HIDDEN, false);

      this._state = MENU_OPEN;
    }
  };

  this.hide = function () {
    if (this._state !== MENU_CLOSED) {
      this._dropdownParent.classList.remove(ACTIVE_CLASS_NAME);

      this._menu.setAttribute(ARIA_HIDDEN, true);

      this._link.focus();

      this._state = MENU_CLOSED;
    }
  };

  this.toggle = function () {
    this[this._state === MENU_CLOSED ? 'show' : 'hide']();
  };

  this.halt = function (e) {
    e.stopPropagation();
    e.preventDefault();
  };

  this._dropdownParent = dropdownParent;
  this._link = this._dropdownParent.querySelector(MENU_LINK_SELECTOR);
  this._menu = this._dropdownParent.querySelector(MENU_SELECTOR);
  this._firstMenuLink = this._menu.querySelector(MENU_LINK_SELECTOR); // Set ARIA attributes

  this._link.setAttribute('aria-haspopup', 'true');

  this._menu.setAttribute(ARIA_ROLE, 'menu');

  this._menu.setAttribute('aria-labelledby', this._link.getAttribute('id'));

  this._menu.setAttribute('aria-hidden', 'true');

  [].forEach.call(this._menu.querySelectorAll('li'), function (el) {
    el.setAttribute(ARIA_ROLE, 'presentation');
  });
  [].forEach.call(this._menu.querySelectorAll('a'), function (el) {
    el.setAttribute(ARIA_ROLE, 'menuitem');
  }); // Toggle on click

  this._link.addEventListener('click', function (e) {
    // e.stopPropagation();
    // e.preventDefault();
    // ddm.toggle();
    if (ddm._state !== MENU_OPEN) {
      e.stopPropagation();
      e.preventDefault();
      ddm.show();
    }
  }); // Toggle on hover


  this._link.addEventListener('mouseover', function (e) {
    e.stopPropagation();
    e.preventDefault();
    var isDesktop = window.matchMedia('only screen and (min-width: 768px)').matches;

    if (isDesktop) {
      // Only run this for desktop only. Submenu is shown on hover using CSS but we have no way to track it in JS. This does that.
      ddm.toggle();
    }
  }); // Close menu when hovered out of - Desktop.


  this._link.addEventListener('mouseout', function (e) {
    e.stopPropagation();
    e.preventDefault();
    var isDesktop = window.matchMedia('only screen and (min-width: 768px)').matches;

    if (isDesktop) {
      ddm.hide();

      ddm._link.blur();
    }
  }); // Keyboard navigation


  document.addEventListener('keydown', function (e) {
    var currentLink, previousSibling, nextSibling, previousLink, nextLink; // if the menu isn't active, ignore

    if (ddm._state !== MENU_OPEN) {
      return;
    } // if the menu is the parent of an open, active submenu, ignore


    if (ddm._menu.querySelector(MENU_ACTIVE_SELECTOR)) {
      return;
    }

    currentLink = ddm._menu.querySelector(':focus'); // Dismiss an open menu on ESC

    if (e.keyCode === 27) {
      /* Esc */
      ddm.halt(e);
      ddm.hide();
    } // Go to the next link on down arrow
    else if (ARROW_KEYS_ENABLED && e.keyCode === 40) {
        /* Down arrow */
        ddm.halt(e); // get the nextSibling (an LI) of the current link's LI

        nextSibling = currentLink ? currentLink.parentNode.nextSibling : null; // if the nextSibling is a text node (not an element), go to the next one

        while (nextSibling && nextSibling.nodeType !== 1) {
          nextSibling = nextSibling.nextSibling;
        }

        nextLink = nextSibling ? nextSibling.querySelector('.menu-item a') : null; // if there is no currently focused link, focus the first one

        if (!currentLink) {
          ddm._menu.querySelector('.menu-item a').focus();
        } else if (nextLink) {
          nextLink.focus();
        }
      } // Go to the previous link on up arrow
      else if (ARROW_KEYS_ENABLED && e.keyCode === 38) {
          /* Up arrow */
          ddm.halt(e); // get the currently focused link

          previousSibling = currentLink ? currentLink.parentNode.previousSibling : null;

          while (previousSibling && previousSibling.nodeType !== 1) {
            previousSibling = previousSibling.previousSibling;
          }

          previousLink = previousSibling ? previousSibling.querySelector('.menu-item a') : null; // if there is no currently focused link, focus the last link

          if (!currentLink) {
            ddm._menu.querySelector('.menu-item:last-child .menu-item a').focus();
          } // else if there is a previous item, go to the previous item
          else if (previousLink) {
              previousLink.focus();
            }
        }
  }); // Dismiss an open menu on outside event

  document.addEventListener(DISMISS_EVENT, function (e) {
    var target = e.target;

    if (target !== ddm._link && !ddm._menu.contains(target)) {
      ddm.hide();

      ddm._link.blur();
    }
  });
}

function initDropdowns() {
  var dropdownParents = document.querySelectorAll('.menu-item-has-children');

  for (var i = 0; i < dropdownParents.length; i++) {
    var ddm = new PureDropdown(dropdownParents[i]);
  }
}

jQuery('.hamburger').click(function () {
  jQuery(this).toggleClass("active");
  jQuery(this).next('.nav-menu').toggleClass("active");
});

function seedprod_add_basic_lightbox(blockId) {
  jQuery("#sp-" + blockId + " a").click(function () {
    return false;
  });
  var imgbasicpreview = new ImgPreviewer('#sp-' + blockId, {
    scrollbar: true,
    dataUrlKey: 'href'
  });
  imgbasicpreview.update();
}

function seedprod_add_gallery_lightbox(blockId) {
  jQuery("#sp-" + blockId + " a.sp-gallery-items").click(function () {
    return false;
  });
  var imgpreview = new ImgPreviewer('#sp-' + blockId + " .sp-gallery-block", {
    scrollbar: true,
    dataUrlKey: 'href'
  });
  imgpreview.update();
  jQuery("#sp-" + blockId + " .sp-gallery-tabs a.sp-gallery-tab-title").click(function () {
    var dataindex = jQuery(this).attr("data-gallery-index");
    jQuery('#sp-' + blockId + ' .sp-gallery-tab-title').removeClass('sp-tab-active');
    jQuery(this).addClass('sp-tab-active'); //console.log(dataindex);

    jQuery.each(jQuery('#sp-' + blockId + ' .sp-gallery-items'), function (i, v) {
      jQuery(this).removeClass('sp-hidden-items');
      jQuery(this).removeClass('zoom-in');

      if (dataindex != 'all') {
        var indexvalues = jQuery(v).data('tags');

        if (indexvalues.indexOf(dataindex) > -1) {} else {
          jQuery(this).addClass('sp-hidden-items');
        }
      }
    });
    imgpreview.update();
  });
}

function seedprod_add_gallery_js(blockId) {
  jQuery("#sp-" + blockId + " .sp-gallery-tabs a.sp-gallery-tab-title").click(function () {
    var dataindex = jQuery(this).attr("data-gallery-index");
    jQuery('#sp-' + blockId + ' .sp-gallery-tab-title').removeClass('sp-tab-active');
    jQuery(this).addClass('sp-tab-active'); //console.log(dataindex);

    jQuery.each(jQuery('#sp-' + blockId + ' .sp-gallery-items'), function (i, v) {
      jQuery(this).removeClass('sp-hidden-items');
      jQuery(this).removeClass('zoom-in');

      if (dataindex != 'all') {
        var indexvalues = jQuery(v).data('tags');

        if (indexvalues.indexOf(dataindex) > -1) {} else {
          jQuery(this).addClass('sp-hidden-items');
        }
      }
    });
  });
} // Check if an element is in the viewport.


jQuery.fn.isInViewport = function () {
  var elementTop = jQuery(this).offset().top;
  var elementBottom = elementTop + jQuery(this).outerHeight();
  var viewportTop = jQuery(window).scrollTop();
  var viewportBottom = viewportTop + jQuery(window).height();
  return elementBottom > viewportTop && elementTop < viewportBottom;
}; // Trigger counter block.


function counter(blockId) {
  var duration = jQuery("#sp-counter-".concat(blockId, " .sp-counter-text-wrapper .sp-counter-number")).attr('data-duration');
  var startNumber = jQuery("#sp-counter-".concat(blockId, " .sp-counter-text-wrapper .sp-counter-number")).attr('data-start-number');
  var endNumber = jQuery("#sp-counter-".concat(blockId, " .sp-counter-text-wrapper .sp-counter-number")).attr('data-end-number');
  var thousandsSeparator = jQuery("#sp-counter-".concat(blockId, " .sp-counter-text-wrapper .sp-counter-number")).attr('data-thousands-separator');
  var separator = jQuery("#sp-counter-".concat(blockId, " .sp-counter-text-wrapper .sp-counter-number")).attr('data-separator');
  var options = {};
  var delimeter = {
    'default': ',',
    'space': ' ',
    'dot': '.'
  };
  options.duration = duration;
  options.delimiter = thousandsSeparator ? delimeter[separator] : '';
  options.toValue = endNumber;
  jQuery("#sp-counter-number-".concat(blockId)).html(startNumber);
  jQuery("#sp-counter-number-".concat(blockId)).numerator(options);
}

function beforeafterslider(blockId, options) {
  console.log(options);
  /*
  let options1 = {
  	default_offset_pct: 0.5,
  	orientation: "horizontal",
  	before_label: "Before",
  	after_label: "After",
  	no_overlay: false,//self.block.settings.overlayColor,
  	move_slider_on_hover: true,
  	move_with_handle_only: true,
  	click_to_move: true
  };
  */

  jQuery("#sp-toggle-".concat(blockId, " .twentytwenty-container")).twentytwenty(options);
}

function hotspotTooltips(blockId, items) {
  var trigger = jQuery("#sp-".concat(blockId, " .sp-hotspot-image")).attr('data-tooltip-trigger');
  var animation = jQuery("#sp-".concat(blockId, " .sp-hotspot-image")).attr('data-tooltip-animation');
  var duration = jQuery("#sp-".concat(blockId, " .sp-hotspot-image")).attr('data-tooltip-duration');
  var position = jQuery("#sp-".concat(blockId, " .sp-hotspot-image")).attr('data-tooltip-position');
  var showArrow = jQuery("#sp-".concat(blockId, " .sp-hotspot-image")).attr('data-tooltip-show-arrow');
  var maxWidth = jQuery("#sp-".concat(blockId, " .sp-hotspot-image")).attr('data-tooltip-max-width');
  items = JSON.parse(items);
  items.map(function (item, index) {
    var $myElement = "#sp-".concat(blockId, " #hotspot-").concat(blockId, "-").concat(index);
    jQuery($myElement).tooltipster({
      animation: animation,
      delay: duration,
      trigger: trigger,
      side: position,
      arrow: 'true' === showArrow ? true : false,
      maxWidth: maxWidth,
      content: item.tooltipContent,
      contentCloning: true,
      contentAsHTML: true
    });
  });
}