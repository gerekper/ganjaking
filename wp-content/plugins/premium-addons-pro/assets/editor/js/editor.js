(function ($) {

    'use strict';

    var PremiumWidgetsEditor = {

        activeSection: null,

        currentEditModel: null,

        inc: null,

        init: function () {

            PremiumWidgetsEditor.activatePathGenerator();

            window.elementor.on('preview:loaded', function () {

                elementor.$preview[0].contentWindow.PremiumWidgetsEditor = PremiumWidgetsEditor;

            });

            elementor.channels.editor.on('section:activated', PremiumWidgetsEditor.onSectionActivate);

            elementor.on('navigator:init', PremiumWidgetsEditor.onNavigatorInit);

        },

        onNavigatorInit: function () {

            elementor.navigator.indicators.magicScroll = {
                icon: 'star',
                settingKeys: ['premium_mscroll_switcher'],
                title: wp.i18n.__('Magic Scroll', 'premium-addons-pro'),
                section: 'section_mscroll'
            };

        },

        onSectionActivate: function (sectionName, elementorEditor) {

            var editModelView = elementorEditor.getOption('editedElementView'),
                editModel = editModelView.model;

            if ('section_mscroll' === sectionName) {

                var $stickyRef = $('.elementor-control-premium_mscroll_sticky_element input');

                $stickyRef.on('input', function () {

                    if ($(this).val().length > 1) {
                        var refElem = $(this).val();

                        if (elementorFrontend.elements.$body.find(refElem).length < 1) {
                            $('.elementor-control-premium_mscroll_sticky_note .elementor-control').removeClass('elementor-hidden-control');
                        } else {
                            $('.elementor-control-premium_mscroll_sticky_note .elementor-control').addClass('elementor-hidden-control');
                        }

                    }

                });

            }

            window.PremiumWidgetsEditor.currentEditModel = editModelView;

            if ('premium-img-layers-addon' === editModel.get('widgetType') || 'premium-addon-image-hotspots' === editModel.get('widgetType')) {

                setTimeout(function () {
                    if (editModelView.$el.parent().hasClass("ui-sortable"))
                        editModelView.$el.parent().sortable('disable');
                }, 500);

            } else {
                setTimeout(function () {
                    if (editModelView.$el.parent().hasClass("ui-sortable"))
                        editModelView.$el.parent().sortable('enable');
                }, 500);
            }

        },

        reRender: function (currentDev) {

            if (currentDev === "desktop")
                currentDev = '';

            $(".elementor-control-premium_img_layers_hor_position_" + currentDev).find("input").trigger("input");

        },

        activateEditorPanelTab: function (tab) {

            var $tab = $("div.elementor-control-" + tab);

            if ($tab.length && !$tab.hasClass('e-open')) {

                $tab.trigger('click');
            }

            jQuery("#elementor-panel-saver-button-save-options, #elementor-panel-saver-button-publish").removeClass("elementor-disabled");

        },

        activatePathGenerator: function () {
            elementor.channels.editor.on("generate", function (e) {

                var data = e._parent.model,
                    blobs = e.options.container.view.$el,
                    blobsAttr = blobs.find('#premium-blob-gen-' + blobs.data('id')).data('blob')[0],
                    blobSize = data.attributes.premium_blob_size.size,
                    seed = Math.random(),
                    newPath = BlobGenerator.svgPath({ seed: seed, size: blobSize, extraPoints: data.attributes.premium_blob_complexity.size, randomness: data.attributes.premium_blob_randomness.size }).trim(),
                    blobHtml = PremiumWidgetsEditor.drawBlob(blobsAttr, newPath),
                    params = {
                        'seed': seed,
                        'html': blobHtml,
                        'path': newPath
                    };

                $e.run("document/elements/settings", { container: e.container, settings: { pa_blob_custom: params }, options: { external: !0 } });

            });

        },

        updateBlobs: function (blobAttr, $scope) {

            if (blobAttr) {

                var models = this.getBlobModels($scope),
                    blobs = blobAttr,
                    blobHtml = '',
                    params = {};

                $.each(blobs, function (index, blob) {

                    if ('pre' === blob.type) {
                        blobHtml = blob.source;

                    } else {
                        var path = BlobGenerator.svgPath({ seed: blob.source.seed, size: blob.size, extraPoints: blob.extraPoints, randomness: blob.randomness }).trim();

                        blobHtml = PremiumWidgetsEditor.drawBlob(blob, path);

                        params = {
                            'seed': blob.source.seed,
                            'html': blobHtml,
                            'path': path
                        };

                        models[index].setExternalChange({ "pa_blob_custom": params });
                    }
                });

            }
        },

        getBlobModels: function ($scope) {

            var targetId = $scope.data('id');

            if (!window.elementor.hasOwnProperty("elements")) {
                return false;
            }

            var editorElements = window.elementor.elements,
                sectionData = {};

            if (!editorElements.models) {
                return false;
            }

            // support for sections and containers only.
            $.each(editorElements.models, function (index, elem) {
                if (targetId == elem.id) { // section
                    sectionData = elem.attributes.settings.attributes;
                } else if (
                    elem.id === $scope.closest(".elementor-top-section").data("id")
                ) { // inner section
                    $.each(elem.attributes.elements.models, function (index, col) {

                        $.each(col.attributes.elements.models, function (index, subSec) {

                            // search inside each column for an inner section only
                            if ( targetId == subSec.id ) {
                                sectionData = subSec.attributes.settings.attributes;
                            }
                        });
                    });
                }
            });

            if (!sectionData.hasOwnProperty("premium_blob_repeater")) {
                return false;
            }

            return sectionData["premium_blob_repeater"].models;
        },

        drawBlob: function (blob, path) {

            var blobHtml = '<svg width="' + blob.size + '" height="' + blob.size + '" viewBox="0 0 ' + blob.size + ' ' + blob.size + '" xmlns="http://www.w3.org/2000/svg">';

            var pathFill = 'none',
                fill = blob.fill;

            switch (blob.fillType) {

                case 'color':
                    pathFill = fill;
                    break;

                case 'image':
                    blobHtml +=
                        '<defs>' +
                        '<pattern id="img' + fill.img.id + '" patternUnits="userSpaceOnUse" width="100%" height="100%">' +

                        '<image href="' + fill.img.url + '" x="' + fill.xpos + '" y="' + fill.ypos + '" width="' + fill.width + '" height="' + fill.height + '" ' + fill.aspect + '" />' +

                        '</pattern>' +
                        '</defs>';

                    pathFill = 'url(#img' + fill.img.id + ')';
                    break;

                case 'gradient':
                    blobHtml += '<defs>';

                    if ('linear' === fill.gradType) {
                        var tagClose = '</linearGradient>';
                        blobHtml += '<linearGradient id="gradient' + blob.id + '" gradientUnits="objectBoundingBox"  gradientTransform="rotate(' + fill.pos + ')">';
                    } else {
                        var tagClose = '</radialGradient>'
                        blobHtml += '<radialGradient id="gradient' + blob.id + '" gradientUnits="objectBoundingBox" cx="' + fill.pos[0] + '%" cy="' + fill.pos[1] + '%">';
                    }

                    blobHtml += '<stop offset="' + fill.firstLoc + '%" stop-color="' + fill.firstColor + '" />' +
                        '<stop offset="' + fill.secLoc + '%" stop-color="' + fill.secColor + '" />';

                    blobHtml += tagClose + '</defs>';
                    pathFill = 'url(#gradient' + blob.id + ')';
                    break;

                default:
                    break;
            }

            blobHtml += '<path fill="' + pathFill + '" d="' + path + '"></path></svg>';

            return blobHtml;
        }

    };

    $(window).on('elementor:init', PremiumWidgetsEditor.init);

    window.PremiumWidgetsEditor = PremiumWidgetsEditor;

}(jQuery));
