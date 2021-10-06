/**
 * Wrapper to add debouncing to any given callback.
 *
 * @since 2.5.2
 *
 * @param {Function} fn             The callback to execute.
 * @param {integer}  debounceLength The amount of time for which to debounce (in milliseconds)
 * @param {bool}     isImmediate    Whether to fire this immediately, or at the tail end of the timeout.
 *
 * @returns {function}
 */
export default function( fn, debounceLength, isImmediate ) {
	// Initialize var to hold our window timeout
	var timeout;
	var lastArgs;
	var lastFn;

	return function() {
		// Initialize local versions of our context and arguments to pass to apply()
		var callbackContext = this;
		var args            = arguments;

		// Create a deferred callback to fire if this shouldn't be immediate.
		var deferredCallback = function() {
			timeout = null;

			if ( ! isImmediate ) {
				fn.apply( callbackContext, args );
			}
		};

		// Begin processing the actual callback.
		var callNow = isImmediate && ! timeout;

		// Reset timeout if it is the same method with the same args.
		if ( args === lastArgs && ( ''+lastFn == ''+fn ) ) {
			clearTimeout( timeout );
		}

		// Set the value of the last function call and arguments to help determine whether the next call is unique.
		var cachePreviousCall = function( fn, args ) {
			lastFn    = fn;
			lastArgs = args;
		}

		timeout = setTimeout( deferredCallback, debounceLength );
		cachePreviousCall( fn, args );

		// Method should be executed on the trailing edge of the timeout. Bail for now.
		if ( ! callNow ) {
			return;
		}

		// Callback should be called immediately, and isn't currently debounced; execute it.
		fn.apply( callbackContext, args );
	};
};
