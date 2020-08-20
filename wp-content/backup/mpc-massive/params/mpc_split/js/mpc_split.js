/*----------------------------------------------------------------------------*\
	MPC_SPLIT PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function update_labels() {
		var $labels = $( '.vc_param_group-list .vc_param-group-admin-labels' );

		$labels.each( function() {
			$( this ).html( $( this ).html().replace( new RegExp( '(\\|){3}', 'g' ), ' | ' ) );
		} );
	}

	function init_split() {
		var $split_fields = $( '.mpc-vc-split-text' ),
			$split_values = $( '.mpc-vc-split' );

		$split_fields.off( 'blur' );
		$split_values.off( 'change mpc.change' );

		$split_fields.on( 'blur', function() {
			var $field = $( this ),
				_value = $field.val();

			if ( _value != '' ) {
				$field.siblings( '.mpc-vc-split' ).val( _value.replace( /\n/g, '|||' ) );
			}

			update_labels();
		} );

		$split_values.on( 'change mpc.change', function() {
			var $field = $( this ),
				$split = $field.siblings( '.mpc-vc-split-text' ),
				_value = $field.val();

			if ( _value != '' ) {
				$split.val( _value.replace( /\|\|\|/g, '\n' ) );
			} else {
				$split.val( '' );
			}

			update_labels();
		} );
	}

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.one( 'mpc.render', function() {
		var	$list_group = $popup.find( '[data-vc-shortcode-param-name="elements"]' ),
			$group_add = $list_group.find( '.vc_param_group-add_content' ),
			$group_duplicate = $list_group.find( '.column_clone' );

		$group_add.on( 'click', function() {
			setTimeout( function(){
				init_split();
			}, 250 );
		} );

		$group_duplicate.on( 'click', function() {
			init_split();
		} );

		init_split();
		update_labels();
	} );


} )( jQuery );
