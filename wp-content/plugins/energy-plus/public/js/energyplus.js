(function($) {
    "use strict";

    function EnergyPlus() {

        var self = this;

        self.$window = $(window);
        self.$document = $(document);

        // Let's Start
        self.init();
    }

    EnergyPlus.prototype = {

        /*
         *	Initialize
         */

        init: function() {
            var self = this;

            self.pulse();
        },

        extensions: function(fn) {
            $.EnergyPlus[fn]();
        },

        changeUrl: function(page) {
            window.history.pushState("", "", page);
        },

        pulse: function() {

          $.ajax({
            type: 'POST',
            url: EnergyPlus_vars.ajax_url,
            data: {
              action: 'energyplus_pulse',
              t: EnergyPlus_vars.energyplus_t,
              i: EnergyPlus_vars.energyplus_i
            },
            dataType: 'json',
            cache: false,
            headers: {
              'cache-control': 'no-cache'
            }
          });
        }
    };

    $.EnergyPlus = EnergyPlus.prototype;

    $(document).ready(function() {
        new EnergyPlus();
    });


})(jQuery);
