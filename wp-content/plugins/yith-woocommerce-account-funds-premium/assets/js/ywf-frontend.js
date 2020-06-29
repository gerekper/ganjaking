/**
 * Created by Your Inspiration on 06/04/2016.
 */
jQuery(document).ready(function ($) {

    $('.ywf_message_amount.ywf_show').on('click','a.ywf_show_form',function(e){
        e.preventDefault();

        $('.ywf_message_amount').removeClass('ywf_show');
        $('.ywf_amount_input_container').slideDown().removeClass('ywf_hide');
    });



});