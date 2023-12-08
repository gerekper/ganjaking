(function($) {
	"use strict";
	var WidgetTableContentHandler = function(e, a) {
        if (void 0 !== e) {
            e.find(".plus-table-wrapper");
            var n = e.data("id")
              , l = e.find(".plus-table")
              , t = e.find("#plus-table-id-" + n)
              , d = !1
              , r = !1
              , i = !1;
            if (0 != t.length) {
                "yes" == a(".elementor-element-" + n + " #" + t[0].id).data("searchable") && (d = !0),
                "yes" == a(".elementor-element-" + n + " #" + t[0].id).data("show-entry") && (r = !0),
                "yes" == a(".elementor-element-" + n + " #" + t[0].id).data("sort-table") && (a(".elementor-element-" + n + " #" + t[0].id + " th").css({
                    cursor: "pointer"
                }),
                i = !0);
                var o = a(".elementor-element-" + n + " #" + t[0].id).data("searchable-label");
                if (d || r || i)
                    a("#" + t[0].id).DataTable({
                        paging: r,
                        searching: d,
                        ordering: i,
                        info: !1,
						"pagingType": "full_numbers",
						"lengthMenu": [[5, 10, 15, -1], [5, 10, 15, "All"]],
                        oLanguage: {
                            sSearch: o
                        }
                    }),
                    e.find(".dataTables_length").addClass("plus-tbl-entry-wrapper plus-table-info"),
                    e.find(".dataTables_filter").addClass("plus-tbl-search-wrapper plus-table-info"),
                    e.find(".plus-table-info").wrapAll('<div class="plus-advance-heading"></div>');
                window.addEventListener("load", s),
                window.addEventListener("resize", s)
            }
        }
        function s() {
            a(window).width() > 767 ? (a(l).addClass("plus-column-rules"),
            a(l).removeClass("plus-no-column-rules")) : (a(l).removeClass("plus-column-rules"),
            a(l).addClass("plus-no-column-rules"))
        }
    };
	$(window).on('elementor/frontend/init', function () {		
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-table.default', WidgetTableContentHandler);
	});
})(jQuery);