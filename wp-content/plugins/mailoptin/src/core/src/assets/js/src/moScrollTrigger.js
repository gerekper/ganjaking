define(["jquery"], function ($) {
    var winheight, docheight, trackLength, throttlescroll;

    function getmeasurements() {
        winheight = $(window).height();
        docheight = $(document).height();
        trackLength = docheight - winheight;
    }

    function amountscrolled() {
        var scrollTop = $(window).scrollTop();
        var pctScrolled = Math.floor(scrollTop / trackLength * 100);

        $.event.trigger('moScrollTrigger', [pctScrolled])
    }

    $(window).on('resize', function () {
        getmeasurements()
    });

    $.moScrollTrigger = function (enable) {
        if (enable === "enable") {
            getmeasurements();
            $(window).on("scroll", function () {
                clearTimeout(throttlescroll);
                throttlescroll = setTimeout(function () {
                    amountscrolled()
                }, 50)
            });
        }
    }
});