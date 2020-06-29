(function ($) {
	tinymce.PluginManager.add('pt_plus_shortcodes', function (editor, url) {
		editor.addButton('pt_plus_shortcodes', {
			text: 'Insert shortcode',
			icon: false,
			type: 'menubutton',
			menu: [
                {
					text : 'Dropcap',
					onclick : function() {
						editor.insertContent('[tp_dropcap font_family="" font_size="40" background="#ff214f" color="#fff" shadow="false" style="1" ]I[/tp_dropcap]nsert your content here');
					}
				},
				{
					text : 'Fancy Link',
					onclick : function() {
						editor.insertContent('[tp_fancy_link title="Insert your content here" link="" target="" style="1" class="" download="" text_color="#252525" text_hover_color="#cccccc" background="#ff214f"]');
					}
				},
				{
					text : 'code',
					onclick : function() {
						editor.insertContent('[tp_code] [/tp_code]');
					}
				},
				{
					text : 'Hightlight',
					onclick : function() {
						editor.insertContent('[tp_hightlight title="Insert your content here" class="" background_hover="#1abc9c" background="#ff214f" text_color="#ffffff" text_hover_color="#121212" animation="yes"]');
					}
				},
				{
					text : 'Blockquote',
					onclick : function() {
						editor.insertContent('[tp_blockquote author="Jhon Doe" link="" target="_blank" color="#fff" background="#ff004b" quote_color="#d71951" border_color="#ff92b2" author_color="#fff" bottom_background="#fb5988" style="1"]Insert your content here[/tp_blockquote]');
					}
				},
				{
                    text   : 'Tooltip',
                    onclick: function () {
                        editor.windowManager.open({
                            title   : 'Insert Tooltip Shortcode',
                            body    : [
                                {
                                    type : 'label',
                                    name : 'popoverTitle',
                                    label: 'Tooltip text'
                                },
                                {
                                    type : 'textbox',
                                    name : 'tooltipContent',
                                },
								{
                                    type : 'textbox',
                                    name : 'tooltiptextcolor',
                                    label: 'Tooltip Title Color'
									},
								{
                                    type    : 'listbox',
                                    name    : 'tooltipColor',
                                    'values': [
                                        {text: 'Default', value: 'default'},
                                        {text: 'Light', value: 'light'},
										{text: 'BorderLess', value: 'borderless'},
										{text: 'Noir', value: 'noir'},
										{text: 'Shadow', value: 'shadow'},
                                    ]
                                },
                                {
                                    type    : 'listbox',
                                    name    : 'tooltipAlign',
                                    'values': [
                                        {text: 'Left', value: 'left'},
                                        {text: 'Right', value: 'right'},
                                        {text: 'Bottom', value: 'bottom'},
                                        {text: 'Top', value: 'top'}
                                    ]
                                },
								{
                                    type    : 'listbox',
                                    name    : 'tooltip_animation',
                                    'values': [
                                        {text: 'Fade', value: 'fade'},
                                        {text: 'Grow', value: 'grow'},
                                        {text: 'Swing', value: 'swing'},
                                        {text: 'Slide', value: 'slide'},
										{text: 'Fall', value: 'fall'}
										
                                    ]
                                },
								 {
                                    type : 'textbox',
                                    name : 'imageurl',
                                    label: 'Custom image url',
                                    id: 'my-image-box'
                                },
                                {
                                    type: 'button',
                                    name: 'selectImage',
                                    text: 'Select Image',
                                    onclick: function () {
                                        window.mb = window.mb || {};

                                        window.mb.frame = wp.media({
                                            frame: 'post',
                                            state: 'insert',
                                            library: {
                                                type: 'image'
                                            },
                                            multiple: false
                                        });

                                        window.mb.frame.on('insert', function () {
                                            var json = window.mb.frame.state().get('selection').first().toJSON();

                                            if (0 > jQuery.trim(json.url.length)) {
                                                return;
                                            }

                                            jQuery('#my-image-box').val(json.url);
                                        });

                                        window.mb.frame.open();
                                    }
                                },
                            ],
                            onsubmit: function (e) {                               
                                var shortcode_text = e.data.tooltipContent.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
								var shortcode_color = e.data.tooltiptextcolor.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                                editor.insertContent('[tp_tooltip color="'+shortcode_color+'" text="' + shortcode_text + '" tooltip_style="'+e.data.tooltipColor+'" align="' + e.data.tooltipAlign + '" animation="' + e.data.tooltip_animation + '" image="' + e.data.imageurl + '" ]Insert your content here[/tp_tooltip]');
                            }
                        });
                    }
                }
			]
		});
	});
})();
