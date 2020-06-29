/**
 * Created by Your Inspiration on 02/07/2015.
 */
(function() {
    tinymce.PluginManager.add('yith_wcdls_button', function( editor, url ) {
        editor.addButton( 'yith_wcdls_button', {
            text: false,
            type: 'menubutton',
            icon: 'ywdls-icon',
            menu: [
                {
                    text: editor.getLang('yith_wcdls_button.accept_offer'),
                    value: '[yith_wcdls_accept_offer]',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: editor.getLang('yith_wcdls_button.decline_offer'),
                    value: '[yith_wcdls_decline_offer]',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
            ]

        });
    });
})();