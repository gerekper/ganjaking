/**
 * Javascript support for the Dashboard pages.
 * All module/page-specific functions are inside this file. It depends on the
 * functions defined in `dashboard.js`
 *
 * @since  4.0.0
 */

/*
 * Initialize the Dashboard once the page is fully loaded.
 */
jQuery(function initModules() {
	// Internal variables, shared between functions.
	var items  // used for plugins/themes list; each item is a project
		,rows  // used for plugins/themes; each row is a project-category
		,lastFilter  // used for the filter text field on plugins/themes page.
		,filterProgress // same as lastFilter.
		,wpMediaFrame // used for wpMedia modal reference.
	;


	// ------------------------------------------------------------------------
	// MAIN DASHBOARD PAGE
	function initDashboard() {
		var projectSearch = jQuery('#project-search'),
				loginForm = jQuery('#wpmudui-login-form');

		projectSearch.on('search', searchProjects);

		loginForm.on("click", ".one-click", disableFields);

	}

	// ------------------------------------------------------------------------
	// PLUGINS PAGE
	function initPlugins() {
		var txtFilter = jQuery('input.search')
			,sortBy = jQuery('#sel_sort')
			,showCat = jQuery('#sel_category')
		;

		items = jQuery('.project-box');
		rows = jQuery('.row-projects');

		jQuery('.wpmud').on('click', '.project-action [data-action]', ajaxProjectAction);
		jQuery('.wpmud').on('click', '.project-box .show-info', showProjectInfo);
		jQuery('.wpmud').on('click', '.project-box .has-update', showUpdateInfo);
		jQuery('body').on('click', '.show-project-update', showUpdateInfo);
		jQuery('body').on('click', '.show-project-changelog', showProjectChangelog);
		jQuery('body').on('click', '.title-action [data-action]', ajaxProjectAction);
		jQuery(document).on('wpmu:show-project', updateProjectBox);
		jQuery(document).on('wpmu:update-done', function() { refreshUpdateCounter('plugin', -1); } );
		txtFilter.on('keyup', filterProjects);
		txtFilter.on('search', filterProjects);
		txtFilter.on('blur', filterProjects);
		sortBy.on('change', changeCategory);
		showCat.on('change', changeCategory);

		alphabeticalIndex(); // This will change category, count projects and sort them.

		// Parse the local routes after a short delay.
		window.setTimeout(function() {
			switch (WDP.localRoutes.action) {
				case 'pid':    showProjectInfo(WDP.localRoutes.param); break;
				case 'update': showUpdateInfo(WDP.localRoutes.param); break;
			}
		}, 20);

		// Display the project info popup.
		function showProjectInfo(param) {
			if (!param) { return; }
			showPopup.call(this, 'info', param);
		}

		// Display the project update info popup.
		function showUpdateInfo(param) {
			if (!param) { return; }
			showPopup.call(this, 'update', param);
		}

		// Display the project update info popup.
		function showProjectChangelog(param) {
			if (!param) { return; }
			showPopup.call(this, 'changelog', param);
		}
	}

	// ------------------------------------------------------------------------
	// THEMES PAGE
	function initThemes() {
		var txtFilter = jQuery('input.search')
			,sortBy = jQuery('#sel_sort')
		;

		items = jQuery('.project-box');
		rows = jQuery('.row-projects');

		jQuery('.wpmud').on('click', '.project-action [data-action]', ajaxProjectAction);
		jQuery('.wpmud').on('click', '.project-box .show-info', showProjectInfo);
		jQuery('.wpmud').on('click', '.project-box .has-update', showUpdateInfo);
		jQuery('body').on('click', '.show-project-update', showUpdateInfo);
		jQuery('body').on('click', '.show-project-changelog', showProjectChangelog);
		jQuery('body').on('click', '.title-action [data-action]', ajaxProjectAction);
		jQuery(document).on('wpmu:show-project', updateProjectBox);
		jQuery(document).on('wpmu:update-done', function() { refreshUpdateCounter('theme', -1); } );
		jQuery(document).on('wpmu:update-done', closeUpfrontUpgrade);
		txtFilter.on('keyup', filterProjects);
		txtFilter.on('search', filterProjects);
		txtFilter.on('blur', filterProjects);
		sortBy.on('change', changeCategory);

		alphabeticalIndex(); // This will change category, count projects and sort them.

		// Parse the local routes after a short delay.
		window.setTimeout(function() {
			switch (WDP.localRoutes.action) {
				case 'pid':    showProjectInfo(WDP.localRoutes.param); break;
				case 'update': showUpdateInfo(WDP.localRoutes.param); break;
			}
		}, 20);

		// Display the project info popup.
		function showProjectInfo(param) {
			if (!param) { return; }
			showPopup.call(this, 'info', param);
		}

		// Display the project update info popup.
		function showUpdateInfo(param) {
			if (!param) { return; }
			showPopup.call(this, 'update', param);
		}

		// Display the project update info popup.
		function showProjectChangelog(param) {
			if (!param) { return; }
			showPopup.call(this, 'changelog', param);
		}
	}

	// ------------------------------------------------------------------------
	// SUPPORT PAGE
	function initSupport() {
		var searchForm = jQuery("#support-search");
		searchForm.submit(function() {
			var query = jQuery('#support-search input[name="q"]').val();
			window.open('https://premium.wpmudev.org/forums/search.php?q='+query+'&forum=support', '_blank');
			return false;
		});
		searchForm.on('click', '.search-icon', function() {
			searchForm.submit();
		});
	}

	// ------------------------------------------------------------------------
	// SETTINGS PAGE
	function initSettings() {
		var userSearch = jQuery('#user-search'),
			btnAdd = jQuery('#user-add'),
			autoUpdate = jQuery('#chk_autoupdate'),
		    mediaLibBtn = jQuery('.wpmud-media-library'),
		    clearImageBtn = jQuery('.wpmud-clear-image-input'),
		    body = jQuery('body')
		;

		userSearch.on('search', searchUsers);
		userSearch.on('item:select', function() { formState(true); });
		userSearch.on('item:clear', function() { formState(false); });
		autoUpdate.on('change', saveSetting);
		formState(false);

		function formState(state) {
			if (state) {
				btnAdd.prop('disabled', false).removeClass('disabled');
			} else {
				btnAdd.prop('disabled', true).addClass('disabled');
			}
		}

		mediaLibBtn.on('click', function (event) {
			event.preventDefault();

			// If the media frame already exists, reopen it.
			if (wpMediaFrame) {
				wpMediaFrame.open();
				return false;
			}

			// Create a new media frame
			wpMediaFrame = wp.media({
				title: mediaLibBtn.data('frame-title'),
				button: {
					text: mediaLibBtn.data('button-text')
				},
				multiple: false
			});

			// When an image is selected in the media frame...
			wpMediaFrame.on('select', function () {

				// Get media attachment details from the frame state
				var attachment = wpMediaFrame.state().get('selection').first().toJSON(),
				    input      = $('#' + mediaLibBtn.data('input-id')),
				    preview    = $('#' + mediaLibBtn.data('preview-id'))
				;

				// Send the attachment URL to our custom image input field.
				preview.css('background-image', 'url(' + attachment.url + ')');
				// Send the attachment url to our input
				input.val(attachment.url);
			});

			wpMediaFrame.on('open', function () {
				if (body.hasClass('wpmud')) {
					body.removeClass('wpmud')
				}
			});

			wpMediaFrame.on('close', function () {
				if (!body.hasClass('wpmud')) {
					body.addClass('wpmud')
				}
			});

			// Finally, open the modal on click
			wpMediaFrame.open();
		});

		clearImageBtn.on('click', function (event) {
			event.preventDefault();
			var input   = $('#' + mediaLibBtn.data('input-id')),
			    preview = $('#' + mediaLibBtn.data('preview-id'))
			;
			// Send the attachment URL to our custom image input field.
			preview.css('background-image', 'url()');
			// Send the attachment url to our input
			input.val('');
		});

	}

	// ------------------------------------------------------------------------
	// Run the initialization functions.
	if (jQuery('.wpmud.wpmud-dashboard').length) { initDashboard(); }
	if (jQuery('.wpmud.wpmud-plugins').length) { initPlugins(); }
	if (jQuery('.wpmud.wpmud-themes').length) { initThemes(); }
	if (jQuery('.wpmud.wpmud-support').length) { initSupport(); }
	if (jQuery('.wpmud.wpmud-settings').length) { initSettings(); }



	//
	// ------------------------------------------------------------------------
	// INERNAL HELPER FUNCTIONS USED ABOVE
	// ------------------------------------------------------------------------
	//



	// Refresh the contents of a project box.
	function updateProjectBox(ev, box, htmlCode) {
		box.replaceWith(htmlCode);
		items = jQuery('.project-box');
		alphabeticalIndex();
	}

	// Refresh the values in the update-counter badges.
	function refreshUpdateCounter(type, step, action) {
		var menu = jQuery('#adminmenu .toplevel_page_wpmudev'),
			badge_total = menu.find('.total-updates'),
			badge_details = menu.find('.' + type + '-updates'),
			count = 0;

		if (badge_details.length) {
			count = parseInt(badge_details.first().find('.countval').text());
			if ('set' === action) { step = step - count; }
			count += step;
			if (count < 0) { count = 0; }
			if ( count ) {
				badge_details.removeClass().addClass(type + '-updates update-plugins count-' + count);
				badge_details.find('.countval').text(count);
			} else {
				badge_details.hide();
			}
		}

		if (badge_total.length) {
			count = parseInt(badge_total.first().find('.countval').text());
			count += step;
			if (count < 0) { count = 0; }
			if ( count ) {
				badge_total.removeClass().addClass('total-updates update-plugins count-' + count);
				badge_total.find('.countval').text(count);
			} else {
				badge_total.hide();
			}
			badge_total.removeAttr('title');
		}

		if ('set' === action ) { return true; }//don't mess with core bubbles

		//handle core count bubbles
		var id = ( 'theme' == type ) ? 'appearance' : 'plugins',
			menu = jQuery('#menu-'+id),
			badge_details = menu.find('.update-plugins'),
			badge_details_count = badge_details.first().find('.'+type+'-count'),
			badge_total = jQuery('#wp-admin-bar-updates .ab-item'),
			count = 0;

		if (badge_details.length) {
			count = parseInt(badge_details_count.text());
			count += step;
			if (count < 0) { count = 0; }
			if ( count ) {
				badge_details.removeClass().addClass('update-plugins count-' + count);
				badge_details_count.text(count);
			} else {
				badge_details_count.text(count);
				badge_details.hide();
			}
		}

		//admin bar updated icon
		if (badge_total.length) {
			orig_count = parseInt(badge_total.first().find('.ab-label').text());
			count = orig_count + step;
			if (count < 0) { count = 0; }
			if ( count ) {
				badge_total.find('.ab-label').text(count);
			} else {
				badge_total.find('.ab-label').hide();
			}
			badge_total.find('.screen-reader-text').remove();
			badge_total.removeAttr('title');
		}
	}

	function closeUpfrontUpgrade( type, pid ) {
		if ( '938297' == pid ) {
			jQuery('.frash-notice.active').hide();
		}
	}

	// Handle Ajax actions of the project action button.
	function ajaxProjectAction(el) {
		var jq = jQuery(this),
			box = jq.closest('.project-box'),
			actions = jQuery('.project-action a'),
			data = {},
			res = {"scope":this, "param":el, "func":ajaxProjectAction},
			theAction = jq.data('action'),
			msg = jq.data("loading");

		if ("project-install" === theAction)  {
			jQuery(document).trigger('wpmu:before-update', [res]);
			if (res && res.cancel) { return false; }
		}

		// Disable all project actions.
		actions.each(function() {
			var el = jQuery(this);
			if (el.hasClass('disabled')) { return true; }
			if (el.prop('disabled')) { return true; }
			el.data('temp-disabled', '1')
				.prop('disabled', true)
				.addClass('disabled');
		});

		if (!box.length && jq.data('project')) {
			var box_sel = '.project-box.project-' + jq.data('project');
			box = jQuery(box_sel);
		}
		if (! box.length) { return; }

		data.action = 'wdp-' + theAction;
		data.hash = jq.data('hash');
		data.pid = box.data('project');

		// The '+' is intentional, to convert the boolean into 1/0.
		data.is_network = + (jQuery('body').hasClass('network-admin'));

		jq.loading(true, msg);
		jQuery.post(
			window.ajaxurl,
			data,
			function(response) {
				WDP.closeOverlay(); // close overlay, if any is open.

				if (handleError(response)) { return; }

				jQuery(document).trigger(
					'wpmu:show-project',
					[box, response.data.html]
				);

				// Optionally update other boxes.
				if (response.data.other) {
					for (var pid2 in response.data.other) {
						if (! response.data.other.hasOwnProperty(pid2)) {
							continue;
						}
						var box2 = jQuery('.project-box.project-' + pid2);
						jQuery(document).trigger(
							'wpmu:show-project',
							[box2, response.data.other[pid2]]
						);
					}
				}

				// Optionally refresh the admin menu.
				if (response.data.admin_menu) {
					var html = jQuery(response.data.admin_menu);
					if (! html.find("#adminmenu").length) {
						reloadPage();
						return;
					} else {
						jQuery("#adminmenu").html( html.find("#adminmenu").html() );
						jQuery(window).trigger("resize");
					}
				}

				changeCategory();

				// Display follow-up overlay if we got one.
				if (response.data.overlay) {
					WDP.showOverlay(response.data.overlay);
				}
			},
			'json'
		)
		.always(function() {
			jq.loading(false);

			// Disable all project actions.
			actions.each(function() {
				var el = jQuery(this);
				if ('1' !== el.data('temp-disabled')) { return true; }
				el.data('temp-disabled', false)
					.prop('disabled', false)
					.removeClass('disabled');
			});
		}).fail(function(xhr) {
			handleError(xhr);
		});

		return false;
	}

	// Hide projects based on text filter.
	function filterProjects() {
		var q = jQuery('input.search').val(),
			words = q.split(' ');

		if (filterProgress) {
			window.setTimeout(filterProjects, 10);
			return;
		}
		filterProgress = true;

		items.removeClass('filter-hide');

		// Quick solution to show all projects.
		if (! q.length) {
			items.show();
			countProjects();
			filterProgress = false;
			lastFilter = '';
			return;
		}

		if (lastFilter === q) {
			filterProgress = false;
			return;
		}
		lastFilter = q;

		for (var i = 0; i < words.length; i += 1) {
			var word = words[i].toLowerCase();

			for (var j = 0; j < items.length; j += 1) {
				var el = jQuery( items[j] );
				if (el.hasClass('filter-hide')) { continue; }

				var title = el.find('h4').text().toLowerCase();

				if (-1 === title.indexOf(word)) {
					el.addClass('filter-hide');
					continue;
				}
			}
		}

		countProjects();
		filterProgress = false;
	}

	// Count projects and hide empty sections.
	function countProjects() {
		var cat = jQuery('#sel_category').val(),
			count_updates = jQuery('.project-box[data-hasupdate=1]').length;

		items.show();
		items.filter('.filter-hide').hide();
		items.filter('.tag-hide').hide();

		for (var i = 0; i < rows.length; i += 1) {
			var row = jQuery(rows[i]),
				count = row.find('.project-box').not('.filter-hide').not('.tag-hide').length,
				el_count = row.find('.section-title .count');

			el_count.text(count);
			if (! count) {
				row.addClass('empty');
			} else {
				row.removeClass('empty');
			}
			row.removeClass('collapsed expanded');
			row.addClass('expanded');
		}


		if (jQuery('body').hasClass('wpmud-plugins')) {
			refreshUpdateCounter('plugin', count_updates, 'set');
		} else if (jQuery('body').hasClass('wpmud-themes')) {
			refreshUpdateCounter('theme', count_updates, 'set');
		}


		sortProjects();
	}

	// Display Projects of a certain category.
	function changeCategory() {
		var cat = jQuery('#sel_category').val(),
			sort = jQuery('#sel_sort').val(),
			catName = jQuery('#sel_category').find('option:selected').text(),
			rowUpdates = jQuery('.row-projects.updates .content-inner'),
			rowInstalled = jQuery('.row-projects.installed .content-inner'),
			rowUninstalled = jQuery('.row-projects.uninstalled .content-inner'),
			boxUpdates = rowUpdates.closest('.row'),
			boxInstalled = rowInstalled.closest('.row'),
			boxUninstalled = rowUninstalled.closest('.row'),
			titleUpdates = boxUpdates.find('.section-title .title'),
			titleInstalled = boxInstalled.find('.section-title .title'),
			titleUninstalled = boxUninstalled.find('.section-title .title'),
			showUpdates = false
			;

		// This is always used for the Themes page (no category there).
		if (!cat) { cat = '0'; }

		if ('0' === cat && 'def' === sort && rowUpdates.length) {
			showUpdates = true;
		}

		// 1. Update the titles/toggle buttons.
		if (boxInstalled.length && boxUninstalled.length) {
			if ('0' === cat) {
				titleUninstalled.text(titleUninstalled.data('title').replace( /%s/, '' ));
				titleInstalled.text(titleInstalled.data('title').replace( /%s/, '' ));
			} else {
				titleUninstalled.text(titleUninstalled.data('title').replace( /%s/, catName ));
				titleInstalled.text(titleInstalled.data('title').replace( /%s/, catName ));
			}
		}

		items.removeClass('tag-hide');

		// Move projects to correct list and hide if needed.
		items.each(function() {
			var item = jQuery(this);

			if (showUpdates && item.data('hasupdate')) {
				rowUpdates.append(item);
			} else if (!item.data('installed') && rowUninstalled.length) {
				rowUninstalled.append(item);
			} else {
				rowInstalled.append(item);
			}

			if ('0' !== cat) {
				if (! item.data('tag-' + cat)) {
					item.addClass('tag-hide');
				}
			}
		});

		lastFilter = '';
		filterProjects();
	}

	// Sort Projects by current sort option.
	function sortProjects() {
		var option = jQuery('#sel_sort').val(),
			sort_dir = 'DESC';

		// Def/Popularity are equal, except def will display updates in top.
		if ('def' === option) {
			option = 'order';
			sort_dir = 'ASC';
		}

		for (var i = 0; i < rows.length; i += 1) {
			var row = jQuery('.content-inner', rows[i]),
				rowItems = row.find('.project-box'),
				order = rowItems.sort(function(a, b) {
					var valA = jQuery(a).data(option),
						valB = jQuery(b).data(option);
					if (! isNaN(valA)) {
						valA = parseInt(valA);
						valB = parseInt(valB);
					}
					if (valA > valB) {
						return ('ASC' == sort_dir ? 1 : -1);
					} else {
						return ('ASC' == sort_dir ? -1 : 1);
					}
				});

			for (var j = 0; j < rowItems.length; j += 1) {
				jQuery(rowItems[j]).appendTo(row);
			}
		}
	}

	// Add the alphabetical index value to all projects.
	function alphabeticalIndex() {
		var order = items.sort(function(a, b) {
			if (jQuery('h4', a).text().toLowerCase() > jQuery('h4', b).text().toLowerCase()) {
				return -1;
			} else {
				return 1;
			}
		});
		for (var i = 0; i < order.length; i += 1) {
			var item = jQuery(order[i]);
			item.data('alphabetical', i);
		}

		changeCategory();
	}

	// Display a project specific popup.
	function showPopup(type, param) {
		var box, pid, params = [], url, btn = false, anim = false;

		if (! isNaN(param)) {
			pid = param;
		} else {
			btn = jQuery(this);
			box = btn.closest('[data-project]');
			pid = box.data('project');

			anim = btn.find('.loading-icon');
			if (!anim.length) { anim = btn.closest('.loading-icon'); }
			if (!anim.length) { anim = btn; }
			btn.addClass('disabled');
			anim.loading(true);
		}
		url = ajaxurl;

		params.push('action=wdp-show-popup');
		params.push('type=' + type);
		params.push('pid=' + pid);
		params.push('hash=' + WDP.data.hash_show_popup);
		url += '?' + params.join('&');

		WDP.showOverlay(
			url,
			{
				onShow: function() {
					if (btn) { btn.removeClass('disabled'); }
					if (anim) { anim.loading(false); }
				}
			}
		);
	}

	// Save a single setting via an Ajax call.
	function saveSetting() {
		var jq = jQuery(this),
			toggle = jq.closest('.toggle'),
			data = {};

		jq.prop('disabled', true);
		toggle.loading(true);

		data.action = 'wdp-' + jq.data('action');
		data.hash = jq.data('hash');
		data.name = jq.attr('name');
		data.value = jq.is(':checked');

		jQuery.post(
			ajaxurl,
			data,
			function(response) {
				if (handleError(response)) { return; }

				WDP.showSuccess();
			},
			'json'
		).always(function() {
			jq.prop('disabled', false);
			toggle.loading(false);
		}).fail(function(xhr) {
			handleError(xhr);
		});
	}

	// Search for admin-users via Ajax (settings page).
	function searchUsers() {
		var userSearch = jQuery('#user-search'),
			username = userSearch.val(),
			data = {};

		if (! username.length) {
			userSearch.trigger('results:clear');
			return;
		}

		data.action = 'wdp-usersearch';
		data.hash = userSearch.data('hash');
		data.q = username;

		userSearch.trigger('progress:start');
		jQuery.post(
			ajaxurl,
			data,
			function(response) {
				if (handleError(response)) { return; }

				userSearch.trigger('results:show', [response.data]);
			},
			'json'
		).always(function() {
			userSearch.trigger('progress:stop');
		}).fail(function(xhr) {
			handleError(xhr);
		});
	}

	// Search for projects via Ajax (dashboard page).
	function searchProjects() {
		var projectSearch = jQuery('#project-search'),
			term = projectSearch.val(),
			data = {};

		if (! term.length) {
			projectSearch.trigger('results:clear');
			return;
		}

		data.action = 'wdp-projectsearch';
		data.hash = projectSearch.data('hash');
		data.q = term;

		projectSearch.trigger('progress:start');
		jQuery.post(
			ajaxurl,
			data,
			function(response) {
				if (handleError(response)) { return; }

				projectSearch.trigger('results:show', [response.data]);
			},
			'json'
		).always(function() {
			projectSearch.trigger('progress:stop');
		}).fail(function(xhr) {
			handleError(xhr);
		});
	}

	// Parse the response and display the error message
	function handleError(response) {
		if (!response) {
			// The requiest failed, definitely an error.
			WDP.showError('Unknown server error.');
			return true;
		}

		if (response.success && 'function' !== typeof response.success) {
			// This is no error, it's a success message!
			return false;
		}

		if (200 != response.status && response.statusText) {
			// The requiest failed, we have a server error-status.
			WDP.showError('Server error status: ' + response.statusText + ' [' + response.status + ']');
			return true;
		}

		// WordPress returned an error-state.
		if (response.data && response.data.message) {
			WDP.showError(response.data.message);
		} else {
			WDP.showError('Unexpected response from WordPress.');
		}
		return true;
	}

	// Reload the page.
	function reloadPage() {
		WDP.showOverlay('#reload');
		window.location.reload();
	}

	// Make fields disabled on form submit (from shared-ui)
	function disableFields() {
		var form, el = jQuery(this);

		window.setTimeout(function() {
			el.prop("disabled", true).addClass("disabled").loading(true);

			if (el.hasClass("wpmudui-btn")) {
				form = el.closest("form");
				if ( form.length ) {
					form.find(":input").prop("disabled", true).addClass("disabled");
					form.prop("disabled", true).addClass("disabled");
				}
			}
		}, 20);

	}

});
