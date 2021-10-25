(function ($) {

    if ('1' === PremiumProSettings.magicSection) {
        $("body").wrapInner('<div class="premium-magic-section-body-inner" />');
    }

    // Hover Box Handler
    var PremiumFlipboxHandler = function ($scope, $) {
        var $flipboxElement = $scope.find(".premium-flip-main-box"),
            height = $flipboxElement.height() / 2,
            width = $flipboxElement.width() / 2;

        if ($scope.hasClass("premium-flip-style-cube")) {

            $flipboxElement.on("mouseenter", function () {

                height = $flipboxElement.height() / 2,
                    width = $flipboxElement.width() / 2;

                moveCube('rotateY', 'premium-flip-frontrl', -90, width);
                moveCube('rotateY', 'premium-flip-backrl', 0, width);

                moveCube('rotateY', 'premium-flip-frontlr', 90, width);
                moveCube('rotateY', 'premium-flip-backlr', 0, width);

                moveCube('rotateX', 'premium-flip-fronttb', -90, height);
                moveCube('rotateX', 'premium-flip-backtb', 0, height);

                moveCube('rotateX', 'premium-flip-frontbt', 90, height);
                moveCube('rotateX', 'premium-flip-backbt', 0, height);

            });

            $flipboxElement.on("mouseleave", function () {
                moveCube('rotateY', 'premium-flip-frontrl', 0, width);
                moveCube('rotateY', 'premium-flip-backrl', 90, width);

                moveCube('rotateY', 'premium-flip-frontlr', 0, width);
                moveCube('rotateY', 'premium-flip-backlr', -90, width);

                moveCube('rotateX', 'premium-flip-fronttb', 0, height);
                moveCube('rotateX', 'premium-flip-backtb', 90, height);

                moveCube('rotateX', 'premium-flip-frontbt', 0, height);
                moveCube('rotateX', 'premium-flip-backbt', -90, height);

            });

        }

        //to rotate elements
        function moveCube(rotation, el, deg, h) {
            $flipboxElement.find('.' + el).css({
                'transform': rotation + '(' + deg + 'deg) translateZ(' + h + 'px)',
                '-webkit-transform': rotation + '(' + deg + 'deg) translateZ(' + h + 'px)',
                '-moz-transform': rotation + '(' + deg + 'deg) translateZ(' + h + 'px)'
            });
        }

        if (!$scope.hasClass("premium-flip-style-flip"))
            return;

        $flipboxElement.on("mouseenter", function () {
            $(this).addClass("flipped");

            if (!$flipboxElement.data("flip-animation"))
                return;
            if (
                $(this)
                    .children(".premium-flip-front")
                    .hasClass("premium-flip-frontrl")
            ) {
                $(this)
                    .find(
                        ".premium-flip-front .premium-flip-front-content-container .premium-flip-front-content .premium-flip-text-wrapper"
                    )
                    .removeClass("PafadeInLeft")
                    .addClass("PafadeInRight");

                $(this)
                    .find(
                        ".premium-flip-back .premium-flip-back-content-container .premium-flip-back-content .premium-flip-back-text-wrapper"
                    )
                    .addClass("PafadeInLeft")
                    .removeClass("PafadeInRight");
            } else if (
                $(this)
                    .children(".premium-flip-front")
                    .hasClass("premium-flip-frontlr")
            ) {
                $(this)
                    .find(
                        ".premium-flip-front .premium-flip-front-content-container .premium-flip-front-content .premium-flip-text-wrapper"
                    )
                    .removeClass("PafadeInRevLeft")
                    .addClass("PafadeInRevRight");

                $(this)
                    .find(
                        ".premium-flip-back .premium-flip-back-content-container .premium-flip-back-content .premium-flip-back-text-wrapper"
                    )
                    .addClass("PafadeInRevLeft")
                    .removeClass("PafadeInRevRight");
            }
        });

        $flipboxElement.on("mouseleave", function () {
            $(this).removeClass("flipped");

            if (!$flipboxElement.data("flip-animation"))
                return;
            if (
                $(this)
                    .children(".premium-flip-front")
                    .hasClass("premium-flip-frontrl")
            ) {
                $(this)
                    .find(
                        ".premium-flip-front .premium-flip-front-content-container .premium-flip-front-content .premium-flip-text-wrapper"
                    )
                    .addClass("PafadeInLeft")
                    .removeClass("PafadeInRight");

                $(this)
                    .find(
                        ".premium-flip-back .premium-flip-back-content-container .premium-flip-back-content .premium-flip-back-text-wrapper"
                    )
                    .removeClass("PafadeInLeft")
                    .addClass("PafadeInRight");
            } else if (
                $(this)
                    .children(".premium-flip-front")
                    .hasClass("premium-flip-frontlr")
            ) {
                $(this)
                    .find(
                        ".premium-flip-front .premium-flip-front-content-container .premium-flip-front-content .premium-flip-text-wrapper"
                    )
                    .addClass("PafadeInRevLeft")
                    .removeClass("PafadeInRevRight");

                $(this)
                    .find(
                        ".premium-flip-back .premium-flip-back-content-container .premium-flip-back-content .premium-flip-back-text-wrapper"
                    )
                    .removeClass("PafadeInRevLeft")
                    .addClass("PafadeInRevRight");
            }
        });
    };

    // Unfold Handler
    var PremiumUnfoldHandler = function ($scope, $) {

        var $unfoldElem = $scope.find(".premium-unfold-wrap"),
            settings = $unfoldElem.data("settings"),
            contentHeight = parseInt($unfoldElem.find(".premium-unfold-content-wrap").outerHeight()),
            foldHeight = settings.foldHeight[elementorFrontend.getCurrentDeviceMode()];

        if (settings.foldSelect === "percent") {
            foldHeight = (foldHeight / 100) * contentHeight;
        }

        $unfoldElem.find(".premium-unfold-button-text").text(settings.foldText);

        $unfoldElem.find(".premium-unfold-content").css('height', foldHeight);

        $unfoldElem.find(".premium-button i").addClass(settings.buttonUnfoldIcon);

        $unfoldElem.on('click', '.premium-button', function (e) {

            e.preventDefault();

            var text = $unfoldElem.find(".premium-unfold-content").hasClass("toggled") ? settings.unfoldText : settings.foldText,
                removeClass = $unfoldElem.find(".premium-unfold-content").hasClass("toggled") ? settings.buttonUnfoldIcon : settings.buttonIcon,
                addClass = $unfoldElem.find(".premium-unfold-content").hasClass("toggled") ? settings.buttonIcon : settings.buttonUnfoldIcon;

            $unfoldElem.find(".premium-unfold-button-text").text(text);

            if ($unfoldElem.find(".premium-unfold-content").hasClass("toggled")) {

                contentHeight = parseInt($unfoldElem.find(".premium-unfold-content-wrap").outerHeight());

                $unfoldElem.find(".premium-unfold-content").animate({ height: contentHeight }, settings.unfoldDur, settings.unfoldEase).removeClass("toggled");

            } else {
                $unfoldElem.find(".premium-unfold-content").animate({ height: foldHeight }, settings.foldDur, settings.foldEase).addClass("toggled");
            }

            $unfoldElem.find(".premium-unfold-gradient").toggleClass("toggled");
            $unfoldElem.find(".premium-button i").removeClass(removeClass).addClass(addClass);
        });
    };

    // Facebook Messenger Handler
    var PremiumFbChatHandler = function ($scope, $) {

        var premiumFbChat = $scope.find(".premium-fbchat-container"),
            premiumFbChatSettings = premiumFbChat.data("settings"),
            currentDevice = elementorFrontend.getCurrentDeviceMode();

        if (premiumFbChat.length > 0) {

            if ("mobile" === currentDevice && premiumFbChatSettings["hideMobile"]) {
                return;
            }

            window.fbAsyncInit = function () {
                FB.init({
                    appId: premiumFbChatSettings["appId"],
                    autoLogAppEvents: !0,
                    xfbml: !0,
                    version: "v2.12"
                });
            };
            (function (a, b, c) {
                var d = a.getElementsByTagName(b)[0];
                a.getElementById(c) ||
                    ((a = a.createElement(b)),
                        (a.id = c),
                        (a.src =
                            "https://connect.facebook.net/" +
                            premiumFbChatSettings["lang"] +
                            "/sdk/xfbml.customerchat.js"),
                        d.parentNode.insertBefore(a, d));
            })(document, "script", "facebook-jssdk");


            $(".elementor-element-overlay .elementor-editor-element-remove").on(
                "click",
                function () {

                    var $this = $(this),
                        parentId = $this.parents("section.elementor-element");

                    if (parentId.find("#premium-fbchat-container").length) {
                        document.location.href = document.location.href;
                    }

                }
            );
        }
    };

    // Twitter Feed Handler
    var PremiumTwitterFeedHandler = function ($scope, $) {
        var $elem = $scope.find(".premium-twitter-feed-wrapper"),
            $loading = $elem.find(".premium-loading-feed"),
            settings = $elem.data("settings"),
            carousel = 'yes' === $elem.data("carousel");

        function get_tweets_data() {
            $elem
                .find(".premium-social-feed-container")
                .socialfeed({
                    twitter: {
                        accounts: settings.accounts,
                        limit: settings.limit || 2,
                        consumer_key: settings.consKey,
                        consumer_secret: settings.consSec,
                        token: "460616970-Deuil3Qx0CnNS2VX9WefxA99gD8OFx1vJ0kn0izb",
                        secret: "GBdekapULnR5iCiLozWQMc9xGYhwZlVO2zKXcpBb7AFFT",
                        tweet_mode: "extended"
                    },
                    length: settings.length || 130,
                    show_media: 'yes' === settings.showMedia,
                    readMore: settings.readMore,
                    template: settings.template,
                    callback: function () {
                        $elem.imagesLoaded(function () {
                            handleTwitterFeed();
                        });
                    }
                });
        }

        function handleTwitterFeed() {
            var headerWrap = $elem.find('.premium-twitter-user-cover');

            if (carousel) {

                var autoPlay = 'yes' === $elem.data("play"),
                    speed = $elem.data("speed") || 5000,
                    rtl = $elem.data("rtl"),
                    colsNumber = $elem.data("col"),
                    prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                    nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>';

                headerWrap.prependTo($elem);
                $(headerWrap).not(':first').remove();

                $elem.find(".premium-social-feed-container").slick({
                    infinite: true,
                    slidesToShow: colsNumber,
                    slidesToScroll: colsNumber,
                    responsive: [{
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                    ],
                    autoplay: autoPlay,
                    autoplaySpeed: speed,
                    rows: 0,
                    rtl: rtl ? true : false,
                    nextArrow: nextArrow,
                    prevArrow: prevArrow,
                    draggable: true,
                    pauseOnHover: true
                });
            }

            if (!carousel && settings.layout === "grid-layout" && !settings.even) {

                var masonryContainer = $elem.find(".premium-social-feed-container");

                masonryContainer.isotope({
                    itemSelector: ".premium-social-feed-element-wrap",
                    percentPosition: true,
                    layoutMode: "masonry",
                    animationOptions: {
                        duration: 750,
                        easing: "linear",
                        queue: false
                    }
                });

                headerWrap.prependTo($elem);
                $(headerWrap).not(':first').remove();

            }
        }

        $.ajax({
            url: get_tweets_data(),
            beforeSend: function () {
                $loading.addClass("premium-show-loading");
            },
            success: function () {
                $loading.removeClass("premium-show-loading");
            },
            error: function () {
                console.log("error getting data from Twitter");
            }
        });

    };


    // Alert Box Handler
    var PremiumAlertBoxHandler = function ($scope, $) {
        var $barElem = $scope.find(".premium-notbar-outer-container"),
            settings = $barElem.data("settings"),
            _this = $($barElem),
            link = settings.link,
            currentDevice = elementorFrontend.getCurrentDeviceMode();

        if (_this.length > 0) {
            if (settings.responsive) {
                if (settings.hideMobs) {
                    if ('mobile' === currentDevice) {
                        $barElem.css("display", "none");
                    }
                }

                if (settings.hideTabs) {
                    if ('tablet' === currentDevice) {
                        $barElem.css("display", "none");
                    }
                }
            }

            if (!elementorFrontend.isEditMode() && (settings.logged || !$("body").hasClass("logged-in"))) {
                if (settings.cookies) {
                    if (notificationReadCookie("premiumNotBar-" + settings.id)) {
                        $barElem.css("display", "none");
                    }
                }
            }

            function notificationSetCookie(cookieName, cookieValue) {
                var today = new Date(),
                    expire = new Date();

                expire.setTime(today.getTime() + 3600000 * settings.interval);

                document.cookie = cookieName + "=" + encodeURI(cookieValue) + ";expires=" + expire.toGMTString() + "; path=/";
            }

            function notificationReadCookie(cookieName) {
                var theCookie = " " + document.cookie;

                var ind = theCookie.indexOf(" " + cookieName + "=");

                if (ind == -1) ind = theCookie.indexOf(";" + cookieName + "=");

                if (ind == -1 || cookieName == "") return "";

                var ind1 = theCookie.indexOf(";", ind + 1);

                if (ind1 == -1) ind1 = theCookie.length;

                return unescape(theCookie.substring(ind + cookieName.length + 2, ind1));
            }

            if (settings.location === "top" && settings.position === "premium-notbar-relative") {
                $($barElem).detach();

                $("body").prepend(_this);
            }

            if (settings.layout === "boxed") {
                var not_width = $barElem
                    .find(".premium-notbar")
                    .parent()
                    .width();

                $barElem.find(".premium-notbar").css("width", not_width);

                $(window).on("resize", function () {
                    var not_width = $barElem
                        .find(".premium-notbar")
                        .parent()
                        .width();

                    $barElem.find(".premium-notbar").css("width", not_width);
                });
            }

            if (!link) {
                $barElem.find(".premium-notbar-close").on("click", function () {

                    $barElem.find(".premium-notbar-background-overlay").remove();

                    if (!elementorFrontend.isEditMode() && (settings.logged || !$("body").hasClass("logged-in"))) {
                        if (settings.cookies) {
                            if (!notificationReadCookie("premiumNotBar-" + settings.id)) {
                                notificationSetCookie("premiumNotBar-" + settings.id, true);
                            }
                        }
                    }

                    if ($(this).hasClass("premium-notbar-top") || $(this).hasClass("premium-notbar-edit-top")) {
                        if (settings.position === "premium-notbar-fixed") {
                            $(this)
                                .parentsUntil(".premium-notbar-outer-container")
                                .css("top", "-1000px");
                        } else {
                            $($barElem).animate({
                                height: "0"
                            }, 300);
                        }
                    } else if ($(this).hasClass("premium-notbar-bottom")) {
                        $(this)
                            .parentsUntil(".premium-notbar-outer-container")
                            .css("bottom", "-1000px");
                    } else {
                        $(this)
                            .parentsUntil(".premium-notbar-outer-container")
                            .css({
                                visibility: "hidden",
                                opacity: "0"
                            });
                    }
                });
            }
        }
    };

    // Charts Handler
    var PremiumChartHandler = function ($scope, $) {
        var $chartElem = $scope.find(".premium-chart-container"),
            settings = $chartElem.data("settings"),
            currentDevice = elementorFrontend.getCurrentDeviceMode(),
            dataSource = $chartElem.data("source"),
            type = settings.type,
            eventsArray = [
                "mousemove",
                "mouseout",
                "click",
                "touchstart",
                "touchmove"
            ],
            printVal = settings.printVal,
            event =
                ("pie" === type || "doughnut" === type) && printVal ? false : eventsArray,
            premiumChartData = $chartElem.data("chart"),
            data = {
                labels: settings.xlabels,
                datasets: []
            },
            chartInstance = null;

        if ("desktop" !== currentDevice) {
            if (settings.legRes)
                settings.legDis = false;

            settings.legPos = settings['legPos_' + currentDevice];
        }


        function renderChart() {

            var ctx = document
                .getElementById(settings.chartId)
                .getContext("2d");

            var globalOptions = {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: "polarArea" === type ? 6 : 0
                    }
                },
                events: event,
                animation: {
                    duration: settings.duration,
                    easing: settings.easing,
                    onComplete: function () {
                        if (!event) {
                            this.defaultFontSize = 16;
                            ctx.font =
                                '15px "Helvetica Neue", "Helvetica", "Arial", sans-serif';

                            ctx.textAlign = "center";
                            ctx.textBaseline = "bottom";

                            this.data.datasets.forEach(function (dataset) {
                                for (var i = 0; i < dataset.data.length; i++) {
                                    var model =
                                        dataset._meta[Object.keys(dataset._meta)[0]].data[i]
                                            ._model,
                                        total =
                                            dataset._meta[Object.keys(dataset._meta)[0]].total,
                                        mid_radius =
                                            model.innerRadius +
                                            (model.outerRadius - model.innerRadius) / 2,
                                        start_angle = model.startAngle,
                                        end_angle = model.endAngle,
                                        mid_angle = start_angle + (end_angle - start_angle) / 2;

                                    var x = mid_radius * Math.cos(mid_angle);
                                    var y = mid_radius * Math.sin(mid_angle);

                                    ctx.fillStyle = settings.yTicksCol;

                                    var percent =
                                        String(Math.round((dataset.data[i] / total) * 100)) + "%";

                                    ctx.fillText(percent, model.x + x, model.y + y + 15);
                                }
                            });
                        }
                    }
                },
                tooltips: {
                    enabled: settings.enTooltips,
                    mode: settings.modTooltips,
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var prefixString = "";
                            if ("pie" == type || "doughnut" == type || "polarArea" == type) {
                                prefixString = data.labels[tooltipItem.index] + ": ";
                            }

                            var dataset = data.datasets[tooltipItem.datasetIndex];

                            var total = dataset.data.reduce(function (previousValue, currentValue) {
                                return parseFloat(previousValue) + parseFloat(currentValue);
                            });

                            var currentValue = dataset.data[tooltipItem.index];

                            var percentage = ((currentValue / total) * 100).toPrecision(3);

                            return (
                                prefixString +
                                (settings.percentage ?
                                    percentage + "%" :
                                    currentValue)
                            );
                        }
                    }
                },
                legend: {
                    display: settings.legDis,
                    position: settings.legPos,
                    reverse: settings.legRev,
                    labels: {
                        usePointStyle: settings.legCircle,
                        boxWidth: parseInt(settings.itemWid),
                        fontColor: settings.legCol,
                        fontSize: parseInt(settings.legSize)
                    }
                }

            };

            var multiScaleOptions = {
                scales: {
                    xAxes: [{
                        barPercentage: settings.xwidth,
                        display: ("pie" === type || "doughnut" === type) ? false : true,
                        gridLines: {
                            display: settings.xGrid,
                            color: settings.xGridCol,
                            lineWidth: settings.xGridWidth,
                            drawBorder: true
                        },
                        scaleLabel: {
                            display: settings.xlabeldis,
                            labelString: settings.xlabel,
                            fontColor: settings.xlabelcol,
                            fontSize: settings.xlabelsize
                        },
                        ticks: {
                            fontSize: settings.xTicksSize,
                            fontColor: settings.xTicksCol,
                            stepSize: settings.stepSize,
                            maxRotation: settings.xTicksRot,
                            minRotation: settings.xTicksRot,
                            beginAtZero: settings.xTicksBeg,
                            callback: function (tick) {
                                var locale = settings.locale || false;
                                return tick.toLocaleString(locale);
                            }
                        }
                    }],
                    yAxes: [{
                        display: ("pie" === type || "doughnut" === type) ? false : true,
                        type: settings.yAxis,
                        gridLines: {
                            display: settings.yGrid,
                            color: settings.yGridCol,
                            lineWidth: settings.yGridWidth,
                        },
                        scaleLabel: {
                            display: settings.ylabeldis,
                            labelString: settings.ylabel,
                            fontColor: settings.ylabelcol,
                            fontSize: settings.ylabelsize
                        },
                        ticks: {
                            suggestedMin: settings.suggestedMin,
                            suggestedMax: settings.suggestedMax,
                            fontSize: settings.yTicksSize,
                            fontColor: settings.yTicksCol,
                            beginAtZero: settings.yTicksBeg,
                            stepSize: settings.stepSize,
                            callback: function (tick) {
                                var locale = settings.locale || false;
                                return tick.toLocaleString(locale);
                            }
                        }
                    }]
                }
            };

            var singleScaleOptions = {
                scale: {
                    ticks: {
                        beginAtZero: settings.yTicksBeg,
                        stepSize: settings.stepSize,
                        suggestedMax: settings.suggestedMax,
                        callback: function (tick) {
                            var locale = settings.locale || false;
                            return tick.toLocaleString(locale);
                        }
                    }
                }
            };

            chartInstance = new Chart(ctx, {
                type: type,
                data: data,
                options: Object.assign(globalOptions, ("radar" !== type && "polarArea" !== type) ? multiScaleOptions : singleScaleOptions)
            });

            if ('custom' === dataSource) {

                premiumChartData.forEach(function (element) {

                    if ("pie" !== type && "doughnut" !== type && "polarArea" !== type) {
                        if ("object" === typeof element.backgroundColor) {

                            //We need to make sure add gradient colors or not.
                            if ("empty" !== element.backgroundColor[element.backgroundColor.length - 1]) {
                                var gradient = ctx.createLinearGradient(0, 0, 0, 600),
                                    secondColor = element.backgroundColor[1] ?
                                        element.backgroundColor[1] :
                                        element.backgroundColor[0];

                                gradient.addColorStop(0, element.backgroundColor[0]);
                                gradient.addColorStop(1, secondColor);
                                element.backgroundColor = gradient;
                                element.hoverBackgroundColor = gradient;
                            }

                        }
                    }

                    data.datasets.push(element);
                    chartInstance.update();
                });

                $("#" + settings.chartId).on("click", function (evt) {
                    var activePoint = chartInstance.getElementAtEvent(evt);
                    if (activePoint[0]) {
                        var URL =
                            chartInstance.data.datasets[activePoint[0]._datasetIndex].links[
                            activePoint[0]._index
                            ];
                        if (URL != null && URL != "") {
                            window.open(URL, settings.target);
                        }
                    }
                });

            }
        }

        function handleChartData(res) {

            var rowsData = res.split(/\r?\n|\r/),
                labels = (rowsData.shift()).split(premiumChartData.separator);

            data.labels = labels;
            rowsData.forEach(function (row, index) {
                if (row.length !== 0) {
                    var colData = {};

                    colData.data = row.split(premiumChartData.separator);
                    //add properties only if repeater element exists
                    if (premiumChartData.props[index]) {
                        colData.borderColor = premiumChartData.props[index].borderColor;
                        colData.borderWidth = premiumChartData.props[index].borderWidth;
                        colData.backgroundColor = premiumChartData.props[index].backgroundColor;
                        colData.hoverBackgroundColor = premiumChartData.props[index].hoverBackgroundColor;
                        colData.label = premiumChartData.props[index].title;
                    }

                    data.datasets.push(colData);
                    chartInstance.update();

                }
            });
        }

        var $checkModal = $chartElem.closest(".premium-modal-box-modal");

        if ($checkModal.length || "load" === settings.event) {

            getChartData();

        } else {
            new Waypoint({
                element: $("#" + settings.chartId),
                offset: Waypoint.viewportHeight() - 250,
                triggerOnce: true,
                handler: function () {
                    getChartData();
                    this.destroy();
                }
            });
        }

        function getChartData() {

            if ('custom' === dataSource) {
                renderChart();
            } else {

                $chartElem.append('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');

                if (premiumChartData.url) {
                    $.ajax({
                        url: premiumChartData.url,
                        type: "GET",
                        success: function (res) {
                            console.log(res);
                            $chartElem.find(".premium-loading-feed").remove();
                            renderChart();
                            handleChartData(res);
                        },
                        error: function (err) {
                            console.log(err);
                        }
                    });
                }

            }

        }

    };

    // Instagram Feed Handler
    var instaCounter = 0;
    var PremiumInstaFeedHandler = function ($scope, $) {
        instaCounter++;

        var $instaElem = $scope.find(".premium-instafeed-container"),
            $loading = $instaElem.find(".premium-loading-feed"),
            settings = $instaElem.data("settings"),
            carousel = $instaElem.data("carousel");

        if (!settings)
            return;

        var token = settings.accesstok;

        if (!token)
            return;

        $.ajax({
            url: PremiumProSettings.ajaxurl,
            type: "GET",
            dataType: "JSON",
            data: {
                action: 'check_instagram_token',
                security: PremiumProSettings.nonce,
                token: token
            },
            success: function (res) {

                if (!res.data.isValid)
                    token = res.data.newToken;

                instaFeedInit();
            },
            error: function (err) {
                console.log(err);
            }
        });

        function instaFeedInit() {

            var feed = new Instafeed({
                api: settings.api,
                target: settings.id,
                accessToken: token,
                get: "user",
                tagName: settings.tags,
                sortBy: settings.sort,
                limit: settings.limit,
                videos: settings.videos,
                words: settings.words,
                templateData: {
                    likes: settings.likes,
                    comments: settings.comments,
                    description: settings.description,
                    link: settings.link,
                    share: settings.share
                },
                afterLoad: function () {

                    //Remove loading spinner
                    $loading.removeClass("premium-show-loading");

                    setTimeout(function () {
                        $($instaElem).find(".premium-insta-feed-wrap a[data-rel^='prettyPhoto']")
                            .prettyPhoto({
                                theme: settings.theme,
                                hook: "data-rel",
                                opacity: 0.7,
                                show_title: false,
                                deeplinking: false,
                                overlay_gallery: false,
                                custom_markup: "",
                                default_width: 900,
                                default_height: 506,
                                social_tools: ""
                            });
                    }, 100);

                    setTimeout(function () {
                        $instaElem.imagesLoaded(function () {

                            if (carousel) {
                                instaCarouselHandler();
                            } else if (settings.masonry) {
                                instagramMasonryGrid();
                            }
                        });
                    }, 1000);

                }
            });

            try {
                feed.run();
            } catch (err) {
                console.log(err);
            }

        }

        function instagramMasonryGrid() {
            $instaElem.isotope({
                itemSelector: ".premium-insta-feed",
                percentPosition: true,
                layoutMode: "masonry",
                animationOptions: {
                    duration: 750,
                    easing: "linear",
                    queue: false
                }
            });

        }

        function instaCarouselHandler() {

            var autoPlay = $instaElem.data("play"),
                speed = $instaElem.data("speed"),
                rtl = $instaElem.data("rtl"),
                colsNumber = $instaElem.data("col"),
                prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>';

            $instaElem.find(".premium-insta-grid").slick({
                infinite: true,
                slidesToShow: colsNumber,
                slidesToScroll: colsNumber,
                responsive: [{
                    breakpoint: 1025,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
                ],
                autoplay: autoPlay,
                autoplaySpeed: speed,
                rows: 0,
                rtl: rtl ? true : false,
                nextArrow: nextArrow,
                prevArrow: prevArrow,
                draggable: true,
                pauseOnHover: true
            });
        }

        //Handle Instagram Videos
        if (settings.videos) {
            $instaElem.on('click', '.premium-insta-video-wrap', function () {
                var $instaVideo = $(this).find("video");
                $instaVideo.get(0).play();
                $instaVideo.css("visibility", "visible");
            });
        }
    };

    // Facebook Feed Handler
    var PremiumFacebookHandler = function ($scope, $) {

        var premiumFacebookFeedElement = $scope.find(".premium-facebook-feed-wrapper"),
            $loading = premiumFacebookFeedElement.find(".premium-loading-feed"),
            settings = premiumFacebookFeedElement.data("settings"),
            carousel = 'yes' === premiumFacebookFeedElement.data("carousel");

        if (!settings)
            return;

        function get_facebook_data() {
            premiumFacebookFeedElement
                .find(".premium-social-feed-container")
                .socialfeed({
                    facebook: {
                        accounts: [settings.accounts],
                        limit: settings.limit || 2,
                        access_token: settings.accessTok
                    },
                    length: settings.length || 130,
                    show_media: 'yes' === settings.showMedia,
                    readMore: settings.readMore,
                    template: settings.template,
                    adminPosts: settings.adminPosts,
                    callback: function () {
                        premiumFacebookFeedElement.imagesLoaded(function () {
                            handleFacebookFeed();
                        });
                    }
                });
        }

        //new function for handling carousel option
        function handleFacebookFeed() {
            if (carousel) {

                var autoPlay = 'yes' === premiumFacebookFeedElement.data("play"),
                    speed = premiumFacebookFeedElement.data("speed") || 5000,
                    rtl = premiumFacebookFeedElement.data("rtl"),
                    colsNumber = premiumFacebookFeedElement.data("col"),
                    prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                    nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>';

                premiumFacebookFeedElement.find(".premium-social-feed-container").slick({
                    infinite: true,
                    slidesToShow: colsNumber,
                    slidesToScroll: colsNumber,
                    responsive: [{
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                    ],
                    autoplay: autoPlay,
                    autoplaySpeed: speed,
                    rows: 0,
                    rtl: rtl ? true : false,
                    nextArrow: nextArrow,
                    prevArrow: prevArrow,
                    draggable: true,
                    pauseOnHover: true
                });
            }

            if (!carousel && settings.layout === "grid-layout" && !settings.even) {

                var masonryContainer = premiumFacebookFeedElement.find(".premium-social-feed-container");

                masonryContainer.isotope({
                    itemSelector: ".premium-social-feed-element-wrap",
                    percentPosition: true,
                    layoutMode: "masonry",
                    animationOptions: {
                        duration: 750,
                        easing: "linear",
                        queue: false
                    }
                });
            }
        }

        $.ajax({
            url: get_facebook_data(),
            beforeSend: function () {
                $loading.addClass("premium-show-loading");
            },
            success: function () {
                $loading.removeClass("premium-show-loading");
            },
            error: function () {
                console.log("error getting data from Facebook");
            }
        });
    };


    // Tabs Handler
    var PremiumTabsHandler = function ($scope, $) {

        var $premiumTabsElem = $scope.find(".premium-tabs-container"),
            currentDevice = elementorFrontend.getCurrentDeviceMode(),
            settings = $premiumTabsElem.data("settings");

        var $navList = $premiumTabsElem.find(".premium-tabs-nav-list");

        if (settings.carousel) {
            if (-1 === settings.carousel_devices.indexOf(currentDevice)) {
                settings.carousel = false;
                $premiumTabsElem.removeClass("elementor-invisible");
            }
        }

        if (settings.carousel) {

            var prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>';

            //Make sure slick is initialized before showing tabs
            $navList.on('init', function () {
                $premiumTabsElem.removeClass("elementor-invisible");
            });

            $navList.slick({
                slidesToShow: settings.slides || 5,
                responsive: [{
                    breakpoint: 1025,
                    settings: {
                        slidesToShow: settings.slides_tab,
                        centerPadding: settings.spacing_tab + "px",
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: settings.slides_mob,
                        centerPadding: settings.spacing_mob + "px",
                    }
                }
                ],
                infinite: true,
                autoplay: false,
                nextArrow: settings.arrows ? nextArrow : '',
                prevArrow: settings.arrows ? prevArrow : '',
                draggable: false,
                rows: 0,
                dots: false,
                useTransform: true,
                centerMode: true,
                centerPadding: settings.spacing + "px",
            });


            $navList.on('click', '.premium-tabs-nav-list-item', function () {
                var tabIndex = $(this).data("slick-index");
                $navList.slick('slickGoTo', tabIndex);
            });

        }

        new CBPFWTabs($premiumTabsElem, settings);


        $(document).ready(function () {

            settings.navigation.map(function (item, index) {

                if (item) {
                    $(item).on("click", function () {

                        if (!settings.carousel) {
                            var $tabToActivate = $navList.find("li").eq(index);

                            $tabToActivate.trigger('click');
                        } else {
                            var activeTabHref = $scope.find(".premium-content-wrap section").eq(index).attr("id");

                            $("a[href=#" + activeTabHref + "]").eq(1).trigger('click');

                            $scope.find(".slick-current").trigger('click');
                        }


                    })
                }

            })
        });

    };


    window.CBPFWTabs = function (t, settings) {

        var self = this,
            id = settings.id,
            i = settings.start;

        self.el = t;

        self.options = {
            start: i
        };

        self.extend = function (t, s) {
            for (var i in s) s.hasOwnProperty(i) && (t[i] = s[i]);
            return t;
        };

        self._init = function () {

            self.tabs = $(id).find(".premium-tabs-nav").first().find("li");
            self.items = $(id).find(".premium-content-wrap").first().find("> section");

            self.current = -1;

            //No tabs will be active by default
            if (-1 !== self.options.start)
                self._show();

            self._initEvents();
        };

        self._initEvents = function () {

            self.tabs.each(function (index, tab) {

                var listIndex = $(tab).data("list-index");

                tab.addEventListener("click", function (s) {
                    s.preventDefault();

                    //If tab is clicked twice
                    if ($(tab).hasClass("tab-current"))
                        return;

                    self._show(listIndex, tab);
                });
            });
        };

        self._show = function (tabIndex, tab) {

            if (self.current >= 0) {
                self.tabs.removeClass("tab-current");
                self.items.removeClass("content-current");
            }

            self.current = tabIndex;

            //Activate the first tab if no tab is clicked yet
            if (void 0 == tabIndex) {
                self.current = self.options.start >= 0 && self.options.start < self.items.length ? self.options.start : 0;
                tab = settings.carousel ? self.tabs.filter(".slick-center:not(.slick-cloned)") : self.tabs[self.current];
            }

            if (settings.carousel) {
                setTimeout(function () {
                    self.tabs.filter(".slick-center:not(.slick-cloned)").addClass("tab-current");
                }, 100);
            } else {
                $(tab).addClass("tab-current");
            }

            var $activeContent = self.items.eq(self.current);

            $activeContent.addClass("content-current");

            //Fix Media Grid height issue.
            if ($activeContent.find(".premium-gallery-container").length) {
                var $mediaGrid = $activeContent.find(".premium-gallery-container"),
                    layout = $mediaGrid.data("settings").img_size;
                setTimeout(function () {

                    if (0 === $mediaGrid.outerHeight()) {
                        if ('metro' === layout) {
                            $mediaGrid.trigger('resize');
                        } else {
                            $mediaGrid.isotope("layout");
                        }
                    }

                }, 100);

            }
        };

        self.options = self.extend({}, self.options);
        self.extend(self.options, i);
        self._init();

    };

    // Magic Section Handler
    var PremiumMagicSectionHandler = function ($scope, $) {

        var $bodyInnerWrap = $("body .premium-magic-section-body-inner"),
            currentDevice = elementorFrontend.getCurrentDeviceMode(),
            $magicElem = $scope.find(".premium-magic-section-wrap"),
            premiumMagicSectionWrap = $scope.find(".premium-magic-section-container"),
            settings = $magicElem.data("settings"),
            offset,
            offsetAw,
            gutter,
            inIcon = settings["inIcon"],
            outIcon = settings["outIcon"];

        function getWraptoOrg() {
            $bodyInnerWrap.css({
                top: 0,
                left: 0,
                right: 0
            });
        }

        getWraptoOrg();

        gutter = getGutter($magicElem);

        $magicElem.ready(function () {

            var $magicContent = $magicElem.find(".premium-magic-section-content-wrap");

            if ($magicContent.outerWidth() > $magicElem.outerWidth())
                $magicElem
                    .find(".premium-magic-section-content-wrap-out")
                    .css("overflow-x", "scroll");

            if ($magicContent.outerHeight() > $magicElem.outerHeight())
                $magicElem
                    .find(".premium-magic-section-content-wrap-out")
                    .css("overflow-y", "scroll");

            switch (settings.position) {
                case "top":
                    offset = -1 * ($magicElem.outerHeight() - gutter);
                    $magicElem.css("top", offset);
                    break;
                case "right":
                    offset = -1 * ($magicElem.outerWidth() - gutter);
                    $magicElem.css("right", offset);
                    break;
                case "left":
                    offset = -1 * ($magicElem.outerWidth() - gutter);
                    $magicElem.css("left", offset);
                    break;
            }
        });

        function getGutter(elem) {

            var settings = $(elem).data("settings"),
                gutter =
                    settings.position === "top" || settings.position === "bottom" ?
                        (settings.gutter / 100) * $(elem).outerHeight() :
                        (settings.gutter / 100) * $(elem).outerWidth();
            return gutter;

        }

        if (settings.responsive) {
            if (settings.hideMobs) {
                if ('mobile' === currentDevice) {
                    premiumMagicSectionWrap.css("display", "none");

                    $(window).on("resize", function () {
                        premiumMagicSectionWrap.css("display", "none");
                    });
                }
            }

            if (settings.hideTabs) {
                if ('tablet' === currentDevice) {
                    premiumMagicSectionWrap.css("display", "none");

                    $(window).on("resize", function () {
                        premiumMagicSectionWrap.css("display", "none");
                    });
                }
            }
        }

        $magicElem
            .find(".premium-magic-section-icon-wrap .premium-magic-section-btn")
            .on("click", function () {
                var nearestMagicSection = $(this).closest(
                    ".premium-magic-section-wrap"
                ),
                    magicSections = $("body")
                        .find("div.premium-magic-section-wrap")
                        .not(nearestMagicSection);
                $.each(magicSections, function (index, elem) {
                    if ($(elem).hasClass("in")) {
                        var sectionPos = $(elem).data("settings")["position"],
                            style = $(elem).data("settings")["style"],
                            inIconAw = $(elem).data("settings")["inIcon"],
                            outIconAw = $(elem).data("settings")["outIcon"],
                            gutterAw = getGutter(elem);
                        if (style === "push") {
                            getWraptoOrg();
                        }
                        $(elem)
                            .find(".premium-magic-section-btn")
                            .removeClass(outIconAw)
                            .addClass(inIconAw);
                        $(elem).toggleClass("in out");
                        switch (sectionPos) {
                            case "top":
                                offsetAw = -1 * ($(elem).outerHeight() - gutterAw);
                                $(elem).animate({
                                    top: offsetAw
                                }, "fast", "linear");
                                break;
                            case "bottom":
                                offsetAw = -1 * ($(elem).outerHeight() - gutterAw);
                                $(elem).animate({
                                    bottom: offsetAw
                                }, "fast", "linear");
                                break;
                            case "left":
                                offsetAw = -1 * ($(elem).outerWidth() - gutterAw);
                                $(elem).animate({
                                    left: offsetAw
                                }, "fast", "linear");
                                break;
                            case "right":
                                offsetAw = -1 * ($(elem).outerWidth() - gutterAw);
                                $(elem).animate({
                                    right: offsetAw
                                }, "fast", "linear");
                                break;
                        }
                    }
                });
                if (nearestMagicSection.hasClass("out")) {
                    $(this)
                        .removeClass(inIcon)
                        .addClass(outIcon);
                } else {
                    $(this)
                        .removeClass(outIcon)
                        .addClass(inIcon);
                }
                if (nearestMagicSection.hasClass("out")) {
                    nearestMagicSection
                        .parent()
                        .siblings(".premium-magic-section-overlay")
                        .addClass("active");
                } else {
                    nearestMagicSection
                        .parent()
                        .siblings(".premium-magic-section-overlay")
                        .removeClass("active");
                }
                nearestMagicSection.toggleClass("in out");
                switch (settings.position) {
                    case "top":
                        offset = -1 * ($magicElem.outerHeight() - gutter);
                        if (nearestMagicSection.hasClass("out")) {
                            nearestMagicSection.animate({
                                top: offset
                            }, "fast", "linear");
                            if (settings.style == "push") {
                                $bodyInnerWrap.animate({
                                    top: 0
                                },
                                    "fast",
                                    "linear"
                                );
                            }
                        } else {
                            nearestMagicSection.animate({
                                top: 0
                            }, "fast", "linear");
                            if (settings.style == "push") {
                                $bodyInnerWrap.animate({
                                    top: -1 * offset
                                },
                                    "fast",
                                    "linear"
                                );
                            }
                        }
                        break;
                    case "bottom":
                        offset = -1 * ($magicElem.outerHeight() - gutter);
                        if (nearestMagicSection.hasClass("out")) {
                            nearestMagicSection.animate({
                                bottom: offset
                            }, "fast", "linear");
                        } else {
                            nearestMagicSection.animate({
                                bottom: 0
                            }, "fast", "linear");
                        }
                        break;
                    case "right":
                        offset = -1 * ($magicElem.outerWidth() - gutter);
                        if (nearestMagicSection.hasClass("out")) {
                            nearestMagicSection.animate({
                                right: offset
                            }, "fast", "linear");
                            if (settings.style == "push") {
                                $bodyInnerWrap.css("left", "auto").animate({
                                    right: 0
                                },
                                    "fast",
                                    "linear"
                                );
                            }
                        } else {
                            nearestMagicSection.animate({
                                right: 0
                            }, "fast", "linear");
                            if (settings.style == "push") {
                                $bodyInnerWrap.css("left", "auto").animate({
                                    right: -1 * offset
                                },
                                    "fast",
                                    "linear"
                                );
                            }
                        }
                        break;
                    case "left":
                        offset = -1 * ($magicElem.outerWidth() - gutter);
                        if (nearestMagicSection.hasClass("out")) {
                            nearestMagicSection.animate({
                                left: offset
                            }, "fast", "linear");
                            if (settings["style"] == "push") {
                                $bodyInnerWrap.css("right", "auto").animate({
                                    left: 0
                                },
                                    "fast",
                                    "linear"
                                );
                            }
                        } else {
                            nearestMagicSection.animate({
                                left: 0
                            }, "fast", "linear");
                            if (settings.style == "push") {
                                $bodyInnerWrap.css("right", "auto").animate({
                                    left: -1 * offset
                                },
                                    "fast",
                                    "linear"
                                );
                            }
                        }
                        break;
                }
            });

        premiumMagicSectionWrap
            .siblings(".premium-magic-section-overlay")
            .on("click", function () {
                $magicElem
                    .siblings(".premium-magic-section-button-trig")
                    .children(".premium-magic-section-btn")
                    .trigger("click");
                $magicElem
                    .find(".premium-magic-section-icon-wrap")
                    .children(".premium-magic-section-btn")
                    .trigger("click");
            });

        $("body").on("click", function (event) {
            var trigButton =
                "div.premium-magic-section-button-trig .premium-magic-section-btn",
                trigIcon =
                    "div.premium-magic-section-icon-wrap .premium-magic-section-btn",
                buttonContent = ".premium-magic-section-btn *",
                magicSec = "div.premium-magic-section-content-wrap-out",
                magicSecContent = "div.premium-magic-section-content-wrap-out *";
            if (
                !$(event.target).is($(buttonContent)) &&
                !$(event.target).is($(trigButton)) &&
                !$(event.target).is($(trigIcon)) &&
                !$(event.target).is($(magicSec)) &&
                !$(event.target).is($(magicSecContent))
            ) {
                if ($magicElem.hasClass("in")) {
                    $magicElem
                        .siblings(".premium-magic-section-button-trig")
                        .children(".premium-magic-section-btn")
                        .trigger("click");
                    $magicElem
                        .find(".premium-magic-section-icon-wrap")
                        .children(".premium-magic-section-btn")
                        .trigger("click");
                }
            }
        });

        $magicElem
            .find(".premium-magic-section-close-wrap")
            .on("click", function () {
                if ($magicElem.hasClass("in")) {
                    $(this)
                        .parent()
                        .siblings(".premium-magic-section-button-trig")
                        .children(".premium-magic-section-btn")
                        .trigger("click");
                    $(this)
                        .siblings(".premium-magic-section-icon-wrap")
                        .children(".premium-magic-section-btn")
                        .trigger("click");
                }
            });

        $magicElem
            .siblings(".premium-magic-section-button-trig")
            .children(".premium-magic-section-btn")
            .on("click", function () {
                var nearestMagicSection = $(this)
                    .closest(".premium-magic-section-button-trig")
                    .siblings(".premium-magic-section-wrap"),
                    magicSections = $("body")
                        .find("div.premium-magic-section-wrap")
                        .not(nearestMagicSection);
                nearestMagicSection.toggleClass("in out");
                $.each(magicSections, function (index, elem) {
                    if ($(elem).hasClass("in")) {
                        var sectionPos = $(elem).data("settings")["position"],
                            style = $(elem).data("settings")["style"],
                            inIconAw = $(elem).data("settings")["inIcon"],
                            outIconAw = $(elem).data("settings")["outIcon"],
                            gutterAw = getGutter(elem);

                        if (style === "push") {
                            getWraptoOrg();
                        }
                        $(elem)
                            .find(".premium-magic-section-btn")
                            .removeClass(outIconAw)
                            .addClass(inIconAw);
                        $(elem).toggleClass("in out");
                        switch (sectionPos) {
                            case "top":
                                offsetAw = -1 * ($(elem).outerHeight() - gutterAw);
                                $(elem).animate({
                                    top: offsetAw
                                }, "fast", "linear");
                                break;
                            case "bottom":
                                offsetAw = -1 * ($(elem).outerHeight() - gutterAw);
                                $(elem).animate({
                                    bottom: offsetAw
                                }, "fast", "linear");
                                break;
                            case "left":
                                offsetAw = -1 * ($(elem).outerWidth() - gutterAw);
                                $(elem).animate({
                                    left: offsetAw
                                }, "fast", "linear");
                                break;
                            case "right":
                                offsetAw = -1 * ($(elem).outerWidth() - gutterAw);
                                $(elem).animate({
                                    right: offsetAw
                                }, "fast", "linear");
                                break;
                        }
                    }
                });
                if (nearestMagicSection.hasClass("out")) {
                    nearestMagicSection
                        .parent()
                        .siblings(".premium-magic-section-overlay")
                        .removeClass("active");
                } else {
                    nearestMagicSection
                        .parent()
                        .siblings(".premium-magic-section-overlay")
                        .addClass("active");
                }
                switch (settings["position"]) {
                    case "top":
                        offset = -1 * ($magicElem.outerHeight() - gutter);
                        if (nearestMagicSection.hasClass("out")) {
                            nearestMagicSection.animate({
                                top: offset
                            }, "fast", "linear");
                            if (settings["style"] == "push") {
                                $bodyInnerWrap.animate({
                                    top: 0
                                },
                                    "fast",
                                    "linear"
                                );
                            }
                        } else {
                            nearestMagicSection.animate({
                                top: 0
                            }, "fast", "linear");
                            if (settings["style"] == "push") {
                                $bodyInnerWrap.animate({
                                    top: -1 * offset
                                },
                                    "fast",
                                    "linear"
                                );
                            }
                        }
                        break;
                    case "bottom":
                        offset = -1 * ($magicElem.outerHeight() - gutter);
                        if (nearestMagicSection.hasClass("out")) {
                            nearestMagicSection.animate({
                                bottom: offset
                            }, "fast", "linear");
                        } else {
                            nearestMagicSection.animate({
                                bottom: 0
                            }, "fast", "linear");
                        }
                        break;
                    case "right":
                        offset = -1 * ($magicElem.outerWidth() - gutter);
                        if (nearestMagicSection.hasClass("out")) {
                            nearestMagicSection.animate({
                                right: offset
                            }, "fast", "linear");
                            if (settings["style"] == "push") {
                                $bodyInnerWrap.css("left", "auto").animate({
                                    right: 0
                                },
                                    "fast",
                                    "linear"
                                );
                            }
                        } else {
                            nearestMagicSection.animate({
                                right: 0
                            }, "fast", "linear");
                            if (settings["style"] == "push") {
                                $bodyInnerWrap.css("left", "auto").animate({
                                    right: -1 * offset
                                },
                                    "fast",
                                    "linear"
                                );
                            }
                        }
                        break;
                    case "left":
                        offset = -1 * ($magicElem.outerWidth() - gutter);
                        if (nearestMagicSection.hasClass("out")) {
                            nearestMagicSection.animate({
                                left: offset
                            }, "fast", "linear");
                            if (settings["style"] == "push") {
                                $bodyInnerWrap.css("right", "auto").animate({
                                    left: 0
                                },
                                    "fast",
                                    "linear"
                                );
                            }
                        } else {
                            nearestMagicSection.animate({
                                left: 0
                            }, "fast", "linear");
                            if (settings["style"] == "push") {
                                $bodyInnerWrap.css("right", "auto").animate({
                                    left: -1 * offset
                                },
                                    "fast",
                                    "linear"
                                );
                            }
                        }
                        break;
                }
            });

        $magicElem.removeClass('magic-section-hide');
    };

    // Preview Window Handler
    var PremiumPreviewWindowHandler = function ($scope, $) {
        var $prevWinElem = $scope.find(".premium-preview-image-wrap"),
            settings = $prevWinElem.data("settings"),
            currentDevice = elementorFrontend.getCurrentDeviceMode(),
            minWidth = null,
            maxWidth = null;

        if ('mobile' === currentDevice) {
            minWidth = settings.minWidthMobs;
            maxWidth = settings.maxWidthMobs;
            //We need to make sure that content will not go out of screen.
            settings.side = ['top', 'bottom'];
        } else if ('tablet' === currentDevice) {
            minWidth = settings.minWidthTabs;
            maxWidth = settings.maxWidthTabs;
        } else {
            minWidth = settings.minWidth;
            maxWidth = settings.maxWidth;
        }

        if (settings.responsive) {

            var previewImageOffset = $prevWinElem.offset().left;

            if (previewImageOffset < settings.minWidth) {
                var difference = settings.minWidth - previewImageOffset;

                settings.minWidth = settings.minWidth - difference;
            }
        }


        var $figure = $prevWinElem.find('.premium-preview-image-figure'),
            floatData = $figure.data();

        if (floatData.float) {

            var animeSettings = {
                targets: $figure[0],
                loop: true,
                direction: 'alternate',
                easing: 'easeInOutSine'
            };

            if (floatData.floatTranslate) {

                animeSettings.translateX = {
                    duration: floatData.floatTranslateSpeed * 1000,
                    value: [floatData.floatxStart || 0, floatData.floatxEnd || 0]
                };

                animeSettings.translateY = {
                    duration: floatData.floatTranslateSpeed * 1000,
                    value: [floatData.floatyStart || 0, floatData.floatyEnd || 0]
                };

            }

            if (floatData.floatRotate) {

                animeSettings.rotateX = {
                    duration: floatData.floatRotateSpeed * 1000,
                    value: [floatData.rotatexStart || 0, floatData.rotatexEnd || 0]
                };

                animeSettings.rotateY = {
                    duration: floatData.floatRotateSpeed * 1000,
                    value: [floatData.rotateyStart || 0, floatData.rotateyEnd || 0]
                };

                animeSettings.rotateZ = {
                    duration: floatData.floatRotateSpeed * 1000,
                    value: [floatData.rotatezStart || 0, floatData.rotatezEnd || 0]
                };

            }

            if (floatData.floatOpacity) {
                animeSettings.opacity = {
                    duration: floatData.floatOpacitySpeed * 1000,
                    value: floatData.floatOpacityValue || 0
                };
            }

            anime(animeSettings);

        }

        $prevWinElem.find(".premium-preview-image-inner-trig-img").tooltipster({
            functionBefore: function () {
                if (settings.hideMobiles && 'mobile' === currentDevice) {
                    return false;
                }
            },
            functionInit: function (instance, helper) {
                var content = $(helper.origin).find("#tooltip_content").detach();
                instance.content(content);
            },
            functionReady: function () {
                $(".tooltipster-box").addClass("tooltipster-box-" + settings.id);
            },
            contentCloning: true,
            plugins: ["sideTip"],
            animation: settings.anim,
            animationDuration: settings.animDur,
            delay: settings.delay,
            trigger: "custom",
            triggerOpen: {
                tap: true,
                mouseenter: true
            },
            triggerClose: {
                tap: true,
                mouseleave: true
            },
            arrow: settings.arrow,
            contentAsHTML: true,
            autoClose: false,
            maxWidth: maxWidth,
            minWidth: minWidth,
            distance: settings.distance,
            interactive: settings.active,
            minIntersection: 16,
            side: settings.side
        });
    };

    // Behance Feed Handler
    var PremiumBehanceFeedHandler = function ($scope, $) {
        var $behanceElem = $scope.find(".premium-behance-container"),
            $loading = $scope.find(".premium-loading-feed"),
            settings = $behanceElem.data("settings");

        function get_behance_data() {

            $behanceElem.embedBehance({
                apiKey: 'XQhsS66hLTKjUoj8Gky7FOFJxNMh23uu',
                userName: settings.username,
                project: 'yes' === settings.project,
                owners: 'yes' === settings.owner,
                appreciations: 'yes' === settings.apprectiations,
                views: 'yes' === settings.views,
                publishedDate: 'yes' === settings.date,
                fields: 'yes' === settings.fields,
                projectUrl: 'yes' === settings.url,
                infiniteScrolling: false,
                description: 'yes' === settings.desc,
                animationEasing: "easeInOutExpo",
                ownerLink: true,
                tags: true,
                containerId: settings.id,
                itemsPerPage: settings.number
            });
        }

        $.ajax({
            url: get_behance_data(),
            beforeSend: function () {
                $loading.addClass("premium-show-loading");
            },
            success: function () {
                $loading.removeClass("premium-show-loading");
            },
            error: function () {
                console.log("error getting data from Behance");
            }
        });
    };

    // Image Layers Handler
    var PremiumImageLayersHandler = function ($scope, $) {

        var $imgLayers = $scope.find(".premium-img-layers-wrapper"),
            currentDevice = elementorFrontend.getCurrentDeviceMode(),
            layers = $imgLayers.find(".premium-img-layers-list-item");

        var applyOn = $imgLayers.data("devices");

        layers
            .each(function (index, layer) {
                var $layer = $(layer),
                    data = $layer.data();

                if (data.scrolls) {
                    if (-1 !== applyOn.indexOf(currentDevice)) {

                        var instance = null,
                            effects = [],
                            vScrollSettings = {},
                            hScrollSettings = {},
                            oScrollSettings = {},
                            bScrollSettings = {},
                            rScrollSettings = {},
                            scaleSettings = {},
                            grayScaleSettings = {},
                            settings = {};

                        if (data.scrolls) {

                            if (data.vscroll) {
                                effects.push('translateY');
                                vScrollSettings = {
                                    speed: data.vscrollSpeed,
                                    direction: data.vscrollDir,
                                    range: {
                                        start: data.vscrollStart,
                                        end: data.vscrollEnd
                                    }
                                };
                            }
                            if (data.hscroll) {
                                effects.push('translateX');
                                hScrollSettings = {
                                    speed: data.hscrollSpeed,
                                    direction: data.hscrollDir,
                                    range: {
                                        start: data.hscrollStart,
                                        end: data.hscrollEnd
                                    }
                                };
                            }
                            if (data.oscroll) {
                                effects.push('opacity');
                                oScrollSettings = {
                                    level: data.oscrollLevel,
                                    fade: data.oscrollEffect,
                                    range: {
                                        start: data.oscrollStart,
                                        end: data.oscrollEnd
                                    }
                                };
                            }
                            if (data.bscroll) {
                                effects.push('blur');
                                bScrollSettings = {
                                    level: data.bscrollLevel,
                                    blur: data.bscrollEffect,
                                    range: {
                                        start: data.bscrollStart,
                                        end: data.bscrollEnd
                                    }
                                };
                            }
                            if (data.rscroll) {
                                effects.push('rotate');
                                rScrollSettings = {
                                    speed: data.rscrollSpeed,
                                    direction: data.rscrollDir,
                                    range: {
                                        start: data.rscrollStart,
                                        end: data.rscrollEnd
                                    }
                                };
                            }
                            if (data.scale) {
                                effects.push('scale');
                                scaleSettings = {
                                    speed: data.scaleSpeed,
                                    direction: data.scaleDir,
                                    range: {
                                        start: data.scaleStart,
                                        end: data.scaleEnd
                                    }
                                };
                            }
                            if (data.gscale) {
                                effects.push('gray');
                                grayScaleSettings = {
                                    level: data.gscaleLevel,
                                    gray: data.gscaleEffect,
                                    range: {
                                        start: data.gscaleStart,
                                        end: data.gscaleEnd
                                    }
                                };
                            }

                        }

                        settings = {
                            elType: 'Widget',
                            vscroll: vScrollSettings,
                            hscroll: hScrollSettings,
                            oscroll: oScrollSettings,
                            bscroll: bScrollSettings,
                            rscroll: rScrollSettings,
                            scale: scaleSettings,
                            gscale: grayScaleSettings,
                            effects: effects
                        };

                        instance = new premiumImageLayersEffects(layer, settings);
                        instance.init();

                    }

                } else if (data.float) {

                    var floatXSettings = null,
                        floatYSettings = null,
                        floatRotateXSettings = null,
                        floatRotateYSettings = null,
                        floatRotateZSettings = null;

                    var animeSettings = {
                        targets: $layer[0],
                        loop: true,
                        direction: 'alternate',
                        easing: 'easeInOutSine'
                    };

                    if (data.floatTranslate) {

                        floatXSettings = {
                            duration: data.floatTranslateSpeed * 1000,
                            value: [data.floatxStart || 0, data.floatxEnd || 0]
                        };

                        animeSettings.translateX = floatXSettings;

                        floatYSettings = {
                            duration: data.floatTranslateSpeed * 1000,
                            value: [data.floatyStart || 0, data.floatyEnd || 0]
                        };

                        animeSettings.translateY = floatYSettings;

                    }

                    if (data.floatRotate) {

                        floatRotateXSettings = {
                            duration: data.floatRotateSpeed * 1000,
                            value: [data.rotatexStart || 0, data.rotatexEnd || 0]
                        };

                        animeSettings.rotateX = floatRotateXSettings;

                        floatRotateYSettings = {
                            duration: data.floatRotateSpeed * 1000,
                            value: [data.rotateyStart || 0, data.rotateyEnd || 0]
                        };

                        animeSettings.rotateY = floatRotateYSettings;

                        floatRotateZSettings = {
                            duration: data.floatRotateSpeed * 1000,
                            value: [data.rotatezStart || 0, data.rotatezEnd || 0]
                        };

                        animeSettings.rotateZ = floatRotateZSettings;

                    }

                    if (data.floatOpacity) {
                        animeSettings.opacity = {
                            duration: data.floatOpacitySpeed * 1000,
                            value: data.floatOpacityValue || 0
                        };
                    }


                    anime(animeSettings);
                }

                if ($layer.data("layer-animation") && " " != $layer.data("layer-animation")) {

                    var waypoint = new Waypoint({
                        element: $($imgLayers),
                        offset: Waypoint.viewportHeight() - 150,
                        handler: function () {

                            $layer
                                .css("opacity", "1")
                                .addClass("animated " + $layer.data("layer-animation"));
                        }
                    });
                }

                if ($layer.hasClass('premium-mask-yes')) {
                    var html = '';
                    $layer.find('.premium-img-layers-text').text().split(' ').forEach(function (word) {
                        html += ' <span class="premium-mask-span">' + word + '</span>';
                    });

                    $layer.find('.premium-img-layers-text').text('').append(html);

                    elementorFrontend.waypoint($scope, function () {
                        $layer.find('.premium-img-layers-text').addClass('premium-mask-active');
                    }, {
                        offset: Waypoint.viewportHeight() - 150,
                        triggerOnce: true
                    });
                }

            });


        $imgLayers.find('.premium-img-layers-list-item[data-parallax="true"]').each(function () {

            var $this = $(this),
                resistance = $(this).data("rate"),
                reverse = -1;

            if ($this.data("mparallax-reverse"))
                reverse = 1;

            $imgLayers.mousemove(function (e) {
                TweenLite.to($this, 0.2, {
                    x: reverse * ((e.clientX - window.innerWidth / 2) / resistance),
                    y: reverse * ((e.clientY - window.innerHeight / 2) / resistance)
                });
            });

            if ($this.data("mparallax-init")) {
                $imgLayers.mouseleave(function () {
                    TweenLite.to($this, 0.4, {
                        x: 0,
                        y: 0
                    });
                });
            }

        });



        var tilts = $imgLayers.find('.premium-img-layers-list-item[data-tilt="true"]'),
            tilt = UniversalTilt.init({
                elements: tilts,
                callbacks: {
                    onMouseLeave: function (el) {
                        el.style.boxShadow = "0 45px 100px rgba(255, 255, 255, 0)";
                    },
                    onDeviceMove: function (el) {
                        el.style.boxShadow = "0 45px 100px rgba(255, 255, 255, 0.3)";
                    }
                }
            });
    };

    // Image Layers Editor Handler
    var PremiumImageLayersEditorHandler = function ($scope, $) {

        var $imgLayers = $scope.find(".premium-img-layers-wrapper"),
            settings = {
                repeater: 'premium_img_layers_images_repeater',
                item: '.premium-img-layers-list-item',
                width: 'premium_img_layers_width',
                hor: 'premium_img_layers_hor_position',
                ver: 'premium_img_layers_ver_position',
                tab: 'premium_img_layers_content',
                offset: 0,
                widgets: ["drag", "resize"]
            },
            instance = null;

        instance = new premiumEditorBehavior($imgLayers, settings);
        instance.init();

    };


    // Image Comparison Handler
    var PremiumImageCompareHandler = function ($scope, $) {

        var $imgCompareElem = $scope.find(".premium-images-compare-container"),
            settings = $imgCompareElem.data("settings");

        $imgCompareElem.imagesLoaded(function () {
            $imgCompareElem.twentytwenty({
                orientation: settings.orientation,
                default_offset_pct: settings.visibleRatio,
                switch_before_label: settings.switchBefore,
                before_label: settings.beforeLabel,
                switch_after_label: settings.switchAfter,
                after_label: settings.afterLabel,
                move_slider_on_hover: settings.mouseMove,
                click_to_move: settings.clickMove,
                show_drag: settings.showDrag,
                show_sep: settings.showSep,
                no_overlay: settings.overlay,
                horbeforePos: settings.beforePos,
                horafterPos: settings.afterPos,
                verbeforePos: settings.verbeforePos,
                verafterPos: settings.verafterPos
            });
        });
    };

    // Content Switcher Handler
    var PremiumContentToggleHandler = function ($scope, $) {

        var PremiumContentToggle = $scope.find(".premium-content-toggle-container");

        var radioSwitch = PremiumContentToggle.find(".premium-content-toggle-switch"),
            contentList = PremiumContentToggle.find(".premium-content-toggle-two-content");

        radioSwitch.prop('checked', false);

        var sides = {};
        sides[0] = contentList.find(
            'li[data-type="premium-content-toggle-monthly"]'
        );
        sides[1] = contentList.find(
            'li[data-type="premium-content-toggle-yearly"]'
        );

        radioSwitch.on("click", function (event) {

            var selected_filter = $(event.target).val();

            if ($(this).hasClass("premium-content-toggle-switch-active")) {

                selected_filter = 0;

                $(this).toggleClass(
                    "premium-content-toggle-switch-normal premium-content-toggle-switch-active"
                );

                hide_not_selected_items(sides, selected_filter);

            } else if ($(this).hasClass("premium-content-toggle-switch-normal")) {

                selected_filter = 1;

                $(this).toggleClass(
                    "premium-content-toggle-switch-normal premium-content-toggle-switch-active"
                );

                hide_not_selected_items(sides, selected_filter);

            }
        });

        function hide_not_selected_items(sides, filter) {
            $.each(sides, function (key, value) {
                if (key != filter) {
                    $(this)
                        .removeClass("premium-content-toggle-is-visible")
                        .addClass("premium-content-toggle-is-hidden");
                } else {
                    $(this)
                        .addClass("premium-content-toggle-is-visible")
                        .removeClass("premium-content-toggle-is-hidden");
                }
            });
        }
    };

    // Hotspots Handler
    var PremiumImageHotspotHandler = function ($scope, $) {

        var $hotspotsElem = $scope.find(".premium-image-hotspots-container"),
            hotspots = $hotspotsElem.find(".tooltip-wrapper"),
            settings = $hotspotsElem.data("settings"),
            isEdit = elementorFrontend.isEditMode(),
            currentDevice = elementorFrontend.getCurrentDeviceMode(),
            triggerClick = null,
            triggerHover = null;

        //Always trigger on click on touch devices
        if ("desktop" !== currentDevice || settings.trigger === "click") {
            triggerClick = true;
            triggerHover = false;
        } else if (settings.trigger === "hover") {
            triggerClick = false;
            triggerHover = true;
        }

        var $floatElem = $hotspotsElem.find('.premium-image-hotspots-img-wrap'),
            floatData = $floatElem.data();

        if (floatData.float) {

            var animeSettings = {
                targets: $floatElem[0],
                loop: true,
                direction: 'alternate',
                easing: 'easeInOutSine'
            };

            if (floatData.floatTranslate) {

                animeSettings.translateX = {
                    duration: floatData.floatTranslateSpeed * 1000,
                    value: [floatData.floatxStart || 0, floatData.floatxEnd || 0]
                };

                animeSettings.translateY = {
                    duration: floatData.floatTranslateSpeed * 1000,
                    value: [floatData.floatyStart || 0, floatData.floatyEnd || 0]
                };

            }

            if (floatData.floatRotate) {

                animeSettings.rotateX = {
                    duration: floatData.floatRotateSpeed * 1000,
                    value: [floatData.rotatexStart || 0, floatData.rotatexEnd || 0]
                };

                animeSettings.rotateY = {
                    duration: floatData.floatRotateSpeed * 1000,
                    value: [floatData.rotateyStart || 0, floatData.rotateyEnd || 0]
                };

                animeSettings.rotateZ = {
                    duration: floatData.floatRotateSpeed * 1000,
                    value: [floatData.rotatezStart || 0, floatData.rotatezEnd || 0]
                };

            }

            if (floatData.floatOpacity) {
                animeSettings.opacity = {
                    duration: floatData.floatOpacitySpeed * 1000,
                    value: floatData.floatOpacityValue || 0
                };
            }

            anime(animeSettings);

        }

        hotspots.tooltipster({
            functionBefore: function () {
                if (settings.hideMobiles && "mobile" === currentDevice)
                    return false;
            },
            functionInit: function (instance, helper) {

                if (!helper)
                    return;

                if (isEdit) {

                    var templateID = $(helper.origin).data('template-id');
                    if (undefined !== templateID) {
                        $.ajax({
                            type: 'GET',
                            url: PremiumProSettings.ajaxurl,
                            data: {
                                action: 'get_elementor_template_content',
                                templateID: templateID
                            }
                        }).success(function (response) {
                            var data = JSON.parse(response).data;

                            if (undefined !== data.template_content) {
                                instance.content(data.template_content);
                            }
                        });
                    }
                }

                var content = $(helper.origin).find("#tooltip_content").detach();
                instance.content(content);

            },
            functionReady: function () {
                $(".tooltipster-box").addClass("tooltipster-box-" + settings.id);
                $(".tooltipster-arrow").addClass("tooltipster-arrow-" + settings.id);
            },
            contentCloning: true,
            plugins: ["sideTip"],
            animation: settings.anim,
            animationDuration: settings.animDur,
            delay: settings.delay,
            trigger: "custom",
            triggerOpen: {
                click: triggerClick,
                tap: true,
                mouseenter: triggerHover
            },
            triggerClose: {
                click: triggerClick,
                tap: true,
                mouseleave: triggerHover
            },
            arrow: settings.arrow,
            contentAsHTML: true,
            autoClose: false,
            minWidth: settings.minWidth,
            maxWidth: settings.maxWidth,
            distance: settings.distance,
            interactive: settings.active,
            minIntersection: 16,
            side: settings.side
        });
    };

    // Hotspots Editor Handler
    var PremiumImageHotspotEditorHandler = function ($scope, $) {

        var $hotspotsElem = $scope.find(".premium-image-hotspots-container"),
            settings = {
                repeater: 'premium_image_hotspots_icons',
                item: '.premium-image-hotspots-main-icons',
                hor: 'preimum_image_hotspots_main_icons_horizontal_position',
                ver: 'preimum_image_hotspots_main_icons_vertical_position',
                tab: 'premium_image_hotspots_icons_settings',
                offset: 1,
                widgets: ["drag"]
            },
            instance = null;

        instance = new premiumEditorBehavior($hotspotsElem, settings);
        instance.init();

    };

    // Table Handler
    var PremiumTableHandler = function ($scope, $) {

        var $tableElem = $scope.find(".premium-table"),
            $premiumTableWrap = $scope.find(".premium-table-wrap"),
            settings = $tableElem.data("settings");

        //Table Sort
        if (settings.sort) {
            if (
                $(window).outerWidth() > 767 ||
                ($(window).outerWidth() < 767 && settings.sortMob)
            ) {
                $tableElem.tablesorter({
                    cssHeader: "premium-table-sort-head",
                    cssAsc: "premium-table-up",
                    cssDesc: "premium-table-down",
                    usNumberFormat: false,
                    sortReset: true,
                    sortRestart: true
                });
            } else {
                $tableElem.find(".premium-table-sort-icon").css("display", "none");
            }
        }

        //Table search
        if (settings.search) {
            $premiumTableWrap.find("#premium-table-search-field").keyup(function () {

                _this = this;

                $tableElem.find("tbody tr").each(function () {

                    if ($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1) {
                        $(this).addClass("premium-table-search-hide");
                    } else {

                        $(this).removeClass("premium-table-search-hide");
                        if ($(this).hasClass('premium-table-row-hidden')) {
                            $(this).removeClass("premium-table-row-hidden").addClass('hidden-by-default');
                        }

                    }

                });

                if ($(_this).val().toLowerCase().length === 0) {
                    $tableElem.find(".hidden-by-default").each(function () {
                        $(this).addClass('premium-table-row-hidden').removeClass('hidden-by-default');
                    });
                }
            });

        }

        //Table show records
        if (settings.records) {

            $premiumTableWrap
                .find(".premium-table-records-box")
                .on("change", function () {
                    var rows = $(this)
                        .find("option:last")
                        .val(),
                        value = parseInt(this.value);

                    if (1 === value) {
                        $tableElem.find("tbody tr").not(".premium-table-search-hide").removeClass("premium-table-hide");
                    } else {
                        $tableElem.find("tbody tr:gt(" + (value - 2) + ")").not(".premium-table-search-hide").addClass("premium-table-hide");

                        $tableElem.find("tbody tr:lt(" + (value - 1) + ")").not(".premium-table-search-hide").removeClass("premium-table-hide");
                    }
                });
        }

        //Tables with CSV Files
        if (settings.dataType === "csv" && '' != settings.csvFile) {

            //If the file is Google Spreadsheet, then we need to  use CORS Proxy.
            // if (-1 !== settings.csvFile.indexOf("docs.google.com"))
            //     settings.csvFile = "https://cors-anywhere.herokuapp.com/" + settings.csvFile;

            $.ajax({
                url: PremiumProSettings.ajaxurl,
                type: "POST",
                data: {
                    action: 'handle_table_data',
                    id: settings.id,
                    security: PremiumProSettings.nonce,
                },
                success: function (res) {

                    if (res.data) {

                        handleCsvData(res.data);

                        if (settings.pagination === "yes")
                            handleTablePagination();

                    } else {

                        $.ajax({
                            url: settings.csvFile,
                            type: "GET",
                            success: function (res) {

                                $.ajax({
                                    url: PremiumProSettings.ajaxurl,
                                    type: "POST",
                                    data: {
                                        action: 'handle_table_data',
                                        id: settings.id,
                                        expire: settings.reload,
                                        tableData: res,
                                        security: PremiumProSettings.nonce,
                                    },
                                    success: function (res) {
                                        console.log(res);
                                    }
                                });

                                if (!res)
                                    return;

                                handleCsvData(res);

                                if (settings.pagination === "yes")
                                    handleTablePagination();

                            },
                            error: function (err) {
                                console.log(err);
                            }
                        });

                    }

                }
            });

            //Handle CSV Data
            function handleCsvData(data) {

                var rowsData = data.split(/\r?\n|\r/),
                    firstRow = settings.firstRow,
                    table_data = "head" === firstRow ? '<thead class="premium-table-head">' : '';

                for (var count = 0; count < rowsData.length; count++) {
                    var cell_data = rowsData[count].split(settings.separator);
                    table_data += '<tr class="premium-table-row">';
                    for (
                        var cell_count = 0; cell_count < cell_data.length; cell_count++
                    ) {
                        if (count === 0 && "head" === firstRow) {
                            table_data +=
                                '<th class="premium-table-cell"><span class="premium-table-text"><span class="premium-table-inner">' +
                                cell_data[cell_count] +
                                "</span>";
                            table_data += "</span></th>";
                        } else {
                            table_data +=
                                '<td class="premium-table-cell"><span class="premium-table-text"><span class="premium-table-inner">' +
                                cell_data[cell_count] +
                                "</span></span></td>";
                        }
                    }
                    table_data += "</tr>";
                    if (count === 0 && "head" === firstRow) {
                        table_data += "</thead>";
                    }
                }
                $tableElem.html("");
                $tableElem.html(table_data);
            }

        }


        if (settings.dataType === "custom" && settings.pagination === "yes")
            handleTablePagination();

        function handleTablePagination() {

            var tableRows = $tableElem.find("tbody tr").length,
                pages = Math.ceil(tableRows / settings.rows);

            $tableElem.find("tbody tr:gt(" + (settings.rows - 1) + ")").addClass("premium-table-row-hidden");

            var paginationHtml = '';
            for (var count = 0; count < pages; count++) {
                var current = 0 === count ? "current" : "";
                paginationHtml += "<li><a href='#' class='page-numbers " + current + "' data-page='" + count + "'>" + (count + 1) + "</a></li>";
            }

            $scope.find(".premium-table-pagination li").eq(0).after(paginationHtml);


            $scope.on("click", ".premium-table-pagination li a", function (e) {
                e.preventDefault();

                var $this = $(this);

                if ($this.hasClass("current"))
                    return;

                $premiumTableWrap.append('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');

                setTimeout(function () {

                    var page = $this.data("page");

                    $tableElem.find("tbody tr").removeClass("premium-table-row-hidden");

                    $scope.find(".premium-table-pagination a.current").removeClass("current");

                    if (!$this.hasClass("prev") && !$this.hasClass("next"))
                        $this.addClass("current");

                    if ($this.hasClass('next') || (pages - 1) === page) {
                        $tableElem.find("tbody tr:lt(" + ((pages - 1) * settings.rows) + ")").addClass("premium-table-row-hidden");
                    } else if ($this.hasClass('prev') || 0 === page) {
                        $tableElem.find("tbody tr:gt(" + (settings.rows - 1) + ")").addClass("premium-table-row-hidden");
                    } else {
                        var gt = ((page + 1) * settings.rows - 1);
                        $tableElem.find("tbody tr:gt(" + gt + ")").addClass("premium-table-row-hidden");
                        $tableElem.find("tbody tr:lt(" + page * settings.rows + ")").addClass("premium-table-row-hidden");
                    }

                    $premiumTableWrap.find(".premium-loading-feed").remove();
                    $('html, body').animate({
                        scrollTop: (($premiumTableWrap.offset().top) - 100)
                    }, 'slow');
                }, 1000);

            });


        }

    };

    // Reviews Handler
    var PremiumReviewHandler = function ($scope, $) {
        var premiumRevElem = $scope.find(".premium-fb-rev-container"),
            revsContainer = premiumRevElem.find(".premium-fb-rev-reviews"),
            revStyle = premiumRevElem.data("style"),
            carousel = premiumRevElem.data("carousel");

        if (carousel) {

            var autoPlay = premiumRevElem.data("play"),
                colsNumber = premiumRevElem.data("col"),
                colsNumberTablet = premiumRevElem.data("col-tab"),
                colsNumberMobile = premiumRevElem.data("col-mobile"),
                speed = premiumRevElem.data("speed"),
                rtl = premiumRevElem.data("rtl"),
                dots = premiumRevElem.data("dots"),
                arrows = premiumRevElem.data("arrows"),
                infinite = premiumRevElem.data("infinite"),
                rows = infinite ? premiumRevElem.data("rows") : 0,
                prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>';

            $(revsContainer).slick({
                infinite: true,
                slidesToShow: colsNumber,
                slidesToScroll: infinite ? 1 : colsNumber,
                responsive: [{
                    breakpoint: 1025,
                    settings: {
                        slidesToShow: infinite ? 1 : colsNumberTablet,
                        slidesToScroll: 1,
                        autoplaySpeed: speed,
                        speed: 300,
                        centerMode: infinite ? true : false,
                        centerPadding: '30px',
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: infinite ? 1 : colsNumberMobile,
                        slidesToScroll: 1,
                        autoplaySpeed: speed,
                        speed: 300,
                        centerMode: infinite ? true : false,
                        centerPadding: '30px',
                    }
                }
                ],
                useTransform: true,
                autoplay: infinite ? true : autoPlay,
                speed: infinite ? speed : 300,
                autoplaySpeed: infinite ? 0 : speed,
                rows: rows,
                rtl: rtl ? true : false,
                arrows: arrows,
                nextArrow: nextArrow,
                prevArrow: prevArrow,
                draggable: true,
                pauseOnHover: infinite ? false : true,
                dots: dots,
                cssEase: infinite ? "linear" : "ease",
                customPaging: function () {
                    return '<i class="fas fa-circle"></i>';
                },
            });

            if ((dots && $scope.hasClass("premium-fb-page-next-yes")) || (dots && arrows)) {

                $('<div class="premium-fb-dots-container"></div>').appendTo(premiumRevElem);
                var dotsContainer = premiumRevElem.find(".premium-fb-dots-container")
                dotsElem = revsContainer.find(".slick-dots");
                $('<div class="premium-fb-empty-dots"></div>').appendTo(dotsContainer);
                $(dotsElem).appendTo(dotsContainer);
                if ($scope.hasClass("premium-fb-page-next-yes")) {
                    var pageWidth = premiumRevElem.find(".premium-fb-rev-page").outerWidth();
                    dotsContainer.find(".premium-fb-empty-dots").css('width', pageWidth + 'px');
                }

            }

        }

        if ("masonry" === revStyle && 1 !== colsNumber && !carousel) {
            revsContainer.isotope({
                // options
                itemSelector: ".premium-fb-rev-review-wrap",
                percentPosition: true,
                layoutMode: "masonry",
                animationOptions: {
                    duration: 750,
                    easing: "linear",
                    queue: false
                }
            });

        }
    };

    // Divider Handler
    var PremiumDividerHandler = function ($scope, $) {
        var $divider = $scope.find(".premium-separator-container"),
            sepSettings = $divider.data("settings"),
            leftBackground = null,
            rightBackground = null;

        if ("custom" === sepSettings) {
            leftBackground = $divider
                .find(".premium-separator-left-side")
                .data("background");

            $divider
                .find(".premium-separator-left-side hr")
                .css("border-image", "url( " + leftBackground + " ) 20% round");

            rightBackground = $divider
                .find(".premium-separator-right-side")
                .data("background");

            $divider
                .find(".premium-separator-right-side hr")
                .css("border-image", "url( " + rightBackground + " ) 20% round");
        }

    };

    // Whatsapp Chat Handler
    var $whatsappElemHandler = function ($scope, $) {
        var $whatsappElem = $scope.find(".premium-whatsapp-container"),
            settings = $whatsappElem.data("settings"),
            currentDevice = elementorFrontend.getCurrentDeviceMode();

        if (settings.hideMobile) {
            if ("mobile" === currentDevice) {
                $($whatsappElem).css("display", "none");
            }
        } else if (settings.hideTab) {
            if ("tablet" === currentDevice) {
                $($whatsappElem).css("display", "none");
            }
        }

        if (settings.tooltips) {
            $whatsappElem.find(".premium-whatsapp-link").tooltipster({
                functionInit: function (instance, helper) {
                    var content = $(helper.origin)
                        .find("#tooltip_content")
                        .detach();
                    instance.content(content);
                },
                functionReady: function () {
                    $(".tooltipster-box").addClass(
                        "tooltipster-box-" + settings.id
                    );
                },
                animation: settings.anim,
                contentCloning: true,
                trigger: "hover",
                arrow: true,
                contentAsHTML: true,
                autoClose: false,
                minIntersection: 16,
                interactive: true,
                delay: 0,
                side: ["right", "left", "top", "bottom"]
            });
        }
    };

    // Multi Scroll Handler
    var PremiumScrollHandler = function ($scope, $) {
        var premiumScrollElem = $scope.find(".premium-multiscroll-wrap"),
            premiumScrollSettings = premiumScrollElem.data("settings"),
            id = premiumScrollSettings["id"];

        function loadMultiScroll() {
            $("#premium-scroll-nav-menu-" + id).removeClass(
                "premium-scroll-responsive"
            );
            $("#premium-multiscroll-" + id).multiscroll({
                verticalCentered: true,
                menu: "#premium-scroll-nav-menu-" + id,
                sectionsColor: [],
                keyboardScrolling: premiumScrollSettings["keyboard"],
                navigation: premiumScrollSettings["dots"],
                navigationPosition: premiumScrollSettings["dotsPos"],
                navigationVPosition: premiumScrollSettings["dotsVPos"],
                navigationTooltips: premiumScrollSettings["dotsText"],
                navigationColor: "#000",
                loopBottom: premiumScrollSettings["btmLoop"],
                loopTop: premiumScrollSettings["topLoop"],
                css3: true,
                paddingTop: 0,
                paddingBottom: 0,
                normalScrollElements: null,
                //          scrollOverflow: true,
                //          scrollOverflowOptions: null,
                touchSensitivity: 5,
                leftSelector: ".premium-multiscroll-left-" + id,
                rightSelector: ".premium-multiscroll-right-" + id,
                sectionSelector: ".premium-multiscroll-temp-" + id,
                anchors: premiumScrollSettings["anchors"],
                fit: premiumScrollSettings["fit"],
                cellHeight: premiumScrollSettings["cellHeight"],
                id: id,
                leftWidth: premiumScrollSettings["leftWidth"],
                rightWidth: premiumScrollSettings["rightWidth"]
            });
        }
        var leftTemps = $(premiumScrollElem).find(".premium-multiscroll-left-temp"),
            rightTemps = $(premiumScrollElem).find(".premium-multiscroll-right-temp"),
            hideTabs = premiumScrollSettings["hideTabs"],
            hideMobs = premiumScrollSettings["hideMobs"],
            deviceType = $("body").data("elementor-device-mode");

        function hideSections(leftSec, rightSec) {
            if ("mobile" === deviceType) {
                $(leftSec).data("hide-mobs") ?
                    $(leftSec).addClass("premium-multiscroll-hide") :
                    "";
                $(rightSec).data("hide-mobs") ?
                    $(rightSec).addClass("premium-multiscroll-hide") :
                    "";
            } else {
                $(leftSec).data("hide-tabs") ?
                    $(leftSec).addClass("premium-multiscroll-hide") :
                    "";
                $(rightSec).data("hide-tabs") ?
                    $(rightSec).addClass("premium-multiscroll-hide") :
                    "";
            }
        }

        function reOrderTemplates() {
            $(premiumScrollElem)
                .parents(".elementor-top-section")
                .removeClass("elementor-section-height-full");
            $.each(rightTemps, function (index) {
                hideSections(leftTemps[index], rightTemps[index]);
                if (premiumScrollSettings["rtl"]) {
                    $(leftTemps[index]).insertAfter(rightTemps[index]);
                } else {
                    $(rightTemps[index]).insertAfter(leftTemps[index]);
                }
            });
            $(premiumScrollElem)
                .find(".premium-multiscroll-inner")
                .removeClass("premium-scroll-fit")
                .css("min-height", premiumScrollSettings["cellHeight"] + "px");
        }

        switch (true) {
            case hideTabs && hideMobs:
                if (deviceType === "desktop") {
                    loadMultiScroll();
                } else {
                    reOrderTemplates();
                }
                break;
            case hideTabs && !hideMobs:
                if (deviceType === "mobile" || deviceType === "desktop") {
                    loadMultiScroll();
                } else {
                    reOrderTemplates();
                }
                break;
            case !hideTabs && hideMobs:
                if (deviceType === "tablet" || deviceType === "desktop") {
                    loadMultiScroll();
                } else {
                    reOrderTemplates();
                }
                break;
            case !hideTabs && !hideMobs:
                loadMultiScroll();
                break;
        }
    };

    // Image Accordion Handler
    var PremiumImageAccordionHandler = function ($scope, $) {

        var $accordElem = $scope.find('.premium-accordion-section'),
            settings = $accordElem.data("settings"),
            $window = $(window),
            $accordItems = $accordElem.find('.premium-accordion-li'),
            count = $accordItems.length;

        if (elementorFrontend.isEditMode()) {

            $accordElem.find(".premium-accord-temp").each(function (index, img) {

                var templateID = $(img).data("template");

                if (undefined !== templateID) {
                    $.ajax({
                        type: "GET",
                        url: PremiumSettings.ajaxurl,
                        dataType: "html",
                        data: {
                            action: "get_elementor_template_content",
                            templateID: templateID
                        }
                    }).success(function (response) {

                        var data = JSON.parse(response).data;

                        if (undefined !== data.template_content) {
                            $(img).html(data.template_content);
                            $window.resize();
                        }
                    });
                }
            });

        }

        //Trigger Hovered Image Width Function on page load only if Default Index option value is set.
        if ($accordElem.find('.premium-accordion-li-active').length) {
            resizeImgs();
        }

        $window.resize(function () {

            $accordElem.find('.premium-accordion-description').css('display', settings.hide_desc > $window.outerWidth() ? 'none' : 'block');

            resizeImgs();
        });

        $accordItems.hover(function () {

            if (!$(this).hasClass('premium-accordion-li-active')) {

                $accordItems.removeClass('premium-accordion-li-active');

                $(this).addClass('premium-accordion-li-active');
            }

            resizeImgs();
        });

        $accordItems.mouseleave(function () {
            $accordElem.find('.premium-accordion-li, .premium-accordion-ul, .premium-accordion-overlay-wrap').attr('style', '');
            $accordItems.removeClass('premium-accordion-li-active');
        });

        function resizeImgs() {

            var currentDevice = elementorFrontend.getCurrentDeviceMode();

            if ('horizontal' === settings.dir) {

                if (settings.imgSize[currentDevice]) {
                    var width = 'width: calc( (100% - ' + settings.imgSize[currentDevice] + '% ) /' + (count - 1) + ')';
                    $accordElem.find('.premium-accordion-li:not(.premium-accordion-li-active)').attr('style', width);
                }

            } else {
                var initialHeight = ('' === settings.initialHeight[currentDevice]) ? 200 : settings.initialHeight[currentDevice],
                    height = ('' === settings.imgSize[currentDevice]) ? 400 : initialHeight * count * settings.imgSize[currentDevice] * 0.01;

                $accordElem.find('.premium-accordion-li-active').attr('style', 'height: ' + height + 'px !important');

                if ('' !== settings.imgSize[currentDevice]) {
                    $accordElem.find('.premium-accordion-li:not(.premium-accordion-li-active)').attr('style', 'height: calc( (' + initialHeight * count + 'px - ' + height + 'px ) /' + (count - 1) + ')');
                }
            }

            if (100 === settings.imgSize[currentDevice]) {
                $accordElem.find('.premium-accordion-li').css({
                    'padding': 0,
                    'margin': 0
                });

                $accordElem.find('.premium-accordion-overlay-wrap').css('width', '100%');
                $accordElem.find('.premium-accordion-ul').css('borderSpacing', '0 0');
            }
        }
    };

    // Background Transition Handler
    var PremiumColorTransitionHandler = function ($scope, $) {

        var scrollElement = $scope.find('.premium-scroll-background'),
            settings = scrollElement.data('settings'),
            currentDevice = elementorFrontend.getCurrentDeviceMode(),
            $window = $(window),
            offset = null,
            isNull = false,
            isSolid = true;

        //Widget Settings
        var elements = settings.elements,
            downColors = settings.down_colors,
            itemsIDs = settings.itemsIDs,
            id = settings.id;

        //Make sure all IDs refer to existing elements.
        for (var i = 0; i < elements.length; i++) {
            if (!$('#' + elements[i]).length) {
                scrollElement.html('<div class="premium-error-notice">Please make sure that IDs added to the widget are valid</div>');
                isNull = true;
                break;
            }
        }

        if (isNull)
            return;

        var $firstElement = $('#' + elements[0]),
            $lastElement = $('#' + elements[elements.length - 1]),
            firstElemOffset = $firstElement.offset().top,
            lastElemOffset = $lastElement.offset().top,
            lastElemeHeight = $lastElement.outerHeight(),
            lastElemeBot = lastElemOffset + lastElemeHeight;

        offset = 'offset' + ('desktop' === currentDevice ? '' : '_' + currentDevice);
        //Get desktop offset if empty
        if ('' === settings[offset])
            offset = 'offset';

        $('<div id="premium-color-transition-' + id + '" class="premium-color-transition"></div>').prependTo($('body'));

        $(document).ready(function () {
            if ($('.premium-color-transition').length > 1)
                $window.on('scroll', checkVisible);
        });

        function checkVisible() {

            if (undefined === firstElemOffset || undefined === lastElemOffset)
                return;

            if ($window.scrollTop() >= lastElemeBot - lastElemeHeight / 4) {
                var index = $('#premium-color-transition-' + id).index();
                if (0 !== index)
                    $('#premium-color-transition-' + id).addClass('premium-bg-transition-hidden');

            }
            if (($window.scrollTop() >= firstElemOffset) && ($window.scrollTop() < lastElemOffset)) {
                $('#premium-color-transition-' + id).removeClass('premium-bg-transition-hidden');
            }
        }

        function visible(selector, partial, hidden) {
            var s = selector.get(0),
                vpHeight = $window.outerHeight(),
                clientSize =
                    hidden === true ? s.offsetWidth * s.offsetHeight : true;
            if (typeof s.getBoundingClientRect === "function") {
                var rec = s.getBoundingClientRect();
                var tViz = rec.top >= 0 && rec.top < vpHeight,
                    bViz = rec.bottom > 0 && rec.bottom <= vpHeight,
                    vVisible = partial ? tViz || bViz : tViz && bViz,
                    vVisible =
                        rec.top < 0 && rec.bottom > vpHeight ? true : vVisible;
                return clientSize && vVisible;
            } else {
                var viewTop = 0,
                    viewBottom = viewTop + vpHeight,
                    position = $window.position(),
                    _top = position.top,
                    _bottom = _top + $window.height(),
                    compareTop = partial === true ? _bottom : _top,
                    compareBottom = partial === true ? _top : _bottom;
                return (
                    !!clientSize &&
                    (compareBottom <= viewBottom && compareTop >= viewTop)
                );
            }
        }

        downColors.forEach(function (color) {
            if (-1 !== color.indexOf('//'))
                isSolid = false;
        });

        if (!settings.gradient)
            isSolid = false;

        if (isSolid) {

            function rowTransitionalColor($row, firstColor, secondColor) {
                "use strict";

                var firstColor = hexToRgb(firstColor),
                    secondColor = hexToRgb(secondColor);

                var scrollPos = 0,
                    currentRow = $row,
                    beginningColor = firstColor,
                    endingColor = secondColor,
                    percentScrolled, newRed, newGreen, newBlue, newColor;

                $(document).scroll(function () {
                    var animationBeginPos = currentRow.offset().top,
                        endPart = currentRow.outerHeight() / 4,
                        animationEndPos = animationBeginPos + currentRow.outerHeight() - endPart;

                    scrollPos = $(this).scrollTop();

                    if (scrollPos >= animationBeginPos && scrollPos <= animationEndPos) {
                        percentScrolled = (scrollPos - animationBeginPos) / (currentRow.outerHeight() - endPart);
                        newRed = Math.abs(beginningColor.r + (endingColor.r - beginningColor.r) * percentScrolled);
                        newGreen = Math.abs(beginningColor.g + (endingColor.g - beginningColor.g) * percentScrolled);
                        newBlue = Math.abs(beginningColor.b + (endingColor.b - beginningColor.b) * percentScrolled);

                        newColor = "rgb(" + newRed + "," + newGreen + "," + newBlue + ")";

                        $('#premium-color-transition-' + id).css({
                            backgroundColor: newColor
                        });

                    } else if (scrollPos > animationEndPos) {
                        $('#premium-color-transition-' + id).css({
                            backgroundColor: endingColor
                        });
                    }
                });
            }

            function hexToRgb(hex) {

                if (-1 !== hex.indexOf("rgb")) {
                    var rgb = (hex.substring(hex.indexOf("(") + 1)).split(",");
                    return {
                        r: parseInt(rgb[0]),
                        g: parseInt(rgb[1]),
                        b: parseInt(rgb[2])
                    };

                } else {
                    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                    return result ? {
                        r: parseInt(result[1], 16),
                        g: parseInt(result[2], 16),
                        b: parseInt(result[3], 16)
                    } : null;
                }
            }

            $('#premium-color-transition-' + id).css({
                backgroundColor: downColors[0]
            });

            var parent_node = $("#premium-color-transition-" + id).closest(".elementor");

            if (0 === parent_node.length)
                parent_node = $(".elementor").first();

            var i = 0;
            var arry_len = downColors.length;
            var isLooped = null;

            $(".elementor-section-wrap > .elementor-element").each(function () {
                if (arry_len <= i)
                    i = 0;

                var firstColor = i,
                    secondColor = i + 1;

                if (downColors[firstColor] !== '' && downColors[firstColor] != undefined) {
                    firstColor = downColors[firstColor];
                }
                if (downColors[secondColor] !== '' && downColors[secondColor] != undefined) {
                    isLooped = false;
                    secondColor = downColors[secondColor];
                } else {
                    i = 0;
                    isLooped = true;
                    secondColor = i;
                    secondColor = downColors[secondColor];
                }

                rowTransitionalColor($(this), firstColor, secondColor);
                if (!isLooped)
                    i++;
            });

        } else {
            //Refresh all Waypoints instances.
            Waypoint.refreshAll();
            elements.forEach(function (element, index) {

                $('<div class="premium-color-transition-layer elementor-repeater-item-' + itemsIDs[index] + '" data-direction="down"></div>').prependTo($('#premium-color-transition-' + id));

                $('<div class="premium-color-transition-layer elementor-repeater-item-' + itemsIDs[index] + '" data-direction="up"></div>').prependTo($('#premium-color-transition-' + id));

                if (visible($('#' + element), true))
                    $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="down"]').addClass('layer-active');

                elementorFrontend.waypoint(
                    $('#' + element),
                    function (direction) {

                        if ('down' === direction) {
                            $('.premium-color-transition-layer').removeClass('layer-active');
                            $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="down"]').addClass('layer-active');
                        }
                    }, {
                    offset: (0 === index) ? '100%' : settings[offset],
                    triggerOnce: false
                }
                );

                elementorFrontend.waypoint(
                    $('#' + element),
                    function (direction) {
                        if ('up' === direction) {
                            if (index === (elements.length - 1)) {
                                var upBackground = $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="up"]').css("background-image"),
                                    downBackground = $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="down"]').css("background-image");

                                if (upBackground !== downBackground) {

                                    $('.premium-color-transition-layer').removeClass('layer-active');
                                    $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="up"]').addClass('layer-active');
                                }
                            } else {
                                $('.premium-color-transition-layer').removeClass('layer-active');
                                $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="up"]').addClass('layer-active');
                            }

                        }
                    }, {
                    offset: -1 * settings[offset],
                    triggerOnce: false
                }
                );

            });

        }

    };

    var PremiumIconBoxHandler = function ($scope, $) {

        var $iconBox = $scope.find(".premium-icon-box-container-out"),
            devices = ['desktop', 'tablet', 'mobile'].filter(function (ele) { return ele != elementorFrontend.getCurrentDeviceMode(); });

        devices.map(function (device) {
            device = ('desktop' !== device) ? device + '-' : '';
            $scope.removeClass(function (index, selector) {
                return (selector.match(new RegExp("(^|\\s)premium-" + device + "icon-box\\S+", 'g')) || []).join(' ');
            });
        });

        if ($iconBox.data("box-tilt")) {
            var reverse = $iconBox.data("box-tilt-reverse");

            UniversalTilt.init({
                elements: $iconBox,
                settings: {
                    reverse: reverse
                },
                callbacks: {
                    onMouseLeave: function (el) {
                        el.style.boxShadow = "0 45px 100px rgba(255, 255, 255, 0)";
                    },
                    onDeviceMove: function (el) {
                        el.style.boxShadow = "0 45px 100px rgba(255, 255, 255, 0.3)";
                    }
                }
            });
        }

    };

    window.premiumImageLayersEffects = function (element, settings) {

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

            if ("rotate" === action) {
                elementSettings.defaults.unit = "deg";
            } else {
                elementSettings.defaults.unit = "px";
            }

            self.updateElement(
                "transform",
                action,
                self.getStep(percents, data) + elementSettings.defaults.unit
            );

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

        self.initScroll = function (event) {

            if (undefined !== event.type)
                if ("load" === event.type) {
                    $el.css("transition", "all 1s ease");
                } else {
                    $el.css("transition", "none");
                }

            if (elementSettings.effects.includes('translateY')) {

                self.initVScroll();

            }

            if (elementSettings.effects.includes('translateX')) {

                self.initHScroll();

            }

            if (elementSettings.effects.includes('opacity')) {

                self.initOScroll();

            }

            if (elementSettings.effects.includes('blur')) {

                self.initBScroll();

            }

            if (elementSettings.effects.includes('gray')) {

                self.initGScroll();

            }

            if (elementSettings.effects.includes('rotate')) {

                self.initRScroll();

            }

            if (elementSettings.effects.includes('scale')) {

                self.initScaleScroll();

            }

        };

        self.initVScroll = function () {
            var percents = self.getPercents();

            self.transform("translateY", percents, elementSettings.vscroll);
        };

        self.initHScroll = function () {
            var percents = self.getPercents();

            self.transform("translateX", percents, elementSettings.hscroll);
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

        self.initOScroll = function () {
            var percents = self.getPercents(),
                data = elementSettings.oscroll,
                movePoint = self.getEffectMovePoint(
                    percents,
                    data.fade,
                    data.range
                ),
                level = data.level / 10,
                opacity =
                    1 -
                    level +
                    self.getEffectValueFromMovePoint(level, movePoint);

            $el.css("opacity", opacity);
        };

        self.initBScroll = function () {

            var percents = self.getPercents(),
                data = elementSettings.bscroll,
                movePoint = self.getEffectMovePoint(percents, data.blur, data.range),
                blur = data.level - self.getEffectValueFromMovePoint(data.level, movePoint);

            self.updateElement('filter', 'blur', blur + 'px');

        };

        self.initGScroll = function () {

            var percents = self.getPercents(),
                data = elementSettings.gscale,
                movePoint = self.getEffectMovePoint(percents, data.gray, data.range),
                grayScale = 10 * (data.level - self.getEffectValueFromMovePoint(data.level, movePoint));

            self.updateElement('filter', 'grayscale', grayScale + '%');

        };

        self.initRScroll = function () {
            var percents = self.getPercents();

            self.transform("rotate", percents, elementSettings.rscroll);
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
                point = self.getPointFromPercents(
                    range.end - range.start,
                    percents - range.start
                );

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

        self.initScaleScroll = function () {
            var percents = self.getPercents(),
                data = elementSettings.scale,
                movePoint = self.getEffectMovePoint(
                    percents,
                    data.direction,
                    data.range
                );

            this.updateElement(
                "transform",
                "scale",
                1 + (data.speed * movePoint) / 1000
            );
        };

        self.getEffectValueFromMovePoint = function (level, movePoint) {
            return (level * movePoint) / 100;
        };

        self.getPointFromPercents = function (movableRange, percents) {
            var movePoint = (percents / movableRange) * 100;

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

            var cssVarKey = "--" + key;

            element.style.setProperty(cssVarKey, value);
        };

        self.updateElementRule = function (rule) {
            var cssValue = "";

            $.each(self.elementRules[rule], function (variableKey) {
                cssValue += variableKey + "(var(--" + variableKey + "))";
            });

            $el.css(rule, cssValue);
        };

    };

    window.premiumEditorBehavior = function ($element, settings) {

        var self = this,
            $el = $element,
            elementSettings = settings,
            editModel = null,
            repeater = null;


        var items = $el.find(elementSettings.item),
            tag = $el.prop('tagName');

        self.init = function () {

            editModel = self.getEditModelBycId();

            if (!items.length || undefined === editModel) {
                return;
            }

            repeater = editModel.get(elementSettings.repeater).models;

            if (elementSettings.widgets.includes("resize")) {

                var resizableOptions = self.getResizableOptions();

            }

            var draggableOptions = self.getDraggableOptions();

            if ('SECTION' !== tag) {
                var $widget = elementor.previewView.$childViewContainer.find('.elementor-widget-wrap');
                $widget.find(elementSettings.item).closest('.elementor-widget-wrap').sortable('disable');
            }


            items.filter(function () {

                if ('absolute' === $(this).css('position')) {

                    $(this).draggable(draggableOptions);

                    if (elementSettings.widgets.includes("resize")) {

                        $(this).resizable(resizableOptions);

                    }

                }

            });

        };

        self.getDraggableOptions = function () {

            var draggableOptions = {};

            draggableOptions.stop = function (e, ui) {

                var index = self.layerToEdit(ui.helper),
                    deviceSuffix = self.getCurrentDeviceSuffix(),
                    hUnit = 'SECTION' === tag ? '%' : repeater[index].get(elementSettings.hor + deviceSuffix).unit,
                    hWidth = window.elementor.helpers.elementSizeToUnit(ui.helper, ui.position.left, hUnit),
                    vUnit = repeater[index].get(elementSettings.ver + deviceSuffix).unit,
                    vWidth = ('%' === vUnit || 'SECTION' === tag) ? self.verticalOffsetToPercent(ui.helper, ui.position.top) : window.elementor.helpers.elementSizeToUnit(ui.helper, ui.position.top, vUnit),
                    settingToChange = {};

                settingToChange[elementSettings.hor + deviceSuffix] = {
                    unit: hUnit,
                    size: hWidth
                };

                settingToChange[elementSettings.ver + deviceSuffix] = {
                    unit: vUnit,
                    size: vWidth
                };

                if ('SECTION' !== tag) {
                    $el.trigger('click');
                } else {
                    $el.find('i.eicon-handle').eq(0).trigger('click');
                }

                window.PremiumWidgetsEditor.activateEditorPanelTab(elementSettings.tab);

                repeater[index].setExternalChange(settingToChange);

            };

            return draggableOptions;

        };

        self.getResizableOptions = function () {

            var resizableOptions = {};

            resizableOptions.handles = self.setHandle();
            resizableOptions.stop = function (e, ui) {

                var index = self.layerToEdit(ui.element),
                    deviceSuffix = self.getCurrentDeviceSuffix(),
                    unit = 'SECTION' === tag ? '%' : repeater[index].get(elementSettings.width + deviceSuffix).unit,
                    width = window.elementor.helpers.elementSizeToUnit(ui.element, ui.size.width, unit),
                    settingToChange = {};

                settingToChange[elementSettings.width + deviceSuffix] = {
                    unit: unit,
                    size: width
                };

                if ('SECTION' !== tag) {
                    $el.trigger('click');
                } else {
                    $el.find('i.eicon-handle').eq(0).trigger('click');
                }

                window.PremiumWidgetsEditor.activateEditorPanelTab(elementSettings.tab);

                repeater[index].setExternalChange(settingToChange);

            };

            return resizableOptions;

        };

        self.getModelcId = function () {

            return $el.closest('.elementor-element').data('model-cid');

        };

        self.getEditModelBycId = function () {

            var cID = self.getModelcId();

            return elementorFrontend.config.elements.data[cID];

        };

        self.getCurrentDeviceSuffix = function () {

            var currentDeviceMode = elementorFrontend.getCurrentDeviceMode();

            return ('desktop' === currentDeviceMode) ? '' : '_' + currentDeviceMode;

        };

        self.layerToEdit = function ($layer) {

            var offset = elementSettings.offset;

            if ('SECTION' === tag && !$el.hasClass("premium-lottie-yes")) {
                var length = $el.find(elementSettings.item).length;

                if (length > 1) {
                    return (length - 1) - $el.find($layer).index();
                }
            }

            return ($el.find($layer).index()) - offset;

        };

        self.verticalOffsetToPercent = function ($el, size) {

            size = size / ($el.offsetParent().height() / 100);

            return Math.round(size * 1000) / 1000;

        };

        self.setHandle = function () {

            return window.elementor.config.is_rtl ? 'w' : 'e';

        };

    };

    $(window).on("elementor/frontend/init", function () {

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-addon-flip-box.default",
            PremiumFlipboxHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-unfold-addon.default",
            PremiumUnfoldHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-addon-facebook-chat.default",
            PremiumFbChatHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-twitter-feed.default",
            PremiumTwitterFeedHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-notbar.default",
            PremiumAlertBoxHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-chart.default",
            PremiumChartHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-addon-instagram-feed.default",
            PremiumInstaFeedHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-facebook-feed.default",
            PremiumFacebookHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-whatsapp-chat.default",
            $whatsappElemHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-addon-tabs.default",
            PremiumTabsHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-addon-magic-section.default",
            PremiumMagicSectionHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-addon-preview-image.default",
            PremiumPreviewWindowHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-behance-feed.default",
            PremiumBehanceFeedHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-img-layers-addon.default",
            PremiumImageLayersHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-addon-image-comparison.default",
            PremiumImageCompareHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-addon-content-toggle.default",
            PremiumContentToggleHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-addon-image-hotspots.default",
            PremiumImageHotspotHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-tables-addon.default",
            PremiumTableHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-google-reviews.default",
            PremiumReviewHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-facebook-reviews.default",
            PremiumReviewHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-yelp-reviews.default",
            PremiumReviewHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-trustpilot-reviews.default",
            PremiumReviewHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-divider.default",
            PremiumDividerHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-multi-scroll.default",
            PremiumScrollHandler
        );

        elementorFrontend.hooks.addAction(
            'frontend/element_ready/premium-image-accordion.default',
            PremiumImageAccordionHandler
        );

        elementorFrontend.hooks.addAction(
            'frontend/element_ready/premium-color-transition.default',
            PremiumColorTransitionHandler
        );

        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-addon-icon-box.default",
            PremiumIconBoxHandler
        );

        if (elementorFrontend.isEditMode()) {

            elementorFrontend.hooks.addAction(
                "frontend/element_ready/premium-img-layers-addon.default",
                PremiumImageLayersEditorHandler
            );

            elementorFrontend.hooks.addAction(
                "frontend/element_ready/premium-addon-image-hotspots.default",
                PremiumImageHotspotEditorHandler
            );

        } else {

        }
    });
})(jQuery);