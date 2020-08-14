jQuery(document).ready(function ($) {
    var id = -1;

    function render_recaptcha() {


        if( ! ywqa.recaptcha ){
            return;
        }

        if (ywqa.recaptcha_version == 'v2' && ywqa.recaptcha && jQuery('#ywqa-g-recaptcha:empty').length) {         //render the recaptcha v2

            if (typeof grecaptcha.render != 'undefined') {
                id = grecaptcha.render('ywqa-g-recaptcha', {
                    'sitekey': ywqa.recaptcha_sitekey
                });
            }
        }
        else{ //Load the recaptcha v3
            grecaptcha.ready(function() {
                grecaptcha.execute(ywqa.recaptcha_sitekey, {action: 'homepage'}).then(function(token) {
                    $('#ywqa-g-recaptcha').prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                });
            });
        }
    }

    if (ywqa.grecaptcha){
        grecaptcha.ready(function() {
            render_recaptcha();
        });
    }


    $(document).on('vc_js', function () {
        render_recaptcha();
    });

    $("div.woocommerce-tabs ul.tabs li.questions_tab").on("click", function (e) {
        e.preventDefault();

        show_question_list(false, false);
    });

    function show_question_list(only_answered, show_all) {
        var data = {
            'action'    : 'show_question_list',
            'product_id': $( "#ywqa-questions-and-answers-product-id" ).val(),
            'answered'  : only_answered,
            'show_all'  : show_all
        };

        var parent_element = $("#ywqa-questions-and-answers");

        if (!parent_element.length) {
            return;
        }

        parent_element.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywqa.ajax_url, data, function (response) {
            if (-1 == response.code) {
                top.location.replace(response.value);
            }
            else if (1 == response.code) {
                $("#ywqa-questions-and-answers").replaceWith(response.items);
            }

            render_recaptcha();

            if (show_all) {
                $("#show-all-questions").remove();
            }

            parent_element.unblock();
        });
    }

    if (ywqa.goto_questions_tab == 1) {
        $(document).find('.woocommerce-tabs ul.tabs li.questions_tab a')[0].click();
    }

    $(document).on("click", "a.vote-question", function (e) {
        e.preventDefault();

        var clicked_item = $(this);

        var data = {
            'action'         : 'vote_question',
            'discussion_id'  : $(this).attr('data-discussion-id'),
            'discussion_vote': $(this).attr('data-discussion-vote'),
            '_wpnonce'       : ywqa.nonce_value,
            'return_path'    : window.location.href
        };

        var parent_element = $(this).closest("div.question-votes");

        parent_element.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywqa.ajax_url, data, function (response) {
            if (-1 == response.code) {
                window.location.href = response.value;
            }
            else if (1 == response.code) {
                parent_element.children("span.question-votes-count").text(response.value);
            }

            parent_element.unblock();
        });
    });

    $(document).on("click", "a.answer-helpful, a.answer-not-helpful", function (e) {
        e.preventDefault();

        var clicked_item = $(this);

        var data = {
            'action'         : 'vote_answer',
            'discussion_id'  : $(this).attr('data-discussion-id'),
            'discussion_vote': $(this).attr('data-discussion-vote'),
            '_wpnonce'       : ywqa.nonce_value,
            'return_path'    : window.location.href
        };

        var parent_element = $(this).closest("div.answer-helpful");

        parent_element.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywqa.ajax_url, data, function (response) {
            if (-1 == response.code) {
                top.location.replace(response.value);
            }
            else if (1 == response.code) {
                parent_element.children("span.answer-stat-text").text(response.value);
            }

            parent_element.unblock();
        });
    });

    $(document).on("click", "a.report-answer-abuse", function (e) {
        e.preventDefault();
        var clicked_item = $(this);

        var data = {
            'action'         : 'report_answer_abuse',
            'discussion_id'  : $(this).attr('data-discussion-id'),
            'discussion_vote': 1,
            '_wpnonce'       : ywqa.nonce_value,
            'return_path'    : window.location.href
        };

        var parent_element = $(this).closest("div.answer-abuse");

        parent_element.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywqa.ajax_url, data, function (response) {
            if (-1 == response.code) {
                top.location.replace(response.value);
            }
            else if (1 == response.code) {
                parent_element.empty().html('<span class="thanks">' + ywqa.abuse_response + '</span>');
            }
            $(document).trigger('yith_ywqa_answer_helpfull',response);
            parent_element.unblock();
        });
    });

    $(document).on("click", "a.read-more", function (e) {
        e.preventDefault();

        var data = {
            'action'       : 'get_full_content',
            'discussion_id': $(this).attr('data-discussion-id')
        };

        var parent_element = $(this).closest("div.answer-content");

        parent_element.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywqa.ajax_url, data, function (response) {

            if (1 == response.code) {
                parent_element.children('.read-more').remove();
                text = response.value.replace(/(?:\r\n|\r|\n)/g, '<br />');
                parent_element.children('span.answer').empty().html(text);
            }

            parent_element.unblock();
        });
    });

    $(document).on("click", "a.goto-page", function (e) {

        e.preventDefault();
        if (!$(this).attr('data-ywqa-product-id').length) {
            return;
        }

        var callback = 'get_questions';
        var parent_element = $(this).closest("div#ywqa_question_list");

        if ($("ol.item-navigation.answers").length) {
            //  clicking on answer pagination item
            callback = 'get_answers';
            parent_element = $(this).closest("div#ywqa_answer_list");
        }

        var data = {
            'action'     : callback,
            'product_id' : $(this).attr('data-ywqa-product-id'),
            'order'      : $(this).attr('data-ywqa-order'),
            'question_id': $(this).attr('data-ywqa-question-id'),
            'page'       : $(this).attr('data-ywqa-page')
        };

        parent_element.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywqa.ajax_url, data, function (response) {

            if (1 == response.code) {
                parent_element.find('ol.ywqa-items-list').empty().append(response.items);
                parent_element.find('ol.item-navigation').empty().append(response.pagination);
            }

            parent_element.unblock();
        });
    });

    $(document).on("click", "input#ywqa-submit-question", function (e) {
        e.preventDefault();

        $("span.operation-error").remove();
        $("span.operation-completed").remove();

        try {
            /**
             * Check for reCaptcha token
             */
            if (ywqa.recaptcha && ywqa.recaptcha_version == 'v2') {
                if ((grecaptcha == null) || (!grecaptcha.getResponse(id))) {
                    $("div.ywqa-ask-question").prepend("<span class='operation-error'>" + ywqa.recaptcha_not_valid + "</span>");
                    return;
                }
            }
        }
        catch (e) {
            $("div.ywqa-ask-question").prepend("<span class='operation-error'>" + ywqa.recaptcha_not_valid + "</span>");
            return;
        }

        if (!is_valid_content()) {
            return;
        }


       if (ywqa.recaptcha_version == 'v2'){
           var recaptcha_object = $('div#ywqa-g-recaptcha TextArea[name="g-recaptcha-response"]').val();
       }
        else{
           var recaptcha_object = $('div#ywqa-g-recaptcha input[name="g-recaptcha-response"]').val();
       }


        var data = {
            'action'    : 'submit_question',
            'text'      : $("#ywqa_user_content").val(),
            'subscribe' : $("#ywqa-notify-user").length ? $("#ywqa-notify-user").prop("checked") : 0,
            'product_id': $("#ywqa-questions-and-answers").data("product-id"),
            'recaptcha' : recaptcha_object
        };

        if ($("#ywqa-guest-name").length) {
            data['name'] = $("input[name='ywqa-guest-name']").val();
        }
        if ($("#ywqa-guest-email").length) {
            data['email'] = $("input[name='ywqa-guest-email']").val();
        }

        var parent_element = $("div.ywqa-ask-question");

        parent_element.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywqa.ajax_url, data, function (response) {

            if (1 == response.code) {
                $("#ywqa-questions-and-answers").replaceWith(response.items);
                render_recaptcha();

                if (1 == response.waiting_approval) {
                    $("div#submit_answer").append("<span class='operation-completed'>" + response.message + "</span>");
                }
            }
            else if (-1 == response.code) {
                $("div#ask_question").empty().append("<span class='operation-completed'>" + response.message + "</span>");
            }
            else {
                $("div#ask_question").append("<span class='operation-error'>" + ywqa.discussion_error + "</span>");
            }

            parent_element.unblock();
        });
    });

    function show_error_message(selector, error_message) {
        $("<span class='operation-error'>" + error_message + "</span>").insertAfter($(selector));
    }

    function is_valid_content(content_id) {

        var $text = $("#ywqa_user_content");
        var $name = $('input[name="ywqa-guest-name"]');
        var $email = $('input[name="ywqa-guest-email"]');

        var error_status = 0;
        //  Check if the question content is not empty
        if ($text.val().length == 0) {
            show_error_message($text, ywqa.content_is_empty);
            error_status = 1;
        }

        // Checks if a mandatory user name is filled, if the field exists
        if ($name.length) {
            if (ywqa.mandatory_guest_data && !$name.val().length) {
                show_error_message($name, ywqa.guest_name_message);
                error_status = 1;
            }
        }

        // Checks for mandatory e-mail not submitted, if the field exists
        if ($email.length) {
            if (ywqa.mandatory_guest_data && !$email.val().length) {
                show_error_message($email, ywqa.guest_email_message);
                error_status = 1;
            }
            else if ($email.val().length) {
                // Checks for e-mail submitted in wrong format
                var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,5}$/i;

                if (!testEmail.test($email.val())) {
                    show_error_message($email, ywqa.guest_email_message);
                    error_status = 1;
                }
            }
        }
        return error_status == 0;
    }

    function validate_field(selector, error_message, reg_expr, is_mandatory) {

        if (is_mandatory && $(selector).val().length == 0) {

            show_error_message(selector, error_message);
            return false;
        }

        if (typeof(reg_expr) !== 'undefined') {
            if (!reg_expr.test($(selector).val())) {
                show_error_message(selector, error_message);
                return false;
            }
        }

        return true;
    }

    $(document).on("click", "input#ywqa-submit-answer", function (e) {
        e.preventDefault();

        $("span.operation-error").remove();
        $("span.operation-completed").remove();

        try {
            /**
             * Check for reCaptcha token
             */
            if (ywqa.recaptcha && ywqa.recaptcha_version == 'v2') {
                if ((grecaptcha == null) || (!grecaptcha.getResponse(id))) {
                    $("div#submit_answer").prepend("<span class='operation-error'>" + ywqa.recaptcha_not_valid + "</span>");
                    return;
                }
            }
        }
        catch (e) {
            $("div#submit_answer").prepend("<span class='operation-error'>" + ywqa.recaptcha_not_valid + "</span>");
            return;
        }

        if (!is_valid_content()) {
            return;
        }

        if (ywqa.recaptcha_version == 'v2'){
            var recaptcha_object = $('div#ywqa-g-recaptcha TextArea[name="g-recaptcha-response"]').val();
        }
        else{
            var recaptcha_object = $('div#ywqa-g-recaptcha input[name="g-recaptcha-response"]').val();
        }

        var data = {
            'action'        : 'submit_answer',
            'answer_content': $("#ywqa_user_content").val(),
            'product_id'    : $("#ywqa-questions-and-answers").data("product-id"),
            'question_id'   : $("input[name='ywqa_question_id']").val(),
            'recaptcha'     : recaptcha_object
        };

        if ($("#ywqa-guest-name").length) {
            data['name'] = $("input[name='ywqa-guest-name']").val();
        }
        if ($("#ywqa-guest-email").length) {
            data['email'] = $("input[name='ywqa-guest-email']").val();
        }
        var parent_element = $("div.ywqa-send-answer");

        parent_element.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywqa.ajax_url, data, function (response) {

            if (1 == response.code) {
                $("#ywqa-questions-and-answers").replaceWith(response.items);

                if (1 == response.waiting_approval) {
                    $("div#submit_answer").append("<span class='operation-completed'>" + response.message + "</span>");
                }
            }
            else if (-1 == response.code) {
                $("div#submit_answer").append("<span class='operation-error'>" + response.message + "</span>");
            }
            else {
                $("div#submit_answer").append("<span class='operation-error'>" + ywqa.discussion_error + "</span>");
            }

            render_recaptcha();

            parent_element.unblock();
        });
    });

    function show_question(question_id, order) {

        var data = {
            'action'       : 'goto_question',
            'discussion_id': question_id,
            'order'        : typeof order !== 'undefined' ? order : 'default'
        };

        var parent_element = $("#ywqa-questions-and-answers");


        parent_element.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywqa.ajax_url, data, function (response) {

            if (-1 == response.code) {
                top.location.replace(response.value);
            }
            else if (1 == response.code) {
                $("#ywqa-questions-and-answers").replaceWith(response.items);
            }

            render_recaptcha();

            parent_element.unblock();

            $([document.documentElement, document.body]).animate({
                scrollTop: $('.woocommerce-tabs').offset().top
            }, 1000);
        });
    }

    $(document).on("click", "a.ywqa-question-order", function (e) {
        e.preventDefault();

        var discussion_id = $("input[name='ywqa_question_id']").val();
        var order = $(this).data("order");

        show_question(discussion_id, order);
    });

    $(document).on("click", "a.goto-question", function (e) {
        e.preventDefault();

        var discussion_id = $(this).attr('data-discussion-id');

        show_question(discussion_id);
    });

    $(document).on("click", "a.back-to-product", function (e) {
        e.preventDefault();

        show_question_list(false, false);
    });

    $(document).on("input", 'input[name="ywqa-search-text"]', function (e) {
        e.preventDefault();

        var data = {
            'action'    : 'filter_content',
            'product_id': $( "#ywqa-questions-and-answers-product-id" ).val(),
            'text'      : $(this).val()
        };


        var content_element = $(this).closest('.ywqa-container').find('.ywqa-content');






        content_element.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywqa.ajax_url,
            data,
            function (response) {
                if (1 == response.code) {
                    content_element.replaceWith(response.items);
                }

                content_element.unblock();
            });
    });

    $(document).on("click", "a.show-questions", function (e) {
        e.preventDefault();

        show_question_list(false, true);
    });
});