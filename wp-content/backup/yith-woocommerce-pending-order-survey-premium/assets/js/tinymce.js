//TinyMCE Button
(function() {
    tinymce.PluginManager.add('ywcpos_shortcode',function(editor, url) {
        var menu_din = {
            text: editor.getLang('ywcpos_shortcode.pending-survey'),
            menu:[]
        };

        jQuery.each(ywcpos_params.tinymce, function(index, elem ){

            menu_din.menu.push(
                {
                    'text' :elem.text,
                    'value': elem.value,
                    'onclick':function(){ editor.insertContent( this.value() );}
                }
            )
        });

            editor.addButton('ywcpos_shortcode', {
                title : 'Add field in email',
                image : url+'/../images/icon-accordion.png',
                type: 'menubutton',
                menu: [
                    {
                        text: editor.getLang('ywcpos_shortcode.firstname'),
                        value: '{{ywcpos_firstname}}',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                    },
                    {
                        text: editor.getLang('ywcpos_shortcode.lastname'),
                        value: '{{ywcpos_lastname}}',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                    },
                    {
                        text: editor.getLang('ywcpos_shortcode.fullname'),
                        value: '{{ywcpos_fullname}}',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                    },
                    {
                        text: editor.getLang('ywcpos_shortcode.useremail'),
                        value: '{{ywcpos_useremail}}',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                    },
                    {
                        text: editor.getLang('ywcpos_shortcode.ordercontent'),
                        value: '{{ywcpos_order}}',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                    },
                    {
                        text: editor.getLang('ywcpos_shortcode.orderlink'),
                        value: '<a href="{{ywcpos_link}}" target="_blank">'+editor.getLang('ywcpos_shortcode.order_link_label')+'</a>',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                    },
                    {
                        text: editor.getLang('ywcpos_shortcode.coupon'),
                        value: '{{ywcpos_coupon}}',
                        onclick: function() {
                            editor.insertContent(this.value());
                        }
                    },

                    menu_din

                ]

            });
    });
})();