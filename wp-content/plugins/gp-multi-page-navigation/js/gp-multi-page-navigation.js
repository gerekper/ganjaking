(function($){

	window.GPMultiPageNavigation = function( args ) {

		var self = this;

		self.formId    = args.formId;
		self.$formElem = $( 'form#gform_' + self.formId );

		self.lastPage                               = args.lastPage;
		self.activationType                         = args.activationType;
		self.labels                                 = args.labels;
		self.enableSubmissionFromLastPageWithErrors = args.enableSubmissionFromLastPageWithErrors;

		self.init = function() {

			if ( self.$formElem.length <= 0 ) {
				self.$formElem = $( '#gform_wrapper_' + self.formId );
			}

			// set page specific elements
			self.$footer                = $( '#gform_page_' + self.formId + '_' + self.getCurrentPage() + ' .gform_page_footer' );
			self.$saveAndContinueButton = self.$footer.find( 'a.gform_save_link' );

			if ( self.activationType == 'last_page' && ! self.isLastPageReached() ) {
				return;
			}

			var $steps = self.$formElem.find( '.gf_step' );

			$steps.each( function( index ) {
				// As of GF 2.5 we cannot rely on the text displayed to figure out the step number
				// as they can change dynamically with conditional logic. Use index in DOM instead.
				var stepNumber = index + 1;

				if ( self.activationType == 'progression' && stepNumber > self.getPageProgression() ) {
					return;
				}

				if ( stepNumber != self.getCurrentPage() ) {
					$( this ).html( self.getPageLinkMarkup( stepNumber, $( this ).html() ) ).addClass( 'gpmpn-step-linked' );
				} else {
					$( this ).addClass( 'gpmpn-step-current' );
				}

			} );

			if ( self.activationType == 'last_page' && ! self.isLastPage() && self.isLastPageReached() ) {
				self.addBackToLastPageButton();
			} else if ( self.activationType == 'progression' && self.getCurrentPage() < self.getPageProgression() ) {
				self.addBackToLastPageButton( self.getPageProgression() );
			} else if ( self.activationType == 'first_page' && ! self.isLastPage() && self.wasFinalSubmissionAttempted() ) {
				self.addNextPageWithErrorsButton();
			}

			var pageLinksSelector = 'a.gpmpn-page-link, a.gwmpn-page-link, .gpmpn-page-link a';

			$( document ).on( 'click', pageLinksSelector, function( event ) {
				event.preventDefault();

				var hrefArray = $( this ).attr( 'href' ).split( '#' );

				if ( hrefArray.length >= 2 ) {

					var $parentForm = $( this ).parents( 'form' ),
						$formElem   = $parentForm.length > 0 ? $parentForm : $( '.gform_wrapper form' ),
						// Get form element for WC GF Add-on.
						$formElem  = $formElem.length > 0 ? $formElem : $( '.gform_wrapper' ).parent( 'form' ),
						formId     = $formElem.attr( 'id' ).split( '_' )[1],
						pageNumber = hrefArray.pop();

					GPMultiPageNavigation.postToPage( pageNumber, formId, true );

				}

			} );

			self.$formElem.data( 'GPMultiPageNavigation', self );

			window[ 'gpmpn_' + self.formId ] = self;

		};

		self.getPageLinkMarkup = function( stepNumber, content ) {
			return '<a href="#' + stepNumber + '" class="gwmpn-page-link gwmpn-default gpmpn-page-link gpmpn-default">' + content + '</a>';
		};

		self.addBackToLastPageButton = function( page ) {

			var page    = typeof page == 'undefined' ? self.lastPage : page,
				$button = '<input type="button" onclick="GPMultiPageNavigation.postToPage( ' + page + ', ' + self.formId + ' );" value="' + self.labels.backToLastPage + '" class="button gform_button gform_last_page_button">';

			self.insertButton( $button );

		};

		self.addNextPageWithErrorsButton = function() {

			var page     = 0,
				label    = self.getErrorPagesCount() > 1 ? self.labels.nextPageWithErrors : self.labels.submit,
				cssClass = self.getErrorPagesCount() > 1 ? 'gform_next_page_errors_button' : 'gform_resubmit_button',
				$button  = '<input type="button" onclick="GPMultiPageNavigation.postToPage( ' + page + ', ' + self.formId + ' );" value="' + label + '" class="button gform_button ' + cssClass + '">';

			if ( self.getErrorPagesCount() <= 1 && ! self.enableSubmissionFromLastPageWithErrors ) {
				self.addBackToLastPageButton();
			} else {
				self.insertButton( $button );
			}

		};

		self.insertButton = function( $button ) {
			if ( self.$saveAndContinueButton.length > 0 ) {
				self.$saveAndContinueButton.before( $button );
			} else {
				self.$footer.append( $button );
			}
		};

		self.getCurrentPage = function() {

			if ( ! self.currentPage ) {
				self.currentPage = self.$formElem.find( 'input#gform_source_page_number_' + self.formId ).val();
			}

			return self.currentPage;
		};

		self.getPageProgression = function() {
			return parseInt( $( 'input#gw_page_progression' ).val() );
		};

		self.getErrorPagesCount = function() {

			if ( ! self.errorPagesCount ) {
				self.errorPagesCount = self.$formElem.find( 'input#gw_error_pages_count' ).val();
			}

			return self.errorPagesCount;
		};

		self.isLastPage = function() {
			return self.getCurrentPage() >= self.lastPage;
		};

		self.isLastPageReached = function() {
			return self.isLastPage() || self.$formElem.find( 'input#gw_last_page_reached' ).val() == true;
		};

		self.wasFinalSubmissionAttempted = function() {
			return self.$formElem.find( 'input#gw_final_submission_attempted' ).val() == true
		};

		GPMultiPageNavigation.postToPage = function( page, formId, bypassValidation ) {

			var $form            = $( 'form#gform_' + formId ),
				$targetPageInput = $form.find( 'input#gform_target_page_number_' + formId );

			$targetPageInput.val( page );

			if ( bypassValidation ) {
				var $bypassValidationInput = $( '<input type="hidden" name="gw_bypass_validation" id="gw_bypass_validation" value="1" />' );
				$form.append( $bypassValidationInput );
			}

			/**
			 * If submit buttons are hidden via conditional logic (next/prev/submit), form will not be able to submit; this code finds
			 * all hidden submit inputs and hides them in a way that will still enable submission.
			 */
			$form.find( '.gform_page_footer:visible' ).find( 'input[type="submit"], input[type="button"]' ).not( ':visible' ).css( { display: 'block', visibility: 'hidden', position: 'absolute' } );

			/**
			 * If attempting to submit the form (page = 0, happens w/ "Next Page with Errors" button), move the Submit
			 * button to the current page so Gravity Forms will not abort the submission.
			 */
			if ( parseInt( page ) === 0 ) {
				$( '#gform_submit_button_' + formId ).appendTo( '.gform_page_footer:visible' ).css( { display: 'block', visibility: 'hidden', position: 'absolute' } );
				/**
				 * GF adds spinners to all Submit and Next buttons when the form is submitted as only one of these button
				 * types is visible for each page. GPMPN moves the submit button to the current page when submitting the
				 * form early (i.e. Next Page with Errors button). This results in multiple spinners showing as the
				 * Submit and Next buttons are on the same page. To resolve this, we set our custom buttons as the spinner
				 * target so only a single spinner is displayed.
				 */
				gform.addFilter( 'gform_spinner_target_elem', function( $target ) {
					// GF doesn't provide a way to check if a function is already bound to a filter so let's remove it
					// as part of the function and it will be rebound when it is applicable again.
					gform.removeFilter( 'gform_spinner_target_elem', 10, 'gpmpn_set_spinner_target' );
					var $nextPage = $( '.gform_next_page_errors_button:visible, .gform_resubmit_button:visible' );
					return $nextPage.length ? $nextPage : $target;
				}, 10, 'gpmpn_set_spinner_target' );
			}

			$form.submit();

		};

		self.init();

	}

})( jQuery );

/**
 * Take over Gravity Forms gformInitSpinner function which allows us to append the spinner after other custom buttons
 */
window.gformOrigInitSpinner = window.gformInitSpinner;
window.gformInitSpinner     = function( formId, spinnerUrl ) {

	if ( typeof spinnerUrl == 'undefined' || ! spinnerUrl ) {
		spinnerUrl = gform.applyFilters( 'gform_spinner_url', gf_global.spinnerUrl, formId );
	}

	var $form = jQuery( '#gform_' + formId );

	$form.submit( function() {
		if ( jQuery( '#gform_ajax_spinner_' + formId ).length == 0 ) {
			$form.find( '.gform_page_footer' ).append( '<img id="gform_ajax_spinner_' + formId + '"  class="gform_ajax_spinner" src="' + spinnerUrl + '" alt="" />' );
		}
	} );

};
