(function ($) {

    $(window).on('elementor/frontend/init', function () {

        /** Handles Custom Cursor for [ sections, columns, widget ]. */
        var premiumGlobalCursorHandler = function ($scope, $) {

            if (!$scope.hasClass('premium-gCursor-yes')) {
                return;
            }

            var disabledDevices = $scope.data('pa_disable_cursor_on') || [],
                isDeviceAllowed = disabledDevices.includes(elementorFrontend.getCurrentDeviceMode()) ? false : true;

            if (!isDeviceAllowed) {
                $scope.removeClass('premium-gCursor-yes');
                return;
            }

            var elType = $scope.data('element_type'),
                eleId = $scope.data('id'),
                settings = {},
                isInnerSection = 'section' === elType ? $scope.hasClass('elementor-inner-section') : $scope.closest('.elementor-section').hasClass('elementor-inner-section'),
                eleInfo = {
                    isInnerSection: isInnerSection
                },
                parentClass = isInnerSection ? 'inner' : 'top';

            if (isInnerSection) { // if the element is/is in an inner section.

                eleInfo.$innerSec = ('section' === elType) ? $scope : $scope.closest('.elementor-inner-section');
                eleInfo.innerSecId = eleInfo.$innerSec.data('id');

                eleInfo.$parentCol = eleInfo.$innerSec.closest('.elementor-top-column');
                eleInfo.parentColId = eleInfo.$parentCol.data('id');

                eleInfo.$parentSec = eleInfo.$parentCol.closest('.elementor-top-section');
                eleInfo.parentSecId = eleInfo.$parentSec.data('id');
            }

            if ('section' !== elType) {
                eleInfo.$section = $scope.closest('.elementor-' + parentClass + '-section');

                if ('widget' === elType) {
                    eleInfo.$col = $scope.closest('.elementor-' + parentClass + '-column');
                }
            }

            eleInfo.colId = eleInfo.$col ? eleInfo.$col.data('id') : '';
            eleInfo.sectionId = eleInfo.$section ? eleInfo.$section.data('id') : '';

            generateSettings(elType, eleId);

            if (!settings) {
                return false;
            }

            // always show the cursor in the editor.
            if (elementorFrontend.isEditMode() || !$scope.data('pa_mobile_disabled')) {
                elementorFrontend.waypoint(
                    $scope,
                    function () {
                        var cursorInstance = new paCustomCursorHandler(elType, $scope, settings);
                        cursorInstance.generateCursor();
                    }
                );
            } else {
                $scope.removeClass('premium-gCursor-yes');
            }

            function generateSettings(type, id) {

                var editMode = elementorFrontend.isEditMode(),
                    tempTarget = $scope.find('#premium-global-cursor-' + id),
                    tempTarget2 = $scope.find('#premium-global-cursor-temp-' + id),
                    tempExist = 0 !== tempTarget.length || 0 !== tempTarget2.length,
                    editMode = elementorFrontend.isEditMode() && tempExist;

                if (editMode) {

                    settings = tempTarget.data('gcursor');

                    if ('widget' === type && !settings) {
                        settings = tempTarget2.data('gcursor');
                    }
                } else {

                    settings = $scope.data('gcursor');
                }

                if (!settings) {
                    return false;
                }

                settings.eleInfo = eleInfo;
                settings.elemId = id;

                if (0 !== Object.keys(settings).length) {
                    return settings;
                }
            }
        };

        elementorFrontend.hooks.addAction("frontend/element_ready/global", premiumGlobalCursorHandler);
    });

})(jQuery);