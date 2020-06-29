(function($) {
	$.fn.matchColumn = function(options) {
		var settings = $.extend({
			showAlert: false,
			alertMessage: '',
			showConfirmation: false,
			confirmationMessage:'',
			defaultValue: 'nomatch',
			beforeResetColumn: function(currentValue, obj){},
			beforeChange: function(currentValue, obj){}
		}, options);

		var previousValue;

		/**
	 * List of dom elements which are in scope
	 */
		var objects = new Array();

		this.each(function() {
			objects.push(this);
		});

		/**
	 * reset Columns which were matched previously with a same value
	 * @param {dom} currentObject current dom
	 * @param {boolean} onInit is invoked on load?
	 */
		var resetColumns = function(currentObject, onInit) {
			if (typeof onInit === undefined) {
				onInit = false;
			}

			isMatchedColumn = false;// if there is at least 1 column matched with a same filed
			for (i = 0; i < objects.length; i++) {
				if (
					!isMatchedColumn
					&& $(currentObject).val() !== settings.defaultValue
					&& !$(objects[i]).is(currentObject)
					&& $(objects[i]).val() === $(currentObject).val()
					) {
						isMatchedColumn = true;
					}
			}
			if (isMatchedColumn) {
				if (
					!onInit
					&& settings.showConfirmation
					&& typeof settings.confirmationMessage !== undefined
					&& settings.confirmationMessage !== ''
					&& settings.defaultValue !== undefined) {// if we want to show a confirmation message
						yes = confirm(settings.confirmationMessage);
						if (!yes) {// if users do not want to make changes
							$(currentObject).val($(currentObject).data('lastValue'));
							return;
						}
				}
				else if (// if we want to show an alert instead and stop
					!onInit
					&& settings.showAlert
					&& typeof settings.alertMessage !== undefined
					&& settings.alertMessage !== ''
					&& settings.defaultValue !== undefined) {
						alert(settings.alertMessage);
					}

				// once jumping here, it's clear to proceed with changes
				for (i = 0; i < objects.length; i++) {
					if (
						!$(objects[i]).is(currentObject)
						&& $(currentObject).val() !== settings.defaultValue
						&& $(objects[i]).val() === $(currentObject).val()
						) {
								if (typeof settings.beforeResetColumn === 'function') {
									settings.beforeResetColumn($(objects[i]).val(), objects[i]);
								}
							$(objects[i]).val(settings.defaultValue).data('lastValue',settings.defaultValue);
							// start something here!
						}
				}
			}
		};

		this.each(function() {
			previousValue = $(this).val();
			resetColumns(this, true);
			$(this).data('lastValue',$(this).val());
		});

		$(this)
		.change(function() {
			resetColumns(this);
			if (typeof settings.beforeChange === 'function') {
				settings.beforeChange($(this).data('lastValue'), this);
			}
			$(this).data('lastValue',$(this).val());
		});
		return this;
	};
}(jQuery));

