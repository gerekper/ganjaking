/**
 * Gutenberg Full Site Editing 
 * 
 * Add attributes or class
 * 
 * @since 6.6.0
 */
( function() {
    portoAddAttributes = function( obj, value, attr = false ) {
        obj.forEach( function( currentObj ) {
            if ( attr ) {
                if ( !currentObj.classList.contains( value ) ) {
                    currentObj.classList.add( value );
                }
            } else {
                if ( currentObj.id != value ) {
                    currentObj.setAttribute( 'id', value );
                }
            }
        } );
    }

    portoGfseFunc = function() {
        var header = document.querySelectorAll( 'header' ),
            footer = document.querySelectorAll( 'footer' ),
            main = document.querySelectorAll( 'main' ),
            pageWrapper = document.querySelectorAll( '.wp-site-blocks' );

        if ( header ) {
            portoAddAttributes( header, 'header' );
        }
        if ( main ) {
            portoAddAttributes( main, 'page-wrapper', true );
        }
        if ( footer ) {
            portoAddAttributes( footer, 'footer' );
            portoAddAttributes( footer, 'footer', true );
        }
        if ( window && window.parent && window.parent.location && pageWrapper ) {
            var page = window.parent.location.href;
            if ( -1 != page.indexOf( 'postType=wp_template_part' ) ) {
                var jsPortoAdminVars = window.parent.js_porto_admin_vars;
                if ( jsPortoAdminVars ) {
                    var templatePartArea = jsPortoAdminVars['gfse_template_area'];
                    if ( templatePartArea == 'header' ) {
                        portoAddAttributes( pageWrapper, 'header' );
                    } else if ( templatePartArea == 'footer' ) {
                        portoAddAttributes( pageWrapper, 'footer' );
                        portoAddAttributes( pageWrapper, 'footer', true );
                    } else {
                        portoAddAttributes( pageWrapper, 'page-wrapper', true );
                    }
                }
            }
        }
    }
    portoGfseFunc();
    window.parent.wp.hooks.addFilter( 'blockEditor.__unstableCanInsertBlockType', 'removeTemplatePartsFromPostTemplates', function( can, blockType, clientId, _ref ) {
        if ( 'core/template-part' == blockType.name ) {
            portoGfseFunc();
        }
        return can;
    } );
} )();