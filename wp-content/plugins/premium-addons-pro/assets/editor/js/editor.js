(function ($) {

    'use strict';

    var PremiumWidgetsEditor = {

        activeSection: null,

        currentEditModel: null,

        inc: null,

        init: function () {

            window.elementor.on('preview:loaded', function () {

                elementor.$preview[0].contentWindow.PremiumWidgetsEditor = PremiumWidgetsEditor;

            });

            elementor.channels.editor.on('section:activated', PremiumWidgetsEditor.onSectionActivate);


        },

        onSectionActivate: function (sectionName, elementorEditor) {

            var editModelView = elementorEditor.getOption('editedElementView'),
                editModel = editModelView.model;

            window.PremiumWidgetsEditor.currentEditModel = editModelView;

            if ('premium-img-layers-addon' === editModel.get('widgetType') || 'premium-addon-image-hotspots' === editModel.get('widgetType')) {

                setTimeout(function () {
                    editModelView.$el.parent().sortable('disable');
                }, 500);

            } else {
                setTimeout(function () {
                    editModelView.$el.parent().sortable('enable');
                }, 500);
            }

        },

        activateEditorPanelTab: function (tab) {

            var $tab = $("div.elementor-control-" + tab);

            if ($tab.length && !$tab.hasClass('elementor-open')) {

                $tab.trigger('click');
            }

            jQuery("#elementor-panel-saver-button-save-options, #elementor-panel-saver-button-publish").removeClass("elementor-disabled");

        }

    };

    $(window).on('elementor:init', PremiumWidgetsEditor.init);

    window.PremiumWidgetsEditor = PremiumWidgetsEditor;

}(jQuery));
