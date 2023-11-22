(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumUnfoldHandler = elementorModules.frontend.handlers.Base.extend({

            getDefaultSettings: function () {

                return {
                    selectors: {
                        unfoldContentWrap: '.premium-unfold-content-wrap',
                        unfoldButtonTxt: '.premium-unfold-button-text',
                        unfoldContent: '.premium-unfold-content',
                        unfoldIcon: '.premium-unfold-icon',
                        unfoldIconHolder: '.premium-icon-holder-unfolded',
                        unfoldGradient: '.premium-unfold-gradient',
                        foldIconHolder: '.premium-icon-holder-fold',
                    }
                }
            },

            getDefaultElements: function () {
                var selectors = this.getSettings('selectors'),
                    elements = {
                        $unfoldElem: this.$element,
                    };

                elements.$unfoldContentWrap = elements.$unfoldElem.find(selectors.unfoldContentWrap);
                elements.$unfoldButtonTxt = elements.$unfoldElem.find(selectors.unfoldButtonTxt);
                elements.$unfoldContent = elements.$unfoldElem.find(selectors.unfoldContent);
                elements.$unfoldIcon = elements.$unfoldElem.find(selectors.unfoldIcon);
                elements.$unfoldIconHolder = elements.$unfoldElem.find(selectors.unfoldIconHolder);
                elements.$unfoldGradient = elements.$unfoldElem.find(selectors.unfoldGradient);
                elements.$foldIconHolder = elements.$unfoldElem.find(selectors.foldIconHolder);

                return elements;
            },

            bindEvents: function () {
                this.run();
            },

            run: function () {

                var $unfoldElem = this.elements.$unfoldElem,
                    $unfoldButtonTxt = this.elements.$unfoldButtonTxt,
                    $unfoldContent = this.elements.$unfoldContent,
                    $unfoldIcon = this.elements.$unfoldIcon,
                    $unfoldIconHolder = this.elements.$unfoldIconHolder,
                    $unfoldGradient = this.elements.$unfoldGradient,
                    $foldIconHolder = this.elements.$foldIconHolder,
                    settings = this.getElementSettings(),
                    $unfoldContentWrap = this.elements.$unfoldContentWrap,
                    contentHeight = parseInt($unfoldContentWrap.outerHeight()),
                    foldHeight = this.getFoldHeight(),
                    foldSelect = settings.premium_unfold_fold_height_select,
                    foldText = settings.premium_unfold_button_fold_text,
                    unfoldText = settings.premium_unfold_button_unfold_text,
                    foldEase = settings.premium_unfold_fold_easing,
                    unfoldEase = settings.premium_unfold_unfold_easing,
                    foldDur = 'custom' === settings.premium_unfold_fold_dur_select ? settings.premium_unfold_fold_dur * 1000 : settings.premium_unfold_fold_dur_select,
                    unfoldDur = 'custom' === settings.premium_unfold_unfold_dur_select ? settings.premium_unfold_unfold_dur * 1000 : settings.premium_unfold_unfold_dur_select;

                if ("percent" === foldSelect) {
                    foldHeight = (foldHeight / 100) * contentHeight;
                }

                $unfoldButtonTxt.text(foldText);

                $unfoldContent.css('height', foldHeight);

                $unfoldIcon.html($unfoldIconHolder.html());

                $unfoldElem.on('click', '.premium-button', function (e) {

                    e.preventDefault();

                    setTimeout(function () {
                        $unfoldElem.removeClass('prevented');
                    }, foldDur + 50);

                    if (!$unfoldElem.hasClass('prevented')) {

                        $unfoldElem.addClass('prevented');

                        var text = $unfoldContent.hasClass("toggled") ? unfoldText : foldText;

                        $unfoldButtonTxt.text(text);

                        if ($unfoldContent.hasClass("toggled")) {

                            contentHeight = parseInt($unfoldContentWrap.outerHeight());

                            $unfoldContent.css("overflow", "visible");

                            $unfoldContent.animate({ height: contentHeight }, unfoldDur, unfoldEase).removeClass("toggled");

                        } else {

                            $unfoldContent.css("overflow", "hidden");
                            $unfoldContent.animate({ height: foldHeight }, foldDur, foldEase).addClass("toggled");
                        }

                        $unfoldGradient.toggleClass("toggled");

                        if ($unfoldContent.hasClass("toggled")) {
                            $unfoldIcon.html($unfoldIconHolder.html());
                        } else {
                            $unfoldIcon.html($foldIconHolder.html());
                        }
                    }
                });
            },

            getFoldHeight: function () {
                var settings = this.getElementSettings(),
                    suffix = 'desktop' === elementorFrontend.getCurrentDeviceMode() ? '' : '_' + elementorFrontend.getCurrentDeviceMode(),
                    unit = settings.premium_unfold_fold_height_select,
                    defaultHeight = 60;

                if ('pixel' === unit) {
                    defaultHeight = 100;
                    suffix = '_pix' + suffix;
                }

                return undefined != settings['premium_unfold_fold_height' + suffix] ? settings['premium_unfold_fold_height' + suffix] : defaultHeight;

            },

        });

        elementorFrontend.elementsHandler.attachHandler('premium-unfold-addon', PremiumUnfoldHandler);
    });
})(jQuery);