jQuery( document ).ready( function() {
	window.setTimeout( function() {
		gform_initialize_tooltips();
	}, 0 );
} );

function gform_initialize_tooltips() {
	var hasScrollbars = gform_system_shows_scrollbars();
	var offset        = hasScrollbars ? 'center+11 top-11' : 'center-3 top-11';

	jQuery( '.gf_tooltip' ).tooltip( {
		show: {
			effect: 'fadeIn',
			duration: 200,
			delay: 100,
		},
		position:     {
			my: 'center bottom',
			at: offset,
		},
		tooltipClass: 'arrow-bottom',
		items: '[aria-label]',
		content: function () {
			return jQuery( this ).attr( 'aria-label' );
		},
		open:         function ( event, ui ) {
			if ( typeof ( event.originalEvent ) === 'undefined' ) {
				return false;
			}

			var $id = jQuery( ui.tooltip ).attr( 'id' );
			jQuery( 'div.ui-tooltip' ).not( '#' + $id ).remove();
		},
		close:        function ( event, ui ) {
			ui.tooltip.hover( function () {
					jQuery( this ).stop( true ).fadeTo( 400, 1 );
				},
				function () {
					jQuery( this ).fadeOut( '500', function () {
						jQuery( this ).remove();
					} );
				} );
		}
	} );
}

function gform_system_shows_scrollbars() {
	var parent = document.createElement("div");
	parent.setAttribute("style", "width:30px;height:30px;");
	parent.classList.add('scrollbar-test');

	var child = document.createElement("div");
	child.setAttribute("style", "width:100%;height:40px");
	parent.appendChild(child);
	document.body.appendChild(parent);

	var scrollbarWidth = 30 - parent.firstChild.clientWidth;

	return scrollbarWidth ? true : false;
}
