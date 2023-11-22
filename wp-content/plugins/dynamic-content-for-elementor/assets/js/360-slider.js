(function ($) {
    var WidgetElements_ThreeSixtySliderHandler = function ($scope, $) {
        elementSettings360 = dceGetElementSettings($scope);
        var threesixty = $scope.find('.dce-threesixty');
        if (threesixty) {
            var frames = Number(threesixty.attr('data-total_frame'));
            threesixty.ThreeSixty({
                totalFrames: frames, // Total no. of image you have for 360 slider
                endFrame: frames, // end frame for the auto spin animation
                imgList: '.threesixty_images', // selector for image list
                progress: '.spinner', // selector to show the loading progress
                imagePath: threesixty.attr('data-pathimages'), // path of the image assets
                filePrefix: '', // file prefix if any
                ext: '.' + threesixty.attr('data-format_file'), // extension for the assets
                height: 'auto',
                width: 'auto',
				position: elementSettings360.navigation_position, // position of controls
                navigation: Boolean( elementSettings360.navigation ),
                responsive: true,
				disableSpin: Boolean( elementSettings360.disable_spin ),
				playSpeed: Number( elementSettings360.play_speed.size ),
            });
        }
    };

    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/dyncontel-threesixtyslider.default', WidgetElements_ThreeSixtySliderHandler);
    });
})(jQuery);
