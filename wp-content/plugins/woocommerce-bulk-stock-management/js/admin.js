/* global jQuery */
(function($) {
	var self = {
		$filter   : null,
		$paged    : null,
		$stocks   : null,
		$action1  : null,
		$action2  : null,
		$doAction1: null, // Apply buttons at the top of table list
		$doAction2: null, // Apply buttons at the bottom of table list
		$form     : null,

	};

	self.init = function() {
		self.$filter    = $( 'input[name="filter"]' );
		self.$paged     = $( 'input[name="paged"]' );
		self.$stocks    = $( 'input.wc_bulk_stock_quantity_value' );
		self.$action1   = $( '[name="action"]' );
		self.$action2   = $( '[name="action2"]' );
		self.$doAction1 = $( '#doaction' );
		self.$doAction2 = $( '#doaction2' );
		self.$form      = $( '#stock-management' );
	};

	self.resetPageOnFilter = function() {
		self.$filter.on( 'click', function() {
			self.$paged.val( 1 );
		} );
	};

	self.updateStocksName = function() {
		self.$stocks.on( 'change', function() {
			$( this ).closest( 'td' ).find( 'input' ).each( function() {
				$( this ).attr( 'name', $( this ).data( 'name' ) );
			} );
		} );
	};

	self.bindUpdateFormMethod = function() {
		// Change form method to POST when:
		// - doAction1 or doAction button is clicked
		// - hits enter on input fields
		self.$doAction1.on( 'click', self.formMethodToPostHandler );
		self.$doAction2.on( 'click', self.formMethodToPostHandler );
		self.$stocks.on( 'keydown', self.formMethodToPostHandler );
	};

	self.formMethodToPostHandler = function( e ) {
		if ( 13 === e.which || 13 === e.keyCode || 'click' === e.type ) {
			self.$form.attr( 'method', 'post' );
		}
	};

	self.bindSetActionToSave = function() {
		self.$stocks.on( 'keydown', self.setActionHandler );
	};

	self.setActionHandler = function( e ) {
		if ( 13 === e.which || 13 === e.keyCode ) {
			self.$action1.val( 'save' );
			self.$action2.val( 'save' );
		}
	};

	self.run = function() {
		self.init();

		self.resetPageOnFilter();
		self.updateStocksName();
		self.bindUpdateFormMethod();
		self.bindSetActionToSave();
	};

	$( self.run );

}( jQuery ));
