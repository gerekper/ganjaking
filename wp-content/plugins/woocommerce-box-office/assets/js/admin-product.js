/* globals jQuery */
( function( $ ) {

	var self = $.extend(
		{
			productTypeSelector: '#product-type',
			ticketTogglerSelector: 'input#_ticket',
			ticketPanels: '.show_if_ticket',
			ticketFieldsSelector: '.ticket_fields',
			productDataMetaBoxSelector: '#woocommerce-product-data',
			insertFieldSelector: 'a.insert',
			deleteFieldSelector: 'a.delete',
			fieldLabelSelector: '.field_label input',
			fieldTypeSelector: '.field_type select',
			ticketFields: [],
			labelVariablesInfoSelector: '.ticket-label-variables-info',
			labelVariablesSelector: '.ticket-label-variables',
			ticketLinkVarSelector: '.ticket-link-var',
			ticketTokenVarSelector: '.ticket-token-var',
			ticketPostVarsSelector: '.ticket-post-vars'
		},
		wcBoxOfficeParams
	);

	self.init = function() {
		self.populateTicketFields();
		self.bindEvents();
		self.setDefaultFieldsIfEmpty();
		self.setDefaultPrintContentIfEmpty();
		self.setDefaultEmailContentIfEmpty();
	};

	self.bindEvents = function() {
		$( self.productTypeSelector ).on( 'change', self.maybeUncheckTicket ).trigger( 'change' );
		$( self.ticketTogglerSelector ).on( 'change', self.toggleTicketPanels ).trigger( 'change' );
		$( self.productDataMetaBoxSelector ).on( 'click', self.ticketFieldsSelector + ' ' + self.insertFieldSelector, self.insertField );
		$( self.productDataMetaBoxSelector ).on( 'click', self.ticketFieldsSelector + ' ' + self.deleteFieldSelector, self.deleteField );
		$( self.productDataMetaBoxSelector ).on( 'change', self.fieldTypeSelector, self.fieldTypeChange );

		$( self.ticketFieldsSelector ).on( 'change', self.fieldLabelSelector, self.populateTicketFields );
		$( self.ticketFieldsSelector ).on( 'row:inserted', self.populateTicketFields );
		$( self.ticketFieldsSelector ).on( 'row:deleted', self.populateTicketFields );
		$( self.labelVariablesSelector ).on( 'click', 'a', self.insertLabelToEditor );
		$( self.ticketLinkVarSelector ).on( 'click', 'a', self.insertLabelToEditor );
		$( self.ticketTokenVarSelector ).on( 'click', 'a', self.insertLabelToEditor );
		$( self.ticketPostVarsSelector ).on( 'click', 'a', self.insertLabelToEditor );
	};

	/**
	 * Maybe uncheck the ticket checkbox if selected product type doesn't display
	 * the ticket checkbox.
	 */
	self.maybeUncheckTicket = function( e ) {
		var $ticketToggler = $( self.ticketTogglerSelector );
		if ( ! $ticketToggler.length ) {
			return;
		}

		var classes = $ticketToggler
			.closest( 'label' )
			.attr( 'class' )
			.trim()
			.split( ' ' );

		var hideTickets = classes.filter( function( className ) {
			return className.startsWith( 'hide_if_' );
		} ).map( function( className ) {
			return className.substring( 'hide_if_'.length );
		} );

		var type = $( e.target ).val();
		if ( $.inArray( type, hideTickets ) >= 0 ) {
			$ticketToggler.removeAttr( 'checked' ).trigger( 'change' );
		}
	}

	self.toggleTicketPanels = function( e ) {
		var checked = $( e.target ).is( ':checked' );

		$( self.ticketPanels ).toggle( checked );
	};

	self.insertField = function( e ) {
		var row = $( this ).data( 'row' );

		$( this ).closest( self.ticketFieldsSelector ).find( 'tbody' ).append( row );
		$( self.ticketFieldsSelector ).trigger( 'row:inserted' );

		return false;
	};

	self.deleteField = function( e ) {
		$( this ).closest( 'tr' ).remove();
		$( self.ticketFieldsSelector ).trigger( 'row:deleted' );
		return false;
	};

	self.fieldTypeChange = function( e ) {
		var $el = $( e.target ),
			selected = $el.val();

		switch ( selected ) {
			case 'select':
			case 'radio':
			case 'checkbox':
				$el.closest( 'tr' ).find( '.field_options textarea').show();
				$el.closest( 'tr' ).find( '.field_options .email-options').hide();
				break;
			case 'email':
				$el.closest( 'tr' ).find( '.field_options textarea').hide();
				$el.closest( 'tr' ).find( '.field_options .email-options').show();
				break;
			default:
				$el.closest( 'tr' ).find( '.field_options textarea').hide();
				$el.closest( 'tr' ).find( '.field_options .email-options').hide();
		}

		return false;
	};

	self.populateTicketFields = function() {
		var ticketFields = $( self.fieldLabelSelector, self.ticketFieldsSelector );

		self.ticketFields = [];
		ticketFields.each( function() {
			self.ticketFields.push( this.value );
		} );

		self.updateLabelVariablesInfo();
	};

	self.updateLabelVariablesInfo = function() {
		if ( self.ticketFields.length ) {
			$( self.labelVariablesSelector ).html( '' );
			$.each( self.ticketFields, function( i, label ) {
				$( self.labelVariablesSelector ).append(
					'<a href="#"><code>{' + label + '}</code></a>'
				);
			} );
			$( self.labelVariablesInfoSelector ).show();
		} else {
			$( self.labelVariablesInfoSelector ).hide();
		}
	};

	self.insertLabelToEditor = function() {
		var activePanel = $( this ).closest( '.woocommerce_options_panel' ),
			label = $( this ).text(),
			editor;

		switch ( activePanel.attr( 'id' ) ) {
			case 'ticket_content_data':
				editor = tinyMCE.get( 'ticket-content-editor' );
				break;
			case 'ticket_email_data':
				editor = tinyMCE.get( 'ticket-email-editor' );
				break;
		}
		editor.insertContent( label );

		return false;
	};

	self.setDefaultFieldsIfEmpty = function() {
		if ( ! self.ticketFields.length ) {
			var labels = ['First Name', 'Last Name', 'Email'],
				types = ['first_name', 'last_name', 'email'];

			// Create three fields.
			$( self.insertFieldSelector, self.ticketFieldsSelector ).trigger( 'click' );
			$( self.insertFieldSelector, self.ticketFieldsSelector ).trigger( 'click' );
			$( self.insertFieldSelector, self.ticketFieldsSelector ).trigger( 'click' );

			$( 'tbody tr', self.ticketFieldsSelector ).each( function( i ) {
				$( self.fieldLabelSelector, this ).val( labels[ i ] ).trigger( 'change' );
				$( self.fieldTypeSelector, this ).val( types[ i ] ).trigger( 'change' );
			} );
		}
	};

	self.setDefaultPrintContentIfEmpty = function() {
		self._setTinyMCEDefaultContent( 'ticket-content-editor', self.defaultPrintContent );
	};

	self.setDefaultEmailContentIfEmpty = function() {
		self._setTinyMCEDefaultContent( 'ticket-email-editor', self.defaultEmailContent );
	};

	/**
	 * We're targetting textarea because tinyMCE might be unloaded.
	 */
	self._setTinyMCEDefaultContent = function( editorId, defaultContent ) {
		var editor = $( '#' + editorId ),
			content = '';

		if ( ! editor ) {
			return;
		}

		content = editor.val();
		if ( ! content ) {
			editor.val( defaultContent );
		}
	};

	$( self.init );

} )( jQuery );
