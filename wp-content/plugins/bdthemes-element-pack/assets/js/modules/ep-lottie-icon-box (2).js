/**
 * Start lottie icon box widget script
 */

(function($, elementor) {

    'use strict';

    // lottie-icon-box  
    var widgetLottieImage = function($scope, $) {

        var $lottie = $scope.find('.bdt-lottie-container'),
        $settings = $lottie.data('settings');

        if (!$lottie.length) {
            return;
        }

        var lottieContainer = document.getElementById($($lottie).attr('id'));

        function lottieRun(lottieContainer) {

            var json_path_url = "";

            if ($settings.is_json_url == 1) {
                if ($settings.json_path) {
                    json_path_url = $settings.json_path;
                }
            } else {
                if ($settings.json_code) {
                    var json_path_data = $settings.json_code;
                    var blob = new Blob([json_path_data], { type: 'application/javascript' });
                    json_path_url = URL.createObjectURL(blob);
                }
            }

            var animation = lottie.loadAnimation({
                container: lottieContainer, // Required
                path: json_path_url, // Required
                renderer: $settings.lottie_renderer, // Required
                autoplay: ('autoplay' === $settings.play_action), // Optional
                loop: $settings.loop, // Optional
            });
            URL.revokeObjectURL(json_path_url);

            animation.addEventListener('DOMLoaded', function(e) {
                var firstFrame = animation.firstFrame;
                var totalFrame = animation.totalFrames;

                function getFrameNumberByPercent(percent) {
                    percent = Math.min(100, Math.max(0, percent));
                    return firstFrame + (totalFrame - firstFrame) * percent / 100;
                }

                var startPoint = getFrameNumberByPercent($settings.start_point),
                endPoint = getFrameNumberByPercent($settings.end_point);

                animation.playSegments([startPoint, endPoint], true);

            });

            // if (1 >= $settings.speed) {
                animation.setSpeed($settings.speed);
            // }

            if ($settings.play_action) {


                if ('column' === $settings.play_action) {
                    lottieContainer = $scope.closest('.elementor-widget-wrap')[0];
                }

                if ('section' === $settings.play_action) {
                    lottieContainer = $scope.closest('.elementor-section')[0];
                }


                if ('click' === $settings.play_action) {
                    lottieContainer = $scope.closest('.elementor-widget-wrap')[0];
                    lottieContainer.addEventListener('click', function() {
                        animation.goToAndPlay(0);
                    });

                } else if ('autoplay' !== $settings.play_action) {
                    lottieContainer.addEventListener('mouseenter', function() {
                        animation.goToAndPlay(0);
                    });
                    // lottieContainer.addEventListener('mouseleave', function () {
                    //     animation.stop();
                    // });


                }

            }

        }


        if ('scroll' === $settings.view_type) {
            elementorFrontend.waypoint($lottie, function() {
                lottieRun(lottieContainer);
            });
        } else {
            lottieRun(lottieContainer);
        }
 
};


jQuery(window).on('elementor/frontend/init', function() {
    elementorFrontend.hooks.addAction('frontend/element_ready/bdt-lottie-icon-box.default', widgetLottieImage);
});

}(jQuery, window.elementorFrontend));

/**
 * End lottie icon box widget script
 */

