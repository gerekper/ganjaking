<?php

use Instagram\Includes\WIS_Plugin;
use YoutubeFeed\Api\YoutubeApi;

/**
 * Youtube widget Class
 */
class WYT_Widget extends WP_Widget {

	private static $app;

	/**
	 * @var WIS_Plugin
	 */
	public $plugin;

	/**
	 * @var array
	 */
	public $sliders;

	/**
	 * @var array
	 */
	public $options_linkto;

	/**
	 * @var array
	 *
	 */
	public $defaults;

	/**
	 * @var YoutubeApi
	 */
	public $api;

	public static function app() {
		return self::$app;
	}

	/**
	 * Initialize the plugin by registering widget and loading public scripts
	 *
	 */
	public function __construct() {
		self::$app = $this;

		// Widget ID and Class Setup
		parent::__construct( 'wyoutube_feed', __( 'Social Slider - Youtube', 'youtube-feed' ), array(
			'classname'   => 'wyoutube-feed',
			'description' => __( 'A widget that displays a Youtube videos ', 'youtube-feed' )
		) );

		$this->plugin         = WIS_Plugin::app();
		$this->sliders        = array(
            "default"          => 'Default'
		);
		$this->options_linkto = array(
			"none"       => 'None',
			"yt_link"    => 'Youtube link',
		);

		$this->defaults = array(
			'title'                => __( 'Youtube Feed', 'yft' ),
			'search'               => '',
			'blocked_users'        => '',
			'blocked_words'        => '',
			'template'             => 'slider',
			'yimages_link'          => 'post_page',
			'custom_url'           => '',
			'request_by'           => YoutubeApi::orderByRelevance,
			'orderby'              => 'rand',
			'images_number'        => 20,
			'columns'              => 2,
			'refresh_hour'         => 5,
		);

		$this->api = new YoutubeApi();

		/**
		 * Фильтр для добавления слайдеров
		 */
		$this->sliders = apply_filters( 'wyt/sliders', $this->sliders );

		/**
		 * Фильтр для добавления popup
		 */
		$this->options_linkto = apply_filters( 'wyt/options/link_to', $this->options_linkto );


		// Enqueue Plugin Styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'public_enqueue' ) );

		// Enqueue Plugin Styles and scripts for admin pages
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

		// Shortcode
		add_shortcode( 'cm_youtube_feed', array( $this, 'shortcode' ) );
		// Action to display posts
		add_action( 'wyoutube_feed', array( $this, 'display_posts' ) );

		//AJAX
		add_action( 'wp_ajax_wyt_add_account_by_token', array( $this, 'add_account_by_token' ) );

	}

	/**
	 * Register widget on widgets init
	 */
	public static function register_widget() {
		register_widget( __CLASS__ );
		register_sidebar( array(
			'name'        => __( 'Youtube Feed - Shortcode Generator', 'yft' ),
			'id'          => 'wyoutube-shortcodes',
			'description' => __( "1. Drag Youtube Feed Widget here. 2. Fill in the fields and hit save. 3. Copy the shortocde generated at the bottom of the widget form and use it on posts or pages.", 'yft' )
		) );
	}

	/**
	 * Enqueue public-facing Scripts and style sheet.
	 */
	public function public_enqueue() {

		wp_enqueue_style( WIS_Plugin::app()->getPrefix() . 'wyt-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );

		wp_enqueue_style( WIS_Plugin::app()->getPrefix() . 'wyt-instag-slider', WYT_PLUGIN_URL . '/assets/css/templates.css', array(), WIS_Plugin::app()->getPluginVersion() );
		wp_enqueue_script( WIS_Plugin::app()->getPrefix() . 'wyt-jquery-pllexi-slider', WYT_PLUGIN_URL . '/assets/js/jquery.flexslider-min.js', array( 'jquery' ), WIS_Plugin::app()->getPluginVersion(), false );
		//wp_enqueue_script( WIS_Plugin::app()->getPrefix() . 'wyoutube', WYT_PLUGIN_URL.'/assets/js/wyoutube.js', array(  ), WIS_Plugin::app()->getPluginVersion(), false );
		wp_enqueue_style( WIS_Plugin::app()->getPrefix() . 'wyt-header', WYT_PLUGIN_URL . '/assets/css/wyt-header.css', array(), WIS_Plugin::app()->getPluginVersion() );
<<<<<<< HEAD

		$ajax = json_encode([
			'url'   => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( "addAccountByToken" ),
		]);
		wp_add_inline_script( WIS_Plugin::app()->getPrefix() . 'wyoutube', "var ajax = $ajax;");
=======
		wp_localize_script( WIS_Plugin::app()->getPrefix() . 'wyoutube', 'ajax', array(
			'url'   => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( "addAccountByToken" ),
		) );
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c
	}

	/**
	 * Enqueue admin side scripts and styles
	 *
	 * @param string $hook
	 */
	public function admin_enqueue( $hook ) {


		if ( 'widgets.php' != $hook && 'post.php' != $hook ) {
			return;
		}
		wp_enqueue_style( 'wyoutube-admin-styles', WYT_PLUGIN_URL . '/admin/assets/css/wyoutube-admin.css', array(), WIS_Plugin::app()->getPluginVersion() );
		wp_enqueue_script( 'wyoutube-admin-script', WYT_PLUGIN_URL . '/admin/assets/js/wyoutube-admin.js', array( 'jquery' ), WIS_Plugin::app()->getPluginVersion(), true );

	}

	/**
	 * The Public view of the Widget
	 *
	 */
	public function widget( $args, $instance ) {

	    //Our variables from the widget settings.
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];

		// Display the widget title
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		do_action( 'wyoutube_feed', $instance );

		echo $args['after_widget'];
	}

	/**
	 * Widget Settings Form
	 *
	 */
	public function form( $instance ) {

	    /* @var $accounts \YoutubeFeed\Api\Channel\YoutubeChannelItem[] */
		$accounts = WIS_Plugin::app()->getPopulateOption( WYT_ACCOUNT_OPTION_NAME, array() );

		if ( ! is_array( $accounts ) ) {
			$accounts = array();
		}
		$sliders        = $this->sliders;
		$options_linkto = $this->options_linkto;

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $this->render_layout_template('widget_settings', [
			'this'           => $this,
		    'accounts'       => $accounts,
            'sliders'        => $sliders,
            'options_linkto' => $options_linkto,
            'instance'       => $instance
        ]);
	}

	/**
	 * Update the widget settings
	 *
	 * @param array $new_instance New instance values
	 * @param array $instance Old instance values
	 *
	 * @return array
	 */
	public function update( $new_instance, $instance ) {
		foreach ( $new_instance as $key => $item ) {
			$new_instance[ $key ] = isset( $new_instance[ $key ] ) ? $new_instance[ $key ] : $this->defaults[ $key ];
			if ( $key == 'title' ) {
				$new_instance[ $key ] = strip_tags( $new_instance[ $key ] );
			}
			$new_instance['widget_id'] = preg_replace( '/[^0-9]/', '', $this->id );
		}

		return $new_instance;
	}

	/**
	 * Update the widget settings
	 *
	 * @param array $instance instance values
	 *
	 * @return array
	 */
	public function defaults( $instance ) {
		$new_instance = [];
		foreach ( $instance as $key => $item ) {
			switch ( $key ) {
				case 'search':
				case 'blocked_users':
				case 'blocked_words':
					$new_instance[ $key ] = ! empty( $instance[ $key ] ) ? $instance[ $key ] : $this->defaults[ $key ];
					break;
				case 'images_number':
				case 'columns':
				case 'refresh_hour':
					$new_instance[ $key ] = absint( $instance[ $key ] );
					break;
				default:
					$new_instance[ $key ] = $instance[ $key ];
					break;
			}
		}

		$new_instance = wp_parse_args( (array) $new_instance, $this->defaults );

		return $new_instance;
	}

	/**
	 * Selected array function echoes selected if in array
	 *
	 * @param array $haystack The array to search in
	 * @param string $current The string value to search in array;
	 *
	 */
	public function selected( $haystack, $current ) {

		if ( is_array( $haystack ) && in_array( $current, $haystack ) ) {
			selected( 1, 1, true );
		}
	}


	/**
	 * Add shortcode function
	 *
	 * @param array $atts shortcode attributes
	 *
	 * @return mixed
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts( array( 'id' => '' ), $atts, 'cm_youtube_feed' );
		$args = get_option( 'widget_wyoutube_feed' );
		if ( isset( $args[ $atts['id'] ] ) ) {
			$args[ $atts['id'] ]['widget_id'] = $atts['id'];

			return $this->display( $args[ $atts['id'] ] );
		}

		return "";
	}

	/**
	 * Echoes the Display Instagram Images method
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	public function display_posts( $args ) {
		echo $this->display( $args );
	}

	/**
	 * Runs the query for images and returns the html
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	private function display( $args ) {

		$args = $this->defaults( $args );

		if ( ! empty( $args['description'] ) && ! is_array( $args['description'] ) ) {
			$args['description'] = explode( ',', $args['description'] );
		}

		if ( $args['refresh_hour'] == 0 ) {
			$args['refresh_hour'] = 5;
		}

		if ( $args['search'] ) {
                $account_data    = WIS_Plugin::app()->get_youtube_feeds( $args['search'] );
                /**
                 * @var $account_data \YoutubeFeed\Api\Channel\YoutubeChannelItem
                 */
				$account_data =  $account_data[$args['search']];
				$args['account'] = $account_data;
				$template_args   = $args;

				$images_data = $this->get_data( $args );

				if ( is_array( $images_data ) && ! empty( $images_data ) ) {
					if ( isset( $images_data['error'] ) ) {
						return $images_data['error'];
					}

					if ( $args['orderby'] != 'rand' ) {
						$args['orderby'] = explode( '-', $args['orderby'] );
						if ( $args['orderby'][0] == 'date' ) {
							$func = 'sort_timestamp_' . $args['orderby'][1];
						} else {
							$func = 'sort_popularity_' . $args['orderby'][1];
						}
						usort( $images_data, array( $this, $func ) );
					} else {
						shuffle( $images_data );
					}

					/* @var $images_data \YoutubeFeed\Api\Video\YoutubeVideo[] */
					foreach ( $images_data as $image_data )
						$template_args['posts'][] = $image_data;


					$return = "";

					if ( $args['show_feed_header'] ) {
						$return .= $this->render_layout_template( 'feed_header_template', [
							'account' => $account_data
						]);
					}

					if($args['yimages_link'] == 'ypopup' && $this->plugin->is_premium()){
						$return .= apply_filters( 'wyt/pro/display', $args, $images_data );
					}

					$return .= $this->render_layout_template( $args['template'], $template_args );

					return $return;
				} else {
					return __( 'No videos found', 'youtube-feed' );
				}
			}



		return "&nbsp;";
	}

	/**
	 * Method renders layout template
	 *
	 * @param string $template_name Template name without ".php"
	 *
	 * @param array $args Template arguments
	 *
	 * @return false|string
	 */
	private function render_layout_template( $template_name, $args ) {
		$path = WYT_PLUGIN_DIR . "/html_templates/$template_name.php";
		if ( file_exists( $path ) ) {
			ob_start();
			include $path;

			return ob_get_clean();
		} else {
			return 'This template does not exist!';
		}
	}



    /**
     * @param $response      \YoutubeFeed\Api\Video\YoutubeVideosResponse
     * @param $blocked_words string
     */
    private function filter_response_by_words(&$response, $blocked_words = ''){
        if(empty($blocked_words))
            return;
        else
            $blocked_words = explode(',', $blocked_words);
	    foreach ($response->items as $key => $video){
	        foreach ($blocked_words as $blocked_word){
	            $title = $video->snippet->title;
                if(stripos($title, $blocked_word))
                    unset($response->items[$key]);
            }
        }
    }

	/**
	 * Trigger refresh for new data
	 *
	 * @param bool $instaData
	 * @param array $old_args
	 * @param array $new_args
	 *
	 * @return bool
	 */
	private function trigger_refresh_data( $instaData, $old_args, $new_args ) {

		$trigger = 0;

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}

		if ( false === $instaData ) {
			$trigger = 1;
		}


		if ( isset( $old_args['saved_images'] ) ) {
			unset( $old_args['saved_images'] );
		}

		if ( isset( $old_args['deleted_images'] ) ) {
			unset( $old_args['deleted_images'] );
		}

		if ( is_array( $old_args ) && is_array( $new_args ) && array_diff( $old_args, $new_args ) !== array_diff( $new_args, $old_args ) ) {
			$trigger = 1;
		}

		if ( $trigger == 1 ) {
			return true;
		}

		return false;
	}

	/**
	 * Stores the fetched data from instagram in WordPress DB using transients
	 *
	 * @param array $search Array of widget settings
	 *
	 * @return array|string data
	 * @throws \Exception
	 */
	public function get_data( $search ) {
		$cache_hours   = $search['refresh_hour'];
		$images_number = $search['images_number'];
		$search_type   = 'channel';
		$search_name   = $search['account']->snippet->title;

		$blocked_users = isset( $search['blocked_users'] ) ? $search['blocked_users'] : '';
		$blocked_words = isset( $search['blocked_words'] ) ? $search['blocked_words'] : '';

		if ( ! isset( $search ) || empty( $search ) ) {
			return __( 'Nothing to search for', 'yft' );
		}


		$opt_name   = "wyoutube_{$search_type}-{$search_name}";
		$resultData = get_transient( $opt_name );
		$old_opts   = get_option( $opt_name, [] );
		$new_opts   = array(
			'search'        => $search_name,
			'blocked_users' => $blocked_users,
			'blocked_words' => $blocked_words,
			'cache_hours'   => $cache_hours,
			'images_number' => $images_number,
		);

		if ( $this->trigger_refresh_data( $resultData, $old_opts, $new_opts ) || ( defined( 'WYT_ENABLE_CACHING' ) && ! WYT_ENABLE_CACHING ) ) {
		//if ( true ) {

			$resultData                = array();
			$old_opts['search']        = $search_name;
			$old_opts['blocked_users'] = $blocked_users;
			$old_opts['blocked_words'] = $blocked_words;
			$old_opts['cache_hours']   = $cache_hours;
			$old_opts['images_number'] = $images_number;

			$images_number = ! $this->plugin->is_premium() && $images_number > 20 ? 20 : $images_number;

			$response = $this->api->getVideos( $search['search'], $images_number, $search['request_by'] );
            //$response = new \YoutubeFeed\Api\Video\YoutubeVideosResponse('{"kind":"youtube#searchListResponse","etag":"hXs_uL18ouAw3nGoc9-If1nP4fA","nextPageToken":"CAUQAA","regionCode":"RU","pageInfo":{"totalResults":102,"resultsPerPage":5},"items":[{"kind":"youtube#searchResult","etag":"TOnHNzr7OAV_KWMqm_GSriLU5NI","id":{"kind":"youtube#video","videoId":"S93c2zix5L4"},"snippet":{"publishedAt":"2019-08-28T08:54:23Z","channelId":"UCDLBW2M4KsUF7A7aHT8mxHw","title":"\u00ab\u0416\u0438\u0432\u043e\u0435\u00bb. \u041e\u0431\u0437\u043e\u0440 \u00ab\u041a\u0440\u0430\u0441\u043d\u043e\u0433\u043e \u0426\u0438\u043d\u0438\u043a\u0430\u00bb","description":"https:\/\/www.patreon.com\/user?u=5206451 \u2014 \u0441\u0442\u0440\u0430\u043d\u0438\u0446\u0430 \u043d\u0430 \u041f\u0430\u0442\u0440\u0435\u043e\u043d\u0435 http:\/\/redcynic.com https:\/\/vk.com\/public_redcynic - \u0433\u0440\u0443\u043f\u043f\u0430 \u00ab\u0412 \u043a\u043e\u043d\u0442\u0430\u043a\u0442\u0435\u00bb \u041d\u0435\u0442 \u043f\u0440\u0435\u0434\u0435\u043b\u0430 ...","thumbnails":{"default":{"url":"https:\/\/i.ytimg.com\/vi\/S93c2zix5L4\/default.jpg","width":120,"height":90},"medium":{"url":"https:\/\/i.ytimg.com\/vi\/S93c2zix5L4\/mqdefault.jpg","width":320,"height":180},"high":{"url":"https:\/\/i.ytimg.com\/vi\/S93c2zix5L4\/hqdefault.jpg","width":480,"height":360}},"channelTitle":"Red Cynic","liveBroadcastContent":"none","publishTime":"2019-08-28T08:54:23Z"}},{"kind":"youtube#searchResult","etag":"jvhpQC20dPda3rXtdKVDY0cB8co","id":{"kind":"youtube#video","videoId":"dW5O5sBUdpE"},"snippet":{"publishedAt":"2017-02-18T12:02:29Z","channelId":"UCDLBW2M4KsUF7A7aHT8mxHw","title":"\u00ab\u0427\u0443\u0436\u043e\u0439 \u043f\u0440\u043e\u0442\u0438\u0432 \u0425\u0438\u0449\u043d\u0438\u043a\u0430\u00bb. \u041e\u0431\u0437\u043e\u0440 \u00ab\u041a\u0440\u0430\u0441\u043d\u043e\u0433\u043e \u0426\u0438\u043d\u0438\u043a\u0430\u00bb","description":"https:\/\/www.patreon.com\/user?u=5206451 \u2014 \u0441\u0442\u0440\u0430\u043d\u0438\u0446\u0430 \u043d\u0430 \u041f\u0430\u0442\u0440\u0435\u043e\u043d\u0435 http:\/\/redcynic.com https:\/\/vk.com\/public_redcynic - \u0433\u0440\u0443\u043f\u043f\u0430 \u00ab\u0412 \u043a\u043e\u043d\u0442\u0430\u043a\u0442\u0435\u00bb \u0421\u043a\u0440\u0430\u0448\u0438\u0432\u0430\u044f ...","thumbnails":{"default":{"url":"https:\/\/i.ytimg.com\/vi\/dW5O5sBUdpE\/default.jpg","width":120,"height":90},"medium":{"url":"https:\/\/i.ytimg.com\/vi\/dW5O5sBUdpE\/mqdefault.jpg","width":320,"height":180},"high":{"url":"https:\/\/i.ytimg.com\/vi\/dW5O5sBUdpE\/hqdefault.jpg","width":480,"height":360}},"channelTitle":"Red Cynic","liveBroadcastContent":"none","publishTime":"2017-02-18T12:02:29Z"}}]}');

            $videos_ids = [];
            foreach ($response->items as $video)
                $videos_ids[] = $video->id->videoId;

            $videosData = $this->api->getVideosData($videos_ids);
            //$videosData = new \YoutubeFeed\Api\Video\YoutubeVideosResponse('{"kind": "youtube#videoListResponse","etag": "lzaBG4KxOEt-ECGeWDUuOR2MXfo","items": [{"kind": "youtube#video","etag": "c9XEcFkuSZuBLtF1d5or492heB8","id": "S93c2zix5L4","snippet": {"publishedAt": "2019-08-28T08:54:23Z","channelId": "UCDLBW2M4KsUF7A7aHT8mxHw","title": "«Живое». Обзор «Красного Циника»","description": "https://www.patreon.com/user?u=5206451 — страница на Патреоне\nhttp://redcynic.com \nhttps://vk.com/public_redcynic - группа «В контакте»\n\nНет предела человеческому уму и... глупости. Уже не первый раз мы сталкиваемся с сюжетом о неведомой космической чуде-юде, встречающейся с толпой людей в экстремальной ситуации. Причём, как правило, люди эти все как один опупенные специалисты. Фильм «Живое» тоже посвящён такому. И если уж исследовать подобные истории, то с целью найти идеальный вариант. Ума или глупости... \n\n*****\n\nПатреон: \nhttps://www.patreon.com/bePatron?u=5206451\n\nhttp://www.donationalerts.ru/r/redcynic\n\nКошельки:\n\nWebMoney: \nR223381292090; \nZ222361875129. \n\nYandex.Money: \n410011854513048.","thumbnails": {"default": {"url": "https://i.ytimg.com/vi/S93c2zix5L4/default.jpg","width": 120,"height": 90},"medium": {"url": "https://i.ytimg.com/vi/S93c2zix5L4/mqdefault.jpg","width": 320,"height": 180},"high": {"url": "https://i.ytimg.com/vi/S93c2zix5L4/hqdefault.jpg","width": 480,"height": 360},"standard": {"url": "https://i.ytimg.com/vi/S93c2zix5L4/sddefault.jpg","width": 640,"height": 480}},"channelTitle": "Red Cynic","tags": ["Живое","Джейк Джилленхол","Ребекка Фергюсон","Райан Рейнольдс","Видеорецензия","Рецензия","Обзор","Подкаст","Красный Циник","Циник","Life","Jake Gyllenhaal","Rebecca Ferguson","Ryan Reynolds","Red Cynic","Videoreview","Review","Redcynic","Video Review","Podcast"],"categoryId": "1","liveBroadcastContent": "none","localized": {"title": "«Живое». Обзор «Красного Циника»","description": "https://www.patreon.com/user?u=5206451 — страница на Патреоне\nhttp://redcynic.com \nhttps://vk.com/public_redcynic - группа «В контакте»\n\nНет предела человеческому уму и... глупости. Уже не первый раз мы сталкиваемся с сюжетом о неведомой космической чуде-юде, встречающейся с толпой людей в экстремальной ситуации. Причём, как правило, люди эти все как один опупенные специалисты. Фильм «Живое» тоже посвящён такому. И если уж исследовать подобные истории, то с целью найти идеальный вариант. Ума или глупости... \n\n*****\n\nПатреон: \nhttps://www.patreon.com/bePatron?u=5206451\n\nhttp://www.donationalerts.ru/r/redcynic\n\nКошельки:\n\nWebMoney: \nR223381292090; \nZ222361875129. \n\nYandex.Money: \n410011854513048."}},"statistics": {"viewCount": "286389","likeCount": "25552","dislikeCount": "438","favoriteCount": "0","commentCount": "1302"}},{"kind": "youtube#video","etag": "Pos6oFmOSFs6i4nmag9vcT7k-xo","id": "dW5O5sBUdpE","snippet": {"publishedAt": "2017-02-18T12:02:29Z","channelId": "UCDLBW2M4KsUF7A7aHT8mxHw","title": "«Чужой против Хищника». Обзор «Красного Циника»","description": "https://www.patreon.com/user?u=5206451 — страница на Патреоне\nhttp://redcynic.com \nhttps://vk.com/public_redcynic - группа «В контакте»\n\nСкрашивая ваше ожидание большого обзора, мы рассмотрим начало противоречивого подфранчайза, фильм \"Чужой против Хищника\".\n\n*****\n\nЕсли хотите финансово помочь каналу, то мы будем очень благодарны. \n\nКошельки:\n\nWebMoney: \nR223381292090; \nZ222361875129. \n\nYandex.Money: \n410011854513048.","thumbnails": {"default": {"url": "https://i.ytimg.com/vi/dW5O5sBUdpE/default.jpg","width": 120,"height": 90},"medium": {"url": "https://i.ytimg.com/vi/dW5O5sBUdpE/mqdefault.jpg","width": 320,"height": 180},"high": {"url": "https://i.ytimg.com/vi/dW5O5sBUdpE/hqdefault.jpg","width": 480,"height": 360},"standard": {"url": "https://i.ytimg.com/vi/dW5O5sBUdpE/sddefault.jpg","width": 640,"height": 480}},"channelTitle": "Red Cynic","tags": ["Хищник","Хищники","Чужой","Чужие","Пол Андерсон","Видеорецензия","Рецензия","Обзор","Подкаст","Красный Циник","Циник","Predator","Predators","Alien","Aliens","Paul W. S. Anderson","Red Cynic","Videoreview","Review","Redcynic","Video Review","Podcast"],"categoryId": "1","liveBroadcastContent": "none","defaultLanguage": "ru","localized": {"title": "«Чужой против Хищника». Обзор «Красного Циника»","description": "https://www.patreon.com/user?u=5206451 — страница на Патреоне\nhttp://redcynic.com \nhttps://vk.com/public_redcynic - группа «В контакте»\n\nСкрашивая ваше ожидание большого обзора, мы рассмотрим начало противоречивого подфранчайза, фильм \"Чужой против Хищника\".\n\n*****\n\nЕсли хотите финансово помочь каналу, то мы будем очень благодарны. \n\nКошельки:\n\nWebMoney: \nR223381292090; \nZ222361875129. \n\nYandex.Money: \n410011854513048."},"defaultAudioLanguage": "ru-Latn"},"statistics": {"viewCount": "300141","likeCount": "20796","dislikeCount": "435","favoriteCount": "0","commentCount": "890"}}],"pageInfo": {"totalResults": 5,"resultsPerPage": 5}}');

            foreach ($response->items as $key => $video){
                $video->snippet = $videosData->items[$key]->snippet;
                $video->statistics = $videosData->items[$key]->statistics;
                $video->comments = $this->api->getCommentsByVideoId($video->id->videoId);
            }

            $this->filter_response_by_words($response, $blocked_words);


			if ( $response ) {
				if ( ! is_array( $response->items ) || ! count( $response->items ) ) {
					return [ 'error' => __( 'There are no publications in this account yet', 'yft' ) ];
				}
				$results = $response;
			} else {
				if ( $resultData ) {
					$results = $resultData;
				}
			}

			if ( empty( $results ) ) {
				return [ 'error' => __( 'No images found', 'yft' ) ];
			}

			foreach ( $results->items as $item ) {
				$resultData[] = $item;
			} // end -> foreach

			update_option( $opt_name, $old_opts );

			if ( is_array( $resultData ) && ! empty( $resultData ) ) {
				set_transient( $opt_name, $resultData, $cache_hours * 60 * 60 );
			}

		} // end -> false === $instaData

		return $resultData;
	}

	/**
	 * Sort Function for timestamp Ascending
	 */
	public function sort_timestamp_ASC( $a, $b ) {
		return $a->snippet->publishedAt > $b->snippet->publishedAt;
	}

	/**
	 * Sort Function for timestamp Descending
	 */
	public function sort_timestamp_DESC( $a, $b ) {
        return $a->snippet->publishedAt < $b->snippet->publishedAt;
	}

	/**
	 * Sort Function for popularity Ascending
	 */
	public function sort_popularity_ASC( $a, $b ) {
		return $a->statistics->viewCount > $b->statistics->viewCount;
	}

	/**
	 * Sort Function for popularity Descending
	 */
	public function sort_popularity_DESC( $a, $b ) {
        return $a->statistics->viewCount < $b->statistics->viewCount;
	}

	/**
	 * Get count of accounts
	 *
	 * @return int
	 */
	public function count_accounts() {
		$account = WIS_Plugin::app()->getOption( WYT_ACCOUNT_OPTION_NAME, array() );

		return count( $account );
	}

} // end of class WYT_Widget
?>
