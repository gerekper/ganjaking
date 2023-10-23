'use strict';

/* global globalThis, jQuery, yith_wcan_shortcodes, accounting */

import { $ } from '../config.js';

export default class YITH_WCAN_Dropdown {
	// current button
	$originalSelect = null;

	// main element
	$_main = null;

	// label element
	$_label = null;

	// dropdown
	$_dropdown = null;

	// search input
	$_search = null;

	// show more link
	$_showMore = null;

	// items list
	$_items = null;

	// current page
	currentPage = 1;

	// options
	options = {};

	// init object
	constructor( el, opts ) {
		this.$originalSelect = el;

		if ( ! this.$originalSelect.is( 'select' ) ) {
			return;
		}

		const defaultPerPage = this.$originalSelect.data( 'per_page' ),
			defaultOrder = this.$originalSelect.data( 'order' ),
			defaultAll = this.$originalSelect.data( 'all-label' ),
			defaults = {
				showSearch: this.$originalSelect.data( 'show_search' ),
				paginate: this.$originalSelect.data( 'paginate' ),
				perPage: defaultPerPage ? defaultPerPage : 10,
				order: defaultOrder ? defaultOrder : 'ASC',
				getElements: null,
				labels: {
					emptyLabel: defaultAll
						? defaultAll
						: yith_wcan_shortcodes.labels?.empty_option,
					searchPlaceholder:
						yith_wcan_shortcodes.labels?.search_placeholder,
					noItemsFound: yith_wcan_shortcodes.labels?.no_items,
					showMore: yith_wcan_shortcodes.labels?.show_more,
				},
			};

		this.options = $.extend( defaults, opts );

		this._hideSelect();
		this._initTemplate();
		this._initActions();

		this.$originalSelect.data( 'dropdown', this ).addClass( 'enhanced' );
	}

	// hide select
	_hideSelect() {
		this.$originalSelect.hide();
	}

	// create dropdown
	_initTemplate() {
		const $mainSpan = $( '<div/>', {
				class: 'yith-wcan-dropdown closed',
			} ),
			$labelSpan = $( '<div/>', {
				class: 'dropdown-label',
				html: this.getLabel(),
			} ),
			$dropdownSpan = $( '<div>', {
				class: 'dropdown-wrapper',
			} ),
			$matchingItemsList = $( '<ul/>', {
				class: 'matching-items filter-items',
			} );

		$dropdownSpan.append( $matchingItemsList );
		$mainSpan.append( $labelSpan ).append( $dropdownSpan );

		if ( this.options.showSearch ) {
			this._initSearchTemplate( $dropdownSpan );
		}

		if ( this.options.paginate ) {
			this._initShowMoreTemplate( $dropdownSpan );
		}

		this.$originalSelect.after( $mainSpan );
		this.$_main = $mainSpan;
		this.$_label = $labelSpan;
		this.$_dropdown = $dropdownSpan;
		this.$_items = $matchingItemsList;
	}

	// create search field
	_initSearchTemplate( $dropdwonSpan ) {
		const $container = $( '<div/>', {
				class: 'search-field-container',
			} ),
			$search = $( '<input/>', {
				name: 's',
				class: 'search-field',
				type: 'search',
				placeholder: this.options.labels.searchPlaceholder,
			} ).attr( 'autocomplete', 'off' );

		$container.append( $search ).prependTo( $dropdwonSpan );
		this.$_search = $search;
	}

	// create showMore field
	_initShowMoreTemplate( $dropdwonSpan ) {
		const $showMore = $( '<a/>', {
			class: 'show-more',
			text: this.options.labels.showMore?.replace(
				'%d',
				this.options.perPage
			),
		} );

		$showMore.on( 'click', this.loadNextPage.bind( this ) ).hide();

		$dropdwonSpan.append( $showMore );
		this.$_showMore = $showMore;
	}

	// init actions performed over dropdown elements
	_initActions() {
		const self = this;

		// main open event
		this.$_main?.on( 'click', ( ev ) => {
			ev.stopPropagation();
			self.toggleDropdown();
		} );
		this.$_dropdown.on( 'click', ( ev ) => {
			ev.stopPropagation();
		} );

		// search event
		this.$_search?.on( 'keyup search', () => {
			self._populateItems();
		} );

		// select event
		this.$_items.on( 'change', ':input', function () {
			let $li = $( this ).closest( 'li' ),
				value = $li.data( 'value' ),
				isActive = false;

			if (
				$li.hasClass( 'disabled' ) &&
				! self.isValueSelected( value )
			) {
				return false;
			}

			$li.toggleClass( 'active' );
			isActive = $li.hasClass( 'active' );

			self._changeItemStatus( value, isActive );
		} );
		this.$_items.on( 'click', 'li:not(.checkbox) a', function ( ev ) {
			let $li = $( this ).closest( 'li' ),
				value = $li.data( 'value' ),
				isActive = false;

			ev.preventDefault();

			if (
				$li.hasClass( 'disabled' ) &&
				! self.isValueSelected( value )
			) {
				return false;
			}

			$li.toggleClass( 'active' );
			isActive = $li.hasClass( 'active' );

			if ( isActive ) {
				$li.siblings().removeClass( 'active' );
			}

			self._changeItemStatus( value, isActive );
		} );
		this.$_items.on( 'click', 'label > a', function ( ev ) {
			const input = $( this ).parent().find( ':input' );

			ev.preventDefault();

			if (
				input.is( '[type="radio"]' ) ||
				input.is( '[type="checkbox"]' )
			) {
				input.prop( 'checked', ! input.prop( 'checked' ) );
			}

			input.change();
		} );

		// select change
		this.$originalSelect.on( 'change', ( ev, selfOriginated ) => {
			if ( selfOriginated ) {
				return;
			}

			self.$_items.find( 'li' ).each( function () {
				const value = $( this ).data( 'value' );

				if ( self.isValueSelected( value ) ) {
					self._selectItem( value );
				} else {
					self._deselectItem( value );
				}
			} );

			self.updateLabel();
		} );

		// close dropdown on external click; do this handler only once for any dropdown in the page
		if ( ! globalThis?.yith_wcan_dropdown_init ) {
			$( document ).on( 'click', this._closeAllDropdowns );
			globalThis.yith_wcan_dropdown_init = true;
		}
	}

	// open dropdown
	openDropdown() {
		this.$_main?.addClass( 'open' ).removeClass( 'closed' );
		this._afterDropdownOpen();
	}

	// close dropdown
	closeDropdown() {
		this.$_main?.removeClass( 'open' ).addClass( 'closed' );
	}

	// close all dropdowns
	_closeAllDropdowns() {
		const dropdowns = $( document )
			.find( 'select.enhanced' )
			.filter( function ( i, select ) {
				const $el = $( select );

				return !! $el.data( 'dropdown' );
			} );

		dropdowns.each( function () {
			$( this ).data( 'dropdown' ).closeDropdown();
		} );
	}

	// close other dropdowns
	_closeOtherDropdowns() {
		const self = this,
			dropdowns = $( document )
				.find( 'select.enhanced' )
				.filter( function ( i, select ) {
					const $el = $( select );

					return (
						!! $el.data( 'dropdown' ) &&
						! $el.is( self.$originalSelect )
					);
				} );

		dropdowns.each( function () {
			$( this ).data( 'dropdown' ).closeDropdown();
		} );
	}

	// toggle dropdown
	toggleDropdown() {
		this.$_main?.toggleClass( 'open' ).toggleClass( 'closed' );

		if ( this.$_main?.hasClass( 'open' ) ) {
			this._afterDropdownOpen();
		}
	}

	// perform operations after dropdown is open
	_afterDropdownOpen() {
		this._closeOtherDropdowns();

		if ( this.$_search?.length ) {
			this.$_search.val( '' );
		}

		this._populateItems();
	}

	// get elements
	getMatchingElements( search, limit ) {
		let matchingElements = [],
			$options = this.getOptions(),
			promise;

		promise = new Promise( ( resolve ) => {
			// first of all, search across select option
			$options.each( function () {
				const t = $( this ),
					value = t.val(),
					label = t.html(),
					regex = new RegExp( '.*' + search + '.*', 'i' ),
					show =
						! search || regex.test( value ) || regex.test( label );

				if ( show ) {
					matchingElements.push( {
						value,
						label,
					} );
				}
			} );

			// then retrieve additional items
			if ( this.options.getElements ) {
				// we're expecting key => value pairs
				this.options
					.getElements( search )
					.then( ( retrievedElements ) => {
						if ( retrievedElements ) {
							// reformat retrieved array
							retrievedElements = retrievedElements.reduce(
								( a, v, i ) => {
									a.push( { label: i, value: v } );
									return a;
								},
								[]
							);

							// merge found results with options
							matchingElements = $.extend(
								matchingElements,
								retrievedElements
							);
						}

						resolve( this._formatItems( matchingElements, limit ) );
					} );
			} else {
				resolve( this._formatItems( matchingElements, limit ) );
			}
		} );

		return promise;
	}

	// format items as key/value pairs for further processing
	_formatItems( items, limit ) {
		let indexes = [],
			hasMore = false;

		// remove duplicates and sort array of results
		items
			.filter( ( v ) => {
				if ( -1 === indexes.indexOf( v.value ) ) {
					indexes.push( v.value );
					return true;
				}

				return false;
			} )
			.sort( ( a, b ) => {
				const order = this.options.order,
					mod = order === 'ASC' ? 1 : -1;

				if ( a.value < b.value ) {
					return -1 * mod;
				} else if ( a.value > b.value ) {
					return mod;
				}

				return 0;
			} );

		// paginate when needed
		if ( limit ) {
			hasMore = limit < Object.keys( items ).length;
			items = items.slice( 0, limit );
		}

		return {
			items,
			hasMore,
		};
	}

	// generate item to append to items list
	_generateItem( value, label ) {
		let active = this.isValueSelected( value ),
			option = this.getOptionByValue( value ),
			$item = $( '<li/>', {
				'data-value': value,
				class: option.length ? option.attr( 'class' ) : '',
			} ),
			$anchor;

		if ( option.length ) {
			const template = option.data( 'template' ),
				count = option.data( 'count' );

			label = template ? template : label;

			if ( !! count ) {
				label += count;
			}
		}

		$anchor = $( '<a/>', {
			href: option.length ? option.data( 'filter_url' ) : '#',
			html: label,
			rel: 'nofollow',
			'data-title': option.length ? option.data( 'title' ) : '',
		} );

		if ( this.$originalSelect.prop( 'multiple' ) ) {
			const $checkbox = $( '<input/>', {
					type: 'checkbox',
					value,
				} ),
				$label = $( '<label>' );

			$checkbox.prop( 'checked', active );
			$label.prepend( $checkbox ).append( $anchor );
			$item.append( $label ).addClass( 'checkbox' );
		} else {
			$item.append( $anchor );
		}

		active ? $item.addClass( 'active' ) : $item.removeClass( 'active' );

		return $item;
	}

	// populate items list
	_populateItems( page ) {
		let search = this.$_search?.length ? this.$_search.val() : '',
			perPage = this.options.paginate ? this.options.perPage : 0,
			limit;

		page = page ? parseInt( page ) : 1;
		limit = page * perPage;

		this.getMatchingElements( search, limit ).then( ( resultSet ) => {
			let matchingItems = resultSet.items,
				items = [],
				hasMore = false;

			// remove all previous items
			this._emptyItems();
			this._hideLoadMore();

			if ( ! matchingItems.length ) {
				items.push(
					$( '<li/>', { text: this.options.labels.noItemsFound } )
				);

				this.currentPage = 1;
			} else {
				for ( const v of matchingItems ) {
					if ( v.value === '' ) {
						items.unshift( this._generateItem( v.value, v.label ) );
					} else {
						items.push( this._generateItem( v.value, v.label ) );
					}
				}

				this.currentPage = page;
				hasMore = resultSet.hasMore;
			}

			this.$_items.append( items );

			this.$originalSelect.trigger( 'yith_wcan_dropdown_updated' );

			if ( hasMore ) {
				this._showLoadMore();
			}
		} );
	}

	// load next page of items
	loadNextPage() {
		const page = this.currentPage + 1;

		this._populateItems( page );
	}

	// set an item as active
	_selectItem( value ) {
		return this._changeItemStatus( value, true );
	}

	// disable an item
	_deselectItem( value ) {
		return this._changeItemStatus( value, false );
	}

	// change item status
	_changeItemStatus( value, status ) {
		const $option = this.$originalSelect.find(
			`option[value="${ value }"]`
		);

		if ( $option.length ) {
			$option.prop( 'selected', status );

			this.closeDropdown();
			this.updateLabel();

			this.$originalSelect.trigger( 'change', [ true ] );

			return true;
		}
		return false;
	}

	// empty items list
	_emptyItems() {
		this.$_items.html( '' );
	}

	// show "Load more" link
	_showLoadMore() {
		this.$_showMore.show();
	}

	// hide "Load more" link
	_hideLoadMore() {
		this.$_showMore.hide();
	}

	// returns select label
	getLabel() {
		return this.hasSelectedValues()
			? this.getSelectedLabels().join( ', ' )
			: this.options.labels.emptyLabel;
	}

	// update label to match new selection
	updateLabel() {
		const label = this.getLabel();

		this.$_label?.html( label );
	}

	// returns select options
	getOptions() {
		return this.$originalSelect.find( 'option' );
	}

	// checks whether select has selected values
	hasSelectedValues() {
		return this.getSelectedOptions().length;
	}

	// checks whether a value is selected
	isValueSelected( value ) {
		const found = this.getSelectedValues().indexOf( value.toString() );

		return -1 !== found;
	}

	// retrieve selected options
	getSelectedOptions() {
		return this.$originalSelect.find( 'option' ).filter( ':selected' );
	}

	// retrieves an option node by value
	getOptionByValue( value ) {
		return this.$originalSelect.find( `option[value="${ value }"]` );
	}

	// retrieve labels for selected options
	getSelectedLabels() {
		const labels = [];

		this.getSelectedOptions().each( function () {
			let $option = $( this ),
				template = $option.data( 'template' );

			template = template
				? template
				: $option.html().replace( /\([0-9]*\)/, '' );

			labels.push( template );
		} );

		return labels;
	}

	// retrieve values for selected options
	getSelectedValues() {
		const values = [];

		this.getSelectedOptions().each( function () {
			values.push( $( this ).val() );
		} );

		return values;
	}

	destroy() {
		// TBD
	}
}
