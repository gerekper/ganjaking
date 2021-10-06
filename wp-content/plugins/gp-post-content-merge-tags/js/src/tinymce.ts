import getMenuItems from "./helpers/getMenuItems";

const $ = window.jQuery;

let currentForm:number | null = null;
let tinymceMenuInitialized:boolean = false; // will be populated progressively as the user makes selections

const { gppcmtData } = window;

window.tinymce.PluginManager.add( 'gppcmt', function( editor: any, url: string ) {

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
            updateTinymceMenu( gppcmtData.initFormId );
        }
    }, 100 );

    function populateMenu( items ) {
        // reset menu items
        editor.buttons.gppcmt.menu.splice( 0, editor.buttons.gppcmt.menu.length );

        for( var i = 0; i < items.length; i++ ) {

            editor.buttons.gppcmt.menu.push( {
                text: items[i].text,
                classes: items[i].classes,
                onclick: function( item ) {
                    handleTinymceMenuSelection( this.type, this.value );
                }.bind( items[i] )
            } );

        }
    }

    function handleTinymceMenuSelection( type, value ) {

        switch( type ) {
            case 'form':
                currentForm = parseInt( value );
                updateTinymceMenu( currentForm );
                break;
            case 'back':
                currentForm = null;
                updateTinymceMenu( 0 );
                break;
            case 'field':
                editor.insertContent( String( value ) );
                break;
        }

    }

    function updateTinymceMenu( value ) {

        if( typeof value == 'undefined' ) {
            value = 0;
        }

        // fetch new menu items
        var $button = $( editor.controlManager.buttons.gppcmt.$el.find( 'button' ) );

        if( ! $button.data( 'content' ) ) {
            $button.data( 'content', $button.html() );
        }

        $button.html( '<span style="display:inline-block;width:20px;height:20px;padding:0;text-align:center;"><img src="' + gppcmtData.gfBaseUrl + '/images/spinner.gif" style="margin-top:2px;" /></span>' );

        getMenuItems( value ).done(function(items) {
            $button.html( $button.data( 'content' ) );

            // add the retrieved menu items
            populateMenu( items );

            // force the menu to redraw
            editor.controlManager.buttons.gppcmt.menu = null;

            // show the menu
            if( tinymceMenuInitialized && $( editor.getContainer() ).is( ':visible' ) ) {
                editor.controlManager.buttons.gppcmt.showMenu();
            }

            tinymceMenuInitialized = true;
        });

    }

} );
