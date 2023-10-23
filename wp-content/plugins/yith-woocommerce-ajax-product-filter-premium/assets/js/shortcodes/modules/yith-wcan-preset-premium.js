'use strict';

/* global globalThis, jQuery, yith_wcan_shortcodes, accounting */

import { $ } from '../config.js';
import YITH_WCAN_Preset from './yith-wcan-preset';
import YITH_WCAN_Dropdown_Premium from './yith-wcan-dropdown-premium';

export default class YITH_WCAN_Preset_Premium extends YITH_WCAN_Preset {
	// init object
	constructor( el ) {
		super( el );

		if ( this.isHorizontal() && ! globalThis.yith_wcan_backdrop_init ) {
			this._initBackdropClick();
			globalThis.yith_wcan_backdrop_init = true;
		}
	}

	// handles click outside of toggles, to close them in horizontal mode
	_initBackdropClick() {
		const isHorizontal = this.isHorizontal();

		if ( isHorizontal ) {
			$( document ).on( 'click', () => {
				if ( this.isMobile ) {
					return;
				}

				this.maybeCloseAllHorizontalToggles();
			} );
		}
	}

	// check if preset has horizontal layout
	isHorizontal() {
		return this.$preset?.hasClass( 'horizontal' );
	}

	// apply filters when possible
	maybeFilter( $initiator ) {
		const $filter = $initiator.closest( '.yith-wcan-filter' );

		// register status change
		this.maybeRegisterStatusChange();

		if (
			this.isHorizontal() &&
			! this.isMobile &&
			this.isHorizontalToggle( $filter.find( '.filter-title' ) )
		) {
			// show count when necessary
			this.maybeUpdateFiltersCount( $filter );

			// skip filtering when started from anything but Save and Clear buttons, in horizontal mode.
			if (
				! $initiator.hasClass( 'apply-filters' ) &&
				! $initiator.hasClass( 'clear-selection' ) &&
				! yith_wcan_shortcodes.instant_horizontal_filter
			) {
				return;
			}
		}

		if (
			yith_wcan_shortcodes.instant_filters &&
			yith_wcan_shortcodes.instant_horizontal_filter
		) {
			this.maybeCloseAllHorizontalToggles();
		}

		super.maybeFilter();
	}

	// show clear selection anchor
	maybeShowClearFilter( $filter ) {
		if (
			! this.isFilterActive( $filter ) ||
			! yith_wcan_shortcodes.show_clear_filter
		) {
			return;
		}

		if ( ! this.isHorizontal() || this.isMobile ) {
			super.maybeShowClearFilter( $filter );
		} else {
			// remove clear selection link if already added.
			$filter.find( '.clear-selection' ).remove();

			// add new clear selection link.
			const $clearButton = $( '<a/>', {
				class: 'clear-selection button',
				text: yith_wcan_shortcodes.labels.clear_selection,
				role: 'button',
			} );

			$clearButton
				.prependTo( $filter.find( '.filter-content-footer' ) )
				.on( 'click', ( ev ) => {
					ev.preventDefault();

					this.deactivateFilter(
						$filter,
						false,
						yith_wcan_shortcodes.instant_filters
					);
					this.maybeUpdateFiltersCount( $filter );
					this.maybeHideClearFilter( $filter );
					this.maybeFilter( $clearButton );

					this.maybeCloseHorizontalToggle(
						$filter.find( '.filter-title.collapsable' )
					);
				} );
		}
	}

	// init filter
	_initFilter( $filter ) {
		super._initFilter( $filter );
		this.maybeUpdateFiltersCount( $filter );
	}

	// init tooltip
	_initTooltip( $filter, position ) {
		if ( this.isHorizontal() ) {
			return;
		}

		super._initTooltip( $filter, position );
	}

	// trigger handling after layout change
	_afterLayoutChange() {
		super._afterLayoutChange();

		if ( this.isMobile ) {
			this._removeAllHorizontalTogglesFooter();
		} else {
			this._addAllHorizontalTogglesFooter();
		}
	}

	// init dropdown object
	_initDropdownObject( $dropdown, opts ) {
		return new YITH_WCAN_Dropdown_Premium( $dropdown, opts );
	}

	// init toggle to generic toggle/target pair
	_initToggle( $toggle, $container, $target ) {
		const isHorizontal = this.isHorizontal();

		if ( $container.hasClass( 'closed' ) ) {
			$target.hide();
		}

		if ( isHorizontal ) {
			this._addHorizontalToggleFooter( $toggle );
		}

		$toggle.off( 'click' ).on( 'click', ( ev ) => {
			ev.stopPropagation();

			if (
				isHorizontal &&
				! this.isMobile &&
				$toggle.hasClass( 'filter-title' )
			) {
				// first of all, close other toggles.
				this.maybeCloseAllHorizontalToggles( $target );

				// now open current one.
				$target.toggle( 0, () => {
					$container.toggleClass( 'opened' ).toggleClass( 'closed' );
				} );
			} else {
				this.toggle( $target, $container );
			}

			$target.trigger( 'yith_wcan_after_toggle_element', [ $container ] );
		} );

		if ( isHorizontal ) {
			$target.on( 'click', ( ev ) => {
				ev.stopPropagation();
			} );
		}
	}

	// check if a specific toggle should be shown in Horizontal mode
	isHorizontalToggle( $toggle ) {
		if ( ! this.isHorizontal() ) {
			return false;
		}

		return $toggle.is( '.collapsable' );
	}

	// close toggle for horizontal layout
	maybeCloseHorizontalToggle( $toggle ) {
		const $content = $toggle.next( '.filter-content' );

		if (
			! $content.length ||
			! this.isHorizontal() ||
			this.isMobile ||
			! $toggle.hasClass( 'filter-title' ) ||
			! this.isHorizontalToggle( $toggle )
		) {
			return;
		}

		$content.hide();
		$toggle.toggleClass( 'opened' ).toggleClass( 'closed' );
	}

	// close all Horizontal toggles
	maybeCloseAllHorizontalToggles( $exclude ) {
		$( '.yith-wcan-filters.horizontal.enhanced' )
			.find( '.filter-title.collapsable' )
			.next( '.filter-content' )
			.not( $exclude )
			.hide()
			.prev( '.filter-title' )
			.removeClass( 'opened' )
			.addClass( 'closed' );
	}

	// add filter content footer for horizontal layout
	_addHorizontalToggleFooter( $toggle ) {
		const $content = $toggle.next( '.filter-content' );

		if (
			! $content.length ||
			! this.isHorizontal() ||
			this.isMobile ||
			! $toggle.hasClass( 'filter-title' ) ||
			! this.isHorizontalToggle( $toggle ) ||
			yith_wcan_shortcodes.instant_horizontal_filter
		) {
			return;
		}

		const $currentFilter = $toggle.closest( '.yith-wcan-filter' ),
			$footer = $( '<div/>', {
				class: 'filter-content-footer',
			} ),
			$applyFilterButton = $( '<a/>', {
				class: 'apply-filters button alt',
				text: yith_wcan_shortcodes.labels.save,
			} );

		$applyFilterButton.on( 'click', () => {
			this.maybeFilter( $applyFilterButton );
			this.maybeCloseAllHorizontalToggles( $toggle );
		} );

		$footer.append( $applyFilterButton ).appendTo( $content );
	}

	// add filter content footer for all horizontal toggles
	_addAllHorizontalTogglesFooter() {
		const self = this;

		this.getFilters().each( function () {
			const $filter = $( this ),
				$toggle = $filter.find( '.filter-title.collapsable' );

			if ( ! $toggle.length ) {
				return;
			}

			self._addHorizontalToggleFooter( $toggle );
		} );
	}

	// remove filter content footer for horizontal layout
	_removeHorizontalToggleFooter( $toggle ) {
		const $content = $toggle.next( '.filter-content' );

		if ( ! $content.length ) {
			return;
		}

		$content.find( '.filter-content-footer' ).remove();
	}

	// add filter content footer for all horizontal toggles
	_removeAllHorizontalTogglesFooter() {
		const self = this;

		this.getFilters().each( function () {
			const $filter = $( this ),
				$toggle = $filter.find( '.filter-title.collapsable' );

			if ( ! $toggle.length ) {
				return;
			}

			self._removeHorizontalToggleFooter( $toggle );
		} );
	}

	// show filters count
	maybeUpdateFiltersCount( $filter ) {
		const $toggle = $filter.find( '.filter-title.collapsable' );

		// remove current count
		$toggle.find( '.filter-count' ).remove();

		if (
			! $toggle.length ||
			! this.isHorizontalToggle( $toggle ) ||
			! this.isFilterActive( $filter )
		) {
			return;
		}

		const count = this.countActiveItems( $filter );

		// if there is any filter active, show count
		if ( ! count ) {
			return;
		}

		const $counter = $( '<span/>', {
			class: 'filter-count',
			text: count,
		} );

		$toggle.append( $counter );
	}
}
