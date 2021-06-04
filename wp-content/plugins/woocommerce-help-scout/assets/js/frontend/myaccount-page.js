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
			var uploadArea = '<div class="input-field"><label class="active">Files</label><div class="my-account-conversation-file-1" style="padding-top: .5rem;"></div></div>';
			$('#plUploadArea').html(uploadArea);
			$('.my-account-conversation-file-1').imageUploader({
				maxFiles: 10,
				extensions: ['.jpg', '.jpeg', '.png', '.pdf', '.JPG', '.JPEG', '.PNG', '.PDF'],
				mimes : ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']
			});
            var c = $(this).data("subject"),
                a = $(this).data("conversation-id");
            $("#support-conversation-wrap-reply").fadeOut(500, function () {
                $("#support-conversation-thread-head-reply").html(woocommerce_help_scout_myaccount_params.reply_to + " " + c),
					//$('input[type="file"]').val('');
					//$('#support-conversation-reply').get(0).reset();
					$('#support-conversation-reply .uploaded').html('');
					$('#support-conversation-reply .image-uploader').removeClass('has-files');
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
            var wrap = $("#support-conversation-wrap-reply"),
				form = $("#support-conversation-reply"),
				title = $("#support-conversation-thread-head-reply");
			block_ui(woocommerce_help_scout_myaccount_params.processing);
			
			var formdata = new FormData();
            var formdata = new FormData($("#support-conversation-reply")[0]);
			
			formdata.append("action", "wc_help_scout_create_thread");
            formdata.append("security", woocommerce_help_scout_form_params.security);
			
			$.ajax({
				type: "POST",
				url: woocommerce_help_scout_myaccount_params.ajax_url,
				cache: false,
				data: formdata,
				dataType: "json",
				processData: false,
				contentType: false,
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
        });
    });
})(jQuery);
