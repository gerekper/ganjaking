/**
 * Start circle info widget script
 */

// this is the main function, here impvaring all html into js DOM as a
// parameter. 
function circleJs(id, circleMoving, movingTime, mouseEvent) {
    var circles = document.querySelectorAll('#' + id + ' .bdt-ep-circle-info-sub-circle');
    var circleContents = document.querySelectorAll('#' + id + '  .bdt-ep-circle-info-item');
    var parent = document.querySelector('#' + id + ' .bdt-ep-circle-info-inner ');

    var i = 2;
    var prevNowPlaying = null;

    if (movingTime <= 0) {
        movingTime = '100000000000';
    }

    if (circleMoving === false) {
        movingTime = '100000000000';
    }

    function myTimer() {
        //console.log('setInterval');
        var dataTab = jQuery(' #' + id + ' .bdt-ep-circle-info-sub-circle.active').data('circle-index');
        var totalSubCircle = jQuery('#' + id + ' .bdt-ep-circle-info-sub-circle').length; // here

        if (dataTab > totalSubCircle || i > totalSubCircle) {
            dataTab = 1;
            i = 1;
        }

        jQuery('#' + id + '  .bdt-ep-circle-info-sub-circle').removeClass('active');
        jQuery('#' + id + ' .bdt-ep-circle-info-sub-circle.active').removeClass('active', this);
        jQuery('#' + id + '  ' + '[data-circle-index=\'' + i + '\']').addClass('active');
        jQuery('#' + id + '  .bdt-ep-circle-info-item').removeClass('active');
        jQuery('#' + id + '  .icci' + i).addClass('active');
        i++;
        var activeIcon = '#' + id + ' .bdt-ep-circle-info-sub-circle i,' + '#' + id + ' .bdt-ep-circle-info-sub-circle svg';
        jQuery(activeIcon).css({
            'transform': 'rotate(' + (360 - (i - 2) * 36) + 'deg)',
            'transition': '2s'
        });
        jQuery('#' + id + ' .bdt-ep-circle-info-inner').css({
            'transform': 'rotate(' + ((i - 2) * 36) + 'deg) ',
            'transition': '1s'
        });

    }
    if (circleMoving === true) {
        var prevNowPlaying = setInterval(myTimer, movingTime);
    }
    if (circleMoving === false) {
        clearInterval(prevNowPlaying);
    }


    // active class toggle methods
    var removeClasses = function removeClasses(nodes, value) {
        var nodes = nodes;
        var value = value;
        if (nodes) return nodes.forEach(function (node) {
            return node.classList.contains(value) && node.classList.remove(value);
        });
        else return false;
    };
    var addClass = function addClass(nodes, index, value) {
        var nodes = nodes;
        var index = index;
        var value = value;
        return nodes ? nodes[index].classList.add(value) : 0;
    };
    var App = {
        initServicesCircle: function initServicesCircle() {
            // info circle
            if (parent) {
                var spreadCircles = function spreadCircles() {
                    // spread the sub-circles around the circle
                    var parent = document.querySelector('#' + id + ' .bdt-ep-circle-info-inner ').getBoundingClientRect();
                    var centerX = 0;
                    var centerY = 0;
                    Array.from(circles).reverse().forEach(function (circle, index) {
                        var circle = circle;
                        var index = index;
                        var angle = index * (360 / circles.length);
                        var x = centerX + (parent.width / 2) * Math.cos((angle * Math.PI) / 180);
                        var y = centerY + (parent.height / 2) * Math.sin((angle * Math.PI) / 180);
                        circle.style.transform = 'translate3d(' + parseFloat(x).toFixed(5) + 'px,' + parseFloat(y).toFixed(5) + 'px,0)';
                    });
                };

                spreadCircles();

                var resizeTimer = void 0;
                window.addEventListener('resize', function () {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function () {
                        spreadCircles();
                    }, 50);
                });
                circles.forEach(function (circle, index) {
                    var circle = circle;
                    var index = index;
                    var circlesToggleFnc = function circlesToggleFnc() {
                        this.index = circle.dataset.circleIndex;
                        if (!circle.classList.contains('active')) {
                            removeClasses(circles, 'active');
                            removeClasses(circleContents, 'active');
                            addClass(circles, index, 'active');
                            addClass(circleContents, index, 'active');
                        }
                    };
                    if (mouseEvent === 'mouseover') {
                        circle.addEventListener('mouseover', circlesToggleFnc, true);
                    } else if (mouseEvent === 'click') {
                        circle.addEventListener('click', circlesToggleFnc, true);
                    } else {
                        circle.addEventListener('mouseover', circlesToggleFnc, true);
                    }
                });
            }
        }
    };
    App.initServicesCircle();
}

(function ($, elementor) {
    'use strict';
    var widgetCircleInfo = function ($scope, $) {
        var $circleInfo = $scope.find('.bdt-ep-circle-info');

        if (!$circleInfo.length) {
            return;
        }

        elementorFrontend.waypoint($circleInfo, function () {
            var $this = jQuery(this);
            var $settings = $this.data('settings');

            circleJs($settings.id, $settings.circleMoving, $settings.movingTime, $settings.mouseEvent);

        }, {
            // offset: 'bottom-in-view'
            offset: '80%'
        });

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-circle-info.default', widgetCircleInfo);
    });
}(jQuery, window.elementorFrontend));

/**
 * End circle info widget script
 */