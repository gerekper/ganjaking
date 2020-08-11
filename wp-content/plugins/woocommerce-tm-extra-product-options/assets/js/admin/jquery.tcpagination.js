/*
 * jQuery Pagination plugin
 *
 * Based on jQuery pagination plugin
 * http://josecebe.github.io/pagination/
 *
 * Copyright 2014-2018, Eugene Simakin
 * Released under Apache 2.0 license
 * http://apache.org/licenses/LICENSE-2.0.html
 *
 * Modified for Extra Product Options
 */

( function( $ ) {
	'use strict';

	var Pagination = function( dom, options ) {
		var tagName;
		var newdom;
		var newTag;

		this.element = $( dom );
		this.mainElement = this.element;
		this.settings = $.extend( {}, $.fn.tcPagination.defaults, options );

		this.settings.totalPages = parseInt( this.settings.totalPages, 10 );
		if ( ! Number.isFinite( this.settings.totalPages ) ) {
			this.settings.totalPages = 0;
		}

		if ( this.settings.startPage < 1 || this.settings.startPage > this.settings.totalPages ) {
			this.settings.startPage = 1;
		}

		this.settings.visiblePages = parseInt( this.settings.visiblePages, 10 );
		if ( ! Number.isFinite( this.settings.visiblePages ) ) {
			this.settings.visiblePages = 5;
		}

		if ( typeof this.settings.onPageClick === 'function' ) {
			this.element.first().on( 'page', this.settings.onPageClick );
		}

		if ( this.settings.totalPages === 1 ) {
			this.element.trigger( 'page', 1 );
			return this;
		}

		tagName = this.element.prop( 'tagName' );

		if ( tagName === 'UL' ) {
			this.listContainer = this.element;
		} else {
			newdom = $( [] );
			this.element.toArray().forEach( function( el ) {
				newTag = $( '<ul></ul>' );
				$( el ).append( newTag );
				newdom.push( newTag[ 0 ] );
			} );
			this.listContainer = newdom;
			this.element = newdom;
		}

		this.show( this.settings.startPage );

		return this;
	};

	Pagination.prototype = {
		constructor: Pagination,

		destroy: function() {
			this.element.empty();
			this.mainElement.removeData( 'tc-pagination' );
			this.element.off( 'page' );

			return this;
		},

		show: function( page ) {
			var pages;

			if ( page < 1 || page > this.settings.totalPages ) {
				return pages;
			}
			this.currentPage = page;

			this.element.trigger( 'beforePage', page );

			pages = this.getPages( page );
			this.render( pages );
			this.setupEvents();

			this.element.trigger( 'page', page );

			return pages;
		},

		buildListItems: function( pages ) {
			var listItems = [];
			var prev;
			var next;
			var _this = this;

			if ( this.settings.first ) {
				listItems.push( this.buildItem( 'first', 1 ) );
			}

			if ( this.settings.prev ) {
				if ( pages.currentPage > 1 ) {
					prev = parseInt( pages.currentPage, 10 ) - 1;
				} else {
					prev = 1;
				}

				listItems.push( this.buildItem( 'prev', prev ) );
			}

			pages.numeric.forEach( function( page ) {
				listItems.push( _this.buildItem( 'page', page ) );
			} );

			if ( this.settings.next ) {
				if ( pages.currentPage < this.settings.totalPages ) {
					next = parseInt( pages.currentPage, 10 ) + 1;
				} else {
					next = this.settings.totalPages;
				}

				listItems.push( this.buildItem( 'next', next ) );
			}

			if ( this.settings.last ) {
				listItems.push( this.buildItem( 'last', this.settings.totalPages ) );
			}

			return listItems;
		},

		buildItem: function( type, page ) {
			var $itemContainer = $( '<li></li>' );
			var $itemContent = $( '<a></a>' );
			var itemText;

			if ( this.settings[ type ] ) {
				itemText = this.settings[ type ];
			} else {
				itemText = page;
			}
			$itemContainer.addClass( this.settings[ type + 'Class' ] );
			$itemContainer.data( 'page', page );
			$itemContainer.data( 'page-type', type );
			$itemContainer.append( $itemContent.attr( 'href', '#' ).html( itemText ) );

			return $itemContainer;
		},

		getPages: function( currentPage ) {
			var pages = [];
			var half = Math.floor( parseInt( this.settings.visiblePages, 10 ) / 2 );
			var start = parseInt( currentPage, 10 ) - half + 1 - ( parseInt( this.settings.visiblePages, 10 ) % 2 );
			var end = currentPage + half;
			var visiblePages = this.settings.visiblePages;
			var itPage;

			if ( visiblePages > this.settings.totalPages ) {
				visiblePages = this.settings.totalPages;
			}

			if ( start <= 0 ) {
				start = 1;
				end = visiblePages;
			}
			if ( end > this.settings.totalPages ) {
				start = parseInt( this.settings.totalPages, 10 ) - parseInt( visiblePages, 10 ) + 1;
				end = this.settings.totalPages;
			}

			itPage = start;
			while ( itPage <= end ) {
				pages.push( itPage );
				itPage = parseInt( itPage, 10 ) + 1;
			}

			return { currentPage: currentPage, numeric: pages };
		},

		render: function( pages ) {
			var _this = this;
			var items;
			var $this;
			var pageType;

			this.listContainer.children().remove();
			items = this.buildListItems( pages );

			items.forEach( function( item ) {
				_this.listContainer.append( item );
			} );

			this.listContainer.children().each( function() {
				$this = $( this );
				pageType = $this.data( 'page-type' );

				switch ( pageType ) {
					case 'page':
						if ( $this.data( 'page' ) === pages.currentPage ) {
							$this.addClass( _this.settings.activeClass );
						}
						break;
					case 'first':
						$this.toggleClass( _this.settings.disabledClass, pages.currentPage === 1 );
						break;
					case 'last':
						$this.toggleClass( _this.settings.disabledClass, pages.currentPage === _this.settings.totalPages );
						break;
					case 'prev':
						$this.toggleClass( _this.settings.disabledClass, pages.currentPage === 1 );
						break;
					case 'next':
						$this.toggleClass( _this.settings.disabledClass, pages.currentPage === _this.settings.totalPages );
						break;
				}
			} );
		},

		setupEvents: function() {
			var _this = this;

			this.listContainer.off( 'click' ).on( 'click', 'li', function( evt ) {
				var $this = $( this );

				if ( $this.hasClass( _this.settings.disabledClass ) || $this.hasClass( _this.settings.activeClass ) ) {
					return false;
				}

				evt.preventDefault();
				_this.show( parseInt( $this.data( 'page' ), 10 ) );
			} );
		}
	};

	$.fn.tcPagination = function( option ) {
		var methodReturn;
		var $this = $( this );
		var data = $this.data( 'tc-pagination' );
		var options;
		var ret;

		if ( typeof option === 'object' ) {
			options = option;
		} else {
			options = {};
		}

		if ( ! data ) {
			data = new Pagination( this, options );
			$this.data( 'tc-pagination', data );
		}

		if ( typeof option === 'string' ) {
			methodReturn = data[ option ].apply( data, [] );
		}

		if ( methodReturn === undefined ) {
			ret = $this;
		} else {
			ret = methodReturn;
		}

		return ret;
	};

	$.fn.tcPagination.defaults = {
		totalPages: 0,
		startPage: 1,
		visiblePages: 5,
		onPageClick: null,
		page: null,
		first: '<i class="tcfa tcfa-angle-double-left"></i>',
		prev: '<i class="tcfa tcfa-angle-left"></i>',
		next: '<i class="tcfa tcfa-angle-right"></i>',
		last: '<i class="tcfa tcfa-angle-double-right"></i>',
		nextClass: 'next',
		prevClass: 'prev',
		lastClass: 'last',
		firstClass: 'first',
		pageClass: 'page',
		activeClass: 'active',
		disabledClass: 'disabled'
	};

	$.fn.tcPagination.Constructor = Pagination;
}( window.jQuery ) );
