/* globals jQuery, wcBoxOfficeParams */
(function($) {

	var self = $.extend(
		{
			ticketSelector: '.order-item-meta-ticket'
		},
		wcBoxOfficeParams
	);

	self.init = function() {
		self.replaceTicketTextWithLink();
	};

	self.replaceTicketTextWithLink = function() {
		$( self.ticketSelector ).each( function() {
			var $el = $( this ),
				cls = $el.attr( 'class' ),
				startIdx = cls.indexOf( 'ticket-id' );

			if ( startIdx >= 0 ) {
				var id = cls.substr( startIdx + 'ticket-id-'.length ),
					url = wcBoxOfficeParams.editPostUrl + '&post=' + id,
					txt = $el.text(),
					a = $( '<a/>' );

				a.attr( 'href', url );
				a.attr( 'target', '_blank' );
				a.text( txt );
				$el.html( a );
			}
		} );
	};

	$( self.init );

})(jQuery);
