(function () {
	'use strict';

	function ttaMapChildEvents( model ) {
		var child_tag = 'vc_tta_toggle' === model.get('shortcode') ? 'vc_tta_toggle_section' : 'vc_tta_section';
		window.vc.events.on(
			'shortcodes:' + child_tag + ':add:parent:' + model.get( 'id' ),
			function ( model ) {
				var active_tab_index, models, parent_model;
				parent_model = window.vc.shortcodes.get( model.get( 'parent_id' ) );
				active_tab_index = parseInt( parent_model.getParam( 'active_section' ), 10 );
				if ( 'undefined' === typeof (active_tab_index) ) {
					active_tab_index = 1;
				}
				models = _.pluck( _.sortBy( window.vc.shortcodes.where( { parent_id: parent_model.get( 'id' ) } ),
					function ( model ) {
						return model.get( 'order' );
					} ), 'id' );
				if ( models.indexOf( model.get( 'id' ) ) === active_tab_index - 1 ) {
					model.set( 'isActiveSection', true );
				}
				return model;
			}
		);
		window.vc.events.on(
			'shortcodes:' + child_tag + ':clone:parent:' + model.get( 'id' ),
			function ( model ) {
				if ( window.vc.ttaSectionActivateOnClone ) {
					model.set( 'isActiveSection', true );
				}
				window.vc.ttaSectionActivateOnClone = false;
			}
		);
	}

	window.vc.events.on( 'shortcodes:vc_tta_accordion:add', ttaMapChildEvents );
	window.vc.events.on( 'shortcodes:vc_tta_tabs:add', ttaMapChildEvents );
	window.vc.events.on( 'shortcodes:vc_tta_tour:add', ttaMapChildEvents );
	window.vc.events.on( 'shortcodes:vc_tta_pageable:add', ttaMapChildEvents );
	window.vc.events.on( 'shortcodes:vc_tta_toggle:add', ttaMapChildEvents );
})();
