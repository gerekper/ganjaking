var FlipCarousel = function($scope, $) {
    var $this = $(".eael-flip-carousel", $scope);

    var style = $this.data("style"),
        start = $this.data("start"),
        fadeIn = $this.data("fadein"),
        loop = $this.data("loop"),
        autoplay = $this.data("autoplay"),
        pauseOnHover = $this.data("pauseonhover"),
        spacing = $this.data("spacing"),
        click = $this.data("click"),
        scrollwheel = $this.data("scrollwheel"),
        touch = $this.data("touch"),
        buttons = $this.data("buttons"),
        buttonPrev = ($this.data("buttonprev")),
        buttonNext = ($this.data("buttonnext")),
        options = {
            style: style,
            start: start,
            fadeIn: fadeIn,
            loop: loop,
            autoplay: autoplay,
            pauseOnHover: pauseOnHover,
            spacing: spacing,
            click: click,
            scrollwheel: scrollwheel,
            tocuh: touch,
            buttons: buttons,
            buttonPrev: '',
            buttonNext: ''
        };

    options.buttonPrev = '<span class="flip-custom-nav">' + buttonPrev + '</span>';
    options.buttonNext = '<span class="flip-custom-nav">' + buttonNext + '</span>';

    $this.flipster(options);
};

jQuery(window).on("elementor/frontend/init", function() {

    if (ea.elementStatusCheck('eaelFlipLoad')) {
        return false;
    }

    elementorFrontend.hooks.addAction(
        "frontend/element_ready/eael-flip-carousel.default",
        FlipCarousel
    );
});
