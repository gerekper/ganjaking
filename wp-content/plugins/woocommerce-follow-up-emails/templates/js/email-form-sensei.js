var init_sensei_search;

jQuery( function ( $ ) {
    jQuery("body").bind("fue_email_type_changed", function(evt, type) {
        sensei_toggle_fields( type );
    });

    jQuery("body").bind("updated_email_details", function() {
        sensei_toggle_interval_type_fields( $("#interval_type").val() );
    });

    jQuery("body").on("change", "#interval_type", function() {
        sensei_toggle_interval_type_fields( $(this).val() );
    });

    $("#fue-email-details").on("change", "#course_id", function() {
        $("#fue-email-details select.condition").trigger("change");
    });

    // conditions
    $("#fue-email-details").on('change', 'select.condition', function() {
        var conditions_with_courses = [
            'have_not_started_first_lesson', 'have_not_completed_a_lesson', 'have_not_completed_a_course'
        ];
        var conditions_with_lessons = [
            'have_not_taken_quiz', 'have_failed_quiz', 'have_passed_quiz'
        ];

        var condition = $(this).val();

        if ($.inArray( condition, conditions_with_courses ) > -1 ) {
            $(this).parents('fieldset').find('.value-courses').show();
            $(this).parents('fieldset').find('.value-courses :input').removeAttr('disabled');

            $("div.value-courses:visible .ajax-select2-init")
                .removeClass('ajax-select2-init')
                .addClass('ajax_select2_courses');

            if ( $("#course_id").val() != "" ) {
                $(this).parents('fieldset').find('.value-courses .ajax_select2_courses').each(function() {
                    if ( !$(this).is(":visible") ) {
                        return;
                    }

                    var data = $("#course_id").select2("data");
                    var el_id = $(this).attr("id").replace('s2id_', '');

                    $("#"+ el_id).select2("data", data);
                });
                $(this).parents('fieldset').find('.value-courses :input').attr('readonly', true);
            } else {
                $(this).parents('fieldset').find('.value-courses :input').removeAttr('readonly');
            }
        } else {
            $(this).parents('fieldset').find('.value-courses').hide();
            $(this).parents('fieldset').find('.value-courses :input').attr('disabled', true);
        }

        if ($.inArray( condition, conditions_with_lessons ) > -1 ) {
            $(this).parents('fieldset').find('.value-lessons').show();
            $(this).parents('fieldset').find('.value-lessons :input').removeAttr('disabled');

            $("div.value-lessons:visible .ajax-select2-init")
                .removeClass('ajax-select2-init')
                .addClass('ajax_select2_lessons');

            if ( $("#course_id").val() != "" ) {

                $(this).parents('fieldset').find('.value-lessons .ajax_select2_lessons').each(function() {
                    var data = $("#course_id").select2("data");
                    var el_id = $(this).attr("id").replace('s2id_', '');

                    $("#"+ el_id).data("filter", '{"course_id": '+ $("#course_id").val() +'}');
                });
            } else {
                $(this).parents('fieldset').find('.value-lessons .ajax_select2_lessons').each(function() {
                    var el_id = $(this).attr("id").replace('s2id_', '');
                    $("#"+ el_id).data("filter", "");
                });
            }
        } else {
            $(this).parents('fieldset').find('.value-lessons').hide();
            $(this).parents('fieldset').find('.value-lessons :input').attr('disabled', true);
        }

        init_select2_fields();

    } );

    // Conditions
    $("#fue-email-details").on('click', '.btn-add-condition', function(e) {
        e.preventDefault();

        $("div.value-courses:visible .ajax-select2-init")
            .removeClass('ajax-select2-init')
            .addClass('ajax_select2_courses');

        $("div.value-quizzes:visible .ajax-select2-init")
            .removeClass('ajax-select2-init')
            .addClass('ajax_select2_lessons');

        init_select2_fields();

        $("select.condition").trigger("change");
    });

    // enable visible input fields
    $('body').bind('updated_email_details', function() {
        init_sensei_search();
        $("#trigger_conditions :input:visible").removeAttr("disabled");

        $(".select2-init:visible")
            .addClass('select2')
            .removeClass('select2-init');

        $("div.value-courses:visible .ajax-select2-init")
            .removeClass('ajax-select2-init')
            .addClass('ajax_select2_courses');

        $("div.value-quizzes:visible .ajax-select2-init")
            .removeClass('ajax-select2-init')
            .addClass('ajax_select2_lessons');

        $("select.condition").trigger("change");

        init_select2_fields();
    });
} );

function sensei_toggle_interval_type_fields( type ) {
    var show = [];
    var hide = ['.sensei-courses', '.sensei-lessons', '.sensei-quizzes', '.sensei-answers'];

    switch (type) {
        case 'course_signup':
        case 'course_completed':
        case 'lesson_added':
            show = ['.sensei-courses'];
            break;

        case 'lesson_start':
        case 'lesson_signup':
        case 'lesson_completed':
            show = ['.sensei-lessons'];
            break;

        case 'quiz_completed':
        case 'quiz_failed':
        case 'quiz_passed':
            show = ['.sensei-quizzes'];
            break;

        case 'specific_answer':
            show = ['.sensei-answers'];
            break;
    }

    for (x = 0; x < hide.length; x++) {
        jQuery(hide[x]).hide();
    }

    for (x = 0; x < show.length; x++) {
        jQuery(show[x]).show();
    }

}

function sensei_toggle_fields( type ) {
    if (type == "sensei") {
        var val  = jQuery("#interval_type").val();
        var show = ['#fue-email-sensei', '.sensei', '.var_sensei'];
        var hide = ['.interval_type_option', '.always_send_tr', '.signup_description', '.product_description_tr', '.product_tr', '.category_tr', '.use_custom_field_tr', '.custom_field_tr', '.var_item_name', '.var_item_category', '.var_item_names', '.var_item_categories', '.var_item_name', '.var_item_category', '.interval_type_after_last_purchase', '.interval_duration_date', '.var_customer', '.var_order'];

        for (x = 0; x < hide.length; x++) {
            jQuery(hide[x]).hide();
        }

        for (x = 0; x < show.length; x++) {
            jQuery(show[x]).show();
        }

        jQuery("option.interval_duration_date").attr("disabled", true);

        jQuery(".interval_duration_date").hide();
    } else {
        var hide = ['#fue-email-sensei', '.course_tr', '.sensei', '.var_sensei'];

        for (x = 0; x < hide.length; x++) {
            jQuery(hide[x]).hide();
        }
    }
}

jQuery(document).ready(function( $ ) {
    sensei_toggle_fields( jQuery("#email_type").val() );

});

(function($) {
    init_sensei_search = function() {
        $(":input.sensei-search").filter(":not(.enhanced)").each( function() {
            var select2_args = {
                allowClear:  true,
                placeholder: jQuery( this ).data( 'placeholder' ),
                dropdownAutoWidth: 'true',
                minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : '3',
                escapeMarkup: function( m ) {
                    return m;
                },
                ajax: {
                    url:         ajaxurl,
                    dataType:    'json',
                    quietMillis: 250,
                    data: function( term, page ) {
                        return {
                            term:     term,
                            action:   jQuery( this ).data( 'action' ),
                            security: jQuery( this ).data( 'nonce' )
                        };
                    },
                    results: function( data, page ) {
                        var terms = [];
                        if ( data ) {
                            jQuery.each( data, function( id, text ) {
                                terms.push( { id: id, text: text } );
                            });
                        }
                        return { results: terms };
                    },
                    cache: true
                }
            };

            if ( jQuery( this ).data( 'multiple' ) === true ) {
                select2_args.multiple = true;
                select2_args.initSelection = function( element, callback ) {
                    var data     = jQuery.parseJSON( element.attr( 'data-selected' ) );
                    var selected = [];

                    jQuery( element.val().split( "," ) ).each( function( i, val ) {
                        selected.push( { id: val, text: data[ val ] } );
                    });
                    return callback( selected );
                };
                select2_args.formatSelection = function( data ) {
                    return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
                };
            } else {
                select2_args.multiple = false;
                select2_args.initSelection = function( element, callback ) {
                    var data = {id: element.val(), text: element.attr( 'data-selected' )};
                    return callback( data );
                };
            }


            jQuery(this).select2(select2_args).addClass( 'enhanced' );
        } );
    }
}(jQuery));