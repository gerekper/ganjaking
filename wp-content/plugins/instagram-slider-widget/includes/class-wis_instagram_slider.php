<?php
/**
 * WIS_InstagramSlider Class
 */
class WIS_InstagramSlider extends WP_Widget {

    private static $app;

	const USERNAME_URL = 'https://www.instagram.com/{username}/';
	const TAG_URL = 'https://www.instagram.com/explore/tags/{tag}/?__a=1';
	//const USERS_SELF_URL = 'https://api.instagram.com/v1/users/self/';
	//const USERS_SELF_MEDIA_URL = 'https://api.instagram.com/v1/users/self/media/recent/';
	const USERS_SELF_URL = 'https://graph.instagram.com/me';
	const USERS_SELF_MEDIA_URL = 'https://graph.instagram.com/';

	const USERS_SELF_URL_NEW = 'https://graph.facebook.com/';

	/**
	 * @var WIS_Plugin
	 */
	public $WIS;

	/**
	 * @var array
	 */
	public $sliders;

	/**
	 * @var array
	 */
	public $options_linkto;

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
		parent::__construct( 'jr_insta_slider', __( 'Social Slider - Instagram', 'instagram-slider-widget' ), array(
				'classname' => 'jr-insta-slider',
				'description' => __( 'A widget that displays a slider with instagram images ', 'instagram-slider-widget' )
			)
		);

		$this->WIS = WIS_Plugin::app();
		$this->sliders = array(
			"slider"           => 'Slider - Normal',
			"slider-overlay"   => 'Slider - Overlay Text',
			"thumbs"           => 'Thumbnails',
			"thumbs-no-border" => 'Thumbnails - Without Border',
		);
        $this->options_linkto = array(
            "image_link" => 'Instagram Image',
            "image_url" => 'Image URL',
            "custom_url" => 'Custom Link',
            "none" => 'None'
        );

		/**
		 * Фильтр для добавления слайдеров
		 */
		$this->sliders = apply_filters('wis/sliders', $this->sliders);

		/**
		 * Фильтр для добавления popup
		 */
        $this->options_linkto = apply_filters('wis/options/link_to', $this->options_linkto);



        // Shortcode
		add_shortcode( 'jr_instagram', array( $this, 'shortcode' ) );

		// Instgram Action to display images
		add_action( 'jr_instagram', array( $this, 'instagram_images' ) );

		// Enqueue Plugin Styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this,	'public_enqueue' ) );

		// Enqueue Plugin Styles and scripts for admin pages
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

		// Ajax action to unblock images from widget
		add_action( 'wp_ajax_jr_delete_insta_dupes', array( $this, 'delete_dupes' ) );

		// Add new attachment field desctiptions
		add_filter( 'attachment_fields_to_edit', array( $this, 'insta_attachment_fields' ) , 10, 2 );

		// Add action for single cron events
		add_action( 'jr_insta_cron', array( $this, 'jr_cron_trigger' ), 10 , 3 );

		add_action( 'wp_ajax_wis_add_account_by_token', array( $this, 'add_account_by_token' ) );

		add_action( 'wp_ajax_wis_delete_account', array( $this, 'delete_account' ) );

	}

	/**
	 * Register widget on widgets init
	 */
	public static function register_widget() {
		register_widget( __CLASS__ );
		register_sidebar( array(
				'name' => __( 'Social Slider - Shortcode Generator', 'instagram-slider-widget' ),
				'id' => 'jr-insta-shortcodes',
				'description' => __( "1. Drag Social Slider Widget here. 2. Fill in the fields and hit save. 3. Copy the shortocde generated at the bottom of the widget form and use it on posts or pages.", 'instagram-slider-widget' )
			)
		);
	}

	/**
	 * Enqueue public-facing Scripts and style sheet.
	 */
	public function public_enqueue() {

		wp_enqueue_style( WIS_Plugin::app()->getPrefix() . 'font-awesome',
            'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'
        );

		wp_enqueue_style(  WIS_Plugin::app()->getPrefix() . 'instag-slider', WIS_PLUGIN_URL.'/assets/css/instag-slider.css', array(), WIS_Plugin::app()->getPluginVersion() );
		wp_enqueue_script( WIS_Plugin::app()->getPrefix() . 'jquery-pllexi-slider', WIS_PLUGIN_URL.'/assets/js/jquery.flexslider-min.js', array( 'jquery' ), WIS_Plugin::app()->getPluginVersion(), false );
		//wp_enqueue_script( WIS_Plugin::app()->getPrefix() . 'jr-insta', WIS_PLUGIN_URL.'/assets/js/jr-insta.js', array(  ), WIS_Plugin::app()->getPluginVersion(), false );
		wp_enqueue_style(  WIS_Plugin::app()->getPrefix() . 'wis-header', WIS_PLUGIN_URL.'/assets/css/wis-header.css', array(), WIS_Plugin::app()->getPluginVersion() );
		wp_localize_script( WIS_Plugin::app()->getPrefix() . 'jr-insta', 'ajax',
			array(
				'url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce("addAccountByToken"),
			)
		);	}

	/**
	 * Enqueue admin side scripts and styles
	 *
	 * @param  string $hook
	 */
	public function admin_enqueue( $hook ) {

		if ( 'widgets.php' != $hook ) {
			return;
		}
		wp_enqueue_style( 'jr-insta-admin-styles', WIS_PLUGIN_DIR.'/admin/assets/css/jr-insta-admin.css', array(), WIS_Plugin::app()->getPluginVersion() );
		wp_enqueue_script( 'jr-insta-admin-script', WIS_PLUGIN_DIR.'/admin/assets/js/jr-insta-admin.js',  array( 'jquery' ), WIS_Plugin::app()->getPluginVersion(), true );

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

		do_action( 'jr_instagram', $instance );

		echo $args['after_widget'];
	}

	/**
	 * Update the widget settings
	 *
	 * @param    array    $new_instance    New instance values
	 * @param    array    $instance    Old instance values
	 *
	 * @return array
	 */
	public function update( $new_instance, $instance ) {

		$instance['title']            = strip_tags( isset($new_instance['title']) ? $new_instance['title'] : null );
		$instance['search_for']       = isset($new_instance['search_for']) ? $new_instance['search_for'] : null;
		$instance['username']         = isset($new_instance['username']) ? $new_instance['username'] : null;
		$instance['account']          = isset($new_instance['account']) ? $new_instance['account'] : null;
		$instance['account_business'] = isset($new_instance['account_business']) ? $new_instance['account_business'] : null;
		$instance['hashtag']          = isset($new_instance['hashtag']) ? $new_instance['hashtag'] : null;
		$instance['blocked_users']    = isset($new_instance['blocked_users']) ? $new_instance['blocked_users'] : null;
		$instance['blocked_words']    = isset($new_instance['blocked_words']) ? $new_instance['blocked_words'] : null;
		$instance['attachment']       = isset($new_instance['attachment']) ? $new_instance['attachment'] : null;
		$instance['template']         = isset($new_instance['template']) ? $new_instance['template'] : 'slider';
		$instance['images_link']      = isset($new_instance['images_link']) ? $new_instance['images_link']  : 'image_link';
		$instance['custom_url']       = isset($new_instance['custom_url']) ?$new_instance['custom_url'] : null;
		$instance['orderby']          = isset($new_instance['orderby']) ? $new_instance['orderby'] : 'rand';
		$instance['images_number']    = isset($new_instance['images_number']) ? $new_instance['images_number'] : 20;
		$instance['columns']          = isset($new_instance['columns']) ? $new_instance['columns'] : 4;
		$instance['refresh_hour']     = isset($new_instance['refresh_hour']) ? $new_instance['refresh_hour'] : 5;
		$instance['image_size']       = isset($new_instance['image_size']) ? $new_instance['image_size'] : 'standard';
		$instance['image_link_rel']   = isset($new_instance['image_link_rel']) ? $new_instance['image_link_rel'] : null;
		$instance['image_link_class'] = isset($new_instance['image_link_class']) ? $new_instance['image_link_class'] : null;
		$instance['no_pin']           = isset($new_instance['no_pin']) ? $new_instance['no_pin'] : null;
		$instance['controls']         = isset($new_instance['controls']) ? $new_instance['controls'] : 'prev_next';
		$instance['animation']        = isset($new_instance['animation']) ? $new_instance['animation'] : 'slide';
		$instance['caption_words']    = isset($new_instance['caption_words']) ? $new_instance['caption_words'] : 20;
		$instance['slidespeed']       = isset($new_instance['slidespeed']) ? $new_instance['slidespeed'] : 7000;
		$instance['description']      = isset($new_instance['description']) ? $new_instance['description'] : array( 'username', 'time','caption' );
		$instance['support_author']   = isset($new_instance['support_author']) ? $new_instance['support_author'] : null;
		$instance['gutter']           = isset($new_instance['gutter']) ? $new_instance['gutter'] : null;
		$instance['masonry_image_width']  = isset($new_instance['masonry_image_width']) ? $new_instance['masonry_image_width'] : 200;
		$instance['slick_slides_to_show'] = isset($new_instance['slick_slides_to_show']) ? $new_instance['slick_slides_to_show'] : 3;
		$instance['slick_slides_padding'] = isset($new_instance['slick_slides_padding']) ? $new_instance['slick_slides_padding'] : 0;
		$instance['show_feed_header'] = isset($new_instance['show_feed_header']) ? $new_instance['show_feed_header'] : 0;
		$instance['highlight_offset'] = isset($new_instance['highlight_offset']) ? $new_instance['highlight_offset'] : 1;
		$instance['highlight_pattern'] = isset($new_instance['highlight_pattern']) ? $new_instance['highlight_pattern'] : 6;

		return $instance;
	}


	/**
	 * Widget Settings Form
	 *
	 */
	public function form( $instance ) {

		$accounts          = WIS_Plugin::app()->getPopulateOption( 'account_profiles', array() );
		$accounts_business = WIS_Plugin::app()->getPopulateOption( 'account_profiles_new', array() );
		if(!is_array($accounts)) $accounts = array();
		if(!is_array($accounts_business)) $accounts_business = array();
		$sliders = $this->sliders;
        $options_linkto = $this->options_linkto;

		if( count($accounts)) $s_for = 'account';
		else if( count($accounts_business) ) $s_for = 'account_business';
		else $s_for =  'username';
        $defaults = array(
			'title'            => __('Social Slider', 'instagram-slider-widget'),
			'search_for'       => $s_for,
			'account'          => '',
			'account_business' => '',
			'username'         => '',
			'hashtag'          => '',
			'blocked_users'    => '',
			'blocked_words'    => '',
			'attachment' 	   => 0,
			'template'         => 'slider',
			'images_link'      => 'image_link',
			'custom_url'       => '',
			'orderby'          => 'rand',
			'images_number'    => 20,
			'columns'          => 4,
			'refresh_hour'     => 5,
			'image_size'       => 'standard',
			'image_link_rel'   => '',
			'image_link_class' => '',
			'no_pin' 	       => 0,
			'controls'		   => 'prev_next',
			'animation'        => 'slide',
			'caption_words'    => 20,
			'slidespeed'       => 7000,
			'description'      => array( 'username', 'time','caption' ),
			'support_author'   => 0,
            'gutter'           => 0,
            'masonry_image_width' => 200,
            'slick_slides_to_show' => 3,
            'slick_slides_padding' => 0,
            'show_feed_header' => 1,
            'highlight_offset' => 1,
            'highlight_pattern' => 6,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		?>
        <div class="jr-container">
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><strong><?php _e('Title:', 'instagram-slider-widget'); ?></strong></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
            </p>
            <p>
                <strong><?php _e( 'Search Instagram for:', 'instagram-slider-widget' ); ?></strong>
                <span class="jr-search-for-container">
                    <label class="jr-seach-for">
                        <input type="radio" id="<?php echo $this->get_field_id( 'search_for' ); ?>" name="<?php echo $this->get_field_name( 'search_for' ); ?>" value="account" <?php checked( 'account', $instance['search_for'] ); ?> />
                        <?php _e( 'Account:', 'instagram-slider-widget' ); ?>
                    </label>
                    <?php
                    if(count($accounts))
                    {?>

                        <select id="<?php echo $this->get_field_id( 'account' ); ?>" class="" name="<?php echo $this->get_field_name( 'account' ); ?>"><?php
                        foreach ($accounts as $acc)
                            {
	                            $selected = $instance['account'] == $acc['username'] ? "selected='selected'" : "";
	                            echo "<option value='{$acc['username']}' {$selected}>{$acc['username']}</option>";
                            }
                            ?>
                        </select><?php
                    }
                    else{
                        echo "<a href='".admin_url('admin.php?page=settings-wisw')."'>".__('Add account in settings','instagram-slider-widget')."</a>";
                    }
                    ?>
                </span>
                <span class="jr-search-for-container">
                    <label class="jr-seach-for">
                        <input type="radio" id="<?php echo $this->get_field_id( 'search_for' ); ?>" name="<?php echo $this->get_field_name( 'search_for' ); ?>" value="account_business" <?php checked( 'account_business', $instance['search_for'] ); ?> />
                        <?php _e( 'Business account:', 'instagram-slider-widget' ); ?>
                    </label>
                    <?php
                    if(count($accounts_business))
                    {?>

                        <select id="<?php echo $this->get_field_id( 'account_business' ); ?>" class="" name="<?php echo $this->get_field_name( 'account_business' ); ?>"><?php
                        foreach ($accounts_business as $acc)
                            {
	                            $selected = $instance['account_business'] == $acc['username'] ? "selected='selected'" : "";
	                            echo "<option value='{$acc['username']}' {$selected}>{$acc['username']}</option>";
                            }
                            ?>
                        </select><?php
                    }
                    else{
                        echo "<a href='".admin_url('admin.php?page=settings-wisw')."'>".__('Add account in settings','instagram-slider-widget')."</a>";
                    }
                    ?>
                </span>
                <span class="jr-search-for-container"><label class="jr-seach-for"><input type="radio" id="<?php echo $this->get_field_id( 'search_for' ); ?>" name="<?php echo $this->get_field_name( 'search_for' ); ?>" value="username" <?php checked( 'username', $instance['search_for'] ); ?> /> <?php _e( 'Username:', 'instagram-slider-widget' ); ?></label> <input id="<?php echo $this->get_field_id( 'username' ); ?>" class="inline-field-text" name="<?php echo $this->get_field_name( 'username' ); ?>" value="<?php echo $instance['username']; ?>" /></span>
                <span class="jr-search-for-container"><label class="jr-seach-for"><input type="radio" id="<?php echo $this->get_field_id( 'search_for' ); ?>" name="<?php echo $this->get_field_name( 'search_for' ); ?>" value="hashtag" <?php checked( 'hashtag', $instance['search_for'] ); ?> /> <?php _e( 'Hashtag:', 'instagram-slider-widget' ); ?></label> <input id="<?php echo $this->get_field_id( 'hashtag' ); ?>" class="inline-field-text" name="<?php echo $this->get_field_name( 'hashtag' ); ?>" value="<?php echo $instance['hashtag']; ?>" placeholder="<?php _e('without # sign', 'instagram-slider-widget') ?>" /></span>
            </p>
            <p class="<?php if ( 'hashtag' != $instance['search_for'] ) echo 'hidden'; ?>">
                <label for="<?php echo $this->get_field_id( 'blocked_users' ); ?>"><?php _e( 'Block Users', 'instagram-slider-widget' ); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'blocked_users' ); ?>" name="<?php echo $this->get_field_name( 'blocked_users' ); ?>" value="<?php echo $instance['blocked_users']; ?>" />
                <span class="jr-description"><?php _e( 'Enter words separated by commas whose images you don\'t want to show', 'instagram-slider-widget' ); ?></span>
            </p>
            <p class="<?php if ( 'hashtag' == $instance['search_for'] ) echo 'hidden'; ?>">
                <label for="<?php echo $this->get_field_id( 'blocked_words' ); ?>"><?php _e( 'Block words', 'instagram-slider-widget' ); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'blocked_words' ); ?>" name="<?php echo $this->get_field_name( 'blocked_words' ); ?>" value="<?php echo $instance['blocked_words']; ?>" />
                <span class="jr-description"><?php _e( 'Enter comma-separated words. If one of them occurs in the image description, the image will not be displayed', 'instagram-slider-widget' ); ?></span>
            </p>
            <p class="<?php if ( 'username' != $instance['search_for'] ) echo 'hidden'; ?>"><strong><?php _e( 'Save in Media Library: ', 'instagram-slider-widget' ); ?></strong>

                <label class="switch" for="<?php echo $this->get_field_id( 'attachment' ); ?>">
                    <input class="widefat" id="<?php echo $this->get_field_id( 'attachment' ); ?>" name="<?php echo $this->get_field_name( 'attachment' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['attachment'] ); ?> /><span class="slider round"></span></label>
                <br><span class="jr-description"><?php _e( ' Turn on to save Instagram Images into WordPress media library.', 'instagram-slider-widget') ?></span>
				<?php
				if ( isset ( $instance['username'] ) && !empty($instance['username'] ) ) {
					echo '<br><button class="button action jr-delete-instagram-dupes" type="button" data-username="'.$instance['username'].'"><strong>Remove</strong> duplicate images for <strong>'.$instance['username'].'</strong></button><span class="jr-spinner"></span>';
					echo '<br><br><strong><span class="deleted-dupes-info"></span></strong>';
					wp_nonce_field( 'jr_delete_instagram_dupes', 'delete_insta_dupes_nonce' );
				}
				?>
            </p>
            <p id="img_to_show">
                <label  for="<?php echo $this->get_field_id( 'images_number' ); ?>"><strong><?php _e( 'Count of images to show:', 'instagram-slider-widget' ); ?></strong>
                    <input  class="small-text" type="number" min="1" max="" id="<?php echo $this->get_field_id( 'images_number' ); ?>" name="<?php echo $this->get_field_name( 'images_number' ); ?>" value="<?php echo $instance['images_number']; ?>" />
                    <span class="jr-description">
                        <?php if(!$this->WIS->is_premium()) {
			                    _e( 'Maximum 20 images in free version.', 'instagram-slider-widget' );
			                     echo " ".sprintf( __( "More in <a href='%s'>PRO version</a>", 'instagram-slider-widget' ), $this->WIS->get_support()->get_pricing_url(true, "wis_widget_settings") );
                    		}
                         ?>
                    </span>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'refresh_hour' ); ?>"><strong><?php _e( 'Check for new images every:', 'instagram-slider-widget' ); ?></strong>
                    <input  class="small-text" type="number" min="1" max="200" id="<?php echo $this->get_field_id( 'refresh_hour' ); ?>" name="<?php echo $this->get_field_name( 'refresh_hour' ); ?>" value="<?php echo $instance['refresh_hour']; ?>" />
                    <span><?php _e('hours', 'instagram-slider-widget'); ?></span>
                </label>
            </p>
            <p class="show_feed_header <?php if ( 'account_business' != $instance['search_for']) echo 'hidden'; ?>">
                <strong><?php _e( 'Show feed header:', 'instagram-slider-widget' ); ?></strong>
                <label class="switch" for="<?php echo $this->get_field_id( 'show_feed_header' ); ?>">
                    <input class="widefat" id="<?php echo $this->get_field_id( 'show_feed_header' ); ?>" name="<?php echo $this->get_field_name( 'show_feed_header' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['show_feed_header'] ); ?> />
                    <span class="slider round"></span>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'template' ); ?>"><strong><?php _e( 'Template', 'instagram-slider-widget' ); ?></strong>
                    <select class="widefat" name="<?php echo $this->get_field_name( 'template' ); ?>" id="<?php echo $this->get_field_id( 'template' ); ?>">
	                    <?php
	                    if(count($sliders)) {
		                    foreach ($sliders as $key => $slider) {
			                    $selected = ($instance['template'] == $key) ? "selected='selected'" : '';
			                    echo "<option value='{$key}' {$selected}>{$slider}</option>\n";
		                    }
	                    }
	                    if(!$this->WIS->is_premium())
                        {
                            ?>
                            <optgroup label="Available in PRO">
                                <option value='1' disabled="disabled">Slick</option>
                                <option value='2' disabled="disabled">Masonry</option>
                                <option value='3' disabled="disabled">Highlight</option>
                            </optgroup>
                            <?php
                        }
	                    ?>
                    </select>
                </label>
            </p>
            <span id="masonry_notice" class="masonry_notice jr-description <?php if ( 'masonry' != $instance['template']) echo 'hidden'; ?>"><?php _e("Not recommended for <strong>sidebar</strong>")?></span>
            <p class="<?php if ( 'thumbs' != $instance['template'] && 'thumbs-no-border' != $instance['template'] ) echo 'hidden'; ?>">
                <label  for="<?php echo $this->get_field_id( 'columns' ); ?>"><strong><?php _e( 'Number of Columns:', 'instagram-slider-widget' ); ?></strong>
                    <input class="small-text" id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>" value="<?php echo $instance['columns']; ?>" />
                    <span class='jr-description'><?php _e('max is 10 ( only for thumbnails template )', 'instagram-slider-widget'); ?></span>
                </label>
            </p>
            <p class="masonry_settings <?php if ( 'masonry' != $instance['template']) echo 'hidden'; ?>">
                <label  for="<?php echo $this->get_field_id( 'gutter' ); ?>"><strong><?php _e( 'Vertical space between item elements:', 'instagram-slider-widget' ); ?></strong>
                    <input class="small-text" id="<?php echo $this->get_field_id( 'gutter' ); ?>" name="<?php echo $this->get_field_name( 'gutter' ); ?>" value="<?php echo $instance['gutter']; ?>" />
                    <span><?php _e('px', 'instagram-slider-widget'); ?></span>
                </label>
                <br>
                <label  for="<?php echo $this->get_field_id( 'masonry_image_width' ); ?>"><strong><?php _e( 'Image width:', 'instagram-slider-widget' ); ?></strong>
                    <input class="small-text" id="<?php echo $this->get_field_id( 'masonry_image_width' ); ?>" name="<?php echo $this->get_field_name( 'masonry_image_width' ); ?>" value="<?php echo $instance['masonry_image_width']; ?>" />
                    <span><?php _e('px', 'instagram-slider-widget'); ?></span>
                </label>
            </p>
            <p class="slick_settings <?php if ( 'slick_slider' != $instance['template']) echo 'hidden'; ?>">
                <label  for="<?php echo $this->get_field_id( 'slick_slides_to_show' ); ?>"><strong><?php _e( 'Pictures per slide:', 'instagram-slider-widget' ); ?></strong>
                    <input class="small-text" id="<?php echo $this->get_field_id( 'slick_slides_to_show' ); ?>" name="<?php echo $this->get_field_name( 'slick_slides_to_show' ); ?>" value="<?php echo $instance['slick_slides_to_show']; ?>" />
                    <span><?php _e('pictures', 'instagram-slider-widget'); ?></span>
                </label>
                <br>
                <strong><?php _e( 'Space between pictures:', 'instagram-slider-widget' ); ?></strong>
                <label class="switch" for="<?php echo $this->get_field_id( 'slick_slides_padding' ); ?>">
                    <input class="widefat" id="<?php echo $this->get_field_id( 'slick_slides_padding' ); ?>" name="<?php echo $this->get_field_name( 'slick_slides_padding' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['slick_slides_padding'] ); ?> />
                    <span class="slider round"></span>
                </label>
            </p>
            <p class="highlight_settings <?php if ( 'highlight' != $instance['template']) echo 'hidden'; ?>">
                <label  for="<?php echo $this->get_field_id( 'highlight_offset' ); ?>"><strong><?php _e( 'Offset', 'instagram-slider-widget' ); ?></strong>
                    <input type="number" min="1" class="small-text" id="<?php echo $this->get_field_id( 'highlight_offset' ); ?>" name="<?php echo $this->get_field_name( 'highlight_offset' ); ?>" value="<?php echo $instance['highlight_offset']; ?>" />
                </label>
                <br>
                <label  for="<?php echo $this->get_field_id( 'highlight_pattern' ); ?>"><strong><?php _e( 'Pattern', 'instagram-slider-widget' ); ?></strong>
                    <input type="number" min="0" class="small-text" id="<?php echo $this->get_field_id( 'highlight_pattern' ); ?>" name="<?php echo $this->get_field_name( 'highlight_pattern' ); ?>" value="<?php echo $instance['highlight_pattern']; ?>" />
                </label>
            </p>
            <p class="slider_normal_settings jr-slider-options <?php if ( 'slider' != $instance['template'] || 'slider-overlay' != $instance['template'] ) echo 'hidden'; ?>">

                    <?php _e( 'Slider Navigation Controls:', 'instagram-slider-widget' ); ?><br>
                    <label class="jr-radio"><input type="radio" id="<?php echo $this->get_field_id( 'controls' ); ?>" name="<?php echo $this->get_field_name( 'controls' ); ?>" value="prev_next" <?php checked( 'prev_next', $instance['controls'] ); ?> /> <?php _e( 'Prev & Next', 'instagram-slider-widget' ); ?></label>
                    <label class="jr-radio"><input type="radio" id="<?php echo $this->get_field_id( 'controls' ); ?>" name="<?php echo $this->get_field_name( 'controls' ); ?>" value="numberless" <?php checked( 'numberless', $instance['controls'] ); ?> /> <?php _e( 'Dotted', 'instagram-slider-widget' ); ?></label>
                    <label class="jr-radio"><input type="radio" id="<?php echo $this->get_field_id( 'controls' ); ?>" name="<?php echo $this->get_field_name( 'controls' ); ?>" value="none" <?php checked( 'none', $instance['controls'] ); ?> /> <?php _e( 'No Navigation', 'instagram-slider-widget' ); ?></label>
                <br>
                    <?php _e( 'Slider Animation:', 'instagram-slider-widget' ); ?><br>
                    <label class="jr-radio"><input type="radio" id="<?php echo $this->get_field_id( 'animation' ); ?>" name="<?php echo $this->get_field_name( 'animation' ); ?>" value="slide" <?php checked( 'slide', $instance['animation'] ); ?> /> <?php _e( 'Slide', 'instagram-slider-widget' ); ?></label>
                    <label class="jr-radio"><input type="radio" id="<?php echo $this->get_field_id( 'animation' ); ?>" name="<?php echo $this->get_field_name( 'animation' ); ?>" value="fade" <?php checked( 'fade', $instance['animation'] ); ?> /> <?php _e( 'Fade', 'instagram-slider-widget' ); ?></label>
                <br>
                    <label  for="<?php echo $this->get_field_id( 'slidespeed' ); ?>"><?php _e( 'Slide Speed:', 'instagram-slider-widget' ); ?>
                        <input type="number" min="1000" max="10000" step="100" class="small-text" id="<?php echo $this->get_field_id( 'slidespeed' ); ?>" name="<?php echo $this->get_field_name( 'slidespeed' ); ?>" value="<?php echo $instance['slidespeed']; ?>" />
                        <span><?php _e('milliseconds', 'instagram-slider-widget'); ?></span>
                        <span class='jr-description'><?php _e('1000 milliseconds = 1 second', 'instagram-slider-widget'); ?></span>
                    </label>
                    <label for="<?php echo $this->get_field_id('description'); ?>"><?php _e( 'Slider Text Description:', 'instagram-slider-widget' ); ?></label>
                    <select size=3 class='widefat' id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>[]" multiple="multiple">
                        <option class="<?php if ( 'hashtag' == $instance['search_for'] ) echo 'hidden'; ?>" value='username' <?php $this->selected( $instance['description'], 'username' ); ?>><?php _e( 'Username', 'instagram-slider-widget'); ?></option>
                        <option value='time'<?php $this->selected( $instance['description'], 'time' ); ?>><?php _e( 'Time', 'instagram-slider-widget'); ?></option>
                        <option value='caption'<?php $this->selected( $instance['description'], 'caption' ); ?>><?php _e( 'Caption', 'instagram-slider-widget'); ?></option>
                    </select>
                    <span class="jr-description"><?php _e( 'Hold ctrl and click the fields you want to show/hide on your slider. Leave all unselected to hide them all. Default all selected.', 'instagram-slider-widget') ?></span>
            </p>
            <p class="words_in_caption <?php if (
                'thumbs' == $instance['template'] ||
                'thumbs-no-border' == $instance['template'] ||
                'highlight' == $instance['template'] ||
                'slick_slider' == $instance['template']
            ) echo 'hidden';?>">
                <label  for="<?php echo $this->get_field_id( 'caption_words' ); ?>"><strong><?php _e( 'Number of words in caption:', 'instagram-slider-widget' ); ?></strong>
                    <input class="small-text" type="number" min="0" max="200" id="<?php echo $this->get_field_id( 'caption_words' ); ?>" name="<?php echo $this->get_field_name( 'caption_words' ); ?>" value="<?php echo $instance['caption_words']; ?>" />
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><strong><?php _e( 'Order by', 'instagram-slider-widget' ); ?></strong>
                    <select class="widefat" name="<?php echo $this->get_field_name( 'orderby' ); ?>" id="<?php echo $this->get_field_id( 'orderby' ); ?>">
                        <option value="date-ASC" <?php selected( $instance['orderby'], 'date-ASC', true); ?>><?php _e( 'Date - Ascending', 'instagram-slider-widget' ); ?></option>
                        <option value="date-DESC" <?php selected( $instance['orderby'], 'date-DESC', true); ?>><?php _e( 'Date - Descending', 'instagram-slider-widget' ); ?></option>
                        <option value="popular-ASC" <?php selected( $instance['orderby'], 'popular-ASC', true); ?>><?php _e( 'Popularity - Ascending', 'instagram-slider-widget' ); ?></option>
                        <option value="popular-DESC" <?php selected( $instance['orderby'], 'popular-DESC', true); ?>><?php _e( 'Popularity - Descending', 'instagram-slider-widget' ); ?></option>
                        <option value="rand" <?php selected( $instance['orderby'], 'rand', true); ?>><?php _e( 'Random', 'instagram-slider-widget' ); ?></option>
                    </select>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'images_link' ); ?>"><strong><?php _e( 'Link to', 'instagram-slider-widget' ); ?></strong>
                    <select class="widefat" name="<?php echo $this->get_field_name( 'images_link' ); ?>" id="<?php echo $this->get_field_id( 'images_link' ); ?>">
                        <!--<option value="image_link" <?php /*selected( $instance['images_link'], 'image_link', true); */?>><?php /*_e( 'Instagram Image', 'instagram-slider-widget' ); */?></option>
                        <option class="<?php /*if ( 'hashtag' == $instance['search_for'] ) echo 'hidden'; */?>" value="user_url" <?php /*selected( $instance['images_link'], 'user_url', true); */?>><?php /*_e( 'Instagram Profile', 'instagram-slider-widget' ); */?></option>
                        <option value="image_url" <?php /*selected( $instance['images_link'], 'image_url', true); */?>><?php /*_e( 'Image URL', 'instagram-slider-widget' ); */?></option>
                        <option class="<?php /*if ( ( !$instance['attachment'] ) || 'hashtag' == $instance['search_for'] ) echo 'hidden'; */?>" value="attachment" <?php /*selected( $instance['images_link'], 'attachment', true); */?>><?php /*_e( 'Attachment Page', 'instagram-slider-widget' ); */?></option>
                        <option value="custom_url" <?php /*selected( $instance['images_link'], 'custom_url', true ); */?>><?php /*_e( 'Custom Link', 'instagram-slider-widget' ); */?></option>
                        <option value="none" <?php /*selected( $instance['images_link'], 'none', true); */?>><?php /*_e( 'None', 'instagram-slider-widget' ); */?></option>
-->                     <?php
                        if(count($options_linkto)) {
                            foreach ($options_linkto as $key => $option) {
                                $selected = selected($instance['images_link'], $key, false);
                                echo "<option value='{$key}' {$selected}>{$option}</option>\n";
                            }
                        }
                        if(!$this->WIS->is_premium())
                        {
                            ?>
                            <optgroup label="Available in PRO">
                                <option value='1' disabled="disabled">Pop Up</option>
                            </optgroup>
                            <?php
                        }
                        ?>
                    </select>
                </label>
            </p>
            <p class="<?php if ( 'custom_url' != $instance['images_link'] ) echo 'hidden'; ?>">
                <label for="<?php echo $this->get_field_id( 'custom_url' ); ?>"><?php _e( 'Custom link:', 'instagram-slider-widget'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'custom_url' ); ?>" name="<?php echo $this->get_field_name( 'custom_url' ); ?>" value="<?php echo $instance['custom_url']; ?>" />
                <span><?php _e('* use this field only if the above option is set to <strong>Custom Link</strong>', 'instagram-slider-widget'); ?></span>
            </p>
            <!--<p>
                <strong>Advanced Options</strong>
				<?php
/*				$advanced_class = '';
				$advanced_text = '[ - Close ]';
				if ( '' == trim( $instance['image_link_rel'] ) && '' == trim( $instance['image_link_class'] ) && '' == trim( $instance['image_size'] ) )  {
				    $advanced_class = 'hidden';
					$advanced_text = '[ + Open ]';
				}
				*/?>
                <a href="#" class="jr-advanced"><?php /*echo $advanced_text;  */?></a>
            </p>-->
            <!-- <div class="jr-advanced-input <?php /*echo $advanced_class;*/ ?>">
               <div class="jr-image-options">
                    <h4 class="jr-advanced-title"><?php /*_e( 'Advanced Image Options', 'instagram-slider-widget'); */?></h4>
                    <p>
                        <label for="<?php /*echo $this->get_field_id( 'image_link_rel' ); */?>"><?php /*_e( 'Image Link rel attribute', 'instagram-slider-widget' ); */?>:</label>
                        <input class="widefat" id="<?php /*echo $this->get_field_id( 'image_link_rel' ); */?>" name="<?php /*echo $this->get_field_name( 'image_link_rel' ); */?>" value="<?php /*echo $instance['image_link_rel']; */?>" />
                        <span class="jr-description"><?php /*_e( 'Specifies the relationship between the current page and the linked website', 'instagram-slider-widget' ); */?></span>
                    </p>
                    <p>
                        <label for="<?php /*echo $this->get_field_id( 'image_link_class' ); */?>"><?php /*_e( 'Image Link class', 'instagram-slider-widget' ); */?>:</label>
                        <input class="widefat" id="<?php /*echo $this->get_field_id( 'image_link_class' ); */?>" name="<?php /*echo $this->get_field_name( 'image_link_class' ); */?>" value="<?php /*echo $instance['image_link_class']; */?>" />
                        <span class="jr-description"><?php /*_e( 'Usefull if you are using jQuery lightbox plugins to open links', 'instagram-slider-widget' ); */?></span>
                    </p>
                    <p><strong><?php /*_e( 'Disable Pinning:', 'instagram-slider-widget' ); */?></strong>
                        <label class="switch" for="<?php /*echo $this->get_field_id( 'no_pin' ); */?>">
                            <input class="widefat" id="<?php /*echo $this->get_field_id( 'no_pin' ); */?>" name="<?php /*echo $this->get_field_name( 'no_pin' ); */?>" type="checkbox" value="1" <?php /*checked( '1', $instance['no_pin'] ); */?> /><span class="slider round"></span></label>
                        <br><span class="jr-description"><?php /*_e( 'Disable pinning for Pinterest on all images from this widget!', 'instagram-slider-widget') */?></span>
                    </p>
                </div>
            </div>-->
			<?php $widget_id = preg_replace( '/[^0-9]/', '', $this->id ); if ( $widget_id != '' ) : ?>
                <p>
                    <label for="jr_insta_shortcode"><?php _e('Shortcode of this Widget:', 'instagram-slider-widget'); ?></label>
                    <input id="jr_insta_shortcode" onclick="this.setSelectionRange(0, this.value.length)" type="text" class="widefat" value="[jr_instagram id=&quot;<?php echo $widget_id ?>&quot;]" readonly="readonly" style="border:none; color:black; font-family:monospace;">
                    <span class="jr-description"><?php _e( 'Use this shortcode in any page or post to display images with this widget configuration!', 'instagram-slider-widget') ?></span>
                </p>
			<?php endif; ?>
        </div>
		<?php
	}

	/**
	 * Selected array function echoes selected if in array
	 *
	 * @param  array $haystack The array to search in
	 * @param  string $current  The string value to search in array;
	 *
	 */
	public function selected( $haystack, $current ) {

		if( is_array( $haystack ) && in_array( $current, $haystack ) ) {
			selected( 1, 1, true );
		}
	}


	/**
	 * Add shorcode function
	 * @param  array $atts shortcode attributes
	 * @return mixed
	 */
	public function shortcode( $atts ) {

		$atts = shortcode_atts( array( 'id' => '' ), $atts, 'jr_instagram' );
		$args = get_option( 'widget_jr_insta_slider' );
		if ( isset($args[$atts['id']] ) ) {
			$args[$atts['id']]['widget_id'] = $atts['id'];
			return $this->display_images( $args[$atts['id']] );
		}
	}

	/**
	 * Echoes the Display Instagram Images method
	 *
	 * @param  array $args
	 *
	 * @return void
	 */
	public function instagram_images( $args ) {
	    echo $this->display_images( $args );
	}

	/**
	 * Cron Trigger Function
     *
	 * @param  string $search_for
	 * @param  int $cache_hours
	 * @param  int $nr_images
	 * @param  bool $attachment
	 */
	public function jr_cron_trigger( $username, $refresh_hour, $images ) {
		$search_for = array();
		$search_for['username'] =  $username;
		$this->instagram_data( $search_for, $refresh_hour, $images, true );
	}

	/**
	 * Runs the query for images and returns the html
	 *
	 * @param  array  $args
	 *
	 * @return string
	 */
	private function display_images( $args ) {
		$account          = isset( $args['account'] ) && !empty( $args['account'] ) ? $args['account'] : false;
		$account_business = isset( $args['account_business'] ) && !empty( $args['account_business'] ) ? $args['account_business'] : false;
		$username         = isset( $args['username'] ) && !empty( $args['username'] ) ? str_replace( '@', '', $args['username'] ) : false;
		$hashtag          = isset( $args['hashtag'] ) && !empty( $args['hashtag'] ) ? str_replace( '#', '', $args['hashtag'] ) : false;
		$blocked_users    = isset( $args['blocked_users'] ) && !empty( $args['blocked_users'] ) ? $args['blocked_users'] : false;
		$blocked_words    = isset( $args['blocked_words'] ) && !empty( $args['blocked_words'] ) ? $args['blocked_words'] : false;
		$attachment       = isset( $args['attachment'] ) ? true : false;
		$template         = isset( $args['template'] ) ? $args['template'] : 'slider';
		$orderby          = isset( $args['orderby'] ) ? $args['orderby'] : 'rand';
		$images_link      = isset( $args['images_link'] ) ? $args['images_link'] : 'image_url';
		$custom_url       = isset( $args['custom_url'] ) ? $args['custom_url'] : '';
		$images_number    = isset( $args['images_number'] ) ? absint( $args['images_number'] ) : 20;
		$columns          = isset( $args['columns'] ) ? absint( $args['columns'] ) : 4;
		$refresh_hour     = isset( $args['refresh_hour'] ) ? absint( $args['refresh_hour'] ) : 5;
		$image_size       = isset( $args['image_size'] ) ? $args['image_size'] : 'standard';
		$image_link_rel   = isset( $args['image_link_rel'] ) ? $args['image_link_rel'] : '';
		$no_pin           = isset( $args['no_pin'] ) ? $args['no_pin'] : 0;
		$image_link_class = isset( $args['image_link_class'] ) ? $args['image_link_class'] : '';
		$controls         = isset( $args['controls'] ) ? $args['controls'] : 'prev_next';
		$animation        = isset( $args['animation'] ) ? $args['animation'] : 'slide';
		$caption_words    = isset( $args['caption_words'] ) ? $args['caption_words'] : 20;
		$slidespeed       = isset( $args['slidespeed'] ) ? $args['slidespeed'] : 7000;
		$description      = isset( $args['description'] ) ? $args['description'] : array();
		$widget_id        = isset( $args['widget_id'] ) ? $args['widget_id'] : preg_replace( '/[^0-9]/', '', $this->id );
		$powered_by_link  = isset( $args['support_author'] ) ? true : false;
		$gutter           = isset( $args['gutter'] ) ? $args['gutter'] : 0;
		$masonry_image_width = isset( $args['masonry_image_width'] ) ? $args['masonry_image_width'] : 200;
		$slick_slides_to_show = isset( $args['slick_slides_to_show'] ) ? $args['slick_slides_to_show'] : 3;
		$slick_slides_padding = isset( $args['slick_slides_padding'] ) ? $args['slick_slides_padding'] : 0;
		$show_feed_header = isset( $args['show_feed_header'] ) ? $args['show_feed_header'] : 0;
		$highlight_offset = isset( $args['highlight_offset'] ) ? $args['highlight_offset'] : 1;
		$highlight_pattern = isset( $args['highlight_pattern'] ) ? $args['highlight_pattern'] : 6;

		if ( !empty( $description ) && !is_array( $description ) ) {
			$description = explode( ',', $description );
		}

		if ( isset ( $args['search_for'] ) && $args['search_for'] == 'hashtag' ) {
			$search = 'hashtag';
			$search_for['hashtag'] = $hashtag;
			$search_for['blocked_users'] = $blocked_users;
			$search_for['blocked_words'] = $blocked_words;
		}
        elseif(isset ( $args['search_for'] ) && $args['search_for'] == 'account' ) {
	        $search                = 'account';
	        $search_for['account'] = $account;
	        $search_for['blocked_words'] = $blocked_words;
        }
		elseif(isset ( $args['search_for'] ) && $args['search_for'] == 'account_business' ) {
			$search = 'account_business';
			$search_for['account_business'] = $account_business;
			$search_for['blocked_words'] = $blocked_words;
		} else {
			$search = 'user';
			$search_for['username'] = $username;
			$search_for['blocked_words'] = $blocked_words;
		}

		if ( $refresh_hour == 0 ) {
			$refresh_hour = 5;
		}


		$template_args = array(
			'search_for'    => $search,
			'attachment'    => $attachment,
			'image_size'    => $image_size,
			'link_rel'      => $image_link_rel,
			'link_class'    => $image_link_class,
			'no_pin'        => $no_pin,
			'caption_words' => $caption_words,
            'masonry_image_width'   => $masonry_image_width,
            'slick_slides_padding'  => $slick_slides_padding,
			'slick_slides_to_show'  => $slick_slides_to_show,
            'highlight_offset'      => $highlight_offset,
            'highlight_pattern'     => $highlight_pattern,

		);

		$images_div_class = 'jr-insta-thumb';
		$ul_class         = ( $template == 'thumbs-no-border' ) ? 'thumbnails no-border jr_col_' . $columns : 'thumbnails jr_col_' . $columns;
		$slider_script    = '';

		if ( $template != 'thumbs' &&  $template != 'thumbs-no-border' ) {

			$template_args['description'] = $description;
			$direction_nav = ( $controls == 'prev_next' ) ? 'true' : 'false';
			$control_nav   = ( $controls == 'numberless' ) ? 'true': 'false';
			$ul_class      = 'slides';

			if( $template == 'slider' ) {
				$images_div_class = 'pllexislider pllexislider-normal instaslider-nr-'. $widget_id;
				$slider_script =
					"<script type='text/javascript'>" . "\n" .
					"	jQuery(document).ready(function($) {" . "\n" .
					"		$('.instaslider-nr-{$widget_id}').pllexislider({" . "\n" .
					"			animation: '{$animation}'," . "\n" .
					"			slideshowSpeed: {$slidespeed}," . "\n" .
					"			directionNav: {$direction_nav}," . "\n" .
					"			controlNav: {$control_nav}," . "\n" .
					"			prevText: ''," . "\n" .
					"			nextText: ''," . "\n" .
					"		});" . "\n" .
					"	});" . "\n" .
					"</script>" . "\n";
            }
			if ( $template == 'slick_slider' || $template == 'masonry' ||  $template == 'highlight') {
			    //return $this->pro_display_images($args);
                return apply_filters( 'wis/pro/display_images', "", $args, $this );
            } else {
				$images_div_class = 'pllexislider pllexislider-overlay instaslider-nr-'. $widget_id;
				$slider_script =
					"<script type='text/javascript'>" . "\n" .
					"	jQuery(document).ready(function($) {" . "\n" .
					"		$('.instaslider-nr-{$widget_id}').pllexislider({" . "\n" .
					"			animation: '{$animation}'," . "\n" .
					"			slideshowSpeed: {$slidespeed}," . "\n" .
					"			directionNav: {$direction_nav}," . "\n" .
					"			controlNav: {$control_nav}," . "\n" .
					"			prevText: ''," . "\n" .
					"			nextText: ''," . "\n" .
					"			start: function(slider){" . "\n" .
					"				slider.hover(" . "\n" .
					"					function () {" . "\n" .
					"						slider.find('.pllex-control-nav, .pllex-direction-nav').stop(true,true).fadeIn();" . "\n" .
					"						slider.find('.jr-insta-datacontainer').fadeIn();" . "\n" .
					"					}," . "\n" .
					"					function () {" . "\n" .
					"						slider.find('.pllex-control-nav, .pllex-direction-nav').stop(true,true).fadeOut();" . "\n" .
					"						slider.find('.jr-insta-datacontainer').fadeOut();" . "\n" .
					"					}" . "\n" .
					"				);" . "\n" .
					"			}" . "\n" .
					"		});" . "\n" .
					"	});" . "\n" .
					"</script>" . "\n";
			}
		}

        //$account = $accounts[$images_data[0]['username']];
        $images_div = '';
		$images_ul  = "<ul class='no-bullet {$ul_class}' id='wis-slides'>\n";

		$output = '';
		$output .= __( 'No images found! <br> Try some other hashtag or username', 'instagram-slider-widget' );

		if ( ( $search == 'user' && $attachment && false ) ) {

			if ( !wp_next_scheduled( 'jr_insta_cron', array(  $search_for['username'], $refresh_hour, $images_number ) ) ) {
				wp_schedule_single_event( time(), 'jr_insta_cron', array(  $search_for['username'], $refresh_hour, $images_number )  );
			}

			$opt_name  = 'jr_insta_' . md5( $search . '_' . $search_for['username'] );
			$attachment_ids = (array) get_option( $opt_name );

			$query_args = array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'post_mime_type' => 'image',
				'posts_per_page' => $images_number,
				'no_found_rows'  => true
			);

			if ( $orderby != 'rand' ) {
				$orderby = explode( '-', $orderby );
				$meta_key = $orderby[0] == 'date' ? 'jr_insta_timestamp' : 'jr_insta_popularity';

				$query_args['meta_key'] = $meta_key;
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = $orderby[1];
			}

			if ( isset( $attachment_ids['saved_images'] ) && !empty( $attachment_ids['saved_images'] ) ) {

				$query_args['post__in']   = $attachment_ids['saved_images'];

			} else {

				$query_args['meta_query'] = array(
					array(
						'key'     => 'jr_insta_username',
						'value'   => $username,
						'compare' => '='
					)
				);
			}

			$instagram_images = new WP_Query( $query_args );

			//if ( $instagram_images->have_posts() ) {
			if ( false ) {

				$output = $slider_script . $images_div . $images_ul;

				while ( $instagram_images->have_posts() ) : $instagram_images->the_post();

					$id = get_the_id();

					if ( 'image_link' == $images_link ) {
						$template_args['link_to'] = get_post_meta( $id, 'jr_insta_link', true );
					} elseif ( 'user_url' == $images_link ) {
						$template_args['link_to'] = 'https://www.instagram.com/' . $username . '/';
					} elseif ( 'image_url' == $images_link ) {
						$template_args['link_to'] = wp_get_attachment_url( $id );
					} elseif ( 'attachment' == $images_link ) {
						$template_args['link_to'] = get_permalink( $id );
					} elseif ( 'custom_url' == $images_link ) {
						$template_args['link_to'] = $custom_url;
					}

					$image_thumb_url  = get_post_meta( $id, 'jr_insta_sizes', true );
					$template_args['image']  = $image_thumb_url[$image_size];

					$output .= $this->get_template( $template, $template_args );

				endwhile;

				$output .= "</ul>\n</div>";

			} else {

				$images_data = $this->instagram_data( $search_for, $refresh_hour, $images_number, false );

				if ( is_array( $images_data ) && !empty( $images_data ) ) {
					if(isset($images_data['error']))
					{
						return $images_data['error'];
					}

					if ( $orderby != 'rand' ) {

						$func = $orderby[0] == 'date' ? 'sort_timestamp_' . $orderby[1] : 'sort_popularity_' . $orderby[1];

						usort( $images_data, array( $this, $func ) );

					} else {

						shuffle( $images_data );
					}

					$output = $slider_script . $images_div . $images_ul;

					foreach ( $images_data as $image_data ) {

						if ( 'image_link' == $images_link ) {
							$template_args['link_to'] = $image_data['link'];
						} elseif ( 'user_url' == $images_link ) {
							$template_args['link_to'] = 'https://www.instagram.com/' . $username . '/';
						} elseif ( 'image_url' == $images_link ) {
							$template_args['link_to'] = $image_data['url'];
						} elseif ( 'custom_url' == $images_link ) {
							$template_args['link_to'] = $custom_url;
						}

						$template_args['type']       = $image_data['type'];
						$template_args['image']      = $image_data['image'];
						$template_args['caption']    = $image_data['caption'];
						$template_args['timestamp']  = $image_data['timestamp'];
						$template_args['username']   = isset( $image_data['username'] ) ? $image_data['username'] : '';
						$template_args['attachment'] = false;

						$output .= $this->get_template( $template, $template_args );
					}

					$output .= "</ul>\n</div>";
				}

			}

			wp_reset_postdata();

		}
		else {
		    $is_business = ($search == 'account_business');
			if ( $is_business )
				$accounts = WIS_Plugin::app()->getOption( 'account_profiles_new' );
			else
				$accounts = WIS_Plugin::app()->getOption( 'account_profiles' );
			$images_data = $this->instagram_data( $search_for, $refresh_hour, $images_number, false );

			/*
			 * Песочница
			 */
			if(isset($_GET['access_token']) && isset($_GET['id'])) {
			    if( $is_business ) {
				    if ( isset( $_COOKIE['wis-demo-account-data'] ) ) {
					    $account = json_decode( stripslashes( $_COOKIE['wis-demo-account-data'] ), true );
				    }
			    }
			    else
			    {
				    $args = array(
					    'fields' => 'id,media_count,username',
					    'access_token' => $_GET['access_token'],
				    );

				    $url = self::USERS_SELF_URL;
				    $url = add_query_arg( $args, $url );
				    $response = wp_remote_get( $url);
				    if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
					    $user = json_decode( wp_remote_retrieve_body( $response ), true );
					    $user['token'] = $_GET['access_token'];
					    $account = $user;
				    }
			    }
			} // конец песочницы
            else {
                if($search !== 'hashtag' && $search !== 'user') $account = $accounts[$images_data[0]['username']];
            }

            $images_div = '';
            if($account){
                $account_data = $account;
            } else {
                $data = WIS_Plugin::app()->getOption('profiles_data_by_username');
                $data = $data['entry_data']['ProfilePage']['0']['graphql']['user'];
                $account_data = array(
                    'username'          => $data['username'],
                    'profile_picture'   => $data['profile_pic_url'],
                    'counts'            => array(
                        'media'         => $data['edge_owner_to_timeline_media']['count'],
                        'followed_by'   => $data['edge_followed_by']['count']
                    ),
                );
            }

            if($show_feed_header && $search == 'account_business' ){
                $images_div .= $this->render_layout_template('feed_header_template', $account_data);
            }
            $images_div .= "<div class='{$images_div_class}'>\n";
			if ( is_array( $images_data ) && !empty( $images_data ) ) {
				if(isset($images_data['error'])) {
					return $images_data['error'];
				}

				if ( $orderby != 'rand' ) {

					$orderby = explode( '-', $orderby );
					if($orderby[0] == 'date') {
						$func = 'sort_timestamp_' . $orderby[1];
                    }
					else{
						$func = $is_business ? 'sort_popularity_' . $orderby[1] : 'sort_timestamp_' . $orderby[1];
                    }

					usort( $images_data, array( $this, $func ) );

				} else {

					shuffle( $images_data );
				}

				$output = $slider_script . $images_div . $images_ul;

				foreach ( $images_data as $image_data ) {

					if ( 'image_link' == $images_link ) {
						$template_args['link_to'] = $image_data['link'];
					} elseif ( 'user_url' == $images_link ) {
						$template_args['link_to'] = 'https://www.instagram.com/' . $username . '/';
					} elseif ( 'image_url' == $images_link ) {
						$template_args['link_to'] = $image_data['url'];
					} elseif ( 'custom_url' == $images_link ) {
						$template_args['link_to'] = $custom_url;
					}

					$template_args['type']      = $image_data['type'];
					$template_args['image']     = $image_data['image'];
					$template_args['caption']   = $image_data['caption'];
					$template_args['timestamp'] = $image_data['timestamp'];
					$template_args['username']  = isset( $image_data['username'] ) ? $image_data['username'] : '';

					$output .= $this->get_template( $template, $template_args );
				}

				$output .= "</ul>\n</div>";
			}
		}
		return $output;

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
        $path = WIS_PLUGIN_DIR."/html_templates/$template_name.php";
		if(file_exists($path)){
            ob_start();
            include $path;
            return ob_get_clean();
        } else {
		    return 'This template does not exist!';
        }
    }

	/**
	 * Function to display Templates styles
	 *
	 * @param    string    $template
	 * @param    array	   $args
	 *
	 * return mixed
	 */
	private function get_template( $template, $args ) {

	    $link_to   = isset( $args['link_to'] ) ? $args['link_to'] : false;
		$image_url = isset( $args['image'] ) ? $args['image'] : false;
		$type = isset( $args['type'] ) ? $args['type'] : '';

		if (
		        ( $args['search_for'] == 'user' && $args['attachment'] !== true )
                || ( $args['search_for'] == 'account' && $args['attachment'] !== true )
                || ( $args['search_for'] == 'account_business' && $args['attachment'] !== true )
                || $args['search_for'] == 'hashtag'
        )
		{
			$caption   = $args['caption'];
			$time      = $args['timestamp'];
			$username  = $args['username'];
		} else {
			$attach_id = get_the_id();
			$caption   = get_the_excerpt();
			$time      = get_post_meta( $attach_id, 'jr_insta_timestamp', true );
			$username  = get_post_meta( $attach_id, 'jr_insta_username', true );
		}

		$caption   = $args['caption'];
		$time      = $args['timestamp'];
		$username  = $args['username'];

		$short_caption = wp_trim_words( $caption, 10, '' );
		$short_caption = strip_tags($short_caption);

		$caption       = wp_trim_words( $caption, $args['caption_words'], '' );
		$nopin         = ( 1 == $args['no_pin'] ) ? 'nopin="nopin"' : '';

		$clean_image_url = WIS_PLUGIN_URL."/assets/image.png";
		$image_src = "<img src='{$clean_image_url}' $nopin class='{$type}' style='opacity: 0;'>";
		$image_output  = $image_src;

		if ( $link_to ) {
			$image_output  = "<a href='$link_to' target='_blank'";

			if ( ! empty( $args['link_rel'] ) ) {
				$image_output .= " rel={$args['link_rel']}";
			}

			if ( ! empty( $args['link_class'] ) ) {
				$image_output .= " class={$args['link_class']}";
			}
			$image_output .= "> $image_src</a>";
		}

		$output = '';

		if( $template == 'slider' ) {
			$output .= "<li style='border:0;' >";

			//$output .= $image_output;
			$output .= "<div style='background: url({$image_url}) no-repeat center center; background-size: cover;'>{$image_output}</div>";

			if ( is_array( $args['description'] ) && count( $args['description'] ) >= 1 ) {

				$output .= "<div class='jr-insta-datacontainer' style=''>\n";

                if ( in_array( 'username', $args['description'] ) && $username ) {
    
                    $output .= "<span class='jr-insta-username'>by <a rel='nofollow' href='https://www.instagram.com/{$username}/' style='color:black; font-weight: 600' target='_blank'>{$username}</a></span>\n";
                }
				if ( $time && in_array( 'time', $args['description'] ) ) {
					$time = human_time_diff( $time );
					$output .= "<strong><span class='jr-insta-time pull-right' style='font-size: 0.9em'>{$time} ago</span></strong>\n";
					$output .= "<br>";
				}


				if ( $caption != '' && in_array( 'caption', $args['description'] ) ) {
					$caption   = preg_replace( '/\@([a-z0-9А-Яа-я_-]+)/u', '&nbsp;<a href="https://www.instagram.com/$1/" rel="nofollow" style="color:black; font-weight: 600" target="_blank">@$1</a>&nbsp;', $caption );
					$caption = preg_replace( '/\#([a-zA-Z0-9А-Яа-я_-]+)/u', '&nbsp;<a href="https://www.instagram.com/explore/tags/$1/" style="color:black; font-weight: 600" rel="nofollow" target="_blank">$0</a>&nbsp;', $caption);
					$output   .= "<span class='jr-insta-caption' style='text-align: left !important;'>{$caption}</span>\n";
				}

				$output .= "</div>\n";
			}

			$output .= "</li>";
			// Template : Slider with text Overlay on mouse over
		} elseif ( $template == 'slider-overlay' ) {
			$output .= "<li class='".$type."'>";

			//$output .= $image_output;
			$output .= "<div id='jr-image-overlay' style='background: url({$image_url}) no-repeat center center; background-size: cover;'>{$image_output}</div>";

			if ( is_array( $args['description'] ) && count( $args['description'] ) >= 1 ) {

				$output .= "<div class='jr-insta-wrap'>\n";

				$output .= "<div class='jr-insta-datacontainer'>\n";

				if ( $time && in_array( 'time', $args['description'] ) ) {
					$time = human_time_diff( $time );
					$output .= "<span class='jr-insta-time'>{$time} ago</span>\n";
				}

				if ( in_array( 'username', $args['description'] ) && $username ) {
					$output .= "<span class='jr-insta-username'>by <a rel='nofollow' target='_blank' href='https://www.instagram.com/{$username}/'>{$username}</a></span>\n";
				}

				if ( $caption != '' && in_array( 'caption', $args['description'] ) ) {
					$caption = preg_replace( '/@([a-z0-9_]+)/i', '&nbsp;<a href="https://www.instagram.com/$1/" rel="nofollow" target="_blank">@$1</a>&nbsp;', $caption );
					$caption = preg_replace( '/\#([a-zA-Z0-9_-]+)/i', '&nbsp;<a href="https://www.instagram.com/explore/tags/$1/" rel="nofollow" target="_blank">$0</a>&nbsp;', $caption);
					$output .= "<span class='jr-insta-caption' style='text-align: left !important;'>{$caption}</span>\n";
				}

				$output .= "</div>\n";

				$output .= "</div>\n";
			}

			$output .= "</li>";

			// Template : Thumbnails no text
		} elseif ( $template == 'thumbs' || $template == 'thumbs-no-border' ) {
			$output .= "<li class='{$type}'>";
			$output .= "<div style='background: url({$image_url}) no-repeat center center; background-size: cover;'>{$image_output}</div>";
			//$output .= "<div></div>";
			$output .= "</li>";

		} else {

			$output .= 'This template does not exist!';
		}

		return $output;
	}


	/**
	 * Trigger refresh for new data
	 * @param  bool   $instaData
	 * @param  array    $old_args
	 * @param  array    $new_args
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
			unset($old_args['saved_images']);
		}

		if ( isset( $old_args['deleted_images'] ) ) {
			unset($old_args['deleted_images']);
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
	 * Get data from instagram by username
	 * @param  string    $username
	 * @return array
	 */
	private function get_data_by_username( $username ) {

		$url = str_replace( '{username}', urlencode( trim( $username ) ), self::USERNAME_URL );
		$response = wp_remote_get( $url, array( 'sslverify' => false, 'timeout' => 60 ) );

		if(strstr( $response['body'], '-cx-PRIVATE-Page__main' )) {
			return ['error' => __( 'Account not found or for this account there are restrictions on Instagram by age', 'instagram-slider-widget' )];
		}

		$json = str_replace( 'window._sharedData = ', '', strstr( $response['body'], 'window._sharedData = ' ) );

		// Compatibility for version of php where strstr() doesnt accept third parameter
		if ( version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
			$json = strstr( $json, '</script>', true );
		} else {
			$json = substr( $json, 0, strpos( $json, '</script>' ) );
		}
		$json = rtrim( $json, ';' );

		// Function json_last_error() is not available before PHP * 5.3.0 version
		if ( function_exists( 'json_last_error' ) ) {

			( $results = json_decode( $json, true ) ) && json_last_error() == JSON_ERROR_NONE;

		} else {
			$results = json_decode( $json, true );
		}

		return $results;
	}

    /**
     * Stores the fetched data from instagram in WordPress DB using transients
     *
     * @param array $search_for  Array of widget settings
     * @param string $cache_hours Cache hours for transient
     * @param string $nr_images   Nr of images to fetch from instagram
     * @param bool   $attachment  Is attachment
     *
     * @return array of localy saved instagram data
     * @throws \Exception
     */
	public function instagram_data( $search_for, $cache_hours, $nr_images, $attachment ) {
		//$nr_images = $nr_images <= 12 ? $nr_images : 12;
		if ( isset( $search_for['account'] ) && !empty( $search_for['account'] ) ) {
			$search = 'account';
			$search_string = $search_for['account'];
		} elseif ( isset( $search_for['account_business'] ) && !empty( $search_for['account_business'] ) ) {
			$search = 'account_business';
			$search_string = $search_for['account_business'];
		} elseif ( isset( $search_for['username'] ) && !empty( $search_for['username'] ) ) {
			$search = 'user';
			$search_string = $search_for['username'];
		} elseif ( isset( $search_for['hashtag'] ) && !empty( $search_for['hashtag'] ) ) {
			$search = 'hashtag';
			$search_string       = $search_for['hashtag'];
			$blocked_users = isset( $search_for['blocked_users'] ) && !empty( $search_for['blocked_users'] ) ? str_replace( '@', '', $search_for['blocked_users'] ) : false;
			$blocked_users_array = $blocked_users ? $this->get_ids_from_usernames( $blocked_users ) : array();
		} else {
			return __( 'Nothing to search for', 'instagram-slider-widget');
		}

		$blocked_users = isset($blocked_users) ? $blocked_users : '';
		$blocked_words = isset( $search_for['blocked_words'] ) && !empty( $search_for['blocked_words'] ) ? $search_for['blocked_words']  : '';

		//песочница
		if(isset($_GET['access_token']) && isset($_GET['id']))
		{
			$search = 'account';
			$search_string = htmlspecialchars( $_GET['access_token']);
		}


		$opt_name  = 'jr_insta_' . md5( $search . '_' . $search_string );
		$instaData = get_transient( $opt_name );
		$old_opts  = (array) get_option( $opt_name );
		$new_opts  = array(
			'search'        => $search,
			'search_string' => $search_string,
			'blocked_users' => $blocked_users,
			'cache_hours'   => $cache_hours,
			'nr_images'     => $nr_images,
			'attachment'    => $attachment
		);

		if ( true === $this->trigger_refresh_data( $instaData, $old_opts, $new_opts ) ) {
		//if ( true ) {

			$instaData = array();
			$old_opts['search']        = $search;
			$old_opts['search_string'] = $search_string;
			$old_opts['blocked_users'] = $blocked_users;
			$old_opts['cache_hours']   = $cache_hours;
			$old_opts['nr_images']     = $nr_images;
			$old_opts['attachment']    = $attachment;

			if ( 'user' == $search ) {

				$results = $this->get_data_by_username($search_string);
				if(isset($results['error'])) return $results['error'];

                WIS_Plugin::app()->updateOption('profiles_data_by_username', $results);
				// ************************************
				// if instagram not return list of posts
				// ************************************
				$is_instaLoginPage = !isset($results['entry_data']['ProfilePage']);
				if($is_instaLoginPage)
				{
					return ['error' => __( 'Instagram requires authorization to view a user profile. Use autorized account in widget settings', 'instagram-slider-widget' )];
				}
				// ************************************
			}
			elseif ( 'account' == $search || 'account_business' == $search )
            {
                $is_business_api = 'account_business' == $search ? true : false;
	            $nr_images = !$this->WIS->is_premium() && $nr_images > 20 ? 20 : $nr_images;
	            //песочница
	            if(isset($_GET['access_token']) && isset($_GET['id'])) {
		            if(isset($_COOKIE['wis-demo-account-data']))
			            $account = json_decode( stripslashes( $_COOKIE['wis-demo-account-data']), true);
		            else
			            $account = $this->get_user_by_token( $_GET['access_token']);
//		                $account = array(
//                            'token' => $_GET['access_token'],
//                            'id' => $_GET['id'],
//                        );
	            }
	            else
	                $account = $this->getAccountById( $search_string, $is_business_api);

                if($is_business_api)
                {
	                if(!isset($_GET['access_token']) && !isset($_GET['id'])) {
		                //Обновляем данные профиля: подписчики, количество постов
		                $this->update_account_profiles( $account['token'], true, $account['username'] );
	                }

	                $args = array(
                        'access_token' => $account['token'],
                        'fields' => "id,username,caption,comments_count,like_count,media_type,media_url,permalink,timestamp,children{media_url,media_type},owner,thumbnail_url",
                        'limit' => 50,
	                );

                    $url = self::USERS_SELF_URL_NEW.$account['id']."/media";
	                $response = wp_remote_get( add_query_arg( $args, $url) );
	                if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
		                $media = json_decode( wp_remote_retrieve_body( $response ), true );
		                $results = $media['data'];
		                //$results = apply_filters('wis/images/count', $results, $media, $nr_images);
		                $next_max_id = null;
		                if(!empty($media['pagination']))
			                $next_max_id = $media['pagination']['next_max_id'];
		                if(!count($results))
			                return ['error' => __( 'There are no publications in this account yet', 'instagram-slider-widget' )];
	                }

                }
                else
                {
	                if(! isset($_GET['access_token'])) {
		                //Обновляем данные профиля: подписчики, количество постов
		                $this->update_account_profiles( $account['token'] );
	                }

	                $args     = array(
		                'fields' => 'id,username,media{id,username,caption,media_type,media_url,permalink,thumbnail_url,timestamp,children{id,media_type,media_url,thumbnail_url}}',
		                'limit' => 50,
		                'access_token' => $account['token'],
	                );
	                $url = self::USERS_SELF_MEDIA_URL.$account['id'];
	                $response = wp_remote_get( add_query_arg( $args, $url) );
	                if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
		                $media = json_decode( wp_remote_retrieve_body( $response ), true );
		                $results = $media['media']['data'];
		                //$results = apply_filters('wis/images/count', $results, $media, $nr_images);
		                if(!is_array( $results) || !count($results))
			                return ['error' => __( 'There are no publications in this account yet', 'instagram-slider-widget' )];
	                }
                }

            }
			else { //hashtag
				//$account = $this->getAccountForHashtag();
				$account = false;
			    if( $account ) {
					$args = array(
						'access_token' => $account['token'],
						'user_id' => $account['id'],
                        'q' => $search_string,
					);
					$url = self::USERS_SELF_URL_NEW."ig_hashtag_search";
					$response = wp_remote_get( add_query_arg( $args, $url) );
					if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
						$media = json_decode( wp_remote_retrieve_body( $response ), true );
						$args = array(
							'access_token' => $account['token'],
							'user_id' => $account['id'],
							'fields' => "id,caption,media_type,media_url,comments_count,like_count,permalink,children{media_type,media_url}",
							'limit' => 50,
						);
						$url = self::USERS_SELF_URL_NEW.$media['data'][0]['id']."/recent_media";
						$response = wp_remote_get( add_query_arg( $args, $url) );
						if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
							$media = json_decode( wp_remote_retrieve_body( $response ), true );
							$media['hashtag'] = true;
							$results = $media;

						}
					}
				}
				else {
					$url      = str_replace( '{tag}', urlencode( trim( $search_string ) ), self::TAG_URL );
					$response = wp_remote_get( $url, array( 'sslverify' => false, 'timeout' => 60 ) );
					$results  = json_decode( $response['body'], true );
				}

			}

			if ( true ) {

				if ( $results && is_array( $results ) ) {

					if ( 'user' == $search ) {
						$entry_data =  isset($results['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges']) ? $results['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'] : array();
					} elseif( 'account' == $search || 'account_business' == $search ) {
						$entry_data = $results;
					} elseif( 'hashtag' == $search ) {
					    if( isset( $results['hashtag'] ) )
						    $entry_data = $results['data'];
					    else
					        $entry_data = isset( $results['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) ? $results['graphql']['hashtag']['edge_hashtag_to_media']['edges'] : array();
					}

					if ( empty( $entry_data ) ) {
						return ['error' => __( 'No images found', 'instagram-slider-widget' )];
					}
					$i = 0;
					foreach ( $entry_data as $current => $result )
					{
                        if(!isset($result['caption'])) $result['caption'] = "";

						if ( $i >= $nr_images ) {
							break;
						}
						else
							$i++;

						if ( 'hashtag' == $search ) {

							//TODO: Доделать черный список с новым API
                            //Чёрный список не работает, так как API не отдает имя пользователя, который создал пост
                            if( isset( $results['hashtag'] ) )
                            {
	                            $result['fbapi'] = true;
	                            if ( isset($result['media_type']) && $result['media_type'] == 'VIDEO' ) {
		                            //$nr_images++;
		                            continue;
	                            }
                            }
                            else
                            {
	                            $result = $result['node'];
	                            if ( in_array( $result['owner']['id'], $blocked_users_array ) ) {
		                            $nr_images++;
		                            continue;
	                            }
                            }
						}

						if ( 'account' == $search ) {
							$image_data = $this->to_media_model_from_account( $result );
						}
						elseif ( 'account_business' == $search ) {
							$image_data = $this->to_media_model_from_account_business( $result );
						}
						elseif ( 'hashtag' == $search && $results['graphql']['hashtag']) {
							$image_data = $this->to_media_model_from_hashtag($result );
						}
						elseif ( 'user' == $search ) {
							$image_data = $this->media_model($result['node'] );
							$image_data['username'] = $search_string;
						}


            			if( $this->is_blocked_by_word($blocked_words, $image_data['caption']) ) {
				            $nr_images++;
            			    continue;
						}

						if ( !$attachment ) {

							$instaData[] = $image_data;

						}
						else {

							if ( isset( $old_opts['saved_images'][$image_data['id']] ) ) {

								if ( is_string( get_post_status( $old_opts['saved_images'][$image_data['id']] ) ) ) {

									$this->update_wp_attachment( $old_opts['saved_images'][$image_data['id']], $image_data );

									$instaData[$image_data['id']] = $old_opts['saved_images'][$image_data['id']];

								} else {
									unset( $old_opts['saved_images'][$image_data['id']] );
								}

							} else {

								$id = $this->save_wp_attachment( $image_data );

								if ( $id && is_numeric( $id ) ) {

									$old_opts['saved_images'][$image_data['id']] = $id;

									$instaData[$image_data['id']] = $id;

								} else {

									return $id;
								}

							} // end isset $saved_images

						} // false to save attachments

					} // end -> foreach

				} // end -> ( $results ) && is_array( $results ) )

			}
			else {

				return $response['response']['message'];

			} // end -> $response['response']['code'] === 200 )

			update_option( $opt_name, $old_opts );

			if ( is_array( $instaData ) && !empty( $instaData )  ) {

				set_transient( $opt_name, $instaData, $cache_hours * 60 * 60 );
			}

		} // end -> false === $instaData

        //if('account' == $search)
            //$instaData = array_merge($instaData, ['next_max_id' => $next_max_id]);
		return $instaData;
	}

	/**
	 * @param string $imageUrl
	 *
	 * @return array
	 */
	private function get_thumbnail_urls( $thumbnails ) {

		$image_thumbnails = array();

		foreach ( $thumbnails as $thumbnail ) {

			switch ( $thumbnail['config_width'] ) {
				case '150':
					$image_thumbnails['thumbnail'] = $thumbnail['src'];
					break;
				case '320':
					$image_thumbnails['low'] = $thumbnail['src'];
					break;
				case '640':
					$image_thumbnails['standard'] = $thumbnail['src'];
					break;
			}
		}

		return $image_thumbnails;
	}


	/**
	 * Media Model
	 * @param  [type] $medias_array [description]
	 * @return [type]               [description]
	 */
	private function media_model( $medias_array ) {

		$m = array();

		foreach ( $medias_array as $prop => $value ) {

			switch ( $prop ) {
				case 'id':
					$m['id'] = $value;
					break;
				case 'code':
				case '__typename':
				    $m['type'] = $value;
				    break;
				case 'shortcode':
					$m['code'] = $value;
					$m['link'] = 'https://www.instagram.com/p/'. $value . '/';
					break;
				case 'owner':
					$m['user_id'] = $value['id'];
					break;
				case 'caption':
					$m['caption'] = $this->sanitize( $value );
					break;
				case 'edge_media_to_caption':
					if ( !empty( $value['edges'] ) ) {
						$first_caption = $value['edges'][0];
						if ( isset( $first_caption['node']['text'] ) ) {
							$m['caption'] = $this->sanitize( $value['edges'][0]['node']['text'] );
						}
					}
					break;
				case 'date':
				case 'taken_at_timestamp':
					$m['timestamp'] = (float) $value;
					break;
				case 'dimensions':
					$m['height'] = $value['height'];
					$m['width']  = $value['width'];
					break;
				case 'display_url':
				case 'display_src':
					$m['url'] = $value;
					$m['image'] = $value;
					if ( isset( $m['sizes'] ) ) {
						$m['sizes']['full'] = $value;
					}
					break;
				case 'edge_liked_by':
				case 'likes':
					$m['likes_count'] = $value['count'];
					break;
				case 'edge_media_to_comment':
				case 'comments':
					$m['comment_count'] = $value['count'];
					break;
				case 'thumbnail_resources':
					$m['sizes'] = $this->get_thumbnail_urls( $value );
					if ( isset( $m['url'] ) ) {
						$m['sizes']['full'] = $m['url'];
					}
					break;
			}

			if ( isset( $m['comment_count'] ) && isset( $m['likes_count'] ) ) {
				$m['popularity'] = (int) ( $m['comment_count'] ) + ( $m['likes_count'] );
			}
		}

		return $m;
	}

	/**
	 * Media Model from account
	 * @param  array $media From API
     *
	 * @return array To plugin format
	 */
	public function to_media_model_from_account( $media ) {

		$m = array();
        switch ($media['media_type'])
        {
            case 'IMAGE':
                $m['type']  = 'GraphImage';
                $m['image'] = $media['media_url'];
                break;
            case 'VIDEO':
                $m['type']      = 'GraphVideo';
                $m['video']     = $media['media_url'];
                $m['thumbnail'] = $media['thumbnail_url'];
                $m['image']    = $media['thumbnail_url'];
                break;
            case 'CAROUSEL_ALBUM':
                $m['type'] = 'GraphSidecar';
                $res = array();
                foreach ($media['children']['data'] as $v)
                {
	                $type = 'images';
                    $t['standard_resolution']['url'] = $v['media_url'];
	                $size = getimagesize($v['media_url']);
	                if(is_array($size)) {
		                $t['standard_resolution']['height'] = $size[1];
		                $t['standard_resolution']['width']  = $size[0];
	                }
	                else $type = 'videos';
                    $res[][$type] = $t;

                }
                $m['sidecar_media'] = $res;
                $m['image']         = $media['media_url'];
                break;
        }

        $m['id']                 = $media['id'];
        $m['username']           = $media['username'];
        $m['caption']            = $this->sanitize( $media['caption'] );
        $m['link']               = $media['permalink'];
        //$m['user_id']            = $media['owner']['id'];
        $m['timestamp']          = $media['timestamp'];
        $m['url']                = $media['media_url'];

		if($media['media_type'] == 'VIDEO')
			$size = getimagesize($media['thumbnail_url']);
		else
            $size = getimagesize($media['media_url']);
		if(is_array($size)) {
			$m['height'] = $size[1];
			$m['width']  = $size[0];
		}

        $m['popularity']         = 0;

		return $m;
	}

	/**
	 * Media Model from account
	 * @param  array $media From API
     *
	 * @return array To plugin format
	 */
	public function to_media_model_from_account_business( $media ) {

		$m = array();
        switch ($media['media_type'])
        {
            case 'IMAGE':
                $m['type'] = 'GraphImage';
                $m['image']      = $media['media_url'];
                break;
            case 'VIDEO':
                $m['type'] = 'GraphVideo';
                $m['video'] = $media['media_url'];
                $m['thumbnail'] = $media['thumbnail_url'];
                $m['image']      = $media['thumbnail_url'];
                break;
            case 'CAROUSEL_ALBUM':
                $m['type'] = 'GraphSidecar';
                $res = array();
                foreach ($media['children']['data'] as $v)
                {
	                $type = 'images';
	                $t['standard_resolution']['url'] = $v['media_url'];
	                $size = getimagesize($v['media_url']);
	                if(is_array($size)) {
		                $t['standard_resolution']['height'] = $size[1];
		                $t['standard_resolution']['width']  = $size[0];
	                }
	                else $type = 'videos';
	                $res[][$type] = $t;
                }
                $m['sidecar_media'] = $res;
                $m['image']      = $media['media_url'];
                break;
        }

        $m['id']                 = $media['id'];
        $m['username']           = $media['username'];
        $m['caption']            = $this->sanitize( $media['caption'] );
        $m['link']               = $media['permalink'];
        $m['user_id']            = $media['owner']['id'];
        $m['timestamp']          = strtotime( $media['timestamp']);
        $m['url']                = $media['media_url'];
		$m['comments']           = $media['comments_count'];
		$m['likes']              = $media['like_count'];

		if($media['media_type'] == 'VIDEO')
		    $size = getimagesize($media['thumbnail_url']);
		else
		    $size = getimagesize($media['media_url']);
		if(is_array($size)) {
			$m['height'] = $size[1];
			$m['width']  = $size[0];
        }

		if ( isset( $m['comments'] ) && isset( $m['likes'] ) ) {
			$m['popularity']         = (int) ( $m['comments'] ) + ( $m['likes'] );
		}
		return $m;
	}

	/**
	 * Media Model from hashtag
	 * @param  array $media From API
	 * @return array To plugin format
	 */
	public function to_media_model_from_hashtag( $media ) {

		$m = array();
		if( isset( $media['fbapi'] ) )
        {
	        $value = $media;
	        switch ($value['media_type'])
	        {
		        case 'IMAGE':
			        $m['type'] = 'GraphImage';
			        $m['image']      = $value['media_url'];
			        break;
		        case 'VIDEO':
			        $m['type'] = 'GraphVideo';
			        $m['video'] = $value['media_url'];
			        $m['thumbnail'] = $value['thumbnail_url'];
			        $m['image']      = $value['thumbnail_url'];
			        break;
		        case 'CAROUSEL_ALBUM':
			        $m['type'] = 'GraphSidecar';
			        $res = array();
			        foreach ($value['children']['data'] as $v)
			        {
				        $t['standard_resolution']['url'] = $v['media_url'];
				        $res[]['images'] = $t;
			        }
			        $m['sidecar_media'] = $res;
			        $m['image']      = $value['children']['data'][0]['media_url'];
			        break;
	        }

	        $m['id']                 = $value['id'];
	        $m['caption']            = $this->sanitize( $value['caption'] );
	        $m['link']               = $value['permalink'];
	        $m['comment_count']      = $value['comments_count'];
	        $m['url']                = $value['media_url'];
	        $m['likes_count']        = $value['like_count'];

	        $m['sizes']['thumbnail'] = $value['media_url'];
	        $m['sizes']['low']       = $value['media_url'];
	        $m['sizes']['standard']  = $value['media_url'];
	        $m['sizes']['full']      = $value['media_url'];

	        if($media['media_type'] == 'VIDEO')
		        $size = getimagesize($value['thumbnail_url']);
	        else
		        $size = getimagesize($value['media_url']);
	        if(is_array($size)) {
		        $m['height'] = $size[1];
		        $m['width']  = $size[0];
	        }

	        $m['popularity']         = (int) ( $m['comment_count'] ) + ( $m['likes_count'] );
        }
		else{
			$value = $media;
            $m['type'] = $value['__typename'];
            $m['id'] = $value['id'];
            $m['code'] = $value['shortcode'];
            $m['link'] = 'https://www.instagram.com/p/'. $value['shortcode'] . '/';
            $m['user_id'] = $value['owner']['id'];

            $m['caption'] = isset($value['edge_media_to_caption']['edges'][0]['node']['text']) ? $value['edge_media_to_caption']['edges'][0]['node']['text'] : "";

            $m['timestamp'] = $value['taken_at_timestamp'];
            $m['url'] = $value['display_url'];
            $m['likes_count'] = $value['edge_liked_by']['count'];
            $m['comment_count'] = $value['edge_media_to_comment']['count'];
            $m['sizes'] = $this->get_thumbnail_urls( $value['thumbnail_resources'] );
			$m['image']      = $value['thumbnail_src'];

			if ( isset( $m['comment_count'] ) && isset( $m['likes_count'] ) ) {
				$m['popularity'] = (int) ( $m['comment_count'] ) + ( $m['likes_count'] );
			}
		}
		return $m;
	}

	/**
	 * Remove Duplicates
	 * @return [type] [description]
	 */
	private function clean_duplicates( $username ) {

		$savedinsta_args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'post_mime_type' => 'image',
			'orderby'		 => 'rand',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'jr_insta_username',
					'compare' => '=',
					'value'   => $username
				),
			),
		);

		$savedinsta   = new WP_Query( $savedinsta_args );

		$opt_name  = 'jr_insta_' . md5( 'user' . '_' . $username );

		$attachment_ids = (array) get_option( $opt_name );

		$deleted_count = 0;

		foreach ( $savedinsta->posts as $post ) {

			if ( !in_array( $post->ID, $attachment_ids['saved_images'] ) ) {

				if ( false !== wp_delete_attachment( $post->ID, true ) ) {
					$deleted_count++;
				}
			}
		}

		wp_reset_postdata();

		return $deleted_count;
	}

	/**
	 * Ajax Call to unblock images
	 * @return void
	 */
	public function delete_dupes() {

		if (function_exists('check_ajax_referer')) {
			check_ajax_referer( 'jr_delete_instagram_dupes' );
		}

		$post = $_POST;
		$return = array(
			'deleted' => $this->clean_duplicates( $post['username'] )
		);

		wp_send_json( $return );
	}

	/**
	 * Ajax Call to add BUSINESS account by token
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function add_account_by_token() {
		if( isset( $_POST['account'] ) && ! empty( $_POST['account'] ) && isset($_POST['_ajax_nonce']))
		{
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( - 2 );
			} else {
				wp_verify_nonce( $_POST['_ajax_nonce'], 'addAccountByToken' );

				$account      = json_decode( stripslashes( $_POST['account']), true );
				$user_profile = array();
				$user_profile = apply_filters( 'wis/account/profiles', $user_profile, true );

				if ( !WIS_Plugin::app()->is_premium() && $this->count_accounts() >= 1) {
					wp_die( 'No premium' );
				}

				$user_profile[ $account['username'] ] = $account;
				WIS_Plugin::app()->updateOption( 'account_profiles_new', $user_profile );

				wp_die( 'Ok' );
			}
		}
		elseif( isset( $_POST['token'] ) && ! empty( $_POST['token'] ) && isset($_POST['_ajax_nonce']))
		{
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( - 2 );
			} else {
				wp_verify_nonce( $_POST['_ajax_nonce'], 'addAccountByToken' );

				$token      =  $_POST['token'];
				$this->update_account_profiles( $token);

				wp_die( '1' );
			}
		}
	}

	/**
	 * Ajax Call to delete account
	 * @return void
	 */
	public function delete_account() {
		if ( isset( $_POST['item_id'] ) && isset( $_POST['is_business'] ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( - 1 );
			} else {
				check_ajax_referer( 'wis_nonce' );

				if((bool)$_POST['is_business'])
					$option_name = 'account_profiles_new';
				else
					$option_name = 'account_profiles';
				$accounts = WIS_Plugin::app()->getPopulateOption( $option_name );
				$accounts_new = array();
				foreach($accounts as $name => $acc) {
				    $id = isset($acc['id']) ? $acc['id'] : 0;
				    if( (int)$id != (int)$_POST['item_id'] && !empty($name) )
				        $accounts_new[$name] = $acc;
				}
				WIS_Plugin::app()->updatePopulateOption( $option_name, $accounts_new);

				wp_send_json_success(__('Account deleted successfully', 'instagram-slider-widget'));
			}
		}
	}

	/**
	 * Get Account data by USERNAME from option in wp_options
	 *
	 * @param string $name
	 * @param bool $is_business
	 *
	 * @return array
	 */
	public function getAccountById($name, $is_business = false)
	{
		if($is_business)
	        $token = WIS_Plugin::app()->getOption( 'account_profiles_new' );
		else
			$token = WIS_Plugin::app()->getOption( 'account_profiles' );
		return $token[$name];
	}

	/**
	 * Get first Account data from option in wp_options
     *
     * @return bool|array
	 */
	public function getAccountForHashtag()
    {
        $token = WIS_Plugin::app()->getOption( 'account_profiles_new', false );
	    if($token && is_array($token) && !empty($token))
            return $token[array_key_first ( $token )];
	    else
		    return false;
    }

	/**
	 * Get Instagram Ids from Usernames into array
	 * @param  string $usernames Comma separated string with instagram users
	 * @return array            An array with instagram ids
	 */
	private function get_ids_from_usernames( $usernames ) {

		$users = explode( ',', trim( $usernames ) );
		$user_ids = (array) get_transient( 'jr_insta_user_ids' );
		$return_ids = array();

		if ( is_array( $users ) && !empty( $users ) ) {

			foreach ( $users as $user ) {

				if ( isset( $user_ids[$user] ) ) {
					continue;
				}

				$results = $this->get_data_by_username($user);
                if ( $results && is_array( $results ) ) {

	                $results = $results['entry_data']['ProfilePage']['0']['graphql']['user'];
                    $user_id = isset( $results['id'] ) ? $results['id'] : false;

                    if ( $user_id ) {

                        $user_ids[$user] = $user_id;

                        set_transient( 'jr_insta_user_ids', $user_ids );
                    }
                }
			}
		}

		foreach ( $users as $user ) {
			if ( isset( $user_ids[$user] ) ) {
				$return_ids[] = $user_ids[$user];
			}
		}

		return $return_ids;
	}


	/**
	 * Updates attachment using the id
	 * @param     int      $attachment_ID
	 * @param     array    image_data
	 * @return    void
	 */
	private function update_wp_attachment( $attachment_ID, $image_data ) {
		update_post_meta( $attachment_ID, 'jr_insta_popularity', $image_data['popularity'] );
		update_post_meta( $attachment_ID, 'jr_insta_likes_count', $image_data['likes_count'] );
		update_post_meta( $attachment_ID, 'jr_insta_comment_count', $image_data['comment_count'] );
	}

	/**
	 * Save Instagram images to upload folder and ads to media.
	 * If the upload fails it returns the remote image url.
	 *
	 * @param    string    $url    		Url of image to download
	 * @param    string    $file    	File path for image
	 *
	 * @return   string    $url 		Url to image
	 */
	private function save_wp_attachment( $image_data ) {

		$image_info = pathinfo( $image_data['url'] );

		if ( !in_array( $image_info['extension'], array( 'jpg', 'jpe', 'jpeg' ) ) ) {
			return false;
		}

		$attachment = array(
			'guid'           => $image_data['url'],
			'post_mime_type' => 'image/jpeg',
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $image_info['basename'] ),
			'post_excerpt'   => $image_data['caption']
		);

		$attachment_metadata = array( 'width' => $image_data['width'], 'height' => $image_data['height'], 'file' => $image_info['basename'] );
		$attachment_metadata['sizes'] = array( 'full' => $attachment_metadata );
		$id = wp_insert_attachment( $attachment );
		wp_update_attachment_metadata( $id, $attachment_metadata );


		unset( $image_data['caption'] );

		foreach ( $image_data as $meta_key => $meta_value ) {
			update_post_meta( $id, 'jr_insta_' . $meta_key, $meta_value );
		}

		return $id;
	}

	/**
	 * Add new attachment Description only for instgram images
	 *
	 * @param  array $form_fields
	 * @param  object $post
	 *
	 * @return array
	 */
	public function insta_attachment_fields( $form_fields, $post ) {

		$instagram_username = get_post_meta( $post->ID, 'jr_insta_username', true );

		if ( !empty( $instagram_username ) ) {

			$form_fields["jr_insta_username"] = array(
				"label" => __( "Instagram Username" ),
				"input" => "html",
				"html"  => "<span style='line-height:31px'><a target='_blank' href='https://www.instagram.com/{$instagram_username}/'>{$instagram_username}</a></span>"
			);

			$instagram_link = get_post_meta( $post->ID, 'jr_insta_link', true );
			if ( !empty( $instagram_link ) ) {
				$form_fields["jr_insta_link"] = array(
					"label" => __( "Instagram Image" ),
					"input" => "html",
					"html"  => "<span style='line-height:31px'><a target='_blank' href='{$instagram_link}'>{$instagram_link}</a></span>"
				);
			}

			$instagram_date = get_post_meta( $post->ID, 'jr_insta_timestamp', true );
			if ( !empty( $instagram_date ) ) {
				$instagram_date = date( "F j, Y, g:i a", $instagram_date );
				$form_fields["jr_insta_time"] = array(
					"label" => __( "Posted on Instagram" ),
					"input" => "html",
					"html"  => "<span style='line-height:31px'>{$instagram_date}</span>"
				);
			}
		}

		return $form_fields;
	}

	/**
	 * Sort Function for timestamp Ascending
	 */
	public function sort_timestamp_ASC( $a, $b ) {
		return $a['timestamp'] > $b['timestamp'];
	}

	/**
	 * Sort Function for timestamp Descending
	 */
	public function sort_timestamp_DESC( $a, $b ) {
		return $a['timestamp'] < $b['timestamp'];
	}

	/**
	 * Sort Function for popularity Ascending
	 */
	public function sort_popularity_ASC( $a, $b ) {
		return $a['popularity'] > $b['popularity'];
	}

	/**
	 * Sort Function for popularity Descending
	 */
	public function sort_popularity_DESC( $a, $b ) {
		return $a['popularity'] < $b['popularity'];
	}

	/**
	 * Sanitize 4-byte UTF8 chars; no full utf8mb4 support in drupal7+mysql stack.
	 * This solution runs in O(n) time BUT assumes that all incoming input is
	 * strictly UTF8.
	 *
	 * @param    string    $input 		The input to be sanitised
	 *
	 * @return string sanitized input
	 */
	private function sanitize( $input ) {

		if ( !empty( $input ) ) {
			$utf8_2byte       = 0xC0 /*1100 0000*/ ;
			$utf8_2byte_bmask = 0xE0 /*1110 0000*/ ;
			$utf8_3byte       = 0xE0 /*1110 0000*/ ;
			$utf8_3byte_bmask = 0XF0 /*1111 0000*/ ;
			$utf8_4byte       = 0xF0 /*1111 0000*/ ;
			$utf8_4byte_bmask = 0xF8 /*1111 1000*/ ;

			$sanitized = "";
			$len       = strlen( $input );
			for ( $i = 0; $i < $len; ++$i ) {

				$mb_char = $input[$i]; // Potentially a multibyte sequence
				$byte    = ord( $mb_char );

				if ( ( $byte & $utf8_2byte_bmask ) == $utf8_2byte ) {
					$mb_char .= $input[++$i];
				} else if ( ( $byte & $utf8_3byte_bmask ) == $utf8_3byte ) {
					$mb_char .= $input[++$i];
					$mb_char .= $input[++$i];
				} else if ( ( $byte & $utf8_4byte_bmask ) == $utf8_4byte ) {
					// Replace with ? to avoid MySQL exception
					$mb_char = '';
					$i += 3;
				}

				$sanitized .= $mb_char;
			}

			$input = $sanitized;
		}

		return $input;
	}

	/**
	 * @param string $token
	 * @param string $is_business
	 * @param string $username
	 *
     * @return bool|array
	 */
	public function update_account_profiles($token, $is_business = false, $username = "") {
		if( $is_business )
        {
	        //Получаем аккаунты привязанные к фейсбуку
	        $args = array(
	                'access_token' => $token,
                    'fields'       => 'instagram_business_account',
                    'limit'        => 200,
                );
	        $url = self::USERS_SELF_URL_NEW."me/accounts";
            $response = wp_remote_get( add_query_arg( $args, $url ) );
	        if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
		        $pages = json_decode( wp_remote_retrieve_body( $response ), true );
		        //$username = $result['data'][0]['name'];
                $html = "";
                $users = array();
                foreach($pages['data'] as $key => $r)
                {
	                if(isset($r['instagram_business_account']) && isset($r['instagram_business_account']['id'])) {
		                $args     = array(
			                'fields'       => 'username,id,followers_count,follows_count,media_count,name,profile_picture_url',
			                'access_token' => $token
		                );
		                $url      = self::USERS_SELF_URL_NEW . $r['instagram_business_account']['id'];
		                $response = wp_remote_get( add_query_arg( $args, $url ) );
		                if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
			                $result = json_decode( wp_remote_retrieve_body( $response ), true );
			                $result['token'] = $token;
			                $users[] = $result;
			                $html .= "<div class='wis-row wis-row-style' id='wis-instagram-row' data-account='".json_encode($result)."'>";
			                $html .= "<div class='wis-col-1 wis-col1-style'><img src='{$result['profile_picture_url']}' width='50' alt='{$result['username']}'></div>";
			                $html .= "<div class='wis-col-2 wis-col2-style'>{$result['name']}<br>@{$result['username']}</div>";
			                $html .= "</div>";
		                }
		                if("" !== $username && $username == $result['username'])
                        {
	                        $user_profile = array();
	                        $user_profile = apply_filters( 'wis/account/profiles', $user_profile, true );

	                        $user_profile[ $result['username'] ] = $result;
	                        WIS_Plugin::app()->updateOption( 'account_profiles_new', $user_profile );
                        }
	                }
                }
		        return array($html, $users);
	        }
        }
		else
        {
            $expires = 0;
        	$profiles = WIS_Plugin::app()->getOption( 'account_profiles', array() );
	        foreach ( $profiles as $profile ) {
                if( $profile['token'] == $token ) {
                    if($profile['expires'] <= time()) {
                        $new = $this->refresh_token( $token );
                        $token = $new['access_token'];
	                    $expires = $new['expires_in']; //5183944 sec
                    }
                    break;
                }
            }

	        $args = array(
		        'fields' => 'id,media_count,username',
		        'access_token' => $token,
	        );

	        $url = self::USERS_SELF_URL;
	        $url = add_query_arg( $args, $url );
	        $response = wp_remote_get( $url);
	        if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
		        $user = json_decode( wp_remote_retrieve_body( $response ), true );
		        if(!isset($user['id']) || empty($user['id'])) return false;

		        $user['token'] = $token;
		        if($expires > 0)
		            $user['expires'] = time()+($expires-86344); //= 5097600 sec = 59 days
                else
                    $user['expires'] = isset($profiles[ $user['username'] ]['expires']) ? $profiles[ $user['username'] ]['expires'] : time()+5097600;
		        $user_profile = array();
		        $user_profile = apply_filters( 'wis/account/profiles', $user_profile );

		        if ( !WIS_Plugin::app()->is_premium() && $this->count_accounts() >= 1) {
		            return array();
		        }

		        $user_profile[ $user['username'] ] = $user;
		        WIS_Plugin::app()->updateOption( 'account_profiles', $user_profile );

                return $user;
	        }
        }
		return false;
    }

	/**
	 * @param string $token
	 *
     * @return array
	 */
	public function refresh_token($token) {
        $args = array(
            'grant_type' => 'ig_refresh_token',
            'access_token' => $token,
        );

        $url = self::USERS_SELF_MEDIA_URL.'refresh_access_token';
        $url = add_query_arg( $args, $url );
        $response = wp_remote_get( $url);
        if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
            $new = json_decode( wp_remote_retrieve_body( $response ), true );
            if(is_array($new)) return $new;
        }
		return array();
    }

	/**
	 * This post is blocked by words?
	 *
     * @param string $words
     * @param string $text
	 *
	 * @return bool
	 */
	public function is_blocked_by_word($words, $text)
	{
		if(empty($words) || empty($text)) return false;
	    $words_array = explode( ',', $words);
		foreach ( $words_array as $word ) {
			$pos = stripos( $text, trim($word) );
			if($pos !== false) return true;
		}
		return false;
	}

	public function get_user_by_token($token)
	{
		$args = array(
	        'fields' => 'id,media_count,username',
	        'access_token' => $token,
        );

        $url = self::USERS_SELF_URL;
        $url = add_query_arg( $args, $url );
        $response = wp_remote_get( $url);
        if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
	        $user = json_decode( wp_remote_retrieve_body( $response ), true );
	        $user['token'] = $token;
	        return $user;
        }
        return false;
	}

	/**
	 * Get count of accounts
	 *
	 * @return int
	 */
	public function count_accounts()
	{
		$account  = WIS_Plugin::app()->getOption( 'account_profiles', array());
	    $accont_b = WIS_Plugin::app()->getOption( 'account_profiles_new', array());
	    return count($account) + count($accont_b);
	}

} // end of class WIS_InstagramSlider
?>
