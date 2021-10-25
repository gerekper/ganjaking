'use strict';
(function (factory) {

	window.gt3Elementor = window.gt3Elementor || {};
	window.gt3Elementor.Core = window.gt3Elementor.Core || {};
	window.gt3Elementor.Core.Editor = window.gt3Elementor.Core.Editor || factory(window.jQuery);

})(function ($) {


	function CoreEditor() {
		if (!this || this.options !== CoreEditor.prototype.options) {
			return new CoreEditor()
		}

		this.initialize();

	}

	$.extend(CoreEditor.prototype, {
		options: {

		},
		initialize: function () {
			var that = this;

			window.elementor.channels.editor.on('section:activated', function (sectionName, editor) {
				var wrap = $('.elementor-control-type-gt3-elementor-core-gallery', editor.$el);

			    var model = editor.getOption('editedElementView').getEditModel(),
                    currentElementType = model.get('elType');
                if ('widget' === currentElementType) {
                    currentElementType = model.get('widgetType');
                }
                if ('gt3-core-shoplist' === currentElementType && 'section_general' === sectionName) {
                    setTimeout(function () {
                        var input = editor.$el.find('[data-setting="hidden_update"]');
                        input.val(input.val() === '1' ? '0' : '1').trigger('input');
                    }, 10);
                }
			});

			elementor.hooks.addAction( 'panel/open_editor/widget/text-editor', function( panel, model, view ) {
			   var $element = view.$el.find( '.elementor-selector' );
			   window.elementor.channels.editor.on('change:text-editor', function (sectionName, editor) {
					that.elementor_def_editor_change(view.$el);
				});
			} );

			window.elementor.hooks.addAction('panel/open_editor/widget/gt3-core-testimonials', that['Testimonials'].bind(that));
			window.elementor.hooks.addAction('panel/open_editor/widget', that['hiddenUpdate'].bind(that));

			window.elementor.hooks.addAction( 'panel/open_editor/widget/toggle', function( panel, model, view ) {

				model.get('settings').on('change',function(changedModel){

					_.each(changedModel.attributes, function (settingValue, settingKey) {

						if ('add_question_marker' === settingKey) {
							var tab_items = {};

							setTimeout(function () {
								if (settingValue === 'yes') {
									view.$el.find('.elementor-tab-title').addClass('add_question_marker').attr('data-question_marker', changedModel.attributes.question_marker);
								}else{
									view.$el.find('.elementor-tab-title').removeClass('add_question_marker')
								}
							}, 400);

						}
					})

				})

			})

			window.elementor.hooks.addAction( 'panel/open_editor/column', function( panel, model, view ) {

				model.get('settings').on('change',function(changedModel){

					_.each(changedModel.changedAttributes(), function (settingValue, settingKey) {
						var control = changedModel.getControl(settingKey);
						if ('items' === settingKey) {
							var tab_items = {};

							_.each(settingValue.models, function(item, i){

								tab_items[i] = {};
								tab_items[i]['title'] = item.attributes['tab_title'];
								tab_items[i]['icon'] = item.attributes['icon'];
								tab_items[i]['type'] = item.attributes["type"];
								tab_items[i]['image'] = item.attributes['image'];
								tab_items[i]['image_hover'] = item.attributes['image_hover'];
								tab_items[i]['gt3_tabs_icon_size'] = item.attributes['gt3_tabs_icon_size'];
								tab_items[i]['_id'] = item.attributes['_id'];

							})
							view.$el.attr('data-tab-items',JSON.stringify(tab_items));
						}
					})

				})

				if ('column' !== model.attributes.elType) {
					return;
				}
			   	var $element = view.$el.find( '.elementor-selector' );

				window.elementor.channels.editor.on('change:column', function (sectionName, editor) {

					if (sectionName.elementSettingsModel.attributes.gt3_carousel && sectionName.elementSettingsModel.attributes.gt3_carousel == "yes") {
						view.$el.addClass('gt3_carousel-elementor');

						var attributes = sectionName.elementSettingsModel.attributes;
						var settings = {};

						settings.items_per_line = attributes.gt3_carousel_items_per_line;
						settings.item_per_line_mobile = attributes.gt3_carousel_items_per_line_mobile;
						settings.item_per_line_tablet = attributes.gt3_carousel_items_per_line_tablet;
						settings.autoplay = attributes.gt3_carousel_autoplay == 'yes' ? true : false;
						settings.autoplaySpeed = attributes.gt3_carousel_autoplay_time;
						settings.dots = attributes.gt3_carousel_nav == 'dots' ? true : false;
						settings.arrows = attributes.gt3_carousel_nav == 'arrows' ? true : false;
						settings.centerMode = attributes.gt3_carousel_center_mode == 'yes' ? true : false;
						settings.l10n = {};
						settings.l10n.prev = attributes.gt3_carousel_nav_prev ? attributes.gt3_carousel_nav_prev : '';
						settings.l10n.next = attributes.gt3_carousel_nav_next ? attributes.gt3_carousel_nav_next : '';

						view.$el.attr('data-settings',JSON.stringify(settings));

						view.$el.addClass('gt3_carousel_items_per_line-'+settings.items_per_line);
						view.$el.addClass('gt3_carousel_items_per_line_tablet-'+settings.item_per_line_tablet);
						view.$el.addClass('gt3_carousel_items_per_line_mobile-'+settings.item_per_line_mobile);

						if (sectionName.elementSettingsModel.attributes.gt3_carousel_back_end && sectionName.elementSettingsModel.attributes.gt3_carousel_back_end == "yes") {
							view.$el.removeClass('gt3_carousel_destroy-elementor');
						}else{
							view.$el.addClass('gt3_carousel_destroy-elementor');
						}
					}else{
						view.$el.removeClass('gt3_carousel-elementor');
						view.$el.removeClass('gt3_carousel_destroy-elementor');
					}




				});
			} );


		},


		elementor_def_editor_change: function (view) {
			return;
		},

		hiddenUpdate: function (panel, model, view) {
			panel.$el.on('click', '[data-event="update_widget"]', function () {
				var input = $(this).closest('#elementor-controls').find('[data-setting*="hidden_update"]').eq(0);
				input.val(input.val() === '1' ? '0' : '1').trigger('input');
			});
		},
		Testimonials: function (panel, model, view) {
			var element, icons, preview, preview_html, icon_preview, modal_edit_title,
				remove_icon, insert_icon, modal_edit_link, index = null, el = null;
			var $modal = $('#testimonial_modal', $('body'));

			/* Update on load widget */
			panel.$el.on('click', '[data-event="update_icons"]', function () {
				var wrapper = $(this).closest('.elementor-repeater-row-controls');
				wrapper.find('.elementor-control-icons_preview .elementor-control-raw-html').html(wrapper.find('.elementor-control-icons [data-setting="icons"]').val());
			});

			/* Open modal */
			panel.$el.on('click', '[data-event="show_modal"]', function () {
				/* open */
				if (!$modal.length) {
					$modal = $(this).closest('.elementor-repeater-row-controls').find('.modal-wrapper').clone();
					$modal.attr('id','testimonial_modal');
					$('body').append($modal);
				}
				$modal.addClass('show');

				/* init elements */
				element = $(this).closest('.elementor-repeater-row-controls');
				icons = element.find('.elementor-control-icons [data-setting="icons"]');
				preview = element.find('.elementor-control-icons_preview .elementor-control-raw-html');
				preview_html = $modal.find('.preview_html');
				icon_preview = $modal.find('.icon_preview');
				modal_edit_title = $modal.find('.modal_edit_title');
				modal_edit_link = $modal.find('.modal_edit_link');
				remove_icon = $modal.find('.remove_icon');
				insert_icon = $modal.find('.insert_icon');

				/* load value */
				preview_html.html(icons.val());

				/* Clear */
				icon_preview.html('');
				modal_edit_title.val('').attr('disabled', 'disabled');
				modal_edit_link.val('').attr('disabled', 'disabled');
				remove_icon.attr('disabled', 'disabled');
				insert_icon.attr('disabled', 'disabled');

				/* Sortable */
				Sortable.create($modal.find('.prev_orig')[0],
					{
						group: {
							name: 'icons',
							pull: 'clone',
							put: false
						},
						sort: false
					}
				);

				Sortable.create($modal.find('.remove_wrapper')[0],
					{
						group: {
							name: 'icons',
							pull: false,
							put: true
						},
						sort: false,
						onAdd: function (e) {
							$(e.item).remove();
						}
					}
				);

				Sortable.create(preview_html[0],
					{
						group: {
							name: 'icons',
							pull: false,
							put: true
						},
						sort: true,
						onAdd: function (e) {
							$(e.item).click();
						}
					}
				);


				$modal.on('click', '.close', function () {
					$(this).closest('.modal-wrapper').removeClass('show');
					$modal.off();
				});

				/* Insert array */
				$modal.on('click', '.prev_orig .social', function (e) {
					e.preventDefault();

					el = $(this);
					index = null;

					icon_preview.html(el.clone());
					modal_edit_title.val(el.attr('title')).removeAttr('disabled');
					modal_edit_link.val('#').removeAttr('disabled');

					insert_icon.removeAttr('disabled').val('Insert');

				});

				/* Preview */
				$modal.on('click', '.preview_html .social', function (e) {
					e.preventDefault();

					index = el = $(this);

					icon_preview.html(el.clone());
					modal_edit_title.val(el.attr('title')).removeAttr('disabled');
					modal_edit_link.val(el.attr('href')).removeAttr('disabled');

					remove_icon.removeAttr('disabled');
					insert_icon.removeAttr('disabled').val('Update');
				});

				/* remove_icon */
				$modal.on('click', '.remove_icon', function (e) {
					e.preventDefault();

					index.fadeOut(300,function () {
						index.remove();
						index = null;
					});


					icon_preview.html('');
					modal_edit_title.val(el.attr('')).attr('disabled', 'disabled');
					modal_edit_link.val(el.attr('')).attr('disabled', 'disabled');
					remove_icon.attr('disabled', 'disabled');
					insert_icon.attr('disabled', 'disabled');
				});

				/* Insert Button */
				$modal.on('click', '.insert_icon', function (e) {
					e.preventDefault();
					if (index !== null) {
						index.attr('title', modal_edit_title.val())
							.attr('href', modal_edit_link.val())
							.find('img').attr('title', modal_edit_title.val());
					} else {
						var insert = el.clone();
						insert.attr('title', modal_edit_title.val())
							.attr('href', modal_edit_link.val())
							.find('img').attr('title', modal_edit_title.val());
						preview_html.append(insert);
					}

					icon_preview.html('');
					modal_edit_title.val('').attr('disabled', 'disabled');
					modal_edit_link.val('').attr('disabled', 'disabled');
					remove_icon.attr('disabled', 'disabled');
					insert_icon.attr('disabled', 'disabled');
				});

				/* Save */
				$modal.on('click', '.save_button', function (e) {
					e.preventDefault();

					icons.val(preview_html.html()).trigger('input');
					preview.html(preview_html.html())
					$(this).closest('.modal-wrapper').removeClass('show');
					$modal.off();
				});
			});

			/* Close modal */

		},
	});


	return CoreEditor;
});


jQuery(window).ready(function () {
	if (typeof window.gt3Elementor.Core.Editor === 'function')
		window.gt3Elementor.Core.Editor = window.gt3Elementor.Core.Editor();
} );

(function ($) {
	window.GT3 = window.GT3 || {};
	window.GT3.Editor = window.GT3.Editor || {};
	window.GT3.Editor.Controls = window.GT3.Editor.Controls || {};

	if (window.GT3.Editor.Controls.CoreQuery === undefined) {
		window.GT3.Editor.Controls.CoreQuery = elementor.modules.controls.BaseMultiple.extend({
			ui: {
				postsPerPageControl: '[data-setting="posts_per_page"]',
				ignoreStickyPostsControl: '[data-setting="ignore_sticky_posts"]',
				ignoreStickyPostsControlWrapper: '.ignore_sticky_posts-wrapper',
				orderByControl: '[data-setting="orderby"]',
				orderControl: '[data-setting="order"]',

				taxonomyControl: '[data-setting="taxonomy"]',
				tagsControl: '[data-setting="tags"]',
				authorControl: '[data-setting="author__in"]',
				postControl: '[data-setting="post__in"]',
				selectedPost__in: '.selected_post__in'
			},

			events: function () {
				return {
					'change @ui.postsPerPageControl': 'onChange',
					'change @ui.ignoreStickyPostsControl': 'onChange',
					'change @ui.orderByControl': 'onChange',
					'change @ui.orderControl': 'onChange',

					'change @ui.taxonomyControl': 'onChange',
					'change @ui.tagsControl': 'onChange',
					'change @ui.authorControl': 'onChange',
					'change @ui.postControl': 'onChange',
				};
			},

			onReady: function () {
				this.settings = Object.assign({
					post_tag: "post_tag",
					post_taxonomy: "category",
					post_type: "post",
					showCategory: false,
					showPost: false,
					showTag: false,
					showUser: false,
				}, this.model.get('settings'));

				this.baseurl = window.location.href.substring(0, window.location.href.indexOf('/wp-admin'));

				this.initTaxonomy();
				this.initTag();
				this.initUser();
				this.initPost();
				if (this.settings.post_type === 'post') {
					this.ui.ignoreStickyPostsControl.prop('checked', this.getControlValue('ignore_sticky_posts') === "1");
				} else {
					this.ui.ignoreStickyPostsControlWrapper.hide();
				}
			},

			initTaxonomy: function () {
				if (!this.settings.showCategory) return;
				var that = this,
					control_value = that.getControlValue('taxonomy') || [];

				$.ajax({
					url: ajaxurl,
					method: 'POST',
					data: {
						action: 'gt3_ajax_query',
						gt3_action: 'get-taxonomy',
						taxonomy: this.settings.post_taxonomy,
						include: control_value,
					}
				}).done(function (data) {
					control_value = data.map(function (val) {
						return val.value;
					});
					that.ui.taxonomyControl.select2({
						value: control_value,
						data:
							data.map(function (val) {
								return {
									id: val.value,
									text: val.label,
									selected: control_value.indexOf(val.value.toString()) !== -1,
								};
							}),
						/* closeOnSelect: false,*/
						multiple: true,
						cache: true,
						ajax: {
							url: ajaxurl,
							method: 'POST',
							dataType: 'json',
							delay: 250,
							cache: true,
							data: function (params) {
								return Object.assign({
									action: 'gt3_ajax_query',
									gt3_action: 'get-taxonomy',
									select2: true,
									taxonomy: that.settings.post_taxonomy,
									exclude: that.getControlValue('taxonomy'),
									hide_empty: true,
								}, params);
							},
							processResults: function (data) {
								return {
									results: data.map(function (val) {
										return {
											id: val.value,
											text: val.label
										};
									})
								}
							},
						},
						minimumInputLength: 1,
					});
				});
			},
			initTag: function () {
				if (!this.settings.showTag) return;
				var that = this,
					control_value = that.getControlValue('tags') || [];


				$.ajax({
					url: ajaxurl,
					method: 'POST',
					data:
						{
							action: 'gt3_ajax_query',
							gt3_action: 'get-taxonomy',
							taxonomy: this.settings.post_tag,
							include: control_value
						}
				}).done(function (data) {
					that.ui.tagsControl.select2({
						value: control_value,
						data: data.map(function (val) {
							return {
								id: val.value,
								text: val.label,
								selected: control_value.indexOf(val.value.toString()) !== -1,
							};
						}),
						/* closeOnSelect: false,*/
						multiple: true,
						cache: true,
						ajax: {
							url: ajaxurl,
							method: 'POST',
							dataType: 'json',
							delay: 250,
							cache: true,
							data: function (params) {
								return Object.assign({
									action: 'gt3_ajax_query',
									gt3_action: 'get-taxonomy',
									select2: true,
									taxonomy: that.settings.post_tag,
									exclude: that.getControlValue('tags'),
									hide_empty: true
								}, params);
							},
							processResults: function (data) {
								return {
									results: data.map(function (val) {
										return {
											id: val.value,
											text: val.label
										};
									})
								}

							},
						},
						minimumInputLength: 1,
					});
				});
			},

			initUser: function () {
				if (!this.settings.showUser) return;
				var that = this,
					control_value = that.getControlValue('author__in') || [];


				$.ajax({
					url: ajaxurl,
					method: 'POST',
					data: {
						action: 'gt3_ajax_query',
						gt3_action: 'get-user',
						post_type: this.settings.post_type,
						include: control_value
					}
				}).done(function (data) {
					that.ui.authorControl.select2({
						value: control_value,
						data: data.map(function (val) {
							return {
								id: val.value,
								text: val.label,
								selected: control_value.indexOf(val.value.toString()) !== -1,
							};
						}),
						/* closeOnSelect: false,*/
						multiple: true,
						cache: true,
						ajax: {
							url: ajaxurl,
							method: 'POST',
							dataType: 'json',
							delay: 250,
							cache: true,
							data: function (params) {
								return Object.assign({
									action: 'gt3_ajax_query',
									gt3_action: 'get-user',
									select2: true,
									post_type: that.settings.post_type,
									exclude: that.getControlValue('author__in')
								}, params);
							},
							processResults: function (data) {
								return {
									results: data.map(function (val) {
										return {
											id: val.value,
											text: val.label
										};
									})
								}

							},
						},
						minimumInputLength: 1,
					});
				});
			},
			initPost: function () {
				if (!this.settings.showPost) return;
				var that = this,
					control_value = that.getControlValue('post__in') || [];

				$.ajax({
					url: ajaxurl,
					method: 'POST',
					data: {
						action: 'gt3_ajax_query',
						gt3_action: 'get-post',
						post_type: this.settings.post_type,
						include: control_value
					}
				}).done(function (data) {
					that.ui.postControl.select2({
						value: control_value,
						data: data.map(function (val) {
							return {
								id: val.value,
								text: val.label,
								selected: control_value.indexOf(val.value.toString()) !== -1,
							};
						}),
						/* closeOnSelect: false,*/
						multiple: true,
						cache: true,
						ajax: {
							url: ajaxurl,
							method: 'POST',
							dataType: 'json',
							delay: 250,
							cache: true,
							data: function (params) {
								return Object.assign({
									action: 'gt3_ajax_query',
									gt3_action: 'get-post',
									select2: true,
									post_type: that.settings.post_type,
									exclude: that.getControlValue('post__in'),
								}, params);
							},
							processResults: function (data) {
								return {
									results: data.map(function (val) {
										return {
											id: val.value,
											text: val.label
										};
									})
								}

							},
						},
						minimumInputLength: 1,
					});
				});
			},

			onChange: function (e) {
				var target = e.currentTarget,
					name = target.getAttribute('data-setting'),
					obj = {},
					val = $(target).val();

				if (['taxonomy', 'tags', 'author__in', 'post__in'].indexOf(name.toString()) !== -1) {
					val = val || [];
				} else if (name === 'ignore_sticky_posts') {
					val = target.checked ? "1" : "0";
					this.ui.ignoreStickyPostsControl.prop('checked', val === "1");
				}
				obj[name] = val;

				this.setValue(obj);
				if (!!this.getControlValue('post__in').length) {
					this.ui.selectedPost__in.hide();
				} else {
					this.ui.selectedPost__in.show();
				}
			}
		});
		elementor.addControlView('gt3-elementor-core-query', window.GT3.Editor.Controls.CoreQuery);
	}
})(jQuery);

window.elementor.hooks.addAction('panel/open_editor/widget',function (action, model) {
    var hiddenUpdate = model.attributes.settings.get('hiddenUpdate');
    if (typeof hiddenUpdate !== 'undefined') {
        model.attributes.settings.set('hiddenUpdate',!hiddenUpdate);
    }
    return false;
});

window.elementor.channels.editor.on('change:gt3-core-shoplist:prod_per_row',function(model){
    model.elementSettingsModel.trigger('change',model)
});

(function ($) {
	window.GT3 = window.GT3 || {};
	window.GT3.Editor = window.GT3.Editor || {};
	window.GT3.Editor.Controls = window.GT3.Editor.Controls || {};

	if (window.GT3.Editor.Controls.CoreGallery === undefined) {
		var __ = wp.i18n.__, _n = wp.i18n._n, sprintf = wp.i18n.sprintf;

		var MediaClearButton = Backbone.View.extend({
			className: 'gt3-clear-media button page-title-action',
			tagName: 'a',
			events: {
				click: function () {
					this.controller.clearItems();
				}
			},
			render: function () {
				this.$el.text(__('Clear Gallery', 'gt3_elementor_gallery'));
				return this;
			},

			initialize: function (options) {
				this.controller = options.controller;
				this.render();
			}
		});

		var MediaStatus = Backbone.View.extend({
			tagName: 'span',
			className: 'gt3-media-status align-center',
			loading: true,
			disableLoading: function(){
				this.loading = false;
				this.$el.removeClass('align-center');
				this.render();
			},

			initialize: function (options) {
				this.controller = options.controller;

				if (!this.controller.get('showStatus')) {
					this.$el.hide();
				}

				this.listenTo(this.controller, 'change:length', this.render);

				this.render();
			},

			render: function () {
				var length = this.controller.get('length'),
					html = this.loading ? '<span class="spinner"></span>' : sprintf(_n('%s image selected', '%s images selected', length, 'gt3_elementor_gallery'), length);

				this.$el.html(html);

				return this;

			}
		});
		var MediaButton = Backbone.View.extend({
			className: 'gt3-add-media',
			tagName: 'a',
			events: {
				click: function () {
					if (this._frame) {
						this._frame.dispose();
					}
					var that = this;

					var insertImage = wp.media.controller.Library.extend({
						defaults: _.defaults({
							query: true,
							id: 'insert-image',
							title: __('Select Files', 'gt3_elementor_gallery'),
							multiple: 'add',
							library: wp.media.query({
								post__not_in: this.controller.get('ids'),
								type: 'image'
							}),
							type: 'image'
						}, wp.media.controller.Library.prototype.defaults)
					});

					this._frame = wp.media({
						button: {text: __('Select', 'gt3_elementor_gallery')},
						state: 'insert-image',
						states: [
							new insertImage()
						]
					});

					this._frame.on('select', function() {
						that.controller.addItems(that._frame.state().get('selection').models);
					}, this);

					this._frame.on('open', function () {
						var timeOutID = null;
						that._frame.on('library:selection:add', librarySelectionAdd.bind(this));

						function librarySelectionAdd() {
							var state = that._frame.state(),
								library = state.get('library'),
								selection = state.get('selection'),
								loading = false;

							if (selection && selection.models) {
								loading = _.some(selection.models, function (attachment) {
									return attachment.get('uploading') === true;
								});
								if (loading) {
									clearTimeout(timeOutID);
									timeOutID = setTimeout(librarySelectionAdd.bind(this), 100);
								} else {
									library.add(selection.models)
								}
							}
						}
					});

					this._frame.open();
				}
			},
			render: function () {
				this.$el.text(__('+ Add Media', 'gt3_elementor_gallery'));
				return this;
			},
			initialize: function (options) {
				this.controller = options.controller;

				this.listenTo(this.controller, 'change:full', function () {
					this.$el.toggle(!this.controller.get('full'));
				});

				this.render();
			}
		});
		var MediaItem = Backbone.View.extend({
			tagName: 'div',
			className: 'gt3-image-item',
			initialize: function (options) {
				this.controller = options.controller;
				this.render = this.render.bind(this);
				this.render();
				this.listenTo(this.model, 'change', this.render);
				this.$el.data('id', this.model.cid);
			},

			events: {
				'click .gt3-remove-media': function (e) {
					e.preventDefault();
					e.stopPropagation();
					this.controller.removeItem(this.model);
					return false;
				},

				'click .gt3-edit-media': function (e) {
					e.preventDefault();
					e.stopPropagation();
					if (this._frame) {
						this._frame.dispose();
					}
					this._frame = wp.media({
						frame: 'edit-attachments',
						controller: {
							gridRouter: {
								navigate: function () {
								},
								baseUrl: function () {
								}
							},
						},
						library: this.controller.get('items'),
						model: this.model,
					});
					this._frame.resetRoute = function () {
					};
					this._frame.open();

					return false;
				}
			},

			render: function () {
				var data = Object.assign({}, this.model.attributes);
				var url = data.icon;
				if (data.type === 'image' && data.sizes) {
					url = (data.sizes.thumbnail) ? data.sizes.thumbnail.url : data.sizes.full.url
				} else {
					url = (data.image && data.image.src && data.image.src !== data.icon) ? data.image.src : data.icon
				}
				this.$el.html('<div class="gt3-media-preview" data-id="'+data.id+'">'+
			'<div class="gt3-media-content">'+
				'<div class="centered"><img src="'+url+'"></div>'+
			'</div>'+
		'</div>'+
		'<div class="gt3-overlay"></div>'+
		'<div class="gt3-media-bar">'+
			'<a class="gt3-edit-media" title="'+__('Edit', 'gt3_elementor_gallery')+'" href="'+data.editLink+'" target="_blank"></a>'+
			'<a href="#" class="gt3-remove-media" title="'+__('Remove', 'gt3_elementor_gallery')+'"></a>'+
		'</div>');
				return this;
			}
		});
		var MediaList = Backbone.View.extend({
			tagName: 'div',
			className: 'gt3-media-list',

			initialize: function (options) {
				this._views = {};
				this.controller = options.controller;

				this.setEvents();
				this.render = this.render.bind(this);
			},

			setEvents: function() {
				this.listenTo(this.controller, 'render', this.render);
			},

			initSortable: function() {
				var collection = this.controller.get('items');
				this.$el.sortable({
					tolerance: 'pointer',
					handle: '.gt3-overlay',

					start: function (event, ui) {
						ui.item.data('sortableIndexStart', ui.item.index());
					}.bind(this),

					update: function (event, ui) {
						var model = collection.at(ui.item.data('sortableIndexStart'));

						collection.remove(model, {
							silent: true
						});

						collection.add(model, {
							silent: true,
							at: ui.item.index()
						});

						collection.trigger('reset', collection);
						this.controller.saveMedia();
					}.bind(this),
				});
			},

			render: function() {
				var items = this.controller.get('items');
				this.$el.empty();
				var view;
				if (items && items.length) {
					items.forEach(function (value, key) {
						view = this._views[value.cid] = new MediaItem({
							model: value,
							controller: this.controller
						});
						this.$el.append(view.$el);
					}.bind(this));
				}
				this.initSortable();
			}
		});
		var MediaController = Backbone.Model.extend({
			defaults: {
				maxFiles: 0,
				ids: [],
				forceDelete: false,
				length: 0,
				showStatus: true,
			},

			initialize: function(options) {
				this.set('ids', _.without(_.map(this.get('ids'), Number), 0, -1));
				this.set('items', new wp.media.model.Attachments());
				this.onChange = options.onChange || function () {
				};

				this.countItems = this.countItems.bind(this);
				this.listenTo(this.get('items'), 'add remove reset change', this.countItems);
			},
			countItems: function() {
				var items = this.get('items'),
					length = items.length,
					max = this.get('maxFiles');
				this.set('length', length);
				this.set('full', max > 0 && length >= max);
				this.set('ids', items.collect('id'));
				this.trigger('render');
			},
			isEmpty: function () {
				return !this.get('length');
			},

			load: function() {
				this.starting = true;
				if (!_.isEmpty(this.get('ids'))) {
					this.get('items').props.set({
						query: true,
						include: this.get('ids'),
						orderby: 'post__in',
						order: 'DESC',
						type: 'image',
						perPage: -1
					});
					this.get('items').more();
				}
			},

			removeItem: function(item) {
				this.get('items').remove(item);
			},

			addItems: function(items) {
				var _items = this.get('items'),
					new_items = _items.slice();

				new_items = new_items.concat(items);

				_items.reset(new_items);
			},
			clearItems: function(force) {
				if (force || confirm(__('Are you really want remove all images?', 'gt3_elementor_gallery'))) {
					this.get('items').reset();
				}
			},
			saveMedia: function() {
				var items = this.get('items');
				this.onChange({
					id_a: items.collect('id'),
					ids: items.collect('id').join(','),
					url: items.map(function (modal) {
						return modal.attributes.sizes && modal.attributes.sizes.large && modal.attributes.sizes.large.url || modal.attributes.url
					}),
					caption: items.collect('caption'),
					description: items.collect('description'),
					title: items.collect('title'),
					json: items.toJSON().map(function (item) {
						return {
							alt: item.alt,
							caption: item.caption,
							description: item.description,
							height: item.height,
							width: item.width,
							id: item.id,
							title: item.title,
							url: item.url,
							sizes: item.sizes,
						}
					}),
					items: items,
				});
			},
		});
		var GalleryView = Backbone.View.extend({
			initialize: function (options) {
				this.controller = new MediaController(_.extend(
					{
						ids: options.value.split(','),
						onChange: options.onChange,
					},
					this.$el.data()
				));
				this.firstLoad = options.firstLoad || false;
				this.controllerChangeLength = this.controllerChangeLength.bind(this);

				this.createList();
				this.createAddButton();
				this.createClearButton();
				this.createStatus();

				this.render();
				this.controller.load();
				this.startTimer = null;
				this.controller.on('change:length', this.controllerChangeLength);
				this.startTimer = setTimeout(function () {
					this.controller.starting = false;
					this.status.disableLoading();
				}.bind(this), 1000);
			},
			controllerChangeLength: function() {
				if (this.controller.starting) {
					clearTimeout(this.startTimer);
					this.startTimer = setTimeout(function () {
						this.controller.starting = false;
						this.firstLoad && this.controller.saveMedia();
						this.status.disableLoading();
					}.bind(this), 1000);
				} else {
					this.controller.saveMedia();
				}
			},
			createList: function () {
				this.list = new MediaList({
					controller: this.controller,
				});
			},

			createAddButton: function () {
				this.addButton = new MediaButton({controller: this.controller});
			},

			createClearButton: function () {
				this.clearButton = new MediaClearButton({controller: this.controller});
			},

			createStatus: function () {
				this.status = new MediaStatus({controller: this.controller});
			},

			render: function () {
				this.$el.empty().append(
					jQuery('<div/>', {
						class: 'gt3-controls',
						html: [
							this.addButton.el,
							this.clearButton.el,
							this.status.el,
						]
					}),
					this.list.el
				);
			}
		});

		window.GT3.Editor.Controls.CoreGallery = elementor.modules.controls.BaseData.extend({
			onReady: function () {
				this.onChange = this.onChange.bind(this);

				var value = this.getControlValue();
				try {
					value = JSON.parse(value);
					if (typeof value === 'number') {
						value = value.toString();
					} else {
						value = value.map(function (item) {
							return item.id;
						}).join(',');
					}
				} catch (ex) {

				}
				if (typeof value !== 'string') {
					value = value.join && value.join(',') || '';
				}
				this.media = new GalleryView({
					el: this.$el,
					value: value,
					onChange: this.onChange,
					firstLoad: false,
				});
			},

			onChange: function (e) {
				this.setValue(e && e.ids || '');
			}
		});
		elementor.addControlView('gt3-elementor-core-gallery', window.GT3.Editor.Controls.CoreGallery);
	}
})(jQuery);

(function ($) {
    elementor.hooks.addFilter( 'element/view', function(ChildView, model, $this){
        var View = ChildView;

        if (model.get('widgetType') === 'text-editor') {

            View = ChildView.extend({
                initialize: function () {
                    ChildView.prototype.initialize.apply(this, arguments);
					var editModel = this.getEditModel();
					this.listenTo( editModel.get( 'settings' ), 'change', this.onSettings__Changed );
                },

                customControls: {
                    'typography_font_size': 'elementor-element-custom_font_size',
                    'typography_font_size_tablet': 'elementor-element-custom_font_size_tablet',
                    'typography_font_size_mobile': 'elementor-element-custom_font_size_mobile',

                    'typography_line_height': 'elementor-element-custom_line_height',
                    'typography_line_height_tablet': 'elementor-element-custom_line_height_tablet',
                    'typography_line_height_mobile': 'elementor-element-custom_line_height_mobile',

                    'typography_font_family': 'elementor-element-custom_font_family',
                    'typography_font_weight': 'elementor-element-custom_font_weight',
                    'text_color': 'elementor-element-custom_color',

                },

                render: function () {

					var settings = this.model.get('settings'),
                        customControls = this.customControls;

                    var keysChanged = Object.keys(settings.toJSON()),
                        needed = Object.keys(customControls),
                        $el = this.$el;
                    var intersection = needed.filter(function (x) {
                        return keysChanged.includes(x)
                    });
                    var action = 'addClass';
                    intersection.forEach(function (value) {
                        switch (value) {
                            case 'typography_font_size':
                            case 'typography_font_size_tablet':
                            case 'typography_font_size_mobile':
                            case 'typography_line_height':
                            case 'typography_line_height_tablet':
                            case 'typography_line_height_mobile':
                                action = settings.get(value).size !== '' ? 'addClass' : 'removeClass';
                                $el[action](customControls[value]);
                                break;
                            case 'typography_font_weight':
                            case 'typography_font_family':
                            case 'text_color':
                                action = settings.get(value) !== '' ? 'addClass' : 'removeClass';
                                $el[action](customControls[value]);
                                break;
                        }
                    });

                    ChildView.prototype.render.apply(this, arguments);
                },
				onSettings__Changed: function (model) {
					var keysChanged = Object.keys(model.changed),
                        customControls = this.customControls,
                        needed = Object.keys(customControls),
                        $el = this.$el;
                    var intersection = needed.filter(function (x) {
                        return keysChanged.includes(x)
                    });
                    var action = 'addClass';
                    intersection.forEach(function (value) {
                        switch (value) {
                            case 'typography_font_size':
                            case 'typography_font_size_tablet':
                            case 'typography_font_size_mobile':
                            case 'typography_line_height':
                            case 'typography_line_height_tablet':
                            case 'typography_line_height_mobile':
                                action = model.changed[value].size !== '' ? 'addClass' : 'removeClass';
                                $el[action](customControls[value]);
                                break;
                            case 'typography_font_weight':
                            case 'typography_font_family':
                            case 'text_color':
                                action = model.changed[value] !== '' ? 'addClass' : 'removeClass';
                                $el[action](customControls[value]);
                                break;
                        }
                    });
                }
            });
        }

        if (model.get('widgetType') === 'gt3-core-imageprocessbar') {
        	View = ChildView.extend({
                initialize: function () {
                    ChildView.prototype.initialize.apply(this, arguments);
					var editModel = this.getEditModel();
					this.listenTo( editModel.get( 'settings' ), 'change', this.onSettings__Changed );
                },

                customControls: {
                    'tab_color': 'elementor-element-custom_font_size',
                    'typography_font_size_tablet': 'elementor-element-custom_font_size_tablet',
                    'typography_font_size_mobile': 'elementor-element-custom_font_size_mobile',

                    'typography_line_height': 'elementor-element-custom_line_height',
                    'typography_line_height_tablet': 'elementor-element-custom_line_height_tablet',
                    'typography_line_height_mobile': 'elementor-element-custom_line_height_mobile',

                    'typography_font_family': 'elementor-element-custom_font_family',
                    'typography_font_weight': 'elementor-element-custom_font_weight',
                    'text_color': 'elementor-element-custom_color',

                },

                hexToRgb: function (hex) {
                	if (hex.indexOf('#') !== -1) {
                		var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
				  		return result ? 'rgb('+parseInt(result[1], 16)+','+parseInt(result[2], 16)+','+parseInt(result[3], 16)+')' : hex;
                	}else{
                		return hex;
                	}

				},

                replace_rgb_to_rgba: function (color,alfa){
					if(color.indexOf('a') == -1){
					    var result = color.replace(')', ', '+alfa+')').replace('rgb', 'rgba');
					    return result;
					}else{
						result = color.replace(/[\d\.]+\)$/g, alfa+')')
						return result;
					}
				},

                render: function () {
                    var settings = this.model.get('settings'),
                    	hexToRgb = this.hexToRgb,
                    	replace_rgb_to_rgba = this.replace_rgb_to_rgba,
                        customControls = this.customControls;

                    var keysChanged = Object.keys(settings.toJSON()),
                        needed = Object.keys(customControls),
                        $el = this.$el;
                    var intersection = needed.filter(function (x) {
                        return keysChanged.includes(x)
                    });
                    intersection.forEach(function (value) {
                        switch (value) {
                            case 'tab_color':
                                if (settings.get(value) !== '') {
                                	setTimeout(function () {
				                        jQuery($el).find('.gt3_process_item__circle_line_before').css({
											'background-image': 'radial-gradient('+replace_rgb_to_rgba(hexToRgb(settings.get(value)),0.5)+' 15%, transparent 30%), radial-gradient('+replace_rgb_to_rgba(hexToRgb(settings.get(value)),0.8)+' 15%, transparent 30%),     radial-gradient('+settings.get(value)+' 15%, transparent 30%),     radial-gradient('+replace_rgb_to_rgba(hexToRgb(settings.get(value)),0.8)+' 15%, transparent 30%),     radial-gradient('+replace_rgb_to_rgba(hexToRgb(settings.get(value)),0.5)+' 15%, transparent 30%),  radial-gradient('+replace_rgb_to_rgba(hexToRgb(settings.get(value)),0.5)+' 15%, transparent 30%)'
										})

										jQuery($el).find('.gt3_process_item__circle_line_after').css({
											'background-image': 'radial-gradient('+replace_rgb_to_rgba(hexToRgb(settings.get(value)),0.5)+' 15%, transparent 30%), radial-gradient('+replace_rgb_to_rgba(hexToRgb(settings.get(value)),0.8)+' 15%, transparent 30%),     radial-gradient('+settings.get(value)+' 15%, transparent 30%),     radial-gradient('+replace_rgb_to_rgba(hexToRgb(settings.get(value)),0.8)+' 15%, transparent 30%),     radial-gradient('+replace_rgb_to_rgba(hexToRgb(settings.get(value)),0.5)+' 15%, transparent 30%),  radial-gradient('+replace_rgb_to_rgba(hexToRgb(settings.get(value)),0.5)+' 15%, transparent 30%)'
										})
				                    }, 600);

                                }

                                break;
                        }
                    });

                    ChildView.prototype.render.apply(this, arguments);
                },
				onSettings__Changed: function (model) {
                    var keysChanged = Object.keys(model.changed),
                    	hexToRgb = this.hexToRgb,
                    	replace_rgb_to_rgba = this.replace_rgb_to_rgba,
                        customControls = this.customControls,
                        needed = Object.keys(customControls),
                        $el = this.$el;
                    var intersection = needed.filter(function (x) {
                        return keysChanged.includes(x)
                    });
                    intersection.forEach(function (value) {
                        switch (value) {
                            case 'tab_color':
                                if (model.changed[value] !== '') {
                                	jQuery($el).find('.gt3_process_item__circle_line_before').css({
										'background-image': 'radial-gradient('+replace_rgb_to_rgba(hexToRgb(model.changed[value]),0.5)+' 15%, transparent 30%), radial-gradient('+replace_rgb_to_rgba(hexToRgb(model.changed[value]),0.8)+' 15%, transparent 30%),     radial-gradient('+model.changed[value]+' 15%, transparent 30%),     radial-gradient('+replace_rgb_to_rgba(hexToRgb(model.changed[value]),0.8)+' 15%, transparent 30%),     radial-gradient('+replace_rgb_to_rgba(hexToRgb(model.changed[value]),0.5)+' 15%, transparent 30%),  radial-gradient('+replace_rgb_to_rgba(hexToRgb(model.changed[value]),0.3)+' 15%, transparent 30%)'
									})

									jQuery($el).find('.gt3_process_item__circle_line_after').css({
										'background-image': 'radial-gradient('+replace_rgb_to_rgba(hexToRgb(model.changed[value]),0.5)+' 15%, transparent 30%), radial-gradient('+replace_rgb_to_rgba(hexToRgb(model.changed[value]),0.8)+' 15%, transparent 30%),     radial-gradient('+model.changed[value]+' 15%, transparent 30%),     radial-gradient('+replace_rgb_to_rgba(hexToRgb(model.changed[value]),0.8)+' 15%, transparent 30%),     radial-gradient('+replace_rgb_to_rgba(hexToRgb(model.changed[value]),0.5)+' 15%, transparent 30%),  radial-gradient('+replace_rgb_to_rgba(hexToRgb(model.changed[value]),0.3)+' 15%, transparent 30%)'
									})
                                }

                                break;
                        }
                    });
                }
            });
        }

        return View;
    } );
})(jQuery);


