/**
 * Created by Your Inspiration on 02/07/2015.
 */
(function() {
    tinymce.PluginManager.add('tc_button', function( editor, url ) {
        editor.addButton( 'tc_button', {
            text: false,
            type: 'menubutton',
            icon: 'ywrvp-icon',
            menu: [
                {
                    text: editor.getLang('tc_button.products'),
                    value: '{products_list}',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: editor.getLang('tc_button.custom_products'),
                    value: '{custom_products_list}',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: editor.getLang('tc_button.coupon_code'),
                    value: '{coupon_code}',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: editor.getLang('tc_button.coupon_expire'),
                    value: '{coupon_expire}',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                }
            ]

        });
    });

})();