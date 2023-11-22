(function ($) {

    ! function (o) {
        var n = {};

        function i(e) {
            if (n[e]) return n[e].exports;
            var t = n[e] = {
                i: e,
                l: !1,
                exports: {}
            };
            return o[e].call(t.exports, t, t.exports, i), t.l = !0, t.exports
        }
        i.m = o, i.c = n, i.d = function (e, t, o) {
            i.o(e, t) || Object.defineProperty(e, t, {
                enumerable: !0,
                get: o
            })
        }, i.r = function (e) {
            "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, {
                value: "Module"
            }), Object.defineProperty(e, "__esModule", {
                value: !0
            })
        }, i.t = function (t, e) {
            if (1 & e && (t = i(t)), 8 & e) return t;
            if (4 & e && "object" == typeof t && t && t.__esModule) return t;
            var o = Object.create(null);
            if (i.r(o), Object.defineProperty(o, "default", {
                enumerable: !0,
                value: t
            }), 2 & e && "string" != typeof t)
                for (var n in t) i.d(o, n, function (e) {
                    return t[e]
                }.bind(null, n));
            return o
        }, i.n = function (e) {
            var t = e && e.__esModule ? function () {
                return e.default
            } : function () {
                return e
            };
            return i.d(t, "a", t), t
        }, i.o = function (e, t) {
            return Object.prototype.hasOwnProperty.call(e, t)
        }, i.p = "", i(i.s = 11)
    }([, , function (e, t, o) {
        "use strict";
        e.exports = function (e) {
            "complete" === document.readyState || "interactive" === document.readyState ? e.call() : document.attachEvent ? document.attachEvent("onreadystatechange", function () {
                "interactive" === document.readyState && e.call()
            }) : document.addEventListener && document.addEventListener("DOMContentLoaded", e)
        }
    }, , function (o, e, t) {
        "use strict";
        (function (e) {
            var t;
            t = "undefined" != typeof window ? window : void 0 !== e ? e : "undefined" != typeof self ? self : {}, o.exports = t
        }).call(this, t(5))
    }, function (e, t, o) {
        "use strict";
        var n, i = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
            return typeof e
        } : function (e) {
            return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
        };
        n = function () {
            return this
        }();
        try {
            n = n || Function("return this")() || (0, eval)("this")
        } catch (e) {
            "object" === ("undefined" == typeof window ? "undefined" : i(window)) && (n = window)
        }
        e.exports = n
    }, , , , , , function (e, t, o) {
        e.exports = o(12)
    }, function (e, t, o) {
        "use strict";
        var n = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
            return typeof e
        } : function (e) {
            return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
        },
            i = l(o(2)),
            a = o(4),
            r = l(o(13));

        function l(e) {
            return e && e.__esModule ? e : {
                default: e
            }
        }
        var s = a.window.jarallax;
        if (a.window.jarallax = r.default, a.window.jarallax.noConflict = function () {
            return a.window.jarallax = s, this
        }, void 0 !== a.jQuery) {
            var c = function () {
                var e = arguments || [];
                Array.prototype.unshift.call(e, this);
                var t = r.default.apply(a.window, e);
                return "object" !== (void 0 === t ? "undefined" : n(t)) ? t : this
            };
            c.constructor = r.default.constructor;
            var u = a.jQuery.fn.jarallax;
            a.jQuery.fn.jarallax = c, a.jQuery.fn.jarallax.noConflict = function () {
                return a.jQuery.fn.jarallax = u, this
            }
        } (0, i.default)(function () {
            (0, r.default)(document.querySelectorAll("[data-jarallax]"))
        })
    }, function (e, j, S) {
        "use strict";
        (function (e) {
            Object.defineProperty(j, "__esModule", {
                value: !0
            });
            var d = function (e, t) {
                if (Array.isArray(e)) return e;
                if (Symbol.iterator in Object(e)) return function (e, t) {
                    var o = [],
                        n = !0,
                        i = !1,
                        a = void 0;
                    try {
                        for (var r, l = e[Symbol.iterator](); !(n = (r = l.next()).done) && (o.push(r.value), !t || o.length !== t); n = !0);
                    } catch (e) {
                        i = !0, a = e
                    } finally {
                        try {
                            !n && l.return && l.return()
                        } finally {
                            if (i) throw a
                        }
                    }
                    return o
                }(e, t);
                throw new TypeError("Invalid attempt to destructure non-iterable instance")
            },
                t = function () {
                    function n(e, t) {
                        for (var o = 0; o < t.length; o++) {
                            var n = t[o];
                            n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(e, n.key, n)
                        }
                    }
                    return function (e, t, o) {
                        return t && n(e.prototype, t), o && n(e, o), e
                    }
                }(),
                p = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
                    return typeof e
                } : function (e) {
                    return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
                },
                o = a(S(2)),
                n = a(S(14)),
                i = S(4);

            function a(e) {
                return e && e.__esModule ? e : {
                    default: e
                }
            }
            var s = -1 < navigator.userAgent.indexOf("MSIE ") || -1 < navigator.userAgent.indexOf("Trident/") || -1 < navigator.userAgent.indexOf("Edge/"),
                r = function () {
                    for (var e = "transform WebkitTransform MozTransform".split(" "), t = document.createElement("div"), o = 0; o < e.length; o++)
                        if (t && void 0 !== t.style[e[o]]) return e[o];
                    return !1
                }(),
                b = void 0,
                v = void 0,
                l = void 0,
                c = !1,
                u = !1;

            function m(e) {
                b = i.window.innerWidth || document.documentElement.clientWidth, v = i.window.innerHeight || document.documentElement.clientHeight, "object" !== (void 0 === e ? "undefined" : p(e)) || "load" !== e.type && "dom-loaded" !== e.type || (c = !0)
            }
            m(), i.window.addEventListener("resize", m), i.window.addEventListener("orientationchange", m), i.window.addEventListener("load", m), (0, o.default)(function () {
                m({
                    type: "dom-loaded"
                })
            });
            var f = [],
                y = !1;

            function g() {
                if (f.length) {
                    l = void 0 !== i.window.pageYOffset ? i.window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop;
                    var t = c || !y || y.width !== b || y.height !== v,
                        o = u || t || !y || y.y !== l;
                    u = c = !1, (t || o) && (f.forEach(function (e) {
                        t && e.onResize(), o && e.onScroll()
                    }), y = {
                        width: b,
                        height: v,
                        y: l
                    }), (0, n.default)(g)
                }
            }
            var h = !!e.ResizeObserver && new e.ResizeObserver(function (e) {
                e && e.length && (0, n.default)(function () {
                    e.forEach(function (e) {
                        e.target && e.target.jarallax && (c || e.target.jarallax.onResize(), u = !0)
                    })
                })
            }),
                x = 0,
                w = function () {
                    function u(e, t) {
                        ! function (e, t) {
                            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
                        }(this, u);
                        var o = this;
                        o.instanceID = x++, o.$item = e, o.defaults = {
                            type: "scroll",
                            speed: .5,
                            imgSrc: null,
                            imgElement: ".jarallax-img",
                            imgSize: "cover",
                            imgPosition: "50% 50%",
                            imgRepeat: "no-repeat",
                            keepImg: !1,
                            elementInViewport: null,
                            zIndex: -100,
                            disableParallax: !1,
                            disableVideo: !1,
                            automaticResize: !0,
                            videoSrc: null,
                            videoStartTime: 0,
                            videoEndTime: 0,
                            videoVolume: 0,
                            videoPlayOnlyVisible: !0,
                            onScroll: null,
                            onInit: null,
                            onDestroy: null,
                            onCoverImage: null
                        };
                        var n = o.$item.getAttribute("data-jarallax"),
                            i = JSON.parse(n || "{}");
                        n && console.warn("Detected usage of deprecated data-jarallax JSON options, you should use pure data-attribute options. See info here - https://github.com/nk-o/jarallax/issues/53");
                        var a = o.$item.dataset || {},
                            r = {};
                        if (Object.keys(a).forEach(function (e) {
                            var t = e.substr(0, 1).toLowerCase() + e.substr(1);
                            t && void 0 !== o.defaults[t] && (r[t] = a[e])
                        }), o.options = o.extend({}, o.defaults, i, r, t), o.pureOptions = o.extend({}, o.options), Object.keys(o.options).forEach(function (e) {
                            "true" === o.options[e] ? o.options[e] = !0 : "false" === o.options[e] && (o.options[e] = !1)
                        }), o.options.speed = Math.min(2, Math.max(-1, parseFloat(o.options.speed))), (o.options.noAndroid || o.options.noIos) && (console.warn("Detected usage of deprecated noAndroid or noIos options, you should use disableParallax option. See info here - https://github.com/nk-o/jarallax/#disable-on-mobile-devices"), o.options.disableParallax || (o.options.noIos && o.options.noAndroid ? o.options.disableParallax = /iPad|iPhone|iPod|Android/ : o.options.noIos ? o.options.disableParallax = /iPad|iPhone|iPod/ : o.options.noAndroid && (o.options.disableParallax = /Android/))), "string" == typeof o.options.disableParallax && (o.options.disableParallax = new RegExp(o.options.disableParallax)), o.options.disableParallax instanceof RegExp) {
                            var l = o.options.disableParallax;
                            o.options.disableParallax = function () {
                                return l.test(navigator.userAgent)
                            }
                        }
                        if ("function" != typeof o.options.disableParallax && (o.options.disableParallax = function () {
                            return !1
                        }), "string" == typeof o.options.disableVideo && (o.options.disableVideo = new RegExp(o.options.disableVideo)), o.options.disableVideo instanceof RegExp) {
                            var s = o.options.disableVideo;
                            o.options.disableVideo = function () {
                                return s.test(navigator.userAgent)
                            }
                        }
                        "function" != typeof o.options.disableVideo && (o.options.disableVideo = function () {
                            return !1
                        });
                        var c = o.options.elementInViewport;
                        c && "object" === (void 0 === c ? "undefined" : p(c)) && void 0 !== c.length && (c = d(c, 1)[0]);
                        c instanceof Element || (c = null), o.options.elementInViewport = c, o.image = {
                            src: o.options.imgSrc || null,
                            $container: null,
                            useImgTag: !1,
                            position: "absolute"
                        }, o.initImg() && o.canInitParallax() && o.init()
                    }
                    return t(u, [{
                        key: "css",
                        value: function (t, o) {
                            return "string" == typeof o ? i.window.getComputedStyle(t).getPropertyValue(o) : (o.transform && r && (o[r] = o.transform), Object.keys(o).forEach(function (e) {
                                t.style[e] = o[e]
                            }), t)
                        }
                    }, {
                        key: "extend",
                        value: function (o) {
                            var n = arguments;
                            return o = o || {}, Object.keys(arguments).forEach(function (t) {
                                n[t] && Object.keys(n[t]).forEach(function (e) {
                                    o[e] = n[t][e]
                                })
                            }), o
                        }
                    }, {
                        key: "getWindowData",
                        value: function () {
                            return {
                                width: b,
                                height: v,
                                y: l
                            }
                        }
                    }, {
                        key: "initImg",
                        value: function () {
                            var e = this,
                                t = e.options.imgElement;
                            return t && "string" == typeof t && (t = e.$item.querySelector(t)), t instanceof Element || (t = null), t && (e.options.keepImg ? e.image.$item = t.cloneNode(!0) : (e.image.$item = t, e.image.$itemParent = t.parentNode), e.image.useImgTag = !0), !!e.image.$item || (null === e.image.src && (e.image.src = e.css(e.$item, "background-image").replace(/^url\(['"]?/g, "").replace(/['"]?\)$/g, "")), !(!e.image.src || "none" === e.image.src))
                        }
                    }, {
                        key: "canInitParallax",
                        value: function () {
                            return r && !this.options.disableParallax()
                        }
                    }, {
                        key: "init",
                        value: function () {
                            var e = this,
                                t = {
                                    position: "absolute",
                                    top: 0,
                                    left: 0,
                                    width: "100%",
                                    height: "100%",
                                    overflow: "hidden",
                                    pointerEvents: "none"
                                },
                                o = {};
                            if (!e.options.keepImg) {
                                var n = e.$item.getAttribute("style");
                                if (n && e.$item.setAttribute("data-jarallax-original-styles", n), e.image.useImgTag) {
                                    var i = e.image.$item.getAttribute("style");
                                    i && e.image.$item.setAttribute("data-jarallax-original-styles", i)
                                }
                            }
                            if ("static" === e.css(e.$item, "position") && e.css(e.$item, {
                                position: "relative"
                            }), "auto" === e.css(e.$item, "z-index") && e.css(e.$item, {
                                zIndex: 0
                            }), e.image.$container = document.createElement("div"), e.css(e.image.$container, t), e.css(e.image.$container, {
                                "z-index": e.options.zIndex
                            }), s && e.css(e.image.$container, {
                                opacity: .9999
                            }), e.image.$container.setAttribute("id", "jarallax-container-" + e.instanceID), e.$item.appendChild(e.image.$container), e.image.useImgTag ? o = e.extend({
                                "object-fit": e.options.imgSize,
                                "object-position": e.options.imgPosition,
                                "font-family": "object-fit: " + e.options.imgSize + "; object-position: " + e.options.imgPosition + ";",
                                "max-width": "none"
                            }, t, o) : (e.image.$item = document.createElement("div"), e.image.src && (o = e.extend({
                                "background-position": e.options.imgPosition,
                                "background-size": e.options.imgSize,
                                "background-repeat": e.options.imgRepeat,
                                "background-image": 'url("' + e.image.src + '")'
                            }, t, o))), "opacity" !== e.options.type && "scale" !== e.options.type && "scale-opacity" !== e.options.type && 1 !== e.options.speed || (e.image.position = "absolute"), "fixed" === e.image.position)
                                for (var a = 0, r = e.$item; null !== r && r !== document && 0 === a;) {
                                    var l = e.css(r, "-webkit-transform") || e.css(r, "-moz-transform") || e.css(r, "transform");
                                    l && "none" !== l && (a = 1, e.image.position = "absolute"), r = r.parentNode
                                }
                            o.position = e.image.position, e.css(e.image.$item, o), e.image.$container.appendChild(e.image.$item), e.onResize(), e.onScroll(!0), e.options.automaticResize && h && h.observe(e.$item), e.options.onInit && e.options.onInit.call(e), "none" !== e.css(e.$item, "background-image") && e.css(e.$item, {
                                "background-image": "none"
                            }), e.addToParallaxList()
                        }
                    }, {
                        key: "addToParallaxList",
                        value: function () {
                            f.push(this), 1 === f.length && g()
                        }
                    }, {
                        key: "removeFromParallaxList",
                        value: function () {
                            var o = this;
                            f.forEach(function (e, t) {
                                e.instanceID === o.instanceID && f.splice(t, 1)
                            })
                        }
                    }, {
                        key: "destroy",
                        value: function () {
                            var e = this;
                            e.removeFromParallaxList();
                            var t = e.$item.getAttribute("data-jarallax-original-styles");
                            if (e.$item.removeAttribute("data-jarallax-original-styles"), t ? e.$item.setAttribute("style", t) : e.$item.removeAttribute("style"), e.image.useImgTag) {
                                var o = e.image.$item.getAttribute("data-jarallax-original-styles");
                                e.image.$item.removeAttribute("data-jarallax-original-styles"), o ? e.image.$item.setAttribute("style", t) : e.image.$item.removeAttribute("style"), e.image.$itemParent && e.image.$itemParent.appendChild(e.image.$item)
                            }
                            e.$clipStyles && e.$clipStyles.parentNode.removeChild(e.$clipStyles), e.image.$container && e.image.$container.parentNode.removeChild(e.image.$container), e.options.onDestroy && e.options.onDestroy.call(e), delete e.$item.jarallax
                        }
                    }, {
                        key: "clipContainer",
                        value: function () {
                            if ("fixed" === this.image.position) {
                                var e = this,
                                    t = e.image.$container.getBoundingClientRect(),
                                    o = t.width,
                                    n = t.height;
                                if (!e.$clipStyles) e.$clipStyles = document.createElement("style"), e.$clipStyles.setAttribute("type", "text/css"), e.$clipStyles.setAttribute("id", "jarallax-clip-" + e.instanceID), (document.head || document.getElementsByTagName("head")[0]).appendChild(e.$clipStyles);
                                var i = "#jarallax-container-" + e.instanceID + " {\n           clip: rect(0 " + o + "px " + n + "px 0);\n           clip: rect(0, " + o + "px, " + n + "px, 0);\n        }";
                                e.$clipStyles.styleSheet ? e.$clipStyles.styleSheet.cssText = i : e.$clipStyles.innerHTML = i
                            }
                        }
                    }, {
                        key: "coverImage",
                        value: function () {
                            var e = this,
                                t = e.image.$container.getBoundingClientRect(),
                                o = t.height,
                                n = e.options.speed,
                                i = "scroll" === e.options.type || "scroll-opacity" === e.options.type,
                                a = 0,
                                r = o,
                                l = 0;
                            return i && (a = n < 0 ? n * Math.max(o, v) : n * (o + v), 1 < n ? r = Math.abs(a - v) : n < 0 ? r = a / n + Math.abs(a) : r += Math.abs(v - o) * (1 - n), a /= 2), e.parallaxScrollDistance = a, l = i ? (v - r) / 2 : (o - r) / 2, e.css(e.image.$item, {
                                height: r + "px",
                                marginTop: l + "px",
                                left: "fixed" === e.image.position ? t.left + "px" : "0",
                                width: t.width + "px"
                            }), e.options.onCoverImage && e.options.onCoverImage.call(e), {
                                image: {
                                    height: r,
                                    marginTop: l
                                },
                                container: t
                            }
                        }
                    }, {
                        key: "isVisible",
                        value: function () {
                            return this.isElementInViewport || !1
                        }
                    }, {
                        key: "onScroll",
                        value: function (e) {
                            var t = this,
                                o = t.$item.getBoundingClientRect(),
                                n = o.top,
                                i = o.height,
                                a = {},
                                r = o;
                            if (t.options.elementInViewport && (r = t.options.elementInViewport.getBoundingClientRect()), t.isElementInViewport = 0 <= r.bottom && 0 <= r.right && r.top <= v && r.left <= b, e || t.isElementInViewport) {
                                var l = Math.max(0, n),
                                    s = Math.max(0, i + n),
                                    c = Math.max(0, -n),
                                    u = Math.max(0, n + i - v),
                                    d = Math.max(0, i - (n + i - v)),
                                    p = Math.max(0, -n + v - i),
                                    m = 1 - 2 * (v - n) / (v + i),
                                    f = 1;
                                if (i < v ? f = 1 - (c || u) / i : s <= v ? f = s / v : d <= v && (f = d / v), "opacity" !== t.options.type && "scale-opacity" !== t.options.type && "scroll-opacity" !== t.options.type || (a.transform = "translate3d(0,0,0)", a.opacity = f), "scale" === t.options.type || "scale-opacity" === t.options.type) {
                                    var y = 1;
                                    t.options.speed < 0 ? y -= t.options.speed * f : y += t.options.speed * (1 - f), a.transform = "scale(" + y + ") translate3d(0,0,0)"
                                }
                                if ("scroll" === t.options.type || "scroll-opacity" === t.options.type) {
                                    var g = t.parallaxScrollDistance * m;
                                    "absolute" === t.image.position && (g -= n), a.transform = "translate3d(0," + g + "px,0)"
                                }
                                t.css(t.image.$item, a), t.options.onScroll && t.options.onScroll.call(t, {
                                    section: o,
                                    beforeTop: l,
                                    beforeTopEnd: s,
                                    afterTop: c,
                                    beforeBottom: u,
                                    beforeBottomEnd: d,
                                    afterBottom: p,
                                    visiblePercent: f,
                                    fromViewportCenter: m
                                })
                            }
                        }
                    }, {
                        key: "onResize",
                        value: function () {
                            this.coverImage(), this.clipContainer()
                        }
                    }]), u
                }(),
                $ = function (e) {
                    ("object" === ("undefined" == typeof HTMLElement ? "undefined" : p(HTMLElement)) ? e instanceof HTMLElement : e && "object" === (void 0 === e ? "undefined" : p(e)) && null !== e && 1 === e.nodeType && "string" == typeof e.nodeName) && (e = [e]);
                    for (var t = arguments[1], o = Array.prototype.slice.call(arguments, 2), n = e.length, i = 0, a = void 0; i < n; i++)
                        if ("object" === (void 0 === t ? "undefined" : p(t)) || void 0 === t ? e[i].jarallax || (e[i].jarallax = new w(e[i], t)) : e[i].jarallax && (a = e[i].jarallax[t].apply(e[i].jarallax, o)), void 0 !== a) return a;
                    return e
                };
            $.constructor = w, j.default = $
        }).call(this, S(5))
    }, function (e, t, o) {
        "use strict";
        var n = o(4),
            i = n.requestAnimationFrame || n.webkitRequestAnimationFrame || n.mozRequestAnimationFrame || function (e) {
                var t = +new Date,
                    o = Math.max(0, 16 - (t - a)),
                    n = setTimeout(e, o);
                return a = t, n
            },
            a = +new Date;
        var r = n.cancelAnimationFrame || n.webkitCancelAnimationFrame || n.mozCancelAnimationFrame || clearTimeout;
        Function.prototype.bind && (i = i.bind(n), r = r.bind(n)), (e.exports = i).cancel = r
    }]);

    /*!
 * ScrollToPlugin 3.6.1
 * https://greensock.com
 *
 * @license Copyright 2021, GreenSock. All rights reserved.
 * Subject to the terms at https://greensock.com/standard-license or for Club GreenSock members, the agreement issued with that membership.
 * @author: Jack Doyle, jack@greensock.com
 */

    !function (t, e) { "object" == typeof exports && "undefined" != typeof module ? e(exports) : "function" == typeof define && define.amd ? define(["exports"], e) : e((t = t || self).window = t.window || {}) }(this, function (e) { "use strict"; function k() { return "undefined" != typeof window } function l() { return i || k() && (i = window.gsap) && i.registerPlugin && i } function m(t) { return "string" == typeof t } function n(t) { return "function" == typeof t } function o(t, e) { var o = "x" === e ? "Width" : "Height", n = "scroll" + o, r = "client" + o; return t === x || t === u || t === c ? Math.max(u[n], c[n]) - (x["inner" + o] || u[r] || c[r]) : t[n] - t["offset" + o] } function p(t, e) { var o = "scroll" + ("x" === e ? "Left" : "Top"); return t === x && (null != t.pageXOffset ? o = "page" + e.toUpperCase() + "Offset" : t = null != u[o] ? u : c), function () { return t[o] } } function r(t, e) { if (!(t = a(t)[0]) || !t.getBoundingClientRect) return console.warn("scrollTo target doesn't exist. Using 0") || { x: 0, y: 0 }; var o = t.getBoundingClientRect(), n = !e || e === x || e === c, r = n ? { top: u.clientTop - (x.pageYOffset || u.scrollTop || c.scrollTop || 0), left: u.clientLeft - (x.pageXOffset || u.scrollLeft || c.scrollLeft || 0) } : e.getBoundingClientRect(), i = { x: o.left - r.left, y: o.top - r.top }; return !n && e && (i.x += p(e, "x")(), i.y += p(e, "y")()), i } function s(t, e, n, i, l) { return isNaN(t) || "object" == typeof t ? m(t) && "=" === t.charAt(1) ? parseFloat(t.substr(2)) * ("-" === t.charAt(0) ? -1 : 1) + i - l : "max" === t ? o(e, n) - l : Math.min(o(e, n), r(t, e)[n] - l) : parseFloat(t) - l } function t() { i = l(), k() && i && document.body && (x = window, c = document.body, u = document.documentElement, a = i.utils.toArray, i.config({ autoKillThreshold: 7 }), g = i.config(), f = 1) } var i, f, x, u, c, a, g, y = { version: "3.6.1", name: "scrollTo", rawVars: 1, register: function register(e) { i = e, t() }, init: function init(e, o, r, i, l) { f || t(); var u = this; u.isWin = e === x, u.target = e, u.tween = r, o = function _clean(t, e, o, r) { if (n(t) && (t = t(e, o, r)), "object" != typeof t) return m(t) && "max" !== t && "=" !== t.charAt(1) ? { x: t, y: t } : { y: t }; if (t.nodeType) return { y: t, x: t }; var i, l = {}; for (i in t) l[i] = "onAutoKill" !== i && n(t[i]) ? t[i](e, o, r) : t[i]; return l }(o, i, e, l), u.vars = o, u.autoKill = !!o.autoKill, u.getX = p(e, "x"), u.getY = p(e, "y"), u.x = u.xPrev = u.getX(), u.y = u.yPrev = u.getY(), null != o.x ? (u.add(u, "x", u.x, s(o.x, e, "x", u.x, o.offsetX || 0), i, l), u._props.push("scrollTo_x")) : u.skipX = 1, null != o.y ? (u.add(u, "y", u.y, s(o.y, e, "y", u.y, o.offsetY || 0), i, l), u._props.push("scrollTo_y")) : u.skipY = 1 }, render: function render(t, e) { for (var n, r, i, l, s, u = e._pt, f = e.target, p = e.tween, c = e.autoKill, a = e.xPrev, y = e.yPrev, d = e.isWin; u;)u.r(t, u.d), u = u._next; n = d || !e.skipX ? e.getX() : a, i = (r = d || !e.skipY ? e.getY() : y) - y, l = n - a, s = g.autoKillThreshold, e.x < 0 && (e.x = 0), e.y < 0 && (e.y = 0), c && (!e.skipX && (s < l || l < -s) && n < o(f, "x") && (e.skipX = 1), !e.skipY && (s < i || i < -s) && r < o(f, "y") && (e.skipY = 1), e.skipX && e.skipY && (p.kill(), e.vars.onAutoKill && e.vars.onAutoKill.apply(p, e.vars.onAutoKillParams || []))), d ? x.scrollTo(e.skipX ? n : e.x, e.skipY ? r : e.y) : (e.skipY || (f.scrollTop = e.y), e.skipX || (f.scrollLeft = e.x)), e.xPrev = e.x, e.yPrev = e.y }, kill: function kill(t) { var e = "scrollTo" === t; !e && "scrollTo_x" !== t || (this.skipX = 1), !e && "scrollTo_y" !== t || (this.skipY = 1) } }; y.max = o, y.getOffset = r, y.buildGetter = p, l() && i.registerPlugin(y), e.ScrollToPlugin = y, e.default = y; if (typeof (window) === "undefined" || window !== e) { Object.defineProperty(e, "__esModule", { value: !0 }) } else { delete e.default } });

    window.premiumParallaxEffects = function (element, settings) {

        var self = this,
            $el = $(element),
            scrolls = $el.data("scrolls"),
            elementSettings = settings,
            elType = elementSettings.elType,
            elOffset = $el.offset();

        //Check if Horizontal Scroll Widget
        var isHScrollWidget = $el.closest(".premium-hscroll-temp").length;

        self.elementRules = {};

        self.init = function () {

            if (scrolls || 'SECTION' === elType) {

                if (!elementSettings.effects.length) {
                    return;
                }
                self.setDefaults();
                elementorFrontend.elements.$window.on('scroll load', self.initScroll);
            } else {
                elementorFrontend.elements.$window.off('scroll load', self.initScroll);
                return;
            }

        };

        self.setDefaults = function () {

            elementSettings.defaults = {};
            elementSettings.defaults.axis = 'y';

        };

        self.transform = function (action, percents, data) {

            if ("down" === data.direction) {
                percents = 100 - percents;
            }

            if (data.range) {
                if (data.range.start > percents && !isHScrollWidget) {
                    percents = data.range.start;
                }

                if (data.range.end < percents && !isHScrollWidget) {
                    percents = data.range.end;
                }

            }

            if ('rotate' === action) {
                elementSettings.defaults.unit = 'deg';
            } else {
                elementSettings.defaults.unit = 'px';
            }

            self.updateElement('transform', action, self.getStep(percents, data) + elementSettings.defaults.unit);

        };

        self.getPercents = function () {

            var dimensions = self.getDimensions();

            var startOffset = innerHeight;

            if (isHScrollWidget) startOffset = 0;

            (elementTopWindowPoint = dimensions.elementTop - pageYOffset),
                (elementEntrancePoint = elementTopWindowPoint - startOffset);

            passedRangePercents =
                (100 / dimensions.range) * (elementEntrancePoint * -1);

            return passedRangePercents;

        };

        self.initScroll = function () {

            if (elementSettings.effects.includes('translateY')) {

                self.initVScroll();

            }

            if (elementSettings.effects.includes('translateX')) {

                self.initHScroll();

            }

        };

        self.initVScroll = function () {

            var percents = self.getPercents();

            self.transform('translateY', percents, elementSettings.vscroll);

        };

        self.initHScroll = function () {

            var percents = self.getPercents();

            self.transform('translateX', percents, elementSettings.hscroll);

        };

        self.getDimensions = function () {

            var elementOffset = elOffset;

            var dimensions = {
                elementHeight: $el.outerHeight(),
                elementWidth: $el.outerWidth(),
                elementTop: elementOffset.top,
                elementLeft: elementOffset.left
            };

            dimensions.range = dimensions.elementHeight + innerHeight;

            return dimensions;

        };

        self.getStep = function (percents, options) {

            return -(percents - 50) * options.speed;

        };

        self.getEffectMovePoint = function (percents, effect, range) {

            var point = 0;

            if (percents < range.start) {
                if ("down" === effect) {
                    point = 0;
                } else {
                    point = 100;
                }
            } else if (percents < range.end) {

                point = self.getPointFromPercents((range.end - range.start), (percents - range.start));

                if ("up" === effect) {
                    point = 100 - point;
                }

            } else if ("up" === effect) {
                point = 0;
            } else if ("down" === effect) {
                point = 100;
            }

            return point;

        };

        self.getEffectValueFromMovePoint = function (level, movePoint) {

            return level * movePoint / 100;

        };

        self.getPointFromPercents = function (movableRange, percents) {

            var movePoint = percents / movableRange * 100;

            return +movePoint.toFixed(2);

        };

        self.updateElement = function (propName, key, value) {

            if (!self.elementRules[propName]) {
                self.elementRules[propName] = {};
            }

            if (!self.elementRules[propName][key]) {
                self.elementRules[propName][key] = true;

                self.updateElementRule(propName);
            }

            var cssVarKey = '--' + key;

            element.style.setProperty(cssVarKey, value);

        };

        self.updateElementRule = function (rule) {

            var cssValue = '';

            $.each(self.elementRules[rule], function (variableKey) {
                cssValue += variableKey + '(var(--' + variableKey + '))';
            });

            $el.css(rule, cssValue);

        };

    };

    $(window).on('elementor/frontend/init', function () {

        var PremiumParallaxHandler = function ($scope) {

            if (!$scope.hasClass("premium-parallax-yes"))
                return;

            var target = $scope,
                sectionId = target.data("id"),
                elementType = target.data('element_type'),
                settings = {},
                tempTarget = target.find('#premium-parallax-' + sectionId),
                editMode = elementorFrontend.isEditMode() && tempTarget.length > 0,
                targetID = editMode ? tempTarget : target;

            generateSettings(targetID);

            if (!settings || undefined == settings.type) {
                return false;
            }

            if ("multi" !== settings.type && "automove" !== settings.type) {
                generateJarallax();
            } else if ("automove" === settings.type) {
                generateAutoMoveBackground();
            } else {
                var currentDevice = elementorFrontend.getCurrentDeviceMode();

                generateMultiLayers(currentDevice);

                if (editMode) {
                    var settings = {
                        repeater: 'premium_parallax_layers_list',
                        item: '.premium-parallax-layer',
                        hor: 'premium_parallax_layer_hor_pos',
                        ver: 'premium_parallax_layer_ver_pos',
                        width: 'premium_parallax_layer_width',
                        tab: 'section_premium_parallax',
                        offset: 0,
                        widgets: ["drag", "resize"]
                    },
                        instance = null;

                    instance = new premiumEditorBehavior(target, settings);
                    instance.init();
                }
            }

            function generateSettings(target) {

                var parallaxSettings = target.data("pa-parallax");

                if (!parallaxSettings) {
                    return false;
                }

                settings.type = parallaxSettings["type"];

                if (undefined == settings.type) {
                    return false;
                }

                if ("multi" !== settings.type && "automove" !== settings.type) {
                    settings.speed = parallaxSettings["speed"];
                    settings.android = parallaxSettings["android"];
                    settings.ios = parallaxSettings["ios"];
                    settings.size = parallaxSettings["size"];
                    settings.position = target.css('backgroundPosition');
                    settings.repeat = parallaxSettings["repeat"];
                } else if ("automove" === settings.type) {
                    settings.speed = parallaxSettings["speed"];
                    settings.direction = parallaxSettings["direction"];
                } else {
                    settings.items = [];
                    $.each(parallaxSettings["items"], function (index, layer) {
                        settings.items.push(layer);
                    });
                    settings.devices = parallaxSettings["devices"];
                    settings.speed = parallaxSettings["speed"];
                }

                if (0 !== Object.keys(settings).length) {
                    return settings;
                }

                return false;
            }

            function responsiveParallax(android, ios) {
                switch (true || 1) {
                    case android && ios:
                        return /iPad|iPhone|iPod|Android/;
                    case android && !ios:
                        return /Android/;
                    case !android && ios:
                        return /iPad|iPhone|iPod/;
                    case !android && !ios:
                        return null;
                }
            }

            function generateJarallax() {

                //Fix image bounce issue on page load
                target.removeClass("premium-parallax-section-hide");

                if (elementType === 'column') {
                    target = $scope.find('.elementor-column-wrap').first();

                    if (target.length < 1)
                        target = $scope.find('.elementor-widget-wrap').first();

                }

                target.jarallax({
                    type: settings.type,
                    speed: settings.speed || 0.1,
                    disableParallax: responsiveParallax(
                        1 == settings.android,
                        1 == settings.ios
                    ),
                    keepImg: true,
                    imgSize: settings.size,
                    imgPosition: settings.position,
                    imgRepeat: settings.repeat
                });

            }

            function generateAutoMoveBackground() {
                var speed = parseInt(settings.speed);

                if (elementType === 'column') {
                    target = $scope.find('.elementor-column-wrap').first();

                    if (target.length < 1)
                        target = $scope.find('.elementor-widget-wrap').first();

                }

                //Remove transitions for Safari to prevent jittering coming from Elementor default transitions.
                var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);

                if (isSafari)
                    target.addClass("premium-parallax-no-trans");

                target.css("background-position", "0px 0px");

                if (settings.direction === "left") {
                    var position = parseInt(target.css("background-position-x"));

                    setInterval(function () {
                        position = position + speed;
                        target.css("backgroundPosition", position + "px 0");
                    }, 70);
                } else if (settings.direction === "right") {
                    var position = parseInt(target.css("background-position-x"));

                    setInterval(function () {
                        position = position - speed;
                        target.css("backgroundPosition", position + "px 0");
                    }, 70);
                } else if (settings.direction === "top") {
                    var position = parseInt(target.css("background-position-y"));

                    setInterval(function () {
                        position = position + speed;
                        target.css("backgroundPosition", "0 " + position + "px");
                    }, 70);
                } else if (settings.direction === "bottom") {
                    var position = parseInt(target.css("background-position-y"));

                    setInterval(function () {
                        position = position - speed;
                        target.css("backgroundPosition", "0 " + position + "px");
                    }, 70);
                }
            }

            function generateMultiLayers(currentDevice) {

                var mouseParallax = "",
                    deviceSuffix = ('desktop' === currentDevice) ? '' : '_' + currentDevice,
                    mouseRate = "";

                target.find(".premium-parallax-layer").remove();

                $.each(settings.items, function (index, layout) {

                    if (!layout.show_layer_on.includes(currentDevice))
                        return;

                    var layerHTML = getLayerHTML(layout);

                    if ('' == layerHTML)
                        return;

                    var layerID = 'premium-parallax-layer-' + layout._id,
                        layerPosition = ' premium-parallax-' + layout.premium_parallax_layer_hor + ' premium-parallax-' + layout.premium_parallax_layer_ver,
                        layerType = ' parallax-' + layout.layer_type;

                    if ("yes" === layout.premium_parallax_layer_mouse && "" !== layout.premium_parallax_layer_rate) {
                        mouseParallax = ' data-parallax="true" ';
                        mouseRate = ' data-rate="' + layout.premium_parallax_layer_rate + '" ';
                    } else {
                        mouseParallax = ' data-parallax="false" ';
                    }

                    if ('img' === layout.layer_type)
                        var width = 'undefined' != typeof layout["premium_parallax_layer_width" + deviceSuffix] ? layout["premium_parallax_layer_width" + deviceSuffix].size : layout["premium_parallax_layer_width"].size;

                    $('<div id="' + layerID + '"' +
                        mouseParallax +
                        mouseRate +
                        ' class="premium-parallax-layer elementor-repeater-item-' + layout._id + layerPosition + layerType + '">' + layerHTML + '</div>'
                    )
                        .prependTo(target)
                        .css({
                            "z-index": layout["premium_parallax_layer_z_index"],
                            "background-size": layout["premium_parallax_layer_back_size"],
                            "width": 'img' === layout.layer_type ? width + "%" : "auto"
                        });

                    var $layer = target.find('#' + layerID);

                    if ('custom' === layout.premium_parallax_layer_hor) {

                        var left = 'undefined' != typeof layout["premium_parallax_layer_hor_pos" + deviceSuffix] ? layout["premium_parallax_layer_hor_pos" + deviceSuffix].size : layout["premium_parallax_layer_hor_pos"].size;

                        $layer.css('left', left + '%');
                    }

                    if ('custom' === layout.premium_parallax_layer_ver) {

                        var top = 'undefined' != typeof layout["premium_parallax_layer_ver_pos" + deviceSuffix] ? layout["premium_parallax_layer_ver_pos" + deviceSuffix].size : layout["premium_parallax_layer_ver_pos"].size;

                        $layer.css('top', top + '%');

                    }

                    if (settings.devices.includes(currentDevice)) {
                        if ('yes' === layout['premium_parallax_layer_scroll']) {
                            if ('yes' === layout['premium_parallax_layer_scroll_hor']) {

                                $layer.attr({
                                    'data-parallax-scroll': 'yes',
                                    'data-parallax-hscroll': 'yes',
                                    'data-parallax-hscroll_speed': layout['premium_parallax_layer_speed_hor']['size'],
                                    'data-parallax-hscroll_start': layout['premium_parallax_layer_view_hor']['sizes']['start'],
                                    'data-parallax-hscroll_end': layout['premium_parallax_layer_view_hor']['sizes']['end'],
                                    'data-parallax-hscroll_direction': layout['premium_parallax_layer_direction_hor']
                                });
                            }

                            if ('yes' === layout['premium_parallax_layer_scroll_ver']) {
                                $layer.attr({
                                    'data-parallax-scroll': 'yes',
                                    'data-parallax-vscroll': 'yes',
                                    'data-parallax-speed': layout['premium_parallax_layer_speed']['size'],
                                    'data-parallax-start': layout['premium_parallax_layer_view']['sizes']['start'],
                                    'data-parallax-end': layout['premium_parallax_layer_view']['sizes']['end'],
                                    'data-parallax-direction': layout['premium_parallax_layer_direction']
                                });
                            }
                        }
                    }


                });

                target.imagesLoaded().done(function () {
                    target.trigger("paParallaxLoaded");
                });

                window.PremiumSvgDrawerHandler(target, $, settings.speed);

                function getLayerHTML(layer) {

                    var html = '',
                        imgID = '' != layer.premium_parallax_layer_id ? 'id="' + layer.premium_parallax_layer_id + '"' : '';

                    if ('img' === layer.layer_type) {

                        if (null !== layer["premium_parallax_layer_image"]["url"] && "" !== layer["premium_parallax_layer_image"]["url"]) {

                            var backgroundImage = layer["premium_parallax_layer_image"]["url"],
                                alt = layer['alt'];

                            html = '<img ' + imgID + ' class="premium-parallax-img" src="' + backgroundImage + '" alt="' + alt + '">';

                        }

                    } else {

                        var attributes = imgID + ' class="' + ("yes" === layer.draw_svg ? "premium-svg-drawer" : "premium-svg-nodraw") + '"';

                        if ("yes" === layer.draw_svg) {
                            attributes += 'data-svg-reverse="' + layer.svg_reverse + '"';
                            attributes += 'data-svg-loop="' + layer.svg_loop + '"';
                            attributes += 'data-svg-sync="' + layer.svg_sync + '"';
                            attributes += 'data-svg-hover="' + layer.svg_hover + '"';
                            attributes += 'data-svg-restart="' + layer.restart_draw + '"';
                            attributes += 'data-svg-fill="' + layer.svg_color + '"';
                            attributes += 'data-svg-stroke="' + layer.svg_stroke + '"';
                            attributes += 'data-svg-frames="' + layer.frames + '"';
                            attributes += 'data-svg-yoyo="' + layer.svg_yoyo + '"';
                            attributes += 'data-svg-point="' + (layer.svg_reverse ? layer.end_point.size : layer.start_point.size) + '"';
                        }

                        html = '<div ' + attributes + '>' + layer.premium_parallax_layer_svg + '</div>';
                    }

                    return html;
                }

                if (-1 !== settings.devices.indexOf(currentDevice)) {
                    target.find('.premium-parallax-layer').each(function (index, layer) {
                        var data = $(layer).data();
                        if ('yes' === data.parallaxScroll) {
                            var effects = [],
                                vScrollSettings = {},
                                hScrollSettings = {},
                                settings = {}

                            if ('yes' === data.parallaxVscroll) {

                                effects.push('translateY');
                                vScrollSettings = {
                                    speed: data.parallaxSpeed,
                                    direction: data.parallaxDirection,
                                    range: {
                                        start: data.parallaxStart,
                                        end: data.parallaxEnd
                                    }
                                };
                            }

                            if ('yes' === data.parallaxHscroll) {
                                effects.push('translateX');
                                hScrollSettings = {
                                    speed: data.parallaxHscroll_speed,
                                    direction: data.parallaxHscroll_direction,
                                    range: {
                                        start: data.parallaxHscroll_start,
                                        end: data.parallaxHscroll_end
                                    }
                                };

                            }

                            settings = {
                                elType: 'SECTION',
                                vscroll: vScrollSettings,
                                hscroll: hScrollSettings,
                                effects: effects
                            },

                                instance = null;

                            instance = new premiumParallaxEffects(layer, settings);

                            instance.init();
                        }
                    });
                }

                target.mousemove(function (e) {
                    $(this)
                        .find('.premium-parallax-layer[data-parallax="true"]')
                        .each(function () {
                            var $this = $(this),
                                resistance = $(this).data("rate");
                            TweenLite.to($this, 0.2, {
                                x: -((e.clientX - window.innerWidth / 2) / resistance),
                                y: -((e.clientY - window.innerHeight / 2) / resistance)
                            });
                        });
                });
            }

        };

        elementorFrontend.hooks.addAction("frontend/element_ready/section", PremiumParallaxHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/column", PremiumParallaxHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/container", PremiumParallaxHandler);


    });

})(jQuery);