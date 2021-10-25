
var currentID = null;

function clearCache(obj, transient, control) {

    jQuery.ajax({
        type: "POST",
        url: settings.ajaxurl,
        dataType: "JSON",
        data: {
            action: "clear_reviews_data",
            security: settings.nonce,
            transient: transient
        },
        success: function () {

            jQuery(obj).parents(".elementor-control-clear_cache").prevAll(".elementor-control-" + control).find("input").trigger("input");

        },
        error: function (err) {
            console.log(err);
        }
    });


}

function clearReviewsCache(obj) {

    if (!obj || !currentID) return;

    var targetControl = jQuery(obj).data("target"),
        transient = null;


    transient = 'papro_reviews_' + jQuery(obj).parents(".elementor-control-clear_cache").prevAll(".elementor-control-" + targetControl).find("input").val() + '_' + currentID;

    clearCache(obj, transient, targetControl);
}

function triggerActions() {
    elementor.channels.editor.on('section:activated', function (sectionName, elementorEditor) {
        currentID = elementorEditor.model.id;
    });
}


jQuery(window).on('elementor:init', triggerActions);