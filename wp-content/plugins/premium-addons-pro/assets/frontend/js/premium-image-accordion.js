(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumImageAccordionHandler = elementorModules.frontend.handlers.Base.extend({

            getDefaultSettings: function () {

                return {
                    selectors: {
                        accordionElem: '.premium-accordion-section',
                        accordionItems: '.premium-accordion-li',
                        accordionTemplate: '.premium-accord-temp',
                        accordionDesc: '.premium-accordion-description'
                    }
                }

            },
            getDefaultElements: function () {

                var selectors = this.getSettings('selectors');

                return {
                    $accordionElem: this.$element.find(selectors.accordionElem),
                    $accordionItems: this.$element.find(selectors.accordionItems),
                    $accordionTemplate: this.$element.find(selectors.accordionTemplate),
                    $accordionDesc: this.$element.find(selectors.accordionDesc)
                }

            },
            bindEvents: function () {
                this.run();
            },

            run: function () {

                var $window = $(window),
                    $accordionElem = this.elements.$accordionElem,
                    $accordionItems = this.elements.$accordionItems;

                if (elementorFrontend.isEditMode()) {
                    this.checkAccordionTemps();
                }

                //Trigger Hovered Image Width Function on page load only if Default Index option value is set.
                if ($accordionElem.find('.premium-accordion-li-active').length > 0) {
                    this.resizeImgs();
                }

                var _this = this,
                    hideDesc = this.getElementSettings('hide_description_thresold');

                $window.resize(function () {
                    _this.elements.$accordionDesc.css('display', hideDesc > $window.outerWidth() ? 'none' : 'block');

                    _this.resizeImgs();
                });

                $accordionItems.hover(function () {

                    $accordionItems.removeClass('premium-accordion-li-active');

                    if (!$(this).hasClass('premium-accordion-li-active')) {

                        $(this).addClass('premium-accordion-li-active');
                    }

                    _this.resizeImgs();
                });

                $accordionItems.mouseleave(function () {
                    $accordionElem.find('.premium-accordion-li, .premium-accordion-ul, .premium-accordion-overlay-wrap').attr('style', '');
                    $accordionItems.removeClass('premium-accordion-li-active');
                });

            },
            checkAccordionTemps: function () {

                var $window = $(window);

                this.elements.$accordionTemplate.each(function (index, img) {

                    var templateID = $(img).data("template");

                    if (undefined !== templateID && '' !== templateID) {
                        $.ajax({
                            type: "GET",
                            url: PremiumProSettings.ajaxurl,
                            dataType: "html",
                            data: {
                                action: "get_elementor_template_content",
                                templateID: templateID
                            }
                        }).success(function (response) {

                            var data;

                            try {
                                data = JSON.parse(response).data;
                            } catch (error) {
                                data = response.data;
                            }

                            if (undefined !== data.template_content) {
                                $(img).html(data.template_content);
                                $window.resize();
                            }
                        });
                    }
                });

            },

            resizeImgs: function () {

                var settings = this.getElementSettings(),
                    $accordionElem = this.elements.$accordionElem,
                    $accordionItems = this.elements.$accordionItems,
                    count = $accordionItems.length,
                    currentDevice = elementorFrontend.getCurrentDeviceMode(),
                    suffix = 'desktop' === currentDevice ? '' : '_' + currentDevice;

                var imgWidth = settings['active_img_size' + suffix].size;

                if ('horizontal' === settings.direction_type) {

                    if (imgWidth) {
                        var width = 'width: calc( (100% - ' + imgWidth + '% ) /' + (count - 1) + ')';
                        $accordionElem.find('.premium-accordion-li:not(.premium-accordion-li-active)').attr('style', width);
                    }

                } else {

                    var imgHeight = settings['height' + suffix].size,
                        initialHeight = ('' === imgHeight) ? 200 : imgHeight,
                        height = ('' === imgWidth) ? 400 : initialHeight * count * imgWidth * 0.01;

                    $accordionElem.find('.premium-accordion-li-active').attr('style', 'height: ' + height + 'px !important');

                    if ('' !== imgWidth) {
                        $accordionElem.find('.premium-accordion-li:not(.premium-accordion-li-active)').attr('style', 'height: calc( (' + initialHeight * count + 'px - ' + height + 'px ) /' + (count - 1) + ')');
                    }

                }

                if (100 === imgWidth) {
                    $accordionItems.css({
                        'padding': 0,
                        'margin': 0
                    });

                    $accordionElem.find('.premium-accordion-overlay-wrap').css('width', '100%');
                    $accordionElem.find('.premium-accordion-ul').css('borderSpacing', '0 0');
                }

            }


        });

        elementorFrontend.elementsHandler.attachHandler('premium-image-accordion', PremiumImageAccordionHandler);
    });
})(jQuery);