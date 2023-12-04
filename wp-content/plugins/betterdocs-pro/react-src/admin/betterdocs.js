(function ($) {
	"use strict";

	$(document).ready(function () {

		$('.select-kb-top').on('change', function() {
			javascript:location.href = "admin.php?page=betterdocs-admin&knowledgebase=" + this.value;
		});

		// drag and drop sortalbe knowledge base
		const kb_index =
			parseInt(betterdocs_pro_admin.paged) > 0
				? (parseInt(betterdocs_pro_admin.paged) - 1) *
				  parseInt($("#" + betterdocs_pro_admin.per_page_id).val())
				: 0;
		const kb_table = $(".taxonomy-knowledge_base #the-list");

		if (kb_table.length > 0) {
			// If the tax table contains items.
			if (!kb_table.find("tr:first-child").hasClass("no-items")) {
				kb_table.sortable({
					placeholder: "betterdocs-drag-drop-kb-tax-placeholder",
					axis: "y",

					// On start, set a height for the placeholder to prevent table jumps.
					start: function (event, ui) {
						const item = $(ui.item[0]);
						const index = item.index();
						$(".betterdocs-drag-drop-kb-tax-placeholder").css(
							"height",
							item.css("height")
						);
					},
					// Update callback.
					update: function (event, ui) {
						const item = $(ui.item[0]);

						const kb_ordering_data = [];

						kb_table
							.find("tr.ui-sortable-handle")
							.each(function () {
								const ele = $(this);
								const term_data = {
									term_id: ele.attr("id").replace("tag-", ""),
									order: parseInt(ele.index()) + 1,
								};
								kb_ordering_data.push(term_data);
							});

						// AJAX Data.
						const data = {
							action: "update_knowledge_base_order",
							kb_ordering_data: kb_ordering_data,
							kb_index: kb_index,
							knowledge_base_order_nonce:
								betterdocs_pro_admin.knowledge_base_order_nonce,
						};

						// Run the ajax request.
						$.ajax({
							type: "POST",
							url: betterdocs_pro_admin.ajaxurl,
							data: data,
							dataType: "JSON",
						});
					},
				});
			}
		}

		$('#betterdocs-meta-reporting_frequency .betterdocs-field-description').hide();
		var reporting_frequency = $('#reporting_frequency').attr("data-value");
        if (reporting_frequency == 'betterdocs_monthly' ) {
            $('#betterdocs-meta-reporting_frequency .betterdocs-field-description').show();
        }

        $('#reporting_frequency').on('select2:select', function (e) {
            var data = e.params.data.id;
            if(data === 'betterdocs_monthly') {
                $('#betterdocs-meta-reporting_frequency .betterdocs-field-description').show();
            } else {
                $('#betterdocs-meta-reporting_frequency .betterdocs-field-description').hide();
            }
        });

		// Disable tabs condition for IA
		var chatTab = $("#chat_tab_visibility_switch"),
			ansTab = $("#answer_tab_visibility_switch"),
			chatTabDependency = [
				"chat_tab_icon",
				"chat_tab_title",
				"chat_subtitle_one",
				"chat_subtitle_two",
			],
			ansTabDependency = [
				"answer_tab_icon",
				"answer_tab_title",
				"answer_tab_subtitle",
			];

		chatTab.prop("checked") === true && ansTab.prop("checked") === true
			? ansTab.prop("checked", false)
			: null;

		ansTab.on("click", function () {
			if (
				chatTab.prop("checked") === true &&
				$(this).prop("checked") === true
			) {
				chatTab.prop("checked", false);

				chatTabDependency.map(function (handle) {
					$("#betterdocs-meta-" + handle).css("display", "table-row");
				});
			}
		});

		chatTab.on("click", function () {
			if (
				ansTab.prop("checked") === true &&
				$(this).prop("checked") === true
			) {
				ansTab.prop("checked", false);

				ansTabDependency.map(function (handle) {
					$("#betterdocs-meta-" + handle).css("display", "table-row");
				});
			}
		});
	});
})(jQuery);
