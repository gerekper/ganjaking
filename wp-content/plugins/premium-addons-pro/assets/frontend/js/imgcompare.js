! function (e) {
    e.fn.twentytwenty = function (t) {
        t = e.extend({
            default_offset_pct: .5,
            orientation: "horizontal",
            switch_before_label: !0,
            before_label: "Before",
            switch_after_label: !0,
            after_label: "After",
            no_overlay: !1,
            move_slider_on_hover: !1,
            move_with_handle_only: !0,
            click_to_move: !1,
            show_drag: !0,
            show_sep: !0,
            horbeforePos: "middle",
            horafterPos: "middle",
            verbeforePos: "center",
            verafterPos: "center"
        }, t);
        return this.each(function () {
            var n = t.default_offset_pct,
                a = e(this),
                r = t.orientation,
                i = "vertical" === r ? "down" : "left",
                s = "vertical" === r ? "up" : "right";
            a.wrap("<div class='premium-twentytwenty-wrapper premium-twentytwenty-" + r + "'></div>"), t.no_overlay ? a.append("<div class='premium-twentytwenty-overlay premium-twentytwenty-hide'></div>") : a.append("<div class='premium-twentytwenty-overlay premium-twentytwenty-show'></div>");
            var l = a.find("img:first"),
                o = a.find("img:last");
            t.show_sep ? a.append("<div class='premium-twentytwenty-handle'></div>") : a.append("<div class='premium-twentytwenty-handle premium-twentytwenty-hide'></div>");
            var p = a.find(".premium-twentytwenty-handle");
            p.append("<span class='premium-twentytwenty-" + i + "-arrow'></span>"), p.append("<span class='premium-twentytwenty-" + s + "-arrow'></span>"), a.addClass("premium-twentytwenty-container"), l.addClass("premium-twentytwenty-before"), o.addClass("premium-twentytwenty-after"), t.show_drag || p.css("opacity", "0");
            var d = a.find(".premium-twentytwenty-overlay");
            if (t.switch_before_label) {
                var m = "<div class='premium-twentytwenty-before-label premium-twentytwenty-before-label-" + t.horbeforePos + " premium-twentytwenty-before-label-" + t.verbeforePos + "'><span>" + t.before_label + "</span></div>";
                d.append(m)
            }
            if (t.switch_after_label) {
                var c = "<div class='premium-twentytwenty-after-label  premium-twentytwenty-after-label-" + t.horafterPos + " premium-twentytwenty-after-label-" + t.verafterPos + "'><span>" + t.after_label + "</span></div>";
                d.append(c)
            }
            var w = function (t) {
                var n, i, s, d, m = (n = t, i = l.width(), s = l.height(), {
                    w: i + "px",
                    h: s + "px",
                    cw: n * i + "px",
                    ch: n * s + "px"
                }),
                    c = e(a).find(".premium-twentytwenty-before-label"),
                    w = e(a).find(".premium-twentytwenty-after-label");
                if (p.css("vertical" === r ? "top" : "left", "vertical" === r ? m.ch : m.cw), "horizontal" === r) {
                    var f = void 0 !== c.css("left") ? parseInt(c.css("left").replace(/px/, "")) : "",
                        v = parseInt(c.outerWidth()),
                        h = void 0 !== w.css("left") ? parseInt(w.css("left").replace(/px/, "")) : "";
                    (y = parseInt(p.css("left").replace(/px/, ""))) < f + v ? c.addClass("premium-label-hidden") : c.removeClass("premium-label-hidden"), y > h ? w.addClass("premium-label-hidden") : w.removeClass("premium-label-hidden")
                } else {
                    var y;
                    f = void 0 !== c.css("top") ? parseInt(c.css("top").replace(/px/, "")) : "",
                        v = parseInt(c.outerHeight()),
                        h = void 0 !== w.css("top") ? parseInt(w.css("top").replace(/px/, "")) : "";
                    (y = parseInt(p.css("top").replace(/px/, ""))) < f + v ? c.addClass("premium-label-hidden") : c.removeClass("premium-label-hidden"), y > h && !(h < 0) ? w.addClass("premium-label-hidden") : w.removeClass("premium-label-hidden")
                }
                d = m, "vertical" === r ? (l.css("clip", "rect(0," + d.w + "," + d.ch + ",0)"), o.css("clip", "rect(" + d.ch + "," + d.w + "," + d.h + ",0)")) : (l.css("clip", "rect(0," + d.cw + "," + d.h + ",0)"), o.css("clip", "rect(0," + d.w + "," + d.h + "," + d.cw + ")")), a.css("height", d.h)
            },
                f = function (e, t) {
                    var n, a, i;
                    return n = "vertical" === r ? (t - h) / u : (e - v) / y, a = 0, i = 1, Math.max(a, Math.min(i, n))
                };
            e(window).on("resize.twentytwenty", function (e) {
                w(n)
            });
            var v = 0,
                h = 0,
                y = 0,
                u = 0,
                _ = function (e) {
                    (e.distX > e.distY && e.distX < -e.distY || e.distX < e.distY && e.distX > -e.distY) && "vertical" !== r ? e.preventDefault() : (e.distX < e.distY && e.distX < -e.distY || e.distX > e.distY && e.distX > -e.distY) && "vertical" === r && e.preventDefault(), a.addClass("active"), v = a.offset().left, h = a.offset().top, y = l.width(), u = l.height()
                },
                b = function (e) {
                    a.hasClass("active") && (n = f(e.pageX, e.pageY), w(n))
                },
                g = function () {
                    a.removeClass("active")
                },
                C = t.move_with_handle_only ? p : a;
            C.on("movestart", _), C.on("move", b), C.on("moveend", g), t.move_slider_on_hover && (a.on("mouseenter", _), a.on("mousemove", b), a.on("mouseleave", g)), p.on("touchmove", function (e) {
                e.preventDefault()
            }), a.find("img").on("mousedown", function (e) {
                e.preventDefault()
            }), t.click_to_move && a.on("click", function (e) {
                v = a.offset().left, h = a.offset().top, y = l.width(), u = l.height(), n = f(e.pageX, e.pageY), w(n)
            }), e(window).trigger("resize.twentytwenty")
        })
    }
}(jQuery);