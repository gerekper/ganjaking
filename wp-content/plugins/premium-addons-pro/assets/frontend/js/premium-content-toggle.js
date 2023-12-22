(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumContentToggleHandler = function ($scope, $) {

            var $contentToggle = $scope.find(".premium-content-toggle-container");

            var radioSwitch = $contentToggle.find(".premium-content-toggle-switch"),
                contentList = $contentToggle.find(".premium-content-toggle-two-content");

            changeSwitcherLayout();

            $(window).on('resize', function () {
                changeSwitcherLayout();
            })

            radioSwitch.prop('checked', false);

            var sides = {};
            sides[0] = contentList.find(
                'li[data-type="premium-content-toggle-monthly"]'
            );
            sides[1] = contentList.find(
                'li[data-type="premium-content-toggle-yearly"]'
            );

            radioSwitch.on("click", function (event) {

                var selected_filter = $(event.target).val();

                if ($(this).hasClass("premium-content-toggle-switch-active")) {

                    selected_filter = 0;

                    $(this).toggleClass(
                        "premium-content-toggle-switch-normal premium-content-toggle-switch-active"
                    );

                    hide_not_selected_items(sides, selected_filter);

                } else if ($(this).hasClass("premium-content-toggle-switch-normal")) {

                    selected_filter = 1;

                    $(this).toggleClass(
                        "premium-content-toggle-switch-normal premium-content-toggle-switch-active"
                    );

                    hide_not_selected_items(sides, selected_filter);

                }
            });

            function changeSwitcherLayout() {

                $contentToggle.removeClass('premium-toggle-stack-yes premium-toggle-stack-no');

                var computedStyle = getComputedStyle($scope[0]);

                $contentToggle.addClass("premium-toggle-stack-" + computedStyle.getPropertyValue('--pa-content-toggle-stack'));

            }

            function hide_not_selected_items(sides, filter) {
                $.each(sides, function (key, value) {
                    if (key != filter) {
                        $(this)
                            .removeClass("premium-content-toggle-is-visible")
                            .addClass("premium-content-toggle-is-hidden");
                    } else {
                        $(this)
                            .addClass("premium-content-toggle-is-visible")
                            .removeClass("premium-content-toggle-is-hidden");
                    }
                });
            }
        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-content-toggle.default', PremiumContentToggleHandler);
    });
})(jQuery);