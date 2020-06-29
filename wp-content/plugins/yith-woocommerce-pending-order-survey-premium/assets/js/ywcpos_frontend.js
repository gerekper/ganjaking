/**
 * Created by Your Inspiration on 13/01/2016.
 */

jQuery(document).ready(function ($) {
    "use strict";

    var form = $('form.pending_survey_form'),
        block_params = {
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            },
            ignoreIfBlocked: true
        },
        close_modal_button = {};

 /*   close_modal_button[ ywcpos_params.close_dialog_txt ] = function(){
        $(this).dialog('close');
    };

    $('#survey_thankyou_content').dialog({
        autoOpen: false,
        modal:true,
        draggable:false,
        show: {
            effect: "blind",
            duration: 1000
        },
        hide: {
            effect: "explode",
            duration: 1000
        },
       buttons: close_modal_button

    });*/

    var validate_form_fields = function (field) {

       var  field_container = field.parent(),
            parent = field_container.parent(),
            valid_form = true;

        if (parent.hasClass('validate-require')) {

            if (field.is('input')) {
                //to do
            }
            else if (field.is('select')) {

                //to do
            }
            else if (field.is('textarea')) {

                var value = field.val();

                valid_form = value != '';

                field_container.addClass('validate_is_required');
            }
        }

        if(valid_form)
            field_container.removeClass('validate_is_required');

        return valid_form;
    },
        get_value_form_field = function( field ){

            var value = '';

            if (field.is('input')) {
                //to do
            }
            else if (field.is('select')) {

                //to do
            }
            else if (field.is('textarea')) {

                 value = field.val();
            }

            return value;
        },
        get_survey_title = function( field ){

            var survey_container = field.parent().parent(),
                title = survey_container.find('h3').html();

            return title;
        },
        getParameterByName = function(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    };

    $('.ywcpos_sendsurvey').on('click', function (e) {

        e.preventDefault();
        var valid_form = true,
            answers = { 'answers': [] };

        form.find('.pending_field').each(function () {

            var field = $(this),
            survey_name = get_survey_title( field ),
            survey_answer = get_value_form_field( field );

            valid_form = valid_form && validate_form_fields( field ),

            answers.answers.push(
                { survey_name  : survey_name,
                  survey_answer : survey_answer
                }
            );
        });

        if (valid_form) {

            answers['action'] = ywcpos_params.actions.validate_survey;
            answers['email_id'] = getParameterByName('email_id' );
            answers['order_id'] = getParameterByName( 'order_id' );
            answers['survey_id'] = $('.survey_id').val();

            form.block( block_params );
            $.ajax({

                type:'POST',
                url: ywcpos_params.admin_url,
                data: answers,
                dataType: 'json',
                success: function (response) {
                    form.unblock();
                    $('.pending_survey_form').find('.ywcpos_sendsurvey').remove();
                    $('#survey_thankyou_content').show();
                }
            })
        }

        $('html, body').animate({scrollTop : 0},800);

    });

    $('input,select,textarea').on('blur change', function (e) {

        validate_form_fields( $( this ) );
    });
});