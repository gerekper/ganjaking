!(function (e, t, o, n) {
    (e.MA_ToggleElement = function (n, i) {
        var l = {
                wrapper: null,
                watchControls: !1,
                speed: 0.3,
                active: 1,
                indicatorColor: "rgba(0, 0, 0, 1)",
                controlItemClass: ".jltma-toggle-content-controls__item",
                indicatorClass: ".jltma-toggle-content-indicator",
                elementClass: ".jltma-toggle-content-element",
                onBeforeToggle: function () {},
                onAfterToggle: function () {},
            },
            s = this;
        s.opts = {};
        var a = e(t),
            g = (e(o), e(n)),
            r = null,
            c = null,
            d = null,
            u = null,
            f = null,
            h = 0,
            m = null;
        (s.init = function () {
            (s.opts = e.extend({}, l, i)), s._construct();
        }),
            (s._construct = function () {
                (r = g.find(s.opts.controlItemClass)), (c = g.find(s.opts.indicatorClass)), (d = g.find(s.opts.elementClass)), (m = r.length), s.opts.active > 0 && s.opts.active <= m && (h = s.opts.active - 1), s.setup(), s.events();
            }),
            (s.setup = function () {
                (u = r.eq(h)), this.opts.watchControls && this.adjust(), this.goTo(h), g.addClass("jltma--is-ready"), a.trigger("jltma/toggle-content/ready");
            }),
            (s.events = function () {
                r.on("click", this.onClick);
            }),
            (s.onClick = function (t) {
                var o = e(t.target).closest(".jltma-toggle-content-controls__item"),
                    n = o.index(),
                    i = n < m - 1 ? n + 1 : 0;
                (h = h === n ? i : n), s.goTo(h);
            }),
            (s.goTo = function (e) {
                s.opts.onBeforeToggle(),
                    (u = r.eq(e)),
                    (f = d.eq(e)),
                    d.hide(),
                    f.show(),
                    r.data("active", !1),
                    r.eq(e).data("active", !0),
                    this.adjust(),
                    r.removeClass("jltma--is-active"),
                    u.addClass("jltma--is-active"),
                    s.opts.onAfterToggle(),
                    a.trigger("jltma/toggle-content/toggle"),
                    a.trigger("resize");
            }),
            (s.adjust = function () {
                var e = u.get(0).offsetLeft,
                    t = u.get(0).offsetTop,
                    o = u.outerWidth(),
                    n = u.outerHeight(),
                    i = void 0 !== u.data("color") ? u.data("color") : s.opts.indicatorColor,
                    l = { left: e, top: t, width: o, height: n };
                i && (l.backgroundColor = i), TweenMax.to(c, s.opts.speed, l);
            }),
            (s.destroy = function () {}),
            s.init();
    }),
        (e.fn.MA_ToggleElement = function (t) {
            return this.each(function () {
                if (
                    ((e.fn.MA_ToggleElement.destroy = function () {
                        void 0 !== o && (e(this).data("toggle-content").destroy(), e(this).removeData("toggle-content"));
                    }),
                    void 0 === e(this).data("toggle-content"))
                ) {
                    var o = new e.MA_ToggleElement(this, t);
                    e(this).data("toggle-content", o);
                }
            });
        });
})(jQuery, window, document);
