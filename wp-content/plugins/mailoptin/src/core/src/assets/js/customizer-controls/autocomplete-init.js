wp.customize.controlConstructor['mailoptin_autocomplete'] = wp.customize.Control.extend({
    ready: function () {
        var control = this;

        jQuery('[data-block-type="autocomplete"]').each(function () {
            var id = jQuery(this).attr('id');
            var options = jQuery(this).data('autocomplete-options');

            var availableTags = options.availableTags;

            function split(val) {
                return val.split(/,\s*/);
            }

            function extractLast(term) {
                return split(term).pop();
            }

            jQuery("#" + id)
            // don't navigate away from the field on tab when selecting an item
                .on("keydown", function (event) {
                    if (event.keyCode === jQuery.ui.keyCode.TAB &&
                        jQuery(this).autocomplete("instance").menu.active) {
                        event.preventDefault();
                    }
                })
                .autocomplete({
                    minLength: 0,
                    source: function (request, response) {
                        // delegate back to autocomplete, but extract the last term
                        response(jQuery.ui.autocomplete.filter(
                            availableTags, extractLast(request.term)));
                    },
                    focus: function () {
                        // prevent value inserted on focus
                        return false;
                    },
                    select: function (event, ui) {
                        var terms = split(this.value);
                        // remove the current input
                        terms.pop();
                        // add the selected item
                        terms.push(ui.item.value);
                        // add placeholder to get the comma-and-space at the end
                        terms.push("");
                        this.value = terms.join(", ");
                        return false;
                    }
                });
        });
    }
});