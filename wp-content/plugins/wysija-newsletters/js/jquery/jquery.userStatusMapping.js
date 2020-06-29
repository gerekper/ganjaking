(function($) {
	$.fn.userStatusMapping = function(options) {
		var settings = $.extend({
			defaultValue: 'status',
			dataElement: '.imported-field',
			statuses: new Array()
		}, options);

		var changeToUserStatus = function(obj){
			cellsInScope = getCellsInScope(obj);
			$.each(cellsInScope, function(index, element){
				_dataElement = ($(element).find(settings.dataElement).length <= 0) ? element : $(element).find(settings.dataElement);
				currentValue = parseInt($(_dataElement).html());
				if (!isNaN(currentValue) && typeof settings.statuses[currentValue] !== 'undefined') {
					$(_dataElement).data('lastValue',currentValue).html(settings.statuses[currentValue]);
				}
			});
		};

		var changeFromUserStatus = function(obj){
			cellsInScope = getCellsInScope(obj);
			$.each(cellsInScope, function(index, element){
				_dataElement = ($(element).find(settings.dataElement).length <= 0) ? element : $(element).find(settings.dataElement);
				if (typeof $(_dataElement).data('lastValue') !== 'undefined') {
					$(_dataElement).html($(_dataElement).data('lastValue'));
					$(_dataElement).data('lastValue', undefined);
				}
			});
		};

		// Return a list of cells of the current column
		var getCellsInScope = function(obj){
			columnPostion = getColumnPosition(obj);
			cells = new Array();
			$(obj).parents('table').children('tbody').children('tr').each(function(index, element){
				cells.push($(this).children('td')[columnPostion]);
			});
			return cells;
		};

		var getColumnPosition = function(obj) {
			return $(obj).parents('th').index();
		}

		var getRowPosition = function(obj) {
			return $(obj).parents('th').parent().index();
		}

		$(this)
		.change(function() {
			if ($(this).val() === settings.defaultValue) {
				changeToUserStatus(this);
			}
		});

		// Utilities
		$.userStatusMapping = {
			changeFromUserStatus : function(obj){changeFromUserStatus(obj);},
			changeToUserStatus : function(obj){changeToUserStatus(obj);}
		};

		return this;
	};
}(jQuery));
