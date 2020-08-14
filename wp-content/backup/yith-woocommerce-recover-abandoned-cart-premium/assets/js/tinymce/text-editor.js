/**
 * Created by Your Inspiration on 02/07/2015.
 */
(function() {
    tinymce.PluginManager.add('tc_button', function( editor, url ) {
        editor.addButton( 'tc_button', {
            text: false,
            type: 'menubutton',
            icon: 'ywrac-icon',
            menu: [
                {
                    text: editor.getLang('tc_button.firstname'),
                    value: '{{ywrac.firstname}}',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: editor.getLang('tc_button.lastname'),
                    value: '{{ywrac.lastname}}',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: editor.getLang('tc_button.fullname'),
                    value: '{{ywrac.fullname}}',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: editor.getLang('tc_button.useremail'),
                    value: '{{ywrac.useremail}}',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: editor.getLang('tc_button.cartcontent'),
                    value: '{{ywrac.cart}}',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: editor.getLang('tc_button.cartlink'),
                    value: '<a href="{{ywrac.cartlink}}">'+ editor.getLang('tc_button.cartlink-label') +'</a>',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: editor.getLang('tc_button.unsubscribelink'),
                    value: '<a href="{{ywrac.unsubscribelink}}">'+ editor.getLang('tc_button.unsubscribelink-label') +'</a>',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: editor.getLang('tc_button.coupon'),
                    value: '{{ywrac.coupon}}',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                }
            ]

        });
    });
})();