var GFPageConditionalLogic = function (args) {
    var self = this,
        $ = jQuery;

    /**
     * Initialize Feed Ordering
     */
    self.init = function () {

        // Assign options to instance.
        self.options = args;

        self.paginationType = self.options.pagination.type;

        self.triggerInputIds = self.getTriggerInputIds(self.options.pages);

        self.formWrapper = '#gform_wrapper_' + self.options.formId;

        if (self.paginationType === 'steps') {
            self.originalCurrentPage = parseInt($(self.formWrapper + ' .gf_step_active .gf_step_number').text(), 10);
        } else if (self.paginationType === 'percentage') {
            self.originalCurrentPage = parseInt($(self.formWrapper + ' .gf_step_current_page').text(), 10);
            self.originalProgress = parseInt($(self.formWrapper + ' .gf_progressbar_percentage span').text(), 10);
        }

        self.evaluatePages();

        self.bindEvents();

    };

    self.bindEvents = function () {

        gform.addAction('gform_input_change', function (elem, formId, inputId) {

            var fieldId = parseInt(inputId, 10) + '';
            var isTriggeredInput = $.inArray(inputId, self.triggerInputIds) !== -1 || $.inArray(fieldId, self.triggerInputIds) !== -1;

            if (self.options.formId == formId && isTriggeredInput) {
                self.evaluatePages();
            }
        });

    };

    self.evaluatePages = function () {

        var page, stepNumber, isMatch, isVisible, progress, visibleStepNumber = 1, currentPage = self.originalCurrentPage;

        for (var i = 0; i < self.options.pages.length; i++) {

            page = self.options.pages[i];
            stepNumber = i + 2; // plus 2 because the first page field is actually Step 2.
            isMatch = self.evaluatePage(page, self.options.formId);
            isVisible = self.isPageVisible(page);

            if (!isMatch && isVisible !== false) {
                self.hidePage(page, stepNumber);
            } else if (isMatch && !isVisible) {
                self.showPage(page, stepNumber);
            }

            // check if the page is visible after evaluation.
            isVisible = self.isPageVisible(page);
            if (isVisible) {
                visibleStepNumber++;

                if (self.paginationType === 'steps') {
                    $('#gf_step_' + self.options.formId + '_' + stepNumber).find('.gf_step_number').html(visibleStepNumber);
                } else if (self.paginationType === 'percentage' && self.originalCurrentPage == stepNumber) {
                    currentPage = visibleStepNumber;
                    $(self.formWrapper + ' .gf_step_current_page').html(currentPage);
                }

                // Reset the button.
                self.resetButton( page );
            }

        }

        if (self.paginationType === 'percentage') {
            currentPage = self.options.pagination.display_progressbar_on_confirmation === true ? ( currentPage - 1 ) : currentPage;
        }

        progress = Math.floor( currentPage / visibleStepNumber * 100 );

        if (self.paginationType === 'percentage') {
            var progressPercent = progress + '%';

            $(self.formWrapper + ' .gf_step_page_count').html(visibleStepNumber);
            $(self.formWrapper + ' .gf_progressbar_percentage span').html(progressPercent);
            $(self.formWrapper + ' .gf_progressbar_percentage').removeClass('percentbar_' + self.originalProgress).addClass('percentbar_' + progress).css('width', progressPercent);
        }

        if ( progress === 100 ) {
            // If the progress is 100% after evaluation, treat the current page as the last one.
            self.updateNextButton( currentPage - 1 );
        } else {
            // Else, just update the button on the last page.
            self.updateNextButton();
        }

        /**
         * Fires after the conditional logic on the form has been evaluated.
         *
         * @since 2.5
         *
         * @param array $pages     A collection of page field objects.
         * @param int   $formId    The form id.
         */
        gform.doAction('gform_frontend_pages_evaluated', self.options.pages, self.options.formId, self);
        gform.doAction('gform_frontend_pages_evaluated_{0}'.format(self.options.formId), self.options.pages, self.options.formId, self);

    };

    self.evaluatePage = function (page, formId) {

        // Pages with no configured conditional logic always a match.
        if (!page.conditionalLogic) {
            return true;
        }

        return gf_get_field_action(formId, page.conditionalLogic) === 'show';
    };

    self.getTriggerInputIds = function () {
        var inputIds = [];
        for (var i = 0; i < self.options.pages.length; i++) {

            var page = self.options.pages[i];

            if (!page.conditionalLogic) {
                continue;
            }

            for (var j = 0; j < page.conditionalLogic.rules.length; j++) {
                var rule = self.options.pages[i].conditionalLogic.rules[j];
                if ($.inArray(rule.fieldId, inputIds) === -1) {
                    inputIds.push(rule.fieldId);
                }
            }

        }
        return inputIds;
    };

    self.isPageVisible = function (page) {

        if (typeof page != 'object') {
            page = self.getPage(page);
            if (!page) {
                return false;
            }
        }

        return typeof page.isVisible != 'undefined' ? page.isVisible : null;
    };

    self.getPage = function (fieldId) {
        for (var i = 0; i < self.options.pages.length; i++) {
            var page = self.options.pages[i];
            if (page.fieldId == fieldId) {
                return page;
            }
        }
        return false;
    };

    self.showPage = function (page, stepNumber) {

        var isVisible = self.isPageVisible(page);

        if (isVisible === true) {
            return;
        }

        page.isVisible = true;
        $('#gf_step_' + self.options.formId + '_' + stepNumber).removeClass('gf_step_hidden');

        /**
         * Fires after the conditional logic on the form has been evaluated and the page has been found to be visible.
         *
         * @since 2.5
         *
         * @param array $pages  A collection of page field objects.
         * @param int   $formId The form id.
         */
        gform.doAction('gform_frontend_page_visible', page, self.options.formId);
        gform.doAction('gform_frontend_page_visible_{0}'.format(self.options.formId), page, self.options.formId);

    };

    self.hidePage = function (page, stepNumber) {

        var isVisible = self.isPageVisible(page);

        if (isVisible === false) {
            return;
        }

        page.isVisible = false;
        $('#gf_step_' + self.options.formId + '_' + stepNumber).addClass('gf_step_hidden');

        /**
         * Fires after the conditional logic on the form has been evaluated and the page has become hidden.
         *
         * @since 2.5
         *
         * @param array $pages  A collection of page field objects.
         * @param int   $formId The form id.
         */
        gform.doAction('gform_frontend_page_hidden', page, self.options.formId);
        gform.doAction('gform_frontend_page_hidden_{0}'.format(self.options.formId), page, self.options.formId);

    };

    self.updateNextButton = function ( lastPageIndex ) {

        var targetPageNumber = parseInt($('#gform_target_page_number_' + self.options.formId).val(), 10),
            lastPageNumber = self.options.pages.length + 1;

        if (targetPageNumber === lastPageNumber || lastPageIndex !== undefined) {
            if ( lastPageIndex === undefined ) {
                lastPageIndex = self.options.pages.length - 1;
            }

            var lastPageField = self.options.pages[ lastPageIndex ],
                lastNextButton = $('#gform_next_button_' + self.options.formId + '_' + lastPageField.fieldId),
                isLastPageVisible = self.isPageVisible(lastPageField),
                formButton = $('#gform_submit_button_' + self.options.formId);

            if (!isLastPageVisible) {
                if (formButton.attr('type') === 'image') {
                    // Cache last next button image alt.
                    if (lastNextButton.attr('type') === 'image') {
                        lastNextButton.data('alt', lastNextButton.attr('alt'));
                    }
                    lastNextButton.attr('type', 'image').attr('src', formButton.attr('src')).attr('alt', formButton.attr('alt')).addClass('gform_image_button').removeClass('button');
                } else {
                    lastNextButton.attr('type', 'button').val(formButton.val()).addClass('button').removeClass('gform_image_button');
                }

                // Set a mark on the page, so later on we can reset the button when evaluating pages.
                self.options.pages[ lastPageIndex ].isUpdated = true;
            } else {
                self.resetButton( lastPageField );
            }
        }

    };

    self.resetButton = function ( page ) {
        // No need to reset if the button hasn't been updated.
        if ( ! page.hasOwnProperty( 'isUpdated' ) ) {
            return;
        }

        delete page.isUpdated;

        var nextButton = $('#gform_next_button_' + self.options.formId + '_' + page.fieldId);
        if (page.nextButton.type === 'image') {
            nextButton.attr('type', 'image').attr('src', page.nextButton.imageUrl).attr('alt', nextButton.data('alt')).addClass('gform_image_button').removeClass('button');
        } else {
            nextButton.attr('type', 'button').val(page.nextButton.text).addClass('button').removeClass('gform_image_button');
        }
    }

    this.init();
};
