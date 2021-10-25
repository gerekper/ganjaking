! function (e) {
    "function" == typeof define && define.amd ? define([], e) : "undefined" != typeof module && null !== module && module.exports ? module.exports = e : e()
}(function () {
    var e = Object.assign || window.jQuery && jQuery.extend,
        t = 8,
        n = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame || function (e, t) {
            return window.setTimeout(function () {
                e()
            }, 25)
        };
    ! function () {
        if ("function" == typeof window.CustomEvent) return !1;

        function e(e, t) {
            t = t || {
                bubbles: !1,
                cancelable: !1,
                detail: void 0
            };
            var n = document.createEvent("CustomEvent");
            return n.initCustomEvent(e, t.bubbles, t.cancelable, t.detail), n
        }
        e.prototype = window.Event.prototype, window.CustomEvent = e
    }();
    var o = {
            textarea: !0,
            input: !0,
            select: !0,
            button: !0
        },
        i = {
            move: "mousemove",
            cancel: "mouseup dragstart",
            end: "mouseup"
        },
        a = {
            move: "touchmove",
            cancel: "touchend",
            end: "touchend"
        },
        c = /\s+/,
        u = {
            bubbles: !0,
            cancelable: !0
        },
        r = "function" == typeof Symbol ? Symbol("events") : {};

    function d(e) {
        return e[r] || (e[r] = {})
    }

    function m(e, t, n, o, i) {
        t = t.split(c);
        var a, u = d(e),
            r = t.length;

        function m(e) {
            n(e, o)
        }
        for (; r--;)(u[a = t[r]] || (u[a] = [])).push([n, m]), e.addEventListener(a, m)
    }

    function v(e, t, n, o) {
        t = t.split(c);
        var i, a, u, r = d(e),
            m = t.length;
        if (r)
            for (; m--;)
                if (a = r[i = t[m]])
                    for (u = a.length; u--;) a[u][0] === n && (e.removeEventListener(i, a[u][1]), a.splice(u, 1))
    }

    function f(t, n, o) {
        var i = new CustomEvent(n, u);
        o && e(i, o), t.dispatchEvent(i)
    }

    function s() {}

    function l(e) {
        e.preventDefault()
    }

    function p(e, t) {
        var n, o;
        if (e.identifiedTouch) return e.identifiedTouch(t);
        for (n = -1, o = e.length; ++n < o;)
            if (e[n].identifier === t) return e[n]
    }

    function g(e, t) {
        var n = p(e.changedTouches, t.identifier);
        if (n && (n.pageX !== t.pageX || n.pageY !== t.pageY)) return n
    }

    function h(e, t) {
        w(e, t, e, Y)
    }

    function X(e, t) {
        Y()
    }

    function Y() {
        v(document, i.move, h), v(document, i.cancel, X)
    }

    function y(e) {
        v(document, a.move, e.touchmove), v(document, a.cancel, e.touchend)
    }

    function w(e, n, o, i) {
        var a, c, u, r, d, m, v, l, p, g = o.pageX - n.pageX,
            h = o.pageY - n.pageY;
        g * g + h * h < t * t || (c = n, u = o, r = g, d = h, m = i, v = (a = e).targetTouches, l = a.timeStamp - c.timeStamp, p = {
            altKey: a.altKey,
            ctrlKey: a.ctrlKey,
            shiftKey: a.shiftKey,
            startX: c.pageX,
            startY: c.pageY,
            distX: r,
            distY: d,
            deltaX: r,
            deltaY: d,
            pageX: u.pageX,
            pageY: u.pageY,
            velocityX: r / l,
            velocityY: d / l,
            identifier: c.identifier,
            targetTouches: v,
            finger: v ? v.length : 1,
            enableMove: function () {
                this.moveEnabled = !0, this.enableMove = s, a.preventDefault()
            }
        }, f(c.target, "movestart", p), m(c))
    }

    function b(e, t) {
        var n = t.timer;
        t.touch = e, t.timeStamp = e.timeStamp, n.kick()
    }

    function T(e, t) {
        var n = t.target,
            o = t.event,
            a = t.timer;
        v(document, i.move, b), v(document, i.end, T), S(n, o, a, function () {
            setTimeout(function () {
                v(n, "click", l)
            }, 0)
        })
    }

    function E(e, t) {
        var n, o = t.target,
            i = t.event,
            c = t.timer;
        p(e.changedTouches, i.identifier) && (n = t, v(document, a.move, n.activeTouchmove), v(document, a.end, n.activeTouchend), S(o, i, c))
    }

    function S(e, t, n, o) {
        n.end(function () {
            return f(e, "moveend", t), o && o()
        })
    }
    if (m(document, "mousedown", function (e) {
            var t;
            1 !== (t = e).which || t.ctrlKey || t.altKey || o[e.target.tagName.toLowerCase()] || (m(document, i.move, h, e), m(document, i.cancel, X, e))
        }), m(document, "touchstart", function (e) {
            if (!o[e.target.tagName.toLowerCase()]) {
                var t = e.changedTouches[0],
                    n = {
                        target: t.target,
                        pageX: t.pageX,
                        pageY: t.pageY,
                        identifier: t.identifier,
                        touchmove: function (e, t) {
                            var n, o, i;
                            (i = g(n = e, o = t)) && w(n, o, i, y)
                        },
                        touchend: function (e, t) {
                            var n;
                            n = t, p(e.changedTouches, n.identifier) && y(n)
                        }
                    };
                m(document, a.move, n.touchmove, n), m(document, a.cancel, n.touchend, n)
            }
        }), m(document, "movestart", function (e) {
            if (!e.defaultPrevented && e.moveEnabled) {
                var t = {
                        startX: e.startX,
                        startY: e.startY,
                        pageX: e.pageX,
                        pageY: e.pageY,
                        distX: e.distX,
                        distY: e.distY,
                        deltaX: e.deltaX,
                        deltaY: e.deltaY,
                        velocityX: e.velocityX,
                        velocityY: e.velocityY,
                        identifier: e.identifier,
                        targetTouches: e.targetTouches,
                        finger: e.finger
                    },
                    o = {
                        target: e.target,
                        event: t,
                        timer: new function (e) {
                            var t = e,
                                o = !1,
                                i = !1;

                            function a(e) {
                                o ? (t(), n(a), i = !0, o = !1) : i = !1
                            }
                            this.kick = function (e) {
                                o = !0, i || a()
                            }, this.end = function (e) {
                                var n = t;
                                e && (i ? (t = o ? function () {
                                    n(), e()
                                } : e, o = !0) : e())
                            }
                        }(function (e) {
                            var n, i, a, c;
                            n = t, i = o.touch, a = o.timeStamp, c = a - n.timeStamp, n.distX = i.pageX - n.startX, n.distY = i.pageY - n.startY, n.deltaX = i.pageX - n.pageX, n.deltaY = i.pageY - n.pageY, n.velocityX = .3 * n.velocityX + .7 * n.deltaX / c, n.velocityY = .3 * n.velocityY + .7 * n.deltaY / c, n.pageX = i.pageX, n.pageY = i.pageY, f(o.target, "move", t)
                        }),
                        touch: void 0,
                        timeStamp: e.timeStamp
                    };
                void 0 === e.identifier ? (m(e.target, "click", l), m(document, i.move, b, o), m(document, i.end, T, o)) : (o.activeTouchmove = function (e, t) {
                    var n, o, i, a, c;
                    n = e, i = (o = t).event, a = o.timer, (c = g(n, i)) && (n.preventDefault(), i.targetTouches = n.targetTouches, o.touch = c, o.timeStamp = n.timeStamp, a.kick())
                }, o.activeTouchend = function (e, t) {
                    E(e, t)
                }, m(document, a.move, o.activeTouchmove, o), m(document, a.end, o.activeTouchend, o))
            }
        }), window.jQuery) {
        var k = "startX startY pageX pageY distX distY deltaX deltaY velocityX velocityY".split(" ");
        jQuery.event.special.movestart = {
            setup: function () {
                return m(this, "movestart", K), !1
            },
            teardown: function () {
                return v(this, "movestart", K), !1
            },
            add: Q
        }, jQuery.event.special.move = {
            setup: function () {
                return m(this, "movestart", j), !1
            },
            teardown: function () {
                return v(this, "movestart", j), !1
            },
            add: Q
        }, jQuery.event.special.moveend = {
            setup: function () {
                return m(this, "movestart", C), !1
            },
            teardown: function () {
                return v(this, "movestart", C), !1
            },
            add: Q
        }
    }

    function K(e) {
        e.enableMove()
    }

    function j(e) {
        e.enableMove()
    }

    function C(e) {
        e.enableMove()
    }

    function Q(e) {
        var t = e.handler;
        e.handler = function (e) {
            for (var n, o = k.length; o--;) e[n = k[o]] = e.originalEvent[n];
            t.apply(this, arguments)
        }
    }
});