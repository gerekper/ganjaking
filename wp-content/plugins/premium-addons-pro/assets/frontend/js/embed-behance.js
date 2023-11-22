! function (e) {
    e.fn.embedBehance = function (t) {
        var i, n = e.extend({
            project: !0,
            owners: !0,
            appreciations: !0,
            views: !0,
            publishedDate: !0,
            projectUrl: !0,
            fields: !0,
            apiKey: "",
            itemsPerPage: "6",
            userName: "",
            infiniteScrolling: !1,
            imageCaption: !1,
            ownerLink: !0,
            description: !0,
            tags: !0,
            themeColor: "#2183ee",
            animationDuration: 300,
            containerId: "",
            animationEasing: "easeInOutExpo",
            coverSize: "404",
            feedObject: false
        }, t),
            a = [],
            s = 1,
            r = [],
            o = "",
            l = [],
            c = 1,
            d = 0,
            p = 0,
            f = 0,
            g = ["https://api.behance.net/v2/users/", n.userName, "/projects?client_id=", n.apiKey, "&per_page=", n.itemsPerPage, "&page=", s];

        e("body").wrapInner(e("<div>").addClass("eb-total-inner-container")).wrapInner(e("<div>").addClass("eb-total-outer-container"));
        var h = e(this).wrap(e("<div>").addClass("eb-container").css({
            position: "relative"
        }));
        e(h).html('<ul class="wrap-projects"></ul>'), e("body").append(C('<div class="eb-loadingicon">' + C("loading") + "</div>"));

        var b = function () {

            a = g, g = g.join(""), r = [];

            if (n.feedObject) {

                handleResponse(n.feedObject);

            } else {


                e.ajax({
                    url: g,
                    dataType: "jsonp",
                    success: function (t) {

                        handleResponse(t);

                    },
                    error: function (e) {
                        console.log("ERROR: ", e)
                    }
                })

            }
        };

        function handleResponse(response) {

            e.each(response.projects, function (e, t) {
                v(e, t)
            });

            e.each(r, function (t, i) {
                o = "", o += i.rawId, o += i.cover, o += i.title, o += i.owners, o += i.appreciations, o = '<li class="wrap-project">' + (o += i.views) + "</li>", e("#premium-behance-container-" + n.containerId + " .wrap-projects").append(o)
            });

            w();


        }

        function v(t, i) {
            r[t] = {
                rawId: m("rawId", i),
                rawProjectUrl: m("rawProjectUrl", i),
                project: m("project", i),
                owners: m("owners", i),
                works: m("works", i),
                cover: m("cover", i),
                appreciations: m("appreciations", i),
                views: m("views", i),
                title: m("title", i),
                publishedDate: m("publishedDate", i),
                projectUrl: m("projectUrl", i),
                fields: m("fields", i),
                description: m("description", i),
                tags: m("tags", i)
            }, 1 == d && (l.title = ".eb-container .box-inner-main .title {\n\tfont-family: " + i.styles.text.title.font_family + ";\n\tfont-weight: " + i.styles.text.title.font_weight + ";\n\tcolor: " + i.styles.text.title.color + ";\n\ttext-align: " + i.styles.text.title.text_align + ";\n\tline-height:  " + i.styles.text.title.line_height + ";\n\tfont-size: " + i.styles.text.title.font_size + ";\n\ttext-decoration: " + i.styles.text.title.text_decoration + ";\n\tfont-style: " + i.styles.text.title.font_style + ";\n\ttext-transform: " + i.styles.text.title.text_transform + ";\n}", l.subtitle = ".eb-container .box-inner-main .sub-title {\n\tfont-family: " + i.styles.text.subtitle.font_family + ";\n\tfont-weight: " + i.styles.text.subtitle.font_weight + ";\n\tcolor: " + i.styles.text.subtitle.color + ";\n\ttext-align: " + i.styles.text.subtitle.text_align + ";\n\tline-height:  " + i.styles.text.subtitle.line_height + ";\n\tfont-size: " + i.styles.text.subtitle.font_size + ";\n\ttext-decoration: " + i.styles.text.subtitle.text_decoration + ";\n\tfont-style: " + i.styles.text.subtitle.font_style + ";\n\ttext-transform: " + i.styles.text.subtitle.text_transform + ";\n}", l.paragraph = ".eb-container .box-inner-main .main-text {\n\tfont-family: " + i.styles.text.paragraph.font_family + ";\n\tfont-weight: " + i.styles.text.paragraph.font_weight + ";\n\tcolor: " + i.styles.text.paragraph.color + ";\n\ttext-align: " + i.styles.text.paragraph.text_align + ";\n\tline-height:  " + i.styles.text.paragraph.line_height + ";\n\tfont-size: " + i.styles.text.paragraph.font_size + ";\n\ttext-decoration: " + i.styles.text.paragraph.text_decoration + ";\n\tfont-style: " + i.styles.text.paragraph.font_style + ";\n\ttext-transform: " + i.styles.text.paragraph.text_transform + ";\n}", l.caption = ".eb-container .box-inner-main .caption {\n\tfont-family: " + i.styles.text.caption.font_family + ";\n\tfont-weight: " + i.styles.text.caption.font_weight + ";\n\tcolor: " + i.styles.text.caption.color + ";\n\ttext-align: " + i.styles.text.caption.text_align + ";\n\tline-height:  " + i.styles.text.caption.line_height + ";\n\tfont-size: " + i.styles.text.caption.font_size + ";\n\ttext-decoration: " + i.styles.text.caption.text_decoration + ";\n\tfont-style: " + i.styles.text.caption.font_style + ";\n\ttext-transform: " + i.styles.text.caption.text_transform + ";\n}", l.link = ".eb-container .box-inner-main a {\n\tfont-family: " + i.styles.text.link.font_family + ";\n\tfont-weight: " + i.styles.text.link.font_weight + ";\n\tcolor: " + i.styles.text.link.color + ";\n\ttext-align: " + i.styles.text.link.text_align + ";\n\tline-height:  " + i.styles.text.link.line_height + ";\n\tfont-size: " + i.styles.text.link.font_size + ";\n\ttext-decoration: " + i.styles.text.link.text_decoration + ";\n\tfont-style: " + i.styles.text.link.font_style + ";\n\ttext-transform: " + i.styles.text.link.text_transform + ";\n}", l.background = ".eb-container .box-inner-main {\n\tbackground-color: #" + i.styles.background.color + ";\n\t}", l.bottom_margin = ".eb-container .box-inner-main .spacer {\n\theight: " + i.styles.spacing.modules.bottom_margin + "px;\n\t}", l.top_margin = ".eb-container .box-inner-main .wrap-works-outer {\n\tpadding-top: " + i.styles.spacing.project.top_margin + "px;\n\t}", l.dividers = ".eb-container .box-inner-main .spacer .divider {\n\tfont-size: " + i.styles.dividers.font_size + ";\n\tline-height: " + i.styles.dividers.line_height + ";\n\theight: " + i.styles.dividers.height + ";\n\tborder-color: " + i.styles.dividers.border_color + ";\n\tmargin: " + i.styles.dividers.margin + ";\n\tposition: " + i.styles.dividers.position + ";\n\ttop: " + i.styles.dividers.top + ";\n\tborder-width: " + i.styles.dividers.border_width + ";\n\tborder-style: " + i.styles.dividers.border_style + ";\n\t}", e("head").append('<style type="text/css" data-css="embed-behance">\n\t' + l.title + "\n" + l.subtitle + "\n" + l.paragraph + "\n" + l.link + "\n" + l.caption + "\n" + l.background + "\n" + l.bottom_margin + "\n" + l.top_margin + "\n" + l.dividers + "\n\t</style>"))
        }

        function m(t, i) {
            var a, s = "",
                r = "";
            switch (t) {
                case "rawId":
                    s = '<div class="raw-project-id" style="display: none;">' + i.id + "</div>";
                    break;
                case "rawProjectUrl":
                    s += '<div class="raw-project-url" style="display: none;">' + i.url + "</div>";
                    break;
                case "owners":
                    1 == n.owners && (s += '<div class="wrap-label">By: </div>', s += '<ul class="wrap-values">', e.each(i.owners, function (e, t) {
                        s += '<li class="single">', 1 == d && (s += '<div class="profile-pic">', 1 == n.ownerLink ? s += '<a style="color: ' + n.themeColor + '" href="' + t.url + '" target="_blank"><img src="' + t.images[100] + '" alt="' + t.display_name + ' profile picture" /></a>' : s += '<img src="' + t.images[100] + '" alt="' + t.display_name + ' profile picture" />', s += "</div>"), s += '<div class="owner-full-name">' + C("owner"), 1 == n.ownerLink ? s += '<a href="' + t.url + '" target="_blank">' + t.display_name + "</a>" : s += t.display_name, s += "</div>", s += "</li>"
                    }), s = '<div class="wrap-owners-outer">' + (s += "</ul>") + "</div>", p = 1);
                    break;
                case "works":
                    s += '<ul class="wrap-values">';
                    var o = i.name;
                    e.each(i.modules, function (t, i) {
                        function a() {
                            return "caption" in i && 1 == n.imageCaption ? '<li class="caption">' + i.caption + "</li>" : ""
                        }

                        function r() {
                            return 1 == i.full_bleed ? " full-bleed" : ""
                        }
                        switch (i.type) {
                            case "media_collection":
                                s += '<ul class="grid-images-list">', e.each(i.components, function (e, t) {
                                    s += '<li class="grid-image-item' + r() + '">', s += "<picture>", s += '<source media="(min-width: 5em)" srcset="' + t.sizes.disp + '">', s += '<img src="' + t.sizes.disp + '" alt="' + o + '" />', s += "</picture>", s += a(), s += "</li>"
                                }), s += "</ul>", s += '<li class="spacer"><div class="divider"></div></li>';
                                break;
                            case "image":
                                s += '<li class="single-image' + r() + '" style="text-align:' + i.alignment + '">', s += "<picture>", s += '<source media="(min-width: 30em)" srcset="' + i.sizes.original + '">', s += '<img src="' + i.sizes.disp + '" alt="' + o + '" />', s += "</picture>", s += "</li>", s += a(), s += '<li class="spacer"><div class="divider"></div></li>';
                                break;
                            case "text":
                                s += '<li class="single-text">' + i.text + "</li>", s += a(), s += '<li class="spacer"><div class="divider"></div></li>';
                                break;
                            case "embed":
                                s += '<li class="single-embed' + r() + '"><div class="inner">' + i.embed + "</div></li>", s += a(), s += '<li class="spacer"><div class="divider"></div></li>'
                        }
                    }), s = '<div class="wrap-works-outer">' + (s += "</ul>") + "</div>";
                    break;
                case "appreciations":
                    1 == n.appreciations && (s += '<div class="wrap-label">' + C("thumbsUp") + "</div>", s = (r = '<div class="wrap-project-info">') + '<div class="wrap-appreciations-outer">' + (s += '<div class="wrap-value"><p class="wrap-app-value">' + i.stats.appreciations + "</p></div>") + "</div>", !0 !== n.views && (s += "</div>"), p = 1);
                    break;
                case "views":
                    1 == n.views && (s += '<div class="wrap-label">' + C("views") + "</div>", s += '<div class="wrap-value"><p class="wrap-view-value">' + i.stats.views + "</p></div>", !0 !== n.appreciations && (r = '<div class="wrap-project-info">'), s = r + '<div class="wrap-views-outer">' + s + "</div>", 1 == n.appreciations && (s += "</div>"), p = 1);
                    break;
                case "cover":
                    var coverImage = i.covers[n.coverSize] || i.covers[404];
                    s += '<div class="wrap-cover-outer"><div class="wrap-cover">', s += '<img src="' + coverImage + '" alt="' + i.name + '" />', 1 == n.fields && (s += '<ul class="fields-in-cover">' + C("fields"), e.each(i.fields, function (e, t) {
                        s += '<li class="single">' + t + "</li>"
                    }), s += "</ul>"), s += "</div></div>";
                    break;
                case "title":
                    1 == n.project && (s += '<p class="wrap-title-text">' + i.name + "</p>");
                    break;
                case "publishedDate":
                    1 == n.publishedDate && (s += '<div class="wrap-label">Published:</div>', s = '<div class="wrap-published-date-outer">' + (s += '<div class="wrap-value">' + (a = i.published_on, new Date(1e3 * a).toDateString()) + "</div>") + "</div>", p = 1);
                    break;
                case "projectUrl":
                    1 == n.projectUrl && (s = '<div class="wrap-project-url">' + (s += '<a href="' + i.url + '" title="' + i.name + '" target="_blank"> Appreciate it in Behance </a>') + "</div>", p = 1);
                    break;
                case "fields":
                    1 == n.fields && (s += '<div class="wrap-label">' + C("fields") + "Fields:</div>", s += '<ul class="wrap-values">', e.each(i.fields, function (e, t) {
                        s += '<li class="single">' + t + "</li>"
                    }), s = '<div class="wrap-fields-outer">' + (s += "</ul>") + "</div>", p = 1);
                    break;
                case "tags":
                    1 == n.tags && (s += '<div class="wrap-label">' + C("tags") + "Tags:</div>", s += '<ul class="wrap-values">', e.each(i.tags, function (e, t) {
                        s += '<li class="single">' + t + "</li>"
                    }), s = '<div class="wrap-tags-outer">' + (s += "</ul>") + "</div>", p = 1);
                    break;
                case "description":
                    1 == n.description && "" !== i.description && (s += '<div class="wrap-description">' + i.description + "</div>")
            }
            return s
        }

        function w() {
            function t() {
                0 == --r && (e(".eb-loadingicon").remove(), 1 == d ? function () {
                    function t() {
                        var t = e(".eb-container .wrap-headings").outerHeight(!0);
                        e(".eb-desktop-info").css("height", "63px"), e(".eb-container .box-project aside .wrap-owners-outer").css("min-height", t)
                    }
                    e("div.box-project").animate({
                        top: 0,
                        opacity: 1
                    }, n.animationDuration), e(window).resize(function () {
                        setTimeout(t, 500)
                    }), t()
                }() : (e("ul.wrap-projects li").animate({
                    opacity: 1
                }), function (t) {
                    function i(t) {
                        if (e(h).find(".eb-pagination-button").length > 0 && e(h).find(".eb-pagination-button").remove(), "show" == t && 0 == n.infiniteScrolling) e(h).append('<div class="premium-behance-btn"><div class="eb-pagination-button"><span>Load More</span> <span class="icon-loading"></span></div></div>');
                        else if ("remove" == t) return c = 0
                    }
                    s++, t[t.length - 1] = s, g = t, t = t.join(""), e.ajax({
                        url: t,
                        dataType: "jsonp",
                        success: function (e) {
                            e.projects.length > 0 ? i("show") : i("remove")
                        },
                        error: function (e) {
                            console.log("Error: ", e)
                        }
                    })
                }(a), f = 0))
            }
            if (1 == d) var i = e(".box-project img");
            else i = e(".wrap-project img");
            var r = i.length;
            if (r) {
                i.each(function () {
                    this.complete ? t() : e(this).one("load", t)
                });
            } else {
                e(".eb-loadingicon").remove();

                e("div.box-project").animate({
                    top: 0,
                    opacity: 1
                }, n.animationDuration);

                function getHeaderHeight() {
                    var t = e(".eb-container .wrap-headings").outerHeight(!0);
                    e(".eb-desktop-info").css("height", "63px");
                    e(".eb-container .box-project aside .wrap-owners-outer").css("min-height", t);
                }

                e(window).resize(function () {
                    setTimeout(getHeaderHeight, 500);
                });

                getHeaderHeight();
            }

        }

        function u() {
            e("div.box-project").animate({
                top: 50,
                opacity: 0
            }, n.animationDuration, function () {
                e(this).remove(), e(".eb-detail-modal-active .eb-total-outer-container").css("position", "relative"), e(".eb-detail-modal-active .eb-total-outer-container > .eb-total-inner-container").css("top", "auto"), e(window).scrollTop(i), e("body").removeClass(".eb-detail-modal-active"), e('style[data-css="embed-behance"]').remove(), d = !1
            }), e(".eb-project-overlay").animate({
                opacity: 0
            }, n.animationDuration, function () {
                e(this).remove()
            }), e(".eb-total-inner-container").animate({
                opacity: 1
            }, n.animationDuration)
        }
        e(h).on("click", ".wrap-project .wrap-cover-outer, .wrap-project .wrap-title-text", function () {
            var t = "https://www.behance.net/v2/projects/" + e(this).parent(".wrap-project").find(".raw-project-id").text() + "?api_key=" + n.apiKey;
            d = 1,
                function (t) {
                    e("body").append('<div class="eb-loadingicon">' + C("loading") + "</div>"), r = [], e.ajax({
                        url: t,
                        dataType: "jsonp",
                        success: function (t) {
                            v(0, t.project),
                                function () {
                                    function t() {
                                        o += '<div class="box-overflow"><div class="box-overflow-inner">', o += r[0].owners, o += r[0].fields, o += r[0].views, o += r[0].appreciations, o += r[0].tags, o += r[0].projectUrl, o += r[0].publishedDate, o += "</div></div>"
                                    }
                                    o = "", o += '<div id="wrap-headings-' + n.containerId + '" class="wrap-headings">', "" != r[0].title ? o += '<div class="inner">' : o += '<div class="inner no-title">', o += '<div class="close-project">' + C("close") + "</div>", o += r[0].title, o += r[0].description, o += "</div>", o += "</div>", o += '<main class="box-inner-main">', o += r[0].works, o += "</main>", 1 == p && (o += '<aside class="box-inner-sidebar sidebar-mobile">', t(), o += '<a class="bh-show" style="background-color: ' + n.themeColor + '"><span class="label">Show Info</span></a>', o += "</aside>", o += '<aside class="box-inner-sidebar sidebar-desktop">', o += '<div class="eb-desktop-info" style="background-color: ' + n.themeColor + '"><span class="label">Info</span></div>', t(), o += "</aside>"), e(o = '<div class="box-project eb-container">' + o + "</div>").insertAfter(e(".eb-total-outer-container")), 1 == p && e(".eb-container .box-project").addClass("has-sidebar"), w()
                                }()
                        },
                        error: function (e) {
                            console.log("ERROR: ", e)
                        }
                    })
                }(t), e("body").addClass("eb-detail-modal-active"), i = e(document).scrollTop(), e(".eb-detail-modal-active .eb-total-outer-container > .eb-total-inner-container").css({
                    top: -i
                }), e(".eb-detail-modal-active .eb-total-outer-container").css("position", "fixed"), e("body").append('<div class="eb-project-overlay"></div>'), e(".eb-project-overlay").animate({
                    opacity: 1
                }, n.animationDuration)
        }), e("body").on("click", ".box-project.eb-container + .eb-project-overlay", function (e) {
            e.target == this && u()
        }), e("body").on("click", ".eb-container .close-project", function () {
            u()
        });
        var x, y = 0;
        e(h).on("click", ".eb-pagination-button:not(.active)", function (t) {
            e(this).addClass("active"), e(this).children(".icon-loading").html(C("loading")), y || (y = 1, g = a, b(), y = 0)
        }), 1 == n.infiniteScrolling && e(window).on("scroll", function () {
            if (clearTimeout(x), 1 == c && 0 == d) {
                var t = e(h).offset().top,
                    i = e(h).outerHeight(),
                    n = e(window).height();
                e(this).scrollTop() > t + i - n - 50 && !f && (x = setTimeout(function () {
                    f = 1, g = a, b()
                }, 300))
            }
        });
        var k = 0;
        e("body").on("click", ".sidebar-mobile .bh-show", function () {
            function t(t) {
                if ("show" == t) {
                    var i = e("body").height() - e(".eb-container .wrap-headings").outerHeight(!0) - 20;
                    e(".sidebar-mobile .bh-show > .label").text("Hide Info"), e(".eb-container aside.sidebar-mobile").addClass("open"), e(".eb-container aside.sidebar-mobile").animate({
                        height: i
                    }, n.animationDuration).css("border-radius", 15)
                } else "hide" == t && (e(".sidebar-mobile .bh-show > .label").text("Show Info"), e("aside.sidebar-mobile").removeClass("open"), e(".eb-container aside.sidebar-mobile").animate({
                    height: 42
                }, n.animationDuration).css("border-radius", 50))
            }
            k ? (k = 0, t("hide")) : (k = 1, t("show"))
        });
        var _ = 0;

        function C(e) {
            switch (e) {
                case "thumbsUp":
                    return '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 478.2 478.2" style="enable-background:new 0 0 478.2 478.2;" xml:space="preserve"> \n<g> \n<path class="st0" d="M457.6,325.1c9.8-12.5,14.5-25.9,13.9-39.7c-0.6-15.2-7.4-27.1-13-34.4c6.5-16.2,9-41.7-12.7-61.5c-15.9-14.5-42.9-21-80.3-19.2c-26.3,1.2-48.3,6.1-49.2,6.3h-0.1c-5,0.9-10.3,2-15.7,3.2c-0.4-6.4,0.7-22.3,12.5-58.1c14-42.6,13.2-75.2-2.6-97C293.8,1.8,267.3,0,259.5,0c-7.5,0-14.4,3.1-19.3,8.8c-11.1,12.9-9.8,36.7-8.4,47.7c-13.2,35.4-50.2,122.2-81.5,146.3c-0.6,0.4-1.1,0.9-1.6,1.4c-9.2,9.7-15.4,20.2-19.6,29.4c-5.9-3.2-12.6-5-19.8-5h-61c-23,0-41.6,18.7-41.6,41.6v162.5c0,23,18.7,41.6,41.6,41.6h61c8.9,0,17.2-2.8,24-7.6l23.5,2.8c3.6,0.5,67.6,8.6,133.3,7.3c11.9,0.9,23.1,1.4,33.5,1.4c17.9,0,33.5-1.4,46.5-4.2c30.6-6.5,51.5-19.5,62.1-38.6c8.1-14.6,8.1-29.1,6.8-38.3c19.9-18,23.4-37.9,22.7-51.9C461.3,337.1,459.5,330.2,457.6,325.1z M48.3,447.3c-8.1,0-14.6-6.6-14.6-14.6V270.1c0-8.1,6.6-14.6,14.6-14.6h61c8.1,0,14.6,6.6,14.6,14.6v162.5c0,8.1-6.6,14.6-14.6,14.6L48.3,447.3L48.3,447.3z M432,313.4c-4.2,4.4-5,11.1-1.8,16.3c0,0.1,4.1,7.1,4.6,16.7c0.7,13.1-5.6,24.7-18.8,34.6c-4.7,3.6-6.6,9.8-4.6,15.4c0,0.1,4.3,13.3-2.7,25.8c-6.7,12-21.6,20.6-44.2,25.4c-18.1,3.9-42.7,4.6-72.9,2.2c-0.4,0-0.9,0-1.4,0c-64.3,1.4-129.3-7-130-7.1h-0.1l-10.1-1.2c0.6-2.8,0.9-5.8,0.9-8.8V270.1c0-4.3-0.7-8.5-1.9-12.4c1.8-6.7,6.8-21.6,18.6-34.3c44.9-35.6,88.8-155.7,90.7-160.9c0.8-2.1,1-4.4,0.6-6.7c-1.7-11.2-1.1-24.9,1.3-29c5.3,0.1,19.6,1.6,28.2,13.5c10.2,14.1,9.8,39.3-1.2,72.7c-16.8,50.9-18.2,77.7-4.9,89.5c6.6,5.9,15.4,6.2,21.8,3.9c6.1-1.4,11.9-2.6,17.4-3.5c0.4-0.1,0.9-0.2,1.3-0.3c30.7-6.7,85.7-10.8,104.8,6.6c16.2,14.8,4.7,34.4,3.4,36.5c-3.7,5.6-2.6,12.9,2.4,17.4c0.1,0.1,10.6,10,11.1,23.3C444.9,295.3,440.7,304.4,432,313.4z"/> \n</g></svg>';
                case "chevronDown":
                    return '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 311.2 203.3" style="enable-background:new 0 0 311.2 203.3;" xml:space="preserve"> \n <g> \n<g> \n<path style="fill: ' + n.themeColor + '" class="st0" d="M155.6,110L263.5,0l47.7,45.6L155.6,203.3L0,47.8L45.6,0.1L155.6,110z"/> \n</g> \n</g> \n</svg>';
                case "chevronUp":
                    return '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 311.2 203.3" style="enable-background:new 0 0 311.2 203.3;" xml:space="preserve"> \n <g> \n<g> \n<path style="fill: ' + n.themeColor + '" class="st0" d="M155.6,93.3l-107.9,110L0,157.7L155.6,0l155.6,155.5l-45.6,47.7L155.6,93.3z"/> \n</g> \n</g> \n</svg>';
                case "chevronRight":
                    return '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 475.7 316.4" style="enable-background:new 0 0 475.7 316.4;" xml:space="preserve"> \n <g> \n<g> \n<path style="fill: ' + n.themeColor + '" class="st0" d="M246.8,158.9L136.8,51l45.6-47.7l157.7,155.6L184.6,314.5l-47.7-45.6L246.8,158.9z"/> \n</g> \n</g> \n</svg>';
                case "chevronLeft":
                    return '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 475.7 316.4" style="enable-background:new 0 0 475.7 316.4;" xml:space="preserve"> \n <g> \n<g> \n<path style="fill: ' + n.themeColor + '" class="st0" d="M340,268.9l-47.7,45.6L136.8,158.9L294.5,3.3L340.1,51l-110,107.9L340,268.9z"/> \n</g> \n</g> \n</svg>';
                case "fields":
                    return '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 570.3 385.6" style="enable-background:new 0 0 570.3 385.6;" xml:space="preserve"> \n <g> \n<g> \n<path class="st0" d="M544.8,100.4c-2-4.8-6.6-7.7-11.6-7.7h-59.1l68.2-69c3.6-3.7,4.7-9.1,2.7-13.7c-2-4.8-6.6-7.7-11.6-7.7H210.1c-3.3,0-6.6,1.3-9.1,3.8L28.9,180.4c-3.6,3.7-4.7,9.1-2.7,13.7c2,4.8,6.6,7.7,11.6,7.7h59.1l-68,69.1c-3.6,3.7-4.7,9.1-2.7,13.7c2,4.8,6.6,7.7,11.6,7.7h323.1c3.3,0,6.6-1.3,9.1-3.8l79.1-80.2h53.8L355.6,357.8h-287l29.1-28.9c2.5-2.3,3.7-5.5,3.7-9c0-3.4-1.3-6.6-3.7-9c-2.3-2.5-5.5-3.7-9-3.7l0,0c-3.3,0-6.5,1.3-9,3.7L29,361.5c-3.7,3.7-4.8,9.1-2.8,13.9s6.6,7.8,11.6,7.8h323.1c3.3,0,6.6-1.3,9.1-3.8l172.3-174.6c3.6-3.7,4.7-9.1,2.7-13.7c-2-4.8-6.6-7.7-11.6-7.7h-59.2l68.2-69.3C545.8,110.6,546.9,105.2,544.8,100.4z M355.6,267.2H68.2l64.4-65.2H361c3.3,0,6.6-1.3,9.1-3.8l79.1-80.2H503L355.6,267.2zM355.6,176.6H68.2L215.5,27.3H503L355.6,176.6z"/> \n</g> \n</g> \n</svg>';
                case "owner":
                    return '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 265.3 311.5" style="enable-background:new 0 0 265.3 311.5;" xml:space="preserve"> \n <g> \n<g> \n<g> \n<path style="fill: ' + n.themeColor + '" class="st0" d="M132.7,163.8c-35.5,0-64.3-55.7-64.3-92c0-35.4,28.8-64.3,64.3-64.3S197,36.3,197,71.8C196.9,108.1,168.1,163.8,132.7,163.8z M132.7,18.4c-29.5,0-53.4,24-53.4,53.4c0,30.6,25,81.1,53.4,81.1c28.5,0,53.4-50.6,53.4-81.1C186.1,42.3,162.1,18.4,132.7,18.4z"/> \n<path style="fill: ' + n.themeColor + '" class="st0" d="M132.7,171.3c-41,0-71.8-60.3-71.8-99.5C60.9,32.2,93.1,0,132.7,0s71.8,32.2,71.8,71.8C204.4,110.9,173.6,171.3,132.7,171.3z M132.7,25.9c-25.3,0-45.9,20.6-45.9,45.9c0,14,5.9,32.9,15,48.2c9.6,16.2,20.9,25.4,30.9,25.4s21.3-9.3,30.9-25.4c9.1-15.3,15-34.2,15-48.2C178.6,46.5,158,25.9,132.7,25.9z"/> \n<g> \n</g> \n<g> \n<g> \n<path style="fill: ' + n.themeColor + '" class="st0" d="M252.4,304c-3,0-5.4-2.4-5.4-5.4c0-58.5-51.3-106.1-114.3-106.1S18.4,240.1,18.4,298.6c0,3-2.4,5.4-5.4,5.4s-5.4-2.4-5.4-5.4c0-64.5,56.1-116.9,125.2-116.9S258,234.2,258,298.6C257.8,301.5,255.4,304,252.4,304z"/> \n<path style="fill: ' + n.themeColor + '" class="st0" d="M252.4,311.5c-7.1,0-12.9-5.8-12.9-12.9c0-54.4-47.9-98.6-106.8-98.6S25.9,244.2,25.9,298.6c0,7.1-5.8,12.9-12.9,12.9s-13-5.8-13-13c0-68.6,59.5-124.4,132.7-124.4c73.1,0,132.7,55.8,132.7,124.4C265.3,305.7,259.5,311.5,252.4,311.5z"/> \n<g> \n</g> \n</g> \n</svg>';
                case "tags":
                    return '<svg id="Capa_1" data-name="Capa 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 463.55 463.34"> \n<path style="fill: ' + n.themeColor + '" class="cls-1" d="M445.75,256.35l-179.9-180a50.17,50.17,0,0,0-35.6-14.8c-1.1,0-2.2,0-3.3.1l-130.1,8.6a28.39,28.39,0,0,0-26.4,26.4l-3.1,46.2a89.66,89.66,0,0,1-18.6-14.3c-13.9-13.9-22.5-31.2-24.3-48.8-1.7-16.7,3.1-31.6,13.5-42,22.1-22.1,62.8-17.2,90.9,10.8a12,12,0,0,0,17-17c-37.6-37.3-93.6-42.1-125-10.7C5.25,36.45-1.95,58.15.45,82.25c2.3,23.1,13.4,45.6,31.2,63.4a111.67,111.67,0,0,0,33.9,23.3l-3.8,57.9a50.29,50.29,0,0,0,14.7,39l180,180a59.74,59.74,0,0,0,42.6,17.6h0a60.16,60.16,0,0,0,42.6-17.6L446,341.55A60.11,60.11,0,0,0,445.75,256.35Zm-16.9,68.1-104.4,104.4a36,36,0,0,1-25.6,10.6h0a36,36,0,0,1-25.6-10.6l-180-180a26.35,26.35,0,0,1-7.7-20.4l3.5-52.4c2,0.3,4,.6,6,0.8,3,0.3,6,.5,8.9.5,20.5,0,38.8-7.2,52.4-20.8a12,12,0,0,0-17-17c-10.4,10.4-25.3,15.2-42,13.5-2.3-.2-4.5-0.6-6.7-1l3.6-53.6a4.41,4.41,0,0,1,4.1-4.1l130.1-8.6c0.6,0,1.2-.1,1.8-0.1a26.57,26.57,0,0,1,18.7,7.7l179.9,179.9A36.21,36.21,0,0,1,428.85,324.45Z" transform="translate(0 -0.11)"/> \n</svg>';
                case "views":
                    return '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 475.7 316.4" style="enable-background:new 0 0 475.7 316.4;" xml:space="preserve"> \n<g> \n<g> \n<path class="st0" d="M237.8,316.4c-70.8,0-130.1-36.6-167.3-67.3C31,216.5,0,176.5,0,158.2s31-58.3,70.5-90.9C107.8,36.6,167,0,237.8,0c70.8,0,130.1,36.6,167.3,67.3c39.5,32.6,70.5,72.5,70.5,90.9s-31,58.3-70.5,90.9C367.9,279.8,308.7,316.4,237.8,316.4z M24.4,158.2C26,165.9,47,198.7,88.2,232c33.8,27.4,87.1,60,149.7,60s115.9-32.6,149.7-60c41.1-33.4,62.2-66.2,63.7-73.8c-1.6-7.7-22.6-40.5-63.7-73.8c-33.8-27.4-87.1-60-149.7-60S121.9,57,88.2,84.4C47,117.7,26,150.5,24.4,158.2z M451.3,158.5L451.3,158.5L451.3,158.5z M451.3,157.8L451.3,157.8L451.3,157.8z"/> \n</g> \n<g> \n<path class="st0" d="M237.8,250c-50.6,0-91.8-41.2-91.8-91.8s41.2-91.8,91.8-91.8s91.8,41.2,91.8,91.8S288.5,250,237.8,250zM237.8,90.7c-37.2,0-67.5,30.3-67.5,67.5c0,37.2,30.3,67.5,67.5,67.5c37.2,0,67.5-30.3,67.5-67.5C305.3,121,275,90.7,237.8,90.7z"/> \n</g> \n</g> \n</svg>';
                case "close":
                    return '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 470.1 470.1" style="enable-background:new 0 0 470.1 470.1;" xml:space="preserve"> \n<g> \n<g> \n<path id="clse-2" class="st0" d="M337.2,30.7L235,133.2L132.9,30.7C109.3-2.2,63.6-9.9,30.7,13.7S-9.8,83,13.7,115.9c4.7,6.6,10.4,12.3,17,17L132.9,235L30.7,337.2c-32.9,23.5-40.5,69.3-17,102.2s69.3,40.5,102.2,17c6.6-4.7,12.3-10.4,17-17L235,337.2l102.2,102.2c23.5,32.9,69.3,40.5,102.2,17s40.5-69.3,17-102.2c-4.7-6.6-10.4-12.3-17-17L337.2,235l102.2-102.2c32.9-23.5,40.5-69.3,17-102.2s-69.3-40.5-102.2-17C347.7,18.4,341.9,24.1,337.2,30.7z"/> \n</g> \n</g> \n</svg>';
                case "loading":
                    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-default"> \n<rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect> \n<rect  x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#ffffff" transform="rotate(0 50 50) translate(0 -30)"> \n<animate attributeName="opacity" from="1" to="0" dur="1s" begin="-1s" repeatCount="indefinite"/> \n</rect> \n<rect  x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#ffffff" transform="rotate(30 50 50) translate(0 -30)"> \n<animate attributeName="opacity" from="1" to="0" dur="1s" begin="-0.9166666666666666s" repeatCount="indefinite"/> \n</rect> \n<rect  x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#ffffff" transform="rotate(60 50 50) translate(0 -30)"> \n<animate attributeName="opacity" from="1" to="0" dur="1s" begin="-0.8333333333333334s" repeatCount="indefinite"/> \n</rect> \n<rect  x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#ffffff" transform="rotate(90 50 50) translate(0 -30)"> \n<animate attributeName="opacity" from="1" to="0" dur="1s" begin="-0.75s" repeatCount="indefinite"/> \n</rect> \n<rect  x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#ffffff" transform="rotate(120 50 50) translate(0 -30)"> \n<animate attributeName="opacity" from="1" to="0" dur="1s" begin="-0.6666666666666666s" repeatCount="indefinite"/> \n</rect> \n<rect  x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#ffffff" transform="rotate(150 50 50) translate(0 -30)"> \n<animate attributeName="opacity" from="1" to="0" dur="1s" begin="-0.5833333333333334s" repeatCount="indefinite"/> \n</rect> \n<rect  x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#ffffff" transform="rotate(180 50 50) translate(0 -30)"> \n<animate attributeName="opacity" from="1" to="0" dur="1s" begin="-0.5s" repeatCount="indefinite"/> \n</rect> \n<rect  x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#ffffff" transform="rotate(210 50 50) translate(0 -30)"> \n<animate attributeName="opacity" from="1" to="0" dur="1s" begin="-0.4166666666666667s" repeatCount="indefinite"/> \n</rect> \n<rect  x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#ffffff" transform="rotate(240 50 50) translate(0 -30)"> \n<animate attributeName="opacity" from="1" to="0" dur="1s" begin="-0.3333333333333333s" repeatCount="indefinite"/> \n</rect> \n<rect  x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#ffffff" transform="rotate(270 50 50) translate(0 -30)"> \n<animate attributeName="opacity" from="1" to="0" dur="1s" begin="-0.25s" repeatCount="indefinite"/> \n</rect> \n<rect  x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#ffffff" transform="rotate(300 50 50) translate(0 -30)"> \n<animate attributeName="opacity" from="1" to="0" dur="1s" begin="-0.16666666666666666s" repeatCount="indefinite"/> \n</rect> \n<rect  x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#ffffff" transform="rotate(330 50 50) translate(0 -30)"> \n<animate attributeName="opacity" from="1" to="0" dur="1s" begin="-0.08333333333333333s" repeatCount="indefinite"/> \n</rect> \n</svg>'
            }
        }
        e("body").on("click", ".sidebar-desktop .eb-desktop-info", function () {
            function t(t) {
                "show" == t ? (e(".eb-container .sidebar-desktop").animate({
                    left: 0
                }, n.animationDuration), e(".eb-container .sidebar-desktop").addClass("info-open"), e('<div class="eb-project-overlay"></div>').insertBefore(".eb-container #wrap-headings-" + n.containerId), e(".box-project.eb-container > .eb-project-overlay").animate({
                    opacity: 1
                }, n.animationDuration)) : "hide" == t && (e(".eb-container .sidebar-desktop").animate({
                    left: -300
                }, n.animationDuration), e(".eb-container .sidebar-desktop").removeClass("info-open"), e(".box-project.eb-container > .eb-project-overlay").animate({
                    opacity: 0
                }, n.animationDuration, function () {
                    e(this).remove()
                }))
            }
            _ ? (_ = 0, t("hide")) : (_ = 1, t("show")), e("body").on("click", ".box-project.eb-container > .eb-project-overlay", function (e) {
                e.target == this && (_ = 0, t("hide"))
            })
        }), b()
    }
}(jQuery);