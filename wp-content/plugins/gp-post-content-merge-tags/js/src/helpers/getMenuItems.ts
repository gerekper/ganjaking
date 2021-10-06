import truncateMiddle from "./truncateMiddle";

const $ = window.jQuery;

export default function getMenuItems(value: number, showBackItem: boolean = true) {

    const { ajaxurl, gppcmtData } = window;

    return $.when( $.post( ajaxurl, {
        action: 'gppcmt_get_form',
        nonce:  gppcmtData.nonce,
        formId: value,
        postId: gppcmtData.postId
    }) ).then(function( response ) {

        var result = $.parseJSON( response ),
            items  = [];

        // if single form is returned get field merge tags
        if( result.id ) {

            if (showBackItem) {
                items.push( {
                    text: '↩ View All Forms',
                    type: 'back',
                    value: -1
                }, {
                    text: '-'
                } );
            }

            if( typeof window.gf_vars == 'undefined' ) {
                window.gf_vars = {};
            }

            window.form = result;
            window.gf_vars.mergeTags = result.mergeTags;

            var GFMergeTags = new window.gfMergeTagsObj( result ),
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

        return $.when(items);

    });

}