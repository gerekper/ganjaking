(function() {

    // Load plugin specific language pack
    //tinymce.PluginManager.requireLangPack('example');
    tinymce.create('tinymce.plugins.WYSIJA_custom_fields', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */

        init: function(ed, url) {
            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
            ed.addCommand('wysijaCustomFieldsInsert', function() {
                ed.windowManager.open({
                    file : url + '/shortcodes.html', // file that contains HTML for our modal window
                    width : 500 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
                    height : 300 + parseInt(ed.getLang('button.delta_height', 0)), // size of our window
                    inline : 1,
                    resizable: false
                }, {
                    plugin_url : url
                });
            });

            // Register example button
            ed.addButton('wysija_custom_fields', {
                title : wysijatrans.customFieldsLabel,
                cmd : 'wysijaCustomFieldsInsert',
                'class': 'wj_custom_fields'
            });
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
        createControl: function(n, cm) {
            return null;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo: function() {
            return {
                longname : 'Wysija Custom Fields',
                author : 'Wysija',
                authorurl : 'http://www.mailpoet.com',
                infourl : 'http://www.mailpoet.com',
                version : '0.3'
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('wysija_custom_fields', tinymce.plugins.WYSIJA_custom_fields);
})();