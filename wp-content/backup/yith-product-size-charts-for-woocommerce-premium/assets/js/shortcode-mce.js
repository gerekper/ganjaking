jQuery( function ( $, document ) {
    tinymce.create( 'tinymce.plugins.yith_wcpsc', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init: function ( ed, url ) {

            ed.addButton( 'add_size_chart', {
                title: 'Add Size Chart',
                cmd: 'add_size_chart',
                image: url + '/../icons/add_chart_size.png'
            } );

            var popup = null;

            ed.addCommand( 'add_size_chart', function () {

                if (! popup){
                    var url_with_params = ajaxurl + '?height=300&width=300&action=yith_wcpsc_get_shortcode';
                    popup = $.fn.yith_wcpsc_popup({
                            ajax: true,
                            url: url_with_params,
                            popup_css: {
                                width: 300,
                                height: 150
                            }
                        });
                }else{
                    popup = popup.yith_wcpsc_popup({
                        popup_css: {
                            width: 300,
                            height: 150
                        }
                    });
                }

                // ADD ACTIONS TO POPUP BUTTONS
                popup
                    .on('click', '.yith-wcpsc-shortcode-cancel', function(){
                    popup.yith_wcpsc_popup('close');
                })

                    .on('click', '.yith-wcpsc-shortcode-add', function(){
                    var selected_id = popup.find('.yith-wcpsc-shortcode-select' ).children(':selected' ).val(),
                        shortcode = '[sizecharts id="'+ selected_id +'"]';
                    ed.execCommand( 'mceInsertContent', 0, shortcode );
                    popup.yith_wcpsc_popup('close');
                });
            } );
        },

        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl: function ( n, cm ) {
            return null;
        },
    } );

    // Register plugin
    tinymce.PluginManager.add( 'yith_wcpsc', tinymce.plugins.yith_wcpsc );
} );