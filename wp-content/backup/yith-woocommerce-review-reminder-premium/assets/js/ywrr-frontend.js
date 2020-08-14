jQuery(document).ready(function ($) {

    if (window.location.hash === ywrr.reviews_tab) {

        var tab_content = ywrr.reviews_tab.replace('#').replace('tab-', '');

        $('.' + tab_content + '_tab a').click();

        if (ywrr.reviews_form !== '') {
            $('html, body').animate({
                scrollTop: $(ywrr.reviews_form).offset().top + parseInt(ywrr.offset)
            }, 500);
        }

    }

});