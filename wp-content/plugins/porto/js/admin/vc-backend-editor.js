jQuery(document).ready(function ($) {
	'use strict';
	if (window.parent.vc && window.parent.vc.events && vc.storage) {
		window.parent.vc.edit_element_block_view.on('afterRender', function () {
			var $el = this.$el,
				widgets = ['porto_ultimate_heading', 'porto_buttons', 'porto_image_comparison', 'porto_interactive_banner', 'vc_custom_heading', 'vc_btn', 'porto_countdown', 'vc_single_image'];
			if ($.inArray($el.attr('data-vc-shortcode'), widgets) >= 0) {
				$el.find('select').each(function () {
					var $this = $(this),
						el_class = $this.attr('class'),
						index_last = el_class.indexOf('_dynamic_source');
					if (index_last >= 0) {
						var index_first = el_class.lastIndexOf(' ', index_last);
						if (index_first == -1) {
							index_first = 0;
						}
						var field_name = el_class.substring(index_first, index_last).trim(),
							field_index = field_name.indexOf('_'),
							field_type = '';
						if (field_index > 0) {
							field_type = field_name.substring(0, field_index);
						} else {
							field_type = field_name;
						}
						if (field_type == 'field' || field_type == 'link' || field_type == 'image') {
							porto_wpb_dynamic_execute($el, field_type, field_name);
						}
					}
				});
			}
		});
		function porto_wpb_dynamic_execute($el, field_type, field_name) {
			var $dynamic_source_object = $el.find('select.' + field_name + '_dynamic_source'),
				dynamic_source = $dynamic_source_object.val(),
				$dynamic_content = $el.find('select.' + field_name + '_dynamic_content');
			porto_wpb_dyanmic_content(dynamic_source, field_type, $dynamic_content);

			$dynamic_source_object.on('change', function () {
				dynamic_source = $(this).val();
				if (field_type == 'field') {
					porto_wpb_dynamic_enable_subcontent($el, $dynamic_content.val(), 'post_date', 'date_format');
				}
				porto_wpb_dyanmic_content(dynamic_source, field_type, $dynamic_content);
			});

			// Format date format
			if (field_type == 'field') {
				porto_wpb_dynamic_enable_subcontent($el, $dynamic_content.val(), 'post_date', 'date_format');
			}

			$dynamic_content.on('change', function () {
				if (field_type == 'field') {
					porto_wpb_dynamic_enable_subcontent($el, $dynamic_content.val(), 'post_date', 'date_format');
				}
			});
		}

		function porto_wpb_dynamic_enable_subcontent($el, dynamic_content_option, content_value, shortcode_param) {
			var $sub_content = $el.find('[data-vc-shortcode-param-name="' + shortcode_param + '"]'),
				$sub_content_select = $el.find('[name="' + shortcode_param + '"]');
			if ($sub_content.length) {
				if (content_value == dynamic_content_option) {
					if ($sub_content.hasClass('vc_dependent-hidden')) {
						$sub_content.removeClass('vc_dependent-hidden');
						$sub_content_select.val($sub_content_select.attr('value'));
					}
				} else {
					$sub_content.addClass('vc_dependent-hidden');
					$sub_content_select.val('');
				}
			}
		}

		function porto_wpb_dyanmic_content(dynamic_source, field_type, dynamic_content) {
			dynamic_content.find('*').remove();
			if ('' != dynamic_source && 'meta_field' != dynamic_source && dynamic_content.length && !dynamic_content.hasClass('.vc_dependent-hidden') && porto_wpb_vars[dynamic_source]) {
				if (porto_wpb_vars[dynamic_source][field_type]) {
					var $contents = porto_wpb_vars[dynamic_source][field_type],
						keys = Object.keys($contents),
						attribute = dynamic_content.attr('data-option'), selected_content = false;

					if (keys.length) {
						dynamic_content.append('<option class="" value="">Select Source...</option>');
						for (let index = 0; index < keys.length; index++) {
							var selected = '';
							if (keys[index] == attribute) {
								selected = 'selected="selected"';
								selected_content = true;
							}
							dynamic_content.append('<option class="' + keys[index] + '" value="' + keys[index] + '" ' + selected + '>' + $contents[keys[index]] + '</option>');
						}
					}
					if (selected_content) {
						dynamic_content.val(attribute).addClass(attribute);
					}
				}
			}
		}

		if (typeof window.vc !== 'undefined') {
			$(document).on('click', '#publish', function (e) {
				// After Post is published
				if ('publish' == $('#original_post_status').val() && typeof js_porto_admin_vars.wpb_backend_ajax !== 'undefined' && js_porto_admin_vars.wpb_backend_ajax == '1') {
					// Stop Default Save 
					e.preventDefault();
					if ('object' == typeof (tinymce) && tinymce.editors.content && !$('#post-body-content .composer-switch').hasClass('vc_backend-status') && 'tinymce' == getUserSetting('editor')) {
						// Visual Backend Editor
						tinymce.editors.content.save();
					} else if ('html' == getUserSetting('editor')) {
						// Html to Visual editor
						$('#content-tmce').trigger('click');
					}
					// Remove P tag
					var $content = $('#content'),
						content = $content.val().trim(),
						__ = wp.i18n.__,
						$this = $(this);
					if (0 == content.indexOf('<p>[')) {
						content = content.slice(3, -4);
						$content.val(content);
					} else if (0 == content.indexOf('<p><span data-mce-type=')) {
						// Backend Editor from Frontend Editor
						content = content.slice(144, -143);
						$content.val(content);
					}
					$this.html(__('Updating..', 'porto')).attr('value', 'Updating..');
					$('#wpb-save-post').html(__('Loading..', 'porto'));
					$.ajax({
						url: js_porto_admin_vars.ajax_url.replace('admin-ajax', 'post'),
						data: $('#post').serialize(),
						method: 'post',
						success: function (response) {
							var $alert = $('<div class="vc_backend_message show-message success">' + __('Successfully Updated.', 'porto') + '</div>');
							$('body').append($alert);
							$('#wpb-save-post').html(__('Update', 'porto'));
							$this.html(__('Update', 'porto')).attr('value', 'Update');
							$alert.fadeIn(400);
							var timerId = setTimeout(function () {
								$alert.fadeOut(900, function () {
									$alert.remove();
								});
							}, 3500);
							$alert.on('click', function (e) {
								clearTimeout(timerId);
								$alert.fadeOut(900, function () {
									$alert.remove();
								});
							})
						}
					}).fail(function (response) {
						var $alert = $('<div class="vc_backend_message show-message error">' + __('Updated Failed.', 'porto') + '</div>');
						$('body').append($alert);
						$alert.fadeIn(400);
						$('#wpb-save-post').html(__('Update', 'porto'));
						$this.html(__('Update', 'porto')).attr('value', 'Update');
						var timerId = setTimeout(function () {
							$alert.fadeOut(900, function () {
								$alert.remove();
							});
						}, 3500);
						$alert.on('click', function (e) {
							clearTimeout(timerId);
							$alert.fadeOut(900, function () {
								$alert.remove();
							});
						})
					})

					// Rank Math SEO Compatibility
					if (undefined != typeof window.rankMathEditor) {
						window.rankMathEditor.assessor.saveMeta();
						window.rankMathEditor.assessor.saveRedirection();
						window.rankMathEditor.assessor.saveSchemas();
					}
				}
			});
		}
	}
});