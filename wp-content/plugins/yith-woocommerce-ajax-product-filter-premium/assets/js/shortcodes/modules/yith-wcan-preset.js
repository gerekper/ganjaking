'use strict';

/* global globalThis, jQuery, yith_wcan_shortcodes, accounting */

import { $ } from '../config.js';
import YITH_WCAN_Dropdown from './yith-wcan-dropdown';

export default class YITH_WCAN_Preset {
	// main preset node
	preset = false;
	$preset = false;

	// target of the filter, if any
	target = false;
	$target = false;

	// filters node
	$filters = false;

	// filter button
	$filterButtons = false;

	// nodes created just for modal layout
	modalElements = {};

	// retains current status of filters
	activeFilters = false;

	// mobile flag
	isMobile = false;

	// slider timeout
	sliderTimeout = false;

	// registers when status has changed
	originalFilters = null;
	dirty = false;

	// init object
	constructor( el ) {
		// main preset node
		this.preset = '#' + el.attr( 'id' );
		this.$preset = el;

		// target of the filter, if any
		this.target = this.$preset.data( 'target' );
		this.$target = this.target ? $( this.target ) : false;

		this._regiterStatus();
		this._initFilterButton();
		this._initResponsive();
		this._initFilters();
		this._initActions();

		this.$preset
			.data( 'preset', this )
			.addClass( 'enhanced' )
			.trigger( 'yith_wcan_preset_initialized', [ this ] );
	}

	// init filters
	_initFilters() {
		const self = this;

		this.getFilters().each( function () {
			const $filter = $( this );

			self._initFilter( $filter );
		} );

		this.maybeShowClearAllFilters();
	}

	// init filter button
	_initFilterButton() {
		this.$filterButtons = this.$preset.find( '.apply-filters' );

		if ( ! this.$filterButtons.length ) {
			return;
		}

		// manage filter button
		this.$filterButtons
			.on( 'click', ( ev ) => {
				ev.preventDefault();
				this.filter();
			} )
			.hide();
	}

	// init generic actions
	_initActions() {
		this.$preset.find( 'form' ).on( 'submit', ( ev ) => {
			ev.preventDefault();
		} );
	}

	// init responsive
	_initResponsive() {
		if ( ! yith_wcan_shortcodes.modal_on_mobile ) {
			return;
		}

		const media = window.matchMedia(
			`(max-width: ${ yith_wcan_shortcodes.mobile_media_query }px)`
		);

		$( window )
			.on( 'resize', () => {
				const isMobile = !! media.matches;

				if ( isMobile !== this.isMobile ) {
					this.isMobile = isMobile;
					this._afterLayoutChange();
				}
			} )
			.resize();
	}

	// init filter
	_initFilter( $filter ) {
		const self = this,
			handleChange = function ( ev ) {
				const t = $( this ),
					$currentFilter = t.closest( '.yith-wcan-filter' ),
					multiple = $currentFilter.length
						? 'yes' === $currentFilter.data( 'multiple' )
						: false,
					$item = t.closest( '.filter-item' ),
					$items = $item.length
						? $currentFilter.find( '.filter-item' ).not( $item )
						: [];

				if ( $item.is( '.disabled' ) && ! $item.is( '.active' ) ) {
					ev.preventDefault();
					return false;
				}

				ev.preventDefault();

				$items.length &&
					! multiple &&
					$items
						.removeClass( 'active' )
						.children( 'label' )
						.find( ':input' )
						.prop( 'checked', false )
						.parent( '.checked' )
						.removeClass( 'checked' );
				$item.length && $item.toggleClass( 'active' );

				// reset active filters.
				self.activeFilters = false;

				self.maybeFilter( $filter );
				self.maybeToggleClearAllFilters();
				self.maybeToggleClearFilter( $currentFilter );
			};

		// handle filter activation/deactivation by click on label (no input involved)
		$filter
			.find( '.filter-item' )
			.not( '.checkbox' )
			.not( '.radio' )
			.on( 'click', 'a', function ( ev ) {
				const t = $( this ),
					$item = t.closest( '.filter-item' );

				if ( ! $( ev?.delegateTarget ).is( $item ) ) {
					return false;
				}

				handleChange.call( this, ev );
			} );

		// handle filter activation/deactivation from input change
		$filter.find( ':input' ).on( 'change', function ( ev ) {
			const t = $( this ),
				$item = t.closest( '.filter-item' );

			if ( $item.is( '.disabled' ) && ! $item.is( '.active' ) ) {
				t.prop( 'checked', false );
				return false;
			}

			handleChange.call( this, ev );
		} );

		// handle filter activation/deactivation by click on label (there is an input whose state can be switched)
		$filter.find( 'label > a' ).on( 'click', function ( ev ) {
			const t = $( this ),
				$item = t.closest( '.filter-item' );

			ev.preventDefault();

			if ( $item.is( '.disabled' ) && ! $item.is( '.active' ) ) {
				return false;
			}

			const $input = t.parent().find( ':input' );

			if (
				$input.is( '[type="radio"]' ) ||
				$input.is( '[type="checkbox"]' )
			) {
				$input.prop( 'checked', ! $input.prop( 'checked' ) );
			}

			$input.change();
		} );

		// init tooltip
		this._initTooltip( $filter );

		// init price slider
		this._initPriceSlider( $filter );

		// init dropdown
		this._initDropdown( $filter );

		// init collapsable
		this._initCollapsable( $filter );

		// init clear anchors
		this.maybeShowClearFilter( $filter );

		// init custom inputs
		if ( this.$preset?.hasClass( 'custom-style' ) ) {
			this._initCustomInput( $filter );
			$filter.on( 'yith_wcan_dropdown_updated', function () {
				const $dropdown = $( this ),
					$current = $dropdown.closest( '.yith-wcan-filter' );

				self._initCustomInput( $current );
			} );
		}
	}

	// init tooltip
	_initTooltip( $filter, position ) {
		$filter.find( '[data-title]' ).each( function () {
			const t = $( this );

			if ( t.hasClass( 'tooltip-added' ) || ! t.data( 'title' ) ) {
				return;
			}

			t.on( 'mouseenter', function () {
				let th = $( this ),
					tooltip = null,
					wrapperWidth = th.outerWidth(),
					left = 0,
					width = 0;

				if (
					! position ||
					( 'top' !== position && 'right' !== position )
				) {
					const container = th.closest( '.filter-item' );

					position =
						container.hasClass( 'color' ) ||
						container.hasClass( 'label' )
							? 'top'
							: 'right';
				}

				tooltip = $( '<span>', {
					class: 'yith-wcan-tooltip',
					html: th.data( 'title' ),
				} );

				th.append( tooltip );

				width = tooltip.outerWidth() + 6;
				tooltip.outerWidth( width );

				if ( 'top' === position ) {
					left = ( wrapperWidth - width ) / 2;
				} else {
					left = wrapperWidth + 15;
				}

				tooltip.css( { left: left.toFixed( 0 ) + 'px' } ).fadeIn( 200 );

				th.addClass( 'with-tooltip' );
			} ).on( 'mouseleave', function () {
				const th = $( this );

				th.find( '.yith-wcan-tooltip' ).fadeOut( 200, function () {
					th.removeClass( 'with-tooltip' )
						.find( '.yith-wcan-tooltip' )
						.remove();
				} );
			} );

			t.addClass( 'tooltip-added' );
		} );
	}

	// init dropdown
	_initDropdown( $filter ) {
		const $dropdown = $filter.find( 'select.filter-dropdown' );

		if ( ! $dropdown.length ) {
			return;
		}

		if (
			$dropdown.hasClass( 'select2-hidden-accessible' ) &&
			'undefined' !== typeof $.fn.selectWoo
		) {
			$dropdown.selectWoo( 'destroy' );
		}

		this._initDropdownObject( $dropdown, {
			paginate: true,
			perPage: yith_wcan_shortcodes.terms_per_page,
		} );
	}

	// init dropdown object
	_initDropdownObject( $dropdown, opts ) {
		return new YITH_WCAN_Dropdown( $dropdown, opts );
	}

	// init price slider
	_initPriceSlider( $filter ) {
		if ( ! $filter.hasClass( 'filter-price-slider' ) ) {
			return;
		}

		const self = this,
			$container = $filter.find( '.price-slider' ),
			$minInput = $container.find( '.price-slider-min' ),
			$maxInput = $container.find( '.price-slider-max' ),
			min = parseFloat( $container.data( 'min' ) ),
			max = parseFloat( $container.data( 'max' ) ),
			currentMin = parseFloat( $minInput.val() ),
			currentMax = parseFloat( $maxInput.val() ),
			step = parseFloat( $container.data( 'step' ) ),
			handleSliderChange = function () {
				if ( self.sliderTimeout ) {
					clearTimeout( self.sliderTimeout );
				}

				self.sliderTimeout = setTimeout( () => {
					self.maybeFilter( $filter );
				}, 300 );
			};

		$filter
			.find( '.price-slider-ui' )
			.off( 'change' )
			.ionRangeSlider( {
				skin: 'round',
				type: 'double',
				min,
				max,
				step,
				from: currentMin,
				to: currentMax,
				min_interval: step,
				values_separator: ' - ',
				prettify: ( v ) => this.formatPrice( v ),
				onChange: ( data ) => {
					$minInput.val( data.from );
					$maxInput.val( data.to );
				},
				onFinish: handleSliderChange,
			} );

		$minInput
			.add( $maxInput )
			.off( 'change' )
			.on( 'change', handleSliderChange )
			.on( 'keyup', ( ev ) => {
				if ( ! ev.key.match( /[0-9,.]/ ) ) {
					ev.preventDefault();
					return false;
				}

				if ( ! $minInput.val() || ! $maxInput.val() ) {
					return;
				}

				handleSliderChange();
			} );
	}

	// init collapsable
	_initCollapsable( $filter ) {
		this._initTitleCollapsable( $filter );
		this._initHierarchyCollapsable( $filter );
	}

	// init toggle on click of the title
	_initTitleCollapsable( $filter ) {
		const $title = $filter.find( '.collapsable' );

		if ( ! $title.length ) {
			return;
		}

		this._initToggle( $title, $title, $filter.find( '.filter-content' ) );
	}

	// init toggle on click of the parent li
	_initHierarchyCollapsable( $filter ) {
		const $items = $filter.find( '.hierarchy-collapsable' );

		if ( ! $items.length ) {
			return;
		}

		// set parents of currently active term as open
		const self = this,
			active = $filter.find( '.active' );

		if ( active.length ) {
			active
				.parents( '.hierarchy-collapsable' )
				.removeClass( 'closed' )
				.addClass( 'opened' );

			if (
				active.hasClass( 'hierarchy-collapsable' ) &&
				yith_wcan_shortcodes.show_current_children
			) {
				active.removeClass( 'closed' ).addClass( 'opened' );
			}
		}

		$items.each( function () {
			const $t = $( this ),
				$toggle = $( '<span/>', {
					class: 'toggle-handle',
				} );

			$toggle.appendTo( $t );

			self._initToggle( $toggle, $t, $t.children( 'ul.filter-items' ) );
		} );
	}

	// init toggle to generic toggle/target pair
	_initToggle( $toggle, $container, $target ) {
		if ( $container.hasClass( 'closed' ) ) {
			$target.hide();
		}

		$toggle.off( 'click' ).on( 'click', ( ev ) => {
			ev.stopPropagation();
			ev.preventDefault();

			this.toggle( $target, $container );

			$target.trigger( 'yith_wcan_after_toggle_element', [ $container ] );
		} );
	}

	// init custom input
	_initCustomInput( $filter ) {
		$filter.find( ':input' ).each( function () {
			let input = $( this ),
				type = input.attr( 'type' ),
				containerClass = `${ type }button`,
				container;

			if ( 'checkbox' !== type && 'radio' !== type ) {
				return;
			}

			if ( input.closest( `.${ containerClass }` ).length ) {
				return;
			}

			if ( input.is( ':checked' ) ) {
				containerClass += ' checked';
			}

			container = $( '<span/>', {
				class: containerClass,
			} );

			input.wrap( container ).on( 'change', function () {
				const t = $( this );

				t.prop( 'checked' )
					? t.parent().addClass( 'checked' )
					: t.parent().removeClass( 'checked' );
			} );
		} );
	}

	// register initial status
	_regiterStatus() {
		this.originalFilters = this.getFiltersProperties();
	}

	// trigger handling after layout change
	_afterLayoutChange() {
		if ( this.isMobile ) {
			this.$preset
				.addClass( 'filters-modal' )
				.attr( 'role', 'dialog' )
				.attr( 'tabindex', '-1' )
				.hide();

			this._addCloseModalButton();
			this._addApplyFiltersModalButton();
			this._switchToCollapsables();

			this.$filterButtons?.hide();
		} else {
			this.$preset
				.removeClass( 'filters-modal' )
				.removeClass( 'open' )
				.removeAttr( 'role' )
				.removeAttr( 'tabindex' )
				.show();

			$( 'body' )
				.css( 'overflow', 'auto' )
				.removeClass( 'yith-wcan-preset-modal-open' );

			this._removeCloseModalButton();
			this._removeApplyFiltersModalButton();
			this._switchBackCollapsables();

			this.$filterButtons?.show();
		}
	}

	// add modal close button
	_addCloseModalButton() {
		const $closeButton = $( '<a/>', {
			class: 'close-button',
			html: '&times;',
			'data-dismiss': 'modal',
			'aria-label': yith_wcan_shortcodes.labels.close,
		} );

		$closeButton
			.prependTo( this.$preset )
			.on( 'click', this.closeModal.bind( this ) );
		this.modalElements.closeButton = $closeButton;
	}

	// remove modal close button
	_removeCloseModalButton() {
		this.modalElements?.closeButton?.remove();
	}

	// show main filter button for the modal
	_addApplyFiltersModalButton() {
		const $filterButton = $( '<button/>', {
			class: 'apply-filters main-modal-button',
			html: yith_wcan_shortcodes.labels.show_results,
			'data-dismiss': 'modal',
		} );

		$filterButton.appendTo( this.$preset ).on( 'click', () => {
			this.filter();
			this.closeModal();
		} );
		this.modalElements.applyFiltersButton = $filterButton;
	}

	// hide main filter button for the modal
	_removeApplyFiltersModalButton() {
		this.modalElements?.applyFiltersButton?.remove();
	}

	// convert all filters to collapsable
	_switchToCollapsables() {
		const self = this;

		this.getFilters().each( function () {
			const $filter = $( this ),
				$title = $filter.find( '.filter-title' );

			if ( ! $title.length || $title.hasClass( 'collapsable' ) ) {
				return;
			}

			$title.addClass( 'collapsable' ).data( 'disable-collapse', true );

			self._initTitleCollapsable( $filter );
		} );
	}

	// switch back filters to their previous collapsable state
	_switchBackCollapsables() {
		this.getFilters().each( function () {
			const $filter = $( this ),
				$title = $filter.find( '.filter-title' );

			if (
				! $title.length ||
				! $title.hasClass( 'collapsable' ) ||
				! $title.data( 'disable-collapse' )
			) {
				return;
			}

			$title
				.removeClass( 'collapsable' )
				.removeData( 'disable-collapse', true )
				.off( 'click' );

			$filter.find( '.filter-content' ).show();
		} );
	}

	// close all collpasable before showing modal
	_openAllCollapsables() {
		this.$filters
			.not( '.no-title' )
			.not( ( i, v ) => {
				return this.isFilterActive( $( v ) );
			} )
			.find( '.filter-content' )
			.show()
			.end()
			.find( '.filter-title' )
			.removeClass( 'closed' )
			.addClass( 'opened' );
	}

	// close all collpasable before showing modal
	_closeAllCollapsables() {
		this.$filters
			.not( '.no-title' )
			.not( ( i, v ) => {
				return this.isFilterActive( $( v ) );
			} )
			.find( '.filter-content' )
			.hide()
			.end()
			.find( '.filter-title' )
			.addClass( 'closed' )
			.removeClass( 'opened' );
	}

	// update status change flag, if filters have changed
	maybeRegisterStatusChange() {
		const currentFilters = this.getFiltersProperties(),
			currentStr = JSON.stringify( currentFilters ),
			originalStr = JSON.stringify( this.originalFilters );

		this.dirty = currentStr !== originalStr;
	}

	// apply filters when possible
	maybeFilter( $initiator ) {
		// register status change
		this.maybeRegisterStatusChange();

		// filter, or show filter button.
		if ( yith_wcan_shortcodes.instant_filters && ! this.isMobile ) {
			this.filter();
		} else if (
			! yith_wcan_shortcodes.instant_filters &&
			! this.isMobile
		) {
			this.dirty
				? this.$filterButtons?.show()
				: this.$filterButtons?.hide();
		} else if ( this.isMobile && this.dirty ) {
			this.$preset.addClass( 'with-filter-button' );
			this.modalElements.applyFiltersButton?.show();
		}
	}

	// main filtering method
	filter() {
		const filter = window?.product_filter;

		filter
			?.doFilter( this.getFiltersProperties(), this.target, this.preset )
			?.done( () => {
				let newPreset = $( this.preset );

				if ( newPreset.length && yith_wcan_shortcodes.scroll_top ) {
					// by default, scroll till top of first preset in the page.
					let targetOffset = newPreset.offset().top;

					if ( !! yith_wcan_shortcodes.scroll_target ) {
						// when we have a specific target, use that for the offset.
						const $scrollTarget = $(
							yith_wcan_shortcodes.scroll_target
						);

						targetOffset = $scrollTarget.length
							? $scrollTarget.offset().top
							: targetOffset;
					} else if ( this.isMobile ) {
						// otherwise, if we're on mobile, scroll to the top of the page
						// (preset could be in an unexpected location).
						targetOffset = 100;
					}

					$( 'body, html' ).animate( {
						scrollTop: targetOffset - 100,
					} );
				}

				// register new filters, clear status flag
				this.originalFilters = this.getFiltersProperties();
				this.dirty = false;
			} );

		if ( this.isMobile ) {
			this.$preset.removeClass( 'with-filter-button' );
			this.modalElements.applyFiltersButton?.hide();
			this.closeModal();
		}
	}

	// get all filter nodes
	getFilters() {
		if ( false === this.$filters ) {
			this.$filters = this.$preset.find( '.yith-wcan-filter' );
		}

		return this.$filters;
	}

	// retrieves all filters that we want to apply
	getActiveFilters() {
		if ( false === this.activeFilters ) {
			this.activeFilters = this.getFiltersProperties();
		}

		return this.activeFilters;
	}

	// check whether there is any filter active
	isAnyFilterActive() {
		return !! Object.keys( this.getActiveFilters() ).length;
	}

	// checks whether current filter is active
	isFilterActive( $filter ) {
		let filterType = $filter.data( 'filter-type' ),
			active,
			filteredActive;

		switch ( filterType ) {
			case 'tax':
			case 'review':
			case 'price_range':
				const $dropdown = $filter.find( '.filter-dropdown' );

				if ( $dropdown.length ) {
					const val = $dropdown.val();

					active = 'object' === typeof val ? !! val?.length : !! val;
					break;
				}

			// if we use type other than dropdown, fallthrough
			case 'stock_sale':
				active = $filter
					.find( '.filter-item' )
					.filter( '.active' ).length;
				break;
			case 'price_slider':
				const step = parseFloat(
						$filter.find( '.price-slider' ).data( 'step' )
					),
					min = parseFloat(
						$filter.find( '.price-slider' ).data( 'min' )
					),
					max = parseFloat(
						$filter.find( '.price-slider' ).data( 'max' )
					),
					currentMin = parseFloat(
						$filter.find( '.price-slider-min' ).val()
					),
					currentMax = parseFloat(
						$filter.find( '.price-slider-max' ).val()
					);

				active =
					Math.abs( currentMin - min ) >= step ||
					Math.abs( currentMax - max ) >= step;
				break;
			case 'orderby':
				active =
					'menu_order' !== $filter.find( '.filter-order-by' ).val();
				break;
			default:
				active = false;
				break;
		}

		filteredActive = $filter.triggerHandler( 'yith_wcan_is_filter_active', [
			active,
			this,
		] );
		active =
			typeof filteredActive !== 'undefined' ? filteredActive : active;

		return active;
	}

	// count the number of active items per filter
	countActiveItems( $filter ) {
		let filterType = $filter.data( 'filter-type' ),
			count;

		switch ( filterType ) {
			case 'tax':
			case 'review':
			case 'price_range':
				const $dropdown = $filter.find( '.filter-dropdown' );

				if ( $dropdown.length ) {
					const val = $dropdown.val();

					count = 'object' === typeof val ? val?.length : +!! val;
					break;
				}

			// if we use type other than dropdown, fallthrough
			case 'stock_sale':
				count = $filter
					.find( '.filter-items' )
					.find( '.active' ).length;
				break;
			case 'orderby':
				if ( this.isFilterActive( $filter ) ) {
					count = 1;
				}
				break;
			case 'price_slider':
			default:
				count = 0;
				break;
		}

		return count;
	}

	// retrieves filter properties for the filter
	getFilterProperties( $filter ) {
		let filterType = $filter.data( 'filter-type' ),
			multiple = 'yes' === $filter.data( 'multiple' ),
			$dropdown = $filter.find( '.filter-dropdown' ),
			properties = {},
			filteredProperties,
			$active;

		switch ( filterType ) {
			case 'tax':
				let activeTerms = [],
					taxonomy = $filter.data( 'taxonomy' ),
					isAttr = 0 === taxonomy.indexOf( 'filter' ),
					relation = $filter.data( 'relation' );

				if ( $dropdown.length ) {
					if ( multiple ) {
						activeTerms = $dropdown.val();
					} else {
						activeTerms.push( $dropdown.val() );
					}
				} else {
					$active = $filter
						.find( '.filter-item' )
						.filter( '.active' )
						.children( 'a, label' );

					activeTerms = $active.get().reduce( function ( a, v ) {
						let val;

						v = $( v );
						val = v.is( 'label' )
							? v.find( ':input' ).val()
							: v.data( 'term-slug' );

						if ( ! val ) {
							return a;
						}

						a.push( val );

						return a;
					}, activeTerms );
				}

				if ( ! multiple ) {
					properties[ taxonomy ] = activeTerms.pop();
				} else {
					const glue = ! isAttr && 'and' === relation ? '+' : ',';
					properties[ taxonomy ] = activeTerms.join( glue );
				}

				if ( isAttr ) {
					properties[ taxonomy.replace( 'filter_', 'query_type_' ) ] =
						relation;
				}

				break;
			case 'review':
				if ( $dropdown.length ) {
					properties.rating_filter = $dropdown.val();
				} else {
					$active = $filter
						.find( '.filter-item' )
						.filter( '.active' )
						.children( 'a, label' );

					if ( ! multiple ) {
						$active = $active.first();
						properties.rating_filter = $active.is( 'label' )
							? $active.find( ':input' ).val()
							: $active.data( 'rating' );
					} else {
						properties.rating_filter = $active
							.get()
							.reduce( function ( a, v ) {
								let val;

								v = $( v );
								val = v.is( 'label' )
									? v.find( ':input' ).val()
									: v.data( 'rating' );

								if ( ! val ) {
									return a;
								}

								a.push( val );

								return a;
							}, [] )
							.join( ',' );
					}
				}
				break;
			case 'price_range':
				if ( $dropdown.length ) {
					if ( multiple ) {
						properties.price_ranges = $dropdown.val().join( ',' );
					} else {
						properties.min_price = $dropdown
							.val()
							.split( '-' )[ 0 ];
						properties.max_price = $dropdown
							.val()
							.split( '-' )[ 1 ];
					}
				} else {
					$active = $filter
						.find( '.filter-item' )
						.filter( '.active' )
						.children( 'a, label' );

					if ( multiple ) {
						properties.price_ranges = $active
							.get()
							.reduce( ( a, v ) => {
								let min = $( v ).data( 'range-min' ),
									max = $( v ).data( 'range-max' );

								a += ( max ? `${ min }-${ max }` : min ) + ',';

								return a;
							}, '' )
							.replace( /^(.*),$/, '$1' );
					} else {
						properties.min_price = parseFloat(
							$active.first().data( 'range-min' )
						);
						properties.max_price = parseFloat(
							$active.first().data( 'range-max' )
						);
					}
				}
				break;
			case 'price_slider':
				properties.min_price = parseFloat(
					$filter.find( '.price-slider-min' ).val()
				);
				properties.max_price = parseFloat(
					$filter.find( '.price-slider-max' ).val()
				);
				break;
			case 'stock_sale':
				if ( $filter.find( '.filter-on-sale' ).is( '.active' ) ) {
					properties.onsale_filter = 1;
				}
				if ( $filter.find( '.filter-in-stock' ).is( '.active' ) ) {
					properties.instock_filter = 1;
				}
				if ( $filter.find( '.filter-featured' ).is( '.active' ) ) {
					properties.featured_filter = 1;
				}
				break;
			case 'orderby':
				properties.orderby = $filter.find( '.filter-order-by' ).val();
				break;
			default:
				break;
		}

		filteredProperties = $filter.triggerHandler(
			'yith_wcan_filter_properties',
			[ properties, self ]
		);
		properties =
			typeof filteredProperties !== 'undefined'
				? filteredProperties
				: properties;

		return properties;
	}

	// retrieves properties for all filters of the preset
	getFiltersProperties() {
		let properties = {};
		const self = this;

		this.getFilters().each( function () {
			const $filter = $( this );

			if ( self.isFilterActive( $filter ) ) {
				const filterProperties = self.getFilterProperties( $filter );

				properties = self.mergeProperties(
					properties,
					filterProperties,
					$filter
				);
			}
		} );

		return properties;
	}

	// retrieve filters matching any of the properties passed
	getFiltersByProperties( properties ) {
		const self = this;

		return this.getFilters().filter( function () {
			const $filter = $( this );

			if ( self.isFilterActive( $filter ) ) {
				let filterProperties = self.getFilterProperties( $filter ),
					hasProp = false;

				for ( const prop in properties ) {
					if (
						[ 'min_price', 'max_price', 'price_ranges' ].includes(
							prop
						) &&
						( filterProperties.min_price ||
							filterProperties.price_ranges )
					) {
						hasProp = true;
						break;
					} else if ( filterProperties[ prop ] ) {
						hasProp = true;
						break;
					}
				}

				return hasProp;
			}

			return false;
		} );
	}

	// show clear selection anchor
	maybeToggleClearFilter( $filter ) {
		if ( ! this.isFilterActive( $filter ) ) {
			this.maybeHideClearFilter( $filter );
		} else {
			this.maybeShowClearFilter( $filter );
		}
	}

	// show clear all selections anchor
	maybeToggleClearAllFilters() {
		if ( ! this.isAnyFilterActive() ) {
			this.maybeHideClearAllFilters();
		} else {
			this.maybeShowClearAllFilters();
		}
	}

	// show clear selection anchor
	maybeShowClearFilter( $filter ) {
		if (
			! this.isFilterActive( $filter ) ||
			! yith_wcan_shortcodes.show_clear_filter
		) {
			return;
		}

		// remove clear selection link if already added.
		$filter.find( '.clear-selection' ).remove();

		// add new clear selection link.
		$( '<a/>', {
			class: 'clear-selection',
			text: yith_wcan_shortcodes.labels.clear_selection,
			role: 'button',
		} )
			.prependTo( $filter.find( '.filter-content' ) )
			.on( 'click', ( ev ) => {
				ev.preventDefault();

				this.deactivateFilter(
					$filter,
					false,
					yith_wcan_shortcodes.instant_filters
				);
				this.maybeHideClearFilter( $filter );

				if ( yith_wcan_shortcodes.instant_filters ) {
					this.closeModal();
				}
			} );
	}

	// show clearAll anchor, when on mobile layout
	maybeShowClearAllFilters() {
		if ( ! this.isAnyFilterActive() || ! this.isMobile ) {
			return;
		}

		// remove clear selection link if already added.
		this.$preset.find( '.clear-selection' ).remove();

		// add new clear selection link.
		$( '<a/>', {
			class: 'clear-selection',
			text: yith_wcan_shortcodes.labels.clear_all_selections,
			role: 'button',
		} )
			.prependTo( this.$preset.find( '.filters-container' ) )
			.on( 'click', ( ev ) => {
				ev.preventDefault();

				this.deactivateAllFilters(
					yith_wcan_shortcodes.instant_filters
				);
				this.maybeHideClearAllFilters();

				if ( yith_wcan_shortcodes.instant_filters ) {
					this.closeModal();
				}
			} );
	}

	// hide clear selection anchor
	maybeHideClearFilter( $filter ) {
		if (
			this.isFilterActive( $filter ) ||
			! yith_wcan_shortcodes.show_clear_filter
		) {
			return;
		}

		// remove clear selection link.
		$filter.find( '.clear-selection' ).remove();
	}

	// show clearAll anchor, when on mobile layout
	maybeHideClearAllFilters() {
		if ( this.isAnyFilterActive() ) {
			return;
		}

		// remove clear selection link.
		this.$preset
			.find( '.filters-container' )
			.children( '.clear-selection' )
			.remove();
	}

	// deactivate filter
	deactivateFilter( $filter, properties, doFilter ) {
		const filterType = $filter.data( 'filter-type' ),
			$items = $filter.find( '.filter-item' ),
			$activeItems = $items.filter( '.active' ),
			$dropdown = $filter.find( '.filter-dropdown' );

		switch ( filterType ) {
			case 'tax':
				const taxonomy = $filter.data( 'taxonomy' );

				if ( $dropdown.length ) {
					if ( ! properties ) {
						$dropdown.find( 'option' ).prop( 'selected', false );
					} else {
						$dropdown.find( 'option' ).each( function () {
							const $option = $( this );

							if (
								$option.val().toString() ===
								properties[ taxonomy ].toString()
							) {
								$option.prop( 'selected', false );
							}
						} );
					}

					$dropdown.change();
				} else if ( ! properties ) {
					$activeItems.children( 'label' ).children( 'a' ).click();
					$activeItems.removeClass( 'active' );
				} else {
					$activeItems.each( function () {
						let $item = $( this ),
							$label = $item.children( 'label' ),
							$anchor = $item.children( 'a' ),
							value;

						value = $label.length
							? $label.find( ':input' ).val()
							: $anchor.data( 'term-slug' );

						if (
							value.toString() ===
							properties[ taxonomy ].toString()
						) {
							$item.children( 'label' ).children( 'a' ).click();
							$item.removeClass( 'active' );
						}
					} );
				}
				break;
			case 'review':
				if ( $dropdown.length ) {
					if ( ! properties ) {
						$dropdown.find( 'option' ).prop( 'selected', false );
					} else {
						$dropdown.find( 'option' ).each( function () {
							const $option = $( this );

							if ( $option.val() === properties.rating_filter ) {
								$option.prop( 'selected', false );
							}
						} );
					}

					$dropdown.change();
				} else if ( ! properties ) {
					$activeItems.children( 'label' ).children( 'a' ).click();
					$activeItems.removeClass( 'active' );
				} else {
					$activeItems.each( function () {
						let $item = $( this ),
							$label = $item.children( 'label' ),
							$anchor = $item.children( 'a' ),
							value;

						value = $label.length
							? $label.find( ':input' ).val()
							: $anchor.data( 'rating' );

						if ( value === properties.rating_filter ) {
							$item.children( 'label' ).children( 'a' ).click();
							$item.removeClass( 'active' );
						}
					} );
				}
				break;
			case 'price_range':
				if ( $dropdown.length ) {
					if ( ! properties ) {
						$dropdown.find( 'option' ).prop( 'selected', false );
					} else {
						$dropdown.find( 'option' ).each( function () {
							const $option = $( this ),
								formattedRange =
									properties.min_price +
									( properties.max_price
										? `-${ properties.max_price }`
										: '' );

							if ( $option.val() === formattedRange ) {
								$option.prop( 'selected', false );
							}
						} );
					}

					$dropdown.change();
				} else if ( ! properties ) {
					$activeItems.children( 'label' ).children( 'a' ).click();
					$activeItems.removeClass( 'active' );
				} else {
					$activeItems.each( function () {
						let $item = $( this ),
							$label = $item.children( 'label' ),
							$anchor = $item.children( 'a' ),
							formattedRange,
							value;

						value = $label.length
							? $label.find( ':input' ).val()
							: $anchor.data( 'min_price' ) +
							  ( $anchor.data( 'max_price' )
									? '-' + $anchor.data( 'max_price' )
									: '' );

						if ( properties.min_price ) {
							formattedRange =
								properties.min_price +
								( properties.max_price
									? '-' + properties.max_price
									: '' );
						} else if ( properties.price_ranges ) {
							formattedRange = properties.price_ranges;
						}

						if ( value === formattedRange ) {
							$item.children( 'label' ).children( 'a' ).click();
							$item.removeClass( 'active' );
						}
					} );
				}
				break;
			case 'price_slider':
				const $priceSlider = $filter.find( '.price-slider' );

				$filter
					.find( '.price-slider-min' )
					.val( $priceSlider.data( 'min' ) );
				$filter
					.find( '.price-slider-max' )
					.val( $priceSlider.data( 'max' ) )
					.change();
				break;
			case 'orderby':
				$filter.find( 'select' ).val( 'menu_order' );
				break;
			case 'stock_sale':
				if ( ! properties ) {
					$filter
						.find( '.filter-in-stock' )
						.find( ':input' )
						.prop( 'checked', false )
						.change();
					$filter
						.find( '.filter-on-sale' )
						.find( ':input' )
						.prop( 'checked', false )
						.change();
					$filter
						.find( '.filter-featured' )
						.find( ':input' )
						.prop( 'checked', false )
						.change();

					$items.removeClass( 'active' );
				} else {
					if ( properties?.instock_filter ) {
						$filter
							.find( '.filter-in-stock' )
							.find( ':input' )
							.prop( 'checked', false )
							.change()
							.closest( '.filter-item' )
							.removeClass( 'active' );
					}

					if ( properties?.onsale_filter ) {
						$filter
							.find( '.filter-on-sale' )
							.find( ':input' )
							.prop( 'checked', false )
							.change()
							.closest( '.filter-item' )
							.removeClass( 'active' );
					}

					if ( properties?.featured_filter ) {
						$filter
							.find( '.filter-featured' )
							.find( ':input' )
							.prop( 'checked', false )
							.change()
							.closest( '.filter-item' )
							.removeClass( 'active' );
					}
				}
				break;
			default:
				$items.removeClass( 'active' );
				break;
		}

		this.activeFilters = false;

		if ( doFilter ) {
			this.filter();
		}
	}

	// deactivate all filters
	deactivateAllFilters( doFilter ) {
		const self = this,
			$filters = this.getFilters();

		$filters.each( function () {
			const $filter = $( this );

			self.deactivateFilter( $filter );
		} );

		this.activeFilters = false;

		if ( doFilter ) {
			this.filter();
		}

		return true;
	}

	// deactivate filters that matches a specific set of properties
	deactivateFilterByProperties( properties, doFilter ) {
		const self = this,
			$filters = this.getFiltersByProperties( properties );

		if ( ! $filters.length ) {
			return false;
		}

		$filters.each( function () {
			const $filter = $( this );

			self.deactivateFilter( $filter, properties, doFilter );
		} );

		return true;
	}

	// open toggle
	toggle( $target, $container, status ) {
		if ( 'undefined' === typeof status ) {
			status = $container.hasClass( 'closed' );
		}

		const method = status ? 'slideDown' : 'slideUp',
			classToAdd = status ? 'opened' : 'closed',
			classToRemove = status ? 'closed' : 'opened';

		$target[ method ]( 400, () => {
			$container.addClass( classToAdd ).removeClass( classToRemove );

			$target.trigger( 'yith_wcan_toggle_element', [
				$container,
				status,
			] );
		} );
	}

	// open filter if title is collapsable
	openFilter( $filter ) {
		const $title = $filter.find( '.collapsable' );

		if ( ! $title.length ) {
			return;
		}

		this.toggle( $filter.find( '.filter-content' ), $title, true );
	}

	// open all filters in a preset
	openAllFilters( $filter ) {
		const self = this,
			$filters = this.getFilters();

		$filters.each( function () {
			self.openFilter( $( this ) );
		} );
	}

	// close filter if title is collapsable
	closeFilter( $filter ) {
		const $title = $filter.find( '.collapsable' );

		if ( ! $title.length ) {
			return;
		}

		this.toggle( $filter.find( '.filter-content' ), $title, false );
	}

	// close all filters in a preset; if a specific filter is pased as parameter, system will keep it open
	closeAllFilters( $filter ) {
		const self = this,
			$filters = this.getFilters();

		$filters.each( function () {
			self.closeFilter( $( this ) );
		} );

		if ( 'undefined' !== typeof $filter ) {
			this.openFilter( $filter );
		}
	}

	// open filters as a modal, when in mobile layout
	openModal() {
		if ( ! this.isMobile ) {
			return;
		}

		if ( yith_wcan_shortcodes.toggles_open_on_modal ) {
			this._openAllCollapsables();
		} else {
			this._closeAllCollapsables();
		}

		$( 'body' )
			.css( 'overflow', 'hidden' )
			.addClass( 'yith-wcan-preset-modal-open' );

		this.$preset.show();

		setTimeout( () => {
			this.$preset.addClass( 'open' );
		}, 100 );
	}

	// close filters modal, when in mobile layout
	closeModal() {
		if ( ! this.isMobile ) {
			return;
		}

		this.$preset.removeClass( 'open' );

		setTimeout( () => {
			this.$preset.hide();
			$( 'body' )
				.css( 'overflow', 'auto' )
				.removeClass( 'yith-wcan-preset-modal-open' );
		}, 300 );
	}

	// utility that formats the price according to store configuration.
	formatPrice( price ) {
		if ( 'undefined' !== typeof accounting ) {
			price = accounting.formatMoney( price, {
				symbol: yith_wcan_shortcodes.currency_format?.symbol,
				decimal: yith_wcan_shortcodes.currency_format?.decimal,
				thousand: yith_wcan_shortcodes.currency_format?.thousand,
				precision: 0,
				format: yith_wcan_shortcodes.currency_format?.format,
			} );
		}

		return price;
	}

	// utility that merges together sets of filter properties
	mergeProperties( set1, set2, $filter ) {
		// search for common properties
		for ( const prop in set2 ) {
			if ( ! set2.hasOwnProperty( prop ) ) {
				continue;
			}

			if ( !! set1[ prop ] ) {
				switch ( prop ) {
					case 'rating_filter':
					case 'min_price':
					case 'max_price':
					case 'onsale_filter':
					case 'instock_filter':
					case 'orderby':
						// just override default value
						set1[ prop ] = set2[ prop ];
						break;
					default:
						if ( 0 === prop.indexOf( 'query_type_' ) ) {
							// query_type param
							set1[ prop ] = set2[ prop ];
						} else {
							// we're dealing with taxonomy
							const isAttr = 0 === prop.indexOf( 'filter_' ),
								glue = isAttr ? ',' : '+';

							let newValue =
								set1[ prop ].replace( ',', glue ) +
								glue +
								set2[ prop ].replace( ',', glue );

							newValue = newValue
								.split( glue )
								.filter(
									( value, index, arr ) =>
										arr.indexOf( value ) === index
								)
								.join( glue );

							set1[ prop ] = newValue;

							if ( isAttr ) {
								const queryTypeParam = prop.replace(
									'filter_',
									'query_type_'
								);

								set1[ queryTypeParam ] = 'and';
								set2[ queryTypeParam ] = 'and';
							}
						}
				}

				delete set2[ prop ];
			}
		}

		$.extend( set1, set2 );

		return set1;
	}
}
