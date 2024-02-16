var EaelParallaxHandler = function($scope, $) {
    var target = $scope,
        sectionId = target.data("id"),
        settings = false,
        editMode = elementorFrontend.isEditMode();

    if (editMode) {
        settings = generateEditorSettings(sectionId);
    }

    if (!editMode || !settings) {
        return false;
    }

    if (settings[0] == "yes") {
        if ("multi" != settings[1] && "automove" != settings[1]) {
            generateJarallax();
        } else if ("automove" == settings[1]) {
            generateAutoMoveBackground();
        } else {
            generateMultiLayers();
        }
    }

    function generateEditorSettings(targetId) {
        var editorElements = null,
            sectionData = {},
            sectionMultiData = {},
            settings = [];

        if (!window.elementor.hasOwnProperty("elements")) {
            return false;
        }

        editorElements = window.elementor.elements;

        if (!editorElements.models) {
            return false;
        }

        $.each(editorElements.models, function(index, elem) {
            if (targetId == elem.id) {
                sectionData = elem.attributes.settings.attributes;
            } else if (
                elem.id == target.closest(".elementor-top-section").data("id")
            ) {
                $.each(elem.attributes.elements.models, function(index, col) {
                    $.each(col.attributes.elements.models, function(
                        index,
                        subSec
                    ) {
                        sectionData = subSec.attributes.settings.attributes;
                    });
                });
            }
        });

        if (!sectionData.hasOwnProperty("eael_parallax_type")) {
            return false;
        }

        if ("" == sectionData["eael_parallax_type"]) {
            return false;
        }

        if (
            "multi" != sectionData["eael_parallax_type"] &&
            "automove" != sectionData["eael_parallax_type"]
        ) {
            settings.push(sectionData["eael_parallax_switcher"]);
            settings.push(sectionData["eael_parallax_type"]);
            settings.push(sectionData["eael_parallax_speed"]);
            settings.push(
                "yes" == sectionData["eael_parallax_android_support"] ? 0 : 1
            );
            settings.push(
                "yes" == sectionData["eael_parallax_ios_support"] ? 0 : 1
            );
            settings.push(sectionData["eael_parallax_background_size"]);
            settings.push(sectionData["eael_parallax_background_pos"]);
        } else if ("automove" == sectionData["eael_parallax_type"]) {
            settings.push(sectionData["eael_parallax_switcher"]);
            settings.push(sectionData["eael_parallax_type"]);
            settings.push(sectionData["eael_auto_speed"]);
            settings.push(sectionData["eael_parallax_auto_type"]);
        } else {
            if (!sectionData.hasOwnProperty("eael_parallax_layers_list")) {
                return false;
            }

            sectionMultiData = sectionData["eael_parallax_layers_list"].models;

            if (0 == sectionMultiData.length) {
                return false;
            }
            settings.push(sectionData["eael_parallax_switcher"]);
            settings.push(sectionData["eael_parallax_type"]);
            settings.push(
                "yes" == sectionData["eael_parallax_layer_invert"] ? 1 : 0
            );
            $.each(sectionMultiData, function(index, obj) {
                settings.push(obj.attributes);
            });
        }

        if (0 !== settings.length) {
            return settings;
        }

        return false;
    }

    function responsiveParallax(android, ios) {
        switch (true || 1) {
            case android && ios:
                return /iPad|iPhone|iPod|Android/;
                break;
            case android && !ios:
                return /Android/;
                break;
            case !android && ios:
                return /iPad|iPhone|iPod/;
                break;
            case !android && !ios:
                return null;
        }
    }

    function generateJarallax() {
        setTimeout(function() {
            target.jarallax({
                type: settings[1],
                speed: settings[2],
                disableParallax: responsiveParallax(
                    1 == settings[3],
                    1 == settings[4]
                ),
                keepImg: true
            });
        }, 500);
    }

    function generateAutoMoveBackground() {
        var speed = parseInt(settings[2]);
        target.css("background-position", "0px 0px");
        if (settings[3] == 11) {
            var position = parseInt(target.css("background-position-x"));
            setInterval(function() {
                position = position + speed;
                target.css("backgroundPosition", position + "px 0");
            }, 70);
        } else if (settings[3] == "right") {
            var position = parseInt(target.css("background-position-x"));
            setInterval(function() {
                position = position - speed;
                target.css("backgroundPosition", position + "px 0");
            }, 70);
        } else if (settings[3] == "top") {
            var position = parseInt(target.css("background-position-y"));
            setInterval(function() {
                position = position + speed;
                target.css("backgroundPosition", "0 " + position + "px");
            }, 70);
        } else if (settings[3] == "bottom") {
            var position = parseInt(target.css("background-position-y"));
            setInterval(function() {
                position = position - speed;
                target.css("backgroundPosition", "0 " + position + "px");
            }, 70);
        }
    }

    function generateMultiLayers() {
        var counter = 0,
            mouseParallax = "",
            mouseRate = "";
        $.each(settings, function(index, layout) {
            if (2 < index) {
                if (
                    null != layout["eael_parallax_layer_image"]["url"] &&
                    "" != layout["eael_parallax_layer_image"]["url"]
                ) {
                    if (
                        "yes" == layout["eael_parallax_layer_mouse"] &&
                        "" != layout["eael_parallax_layer_rate"]
                    ) {
                        mouseParallax = ' data-parallax="true" ';
                        mouseRate =
                            ' data-rate="' +
                            layout["eael_parallax_layer_rate"] +
                            '" ';
                    } else {
                        mouseParallax = ' data-parallax="false" ';
                    }
                    var backgroundImage =
                            layout["eael_parallax_layer_image"]["url"],
                        $html = $(
                            '<div id="eael-parallax-layer-' +
                                counter +
                                '"' +
                                mouseParallax +
                                mouseRate +
                                ' class="eael-parallax-layer"></div>'
                        )
                            .prependTo(target)
                            .css({
                                "z-index":
                                    layout["eael_parallax_layer_z_index"],
                                "background-image":
                                    "url(" + backgroundImage + ")",
                                "background-size":
                                    layout["eael_parallax_layer_back_size"],
                                "background-position-x":
                                    layout["eael_parallax_layer_hor_pos"] + "%",
                                "background-position-y":
                                    layout["eael_parallax_layer_ver_pos"] + "%"
                            });

                    counter++;
                }
            }
        });
        target.mousemove(function(e) {
            $(this)
                .find('.eael-parallax-layer[data-parallax="true"]')
                .each(function(index, element) {
                    $(this).parallax($(this).data("rate"), e);
                });
        });
    }
};

jQuery(window).on("elementor/frontend/init", function() {
    elementorFrontend.hooks.addAction(
        "frontend/element_ready/section",
        EaelParallaxHandler
    );
    elementorFrontend.hooks.addAction(
        "frontend/element_ready/container",
        EaelParallaxHandler
    );
});
