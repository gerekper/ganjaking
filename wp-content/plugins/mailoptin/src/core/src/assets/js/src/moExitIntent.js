/**
 * Inspired by https://github.com/flaviovs/jquery.exitintent
 */
define(["jquery"], function ($) {
    var timer;

    function trackLeave(ev) {
        if (ev.clientY > 20) {
            return;
        }

        if (timer) {
            clearTimeout(timer);
        }

        // delay triggering exit intent if visitor cursor move out and enter the viewport.
        timer = setTimeout(function () {
            timer = null;
            $.event.trigger("moExitIntent");
        }, 300);
    }

    function trackEnter() {
        if (timer) {
            clearTimeout(timer);
            timer = null;
        }
    }

    $.moExitIntent = function (enable) {
        if (enable === "enable") {
            $(window).on('mouseleave.moOptin', trackLeave);
            $(window).on('mouseenter.moOptin', trackEnter);
        } else if (enable === "disable") {
            trackEnter(); // Turn off any outstanding timer
            $(window).off("mouseleave.moOptin", trackLeave);
            $(window).off("mouseenter.moOptin", trackEnter);
        }
    };
});