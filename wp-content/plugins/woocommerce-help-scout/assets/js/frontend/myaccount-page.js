(function ($) {
    "use strict";
    $(function () {
        function block_ui(msg) {
            if (!msg) {
                return;
            }
            $.blockUI({
                message: msg,
                baseZ: 99999,
                overlayCSS: { background: "#fff", opacity: 0.6 },
                css: { padding: "20px", zindex: "9999999", textAlign: "center", color: "#555", border: "3px solid #aaa", backgroundColor: "#fff", cursor: "wait", lineHeight: "24px" },
            });
        }
        function scrollToConversation() {
            var wrap = $("#support-conversation-wrap");
            $("html, body").animate({ scrollTop: wrap.offset().top }, 500);
        }
        function scrollToConversationReply() {
            var wrap = $("#support-conversation-wrap-reply");
            $("html, body").animate({ scrollTop: wrap.offset().top }, 500);
        }
        $("#support-conversations-table .conversation-view").on("click", function (e) {
            e.preventDefault();
            $("#support-conversation-wrap").show();
            $("#support-conversation-wrap-reply").hide();
            var conversation_id = $(this).data("conversation-id"),
                wrap = $("#support-conversation-wrap");
            var currentSub = $(this).data("subject");
            block_ui(woocommerce_help_scout_myaccount_params.getting_conversation);
            $.ajax({
                type: "GET",
                url: woocommerce_help_scout_myaccount_params.ajax_url,
                cache: false,
                dataType: "json",
                data: { action: "wc_help_scout_get_conversation", security: woocommerce_help_scout_myaccount_params.security, conversation_id: conversation_id },
                success: function (data) {
                    var html = "",
                        error_message = woocommerce_help_scout_myaccount_params.error;
                    $.unblockUI();
                    if (null !== data && "" === data.error) {
                        html += '<h3 id="support-conversation-thread-head">"' + currentSub + '":</h3>';
                        html += '<ul id="support-conversation-thread">';
                        $.each(data.threads, function (key, thread) {
                            html += "<li>";
                            html += "<strong>" + thread.author + "</strong> <small>(" + thread.date + ")</small>:";
                            html += thread.message;
                            html += "</li>";
                        });
                        html += "</ul>";
                        html += '<p><a href="#" data-conversation-id="' + conversation_id + '" data-subject="' + currentSub + '" class="button conversation-reply">' + woocommerce_help_scout_myaccount_params.reply + "</a></p>";
                        wrap.empty().prepend(html);
                        scrollToConversation();
                    } else {
                        if (null !== data && null !== data.error && "" !== data.error) {
                            error_message = data.error;
                        }
                        $(".woocommerce-error", wrap).remove();
                        wrap.prepend('<div class="woocommerce-error">' + error_message + "</div>");
                    }
                },
                error: function () {
                    $.unblockUI();
                    $(".woocommerce-error", wrap).remove();
                    wrap.prepend('<div class="woocommerce-error">' + woocommerce_help_scout_myaccount_params.error + "</div>");
                },
            });
        });
        $("body").on("click", ".conversation-reply", function (e) {
            e.preventDefault();
            $("#support-conversation-wrap").hide();
            var c = $(this).data("subject"),
                a = $(this).data("conversation-id");
            $("#support-conversation-wrap-reply").fadeOut(500, function () {
                $("#support-conversation-thread-head-reply").html(woocommerce_help_scout_myaccount_params.reply_to + " " + c),
                    $("#reply_conversation_id").val(a),
                    $("#reply_user_id").val(woocommerce_help_scout_myaccount_params.user_id),
                    $("#reply_submit_btn").val(woocommerce_help_scout_myaccount_params.send),
                    $(this).show();
					$('.woocommerce-message').remove();
					$('#support-conversation-reply').show();
            }),
                scrollToConversationReply();
        });
        $("body").on("submit", "#support-conversation-reply", function (e) { 
            e.preventDefault();
            if ($("#my-account-conversation-file-1").plupload("getFiles").length > 0) {
                $("#my-account-conversation-file-1").on("complete", function () {
                    var wrap = $("#support-conversation-wrap-reply"),
                        form = $("#support-conversation-reply"),
                        title = $("#support-conversation-thread-head-reply");
                    block_ui(woocommerce_help_scout_myaccount_params.processing);
                    $.ajax({
                        type: "POST",
                        url: woocommerce_help_scout_myaccount_params.ajax_url,
                        cache: false,
                        dataType: "json",
                        data: {
                            conversation_id: $('input[name="conversation_id"]', form).val(),
                            conversation_message: $('textarea[name="conversation_message"]', form).val(),
                            user_id: $('input[name="user_id"]', form).val(),
                            uploaded_files: $('input[name="uploaded_files"]', form).val(),
                            action: "wc_help_scout_create_thread",
                            security: woocommerce_help_scout_myaccount_params.security,
                        },
                        success: function (data) {
                            $("#support-conversation-reply")[0].reset();
                            $("#my_account_conversation_uploaded_files_1").val('');
							$('#support-conversation-reply').hide();
							$('#plUploadArea').empty();
							var newArea = '<div id="my-account-conversation-file-1" class="pluploder-ui-input"><p>Your browser doesn\'t have Flash, Silverlight or HTML5 support.</p></div>';
							$('#plUploadArea').html(newArea);
							$("#my-account-conversation-file-1").plupload({
								runtimes: "html5,flash,silverlight,html4",
								url: woocommerce_help_scout_form_params.ajax_url + "?action=wc_help_scout_upload_attachments",
								max_file_count: 20,
								chunk_size: "1mb",
								filters: {
									max_file_size: "1000mb",
									mime_types: [
										{ title: "Image files", extensions: "jpg,gif,png,jpeg" },
										{ title: "Pdf files", extensions: "pdf" }
									],
								},
								rename: true,
								sortable: true,
								dragdrop: true,
								views: { list: true, thumbs: true, active: "thumbs" },
								init: {
									FileUploaded: function (up, file, info) {
										console.log(file.name);
										var beforeFiles = $("#my_account_conversation_uploaded_files_1").val();
										if (beforeFiles != "") {
											$("#my_account_conversation_uploaded_files_1").val(beforeFiles + "," + file.name);
										} else {
											$("#my_account_conversation_uploaded_files_1").val(file.name);
										}
									},
								},
							});
                            $.unblockUI();
                            if (null !== data && 1 === data.error) {
                                $(".woocommerce-error", wrap).remove();
                                title.after('<div class="woocommerce-message">' + data.message + "</div>");
                            } else {
                                var error_message = woocommerce_help_scout_myaccount_params.error;
                                if (null !== data && null !== data.message && "" !== data.message) {
                                    error_message = data.message;
                                }
                                $(".woocommerce-error", wrap).remove();
                                title.after('<div class="woocommerce-error">' + error_message + "</div>");
                            }
                        },
                        error: function () {
                            $.unblockUI();
                            $(".woocommerce-error", wrap).remove();
                            title.after('<div class="woocommerce-error">' + woocommerce_help_scout_myaccount_params.error + "</div>");
                        },
                    });
                });
                $("#my-account-conversation-file-1").plupload("start");
            } else {
                var wrap = $("#support-conversation-wrap-reply"),
                    form = $("#support-conversation-reply"),
                    title = $("#support-conversation-thread-head-reply");
                block_ui(woocommerce_help_scout_myaccount_params.processing);
                $.ajax({
                    type: "POST",
                    url: woocommerce_help_scout_myaccount_params.ajax_url,
                    cache: false,
                    dataType: "json",
                    data: {
                        conversation_id: $('input[name="conversation_id"]', form).val(),
                        conversation_message: $('textarea[name="conversation_message"]', form).val(),
                        user_id: $('input[name="user_id"]', form).val(),
                        uploaded_files: $('input[name="uploaded_files"]', form).val(),
                        action: "wc_help_scout_create_thread",
                        security: woocommerce_help_scout_myaccount_params.security,
                    },
                    success: function (data) {
                        $("#support-conversation-reply")[0].reset();
						$('#support-conversation-reply').hide();
                        $.unblockUI();
                        if (null !== data && 1 === data.error) {
                            $(".woocommerce-error", wrap).remove();
                            title.after('<div class="woocommerce-message">' + data.message + "</div>");
                        } else {
                            var error_message = woocommerce_help_scout_myaccount_params.error;
                            if (null !== data && null !== data.message && "" !== data.message) {
                                error_message = data.message;
                            }
                            $(".woocommerce-error", wrap).remove();
                            title.after('<div class="woocommerce-error">' + error_message + "</div>");
                        }
                    },
                    error: function () {
                        $.unblockUI();
                        $(".woocommerce-error", wrap).remove();
                        title.after('<div class="woocommerce-error">' + woocommerce_help_scout_myaccount_params.error + "</div>");
                    },
                });
            }
            return false;
        });
    });
})(jQuery);
