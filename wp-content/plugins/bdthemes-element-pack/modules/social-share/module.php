<?php
namespace ElementPack\Modules\SocialShare;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {


	private static $medias = [
		'baidu' => [
			'title' => 'Baidu',
		],
		'blogger' => [
			'title' => 'Blogger',
		],
		'buffer' => [
			'title'       => 'Buffer',
			'has_counter' => true,
		],
		'delicious' => [
			'title' => 'Delicious',
		],
		'digg' => [
			'title' => 'Digg',
		],
		'evernote' => [
			'title' => 'Evernote',
		],
		'facebook' => [
			'title'       => 'Facebook',
			'has_counter' => true,
		],
		'flipboard' => [
			'title' => 'Flipboard',
		],
		'instapaper' => [
			'title' => 'Instapaper',
		],
		'linkedin' => [
			'title'       => 'Linkedin',
			'has_counter' => true,
		],
		'liveinternet' => [
			'title' => 'LiveInternet',
		],
		'livejournal' => [
			'title' => 'LiveJournal',
		],
		'mix' => [
			'title' => 'Mix',
		],
		'moimir' => [
			'title'       => 'Mail.Ru',
			'has_counter' => true,
		],
		'meneame' => [
			'title' => 'meneame',
		],
		'odnoklassniki' => [
			'title'       => 'OK',
			'has_counter' => true,
		],
		'pocket' => [
			'title'       => 'Pocket',
			'has_counter' => true,
		],
		'pinterest' => [
			'title'       => 'Pinterest',
			'has_counter' => true,
		],
		'reddit' => [
			'title' => 'Reddit',
		],
		'renren' => [
			'title' => 'Renren',
		],
		'tumblr' => [
			'title'       => 'Tumblr',
			'has_counter' => true,
		],
		'surfingbird' => [
			'title' => 'Surfingbird',
		],
		'twitter' => [
			'title' => 'Twitter',
		],
		'vkontakte' => [
			'title'       => 'Vkontakte',
			'has_counter' => true,
		],
		'weibo' => [
			'title' => 'Weibo',
		],
		'wordpress' => [
			'title' => 'Wordpress',
		],
		'xing' => [
			'title' => 'Xing',
		],
		// Mobile Device Sharing
		'line' => [
			'title' => 'LINE',
		],
		'skype' => [
			'title' => 'Skype',
		],
		'telegram' => [
			'title' => 'Telegram',
		],
		'viber' => [
			'title' => 'Viber',
		],
		'wechat' => [
			'title' => 'WeChat',
		],
		'whatsapp' => [
			'title' => 'WhatsApp',
		],
		'link' => [
			'title'       => 'Copy Link',
		],
	];

	public static function get_social_media( $media_name = null ) {
		if ( $media_name ) {
			return isset( self::$medias[ $media_name ] ) ? self::$medias[ $media_name ] : null;
		}

		return self::$medias;
	}

	public function get_name() {
		return 'social';
	}

	public function get_widgets() {

		$widgets = [
			'Social_Share',
		];

		return $widgets;
	}
}
