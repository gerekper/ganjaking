jQuery(function ($) {


    //TinyMCE Button
    var image_url = '';

    tinymce.create('tinymce.plugins.YITH_WooCommerce_Donations', {
        init: function (ed, url) {
            ed.addButton('ywcds_shortcode', {
                title: 'Add Donation',
                image: url + '/../images/donate-icon.png',
                onclick: function () {
                    ed.windowManager.open({

                        body: [
                            {
                                type: 'textbox',
                                name: 'donation_amount',

                                label:ed.getLang( 'ywcds_shortcode.multi_amount_label'),
                                placeholder : ed.getLang('ywcds_shortcode.multi_amount_placeholder')
                            },
                            {
                                type: 'listbox',
                                name:'donation_amount_style',
                                label: ed.getLang( 'ywcds_shortcode.multi_amount_style'),
                                values:
                                   ed.getLang( 'ywcds_shortcode.multi_amount_options' )
                                ,
                                value: 'radio'
                            },
                            {
                                type   : 'checkbox',
                                name   : 'show_donation_reference',
                                label  : ed.getLang( 'ywcds_shortcode.show_donation_reference'),
                                checked : false
                            },
                            {
                                type : 'textbox',
                                name : 'extra_desc_label',
                                label : ed.getLang( 'ywcds_shortcode.extra_desc_label' ),
                            }
                        ],
                        onsubmit: function (e) {

                            var str = '[yith_wcds_donations',
                                win = window.dialogArguments || opener || parent || top;

                                if( e.data.donation_amount !== '' ){
                                    str+=" donation_amount='"+e.data.donation_amount+"' donation_style='"+e.data.donation_amount_style+"'";
                                }

                                if( e.data.show_donation_reference ){

                                    str+=" show_extra_desc='on'";

                                    if( e.data.extra_desc_label !== '' ){
                                        var label = e.data.extra_desc_label.replace(':','');

                                        str+= ' extra_desc_label="'+label+'"';
                                    }
                                }


                                str+="]";
                            ed.insertContent(str);

                        }
                    });
                },
            });
        },
        createControl: function (n, cm) {
            return null;
        },
        getInfo: function () {
            return {
                longname: 'YITH WooCommerce Donations',
                author: 'YITHEMES',
                authorurl: 'http://yithemes.com/',
                infourl: 'http://yithemes.com/',
                version: "1.0"
            };
        }
    });
    tinymce.PluginManager.add('ywcds_shortcode', tinymce.plugins.YITH_WooCommerce_Donations);

});
