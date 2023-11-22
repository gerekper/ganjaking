; (function ($, elementor) {

$(window).on('elementor/frontend/init', function () {

    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {

        $scope.hasClass('elementor-element-edit-mode') && $scope.addClass('bdt-background-overlay-yes');

    });

});

}) (jQuery, window.elementorFrontend);