; (function ($, elementor) {
    'use strict';

    $(window).on('elementor/frontend/init', function () {

        var ModuleHandler = elementorModules.frontend.handlers.Base, MegaMenu;


        MegaMenu = ModuleHandler.extend({
            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {};
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_megamenu_') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('ep_megamenu_' + key);
            },

            run: function () {
                var $this = this;
                var options = this.getDefaultSettings(),
                    widgetID = this.$element.data('id');
                var element = this.findElement('.elementor-widget-container').get(0);
                if (jQuery(this.$element).hasClass('elementor-section')) {
                    element = this.$element.get(0);
                }
                var $container = this.$element.find(".ep-megamenu");


                if (!$container.length) {
                    return;
                }

                //initial breking issue fixed
                $container.find('.megamenu-header-mobile').removeAttr('style');
                $container.removeClass('initialized');

               var dropMenu =  $($container).find('.ep-megamenu-vertical-dropdown');

                bdtUIkit.drop(dropMenu, {
                    offset: (this.settings("vertical_dropdown_offset") !== undefined) ? this.settings('vertical_dropdown_offset') : '10',
                    animation: (this.settings("vertical_dropdown_animation_type") !== undefined) ? this.settings('vertical_dropdown_animation_type') : 'fade',
                    duration: (this.settings("vertical_dropdown_animation_duration") !== undefined) ? this.settings('vertical_dropdown_animation_duration') : '200',
                    mode: (this.settings("vertical_dropdown_mode") !== undefined) ? this.settings('vertical_dropdown_mode') : 'click',
                    animateOut: (this.settings("vertical_dropdown_animate_out") !== undefined) ? this.settings('vertical_dropdown_animate_out') : false,
                });

                //has megamenu
                var $megamenu_items = $container.find('.ep-has-megamenu');
                var $subDropdown = $container.find('.ep-default-submenu-panel');




                //dropdown options
                options.flip = false;
                options.offset = (this.settings("offset.size") !== '') ? this.settings('offset.size') : '10';
                options.animation = (this.settings("animation_type") !== undefined) ? this.settings('animation_type') : 'fade';
                options.duration = (this.settings("animation_duration") !== undefined) ? this.settings('animation_duration') : '200';
                options.mode = (this.settings("mode") !== undefined) ? this.settings('mode') : 'hover';
                options.animateOut = (this.settings("animate_out") !== undefined) ? this.settings('animate_out') : false;



                $($megamenu_items).each(function (index, item) {
                    var $drop = $(item).find('.ep-megamenu-panel');
                    var widthType = $(item).data('width-type');

                    var defaltWidthSelector = $(item).closest('.e-con-inner');
                    if (defaltWidthSelector.length <= 0){
                        var defaltWidthSelector =
                            $(item).closest(".elementor-container");

                    }

                    if ('horizontal' === $this.settings('direction')) {
                        switch (widthType) {
                            case 'custom':
                                options.stretch = null;
                                options.target = null;
                                options.boundary = null;
                                options.pos = $(item).data('content-pos');
                                $(this).find(".ep-megamenu-panel").css({
                                    "min-width": $(item).data('content-width'),
                                    "max-width": $(item).data('content-width'),
                                });
                                break;
                            case 'full':
                                options.stretch = 'x';
                                options.target = '#ep-megamenu-' + widgetID;
                                options.boundary = false;
                                break;
                            default:
                                options.stretch = 'x';
                                options.target = '#ep-megamenu-' + widgetID;
                                options.boundary = defaltWidthSelector
                            break;
                        }
                    } else if ('vertical' === $this.settings('direction')) {
                        switch (widthType) {
                            case 'custom':
                                options.stretch = false;
                                options.target = false;
                                options.boundary = false;
                                $(this).find(".ep-megamenu-panel").css({
                                    "min-width": $(item).data('content-width'),
                                    "max-width": $(item).data('content-width'),
                                });
                                break;
                            default:
                                options.stretch = 'x';
                                break;

                        }
                        //check is RTL
                        if ($($container).data("is-rtl") === 1) {
                            options.pos = 'left-top';
                        } else {
                            options.pos = 'right-top';
                        }
                    }

                    // options.toggle = $($drop).closest('.menu-item').find('.ep-menu-nav-link');

                    bdtUIkit.drop($drop, options);
                });


                $($subDropdown).each(function (index, item) {
                    if ('horizontal' === $this.settings('direction')) {
                        if ($(item).hasClass('ep-parent-element')){
                            options.pos = 'bottom-left';
                        }else{
                            options.pos = 'right-top';
                        }
                    } else if ('vertical' === $this.settings('direction')) {
                                options.stretch = false;
                                $(this).find(".ep-megamenu-panel").css({
                                    "padding-left": "20px"
                                });

                        //check is RTL
                        if ($($container).data("is-rtl") === 1) {
                            options.pos = 'left-top';
                        } else {
                            options.pos = 'right-top';
                        }
                    }
                    options.stretch = false;
                    options.target = false;
                    options.flip = true;


                    bdtUIkit.drop(item, options);
                });


                  var dropWrapper = $(element).closest('.elementor-top-section');
                if(dropWrapper.length <= 0){
                    var dropWrapper = $(element).closest(".elementor-element.e-con.e-parent");
                    if(dropWrapper.length <= 0){
                        var dropWrapper = $(element).closest(".elementor-widget-bdt-mega-menu");
                    }
                }


                if ($(dropWrapper).find('.ep-virtual-area').length === 0) {
                    // code to run if it isn't there
                    $('#ep-megamenu-' + widgetID).clone().appendTo(dropWrapper).wrapAll('<div class="ep-virtual-area" />');
                    dropWrapper.find('.ep-virtual-area [id]').each(function (index, obj) {
                        let old_id = $(obj).attr('id');
                        $(obj).attr('id', old_id + '-virtual');

                    });
                    dropWrapper.find('.ep-virtual-area [fill]').each(function (index, obj) {
                        let fill_id = $(obj).attr('fill');
                        if (fill_id.indexOf('url(#') == 0) {
                            $(obj).attr('fill', ('url(#', fill_id.slice(0, -1) + '-virtual)'));
                        }
                    });
                }


                /**
                 * Remove Attributes from Virtual DOMS
                 */

                dropWrapper.find('.ep-virtual-area .bdt-navbar-nav').removeAttr('class');
                dropWrapper.find('.ep-virtual-area .menu-item').removeAttr('data-width-type');
                dropWrapper.find('.ep-virtual-area .menu-item').removeAttr('data-content-width');
                dropWrapper.find('.ep-virtual-area .menu-item').removeAttr('data-content-pos');
                dropWrapper.find('.ep-virtual-area .menu-item-has-children').addClass('ep-has-megamenu');
                dropWrapper.find('.ep-virtual-area .ep-megamenu-panel').removeClass().addClass('bdt-accordion-content');
                dropWrapper.find('.ep-virtual-area .bdt-accordion-content').removeAttr('style');


                $(this).find('.details').removeClass("hidden");
                $($container).find(".sub-menu-toggle").remove();


                if ($(dropWrapper).find('.bdt-accrodion-title-megamenu').length === 0) {
                    dropWrapper.find('.ep-virtual-area .ep-menu-nav-link').wrap("<span class='bdt-accordion-title bdt-accrodion-title-megamenu'></span>");
                    dropWrapper.find('.ep-virtual-area .ep-menu-nav-link').attr("onclick", "event.stopPropagation();");
                    dropWrapper.find('.ep-virtual-area .bdt-megamenu-indicator').remove();
                    $('<i class="bdt-megamenu-indicator ep-icon-arrow-down-3"></i>').appendTo(dropWrapper.find('.ep-has-megamenu .bdt-accordion-title'));
                }


                /**
                 *  Mobile toggler
                 */
                var $toggler = $container.find('.bdt-navbar-toggle');
                var $toggleContent = dropWrapper.find('.ep-virtual-area');
                bdtUIkit.drop($toggleContent, {
                    offset: (this.settings("offset_mobile.size") !== '') ? this.settings('offset_mobile.size') : '5',
                    // offset:5,
                    toggle: $toggler,
                    animation: (this.settings("animation_type") !== undefined) ? this.settings('animation_type') : 'fade',
                    duration: (this.settings("animation_duration") !== undefined) ? this.settings('animation_duration') : '200',
                    mode: 'click',
                });


                //ACCORDION
                bdtUIkit.accordion('#ep-megamenu-' + widgetID + '-virtual', {
                    'offset': 10
                });

            }
        });


        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-mega-menu.default',
            function ($scope) {
                elementorFrontend.elementsHandler.addHandler(MegaMenu, {
                    $element: $scope
                });
            }
        );
    });

})(jQuery, window.elementorFrontend);