/*
 * Table of Contents jQuery Plugin - jquery.toc
 *
 * Copyright 2013-2016 Nikhil Dabas
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except
 * in compliance with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under the License
 * is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express
 * or implied.  See the License for the specific language governing permissions and limitations
 * under the License. 
 */

( function( $ ) {

    // Builds a list with the table of contents in the current selector.
    // options:
    //   content: where to look for headings
    //   headings: string with a comma-separated list of selectors to be used as headings, ordered
    //   by their relative hierarchy level

    OffSet = {

        _setoffset: function( lists ){

                if ( window.matchMedia("(max-width: 767px)").matches ) {
                    
                    if( undefined == lists.data( 'scroll-offset-mobile' ) ){
                        scroll_offset = lists.data( 'scroll-offset' );
                        return scroll_offset;
                    } else {
                        scroll_offset = lists.data( 'scroll-offset-mobile' );
                        return scroll_offset;
                    }

                } else if ( window.matchMedia("(max-width: 976px)").matches ) {

                        if( undefined == lists.data( 'scroll-offset-tablet' ) ){
                            scroll_offset = lists.data( 'scroll-offset' );
                            return scroll_offset;
                        } else {
                            scroll_offset = lists.data( 'scroll-offset-tablet' );
                            return scroll_offset;
                        }
                    } else {
                        scroll_offset = lists.data( 'scroll-offset' );
                        return scroll_offset;
            }
        },

        __scroll_to_top_offset: function( lists, scroll_to_top_offset ) {
            if ( window.matchMedia("(max-width: 767px)").matches ) {
                    
                if( undefined == lists.data( 'scroll-to-top-offset-mobile' ) ){
                    return scroll_to_top_offset;
                } else {
                    scroll_to_top_offset = lists.data( 'scroll-to-top-offset-mobile' );
                    return scroll_to_top_offset;
                }

            } else if ( window.matchMedia("(max-width: 976px)").matches  ) {

                    if( undefined == lists.data( 'scroll-to-top-offset-tablet' ) ){
                        return scroll_to_top_offset;
                    } else {
                        scroll_to_top_offset = lists.data( 'scroll-to-top-offset-tablet' );
                        return scroll_to_top_offset;
                    }
            } else {
                return scroll_to_top_offset;
            } 
        }
    }

    var toc = function ( options ) {
        return this.each( function () {
            var root = $(this),
                data = root.data(),
                thisOptions,
                stack = [root], // The upside-down stack keeps track of list elements
                listTag = this.tagName,
                currentLevel = 0,
                headingSelectors;

            // Defaults: plugin parameters override data attributes, which override our defaults
            thisOptions = $.extend(
                {content: "body", headings: "h1,h2,h3,h4,h5,h6"},
                {content: data.toc || undefined, headings: data.tocHeadings || undefined},
                options
            );
            headingSelectors = thisOptions.headings.split(",");

            if( ! $( thisOptions.content ).find( thisOptions.headings ).length ) {
                $widget_scope = $( 'body' ).find( '.elementor-element-' + options.scope );
                $widget_scope.find( '.uael-toc-main-wrapper' ).addClass( 'uael-toc-content-empty' );
            }

            $( thisOptions.content ).find( thisOptions.headings ).addClass( "uael-toc-text" );

            var exclude_parent = $( 'body' ).find( '.uae-toc-hide-heading' );
            exclude_parent.each( function( i ) {
                var $this = $( this );
                if( $this.hasClass( 'uael-toc-text' ) ) {
                    $this.addClass( 'uael-toc-hidden-item' );
                }
                $this.find( '.uael-toc-text' ).addClass( 'uael-toc-hidden-item' );
            });

            // Set up some automatic IDs if we do not already have them
            $(thisOptions.content).find(thisOptions.headings).attr("id", function (index, attr) {
                // In HTML5, the id attribute must be at least one character long and must not
                // contain any space characters.
                //
                // We just use the HTML5 spec now because all browsers work fine with it.
                // https://mathiasbynens.be/notes/html5id-class
                if ( undefined !== attr ) {
                    attr = attr.replace( /[&\/\\#,+()$!~%.'":*?<>{}]/g, "" );
                }

                var generateUniqueId = function (text) {
                    // Generate a valid ID. Spaces are replaced with underscores. We also check if
                    // the ID already exists in the document. If so, we append "_1", "_2", etc.
                    // until we find an unused ID.

                    if (text.length === 0) {
                        text = "?";
                    }

                    var baseId = text.replace(/\s+/g, "_"), suffix = "", count = 1;
                    baseId = baseId.replace(/[&\/\\#,+()$!~%.'":*?<>{}]/g, "");

                    while (document.getElementById(baseId + suffix) !== null) {
                        suffix = "_" + count++;
                    }

                    return baseId + suffix;
                };

                return attr || generateUniqueId($(this).text());
            }).each(function () {
                // What level is the current heading?
                var elem = $(this), level = $.map(headingSelectors, function (selector, index) {
                    return elem.is(selector) ? index : undefined;
                })[0];

                if( elem.hasClass( 'uael-toc-hidden-item' ) ) {
                    return;
                }

                if (level > currentLevel) {
                    // If the heading is at a deeper level than where we are, start a new nested
                    // list, but only if we already have some list items in the parent. If we do
                    // not, that means that we're skipping levels, so we can just add new list items
                    // at the current level.
                    // In the upside-down stack, unshift = push, and stack[0] = the top.
                    var parentItem = stack[0].children("li:last")[0];
                    if (parentItem) {
                        stack.unshift($("<" + listTag + "/>").appendTo(parentItem));
                    }
                } else {
                    // Truncate the stack to the current level by chopping off the 'top' of the
                    // stack. We also need to preserve at least one element in the stack - that is
                    // the containing element.
                    stack.splice(0, Math.min(currentLevel - level, Math.max(stack.length - 1, 0)));
                }

                // Add the list item
                $("<li/>").appendTo(stack[0]).append(
                    $("<a/>").text(elem.text()).attr("href", "#" + elem.attr("id"))
                );

                currentLevel = level;
            });
        });
    }, old = $.fn.toc;

    $.fn.toc = toc;

    $.fn.toc.noConflict = function () {
        $.fn.toc = old;
        return this;
    };

    // Data API
    $( function () {
        toc.call($("[data-toc]"));
    });

    var scroll_element = null;

    UAELTableOfContents = {

        /**
         * Hide scroll to top button on scroll
         *
         */
        _showHideScroll: function() {
            scroll_element = $( ".uael-scroll-top-icon" );
            if ( null != scroll_element ) {
                if ( $( window ).scrollTop() > 300 ) {
                    scroll_element.addClass( "uael-toc__show-scroll" );
                } else {
                    scroll_element.removeClass( "uael-toc__show-scroll" );
                }
            }
        },

        /**
         * Show Hide toggle button
         *
         */
         _toggleButton: function( separator, wrapper, toggle_content ) {
            separator.toggle( 100 );
            if ( wrapper.hasClass( 'content-show' ) ) {
                toggle_content.slideUp( 350 );
                wrapper.removeClass( 'content-show' );
            } else {
                toggle_content.slideDown( 350 );
                wrapper.addClass( 'content-show' );
            }

            if( wrapper.hasClass( 'uael-toc-auto-collapse' ) ) {
                wrapper.removeClass( 'uael-toc-auto-collapse' );
            } else {
                wrapper.toggleClass( 'uael-toc-hidden' );
            }
        }

    }; 


    WidgetUAELTableOfContents = function( $scope, $ ) { 

        var body_wrap =  $( 'body' );
        var $body = body_wrap.find( '.entry-content' );
        var node_id = $scope.data( 'id' );
        var toggle_button = $scope.find( '.uael-toc-switch' );
        var toggle_content = $scope.find( '.uael-toc-toggle-content' );
        var is_collapsible = toggle_button.data( 'is-collapsible' );
        var wrapper = $scope.find( '.uael-toc-main-wrapper' );
        var selected_headings = wrapper.data( 'headings' );
        var lists = $scope.find( '.uael-toc-list' );
        var scroll_delay = lists.data( 'scroll' );
        var separator = $scope.find( '.uael-separator-parent' );
        var scroll_offset = OffSet._setoffset( lists );
        var lists_scroll_to_top_offset = lists.data( 'scroll-to-top-offset' );
        var scroll_to_top_offset = OffSet.__scroll_to_top_offset( lists, lists_scroll_to_top_offset );
        
        if( $body.length === 0 ) {
            $body = body_wrap.find( '.page-content' );
        }

        if( $body.length === 0 ) {
            $body = body_wrap.find( 'div[data-elementor-type]' );
        }

        window.onresize = function( ) {
            scroll_offset = OffSet._setoffset( lists );
            lists_scroll_to_top_offset = lists.data( 'scroll-to-top-offset' );
            scroll_to_top_offset = OffSet.__scroll_to_top_offset( lists, lists_scroll_to_top_offset );
        }

        // Toggle content on Show/Hide button.
        toggle_button.on( 'click', function( e ) {

            if( 'yes' === is_collapsible ) {
                UAELTableOfContents._toggleButton( separator, wrapper, toggle_content );
            }

        });

        // Execute TOC function.
        $scope.find( '.uael-toc-list' ).toc( { content: $body, headings: selected_headings, scope: node_id } );

        wrapper.find( '.uael-toc-list a' ).on( 'click', function () {
            
            if( '' == scroll_offset || 'undefined' == typeof scroll_offset ) {
                $( 'html, body' ).animate( {
                    scrollTop: $( $.attr( this, 'href' ) ).offset().top-30
                }, scroll_delay );        
            } else {
                $( 'html, body' ).animate( {
                    scrollTop: $( $.attr( this, 'href' ) ).offset().top-(scroll_offset)
                }, scroll_delay );
            }

            // Add class to active heading.
            $scope.find( '.uael-toc-list a' ).not( this ).removeClass( 'uael-toc-active-heading' );
            $( this ).addClass( 'uael-toc-active-heading' );

            return false;
        });

        $scope.find( '.uael-toc-wrapper li' ).each( function( i ) {
            $( this ).attr( "id", "toc-li-" + i );
        });

        $scope.find( '.uael-scroll-top-icon' ).on( 'click', function( e ) {
            if( '' == scroll_to_top_offset || 'undefined' == typeof scroll_to_top_offset ) {
                $( "html, body" ).animate( {
                    scrollTop: wrapper.offset().top
                }, scroll_delay );
            } else {
                $( 'html, body' ).animate( {
                    scrollTop: scroll_to_top_offset
                }, scroll_delay );
            }
            
        });

        $( document ).on( "scroll", UAELTableOfContents._showHideScroll  );
 
    }

    $( window ).on( 'elementor/frontend/init', function () {

        elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-table-of-contents.default', WidgetUAELTableOfContents );

    });

} )( jQuery );
