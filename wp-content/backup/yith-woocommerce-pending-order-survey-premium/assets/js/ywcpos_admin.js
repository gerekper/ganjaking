/**
 * Created by Your Inspiration on 13/01/2016.
 */

jQuery(document).ready( function($){
    "use strict";

    var pending_survey = $('.pending_order_surveys_list'),
        report_acc = $('#ywcpos_pending_order_survey_report-container'),
        update_pending_survey = function(){

           $('div.survey_question',pending_survey).each( function(index){

               var t = $(this),
                   order = t.find('.survey_order');
               order.val( index );
           });
        },
        close_tab = function(){
            $('.survey_question.closed').each(function(){

                $(this).find('.ywcpos_survey_content').hide();
            });
        },
        close_report_tab = function(){
            $('.single_report.closed').each(function(){

                $(this).find('.answers_container').hide();
            });
        };

    close_tab();
    close_report_tab();
    pending_survey.sortable({
        items: 'div.survey_question',
        cursor: 'move',
        scrollSensitivity: 40,
        forcePlaceholderSize: true,
        helper: 'clone',
        opacity: 0.80,
        revert: true,
        axis: 'y',
        handle:'h3',
        stop: function (event, ui) {
            update_pending_survey();
        }
    });

    $('.add_survey_question').on('click', function(e){

        e.preventDefault();
        var items_size = pending_survey.find('div.survey_question').length,
            data = {
                ywcpos_loop: items_size,
                action: ywcpos_params.actions.add_new_question
        };

        $.ajax({

            type: 'POST',
            url: ywcpos_params.admin_url,
            data: data,
            dataType: 'json',
            success: function (response) {

                pending_survey.append(response.result);
                close_tab();
            }

        });
    });

    pending_survey.on('click', '.survey_question h3', function(event){
        // If the user clicks on some form input inside the h3, like a select list (for variations), the box should not be toggled
        if ($(event.target).filter(':input, option').length) return;

        $(this).next('.ywcpos_survey_content').stop().slideToggle();
        $( this ).parent( '.survey_question' ).toggleClass( 'closed' ).toggleClass( 'open' );
    });

    report_acc.on('click', '.single_report h3', function(event){
        // If the user clicks on some form input inside the h3, like a select list (for variations), the box should not be toggled
        if ($(event.target).filter(':input, option').length) return;

        $(this).next('.answers_container').stop().slideToggle();
        $( this ).parent( '.single_report' ).toggleClass( 'closed' ).toggleClass( 'open' );
    });

    pending_survey.on('click', '.survey_question h3 .remove_question', function(e){
        e.preventDefault();
        var header= $(this).parent(),
            container = header.parent();
        setTimeout( function(){ container.remove();}, 500 );
        update_pending_survey();

    });
    
    pending_survey.on( 'blur change','.qst_txt',function(e){
    	var content = $(this).parents('.survey_question'),
    		label= content.find('h3 span.survey_qst');
    	
    	label.html( $(this).val() );
    });

    $('.ywcpos_send_email').on('click', function(e){

        e.preventDefault();

        var t = $(this),
            order_id = t.data('order_id'),
            select_template = t.prev('.ywcpos_template_email'),
            data = {
                ywcpos_order_id : order_id,
                ywcpos_template : select_template.val(),
                action: ywcpos_params.actions.send_pending_email
            };

        t.after('<img src="'+ywcpos_params.ajax_loader+'">');

        $.ajax({

            type: 'POST',
            url: ywcpos_params.admin_url,
            data: data,
            dataType: 'json',
            success: function (response) {

                t.next().remove();
            }

        });
    });

    //show more/less
    var max_char = report_acc.data('max_char'),
        show_more_txt = report_acc.data('show_more_txt'),
        show_less_txt = report_acc.data('show_less_txt');

    $('.ywcpos_more').each(function(){

        var content = $(this).html();
        if(content.length > max_char) {


            var c = content.substr(0, max_char);
            var h = content.substr(max_char, content.length - max_char);

            var html = c + '<span class="ywcpos_morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="ywcpos_morelink">' + show_more_txt + '</a></span>';

            $(this).html(html);
        }
    });

    $(".ywcpos_morelink").click(function(){
        if($(this).hasClass("less")) {
            $(this).removeClass("less");
            $(this).html(show_more_txt);
        } else {
            $(this).addClass("less");
            $(this).html(show_less_txt);
        }
        $(this).parent().prev().toggle('slow');
        $(this).prev().toggle();
        return false;
    });

});