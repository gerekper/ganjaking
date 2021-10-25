! function (t, e, i, o, n) {
    t.fn.multiscroll = function (s) {
        var l = t.fn.multiscroll;
        s = t.extend({
            verticalCentered: !0,
            scrollingSpeed: 700,
            easing: "easeInOutCubic",
            menu: !1,
            menuPos: "left",
            sectionsColor: [],
            anchors: [],
            navigation: !1,
            navigationPosition: "right",
            navigationVPosition: "top",
            navigationColor: "#000",
            navigationTooltips: [],
            loopBottom: !1,
            loopTop: !1,
            css3: !1,
            paddingTop: 0,
            paddingBottom: 0,
            fixedElements: null,
            normalScrollElements: null,
            keyboardScrolling: !0,
            touchSensitivity: 5,
            sectionSelector: ".ms-section",
            leftSelector: ".ms-left",
            rightSelector: ".ms-right",
            cellHeight: 400,
            fit: null,
            afterLoad: null,
            onLeave: null,
            afterRender: null,
            afterResize: null,
            id: null,
            leftWidth: 50,
            rightWidth: 50
        }, s);

        t.extend(t.easing, {
            easeInOutCubic: function (x, t, b, c, d) {
                if ((t /= d / 2) < 1) return c / 2 * t * t * t + b;
                return c / 2 * ((t -= 2) * t * t + 2) + b;
            }
        });

        var a = 600,
            r = 0,
            m = navigator.userAgent.match(/(iPhone|iPod|iPad|Android|playbook|silk|BlackBerry|BB10|Windows Phone|Tizen|Bada|webOS|IEMobile|Opera Mini)/),
            c = "ontouchstart" in e || 0 < navigator.msMaxTouchPoints || navigator.maxTouchPoints;
        ".ms-right" !== s.rightSelector && t(s.rightSelector).addClass("ms-right"), ".ms-left" !== s.leftSelector && t(s.leftSelector).addClass("ms-left");
        var u, d, p, f = t("#premium-multiscroll-" + s.id + " .ms-left").find(".ms-section").length,
            v = !1,
            h = t(e).height(),
            g = e.PointerEvent ? {
                down: "pointerdown",
                move: "pointermove"
            } : {
                    down: "MSPointerDown",
                    move: "MSPointerMove"
                },
            x = {
                touchmove: "ontouchmove" in e ? "touchmove" : g.move,
                touchstart: "ontouchstart" in e ? "touchstart" : g.down
            };

        function b(i, o, n) {
            var s = i.get(0),
                l = t(e).outerHeight(),
                a = !0 !== n || s.offsetWidth * s.offsetHeight;
            if ("function" == typeof s.getBoundingClientRect) {
                var r = s.getBoundingClientRect(),
                    m = r.top >= 0 && r.top < l,
                    c = r.bottom > 0 && r.bottom <= l,
                    u = o ? m || c : m && c;
                return u = r.top < 0 && r.bottom > l || u, a && u
            }
            var d = 0 + l,
                p = t(e).position().top,
                f = p + t(e).height();
            return !!a && (!0 === o ? p : f) <= d && (!0 === o ? f : p) >= 0
        }

        function y() {
            var i = e.location.hash.replace("#", "");
            if (i.length) {
                var o = t("#premium-multiscroll-" + s.id + " .ms-left").find('[data-anchor="' + i + '"]'),
                    n = b(t("#premium-multiscroll-" + s.id), !1, !1) || "fit" === s.fit;
                ("undefined" == typeof lastScrolledDestiny || i !== lastScrolledDestiny) && n && B(o)
            }
        }

        function w(e) {
            e.preventDefault();
            var i = t(this).parent().index();
            B(t("#premium-multiscroll-" + s.id + " .ms-left .ms-section").eq(i))
        }

        function C() {
            var e = t(this).data("tooltip");
            t('<div class="multiscroll-tooltip ' + s.navigationPosition + " " + s.navigationVPosition + '">' + e + "</div>").hide().appendTo(t(this)).fadeIn(200)
        }

        function S() {
            t(this).find(".multiscroll-tooltip").fadeOut(200, function () {
                t(this).remove()
            })
        }
        P(), A(), s.css3 && (s.css3 = function () {
            var t, o = i.createElement("p"),
                s = {
                    webkitTransform: "-webkit-transform",
                    OTransform: "-o-transform",
                    msTransform: "-ms-transform",
                    MozTransform: "-moz-transform",
                    transform: "transform"
                };
            for (var l in i.body.insertBefore(o, null), s) o.style[l] !== n && (o.style[l] = "translate3d(1px,1px,1px)", t = e.getComputedStyle(o).getPropertyValue(s[l]));
            return i.body.removeChild(o), t !== n && 0 < t.length && "none" !== t
        }()), t("html, body").css({
            height: "100%"
        }), ".ms-section" !== s.sectionSelector && t(s.sectionSelector).each(function () {
            t(this).addClass("ms-section")
        }), s.navigation && (t('<div id="multiscroll-nav-' + s.id + '" class="multiscroll-nav"><ul class="premium-multiscroll-dot-list"></ul></div>').prependTo("#premium-multiscroll-" + s.id), (u = t("#multiscroll-nav-" + s.id)).css("color", s.navigationColor), u.addClass(s.navigationPosition).addClass(s.navigationVPosition)), t("#premium-multiscroll-" + s.id + " .ms-left").css({
            width: s.leftWidth + "%"
        }), t("#premium-multiscroll-" + s.id + " .ms-right").css({
            width: "calc(" + s.rightWidth + "% + 1px )"
        }), t(".ms-right, .ms-left").css({
            position: "absolute",
            height: "100%",
            "-ms-touch-action": "none"
        }), t(".ms-right").css({
            right: "0",
            top: "0",
            "-ms-touch-action": "none",
            "touch-action": "none"
        }), t(".ms-left").css({
            left: "0",
            top: "0",
            "-ms-touch-action": "none",
            "touch-action": "none"
        }), t("#premium-multiscroll-" + s.id + " .ms-left .ms-section, #premium-multiscroll-" + s.id + " .ms-right .ms-section").each(function () {
            var e = t(this).index();
            if ((s.paddingTop || s.paddingBottom) && t(this).css("padding", s.paddingTop + " 0 " + s.paddingBottom + " 0"), void 0 !== s.sectionsColor[e] && t(this).css("background-color", s.sectionsColor[e]), void 0 !== s.anchors[e] && t(this).attr("data-anchor", s.anchors[e]), s.verticalCentered && t(this).addClass("ms-table").wrapInner('<div class="ms-tableCell" style="height: ' + s.cellHeight + 'px" />'), t(this).closest(".ms-left").length && s.navigation) {
                var i = "";
                s.anchors.length && (i = s.anchors[e]);
                var o = s.navigationTooltips[e];
                void 0 === o && (o = ""), s.navigation && u.find("ul").append('<li data-tooltip="' + o + '"><a href="#' + i + '"><span></span></a></li>')
            }
        }), t("#premium-multiscroll-" + s.id + " .ms-right").html(t("#premium-multiscroll-" + s.id + " .ms-right").find(".ms-section").get().reverse()), t("#premium-multiscroll-" + s.id + " .ms-left .ms-section, #premium-multiscroll-" + s.id + " .ms-right .ms-section").each(function () {
            var e = t(this).index();
            t(this).css({
                height: "100%"
            }), !e && s.navigation && u.find("li").eq(e).find("a").addClass("active"), !e && s.anchors.length && t(s.menu).find("li").eq(e).addClass("active")
        }).promise().done(function () {
            t("#premium-multiscroll-" + s.id + " .ms-left .ms-section.active").length || (t("#premium-multiscroll-" + s.id + " .ms-right").find(".ms-section").last().addClass("active"), t("#premium-multiscroll-" + s.id + " .ms-left").find(".ms-section").first().addClass("active")), s.navigation && u.css("margin-top", "-" + u.height() / 2 + "px"), t.isFunction(s.afterRender) && s.afterRender.call(this), k(), F(), t(e).on("load", function () { })
        }), t(i).keydown(function (e) {
            var o = t("#premium-multiscroll-" + s.id + " .ms-left .ms-section.active").next(".ms-section"),
                n = t("#premium-multiscroll-" + s.id + " .ms-left .ms-section.active").prev(".ms-section");
            clearTimeout(p);
            var l = t(i.activeElement);
            if (b(t("#premium-multiscroll-" + s.id), !1, !1) && !l.is("textarea") && !l.is("input") && !l.is("select") && s.keyboardScrolling) {
                var a = e.which;
                t.inArray(a, [40, 38, 32, 33, 34]) > -1 && (38 === a || 33 === a ? (n.length > 0 || s.loopTop) && e.preventDefault() : 40 !== a && 34 !== a || (o.length > 0 || s.loopBottom) && e.preventDefault()), p = setTimeout(function () {
                    ! function (e) {
                        var i = e.shiftKey;
                        switch (e.which) {
                            case 38:
                            case 33:
                                !v && Y();
                                break;
                            case 32:
                                if (i) {
                                    Y();
                                    break
                                }
                            case 40:
                            case 34:
                                !v && I();
                                break;
                            case 36:
                                B(isNaN(1) ? t("#premium-multiscroll-" + s.id + ' .ms-left [data-anchor="1"]') : t("#premium-multiscroll-" + s.id + " .ms-left .ms-section").eq(0))
                        }
                    }(e)
                }, 150)
            }
        }), t(e).on("hashchange", y), t(i).mousedown(function (t) {
            if (1 == t.button) return t.preventDefault(), !1
        }), t(i).on("click", "#premium-scroll-nav-menu-" + s.id + " a", w), t(i).on("click", "#multiscroll-nav-" + s.id + " a", w), t(i).on({
            mouseenter: C,
            mouseleave: S
        }, "#multiscroll-nav-" + s.id + " li"), s.normalScrollElements && (t(i).on("mouseenter", s.normalScrollElements, function () {
            q(!1)
        }), t(i).on("mouseleave", s.normalScrollElements, function () {
            q(!0)
        })), t(e).on("resize", E);
        var T = h;

        function E() {
            if (m) {
                var n = t(i.activeElement);
                if (!n.is("textarea") && !n.is("input") && !n.is("select")) {
                    var s = t(e).height();
                    o.abs(s - T) > 20 * o.max(T, s) / 100 && (L(!0), T = s)
                }
            } else clearTimeout(d), d = setTimeout(function () {
                L(!0)
            }, 350)
        }

        function L(i) {
            h = t(e).height(), t(".ms-tableCell").each(function () {
                t(this).css({
                    height: function (t) {
                        var e = h;
                        if (s.paddingTop || s.paddingBottom) {
                            var i = parseInt(t.css("padding-top")) + parseInt(t.css("padding-bottom"));
                            e = h - i
                        }
                        return e
                    }(t(this).parent())
                })
            }), s.scrollOverflow && scrollBarHandler.createScrollBarForAll(), k(), t.isFunction(s.afterResize) && s.afterResize.call(this)
        }

        function k() {
            s.css3 ? (R(t("#premium-multiscroll-" + s.id + " .ms-left"), "translate3d(0px, -" + t("#premium-multiscroll-" + s.id + " .ms-left").find(".ms-section.active").position().top + "px, 0px)", !1), R(t("#premium-multiscroll-" + s.id + " .ms-right"), "translate3d(0px, -" + t("#premium-multiscroll-" + s.id + " .ms-right").find(".ms-section.active").position().top + "px, 0px)", !1)) : (t("#premium-multiscroll-" + s.id + " .ms-left").css("top", -t("#premium-multiscroll-" + s.id + " .ms-left").find(".ms-section.active").position().top), t("#premium-multiscroll-" + s.id + " .ms-right").css("top", -t("#premium-multiscroll-" + s.id + " .ms-right").find(".ms-section.active").position().top))
        }

        function B(e) {
            var i, o, n = e.index(),
                l = t("#premium-multiscroll-" + s.id + " .ms-right").find(".ms-section").eq(f - 1 - n),
                r = e.data("anchor"),
                m = t("#premium-multiscroll-" + s.id + " .ms-left .ms-section.active").index() + 1,
                c = (i = e, o = t("#premium-multiscroll-" + s.id + " .ms-left .ms-section.active").index(), i.index() < o ? "up" : "down");
            v = !0;
            var u, d, p, h = e.position().top,
                g = l.position().top;
            if (l.addClass("active").siblings().removeClass("active"), e.addClass("active").siblings().removeClass("active"), s.anchors.length, F(), s.css3) {
                t.isFunction(s.onLeave) && s.onLeave.call(this, m, n + 1, c);
                var x = "translate3d(0px, -" + h + "px, 0px)",
                    b = "translate3d(0px, -" + g + "px, 0px)";
                R(t("#premium-multiscroll-" + s.id + " .ms-left"), x, !0), R(t("#premium-multiscroll-" + s.id + " .ms-right"), b, !0), setTimeout(function () {
                    t.isFunction(s.afterLoad) && s.afterLoad.call(this, r, n + 1), setTimeout(function () {
                        v = !1
                    }, a)
                }, s.scrollingSpeed)
            } else t.isFunction(s.onLeave) && s.onLeave.call(this, m, n + 1, c), t("#premium-multiscroll-" + s.id + " .ms-left").animate({
                top: -h
            }, s.scrollingSpeed, s.easing, function () {
                t.isFunction(s.afterLoad) && s.afterLoad.call(this, r, n + 1), setTimeout(function () {
                    v = !1
                }, a)
            }), t("#premium-multiscroll-" + s.id + " .ms-right").animate({
                top: -g
            }, s.scrollingSpeed, s.easing);
            lastScrolledDestiny = r, u = r, s.menu && (t(s.menu).find(".active").removeClass("active"), t(s.menu).find('[data-menuanchor="' + u + '"]').addClass("active")), d = r, p = n, s.navigation && (t("#multiscroll-nav-" + s.id).find(".active").removeClass("active"), d ? t("#multiscroll-nav-" + s.id).find('a[href="#' + d + '"]').addClass("active") : t("#multiscroll-nav-" + s.id).find("li").eq(p).find("a").addClass("active"))
        }

        function P() {
            var t = i.getElementById("premium-multiscroll-" + s.id);
            i.addEventListener ? t.addEventListener("wheel", D, !1) : t.attachEvent("onmousewheel", D)
        }

        function D(i) {
            var n, l = t("#premium-multiscroll-" + s.id + " .ms-left .ms-section.active").next(".ms-section"),
                a = t("#premium-multiscroll-" + s.id + " .ms-left .ms-section.active").prev(".ms-section"),
                m = t("#premium-multiscroll-" + s.id).offset().top,
                c = (t("#premium-multiscroll-" + s.id).outerHeight(), n = i, n = e.event || n, o.max(-1, o.min(1, n.wheelDelta || -n.deltaY || -n.detail)));
            return ("fit" === s.fit || b(t("#premium-multiscroll-" + s.id), !1, !1)) && (v && r > 0 && M(i), null !== i.target.closest("#premium-multiscroll-" + s.id) && (c < 0 ? (l.length > 0 || s.loopBottom) && (M(i), v || (r++, "fit" === s.fit && t("html, body").stop().clearQueue().animate({
                scrollTop: m
            }, 700), I())) : c > 0 && (a.length > 0 || s.loopTop) && (M(i), v || ("fit" === s.fit && t("html, body").stop().clearQueue().animate({
                scrollTop: m
            }, 700), Y())))), !1
        }

        function z(t) {
            (t = t || o.event).preventDefault && t.preventDefault(), t.returnValue = !1
        }

        function M(t) {
            o.addEventListener && o.addEventListener("DOMMouseScroll", z(t), !1), o.onwheel = z(t), o.onmousewheel = i.onmousewheel = z(t), o.ontouchmove = z(t)
        }

        function R(t, e, i) {
            var o;
            t.toggleClass("premium-scroll-easing", i), t.css({
                "-webkit-transform": o = e,
                "-moz-transform": o,
                "-ms-transform": o,
                transform: o
            })
        }

        function F() {
            var e = t("#premium-multiscroll-" + s.id + " .ms-left .ms-section.active"),
                i = e.data("anchor"),
                o = e.index(),
                n = String(o);
            s.anchors.length && (n = i), n = n.replace("/", "-").replace("#", "");
            var l = new RegExp("\\b\\s?ms-viewing-[^\\s]+\\b", "g");
            t("body")[0].className = t("body")[0].className.replace(l, ""), t("body").addClass("ms-viewing-" + n)
        }

        function I() {
            var e = t("#premium-multiscroll-" + s.id + " .ms-left .ms-section.active").next(".ms-section");
            !e.length && s.loopBottom && (e = t("#premium-multiscroll-" + s.id + " .ms-left .ms-section").first()), e.length && B(e)
        }

        function Y() {
            var e = t("#premium-multiscroll-" + s.id + " .ms-left .ms-section.active").prev(".ms-section");
            !e.length && s.loopTop && (e = t("#premium-multiscroll-" + s.id + " .ms-left .ms-section").last()), e.length && B(e)
        }

        function q(t) {
            t ? P() : i.addEventListener ? (i.removeEventListener("mousewheel", D, !1), i.removeEventListener("wheel", D, !1)) : i.detachEvent("onmousewheel", D)
        }
        var H = 0,
            O = 0;

        function W(i) {
            if (X(i) && (t("#premium-multiscroll-" + s.id + " .ms-left .ms-section.active"), null !== i.target.closest("#premium-multiscroll-" + s.id) && !v)) {
                var n = N(i);
                O = n.y, n.x, o.abs(H - O) > t(e).height() / 100 * s.touchSensitivity && (O < H ? I() : H < O && Y())
            }
        }

        function X(t) {
            return void 0 === t.pointerType || "mouse" != t.pointerType
        }

        function V(t) {
            if (X(t)) {
                var e = N(t);
                H = e.y, e.x
            }
        }

        function A() {
            (c || m) && (i.removeEventListener(x.touchstart, V), i.removeEventListener(x.touchmove, W, {
                passive: !1
            }), i.addEventListener(x.touchstart, V), i.addEventListener(x.touchmove, W, {
                passive: !1
            }))
        }

        function N(t) {
            var e = [];
            return e.y = void 0 !== t.pageY && (t.pageY || t.pageX) ? t.pageY : t.touches[0].pageY, e.x = void 0 !== t.pageX && (t.pageY || t.pageX) ? t.pageX : t.touches[0].pageX, c && X(t) && void 0 !== t.touches && (e.y = t.touches[0].pageY, e.x = t.touches[0].pageX), e
        }
        l.destroy = function () {
            q(!1), (c || m) && (i.removeEventListener(x.touchstart, V), i.removeEventListener(x.touchmove, W, {
                passive: !1
            })), t(e).off("hashchange", y).off("resize", E), t(i).off("mouseenter", "#multiscroll-nav-" + s.id + " li").off("mouseleave", "#multiscroll-nav-" + s.id + " li").off("click", "#multiscroll-nav-" + s.id + " a").off("click", "#premium-scroll-nav-menu-" + s.id + " a")
        }, l.build = function () {
            q(!0), A(), t(e).on("hashchange", y).on("resize", E), t(i).on("mouseenter", "#multiscroll-nav-" + s.id + " li", C).on("mouseleave", "#multiscroll-nav-" + s.id + " li", S).on("click", "#multiscroll-nav-" + s.id + " a", w).on("click", "#premium-scroll-nav-menu-" + s.id + " a", w)
        }
    }
}(jQuery, window, document, Math);