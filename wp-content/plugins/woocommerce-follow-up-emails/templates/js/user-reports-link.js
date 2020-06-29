jQuery(document).ready(function($) {
    var $form = $("#your-profile");
    var menu = '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper"> \
        <a href="#" class="nav-tab nav-tab-active">'+ FUE_USER_REPORT.options_title +'</a> \
        <a href="'+ FUE_USER_REPORT.reports_link +'" class="nav-tab">'+ FUE_USER_REPORT.reports_title +'</a> \
        </h2>';

    $form.prepend( menu );
});
