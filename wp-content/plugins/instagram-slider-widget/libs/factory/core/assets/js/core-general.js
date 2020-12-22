/**
 *
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 02.09.2020, Webcraftic
 * @version 1.0
 */

(function($) {
	'use strict';

	if( !$.wfactory_439 ) {
		$.wfactory_439 = {};
	}

	$.wfactory_439.filters = $.wfactory_439.filters || {

		/**
		 * A set of registered filters.
		 */
		_items: {},

		/**
		 * A set of priorities of registered filters.
		 */
		_priorities: {},

		/**
		 * Applies filters to a given input value.
		 */
		run: function(filterName, args) {
			var input = args && args.length > 0 ? args[0] : null;
			if( !this._items[filterName] ) {
				return input;
			}

			for( var i in this._priorities[filterName] ) {
				if( !this._priorities[filterName].hasOwnProperty(i) ) {
					continue;
				}

				var priority = this._priorities[filterName][i];

				for( var k = 0; k < this._items[filterName][priority].length; k++ ) {
					var f = this._items[filterName][priority][k];
					input = f.apply(f, args);
				}
			}

			return input;
		},

		/**
		 * Registers a new filter.
		 */
		add: function(filterName, callback, priority) {

			if( !priority ) {
				priority = 10;
			}

			if( !this._items[filterName] ) {
				this._items[filterName] = {};
			}
			if( !this._items[filterName][priority] ) {
				this._items[filterName][priority] = [];
			}
			this._items[filterName][priority].push(callback);

			if( !this._priorities[filterName] ) {
				this._priorities[filterName] = [];
			}
			if( $.inArray(priority, this._priorities[filterName]) === -1 ) {
				this._priorities[filterName].push(priority);
			}

			this._priorities[filterName].sort(function(a, b) {
				return a - b;
			});
		}
	};

	$.wfactory_439.hooks = $.wfactory_439.hooks || {

		/**
		 * Applies filters to a given input value.
		 */
		run: function(filterName, args) {
			$.wfactory_439.filters.run(filterName, args);
		},

		/**
		 * Registers a new filter.
		 */
		add: function(filterName, callback, priority) {
			$.wfactory_439.filters.add(filterName, callback, priority);
		}
	};

})(jQuery);
