/**
 * Created by Your Inspiration on 02/07/2015.
 */
(function() {
    tinymce.PluginManager.add('tc_button', function( editor, url ) {

        var menu_item;

        if( editor.id == 'woocommerce_yith_waitlist_mail_instock_mail_content' ) {
            menu_item = {
                text: editor.getLang('tc_button.product_link'),
                value: '{product_link}',
                onclick: function() {
                    editor.insertContent(this.value());
                }
            }
        }
        else if( editor.id == 'woocommerce_yith_waitlist_mail_subscribe_mail_content' ) {
            menu_item = {
                text: editor.getLang('tc_button.remove_link'),
                value: '{remove_link}',
                onclick: function() {
                    editor.insertContent(this.value());
                }
            }
        }
        else if( editor.id == 'woocommerce_yith_waitlist_mail_subscribe_optin_mail_content' ) {
            menu_item = {
                text: editor.getLang('tc_button.confirm_link'),
                value: '{confirm_link}',
                onclick: function() {
                    editor.insertContent(this.value());
                }
            }
        }
        else if( editor.id == 'woocommerce_yith_waitlist_mail_admin_mail_content' ) {
            menu_item = {
                text: editor.getLang('tc_button.user_email'),
                value: '{user_email}',
                onclick: function() {
                    editor.insertContent(this.value());
                }
            }
        }

        editor.addButton( 'tc_button', {
            text: false,
            type: 'menubutton',
            icon: 'ywcwtl-icon',
            menu: [
                {
                    text: editor.getLang('tc_button.blogname'),
                    value: '{blogname}',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: editor.getLang('tc_button.site_title'),
                    value: '{site_title}',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: editor.getLang('tc_button.product_title'),
                    value: '{product_title}',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                menu_item
            ]

        });
    });

})();