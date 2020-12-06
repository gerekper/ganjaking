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
  }); //var j2 = jQuery.ajax( "/" );

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
    } // alert( "We got what we came for!" );

  }).fail(function (jqXHR, textStatus, errorThrown) {
    if (seeprod_enable_recaptcha === 1) {
      grecaptcha.reset(sp_emplacementRecaptcha[id]);
    } // var response = JSON.parse(j1.responseText);
    // var errorString  = '';
    // jQuery.each( response.errors, function( key, value) {
    //     errorString +=  value ;
    // });
    // alert(errorString);


    console.log(j1);
    console.log(j2);
  });
  return;
} // countdown


var x = [];

function countdown(type, ts, id, action, redirect) {
  var now = new Date().getTime();

  if (type == 'vt') {
    ts = ts + now; //console.log(ts);

    var seedprod_enddate = seedprodCookies.get('seedprod_enddate_' + id);

    if (seedprod_enddate != undefined) {
      ts = seedprod_enddate;
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


      if (action === "2") {
        window.location.href = redirect;
      }
    }
  }, 1000);
}

function pad(n, width, z) {
  z = z || "0";
  n = n + "";
  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
} // remove any theme css


jQuery(document).ready(function ($) {
  $('link[href*="/wp-content/themes/"]').remove();
}); // Dynamic Text
// jQuery(document).ready(function ($) {
// 	var default_format = "{MM}/{dd}/{yyyy}";
// 	var html = $("#sp-page").html();
// 	var newTxt = html.split("[#");
// 	for (var i = 1; i < newTxt.length; i++) {
// 		var format = default_format;
// 		var tag = newTxt[i].split("]")[0];
// 		var parts = tag.split(":");
// 		if (parts.length > 1) {
// 			format = parts[1];
// 		} else {
// 			format = default_format;
// 		}
// 		var d = Date.create(parts[0]);
// 		var regex = "\\[#" + tag + "]";
// 		var re = new RegExp(regex, "g");
// 		$("#sp-page *").replaceText(re, d.format(format));
// 	}
// });

/*!-----------------------------------------------------------------------------
 * easy_background
 * v2.0 - built 2017-10-30
 * Licensed under the MIT License.
 * http://www.testersite.it/github/easy_background/v3/
 * ----------------------------------------------------------------------------
 * Copyright (C) 2017 Eugenio Segala
 * --------------------------------------------------------------------------*/

function easy_background(selector, sld_args) {
  function empty_img(x) {
    if (x) {
      return "<img src='" + x + "'>";
    } else {
      return "";
    }
  } //use object same as arrays in php {nameofindex:variable} inside object you can use arrays [value1,val2] (variable in object can be as array
  //var sld_args={i:["img/555.jpg","img/44.jpg","img/33.jpg","img/22.jpg","img/11.jpg","img/1.jpg","img/2.jpg","img/3.jpg","img/4.jpg","img/5.jpg"],d:[3000,3000,3000,3000,3000] };
  //if delay is empty or forgotten then use this default value


  var def_del = 5000;
  var p = document.createElement("div");
  p.innerHTML = " ";
  p.classList.add("easy_slider");
  document.body.insertBefore(p, document.body.firstChild); //switch all values in object -- objectname.index in you case sld_args is object and i is index of array which keep images (i). We use this function for fill div with img tags
  //and for insert delays into empty or forgotten places in object

  sld_args.slide.forEach(function (v, i) {
    if (v) {
      document.querySelector(".easy_slider").innerHTML += empty_img(v);

      if (typeof sld_args.delay[i] == 'undefined' || typeof sld_args.delay[i] == '' || sld_args.delay[i] == 0) {
        sld_args.delay[i] = def_del;
      }
    }
  }); //add various style on selector

  document.querySelector(".easy_slider").style.display = "none"; //add various style on selector

  document.querySelector(selector).style.backgroundSize = "cover";
  document.querySelector(selector).style.backgroundRepeat = "no-repeat";
  document.querySelector(selector).style.backgroundPosition = "center center";
  setTimeout(function () {
    //add various style on selector
    if (typeof sld_args.transition_timing === 'undefined') {
      sld_args.transition_timing = "ease-in";
    }

    if (typeof sld_args.transition_duration === 'undefined') {
      sld_args.transition_duration = 500;
    }

    var transition = "all " + sld_args.transition_duration + 'ms ' + sld_args.transition_timing;
    document.querySelector(selector).style.WebkitTransition = transition;
    document.querySelector(selector).style.MozTransition = transition;
    document.querySelector(selector).style.MsTransition = transition;
    document.querySelector(selector).style.OTransition = transition;
    document.querySelector(selector).style.transition = transition;
  }, 100); //this n is number of row  in object - if first row one function if more than 1 then other

  var n = 1; //li collection previous delays from previous slides

  var li = 0;

  function slider() {
    //switching all images one by one
    sld_args.slide.forEach(function (vvv, iii) {
      //here go all slides except first
      if (n > 1) {
        //set delay from collected number from previous slides
        var delay = li;
        setTimeout(function () {
          document.querySelector(selector).style.backgroundImage = "url('" + vvv + "')";
        }, delay); // >1
        //collecting delays from curent

        li = li + sld_args.delay[iii];
      } else {
        //this function for only  first slide
        //next row
        n++; //collect delay first time

        li = sld_args.delay[iii];
        document.querySelector(selector).style.backgroundImage = "url('" + vvv + "')";
      }
    });
  }

  ;
  slider();
  setInterval(function () {
    // REPEAT
    slider(); //here used length of array of delays in object instead you tot_time variable
  }, sld_args.delay.length);
}