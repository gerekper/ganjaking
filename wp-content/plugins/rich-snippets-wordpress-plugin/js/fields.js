var rs_fields = {};

(
function () {
	'use strict';

	rs_fields = function ( doc ) {
		doc = (
		typeof doc !== 'undefined'
		) ? doc : document;

		this.doc = doc;
		var self = this;

		this.init = function ( doc ) {
			self.doc = (
			typeof doc !== 'undefined'
			) ? doc : self.doc;

			jQuery( self.doc ).find( '.misc-fields-rating5' ).each( function () {
				self.init_rating5( jQuery( this ) );
			} );

			jQuery( self.doc ).find( '.misc-field-range' ).each( function () {
				self.init_range( jQuery( this ) );
			} );
		};

		this.init_rating5 = function ( $obj ) {
			if ( 1 === parseInt( $obj.data( 'initialized' ) ) ) {
				return;
			}

			var self = this;

			$obj.find( '.star' ).on( 'click', function () {
				self.rate_5( jQuery( this ) );
			} );

			jQuery( self.doc ).on( 'click', '.misc-fields-rating5 .star-cancel', function () {
				jQuery( this ).parent().find( '.star' ).removeClass( 'dashicons-star-filled' ).addClass( 'dashicons-star-empty' );
				jQuery( this ).parent().find( 'input' ).val( 0 );
			} );

			$obj.data( 'initialized', 1 );

		};

		this.init_range = function ( $obj ) {
			if ( 1 === parseInt( $obj.data( 'initialized' ) ) ) {
				return;
			}

			var self = this;

			$obj.on( 'change mousemove', function () {
				self.rate_100( jQuery( this ) );
			} );

			$obj.data( 'initialized', 1 );
		};

		this.rate_100 = function ( $obj ) {
			var val = $obj.val();
			$obj.parent().find( '.misc-field-range-view' ).text( val );
		};

		this.rate_5 = function ( $obj ) {
			$obj.prevAll( '.star' ).addBack().removeClass( 'dashicons-star-empty' ).addClass( 'dashicons-star-filled' );
			$obj.nextAll( '.star' ).not( $obj ).removeClass( 'dashicons-star-filled' ).addClass( 'dashicons-star-empty' );

			$obj.parent().find( 'input' ).val( $obj.prevAll( '.star' ).addBack().length );
		};

	};

	jQuery( document ).ready( function () {
		rs_fields = new rs_fields();
		rs_fields.init();
	} );

}
)();
