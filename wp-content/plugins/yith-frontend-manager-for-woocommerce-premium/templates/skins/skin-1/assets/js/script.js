jQuery(document).ready(function ($) {

    // Mobile Navigation toggle
    var nav_toggle = $('#yith_wcfm-nav-toggle'),
        nav_toggle_link = nav_toggle.find('a'),
        navigation = $('.yith_wcfm-main-content-wrap');
    nav_toggle.on('click', function () {
        navigation.toggleClass('responsive-nav-closed');

        nav_toggle_link.attr('aria-expanded', !navigation.hasClass('responsive-nav-closed'));
    });

    // Enable double tap to go on main navigation
    $('#yith-wcfm-navigation-menu li:has(ul)').doubleTapToGo();


    // Style the checkbox inside the plugin
    var unstyled_checkbox = $('input[type="checkbox"]').not('.on_off');
    unstyled_checkbox.wrap('<span class="styled_checkbox"></span>');
    $('<i></i>').insertAfter(unstyled_checkbox);


    // Expand responsive tables
    var toggle_row = $('#yith_wcfm-main-content .wp-list-table .toggle-row');
    toggle_row.on('click', function () {
        var t = $(this);
        t.parents('tr').toggleClass('is-expanded');
    })
});