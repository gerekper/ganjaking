(function ($) {

    if ($('.premium-kenburns-yes').length) {

        Object.values(window.scopes_array).forEach(function ($scope) {
            premiumKenBurnsHandler($scope);
        });
    }


    function premiumKenBurnsHandler($scope) {

        var target = $scope,
            sectionId = target.data("id"),
            settings = {},
            editMode = elementorFrontend.isEditMode(),
            targetID = editMode ? target.find('#premium-kenburns-' + sectionId) : target;

        if (!editMode) {
            settings = generatePreviewSettings(targetID);
        }

        if (!settings) {
            return false;
        }

        if (!editMode) {
            generateKenBurns();
        }

        function generatePreviewSettings(target) {

            var generalSettings = target.data("kenburns");

            if (!generalSettings) {
                return false;
            }

            settings.fx = generalSettings["fx"];
            settings.speed = generalSettings["speed"];
            settings.fade = generalSettings["fade"];
            settings.slides = generalSettings["slides"];
            settings.infinite = generalSettings["infinite"];

            if (0 !== Object.keys(settings).length) {
                return settings;
            }

        }

        function generateKenBurns() {

            target.attr("data-cycle-fx", settings.fx);

            target.attr("data-cycle-timeout", settings.speed - 500);

            target.attr("data-cycle-speed", settings.fade);

            target.addClass("cycle-slideshow");


            var layout = "",
                elementType = target.data('element_type');

            $.each(settings.slides, function (index, slide) {
                if (null !== slide["premium_kenburns_images"]["url"]) {
                    layout +=
                        '<img class="premium-kenburns-img ' +
                        slide["premium_kenburns_image_fit"] +
                        " premium-kenburns-" +
                        slide["premium_kenburns_dir"] +
                        '" src="' +
                        slide["premium_kenburns_images"]["url"] +
                        '" data-zoom="' + slide["premium_kenburns_zoom_dir"] + '" >';
                }
            });

            target.prepend('<div class="premium-kenburns-overlay"/>');
            target.prepend(layout);

            var sectionIndex,
                firstSlide = target.find(".premium-kenburns-img:first-child");

            if (elementType === 'column') {
                sectionIndex = target.find(".elementor-column-wrap").css("z-index")
            }
            else {
                sectionIndex = target.find(".elementor-container").css("z-index")
            }

            if (settings.slides.length > 1 || !settings.infinite) {
                var className = "premium-kenburns-zoom-" + firstSlide.data("zoom");
                firstSlide.addClass(className);
            }


            if (settings.slides.length === 1 && settings.infinite) {
                var className = "kenburns-scale-infinite-" + firstSlide.data("zoom");
                firstSlide.addClass(className);
            }

            target
                .find(".premium-kenburns-img:first-child")
                .addClass("premium-kenburns-center");

            if (0 === sectionIndex || "auto" === sectionIndex) {
                if (elementType === 'column') {
                    target.find(".elementor-column-wrap").css("z-index", "1");
                }
                else {
                    target.find(".elementor-container").css("z-index", "1");
                }
            }

            $(".cycle-slideshow").on("cycle-before", function (
                event,
                optionHash,
                outgoingSlideEl,
                incomingSlideEl
            ) {
                var nextClass = "premium-kenburns-zoom-" + $(incomingSlideEl).data("zoom");
                $(incomingSlideEl).addClass(nextClass);
            });

            $(".cycle-slideshow").on("cycle-after", function (
                event,
                optionHash,
                outgoingSlideEl,
                incomingSlideEl
            ) {
                var prevClass = "premium-kenburns-zoom-" + $(outgoingSlideEl).data("zoom");
                $(outgoingSlideEl).removeClass(prevClass);
            });
        }
    };

    ! function (e) {
        "use strict";

        function t(e) {
            return (e || "").toLowerCase()
        }
        e.fn.cycle = function (i) {
            var n;
            return 0 !== this.length || e.isReady ? this.each(function () {
                var n, s, o, c, l = e(this),
                    r = e.fn.cycle.log;
                if (!l.data("cycle.opts")) {
                    for (var a in (!1 === l.data("cycle-log") || i && !1 === i.log || s && !1 === s.log) && (r = e.noop), r("--c2 init--"), n = l.data()) n.hasOwnProperty(a) && /^cycle[A-Z]+/.test(a) && (c = n[a], r((o = a.match(/^cycle(.*)/)[1].replace(/^[A-Z]/, t)) + ":", c, "(" + typeof c + ")"), n[o] = c);
                    (s = e.extend({}, e.fn.cycle.defaults, n, i || {})).timeoutId = 0, s.paused = s.paused || !1, s.container = l, s._maxZ = s.maxZ, s.API = e.extend({
                        _container: l
                    }, e.fn.cycle.API), s.API.log = r, s.API.trigger = function (e, t) {
                        return s.container.trigger(e, t), s.API
                    }, l.data("cycle.opts", s), l.data("cycle.API", s.API), s.API.trigger("cycle-bootstrap", [s, s.API]), s.API.addInitialSlides(), s.API.preInitSlideshow(), s.slides.length && s.API.initSlideshow()
                }
            }) : (n = {
                s: this.selector,
                c: this.context
            }, e.fn.cycle.log("requeuing slideshow (dom not ready)"), e(function () {
                e(n.s, n.c).cycle(i)
            }), this)
        }, e.fn.cycle.API = {
            opts: function () {
                return this._container.data("cycle.opts")
            },
            addInitialSlides: function () {
                var t = this.opts(),
                    i = t.slides;
                t.slideCount = 0, t.slides = e(), i = i.jquery ? i : t.container.find(i), t.random && i.sort(function () {
                    return Math.random() - .5
                }), t.API.add(i)
            },
            preInitSlideshow: function () {
                var t = this.opts();
                t.API.trigger("cycle-pre-initialize", [t]);
                var i = e.fn.cycle.transitions[t.fx];
                i && e.isFunction(i.preInit) && i.preInit(t), t._preInitialized = !0
            },
            postInitSlideshow: function () {
                var t = this.opts();
                t.API.trigger("cycle-post-initialize", [t]);
                var i = e.fn.cycle.transitions[t.fx];
                i && e.isFunction(i.postInit) && i.postInit(t)
            },
            initSlideshow: function () {
                var t, i = this.opts(),
                    n = i.container;
                i.API.calcFirstSlide(), "static" == i.container.css("position") && i.container.css("position", "relative"), e(i.slides[i.currSlide]).css({
                    opacity: 1,
                    display: "block",
                    visibility: "visible"
                }), i.API.stackSlides(i.slides[i.currSlide], i.slides[i.nextSlide], !i.reverse), i.pauseOnHover && (!0 !== i.pauseOnHover && (n = e(i.pauseOnHover)), n.hover(function () {
                    i.API.pause(!0)
                }, function () {
                    i.API.resume(!0)
                })), i.timeout && (t = i.API.getSlideOpts(i.currSlide), i.API.queueTransition(t, t.timeout + i.delay)), i._initialized = !0, i.API.updateView(!0), i.API.trigger("cycle-initialized", [i]), i.API.postInitSlideshow()
            },
            pause: function (t) {
                var i = this.opts(),
                    n = i.API.getSlideOpts(),
                    s = i.hoverPaused || i.paused;
                t ? i.hoverPaused = !0 : i.paused = !0, s || (i.container.addClass("cycle-paused"), i.API.trigger("cycle-paused", [i]).log("cycle-paused"), n.timeout && (clearTimeout(i.timeoutId), i.timeoutId = 0, i._remainingTimeout -= e.now() - i._lastQueue, (i._remainingTimeout < 0 || isNaN(i._remainingTimeout)) && (i._remainingTimeout = void 0)))
            },
            resume: function (e) {
                var t = this.opts(),
                    i = !t.hoverPaused && !t.paused;
                e ? t.hoverPaused = !1 : t.paused = !1, i || (t.container.removeClass("cycle-paused"), 0 === t.slides.filter(":animated").length && t.API.queueTransition(t.API.getSlideOpts(), t._remainingTimeout), t.API.trigger("cycle-resumed", [t, t._remainingTimeout]).log("cycle-resumed"))
            },
            add: function (t, i) {
                var n, s = this.opts(),
                    o = s.slideCount;
                "string" == e.type(t) && (t = e.trim(t)), e(t).each(function () {
                    var t, n = e(this);
                    i ? s.container.prepend(n) : s.container.append(n), s.slideCount++, t = s.API.buildSlideOpts(n), s.slides = i ? e(n).add(s.slides) : s.slides.add(n), s.API.initSlide(t, n, --s._maxZ), n.data("cycle.opts", t), s.API.trigger("cycle-slide-added", [s, t, n])
                }), s.API.updateView(!0), s._preInitialized && 2 > o && s.slideCount >= 1 && (s._initialized ? s.timeout && (n = s.slides.length, s.nextSlide = s.reverse ? n - 1 : 1, s.timeoutId || s.API.queueTransition(s)) : s.API.initSlideshow())
            },
            calcFirstSlide: function () {
                var e, t = this.opts();
                ((e = parseInt(t.startingSlide || 0, 10)) >= t.slides.length || 0 > e) && (e = 0), t.currSlide = e, t.reverse ? (t.nextSlide = e - 1, t.nextSlide < 0 && (t.nextSlide = t.slides.length - 1)) : (t.nextSlide = e + 1, t.nextSlide == t.slides.length && (t.nextSlide = 0))
            },
            calcNextSlide: function () {
                var e, t = this.opts();
                t.reverse ? (e = t.nextSlide - 1 < 0, t.nextSlide = e ? t.slideCount - 1 : t.nextSlide - 1, t.currSlide = e ? 0 : t.nextSlide + 1) : (e = t.nextSlide + 1 == t.slides.length, t.nextSlide = e ? 0 : t.nextSlide + 1, t.currSlide = e ? t.slides.length - 1 : t.nextSlide - 1)
            },
            calcTx: function (t, i) {
                var n, s = t;
                return s._tempFx ? n = e.fn.cycle.transitions[s._tempFx] : i && s.manualFx && (n = e.fn.cycle.transitions[s.manualFx]), n || (n = e.fn.cycle.transitions[s.fx]), s._tempFx = null, this.opts()._tempFx = null, n || (n = e.fn.cycle.transitions.fade, s.API.log('Transition "' + s.fx + '" not found.  Using fade.')), n
            },
            prepareTx: function (e, t) {
                var i, n, s, o, c, l = this.opts();
                return l.slideCount < 2 ? void (l.timeoutId = 0) : (!e || l.busy && !l.manualTrump || (l.API.stopTransition(), l.busy = !1, clearTimeout(l.timeoutId), l.timeoutId = 0), void (l.busy || (0 !== l.timeoutId || e) && (n = l.slides[l.currSlide], s = l.slides[l.nextSlide], o = l.API.getSlideOpts(l.nextSlide), c = l.API.calcTx(o, e), l._tx = c, e && void 0 !== o.manualSpeed && (o.speed = o.manualSpeed), l.nextSlide != l.currSlide && (e || !l.paused && !l.hoverPaused && l.timeout) ? (l.API.trigger("cycle-before", [o, n, s, t]), c.before && c.before(o, n, s, t), i = function () {
                    l.busy = !1, l.container.data("cycle.opts") && (c.after && c.after(o, n, s, t), l.API.trigger("cycle-after", [o, n, s, t]), l.API.queueTransition(o), l.API.updateView(!0))
                }, l.busy = !0, c.transition ? c.transition(o, n, s, t, i) : l.API.doTransition(o, n, s, t, i), l.API.calcNextSlide(), l.API.updateView()) : l.API.queueTransition(o))))
            },
            doTransition: function (t, i, n, s, o) {
                var c = t,
                    l = e(i),
                    r = e(n),
                    a = e(t.container).data("cycle-speed"),
                    d = function () {
                        r.animate(c.animIn || {
                            opacity: 1
                        }, a, c.easeIn || c.easing, o)
                    };
                r.css(c.cssBefore || {}), l.animate(c.animOut || {}, c.speed, c.easeOut || c.easing, function () {
                    l.css(c.cssAfter || {}), c.sync || d()
                }), c.sync && d()
            },
            queueTransition: function (t, i) {
                var n = this.opts(),
                    s = e(t.container).data("cycle-timeout"),
                    o = void 0 !== s ? s : t.timeout;
                return 0 === n.nextSlide && 0 == --n.loop ? (n.API.log("terminating; loop=0"), n.timeout = 0, o ? setTimeout(function () {
                    n.API.trigger("cycle-finished", [n])
                }, o) : n.API.trigger("cycle-finished", [n]), void (n.nextSlide = n.currSlide)) : void 0 !== n.continueAuto && (!1 === n.continueAuto || e.isFunction(n.continueAuto) && !1 === n.continueAuto()) ? (n.API.log("terminating automatic transitions"), n.timeout = 0, void (n.timeoutId && clearTimeout(n.timeoutId))) : void (o && (n._lastQueue = e.now(), void 0 === i && (n._remainingTimeout = t.timeout), n.paused || n.hoverPaused || (n.timeoutId = setTimeout(function () {
                    n.API.prepareTx(!1, !n.reverse)
                }, o))))
            },
            stopTransition: function () {
                var e = this.opts();
                e.slides.filter(":animated").length && (e.slides.stop(!1, !0), e.API.trigger("cycle-transition-stopped", [e])), e._tx && e._tx.stopTransition && e._tx.stopTransition(e)
            },
            advanceSlide: function (e) {
                var t = this.opts();
                return clearTimeout(t.timeoutId), t.timeoutId = 0, t.nextSlide = t.currSlide + e, t.nextSlide < 0 ? t.nextSlide = t.slides.length - 1 : t.nextSlide >= t.slides.length && (t.nextSlide = 0), t.API.prepareTx(!0, e >= 0), !1
            },
            buildSlideOpts: function (i) {
                var n, s, o = this.opts(),
                    c = i.data() || {};
                for (var l in c) c.hasOwnProperty(l) && /^cycle[A-Z]+/.test(l) && (n = c[l], s = l.match(/^cycle(.*)/)[1].replace(/^[A-Z]/, t), o.API.log("[" + (o.slideCount - 1) + "]", s + ":", n, "(" + typeof n + ")"), c[s] = n);
                (c = e.extend({}, e.fn.cycle.defaults, o, c)).slideNum = o.slideCount;
                try {
                    delete c.API, delete c.slideCount, delete c.currSlide, delete c.nextSlide, delete c.slides
                } catch (e) { }
                return c
            },
            getSlideOpts: function (t) {
                var i = this.opts();
                void 0 === t && (t = i.currSlide);
                var n = i.slides[t],
                    s = e(n).data("cycle.opts");
                return e.extend({}, i, s)
            },
            initSlide: function (t, i, n) {
                var s = this.opts();
                i.css(t.slideCss || {}), n > 0 && i.css("zIndex", 0), isNaN(t.speed) && (t.speed = e.fx.speeds[t.speed] || e.fx.speeds._default), t.sync || (t.speed = t.speed / 2), i.addClass(s.slideClass)
            },
            updateView: function (e, t) {
                var i = this.opts();
                if (i._initialized) {
                    var n = i.API.getSlideOpts(),
                        s = i.slides[i.currSlide];
                    !e && !0 !== t && (i.API.trigger("cycle-update-view-before", [i, n, s]), i.updateView < 0) || (i.slideActiveClass && i.slides.removeClass(i.slideActiveClass).eq(i.currSlide).addClass(i.slideActiveClass), e && i.hideNonActive && i.slides.filter(":not(." + i.slideActiveClass + ")").css("visibility", "hidden"), 0 === i.updateView && setTimeout(function () {
                        i.API.trigger("cycle-update-view", [i, n, s, e])
                    }, n.speed / (i.sync ? 2 : 1)), 0 !== i.updateView && i.API.trigger("cycle-update-view", [i, n, s, e]), e && i.API.trigger("cycle-update-view-after", [i, n, s]))
                }
            },
            getComponent: function (t) {
                var i = this.opts(),
                    n = i[t];
                return "string" == typeof n ? /^\s*[\>|\+|~]/.test(n) ? i.container.find(n) : e(n) : n.jquery ? n : e(n)
            },
            stackSlides: function (t, i, n) {
                var s = this.opts();
                t || (t = s.slides[s.currSlide], i = s.slides[s.nextSlide], n = !s.reverse), e(t).css("zIndex", 0), s.maxZ;
                var o, c = s.slideCount;
                if (n) {
                    for (o = s.currSlide + 1; c > o; o++) e(s.slides[o]).css("zIndex", 0);
                    for (o = 0; o < s.currSlide; o++) e(s.slides[o]).css("zIndex", 0)
                } else {
                    for (o = s.currSlide - 1; o >= 0; o--) e(s.slides[o]).css("zIndex", 0);
                    for (o = c - 1; o > s.currSlide; o--) e(s.slides[o]).css("zIndex", 0)
                }
                e(i).css("zIndex", 0)
            },
            getSlideIndex: function (e) {
                return this.opts().slides.index(e)
            }
        }, e.fn.cycle.log = function () {
            window.console && console.log && console.log("[cycle2] " + Array.prototype.join.call(arguments, " "))
        }, e.fn.cycle.version = function () {
            return "Cycle2: 2.1.6"
        }, e.fn.cycle.transitions = {
            custom: {},
            none: {
                before: function (e, t, i, n) {
                    e.API.stackSlides(i, t, n), e.cssBefore = {
                        opacity: 1,
                        visibility: "visible",
                        display: "block"
                    }
                }
            },
            fade: {
                before: function (t, i, n, s) {
                    var o = t.API.getSlideOpts(t.nextSlide).slideCss || {};
                    t.API.stackSlides(i, n, s), t.cssBefore = e.extend(o, {
                        opacity: 0,
                        visibility: "visible",
                        display: "block"
                    }), t.animIn = {
                        opacity: 1
                    }, t.animOut = {
                        opacity: 0
                    }
                }
            },
            fadeout: {
                before: function (t, i, n, s) {
                    var o = t.API.getSlideOpts(t.nextSlide).slideCss || {};
                    t.API.stackSlides(i, n, s), t.cssBefore = e.extend(o, {
                        opacity: 1,
                        visibility: "visible",
                        display: "block"
                    }), t.animOut = {
                        opacity: 0
                    }
                }
            },
            scrollHorz: {
                before: function (e, t, i, n) {
                    e.API.stackSlides(t, i, n);
                    var s = e.container.css("overflow", "hidden").width();
                    e.cssBefore = {
                        left: n ? s : -s,
                        top: 0,
                        opacity: 1,
                        visibility: "visible",
                        display: "block"
                    }, e.cssAfter = {
                        zIndex: 0,
                        left: 0
                    }, e.animIn = {
                        left: 0
                    }, e.animOut = {
                        left: n ? -s : s
                    }
                }
            },
            scrollVert: {
                before: function (opts, curr, next, fwd) {
                    opts.API.stackSlides(opts, curr, next, fwd);
                    var height = opts.container.css('overflow', 'hidden').height();
                    opts.cssBefore = {
                        top: fwd ? -height : height,
                        left: 0,
                        opacity: 1,
                        display: 'block',
                        visibility: 'visible'
                    };
                    opts.animIn = {
                        top: 0
                    };
                    opts.animOut = {
                        top: fwd ? height : -height
                    };
                }
            }
        }, e.fn.cycle.defaults = {
            allowWrap: !0,
            autoSelector: ".cycle-slideshow[data-cycle-auto-init!=false]",
            delay: 0,
            easing: null,
            fx: "fade",
            hideNonActive: !0,
            loop: 0,
            manualFx: void 0,
            manualSpeed: void 0,
            manualTrump: !0,
            maxZ: 100,
            pauseOnHover: !1,
            reverse: !1,
            slideActiveClass: "cycle-slide-active",
            slideClass: "cycle-slide",
            slideCss: {
                position: "absolute",
                top: 0,
                left: 0
            },
            slides: "> img.premium-kenburns-img",
            speed: 500,
            startingSlide: 0,
            sync: !0,
            timeout: 4e3,
            updateView: 0
        }, e(document).ready(function () {
            e(e.fn.cycle.defaults.autoSelector).cycle()
        })
    }(jQuery),
        function (e) {
            "use strict";

            function t(t, i) {
                var n, s, o, c, l, r = i.autoHeight;
                if ("container" == r) s = e(i.slides[i.currSlide]).outerHeight(), i.container.height(s);
                else if (i._autoHeightRatio) i.container.height(i.container.width() / i._autoHeightRatio);
                else if ("calc" === r || "number" == e.type(r) && r >= 0) {
                    if ((o = "calc" === r ? (c = 0, l = -1, i.slides.each(function (t) {
                        var i = e(this).height();
                        i > l && (l = i, c = t)
                    }), c) : r >= i.slides.length ? 0 : r) == i._sentinelIndex) return;
                    i._sentinelIndex = o, i._sentinel && i._sentinel.remove(), (n = e(i.slides[o].cloneNode(!0))).removeAttr("id name rel").find("[id],[name],[rel]").removeAttr("id name rel"), n.css({
                        position: "absolute",
                        visibility: "hidden",
                        display: "block"
                    }).prependTo(i.container).addClass("cycle-sentinel cycle-slide").removeClass("cycle-slide-active"), n.find("*").css("visibility", "hidden"), i._sentinel = n
                }
            }

            function i(t, i, n, s) {
                var o = e(s).outerHeight();
                i.container.animate({
                    height: o
                }, i.autoHeightSpeed, i.autoHeightEasing)
            }

            function n(s, o) {
                o._autoHeightOnResize && (e(window).off("resize orientationchange", o._autoHeightOnResize), o._autoHeightOnResize = null), o.container.off("cycle-slide-added cycle-slide-removed", t), o.container.off("cycle-destroyed", n), o.container.off("cycle-before", i), o._sentinel && (o._sentinel.remove(), o._sentinel = null)
            }
            e.extend(e.fn.cycle.defaults, {
                autoHeight: 0,
                autoHeightSpeed: 250,
                autoHeightEasing: null
            }), e(document).on("cycle-initialized", function (s, o) {
                function c() {
                    t(0, o)
                }
                var l, r = o.autoHeight,
                    a = e.type(r),
                    d = null;
                ("string" === a || "number" === a) && (o.container.on("cycle-slide-added cycle-slide-removed", t), o.container.on("cycle-destroyed", n), "container" == r ? o.container.on("cycle-before", i) : "string" === a && /\d+\:\d+/.test(r) && (l = (l = r.match(/(\d+)\:(\d+)/))[1] / l[2], o._autoHeightRatio = l), "number" !== a && (o._autoHeightOnResize = function () {
                    clearTimeout(d), d = setTimeout(c, 50)
                }, e(window).on("resize orientationchange", o._autoHeightOnResize)), setTimeout(c, 30))
            })
        }(jQuery),
        function (e) {
            "use strict";
            e.extend(e.fn.cycle.defaults, {
                caption: "> .cycle-caption",
                captionTemplate: "{{slideNum}} / {{slideCount}}",
                overlay: "> .cycle-overlay",
                overlayTemplate: "<div>{{title}}</div><div>{{desc}}</div>",
                captionModule: "caption"
            }), e(document).on("cycle-update-view", function (t, i, n, s) {
                "caption" === i.captionModule && e.each(["caption", "overlay"], function () {
                    var e = n[this + "Template"],
                        t = i.API.getComponent(this);
                    t.length && e ? (t.html(i.API.tmpl(e, n, i, s)), t.show()) : t.hide()
                })
            }), e(document).on("cycle-destroyed", function (t, i) {
                e.each(["caption", "overlay"], function () {
                    var e = i[this + "Template"];
                    i[this] && e && i.API.getComponent("caption").empty()
                })
            })
        }(jQuery),
        function (e) {
            "use strict";
            var t = e.fn.cycle;
            e.fn.cycle = function (i) {
                var n, s, o, c = e.makeArray(arguments);
                return "number" == e.type(i) ? this.cycle("goto", i) : "string" == e.type(i) ? this.each(function () {
                    var l;
                    return n = i, void 0 === (o = e(this).data("cycle.opts")) ? void t.log('slideshow must be initialized before sending commands; "' + n + '" ignored') : (n = "goto" == n ? "jump" : n, s = o.API[n], e.isFunction(s) ? ((l = e.makeArray(c)).shift(), s.apply(o.API, l)) : void t.log("unknown command: ", n))
                }) : t.apply(this, arguments)
            }, e.extend(e.fn.cycle, t), e.extend(t.API, {
                next: function () {
                    var e = this.opts();
                    if (!e.busy || e.manualTrump) {
                        var t = e.reverse ? -1 : 1;
                        !1 === e.allowWrap && e.currSlide + t >= e.slideCount || (e.API.advanceSlide(t), e.API.trigger("cycle-next", [e]).log("cycle-next"))
                    }
                },
                prev: function () {
                    var e = this.opts();
                    if (!e.busy || e.manualTrump) {
                        var t = e.reverse ? 1 : -1;
                        !1 === e.allowWrap && e.currSlide + t < 0 || (e.API.advanceSlide(t), e.API.trigger("cycle-prev", [e]).log("cycle-prev"))
                    }
                },
                destroy: function () {
                    this.stop();
                    var t = this.opts(),
                        i = e.isFunction(e._data) ? e._data : e.noop;
                    clearTimeout(t.timeoutId), t.timeoutId = 0, t.API.stop(), t.API.trigger("cycle-destroyed", [t]).log("cycle-destroyed"), t.container.removeData(), i(t.container[0], "parsedAttrs", !1), t.retainStylesOnDestroy || (t.container.removeAttr("style"), t.slides.removeAttr("style"), t.slides.removeClass(t.slideActiveClass)), t.slides.each(function () {
                        var n = e(this);
                        n.removeData(), n.removeClass(t.slideClass), i(this, "parsedAttrs", !1)
                    })
                },
                jump: function (e, t) {
                    var i, n = this.opts();
                    if (!n.busy || n.manualTrump) {
                        var s = parseInt(e, 10);
                        if (isNaN(s) || 0 > s || s >= n.slides.length) return void n.API.log("goto: invalid slide index: " + s);
                        if (s == n.currSlide) return void n.API.log("goto: skipping, already on slide", s);
                        n.nextSlide = s, clearTimeout(n.timeoutId), n.timeoutId = 0, n.API.log("goto: ", s, " (zero-index)"), i = n.currSlide < n.nextSlide, n._tempFx = t, n.API.prepareTx(!0, i)
                    }
                },
                stop: function () {
                    var t = this.opts(),
                        i = t.container;
                    clearTimeout(t.timeoutId), t.timeoutId = 0, t.API.stopTransition(), t.pauseOnHover && (!0 !== t.pauseOnHover && (i = e(t.pauseOnHover)), i.off("mouseenter mouseleave")), t.API.trigger("cycle-stopped", [t]).log("cycle-stopped")
                },
                reinit: function () {
                    var e = this.opts();
                    e.API.destroy(), e.container.cycle()
                },
                remove: function (t) {
                    for (var i, n, s = this.opts(), o = [], c = 1, l = 0; l < s.slides.length; l++) i = s.slides[l], l == t ? n = i : (o.push(i), e(i).data("cycle.opts").slideNum = c, c++);
                    n && (s.slides = e(o), s.slideCount--, e(n).remove(), t == s.currSlide ? s.API.advanceSlide(1) : t < s.currSlide ? s.currSlide-- : s.currSlide++, s.API.trigger("cycle-slide-removed", [s, t, n]).log("cycle-slide-removed"), s.API.updateView())
                }
            }), e(document).on("click.cycle", "[data-cycle-cmd]", function (t) {
                t.preventDefault();
                var i = e(this),
                    n = i.data("cycle-cmd"),
                    s = i.data("cycle-context") || ".cycle-slideshow";
                e(s).cycle(n, i.data("cycle-arg"))
            })
        }(jQuery),
        function (e) {
            "use strict";

            function t(t, i) {
                var n;
                return t._hashFence ? void (t._hashFence = !1) : (n = window.location.hash.substring(1), void t.slides.each(function (s) {
                    if (e(this).data("cycle-hash") == n) {
                        if (!0 === i) t.startingSlide = s;
                        else {
                            var o = t.currSlide < s;
                            t.nextSlide = s, t.API.prepareTx(!0, o)
                        }
                        return !1
                    }
                }))
            }
            e(document).on("cycle-pre-initialize", function (i, n) {
                t(n, !0), n._onHashChange = function () {
                    t(n, !1)
                }, e(window).on("hashchange", n._onHashChange)
            }), e(document).on("cycle-update-view", function (e, t, i) {
                i.hash && "#" + i.hash != window.location.hash && (t._hashFence = !0, window.location.hash = i.hash)
            }), e(document).on("cycle-destroyed", function (t, i) {
                i._onHashChange && e(window).off("hashchange", i._onHashChange)
            })
        }(jQuery),
        function (e) {
            "use strict";
            e.extend(e.fn.cycle.defaults, {
                loader: !1
            }), e(document).on("cycle-bootstrap", function (t, i) {
                var n;
                i.loader && (n = i.API.add, i.API.add = function (t, s) {
                    function o(e, t) {
                        return e.data("index") - t.data("index")
                    }
                    var c = [];
                    if ("string" == e.type(t)) t = e.trim(t);
                    else if ("array" === e.type(t))
                        for (var l = 0; l < t.length; l++) t[l] = e(t[l])[0];
                    var r = (t = e(t)).length;
                    r && (t.css("visibility", "hidden").appendTo("body").each(function (t) {
                        function l() {
                            var t, l;
                            0 == --a && (--r, t = d, "wait" == i.loader ? (c.push(t), 0 === r && (c.sort(o), n.apply(i.API, [c, s]), i.container.removeClass("cycle-loading"))) : (l = e(i.slides[i.currSlide]), n.apply(i.API, [t, s]), l.show(), i.container.removeClass("cycle-loading")))
                        }
                        var a = 0,
                            d = e(this),
                            u = d.is("div") ? d : d.find(".premium-kenburns-img");
                        return d.data("index", t), (u = u.filter(":not(.cycle-loader-ignore)").filter(':not([src=""])')).length ? (a = u.length, void u.each(function () {
                            this.complete ? l() : e(this).load(function () {
                                l()
                            }).on("error", function () {
                                0 == --a && (i.API.log("slide skipped; img not loaded:", this.src), 0 == --r && "wait" == i.loader && n.apply(i.API, [c, s]))
                            })
                        })) : (--r, void c.push(d))
                    }), r && i.container.addClass("cycle-loading"))
                })
            })
        }(jQuery),
        function (e) {
            "use strict";

            function t(t, i, n) {
                var s;
                t.API.getComponent("pager").each(function () {
                    var o = e(this);
                    if (i.pagerTemplate) {
                        var c = t.API.tmpl(i.pagerTemplate, i, t, n[0]);
                        s = e(c).appendTo(o)
                    } else s = o.children().eq(t.slideCount - 1);
                    s.on(t.pagerEvent, function (e) {
                        t.pagerEventBubble || e.preventDefault(), t.API.page(o, e.currentTarget)
                    })
                })
            }

            function i(e, t) {
                var i = this.opts();
                if (!i.busy || i.manualTrump) {
                    var n = e.children().index(t),
                        s = i.currSlide < n;
                    i.currSlide != n && (i.nextSlide = n, i._tempFx = i.pagerFx, i.API.prepareTx(!0, s), i.API.trigger("cycle-pager-activated", [i, e, t]))
                }
            }
            e.extend(e.fn.cycle.defaults, {
                pager: "> .cycle-pager",
                pagerActiveClass: "cycle-pager-active",
                pagerEvent: "click.cycle",
                pagerEventBubble: void 0,
                pagerTemplate: "<span>&bull;</span>"
            }), e(document).on("cycle-bootstrap", function (e, i, n) {
                n.buildPagerLink = t
            }), e(document).on("cycle-slide-added", function (e, t, n, s) {
                t.pager && (t.API.buildPagerLink(t, n, s), t.API.page = i)
            }), e(document).on("cycle-slide-removed", function (t, i, n) {
                i.pager && i.API.getComponent("pager").each(function () {
                    var t = e(this);
                    e(t.children()[n]).remove()
                })
            }), e(document).on("cycle-update-view", function (t, i) {
                i.pager && i.API.getComponent("pager").each(function () {
                    e(this).children().removeClass(i.pagerActiveClass).eq(i.currSlide).addClass(i.pagerActiveClass)
                })
            }), e(document).on("cycle-destroyed", function (e, t) {
                var i = t.API.getComponent("pager");
                i && (i.children().off(t.pagerEvent), t.pagerTemplate && i.empty())
            })
        }(jQuery),
        function (e) {
            "use strict";
            e.extend(e.fn.cycle.defaults, {
                next: "> .cycle-next",
                nextEvent: "click.cycle",
                disabledClass: "disabled",
                prev: "> .cycle-prev",
                prevEvent: "click.cycle",
                swipe: !1
            }), e(document).on("cycle-initialized", function (e, t) {
                if (t.API.getComponent("next").on(t.nextEvent, function (e) {
                    e.preventDefault(), t.API.next()
                }), t.API.getComponent("prev").on(t.prevEvent, function (e) {
                    e.preventDefault(), t.API.prev()
                }), t.swipe) {
                    var i = t.swipeVert ? "swipeUp.cycle" : "swipeLeft.cycle swipeleft.cycle",
                        n = t.swipeVert ? "swipeDown.cycle" : "swipeRight.cycle swiperight.cycle";
                    t.container.on(i, function () {
                        t._tempFx = t.swipeFx, t.API.next()
                    }), t.container.on(n, function () {
                        t._tempFx = t.swipeFx, t.API.prev()
                    })
                }
            }), e(document).on("cycle-update-view", function (e, t) {
                if (!t.allowWrap) {
                    var i = t.disabledClass,
                        n = t.API.getComponent("next"),
                        s = t.API.getComponent("prev"),
                        o = t._prevBoundry || 0,
                        c = void 0 !== t._nextBoundry ? t._nextBoundry : t.slideCount - 1;
                    t.currSlide == c ? n.addClass(i).prop("disabled", !0) : n.removeClass(i).prop("disabled", !1), t.currSlide === o ? s.addClass(i).prop("disabled", !0) : s.removeClass(i).prop("disabled", !1)
                }
            }), e(document).on("cycle-destroyed", function (e, t) {
                t.API.getComponent("prev").off(t.nextEvent), t.API.getComponent("next").off(t.prevEvent), t.container.off("swipeleft.cycle swiperight.cycle swipeLeft.cycle swipeRight.cycle swipeUp.cycle swipeDown.cycle")
            })
        }(jQuery),
        function (e) {
            "use strict";
            e.extend(e.fn.cycle.defaults, {
                progressive: !1
            }), e(document).on("cycle-pre-initialize", function (t, i) {
                if (i.progressive) {
                    var n, s, o = i.API,
                        c = o.next,
                        l = o.prev,
                        r = o.prepareTx,
                        a = e.type(i.progressive);
                    if ("array" == a) n = i.progressive;
                    else if (e.isFunction(i.progressive)) n = i.progressive(i);
                    else if ("string" == a) {
                        if (s = e(i.progressive), !(n = e.trim(s.html()))) return;
                        if (/^(\[)/.test(n)) try {
                            n = e.parseJSON(n)
                        } catch (e) {
                            return void o.log("error parsing progressive slides", e)
                        } else (n = n.split(new RegExp(s.data("cycle-split") || "\n")))[n.length - 1] || n.pop()
                    }
                    r && (o.prepareTx = function (e, t) {
                        var s, o;
                        return e || 0 === n.length ? void r.apply(i.API, [e, t]) : void (t && i.currSlide == i.slideCount - 1 ? (o = n[0], n = n.slice(1), i.container.one("cycle-slide-added", function (e, t) {
                            setTimeout(function () {
                                t.API.advanceSlide(1)
                            }, 50)
                        }), i.API.add(o)) : t || 0 !== i.currSlide ? r.apply(i.API, [e, t]) : (s = n.length - 1, o = n[s], n = n.slice(0, s), i.container.one("cycle-slide-added", function (e, t) {
                            setTimeout(function () {
                                t.currSlide = 1, t.API.advanceSlide(-1)
                            }, 50)
                        }), i.API.add(o, !0)))
                    }), c && (o.next = function () {
                        var e = this.opts();
                        if (n.length && e.currSlide == e.slideCount - 1) {
                            var t = n[0];
                            n = n.slice(1), e.container.one("cycle-slide-added", function (e, t) {
                                c.apply(t.API), t.container.removeClass("cycle-loading")
                            }), e.container.addClass("cycle-loading"), e.API.add(t)
                        } else c.apply(e.API)
                    }), l && (o.prev = function () {
                        var e = this.opts();
                        if (n.length && 0 === e.currSlide) {
                            var t = n.length - 1,
                                i = n[t];
                            n = n.slice(0, t), e.container.one("cycle-slide-added", function (e, t) {
                                t.currSlide = 1, t.API.advanceSlide(-1), t.container.removeClass("cycle-loading")
                            }), e.container.addClass("cycle-loading"), e.API.add(i, !0)
                        } else l.apply(e.API)
                    })
                }
            })
        }(jQuery),
        function (e) {
            "use strict";
            e.extend(e.fn.cycle.defaults, {
                tmplRegex: "{{((.)?.*?)}}"
            }), e.extend(e.fn.cycle.API, {
                tmpl: function (t, i) {
                    var n = new RegExp(i.tmplRegex || e.fn.cycle.defaults.tmplRegex, "g"),
                        s = e.makeArray(arguments);
                    return s.shift(), t.replace(n, function (t, i) {
                        var n, o, c, l, r = i.split(".");
                        for (n = 0; n < s.length; n++)
                            if (c = s[n]) {
                                if (r.length > 1)
                                    for (l = c, o = 0; o < r.length; o++) c = l, l = l[r[o]] || i;
                                else l = c[i];
                                if (e.isFunction(l)) return l.apply(c, s);
                                if (null != l && l != i) return l
                            } return i
                    })
                }
            })
        }(jQuery);
})(jQuery);