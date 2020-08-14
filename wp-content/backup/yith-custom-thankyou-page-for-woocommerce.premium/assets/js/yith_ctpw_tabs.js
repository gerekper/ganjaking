/**
 * Social Box Tabs Admin Script Doc Comment
 *
 * @category Script
 * @package  Yith Custom Thank You Page for Woocommerce
 * @author    Armando Liccardo
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * @link http://www.yithemes.com
 */

( function () {
	'use strict'
	/**
	 * Tabs
	 *
	 * @description The Tabs component.
	 * @param {Object} options The options hash.
	 */
	var tabs = function ( options ) {

		var el = document.querySelector( options.el )
		var tabNavigationLinks = el.querySelectorAll( options.tabNavigationLinks )
		var tabContentContainers = el.querySelectorAll( options.tabContentContainers )
		var activeIndex = 0
		var initCalled = false

		/**
		 * Init
		 *
		 * @description Initializes the component by removing the no-js class from
		 *   the component, and attaching event listeners to each of the nav items.
		 *   Returns nothing.
		 */
		var init = function () {
			if ( !initCalled ) {
				initCalled = true
				el.classList.remove( 'no-js' )
				var tnl = tabNavigationLinks.length
				for ( var i = 0; i < tnl; i++ ) {
					var link = tabNavigationLinks[i]
					handleClick( link, i )
				}
			}
		}

		/**
		 * HandleClick
		 *
		 * @description Handles click event listeners on each of the links in the
		 *   tab navigation. Returns nothing.
		 * @param {HTMLElement} link The link to listen for events on
		 * @param {Number} index The index of that link
		 */
		var handleClick = function ( link, index ) {
			link.addEventListener( 'click', function ( e ) {
				e.preventDefault()
				goToTab( index )
				return false
			} )
		}

		/**
		 * GoToTab
		 *
		 * @description Goes to a specific tab based on index. Returns nothing.
		 * @param {Number} index The index of the tab to go to
		 */
		var goToTab = function ( index ) {
			if ( index !== activeIndex && index >= 0 && index <= tabNavigationLinks.length ) {
				tabNavigationLinks[activeIndex].classList.remove( 'is-active' )
				tabNavigationLinks[index].classList.add( 'is-active' )
				tabContentContainers[activeIndex].classList.remove( 'is-active' )
				tabContentContainers[index].classList.add( 'is-active' )
				activeIndex = index
			}
		}

		/**
		 * Returns init and goToTab
		 */
		return {
			init: init,
			goToTab: goToTab
		}

	}

	/**
	 * Attach to global namespace
	 */
	window.tabs = tabs

} )()
