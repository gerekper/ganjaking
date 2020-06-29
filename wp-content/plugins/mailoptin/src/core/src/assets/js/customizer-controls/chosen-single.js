wp.customize.controlConstructor.mailoptin_single_chosen = wp.customize.Control.extend({
    ready: function () {
        var control = this;
        wp.customize.Control.prototype.ready.call(control);

        jQuery('.mailoptin-single-chosen').each(function () {
            var options = jQuery(this).data('chosen-attr');
            jQuery(this).chosen(options);
        });
    }
});