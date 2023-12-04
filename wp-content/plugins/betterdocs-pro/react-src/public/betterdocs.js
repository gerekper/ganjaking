class BetterDocsPro {
    constructor(config) {
        this.config = config;
        this.initialize();
        this.init();
    }

    init() {
        // console.log(this.config);
        this.mkbTabGrid();
        this.liveSearch();
    }

    initialize() {
        var $ = jQuery;
        this.body = $('body');
    }

    liveSearch() {
        var $ = jQuery;
        $('.search-submit').on('click', function( e ) {
			e.preventDefault();
		});
    }

    mkbTabGrid() {
        var $ = jQuery;
        $('.betterdocs-tabs-nav-wrapper a').first().addClass('active');
        $('.betterdocs-tabgrid-content-wrapper').first().addClass('active');
        $('.tab-content-1').addClass('active');
        $('.betterdocs-tabs-nav-wrapper a').click(function (e) {
            e.preventDefault();
            $(this).siblings('a').removeClass('active').end().addClass('active');
            let selectedTab = this.getAttribute('data-toggle-target');
            $('.betterdocs-tabgrid-content-wrapper'+selectedTab).addClass('active').siblings().removeClass('active');
        });
    }
}

(function ($) {
    "use strict";
    new BetterDocsPro(window?.betterdocsConfig);
})(jQuery)
