/*!
 Version: 1.0
 Author: GT3 Themes
 Website: https//gt3themes.com
 */

'use strict';

jQuery(function ($) {
	var __ = wp.i18n.__,
		_n = wp.i18n._n,
		sprintf = wp.i18n.sprintf;

	window.gt3 = window.gt3 || {};

	var views = gt3.views = gt3.views || {},
		models = gt3.models = gt3.models || {},
		Controller, MediaField, MediaList, MediaItem, MediaButton, MediaClearButton, ImageField, MediaStatus;

	Controller = models.Controller = Backbone.Model.extend({
		defaults: {
			maxFiles: 0,
			ids: [],
			mimeType: 'images',
			forceDelete: false,
			length: 0
		},

		initialize: function (options) {
			if ('$input' in options) this.$input = options.$input;
			this.set('ids', _.without(_.map(this.get('ids'), Number), 0, -1));
			this.set('items', new wp.media.model.Attachments());

			this.listenTo(this.get('items'), 'add remove reset change', this.countItems);

			this.on('destroy', function (e) {
				if (this.get('forceDelete')) {
					this.get('items').each(function (item) {
						item.destroy();
					});
				}
			});
		},

		countItems: function () {
			var items = this.get('items'),
				length = items.length,
				max = this.get('maxFiles');
			this.set('length', length);
			this.set('full', max > 0 && length >= max);
			this.set('ids', items.collect('id'));
			this.trigger('render');
		},

		load: function () {
			this.starting = true;
			if (!_.isEmpty(this.get('ids'))) {
				this.get('items').props.set({
					query: true,
					include: this.get('ids'),
					orderby: 'post__in',
					order: 'DESC',
					type: this.get('mimeType'),
					perPage: this.get('maxFiles') || -1
				});
				this.get('items').more();
			}
			setTimeout(function () {
				this.starting = false;
				this.get('items').more();
			}.bind(this), 1000);
		},

		removeItem: function (item) {
			this.get('items').remove(item);
			if (this.get('forceDelete')) {
				item.destroy();
			}
			this.saveToInput();
		},

		addItems: function (items) {
			var _items = this.get('items'),
				new_items = _items.slice();
			items = items.filter(function (item) {
				return item.attributes.sizes
			});

			new_items = items.concat(...new_items);

			_items.reset(new_items);
		},
		clearItems: function (force) {
			if (force || confirm(__("Do you really want to remove all images?", "gt3pg_pro"))) {
				this.get('items').reset();
				this.saveToInput();
			}
		},
		saveToInput: function () {
			var items = this.get('items').collect('id');
			this.$input.trigger('focus').val(items.join(',')).trigger('change').trigger('blur').trigger('input');
		},
		getItemsIds: function () {
			return this.get('items').collect('id');
		}
	});

	MediaField = views.MediaField = Backbone.View.extend({
		initialize: function (options) {
			var that = this;
			this.$input = $(options.input);
			this.controller = new Controller(_.extend(
				{
					fieldName: this.$input.attr('name'),
					ids: this.$input.val().split(','),
					$input: this.$input,
				},
				this.$el.data()
			));
			this.controllerChangeLength = this.controllerChangeLength.bind(this);
			this.endStarting = this.endStarting.bind(this);
			// this.dispose = this.dispose.bind(this);


			this.createList();
			this.createAddButton();
			this.createClearButton();
			this.createStatus();

			this.$input.on('remove', function () {
				that.controller.destroy();
			});

			this.controller.on('change:length', this.controllerChangeLength);

			this.render();
			this.controller.load();
			this.startTimer = null;
			this.controller.on('change:length', this.controllerChangeLength);
			this.startTimer = this.controller.starting && setTimeout(this.endStarting, 1000);
		},

		endStarting: function () {
			this.controller.starting = false;
			this.firstLoad && this.controller.saveToInput();
		},

		controllerChangeLength: function () {
			if (this.controller.starting) {
				clearTimeout(this.startTimer);
				this.startTimer = setTimeout(this.endStarting, 1000);
			} else {
				this.controller.saveToInput();
			}
		},

		createList: function () {
			this.list = new MediaList({controller: this.controller});
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
				this.addButton.el,
				this.clearButton.el,
				this.status.el,
				this.list.el
			);
		}
	});

	MediaList = views.MediaList = Backbone.View.extend({
		tagName: 'div',
		className: 'gt3-media-list',

		addItemView: function (item) {
			var view = this._views[item.cid] = new this.itemView({
				model: item,
				controller: this.controller
			});

			if (this.controller.starting === true) {
				this.$el.append(view.el);
			} else {
				this.$el.prepend(view.el);
			}
		},

		removeItemView: function (item) {
			if (this._views[item.cid]) {
				this._views[item.cid].remove();
				delete this._views[item.cid];
			}
		},

		initialize: function (options) {
			this._views = {};
			this.controller = options.controller;
			this.itemView = options.itemView || MediaItem;

			this.setEvents();

			this.initSortable();
		},

		setEvents: function () {
			// this.listenTo(this.controller.get('items'), 'add', this.addItemView);
			// this.listenTo(this.controller.get('items'), 'remove', this.removeItemView);
			this.listenTo(this.controller, 'render', this.render);
		},

		initSortable: function () {
			var that = this;
			var collection = this.controller.get('items');
			this.$el.sortable({
				tolerance: 'pointer',
				handle: '.gt3-overlay',

				// Record the initial `index` of the dragged model.
				start: function (event, ui) {
					ui.item.data('sortableIndexStart', ui.item.index());
				},

				update: function (event, ui) {
					var model = collection.at(ui.item.data('sortableIndexStart'));

					collection.remove(model, {
						silent: true
					});
					collection.add(model, {
						silent: true,
						at: ui.item.index()
					});

					// collection.trigger('reset', collection);
					that.controller.saveToInput();
				}
			});
		},

		render: function () {
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

	/***
	 * MediaStatus
	 * Tracks status of media field if maxStatus is greater than 0
	 */
	MediaStatus = views.MediaStatus = Backbone.View.extend({
		tagName: 'span',
		className: 'gt3-media-status',
		// template: wp.template('gt3-media-status'),

		//Initialize
		initialize: function (options) {
			this.controller = options.controller;

			if (!this.controller.get('showStatus')) {
				this.$el.hide();
			}

			this.listenTo(this.controller, 'change:length', this.render);

			this.render();
		},

		render: function () {
			var length = this.controller.get('length');
			this.$el.html(length ? sprintf(_n("%s file", "%s files", length, "gt3pg_pro"), length) : __("No files", "gt3pg_pro"));
		}
	});

	/***
	 * Media Button
	 * Selects and adds ,edia to controller
	 */
	MediaButton = views.MediaButton = Backbone.View.extend({
		className: 'gt3-add-media button button-primary',
		tagName: 'a',
		events: {
			click: function () {
				// Destroy the previous collection frame.
				if (this._frame) {
					this._frame.dispose();
				}
				var that = this;

				var insertImage = wp.media.controller.Library.extend({
					defaults: _.defaults({
						query: true,
						id: 'insert-image',
						title: __("Select Files", "gt3pg_pro"),
						multiple: 'add',
						library: wp.media.query({
							post__not_in: this.controller.get('ids'),
							type: 'image'
						}),
						type: 'image'
					}, wp.media.controller.Library.prototype.defaults)
				});

				this._frame = wp.media({
					button: {text: __("Select", "gt3pg_pro")},
					state: 'insert-image',
					states: [
						new insertImage()
					]
				});

				this._frame.on('select', function () {
					that.controller.addItems(that._frame.state().get('selection').models.filter((model) => model.get('sizes')));
				}, this);

				this._frame.on('open', function () {
					let timeOutID = null;
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

				/*this._frame.on('select', function () {
					var selection = this._frame.state().get('selection');
					this.controller.addItems(selection.models);
				}, this);*/

				this._frame.open();
			}
		},
		render: function () {
			this.$el.text(__("+ Add Media", "gt3pg_pro"));
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

	MediaClearButton = views.MediaClearButton = Backbone.View.extend({
		className: 'gt3-clear-media button page-title-action',
		tagName: 'a',
		events: {
			click: function () {
				this.controller.clearItems();
			}
		},
		render: function () {
			this.$el.text(__("Clear Gallery", "gt3pg_pro"));
			return this;
		},

		initialize: function (options) {
			this.controller = options.controller;

			this.render();
		}
	});

	MediaItem = views.Index = Backbone.View.extend({
		tagName: 'div',
		className: 'gt3-image-item',
		initialize: function (options) {
			this.controller = options.controller;
			this.render();
			this.listenTo(this.model, 'change', function () {
				this.render();
			});

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
			const data = Object.assign({}, this.model.attributes);
			let url = data.icon,
				title = data.title;
			if (data.type === 'image' && data.sizes) {
				url = (data.sizes.thumbnail) ? data.sizes.thumbnail.url : data.sizes.full.url
			} else {
				url = (data.image && data.image.src && data.image.src !== data.icon) ? data.image.src : data.icon
			}
			this.$el.html(`
		<div class="gt3-media-preview" data-id="${data.id}" style="border: 1px solid #e0e0e0" >
			<div class="gt3-media-content">
				<div class="centered"><img src="${url}" title="${title}" ></div>
			</div>
		</div>
		<div class="gt3-overlay" title="${title}" ></div>
		<div class="gt3-media-bar">
			<a class="gt3-edit-media" title="${__("Edit", "gt3pg_pro")}" href="${data.editLink}" target="_blank"></a>
			<a href="#" class="gt3-remove-media" title="${__("Remove", "gt3pg_pro")}"></a>
		</div>`);
			return this;
		}
	});

	ImageField = views.ImageField = MediaField.extend({
		createList: function () {
			this.list = new MediaList({
				controller: this.controller,
				itemView: MediaItem
			});
		}
	});

	function initImageField(event, element) {
		new ImageField({input: element, el: $(element).siblings('div.gt3-media-view')});
	}

	$('input.gt3-image_advanced').each(initImageField);
});
