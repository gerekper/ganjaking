jQuery(document).ready(function($) {

    $('.yith-paypal-notice.is-dismissible').on('click', '.notice-dismiss', function (e) {

        Cookies.set('yith_adp_hide_notice', 1, { path: '/' } );

    });
});