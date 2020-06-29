/* global admin_i18n, yith_pos, ajaxurl */
( function ( $ ) {
    var block_params = {
            message        : null,
            overlayCSS     : {
                background: '#fff',
                opacity   : 0.7
            },
            ignoreIfBlocked: true
        },
        toIntVal     = function ( number ) {
            return parseInt( number, 10 );
        },
        i;


    var notices = {
        getWrapper: function () {
            return $( '.yith-pos-store-notices:visible' );
        },

        add: function ( _id, _text, _type ) {
            var wrapper = notices.getWrapper();
            if ( wrapper.length ) {
                var noticeID = "yith-pos-admin-notice__" + _id,
                    notice   = wrapper.find( '#' + noticeID );

                if ( !notice.length ) {
                    notice = $( "<div id='" + noticeID + "' class='yith-pos-admin-notice yith-pos-admin-notice--" + _type + "'>" + _text + "<span class='yith-pos-admin-notice__dismiss' /></div>" );
                    wrapper.append( notice );
                }

                notice.show();
                yith_pos.scrollTo( wrapper );
            }
        }
    };

    var wizard = {
        currentPage                : 1,
        postID                     : $( '#post_ID' ).val(),
        postAuthor                 : $( '#post_author' ).val(),
        postType                   : $( '#post_type' ).val(),
        userID                     : $( '#user_ID' ).val(),
        wpNonce                    : $( '#_wpnonce' ).val(),
        yithNonce                  : $( '#yit_metaboxes_nonce' ).val(),
        dom                        : {
            banner            : $( '.yith-plugin-fw-banner' ).first(),
            metabox           : $( '#yith-pos-store' ),
            tabAnchorsLi      : $( '#yith-pos-store .metaboxes-tabs li' ),
            tabAnchors        : $( '#yith-pos-store .metaboxes-tabs li a' ),
            tabPanels         : $( '#yith-pos-store .tabs-panel' ),
            tabContainer      : $( '#yith-pos-store .metaboxes-tab' ),
            currentPage       : $( '#_wizard_current_page' ),
            hasCurrentPageData: $( '.yith-pos-wizard__has-current-page-data' ),
            form              : $( 'form#post' ),
            prev              : $( '#yith-pos-wizard-pagination__prev' ),
            next              : $( '#yith-pos-wizard-pagination__next' ),
            save              : $( '#yith-pos-wizard-pagination__save' ),
            nav               : $( '#yith-pos-wizard-nav' ),
            navSteps          : $( '.yith-pos-wizard-nav__step' ),
            navBetweenSteps   : $( '.yith-pos-wizard-nav__between-steps' ),
            storeSummary      : false
        },
        init                       : function () {
            this._initVars();
            this._initEvents();

            wizard.setPage( wizard.currentPage );
        },
        _initVars                  : function () {
            this.currentPage = toIntVal( this.dom.currentPage.val() );
            if ( isNaN( this.currentPage ) ) {
                this.currentPage = 1;
            }
        },
        _initEvents                : function () {
            this.dom.prev.on( 'click', this.prev );
            this.dom.next.on( 'click', this.next );
            this.dom.save.on( 'click', this.publish );
            this.dom.navSteps.on( 'click', this.moveToPage );
            $( document ).on( 'click', '.yith-pos-store-wizard-summary__box', this.summaryMoveToPage );
        },
        _serializeObject           : function ( element ) {
            var o = {},
                a = element.serializeArray();
            $.each( a, function () {
                if ( o[ this.name ] ) {
                    if ( !o[ this.name ].push ) {
                        o[ this.name ] = [o[ this.name ]];
                    }
                    o[ this.name ].push( this.value || '' );
                } else {
                    o[ this.name ] = this.value || '';
                }
            } );
            return o;
        },
        setPage                    : function ( page ) {
            page         = toIntVal( page );
            var _maxPage = Math.max( page, wizard.dom.currentPage.val() );

            wizard.currentPage = page;
            wizard.dom.currentPage.val( _maxPage );
            wizard.dom.hasCurrentPageData.data( 'current-page', page ).attr( 'data-current-page', page );

            this.dom.prev.html( this.dom.prev.data( 'text' ).replace( '%s', page - 1 ) );
            this.dom.next.html( this.dom.next.data( 'text' ).replace( '%s', page + 1 ) );

            if ( typeof wizard.dom.navSteps[ page - 1 ] !== 'undefined' ) {
                wizard.dom.navSteps.removeClass( 'active done' );
                wizard.dom.navBetweenSteps.removeClass( 'active done' );
                for ( i = 0; i < page - 1; i++ ) {
                    $( wizard.dom.navSteps[ i ] ).addClass( 'done' );
                    if ( typeof wizard.dom.navSteps[ i - 1 ] !== 'undefined' ) {
                        $( wizard.dom.navBetweenSteps[ i - 1 ] ).addClass( 'done' );
                    }
                }
                $( wizard.dom.navSteps[ page - 1 ] ).addClass( 'active' );

                if ( typeof wizard.dom.navSteps[ page - 2 ] !== 'undefined' ) {
                    $( wizard.dom.navBetweenSteps[ page - 2 ] ).addClass( 'active' );
                }
            }

            wizard.dom.navSteps.removeClass( 'clickable' );
            for ( i = 0; i < _maxPage; i++ ) {
                if ( typeof wizard.dom.navSteps[ i ] !== 'undefined' ) {
                    $( wizard.dom.navSteps[ i ] ).addClass( 'clickable' );
                }
            }

            if ( typeof wizard.dom.tabAnchors[ page - 1 ] !== 'undefined' ) {
                $( wizard.dom.tabAnchors[ page - 1 ] ).trigger( 'click' );
            }

            if ( 3 === page ) {
                if ( !$( '#yith-pos-store-metabox-registers-list .yith-pos-store-register' ).length ) {
                    var newRegisterBtn = $( '#yith-pos-store-metabox-registers-list__new' );

                    if ( !newRegisterBtn.is( '.closed' ) ) {
                        newRegisterBtn.click();
                    }

                    newRegisterBtn.hide();
                    $( document ).on( 'yith_pos_register_created', function () {
                        newRegisterBtn.show();
                    } );
                }
            }

            if ( 4 === page ) {
                wizard.loadStoreSummary();
            } else {
                wizard.hideStoreSummary();
            }
        },
        next                       : function () {
            var nextPage = toIntVal( wizard.currentPage + 1 );
            if ( wizard.currentPage < 4 ) {
                wizard.dom.currentPage.val( Math.max( nextPage, wizard.dom.currentPage.val() ) );
                wizard.saveChanges( {
                                        complete: function () {
                                            wizard.setPage( nextPage );
                                            wizard.scrollTop();
                                        }
                                    } );
            }
        },
        prev                       : function () {
            if ( wizard.currentPage > 1 ) {
                wizard.setPage( wizard.currentPage - 1 );
                wizard.scrollTop();
            }
        },
        scrollTop                  : function () {
            var scrollTop = wizard.dom.banner.offset().top - 32 - 20;
            $( 'html, body' ).animate( { scrollTop: scrollTop }, 500 );
        },
        moveToPage                 : function ( e ) {
            var self = $( e.target ).closest( '.yith-pos-wizard-nav__step' ),
                page = self.data( 'step' );

            if ( self.is( '.clickable' ) ) {
                if ( wizard.currentPage < 4 && wizard.currentPage !== page ) {
                    wizard.saveChanges( {
                                            complete: function () {
                                                wizard.setPage( page );
                                            }
                                        } );
                } else {
                    wizard.setPage( page );
                }
            }
        },
        summaryMoveToPage          : function ( e ) {
            var self = $( e.target ).closest( '.yith-pos-store-wizard-summary__box' ),
                page = self.data( 'step' );
            wizard.setPage( page );
        },
        loadStoreSummary           : function () {
            if ( !wizard.dom.storeSummary ) {
                wizard.dom.storeSummary = $( "<div class='tabs-panel' id='store-summary'></div>" );
                wizard.dom.tabContainer.append( wizard.dom.storeSummary );
            }

            wizard.dom.tabContainer.block( block_params );

            $.ajax( {
                        type    : "POST",
                        data    : {
                            action: 'yith_pos_wizard_get_summary',
                            id    : wizard.postID
                        },
                        url     : ajaxurl,
                        success : function ( response ) {
                            wizard.dom.storeSummary.html( response ).show();
                        },
                        complete: function () {
                            wizard.dom.tabPanels.hide();
                            wizard.dom.tabAnchorsLi.removeClass( 'tabs' );
                            wizard.dom.tabContainer.unblock();
                        }
                    } );
        },
        hideStoreSummary           : function () {
            wizard.dom.storeSummary && wizard.dom.storeSummary.hide();
        },
        getCurrentPage             : function () {
            var currentPage = false;
            if ( typeof wizard.dom.tabPanels[ wizard.currentPage - 1 ] !== 'undefined' ) {
                currentPage = $( wizard.dom.tabPanels[ wizard.currentPage - 1 ] );
            }
            return currentPage;
        },
        getCurrentPageData         : function () {
            var obj         = false,
                currentPage = wizard.getCurrentPage();
            if ( currentPage ) {
                if ( wizard.currentPage !== 3 ) {
                    obj = wizard._serializeObject( currentPage.find( 'input,select,textarea' ).filter( ':not(.yith-pos-create-user-form__field)' ) );
                } else {
                    obj = {};
                }
                obj[ wizard.dom.currentPage.attr( 'name' ) ] = wizard.dom.currentPage.val();
            }
            return obj;
        },
        validateFieldsOnCurrentPage: function () {
            var validation  = true,
                currentPage = wizard.getCurrentPage(),
                fields, invalidFields;

            if ( currentPage ) {
                if ( wizard.currentPage === 3 ) {
                    if ( !$( '#yith-pos-store-metabox-registers-list .yith-pos-store-register' ).length ) {
                        validation = false;
                        notices.add( 'one-register-required', admin_i18n.one_register_required, 'error' );
                    }
                } else {
                    fields = currentPage.find( 'input,select,textarea' ).filter( ':not(.yith-pos-create-user-form__field)' );
                    fields.trigger( 'validate_field' );
                    invalidFields = fields.filter( '.invalid' );
                    if ( invalidFields.length ) {
                        invalidFields.first().focus();
                        validation = false;
                    }
                }
            }

            return validation;
        },
        saveChanges                : function ( options ) {
            if ( wizard.validateFieldsOnCurrentPage() ) {
                var currentPage = wizard.getCurrentPage(),
                    data        = wizard.getCurrentPageData(),
                    defaults    = {
                        success : function () {
                        },
                        complete: function () {
                        }
                    };
                options         = $.extend( {}, defaults, options );
                if ( !data ) {
                    return;
                }

                data = $.extend( {}, data, {
                    action                                    : 'yith_pos_wizard_save_store',
                    post_ID                                   : wizard.postID,
                    post_author                               : wizard.postAuthor,
                    post_type                                 : wizard.postType,
                    _wpnonce                                  : wizard.wpNonce,
                    yit_metaboxes_nonce                       : wizard.yithNonce,
                    yith_metabox_allow_ajax_saving            : 'yith-pos-store',
                    yith_metabox_allow_ajax_partial_saving_tab: currentPage.attr( 'id' )
                } );

                wizard.dom.tabContainer.block( block_params );

                $.ajax( {
                            type    : "POST",
                            data    : data,
                            url     : ajaxurl,
                            success : function ( response ) {
                                wizard.dom.tabContainer.unblock();
                                options.success( response );
                            },
                            complete: function ( response ) {
                                wizard.dom.tabContainer.unblock();
                                options.complete( response );
                            }
                        } );
            }
        },
        publish                    : function () {
            var status  = $( "<input type='hidden' name='post_status' value='publish' />" ),
                publish = $( "<input type='hidden' name='publish' value='1' />" );
            wizard.dom.form.append( status );
            wizard.dom.form.append( publish );
            wizard.dom.form.submit();
        }
    };

    wizard.init();
} )( jQuery );