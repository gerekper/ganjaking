jQuery(document).ready(function ($) {

    $('.mo-control-desktop').addClass('active');

    $('.mo-responsive-options .preview-desktop').click(function () {
        $('.wp-full-overlay').removeClass('preview-mobile').removeClass('preview-tablet').addClass('preview-desktop');
        $('.mo-responsive-options button').removeClass('active');
        $(this).addClass('active');
        $('.mo-control-mobile, .mo-control-tablet').removeClass('active');
        $('.mo-control-desktop').addClass('active');
    });

    $('.mo-responsive-options .preview-tablet').click(function () {
        $('.wp-full-overlay').removeClass('preview-desktop').removeClass('preview-mobile').addClass('preview-tablet');
        $('.mo-responsive-options button').removeClass('active');
        $(this).addClass('active');
        $('.mo-control-desktop, .mo-control-mobile').removeClass('active');
        $('.mo-control-tablet').addClass('active');
    });

    $('.mo-responsive-options .preview-mobile').click(function () {
        $('.wp-full-overlay').removeClass('preview-desktop').removeClass('preview-tablet').addClass('preview-mobile');
        $('.mo-responsive-options button').removeClass('active');
        $(this).addClass('active');
        $('.mo-control-desktop, .mo-control-tablet').removeClass('active');
        $('.mo-control-mobile').addClass('active');
    });

});