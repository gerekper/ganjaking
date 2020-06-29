jQuery(function ($) {

	var $catalogUpdatingFrequencyRow = $('#woocommerce-pinterest-catalog-updating-frequency-days').parents('tr');
	var $catalogAutoUpdatingCheckbox = $('#woocommerce_pinterest_enable_catalog_auto_updating');
	var $googleCatalogTable          = $('#woocommerce-pinterest-google-catalog-table');
	var $categoryBoardsTable         = $('#woocommerce-pinterest-category-board-table');


	//Insert variable into input
	$(document).on('click', 'button[data-var]', function () {
		var button = $(this);

		insertText(button.data('field'), button.data('var'));
	});

	//Get google categories by parent
	$(document).on('change', '.woocommerce-pinterest-google-category-select', function (e) {
		var $changedSelect      = $(e.target);
		var $changedSelectValue = $changedSelect.val();

		$changedSelect.nextAll('select').remove();

		if ($changedSelect.is(':last-of-type')) {

			$changedSelect.parent('fieldset')
				.children('.woocommerce-pinterest-google-categories-mapping')
				.val($changedSelectValue);
		}

		if ($changedSelectValue !== '') {
			$.ajax({
				url: window.ajaxurl,
				method: 'GET',
				dataType: 'json',
				data: {
					action: premmerceSettings.get_google_categories_action,
					parentId: $changedSelectValue,
					_ajax_nonce: premmerceSettings.get_google_categories_nonce
				},
				success: function (response) {

					if (response.length > 0) {
						var $newSelect = $changedSelect.clone().val('').empty();

						$newSelect.append(new Option(premmerceSettings.not_selected_option_name));

						$.each(response, function (index, optionData) {
							$newSelect.append(new Option(optionData.name, optionData.id));
						});

						$newSelect.insertAfter($changedSelect);
					}
				}
			});
		}
	});

	//Save google categories table
	$(document).on('click', '#save-google-categories-table', function(e){
		e.preventDefault();


		var categoriesData = [];
		$googleCatalogTable.find('input.woocommerce-pinterest-google-categories-mapping').each(function (index, element) {
			var $input                   = $(element);
			var wcCategoryId             = $input.data('wc-category-id');
			categoriesData[wcCategoryId] = $input.val();
		});
		var $clickedButton = $(this);

		$.ajax({
			url: window.ajaxurl,
			method: 'POST',
			dataType: 'json',
			data: {
				action: premmerceSettings.save_google_categories_mapping_action,
				categories: categoriesData,
				_ajax_nonce: premmerceSettings.save_google_categories_mapping_nonce
			},
			success: function (response) {
				if(response.success){
					showSavedMessage($clickedButton);
				}
				else{
					alert("Error! Categories data wasn't saved.");
				}
			}
		});
	});

	function showSavedMessage($saveButton){
		$saveButton.parents('td').find('.woocommerce-pinterest-table-saved-message').show().delay(3000).fadeOut();
	}


	//Save category boards table
	$(document).on('click', '#save-category-boards-table', function (e) {
		e.preventDefault();
		var data = [];


		$categoryBoardsTable.find('tbody tr').each(function(){
			var row    = $(this);
			var boards = {};

			var catId = row.find('[data-category-id]').attr('data-category-id');

			row.find('.woocommerce-pinterest-category-board-selectors .woocommerce-pinterest-board-select-container').each(function(index){
				var boardContainer = $(this);

				boards[index] = {
					cat_id: catId,
					board_id: boardContainer.find('.woocommerce-pinterest-category-board-relations').val(),
					relation_id: boardContainer.find('[data-relations-id]').val()
				};

			});

			data.push(boards);
		});

		var $clickedButton = $(this);

		$.ajax({
			url: window.ajaxurl,
			method: 'POST',
			dataType: 'json',
			data: {
				action: premmerceSettings.save_category_boards_relations_action,
				data: data,
				_ajax_nonce: premmerceSettings.save_category_boards_relations_nonce
			},
			success: function (response) {
				if( response.success){
					showSavedMessage($clickedButton);
				}
				else{
					alert("Error! Boards settings wasn't saved")
				}
			}
		});
	});

	//Add new board select to category-board table
	$(document).on('click', '.woocommerce-pinterest-category-board-new-select', function(e){
		e.preventDefault();
		var $targetButton          = $(e.target);
		var $selectContainers      = $targetButton.parent('td').find('fieldset').find('.woocommerce-pinterest-board-select-container');
		var $clonedSelectContainer = $selectContainers.first().clone();
		$clonedSelectContainer.find('select').val('');
		$clonedSelectContainer.find('input').val('');
		var $lastSelectContainer = $selectContainers.last();
		var regexp               = new RegExp( '^([^\\[]+\\[\\d+\]\\[)(\\d+)(\]\\[board_id\])');
		var nextIndex            = parseInt($lastSelectContainer.find('select').attr('name').match(regexp)['2']) + 1;
		var name                 = $clonedSelectContainer.find('select').attr('name');

		name = name.replace(regexp, function (match, p1, p2, p3, offset, string) {
			return [p1, nextIndex, p3].join('');
		});

		$clonedSelectContainer.find('select').attr('name', name);
		$clonedSelectContainer.insertAfter($lastSelectContainer);
	});

	//Remove board select
	$(document).on('click', '.woocommerce-pinterest-remove-board-select', function(e){
		e.preventDefault();

		$(this).parents('.woocommerce-pinterest-board-select-container').remove();
	});

	//Sort select options in user defined order
	var $strategySelector = $('#woocommerce_pinterest_tags_fetching_strategy');
	$strategySelector.select2();
	$("ul.select2-selection__rendered").sortable({
		containment: 'parent',
		update: function (e) {
			var target = $(e.target);
			target.children("li[title]").each(function (i, obj) {
				$strategySelector.children('option').each(function () {
					var $obj = $(obj);
					if (($(this).html() == obj.title)) {

						var $option = $(this);
						var $parent = $option.parent();
						$option.detach();
						$parent.append($option);
					}
				});
			});

			$strategySelector.trigger('change');
		}
	});

	//Toggle regenerate catalog options row
	$catalogUpdatingFrequencyRow.toggle($catalogAutoUpdatingCheckbox.is(':checked'));
	$catalogAutoUpdatingCheckbox.on('change', function () {
		$catalogUpdatingFrequencyRow.toggle();
	});

	//Copy catalog file path to clipboard
	$(document).on('click', '.woocommerce-pinterest-copy-catalog-url-button', function () {
		$('.woocommerce-pinterest-catalog-url-field').select();
		document.execCommand('copy');
	});

	//Insert text into textarea
	function insertText(fieldSelector, text) {

		var field = document.querySelector(fieldSelector);

		if (!field) {
			return;
		}

		field.focus();
		var value    = field.value;
		var position = field.selectionStart;
		field.value  = value.slice(0, position) + text + value.slice(position);
		position    += text.length;
		field.setSelectionRange(position, position);
	}


	$('[name=woocommerce_pinterest_pin_time]').on('change', function (e) {
		var fields = [
			$('#woocommerce_pinterest_pin_defer_day').closest('tr'),
			$('#woocommerce_pinterest_pin_defer_time').closest('tr'),
			$('#woocommerce_pinterest_pin_defer_interval').closest('tr'),
		];

		$('[name=woocommerce_pinterest_pin_defer_interval]').trigger('change');

		if ($(this).val() === 'defer') {
			fields.forEach(function ($field) {
				$field.show();
			});
		} else {
			fields.forEach(function ($field) {
				$field.hide();
			});
		}

	}).trigger('change');


	$('[name=woocommerce_pinterest_pin_defer_interval]').on('change', function (e) {
		var fields = [
			$('#woocommerce_pinterest_pin_defer_pins_per_interval').closest('tr'),
		];

		if ($(this).val() === '-1' || $('[name=woocommerce_pinterest_pin_time]').val() !== 'defer') {
			fields.forEach(function ($field) {
				$field.hide();
			});
		} else {
			fields.forEach(function ($field) {
				$field.show();
			});
		}
	}).trigger('change');

	$('[name=woocommerce_pinterest_pinterest_image_size_type]').on('change', function (e) {
		var $pinterestImageFields = $('#pinterest_image_size-field');

		if ($(this).val() === 'pinterest_image') {
			$pinterestImageFields.show();
		} else {
			$pinterestImageFields.hide();
		}
	}).trigger('change');

	//Open/close settings fields and save it's state
	$(document).on('click', 'h3.wc-settings-sub-title', function (e) {
		var $title = $(this).toggleClass('closed');

		var sectionId = $title.prop('id').replace('woocommerce_pinterest_', '');

		var closed = $title.hasClass('closed');

		$.ajax({
			url: window.ajaxurl,
			method: 'POST',
			dataType: 'json',
			data: {
				action: premmerceSettings.update_settings_page_boxes_states_action,
				sectionId: sectionId,
				closed: closed,
				_ajax_nonce: premmerceSettings.update_settings_page_boxes_states_nonce
			}
		});
	})
});
