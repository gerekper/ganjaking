/*
 * jQuery FlexSlider v2.4.0
 * Copyright 2012 WooThemes
 * Contributing Author: Tyler Smith
 */!function ($) {
     $.flexslider = function (e, t) { var a = $(e); a.vars = $.extend({}, $.flexslider.defaults, t); var n = a.vars.namespace, i = window.navigator && window.navigator.msPointerEnabled && window.MSGesture, s = ("ontouchstart" in window || i || window.DocumentTouch && document instanceof DocumentTouch) && a.vars.touch, r = "click touchend MSPointerUp keyup", o = "", l, c = "vertical" === a.vars.direction, d = a.vars.reverse, u = a.vars.itemWidth > 0, v = "fade" === a.vars.animation, p = "" !== a.vars.asNavFor, m = {}, f = !0; $.data(e, "flexslider", a), m = { init: function () { a.animating = !1, a.currentSlide = parseInt(a.vars.startAt ? a.vars.startAt : 0, 10), isNaN(a.currentSlide) && (a.currentSlide = 0), a.animatingTo = a.currentSlide, a.atEnd = 0 === a.currentSlide || a.currentSlide === a.last, a.containerSelector = a.vars.selector.substr(0, a.vars.selector.search(" ")), a.slides = $(a.vars.selector, a), a.container = $(a.containerSelector, a), a.count = a.slides.length, a.syncExists = $(a.vars.sync).length > 0, "slide" === a.vars.animation && (a.vars.animation = "swing"), a.prop = c ? "top" : "marginLeft", a.args = {}, a.manualPause = !1, a.stopped = !1, a.started = !1, a.startTimeout = null, a.transitions = !a.vars.video && !v && a.vars.useCSS && function () { var e = document.createElement("div"), t = ["perspectiveProperty", "WebkitPerspective", "MozPerspective", "OPerspective", "msPerspective"]; for (var n in t) if (void 0 !== e.style[t[n]]) return a.pfx = t[n].replace("Perspective", "").toLowerCase(), a.prop = "-" + a.pfx + "-transform", !0; return !1 }(), a.ensureAnimationEnd = "", "" !== a.vars.controlsContainer && (a.controlsContainer = $(a.vars.controlsContainer).length > 0 && $(a.vars.controlsContainer)), "" !== a.vars.manualControls && (a.manualControls = $(a.vars.manualControls).length > 0 && $(a.vars.manualControls)), a.vars.randomize && (a.slides.sort(function () { return Math.round(Math.random()) - .5 }), a.container.empty().append(a.slides)), a.doMath(), a.setup("init"), a.vars.controlNav && m.controlNav.setup(), a.vars.directionNav && m.directionNav.setup(), a.vars.keyboard && (1 === $(a.containerSelector).length || a.vars.multipleKeyboard) && $(document).bind("keyup", function (e) { var t = e.keyCode; if (!a.animating && (39 === t || 37 === t)) { var n = 39 === t ? a.getTarget("next") : 37 === t ? a.getTarget("prev") : !1; a.flexAnimate(n, a.vars.pauseOnAction) } }), a.vars.mousewheel && a.bind("mousewheel", function (e, t, n, i) { e.preventDefault(); var s = a.getTarget(0 > t ? "next" : "prev"); a.flexAnimate(s, a.vars.pauseOnAction) }), a.vars.pausePlay && m.pausePlay.setup(), a.vars.slideshow && a.vars.pauseInvisible && m.pauseInvisible.init(), a.vars.slideshow && (a.vars.pauseOnHover && a.hover(function () { a.manualPlay || a.manualPause || a.pause() }, function () { a.manualPause || a.manualPlay || a.stopped || a.play() }), a.vars.pauseInvisible && m.pauseInvisible.isHidden() || (a.vars.initDelay > 0 ? a.startTimeout = setTimeout(a.play, a.vars.initDelay) : a.play())), p && m.asNav.setup(), s && a.vars.touch && m.touch(), (!v || v && a.vars.smoothHeight) && $(window).bind("resize orientationchange focus", m.resize), a.find("img").attr("draggable", "false"), setTimeout(function () { a.vars.start(a) }, 200) }, asNav: { setup: function () { a.asNav = !0, a.animatingTo = Math.floor(a.currentSlide / a.move), a.currentItem = a.currentSlide, a.slides.removeClass(n + "active-slide").eq(a.currentItem).addClass(n + "active-slide"), i ? (e._slider = a, a.slides.each(function () { var e = this; e._gesture = new MSGesture, e._gesture.target = e, e.addEventListener("MSPointerDown", function (e) { e.preventDefault(), e.currentTarget._gesture && e.currentTarget._gesture.addPointer(e.pointerId) }, !1), e.addEventListener("MSGestureTap", function (e) { e.preventDefault(); var t = $(this), n = t.index(); $(a.vars.asNavFor).data("flexslider").animating || t.hasClass("active") || (a.direction = a.currentItem < n ? "next" : "prev", a.flexAnimate(n, a.vars.pauseOnAction, !1, !0, !0)) }) })) : a.slides.on(r, function (e) { e.preventDefault(); var t = $(this), i = t.index(), s = t.offset().left - $(a).scrollLeft(); 0 >= s && t.hasClass(n + "active-slide") ? a.flexAnimate(a.getTarget("prev"), !0) : $(a.vars.asNavFor).data("flexslider").animating || t.hasClass(n + "active-slide") || (a.direction = a.currentItem < i ? "next" : "prev", a.flexAnimate(i, a.vars.pauseOnAction, !1, !0, !0)) }) } }, controlNav: { setup: function () { a.manualControls ? m.controlNav.setupManual() : m.controlNav.setupPaging() }, setupPaging: function () { var e = "thumbnails" === a.vars.controlNav ? "control-thumbs" : "control-paging", t = 1, i, s; if (a.controlNavScaffold = $('<ol class="' + n + "control-nav " + n + e + '"></ol>'), a.pagingCount > 1) for (var l = 0; l < a.pagingCount; l++) { if (s = a.slides.eq(l), i = "thumbnails" === a.vars.controlNav ? '<img src="' + s.attr("data-thumb") + '"/>' : "<a>" + t + "</a>", "thumbnails" === a.vars.controlNav && !0 === a.vars.thumbCaptions) { var c = s.attr("data-thumbcaption"); "" != c && void 0 != c && (i += '<span class="' + n + 'caption">' + c + "</span>") } a.controlNavScaffold.append("<li>" + i + "</li>"), t++ } a.controlsContainer ? $(a.controlsContainer).append(a.controlNavScaffold) : a.append(a.controlNavScaffold), m.controlNav.set(), m.controlNav.active(), a.controlNavScaffold.delegate("a, img", r, function (e) { if (e.preventDefault(), "" === o || o === e.type) { var t = $(this), i = a.controlNav.index(t); t.hasClass(n + "active") || (a.direction = i > a.currentSlide ? "next" : "prev", a.flexAnimate(i, a.vars.pauseOnAction)) } "" === o && (o = e.type), m.setToClearWatchedEvent() }) }, setupManual: function () { a.controlNav = a.manualControls, m.controlNav.active(), a.controlNav.bind(r, function (e) { if (e.preventDefault(), "" === o || o === e.type) { var t = $(this), i = a.controlNav.index(t); t.hasClass(n + "active") || (a.direction = i > a.currentSlide ? "next" : "prev", a.flexAnimate(i, a.vars.pauseOnAction)) } "" === o && (o = e.type), m.setToClearWatchedEvent() }) }, set: function () { var e = "thumbnails" === a.vars.controlNav ? "img" : "a"; a.controlNav = $("." + n + "control-nav li " + e, a.controlsContainer ? a.controlsContainer : a) }, active: function () { a.controlNav.removeClass(n + "active").eq(a.animatingTo).addClass(n + "active") }, update: function (e, t) { a.pagingCount > 1 && "add" === e ? a.controlNavScaffold.append($("<li><a>" + a.count + "</a></li>")) : 1 === a.pagingCount ? a.controlNavScaffold.find("li").remove() : a.controlNav.eq(t).closest("li").remove(), m.controlNav.set(), a.pagingCount > 1 && a.pagingCount !== a.controlNav.length ? a.update(t, e) : m.controlNav.active() } }, directionNav: { setup: function () { var e = $('<ul class="' + n + 'direction-nav"><li class="' + n + 'nav-prev"><a class="' + n + 'prev" href="#">' + a.vars.prevText + '</a></li><li class="' + n + 'nav-next"><a class="' + n + 'next" href="#">' + a.vars.nextText + "</a></li></ul>"); a.controlsContainer ? ($(a.controlsContainer).append(e), a.directionNav = $("." + n + "direction-nav li a", a.controlsContainer)) : (a.append(e), a.directionNav = $("." + n + "direction-nav li a", a)), m.directionNav.update(), a.directionNav.bind(r, function (e) { e.preventDefault(); var t; ("" === o || o === e.type) && (t = a.getTarget($(this).hasClass(n + "next") ? "next" : "prev"), a.flexAnimate(t, a.vars.pauseOnAction)), "" === o && (o = e.type), m.setToClearWatchedEvent() }) }, update: function () { var e = n + "disabled"; 1 === a.pagingCount ? a.directionNav.addClass(e).attr("tabindex", "-1") : a.vars.animationLoop ? a.directionNav.removeClass(e).removeAttr("tabindex") : 0 === a.animatingTo ? a.directionNav.removeClass(e).filter("." + n + "prev").addClass(e).attr("tabindex", "-1") : a.animatingTo === a.last ? a.directionNav.removeClass(e).filter("." + n + "next").addClass(e).attr("tabindex", "-1") : a.directionNav.removeClass(e).removeAttr("tabindex") } }, pausePlay: { setup: function () { var e = $('<div class="' + n + 'pauseplay"><a></a></div>'); a.controlsContainer ? (a.controlsContainer.append(e), a.pausePlay = $("." + n + "pauseplay a", a.controlsContainer)) : (a.append(e), a.pausePlay = $("." + n + "pauseplay a", a)), m.pausePlay.update(a.vars.slideshow ? n + "pause" : n + "play"), a.pausePlay.bind(r, function (e) { e.preventDefault(), ("" === o || o === e.type) && ($(this).hasClass(n + "pause") ? (a.manualPause = !0, a.manualPlay = !1, a.pause()) : (a.manualPause = !1, a.manualPlay = !0, a.play())), "" === o && (o = e.type), m.setToClearWatchedEvent() }) }, update: function (e) { "play" === e ? a.pausePlay.removeClass(n + "pause").addClass(n + "play").html(a.vars.playText) : a.pausePlay.removeClass(n + "play").addClass(n + "pause").html(a.vars.pauseText) } }, touch: function () { function t(t) { a.animating ? t.preventDefault() : (window.navigator.msPointerEnabled || 1 === t.touches.length) && (a.pause(), g = c ? a.h : a.w, S = Number(new Date), x = t.touches[0].pageX, b = t.touches[0].pageY, f = u && d && a.animatingTo === a.last ? 0 : u && d ? a.limit - (a.itemW + a.vars.itemMargin) * a.move * a.animatingTo : u && a.currentSlide === a.last ? a.limit : u ? (a.itemW + a.vars.itemMargin) * a.move * a.currentSlide : d ? (a.last - a.currentSlide + a.cloneOffset) * g : (a.currentSlide + a.cloneOffset) * g, p = c ? b : x, m = c ? x : b, e.addEventListener("touchmove", n, !1), e.addEventListener("touchend", s, !1)) } function n(e) { x = e.touches[0].pageX, b = e.touches[0].pageY, h = c ? p - b : p - x, y = c ? Math.abs(h) < Math.abs(x - m) : Math.abs(h) < Math.abs(b - m); var t = 500; (!y || Number(new Date) - S > t) && (e.preventDefault(), !v && a.transitions && (a.vars.animationLoop || (h /= 0 === a.currentSlide && 0 > h || a.currentSlide === a.last && h > 0 ? Math.abs(h) / g + 2 : 1), a.setProps(f + h, "setTouch"))) } function s(t) { if (e.removeEventListener("touchmove", n, !1), a.animatingTo === a.currentSlide && !y && null !== h) { var i = d ? -h : h, r = a.getTarget(i > 0 ? "next" : "prev"); a.canAdvance(r) && (Number(new Date) - S < 550 && Math.abs(i) > 50 || Math.abs(i) > g / 2) ? a.flexAnimate(r, a.vars.pauseOnAction) : v || a.flexAnimate(a.currentSlide, a.vars.pauseOnAction, !0) } e.removeEventListener("touchend", s, !1), p = null, m = null, h = null, f = null } function r(t) { t.stopPropagation(), a.animating ? t.preventDefault() : (a.pause(), e._gesture.addPointer(t.pointerId), w = 0, g = c ? a.h : a.w, S = Number(new Date), f = u && d && a.animatingTo === a.last ? 0 : u && d ? a.limit - (a.itemW + a.vars.itemMargin) * a.move * a.animatingTo : u && a.currentSlide === a.last ? a.limit : u ? (a.itemW + a.vars.itemMargin) * a.move * a.currentSlide : d ? (a.last - a.currentSlide + a.cloneOffset) * g : (a.currentSlide + a.cloneOffset) * g) } function o(t) { t.stopPropagation(); var a = t.target._slider; if (a) { var n = -t.translationX, i = -t.translationY; return w += c ? i : n, h = w, y = c ? Math.abs(w) < Math.abs(-n) : Math.abs(w) < Math.abs(-i), t.detail === t.MSGESTURE_FLAG_INERTIA ? void setImmediate(function () { e._gesture.stop() }) : void ((!y || Number(new Date) - S > 500) && (t.preventDefault(), !v && a.transitions && (a.vars.animationLoop || (h = w / (0 === a.currentSlide && 0 > w || a.currentSlide === a.last && w > 0 ? Math.abs(w) / g + 2 : 1)), a.setProps(f + h, "setTouch")))) } } function l(e) { e.stopPropagation(); var t = e.target._slider; if (t) { if (t.animatingTo === t.currentSlide && !y && null !== h) { var a = d ? -h : h, n = t.getTarget(a > 0 ? "next" : "prev"); t.canAdvance(n) && (Number(new Date) - S < 550 && Math.abs(a) > 50 || Math.abs(a) > g / 2) ? t.flexAnimate(n, t.vars.pauseOnAction) : v || t.flexAnimate(t.currentSlide, t.vars.pauseOnAction, !0) } p = null, m = null, h = null, f = null, w = 0 } } var p, m, f, g, h, S, y = !1, x = 0, b = 0, w = 0; i ? (e.style.msTouchAction = "none", e._gesture = new MSGesture, e._gesture.target = e, e.addEventListener("MSPointerDown", r, !1), e._slider = a, e.addEventListener("MSGestureChange", o, !1), e.addEventListener("MSGestureEnd", l, !1)) : e.addEventListener("touchstart", t, !1) }, resize: function () { !a.animating && a.is(":visible") && (u || a.doMath(), v ? m.smoothHeight() : u ? (a.slides.width(a.computedW), a.update(a.pagingCount), a.setProps()) : c ? (a.viewport.height(a.h), a.setProps(a.h, "setTotal")) : (a.vars.smoothHeight && m.smoothHeight(), a.newSlides.width(a.computedW), a.setProps(a.computedW, "setTotal"))) }, smoothHeight: function (e) { if (!c || v) { var t = v ? a : a.viewport; e ? t.animate({ height: a.slides.eq(a.animatingTo).height() }, e) : t.height(a.slides.eq(a.animatingTo).height()) } }, sync: function (e) { var t = $(a.vars.sync).data("flexslider"), n = a.animatingTo; switch (e) { case "animate": t.flexAnimate(n, a.vars.pauseOnAction, !1, !0); break; case "play": t.playing || t.asNav || t.play(); break; case "pause": t.pause() } }, uniqueID: function (e) { return e.filter("[id]").add(e.find("[id]")).each(function () { var e = $(this); e.attr("id", e.attr("id") + "_clone") }), e }, pauseInvisible: { visProp: null, init: function () { var e = m.pauseInvisible.getHiddenProp(); if (e) { var t = e.replace(/[H|h]idden/, "") + "visibilitychange"; document.addEventListener(t, function () { m.pauseInvisible.isHidden() ? a.startTimeout ? clearTimeout(a.startTimeout) : a.pause() : a.started ? a.play() : a.vars.initDelay > 0 ? setTimeout(a.play, a.vars.initDelay) : a.play() }) } }, isHidden: function () { var e = m.pauseInvisible.getHiddenProp(); return e ? document[e] : !1 }, getHiddenProp: function () { var e = ["webkit", "moz", "ms", "o"]; if ("hidden" in document) return "hidden"; for (var t = 0; t < e.length; t++) if (e[t] + "Hidden" in document) return e[t] + "Hidden"; return null } }, setToClearWatchedEvent: function () { clearTimeout(l), l = setTimeout(function () { o = "" }, 3e3) } }, a.flexAnimate = function (e, t, i, r, o) { if (a.vars.animationLoop || e === a.currentSlide || (a.direction = e > a.currentSlide ? "next" : "prev"), p && 1 === a.pagingCount && (a.direction = a.currentItem < e ? "next" : "prev"), !a.animating && (a.canAdvance(e, o) || i) && a.is(":visible")) { if (p && r) { var l = $(a.vars.asNavFor).data("flexslider"); if (a.atEnd = 0 === e || e === a.count - 1, l.flexAnimate(e, !0, !1, !0, o), a.direction = a.currentItem < e ? "next" : "prev", l.direction = a.direction, Math.ceil((e + 1) / a.visible) - 1 === a.currentSlide || 0 === e) return a.currentItem = e, a.slides.removeClass(n + "active-slide").eq(e).addClass(n + "active-slide"), !1; a.currentItem = e, a.slides.removeClass(n + "active-slide").eq(e).addClass(n + "active-slide"), e = Math.floor(e / a.visible) } if (a.animating = !0, a.animatingTo = e, t && a.pause(), a.vars.before(a), a.syncExists && !o && m.sync("animate"), a.vars.controlNav && m.controlNav.active(), u || a.slides.removeClass(n + "active-slide").eq(e).addClass(n + "active-slide"), a.atEnd = 0 === e || e === a.last, a.vars.directionNav && m.directionNav.update(), e === a.last && (a.vars.end(a), a.vars.animationLoop || a.pause()), v) s ? (a.slides.eq(a.currentSlide).css({ opacity: 0, zIndex: 1 }), a.slides.eq(e).css({ opacity: 1, zIndex: 2 }), a.wrapup(f)) : (a.slides.eq(a.currentSlide).css({ zIndex: 1 }).animate({ opacity: 0 }, a.vars.animationSpeed, a.vars.easing), a.slides.eq(e).css({ zIndex: 2 }).animate({ opacity: 1 }, a.vars.animationSpeed, a.vars.easing, a.wrapup)); else { var f = c ? a.slides.filter(":first").height() : a.computedW, g, h, S; u ? (g = a.vars.itemMargin, S = (a.itemW + g) * a.move * a.animatingTo, h = S > a.limit && 1 !== a.visible ? a.limit : S) : h = 0 === a.currentSlide && e === a.count - 1 && a.vars.animationLoop && "next" !== a.direction ? d ? (a.count + a.cloneOffset) * f : 0 : a.currentSlide === a.last && 0 === e && a.vars.animationLoop && "prev" !== a.direction ? d ? 0 : (a.count + 1) * f : d ? (a.count - 1 - e + a.cloneOffset) * f : (e + a.cloneOffset) * f, a.setProps(h, "", a.vars.animationSpeed), a.transitions ? (a.vars.animationLoop && a.atEnd || (a.animating = !1, a.currentSlide = a.animatingTo), a.container.unbind("webkitTransitionEnd transitionend"), a.container.bind("webkitTransitionEnd transitionend", function () { clearTimeout(a.ensureAnimationEnd), a.wrapup(f) }), clearTimeout(a.ensureAnimationEnd), a.ensureAnimationEnd = setTimeout(function () { a.wrapup(f) }, a.vars.animationSpeed + 100)) : a.container.animate(a.args, a.vars.animationSpeed, a.vars.easing, function () { a.wrapup(f) }) } a.vars.smoothHeight && m.smoothHeight(a.vars.animationSpeed) } }, a.wrapup = function (e) { v || u || (0 === a.currentSlide && a.animatingTo === a.last && a.vars.animationLoop ? a.setProps(e, "jumpEnd") : a.currentSlide === a.last && 0 === a.animatingTo && a.vars.animationLoop && a.setProps(e, "jumpStart")), a.animating = !1, a.currentSlide = a.animatingTo, a.vars.after(a) }, a.animateSlides = function () { !a.animating && f && a.flexAnimate(a.getTarget("next")) }, a.pause = function () { clearInterval(a.animatedSlides), a.animatedSlides = null, a.playing = !1, a.vars.pausePlay && m.pausePlay.update("play"), a.syncExists && m.sync("pause") }, a.play = function () { a.playing && clearInterval(a.animatedSlides), a.animatedSlides = a.animatedSlides || setInterval(a.animateSlides, a.vars.slideshowSpeed), a.started = a.playing = !0, a.vars.pausePlay && m.pausePlay.update("pause"), a.syncExists && m.sync("play") }, a.stop = function () { a.pause(), a.stopped = !0 }, a.canAdvance = function (e, t) { var n = p ? a.pagingCount - 1 : a.last; return t ? !0 : p && a.currentItem === a.count - 1 && 0 === e && "prev" === a.direction ? !0 : p && 0 === a.currentItem && e === a.pagingCount - 1 && "next" !== a.direction ? !1 : e !== a.currentSlide || p ? a.vars.animationLoop ? !0 : a.atEnd && 0 === a.currentSlide && e === n && "next" !== a.direction ? !1 : a.atEnd && a.currentSlide === n && 0 === e && "next" === a.direction ? !1 : !0 : !1 }, a.getTarget = function (e) { return a.direction = e, "next" === e ? a.currentSlide === a.last ? 0 : a.currentSlide + 1 : 0 === a.currentSlide ? a.last : a.currentSlide - 1 }, a.setProps = function (e, t, n) { var i = function () { var n = e ? e : (a.itemW + a.vars.itemMargin) * a.move * a.animatingTo, i = function () { if (u) return "setTouch" === t ? e : d && a.animatingTo === a.last ? 0 : d ? a.limit - (a.itemW + a.vars.itemMargin) * a.move * a.animatingTo : a.animatingTo === a.last ? a.limit : n; switch (t) { case "setTotal": return d ? (a.count - 1 - a.currentSlide + a.cloneOffset) * e : (a.currentSlide + a.cloneOffset) * e; case "setTouch": return d ? e : e; case "jumpEnd": return d ? e : a.count * e; case "jumpStart": return d ? a.count * e : e; default: return e } }(); return -1 * Math.floor(i) + "px" }(); a.transitions && (i = c ? "translate3d(0," + i + ",0)" : "translate3d(" + i + ",0,0)", n = void 0 !== n ? n / 1e3 + "s" : "0s", a.container.css("-" + a.pfx + "-transition-duration", n), a.container.css("transition-duration", n)), a.args[a.prop] = i, (a.transitions || void 0 === n) && a.container.css(a.args), a.container.css("transform", i) }, a.setup = function (e) { if (v) a.slides.css({ width: "100%", "float": "left", marginRight: "-100%", position: "relative" }), "init" === e && (s ? a.slides.css({ opacity: 0, display: "block", webkitTransition: "opacity " + a.vars.animationSpeed / 1e3 + "s ease", zIndex: 1 }).eq(a.currentSlide).css({ opacity: 1, zIndex: 2 }) : 0 == a.vars.fadeFirstSlide ? a.slides.css({ opacity: 0, display: "block", zIndex: 1 }).eq(a.currentSlide).css({ zIndex: 2 }).css({ opacity: 1 }) : a.slides.css({ opacity: 0, display: "block", zIndex: 1 }).eq(a.currentSlide).css({ zIndex: 2 }).animate({ opacity: 1 }, a.vars.animationSpeed, a.vars.easing)), a.vars.smoothHeight && m.smoothHeight(); else { var t, i; "init" === e && (a.viewport = $('<div class="' + n + 'viewport"></div>').css({ overflow: "hidden", position: "relative" }).appendTo(a).append(a.container), a.cloneCount = 0, a.cloneOffset = 0, d && (i = $.makeArray(a.slides).reverse(), a.slides = $(i), a.container.empty().append(a.slides))), a.vars.animationLoop && !u && (a.cloneCount = 2, a.cloneOffset = 1, "init" !== e && a.container.find(".clone").remove(), a.container.append(m.uniqueID(a.slides.first().clone().addClass("clone")).attr("aria-hidden", "true")).prepend(m.uniqueID(a.slides.last().clone().addClass("clone")).attr("aria-hidden", "true"))), a.newSlides = $(a.vars.selector, a), t = d ? a.count - 1 - a.currentSlide + a.cloneOffset : a.currentSlide + a.cloneOffset, c && !u ? (a.container.height(200 * (a.count + a.cloneCount) + "%").css("position", "absolute").width("100%"), setTimeout(function () { a.newSlides.css({ display: "block" }), a.doMath(), a.viewport.height(a.h), a.setProps(t * a.h, "init") }, "init" === e ? 100 : 0)) : (a.container.width(200 * (a.count + a.cloneCount) + "%"), a.setProps(t * a.computedW, "init"), setTimeout(function () { a.doMath(), a.newSlides.css({ width: a.computedW, "float": "left", display: "block" }), a.vars.smoothHeight && m.smoothHeight() }, "init" === e ? 100 : 0)) } u || a.slides.removeClass(n + "active-slide").eq(a.currentSlide).addClass(n + "active-slide"), a.vars.init(a) }, a.doMath = function () { var e = a.slides.first(), t = a.vars.itemMargin, n = a.vars.minItems, i = a.vars.maxItems; a.w = void 0 === a.viewport ? a.width() : a.viewport.width(), a.h = e.height(), a.boxPadding = e.outerWidth() - e.width(), u ? (a.itemT = a.vars.itemWidth + t, a.minW = n ? n * a.itemT : a.w, a.maxW = i ? i * a.itemT - t : a.w, a.itemW = a.minW > a.w ? (a.w - t * (n - 1)) / n : a.maxW < a.w ? (a.w - t * (i - 1)) / i : a.vars.itemWidth > a.w ? a.w : a.vars.itemWidth, a.visible = Math.floor(a.w / a.itemW), a.move = a.vars.move > 0 && a.vars.move < a.visible ? a.vars.move : a.visible, a.pagingCount = Math.ceil((a.count - a.visible) / a.move + 1), a.last = a.pagingCount - 1, a.limit = 1 === a.pagingCount ? 0 : a.vars.itemWidth > a.w ? a.itemW * (a.count - 1) + t * (a.count - 1) : (a.itemW + t) * a.count - a.w - t) : (a.itemW = a.w, a.pagingCount = a.count, a.last = a.count - 1), a.computedW = a.itemW - a.boxPadding }, a.update = function (e, t) { a.doMath(), u || (e < a.currentSlide ? a.currentSlide += 1 : e <= a.currentSlide && 0 !== e && (a.currentSlide -= 1), a.animatingTo = a.currentSlide), a.vars.controlNav && !a.manualControls && ("add" === t && !u || a.pagingCount > a.controlNav.length ? m.controlNav.update("add") : ("remove" === t && !u || a.pagingCount < a.controlNav.length) && (u && a.currentSlide > a.last && (a.currentSlide -= 1, a.animatingTo -= 1), m.controlNav.update("remove", a.last))), a.vars.directionNav && m.directionNav.update() }, a.addSlide = function (e, t) { var n = $(e); a.count += 1, a.last = a.count - 1, c && d ? void 0 !== t ? a.slides.eq(a.count - t).after(n) : a.container.prepend(n) : void 0 !== t ? a.slides.eq(t).before(n) : a.container.append(n), a.update(t, "add"), a.slides = $(a.vars.selector + ":not(.clone)", a), a.setup(), a.vars.added(a) }, a.removeSlide = function (e) { var t = isNaN(e) ? a.slides.index($(e)) : e; a.count -= 1, a.last = a.count - 1, isNaN(e) ? $(e, a.slides).remove() : c && d ? a.slides.eq(a.last).remove() : a.slides.eq(e).remove(), a.doMath(), a.update(t, "remove"), a.slides = $(a.vars.selector + ":not(.clone)", a), a.setup(), a.vars.removed(a) }, m.init() }, $(window).blur(function (e) { focused = !1 }).focus(function (e) { focused = !0 }), $.flexslider.defaults = { namespace: "flex-", selector: ".slides > li", animation: "fade", easing: "swing", direction: "horizontal", reverse: !1, animationLoop: !0, smoothHeight: !1, startAt: 0, slideshow: !0, slideshowSpeed: 7e3, animationSpeed: 600, initDelay: 0, randomize: !1, fadeFirstSlide: !0, thumbCaptions: !1, pauseOnAction: !0, pauseOnHover: !1, pauseInvisible: !0, useCSS: !0, touch: !0, video: !1, controlNav: !0, directionNav: !0, prevText: "Previous", nextText: "Next", keyboard: !0, multipleKeyboard: !1, mousewheel: !1, pausePlay: !1, pauseText: "Pause", playText: "Play", controlsContainer: "", manualControls: "", sync: "", asNavFor: "", itemWidth: 0, itemMargin: 0, minItems: 1, maxItems: 0, move: 0, allowOneSlide: !0, start: function () { }, before: function () { }, after: function () { }, end: function () { }, added: function () { }, removed: function () { }, init: function () { } }, $.fn.flexslider = function (e) {
         if (void 0 === e && (e = {}), "object" == typeof e) return this.each(function () { var t = $(this), a = e.selector ? e.selector : ".slides > li", n = t.find(a); 1 === n.length && e.allowOneSlide === !0 || 0 === n.length ? (n.fadeIn(400), e.start && e.start(t)) : void 0 === t.data("flexslider") && new $.flexslider(this, e) }); var t = $(this).data("flexslider"); switch (e) {
             case "play": t.play(); break; case "pause": t.pause(); break; case "stop": t.stop(); break; case "next": t.flexAnimate(t.getTarget("next"), !0); break; case "prev": case "previous": t.flexAnimate(t.getTarget("prev"), !0); break; default: "number" == typeof e && t.flexAnimate(e, !0)
         }
     }
 }(jQuery);
/*
 * ##################################################################################################################################################

* $ lightbox_me
* By: Buck Wilson
* Version : 2.4
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

(function (n) { n.fn.lightbox_me = function (t) { return this.each(function () { function e() { var t = r[0].style; i.destroyOnClose ? r.add(u).remove() : r.add(u).hide(); i.parentLightbox && i.parentLightbox.fadeIn(200); i.preventScroll && n("body").css("overflow", ""); s.remove(); r.undelegate(i.closeSelector, "click"); r.unbind("close", e); r.unbind("repositon", f); n(window).unbind("resize", o); n(window).unbind("resize", f); n(window).unbind("scroll", f); n(window).unbind("keyup.lightbox_me"); i.onClose() } function c(n) { (n.keyCode == 27 || n.DOM_VK_ESCAPE == 27 && n.which == 0) && i.closeEsc && e() } function o() { n(window).height() < n(document).height() ? (u.css({ height: n(document).height() + "px" }), s.css({ height: n(document).height() + "px" })) : u.css({ height: "100%" }) } function f() { var u = r[0].style, t; r.css({ left: "50%", marginLeft: r.outerWidth() / -2, zIndex: i.zIndex + 3 }); r.height() + 80 >= n(window).height() && r.css("position") != "absolute" ? (t = n(document).scrollTop() + 40, r.css({ position: "absolute", top: t + "px", marginTop: 0 })) : r.height() + 80 < n(window).height() && (i.centered ? r.css({ position: "fixed", top: "50%", marginTop: r.outerHeight() / -2 }) : r.css({ position: "fixed" }).css(i.modalCSS), i.preventScroll && n("body").css("overflow", "hidden")) } var i = n.extend({}, n.fn.lightbox_me.defaults, t), u = n(), r = n(this), s = n('<iframe id="foo" style="z-index: ' + (i.zIndex + 1) + ';border: none; margin: 0; padding: 0; position: absolute; width: 100%; height: 100%; top: 0; left: 0; filter: mask();"/>'), h; i.showOverlay && (h = n(".js_lb_overlay:visible"), u = h.length > 0 ? n('<div class="lb_overlay_clear js_lb_overlay"/>') : n('<div class="' + i.classPrefix + '_overlay js_lb_overlay"/>')); n("body").append(r.hide()).append(u); i.showOverlay && (o(), u.css({ position: "absolute", width: "100%", top: 0, left: 0, right: 0, bottom: 0, zIndex: i.zIndex + 2, display: "none" }), u.hasClass("lb_overlay_clear") || u.css(i.overlayCSS)); i.showOverlay ? u.fadeIn(i.overlaySpeed, function () { f(); r[i.appearEffect](i.lightboxSpeed, function () { o(); f(); i.onLoad() }) }) : (f(), r[i.appearEffect](i.lightboxSpeed, function () { i.onLoad() })); i.parentLightbox && i.parentLightbox.fadeOut(200); n(window).resize(o).resize(f).scroll(f); n(window).bind("keyup.lightbox_me", c); i.closeClick && u.click(function (n) { e(); n.preventDefault }); r.delegate(i.closeSelector, "click", function (n) { e(); n.preventDefault() }); r.bind("close", e); r.bind("reposition", f) }) }; n.fn.lightbox_me.defaults = { appearEffect: "fadeIn", appearEase: "", overlaySpeed: 250, lightboxSpeed: 300, closeSelector: ".close", closeClick: !0, closeEsc: !0, destroyOnClose: !1, showOverlay: !0, parentLightbox: !1, preventScroll: !1, onLoad: function () { }, onClose: function () { }, classPrefix: "lb", zIndex: 999, centered: !1, modalCSS: { top: "40px" }, overlayCSS: { background: "black", opacity: .3 } } })(jQuery)

/*! nanoScrollerJS - v0.8.4 - (c) 2014 James Florentino; Licensed MIT */
!function (a) { return "function" == typeof define && define.amd ? define(["jquery"], function (b) { return a(b, window, document) }) : a(jQuery, window, document) }(function (a, b, c) { "use strict"; var d, e, f, g, h, i, j, k, l, m, n, o, p, q, r, s, t, u, v, w, x, y, z, A, B, C, D, E, F, G, H; z = { paneClass: "nano-pane", sliderClass: "nano-slider", contentClass: "nano-content", iOSNativeScrolling: !1, preventPageScrolling: !1, disableResize: !1, alwaysVisible: !1, flashDelay: 1500, sliderMinHeight: 20, sliderMaxHeight: null, documentContext: null, windowContext: null }, u = "scrollbar", t = "scroll", l = "mousedown", m = "mouseenter", n = "mousemove", p = "mousewheel", o = "mouseup", s = "resize", h = "drag", i = "enter", w = "up", r = "panedown", f = "DOMMouseScroll", g = "down", x = "wheel", j = "keydown", k = "keyup", v = "touchmove", d = "Microsoft Internet Explorer" === b.navigator.appName && /msie 7./i.test(b.navigator.appVersion) && b.ActiveXObject, e = null, D = b.requestAnimationFrame, y = b.cancelAnimationFrame, F = c.createElement("div").style, H = function () { var a, b, c, d, e, f; for (d = ["t", "webkitT", "MozT", "msT", "OT"], a = e = 0, f = d.length; f > e; a = ++e) if (c = d[a], b = d[a] + "ransform", b in F) return d[a].substr(0, d[a].length - 1); return !1 }(), G = function (a) { return H === !1 ? !1 : "" === H ? a : H + a.charAt(0).toUpperCase() + a.substr(1) }, E = G("transform"), B = E !== !1, A = function () { var a, b, d; return a = c.createElement("div"), b = a.style, b.position = "absolute", b.width = "100px", b.height = "100px", b.overflow = t, b.top = "-9999px", c.body.appendChild(a), d = a.offsetWidth - a.clientWidth, c.body.removeChild(a), d }, C = function () { var a, c, d; return c = b.navigator.userAgent, (a = /(?=.+Mac OS X)(?=.+Firefox)/.test(c)) ? (d = /Firefox\/\d{2}\./.exec(c), d && (d = d[0].replace(/\D+/g, "")), a && +d > 23) : !1 }, q = function () { function j(d, f) { this.el = d, this.options = f, e || (e = A()), this.$el = a(this.el), this.doc = a(this.options.documentContext || c), this.win = a(this.options.windowContext || b), this.body = this.doc.find("body"), this.$content = this.$el.children("." + f.contentClass), this.$content.attr("tabindex", this.options.tabIndex || 0), this.content = this.$content[0], this.previousPosition = 0, this.options.iOSNativeScrolling && null != this.el.style.WebkitOverflowScrolling ? this.nativeScrolling() : this.generate(), this.createEvents(), this.addEvents(), this.reset() } return j.prototype.preventScrolling = function (a, b) { if (this.isActive) if (a.type === f) (b === g && a.originalEvent.detail > 0 || b === w && a.originalEvent.detail < 0) && a.preventDefault(); else if (a.type === p) { if (!a.originalEvent || !a.originalEvent.wheelDelta) return; (b === g && a.originalEvent.wheelDelta < 0 || b === w && a.originalEvent.wheelDelta > 0) && a.preventDefault() } }, j.prototype.nativeScrolling = function () { this.$content.css({ WebkitOverflowScrolling: "touch" }), this.iOSNativeScrolling = !0, this.isActive = !0 }, j.prototype.updateScrollValues = function () { var a, b; a = this.content, this.maxScrollTop = a.scrollHeight - a.clientHeight, this.prevScrollTop = this.contentScrollTop || 0, this.contentScrollTop = a.scrollTop, b = this.contentScrollTop > this.previousPosition ? "down" : this.contentScrollTop < this.previousPosition ? "up" : "same", this.previousPosition = this.contentScrollTop, "same" !== b && this.$el.trigger("update", { position: this.contentScrollTop, maximum: this.maxScrollTop, direction: b }), this.iOSNativeScrolling || (this.maxSliderTop = this.paneHeight - this.sliderHeight, this.sliderTop = 0 === this.maxScrollTop ? 0 : this.contentScrollTop * this.maxSliderTop / this.maxScrollTop) }, j.prototype.setOnScrollStyles = function () { var a; B ? (a = {}, a[E] = "translate(0, " + this.sliderTop + "px)") : a = { top: this.sliderTop }, D ? (y && this.scrollRAF && y(this.scrollRAF), this.scrollRAF = D(function (b) { return function () { return b.scrollRAF = null, b.slider.css(a) } }(this))) : this.slider.css(a) }, j.prototype.createEvents = function () { this.events = { down: function (a) { return function (b) { return a.isBeingDragged = !0, a.offsetY = b.pageY - a.slider.offset().top, a.slider.is(b.target) || (a.offsetY = 0), a.pane.addClass("active"), a.doc.bind(n, a.events[h]).bind(o, a.events[w]), a.body.bind(m, a.events[i]), !1 } }(this), drag: function (a) { return function (b) { return a.sliderY = b.pageY - a.$el.offset().top - a.paneTop - (a.offsetY || .5 * a.sliderHeight), a.scroll(), a.contentScrollTop >= a.maxScrollTop && a.prevScrollTop !== a.maxScrollTop ? a.$el.trigger("scrollend") : 0 === a.contentScrollTop && 0 !== a.prevScrollTop && a.$el.trigger("scrolltop"), !1 } }(this), up: function (a) { return function () { return a.isBeingDragged = !1, a.pane.removeClass("active"), a.doc.unbind(n, a.events[h]).unbind(o, a.events[w]), a.body.unbind(m, a.events[i]), !1 } }(this), resize: function (a) { return function () { a.reset() } }(this), panedown: function (a) { return function (b) { return a.sliderY = (b.offsetY || b.originalEvent.layerY) - .5 * a.sliderHeight, a.scroll(), a.events.down(b), !1 } }(this), scroll: function (a) { return function (b) { a.updateScrollValues(), a.isBeingDragged || (a.iOSNativeScrolling || (a.sliderY = a.sliderTop, a.setOnScrollStyles()), null != b && (a.contentScrollTop >= a.maxScrollTop ? (a.options.preventPageScrolling && a.preventScrolling(b, g), a.prevScrollTop !== a.maxScrollTop && a.$el.trigger("scrollend")) : 0 === a.contentScrollTop && (a.options.preventPageScrolling && a.preventScrolling(b, w), 0 !== a.prevScrollTop && a.$el.trigger("scrolltop")))) } }(this), wheel: function (a) { return function (b) { var c; if (null != b) return c = b.delta || b.wheelDelta || b.originalEvent && b.originalEvent.wheelDelta || -b.detail || b.originalEvent && -b.originalEvent.detail, c && (a.sliderY += -c / 3), a.scroll(), !1 } }(this), enter: function (a) { return function (b) { var c; if (a.isBeingDragged) return 1 !== (b.buttons || b.which) ? (c = a.events)[w].apply(c, arguments) : void 0 } }(this) } }, j.prototype.addEvents = function () { var a; this.removeEvents(), a = this.events, this.options.disableResize || this.win.bind(s, a[s]), this.iOSNativeScrolling || (this.slider.bind(l, a[g]), this.pane.bind(l, a[r]).bind("" + p + " " + f, a[x])), this.$content.bind("" + t + " " + p + " " + f + " " + v, a[t]) }, j.prototype.removeEvents = function () { var a; a = this.events, this.win.unbind(s, a[s]), this.iOSNativeScrolling || (this.slider.unbind(), this.pane.unbind()), this.$content.unbind("" + t + " " + p + " " + f + " " + v, a[t]) }, j.prototype.generate = function () { var a, c, d, f, g, h, i; return f = this.options, h = f.paneClass, i = f.sliderClass, a = f.contentClass, (g = this.$el.children("." + h)).length || g.children("." + i).length || this.$el.append('<div class="' + h + '"><div class="' + i + '" /></div>'), this.pane = this.$el.children("." + h), this.slider = this.pane.find("." + i), 0 === e && C() ? (d = b.getComputedStyle(this.content, null).getPropertyValue("padding-right").replace(/[^0-9.]+/g, ""), c = { right: -14, paddingRight: +d + 14 }) : e && (c = { right: -e }, this.$el.addClass("has-scrollbar")), null != c && this.$content.css(c), this }, j.prototype.restore = function () { this.stopped = !1, this.iOSNativeScrolling || this.pane.show(), this.addEvents() }, j.prototype.reset = function () { var a, b, c, f, g, h, i, j, k, l, m, n; return this.iOSNativeScrolling ? void (this.contentHeight = this.content.scrollHeight) : (this.$el.find("." + this.options.paneClass).length || this.generate().stop(), this.stopped && this.restore(), a = this.content, f = a.style, g = f.overflowY, d && this.$content.css({ height: this.$content.height() }), b = a.scrollHeight + e, l = parseInt(this.$el.css("max-height"), 10), l > 0 && (this.$el.height(""), this.$el.height(a.scrollHeight > l ? l : a.scrollHeight)), i = this.pane.outerHeight(!1), k = parseInt(this.pane.css("top"), 10), h = parseInt(this.pane.css("bottom"), 10), j = i + k + h, n = Math.round(j / b * j), n < this.options.sliderMinHeight ? n = this.options.sliderMinHeight : null != this.options.sliderMaxHeight && n > this.options.sliderMaxHeight && (n = this.options.sliderMaxHeight), g === t && f.overflowX !== t && (n += e), this.maxSliderTop = j - n, this.contentHeight = b, this.paneHeight = i, this.paneOuterHeight = j, this.sliderHeight = n, this.paneTop = k, this.slider.height(n), this.events.scroll(), this.pane.show(), this.isActive = !0, a.scrollHeight === a.clientHeight || this.pane.outerHeight(!0) >= a.scrollHeight && g !== t ? (this.pane.hide(), this.isActive = !1) : this.el.clientHeight === a.scrollHeight && g === t ? this.slider.hide() : this.slider.show(), this.pane.css({ opacity: this.options.alwaysVisible ? 1 : "", visibility: this.options.alwaysVisible ? "visible" : "" }), c = this.$content.css("position"), ("static" === c || "relative" === c) && (m = parseInt(this.$content.css("right"), 10), m && this.$content.css({ right: "", marginRight: m })), this) }, j.prototype.scroll = function () { return this.isActive ? (this.sliderY = Math.max(0, this.sliderY), this.sliderY = Math.min(this.maxSliderTop, this.sliderY), this.$content.scrollTop(this.maxScrollTop * this.sliderY / this.maxSliderTop), this.iOSNativeScrolling || (this.updateScrollValues(), this.setOnScrollStyles()), this) : void 0 }, j.prototype.scrollBottom = function (a) { return this.isActive ? (this.$content.scrollTop(this.contentHeight - this.$content.height() - a).trigger(p), this.stop().restore(), this) : void 0 }, j.prototype.scrollTop = function (a) { return this.isActive ? (this.$content.scrollTop(+a).trigger(p), this.stop().restore(), this) : void 0 }, j.prototype.scrollTo = function (a) { return this.isActive ? (this.scrollTop(this.$el.find(a).get(0).offsetTop), this) : void 0 }, j.prototype.stop = function () { return y && this.scrollRAF && (y(this.scrollRAF), this.scrollRAF = null), this.stopped = !0, this.removeEvents(), this.iOSNativeScrolling || this.pane.hide(), this }, j.prototype.destroy = function () { return this.stopped || this.stop(), !this.iOSNativeScrolling && this.pane.length && this.pane.remove(), d && this.$content.height(""), this.$content.removeAttr("tabindex"), this.$el.hasClass("has-scrollbar") && (this.$el.removeClass("has-scrollbar"), this.$content.css({ right: "" })), this }, j.prototype.flash = function () { return !this.iOSNativeScrolling && this.isActive ? (this.reset(), this.pane.addClass("flashed"), setTimeout(function (a) { return function () { a.pane.removeClass("flashed") } }(this), this.options.flashDelay), this) : void 0 }, j }(), a.fn.nanoScroller = function (b) { return this.each(function () { var c, d; if ((d = this.nanoscroller) || (c = a.extend({}, z, b), this.nanoscroller = d = new q(this, c)), b && "object" == typeof b) { if (a.extend(d.options, b), null != b.scrollBottom) return d.scrollBottom(b.scrollBottom); if (null != b.scrollTop) return d.scrollTop(b.scrollTop); if (b.scrollTo) return d.scrollTo(b.scrollTo); if ("bottom" === b.scroll) return d.scrollBottom(0); if ("top" === b.scroll) return d.scrollTop(0); if (b.scroll && b.scroll instanceof a) return d.scrollTo(b.scroll); if (b.stop) return d.stop(); if (b.destroy) return d.destroy(); if (b.flash) return d.flash() } return d.reset() }) }, a.fn.nanoScroller.Constructor = q });



/*
 * ##################################################################################################################################################
 * EventOn Slider add-on
 * Copyright 2015 pixor.it
 */

function stripHTMLtoText(html) {
    var tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || "";
}
function ajax_getEvents(type, idslider, lan_arr, lan_arr_eosa, paged, eid, date) {
    var arr_shortcode = window[idslider + '_eo_js_array_sc'];

    jQuery.post(ajaxURL, { 'action': 'ajax_getEvents', 'arrSc': arr_shortcode, 'lan_arr': lan_arr, 'lan_arr_eosa': lan_arr_eosa, 'paged': paged, 'date_filter': date }, function (response) {
        if (type == "masonry") {
            generateItems_masonry(response, eid);
            showSlider(eid);
        }
    });
}
function getExtraFieldsHTMLajax(slider_type, style, color, arr) {
    var html = "";
    var explodeArr;

    if (((slider_type == "carousel") || (slider_type == "masonry")) && (style == "a")) {
        for (var i = 0; i < arr.length; i++) {
            html += '<div class="eo_s2_row ef' + "" + i + "" + '"><span class="eo_icon_box_2"><i class="fa ' + "" + arr[i]["icon"] + "" + '"></i></span><span class="so_title">';
            if (arr[i]["type"] == "button") {
                explodeArr = arr[i]["content"].split("|");
                html += '<a class="global_button" href="' + "" + explodeArr[1] + "" + '">' + "" + explodeArr[0] + "" + '</a></span>';
            }
            if ((arr[i]["type"] == "text") || (arr[i]["type"] == "textarea")) {
                html += arr[i]["content"] + "" + '</span>';
            }
            html += "</div>";
        }
    }

    if (((slider_type == "carousel") || (slider_type == "masonry")) && (style == "b")) {
        for (i = 0; i < arr.length; i++) {
            html += ' <div class="eo_card_row ef' + "" + i + "" + '"><i class="fa ' + "" + arr[i]["icon"] + "" + '" style="color: #' + "" + color + "" + '"></i><span class="eo_card_sotitle">';
            if (arr[i]["type"] == "button") {
                explodeArr = arr[i]["content"].split("|");
                html += '<a class="global_button" href="' + "" + explodeArr[1] + "" + '">' + "" + explodeArr[0] + "" + '</a></span>';
            }
            if ((arr[i]["type"] == "text") || (arr[i]["type"] == "textarea")) {
                html += arr[i]["content"] + "" + "</span>";
            }
            html += "</div>";
        }
    }

    if ((slider_type == "slider") && (style == "a")) {
        for (i = 0; i < arr.length; i++) {
            html += ' <div class="ef_row ef' + "" + i + "" + '"><i class="fa ' + "" + arr[i]["icon"] + "" + ' eo_i" style="color: #' + "" + color + "" + '"></i><span class="so_title">';
            if (arr[i]["type"] == "button") {
                explodeArr = arr[i]["content"].split("|");
                html += '<a class="global_button" href="' + "" + explodeArr[1] + "" + '">' + "" + explodeArr[0] + "" + '</a></span>';
            }
            if ((arr[i]["type"] == "text") || (arr[i]["type"] == "textarea")) {
                html += arr[i]["content"] + "" + "</span>";
            }
            html += "</div>";
        }
    }
    if ((slider_type == "slider") && (style == "b")) {
        for (i = 0; i < arr.length; i++) {
            html += ' <div class="s1b_mrow ef' + "" + i + "" + '"><i class="fa ' + "" + arr[i]["icon"] + "" + '" style="color: #' + "" + color + "" + '"></i>';
            if (arr[i]["type"] == "button") {
                explodeArr = arr[i]["content"].split("|");
                html += '<a class="global_button" href="' + "" + explodeArr[1] + "" + '">' + "" + explodeArr[0] + "" + '</a>';
            }
            if ((arr[i]["type"] == "text") || (arr[i]["type"] == "textarea")) {
                html += arr[i]["content"] + "" + "";
            }
            html += "</div>";
        }
    }
    if ((slider_type == "minicarousel") && (style == "a")) {
        html = '<i class="fa ' + "" + arr["icon"] + "" + '" style="color: #' + "" + color + "" + '"></i>';
        if (arr["type"] == "button") {
            explodeArr = arr[i]["content"].split("|");
            html += '<a class="global_button" href="' + "" + explodeArr[1] + "" + '">' + "" + explodeArr[0] + "" + '</a>';
        }
        if ((arr["type"] == "text") || (arr["type"] == "textarea")) {
            html += arr["content"];
        }
    }
    return html;
}
function getExtraFieldsHTML(arr, style, color) {
    var fullhtml = "";
    var classA; var classB;
    if (style == "a") {
        classA = "eoas_evcal_evdata_row";
        classB = "eoas_evo_h3";
    }
    if (style == "b") {
        classA = "global_row";
        classB = "global_label";
    }
    for (var i = 0; i < arr.length; i++) {
        var html = '<div class="' + classA + '"><div class="' + classB + '"><i class="fa ' + arr[i]['icon'] + ' eo_i" style="color: #' + color + '"></i>' + arr[i]['label'] + '</div>';
        if (arr[i]['type'] == "button") {
            html += '<a class="global_button" href="' + arr[i]['content'].split("|")[1] + '">' + arr[i]['content'].split("|")[0] + '</a>';
        }
        if ((arr[i]['type'] == "text") || (arr[i]['type'] == "textarea")) {
            html += '<div class="global_text">' + arr[i]['content'] + '</div>';
        }
        fullhtml += html + "</div>";
    }

    return fullhtml;
}
function getGridSize(id, minWidth) {
    var slider = jQuery("#" + id);
    var innw = jQuery(slider).innerWidth();
    var item_num = Math.floor(innw / minWidth);
    var res;
    if (item_num > 0) res = item_num; else res = 1;
    if (item_num > jQuery(slider).find(".slides li").length) jQuery(slider).addClass("block_slideshow");
    return res;
}


function isSlideshow(id_source) {
    var container_width = jQuery(id_source + " .flex-viewport").outerWidth();
    var singleitem_width = jQuery(id_source + " .slides li").outerWidth();
    var num_items = jQuery(id_source + " .slides li").length;
    if ((container_width + 1) > (singleitem_width * num_items)) return false;
    else return true;
}

//Hide arrow nex/prev if items < slider width
function hideNavigationArrow(id_source) {
    if (isSlideshow(id_source) == false) {
        jQuery(id_source + " .box_arrow").css('display', 'none');
        jQuery(id_source + " .eo_s1_arrow").css('display', 'none');
        jQuery(id_source + " .c1b_box_arr").css('display', 'none');
        jQuery(id_source + ".micro_padd").css("padding-left", "0px");
        jQuery(id_source + ".micro_padd").css("padding-right", "0px");

        try {
            jQuery(id_source + " .flexslider_event").flexslider("stop");
            jQuery(id_source + " .flexslider_event").flexslider("pause");
        } catch (e) { }
    }
}

//Popup Event - data filling
function fillPopEvent(index, id, main_array, direction, id_source) {
    jQuery('#' + id + '_box_card').html("");
    jQuery('#' + id + '_mebox_img').css('background-image', 'url(' + main_array[index][4] + ')');
    jQuery('#' + id + '_mebox_border').css('border-left-color', '#' + main_array[index][10]);
    jQuery('#' + id + '_mebox_day').html(main_array[index][0]);
    jQuery('#' + id + '_mebox_month').html(main_array[index][1]);
    jQuery('#' + id + '_mebox_title').html("<span>" + main_array[index][2] + "</span>");
    jQuery('#' + id + '_mebox_subtitle').html(main_array[index][3]);
    jQuery('#' + id + '_mebox_desc').html(main_array[index][7]);
    jQuery('#' + id + '_mebox_location').html(getAddressEOSA(main_array[index][5]));
    jQuery('#' + id + '_mebox_date').html(main_array[index][9]);
    jQuery('#' + id + ' .eo_s4_downbox .eo_icon_box_2').css('color', '#' + main_array[index][10]);
    jQuery('#' + id + '_box_card').attr({ "oesa_index": index });
    jQuery('#' + id + '-mini-event-box .eo_icon_box_2 .fa').css('color', '#' + main_array[index][10]);

    if (main_array[index][18] == "yes") {
        jQuery('#' + id + '_box_card').addClass("cancelled_event");
    } else {
        jQuery('#' + id + '_box_card').removeClass("cancelled_event");
    }

    posPopEvent(id_source, id, direction);
}
//Is out of screen ?
function isScrolledIntoView(id) {
    var $elem = jQuery(id);
    var $window = jQuery(window);

    var docViewTop = $window.scrollTop();
    var docViewBottom = docViewTop + $window.height();

    var elemTop = $elem.offset().top;
    var elemBottom = elemTop + $elem.height();

    return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
}

//Popup Event
function posPopEvent(id_source, id, direction) {

    var source_width = jQuery('#' + id_source).outerWidth();
    var source_height = jQuery('#' + id_source).outerHeight();
    var source_pos = jQuery('#' + id_source).position();

    jQuery('#' + id + '_box_card').html(jQuery('#' + id + '-mini-event-box').html());
    jQuery('#' + id + '_box_card').css('opacity', '0');
    jQuery('#' + id + '_box_card').css('display', 'block');

    var popup_width = jQuery('#' + id + '_box_card').outerWidth();
    var popup_height = jQuery('#' + id + '_box_card .eoas_evo_minipop_body').outerHeight();

    var marginLeft = 0;
    var sign = "-";
    if (popup_width > source_width) {
        marginLeft = (popup_width - source_width) / 2
    }
    if (popup_width < source_width) {
        marginLeft = (source_width - popup_width) / 2
        sign = '+';
    }

    if (direction == "auto") {
        jQuery('#' + id + '_box_card').css('top', (source_pos.top - popup_height - 15) + 'px');
        if (isScrolledIntoView('#' + id + '_box_card')) direction = "top";
        else direction = "down";
    }
    if (direction == "down") {
        jQuery('#' + id + '_box_card').css('top', (source_pos.top + source_height + 15) + 'px');
        jQuery('#' + id + ' .eoas_evo_minipop_arrow').css('display', 'block');
        jQuery('#' + id + ' .eoas_evo_minipop_arrow_down').css('display', 'none');
    }
    if (direction == "top") {
        jQuery('#' + id + '_box_card').css('top', (source_pos.top - popup_height - 15) + 'px');
        jQuery('#' + id + ' .eoas_evo_minipop_arrow').css('display', 'none');
        jQuery('#' + id + ' .eoas_evo_minipop_arrow_down').css('display', 'block');
    }
    jQuery('#' + id + '_box_card').css('left', source_pos.left + 'px');
    jQuery('#' + id + '_box_card').css('margin-left', sign + marginLeft + 'px');

    //animation
    jQuery('#' + id + '_box_card').animate({
        'top': '-=10px',
        'opacity': '1',
    }, 500);
}

//Cover image card
var small = true;
function animaImage(obj) {
    var _height = 120;
    var isB = jQuery(obj).hasClass("eo_boxs2_img");
    if (isB) _height = 300;
    if (small) {
        small = false;
        _height = 350;
        if (isB) {
            _height = 500;
            jQuery('.eo_boxs2_img .oflow').hide();
        }
    } else { small = true; }
    jQuery(obj).animate({ height: _height }, 500, function () {
        if (isB && small == true) jQuery('.eo_boxs2_img .oflow').show(200);
    });
}


//Popup
function posPop(id, id_source, text_msg) {
    if (isTextOverflowRow('#' + id_source + " .overflow_box")) {
        jQuery('#' + id + '_eosa_popup').css('opacity', '0');
        jQuery('#' + id + '_eosa_popup').css('display', 'inline-block');
        var source_width = jQuery('#' + id_source).outerWidth();
        var source_height = jQuery('#' + id_source).outerHeight();
        var source_pos = jQuery('#' + id_source).position();

        jQuery('#' + id + '_eosa_popup_p').html(text_msg);

        var popup_width = jQuery('#' + id + '_eosa_popup').outerWidth();
        var marginLeft = 0;
        var sign = "-";
        if (popup_width > source_width) {
            marginLeft = (popup_width - source_width) / 2
        }
        if (popup_width < source_width) {
            marginLeft = (source_width - popup_width) / 2
            sign = '+';
        }
        jQuery('#' + id + '_eosa_popup').css('left', source_pos.left + 'px');
        jQuery('#' + id + '_eosa_popup').css('top', (source_pos.top - 35) + 'px');
        jQuery('#' + id + '_eosa_popup').css('margin-left', sign + marginLeft + 'px');


        //animation
        jQuery('#' + id + '_eosa_popup').animate({
            'top': '-=10px',
            'opacity': '1',
        }, 500);
    }
}
function hidePop(id) {
    jQuery('#' + id + '_eosa_popup').stop();
    jQuery('#' + id + '_eosa_popup').css('top', '0px');
    jQuery('#' + id + '_eosa_popup').css('opacity', '0');
    jQuery('#' + id + '_eosa_popup').css('display', 'none');
}


function hideBoxOverlay(obj) {
    setTimeout(function () { jQuery(obj).css('display', 'none'); }, 300);
}

jQuery('.main_event').on('mouseenter', '.event_img_s2', function () {
    jQuery('.box_overlaybox').css('display', 'none');
    var id = '#' + jQuery(this).attr('id') + " .box_overlaybox";
    jQuery(id).css('width', jQuery(this).outerWidth() + 'px');
    jQuery(id).css('opacity', '0');
    jQuery(id).css('display', 'block');
    jQuery(id).animate({ 'opacity': '1' }, 500);
});
jQuery('.main_event').on('mouseenter', '.eo_s3_box', function () {
    jQuery('.box_overlaybox_s3').css('display', 'none');
    var id = '#' + jQuery(this).attr('id') + " .box_overlaybox_s3";
    jQuery(id).css('width', jQuery(this).outerWidth() + 'px');
    jQuery(id).css('opacity', '0');
    jQuery(id).css('display', 'block');
    jQuery(id).animate({ 'opacity': '1' }, 500);
});
jQuery('.main_event').on('mouseleave', '.box_overlaybox', function () {
    jQuery(this).css('display', 'none');
    jQuery(this).stop(true, true);
});
jQuery('.main_event').on('mouseleave', '.box_overlaybox_s3', function () {
    jQuery(this).css('display', 'none');
});


function fillEventEOSA(index, id, main_array) {
    var arr_shortcode = window[id + '_eo_js_array_sc'];
    var tmp;

    if (arr_shortcode[4] == "a") {
        jQuery('#' + id + '_eo_date').html(main_array[index][0]);
        jQuery('#' + id + '_eo_date_m').html(main_array[index][1]);
    }
    if (arr_shortcode[4] == "b") {
        jQuery('#' + id + '_eo_date').html(main_array[index][0] + ' ' + main_array[index][1]);
        jQuery('#' + id + '_eo_date_m').html(main_array[index][12]);
        jQuery('#' + id + '-eoas-full-event-box .eos_color').css('color', '#' + main_array[index][10]);
    }

    if (main_array[index][18] == "yes") {
        jQuery('#' + id + '-eoas-full-event-box').addClass("cancelled_event");
    } else {
        jQuery('#' + id + '-eoas-full-event-box').removeClass("cancelled_event");
    }

    jQuery('#' + id + '_eo_title').html(main_array[index][2]);
    tmp = main_array[index][3];
    if (tmp.length > 0) {
        jQuery('#' + id + '_eo_subtitle').css("visibility", "visible");
        jQuery('#' + id + '_eo_subtitle').html(tmp);
    } else jQuery('#' + id + '_eo_subtitle').css("visibility", "hidden");
    jQuery('#' + id + '_eo_image').css('background-image', 'url(' + main_array[index][4] + ')');
    jQuery('#' + id + '_eo_location').html(getAddressEOSA(main_array[index][5]));
    if (main_array[index][6].length > 0) {
        jQuery('#' + id + '_eo_organizer').html(main_array[index][6]);
        jQuery('#' + id + '_eo_organizer_box').css('display', 'block');
    } else jQuery('#' + id + '_eo_organizer_box, .eo_boxs2_organizer').css('display', 'none');

    if (main_array[index][16].length > 0) {
        jQuery('#' + id + '_read_more_box').html(main_array[index][16]);
    }

    var desc_id = '#' + id + '_eo_desc';
    jQuery(desc_id).html(main_array[index][13]);
    jQuery(desc_id).css('height', '56px');
    jQuery('#' + id + '_eo_time_long').html(main_array[index][9]);
    jQuery('#' + id + '_eo_main_date').css('border-left-color', '#' + main_array[index][10]);
    jQuery('#' + id + '_descButton').hide();

    if ((arr_shortcode[5] == 'all') || (arr_shortcode[5] == 'in')) jQuery('#' + id + '_eo_customfields_box').html(getExtraFieldsHTML(main_array[index][14], arr_shortcode[4], main_array[index][10]));

}

function showEventEOSA(index, id, main_array) {
    fillEventEOSA(index, id, main_array);
    var item = jQuery('#' + id + '-eoas-full-event-box');
    jQuery(item).lightbox_me({
        centered: true,
        overlayCSS: { background: 'black', opacity: .5 },
        onLoad: function () {
            jQuery('#' + id + '-eoas-full-event-box').find('input:first').focus();
            initializeEOSA(id + '-map-canvas-full-event', main_array[index][5], id);
            if (isTextOverflow('#' + id + '_eo_desc')) jQuery('#' + id + '_descButton').show();
        },
    });

    if (jQuery(window).width() < 769) {
        setTimeout(function () {
            jQuery(".eoas_evo_pop_body").css("top", jQuery(window).scrollTop() + "px");
        }, 300);
    }
}

function showEventEOSAdropdown(index, id, main_array) {
    jQuery('#' + id + '_box_dropdown').html("");
    jQuery('#' + id + '_box_dropdown').css('height', '0px');
    fillEventEOSA(index, id, main_array)
    jQuery('#' + id + '-eoas-full-event-box .dropdown_arrow').html("<div class='eosa_close_x color' onclick='hideEventEOSAdropdown(\"" + id + "\")'><i class='fa fa-chevron-up'></i></div>");
    jQuery('#' + id + '_box_dropdown').html(jQuery('#' + id + '-eoas-full-event-box').html());

    initializeEOSA(id + '-map-canvas-full-event', main_array[index][5], id);

    if (isTextOverflow('#' + id + '_eo_desc')) jQuery('#' + id + '_descButton').show();

    var iScrollHeight = jQuery('#' + id + '_box_dropdown').prop("scrollHeight");
    jQuery('#' + id + '_box_dropdown').animate({ "height": iScrollHeight }, { duration: "slow" });
}
function hideEventEOSAdropdown(id) {
    jQuery('#' + id + '_box_dropdown .eosa_close_x').hide();
    jQuery('#' + id + '_box_dropdown').animate({ "height": "0px" }, { duration: "slow" });
}
function showEventOESAinit(index, id, main_array, showtype, optionalArg, optionalArg2) {
    optionalArg = (typeof optionalArg === "undefined") ? "" : optionalArg;
    optionalArg2 = (typeof optionalArg2 === "undefined") ? "" : optionalArg2;
    if (showtype == 'lightbox') showEventEOSA(index, id, main_array);
    if (showtype == 'dropdown') showEventEOSAdropdown(index, id, main_array);
    if (showtype == 'card') fillPopEvent(index, id, main_array, optionalArg, optionalArg2);
    if ((showtype == 'link') || (showtype == 'customlink')) document.location = main_array[index][11];
    if ((showtype == 'originalL') || (showtype == 'originalD')) showOriginalEventOn(index, id, main_array, showtype);

    if (showtype == 'lightbox_l') {
        jQuery('#' + id + '_eosa_fulllist_box').trigger('close');
        showEventEOSA(index, id, main_array);
        try { jQuery('#' + id + '_box_dropdown').html(""); } catch (err) { }
    }
}


var timerVar;
var timerVar2;
var timerVar3;
var previusHeight = 0;
var eosaIframe;
var isIFrameLoad = false;
var HTMLaddon_RSVP;
function eosa_original_event_init(plugin_url) {
    if (getURLParameter("eosa_original_page") == "yes") {
        var cssLink = jQuery("<link rel='stylesheet' type='text/css' href='" + plugin_url + "/eventon-slider-addon/assets/css/style.css?ver=1.1'>");
        jQuery("head").append(cssLink);
       jQuery("html").addClass("eosa_oe_c");
    }
    jQuery("body").css("visibility", "visible");
    jQuery("body").attr({ "eosa_iload": "yes" });
}
function loadAsync() {
    var el = jQuery(".evors_popup");
    if (typeof (el) !== "undefined" && el !== null) {
        jQuery(el).html("<div id='evors_get_form'>" + HTMLaddon_RSVP + "</div>");
        clearInterval(timerVar3);
    }
}

function showOriginalEventOn(index, id, main_array, showtype) {
    var idCont = "";
    var HTMLloader = "<div class='eosa_loader_" + showtype + "'>" + jQuery('#eosa_loader_' + id).html() + "</div>";
    clearInterval(timerVar);
    clearInterval(timerVar2);
    jQuery(".eosa_iframe").css("visibility", "hidden");
    if (showtype == 'originalL') idCont = '#' + id + '-eoas-full-event-box';
    if (showtype == 'originalD') idCont = '#' + id + '_box_dropdown';
    jQuery(".eosa_iframe").remove();
    jQuery(idCont).html(HTMLloader + "<div class='skin-dark'><a class='eoas_evopopclose eosa_orig_x' onclick='jQuery(\"#" + id + "-eoas-full-event-box\").trigger(\"close\");'>X</a></div>" +
        "<iframe class='eosa_iframe' id='eosa_iframe' src='" + main_array[index][11] + "&eosa_original_page=yes' scrolling='no'></iframe><div class='dropdown_arrow'><div class='eosa_close_x color x_dd_ori' onclick='hideEventEOSAdropdown(\"" + id + "\")'><i class='fa fa-chevron-up'></i></div></div>");
    jQuery(idCont).addClass("eosa_or_b")
    if (showtype == 'originalL') {
        jQuery(idCont).lightbox_me({
            centered: true,
            overlayCSS: { background: 'black', opacity: .5 },
        });
    }
    if (showtype == 'originalD') {
        jQuery(idCont).css('height', '50px');
    }
    timerVar2 = self.setInterval("checkLoadIframe('" + id + "','" + showtype + "')", 150);
    jQuery('.eosa_iframe').load(function () {
        if (isIFrameLoad == false) eosaiframeLoad(id, showtype);
    });
}

function checkLoadIframe(id, showtype) {
    var el = document.getElementById('eosa_iframe').contentWindow.document.body;
    if (typeof (el) !== "undefined" && el !== null) {
        if (jQuery(el).attr("eosa_iload") == "yes") {
            eosaiframeLoad(id, showtype);
            isIFrameLoad = true;
            clearInterval(timerVar2);
        }
    }
}
function eosaiframeLoad(id, showtype) {
    jQuery(".eosa_iframe").css("visibility", "visible");
    eosaIframe = document.getElementById('eosa_iframe');
    timerVar = self.setInterval("iframeDynHeight()", 150);
    jQuery(eosaIframe).css("height", jQuery(eosaIframe).contents().find('.eventon_single_event')[0].scrollHeight + "px");
    jQuery('#' + id + '-eoas-full-event-box').trigger('reposition');
    if (showtype == 'originalL') {
        jQuery(".eosa_loader_" + showtype).css("display", "none");
        jQuery(".eosa_orig_x").css("display", "block");
    }
    if (showtype == 'originalD') {
        jQuery(".eosa_loader_" + showtype).css("display", "none");
        jQuery(".x_dd_ori").css("display", "block");
        var iScrollHeight = jQuery(eosaIframe).contents().find('.eventon_single_event')[0].scrollHeight;
        jQuery('#' + id + '_box_dropdown').animate({ "height": iScrollHeight }, 700, function () { jQuery('#' + id + '_box_dropdown').css("height", "auto"); });
    }
}

function iframeDynHeight() {
    var nowHeight = jQuery(eosaIframe).contents().find('.eventon_single_event')[0].scrollHeight;
    if (previusHeight != nowHeight) {
        previusHeight = nowHeight;
        jQuery(eosaIframe).css("height", nowHeight + "px");
    }
}

function showEventCard(id, showtype) {
    jQuery('#' + id + '_box_card').hide();
    showEventOESAinit(jQuery('#' + id + '_box_card').attr("oesa_index"), id, window[id + '_eo_js_array'], showtype);
}
function showMapEOSA(address, id) {
    jQuery('#' + id + '-eoas-map-bar-text').html(getAddressEOSA(address));
    jQuery('#' + id + '-map-canvas-box').lightbox_me({
        centered: true,
        overlayCSS: { background: 'black', opacity: .5 },
        onLoad: function () {
            jQuery('#' + id + '-map-canvas-box').find('input:first').focus();
            initializeEOSA(id + '-map-canvas', address, id);
        },
    });
}
function showMapCard(id) {
    jQuery('#' + id + '_box_card').hide();
    showMapEOSA(jQuery('#' + id + '-mini-event-box #' + id + '_mebox_location').html(), id);
}

var isFirstArr = new Array();
function showEventList(id, main_array) {
    var isNano = false;
    var isFirst = true;
    if ((typeof (isFirstArr[id]) !== "undefined" && isFirstArr[id] !== null)) isFirst = false;
    if (isFirst) {
        var txt_html = '';
        for (i = 0; i < main_array.length; i++) {
            txt_html = txt_html + '<div class="eosafl_item" onclick="showEventOESAinit(\'' + i + '\',\'' + id + '\',' + id + '_eo_js_array,\'lightbox_l\')"><div class="eosafl_date" style="border-left-color: #' + main_array[i][10] + '"><div>' + main_array[i][0] + '</div><span>' + main_array[i][1] + '</span></div>' +
            '<div class="eosafl_title"><div>' + main_array[i][2] + '</div><p>' + main_array[i][5] + '</p></div>' +
            '<div class="event_clear"></div>' +
            '</div>';
        }
        if (main_array.length > 10) {
            txt_html = '<a class="eoas_evopopclose close2" onclick="jQuery(\'#' + id + '_eosa_fulllist_box\').trigger(\'close\');">X</a><div class="nano-content">' + txt_html + "</div>"
            jQuery("#" + id + "_eosa_fulllist_box").addClass("nano");
            isNano = true;
        } else {
            txt_html = '<a class="eoas_evopopclose close2" onclick="jQuery(\'#' + id + '_eosa_fulllist_box\').trigger(\'close\');">X</a>' + txt_html
            jQuery("#" + id + "_eosa_fulllist_box").removeClass("nano");
        }
        jQuery("#" + id + "_eosa_fulllist_box").html(txt_html);
    }

    jQuery("#" + id + "_eosa_fulllist_box").lightbox_me({
        centered: true,
        onLoad: function () {
            if (isNano && isFirst) jQuery("#" + id + "_eosa_fulllist_box").nanoScroller();
            isFirstArr[id] = true;
        },
    });
}

//Google Map
var geocoderEOSA;
var mapEOSA;
function initializeEOSA(id_target, address_string, id) {
    var arr;
    var lng = 1;
    var lat = 1;
    var address = address_string;
    if (address_string.length > 0) {
        arr = address_string.split("|");
        if (arr.length == 3) {
            address = arr[0];
            lat = parseFloat(arr[1]);
            lng = parseFloat(arr[2]);
        }
    }

    geocoderEOSA = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(lng, lat);
    var mapOptions = {
        zoom: 8,
        center: latlng
    }
    mapEOSA = new google.maps.Map(document.getElementById(id_target), mapOptions);

    if (lat == 1 && lng == 1) {
        geocoderEOSA.geocode({ 'address': address }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                mapEOSA.setCenter(results[0].geometry.location);
                var marker = new google.maps.Marker({
                    map: mapEOSA,
                    position: results[0].geometry.location
                });
            } else { }
        });
    } else {
        var marker = new google.maps.Marker({
            position: latlng,
            map: mapEOSA
        });
    }
}


function scrollContentT(id, idButton) {
    jQuery('#' + id + '_box_dropdown').css("height", "100%");
    scrollContent(id + '_eo_desc', idButton);
}
function scrollContent(id, idButton) {
    var iScrollHeight = jQuery('#' + id).prop("scrollHeight");
    jQuery('#' + id).animate({ "height": iScrollHeight }, { duration: "slow" });
    jQuery('#' + idButton).hide();
}
function isTextOverflow(id) {
    //devo chiamarlo dopo che l'elemento � stato reso visibile
    if (jQuery(id)[0].scrollHeight > jQuery(id).innerHeight()) {
        return true;
    } else return false;
}
function isTextOverflowRow(id) {
    //devo chiamarlo dopo che l'elemento � stato reso visibile
    if (jQuery(id)[0].scrollWidth > jQuery(id).innerWidth()) {
        return true;
    } else return false;
}
function showSlider(id) {
    jQuery('#eosa_loader_' + id).css("display", "none");
    jQuery('#' + id).css("visibility", "visible");
}

//Hide box onclick out
jQuery(document).mouseup(function (e) {
    var container = jQuery(".eosa_box_card");

    if (!container.is(e.target)
        && container.has(e.target).length === 0) {
        container.hide();
    }
});
function getURLParameter(name) {
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [, ""])[1].replace(/\+/g, '%20')) || "";
}
function isEmpty(obj) { if (typeof (obj) !== "undefined" && obj !== null && (obj.length > 0 || typeof (obj) == 'number') && obj !== "undefined") return false; else return true; }

function getAddressEOSA(address) {
    var address_txt = address;
    if (address_txt.length > 0) {
        arr = address_txt.split("|");
        if (arr.length == 3) return arr[0];
    }
    return address;
}

