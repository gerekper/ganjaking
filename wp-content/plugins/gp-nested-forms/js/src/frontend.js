/**
* Nested Forms, mama!
*/
import * as focusTrap from 'focus-trap';
import tingle from 'tingle.js';

const ko = window.ko;

( function( $ ) {

	window.GPNestedForms = function( args ) {

		var self = this;

		// copy all args to current object: formId, fieldId
		for ( const prop in args ) {
			if ( args.hasOwnProperty( prop ) ) {
				self[prop] = args[prop];
			}
		}

		self.destroy = function() {
			self.modal.destroy();
			$( document ).off( '.{0}'.format( self.getNamespace() ) );
			window.gform.removeHook( 'action', 'gform_list_post_item_add', 10, self.getNamespace() );
			window.gform.removeHook( 'action', 'gform_list_post_item_delete', 10, self.getNamespace() );
			gform.removeFilter( 'gform_calculation_formula', 10, 'gpnf_{0}_{1}'.format( self.formId, self.fieldId ) );
			ko.cleanNode(self.$fieldContainer[0]);
		}

		self.init = function() {

			self.id 				  = self.getDebugId();
			self.$fieldContainer      = $( '#field_{0}_{1}'.format( self.formId, self.fieldId ) );
			self.$parentFormContainer = self.$fieldContainer.parents('form').first();
			self.$currentPage 		  = self.$parentFormContainer.find('.gform_page:visible');
			self.$modalSource         = $( '.gpnf-nested-form-{0}-{1}'.format( self.formId, self.fieldId ) );
			self.isActive             = false;

			var inHiddenPage = !!(self.$currentPage.length &&
				!self.$currentPage.find(self.$fieldContainer).length);

			// Handle init when form is reloaded via AJAX.
			if ( typeof window[ 'GPNestedForms_{0}_{1}'.format( self.formId, self.fieldId ) ] !== 'undefined' ) {
				var oldGPNF  = window[ 'GPNestedForms_{0}_{1}'.format( self.formId, self.fieldId ) ];
				self.entries = oldGPNF.entries;

				oldGPNF.destroy();
			}

			/**
			 * Calculations need to be init if the form is not visible to support multi-page forms that use call on
			 * Nested Form calculations such as {Nested Form:xx:sum=yy} from other pages.
			 *
			 * See #21815
			 */
			self.initKnockout();
			self.initCalculations();

			if (inHiddenPage) {
				console.debug('Nested form is not visible. Skipping loading.');

				return;
			}

			var sessionPromise = self.initSession();

			// Click handler for add entry button.
			$( document ).on( 'click.{0}'.format( self.getNamespace() ), '#field_{0}_{1} .gpnf-add-entry'.format( self.formId, self.fieldId ), self.openAddModal );

			self.initModal();
			self.addColorStyles();

			window[ 'GPNestedForms_{0}_{1}'.format( self.formId, self.fieldId ) ] = self;

			/**
			 * Filter whether or not the child form HTML should be immediately fetched when the parent form is loaded.
			 *
			 * @since 1.0-beta-9
			 *
			 * @param boolean 			autoLoadChildForm   Whether or not to load child form HTML on parent form load.
			 * @param int           	formId 				The parent form ID.
			 * @param int             	fieldId   			The field ID of the Nested Form field.
			 * @param {GPNestedForms} 	gpnf      			Current instance of the GPNestedForms object.
			 */
			if (gform.applyFilters('gpnf_fetch_form_html_on_load', true, self.formId, self.fieldId, self)) {
				sessionPromise.always(self.getFormHtml);
			}
		};

		/**
		 * Initialize cookie for GPNF via AJAX.
		 *
		 * Session should only be initialized once per parent form.
		 *
		 * @returns {JQueryXHR}
		 */
		self.initSession = function() {
			if (typeof window['gpnfSessionPromise_' + self.formId] === 'undefined') {
				window['gpnfSessionPromise_' + self.formId] = $.post( self.ajaxUrl, self.sessionData, function( response ) {
					/**
					 * Do something after the Nested Forms session has been initialized.
					 *
					 * @since 1.0-beta-8.62
					 *
					 * @param {GPNestedForms} gpnf Current instance of the GPNestedForms class.
					 */
					gform.doAction( 'gpnf_session_initialized', self );
				} );
			}

			return window['gpnfSessionPromise_' + self.formId];
		};

		self.initModal = function() {

			self.modalArgs = gform.applyFilters( 'gpnf_modal_args', {
				labels: self.modalLabels,
				closeLabel: self.modalLabels.closeScreenReaderLabel,
				colors: self.modalColors,
				footer: true,
				stickyFooter: self.modalStickyFooter,
				closeMethods: [ 'button' ],
				cssClass: [ self.modalClass, 'gpnf-modal', 'gpnf-modal-{0}-{1}'.format( self.formId, self.fieldId ) ],
				onOpen: function() {
					self.isActive = true;

					if ( ! self.trap && self.enableFocusTrap ) {
						self.trap = focusTrap.createFocusTrap( self.modal.modalBox.parentElement, {
							escapeDeactivates: false,
							allowOutsideClick: true,
						} );
					}

					if ( self.trap ) {
						self.trap.activate();
					}
				},
				onClose: function() {
					self.clearModalContent();
					self.setParentFocus();

					if ( self.trap ) {
						self.trap.deactivate();
					}

					self.isActive = false;
				},
				beforeOpen: function() {
					self.$modal
						.attr( 'role', 'dialog' )
						.attr( 'aria-label', self.getModalTitle() )
						.attr( 'aria-modal', 'true' );
					self.$modal.find( '.tingle-modal__close' )
						.attr( 'aria-label', self.modalLabels.closeScreenReaderLabel );
				},
				beforeClose: function() { return true; }
			}, self.formId, self.fieldId, self );

			if ( self.modal ) {
				self.$modal = $( self.modal.modal );
				return;
			}

			self.modal  = new tingle.modal( self.modalArgs );
			self.$modal = $( self.modal.modal );

			self.bindResizeEvents();

			// Re-init modaled forms; 'gpnf_post_render' triggered on any nested form's first load every time a nested
			// form is retrieved via ajax (aka editing, first load and each page load).
			$( document ).on( 'gpnf_post_render.{0}'.format( self.getNamespace() ), function( event, formId, currentPage ) {

				var $nestedForm = $( '#gform_wrapper_' + formId );

				/**
				 * Only initialize the scripts for a given child form if it is the current form. In the case where
				 * multiple Nested Form fields include the same child form, use the isActive flag (set when
				 * opening/closing a Nested Form field's modal) to ensure we're only initializing scripts for the
				 * current instance of that child form.
				 */
				if ( formId == self.nestedFormId && $nestedForm.length > 0 && self.isActive ) {

					self.scrollToTop();

					// Don't re-init buttons on the confirmation page; currentPage is undefined on the confirmation page.
					if ( currentPage ) {
						self.initFormScripts(currentPage);
						// Set the focus on the first focusable field on each page render of the child form. Timeout
						// helps with Mac's VoiceOver which does not consistently follow the focus without it.
						setTimeout( function () {
							const $firstInput = self.$modal.find( 'input, select, textarea' ).filter( ':visible' ).first();

							if ( $firstInput.hasClass( 'datepicker' ) ) {
								const inputAlreadyDisabled = $firstInput.prop( 'disabled' );
								$firstInput?.datepicker( 'disable' );

								/* Disabling datepicker() also sets disabled on the input. We don't want that, we only want to disable the datepicker so it doesn't show on initial focus. */
								if ( !inputAlreadyDisabled ) {
									$firstInput.prop( 'disabled', false );
								}
							}

							$firstInput.focus();

							if ( $firstInput.hasClass( 'datepicker' ) ) {
								$firstInput?.datepicker( 'enable' );
							}
						} );
						self.addModalButtons();
						self.observeDefaultButtons();
						self.triggerButtonAnimationOnSubmit();
					}

				}

			} );

		};

		self.initKnockout = function() {
			/**
			 * If VM already exists, reset the observable array as rebinding can cause issues.
			 */
			if (self.viewModel && ko.dataFor(self.$fieldContainer[0])) {
				self.viewModel.entries(self.prepareEntriesForKnockout(self.entries));
				return;
			}

			// Setup Knockout to handle our Nested Form field entries.
			self.viewModel = new EntriesModel(self.prepareEntriesForKnockout(self.entries), self);
			ko.cleanNode(self.$fieldContainer[0]);
			ko.applyBindings(self.viewModel, self.$fieldContainer[0]);
		};

		self.initCalculations = function() {

			gform.addFilter( 'gform_calculation_formula', self.parseCalcs, 10, 'gpnf_{0}_{1}'.format( self.formId, self.fieldId ) );
			self.runCalc( self.formId );

		};

		self.openAddModal = function( event ) {

			event.preventDefault();

			event.target.disabled = true;

			var $spinner = new AjaxSpinner( event.target, self.spinnerUrl, '' );

			self.getFormHtml().done(function (html) {
				self.setModalContent(html);
				self.openModal( $( event.target ) );
			}).always(function() {
				$spinner.destroy();
				event.target.disabled = false;
			});

		};

		self.openModal = function( trigger ) {
			self.saveParentFocus( trigger );
			self.modal.open();
			/**
			 * We need to to manually trigger our `gpnf_post_render` event so that init scripts are executed in
			 * two scenarios.
			 *
			 * 1. When running GF 2.5 as it wraps init scripts in DOMContentLoaded (instead of jQuery's ready event) so
			 *    child form init scripts are not automatically executed when they're included in the DOM.
			 * 2. When the version of jQuery is less than v3. v3.5 is included in WordPress 5.5+. Before that,
			 *    jQuery v1.12.4 was included. Not sure why this is necessary but I'm assuming most users will either be
			 *    on WordPress 5.5+ - or - progressively, they'll be on GF 2.5.
			 */
			if ( self.isGF25 || parseInt( jQuery.fn.jquery ) < 3 ) {
				$( document ).trigger( 'gpnf_post_render', [ self.nestedFormId, '1' ] );
			}
			self.initIframe( self.nestedFormId );
		};

		self.saveParentFocus = function( trigger ) {
			self.parentFocus = trigger;
		};

		self.setParentFocus = function() {

			var $focus;

			switch ( typeof self.parentFocus ) {
				case 'undefined':
					// This is here to account for 3rd-parties calling openModal() without adding a trigger.
					return;
				case 'function':
					$focus = self.parentFocus.apply();
					break;
				default:
					$focus = self.parentFocus;
					break;
			}

			$focus.focus();

		};

		self.scrollToTop = function() {

			// Scroll back to the top of the modal when a new page is loaded or there is a validation error.
			var modalContainerNode = $( self.modal.modal )[0];
			if ( modalContainerNode.scroll ) {
				modalContainerNode.scroll( { top: 0, left: 0, behavior: 'smooth' } );
			} else {
				modalContainerNode.scrollTop = 0;
			}
		};

		self.observeDefaultButtons = function() {
			var observer = self.getDefaultButtonObserver();
			self.getDefaultButtons().each( function() {
				observer.observe( $( this )[0], { attributes: true, childList: true } );
			} );
		};

		self.getDefaultButtonObserver = function() {
			return new MutationObserver( function( mutations ) {
				mutations.forEach(function(mutation) {
					if ( mutation.type == 'attributes' && ( mutation.attributeName == 'style' || mutation.attributeName == 'disabled' ) ) {
						self.addModalButtons();
					}
				} );
			} );
		};

		/**
		 * Trigger spinner on Submit/Next buttons when form submission is triggered by Enter keypress.
		 *
		 * This is an accessibility/usability improvement that provides a better visual indicator that the form is being submitted.
		 */
		self.triggerButtonAnimationOnSubmit = function() {
			self.$modal.on( 'keypress', function( event ) {
				self.$modal.data( 'gpnfEnterPressed', event.which === 13 );
			} );
			self.$modal.find( 'form' ).on( 'submit', function() {
				if ( self.$modal.data( 'gpnfEnterPressed' ) ) {
					self.$modal.data( 'gpnfEnterPressed', false );
					$( self.modal.modalBoxFooter ).find( '.gpnf-btn-submit, .gpnf-btn-next' ).addClass( 'gpnf-spinner' );
				}
			} );
		}

		self.setModalContent = function( html, mode ) {

			$( document ).off( 'gform_post_render.gpnf' );

			self.setMode( typeof mode === 'undefined' ? 'add' : 'edit' );

			$( self.modal.modalBoxContent )
				.html( typeof html !== 'undefined' ? html : self.formHtml )
				.prepend( '<div class="gpnf-modal-header" style="background-color:{1}">{0}</div>'.format( self.getModalTitle(), self.modalHeaderColor ) );

			self.$modal.find( 'input[name="gpnf_nested_form_field_id"]' ).val( self.fieldId );

			self.addModalButtons();
			self.stashFormData();

			var observer = self.getDefaultButtonObserver();
			self.getDefaultButtons().each( function() {
				observer.observe( $( this )[0], { attributes: true, childList: true } );
			} );

		};

		self.clearModalContent = function() {
			$( self.modal.modalBoxContent ).html( '' );
		};

		self.setMode = function( mode ) {
			self.mode = mode;
		};

		self.getMode = function() {
			return self.mode ? self.mode : 'add';
		};

		self.getModalTitle = function() {
			return self.getMode() === 'add' ? self.modalArgs.labels.title : self.modalArgs.labels.editTitle;
		};

		/**
		 * Logic borrowed from gravityforms.js (Lines 2539-2551)
		 *
		 * @returns {boolean}
		 */
		self.hasPendingUploads = function() {
			var pendingUploads = false;

			if (!gfMultiFileUploader || !gfMultiFileUploader.uploaders) {
				return false;
			}

			$.each(gfMultiFileUploader.uploaders, function(i, uploader){
				if(uploader.total.queued>0){
					pendingUploads = true;
					return false;
				}
			});

			return pendingUploads;
		}

		self.addModalButtons = function() {

			self.modal.modalBoxFooter.innerHTML = '';

			/**
			 * Filter the CSS classes of buttons in the Nested Form modal.
			 *
			 * @since 1.0-beta-10.1
			 *
			 * @param string 			buttonClasses 		CSS classes for the button
			 * @param string 			buttonType 			The button type. Will be one of the following: 'submit', 'next', 'previous', 'delete', 'cancel'
			 * @param int           	formId 				The parent form ID.
			 * @param int             	fieldId   			The field ID of the Nested Form field.
			 * @param {GPNestedForms} 	gpnf      			Current instance of the GPNestedForms object.
			 */
			var cancelButtonCSSClasses = window.gform.applyFilters('gpnf_modal_button_css_classes', 'tingle-btn tingle-btn--default gpnf-btn-cancel', 'cancel', self.formId, self.fieldId, self);

			self.modal.addFooterBtn( self.modalArgs.labels.cancel, cancelButtonCSSClasses, function() {
				self.handleCancelClick( $( this ) );
			} );

			self.getDefaultButtons().each( function() {
				var $button = $( this );
				var buttonType = null;
				// Check if WooCommerce Gravity Forms is active. It hides Submit buttons by default and replaces them
				// with an "Add to Cart" button. We can ignore that in nested forms. We should also check for the style
				// attribute on the element to ensure that conditional paging functions in those scenarios.
				var isWooCommercePage = typeof window.jQuery.fn.wc_gravity_form === 'function';
				if ( $button[0].style.display !== 'none' || ( isWooCommercePage && $button[0].style.display === '' ) ) {

					var isSubmitButton = ( $button.attr( 'type' ) === 'submit' || $button.attr( 'type' ) === 'image' ),
						label          = isSubmitButton ? self.getSubmitButtonLabel() : $button.val(),
						classes        = [ 'tingle-btn', 'tingle-btn--primary' ],
						isDisabled     = $button.is( ':disabled' );

					if ( $button.hasClass( 'gform_previous_button' ) ) {
						classes.push( 'gpnf-btn-previous' );
						buttonType = 'previous';
					} else if ( $button.hasClass( 'gform_next_button' ) ) {
						buttonType = 'next';
						classes.push( 'gpnf-btn-next' );
					} else {
						buttonType = 'submit';
						classes.push( 'gpnf-btn-submit' );
					}

					classes = window.gform.applyFilters('gpnf_modal_button_css_classes', classes.join(' '), buttonType, self.formId, self.fieldId, self);

					var tingleBtn = self.modal.addFooterBtn( label, classes, function( event ) {
						if (self.hasPendingUploads()) {
							var gfStrings = typeof gform_gravityforms != 'undefined' ? gform_gravityforms.strings : {};
							alert(gfStrings.currently_uploading);

							return;
						}

						$( event.target ).addClass( 'gpnf-spinner' );
						$button.click();
					} );

					if ( isDisabled ) {
						$( tingleBtn ).prop( 'disabled', true );
					}

				}
			} );

			var mobileCancelClasses = window.gform.applyFilters('gpnf_modal_button_css_classes', 'tingle-btn tingle-btn--default gpnf-btn-cancel-mobile', 'cancel-mobile', self.formId, self.fieldId, self);

			self.modal.addFooterBtn( self.modalArgs.labels.cancel, mobileCancelClasses, function() {
				self.handleCancelClick( $( this ) );
			} );

			// If we're in edit mode - AND - there is a form, show the delete button. Otherwise, we're showing an error message.
			if ( self.mode == 'edit' && $( self.modal.modalBoxContent ).find( '.gform_wrapper' ).length > 0 ) {
				var deleteButtonClasses = window.gform.applyFilters('gpnf_modal_button_css_classes', 'tingle-btn tingle-btn--danger tingle-btn--pull-left gpnf-btn-delete', 'delete', self.formId, self.fieldId, self);

				self.modal.addFooterBtn( self.modalArgs.labels.delete, deleteButtonClasses, function() {
					var $button = $( this );
					var isConfirmActionEnabled = self.modalArgs.labels.confirmAction !== false && self.modalArgs.labels.confirmAction !== '';
					if ( ! $button.data( 'isConfirming' ) && isConfirmActionEnabled ) {
						$button
							.data( 'isConfirming', true )
							.text( self.modalArgs.labels.confirmAction );
						setTimeout( function() {
							$button
								.data( 'isConfirming', false )
								.text( self.modalArgs.labels.delete );
						}, 3000 );
					} else {
						self.getEntryRow( self.getCurrentEntryId() ).find( '.delete-button' ).click();
						self.modal.close();
					}
				} );
			}

		};

		self.getSubmitButtonLabel = function() {

			var mode = self.getMode();

			if ( mode === 'add' && self.modalArgs.labels.submit ) {
				return self.modalArgs.labels.submit;
			} else if ( mode === 'edit' && self.modalArgs.labels.editSubmit ) {
				return self.modalArgs.labels.editSubmit;
			}

			return self.getModalTitle();
		}

		self.addColorStyles = function() {

			if ( self.$style && typeof self.$style.remove === 'function' ) {
				self.$style.remove();
			}

			self.$style = '<style type="text/css"> \
					.gpnf-modal-{0}-{1} .tingle-btn--primary { background-color: {2}; } \
					.gpnf-modal-{0}-{1} .tingle-btn--default { background-color: {3}; } \
					.gpnf-modal-{0}-{1} .tingle-btn--danger { background-color: {4}; } \
				</style>'.format( self.formId, self.fieldId, self.modalArgs.colors.primary, self.modalArgs.colors.secondary, self.modalArgs.colors.danger );

			$( 'head' ).append( self.$style );

		};

		/**
		 * Gets nested form's submit button
		 *
		 * Looks for HTML elements in the nested form's footer and returns them in
		 * a jQuery object.
		 *
		 * This will match the following elements:
		 * <input type="button">
		 * <input type="submit">
		 * <input type="image">
		 * <button>
		 *
		 * @param void
		 * @return object   jQuery object containing default buttons
		 */
		self.getDefaultButtons = function() {
			/*
			 * The .first() call here is used in the event that the parent form markup is on the page more than one time. This can happen with
			 * third-party plugins such as wpDataTables.
			 */
			return $( '#gform_page_{0}_{1} .gform_page_footer, #gform_{0} .gform_footer'.format( self.nestedFormId, self.getCurrentPage() ) ).first().find( 'input[type="button"], input[type="submit"], input[type="image"], button' );
		};

		self.handleCancelClick = function( $button ) {
			/**
			 * Filter if GPNF should not warn before canceling adding a new entry.
			 *
			 * Return "true" here to disable the "Are you sure?" button prompt.
			 *
			 * @since 1.0-beta-9.24
			 */
			var disableNewCancelConfirmation = window.gform.applyFilters( 'gpnf_disable_new_cancel_confirmation', self.modalArgs.labels.confirmAction === false || self.modalArgs.labels.confirmAction === '' );
			if ( $button.data( 'isConfirming' ) ) {
				self.modal.close();
			} else if ( self.hasChanges() && ! disableNewCancelConfirmation ) {
				$button
					.data( 'isConfirming', true )
					.removeClass( 'tingle-btn--default' )
					.addClass( 'tingle-btn--danger' )
					.text( self.modalArgs.labels.confirmAction );
				setTimeout( function() {
					$button
						.data( 'isConfirming', false )
						.removeClass( 'tingle-btn--danger' )
						.addClass( 'tingle-btn--default' )
						.text( self.modalArgs.labels.cancel );
				}, 3000 );
			} else {
				self.modal.close();
			}
		};

		self.setMode = function( mode ) {
			self.mode = mode;
		};

		self.getMode = function() {
			return self.mode ? self.mode : 'add';
		};

		self.stashFormData = function() {
			self.formData = self.$modal.find( 'form' ).serialize();
		};

		self.hasChanges = function() {
			return self.$modal.find( 'form' ).serialize() !== self.formData;
		};

		self.bindResizeEvents = function() {

			$( document ).on( 'gpnf_post_render.{0}'.format( self.getNamespace() ), function() {
				self.modal.checkOverflow();
			} );

			$( document ).on( 'gform_post_conditional_logic.{0}'.format( self.getNamespace() ), function( event, formId ) {
				if ( self.nestedFormId == formId ) {
					self.modal.checkOverflow();
				}
			} );

			gform.addAction( 'gform_list_post_item_add', self.modal.checkOverflow, 10, self.getNamespace() );
			gform.addAction( 'gform_list_post_item_delete', self.modal.checkOverflow, 10, self.getNamespace() );

		};

		self.isBound = function( elem ) {
			return ! ! ko.dataFor( elem );
		};

		self.prepareEntriesForKnockout = function( entries ) {
			for ( var i = 0; i < entries.length; i++ ) {
				entries[i] = self.prepareEntryForKnockout( entries[i] );
			}
			return entries;
		};

		self.prepareEntryForKnockout = function( entry ) {

			// IE8 hack to fix recursive loop issue; props to Josh Casey
			var entryTemplate = $.extend( {}, entry );

			for ( var prop in entryTemplate ) {
				if ( entry.hasOwnProperty( prop ) ) {
					var item = entry[ prop ];
					if ( item.label === false ) {
						item.label = '';
					}
					entry['f' + prop] = item;
				}
			}

			return entry;
		};

		self.refreshMarkup = function() {

			return $.post( self.ajaxUrl, {
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

					self.setModalContent( response, 'edit' );
					self.openModal( function() {
						// This is the element to which the focus should be returned after the modal is closed.
						return self.$parentFormContainer.find( '[data-entryid="' + entryId + '"]' ).find( '.edit' ).find( 'a, button' );
					} );

            } );

		};

		self.deleteEntry = function( item, $trigger ) {

			/**
			 * Filter whether the child entry should be deleted. Useful for
			 * adding custom confirmations before deleting.
			 *
			 * @since 1.0-rc-1.10
			 *
			 * @param boolean 			shouldDelete 		Whether the child entry should be deleted.
			 * @param object 			item 			    The child entry being deleted.
			 * @param JQuery           	$trigger 			The element that triggered the deletion.
			 * @param {GPNestedForms} 	gpnf      			Current instance of the GPNestedForms object.
			 */
			if ( window.gform.applyFilters( 'gpnf_should_delete', true, item, $trigger, self ) === false ) {
				return;
			}

			var $spinner = new AjaxSpinner( $trigger, self.spinnerUrl, '' );
			$trigger.css( { visibility: 'hidden' } );

			$.post( self.ajaxUrl, {
				action: 'gpnf_delete_entry',
				nonce:  GPNFData.nonces.deleteEntry,
				gpnf_entry_id: item.id
				}, function( response ) {

					$spinner.destroy();
					$trigger.css( { visibility: 'visible' } );

					if ( ! response ) {
						console.log( 'Error: no response.' );
						return;
					} else if ( ! response.success ) {
						console.log( 'Error:' + response.data );
						return;
					}

					// Success!
					self.viewModel.entries.remove( item );

					/**
					 * Filter to determine if the child form HTML should be refreshed after deleting child entries.
					 *
					 * Return "false" here to disable refreshing child form HTML via AJAX after entries are deleted.
					 *
					 * @since 1.0-beta-9.28
					 *
					 * @param boolean 			refreshMarkup   	Whether or not to refresh HTML after deleting entries.
					 * @param int           	formId 				The parent form ID.
					 * @param int             	fieldId   			The field ID of the Nested Form field.
					 * @param {GPNestedForms} 	gpnf      			Current instance of the GPNestedForms object.
					 */
					if ( window.gform.applyFilters( 'gpnf_fetch_form_html_after_delete', true, self.formId, self.fieldId, self ) ) {
						self.refreshMarkup();
					}
            } );

		};

		self.duplicateEntry = function( entryId, $trigger ) {

			var $spinner = new AjaxSpinner( $trigger, self.spinnerUrl, '' );
			$trigger.css( { visibility: 'hidden' } );

			$.post(
				self.ajaxUrl,
				{
					action: 'gpnf_duplicate_entry',
					nonce: GPNFData.nonces.duplicateEntry,
					gpnf_entry_id: entryId,
					gpnf_parent_form_id: self.formId,
					gpnf_nested_form_field_id: self.fieldId
				},
				function( response ) {

					$spinner.destroy();
					$trigger.css( { visibility: 'visible' } );

					if ( response.success ) {
						GPNestedForms.loadEntry( response.data );
					}

					/**
					 * Do something after a child entry has been duplicated on the frontend.
					 *
					 * @param object entry    The properties of the child entry including field values.
					 * @param object response The full response from the duplication AJAX request.
					 *
					 * @since 1.0-beta-8.70
					 */
					gform.doAction( 'gpnf_post_duplicate_entry', response.data.entry, response );

				}
			);
		};

		self.getFormHtml = function() {

			if (self.formHtml) {
				return $.when(self.formHtml);
			}

			return self.refreshMarkup();

		};

		self.initFormScripts = function( currentPage ) {

			/**
			 * Do something immediately before the nested form modal is initialized.
			 *
			 * @since 1.0-beta-8.25
			 *
			 * @param int            nestedFormId The ID of the nested form being initialized in the modal.
			 * @param \GPNestedForms gpnf         The instance of GPNestedForms (specific to the parent form and Nested Form field) that is initializing the modal.
			 */
			window.gform.doAction( 'gpnf_init_nested_form', self.nestedFormId, self );
			$( document ).trigger( 'gform_post_render', [ self.nestedFormId, currentPage ] );

			if ( window['gformInitDatepicker'] ) {
				/*
				 * Disable focusTrap if interacting with date picker. Even though we have allowOutsideClick enabled, this addresses
				 * a bug with Firefox where the selects cannot be interacted with.
				 */
				if (self.trap) {
					gform.addFilter( 'gform_datepicker_options_pre_init', function ( optionsObj, formId ) {
						if ( formId != self.nestedFormId ) {
							return optionsObj;
						}

						var origBeforeShow = optionsObj.beforeShow;
						optionsObj.beforeShow = function ( input, inst ) {
							origBeforeShow( input, inst );
							self.trap.deactivate({ returnFocus: false });
						}
						var origOnClose = optionsObj.onClose;
						optionsObj.onClose = function ( input, inst ) {
							origOnClose( input, inst );
							self.trap.activate();
						}
						return optionsObj;
					} );
				}

				self.$modal.find( '.datepicker' ).each( function() {
					// Remove 'hasDatepicker' before initializing. This seems to be added when viewing
					// in a Gravity Flow Inbox page and breaks jQuery's Date Picker.
					$( this ).removeClass( 'hasDatepicker' );
					gformInitSingleDatepicker( $( this ) );
				} );
			}

			self.handleParentMergeTag();

			gform.addAction('gform_post_conditional_logic_field_action', function (formId, action, targetId, defaultValues, isInit) {
				if ( self.nestedFormId == formId ) {
					var fieldId = gf_get_input_id_by_html_id(targetId);
					self.handleParentMergeTag( [fieldId] );
				}
			});
		};

		/**
		 * We really need a better way to trigger calculations.
		 */
		self.runCalc = function() {
			$( document ).trigger( 'gform_post_conditional_logic', [ self.formId, [], false ] );
		};

		self.parseCalcs = function( formula, formulaField, formId, calcObj ) {

			if ( formId != self.formId ) {
				return formula;
			}

			var matches = getMatchGroups( formula, /{[^{]*?:([0-9]+):(sum|total|count)=?([0-9]*)}/i );
			$.each( matches, function( i, group ) {

				var search            = group[0],
					nestedFormFieldId = group[1],
					func              = group[2],
					targetFieldId     = group[3],
					replace           = 0;

				if ( nestedFormFieldId != self.fieldId ) {
					return;
				}

				/*
				 * Return 0 if the Nested Form field is hidden with Conditional Logic.
				 * This matches the backend behavior of discarding any child entries if the Nested Form field is hidden.
				 */
				if ( gformIsHidden( self.$fieldContainer.find('> :first-child') ) ) {
					return 0;
				}

				switch ( func ) {
					case 'sum':
						var total = 0;
						self.viewModel.entries().forEach( function( entry ) {
							var value = 0;
							if ( typeof entry[ targetFieldId ] !== 'undefined' ) {
								value = entry[ targetFieldId ].value ? gformToNumber( entry[ targetFieldId ].value ) : 0;
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

		self.handleParentMergeTag = function ( fieldIds ) {

			var $inputs;

			if ( typeof fieldIds !== 'undefined' ) {
				var selectors = [];
				$.each( fieldIds, function( index, fieldId ) {
					selectors.push( '#field_{0}_{1}'.format( self.nestedFormId, fieldId ) );
				} );
				$inputs = self.$modal.find( selectors.join( ',' ) ).find( ':input' );
			} else {
				$inputs = self.$modal.find( ':input' );
			}

			$inputs.each(function () {
				var $this = $( this );
				var value = $this.data( 'gpnf-value' );

				if ( ! value) {
					return true;
				}


				/**
				 * Removes parent merge tags for child/parent values that are empty
				 * so when editing or if a validation error comes up, there
				 * aren't fields still showing {Parent} merge tags.
				 */
				function clearEmptyParentMergeTags(el) {
					var $el = $(el);
					value = $el.val();

					var parentMergeTags = self.getParentMergeTags( value );

					if ( parentMergeTags.length ) {
						for ( var i = 0, max = parentMergeTags.length; i < max; i ++ ) {
							value = value.replace( parentMergeTags[i][0], '' );
						}

						$el.val( value ).change().trigger("chosen:updated");
					}
				}

				/* Do not copy in parent merge tag values, only clear out ones with empty values */
				if ( self.$modal.find( '.gform_validation_error' ).length !== 0 ) {
					clearEmptyParentMergeTags(this);
					return true;
				}

				var parentMergeTagMatches = self.getParentMergeTags( value );

				if ( ! parentMergeTagMatches) {
					return true;
				}

				for ( var i = 0; i < parentMergeTagMatches.length; i++ ) {

					var inputId = parentMergeTagMatches[i][1];

					if (isNaN( inputId )) {
						return true;
					}

					var $parentInput = self.$parentFormContainer.find( '#input_' + self.formId + '_' + inputId.split( '.' ).join( '_' ) );
					if ( $parentInput.hasClass( 'gfield_radio' ) || $parentInput.hasClass( 'gfield_checkbox' ) ) {
						$parentInput = $parentInput.find( 'input:checked' );
					}

					var parentValue = [];

					// Apply input label if :label modifier is added
					if ( parentMergeTagMatches[i][0].indexOf(':label') !== -1 ) {
						$parentInput.each(function() {
							var $input = $(this);

							if ( $input.hasClass('gfield_select') ) {
								parentValue.push($input.find('option:selected').text());
							} else {
								parentValue.push($input.parent().find('label').text());
							}
						});
					} else {
						$parentInput.each(function() {
							parentValue.push($(this).val());
						});
					}

					// Convert array of values to string.
					parentValue = parentValue.join(', ');

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
					parentValue = gform.applyFilters( 'gpnf_parent_merge_tag_value', $parentInput.length ? parentValue : '', inputId, self.formId, self );

					value = value.replace( parentMergeTagMatches[i][0], parentValue );

				}

				value = value.trim();

				var currentValue = $( this ).val();

				/**
				 * Filter whether GPNF should re-populate any parent merge tags  when editing an entry
				 *
				 * @since 1.0-beta-9.28
				 *
				 * @param boolean 			replace_parent_merge_tag   Whether or not to re-apply parent merge tags
				 * @param int           	formId 				       The parent form ID.
				 */
				if ( self.mode === 'edit' && ! gform.applyFilters( 'gpnf_replace_parent_merge_tag_on_edit', false, self.formId ) ) {
					// Skip processing edited/populated merge tags.
					// One exception here is that we need to ensure that `{parent}` merge tags are removed.
					// This can occur when editing a child entry with an empty field and an empty parent field value.
					// See: HS#27251
					clearEmptyParentMergeTags(this);

					// Update GP Read Only hidden capture to the current value of the input that way when editing it doesn't revert to
					// the parent merge tag as the value.
					$( this ).siblings( `input[name^="gwro_hidden_capture_"]` )
					         .val( currentValue );

					return true;
				} else {
					// Update GP Read Only hidden capture to the new value of the input from the parent merge tag.
					$( this ).siblings( `input[name^="gwro_hidden_capture_"]` )
					         .val( value );
				}

				if ( currentValue != value ) {
					$( this ).val( value ).change().trigger( "chosen:updated" );

					// TinyMCE appears to initialize _before_ the parent merge tags are handled. Instead of changing priority, reset the TinyMCE editor content accordingly.
					const inputIdBits = $( this ).prop( 'id' )?.split( '_' );
					const fieldId = inputIdBits?.[2];

					const tinyMCEEditor = window?.tinyMCE?.editors?.[`input_${self.nestedFormId}_${fieldId}`];

					if ( tinyMCEEditor ) {
						tinyMCEEditor.setContent( value );
					}
				}
			});

		};

		self.getParentMergeTags = function( string ) {

			var matches = [],
				pattern = /{Parent:(\d+(\.\d+)?)[^\}]*}/i;

			while ( pattern.test( string ) ) {
				var i      = matches.length;
				matches[i] = pattern.exec( string );
				string     = string.replace( '' + matches[i][0], '' );
			}

			return matches;
		};

		self.getCurrentPage = function() {
			var currentPage = $( '#gform_source_page_number_{0}'.format( self.nestedFormId ) ).val();
			return Math.max( 1, parseInt( currentPage ) );
		};

		self.getCurrentEntryId = function() {
			return self.$modal.find( 'input[name="gpnf_entry_id"]' ).val()
		};

		self.getEntryRow = function( entryId ) {
			return $( '.gpnf-nested-entries [data-entryid="' + entryId + '"]' );
		};

		self.getDebugId = function() {
			return 'xxxxxxxx'.replace( /[xy]/g, function ( c ) {
				var r = Math.random() * 16 | 0, v = c == 'x' ? r : r & 0x3 | 0x8;
				return v.toString( 16 );
			} );
		};

		self.getNamespace = function() {
			return 'gpnf-{0}-{1}'.format( self.formId, self.fieldId );
		};

		/**
		 * Initialize the iframe to receive posted form response. Cannot rely on Gravity Forms 2.5+ to handle this due
		 * to a loading order-of-events issue introduced by the switch to DOMContentLoaded from document.ready.
		 *
		 * @param {int} formId
		 */
		self.initIframe = function( formId ) {
			$( '#gform_ajax_frame_{0}'.format( formId ) )
				// This hasn't been proven to be necessary but added this to make it as bulletproof as possible given
				// that < GF 2.5 will still bind its own event to the iframe's load.
				.off( 'load' )
				.on( 'load', function() {
					var contents    = $( this ).contents().find( '*' ).html();
					var is_postback = contents.indexOf( 'GF_AJAX_POSTBACK' ) >= 0;
					if ( ! is_postback ) {
						return;
					}
					var form_content    = $( this ).contents().find( '#gform_wrapper_{0}'.format( formId ) );
					var is_confirmation = $( this ).contents().find( '#gform_confirmation_wrapper_{0}'.format( formId ) ).length > 0;
					var is_redirect     = contents.indexOf( 'gformRedirect(){' ) >= 0;
					var is_form         = form_content.length > 0 && ! is_redirect && ! is_confirmation;
					var $formWrapper    = $( '#gform_wrapper_{0}'.format( formId ) );
					if ( is_form ) {
						$formWrapper.html( form_content.html() );
						if (form_content.hasClass( 'gform_validation_error' )) {
							$formWrapper.addClass( 'gform_validation_error' );
						} else {
							$formWrapper.removeClass( 'gform_validation_error' );
						}
						setTimeout( function() { /* delay the scroll by 50 milliseconds to fix a bug in chrome */ }, 50 );
						if ( window['gformInitPriceFields']) {
							gformInitPriceFields();
						}
						var current_page = $( '#gform_source_page_number_{0}'.format( formId ) ).val();
						$( document ).trigger( 'gform_page_loaded', [ formId, current_page] );
						window[ 'gf_submitting_{0}'.format( formId ) ] = false;
					} else if ( ! is_redirect) {
						var confirmation_content = $( this ).contents().find( '.GF_AJAX_POSTBACK' ).html();
						if ( ! confirmation_content) {
							confirmation_content = contents;
						}
						setTimeout( function() {
							$formWrapper.replaceWith( confirmation_content );
							$( document ).trigger( 'gform_confirmation_loaded', [ formId ] );
							window[ 'gf_submitting_{0}'.format( formId ) ] = false;
						}, 50 );
					}
					$( document ).trigger( 'gpnf_post_render', [ formId, current_page] );
				} );
		}

		/**
		 * Static function called via the confirmation of the nested form. Loads the newly created entry into the
		 * Nested Form field displayed on the parent form.
		 *
		 * @param args
		 */
		GPNestedForms.loadEntry = function( args ) {

			/** @var \GPNestedForms gpnf */
			var gpnf = window[ 'GPNestedForms_{0}_{1}'.format( args.formId, args.fieldId ) ];

			const entry    = gpnf.prepareEntryForKnockout( args.fieldValues );
			entry.id = args.entryId;

			// edit
			if ( args.mode == 'edit' ) {

				// get index of entry
				var entryEditing     = self.getEntryRow( entry.id );
				var replacementIndex = entryEditing.index();

				// remove old entry, add updated
				gpnf.viewModel.entries.remove( function( item ) { return item.id == entry.id } );
				gpnf.viewModel.entries.splice( replacementIndex, 0, entry );

			}
			// add
			else {

				gpnf.viewModel.entries.push( entry );

				/**
				 * Filter to determine if the child form HTML should be refreshed after adding entries.
				 *
				 * Return "false" here to disable refreshing child form HTML via AJAX after new entries are added.
				 *
				 * @since 1.0-beta-9.28
				 *
				 * @param boolean 			refreshMarkup   	Whether or not to refresh HTML after adding entries.
				 * @param int           	formId 				The parent form ID.
				 * @param int             	fieldId   			The field ID of the Nested Form field.
				 * @param {GPNestedForms} 	gpnf      			Current instance of the GPNestedForms object.
				 */
				if ( window.gform.applyFilters( 'gpnf_fetch_form_html_after_add', true, self.formId, self.fieldId, self ) ) {
					gpnf.refreshMarkup();
				}

			}

			if ( gpnf.isActive ) {
				gpnf.modal.close();
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
			gpnf.$parentFormContainer.children( 'form' ).trigger( 'change' );
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
		};

		self.duplicateEntry = function( item, event ) {
			gpnf.duplicateEntry( item.id, $( event.target ) );
		}

	};

	// # GENERAL HELPERS

	function AjaxSpinner( elem, imageSrc, inlineStyles ) {

		imageSrc     = typeof imageSrc == 'undefined' || ! imageSrc ? gf_global.base_url + '/images/spinner.gif' : imageSrc;
		inlineStyles = typeof inlineStyles != 'undefined' ? inlineStyles : '';

		this.elem  = elem;
		this.image = '<img class="gfspinner" src="' + imageSrc + '" style="' + inlineStyles + '" />';

		this.init = function() {
			this.spinner = jQuery( this.image );
			jQuery( this.elem ).after( this.spinner );
			return this;
		};

		this.destroy = function() {
			jQuery( this.spinner ).remove();
		};

		return this.init();
	}

} )( jQuery );
