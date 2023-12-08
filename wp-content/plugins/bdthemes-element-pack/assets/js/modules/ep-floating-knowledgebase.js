/**
 * Start Content Switcher widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetFloatingKnowledgebase = function ($scope, $) {

        var $floatingKnowledgebase = $scope.find('.bdt-floating-knowledgebase'),
            $settings = $floatingKnowledgebase.data('settings');

        if (!$floatingKnowledgebase.length) {
            return;
        }

        ;
        (function ($) {
            "use strict";
            /*
             *--------------------------------------------------------------------------
             * CONFIG OR SETTINGS - Customize the help center
             *--------------------------------------------------------------------------
             */
            var helpCenterConfig = {
                // primaryColor: "#007bff", // Floating button color
                linkColor: "#007bff", // Color of anchor tags or links
                showHelperText: true, // Helper text beside floating button(true|false)
                helperTextLabel: $settings.helperTextLabel || "Have any queries?<br /><strong>Check Help Center</strong>", // Helper text label
                showContactUsLink: true, // Hide or show contact us link(true|false)
                contactUsLabel: $settings.supportLinkText || "Still no luck? We can help!", // Text of contact us link
                contactUsLink: $settings.supportLink || "/contact-us", // Contact us link
                noResultsLabel: $settings.noSearchResultText || "Sorry, we don’t have any results. Updates are being added all the time.", // No results found text
                resetOnPopupClose: false, // Reset popup back to original state on close(true|false)
                btnZindex: 999, // Z-index property of floating button
                popupZindex: 998, // Z-index property of popup
                onPopupOpen: function () {}, // Callback function which runs on popup open
                onPopupClose: function () {} // Callback function which runs on popup close
            }

            var nodesStack = []; // Array to hande "nodes/html-content" back-forth pagination
            var titleStack = []; // Array to hande "title" back-forth pagination
            var jsonData; // JSON data

            /**
             * Get the JSON Data and Initialize plugin
             */


            $.fn.floatingHelpCenter = function () {
                jsonData = $settings.data_source;
                renderHelpCenterBtn(helpCenterConfig); // render popup button
                renderPopup(jsonData); // render popup with JSON data
            }
            /**
             * Renders the help center button using config
             * @param {Object} config - Contains config/preferences
             */
            function renderHelpCenterBtn(config) {
                var $btnWrap = $("<div>", {
                    class: "floating-help-center__btn"
                }).css('zIndex', helpCenterConfig.btnZindex);
                $btnWrap.click(togglePopup);
                var $helperText = $("<p>", {
                    class: "helper-txt"
                }).html(helpCenterConfig.helperTextLabel);
                var $btn = $("<button>", {
                    class: "btn"
                });
                $("#bdt-floating-help-center").append($btnWrap.append(function () {
                    if (helpCenterConfig.showHelperText) {
                        return $helperText;
                    }
                }, $btn));
            }

            /**
             * Shows or hide the popup on button click
             */
            function togglePopup() {
                var $popup = $("#bdt-floating-help-center .floating-help-center__popup");
                var $popBtn = $("#bdt-floating-help-center .floating-help-center__btn");
                var popupActiveClass = "floating-help-center__popup--active";
                if ($popup.hasClass(popupActiveClass)) {
                    $popup.removeClass(popupActiveClass);
                    $popBtn.removeClass(popupActiveClass);
                    helpCenterConfig.onPopupClose.call(this);
                } else {
                    if (helpCenterConfig.resetOnPopupClose) {
                        resetPopupContent();
                    }
                    $popup.addClass(popupActiveClass);
                    $popBtn.addClass(popupActiveClass);
                    helpCenterConfig.onPopupOpen.call(this);
                }
            }

            /**
             * Renders the popup with populated data
             * @param {Object} data - contains json data
             */
            function renderPopup(data) {



                var $outerWrap = $("<div>", {
                    id: "floatingHelpCenterPopup",
                    class: "floating-help-center__popup"
                }).css('zIndex', helpCenterConfig.popupZindex);;

                var $searchOuter = $("<div>", {
                    class: "searchbox"
                });
                var $searchIcon = $("<div>", {
                    class: "searchbox__search-icon"
                }).html(searchSVG);
                var $input = $("<input>", {
                    class: "searchbox__input",
                    type: "text",
                    placeholder: "Search..."
                });
                var $crossIcon = $("<div>", {
                    class: "searchbox__cross-icon"
                }).html(crossSVG).css({
                    display: "none"
                });
                var $resizerIcon = $("<div>", {
                    class: "bdt-resizer-icon"
                }).html(resizerSVG);

                $crossIcon.click(resetPopupContent);
                $resizerIcon.click(() => {
                    $outerWrap.toggleClass('bdt-content-expand');
                });
                $input.on("input", searchInputHandler);
                // $searchOuter.append($searchIcon, $input, $crossIcon);
                $searchOuter.append($searchIcon, $input, $resizerIcon);
                var $helpList = $("<ul>", {
                    id: "listItemsContainer",
                    class: "help-list"
                });
                var $externalLinkWrap = $("<div>", {
                    id: "externalLinkWrap",
                    class: "external"
                });
                var $externalLink = $("<a>", {
                    id: "externalLinkWrap",
                    class: "external__link",
                    target: "_blank",
                    href: helpCenterConfig.contactUsLink
                }).text(helpCenterConfig.contactUsLabel);
                var $externalArrow = $("<span>", {
                    class: "external__arrow"
                }).html(externalArrowSVG);
                $externalLinkWrap.append($externalLink.append($externalArrow));

                var $headerWrap = $("<div>", {
                    id: "headerWrap",
                    class: "bdt-header"
                });

                if ($settings.logo.url) {
                    if ($settings.title) {
                        $settings.logo.alt = $settings.title;
                    }

                    $headerWrap.append('<div class="bdt-header-logo"><img src="' + $settings.logo.url + '" alt="' + $settings.logo.alt + '"></div>');
                }

                if ($settings.title) {
                    $headerWrap.append('<div class="bdt-header-title">' + $settings.title + '</div>');
                }

                if ($settings.description) {
                    $headerWrap.append('<div class="bdt-header-description">' + $settings.description + '</div>');
                }

                $("#bdt-floating-help-center").append($outerWrap.append(function () {
                    if (helpCenterConfig.showContactUsLink) {
                        return [$headerWrap, $searchOuter, $helpList, $externalLinkWrap];
                    }
                    return [$headerWrap, $searchOuter, $helpList];
                }));
                setPopupContent(data);
            }

            /**
             * Search input listener and sends input query to
             * findObject() function
             */
            function searchInputHandler() {
                var $crossIcon = $("#bdt-floating-help-center .searchbox__cross-icon");
                var query = $(this).val();
                if (query && query !== "") {
                    $crossIcon.css({
                        display: "block"
                    });
                    var resultsArr = findObject(jsonData, "title", query, true);
                    setPopupContent(resultsArr, "search");
                } else {
                    $crossIcon.css({
                        display: "none"
                    });
                    resetPopupContent();
                }
            }

            /**
             * Enables or Disables Search Input
             */
            function searchInputReadonlyToggle() {
                var $searchInput = $("#bdt-floating-help-center .searchbox__input");
                $searchInput.attr('readonly', nodesStack.length > 1);
            }

            /**
             * Displays the list of questions/title
             * @param {Array} data - Data array
             */
            function renderPopupContentList(data) {
                $.each(data, function (index, listObj) {
                    var $listWrap = $("<li>", {
                        class: "help-list__item"
                    });
                    $listWrap.click(function () {
                        listItemClickHandler(this);
                    });
                    var $listArrow = $("<span>", {
                        class: "help-list__item-arrow"
                    }).html(listArrowSVG);
                    var $listText = $("<span>", {
                        class: "help-list__item-txt"
                    }).html(listObj["title"]);
                    $("#bdt-floating-help-center #listItemsContainer").append($listWrap.append($listText, $listArrow));
                });
            }

            /**
             * Hides or shows the back button on search input
             */
            function toggleBackButton() {
                $("#bdt-floating-help-center .searchbox__search-icon").unbind();
                nodesStack.length > 1 ?
                    $(".searchbox__search-icon").html(backSVG).click(backBtnHandler) :
                    $("#bdt-floating-help-center .searchbox__search-icon").html(searchSVG);

                if (nodesStack.length > 1) {
                    $('#floatingHelpCenterPopup').addClass('bdt-content-expand');
                } else {
                    $('#floatingHelpCenterPopup').removeClass('bdt-content-expand');
                }
            }

            /**
             * Resets the previous html content before 
             * populating with new content
             */
            function resetPreviousState() {
                $("#bdt-floating-help-center #htmlContent, #bdt-floating-help-center #noResultTxt").remove();
                $("#bdt-floating-help-center #listItemsContainer").html("");
                $('#floatingHelpCenterPopup').removeClass('bdt-content-open');
            }

            /**
             * Performs set of operations on back button click
             */
            function backBtnHandler() {
                nodesStack.pop();
                titleStack.pop();
                var lastNode = nodesStack.pop();
                var lastTitle = titleStack.pop();
                setInputTitle(lastTitle);
                setPopupContent(lastNode);
                searchInputReadonlyToggle();
            }

            /**
             * Checks the data type and calls the rendering functions accordingly
             * @param {Object|Array|String} data - Data to be rendered
             * @param {string} event - Event type(search)  
             */
            function setPopupContent(data, event) {
                if (data.length === 0) {
                    renderNoResults();
                    return;
                }
                if (data.length > 1) {
                    beforeSetPopupContent(data, event);
                    renderPopupContentList(data);
                    return;
                }
                if (event === "search") {
                    beforeSetPopupContent(data, event);
                    renderPopupContentList(data);
                    return;
                }
                var destructuredObj = data.pop();
                if (typeof destructuredObj === "string") {
                    beforeSetPopupContent([destructuredObj], event);
                    renderHTML("", destructuredObj);
                    return;
                }
                if (destructuredObj.hasOwnProperty("nodes")) {
                    beforeSetPopupContent(destructuredObj["nodes"], event);
                    renderPopupContentList(destructuredObj["nodes"]);
                    return;
                }
                beforeSetPopupContent([destructuredObj["html"]], event);
                renderHTML(destructuredObj["title"], destructuredObj["html"]);
            }

            /**
             * Performs set of operation before popup content is set
             * @param {Object|Array|string} data - Data to be rendered 
             * @param {string} event - Type of event
             */
            function beforeSetPopupContent(data, event) {
                if (event === undefined || event !== "search") {
                    nodesStack.push(data);
                }
                toggleBackButton();
                resetPreviousState();
            }

            /**
             * Links other objects title in anchor tags
             * in attribute "data-title"
             */
            function anchorDataTitleHandler() {
                $("#bdt-floating-help-center #htmlContent a").click(function () {
                    var dataTitle = $(this).attr("data-title");
                    if (dataTitle && dataTitle !== "") {
                        var resultsArr = findObject(jsonData, "title", dataTitle);
                        setInputTitle(dataTitle);
                        setPopupContent(resultsArr);
                        return false;
                    }
                });
            }

            /**
             * Resets the popup content to initial state
             */
            function resetPopupContent() {
                nodesStack = [];
                titleStack = [];
                setInputTitle("");
                $("#bdt-floating-help-center .searchbox__cross-icon").css({
                    display: "none"
                });
                setPopupContent(jsonData);
                $("#bdt-floating-help-center #htmlContent, #bdt-floating-help-center #noResultTxt").remove();
            }

            /**
             * Set's title in searchbar
             * @param {string} title - Title
             */
            function setInputTitle(title) {
                if (title && title !== "") {
                    // $("#bdt-floating-help-center .searchbox__input").val(title);
                    $("#bdt-floating-help-center .searchbox__input").val(" ");
                    titleStack.push(title);
                } else {
                    $("#bdt-floating-help-center .searchbox__input").val("");
                }
            }

            /**
             * Click handler for lists
             * @param {HTMLElement} listElement - list element
             */
            function listItemClickHandler(listElement) {
                var listTitle = $(listElement).find(".help-list__item-txt").text();
                var matchedObj = findObject(jsonData, "title", listTitle);
                setInputTitle(listTitle);
                setPopupContent(matchedObj);
                searchInputReadonlyToggle();
            }

            /**
             * Displays HTML help article
             * @param {string} title - Title of article
             * @param {string} htmlContent - Content of article
             */
            function renderHTML(title, htmlContent) {
                $(".searchbox__cross-icon").css({
                    display: "none"
                });
                var $htmlContentElement = $("<div>", {
                    id: "htmlContent",
                    class: "html-content"
                }).html(htmlContent);
                var articleTitle = title || $("#bdt-floating-help-center .searchbox__input").val();
                $htmlContentElement.prepend($("<h5>", {
                    id: "contentTitle",
                    class: "html-content__title"
                }).html(articleTitle));
                $htmlContentElement.find("a").not('.callout-block a').css({
                    "color": helpCenterConfig.linkColor
                });
                $htmlContentElement.insertBefore("#bdt-floating-help-center #listItemsContainer");
                $('#floatingHelpCenterPopup').addClass('bdt-content-open');

                anchorDataTitleHandler();
            }

            /**
             * Shows no results message
             */
            function renderNoResults() {
                resetPreviousState();
                var $noResultsLabel = $("<p>", {
                    id: "noResultTxt",
                    class: "no-result"
                }).html(helpCenterConfig.noResultsLabel);
                if ($("#noResultTxt").length === 0) {
                    $noResultsLabel.insertAfter("#bdt-floating-help-center .searchbox");
                }
            }

            /**
             * @param {Object} obj - Object where search has to be performed 
             * @param {string} key - Attribute Key
             * @param {string} value - Value 
             * @param {boolean} performSearch - Search perform or finding value by key
             * @returns {Object} obj - Result filtered object
             */
            function findObject(obj, key, value, performSearch) {
                value = performSearch === true ? value.toUpperCase() : value;
                var results = [];

                function recursiveSearch(obj) {
                    if (!obj || _typeof(obj) !== "object") {
                        return;
                    }
                    if (performSearch) {
                        if (obj[key] && obj[key].toUpperCase().indexOf(value) > -1) {
                            results.push(obj);
                        }
                    } else {
                        if (obj[key] === value) {
                            results.push(obj);
                        }
                    }
                    Object.keys(obj).forEach(function (k) {
                        recursiveSearch(obj[k]);
                    });
                }
                recursiveSearch(obj);
                return results;
            }


            /**
             * Helper function for old browsers
             */
            function _typeof(obj) {
                "@babel/helpers - typeof";
                if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
                    _typeof = function _typeof(obj) {
                        return typeof obj;
                    };
                } else {
                    _typeof = function _typeof(obj) {
                        return obj &&
                            typeof Symbol === "function" &&
                            obj.constructor === Symbol &&
                            obj !== Symbol.prototype ? "symbol" : typeof obj;
                    };
                }
                return _typeof(obj);
            }

            /**
             * floating help center window object
             * and public functions
             */
            return window.floatingHelpCenter = {
                init: function () {
                    $.fn.floatingHelpCenter();
                },
                toggle: function () {
                    togglePopup();
                },
                isOpen: function () {
                    return $("#bdt-floating-help-centerPopup").hasClass("floating-help-center__popup--active");
                }
            };

        })(jQuery);
        // Search icon svg
        var searchSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M15.2 16.34a7.5 7.5 0 1 1 1.38-1.45l4.2 4.2a1 1 0 1 1-1.42 1.41l-4.16-4.16zm-4.7.16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"></path></svg>';

        // Cross icon svg
        var crossSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M13.06 12.15l5.02-5.03a.75.75 0 1 0-1.06-1.06L12 11.1 6.62 5.7a.75.75 0 1 0-1.06 1.06l5.38 5.38-5.23 5.23a.75.75 0 1 0 1.06 1.06L12 13.2l4.88 4.87a.75.75 0 1 0 1.06-1.06l-4.88-4.87z"></path></svg>';

        // Resizer icon svg
        var resizerSVG = '<svg class="bdt-expand" xmlns="http://www.w3.org/2000/svg" height="24" width="24" fill="currentColor"><path d="M2.675 21.325v-8.65h2.65v4.15l11.5-11.5h-4.15v-2.65h8.65v8.65h-2.65v-4.15l-11.5 11.5h4.15v2.65Z"></path></svg>';

        resizerSVG += '<svg class="bdt-close" xmlns="http://www.w3.org/2000/svg" height="24" width="24" fill="currentColor"><path d="m3.075 22.775-1.85-1.85L7.5 14.65H3.35V12H12v8.65H9.35V16.5ZM12 12V3.35h2.65V7.5l6.275-6.275 1.85 1.85L16.5 9.35h4.15V12Z"></path></svg>';

        // back icon svg
        var backSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M15.45 17.97L9.5 12.01a.25.25 0 0 1 0-.36l5.87-5.87a.75.75 0 0 0-1.06-1.06l-5.87 5.87c-.69.68-.69 1.8 0 2.48l5.96 5.96a.75.75 0 0 0 1.06-1.06z"></path></svg>';

        // list arrow svg
        var listArrowSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path fill="currentColor" d="M6.47 4.29l3.54 3.53c.1.1.1.26 0 .36L6.47 11.7a.75.75 0 1 0 1.06 1.06l3.54-3.53c.68-.69.68-1.8 0-2.48L7.53 3.23a.75.75 0 0 0-1.06 1.06z"></path></svg>';

        var externalArrowSVG = '<svg fill="#3a3f3f" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M11.268 5.824L5.232 11.86a.75.75 0 1 1-1.06-1.06L10.22 4.75H5.75a.75.75 0 0 1 0-1.5h6.268a.75.75 0 0 1 .75.75v6.243a.75.75 0 0 1-1.5 0v-4.42z" fill="currentColor"></path></svg>';

        floatingHelpCenter.init();

    }


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-floating-knowledgebase.default', widgetFloatingKnowledgebase);
    });


}(jQuery, window.elementorFrontend));

/**
 * End Content Switcher widget script
 */