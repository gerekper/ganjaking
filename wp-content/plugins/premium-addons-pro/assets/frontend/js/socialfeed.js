"function" != typeof Object.create && (Object.create = function (t) {
    function e() { }
    return e.prototype = t, new e
}),
    function (t, e, i, a) {
        t.fn.socialfeed = function (e) {
            var i = t.extend({
                plugin_folder: "",
                template: "template.html",
                show_media: !1,
                readMore: "Read More »",
                media_min_width: 300,
                length: 500,
                date_format: "ll",
                date_locale: "en",
                adminPosts: false,
                totalPostsNumber: null,
                messagedPosts: 0,
                adminPostsNumber: 0,
                since: {
                    years: " years ago",
                    months: " months ago",
                    weeks: " weeks ago",
                    minutes: " minutes ago",
                    seconds: " seconds ago"
                }
            }, e),
                n = t(this),
                o = ["facebook", "twitter"],
                r = 0,
                c = 0;
            o.forEach(function (t) {
                i[t] && (i[t].accounts ? r += i[t].limit * i[t].accounts.length : i[t].urls ? r += i[t].limit * i[t].urls.length : r += i[t].limit)
            });
            var s = {
                request: function (e, i) {
                    t.ajax({
                        url: e,
                        dataType: "jsonp",
                        success: i
                    })
                },
                get_request: function (e, i) {
                    t.get(e, i, "json")
                },
                wrapLinks: function (t, e) {
                    return t.replace(/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gi, s.wrapLinkTemplate)
                },
                wrapLinkTemplate: function (t) {
                    return '<a target="_blank" href="' + t + '">' + t + "</a>"
                },
                shorten: function (e) {
                    return (e = t.trim(e)).length > i.length ? jQuery.trim(e).substring(0, i.length).split(" ").slice(0, -1).join(" ") + "..." : e
                },
                stripHTML: function (t) {
                    return null == t ? "" : t.replace(/(<([^>]+)>)|nbsp;|\s{2,}|/gi, "")
                }
            };

            function u(t, e) {
                this.content = e, this.content.social_network = t, this.content.attachment = this.content.attachment === a ? "" : this.content.attachment, this.content.time_ago = e.dt_create, this.content.dt_create = this.content.dt_create.valueOf(), this.content.text = s.wrapLinks(s.shorten(e.message + " " + e.description), e.social_network), this.content.moderation_passed = !i.moderation || i.moderation(this.content), l[t].posts.push(this)
            }
            u.prototype = {
                render: function () {
                    var e = l.template(this.content),
                        a = this.content;
                    if (0 !== t(n).children("[social-feed-id=" + a.id + "]").length) return !1;
                    if (0 === t(n).children().length) t(n).append(e);
                    else {
                        var o = -1;
                        if (this.content.social_network, t.each(t(n).children(), function () { }), t(n).append(e), o >= 0) {
                            o++;
                            var s = t(n).children("div:nth-child(" + o + ")"),
                                u = t(n).children("div:last-child");
                            t(u).insertBefore(s)
                        }
                    }
                    if (i.media_min_width) {
                        var d = "[social-feed-id=" + a.id + "] img.attachment",
                            m = t(d),
                            h = new Image,
                            f = m.attr("src");
                        t(h).on("load", function () {
                            h.width < i.media_min_width && m.hide(), delete h
                        }).on("error", function () {
                            m.hide()
                        }).attr({
                            src: f
                        })
                    }

                    if (i.totalPostsNumber)
                        r = i.messagedPosts;

                    ++c == (i.adminPosts ? i.adminPostsNumber : r) && i.callback && i.callback()
                }
            };
            var l = {
                template: !1,
                init: function () {
                    l.getTemplate(function () {
                        o.forEach(function (t) {
                            i[t] && (i[t].accounts ? i[t].accounts.forEach(function (e) {
                                l[t].getData(e)
                            }) : i[t].urls ? i[t].urls.forEach(function (e) {
                                l[t].getData(e)
                            }) : l[t].getData())
                        })
                    })
                },
                getTimeAgo: function (t, source) {

                    //First, we need to format the date pulled from Facebook
                    var timeOffset = 0;
                    if ("fb" === source) {
                        t = t.replace(/-/g, '/').replace('T', ' ').split('+')[0];
                        timeOffset = new Date(t).getTimezoneOffset() * 60 * 1000;
                    }

                    var e = Date.now() - new Date(t).getTime() + timeOffset;

                    // var e = Date.now() - new Date(t).getTime();

                    return e < 6e4 ? 1 < (e = Math.round(e / 1e3)) ? e + " seconds ago" : e + " second ago" : e < 36e5 ? 1 < (e = Math.round(e / 6e4)) ? e + " minutes ago" : e + " minute ago" : e < 864e5 ? 1 < (e = Math.round(e / 36e5)) ? e + " hours ago" : e + " hour ago" : e < 2592e6 ? 1 < (e = Math.round(e / 864e5)) ? e + " days ago" : e + " day ago" : e < 31536e6 ? 1 < (e = Math.round(e / 2592e6)) ? e + i.since.months : e + " month ago" : 1 < (e = Math.round(e / 31536e6)) ? e + i.since.years : e + " year ago"
                },
                getTemplate: function (e) {
                    return l.template ? e() : i.template_html ? (l.template = doT.template(i.template_html), e()) : void t.get(i.template, function (t) {
                        return l.template = doT.template(t), e()
                    })
                },
                twitter: {
                    posts: [],
                    loaded: !1,
                    api: "https://api.twitter.com/1.1/statuses/user_timeline.json",
                    getData: function (t) {
                        var e = new Codebird;
                        switch (e.setToken(i.twitter.token, i.twitter.secret) || e.setConsumerKey(i.twitter.consumer_key, i.twitter.consumer_secret), i.twitter.proxy !== a && e.setProxy(i.twitter.proxy), t[0]) {
                            case "@":
                                var n = t.substr(1);
                                e.__call("statuses_userTimeline", {
                                    id: n,
                                    count: i.twitter.limit,
                                    tweet_mode: void 0 === i.twitter.tweet_mode ? "compatibility" : i.twitter.tweet_mode
                                }, l.twitter.utility.getPosts, !0);
                                break;
                            case "#":
                                // var o = t.substr(1);
                                e.__call("search_tweets", {
                                    q: t,
                                    count: i.twitter.limit,
                                    tweet_mode: void 0 === i.twitter.tweet_mode ? "compatibility" : i.twitter.tweet_mode
                                }, function (d) {
                                    jQuery.ajax({
                                        url: l.twitter.utility.getPosts(d.statuses)
                                    }).done(function () {
                                        i.callback();
                                    })
                                    //                                    l.twitter.utility.getPosts(t.statuses)
                                }, !0)
                        }
                    },
                    utility: {
                        getPosts: function (e) {
                            var counter = 1;
                            e && t.each(e, function () {
                                new u("twitter", l.twitter.utility.unifyPostData(this, counter)).render();
                                counter += counter;
                            })
                        },
                        unifyPostData: function (t, c) {
                            var e = {};
                            function roundDown(number, decimals) {
                                decimals = decimals || 0;
                                return (Math.floor(number * Math.pow(10, decimals)) / Math.pow(10, decimals));
                            }
                            //Format numbers as per Twitter formatting.
                            function formatBigNumbers(num) {
                                if (num >= 1000000000) {
                                    return roundDown((num / 1000000000), 1) + 'G';
                                }
                                if (num >= 1000000) {
                                    return roundDown((num / 1000000), 1) + 'M';
                                }
                                if (num >= 10000) {
                                    return roundDown((num / 1000), 1) + 'K';
                                }
                                return num;
                            }

                            if (t.id && (
                                e.counter = c,
                                e.id = t.id_str,
                                e.screen_name = t.user.screen_name,
                                e.retweet_count = t.retweet_count,
                                e.favorite_count = t.favorite_count,
                                e.showHeader = "yes" === i.twitter.header,
                                e.dt_create = l.getTimeAgo(t.created_at, "twitter"),
                                e.author_link = "https://twitter.com/" + t.user.screen_name,
                                e.author_picture = t.user.profile_image_url_https,
                                e.author_picture_hq = e.author_picture.replace('_normal', ''),
                                e.cover_picture = t.user.profile_banner_url,
                                e.readMore = i.readMore,
                                e.post_url = e.author_link + "/status/" + t.id_str,
                                e.author_name = t.user.name,
                                e.message = void 0 === t.text ? t.full_text : t.text,
                                e.description = "",
                                e.link = "https://twitter.com/" + t.user.screen_name + "/status/" + t.id_str,
                                e.followers_count = formatBigNumbers(t.user.followers_count),
                                e.following_count = formatBigNumbers(t.user.friends_count),
                                e.tweets_count = formatBigNumbers(t.user.statuses_count),
                                !0 === i.show_media && t.entities.media && t.entities.media.length > 0)) {
                                var a = t.entities.media[0].media_url_https;
                                a && (e.attachment = '<img class="attachment" src="' + a + '" />')
                            }

                            return e
                        }
                    }
                },
                facebook: {
                    posts: [],
                    graph: "https://graph.facebook.com/",
                    loaded: !1,
                    getData: function (t) {

                        if (i.facebook.feedObject) {
                            l.facebook.utility.getPosts(i.facebook.feedObject);
                        } else {

                            var e = function (t) { s.request(t, l.facebook.utility.getPosts) },
                                a = "?fields=id,from,message,created_time,admin_creator,story";

                            a += !0 === i.show_media ? ",full_picture" : "";

                            var n, o = "&limit=" + i.facebook.limit,
                                r = "&access_token=" + i.facebook.access_token + "&callback=?";

                            switch (t[0]) {
                                case "@":
                                    var c = t.substr(1);
                                    l.facebook.utility.getUserId(c, function (t) {
                                        "" !== t.id && (n = l.facebook.graph + "v5.0/" + t.id + "/posts" + a + o + r, e(n))
                                    });
                                    break;
                                case "!":
                                    var u = t.substr(1);
                                    n = l.facebook.graph + "v5.0/" + u + "/feed" + a + o + r, e(n);
                                    break;
                                default:
                                    e(n)
                            }

                        }


                    },
                    utility: {
                        getUserId: function (e, a) {
                            var n = "https://graph.facebook.com/" + e + "?&access_token=" + i.facebook.access_token + "&callback=?";
                            t.get(n, a, "json")
                        },
                        prepareAttachment: function (t) {
                            var e = t.full_picture;
                            return -1 !== e.indexOf("_b.") || (-1 !== e.indexOf("safe_image.php") ? e = l.facebook.utility.getExternalImageURL(e, "url") : -1 !== e.indexOf("app_full_proxy.php") ? e = l.facebook.utility.getExternalImageURL(e, "src") : t.object_id && (e = l.facebook.graph + t.object_id + "/picture/?type=normal")), '<img class="attachment" src="' + e + '" />'
                        },
                        getExternalImageURL: function (t, e) {
                            return -1 === (t = decodeURIComponent(t).split(e + "=")[1]).indexOf("fbcdn-sphotos") ? t.split("&")[0] : t
                        },
                        getPosts: function (t) {

                            var showAdminPosts = i.adminPosts;

                            if (t.data)
                                i.totalPostsNumber = t.data.length;

                            t.data && t.data.forEach(function (t) {

                                var shouldRender = (i.show_media && t.full_picture) || t.message;
                                if (null !== t.from && shouldRender) {
                                    i.messagedPosts++;
                                    if (!showAdminPosts) {
                                        new u("facebook", l.facebook.utility.unifyPostData(t)).render()
                                    } else if (t.admin_creator) {
                                        i.adminPostsNumber++;
                                        new u("facebook", l.facebook.utility.unifyPostData(t)).render()
                                    }
                                }

                            });

                        },
                        unifyPostData: function (t) {
                            var e = {},
                                a = t.message ? t.message : t.story;
                            if (e.id = t.id, e.dt_create = l.getTimeAgo(t.created_time, "fb"), e.author_link = "http://facebook.com/" + t.from.id, e.author_picture = l.facebook.graph + t.from.id + "/picture", e.readMore = i.readMore, e.author_name = t.from.name, e.name = t.name || "", e.message = a || "", e.description = t.description ? t.description : "", e.link = t.link ? t.link : "http://facebook.com/" + t.id, !0 === i.show_media && t.full_picture) {
                                var n = l.facebook.utility.prepareAttachment(t);
                                n && (e.attachment = n)
                            }
                            return e
                        }
                    }
                }
            };
            return this.each(function () {
                l.init(), i.update_period && setInterval(function () {
                    return l.init()
                }, i.update_period)
            })
        }
    }(jQuery);