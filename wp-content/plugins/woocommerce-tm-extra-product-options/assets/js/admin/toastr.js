/*
 * Toastr
 * Copyright 2012-2015
 * Authors: John Papa, Hans Fjällemark, and Tim Ferrell.
 * All Rights Reserved.
 * Use, reproduction, distribution, and modification of this code is subject to the terms and
 * conditions of the MIT license, available at http://www.opensource.org/licenses/mit-license.php
 *
 * ARIA Support: Greta Krafsig
 *
 * Project: https://github.com/CodeSeven/toastr
 * reformatted code by themeComplete
 */
/* global define */
( function( define ) {
	'use strict';

	define( [ 'jquery' ], function( $ ) {
		return ( function() {
			var $container;
			var listener;
			var toastId = 0;
			var toastType = {
				error: 'error',
				info: 'info',
				success: 'success',
				warning: 'warning'
			};

			var toastr;

			var previousToast;

			////////////////

			// internal functions

			function getDefaults() {
				return {
					tapToDismiss: true,
					toastClass: 'toast',
					containerId: 'toast-container',
					debug: false,

					showMethod: 'fadeIn', //fadeIn, slideDown, and show are built into jQuery
					showDuration: 300,
					showEasing: 'linear', //swing and linear are built into jQuery
					onShown: undefined,
					hideMethod: 'fadeOut',
					hideDuration: 1000,
					hideEasing: 'linear',
					onHidden: undefined,
					closeMethod: false,
					closeDuration: false,
					closeEasing: false,
					closeOnHover: true,

					extendedTimeOut: 1000,
					iconClasses: {
						error: 'toast-error',
						info: 'toast-info',
						success: 'toast-success',
						warning: 'toast-warning'
					},
					iconClass: 'toast-info',
					positionClass: 'toast-top-right',
					timeOut: 5000, // Set timeOut and extendedTimeOut to 0 to make it sticky
					titleClass: 'toast-title',
					messageClass: 'toast-message',
					escapeHtml: false,
					target: 'body',
					closeHtml: '<button type="button">&times;</button>',
					closeClass: 'toast-close-button',
					newestOnTop: true,
					preventDuplicates: false,
					progressBar: false,
					progressClass: 'toast-progress',
					rtl: false
				};
			}

			function getOptions() {
				return $.extend( {}, getDefaults(), toastr.options );
			}

			function createContainer( options ) {
				$container = $( '<div/>' ).attr( 'id', options.containerId ).addClass( options.positionClass );

				$container.appendTo( $( options.target ) );
				return $container;
			}

			function getContainer( options, create ) {
				if ( ! options ) {
					options = getOptions();
				}
				$container = $( '#' + options.containerId );
				if ( $container.length ) {
					return $container;
				}
				if ( create ) {
					$container = createContainer( options );
				}
				return $container;
			}

			function removeToast( $toastElement ) {
				if ( ! $container ) {
					$container = getContainer();
				}
				if ( $toastElement.is( ':visible' ) ) {
					return;
				}
				$toastElement.remove();
				$toastElement = null;
				if ( $container.children().length === 0 ) {
					$container.remove();
					previousToast = undefined;
				}
			}

			function clearToast( $toastElement, options, clearOptions ) {
				var force = clearOptions && clearOptions.force ? clearOptions.force : false;
				if ( $toastElement && ( force || $( ':focus', $toastElement ).length === 0 ) ) {
					$toastElement[ options.hideMethod ]( {
						duration: options.hideDuration,
						easing: options.hideEasing,
						complete: function() {
							removeToast( $toastElement );
						}
					} );
					return true;
				}
				return false;
			}

			function clearContainer( options ) {
				var i;
				var toastsToClear = $container.children();
				for ( i = toastsToClear.length - 1; i >= 0; i -= 1 ) {
					clearToast( $( toastsToClear[ i ] ), options );
				}
			}

			function publish( args ) {
				if ( ! listener ) {
					return;
				}
				listener( args );
			}

			function notify( map ) {
				var options = getOptions();
				var iconClass = map.iconClass || options.iconClass;

				var intervalId = null;
				var $toastElement;
				var $titleElement;
				var $messageElement;
				var $progressElement;
				var $closeElement;
				var progressBar = {
					intervalId: null,
					hideEta: null,
					maxHideTime: null
				};
				var response;

				function escapeHtml( source ) {
					if ( source === null || source === undefined ) {
						source = '';
					}

					return source.replace( /&/g, '&amp;' ).replace( /"/g, '&quot;' ).replace( /'/g, '&#39;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
				}

				function setAria() {
					var ariaValue = '';
					switch ( map.iconClass ) {
						case 'toast-success':
						case 'toast-info':
							ariaValue = 'polite';
							break;
						default:
							ariaValue = 'assertive';
					}
					$toastElement.attr( 'aria-live', ariaValue );
				}

				function hideToast( override ) {
					var method = override && options.closeMethod !== false ? options.closeMethod : options.hideMethod;
					var duration = override && options.closeDuration !== false ? options.closeDuration : options.hideDuration;
					var easing = override && options.closeEasing !== false ? options.closeEasing : options.hideEasing;
					if ( $( ':focus', $toastElement ).length && ! override ) {
						return;
					}
					clearTimeout( progressBar.intervalId );
					return $toastElement[ method ]( {
						duration: duration,
						easing: easing,
						complete: function() {
							removeToast( $toastElement );
							clearTimeout( intervalId );
							if ( options.onHidden && response.state !== 'hidden' ) {
								options.onHidden();
							}
							response.state = 'hidden';
							response.endTime = new Date();
							publish( response );
						}
					} );
				}

				function delayedHideToast() {
					if ( options.timeOut > 0 || options.extendedTimeOut > 0 ) {
						intervalId = setTimeout( hideToast, options.extendedTimeOut );
						progressBar.maxHideTime = parseFloat( options.extendedTimeOut );
						progressBar.hideEta = new Date().getTime() + progressBar.maxHideTime;
					}
				}

				function stickAround() {
					clearTimeout( intervalId );
					progressBar.hideEta = 0;
					$toastElement.stop( true, true )[ options.showMethod ]( { duration: options.showDuration, easing: options.showEasing } );
				}

				function handleEvents() {
					if ( options.closeOnHover ) {
						$toastElement.on( 'mouseenter', stickAround );
						$toastElement.on( 'mouseleave', delayedHideToast );
					}

					if ( ! options.onclick && options.tapToDismiss ) {
						$toastElement.on( 'click', hideToast );
					}

					if ( options.closeButton && $closeElement ) {
						$closeElement.on( 'click', function( event ) {
							if ( event.stopPropagation ) {
								event.stopPropagation();
							} else if ( event.cancelBubble !== undefined && event.cancelBubble !== true ) {
								event.cancelBubble = true;
							}

							if ( options.onCloseClick ) {
								options.onCloseClick( event );
							}

							hideToast( true );
						} );
					}

					if ( options.onclick ) {
						$toastElement.on( 'click', function( event ) {
							options.onclick( event );
							hideToast();
						} );
					}
				}

				function updateProgress() {
					var percentage = ( ( progressBar.hideEta - new Date().getTime() ) / progressBar.maxHideTime ) * 100;
					$progressElement.width( percentage + '%' );
				}

				function displayToast() {
					$toastElement.hide();

					$toastElement[ options.showMethod ]( { duration: options.showDuration, easing: options.showEasing, complete: options.onShown } );

					if ( options.timeOut > 0 ) {
						intervalId = setTimeout( hideToast, options.timeOut );
						progressBar.maxHideTime = parseFloat( options.timeOut );
						progressBar.hideEta = new Date().getTime() + progressBar.maxHideTime;
						if ( options.progressBar ) {
							progressBar.intervalId = setInterval( updateProgress, 10 );
						}
					}
				}

				function setIcon() {
					if ( map.iconClass ) {
						$toastElement.addClass( options.toastClass ).addClass( iconClass );
					}
				}

				function setSequence() {
					if ( options.newestOnTop ) {
						$container.prepend( $toastElement );
					} else {
						$container.append( $toastElement );
					}
				}

				function setTitle() {
					var suffix;
					if ( map.title ) {
						suffix = map.title;
						if ( options.escapeHtml ) {
							suffix = escapeHtml( map.title );
						}
						$titleElement.append( suffix ).addClass( options.titleClass );
						$toastElement.append( $titleElement );
					}
				}

				function setMessage() {
					var suffix;
					if ( map.message ) {
						suffix = map.message;
						if ( options.escapeHtml ) {
							suffix = escapeHtml( map.message );
						}
						$messageElement.append( suffix ).addClass( options.messageClass );
						$toastElement.append( $messageElement );
					}
				}

				function setCloseButton() {
					if ( options.closeButton ) {
						$closeElement.addClass( options.closeClass ).attr( 'role', 'button' );
						$toastElement.prepend( $closeElement );
					}
				}

				function setProgressBar() {
					if ( options.progressBar ) {
						$progressElement.addClass( options.progressClass );
						$toastElement.prepend( $progressElement );
					}
				}

				function setRTL() {
					if ( options.rtl ) {
						$toastElement.addClass( 'rtl' );
					}
				}

				function shouldExit( opts, mmap ) {
					if ( opts.preventDuplicates ) {
						if ( mmap.message === previousToast ) {
							return true;
						}
						previousToast = mmap.message;
					}
					return false;
				}

				function personalizeToast() {
					setIcon();
					setTitle();
					setMessage();
					setCloseButton();
					setProgressBar();
					setRTL();
					setSequence();
					setAria();
				}

				if ( typeof map.optionsOverride !== 'undefined' ) {
					options = $.extend( options, map.optionsOverride );
					iconClass = map.optionsOverride.iconClass || iconClass;
				}

				if ( shouldExit( options, map ) ) {
					return;
				}

				toastId = toastId + 1;

				$container = getContainer( options, true );

				$toastElement = $( '<div/>' );
				$titleElement = $( '<div/>' );
				$messageElement = $( '<div/>' );
				$progressElement = $( '<div/>' );
				$closeElement = $( options.closeHtml );

				response = {
					toastId: toastId,
					state: 'visible',
					startTime: new Date(),
					options: options,
					map: map
				};

				personalizeToast();

				displayToast();

				handleEvents();

				publish( response );

				if ( options.debug && window.console ) {
					window.console.log( response );
				}

				return $toastElement;
			}

			function error( message, title, optionsOverride ) {
				return notify( {
					type: toastType.error,
					iconClass: getOptions().iconClasses.error,
					message: message,
					optionsOverride: optionsOverride,
					title: title
				} );
			}

			function info( message, title, optionsOverride ) {
				return notify( {
					type: toastType.info,
					iconClass: getOptions().iconClasses.info,
					message: message,
					optionsOverride: optionsOverride,
					title: title
				} );
			}

			function subscribe( callback ) {
				listener = callback;
			}

			function success( message, title, optionsOverride ) {
				return notify( {
					type: toastType.success,
					iconClass: getOptions().iconClasses.success,
					message: message,
					optionsOverride: optionsOverride,
					title: title
				} );
			}

			function warning( message, title, optionsOverride ) {
				return notify( {
					type: toastType.warning,
					iconClass: getOptions().iconClasses.warning,
					message: message,
					optionsOverride: optionsOverride,
					title: title
				} );
			}

			function clear( $toastElement, clearOptions ) {
				var options = getOptions();
				if ( ! $container ) {
					getContainer( options );
				}
				if ( ! clearToast( $toastElement, options, clearOptions ) ) {
					clearContainer( options );
				}
			}

			function remove( $toastElement ) {
				var options = getOptions();
				if ( ! $container ) {
					getContainer( options );
				}
				if ( $toastElement && $( ':focus', $toastElement ).length === 0 ) {
					removeToast( $toastElement );
					return;
				}
				if ( $container.children().length ) {
					$container.remove();
				}
			}

			toastr = {
				clear: clear,
				remove: remove,
				error: error,
				getContainer: getContainer,
				info: info,
				options: {},
				subscribe: subscribe,
				success: success,
				version: '2.1.4',
				warning: warning
			};

			return toastr;
		}() );
	} );
}(
	typeof define === 'function' && define.amd
		? define
		: function( deps, factory ) {
			'use strict';

			if ( typeof window.module !== 'undefined' && window.module.exports ) {
				//Node
				window.module.exports = factory( window.require( 'jquery' ) );
			} else {
				window.toastr = factory( window.jQuery );
			}
		}
) );
