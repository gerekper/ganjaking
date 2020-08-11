(function ($) {
	"use strict";

	/**
	 * BetterDocs Admin JS
	 */

	$.betterdocsAdmin = $.betterdocsAdmin || {};

	$(document).ready(function () {
		$.betterdocsAdmin.init();

		var qVars = $.betterdocsAdmin.get_query_vars("page");
		if (qVars != undefined) {
			if (qVars.indexOf("betterdocs-settings") >= 0) {
				var cSettingsTab = qVars.split("#");
				$(
					'.betterdocs-settings-menu li[data-tab="' +
						cSettingsTab[1] +
						'"]'
				).trigger("click");
			}
		}
	});

	$.betterdocsAdmin.init = function () {
		$.betterdocsAdmin.toggleFields();
		$.betterdocsAdmin.bindEvents();
		$.betterdocsAdmin.initializeFields();
	};

	$.betterdocsAdmin.bindEvents = function () {
		//Advance Checkbox with SweetAlear
		$("body").on(
			"click",
			".betterdocs-adv-checkbox-wrap label, .betterdocs-stats-tease",
			function (e) {
				if (typeof $(this)[0].dataset.swal == "undefined") {
					return;
				}
				if (typeof $(this)[0].dataset.swal != "undefined") {
					e.preventDefault();
				}
				var premium_content = document.createElement("p");
				var premium_anchor = document.createElement("a");

				premium_anchor.setAttribute("href", "https://betterdocs.co");
				premium_anchor.innerText = "Premium";
				premium_anchor.style.color = "red";
				premium_content.innerHTML =
					"You need to upgrade to the <strong>" +
					premium_anchor.outerHTML +
					" </strong> Version to use this feature";

				swal({
					title: "Opps...",
					content: premium_content,
					icon: "warning",
					buttons: [false, "Close"],
					dangerMode: true,
				});
			}
		);

		/**
		 * Group Field Events
		 */
		$(".betterdocs-group-field .betterdocs-group-field-title").on(
			"click",
			function (e) {
				e.preventDefault();
				if ($(e.srcElement).hasClass("betterdocs-group-field-title")) {
					$.betterdocsAdmin.groupToggle(this);
				}
			}
		);
		$(".betterdocs-group-field .betterdocs-group-clone").on(
			"click",
			function () {
				$.betterdocsAdmin.cloneGroup(this);
			}
		);
		$("body").on(
			"click",
			".betterdocs-group-field .betterdocs-group-remove",
			function () {
				$.betterdocsAdmin.removeGroup(this);
			}
		);

		/**
		 * Media Field
		 */
		$(".betterdocs-media-field-wrapper .betterdocs-media-upload-button").on(
			"click",
			function (e) {
				e.preventDefault();
				$.betterdocsAdmin.initMediaField(this);
			}
		);
		$(".betterdocs-media-field-wrapper .betterdocs-media-remove-button").on(
			"click",
			function (e) {
				e.preventDefault();
				$.betterdocsAdmin.removeMedia(this);
			}
		);

		/**
		 * Settings Tab
		 */
		$(".betterdocs-settings-menu li").on("click", function (e) {
			$.betterdocsAdmin.settingsTab(this);
		});
		$(".betterdocs-settings-button").on("click", function (e) {
			e.preventDefault();
			var form = $(this).parents("#betterdocs-settings-form");
			$.betterdocsAdmin.submitSettings(this, form);
		});

		$(".betterdocs-opt-alert").on("click", function (e) {
			$.betterdocsAdmin.fieldAlert(this);
		});

		/**
		 * Reset Section Settings
		 */
		$(".betterdocs-section-reset").on("click", function (e) {
			e.preventDefault();
			$.betterdocsAdmin.resetSection(this);
		});
	};

	$.betterdocsAdmin.initializeFields = function () {
		$.betterdocsAdmin.innerTab();
		if (
			$(".betterdocs-meta-field, .betterdocs-settings-field").length > 0
		) {
			$(".betterdocs-meta-field, .betterdocs-settings-field").map(
				function (iterator, item) {
					var node = item.nodeName;
					if (node === "SELECT") {
						$(item).select2({
							placeholder: "Select any",
						});
					}
				}
			);
		}

		if ($(".betterdocs-countdown-datepicker").length > 0) {
			$(".betterdocs-countdown-datepicker").each(function () {
				$(this).find("input").datepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat: "DD, d MM, yy",
				});
			});
		}

		$(
			".betterdocs-metabox-wrapper .betterdocs-meta-field, .betterdocs-settings-field"
		).trigger("change");

		if ($(".betterdocs-colorpicker-field").length > 0) {
			if ("undefined" !== typeof $.fn.wpColorPicker) {
				$(".betterdocs-colorpicker-field").each(function () {
					var color = $(this).val();
					$(this)
						.wpColorPicker({
							change: function (event, ui) {
								var element = event.target;
								var color = ui.color.toString();
								$(element)
									.parents(".wp-picker-container")
									.find("input.betterdocs-colorpicker-field")
									.val(color)
									.trigger("change");
							},
						})
						.parents(".wp-picker-container")
						.find(".wp-color-result")
						.css("background-color", "#" + color);
				});
			}
		}
		$.betterdocsAdmin.groupField();
		$(".betterdocs-meta-template-editable").trigger("blur");
	};

	$.betterdocsAdmin.innerTab = function () {
		if ($(".betterdocs-section-inner-tab").length <= 0) {
			return;
		}

		$(".betterdocs-section-inner-tab").each(function (i, item) {
			$(item)
				.find("ul")
				.on("click", "li", function (e) {
					var target = e.currentTarget.dataset.target;
					$(this)
						.addClass("betterdocs-active")
						.siblings()
						.removeClass("betterdocs-active");
					$("#" + target)
						.show()
						.siblings()
						.hide();
				});
			$(item).find("ul").find("li:first").trigger("click");
		});
	};

	$.betterdocsAdmin.groupField = function () {
		if ($(".betterdocs-group-field-wrapper").length < 0) {
			return;
		}

		var fields = $(".betterdocs-group-field-wrapper");

		fields.each(function () {
			var $this = $(this),
				groups = $this.find(".betterdocs-group-field"),
				firstGroup = $this.find(".betterdocs-group-field:first"),
				lastGroup = $this.find(".betterdocs-group-field:last");

			groups.each(function () {
				var groupContent = $(this)
					.find(".betterdocs-group-field-title:not(.open)")
					.next();
				if (groupContent.is(":visible")) {
					groupContent.addClass("open");
				}
			});

			$this.find(".betterdocs-group-field-add").on("click", function (e) {
				e.preventDefault();

				var fieldId = $this.attr("id"),
					dataId = $this.data("name"),
					wrapper = $this.find(".betterdocs-group-fields-wrapper"),
					groups = $this.find(".betterdocs-group-field"),
					firstGroup = $this.find(".betterdocs-group-field:first"),
					lastGroup = $this.find(".betterdocs-group-field:last"),
					clone = $($this.find(".betterdocs-group-template").html()),
					groupId = parseInt(lastGroup.data("id")),
					nextGroupId = 1,
					title = clone.data("group-title");

				if (!isNaN(groupId)) {
					nextGroupId = groupId + 1;
				}

				groups.each(function () {
					$(this).removeClass("open");
				});

				// Reset all data of clone object.
				clone.attr("data-id", nextGroupId);
				clone.addClass("open");
				// clone.find('.betterdocs-group-field-title > span').html(title + ' ' + nextGroupId);
				clone
					.find("tr.betterdocs-field[id*=" + fieldId + "]")
					.each(function () {
						var fieldName = dataId;
						var fieldNameSuffix = $(this)
							.attr("id")
							.split("[1]")[1];
						var nextFieldId =
							fieldName +
							"[" +
							nextGroupId +
							"]" +
							fieldNameSuffix;
						var label = $(this).find("th label");

						$(this)
							.find('[name*="' + fieldName + '[1]"]')
							.each(function () {
								var inputName = $(this)
									.attr("name")
									.split("[1]");
								var inputNamePrefix = inputName[0];
								var inputNameSuffix = inputName[1];
								var newInputName =
									inputNamePrefix +
									"[" +
									nextGroupId +
									"]" +
									inputNameSuffix;
								$(this)
									.attr("id", newInputName)
									.attr("name", newInputName);
								label.attr("for", newInputName);
							});

						$(this).attr("id", nextFieldId);
					});

				clone.insertBefore($(this));

				$.betterdocsAdmin.resetFieldIds($(".betterdocs-group-field"));
			});
		});
	};

	/**
	 * This function will change tab
	 * with menu click & Next Previous Button Click
	 */
	$.betterdocsAdmin.tabChanger = function (buttonName) {
		var button = $(buttonName),
			tabID = button.data("tabid"),
			tabKey = button.data("tab"),
			tab;

		if (tabKey != "") {
			tab = $("#betterdocs-" + tabKey);
			$("#betterdocs_builder_current_tab").val(tabKey);
		}

		if (buttonName.nodeName !== "BUTTON") {
			button
				.parent()
				.find("li")
				.each(function (i) {
					if (i < tabID) {
						$(this).addClass("betterdocs-complete");
					} else {
						$(this).removeClass("betterdocs-complete");
					}
				});

			button.addClass("active").siblings().removeClass("active");
			tab.addClass("active").siblings().removeClass("active");
			return;
		}
		if (tab === undefined) {
			$("#publish").trigger("click");
			return;
		}
		$('.betterdocs-metatab-menu li[data-tabid="' + tabID + '"]').trigger(
			"click"
		);
		$(
			'.betterdocs-builder-tab-menu li[data-tabid="' + tabID + '"]'
		).trigger("click");
	};

	$.betterdocsAdmin.toggleFields = function () {
		$(".betterdocs-meta-field, .betterdocs-settings-field").on(
			"change",
			function (e) {
				$.betterdocsAdmin.checkDependencies(this);
			}
		);
	};

	$.betterdocsAdmin.toggle = function (array, func, prefix, suffix, id) {
		var i = 0;
		suffix = "undefined" == typeof suffix ? "" : suffix;

		if (typeof array !== "undefined") {
			for (; i < array.length; i++) {
				var selector = prefix + array[i] + suffix;
				$(selector)[func]();
			}
		}
	};

	$.betterdocsAdmin.checkDependencies = function (variable) {
		if (betterdocsAdminConfig.toggleFields === null) {
			return;
		}

		var current = $(variable),
			container = current.parents(".betterdocs-field:first"),
			id = container.data("id"),
			value = current.val();

		if ("checkbox" === current.attr("type")) {
			if (!current.is(":checked")) {
				value = 0;
			} else {
				value = 1;
			}
		}

		if (current.hasClass("betterdocs-theme-selected")) {
			var currentTheme = current
				.parents(".betterdocs-theme-control-wrapper")
				.data("name");
			value = $("#" + currentTheme).val();
		}

		var mainid = id;

		if (betterdocsAdminConfig.toggleFields.hasOwnProperty(id)) {
			var canShow = betterdocsAdminConfig.toggleFields[id].hasOwnProperty(
				value
			);
			var canHide = true;
			if (betterdocsAdminConfig.hideFields[id]) {
				var canHide = betterdocsAdminConfig.hideFields[
					id
				].hasOwnProperty(value);
			}

			if (
				betterdocsAdminConfig.toggleFields.hasOwnProperty(id) &&
				canHide
			) {
				$.each(betterdocsAdminConfig.toggleFields[id], function (
					key,
					array
				) {
					$.betterdocsAdmin.toggle(
						array.fields,
						"hide",
						"#betterdocs-meta-",
						"",
						mainid
					);
					$.betterdocsAdmin.toggle(
						array.sections,
						"hide",
						"#betterdocs-settings-",
						"",
						mainid
					);
				});
			}

			if (canShow) {
				$.betterdocsAdmin.toggle(
					betterdocsAdminConfig.toggleFields[id][value].fields,
					"show",
					"#betterdocs-meta-",
					"",
					mainid
				);
				$.betterdocsAdmin.toggle(
					betterdocsAdminConfig.toggleFields[id][value].sections,
					"show",
					"#betterdocs-settings-",
					"",
					mainid
				);
			}
		}

		if (betterdocsAdminConfig.hideFields.hasOwnProperty(id)) {
			var hideFields = betterdocsAdminConfig.hideFields[id];

			if (hideFields.hasOwnProperty(value)) {
				$.betterdocsAdmin.toggle(
					hideFields[value].fields,
					"hide",
					"#betterdocs-meta-",
					"",
					mainid
				);
				$.betterdocsAdmin.toggle(
					hideFields[value].sections,
					"hide",
					"#betterdocs-settings-",
					"",
					mainid
				);
			}
		}
	};

	$.betterdocsAdmin.groupToggle = function (group) {
		var input = $(group),
			wrapper = input.parents(".betterdocs-group-field");

		if (wrapper.hasClass("open")) {
			wrapper.removeClass("open");
		} else {
			wrapper.addClass("open").siblings().removeClass("open");
		}
	};

	$.betterdocsAdmin.removeGroup = function (button) {
		var groupId = $(button)
				.parents(".betterdocs-group-field")
				.attr("data-id"),
			group = $(button).parents(
				'.betterdocs-group-field[data-id="' + groupId + '"]'
			),
			parent = group.parent();

		group.fadeOut({
			duration: 300,
			complete: function () {
				$(this).remove();
			},
		});

		$.betterdocsAdmin.resetFieldIds(parent.find(".betterdocs-group-field"));
	};

	$.betterdocsAdmin.cloneGroup = function (button) {
		var groupId = $(button)
				.parents(".betterdocs-group-field")
				.attr("data-id"),
			group = $(button).parents(
				'.betterdocs-group-field[data-id="' + groupId + '"]'
			),
			clone = $(group.clone()),
			lastGroup = $(button)
				.parents(".betterdocs-group-fields-wrapper")
				.find(".betterdocs-group-field:last"),
			parent = group.parent(),
			nextGroupID = $(lastGroup).data("id") + 1;

		group.removeClass("open");

		clone.attr("data-id", nextGroupID);
		clone.insertAfter(group);
		$.betterdocsAdmin.resetFieldIds(parent.find(".betterdocs-group-field"));
	};

	$.betterdocsAdmin.resetFieldIds = function (groups) {
		if (groups.length <= 0) {
			return;
		}
		var groupID = 0;

		groups.map(function (iterator, item) {
			var item = $(item),
				fieldName = item.data("field-name"),
				groupInfo = item
					.find(".betterdocs-group-field-info")
					.data("info"),
				subFields = groupInfo.group_sub_fields;

			item.attr("data-id", groupID);

			var table_row = item.find("tr.betterdocs-field");

			table_row.each(function (i, child) {
				var child = $($(child)[0]),
					childInput = child.find(
						'[name*="betterdocs_meta_' + fieldName + '"]'
					),
					key = childInput.attr("data-key"),
					subKey = subFields[i].original_name,
					dataID = fieldName + "[" + groupID + "][" + subKey + "]",
					idName = "betterdocs-meta-" + dataID,
					inputName = "betterdocs_meta_" + dataID;

				child.attr("data-id", dataID);
				child.attr("id", idName);

				if (key != undefined) {
					childInput.attr("id", inputName);
					childInput.attr("name", inputName);
					childInput.attr("data-key", dataID);
				} else {
					if (childInput.length > 1) {
						childInput.each(function (i, subInput) {
							if (subInput.type === "text") {
								var subInputName = inputName + "[url]";
							}
							if (subInput.type === "hidden") {
								var subInputName = inputName + "[id]";
							}
							subInput = $(subInput);
							subInput.attr("id", subInputName);
							subInput.attr("name", subInputName);
							subInput.attr("data-key", dataID);
						});
					}
				}
			});

			groupID++;
		});
	};

	$.betterdocsAdmin.initMediaField = function (button) {
		var button = $(button),
			wrapper = button.parents(".betterdocs-media-field-wrapper"),
			removeButton = wrapper.find(".betterdocs-media-remove-button"),
			imgContainer = wrapper.find(".betterdocs-thumb-container"),
			idField = wrapper.find(".betterdocs-media-id"),
			urlField = wrapper.find(".betterdocs-media-url");

		// Create a new media frame
		var frame = wp.media({
			title: "Upload Photo",
			button: {
				text: "Use this photo",
			},
			multiple: false, // Set to true to allow multiple files to be selected
		});

		// When an image is selected in the media frame...
		frame.on("select", function () {
			// Get media attachment details from the frame state
			var attachment = frame.state().get("selection").first().toJSON();
			/**
			 * Set image to the image container
			 */
			imgContainer
				.addClass("betterdocs-has-thumb")
				.append(
					'<img src="' +
						attachment.url +
						'" alt="" style="max-width:100%;"/>'
				);
			idField.val(attachment.id); // set image id
			urlField.val(attachment.url); // set image url
			// Hide the upload button
			button.addClass("hidden");
			// Show the remove button
			removeButton.removeClass("hidden");
		});
		// Finally, open the modal on click
		frame.open();
	};

	$.betterdocsAdmin.removeMedia = function (button) {
		var button = $(button),
			wrapper = button.parents(".betterdocs-media-field-wrapper"),
			uploadButton = wrapper.find(".betterdocs-media-upload-button"),
			imgContainer = wrapper.find(".betterdocs-has-thumb"),
			idField = wrapper.find(".betterdocs-media-id"),
			urlField = wrapper.find(".betterdocs-media-url");

		imgContainer.removeClass("betterdocs-has-thumb").find("img").remove();

		urlField.val(""); // URL field has to be empty
		idField.val(""); // ID field has to empty as well

		button.addClass("hidden"); // Hide the remove button first
		uploadButton.removeClass("hidden"); // Show the uplaod button
	};

	$.betterdocsAdmin.fieldAlert = function (button) {
		var premium_content = document.createElement("p");
		var premium_anchor = document.createElement("a");

		premium_anchor.setAttribute("href", "https://betterdocs.co/upgrade");
		premium_anchor.innerText = "Premium";
		premium_anchor.style.color = "red";
		premium_content.innerHTML =
			"You need to upgrade to the <strong>" +
			premium_anchor.outerHTML +
			" </strong> Version to use this feature";

		swal({
			title: "Opps...",
			content: premium_content,
			icon: "warning",
			buttons: [false, "Close"],
			dangerMode: true,
		});
	};

	$.betterdocsAdmin.resetSection = function (button) {
		var button = $(button),
			parent = button.parents(".betterdocs-meta-section"),
			fields = parent.find(".betterdocs-meta-field"),
			updateFields = [];

		window.fieldsss = fields;
		fields.map(function (iterator, item) {
			var item = $(item),
				default_value = item.data("default");

			item.val(default_value);

			if (item.hasClass("wp-color-picker")) {
				item.parents(".wp-picker-container")
					.find(".wp-color-result")
					.removeAttr("style");
			}
			if (item[0].id == "betterdocs_meta_border") {
				item.trigger("click");
			} else {
				item.trigger("change");
			}
		});
	};

	$.betterdocsAdmin.settingsTab = function (button) {
		var button = $(button),
			tabToGo = button.data("tab");

		button.addClass("active").siblings().removeClass("active");
		$("#betterdocs-" + tabToGo)
			.addClass("active")
			.siblings()
			.removeClass("active");
	};

	$.betterdocsAdmin.submitSettings = function (button, form) {
		var button = $(button),
			submitKey = button.data("key"),
			nonce = button.data("nonce"),
			formData = $(form).serializeArray();

		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				action: "betterdocs_general_settings",
				key: submitKey,
				nonce: nonce,
				form_data: formData,
			},
			beforeSend: function () {
				button.html("<span>Saving...</span>");
			},
			success: function (res) {
				button.html("Save Settings");
				if (res.data === "success") {
					swal({
						title: "Settings Saved!",
						text: "Click OK to continue",
						icon: "success",
						buttons: [false, "Ok"],
						timer: 2000,
					}).then(function () {
						$(".betterdocs-save-now").removeClass(
							"betterdocs-save-now"
						);
						location.reload();
					});
				} else {
					swal({
						title: "Settings Not Saved!",
						text: "Click OK to continue",
						icon: "error",
						buttons: [false, "Ok"],
						timer: 1000,
					});
				}
			},
		});
	};

	$.betterdocsAdmin.get_query_vars = function (name) {
		var vars = {};
		window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (
			m,
			key,
			value
		) {
			vars[key] = value;
		});
		if (name != "") {
			return vars[name];
		}
		return vars;
	};
})(jQuery);
