/* global yith_pos_store_edit, yith_pos, ajaxurl */
( function ( $ ) {
    var metabox  = $( '#yith-pos-store' ),
        isWizard = $( 'body' ).hasClass( 'yith-pos-store-wizard' ),
        storeID  = yith_pos_store_edit.post_id;


    if ( !isWizard ) {
        // Required fields validation before submitting form of Stores
        $( document ).on( 'submit', 'body.post-type-yith-pos-store form#post', function ( e ) {
            $( document ).trigger( 'yith-pos-registers-list:deleteRegisterToCreate' );
            var form   = $( e.target ),
                fields = form.find( 'input,select,textarea' ).filter( ':not(.yith-pos-create-user-form__field)' ),
                invalidFields, firstInvalidField, tabID, tabAnchor;

            fields.trigger( 'validate_field' );
            invalidFields = fields.filter( '.invalid' );
            if ( invalidFields.length ) {
                firstInvalidField = invalidFields.first();
                tabID             = firstInvalidField.closest( '.tabs-panel' ).attr( 'id' );
                tabAnchor         = metabox.find( 'ul.metaboxes-tabs li a[href=#' + tabID + ']' );
                tabAnchor && tabAnchor.click();
                firstInvalidField.focus();
                e.preventDefault();
            }
        } );
    }

    var registersList = {
        dom                    : {
            list             : $( '#yith-pos-store-metabox-registers-list' ),
            newBtn           : $( '#yith-pos-store-metabox-registers-list__new' ),
            createFormWrapper: $( '#yith-pos-store-metabox-registers-list__create-form-wrapper' )
        },
        init                   : function () {
            registersList.dom.newBtn.on( 'click', registersList.newRegisterFormHandler );
            registersList.dom.createFormWrapper.on( 'click', '.yith-pos-register-create', registersList.create );
            registersList.dom.list.on( 'click', '.yith-pos-register-update', registersList.update );
            registersList.dom.list.on( 'click', '.yith-pos-register-delete', registersList.deleteRegister );

            // external events
            $( document ).on( 'yith-pos-registers-list:deleteRegisterToCreate', registersList.deleteRegisterToCreate );

            // at least one payment method
            $( document ).on( 'click', '.yith-pos-register-payment-methods input[type=checkbox]', registersList.atLeastOnePaymentMethod );

            // real-time title editing
            $( document ).on( 'change keyup', '.yith-pos-store-register__name-field-container', registersList.realtimeTitleEditing );
        },
        atLeastOnePaymentMethod: function ( e ) {
            var currentCheckbox   = $( e.target ),
                checkedCheckboxes = currentCheckbox.closest( '.yith-pos-register-payment-methods' ).find( 'input[type=checkbox]:checked' );

            if ( !checkedCheckboxes.length ) {
                currentCheckbox.prop( 'checked', true );
            }

        },
        getRegisterToCreate    : function () {
            var register = registersList.dom.createFormWrapper.find( '.yith-pos-store-register' ).first();
            return register.length ? register : false;
        },
        deleteRegisterToCreate : function () {
            registersList.dom.createFormWrapper.find( '.yith-pos-store-register' ).remove();
        },
        initFields             : function () {
            $( document.body )
                .trigger( 'wc-enhanced-select-init' )
                .trigger( 'yith-framework-enhanced-select-init' )
                .trigger( 'yith-plugin-fw-metabox-init-deps' );
        },
        newRegisterFormHandler : function ( event ) {
            event.preventDefault();
            if ( !registersList.dom.newBtn.is( '.closed' ) ) {
                registersList.openForm();
            } else {
                registersList.closeForm();
            }
        },
        openForm               : function () {
            var _register = registersList.getRegisterToCreate();

            if ( !_register ) {
                var _template = registersList.dom.newBtn.data( 'template' );
                _register     = $( _template );
                _register.hide();
                registersList.dom.createFormWrapper.append( _register );
                registersList.initFields();

                if ( isWizard ) {
                    // populate cashiers
                    var _select = _register.find( '.show-cashiers-list select' ), _opt;
                    $( '#_cashiers option' ).each( function () {
                        _opt = new Option( $( this ).html().replace( '&amp;ndash;', '-' ), $( this ).val(), false, false );
                        _select.append( _opt );
                    } );
                    _select.trigger( 'change' );
                }

            }
            _register.slideDown();
            registersList.dom.newBtn.html( registersList.dom.newBtn.data( 'close-text' ) ).addClass( 'closed' );
        },
        closeForm              : function () {
            var _register = registersList.getRegisterToCreate();
            if ( _register ) {
                _register.slideUp();
            }

            registersList.dom.newBtn.html( registersList.dom.newBtn.data( 'text' ) ).removeClass( 'closed' );
        },
        serializeObject        : function ( fields, namePrefixToReplace ) {
            var o                   = {},
                a                   = fields.serializeArray(),
                uncheckedCheckboxes = $.map( fields.filter( "input[type='checkbox'].on_off:not(:checked)" ), function ( obj ) {
                    return { name: $( obj ).attr( 'name' ), value: $( obj ).val() };
                } );

            a = a.concat( uncheckedCheckboxes );

            $.each( a, function () {
                var thisName = this.name;
                if ( thisName && namePrefixToReplace ) {
                    thisName = thisName.replace( namePrefixToReplace, '' );
                }
                if ( o[ thisName ] ) {
                    if ( !o[ thisName ].push ) {
                        o[ thisName ] = [o[ thisName ]];
                    }
                    o[ thisName ].push( this.value || '' );
                } else {
                    o[ thisName ] = this.value || '';
                }
            } );
            return o;
        },
        getRegisterData        : function ( register ) {
            var _fields          = register.find( 'input,select,textarea' ),
                _fieldNamePrefix = register.data( 'field-name-prefix' );

            _fields.trigger( 'validate_field' );

            return registersList.serializeObject( _fields, _fieldNamePrefix );
        },
        addMessage             : function ( _register, _text, _type ) {
            var _message = $( "<div class='yith-pos-admin-notice yith-pos-admin-notice--" + _type + "'>" + _text + "<span class='yith-pos-admin-notice__dismiss'></span></div>" );
            _register.find( '.yith-pos-settings-box__content' ).prepend( _message );
            yith_pos.scrollTo( _register );
        },
        removeMessages         : function ( _register ) {
            _register.find( '.yith-pos-admin-notice' ).remove();
        },
        create                 : function () {
            var _register = registersList.getRegisterToCreate();
            if ( _register ) {
                registersList.removeMessages( _register );

                var _data = registersList.getRegisterData( _register );

                if ( !_register.find( '.validate-error' ).length ) {
                    _register.block( yith_pos.blockParams );

                    _data.action   = 'yith_pos_create_register';
                    _data.store_id = storeID;
                    _data.security = yith_pos_store_edit.create_register_nonce;

                    $.ajax( {
                                type    : 'POST',
                                data    : _data,
                                url     : ajaxurl,
                                success : function ( response ) {
                                    if ( typeof response.success !== 'undefined' && response.success ) {
                                        var _new_register = $( response.register_html );
                                        registersList.dom.list.append( _new_register );
                                        registersList.initFields();
                                        registersList.deleteRegisterToCreate();
                                        registersList.closeForm();
                                        $( document ).trigger( 'yith_pos_register_created', [_new_register] );
                                    } else {
                                        if ( typeof response.error !== 'undefined' ) {
                                            registersList.addMessage( _register, response.error, 'error' );
                                        }
                                    }
                                },
                                complete: function () {
                                    _register.unblock();
                                    $(document).find( '.yith-pos-admin-notice' ).remove();
                                }
                            } );
                }
            }
        },
        update                 : function ( event ) {
            var _button   = $( event.target ),
                _register = _button.closest( '.yith-pos-store-register' ), _data;
            if ( _register ) {
                registersList.removeMessages( _register );

                _data    = registersList.getRegisterData( _register );
                _data.id = _register.data( 'register-id' );

                if ( !_register.find( '.validate-error' ).length ) {
                    _register.block( yith_pos.blockParams );

                    _data.action   = 'yith_pos_update_register';
                    _data.security = yith_pos_store_edit.update_register_nonce;

                    $.ajax( {
                                type    : 'POST',
                                data    : _data,
                                url     : ajaxurl,
                                success : function ( response ) {
                                    if ( typeof response.success !== 'undefined' && response.success ) {
                                        if ( typeof response.message !== 'undefined' ) {
                                            registersList.addMessage( _register, response.message, 'success' );
                                        }
                                    }

                                    if ( typeof response.error !== 'undefined' ) {
                                        registersList.addMessage( _register, response.error, 'error' );
                                    }
                                },
                                complete: function () {
                                    _register.unblock();
                                }
                            } );
                }
            }
        },
        deleteRegister                 : function ( event ) {
            var _button   = $( event.target ),
                _register = _button.closest( '.yith-pos-store-register' ), _data;
            _register && registersList.removeMessages( _register );

            if ( _register && window.confirm( yith_pos_store_edit.i18n_register_delete_confirmation ) ) {
                _data = {
                    id      : _register.data( 'register-id' ),
                    action  : 'yith_pos_delete_register',
                    security: yith_pos_store_edit.delete_register_nonce
                };

                _register.block( yith_pos.blockParams );

                $.ajax( {
                            type    : 'POST',
                            data    : _data,
                            url     : ajaxurl,
                            success : function ( response ) {
                                if ( typeof response.success !== 'undefined' && response.success ) {
                                    _register.unblock();
                                    _register.slideUp( 400, function () {
                                        _register.remove();
                                    } );
                                } else {
                                    if ( typeof response.error !== 'undefined' ) {
                                        registersList.addMessage( _register, response.error, 'error' );
                                    }
                                }
                            },
                            complete: function () {
                                _register.unblock();
                            }
                        } );

            }
        },
        realtimeTitleEditing   : function ( event ) {
            var _editInput = $( event.target ),
                _section   = _editInput.closest( '.yith-pos-store-register' ),
                _title     = _section.find( '.yith-pos-settings-box__header .yith-pos-settings-box__title' ).first();

            if ( _title.length ) {
                _title.html( _editInput.val() );
            }
        }
    };
    registersList.init();

    /**
     * Register Field Deps
     */
    $( document ).on( 'change', '.yith-pos-store-register .on_off, .yith-pos-store-register [type=checkbox]', function () {
        var $t     = $( this ),
            id     = $t.attr( 'id' ),
            target = $( document ).find( '[data-dep-id=' + id + ']' );

        if ( typeof target !== 'undefined' && $t.is( ':checked' ) ) {
            $( target ).css( { opacity: 1 } );
        } else {
            $( target ).css( { opacity: 0.3 } );
        }
    } );
    $( document ).find( '.yith-pos-store-register .on_off, .yith-pos-store-register [type=checkbox]' ).change();

    /**
     * Edit register link opens the register tab
     */
    if ( 'URLSearchParams' in window ) {
        var urlParams  = new URLSearchParams( window.location.search ), // jshint ignore:line
            registerID = urlParams.get( 'yith-pos-edit-register' ),
            register   = $( '.yith-pos-store-register[data-register-id=' + registerID + ']' );

        if ( registerID && register.length ) {
            $( '#yith-plugin-fw-metabox-tab-edit_store_registers-anchor a' ).click();

            register.find( '.yith-pos-settings-box__toggle' ).click();
            setTimeout( function () {
                yith_pos.scrollTo( register );
            }, 200 );
        }
    }

} )( jQuery );