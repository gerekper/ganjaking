; (function ($, elementor) {
$(window).on('elementor/frontend/init', function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
        textReadMore;

    textReadMore = ModuleHandler.extend({
        bindEvents: function () {
            this.run();
        },
        getDefaultSettings: function () {
            return {
                allowHTML: true,
            };
        },

        onElementChange: debounce(function (prop) {
            if (prop.indexOf('ep_text_read_more_') !== -1) {
                this.run();
            }
        }, 400),

        settings: function (key) {
            return this.getElementSettings('ep_text_read_more_' + key);
        },

        run: function () {
            var tileScroll_ID = 'bdt-tile-scroll-container-' + this.$element.data('id'),
                widgetID = this.$element.data('id'),
                widgetContainer = $('.elementor-element-' + widgetID);
            var button_style = this.settings('button_style');
            if (this.settings('enable') === 'yes') {
                const dReadMore = new DReadMore();

                window.addEventListener('resize', function () {
                    dReadMore.forEach(function (item) {
                        item.update();
                    });
                });
                // let truncateElement = new Cuttr('.elementor-widget-container', {
                //     truncate: this.settings('truncate') ? this.settings('truncate') : 'characters',
                //     length: this.settings('length') ? this.settings('length') : 10,
                //     ending: this.settings('ending'),
                //     loadedClass: 'cuttr--loaded',
                //     title: this.settings('title') ? this.settings('title'): false,
                //     readMore: this.settings('show_button') ? this.settings('show_button'): false,
                //     readMoreText: this.settings('button_text') ? this.settings('button_text'): 'Read More',
                //     // readMoreText:'<i class="eicon-h-align-left">Read More</i>',
                //     readLessText: this.settings('button_text_less') ? this.settings('button_text_less'): 'Read Less',
                //     readMoreBtnPosition: this.settings('button_position') ? this.settings('button_position'): 'after',
                //     readMoreBtnTag: 'div',
                //     readMoreBtnSelectorClass: 'bdt-text-read-more-btn',
                //     readMoreBtnAdditionalClasses: this.settings('button_style') ? this.settings('button_style'): 'nnn',
                // })
            } else {
                return;
            }

        }
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
        elementorFrontend.elementsHandler.addHandler(textReadMore, {
            $element: $scope
        });
    });
});
})(jQuery, window.elementorFrontend);