! function ($) {
    $.extend({
        tablesorter: new function () {
            var parsers = [],
                widgets = [];

            function benchmark(e, t) {
                log(e + "," + ((new Date).getTime() - t.getTime()) + "ms")
            }

            function log(e) {
                "undefined" != typeof console && void 0 !== console.debug ? console.log(e) : alert(e)
            }

            function buildParserCache(e, t) {
                if (e.config.debug) var r = "";
                if (0 != e.tBodies.length) {
                    var n = e.tBodies[0].rows;
                    if (n[0])
                        for (var o = [], i = n[0].cells.length, a = 0; a < i; a++) {
                            var s = !1;
                            $.metadata && $(t[a]).metadata() && $(t[a]).metadata().sorter ? s = getParserById($(t[a]).metadata().sorter) : e.config.headers[a] && e.config.headers[a].sorter && (s = getParserById(e.config.headers[a].sorter)), s || (s = detectParserForColumn(e, n, -1, a)), e.config.debug && (r += "column:" + a + " parser:" + s.id + "\n"), o.push(s)
                        }
                    return e.config.debug && log(r), o
                }
            }

            function detectParserForColumn(e, t, r, n) {
                for (var o = parsers.length, i = !1, a = !1, s = !0;
                    "" == a && s;) t[++r] ? (i = getNodeFromRowAndCellIndex(t, r, n), a = trimAndGetNodeText(e.config, i), e.config.debug && log("Checking if value was empty on row:" + r)) : s = !1;
                for (var d = 1; d < o; d++)
                    if (parsers[d].is(a, e, i)) return parsers[d];
                return parsers[0]
            }

            function getNodeFromRowAndCellIndex(e, t, r) {
                return e[t].cells[r]
            }

            function trimAndGetNodeText(e, t) {
                return $.trim(getElementText(e, t))
            }

            function getParserById(e) {
                for (var t = parsers.length, r = 0; r < t; r++)
                    if (parsers[r].id.toLowerCase() == e.toLowerCase()) return parsers[r];
                return !1
            }

            function buildCache(e) {
                if (e.config.debug) var t = new Date;
                for (var r = e.tBodies[0] && e.tBodies[0].rows.length || 0, n = e.tBodies[0].rows[0] && e.tBodies[0].rows[0].cells.length || 0, o = e.config.parsers, i = {
                    row: [],
                    normalized: []
                }, a = 0; a < r; ++a) {
                    var s = $(e.tBodies[0].rows[a]),
                        d = [];
                    if (s.hasClass(e.config.cssChildRow)) i.row[i.row.length - 1] = i.row[i.row.length - 1].add(s);
                    else {
                        i.row.push(s);
                        for (var c = 0; c < n; ++c) d.push(o[c].format(getElementText(e.config, s[0].cells[c]), e, s[0].cells[c]));
                        d.push(i.normalized.length), i.normalized.push(d), d = null
                    }
                }
                return e.config.debug && benchmark("Building cache for " + r + " rows:", t), i
            }

            function getElementText(e, t) {
                return t ? (e.supportsTextContent || (e.supportsTextContent = t.textContent || !1), "simple" == e.textExtraction ? e.supportsTextContent ? t.textContent : t.childNodes[0] && t.childNodes[0].hasChildNodes() ? t.childNodes[0].innerHTML : t.innerHTML : "function" == typeof e.textExtraction ? e.textExtraction(t) : $(t).text()) : ""
            }

            function appendToTable(e, t) {
                if (e.config.debug) var r = new Date;
                for (var n = t, o = n.row, i = n.normalized, a = i.length, s = i[0].length - 1, d = $(e.tBodies[0]), c = [], l = 0; l < a; l++) {
                    var u = i[l][s];
                    if (c.push(o[u]), !e.config.appender)
                        for (var f = o[u].length, h = 0; h < f; h++) d[0].appendChild(o[u][h])
                }
                e.config.appender && e.config.appender(e, c), c = null, e.config.debug && benchmark("Rebuilt table:", r), applyWidget(e), setTimeout(function () {
                    $(e).trigger("sortEnd")
                }, 0)
            }

            function buildHeaders(e) {
                if (e.config.debug) var t = new Date;
                $.metadata;
                var r = computeTableHeaderCellIndexes(e);
                return $tableHeaders = $(e.config.selectorHeaders, e).each(function (t) {
                    if (this.column = r[this.parentNode.rowIndex + "-" + this.cellIndex], this.order = formatSortingOrder(e.config.sortInitialOrder), this.count = this.order, (checkHeaderMetadata(this) || checkHeaderOptions(e, t)) && (this.sortDisabled = !0), checkHeaderOptionsSortingLocked(e, t) && (this.order = this.lockedOrder = checkHeaderOptionsSortingLocked(e, t)), !this.sortDisabled) {
                        var n = $(this).addClass(e.config.cssHeader);
                        e.config.onRenderHeader && e.config.onRenderHeader.apply(n)
                    }
                    e.config.headerList[t] = this
                }), e.config.debug && (benchmark("Built headers:", t), log($tableHeaders)), $tableHeaders
            }

            function computeTableHeaderCellIndexes(e) {
                for (var t = [], r = {}, n = e.getElementsByTagName("THEAD")[0].getElementsByTagName("TR"), o = 0; o < n.length; o++)
                    for (var i = n[o].cells, a = 0; a < i.length; a++) {
                        var s, d = i[a],
                            c = d.parentNode.rowIndex,
                            l = c + "-" + d.cellIndex,
                            u = d.rowSpan || 1,
                            f = d.colSpan || 1;
                        void 0 === t[c] && (t[c] = []);
                        for (var h = 0; h < t[c].length + 1; h++)
                            if (void 0 === t[c][h]) {
                                s = h;
                                break
                            } r[l] = s;
                        for (h = c; h < c + u; h++) {
                            void 0 === t[h] && (t[h] = []);
                            for (var g = t[h], m = s; m < s + f; m++) g[m] = "x"
                        }
                    }
                return r
            }

            function checkCellColSpan(e, t, r) {
                for (var n = [], o = e.tHead.rows, i = o[r].cells, a = 0; a < i.length; a++) {
                    var s = i[a];
                    s.colSpan > 1 ? n = n.concat(checkCellColSpan(e, headerArr, r++)) : (1 == e.tHead.length || s.rowSpan > 1 || !o[r + 1]) && n.push(s)
                }
                return n
            }

            function checkHeaderMetadata(e) {
                return !(!$.metadata || !1 !== $(e).metadata().sorter)
            }

            function checkHeaderOptions(e, t) {
                return !(!e.config.headers[t] || !1 !== e.config.headers[t].sorter)
            }

            function checkHeaderOptionsSortingLocked(e, t) {
                return !(!e.config.headers[t] || !e.config.headers[t].lockedOrder) && e.config.headers[t].lockedOrder
            }

            function applyWidget(e) {
                for (var t = e.config.widgets, r = t.length, n = 0; n < r; n++) getWidgetById(t[n]).format(e)
            }

            function getWidgetById(e) {
                for (var t = widgets.length, r = 0; r < t; r++)
                    if (widgets[r].id.toLowerCase() == e.toLowerCase()) return widgets[r]
            }

            function formatSortingOrder(e) {
                return "Number" != typeof e ? "desc" == e.toLowerCase() ? 1 : 0 : 1 == e ? 1 : 0
            }

            function isValueInArray(e, t) {
                for (var r = t.length, n = 0; n < r; n++)
                    if (t[n][0] == e) return !0;
                return !1
            }

            function setHeadersCss(e, t, r, n) {
                t.removeClass(n[0]).removeClass(n[1]);
                var o = [];
                t.each(function (e) {
                    this.sortDisabled || (o[this.column] = $(this))
                });
                for (var i = r.length, a = 0; a < i; a++) o[r[a][0]].addClass(n[r[a][1]])
            }

            function fixColumnWidth(e, t) {
                if (e.config.widthFixed) {
                    var r = $("<colgroup>");
                    $("tr:first td", e.tBodies[0]).each(function () {
                        r.append($("<col>").css("width", $(this).width()))
                    }), $(e).prepend(r)
                }
            }

            function updateHeaderSortCount(e, t) {
                for (var r = e.config, n = t.length, o = 0; o < n; o++) {
                    var i = t[o],
                        a = r.headerList[i[0]];
                    a.count = i[1], a.count++
                }
            }

            function multisort(table, sortList, cache) {
                if (table.config.debug) var sortTime = new Date;
                for (var dynamicExp = "var sortWrapper = function(a,b) {", l = sortList.length, i = 0; i < l; i++) {
                    var c = sortList[i][0],
                        order = sortList[i][1],
                        s = "text" == table.config.parsers[c].type ? makeSortFunction("text", 0 == order ? "asc" : "desc", c) : makeSortFunction("numeric", 0 == order ? "asc" : "desc", c),
                        e = "e" + i;
                    dynamicExp += "var " + e + " = " + s, dynamicExp += "if(" + e + ") { return " + e + "; } ", dynamicExp += "else { "
                }
                var orgOrderCol = cache.normalized[0].length - 1;
                dynamicExp += "return a[" + orgOrderCol + "]-b[" + orgOrderCol + "];";
                for (var i = 0; i < l; i++) dynamicExp += "}; ";
                return dynamicExp += "return 0; ", dynamicExp += "}; ", table.config.debug && benchmark("Evaling expression:" + dynamicExp, new Date), eval(dynamicExp), cache.normalized.sort(sortWrapper), table.config.debug && benchmark("Sorting on " + sortList.toString() + " and dir " + order + " time:", sortTime), cache
            }

            function makeSortFunction(e, t, r) {
                var n = "a[" + r + "]",
                    o = "b[" + r + "]";
                return "text" == e && "asc" == t ? "(" + n + " == " + o + " ? 0 : (" + n + " === null ? Number.POSITIVE_INFINITY : (" + o + " === null ? Number.NEGATIVE_INFINITY : (" + n + " < " + o + ") ? -1 : 1 )));" : "text" == e && "desc" == t ? "(" + n + " == " + o + " ? 0 : (" + n + " === null ? Number.POSITIVE_INFINITY : (" + o + " === null ? Number.NEGATIVE_INFINITY : (" + o + " < " + n + ") ? -1 : 1 )));" : "numeric" == e && "asc" == t ? "(" + n + " === null && " + o + " === null) ? 0 :(" + n + " === null ? Number.POSITIVE_INFINITY : (" + o + " === null ? Number.NEGATIVE_INFINITY : " + n + " - " + o + "));" : "numeric" == e && "desc" == t ? "(" + n + " === null && " + o + " === null) ? 0 :(" + n + " === null ? Number.POSITIVE_INFINITY : (" + o + " === null ? Number.NEGATIVE_INFINITY : " + o + " - " + n + "));" : void 0
            }

            function makeSortText(e) {
                return "((a[" + e + "] < b[" + e + "]) ? -1 : ((a[" + e + "] > b[" + e + "]) ? 1 : 0));"
            }

            function makeSortTextDesc(e) {
                return "((b[" + e + "] < a[" + e + "]) ? -1 : ((b[" + e + "] > a[" + e + "]) ? 1 : 0));"
            }

            function makeSortNumeric(e) {
                return "a[" + e + "]-b[" + e + "];"
            }

            function makeSortNumericDesc(e) {
                return "b[" + e + "]-a[" + e + "];"
            }

            function sortText(e, t) {
                return table.config.sortLocaleCompare ? e.localeCompare(t) : e < t ? -1 : e > t ? 1 : 0
            }

            function sortTextDesc(e, t) {
                return table.config.sortLocaleCompare ? t.localeCompare(e) : t < e ? -1 : t > e ? 1 : 0
            }

            function sortNumeric(e, t) {
                return e - t
            }

            function sortNumericDesc(e, t) {
                return t - e
            }

            function getCachedSortType(e, t) {
                return e[t].type
            }
            this.defaults = {
                cssHeader: "header",
                cssAsc: "headerSortUp",
                cssDesc: "headerSortDown",
                cssChildRow: "expand-child",
                sortInitialOrder: "asc",
                sortMultiSortKey: "shiftKey",
                sortForce: null,
                sortAppend: null,
                sortLocaleCompare: !0,
                textExtraction: "simple",
                parsers: {},
                widgets: [],
                widgetZebra: {
                    css: ["even", "odd"]
                },
                headers: {},
                widthFixed: !1,
                cancelSelection: !0,
                sortList: [],
                headerList: [],
                dateFormat: "us",
                decimal: "/.|,/g",
                onRenderHeader: null,
                selectorHeaders: "thead th",
                debug: !1
            }, this.benchmark = benchmark, this.construct = function (e) {
                return this.each(function () {
                    if (this.tHead && this.tBodies) {
                        var t, r, n, o;
                        this.config = {}, o = $.extend(this.config, $.tablesorter.defaults, e), t = $(this), $.data(this, "tablesorter", o), r = buildHeaders(this), this.config.parsers = buildParserCache(this, r), n = buildCache(this);
                        var i = [o.cssDesc, o.cssAsc];
                        fixColumnWidth(this), r.click(function (e) {
                            var a = t[0].tBodies[0] && t[0].tBodies[0].rows.length || 0;
                            if (!this.sortDisabled && a > 0) {
                                t.trigger("sortStart");
                                $(this);
                                var s = this.column;
                                if (this.order = this.count++ % 2, this.lockedOrder && (this.order = this.lockedOrder), e[o.sortMultiSortKey])
                                    if (isValueInArray(s, o.sortList))
                                        for (u = 0; u < o.sortList.length; u++) {
                                            var d = o.sortList[u],
                                                c = o.headerList[d[0]];
                                            d[0] == s && (c.count = d[1], c.count++, d[1] = c.count % 2)
                                        } else o.sortList.push([s, this.order]);
                                else {
                                    if (o.sortList = [], null != o.sortForce)
                                        for (var l = o.sortForce, u = 0; u < l.length; u++) l[u][0] != s && o.sortList.push(l[u]);
                                    o.sortList.push([s, this.order])
                                } return setTimeout(function () {
                                    setHeadersCss(t[0], r, o.sortList, i), appendToTable(t[0], multisort(t[0], o.sortList, n))
                                }, 1), !1
                            }
                        }).mousedown(function () {
                            if (o.cancelSelection) return this.onselectstart = function () {
                                return !1
                            }, !1
                        }), t.bind("update", function () {
                            var e = this;
                            setTimeout(function () {
                                e.config.parsers = buildParserCache(e, r), n = buildCache(e)
                            }, 1)
                        }).bind("updateCell", function (e, t) {
                            var r = this.config,
                                o = [t.parentNode.rowIndex - 1, t.cellIndex];
                            n.normalized[o[0]][o[1]] = r.parsers[o[1]].format(getElementText(r, t), t)
                        }).bind("sorton", function (e, t) {
                            $(this).trigger("sortStart"), o.sortList = t;
                            var a = o.sortList;
                            updateHeaderSortCount(this, a), setHeadersCss(this, r, a, i), appendToTable(this, multisort(this, a, n))
                        }).bind("appendCache", function () {
                            appendToTable(this, n)
                        }).bind("applyWidgetId", function (e, t) {
                            getWidgetById(t).format(this)
                        }).bind("applyWidgets", function () {
                            applyWidget(this)
                        }), $.metadata && $(this).metadata() && $(this).metadata().sortlist && (o.sortList = $(this).metadata().sortlist), o.sortList.length > 0 && t.trigger("sorton", [o.sortList]), applyWidget(this)
                    }
                })
            }, this.addParser = function (e) {
                for (var t = parsers.length, r = !0, n = 0; n < t; n++) parsers[n].id.toLowerCase() == e.id.toLowerCase() && (r = !1);
                r && parsers.push(e)
            }, this.addWidget = function (e) {
                widgets.push(e)
            }, this.formatFloat = function (e) {
                var t = parseFloat(e);
                return isNaN(t) ? 0 : t
            }, this.formatInt = function (e) {
                var t = parseInt(e);
                return isNaN(t) ? 0 : t
            }, this.isDigit = function (e, t) {
                return /^[-+]?\d*$/.test($.trim(e.replace(/[,.']/g, "")))
            }, this.clearTableBody = function (e) {
                if ($.browser.msie) {
                    (function () {
                        for (; this.firstChild;) this.removeChild(this.firstChild)
                    }).apply(e.tBodies[0])
                } else e.tBodies[0].innerHTML = ""
            }
        }
    }), $.fn.extend({
        tablesorter: $.tablesorter.construct
    });
    var ts = $.tablesorter;
    ts.addParser({
        id: "text",
        is: function (e) {
            return !0
        },
        format: function (e) {
            return $.trim(e.toLocaleLowerCase())
        },
        type: "text"
    }), ts.addParser({
        id: "digit",
        is: function (e, t) {
            var r = t.config;
            return $.tablesorter.isDigit(e, r)
        },
        format: function (e) {
            return $.tablesorter.formatFloat(e)
        },
        type: "numeric"
    }), ts.addParser({
        id: "currency",
        is: function (e) {
            return /^[£$€?.]/.test(e)
        },
        format: function (e) {
            return $.tablesorter.formatFloat(e.replace(new RegExp(/[£$€]/g), ""))
        },
        type: "numeric"
    }), ts.addParser({
        id: "ipAddress",
        is: function (e) {
            return /^\d{2,3}[\.]\d{2,3}[\.]\d{2,3}[\.]\d{2,3}$/.test(e)
        },
        format: function (e) {
            for (var t = e.split("."), r = "", n = t.length, o = 0; o < n; o++) {
                var i = t[o];
                2 == i.length ? r += "0" + i : r += i
            }
            return $.tablesorter.formatFloat(r)
        },
        type: "numeric"
    }), ts.addParser({
        id: "url",
        is: function (e) {
            return /^(https?|ftp|file):\/\/$/.test(e)
        },
        format: function (e) {
            return jQuery.trim(e.replace(new RegExp(/(https?|ftp|file):\/\//), ""))
        },
        type: "text"
    }), ts.addParser({
        id: "isoDate",
        is: function (e) {
            return /^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/.test(e)
        },
        format: function (e) {
            return $.tablesorter.formatFloat("" != e ? new Date(e.replace(new RegExp(/-/g), "/")).getTime() : "0")
        },
        type: "numeric"
    }), ts.addParser({
        id: "percent",
        is: function (e) {
            return /\%$/.test($.trim(e))
        },
        format: function (e) {
            return $.tablesorter.formatFloat(e.replace(new RegExp(/%/g), ""))
        },
        type: "numeric"
    }), ts.addParser({
        id: "usLongDate",
        is: function (e) {
            return e.match(new RegExp(/^[A-Za-z]{3,10}\.? [0-9]{1,2}, ([0-9]{4}|'?[0-9]{2}) (([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(AM|PM)))$/))
        },
        format: function (e) {
            return $.tablesorter.formatFloat(new Date(e).getTime())
        },
        type: "numeric"
    }), ts.addParser({
        id: "shortDate",
        is: function (e) {
            return /\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}/.test(e)
        },
        format: function (e, t) {
            var r = t.config;
            return e = e.replace(/\-/g, "/"), "us" == r.dateFormat ? e = e.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/, "$3/$1/$2") : "uk" == r.dateFormat ? e = e.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/, "$3/$2/$1") : "dd/mm/yy" != r.dateFormat && "dd-mm-yy" != r.dateFormat || (e = e.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2})/, "$1/$2/$3")), $.tablesorter.formatFloat(new Date(e).getTime())
        },
        type: "numeric"
    }), ts.addParser({
        id: "time",
        is: function (e) {
            return /^(([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(am|pm)))$/.test(e)
        },
        format: function (e) {
            return $.tablesorter.formatFloat(new Date("2000/01/01 " + e).getTime())
        },
        type: "numeric"
    }), ts.addParser({
        id: "metadata",
        is: function (e) {
            return !1
        },
        format: function (e, t, r) {
            var n = t.config,
                o = n.parserMetadataName ? n.parserMetadataName : "sortValue";
            return $(r).metadata()[o]
        },
        type: "numeric"
    }), ts.addWidget({
        id: "zebra",
        format: function (e) {
            if (e.config.debug) var t = new Date;
            var r, n, o = -1;
            $("tr:visible", e.tBodies[0]).each(function (t) {
                (r = $(this)).hasClass(e.config.cssChildRow) || o++, n = o % 2 == 0, r.removeClass(e.config.widgetZebra.css[n ? 0 : 1]).addClass(e.config.widgetZebra.css[n ? 1 : 0])
            }), e.config.debug && $.tablesorter.benchmark("Applying Zebra widget", t)
        }
    })
}(jQuery);