(function ($) {
    var MoToastr = function () {

        this.toast = function (type, title, message, options) {
            options = options || {};
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "0",
                "extendedTimeOut": "0",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            $.extend(toastr.options, options);

            toastr[type](message, title);
        };
    };

    window.moToastr = new MoToastr().toast;

})(jQuery);