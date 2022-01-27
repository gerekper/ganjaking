(function($){
	"use strict";
	setUserSetting('hidetb', 1);
	tinymce.PluginManager.add('gt3_external_tinymce_plugins', function(editor, url){
		var property = 'color';
		var command = 'ForeColor';
		var index = 400;

		var conditions = {
		'selection' : function ( editor ){	/* something should be selected */
			return !editor.selection.isCollapsed();
		},
		'single_char_selected' : function ( editor ){	/* single character should be selected */
			return editor.selection.getContent().length == 1;
		},
		'sc_selection' : function ( editor ){	/* single or multiple shortcodes should be selected */
			var selection = editor.selection.getContent();
			return /^((\s|\n|\t|(&nbsp;)|(<p>)|(<\/p>)|(<br \/>))*((\[[^\]]+\](\[\/[^\]]+\])?)|(\[[^\]]+\].+\[\/[^\]]+\]))(\s|\n|\t|(&nbsp;)|(<p>)|(<\/p>)|(<br \/>))*)+$/.test( selection );
		},
		'sc_selection_or_nothing' : function ( editor ){	/* single shortcode or multiple shortcodes or nothing */
			var selection = editor.selection.getContent();
			return !selection.length || /^((\s|\n|\t|(&nbsp;)|(<p>)|(<\/p>)|(<br \/>))*((\[[^\]]+\](\[\/[^\]]+\])?)|(\[[^\]]+\].+\[\/[^\]]+\]))(\s|\n|\t|(&nbsp;)|(<p>)|(<\/p>)|(<br \/>))*)+$/.test( selection );
		},
		'list_selection' : function ( editor ){
			var selection = editor.selection.getSelectedBlocks();
			if (selection.length) {
				if ( (selection[0].tagName == 'LI' && selection[0].parentNode.tagName == 'UL') || selection[0].tagName == 'UL' ) {
					return true;
				}
			}
			return false;
		},
		'link_selection' : function ( editor ){
			var selection = editor.selection.getNode();
			if ( (selection.tagName == 'A')) {
				return true;
			}
			return false;
		}
	}

	var icons = [{text:"twitter-square",value:"fa fa-twitter-square",icon:" fa fa-twitter-square"},{text:"facebook-square",value:"fa fa-facebook-square",icon:" fa fa-facebook-square"},{text:"linkedin-square",value:"fa fa-linkedin-square",icon:" fa fa-linkedin-square"},{text:"github-square",value:"fa fa-github-square",icon:" fa fa-github-square"},{text:"twitter",value:"fa fa-twitter",icon:" fa fa-twitter"},{text:"facebook",value:"fa fa-facebook",icon:" fa fa-facebook"},{text:"github",value:"fa fa-github",icon:" fa fa-github"},{text:"pinterest",value:"fa fa-pinterest",icon:" fa fa-pinterest"},{text:"pinterest-square",value:"fa fa-pinterest-square",icon:" fa fa-pinterest-square"},{text:"google-plus-square",value:"fa fa-google-plus-square",icon:" fa fa-google-plus-square"},{text:"google-plus",value:"fa fa-google-plus",icon:" fa fa-google-plus"},{text:"linkedin",value:"fa fa-linkedin",icon:" fa fa-linkedin"},{text:"github-alt",value:"fa fa-github-alt",icon:" fa fa-github-alt"},{text:"bitcoin",value:"fa fa-bitcoin",icon:" fa fa-bitcoin"},{text:"youtube-square",value:"fa fa-youtube-square",icon:" fa fa-youtube-square"},{text:"youtube",value:"fa fa-youtube",icon:" fa fa-youtube"},{text:"youtube-play",value:"fa fa-youtube-play",icon:" fa fa-youtube-play"},{text:"dropbox",value:"fa fa-dropbox",icon:" fa fa-dropbox"},{text:"stack-overflow",value:"fa fa-stack-overflow",icon:" fa fa-stack-overflow"},{text:"instagram",value:"fa fa-instagram",icon:" fa fa-instagram"},{text:"bitbucket",value:"fa fa-bitbucket",icon:" fa fa-bitbucket"},{text:"bitbucket-square",value:"fa fa-bitbucket-square",icon:" fa fa-bitbucket-square"},{text:"tumblr",value:"fa fa-tumblr",icon:" fa fa-tumblr"},{text:"tumblr-square",value:"fa fa-tumblr-square",icon:" fa fa-tumblr-square"},{text:"dribbble",value:"fa fa-dribbble",icon:" fa fa-dribbble"},{text:"skype",value:"fa fa-skype",icon:" fa fa-skype"},{text:"foursquare",value:"fa fa-foursquare",icon:" fa fa-foursquare"},{text:"trello",value:"fa fa-trello",icon:" fa fa-trello"},{text:"vimeo-square",value:"fa fa-vimeo-square",icon:" fa fa-vimeo-square"},{text:"slack",value:"fa fa-slack",icon:" fa fa-slack"},{text:"yahoo",value:"fa fa-yahoo",icon:" fa fa-yahoo"},{text:"google",value:"fa fa-google",icon:" fa fa-google"},{text:"reddit",value:"fa fa-reddit",icon:" fa fa-reddit"},{text:"reddit-square",value:"fa fa-reddit-square",icon:" fa fa-reddit-square"},{text:"behance",value:"fa fa-behance",icon:" fa fa-behance"},{text:"behance-square",value:"fa fa-behance-square",icon:" fa fa-behance-square"},{text:"steam",value:"fa fa-steam",icon:" fa fa-steam"},{text:"steam-square",value:"fa fa-steam-square",icon:" fa fa-steam-square"},{text:"spotify",value:"fa fa-spotify",icon:" fa fa-spotify"},{text:"soundcloud",value:"fa fa-soundcloud",icon:" fa fa-soundcloud"},{text:"vine",value:"fa fa-vine",icon:" fa fa-vine"},{text:"codepen",value:"fa fa-codepen",icon:" fa fa-codepen"},{text:"jsfiddle",value:"fa fa-jsfiddle",icon:" fa fa-jsfiddle"},{text:"git-square",value:"fa fa-git-square",icon:" fa fa-git-square"},{text:"git",value:"fa fa-git",icon:" fa fa-git"},{text:"share-alt",value:"fa fa-share-alt",icon:" fa fa-share-alt"},{text:"share-alt-square",value:"fa fa-share-alt-square",icon:" fa fa-share-alt-square"},{text:"twitch",value:"fa fa-twitch",icon:" fa fa-twitch"},{text:"yelp",value:"fa fa-yelp",icon:" fa fa-yelp"},{text:"paypal",value:"fa fa-paypal",icon:" fa fa-paypal"},{text:"cc-visa",value:"fa fa-cc-visa",icon:" fa fa-cc-visa"},{text:"cc-mastercard",value:"fa fa-cc-mastercard",icon:" fa fa-cc-mastercard"},{text:"cc-paypal",value:"fa fa-cc-paypal",icon:" fa fa-cc-paypal"},{text:"cc-stripe",value:"fa fa-cc-stripe",icon:" fa fa-cc-stripe"},{text:"facebook-official",value:"fa fa-facebook-official",icon:" fa fa-facebook-official"},{text:"pinterest-p",value:"fa fa-pinterest-p",icon:" fa fa-pinterest-p"},{text:"whatsapp",value:"fa fa-whatsapp",icon:" fa fa-whatsapp"},{text:"medium",value:"fa fa-medium",icon:" fa fa-medium"},{text:"cc-jcb",value:"fa fa-cc-jcb",icon:" fa fa-cc-jcb"},{text:"tripadvisor",value:"fa fa-tripadvisor",icon:" fa fa-tripadvisor"},{text:"wikipedia-w",value:"fa fa-wikipedia-w",icon:" fa fa-wikipedia-w"},{text:"500px",value:"fa fa-500px",icon:" fa fa-500px"},{text:"vimeo",value:"fa fa-vimeo",icon:" fa fa-vimeo"},{text:"reddit-alien",value:"fa fa-reddit-alien",icon:" fa fa-reddit-alien"},{text:"snapchat",value:"fa fa-snapchat",icon:" fa fa-snapchat"},{text:"snapchat-ghost",value:"fa fa-snapchat-ghost",icon:" fa fa-snapchat-ghost"},{text:"snapchat-square",value:"fa fa-snapchat-square",icon:" fa fa-snapchat-square"},{text:"google-plus-circle",value:"fa fa-google-plus-circle",icon:" fa fa-google-plus-circle"},{text:"telegram",value:"fa fa-telegram",icon:" fa fa-telegram"},
            {text:"glass",value:"fa fa-glass",icon:" fa fa-glass"},{text:"music",value:"fa fa-music",icon:" fa fa-music"},{text:"search",value:"fa fa-search",icon:" fa fa-search"},{text:"envelope-o",value:"fa fa-envelope-o",icon:" fa fa-envelope-o"},{text:"heart",value:"fa fa-heart",icon:" fa fa-heart"},{text:"star",value:"fa fa-star",icon:" fa fa-star"},{text:"star-o",value:"fa fa-star-o",icon:" fa fa-star-o"},{text:"user",value:"fa fa-user",icon:" fa fa-user"},{text:"film",value:"fa fa-film",icon:" fa fa-film"},{text:"th-large",value:"fa fa-th-large",icon:" fa fa-th-large"},{text:"th",value:"fa fa-th",icon:" fa fa-th"},{text:"th-list",value:"fa fa-th-list",icon:" fa fa-th-list"},{text:"check",value:"fa fa-check",icon:" fa fa-check"},{text:"remove",value:"fa fa-remove",icon:" fa fa-remove"},{text:"close",value:"fa fa-close",icon:" fa fa-close"},{text:"times",value:"fa fa-times",icon:" fa fa-times"},{text:"search-plus",value:"fa fa-search-plus",icon:" fa fa-search-plus"},{text:"search-minus",value:"fa fa-search-minus",icon:" fa fa-search-minus"},{text:"power-off",value:"fa fa-power-off",icon:" fa fa-power-off"},{text:"signal",value:"fa fa-signal",icon:" fa fa-signal"},{text:"gear",value:"fa fa-gear",icon:" fa fa-gear"},{text:"cog",value:"fa fa-cog",icon:" fa fa-cog"},{text:"trash-o",value:"fa fa-trash-o",icon:" fa fa-trash-o"},{text:"home",value:"fa fa-home",icon:" fa fa-home"},{text:"file-o",value:"fa fa-file-o",icon:" fa fa-file-o"},{text:"clock-o",value:"fa fa-clock-o",icon:" fa fa-clock-o"},{text:"road",value:"fa fa-road",icon:" fa fa-road"},{text:"download",value:"fa fa-download",icon:" fa fa-download"},{text:"arrow-circle-o-down",value:"fa fa-arrow-circle-o-down",icon:" fa fa-arrow-circle-o-down"},{text:"arrow-circle-o-up",value:"fa fa-arrow-circle-o-up",icon:" fa fa-arrow-circle-o-up"},{text:"inbox",value:"fa fa-inbox",icon:" fa fa-inbox"},{text:"play-circle-o",value:"fa fa-play-circle-o",icon:" fa fa-play-circle-o"},{text:"rotate-right",value:"fa fa-rotate-right",icon:" fa fa-rotate-right"},{text:"repeat",value:"fa fa-repeat",icon:" fa fa-repeat"},{text:"refresh",value:"fa fa-refresh",icon:" fa fa-refresh"},{text:"list-alt",value:"fa fa-list-alt",icon:" fa fa-list-alt"},{text:"lock",value:"fa fa-lock",icon:" fa fa-lock"},{text:"flag",value:"fa fa-flag",icon:" fa fa-flag"},{text:"headphones",value:"fa fa-headphones",icon:" fa fa-headphones"},{text:"volume-off",value:"fa fa-volume-off",icon:" fa fa-volume-off"},{text:"volume-down",value:"fa fa-volume-down",icon:" fa fa-volume-down"},{text:"volume-up",value:"fa fa-volume-up",icon:" fa fa-volume-up"},{text:"qrcode",value:"fa fa-qrcode",icon:" fa fa-qrcode"},{text:"barcode",value:"fa fa-barcode",icon:" fa fa-barcode"},{text:"tag",value:"fa fa-tag",icon:" fa fa-tag"},{text:"tags",value:"fa fa-tags",icon:" fa fa-tags"},{text:"book",value:"fa fa-book",icon:" fa fa-book"},{text:"bookmark",value:"fa fa-bookmark",icon:" fa fa-bookmark"},{text:"print",value:"fa fa-print",icon:" fa fa-print"},{text:"camera",value:"fa fa-camera",icon:" fa fa-camera"},{text:"font",value:"fa fa-font",icon:" fa fa-font"},{text:"bold",value:"fa fa-bold",icon:" fa fa-bold"},{text:"italic",value:"fa fa-italic",icon:" fa fa-italic"},{text:"text-height",value:"fa fa-text-height",icon:" fa fa-text-height"},{text:"text-width",value:"fa fa-text-width",icon:" fa fa-text-width"},{text:"align-left",value:"fa fa-align-left",icon:" fa fa-align-left"},{text:"align-center",value:"fa fa-align-center",icon:" fa fa-align-center"},{text:"align-right",value:"fa fa-align-right",icon:" fa fa-align-right"},{text:"align-justify",value:"fa fa-align-justify",icon:" fa fa-align-justify"},{text:"list",value:"fa fa-list",icon:" fa fa-list"},{text:"dedent",value:"fa fa-dedent",icon:" fa fa-dedent"},{text:"outdent",value:"fa fa-outdent",icon:" fa fa-outdent"},{text:"indent",value:"fa fa-indent",icon:" fa fa-indent"},{text:"video-camera",value:"fa fa-video-camera",icon:" fa fa-video-camera"},{text:"photo",value:"fa fa-photo",icon:" fa fa-photo"},{text:"image",value:"fa fa-image",icon:" fa fa-image"},{text:"picture-o",value:"fa fa-picture-o",icon:" fa fa-picture-o"},{text:"pencil",value:"fa fa-pencil",icon:" fa fa-pencil"},{text:"map-marker",value:"fa fa-map-marker",icon:" fa fa-map-marker"},{text:"adjust",value:"fa fa-adjust",icon:" fa fa-adjust"},{text:"tint",value:"fa fa-tint",icon:" fa fa-tint"},{text:"edit",value:"fa fa-edit",icon:" fa fa-edit"},{text:"pencil-square-o",value:"fa fa-pencil-square-o",icon:" fa fa-pencil-square-o"},{text:"share-square-o",value:"fa fa-share-square-o",icon:" fa fa-share-square-o"},{text:"check-square-o",value:"fa fa-check-square-o",icon:" fa fa-check-square-o"},{text:"arrows",value:"fa fa-arrows",icon:" fa fa-arrows"},{text:"step-backward",value:"fa fa-step-backward",icon:" fa fa-step-backward"},{text:"fast-backward",value:"fa fa-fast-backward",icon:" fa fa-fast-backward"},{text:"backward",value:"fa fa-backward",icon:" fa fa-backward"},{text:"play",value:"fa fa-play",icon:" fa fa-play"},{text:"pause",value:"fa fa-pause",icon:" fa fa-pause"},{text:"stop",value:"fa fa-stop",icon:" fa fa-stop"},{text:"forward",value:"fa fa-forward",icon:" fa fa-forward"},{text:"fast-forward",value:"fa fa-fast-forward",icon:" fa fa-fast-forward"},{text:"step-forward",value:"fa fa-step-forward",icon:" fa fa-step-forward"},{text:"eject",value:"fa fa-eject",icon:" fa fa-eject"},{text:"chevron-left",value:"fa fa-chevron-left",icon:" fa fa-chevron-left"},{text:"chevron-right",value:"fa fa-chevron-right",icon:" fa fa-chevron-right"},{text:"plus-circle",value:"fa fa-plus-circle",icon:" fa fa-plus-circle"},{text:"minus-circle",value:"fa fa-minus-circle",icon:" fa fa-minus-circle"},{text:"times-circle",value:"fa fa-times-circle",icon:" fa fa-times-circle"},{text:"check-circle",value:"fa fa-check-circle",icon:" fa fa-check-circle"},{text:"question-circle",value:"fa fa-question-circle",icon:" fa fa-question-circle"},{text:"info-circle",value:"fa fa-info-circle",icon:" fa fa-info-circle"},{text:"crosshairs",value:"fa fa-crosshairs",icon:" fa fa-crosshairs"},{text:"times-circle-o",value:"fa fa-times-circle-o",icon:" fa fa-times-circle-o"},{text:"check-circle-o",value:"fa fa-check-circle-o",icon:" fa fa-check-circle-o"},{text:"ban",value:"fa fa-ban",icon:" fa fa-ban"},{text:"arrow-left",value:"fa fa-arrow-left",icon:" fa fa-arrow-left"},{text:"arrow-right",value:"fa fa-arrow-right",icon:" fa fa-arrow-right"},{text:"arrow-up",value:"fa fa-arrow-up",icon:" fa fa-arrow-up"},{text:"arrow-down",value:"fa fa-arrow-down",icon:" fa fa-arrow-down"},{text:"mail-forward",value:"fa fa-mail-forward",icon:" fa fa-mail-forward"},{text:"share",value:"fa fa-share",icon:" fa fa-share"},{text:"expand",value:"fa fa-expand",icon:" fa fa-expand"},{text:"compress",value:"fa fa-compress",icon:" fa fa-compress"},{text:"plus",value:"fa fa-plus",icon:" fa fa-plus"},{text:"minus",value:"fa fa-minus",icon:" fa fa-minus"},{text:"asterisk",value:"fa fa-asterisk",icon:" fa fa-asterisk"},{text:"exclamation-circle",value:"fa fa-exclamation-circle",icon:" fa fa-exclamation-circle"},{text:"gift",value:"fa fa-gift",icon:" fa fa-gift"},{text:"leaf",value:"fa fa-leaf",icon:" fa fa-leaf"},{text:"fire",value:"fa fa-fire",icon:" fa fa-fire"},{text:"eye",value:"fa fa-eye",icon:" fa fa-eye"},{text:"eye-slash",value:"fa fa-eye-slash",icon:" fa fa-eye-slash"},{text:"warning",value:"fa fa-warning",icon:" fa fa-warning"},{text:"exclamation-triangle",value:"fa fa-exclamation-triangle",icon:" fa fa-exclamation-triangle"},{text:"plane",value:"fa fa-plane",icon:" fa fa-plane"},{text:"calendar",value:"fa fa-calendar",icon:" fa fa-calendar"},{text:"random",value:"fa fa-random",icon:" fa fa-random"},{text:"comment",value:"fa fa-comment",icon:" fa fa-comment"},{text:"magnet",value:"fa fa-magnet",icon:" fa fa-magnet"},{text:"chevron-up",value:"fa fa-chevron-up",icon:" fa fa-chevron-up"},{text:"chevron-down",value:"fa fa-chevron-down",icon:" fa fa-chevron-down"},{text:"retweet",value:"fa fa-retweet",icon:" fa fa-retweet"},{text:"shopping-cart",value:"fa fa-shopping-cart",icon:" fa fa-shopping-cart"},{text:"folder",value:"fa fa-folder",icon:" fa fa-folder"},{text:"folder-open",value:"fa fa-folder-open",icon:" fa fa-folder-open"},{text:"arrows-v",value:"fa fa-arrows-v",icon:" fa fa-arrows-v"},{text:"arrows-h",value:"fa fa-arrows-h",icon:" fa fa-arrows-h"},{text:"bar-chart-o",value:"fa fa-bar-chart-o",icon:" fa fa-bar-chart-o"},{text:"bar-chart",value:"fa fa-bar-chart",icon:" fa fa-bar-chart"},{text:"camera-retro",value:"fa fa-camera-retro",icon:" fa fa-camera-retro"},{text:"key",value:"fa fa-key",icon:" fa fa-key"},{text:"gears",value:"fa fa-gears",icon:" fa fa-gears"},{text:"cogs",value:"fa fa-cogs",icon:" fa fa-cogs"},{text:"comments",value:"fa fa-comments",icon:" fa fa-comments"},{text:"thumbs-o-up",value:"fa fa-thumbs-o-up",icon:" fa fa-thumbs-o-up"},{text:"thumbs-o-down",value:"fa fa-thumbs-o-down",icon:" fa fa-thumbs-o-down"},{text:"star-half",value:"fa fa-star-half",icon:" fa fa-star-half"},{text:"heart-o",value:"fa fa-heart-o",icon:" fa fa-heart-o"},{text:"sign-out",value:"fa fa-sign-out",icon:" fa fa-sign-out"},{text:"thumb-tack",value:"fa fa-thumb-tack",icon:" fa fa-thumb-tack"},{text:"external-link",value:"fa fa-external-link",icon:" fa fa-external-link"},{text:"sign-in",value:"fa fa-sign-in",icon:" fa fa-sign-in"},{text:"trophy",value:"fa fa-trophy",icon:" fa fa-trophy"},{text:"upload",value:"fa fa-upload",icon:" fa fa-upload"},{text:"lemon-o",value:"fa fa-lemon-o",icon:" fa fa-lemon-o"},{text:"phone",value:"fa fa-phone",icon:" fa fa-phone"},{text:"square-o",value:"fa fa-square-o",icon:" fa fa-square-o"},{text:"bookmark-o",value:"fa fa-bookmark-o",icon:" fa fa-bookmark-o"},{text:"phone-square",value:"fa fa-phone-square",icon:" fa fa-phone-square"},{text:"facebook-f",value:"fa fa-facebook-f",icon:" fa fa-facebook-f"},{text:"unlock",value:"fa fa-unlock",icon:" fa fa-unlock"},{text:"credit-card",value:"fa fa-credit-card",icon:" fa fa-credit-card"},{text:"feed",value:"fa fa-feed",icon:" fa fa-feed"},{text:"rss",value:"fa fa-rss",icon:" fa fa-rss"},{text:"hdd-o",value:"fa fa-hdd-o",icon:" fa fa-hdd-o"},{text:"bullhorn",value:"fa fa-bullhorn",icon:" fa fa-bullhorn"},{text:"bell",value:"fa fa-bell",icon:" fa fa-bell"},{text:"certificate",value:"fa fa-certificate",icon:" fa fa-certificate"},{text:"hand-o-right",value:"fa fa-hand-o-right",icon:" fa fa-hand-o-right"},{text:"hand-o-left",value:"fa fa-hand-o-left",icon:" fa fa-hand-o-left"},{text:"hand-o-up",value:"fa fa-hand-o-up",icon:" fa fa-hand-o-up"},{text:"hand-o-down",value:"fa fa-hand-o-down",icon:" fa fa-hand-o-down"},{text:"arrow-circle-left",value:"fa fa-arrow-circle-left",icon:" fa fa-arrow-circle-left"},{text:"arrow-circle-right",value:"fa fa-arrow-circle-right",icon:" fa fa-arrow-circle-right"},{text:"arrow-circle-up",value:"fa fa-arrow-circle-up",icon:" fa fa-arrow-circle-up"},{text:"arrow-circle-down",value:"fa fa-arrow-circle-down",icon:" fa fa-arrow-circle-down"},{text:"globe",value:"fa fa-globe",icon:" fa fa-globe"},{text:"wrench",value:"fa fa-wrench",icon:" fa fa-wrench"},{text:"tasks",value:"fa fa-tasks",icon:" fa fa-tasks"},{text:"filter",value:"fa fa-filter",icon:" fa fa-filter"},{text:"briefcase",value:"fa fa-briefcase",icon:" fa fa-briefcase"},{text:"arrows-alt",value:"fa fa-arrows-alt",icon:" fa fa-arrows-alt"},{text:"group",value:"fa fa-group",icon:" fa fa-group"},{text:"users",value:"fa fa-users",icon:" fa fa-users"},{text:"chain",value:"fa fa-chain",icon:" fa fa-chain"},{text:"link",value:"fa fa-link",icon:" fa fa-link"},{text:"cloud",value:"fa fa-cloud",icon:" fa fa-cloud"},{text:"flask",value:"fa fa-flask",icon:" fa fa-flask"},{text:"cut",value:"fa fa-cut",icon:" fa fa-cut"},{text:"scissors",value:"fa fa-scissors",icon:" fa fa-scissors"},{text:"copy",value:"fa fa-copy",icon:" fa fa-copy"},{text:"files-o",value:"fa fa-files-o",icon:" fa fa-files-o"},{text:"paperclip",value:"fa fa-paperclip",icon:" fa fa-paperclip"},{text:"save",value:"fa fa-save",icon:" fa fa-save"},{text:"floppy-o",value:"fa fa-floppy-o",icon:" fa fa-floppy-o"},{text:"square",value:"fa fa-square",icon:" fa fa-square"},{text:"navicon",value:"fa fa-navicon",icon:" fa fa-navicon"},{text:"reorder",value:"fa fa-reorder",icon:" fa fa-reorder"},{text:"bars",value:"fa fa-bars",icon:" fa fa-bars"},{text:"list-ul",value:"fa fa-list-ul",icon:" fa fa-list-ul"},{text:"list-ol",value:"fa fa-list-ol",icon:" fa fa-list-ol"},{text:"strikethrough",value:"fa fa-strikethrough",icon:" fa fa-strikethrough"},{text:"underline",value:"fa fa-underline",icon:" fa fa-underline"},{text:"table",value:"fa fa-table",icon:" fa fa-table"},{text:"magic",value:"fa fa-magic",icon:" fa fa-magic"},{text:"truck",value:"fa fa-truck",icon:" fa fa-truck"},{text:"money",value:"fa fa-money",icon:" fa fa-money"},{text:"caret-down",value:"fa fa-caret-down",icon:" fa fa-caret-down"},{text:"caret-up",value:"fa fa-caret-up",icon:" fa fa-caret-up"},{text:"caret-left",value:"fa fa-caret-left",icon:" fa fa-caret-left"},{text:"caret-right",value:"fa fa-caret-right",icon:" fa fa-caret-right"},{text:"columns",value:"fa fa-columns",icon:" fa fa-columns"},{text:"unsorted",value:"fa fa-unsorted",icon:" fa fa-unsorted"},{text:"sort",value:"fa fa-sort",icon:" fa fa-sort"},{text:"sort-down",value:"fa fa-sort-down",icon:" fa fa-sort-down"},{text:"sort-desc",value:"fa fa-sort-desc",icon:" fa fa-sort-desc"},{text:"sort-up",value:"fa fa-sort-up",icon:" fa fa-sort-up"},{text:"sort-asc",value:"fa fa-sort-asc",icon:" fa fa-sort-asc"},{text:"envelope",value:"fa fa-envelope",icon:" fa fa-envelope"},{text:"rotate-left",value:"fa fa-rotate-left",icon:" fa fa-rotate-left"},{text:"undo",value:"fa fa-undo",icon:" fa fa-undo"},{text:"legal",value:"fa fa-legal",icon:" fa fa-legal"},{text:"gavel",value:"fa fa-gavel",icon:" fa fa-gavel"},{text:"dashboard",value:"fa fa-dashboard",icon:" fa fa-dashboard"},{text:"tachometer",value:"fa fa-tachometer",icon:" fa fa-tachometer"},{text:"comment-o",value:"fa fa-comment-o",icon:" fa fa-comment-o"},{text:"comments-o",value:"fa fa-comments-o",icon:" fa fa-comments-o"},{text:"flash",value:"fa fa-flash",icon:" fa fa-flash"},{text:"bolt",value:"fa fa-bolt",icon:" fa fa-bolt"},{text:"sitemap",value:"fa fa-sitemap",icon:" fa fa-sitemap"},{text:"umbrella",value:"fa fa-umbrella",icon:" fa fa-umbrella"},{text:"paste",value:"fa fa-paste",icon:" fa fa-paste"},{text:"clipboard",value:"fa fa-clipboard",icon:" fa fa-clipboard"},{text:"lightbulb-o",value:"fa fa-lightbulb-o",icon:" fa fa-lightbulb-o"},{text:"exchange",value:"fa fa-exchange",icon:" fa fa-exchange"},{text:"cloud-download",value:"fa fa-cloud-download",icon:" fa fa-cloud-download"},{text:"cloud-upload",value:"fa fa-cloud-upload",icon:" fa fa-cloud-upload"},{text:"user-md",value:"fa fa-user-md",icon:" fa fa-user-md"},{text:"stethoscope",value:"fa fa-stethoscope",icon:" fa fa-stethoscope"},{text:"suitcase",value:"fa fa-suitcase",icon:" fa fa-suitcase"},{text:"bell-o",value:"fa fa-bell-o",icon:" fa fa-bell-o"},{text:"coffee",value:"fa fa-coffee",icon:" fa fa-coffee"},{text:"cutlery",value:"fa fa-cutlery",icon:" fa fa-cutlery"},{text:"file-text-o",value:"fa fa-file-text-o",icon:" fa fa-file-text-o"},{text:"building-o",value:"fa fa-building-o",icon:" fa fa-building-o"},{text:"hospital-o",value:"fa fa-hospital-o",icon:" fa fa-hospital-o"},{text:"ambulance",value:"fa fa-ambulance",icon:" fa fa-ambulance"},{text:"medkit",value:"fa fa-medkit",icon:" fa fa-medkit"},{text:"fighter-jet",value:"fa fa-fighter-jet",icon:" fa fa-fighter-jet"},{text:"beer",value:"fa fa-beer",icon:" fa fa-beer"},{text:"h-square",value:"fa fa-h-square",icon:" fa fa-h-square"},{text:"plus-square",value:"fa fa-plus-square",icon:" fa fa-plus-square"},{text:"angle-double-left",value:"fa fa-angle-double-left",icon:" fa fa-angle-double-left"},{text:"angle-double-right",value:"fa fa-angle-double-right",icon:" fa fa-angle-double-right"},{text:"angle-double-up",value:"fa fa-angle-double-up",icon:" fa fa-angle-double-up"},{text:"angle-double-down",value:"fa fa-angle-double-down",icon:" fa fa-angle-double-down"},{text:"angle-left",value:"fa fa-angle-left",icon:" fa fa-angle-left"},{text:"angle-right",value:"fa fa-angle-right",icon:" fa fa-angle-right"},{text:"angle-up",value:"fa fa-angle-up",icon:" fa fa-angle-up"},{text:"angle-down",value:"fa fa-angle-down",icon:" fa fa-angle-down"},{text:"desktop",value:"fa fa-desktop",icon:" fa fa-desktop"},{text:"laptop",value:"fa fa-laptop",icon:" fa fa-laptop"},{text:"tablet",value:"fa fa-tablet",icon:" fa fa-tablet"},{text:"mobile-phone",value:"fa fa-mobile-phone",icon:" fa fa-mobile-phone"},{text:"mobile",value:"fa fa-mobile",icon:" fa fa-mobile"},{text:"circle-o",value:"fa fa-circle-o",icon:" fa fa-circle-o"},{text:"quote-left",value:"fa fa-quote-left",icon:" fa fa-quote-left"},{text:"quote-right",value:"fa fa-quote-right",icon:" fa fa-quote-right"},{text:"spinner",value:"fa fa-spinner",icon:" fa fa-spinner"},{text:"circle",value:"fa fa-circle",icon:" fa fa-circle"},{text:"mail-reply",value:"fa fa-mail-reply",icon:" fa fa-mail-reply"},{text:"reply",value:"fa fa-reply",icon:" fa fa-reply"},{text:"folder-o",value:"fa fa-folder-o",icon:" fa fa-folder-o"},{text:"folder-open-o",value:"fa fa-folder-open-o",icon:" fa fa-folder-open-o"},{text:"smile-o",value:"fa fa-smile-o",icon:" fa fa-smile-o"},{text:"frown-o",value:"fa fa-frown-o",icon:" fa fa-frown-o"},{text:"meh-o",value:"fa fa-meh-o",icon:" fa fa-meh-o"},{text:"gamepad",value:"fa fa-gamepad",icon:" fa fa-gamepad"},{text:"keyboard-o",value:"fa fa-keyboard-o",icon:" fa fa-keyboard-o"},{text:"flag-o",value:"fa fa-flag-o",icon:" fa fa-flag-o"},{text:"flag-checkered",value:"fa fa-flag-checkered",icon:" fa fa-flag-checkered"},{text:"terminal",value:"fa fa-terminal",icon:" fa fa-terminal"},{text:"code",value:"fa fa-code",icon:" fa fa-code"},{text:"mail-reply-all",value:"fa fa-mail-reply-all",icon:" fa fa-mail-reply-all"},{text:"reply-all",value:"fa fa-reply-all",icon:" fa fa-reply-all"},{text:"star-half-empty",value:"fa fa-star-half-empty",icon:" fa fa-star-half-empty"},{text:"star-half-full",value:"fa fa-star-half-full",icon:" fa fa-star-half-full"},{text:"star-half-o",value:"fa fa-star-half-o",icon:" fa fa-star-half-o"},{text:"location-arrow",value:"fa fa-location-arrow",icon:" fa fa-location-arrow"},{text:"crop",value:"fa fa-crop",icon:" fa fa-crop"},{text:"code-fork",value:"fa fa-code-fork",icon:" fa fa-code-fork"},{text:"unlink",value:"fa fa-unlink",icon:" fa fa-unlink"},{text:"chain-broken",value:"fa fa-chain-broken",icon:" fa fa-chain-broken"},{text:"question",value:"fa fa-question",icon:" fa fa-question"},{text:"info",value:"fa fa-info",icon:" fa fa-info"},{text:"exclamation",value:"fa fa-exclamation",icon:" fa fa-exclamation"},{text:"superscript",value:"fa fa-superscript",icon:" fa fa-superscript"},{text:"subscript",value:"fa fa-subscript",icon:" fa fa-subscript"},{text:"eraser",value:"fa fa-eraser",icon:" fa fa-eraser"},{text:"puzzle-piece",value:"fa fa-puzzle-piece",icon:" fa fa-puzzle-piece"},{text:"microphone",value:"fa fa-microphone",icon:" fa fa-microphone"},{text:"microphone-slash",value:"fa fa-microphone-slash",icon:" fa fa-microphone-slash"},{text:"shield",value:"fa fa-shield",icon:" fa fa-shield"},{text:"calendar-o",value:"fa fa-calendar-o",icon:" fa fa-calendar-o"},{text:"fire-extinguisher",value:"fa fa-fire-extinguisher",icon:" fa fa-fire-extinguisher"},{text:"rocket",value:"fa fa-rocket",icon:" fa fa-rocket"},{text:"maxcdn",value:"fa fa-maxcdn",icon:" fa fa-maxcdn"},{text:"chevron-circle-left",value:"fa fa-chevron-circle-left",icon:" fa fa-chevron-circle-left"},{text:"chevron-circle-right",value:"fa fa-chevron-circle-right",icon:" fa fa-chevron-circle-right"},{text:"chevron-circle-up",value:"fa fa-chevron-circle-up",icon:" fa fa-chevron-circle-up"},{text:"chevron-circle-down",value:"fa fa-chevron-circle-down",icon:" fa fa-chevron-circle-down"},{text:"html5",value:"fa fa-html5",icon:" fa fa-html5"},{text:"css3",value:"fa fa-css3",icon:" fa fa-css3"},{text:"anchor",value:"fa fa-anchor",icon:" fa fa-anchor"},{text:"unlock-alt",value:"fa fa-unlock-alt",icon:" fa fa-unlock-alt"},{text:"bullseye",value:"fa fa-bullseye",icon:" fa fa-bullseye"},{text:"ellipsis-h",value:"fa fa-ellipsis-h",icon:" fa fa-ellipsis-h"},{text:"ellipsis-v",value:"fa fa-ellipsis-v",icon:" fa fa-ellipsis-v"},{text:"rss-square",value:"fa fa-rss-square",icon:" fa fa-rss-square"},{text:"play-circle",value:"fa fa-play-circle",icon:" fa fa-play-circle"},{text:"ticket",value:"fa fa-ticket",icon:" fa fa-ticket"},{text:"minus-square",value:"fa fa-minus-square",icon:" fa fa-minus-square"},{text:"minus-square-o",value:"fa fa-minus-square-o",icon:" fa fa-minus-square-o"},{text:"level-up",value:"fa fa-level-up",icon:" fa fa-level-up"},{text:"level-down",value:"fa fa-level-down",icon:" fa fa-level-down"},{text:"check-square",value:"fa fa-check-square",icon:" fa fa-check-square"},{text:"pencil-square",value:"fa fa-pencil-square",icon:" fa fa-pencil-square"},{text:"external-link-square",value:"fa fa-external-link-square",icon:" fa fa-external-link-square"},{text:"share-square",value:"fa fa-share-square",icon:" fa fa-share-square"},{text:"compass",value:"fa fa-compass",icon:" fa fa-compass"},{text:"toggle-down",value:"fa fa-toggle-down",icon:" fa fa-toggle-down"},{text:"caret-square-o-down",value:"fa fa-caret-square-o-down",icon:" fa fa-caret-square-o-down"},{text:"toggle-up",value:"fa fa-toggle-up",icon:" fa fa-toggle-up"},{text:"caret-square-o-up",value:"fa fa-caret-square-o-up",icon:" fa fa-caret-square-o-up"},{text:"toggle-right",value:"fa fa-toggle-right",icon:" fa fa-toggle-right"},{text:"caret-square-o-right",value:"fa fa-caret-square-o-right",icon:" fa fa-caret-square-o-right"},{text:"euro",value:"fa fa-euro",icon:" fa fa-euro"},{text:"eur",value:"fa fa-eur",icon:" fa fa-eur"},{text:"gbp",value:"fa fa-gbp",icon:" fa fa-gbp"},{text:"dollar",value:"fa fa-dollar",icon:" fa fa-dollar"},{text:"usd",value:"fa fa-usd",icon:" fa fa-usd"},{text:"rupee",value:"fa fa-rupee",icon:" fa fa-rupee"},{text:"inr",value:"fa fa-inr",icon:" fa fa-inr"},{text:"cny",value:"fa fa-cny",icon:" fa fa-cny"},{text:"rmb",value:"fa fa-rmb",icon:" fa fa-rmb"},{text:"yen",value:"fa fa-yen",icon:" fa fa-yen"},{text:"jpy",value:"fa fa-jpy",icon:" fa fa-jpy"},{text:"ruble",value:"fa fa-ruble",icon:" fa fa-ruble"},{text:"rouble",value:"fa fa-rouble",icon:" fa fa-rouble"},{text:"rub",value:"fa fa-rub",icon:" fa fa-rub"},{text:"won",value:"fa fa-won",icon:" fa fa-won"},{text:"krw",value:"fa fa-krw",icon:" fa fa-krw"},{text:"btc",value:"fa fa-btc",icon:" fa fa-btc"},{text:"file",value:"fa fa-file",icon:" fa fa-file"},{text:"file-text",value:"fa fa-file-text",icon:" fa fa-file-text"},{text:"sort-alpha-asc",value:"fa fa-sort-alpha-asc",icon:" fa fa-sort-alpha-asc"},{text:"sort-alpha-desc",value:"fa fa-sort-alpha-desc",icon:" fa fa-sort-alpha-desc"},{text:"sort-amount-asc",value:"fa fa-sort-amount-asc",icon:" fa fa-sort-amount-asc"},{text:"sort-amount-desc",value:"fa fa-sort-amount-desc",icon:" fa fa-sort-amount-desc"},{text:"sort-numeric-asc",value:"fa fa-sort-numeric-asc",icon:" fa fa-sort-numeric-asc"},{text:"sort-numeric-desc",value:"fa fa-sort-numeric-desc",icon:" fa fa-sort-numeric-desc"},{text:"thumbs-up",value:"fa fa-thumbs-up",icon:" fa fa-thumbs-up"},{text:"thumbs-down",value:"fa fa-thumbs-down",icon:" fa fa-thumbs-down"},{text:"xing",value:"fa fa-xing",icon:" fa fa-xing"},{text:"xing-square",value:"fa fa-xing-square",icon:" fa fa-xing-square"},{text:"flickr",value:"fa fa-flickr",icon:" fa fa-flickr"},{text:"adn",value:"fa fa-adn",icon:" fa fa-adn"},{text:"long-arrow-down",value:"fa fa-long-arrow-down",icon:" fa fa-long-arrow-down"},{text:"long-arrow-up",value:"fa fa-long-arrow-up",icon:" fa fa-long-arrow-up"},{text:"long-arrow-left",value:"fa fa-long-arrow-left",icon:" fa fa-long-arrow-left"},{text:"long-arrow-right",value:"fa fa-long-arrow-right",icon:" fa fa-long-arrow-right"},{text:"apple",value:"fa fa-apple",icon:" fa fa-apple"},{text:"windows",value:"fa fa-windows",icon:" fa fa-windows"},{text:"android",value:"fa fa-android",icon:" fa fa-android"},{text:"linux",value:"fa fa-linux",icon:" fa fa-linux"},{text:"female",value:"fa fa-female",icon:" fa fa-female"},{text:"male",value:"fa fa-male",icon:" fa fa-male"},{text:"gittip",value:"fa fa-gittip",icon:" fa fa-gittip"},{text:"gratipay",value:"fa fa-gratipay",icon:" fa fa-gratipay"},{text:"sun-o",value:"fa fa-sun-o",icon:" fa fa-sun-o"},{text:"moon-o",value:"fa fa-moon-o",icon:" fa fa-moon-o"},{text:"archive",value:"fa fa-archive",icon:" fa fa-archive"},{text:"bug",value:"fa fa-bug",icon:" fa fa-bug"},{text:"vk",value:"fa fa-vk",icon:" fa fa-vk"},{text:"weibo",value:"fa fa-weibo",icon:" fa fa-weibo"},{text:"renren",value:"fa fa-renren",icon:" fa fa-renren"},{text:"pagelines",value:"fa fa-pagelines",icon:" fa fa-pagelines"},{text:"stack-exchange",value:"fa fa-stack-exchange",icon:" fa fa-stack-exchange"},{text:"arrow-circle-o-right",value:"fa fa-arrow-circle-o-right",icon:" fa fa-arrow-circle-o-right"},{text:"arrow-circle-o-left",value:"fa fa-arrow-circle-o-left",icon:" fa fa-arrow-circle-o-left"},{text:"toggle-left",value:"fa fa-toggle-left",icon:" fa fa-toggle-left"},{text:"caret-square-o-left",value:"fa fa-caret-square-o-left",icon:" fa fa-caret-square-o-left"},{text:"dot-circle-o",value:"fa fa-dot-circle-o",icon:" fa fa-dot-circle-o"},{text:"wheelchair",value:"fa fa-wheelchair",icon:" fa fa-wheelchair"},{text:"turkish-lira",value:"fa fa-turkish-lira",icon:" fa fa-turkish-lira"},{text:"try",value:"fa fa-try",icon:" fa fa-try"},{text:"plus-square-o",value:"fa fa-plus-square-o",icon:" fa fa-plus-square-o"},{text:"space-shuttle",value:"fa fa-space-shuttle",icon:" fa fa-space-shuttle"},{text:"envelope-square",value:"fa fa-envelope-square",icon:" fa fa-envelope-square"},{text:"wordpress",value:"fa fa-wordpress",icon:" fa fa-wordpress"},{text:"openid",value:"fa fa-openid",icon:" fa fa-openid"},{text:"institution",value:"fa fa-institution",icon:" fa fa-institution"},{text:"bank",value:"fa fa-bank",icon:" fa fa-bank"},{text:"university",value:"fa fa-university",icon:" fa fa-university"},{text:"mortar-board",value:"fa fa-mortar-board",icon:" fa fa-mortar-board"},{text:"graduation-cap",value:"fa fa-graduation-cap",icon:" fa fa-graduation-cap"},{text:"stumbleupon-circle",value:"fa fa-stumbleupon-circle",icon:" fa fa-stumbleupon-circle"},{text:"stumbleupon",value:"fa fa-stumbleupon",icon:" fa fa-stumbleupon"},{text:"delicious",value:"fa fa-delicious",icon:" fa fa-delicious"},{text:"digg",value:"fa fa-digg",icon:" fa fa-digg"},{text:"pied-piper-pp",value:"fa fa-pied-piper-pp",icon:" fa fa-pied-piper-pp"},{text:"pied-piper-alt",value:"fa fa-pied-piper-alt",icon:" fa fa-pied-piper-alt"},{text:"drupal",value:"fa fa-drupal",icon:" fa fa-drupal"},{text:"joomla",value:"fa fa-joomla",icon:" fa fa-joomla"},{text:"language",value:"fa fa-language",icon:" fa fa-language"},{text:"fax",value:"fa fa-fax",icon:" fa fa-fax"},{text:"building",value:"fa fa-building",icon:" fa fa-building"},{text:"child",value:"fa fa-child",icon:" fa fa-child"},{text:"paw",value:"fa fa-paw",icon:" fa fa-paw"},{text:"spoon",value:"fa fa-spoon",icon:" fa fa-spoon"},{text:"cube",value:"fa fa-cube",icon:" fa fa-cube"},{text:"cubes",value:"fa fa-cubes",icon:" fa fa-cubes"},{text:"recycle",value:"fa fa-recycle",icon:" fa fa-recycle"},{text:"automobile",value:"fa fa-automobile",icon:" fa fa-automobile"},{text:"car",value:"fa fa-car",icon:" fa fa-car"},{text:"cab",value:"fa fa-cab",icon:" fa fa-cab"},{text:"taxi",value:"fa fa-taxi",icon:" fa fa-taxi"},{text:"tree",value:"fa fa-tree",icon:" fa fa-tree"},{text:"deviantart",value:"fa fa-deviantart",icon:" fa fa-deviantart"},{text:"database",value:"fa fa-database",icon:" fa fa-database"},{text:"file-pdf-o",value:"fa fa-file-pdf-o",icon:" fa fa-file-pdf-o"},{text:"file-word-o",value:"fa fa-file-word-o",icon:" fa fa-file-word-o"},{text:"file-excel-o",value:"fa fa-file-excel-o",icon:" fa fa-file-excel-o"},{text:"file-powerpoint-o",value:"fa fa-file-powerpoint-o",icon:" fa fa-file-powerpoint-o"},{text:"file-photo-o",value:"fa fa-file-photo-o",icon:" fa fa-file-photo-o"},{text:"file-picture-o",value:"fa fa-file-picture-o",icon:" fa fa-file-picture-o"},{text:"file-image-o",value:"fa fa-file-image-o",icon:" fa fa-file-image-o"},{text:"file-zip-o",value:"fa fa-file-zip-o",icon:" fa fa-file-zip-o"},{text:"file-archive-o",value:"fa fa-file-archive-o",icon:" fa fa-file-archive-o"},{text:"file-sound-o",value:"fa fa-file-sound-o",icon:" fa fa-file-sound-o"},{text:"file-audio-o",value:"fa fa-file-audio-o",icon:" fa fa-file-audio-o"},{text:"file-movie-o",value:"fa fa-file-movie-o",icon:" fa fa-file-movie-o"},{text:"file-video-o",value:"fa fa-file-video-o",icon:" fa fa-file-video-o"},{text:"file-code-o",value:"fa fa-file-code-o",icon:" fa fa-file-code-o"},{text:"life-bouy",value:"fa fa-life-bouy",icon:" fa fa-life-bouy"},{text:"life-buoy",value:"fa fa-life-buoy",icon:" fa fa-life-buoy"},{text:"life-saver",value:"fa fa-life-saver",icon:" fa fa-life-saver"},{text:"support",value:"fa fa-support",icon:" fa fa-support"},{text:"life-ring",value:"fa fa-life-ring",icon:" fa fa-life-ring"},{text:"circle-o-notch",value:"fa fa-circle-o-notch",icon:" fa fa-circle-o-notch"},{text:"ra",value:"fa fa-ra",icon:" fa fa-ra"},{text:"resistance",value:"fa fa-resistance",icon:" fa fa-resistance"},{text:"rebel",value:"fa fa-rebel",icon:" fa fa-rebel"},{text:"ge",value:"fa fa-ge",icon:" fa fa-ge"},{text:"empire",value:"fa fa-empire",icon:" fa fa-empire"},{text:"y-combinator-square",value:"fa fa-y-combinator-square",icon:" fa fa-y-combinator-square"},{text:"yc-square",value:"fa fa-yc-square",icon:" fa fa-yc-square"},{text:"hacker-news",value:"fa fa-hacker-news",icon:" fa fa-hacker-news"},{text:"tencent-weibo",value:"fa fa-tencent-weibo",icon:" fa fa-tencent-weibo"},{text:"qq",value:"fa fa-qq",icon:" fa fa-qq"},{text:"wechat",value:"fa fa-wechat",icon:" fa fa-wechat"},{text:"weixin",value:"fa fa-weixin",icon:" fa fa-weixin"},{text:"send",value:"fa fa-send",icon:" fa fa-send"},{text:"paper-plane",value:"fa fa-paper-plane",icon:" fa fa-paper-plane"},{text:"send-o",value:"fa fa-send-o",icon:" fa fa-send-o"},{text:"paper-plane-o",value:"fa fa-paper-plane-o",icon:" fa fa-paper-plane-o"},{text:"history",value:"fa fa-history",icon:" fa fa-history"},{text:"circle-thin",value:"fa fa-circle-thin",icon:" fa fa-circle-thin"},{text:"header",value:"fa fa-header",icon:" fa fa-header"},{text:"paragraph",value:"fa fa-paragraph",icon:" fa fa-paragraph"},{text:"sliders",value:"fa fa-sliders",icon:" fa fa-sliders"},{text:"bomb",value:"fa fa-bomb",icon:" fa fa-bomb"},{text:"soccer-ball-o",value:"fa fa-soccer-ball-o",icon:" fa fa-soccer-ball-o"},{text:"futbol-o",value:"fa fa-futbol-o",icon:" fa fa-futbol-o"},{text:"tty",value:"fa fa-tty",icon:" fa fa-tty"},{text:"binoculars",value:"fa fa-binoculars",icon:" fa fa-binoculars"},{text:"plug",value:"fa fa-plug",icon:" fa fa-plug"},{text:"slideshare",value:"fa fa-slideshare",icon:" fa fa-slideshare"},{text:"newspaper-o",value:"fa fa-newspaper-o",icon:" fa fa-newspaper-o"},{text:"wifi",value:"fa fa-wifi",icon:" fa fa-wifi"},{text:"calculator",value:"fa fa-calculator",icon:" fa fa-calculator"},{text:"google-wallet",value:"fa fa-google-wallet",icon:" fa fa-google-wallet"},{text:"cc-discover",value:"fa fa-cc-discover",icon:" fa fa-cc-discover"},{text:"cc-amex",value:"fa fa-cc-amex",icon:" fa fa-cc-amex"},{text:"bell-slash",value:"fa fa-bell-slash",icon:" fa fa-bell-slash"},{text:"bell-slash-o",value:"fa fa-bell-slash-o",icon:" fa fa-bell-slash-o"},{text:"trash",value:"fa fa-trash",icon:" fa fa-trash"},{text:"copyright",value:"fa fa-copyright",icon:" fa fa-copyright"},{text:"at",value:"fa fa-at",icon:" fa fa-at"},{text:"eyedropper",value:"fa fa-eyedropper",icon:" fa fa-eyedropper"},{text:"paint-brush",value:"fa fa-paint-brush",icon:" fa fa-paint-brush"},{text:"birthday-cake",value:"fa fa-birthday-cake",icon:" fa fa-birthday-cake"},{text:"area-chart",value:"fa fa-area-chart",icon:" fa fa-area-chart"},{text:"pie-chart",value:"fa fa-pie-chart",icon:" fa fa-pie-chart"},{text:"line-chart",value:"fa fa-line-chart",icon:" fa fa-line-chart"},{text:"lastfm",value:"fa fa-lastfm",icon:" fa fa-lastfm"},{text:"lastfm-square",value:"fa fa-lastfm-square",icon:" fa fa-lastfm-square"},{text:"toggle-off",value:"fa fa-toggle-off",icon:" fa fa-toggle-off"},{text:"toggle-on",value:"fa fa-toggle-on",icon:" fa fa-toggle-on"},{text:"bicycle",value:"fa fa-bicycle",icon:" fa fa-bicycle"},{text:"bus",value:"fa fa-bus",icon:" fa fa-bus"},{text:"ioxhost",value:"fa fa-ioxhost",icon:" fa fa-ioxhost"},{text:"angellist",value:"fa fa-angellist",icon:" fa fa-angellist"},{text:"cc",value:"fa fa-cc",icon:" fa fa-cc"},{text:"shekel",value:"fa fa-shekel",icon:" fa fa-shekel"},{text:"sheqel",value:"fa fa-sheqel",icon:" fa fa-sheqel"},{text:"ils",value:"fa fa-ils",icon:" fa fa-ils"},{text:"meanpath",value:"fa fa-meanpath",icon:" fa fa-meanpath"},{text:"buysellads",value:"fa fa-buysellads",icon:" fa fa-buysellads"},{text:"connectdevelop",value:"fa fa-connectdevelop",icon:" fa fa-connectdevelop"},{text:"dashcube",value:"fa fa-dashcube",icon:" fa fa-dashcube"},{text:"forumbee",value:"fa fa-forumbee",icon:" fa fa-forumbee"},{text:"leanpub",value:"fa fa-leanpub",icon:" fa fa-leanpub"},{text:"sellsy",value:"fa fa-sellsy",icon:" fa fa-sellsy"},{text:"shirtsinbulk",value:"fa fa-shirtsinbulk",icon:" fa fa-shirtsinbulk"},{text:"simplybuilt",value:"fa fa-simplybuilt",icon:" fa fa-simplybuilt"},{text:"skyatlas",value:"fa fa-skyatlas",icon:" fa fa-skyatlas"},{text:"cart-plus",value:"fa fa-cart-plus",icon:" fa fa-cart-plus"},{text:"cart-arrow-down",value:"fa fa-cart-arrow-down",icon:" fa fa-cart-arrow-down"},{text:"diamond",value:"fa fa-diamond",icon:" fa fa-diamond"},{text:"ship",value:"fa fa-ship",icon:" fa fa-ship"},{text:"user-secret",value:"fa fa-user-secret",icon:" fa fa-user-secret"},{text:"motorcycle",value:"fa fa-motorcycle",icon:" fa fa-motorcycle"},{text:"street-view",value:"fa fa-street-view",icon:" fa fa-street-view"},{text:"heartbeat",value:"fa fa-heartbeat",icon:" fa fa-heartbeat"},{text:"venus",value:"fa fa-venus",icon:" fa fa-venus"},{text:"mars",value:"fa fa-mars",icon:" fa fa-mars"},{text:"mercury",value:"fa fa-mercury",icon:" fa fa-mercury"},{text:"intersex",value:"fa fa-intersex",icon:" fa fa-intersex"},{text:"transgender",value:"fa fa-transgender",icon:" fa fa-transgender"},{text:"transgender-alt",value:"fa fa-transgender-alt",icon:" fa fa-transgender-alt"},{text:"venus-double",value:"fa fa-venus-double",icon:" fa fa-venus-double"},{text:"mars-double",value:"fa fa-mars-double",icon:" fa fa-mars-double"},{text:"venus-mars",value:"fa fa-venus-mars",icon:" fa fa-venus-mars"},{text:"mars-stroke",value:"fa fa-mars-stroke",icon:" fa fa-mars-stroke"},{text:"mars-stroke-v",value:"fa fa-mars-stroke-v",icon:" fa fa-mars-stroke-v"},{text:"mars-stroke-h",value:"fa fa-mars-stroke-h",icon:" fa fa-mars-stroke-h"},{text:"neuter",value:"fa fa-neuter",icon:" fa fa-neuter"},{text:"genderless",value:"fa fa-genderless",icon:" fa fa-genderless"},{text:"server",value:"fa fa-server",icon:" fa fa-server"},{text:"user-plus",value:"fa fa-user-plus",icon:" fa fa-user-plus"},{text:"user-times",value:"fa fa-user-times",icon:" fa fa-user-times"},{text:"hotel",value:"fa fa-hotel",icon:" fa fa-hotel"},{text:"bed",value:"fa fa-bed",icon:" fa fa-bed"},{text:"viacoin",value:"fa fa-viacoin",icon:" fa fa-viacoin"},{text:"train",value:"fa fa-train",icon:" fa fa-train"},{text:"subway",value:"fa fa-subway",icon:" fa fa-subway"},{text:"yc",value:"fa fa-yc",icon:" fa fa-yc"},{text:"y-combinator",value:"fa fa-y-combinator",icon:" fa fa-y-combinator"},{text:"optin-monster",value:"fa fa-optin-monster",icon:" fa fa-optin-monster"},{text:"opencart",value:"fa fa-opencart",icon:" fa fa-opencart"},{text:"expeditedssl",value:"fa fa-expeditedssl",icon:" fa fa-expeditedssl"},{text:"battery-4",value:"fa fa-battery-4",icon:" fa fa-battery-4"},{text:"battery",value:"fa fa-battery",icon:" fa fa-battery"},{text:"battery-full",value:"fa fa-battery-full",icon:" fa fa-battery-full"},{text:"battery-3",value:"fa fa-battery-3",icon:" fa fa-battery-3"},{text:"battery-three-quarters",value:"fa fa-battery-three-quarters",icon:" fa fa-battery-three-quarters"},{text:"battery-2",value:"fa fa-battery-2",icon:" fa fa-battery-2"},{text:"battery-half",value:"fa fa-battery-half",icon:" fa fa-battery-half"},{text:"battery-1",value:"fa fa-battery-1",icon:" fa fa-battery-1"},{text:"battery-quarter",value:"fa fa-battery-quarter",icon:" fa fa-battery-quarter"},{text:"battery-0",value:"fa fa-battery-0",icon:" fa fa-battery-0"},{text:"battery-empty",value:"fa fa-battery-empty",icon:" fa fa-battery-empty"},{text:"mouse-pointer",value:"fa fa-mouse-pointer",icon:" fa fa-mouse-pointer"},{text:"i-cursor",value:"fa fa-i-cursor",icon:" fa fa-i-cursor"},{text:"object-group",value:"fa fa-object-group",icon:" fa fa-object-group"},{text:"object-ungroup",value:"fa fa-object-ungroup",icon:" fa fa-object-ungroup"},{text:"sticky-note",value:"fa fa-sticky-note",icon:" fa fa-sticky-note"},{text:"sticky-note-o",value:"fa fa-sticky-note-o",icon:" fa fa-sticky-note-o"},{text:"cc-diners-club",value:"fa fa-cc-diners-club",icon:" fa fa-cc-diners-club"},{text:"clone",value:"fa fa-clone",icon:" fa fa-clone"},{text:"balance-scale",value:"fa fa-balance-scale",icon:" fa fa-balance-scale"},{text:"hourglass-o",value:"fa fa-hourglass-o",icon:" fa fa-hourglass-o"},{text:"hourglass-1",value:"fa fa-hourglass-1",icon:" fa fa-hourglass-1"},{text:"hourglass-start",value:"fa fa-hourglass-start",icon:" fa fa-hourglass-start"},{text:"hourglass-2",value:"fa fa-hourglass-2",icon:" fa fa-hourglass-2"},{text:"hourglass-half",value:"fa fa-hourglass-half",icon:" fa fa-hourglass-half"},{text:"hourglass-3",value:"fa fa-hourglass-3",icon:" fa fa-hourglass-3"},{text:"hourglass-end",value:"fa fa-hourglass-end",icon:" fa fa-hourglass-end"},{text:"hourglass",value:"fa fa-hourglass",icon:" fa fa-hourglass"},{text:"hand-grab-o",value:"fa fa-hand-grab-o",icon:" fa fa-hand-grab-o"},{text:"hand-rock-o",value:"fa fa-hand-rock-o",icon:" fa fa-hand-rock-o"},{text:"hand-stop-o",value:"fa fa-hand-stop-o",icon:" fa fa-hand-stop-o"},{text:"hand-paper-o",value:"fa fa-hand-paper-o",icon:" fa fa-hand-paper-o"},{text:"hand-scissors-o",value:"fa fa-hand-scissors-o",icon:" fa fa-hand-scissors-o"},{text:"hand-lizard-o",value:"fa fa-hand-lizard-o",icon:" fa fa-hand-lizard-o"},{text:"hand-spock-o",value:"fa fa-hand-spock-o",icon:" fa fa-hand-spock-o"},{text:"hand-pointer-o",value:"fa fa-hand-pointer-o",icon:" fa fa-hand-pointer-o"},{text:"hand-peace-o",value:"fa fa-hand-peace-o",icon:" fa fa-hand-peace-o"},{text:"trademark",value:"fa fa-trademark",icon:" fa fa-trademark"},{text:"registered",value:"fa fa-registered",icon:" fa fa-registered"},{text:"creative-commons",value:"fa fa-creative-commons",icon:" fa fa-creative-commons"},{text:"gg",value:"fa fa-gg",icon:" fa fa-gg"},{text:"gg-circle",value:"fa fa-gg-circle",icon:" fa fa-gg-circle"},{text:"odnoklassniki",value:"fa fa-odnoklassniki",icon:" fa fa-odnoklassniki"},{text:"odnoklassniki-square",value:"fa fa-odnoklassniki-square",icon:" fa fa-odnoklassniki-square"},{text:"get-pocket",value:"fa fa-get-pocket",icon:" fa fa-get-pocket"},{text:"safari",value:"fa fa-safari",icon:" fa fa-safari"},{text:"chrome",value:"fa fa-chrome",icon:" fa fa-chrome"},{text:"firefox",value:"fa fa-firefox",icon:" fa fa-firefox"},{text:"opera",value:"fa fa-opera",icon:" fa fa-opera"},{text:"internet-explorer",value:"fa fa-internet-explorer",icon:" fa fa-internet-explorer"},{text:"tv",value:"fa fa-tv",icon:" fa fa-tv"},{text:"television",value:"fa fa-television",icon:" fa fa-television"},{text:"contao",value:"fa fa-contao",icon:" fa fa-contao"},{text:"amazon",value:"fa fa-amazon",icon:" fa fa-amazon"},{text:"calendar-plus-o",value:"fa fa-calendar-plus-o",icon:" fa fa-calendar-plus-o"},{text:"calendar-minus-o",value:"fa fa-calendar-minus-o",icon:" fa fa-calendar-minus-o"},{text:"calendar-times-o",value:"fa fa-calendar-times-o",icon:" fa fa-calendar-times-o"},{text:"calendar-check-o",value:"fa fa-calendar-check-o",icon:" fa fa-calendar-check-o"},{text:"industry",value:"fa fa-industry",icon:" fa fa-industry"},{text:"map-pin",value:"fa fa-map-pin",icon:" fa fa-map-pin"},{text:"map-signs",value:"fa fa-map-signs",icon:" fa fa-map-signs"},{text:"map-o",value:"fa fa-map-o",icon:" fa fa-map-o"},{text:"map",value:"fa fa-map",icon:" fa fa-map"},{text:"commenting",value:"fa fa-commenting",icon:" fa fa-commenting"},{text:"commenting-o",value:"fa fa-commenting-o",icon:" fa fa-commenting-o"},{text:"houzz",value:"fa fa-houzz",icon:" fa fa-houzz"},{text:"black-tie",value:"fa fa-black-tie",icon:" fa fa-black-tie"},{text:"fonticons",value:"fa fa-fonticons",icon:" fa fa-fonticons"},{text:"edge",value:"fa fa-edge",icon:" fa fa-edge"},{text:"credit-card-alt",value:"fa fa-credit-card-alt",icon:" fa fa-credit-card-alt"},{text:"codiepie",value:"fa fa-codiepie",icon:" fa fa-codiepie"},{text:"modx",value:"fa fa-modx",icon:" fa fa-modx"},{text:"fort-awesome",value:"fa fa-fort-awesome",icon:" fa fa-fort-awesome"},{text:"usb",value:"fa fa-usb",icon:" fa fa-usb"},{text:"product-hunt",value:"fa fa-product-hunt",icon:" fa fa-product-hunt"},{text:"mixcloud",value:"fa fa-mixcloud",icon:" fa fa-mixcloud"},{text:"scribd",value:"fa fa-scribd",icon:" fa fa-scribd"},{text:"pause-circle",value:"fa fa-pause-circle",icon:" fa fa-pause-circle"},{text:"pause-circle-o",value:"fa fa-pause-circle-o",icon:" fa fa-pause-circle-o"},{text:"stop-circle",value:"fa fa-stop-circle",icon:" fa fa-stop-circle"},{text:"stop-circle-o",value:"fa fa-stop-circle-o",icon:" fa fa-stop-circle-o"},{text:"shopping-bag",value:"fa fa-shopping-bag",icon:" fa fa-shopping-bag"},{text:"shopping-basket",value:"fa fa-shopping-basket",icon:" fa fa-shopping-basket"},{text:"hashtag",value:"fa fa-hashtag",icon:" fa fa-hashtag"},{text:"bluetooth",value:"fa fa-bluetooth",icon:" fa fa-bluetooth"},{text:"bluetooth-b",value:"fa fa-bluetooth-b",icon:" fa fa-bluetooth-b"},{text:"percent",value:"fa fa-percent",icon:" fa fa-percent"},{text:"gitlab",value:"fa fa-gitlab",icon:" fa fa-gitlab"},{text:"wpbeginner",value:"fa fa-wpbeginner",icon:" fa fa-wpbeginner"},{text:"wpforms",value:"fa fa-wpforms",icon:" fa fa-wpforms"},{text:"envira",value:"fa fa-envira",icon:" fa fa-envira"},{text:"universal-access",value:"fa fa-universal-access",icon:" fa fa-universal-access"},{text:"wheelchair-alt",value:"fa fa-wheelchair-alt",icon:" fa fa-wheelchair-alt"},{text:"question-circle-o",value:"fa fa-question-circle-o",icon:" fa fa-question-circle-o"},{text:"blind",value:"fa fa-blind",icon:" fa fa-blind"},{text:"audio-description",value:"fa fa-audio-description",icon:" fa fa-audio-description"},{text:"volume-control-phone",value:"fa fa-volume-control-phone",icon:" fa fa-volume-control-phone"},{text:"braille",value:"fa fa-braille",icon:" fa fa-braille"},{text:"assistive-listening-systems",value:"fa fa-assistive-listening-systems",icon:" fa fa-assistive-listening-systems"},{text:"asl-interpreting",value:"fa fa-asl-interpreting",icon:" fa fa-asl-interpreting"},{text:"american-sign-language-interpreting",value:"fa fa-american-sign-language-interpreting",icon:" fa fa-american-sign-language-interpreting"},{text:"deafness",value:"fa fa-deafness",icon:" fa fa-deafness"},{text:"hard-of-hearing",value:"fa fa-hard-of-hearing",icon:" fa fa-hard-of-hearing"},{text:"deaf",value:"fa fa-deaf",icon:" fa fa-deaf"},{text:"glide",value:"fa fa-glide",icon:" fa fa-glide"},{text:"glide-g",value:"fa fa-glide-g",icon:" fa fa-glide-g"},{text:"signing",value:"fa fa-signing",icon:" fa fa-signing"},{text:"sign-language",value:"fa fa-sign-language",icon:" fa fa-sign-language"},{text:"low-vision",value:"fa fa-low-vision",icon:" fa fa-low-vision"},{text:"viadeo",value:"fa fa-viadeo",icon:" fa fa-viadeo"},{text:"viadeo-square",value:"fa fa-viadeo-square",icon:" fa fa-viadeo-square"},{text:"pied-piper",value:"fa fa-pied-piper",icon:" fa fa-pied-piper"},{text:"first-order",value:"fa fa-first-order",icon:" fa fa-first-order"},{text:"yoast",value:"fa fa-yoast",icon:" fa fa-yoast"},{text:"themeisle",value:"fa fa-themeisle",icon:" fa fa-themeisle"},{text:"google-plus-official",value:"fa fa-google-plus-official",icon:" fa fa-google-plus-official"},{text:"fa",value:"fa fa-fa",icon:" fa fa-fa"},{text:"font-awesome",value:"fa fa-font-awesome",icon:" fa fa-font-awesome"},{text:"handshake-o",value:"fa fa-handshake-o",icon:" fa fa-handshake-o"},{text:"envelope-open",value:"fa fa-envelope-open",icon:" fa fa-envelope-open"},{text:"envelope-open-o",value:"fa fa-envelope-open-o",icon:" fa fa-envelope-open-o"},{text:"linode",value:"fa fa-linode",icon:" fa fa-linode"},{text:"address-book",value:"fa fa-address-book",icon:" fa fa-address-book"},{text:"address-book-o",value:"fa fa-address-book-o",icon:" fa fa-address-book-o"},{text:"vcard",value:"fa fa-vcard",icon:" fa fa-vcard"},{text:"address-card",value:"fa fa-address-card",icon:" fa fa-address-card"},{text:"vcard-o",value:"fa fa-vcard-o",icon:" fa fa-vcard-o"},{text:"address-card-o",value:"fa fa-address-card-o",icon:" fa fa-address-card-o"},{text:"user-circle",value:"fa fa-user-circle",icon:" fa fa-user-circle"},{text:"user-circle-o",value:"fa fa-user-circle-o",icon:" fa fa-user-circle-o"},{text:"user-o",value:"fa fa-user-o",icon:" fa fa-user-o"},{text:"id-badge",value:"fa fa-id-badge",icon:" fa fa-id-badge"},{text:"drivers-license",value:"fa fa-drivers-license",icon:" fa fa-drivers-license"},{text:"id-card",value:"fa fa-id-card",icon:" fa fa-id-card"},{text:"drivers-license-o",value:"fa fa-drivers-license-o",icon:" fa fa-drivers-license-o"},{text:"id-card-o",value:"fa fa-id-card-o",icon:" fa fa-id-card-o"},{text:"quora",value:"fa fa-quora",icon:" fa fa-quora"},{text:"free-code-camp",value:"fa fa-free-code-camp",icon:" fa fa-free-code-camp"},{text:"thermometer-4",value:"fa fa-thermometer-4",icon:" fa fa-thermometer-4"},{text:"thermometer",value:"fa fa-thermometer",icon:" fa fa-thermometer"},{text:"thermometer-full",value:"fa fa-thermometer-full",icon:" fa fa-thermometer-full"},{text:"thermometer-3",value:"fa fa-thermometer-3",icon:" fa fa-thermometer-3"},{text:"thermometer-three-quarters",value:"fa fa-thermometer-three-quarters",icon:" fa fa-thermometer-three-quarters"},{text:"thermometer-2",value:"fa fa-thermometer-2",icon:" fa fa-thermometer-2"},{text:"thermometer-half",value:"fa fa-thermometer-half",icon:" fa fa-thermometer-half"},{text:"thermometer-1",value:"fa fa-thermometer-1",icon:" fa fa-thermometer-1"},{text:"thermometer-quarter",value:"fa fa-thermometer-quarter",icon:" fa fa-thermometer-quarter"},{text:"thermometer-0",value:"fa fa-thermometer-0",icon:" fa fa-thermometer-0"},{text:"thermometer-empty",value:"fa fa-thermometer-empty",icon:" fa fa-thermometer-empty"},{text:"shower",value:"fa fa-shower",icon:" fa fa-shower"},{text:"bathtub",value:"fa fa-bathtub",icon:" fa fa-bathtub"},{text:"s15",value:"fa fa-s15",icon:" fa fa-s15"},{text:"bath",value:"fa fa-bath",icon:" fa fa-bath"},{text:"podcast",value:"fa fa-podcast",icon:" fa fa-podcast"},{text:"window-maximize",value:"fa fa-window-maximize",icon:" fa fa-window-maximize"},{text:"window-minimize",value:"fa fa-window-minimize",icon:" fa fa-window-minimize"},{text:"window-restore",value:"fa fa-window-restore",icon:" fa fa-window-restore"},{text:"times-rectangle",value:"fa fa-times-rectangle",icon:" fa fa-times-rectangle"},{text:"window-close",value:"fa fa-window-close",icon:" fa fa-window-close"},{text:"times-rectangle-o",value:"fa fa-times-rectangle-o",icon:" fa fa-times-rectangle-o"},{text:"window-close-o",value:"fa fa-window-close-o",icon:" fa fa-window-close-o"},{text:"bandcamp",value:"fa fa-bandcamp",icon:" fa fa-bandcamp"},{text:"grav",value:"fa fa-grav",icon:" fa fa-grav"},{text:"etsy",value:"fa fa-etsy",icon:" fa fa-etsy"},{text:"imdb",value:"fa fa-imdb",icon:" fa fa-imdb"},{text:"ravelry",value:"fa fa-ravelry",icon:" fa fa-ravelry"},{text:"eercast",value:"fa fa-eercast",icon:" fa fa-eercast"},{text:"microchip",value:"fa fa-microchip",icon:" fa fa-microchip"},{text:"snowflake-o",value:"fa fa-snowflake-o",icon:" fa fa-snowflake-o"},{text:"superpowers",value:"fa fa-superpowers",icon:" fa fa-superpowers"},{text:"wpexplorer",value:"fa fa-wpexplorer",icon:" fa fa-wpexplorer"},{text:"meetup",value:"fa fa-meetup",icon:" fa fa-meetup"},{text:"theme_line",value:"gt3-line-icon",icon:" gt3-line-icon"}]



		 editor.addButton('SocialIcon', {
			text: ' Icon',
			icon: ' fa fa-font-awesome',
			tooltip: 'Push to insert Icon',
			onclick: function(){
				editor.windowManager.open({
					title: 'Insert Icon',
					width: 600,
					height: 340,
					body: [
						{
		                    type   : 'combobox',
		                    name   : 'icon_select',
		                    label  : 'Type icon code or choose from select',
		                    values : icons,
		                    value : 'fa fa-twitter-square' // Sets the default
		                },
		                {
		                    type   : 'textbox',
		                    name   : 'icon_link',
		                    label  : 'Icon Link',
		                    tooltip: 'Type url',
		                    value  : ''
		                },
		                {
		                    type   : 'checkbox',
		                    name   : 'icon_link_new_tab',
		                    label  : 'Link in new tab',
		                    text   : 'Open link in a new tab',
		                    checked : true
		                },
		                {
							type: 'textbox',
							name: 'icon_color',
							label: 'Icon color',
							onPostRender: colorPickerFunc
						},
						{
							type: 'textbox',
							name: 'icon_hover_color',
							label: 'Icon hover color',
							onPostRender: colorPickerFunc
						},
						{
		                    type   : 'textbox',
		                    name   : 'icon_size',
		                    label  : 'Icon Size',
		                    value  : '14px'
		                },
		                {
		                    type   : 'textbox',
		                    name   : 'icon_margin_right',
		                    label  : 'Icon margin (margin-right in px)',
		                    value  : '15',
		                    onPostRender: numberFunc
		                },
		                {
		                    type   : 'textbox',
		                    name   : 'icon_margin_top',
		                    label  : 'Icon margin (margin-top in px)',
		                    value  : '5',
		                    onPostRender: numberFunc
		                },
					],
					onsubmit: function(e){
						var selected_text = tinyMCE.activeEditor.selection.getContent();
						var icon_link = e.data.icon_link != '' ? e.data.icon_link : '#';
						var icon_select = e.data.icon_select != '' ? e.data.icon_select : 'fa fa-share-alt';
						var icon_size = e.data.icon_size != '' ? e.data.icon_size : 'inherit';
						var icon_color = e.data.icon_color != '' ? e.data.icon_color : 'inherit';
						editor.insertContent('<a class="gt3_icon_link'+((e.data.icon_color != '' || e.data.icon_hover_color != '') ? ' gt3_custom_color' : '')+'"'+' href="'+icon_link+'"'+(e.data.icon_link_new_tab != '' ? ' target="_blank"' : ' target="_self"')+' '+(e.data.icon_color != '' ? 'data-color="'+e.data.icon_color+'"' : 'data-color="inherit"')+' '+(e.data.icon_hover_color != '' ? 'data-hover-color="'+e.data.icon_hover_color+'"' : 'data-hover-color="inherit"')+' style="font-size: '+icon_size+';color:'+icon_color+';'+(e.data.icon_margin_right != '' ? 'margin-right:'+e.data.icon_margin_right+'px;' : '')+(e.data.icon_margin_top != '' ? 'margin-top:'+e.data.icon_margin_top+'px;' : '')+'"><i class="'+icon_select+'">&nbsp;</i></a>&nbsp;');
					}

				});
			}
		}
		);


		editor.addButton('DropCaps', {
			text: ' DropCaps',
			icon: ' fa fa-font',
			tooltip: 'Select a single character',
			onclick: function(){
				editor.windowManager.open({
					title: 'Drops caps settings',
					width: 300,
					height: 70,
					body: [
						{
							type: 'textbox',
							name: 'text_color',
							label: 'Сolor',
							value  : '#3b55e6',
							onPostRender: colorPickerFunc
						},
					],
					onsubmit: function(e){
						var cont = tinyMCE.activeEditor.getContent();

						var selected_text = tinyMCE.activeEditor.selection.getContent();
						if(jQuery(tinyMCE.activeEditor.selection.getNode()).find('.gt3_dropcaps').length){
							selected_text = selected_text.replace(/<\/?span[^>]*>/g, "");
						}
						editor.insertContent('<span class="gt3_dropcaps" '+(e.data.text_color != '' ? 'style="font-size: 3.33em;line-height: 0.92;color:'+e.data.text_color+';' : '')+'">' + selected_text.charAt(0) + '</span>' + selected_text.slice(1));
					}
				});

			},
			onpostrender: function (){
				var ctrl = this;
				var sc_required = 'single_char_selected';
				editor.on( 'NodeChange', function (){
					var match = conditions[sc_required]( editor );
					if ( !match && !ctrl.disabled() ){
						ctrl.disabled( true );
					}
					else if ( match && ctrl.disabled() ){
						ctrl.disabled( false );
					}
				});
			}
		}
		);


		editor.addButton('Highlighter', {
			text: ' Highlighter',
			icon: ' fa fa-pencil',
			tooltip: 'Select a string',
			onclick: function(){
				editor.windowManager.open({
					title: 'Highlighter settings',
					width: 300,
					height: 110,
					body: [
						{
							type: 'textbox',
							name: 'text_bg_color',
							label: 'Background Сolor',
							value  : '#3b55e6',
							onPostRender: colorPickerFunc
						},
						{
							type: 'textbox',
							name: 'text_color',
							label: 'Сolor',
							value  : '#3b55e6',
							onPostRender: colorPickerFunc
						},
					],
					onsubmit: function(e){
						var cont = tinyMCE.activeEditor.getContent();

						var selected_text = tinyMCE.activeEditor.selection.getContent();
						editor.insertContent('<span class="gt3_highlighter" '+(
							(e.data.text_color != '' || e.data.text_bg_color != '')
							? 'style="background-color:'+e.data.text_bg_color+';'+'color:'+e.data.text_color+';"'
							: '')+'>' + selected_text + '</span>');
					}
				});

			},
			onpostrender: function (){
				var ctrl = this;
				var required = 'selection';
				editor.on( 'NodeChange', function (){
					var match = conditions[required]( editor );
					if ( !match && !ctrl.disabled() ){
						ctrl.disabled( true );
					}
					else if ( match && ctrl.disabled() ){
						ctrl.disabled( false );
					}
				});
			}
		}
		);

		editor.addButton('SecondaryFont', {
			text: ' Secondary Font',
			icon: ' fa fa-paper-plane',
			tooltip: 'Select a string',
			onclick: function(){

				var selection = editor.selection.getNode();
				if (jQuery(selection).hasClass('gt3_secondary_font')) {
					jQuery(selection).removeClass('gt3_secondary_font')
				}else{
					var cont = tinyMCE.activeEditor.getContent();
					var selected_text = tinyMCE.activeEditor.selection.getContent();
					editor.insertContent('<span class="gt3_secondary_font">' + selected_text + '</span>');
				}

			},
		}
		);

		editor.addButton('TitleLine', {
			text: ' Title Line',
			icon: ' fa fa-minus',
			tooltip: 'Select a string',
			onclick: function(){
				var selected_text = tinyMCE.activeEditor.selection.getNode();
				if (jQuery(selected_text).find('.gt3_title_line').length || jQuery(selected_text).parent().find('.gt3_title_line').length) {
					jQuery(selected_text).find('.gt3_title_line').remove();
					jQuery(selected_text).parent().find('.gt3_title_line').remove();
					editor.execCommand("mceRepaint");
				}else{
					editor.windowManager.open({
						title: 'Title Line settings',
						width: 300,
						height: 110,
						body: [
							{
								type: 'textbox',
								name: 'line_color',
								label: 'Line Сolor',
								value  : '#3b55e6',
								onPostRender: colorPickerFunc
							},
							{
			                    type   : 'listbox',
			                    name   : 'line_position',
			                    label  : 'Line Position',
			                    values : [
			                        { text: 'left', value: 'left' },
			                        { text: 'right', value: 'right' },
			                    ],
			                    value : 'left',
			                },
						],
						onsubmit: function(e){
							var cont = tinyMCE.activeEditor.getContent();
							var title_line = '';

							var selected_text = tinyMCE.activeEditor.selection.getContent();
							if (jQuery(tinyMCE.activeEditor.selection.getNode()).find('.gt3_title_line').length) {
								jQuery(tinyMCE.activeEditor.selection.getNode()).find('.gt3_title_line').remove()
								editor.insertContent(selected_text);
							}else{						
								title_line = '<span class="gt3_title_line '+e.data.line_position+'" '+(
								( e.data.line_color != '')
								? 'style="color:'+e.data.line_color+';"'
								: '')+'><span></span></span>';

								if (e.data.line_position == 'right') {
									title_line = selected_text+title_line;
								}else{
									title_line = title_line+selected_text;
								}

								editor.insertContent(title_line);
							}
						}
					});
				}

			},
			onpostrender: function (){
				var ctrl = this;
				var required = 'selection';
				editor.on( 'NodeChange', function (){
					var match = conditions[required]( editor );
					if ( !match && !ctrl.disabled() ){
						ctrl.disabled( true );
					}
					else if ( match && ctrl.disabled() ){
						ctrl.disabled( false );
					}
				});
			}
		}
		);

		editor.addButton('LinkStyling', {
			text: ' Link Styling',
			icon: ' fa fa-paint-brush',
			tooltip: 'Select a link',
			onclick: function(){
				editor.windowManager.open({
					title: 'Link Styling settings',
					width: 450,
					height: 190,
					body: [
						{
							type: 'textbox',
							name: 'text_color',
							label: 'Сolor',
							value  : '',
							onPostRender: colorPickerFunc
						},
						{
							type: 'textbox',
							name: 'text_color_hover',
							label: 'Сolor on Hover',
							value  : '',
							onPostRender: colorPickerFunc
						},
						{
		                    type   : 'checkbox',
		                    name   : 'underline',
		                    label  : 'Underline text decoration',
		                    checked : true
		                },
		                {
		                    type   : 'checkbox',
		                    name   : 'hover_underline',
		                    label  : 'Underline text decoration on Hover',
		                    checked : true
		                },
					],
					onsubmit: function(e){
						var cont = tinyMCE.activeEditor.selection.getNode();
						jQuery(cont).addClass('gt3_styled_link');
						jQuery(cont).removeAttr('data-color').removeAttr('data-hover-color').removeAttr('style').removeClass('gt3_styled_link__underline').removeClass('gt3_styled_link__underline_on_hover');
						if (e.data.text_color != '') {
							jQuery(cont).addClass('gt3_custom_color').attr('data-color',e.data.text_color).css('color',e.data.text_color);
						}
						if (e.data.text_color_hover != '') {
							jQuery(cont).addClass('gt3_custom_color').attr('data-hover-color',e.data.text_color_hover);
						}
						if (e.data.underline) {
							jQuery(cont).addClass('gt3_styled_link__underline')
						}
						if (e.data.hover_underline) {
							jQuery(cont).addClass('gt3_styled_link__underline_on_hover')
						}
					}
				});

			},
			onpostrender: function (){
				var ctrl = this;
				var required = 'link_selection';
				editor.on( 'NodeChange', function (){
					var match = conditions[required]( editor );
					if ( !match && !ctrl.disabled() ){
						ctrl.disabled( true );
					}
					else if ( match && ctrl.disabled() ){
						ctrl.disabled( false );
					}
				});
			}
		}
		);



		editor.addButton('ListStyle', {
			text: ' List Styling',
			icon: ' fa fa-list',
			tooltip: '(Select a list)',
			onclick: function(){
				editor.windowManager.open({
					title: 'List Styling settings',
					width: 500,
					height: 120,
					body: [
						{
		                    type   : 'combobox',
		                    name   : 'icon_select',
		                    label  : 'Type icon code or choose from select',
		                    values : icons,
		                    value : 'fa fa-twitter-square' // Sets the default
		                },
						{
							type: 'textbox',
							name: 'icon_color',
							label: 'Icon Сolor',
							value  : '#',
							onPostRender: colorPickerFunc
						},
					],
					onsubmit: function(e){
						var icon_select = e.data.icon_select != '' ? e.data.icon_select : 'fa fa-check';
						var icon_color 	= e.data.icon_color != '' ? e.data.icon_color : '';
						var cont = tinyMCE.activeEditor.selection.getNode();
						if (cont.tagName == 'LI') {
							var element = jQuery(cont).parent('ul');
						}else if(cont.tagName == 'UL'){
							var element = jQuery(cont);
						}else if(cont.tagName != 'UL' || cont.tagName != 'LI'){
							var element = jQuery(cont).parentsUntil('ul');
						}else{
							return;
						}
						element.addClass('gt3_styled_list');
						element.children('li').children('.gt3_list__icon').remove();
						element.children('li').prepend('<i class="gt3_list__icon '+icon_select+'"'+(icon_color != '' ? 'style="color:'+icon_color+';"' : '')+'></i>');
					}
				});

			},
			onpostrender: function (){
				var ctrl = this;
				var required = 'list_selection';
				editor.on( 'NodeChange', function (){
					var match = conditions[required]( editor );
					if ( !match && !ctrl.disabled() ){
						ctrl.disabled( true );
					}
					else if ( match && ctrl.disabled() ){
						ctrl.disabled( false );
					}
				});
			}
		}
		);

		editor.addButton('Columns', {
			text: ' Columns',
			icon: ' fa fa-columns',
			tooltip: '(Select a Column)',
			onclick: function(){
				editor.windowManager.open({
					title: 'Columns settings',
					width: 500,
					height: 80,
					body: [
						{
		                    type   : 'listbox',
		                    name   : 'columns',
		                    label  : 'Choose Columns',
		                    values : [
		                        { text: '1/1', value: '12' },
		                        { text: '1/2 + 1/2', value: '6_6' },
		                        { text: '2/3 + 1/3', value: '8_4' },
		                        { text: '1/3 + 1/3 + 1/3', value: '4_4_4' },
		                        { text: '1/4 + 1/4 + 1/4 + 1/4', value: '3_3_3_3' },
		                        { text: '1/4 + 3/4', value: '3_9' },
		                        { text: '1/4 + 1/2 + 1/4', value: '3_6_3' },
		                        { text: '5/6 + 1/6', value: '10_2' },
		                        { text: '1/6 + 1/6 + 1/6 + 1/6 + 1/6 + 1/6', value: '2_2_2_2_2_2' },
		                        { text: '1/6 + 4/6 + 1/6', value: '2_8_2' },
		                        { text: '1/6 + 1/6 + 1/6 + 3/6', value: '2_2_2_6' },
		                    ],
		                    value : '12',
		                },
					],
					onsubmit: function(e){
						var selected_text = tinyMCE.activeEditor.selection.getContent();
						var columns = e.data.columns;
						var columns_array = columns.split("_");
						var first_column = '';
						for (var i = 0; i <= columns_array.length - 1; i++) {
							first_column += '<div class="span'+columns_array[i]+'">';
							if (i == 0) {
								first_column += selected_text;
							}
							first_column += '&nbsp;</div>';
						}
						editor.insertContent('<div class="row">' + first_column + '</div>');
					}
				});

			}
		}
		);

		editor.addButton('ToolTip', {
				text: ' ToolTip',
				icon: ' fa fa-hand-o-up',
				tooltip: 'Select a string',
				onclick: function(){
					var selection = editor.selection.getNode();
					if (!jQuery(selection).hasClass('gt3_custom_tooltip')) {
						editor.windowManager.open({
							title: 'ToolTip',
							width: 500,
							height: 80,
							body: [
								{
									type   : 'textbox',
									name   : 'tooltip',
									label  : 'Type ToolTip',
								},
							],
							onsubmit: function(e){
								var tooltip = e.data.tooltip;
								if (jQuery(selection).hasClass('gt3_custom_tooltip')) {
									jQuery(selection).removeClass('gt3_custom_tooltip').removeAttr('data-gt3-tooltip')
								}else if(tooltip.length){
									var cont = tinyMCE.activeEditor.getContent();
									var selected_text = tinyMCE.activeEditor.selection.getContent();
									editor.insertContent('<span class="gt3_custom_tooltip" data-gt3-tooltip="'+tooltip+'">' + selected_text + '</span>');
								}
							}
						});
					}else{
						jQuery(selection).removeClass('gt3_custom_tooltip').removeAttr('data-gt3-tooltip')
					}
				},

			}
		);

		var colorPickerFunc = function(){
			var panel = $(this.getEl());
			setTimeout(function(){
				var pos = panel.position();
				var id = panel.attr("id");
				panel.parent().append('<input type="text" name="color_input" value="'+panel[0].value+'" class="' + id + '">');
				var obj_c = $("." + id);
				obj_c.wpColorPicker({
					defaultColor: panel[0].value,
					change: function(event, ui){
						panel.val((obj_c.wpColorPicker('color')));
					},
					clear: function(){
					}
				}).parentsUntil(".wp-picker-container").parent('.wp-picker-container').css(
					{
						"background": "white",
						"position": "absolute",
						"padding": "5px 0 5px 5px",
						"top": pos.top,
						"left": pos.left,
						"right": "auto",
						"z-index": index--
					}
				);
				panel.hide();

			}, 200);
			panel.val(this.settings.color);
		};

		var numberFunc = function(){
			var panel = $(this.getEl());
			panel.attr('type','number');
		}

	});
})(jQuery);
