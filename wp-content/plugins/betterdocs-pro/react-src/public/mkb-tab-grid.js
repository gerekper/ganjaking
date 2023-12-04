jQuery(document).ready(function ($) {
    $('.betterdocs-tabs-nav-wrapper a').first().addClass('active');
    $('.betterdocs-tabgrid-content-wrapper').first().addClass('active');
    $('.tab-content-1').addClass('active');
    $('.betterdocs-tabs-nav-wrapper a').click(function (e) {
        e.preventDefault();
        $(this).siblings('a').removeClass('active').end().addClass('active');
        let selectedTab = this.getAttribute('data-toggle-target');
        $('.betterdocs-tabgrid-content-wrapper[data-tab_target="' + selectedTab + '"]')
            .addClass('active').siblings().removeClass('active');
    });
})
