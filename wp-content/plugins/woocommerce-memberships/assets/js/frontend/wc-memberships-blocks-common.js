jQuery(function ($) {
	var wc_memberships_common = window.wc_memberships_blocks_common || {};

	/**
	 * Initializes membership directory
	 *
	 * @since 1.23.0
	 */
	function initMembershipDirectory() {
		//Helper to merge deeply two Json Object
		const deepMerge = (source, target) => {
			for (const [key, val] of Object.entries(source)) {
				if (val !== null && typeof val === `object`) {
					if (target[key] === undefined) {
						target[key] = new val.__proto__.constructor();
					}
					deepMerge(val, target[key]);
				} else {
					target[key] = val;
				}
			}
			return target;
		};

		//Helper to update query params
		const updateQueryString = (key, value) => {
			if (history.pushState) {
				key = 'wcm_dir_' + key;
				let searchParams = new URLSearchParams(window.location.search);
				if (value) {
					searchParams.set(key, value);
				} else {
					searchParams.delete(key);
				}
				let newUrl = new URL(window.location.href);
				newUrl.search = searchParams.toString();
				window.history.pushState({ path: newUrl.toString() }, '', newUrl.toString());
			}
		};

		// Helper to get query params from url
		const getQueryParameter = (name) => {
			return new URLSearchParams(window.location.search).get('wcm_dir_' + name);
		};

		//Update the directory query parameter
		const updateDirectoryQueryParams = (args = {}) => {
			updateQueryString('search', args.search);
			updateQueryString('plan', args.plan);
			updateQueryString('status', args.status);
			updateQueryString('page', args.page == 1 ? null : args.page);
		};

		// Get memberships data
		const getMembershipsData = (args = {}) => {
			updateDirectoryQueryParams(args.requestData);
			$.get({
				url: wc_memberships_common.restUrl + (args.endPoint || ''),
				data: args.requestData || {},
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-WP-Nonce', wc_memberships_common.restNonce);
				},
			})
				.done(function (response, textStatus, request) {
					args.callBack(response, textStatus, request);
				})
				.fail(function () {
					console.log('error');
				})
				.always(function () {
					console.log('finished');
				});
		};

		//Setup directory
		const setupDirectory = ($directory, args = {}) => {
			let directoryId = $directory.data('directory-id');
			let directorySettings = $directory.data('directory-data');
			let requestData = {};
			let filterPlan,
				filterStatus,
				searchInput = '';

			// Request for specific plans members
			if (directorySettings.membershipPlans.length) {
				requestData.plan = directorySettings.membershipPlans.join(',');
			}

			//Filter plans
			if ((filterPlan = $directory.find('.wcm-plans').val().join(','))) {
				requestData.plan = filterPlan;
			}

			// Request for specific status members
			if (directorySettings.membershipStatus.length) {
				requestData.status = directorySettings.membershipStatus.join(',');
			}

			// Filter statues
			if ((filterStatus = $directory.find('.wcm-status').val().join(','))) {
				requestData.status = filterStatus;
			}

			// Add search param
			if ((searchInput = $directory.find('.wcm-search-input').val())) {
				requestData.search = searchInput;
			}

			// set the current page if provided
			if (args.requestData && args.requestData.page) {
				$directory
					.find('.wcm-pagination-wrapper')
					.attr('data-current-page', args.requestData.page);
			}

			$directory.find('.wmc-loader').show();

			const membershipData = {
				endPoint: 'wc/v4/memberships/members',
				requestData: {
					_includes: 'customer_data',
					per_page: directorySettings.perPage,
					...requestData,
				},
				callBack: (response, textStatus, request) => {
					if (response.length) {
						let members = '';
						response.forEach((data) => {
							data.directorySettings = directorySettings;

							members += getDirectoryItem(data);
						});

						$directory.find('.wcm-directory-list-wrapper').html(members);
					} else {
						if (filterPlan || filterStatus || searchInput) {
							let message = wc_memberships_common.keywords.search_not_found;
							$directory.find('.wcm-directory-list-wrapper').html('<div class="directory-placeholder-box"><p>' + message + '</p></div>');
						} else {
							let message = wc_memberships_common.keywords.results_not_found;
							$directory.find('.wcm-directory-list-wrapper').html('<div class="directory-placeholder-box"><p>' + message + '</p></div>');
						}
					}
					const totalMembers = request.getResponseHeader('x-wp-total');
					const totalPages = request.getResponseHeader('x-wp-totalpages');
					addPagination($directory, totalPages);
					setupPagination($directory);
					$directory.find('.wmc-loader').hide();
				},
			};
			getMembershipsData(deepMerge(membershipData, args));
		};

		// Add Pagination settings
		const addPagination = ($directory, totalPages) => {
			const currentPage = $directory
				.find('.wcm-pagination-wrapper')
				.attr('data-current-page');

			$directory
				.find('.wcm-pagination-wrapper')
				.attr('data-total-pages', totalPages);
			if (0 == totalPages) {
				$directory.find('.wcm-pagination-wrapper').hide();
				return;
			} else {
				$directory.find('.wcm-pagination-wrapper').show();
			}

			if (currentPage >= totalPages) {
				$directory.find('.wcm-pagination-wrapper .next').hide();
			} else {
				$directory.find('.wcm-pagination-wrapper .next').show();
			}

			if (1 == currentPage) {
				$directory.find('.wcm-pagination-wrapper .previous').hide();
			} else {
				$directory.find('.wcm-pagination-wrapper .previous').show();
			}
		};

		// Setup pagination
		const setupPagination = ($directory) => {
			$directory
				.find('.wcm-pagination-wrapper .wcm-pagination')
				.off('click')
				.on('click', (e) => {
					const currentPage = $directory
						.find('.wcm-pagination-wrapper')
						.attr('data-current-page');
					const totalPages = $directory
						.find('.wcm-pagination-wrapper')
						.attr('data-total-pages');
					//next btn clicked
					e.preventDefault();
					if (e.currentTarget.classList.contains('next')) {
						if (currentPage < totalPages) {
							let nextPage = parseInt(currentPage) + 1;
							$directory
								.find('.wcm-pagination-wrapper')
								.attr('data-current-page', nextPage);
							setupDirectory($directory, { requestData: { page: nextPage } });
						}
					}
					//previous btn clicked
					if (e.currentTarget.classList.contains('previous')) {
						if (currentPage > 1) {
							let previousPage = parseInt(currentPage) - 1;
							$directory
								.find('.wcm-pagination-wrapper')
								.attr('data-current-page', previousPage);
							setupDirectory($directory, {
								requestData: { page: previousPage },
							});
						}
					}
				});
		};

		// Get member fields
		const getProfileFields = (profileFields, userFields) => {
			let fields = '';
			userFields.forEach((field) => {
				if (profileFields.includes(field.slug) && field.value) {
					fields += `<div class="info-box profile-fields"><label>${field.name}: </label><span>${field.value}</span></div>`;
				}
			});
			return fields;
		};

		// Get Member directory Item layout
		const getDirectoryItem = (args = {}) => {
			const {
				customer_data: customer,
				plan_name: planName,
				profile_fields,
			} = args;
			const {
				showBio,
				showEmail,
				showPhone,
				showAddress,
				avatar: showAvatar,
				avatarSize,
				profileFields,
			} = args.directorySettings;

			const Item = `
			<div class="wcm-directory-member-wrapper">
				<div class="wcm-directory-member">
					${
						showAvatar
							? `<img src="${customer.avatar}" style="width:${avatarSize}px">`
							: ''
					}
					<h4>${customer.first_name} ${customer.last_name} </h4>
					${showBio ? `<div class="bio-box">${customer.bio}</div>` : ''}
					<div class="info-box"><label>${
						wc_memberships_common.keywords.plan
					}: </label><span>${planName}</span></div>
					${
						showEmail && customer.user_email
							? `<div class="info-box"><label>${wc_memberships_common.keywords.email}: </label><span>${customer.user_email}</span></div>`
							: ''
					}
					${
						showPhone && customer.phone
							? `<div class="info-box"><label>${wc_memberships_common.keywords.phone}: </label><span>${customer.phone}</
					span></div>`
							: ''
					}
					${
						showAddress && customer.address
							? `<div class="info-box"><label>${wc_memberships_common.keywords.address}: </label><span>${customer.address}</
					span></div>`
							: ''
					}
					${
						profileFields.length && profile_fields.length
							? getProfileFields(profileFields, profile_fields)
							: ''
					}
				</div>
			</div>`;

			return Item;
		};

		//setup directory with query params
		const setupDirectoryWithQParams = ($directory) => {
			let plans,
				status,
				search,
				page = '';
			let args = {};

			if ((plans = getQueryParameter('plan'))) {
				$directory.find('.wcm-plans').val(plans.split(',')).trigger('change');
			}
			if ((status = getQueryParameter('status'))) {
				$directory.find('.wcm-status').val(status.split(',')).trigger('change');
			}
			if ((search = getQueryParameter('search'))) {
				$directory.find('.wcm-search-input').val(search);
			}
			if ((page = getQueryParameter('page'))) {
				$directory
					.find('.wcm-pagination-wrapper')
					.attr('data-current-page', page);
				args['requestData'] = { page: page };
			}

			setupDirectory($directory, args);
		};

		if (
			$('.wc-memberships-directory-container.wcm-directory-front-end').length
		) {
			$('.wc-memberships-directory-filter-wrapper .wcm-select').select2();
			$('.wc-memberships-directory-container.wcm-directory-front-end').each(
				(index, directory) => {
					//setup directory with query params
					setupDirectoryWithQParams($(directory));

					$(directory)
						.find('.wcm-filter-btn,.wcm-search-btn')
						.click(() => {
							setupDirectory($(directory), { requestData: { page: 1 } });
						});

					//On enter key press trigger search
					$(directory)
						.find('.wcm-search-input')
						.keyup((e) => {
							if (e.keyCode === 13) {
								setupDirectory($(directory), { requestData: { page: 1 } });
							}
						});
				}
			);
		}
	}

	initMembershipDirectory();
});
