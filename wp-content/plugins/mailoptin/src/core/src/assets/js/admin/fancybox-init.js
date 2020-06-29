(function ($) {
    $(document).ready(function () {
        $(".mo-open-link-fancybox").click(function (e) {
            e.preventDefault();
            $.fancybox.open({
                src: $(this).attr("href"),
                type: 'iframe'
            });
        });
    });
})(jQuery);