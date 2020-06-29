(function() {

  /* globals tinymce */

  "use strict";

  tinymce.PluginManager.add('mfnsc', function(editor, url) {
    editor.addButton('mfnsc', {
      text: false,
      type: 'menubutton',
      icon: 'mfn-sc',
      classes: 'widget btn mfnsc',
      menu: [{
        text: 'Column',
        menu: [{
          text: '1/1',
          onclick: function() {
            editor.insertContent('[one]Insert your content here[/one]');
          }
        }, {
          text: '1/2',
          onclick: function() {
            editor.insertContent('[one_second]Insert your content here[/one_second]');
          }
        }, {
          text: '1/3',
          onclick: function() {
            editor.insertContent('[one_third]Insert your content here[/one_third]');
          }
        }, {
          text: '2/3',
          onclick: function() {
            editor.insertContent('[two_third]Insert your content here[/two_third]');
          }
        }, {
          text: '1/4',
          onclick: function() {
            editor.insertContent('[one_fourth]Insert your content here[/one_fourth]');
          }
        }, {
          text: '2/4',
          onclick: function() {
            editor.insertContent('[two_fourth]Insert your content here[/two_fourth]');
          }
        }, {
          text: '3/4',
          onclick: function() {
            editor.insertContent('[three_fourth]Insert your content here[/three_fourth]');
          }
        }, {
          text: '1/5',
          onclick: function() {
            editor.insertContent('[one_fifth]Insert your content here[/one_fifth]');
          }
        }, {
          text: '2/5',
          onclick: function() {
            editor.insertContent('[two_fifth]Insert your content here[/two_fifth]');
          }
        }, {
          text: '3/5',
          onclick: function() {
            editor.insertContent('[three_fifth]Insert your content here[/three_fifth]');
          }
        }, {
          text: '4/5',
          onclick: function() {
            editor.insertContent('[four_fifth]Insert your content here[/four_fifth]');
          }
        }, {
          text: '1/6',
          onclick: function() {
            editor.insertContent('[one_sixth]Insert your content here[/one_sixth]');
          }
        }, {
          text: '2/6',
          onclick: function() {
            editor.insertContent('[two_sixth]Insert your content here[/two_sixth]');
          }
        }, {
          text: '3/6',
          onclick: function() {
            editor.insertContent('[three_sixth]Insert your content here[/three_sixth]');
          }
        }, {
          text: '4/6',
          onclick: function() {
            editor.insertContent('[four_sixth]Insert your content here[/four_sixth]');
          }
        }, {
          text: '5/6',
          onclick: function() {
            editor.insertContent('[five_sixth]Insert your content here[/five_sixth]');
          }
        }, ]
      }, {
        text: 'Content',
        menu: [{
          text: 'Alert',
          onclick: function() {
            editor.insertContent('[alert style="warning"]Insert your content here[/alert]');
          }
        }, {
          text: 'Blockquote',
          onclick: function() {
            editor.insertContent('[blockquote author="" link="" target="_blank"]Insert your content here[/blockquote]');
          }
        }, {
          text: 'Button',
          onclick: function() {
            editor.insertContent('[button title="Button" link="" target="_blank" align="" icon="" icon_position="" color="" font_color="" size="2" full_width="" class="" download="" rel=""]');
          }
        }, {
          text: 'Code',
          onclick: function() {
            editor.insertContent('[code]Insert your content here[/code]');
          }
        }, {
          text: 'Content Link',
          onclick: function() {
            editor.insertContent('[content_link title="" icon="icon-lamp" link="" target="_blank" class="" download=""]');
          }
        }, {
          text: 'Counter Inline',
          onclick: function() {
            editor.insertContent('[counter_inline value="500"]');
          }
        }, {
          text: 'Divider',
          onclick: function() {
            editor.insertContent('[divider height="30" style="default" line="default" color="" themecolor="0"]');
          }
        }, {
          text: 'Dropcap',
          onclick: function() {
            editor.insertContent('[dropcap font="" size="1" background="" color="" circle="0" transparent="0"]I[/dropcap]nsert your content here');
          }
        }, {
          text: 'Fancy Link',
          onclick: function() {
            editor.insertContent('[fancy_link title="" link="" target="" style="1" class="" download=""]');
          }
        }, {
          text: 'Google Font',
          onclick: function() {
            editor.insertContent('[google_font font="Open Sans" size="25" weight="400" italic="0" letter_spacing="" color="#626262" subset="" inline=""]Insert your content here[/google_font]');
          }
        }, {
          text: 'Heading',
          onclick: function() {
            editor.insertContent('[heading tag="h2" align="center" color="#000" style="lines" color2="#000"]Insert your content here[/heading]');
          }
        }, {
          text: 'Highlight',
          onclick: function() {
            editor.insertContent('[highlight background="" color=""]Insert your content here[/highlight]');
          }
        }, {
          text: 'Hr',
          onclick: function() {
            editor.insertContent('[hr height="30" style="default" line="default" color="" themecolor="0"]');
          }
        }, {
          text: 'Icon',
          onclick: function() {
            editor.insertContent('[icon type="icon-lamp" color=""]');
          }
        }, {
          text: 'Icon Bar',
          onclick: function() {
            editor.insertContent('[icon_bar icon="icon-lamp" link="" target="_blank" size="" social=""]');
          }
        }, {
          text: 'Icon Block',
          onclick: function() {
            editor.insertContent('[icon_block icon="icon-lamp" align="" color="" size="25"]');
          }
        }, {
          text: 'Idea',
          onclick: function() {
            editor.insertContent('[idea]Insert your content here[/idea]');
          }
        }, {
          text: 'Image',
          onclick: function() {
            editor.insertContent('[image src="" size="" width="" height="" align="" stretch="0" border="0" margin_top="" margin_bottom="" link_image="" link="" target="" hover="" alt="" caption="" greyscale="" animate=""]');
          }
        }, {
          text: 'Popup',
          onclick: function() {
            editor.insertContent('[popup title="Title" padding="0" button="0"]Insert your popup content here[/popup]');
          }
        }, {
          text: 'Progress Icons',
          onclick: function() {
            editor.insertContent('[progress_icons icon="icon-heart-line" image="" count="5" active="3" background=""]');
          }
        }, {
          text: 'Share Box',
          onclick: function() {
            editor.insertContent('[share_box]');
          }
        }, {
          text: 'Table',
          onclick: function() {
            editor.insertContent('<table><thead><tr><th>Column 1 heading</th><th>Column 2 heading</th><th>Column 3 heading</th></tr></thead><tbody><tr><td>Row 1 col 1 content</td><td>Row 1 col 2 content</td><td>Row 1 col 3 content</td></tr><tr><td>Row 2 col 1 content</td><td>Row 2 col 2 content</td><td>Row 2 col 3 content</td></tr></tbody></table>');
          }
        }, {
          text: 'Tooltip',
          onclick: function() {
            editor.insertContent('[tooltip hint="Insert your hint here"]Insert your content here[/tooltip]');
          }
        }, {
          text: 'Tooltip Image',
          onclick: function() {
            editor.insertContent('[tooltip_image hint="Insert your hint here" image=""]Insert your content here[/tooltip_image]');
          }
        }, {
          text: 'Video',
          onclick: function() {
            editor.insertContent('[video_embed video="62954028" parameters="" mp4="" ogv="" placeholder="" html5_parameters="" width="700" height="400"]');
          }
        }, ]
      }, {
        text: 'Builder',
        menu: [{
          text: 'Accordion',
          onclick: function() {
            editor.insertContent('[accordion title="" open1st="0" openAll="0" style=""][accordion_item title="Title"]Content[/accordion_item][/accordion]');
          }
        }, {
          text: 'Article Box',
          onclick: function() {
            editor.insertContent('[article_box image="" slogan="" title="" link="" target="_blank" animate=""]');
          }
        }, {
          text: 'Before After',
          onclick: function() {
            editor.insertContent('[before_after image_before="" image_after=""]');
          }
        }, {
          text: 'Blog',
          onclick: function() {
            editor.insertContent('[blog count="2" style="grid" columns="2" title_tag="h2" category="" category_multi="" exclude_id="" filters="0" more="1" pagination="0" load_more="" greyscale="0" margin="" events="0"]');
          }
        }, {
          text: 'Blog News',
          onclick: function() {
            editor.insertContent('[blog_news title="" style="" count="5" category="" category_multi="" excerpt="0" link="" link_title=""]');
          }
        }, {
          text: 'Blog Teaser',
          onclick: function() {
            editor.insertContent('[blog_teaser title="" title_tag="h3" category="" category_multi="" margin="1"]');
          }
        }, {
          text: 'Blog Slider',
          onclick: function() {
            editor.insertContent('[blog_slider title="" count="5" category="" category_multi="" more="0" style="" navigation=""]');
          }
        }, {
          text: 'Call to Action',
          onclick: function() {
            editor.insertContent('[call_to_action title="" icon="icon-lamp" link="" button_title="" class="" target="_blank" animate=""]Insert your content here[/call_to_action]');
          }
        }, {
          text: 'Chart',
          onclick: function() {
            editor.insertContent('[chart title="" percent="" label="" icon="" image="" color="" line_width=""]');
          }
        }, {
          text: 'Clients',
          onclick: function() {
            editor.insertContent('[clients in_row="6" category="" orderby="menu_order" order="ASC" style="" greyscale="0"]');
          }
        }, {
          text: 'Clients Slider',
          onclick: function() {
            editor.insertContent('[clients_slider title="" category="" orderby="menu_order" order="ASC"]');
          }
        }, {
          text: 'Contact Box',
          onclick: function() {
            editor.insertContent('[contact_box title="" address="" telephone="" telephone_2="" fax="" email="" www="" image="" animate=""]');
          }
        }, {
          text: 'Countdown',
          onclick: function() {
            editor.insertContent('[countdown date="12/30/2016 12:00:00" timezone="0" show="dhms"]');
          }
        }, {
          text: 'Counter',
          onclick: function() {
            editor.insertContent('[counter icon="icon-lamp" color="" image="" number="44" prefix="" label="%" title="" type="vertical" animate=""]');
          }
        }, {
          text: 'Fancy Divider',
          onclick: function() {
            editor.insertContent('[fancy_divider style="" color_top="" color_bottom=""]');
          }
        }, {
          text: 'Fancy Heading',
          onclick: function() {
            editor.insertContent('[fancy_heading title="" h1="0" icon="icon-lamp" slogan="" style="icon" animate=""]Insert your content here[/fancy_heading]');
          }
        }, {
          text: 'FAQ',
          onclick: function() {
            editor.insertContent('[faq title="" open1st="0" openAll="0"][faq_item title="Title" number="1"]Content[/faq_item][/faq]');
          }
        }, {
          text: 'Feature Box',
          onclick: function() {
            editor.insertContent('[feature_box image="" title="" background="" link="" target=""]Insert your content here[/feature_box]');
          }
        }, {
          text: 'Feature List',
          onclick: function() {
            editor.insertContent('[feature_list columns="4"][item icon="icon-lamp" title="" link="" target="" animate=""][/feature_list]');
          }
        }, {
          text: 'Flat Box',
          onclick: function() {
            editor.insertContent('[flat_box icon="icon-lamp" icon_image="" background="" image="" title="" link="" target="" animate=""]Insert your content here[/flat_box]');
          }
        }, {
          text: 'Hover Box',
          onclick: function() {
            editor.insertContent('[hover_box image="" image_hover="" link="" target=""]');
          }
        }, {
          text: 'Hover Color',
          onclick: function() {
            editor.insertContent('[hover_color align="center" background="#2991D6" background_hover="#236A9C" border="" border_hover="" border_width="2px" padding="40px 30px" link="" target="" class="" style=""]Insert your content here[/hover_color]');
          }
        }, {
          text: 'How It Works',
          onclick: function() {
            editor.insertContent('[how_it_works image="" number="" title="" style="centered" border="1" link="" target="" animate=""]Insert your content here[/how_it_works]');
          }
        }, {
          text: 'Icon Box',
          onclick: function() {
            editor.insertContent('[icon_box title="" icon="icon-lamp" image="" icon_position="" border="0" link="" target="_blank" class="" animate=""]Insert your content here[/icon_box]');
          }
        }, {
          text: 'Info Box',
          onclick: function() {
            editor.insertContent('[info_box title="" image="" animate=""]Insert your content here[/info_box]');
          }
        }, {
          text: 'List',
          onclick: function() {
            editor.insertContent('[list icon="icon-lamp" image="" title="" link="" target="_blank" style="1" animate=""]Insert your content here[/list]');
          }
        }, {
          text: 'Map',
          onclick: function() {
            editor.insertContent('[map lat="" lng="" zoom="13" height="200" type="ROADMAP" controls="mapType" draggable="" border="0" icon="" color="" latlng="" title="" telephone="" email="" www="" style="box"]Insert your content here[/map]');
          }
        }, {
          text: 'Opening Hours',
          onclick: function() {
            editor.insertContent('[opening_hours title="" image="" animate=""]Insert your content here[/opening_hours]');
          }
        }, {
          text: 'Our Team',
          onclick: function() {
            editor.insertContent('[our_team image="" title="" subtitle="" email="" phone="" facebook="" twitter="" linkedin="" vcard="" blockquote="" style="vertical" link="" target="" animate=""]Insert your content here[/our_team]');
          }
        }, {
          text: 'Our Team List',
          onclick: function() {
            editor.insertContent('[our_team_list image="" title="" subtitle="" blockquote="" email="" phone="" facebook="" twitter="" linkedin="" vcard="" link="" target=""]Insert your content here[/our_team_list]');
          }
        }, {
          text: 'Photo Box',
          onclick: function() {
            editor.insertContent('[photo_box title="" image="" align="" link="" target="_blank" greyscale="0" animate=""]Insert your content here[/photo_box]');
          }
        }, {
          text: 'Portfolio',
          onclick: function() {
            editor.insertContent('[portfolio count="2" style="grid" columns="3" category="" category_multi="" orderby="date" order="DESC" exclude_id="" related="0" filters="0" pagination="0" load_more="0" greyscale="0"]');
          }
        }, {
          text: 'Portfolio Slider',
          onclick: function() {
            editor.insertContent('[portfolio_slider count="5" category="" category_multi="" orderby="date" order="DESC" arrows="" size="small"]');
          }
        }, {
          text: 'Pricing Item',
          onclick: function() {
            editor.insertContent('[pricing_item image="" title="" price="" currency="" currency_pos="left" period="" subtitle="" link_title="" icon="" link="" target="" featured="0" style="box" animate=""]<ul><li><strong>List</strong> item</li></ul>[/pricing_item]');
          }
        }, {
          text: 'Progress Bars',
          onclick: function() {
            editor.insertContent('[progress_bars title=""][bar title="Bar1" value="50" size="20" color=""][/progress_bars]');
          }
        }, {
          text: 'Promo Box',
          onclick: function() {
            editor.insertContent('[promo_box image="" title="" btn_text="" btn_link="" target="_blank" position="" border="0" animate=""]Insert your content here[/promo_box]');
          }
        }, {
          text: 'Quick Fact',
          onclick: function() {
            editor.insertContent('[quick_fact heading="" title="" number="" prefix="" label="%" align="center"  animate=""]Insert your content here[/quick_fact]');
          }
        }, {
          text: 'Shop Slider',
          onclick: function() {
            editor.insertContent('[shop_slider title="" count="5" show="best-selling" category="" orderby="date" order="DESC"]');
          }
        }, {
          text: 'Slider',
          onclick: function() {
            editor.insertContent('[slider style="" category="" orderby="date" order="DESC"]');
          }
        }, {
          text: 'Sliding Box',
          onclick: function() {
            editor.insertContent('[sliding_box image="" title="" link="" target="_blank" animate=""]');
          }
        }, {
          text: 'Story Box',
          onclick: function() {
            editor.insertContent('[story_box image="" style="horizontal" title="" link="" target="_blank" animate=""]Insert your content here[/story_box]');
          }
        }, {
          text: 'Tabs',
          onclick: function() {
            editor.insertContent('[tabs title="" type=""][tab title="Title"]Content[/tab][/tabs]');
          }
        }, {
          text: 'Testimonials',
          onclick: function() {
            editor.insertContent('[testimonials category="" orderby="menu_order" order="ASC" style="" hide_photos="0"]');
          }
        }, {
          text: 'Testimonials List',
          onclick: function() {
            editor.insertContent('[testimonials_list category="" orderby="menu_order" order="ASC" style=""]');
          }
        }, {
          text: 'Trailer Box',
          onclick: function() {
            editor.insertContent('[trailer_box image="" orientation="vertical" slogan="" title="" link="" target="_blank" style="" animate=""]');
          }
        }, {
          text: 'Zoom Box',
          onclick: function() {
            editor.insertContent('[zoom_box image="" bg_color="#000" content_image="" link="" target="_blank"]Insert your content here[/zoom_box]');
          }
        }, ]
      }]

    });

  });

})();
