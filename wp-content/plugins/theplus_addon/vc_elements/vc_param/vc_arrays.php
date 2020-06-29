<?php
/*------------------------------------contact form 7 get post---------------------------*/
if (is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
	$cf7           = get_posts('post_type="wpcf7_contact_form"&numberposts=-1');
	$contact_forms = array();
	if ($cf7) {
		$contact_forms['Select Form'] = '';
		foreach ($cf7 as $cform) {
			$contact_forms[$cform->post_title] = $cform->ID;
		}
	} else {
		$contact_forms[__('No contact forms found', 'pt_theplus')] = 0;
	}
}
/*------------------------------------contact form 7 get post---------------------------*/
$layout           = array(
    "type" => "dropdown",
    "heading" => __("Layout", "pt_theplus"),
    "param_name" => "layout",
    "description" => __("Display Post Layout...", "pt_theplus"),
    "admin_label" => true,
    "value" => array(
        'Grid Layout' => 'grid',
        'Masonry Layout' => 'masonry',
    ),
    "std" => 'grid'
);


$svg_attach      = array(
    'type' => 'attach_image',
    'heading' => __('Only Svg', 'pt_theplus'),
    'param_name' => 'svg_image',
    'value' => '',
    'description' => __('Select Only .svg File from media library.', 'pt_theplus'),
    'admin_label' => true,
    'dependency' => array(
        'element' => 'svg_icon',
        'value' => array(
            "img"
        )
    ),
    'group' => __('Icon Option', 'pt_theplus')
);
$svg_attach_icon = array(
    'type' => 'dropdown',
    'heading' => __('Only Svg', 'pt_theplus'),
    'param_name' => 'svg_d_icon',
    "value" => array(
        __("1. App", "pt_theplus") => "app.svg",
        __("2. Arrow", "pt_theplus") => "arrow.svg",
        __("3. Art", "pt_theplus") => "art.svg",
        __("4. Banknote", "pt_theplus") => "banknote.svg",
        __("5. Building", "pt_theplus") => "building.svg",
        __("6. Bulb-idea", "pt_theplus") => "bulb-idea.svg",
        __("7. Calendar", "pt_theplus") => "calendar.svg",
        __("8. Call", "pt_theplus") => "call.svg",
        __("9. Camera", "pt_theplus") => "camera.svg",
        __("10. Cart", "pt_theplus") => "cart.svg",
        __("11. Cd", "pt_theplus") => "cd.svg",
        __("12. Clip", "pt_theplus") => "clip.svg",
        __("13. Clock", "pt_theplus") => "clock.svg",
        __("14. Cloud", "pt_theplus") => "cloud.svg",
        __("15. Comment", "pt_theplus") => "comment.svg",
        __("16. Content-board", "pt_theplus") => "content-board.svg",
        __("17. Cup", "pt_theplus") => "cup.svg",
        __("18. Diamond", "pt_theplus") => "diamond.svg",
        __("19. Earth", "pt_theplus") => "earth.svg",
        __("20. Eye", "pt_theplus") => "eye.svg",
        __("21. Finger", "pt_theplus") => "finger.svg",
        __("22. Fingerprint", "pt_theplus") => "fingerprint.svg",
        __("23. Food", "pt_theplus") => "food.svg",
        __("24. Foundation", "pt_theplus") => "foundation.svg",
        __("25. Gear", "pt_theplus") => "gear.svg",
        __("26. Graphics-design", "pt_theplus") => "graphics-design.svg",
        __("27. Handshakeandshake", "pt_theplus") => "handshake.svg",
        __("28. Hard-disk", "pt_theplus") => "hard-disk.svg",
        __("29. Heart", "pt_theplus") => "heart.svg",
        __("30. Hook", "pt_theplus") => "hook.svg",
        __("31. Image", "pt_theplus") => "image.svg",
        __("32. Key", "pt_theplus") => "key.svg",
        __("33. Laptop", "pt_theplus") => "laptop.svg",
        __("34. Layers", "pt_theplus") => "layers.svg",
        __("35. List", "pt_theplus") => "list.svg",
        __("36. Location", "pt_theplus") => "location.svg",
        __("37. Loudspeaker", "pt_theplus") => "loudspeaker.svg",
        __("38. Mail", "pt_theplus") => "mail.svg",
        __("39. Map", "pt_theplus") => "map.svg",
        __("40. Mic", "pt_theplus") => "mic.svg",
        __("41. Mind", "pt_theplus") => "mind.svg",
        __("42. Mobile", "pt_theplus") => "mobile.svg",
        __("43. Mobile-comment", "pt_theplus") => "mobile-comment.svg",
        __("44. Music", "pt_theplus") => "music.svg",
        __("45. News", "pt_theplus") => "news.svg",
        __("46. Note", "pt_theplus") => "note.svg",
        __("47. Offer", "pt_theplus") => "offer.svg",
        __("48. Paperplane", "pt_theplus") => "paperplane.svg",
        __("49. Pendrive", "pt_theplus") => "pendrive.svg",
        __("50. Person", "pt_theplus") => "person.svg",
        __("51. Photography", "pt_theplus") => "photography.svg",
        __("52. Posisvg", "pt_theplus") => "posisvg.svg",
        __("53. Recycle", "pt_theplus") => "recycle.svg",
        __("54. Ruler", "pt_theplus") => "ruler.svg",
        __("55. Satelite", "pt_theplus") => "satelite.svg",
        __("56. Search", "pt_theplus") => "search.svg",
        __("57. Secure", "pt_theplus") => "secure.svg",
        __("58. Server", "pt_theplus") => "server.svg",
        __("59. Setting", "pt_theplus") => "setting.svg",
        __("60. Share", "pt_theplus") => "share.svg",
        __("61. Smiley", "pt_theplus") => "smiley.svg",
        __("62. Sound", "pt_theplus") => "sound.svg",
        __("63. Stack", "pt_theplus") => "stack.svg",
        __("64. Star", "pt_theplus") => "star.svg",
        __("65. Study", "pt_theplus") => "study.svg",
        __("66. Suitcase", "pt_theplus") => "suitcase.svg",
        __("67. Tag", "pt_theplus") => "tag.svg",
        __("68. Tempsvg", "pt_theplus") => "tempsvg.svg",
        __("69. Thumbsup", "pt_theplus") => "thumbsup.svg",
        __("70. Tick", "pt_theplus") => "tick.svg",
        __("71. Trash", "pt_theplus") => "trash.svg",
        __("72. Truck", "pt_theplus") => "truck.svg",
        __("73. Tv", "pt_theplus") => "tv.svg",
        __("74. User", "pt_theplus") => "user.svg",
        __("75. Video", "pt_theplus") => "video.svg",
        __("76. Video-production", "pt_theplus") => "video-production.svg",
        __("77. Wallet", "pt_theplus") => "wallet.svg"
    ),
    'description' => __('Select Only .', 'pt_theplus'),
    'admin_label' => true,
    'dependency' => array(
        'element' => 'svg_icon',
        'value' => array(
            "svg"
        )
    ),
    'group' => __('Icon Option', 'pt_theplus'),
    'std' => 'app.svg'
);
$svg_type        = array(
    "type" => "dropdown",
    "heading" => __("Select Style Image", "pt_theplus"),
    "param_name" => "svg_type",
    "value" => array(
        __("Delayed", "pt_theplus") => "delayed",
        __("Sync", "pt_theplus") => "sync",
        __("One-By-One", "pt_theplus") => "oneByOne",
        __("Scenario-Sync", "pt_theplus") => "scenario-sync"
    ),
    "description" => "",
    "std" => 'delayed',
    'admin_label' => true,
    'dependency' => array(
        'element' => 'svg_icon',
        'value' => array(
            "svg",
            "img"
        ),
        ''
    ),
    'group' => __('Icon Option', 'pt_theplus')
);
$svg_duration    = array(
    "type" => "textfield",
    "heading" => esc_html__("Duration", 'pt_theplus'),
    "param_name" => "duration",
    "value" => '30',
    'admin_label' => true,
    'dependency' => array(
        'element' => 'svg_icon',
        'value' => array(
            "svg",
            "img"
        ),
        ''
    ),
    'group' => __('Icon Option', 'pt_theplus')
);
$svg_width       = array(
    "type" => "textfield",
    "heading" => esc_html__("Max Width Svg", 'pt_theplus'),
    "param_name" => "max_width",
    "value" => '100px',
    "description" => __("Ex. 100px,200px,500px...", "pt_theplus"),
    'admin_label' => true,
    'dependency' => array(
        'element' => 'svg_icon',
        'value' => array(
            "svg",
            "img"
        ),
        ''
    ),
    'group' => __('Icon Option', 'pt_theplus')
);
$svg_border      = array(
    'type' => 'colorpicker',
    'heading' => __('Border/Stoke Color', 'pt_theplus'),
    'edit_field_class' => 'vc_col-xs-6',
    'param_name' => 'border_stroke_color',
    "value" => '#ff0000',
    'dependency' => array(
        'element' => 'svg_icon',
        'value' => array(
            "svg",
            "img"
        ),
        ''
    ),
    'group' => __('Icon Option', 'pt_theplus')
);