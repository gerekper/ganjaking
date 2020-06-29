/**
 * Created by Your Inspiration on 12/11/2015.
 */
jQuery(document).ready(function($){

    var surveys,
        answers,
        block_params = {
            message: null,
            overlayCSS: {
                background: '#000',
                opacity: 0.6
            },
            ignoreIfBlocked: true
        };

    var init_surveys = function() {

        surveys = $('.suverys_answers');
        answers = surveys.find('li').get();
        answers.sort(function (a, b) {
            var compA = parseInt($(a).attr('rel'));
            var compB = parseInt($(b).attr('rel'));
            return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
        });

        $(answers).each(function (idx, itm) {
            surveys.append(itm);
        });
        //ordering
        surveys.sortable({
            items: 'li',
            cursor: 'move',
            scrollSensitivity: 40,
            forcePlaceholderSize: true,
            helper: 'clone',
            opacity: 0.80,
            revert: true,
            axis: 'y',
            handle: 'span.survey_handle',
            stop: function (event, ui) {
            }
        });

        $('.surveys_error').hide();
    };

    $(document).on('init-surveys', function(){

        init_surveys();
    }).trigger('init-surveys');


    $('.add_answer').on('click', function(e){

        e.preventDefault();
        var items_size = answers.length;


            var data = {
                ywcsur_loop: items_size,
                action: yith_survey_params.actions.add_new_survey_answer
            };

            $.ajax({

                type: 'POST',
                url: yith_survey_params.ajax_url,
                data: data,
                dataType: 'json',
                success: function (response) {
                    surveys.append(response.result);
                    $('body').trigger('init-surveys');
                }

            });
    });

    $(document).on( 'click','.remove_answer',function(e){

        e.preventDefault();

        var li = $(this).parent( 'li.surveys_answer'),
            answer_id = li.find('.yith_survey_answer_post_id').val();

       li.block( block_params );

        if( answer_id==-1 ){

            setTimeout( function(){  li.unblock(); li.remove();}, 500 );
            $('body').trigger('init-surveys') ;

        }else {
            var data = {
                ywcsur_answer_id: answer_id,
                action: yith_survey_params.actions.remove_survey_answer
            };

            $.ajax({

                type: 'POST',
                url: yith_survey_params.ajax_url,
                data: data,
                dataType: 'json',
                success: function (response) {

                    li.unblock();
                    li.remove();
                    $('body').trigger('init-surveys');
                }

            });
        }

    });

    $('.survey_report_view_order_details').on('click',function(e){
        e.preventDefault();

        var t = $(this),
            ul_orders = t.next('.survey_report_show_details');

        ul_orders.toggleClass('show_details');
    });

});