( function( $ ) {

    var currentForm = false,
        initialized = false; // will be populated progressively as the user makes selections

    tinymce.PluginManager.add( 'gppcmt', function( editor, url ) {

        editor.addButton( 'gppcmt', {
            type: 'menubutton',
            title: 'Insert Gravity Forms Merge Tags',
            image: gppcmtData.gfBaseUrl + '/images/icon-drop-list.png',
            classes: 'gppcmt',
            menu: []
        } );

        // tinymce takes a second to actually create the button, poll until button exists before initializing the menu
        var initPoll = setInterval( function() {
            if( editor.controlManager.buttons.gppcmt ) {
                clearInterval( initPoll );
                updateMenu( gppcmtData.initFormId );
            }
        }, 100 );

        function updateMenu( value ) {

            if( typeof value == 'undefined' ) {
                value = 0;
            }

            // fetch new menu items
            getMenuItems( value );

        }

        function handleMenuSelection( type, value ) {

            switch( type ) {
                case 'form':
                    currentForm = parseInt( value );
                    updateMenu( currentForm );
                    break;
                case 'back':
                    currentForm = false;
                    updateMenu( 0 );
                    break;
                case 'field':
                    editor.insertContent( String( value ) );
                    break;
            }

        }

        function getMenuItems( value ) {

            var $button = $( editor.controlManager.buttons.gppcmt.$el.find( 'button' ) );

            if( ! $button.data( 'content' ) ) {
                $button.data( 'content', $button.html() );
            }

            $button.html( '<span style="display:inline-block;width:20px;height:20px;padding:0;text-align:center;"><img src="' + gppcmtData.gfBaseUrl + '/images/spinner.gif" style="margin-top:2px;" /></span>' );

            $.post( ajaxurl, {
                action: 'gppcmt_get_form',
                nonce:  gppcmtData.nonce,
                formId: value,
                postId: gppcmtData.postId
            }, function( response ) {

                $button.html( $button.data( 'content' ) );

                var result = $.parseJSON( response ),
                    items  = [];

                // if single form is returned get field merge tags
                if( result.id ) {

                    items.push( {
                        text: '↩ View All Forms',
                        type: 'back',
                        value: -1
                    }, {
                        text: '-'
                    } );

                    if( typeof window.gf_vars == 'undefined' ) {
                        window.gf_vars = {};
                    }

                    form = result;
                    gf_vars.mergeTags = result.mergeTags;

                    var GFMergeTags = new gfMergeTagsObj( result ),
                        mergeTags   = GFMergeTags.getMergeTags( result.fields, '#content' );

                    for( var group in mergeTags ) {

                        if( ! mergeTags.hasOwnProperty( group ) ) {
                            continue;
                        }

                        var label = mergeTags[ group ].label,
                            tags  = mergeTags[ group ].tags;

                        // skip groups without any tags
                        if( tags.length <= 0 ) {
                            continue;
                        }

                        if( label ) {
                            items.push( {
                                text: '-'
                            }, {
                                text: '- ' + label + ' -',
                                classes: 'header'
                            } );
                        }

                        for( var i = 0; i < tags.length; i++ ) {
                            items.push( {
                                type: 'field',
                                text: truncateMiddle( tags[ i ].label, 40 ),
                                value: tags [ i ].tag,
                                classes: 'field'
                            } );
                        }

                    }

                }
                // otherwise, prepare our forms as menu items
                else {

                    items.push( {
                        text: '- Select a Form -',
                        classes: 'header'
                    } );

                    for( var i = 0; i < result.length; i++ ) {
                        items.push( {
                            type:  'form',
                            text:  result[i].title,
                            value: result[i].id,
                            classes: 'form'
                        } );
                    }
                }

                // add the retrieved menu items
                populateMenu( items );

                // force the menu to redraw
                editor.controlManager.buttons.gppcmt.menu = null;

                // show the menu
                if( initialized && $( editor.getContainer() ).is( ':visible' ) ) {
                    editor.controlManager.buttons.gppcmt.showMenu();
                }

                initialized = true;

            } );

        }

        function populateMenu( items ) {

            // reset menu items
            editor.buttons.gppcmt.menu.splice( 0, editor.buttons.gppcmt.menu.length );

            for( var i = 0; i < items.length; i++ ) {

                editor.buttons.gppcmt.menu.push( {
                    text: items[i].text,
                    classes: items[i].classes,
                    onclick: function( item ) {
                        handleMenuSelection( this.type, this.value );
                    }.bind( items[i] )
                } );

            }


        }

	    function truncateMiddle( text, length ) {

            if( ! text || text.length <= length ) {
			    return text;
            }

            var halfLength = length / 2;

		    return text.substr( 0, halfLength ) + '...' + text.substr( text.length - ( halfLength - 1 ), halfLength );
	    }

    } );

} ) ( jQuery );