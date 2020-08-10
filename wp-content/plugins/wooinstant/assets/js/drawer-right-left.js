(function($) {
    'use strict';

    jQuery(document).ready(function() {

        //Nav Toggler
        $(document).on('click', '#wi-toggler, .added_to_cart', function(e) {
            e.preventDefault();

            var targetClass = $('#wi-toggler, .added_to_cart');

            if (targetClass.hasClass('open')) {
                targetClass.removeClass('open');
                $('.wi-container').removeClass('panel-open');
            } else {
                targetClass.addClass('open');
                $('.wi-container').addClass('panel-open');
            }
            //Update cart on Nav Toggle
            jQuery('[name="update_cart"]').trigger('click'); // Update Cart

        });

        //Collapse Nav if click on body
        $('html').on('click', function (e) {
            if (!$('#wi-toggler, .added_to_cart, .add_to_cart_button').is(e.target) && $('#wi-toggler, .added_to_cart, .add_to_cart_button').has(e.target).length === 0 && !$('.wi-inner').is(e.target) && $('.wi-inner').has(e.target).length === 0) {
                $('#wi-toggler, .added_to_cart').removeClass('open');
                $('.wi-container').removeClass('panel-open');
            }
        });

    });

})(jQuery);