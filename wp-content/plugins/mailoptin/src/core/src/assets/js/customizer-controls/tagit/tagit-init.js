wp.customize.controlConstructor.mailoptin_tagit = wp.customize.Control.extend({
    ready: function () {
        var control = this;

        var save_changes_handler = function (id) {
            control.setting.set(jQuery("#" + id).tagit('assignedTags'));
        };

        jQuery('[data-block-type="tagit"]').each(function () {
            var id = jQuery(this).attr('id');
            var options = jQuery(this).data('tagit-options');

            options.beforeTagAdded = function (event, ui) {

                if (ui.duringInitialization === true) return;

                if (ui.tagLabel.indexOf('http://') !== -1 || ui.tagLabel.indexOf('https://') !== -1 || ui.tagLabel.indexOf('www.') !== -1) {
                    return false;
                }
            };

            options.afterTagAdded = function (event, ui) {

                // duringInitialization is a boolean indicating whether the tag was added during the initial construction of this widget.
                // e.g. when initializing tag-it on an input with preloaded data.
                // You can use this to tell whether the event was initiated by the user or by the widget's initialization.
                if (ui.duringInitialization === true) return;

                val = ui.tagLabel.replace('https://', '').replace('http://', '').replace('www.', '');

                save_changes_handler(id)
            };

            options.afterTagRemoved = function () {
                save_changes_handler(id)
            };

            jQuery("#" + id).tagit(options);

        });
    }
});