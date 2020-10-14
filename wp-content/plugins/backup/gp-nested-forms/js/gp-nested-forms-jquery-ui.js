/**
* Nested Forms, mama!
*/

( function( $ ) {

	window.GPNestedForms = function( args ) {

		var self = this;

		// copy all args to current object: formId, fieldId
		for( prop in args ) {
			if( args.hasOwnProperty( prop ) )
				self[prop] = args[prop];
		}

		self.init = function() {

			self.initSession();

			// Handle init when form is reloaded via AJAX.
			if( typeof window[ 'GPNestedForms_{0}_{1}'.format( self.formId, self.fieldId ) ] !== 'undefined' ) {
				var oldGPNF = window[ 'GPNestedForms_{0}_{1}'.format( self.formId, self.fieldId ) ];
				self.entries = oldGPNF.entries;
				gform.removeFilter( 'gform_calculation_formula', 10, 'gpnf_{0}_{1}'.format( self.formId, self.fieldId ) );
				/* Hack: fixes issue when Beaver Builder triggers ready event again without reloading UI */
				self.viewModel = oldGPNF.viewModel;
			}

			self.parentFormContainer = $( '#gform_wrapper_' + self.formId );
			self.fieldContainer      = $( '#field_' + self.formId + '_' + self.fieldId );
			self.modalElem           = $( '.gpnf-nested-form-' + self.formId + '-' + self.fieldId );
			self.editDialogElem      = $( '.gpnf-edit-form-' + self.formId + '-' + self.fieldId );
			self.formHtml            = self.getFormHtml();
			self.entriesInput        = $( '#input_' + self.formId + '_' + self.fieldId );
			self.scrollY             = 0;
			self.modalArgs = gform.applyFilters( 'gpnf_modal_args', {
				autoOpen: false,
				modal: true,
				width: self.modalWidth < $( document ).width() ? self.modalWidth : $( document ).width() - 40,
				height: self.modalHeight,
				title: self.modalTitle,
				closeText: 'Close',
				buttons: { },
				position: self.getPosition(),
				dialogClass: self.modalClass,
				draggable: false,
				resizable: false,
				close: function( event, ui ) {
					// Any time either dialog is closed, remove the dialog's form completely from the UI so there are
					// never two of the same form on the page at the same time.
					self.modalDialog.html( '' );
					self.editDialog.html( '' );
					$( '.ui-widget-overlay' ).removeClass( self.modalClass );
					window.scroll( 0, self.scrollY );
				},
				open: function() {

					if( self.modalHeaderColor ) {
						self.modalDialog.siblings( '.ui-dialog-titlebar' ).css( 'background-color', self.modalHeaderColor );
						self.editDialog.siblings( '.ui-dialog-titlebar' ).css( 'background-color', self.modalHeaderColor );
					}

					$( '.ui-widget-overlay' ).addClass( self.modalClass );

					if( self.hasConditionalLogic ) {

						$( document ).on( 'gform_post_conditional_logic', function( event, formId ) {
							if( self.nestedFormId == formId ) {
								self.repositionModals();
							}
						} );

					} else {

						self.repositionModals();

					}

				}
			}, self.formId, self.fieldId, self );

			/**
			 * Fix conflict when Bootstrap is loaded AFTER jQuery UI; the close button on jQuery UI does not appear.
			 * https://stackoverflow.com/questions/17367736/jquery-ui-dialog-missing-close-icon
			 */
			if( $.fn.button.noConflict ) {
				$.fn.bootstrapBtn = $.fn.button.noConflict();
			}

			self.modalDialog = self.modalElem.dialog( self.modalArgs );
			self.editDialog  = self.editDialogElem.dialog( $.extend( {}, self.modalArgs, { title: self.editModalTitle } ) );

			if( ! self.isBound( self.fieldContainer[0] ) ) {
				self.viewModel = new EntriesModel( self.prepareEntriesForKnockout( self.entries ), self );
				ko.applyBindings( self.viewModel, self.fieldContainer[0] );
			}

			// Click handler for add entry button.
			$( document ).on( 'click', '#field_' + self.formId + '_' + self.fieldId + ' .gpnf-add-entry', function( event ) {

				event.preventDefault();

				// Save scroll position so we can return to it when closing the modal.
				self.scrollY = window.scrollY ? window.scrollY : window.pageYOffset;

				self.modalElem.html( self.formHtml );
				self.modalDialog.dialog( 'open' );
				self.initFormScripts();
				self.modalElem.find( 'input[name="gpnf_nested_form_field_id"]' ).val( self.fieldId );

			} );

			// Re-init modaled forms; 'gpnf_post_render' triggered on any nested form's first load every time a nested
			// form is retrieved via ajax (aka editing, first load and each page load).
			$( document ).bind( 'gpnf_post_render', function( event, formId, currentPage ) {

				var $nestedForm = $( '#gform_wrapper_' + formId );
				if( formId == self.nestedFormId && $nestedForm.length > 0 ) {
					$( document ).trigger( 'gform_post_render', [ self.nestedFormId, currentPage ] );
				}

                self.handleParentMergeTag();
				self.repositionModals();

			} );

			// Reposition modals when window is resized.
			$( window ).resize( function() {
				self.repositionModals();
			} );

			// Init calculation functionality.
			gform.addFilter( 'gform_calculation_formula', self.parseCalcs, 10, 'gpnf_{0}_{1}'.format( self.formId, self.fieldId ) );

			self.runCalc( self.formId );

			self.parentFormContainer.data( 'GPNestedForms_' + self.fieldId, self );

			window[ 'GPNestedForms_{0}_{1}'.format( self.formId, self.fieldId ) ] = self;

		};

		self.initSession = function() {
			$.post( self.ajaxUrl, self.sessionData, function( response ) { } );
		};

		self.repositionModals = function() {

			// Repositioning the modal will scroll the body up to the top of the modal. This isn't a problem for small
			// modals but for large models that exceed the height of the window, this creates a very confusing UX.
			// Solution? The getPosition() method will now determine if the modal is larger than than window and return
			// a "dnr" (do not reposition) property.
			var position = self.getPosition( self.modalDialog );
			if( ! position.dnr ) {
				self.modalDialog.dialog( 'option', 'position', position );
			}

			position = self.getPosition( self.editDialog );
			if( ! position.dnr ) {
				self.editDialog.dialog( 'option', 'position', position );
			}


		};

		self.getPosition = function( $dialog ) {

			if( ! $dialog ) {
				return { my: 'center', at: 'center', of: window };
			}

			var windowHeight   = $( window ).height(),
				/*documentHeight = $( document ).height(),*/
				modalHeight    = $dialog.parents( '.ui-dialog' ).height(),
				alignment      = 'center',
			    of             = window,
				dnr            = false;

			/*if ( modalHeight >= documentHeight ) {
				alignment = 'center top';
				of = document;
				$dialog.data( 'do-not-reposition', false );
			} else */
			if ( modalHeight >= windowHeight ) {
				alignment = 'center top';
				of = window;
				if( $dialog.data( 'do-not-reposition' ) ) {
					dnr = true;
				} else {
					$dialog.data( 'do-not-reposition', true );
				}
			} else {
				$dialog.data( 'do-not-reposition', false );
			}

			var position = { my: alignment, at: alignment, of: of, dnr: dnr };

			return position;
		};

		self.isBound = function( elem ) {
			return !! ko.dataFor( elem );
	    };

		self.prepareEntriesForKnockout = function( entries ) {
			for( var i = 0; i < entries.length; i++ ) {
				entries[i] = self.prepareEntryForKnockout( entries[i] );
			}
			return entries;
		};

		self.prepareEntryForKnockout = function( entry ) {

			// IE8 hack to fix recursive loop issue; props to Josh Casey
			var entryTemplate = $.extend( {}, entry );

			for( var prop in entryTemplate ) {
				if( entry.hasOwnProperty( prop ) ) {
					var item = entry[ prop ];
					if( item.label === false ) {
						item.label = '';
					}
                    entry['f' + prop] = item;
                }
			}

            return entry;
		};

		self.refreshMarkup = function() {

			$.post( self.ajaxUrl, {
				action: 'gpnf_refresh_markup',
				nonce: GPNFData.nonces.refreshMarkup,
				gpnf_parent_form_id: self.formId,
				gpnf_nested_form_field_id: self.fieldId
			}, function( response ) {
				self.formHtml = response;
			} );

		};

        self.editEntry = function( entryId, $trigger ) {

        	var $spinner = new AjaxSpinner( $trigger, self.spinnerUrl, '' );
	        $trigger.css( { visibility: 'hidden' } );

            $.post( self.ajaxUrl, {
                action: 'gpnf_edit_entry',
	            nonce: GPNFData.nonces.editEntry,
                gpnf_entry_id: entryId,
                gpnf_parent_form_id: self.formId,
                gpnf_nested_form_field_id: self.fieldId
            }, function( response ) {

            	$spinner.destroy();
	            $trigger.css( { visibility: 'visible' } );

				// Save scroll position so we can return to it when closing the modal.
				self.scrollY = window.scrollY ? window.scrollY : window.pageYOffset;

                self.editDialog
	                .html( response )
	                .dialog( 'open' );

                self.initFormScripts();

            } );

        };

        self.deleteEntry = function( item, $trigger ) {

	        var $spinner = new AjaxSpinner( $trigger, self.spinnerUrl, '' );
	        $trigger.css( { visibility: 'hidden' } );

        	$.post( self.ajaxUrl, {
		        action: 'gpnf_delete_entry',
		        nonce:  GPNFData.nonces.deleteEntry,
		        gpnf_entry_id: item.id
	        }, function( response ) {

		        $spinner.destroy();
		        $trigger.css( { visibility: 'visible' } );

		        if( ! response ) {
			        console.log( 'Error: no response.' );
			        return;
		        } else if( ! response.success ) {
			        console.log( 'Error:' + response.data );
			        return;
		        }

		        // Success!
		        self.viewModel.entries.remove( item );

		        self.refreshMarkup();

	        } );

        };

        self.getFormHtml = function() {

            // check stash for HTML first, required for AJAX-enabled parent forms
            var formHtml = self.modalElem.data( 'formHtml' );
            if( ! formHtml ) {
                formHtml = self.modalElem.html();
            }

            // stash for AJAX-enabled parent forms
            self.modalElem.data( 'formHtml', formHtml );

            // clear the existing markup to prevent tabindex and script conflicts from multiple IDs existing in the same DOM
            self.modalElem.html( '' );

            return formHtml;
        };

        self.handleParentMergeTag = function () {

            self.modalElem.find(':input').each(function () {
                var $this = $(this);
                var value = $this.data('gpnf-value');

                if ($this.data('gpnf-changed')) {
                    return true;
                }

                if (!value) {
                    return true;
                }

                var parentMergeTagMatches = /{Parent:(\d+(\.\d+)?)}/gi.exec(value);

                if (!parentMergeTagMatches) {
                    return true;
                }

                var inputId = parentMergeTagMatches[1];

                if (isNaN(inputId)) {
                    return true;
                }

                var $parentInput = self.parentFormContainer.find('#input_' + self.formId + '_' + inputId.split('.').join('_'));
                if( $parentInput.hasClass( 'gfield_radio' ) ) {
					$parentInput = $parentInput.find( 'input:checked' );
				}

                var currentValue = $(this).val(),
					/**
					 * Filter the value of the parent merge tag before it is replaced in the field.
					 *
					 * @since 1.0-beta-8.0
					 *
					 * @param string          value           Value that will replace the parent merge tag in the field.
					 * @param float           inputId         ID of the field/input targeted by the parent merge tag.
					 * @param int             formId          ID of the current form.
					 * @param {GPNestedForms} gpnf            Current instance of the GPNestedForms object.
					 */
                    parentValue = gform.applyFilters( 'gpnf_parent_merge_tag_value', $parentInput.val(), inputId, self.formId, self );

                if (currentValue != parentValue) {
                    $(this).val(parentValue).change();
                }

                $(this).on('change', function () {
                    $(this).data('gpnf-changed', true);
                });
            });

        };

        self.initFormScripts = function( currentPage ) {
			window.gform.doAction( 'gpnf_init_nested_form', self.nestedFormId );

			// @todo: add support for multi-page forms by updating "1" to the currrent page ID
			$(document).trigger( 'gform_post_render', [ self.nestedFormId, 1 ] );
            if( window['gformInitDatepicker'] ) {
                gformInitDatepicker();
            }

            self.handleParentMergeTag();
        };

		/**
		 * We really need a better way to trigger calculations.
		 */
		self.runCalc = function() {
			$( document ).trigger( 'gform_post_conditional_logic', [ self.formId, [], false ]  );
		};

		self.parseCalcs = function( formula, formulaField, formId, calcObj ) {

			var matches = getMatchGroups( formula, /{[^{]*?:([0-9]+):(sum|total|count)=?([0-9]*)}/i );
			$.each( matches, function( i, group ) {

				var search            = group[0],
					nestedFormFieldId = group[1],
					func              = group[2],
					targetFieldId     = group[3],
					replace           = 0;

				if( nestedFormFieldId != self.fieldId ) {
					return;
				}

				switch( func ) {
					case 'sum':
						var total = 0;
						self.viewModel.entries().forEach( function( entry ) {
							var value = 0;
							if( typeof entry[ targetFieldId ] !== 'undefined' ) {
								value = entry[ targetFieldId ].value ? entry[ targetFieldId ].value : 0;
							}
							total += parseFloat( value );
						} );
						replace = total;
						break;
					case 'total':
						var total = 0;
						self.viewModel.entries().forEach( function( entry ) {
							total += parseFloat( entry.total );
						} );
						replace = total;
						break;
					case 'count':
						replace = self.viewModel.entries().length;
						break;
				}

				formula = formula.replace( search, replace );

			} );

			return formula;
		};

		/**
		 * Static function called via the confirmation of the nested form. Loads the newly created entry into the
		 * Nested Form field displayed on the parent form.
		 *
		 * @param args
		 */
		GPNestedForms.loadEntry = function( args ) {

			var gpnf = $( '#gform_wrapper_' + args.formId ).data( 'GPNestedForms_' + args.fieldId );

			entry = gpnf.prepareEntryForKnockout( args.fieldValues );
			entry.id = args.entryId;

			// edit
			if( args.mode == 'edit' ) {

				// get index of entry
				var entryEditing = $( 'table.gpnf-nested-entries [data-entryid="' + entry.id + '"]' );
				var replacementIndex = entryEditing.index();

				// remove old entry, add updated
				gpnf.viewModel.entries.remove( function( item ) { return item.id == entry.id } );
				gpnf.viewModel.entries.splice( replacementIndex, 0, entry );

				// close dialog
				gpnf.editDialog.dialog( 'close' );

			}
			// add
			else {

				gpnf.modalDialog.dialog( 'close' );
				gpnf.viewModel.entries.push( entry );

				gpnf.refreshMarkup();

			}

		};

		self.init();

	};

	var EntriesModel = function( entries, gpnf ) {

        var self = this;

        self.entries = ko.observableArray( entries );

		/**
		 * Trigger change event on the form when entries change. This helps notify other plugins that the form has
		 * updated if they're listening to the form 'change' events.
		 */
		self.entries.subscribe(function () {
			gpnf.parentFormContainer.children('form').trigger('change');
		});

        self.isMaxed = ko.computed( function() {
        	var max = gform.applyFilters( 'gpnf_entry_limit_max', gpnf.entryLimitMax, gpnf.formId, gpnf.fieldId, gpnf );

	        return max !== '' && self.entries().length >= max;
        } );

        self.entryIds = ko.computed( function() {
            var entryIds = [];
            $.each( self.entries(), function( i, item ) {
                entryIds.push( item.id );
            } );
            return entryIds;
        }, self );

        /**
		 * Run calculations anytime entries modified.
		 */
		self.runCalc = ko.computed( function() {
	        gpnf.runCalc();
	        return self.entries().length;
        }, self );

        self.editEntry = function( item, event ) {
            gpnf.editEntry( item.id, $( event.target ) );
        };

        self.deleteEntry = function( item, event ) {
			gpnf.deleteEntry( item, $( event.target ) );
        }

    };

	/**
	 * Event Handler
	 *
	 * GPNF outputs all inline scripts to the footer for the Nested Form. This means that scripts binding directly to
	 * the document gform_post_render event will trigger before GF's default gform_post_render function which handles
	 * setting various form data (i.e. conditional logic, number formats). If those scripts are using that data it will
	 * generate errors since that data has not yet been defined.
	 *
	 * Our first attempt at solving this involved using $._data() to prioritize our namespaced gform_post_render functions.
	 * This proved to be unreliable (though I'd be willing to revisit).
	 *
	 * Our current solution is the Event Handler. We bind to gform_post_render as early as possible (see
	 * GP_Nested_Forms::handle_event_handler()) and call our gpnfEventHandler(). This function will
	 * a) get an array of all of our namespaced gform_post_render.gpnf bindings
	 * b) unbind them, and
	 * c) call them any time this function is called.
	 *
	 * To recreate, enable the Gravity Forms Dependency Fields add-on on a nested form and disable this function.
	 */
	window.gpnfEventHandler = function( event, formId, currentPage ) {

		if( typeof window.gpnfEvents == 'undefined' ) {
			window.gpnfEvents = [];
		}

		if( window.gpnfEvents.length == 0 ) {
			var events = $._data( document ).events.gform_post_render;
			$.each( events, function( index, event ) {
				if( event.namespace == 'gpnf' ) {
					window.gpnfEvents.push( event.handler );
				}
			} );
			$( document ).off( 'gform_post_render.gpnf' );
		}

		$.each( window.gpnfEvents, function( index, event ) {
			event( event, formId, currentPage );
		} );

	};



    // # GENERAL HELPERS

	function AjaxSpinner( elem, imageSrc, inlineStyles ) {

		imageSrc     = typeof imageSrc == 'undefined' || ! imageSrc ? gf_global.base_url + '/images/spinner.gif' : imageSrc;
		inlineStyles = typeof inlineStyles != 'undefined' ? inlineStyles : '';

		this.elem = elem;
		this.image = '<img class="gfspinner" src="' + imageSrc + '" style="' + inlineStyles + '" />';

		this.init = function() {
			this.spinner = jQuery(this.image);
			jQuery(this.elem).after(this.spinner);
			return this;
		};

		this.destroy = function() {
			jQuery(this.spinner).remove();
		};

		return this.init();
	}

} )( jQuery );
