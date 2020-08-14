/**
 * Created by Your Inspiration on 19/11/2015.
 */

jQuery(document).ready(function($){

var survey_in_single_product = $('.survey_after_before_add_to_cart .yith_surveys_container'),
    survey_after_summary     =$('.survey_after_product_summary'),
    survey_in_other_page = $('.yith_surveys_other_container');

    if( survey_after_summary.find('.surveys_list *').size()>0 )
            survey_after_summary.removeClass('survey_hide');

    var parent_other_survey = survey_in_other_page.parent();

    if( parent_other_survey.hasClass('widget_yith-wc-surveys') && survey_in_other_page.find('*').size()==0 )
        parent_other_survey.remove();

    $('.yith_send_answer').on( 'click', function(e){


        e.preventDefault();
        var t = $(this),
            content = t.parents('.surveys_list'),
            surveys = content.find('.yith_surveys_answers'),
            survey_ids = new Array(),
            answer_ids = new Array(),
            block_params = {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                },
                ignoreIfBlocked: true
            };


        surveys.each(function(){

            var id = $(this).attr('id'),
                ans_id = $(this).val();

            if( ans_id != '' ){

                id = id.split('-');
                id = id[1];
                survey_ids.push( id );
                answer_ids.push( ans_id );
            }
        });
        var i = 0;
        function send_all_votes( survey_id, answer_id ){

            var data = {

                yith_surveys_ids : survey_id,
                yith_answers_ids : answer_id,
                action: yith_survey_frontend_params.actions.save_surveys_voting
            };


           t.addClass( 'loading' );
            $.ajax({
                    type: 'POST',
                    url: yith_survey_frontend_params.ajax_url,
                    data: data,
                    dataType: 'json',
                    success: function( response ) {
                        if( i==survey_ids.length || typeof response.result=='undefined' || response.result == 'already_response' ){
                            t.removeClass( 'loading' );
                        }
                        if( response.result == 'true' ){

                            var current_survey_select = $('#yith_surveys_answers-'+survey_id),
                                container_survey_select = current_survey_select.parents('.yith_single_surveys_container');
                            container_survey_select.html( '<div class="yith_survey_thanks">'+yith_survey_frontend_params.messages.survey_thanks_voting+'</div>' );
                            i++;
                            send_all_votes( survey_ids[i], answer_ids[i] );
                        }
                        if( i== surveys.size() ) {
                            t.removeClass( 'loading' );
                            t.hide();
                        }

                    }
                });

        }
        if( survey_ids.length > 0 ){

           // content.block( block_params );

            send_all_votes( survey_ids[i], answer_ids[i] );
        }

    });

    $('.yith_send_single_answer').on('click', function(e){

        e.preventDefault();

        var t = $(this),
            content = t.parents('.yith_single_surveys_container'),
            survey_form = content.find('.yith_surveys_answers'),
            survey_id = survey_form.attr('id'),
            answ_id = survey_form.val();

        if( answ_id != '' ){

            survey_id = survey_id.split('-');
            survey_id = survey_id[1];

            var  block_params = {
                    message: null,
                    overlayCSS: {
                        background: '#000',
                        opacity: 0.6
                    },
                    ignoreIfBlocked: true
                    },
                 data = {
                    yith_surveys_ids : survey_id,
                    yith_answers_ids : answ_id,
                    action: yith_survey_frontend_params.actions.save_surveys_voting
                    };

            content.block( block_params );
            $.ajax({
                type: 'POST',
                url: yith_survey_frontend_params.ajax_url,
                data: data,
                dataType: 'json',
                success: function( response ) {

                    if( response.result == 'true'){

                        var current_survey_select = $('#yith_surveys_answers-'+survey_id),
                        container_survey_select = current_survey_select.parents('.yith_single_surveys_container');

                        container_survey_select.after( '<div class="yith_survey_thanks">'+yith_survey_frontend_params.messages.survey_thanks_voting+'</div>' );
                        if( yith_survey_frontend_params.hide_survey_after_answer == 'yes' )
                            container_survey_select.addClass('survey_hide');
                    }

                    content.unblock();
                    t.hide();

                }
            });


        }


    });

    $('body').on('init-survey-form', function(){

        //init_survey_form();
    }).trigger('init-survey-form');


});