var ImageHotspotHandler = function ($scope, $) {
    
    //fixed tooltip blink issue
    // when body has position relative in wp login mode tipso top not working properly
    const position = $("body.elementor-page.logged-in.admin-bar");
    if (position.css("position")==="relative" && typeof $(".eael-hot-spot-wrap").data('tipso') !== 'undefined'){
        position.css("position","inherit");
    }
    
    $('.eael-hot-spot-tooptip').each(function () {
        var $position_local = $(this).data('tooltip-position-local'),
            $position_global = $(this).data('tooltip-position-global'),
            $width = $(this).data('tooltip-width'),
            $size = $(this).data('tooltip-size'),
            $animation_in = $(this).data('tooltip-animation-in'),
            $animation_out = $(this).data('tooltip-animation-out'),
            $animation_speed = $(this).data('tooltip-animation-speed'),
            $animation_delay = $(this).data('tooltip-animation-delay'),
            $background = $(this).data('tooltip-background'),
            $text_color = $(this).data('tooltip-text-color'),
            $arrow =
                $(this).data('eael-tooltip-arrow') === 'yes' ? true : false,
            $position = $position_local
        if (
            typeof $position_local === 'undefined' ||
            $position_local === 'global'
        ) {
            $position = $position_global
        }
        if (typeof $animation_out === 'undefined' || !$animation_out) {
            $animation_out = $animation_in
        }

        $(this).tipso({
            speed: $animation_speed,
            delay: $animation_delay,
            width: $width,
            background: $background,
            color: $text_color,
            size: $size,
            position: $position,
            animationIn: (typeof $animation_in != 'undefined') ? 'animate__' + $animation_in : '',
            animationOut: (typeof $animation_out != 'undefined') ? 'animate__' + $animation_out : '',
            showArrow: $arrow,
            autoClose: true,
            tooltipHover: true,
        })
    })

    // $('.eael-hot-spot-wrap').on('click', function (e) {
    //     // e.preventDefault();
    //     // e.stopImmediatePropagation();
    //     $link = $(this).data('link')
    //     $link_target = $(this).data('link-target')
    //
    //     if (typeof $link != 'undefined' && $link != '#') {
    //
    //         if ($link_target == '_blank') {
    //
    //             window.open($link)
    //         } else {
    //             alert('hash');
    //             window.location.hash = $link
    //         }
    //     }
    // })
}
jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction(
        'frontend/element_ready/eael-image-hotspots.default',
        ImageHotspotHandler
    )
})
