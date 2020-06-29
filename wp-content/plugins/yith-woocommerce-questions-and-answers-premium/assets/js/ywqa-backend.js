jQuery(document).ready(function ($) {

    function get_editor_text(element) {

        if ($("#wp-" + element + "-wrap").hasClass("tmce-active")) {
            return tinyMCE.get(element).getContent();
        } else {
            return $('#' + element).val();
        }
    }

    $(document).on("click", "input#publish", function (e) {
        if (0 == $("input#title").val().length) {
            e.preventDefault();
            alert("Please fill in a title");
            return false;
        }

        if ("-1" === $("#select_product").val()) {
            e.preventDefault();
            alert("Please select a product");
            return false;
        }
    });

    $(document).on('click', "input#submit-answer", (function (e) {
        e.preventDefault();

        $("#question-content-div .error").empty();
        $("#question-content-div .success").empty();

        //var answer_text = tinyMCE.get("respond-to-question").getContent();
        var answer_text = get_editor_text("respond-to-question");

        if (answer_text.length == 0) {
            $("#wp-respond-to-question-wrap").after('<span class="error">' + ywqa.empty_answer + '</span>');
            return false;
        }

        var data = {
            'action': 'admin_respond_to_question',
            'question_id': $("#post_ID").val(),
            'answer_content': answer_text,
            'product_id': $("input#product_id").val(),
            'answered_backend': '1',
        };

        var parent_element = $("#question-content-div");

        parent_element.block({
            message: null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity: .6
            }
        });

        $.post(ywqa.ajax_url, data, function (response) {
            //  retrieve new status and set "selected" CSS class
            if (1 == response.code) {
                $("#respond-to-question").after('<span class="success">' + ywqa.answer_success + '</span>')
                $("#answers").replaceWith(response.items);
            }
            else if (-1 == response.code) {
                $("#respond-to-question").after('<span class="error">' + ywqa.answer_error + '</span>')
            }

            parent_element.unblock();
        });
    }));

    function start_current_operation(current_element, op_type) {

        var container = $(current_element).closest("li.discussion-container");

        var actions_section = container.find("div.ywqa-actions");
        var confirm_section = container.find("div.ywqa-modify-content");
        var editor = container.find("div.wp-editor-wrap.tmce-active");
        var content_div = container.find("div.answer-content");
        var confirm_element = container.find("a.action-confirm");
        var confirm_delete_message = container.find("span.confirm-delete");
        var content_status = container.find("div.badge.unapproved");

        confirm_section.css("display", "inherit");
        actions_section.css("display", "none");
        content_div.css("display", "none");
        content_status.css("display", "none");

        if ("edit" == op_type) {
            //  Show an editor for the answer content
            editor.css("display", "inherit");
        }
        else if ("delete" == op_type) {
            //  Show a confirmation message before really deleting the answer
            confirm_delete_message.css("display", "inherit");
        }

        confirm_element.data("op-type", op_type);
    }

    function end_current_operation(current_element, op_type) {
        var container = $(current_element).closest("li.discussion-container");

        var actions_section = container.find("div.ywqa-actions");
        var confirm_section = container.find("div.ywqa-modify-content");
        var editor = container.find("div.wp-editor-wrap.tmce-active");
        var content_div = container.find("div.answer-content");
        var confirm_element = container.find("a.action-confirm");
        var confirm_delete_message = container.find("span.confirm-delete");
        var content_status = container.find("div.badge.unapproved");

        editor.css("display", "none");
        confirm_section.css("display", "none");
        confirm_delete_message.css("display", "none");

        content_div.css("display", "inherit");
        actions_section.css("display", "inherit");
        content_status.css("display", "inherit");

        confirm_element.data("op-type", "");
    }

    /**
     * Let the user starting to modify the content
     */
    $(document).on('click', "a.action-modify", (function (e) {
        e.preventDefault();

        var container = $(this).closest("li.discussion-container");

        container.block({
            message: null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity: .6
            }
        });

        start_current_operation($(this), "edit");

        container.unblock();
    }))

    /**
     * Let the user to delete an answer
     */
    $(document).on('click', "a.action-delete", (function (e) {
        e.preventDefault();

        var container = $(this).closest("li.discussion-container");

        container.block({
            message: null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity: .6
            }
        });

        start_current_operation($(this), "delete");

        container.unblock();
    }))


    /**
     * Let the user starting to modify the content
     */
    $(document).on('click', "a.action-confirm", (function (e) {
        e.preventDefault();

        var action_type = $(this).data("op-type");

        var current_element = $(this);

        var confirm_section = $("div.ywqa-modify-content");
        var container = $(this).closest("li.discussion-container");
        var item_id = $(this).data("discussion-id");

        var answer_text = '';
        if ("edit" == action_type) {
            //answer_text = tinyMCE.get("edit-answer-" + item_id).getContent();
            answer_text = get_editor_text("edit-answer-" + item_id);
        }

        var data = {
            'action': 'edit_discussion_content',
            'discussion_id': item_id,
            'discussion_content': answer_text,
            'action_type': action_type
        };

        container.block({
            message: null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity: .6
            }
        });

        $.post(ywqa.ajax_url, data, function (response) {

            //  retrieve new status and set "selected" CSS class
            if (response.code) {
                //  content updated
                if ("edit" == action_type) {
                    container.find("span.answer-text").html(answer_text);
                }
                else if ("delete" == action_type) {
                    container.remove();
                }

                end_current_operation(current_element, '');
            }
            else if (-1 == response.code) {
                //  something goes wrong...
                confirm_section.after('<span class="error">' + ywqa.answer_error + '</span>')
            }

            container.unblock();
        });
    }))

    /**
     * Let the user starting to modify the content
     */
    $(document).on('click', "a.change-status", (function (e) {
        e.preventDefault();

        var action_type = $(this).data("action");

        var current_element = $(this);

        var container = $(this).closest("li.discussion-container");

        var data = {
            'action': "change_answer_status",
            'action_type': action_type,
            'discussion_id': $(this).data("discussion-id")
        };

        container.block({
            message: null,
            overlayCSS: {
                background: "#fff url(" + ywqa.loader + ") no-repeat center",
                opacity: .6
            }
        });

        $.post(ywqa.ajax_url, data, function (response) {
            //  retrieve new status and set "selected" CSS class
            if (response.code > 0) {
                if ('' != response.result) {
                    container.replaceWith(response.result);
                }
            }
            else if (-1 == response.code) {
                //  something goes wrong...
                confirm_section.after('<span class="error">' + ywqa.answer_error + '</span>')
            }

            container.unblock();
        });
    }))

    /**
     * Let the user starting to modify the content
     */
    $(document).on('click', "a.action-cancel", (function (e) {
        e.preventDefault();
        end_current_operation($(this), '')
    }));
});