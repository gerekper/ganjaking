<?php

require_once WIS_PLUGIN_DIR . '/includes/socials/class.wis_facebook.php';

/**
 * WIS_FacebookSlider Class
 */
class WIS_FacebookSlider extends WP_Widget {

    private static $app;

	/**
	 * @var WIS_Plugin
	 */
	public $WIS;

	/**
	 * @var WIS_Facebook
	 */
	public $FACEBOOK;

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
		parent::__construct( 'wis_facebook_slider', __( 'Social Slider - Facebook', 'instagram-slider-widget' ), array(
				'classname' => 'jr-insta-slider',
				'description' => __( 'A widget that displays a slider with Facebook posts ', 'instagram-slider-widget' )
			)
		);

		$this->WIS = WIS_Plugin::app();
		$this->FACEBOOK = WIS_Plugin::social( 'WIS_Facebook');

		$this->sliders = array(
			"slider"           => 'Slider - Normal',
		);
        $this->options_linkto = array(
            "image_link" => 'Facebook post',
            "image_url" => 'Facebook page',
            "none" => 'None'
        );

		/**
		 * Фильтр для добавления слайдеров
		 */
		$this->sliders = apply_filters('wis/facebook/sliders', $this->sliders);

		/**
		 * Фильтр для добавления popup
		 */
        $this->options_linkto = apply_filters('wis/options/link_to', $this->options_linkto);

        // Shortcode
		add_shortcode( 'jr_facebook', array( $this, 'shortcode' ) );

		// Instgram Action to display images
		add_action( 'jr_facebook', array( $this, 'facebook_images' ) );

		// Enqueue Plugin Styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this,	'public_enqueue' ) );

		// Enqueue Plugin Styles and scripts for admin pages
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
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

		wp_enqueue_style(  WIS_Plugin::app()->getPrefix() . 'fb-slider', WIS_PLUGIN_URL.'/assets/css/instag-slider.css', array(), WIS_Plugin::app()->getPluginVersion() );
		wp_enqueue_style(  WIS_Plugin::app()->getPrefix() . 'fb-header', WIS_PLUGIN_URL.'/assets/css/wis-header.css', array(), WIS_Plugin::app()->getPluginVersion() );

		wp_enqueue_script( WIS_Plugin::app()->getPrefix() . 'jquery-pllexi-slider', WIS_PLUGIN_URL.'/assets/js/jquery.flexslider-min.js', array( 'jquery' ), WIS_Plugin::app()->getPluginVersion(), false );
		//wp_enqueue_script( WIS_Plugin::app()->getPrefix() . 'jr-fb', WIS_PLUGIN_URL.'/assets/js/jr-insta.js', array(  ), WIS_Plugin::app()->getPluginVersion(), false );
	}

	/**
	 * Enqueue admin side scripts and styles
	 *
	 * @param  string $hook
	 */
	public function admin_enqueue( $hook ) {

		if ( 'widgets.php' != $hook ) {
			return;
		}
		wp_enqueue_style( WIS_Plugin::app()->getPrefix() . 'fb-admin-styles', WIS_PLUGIN_DIR.'/admin/assets/css/jr-insta-admin.css', array(), WIS_Plugin::app()->getPluginVersion() );
		wp_enqueue_script( WIS_Plugin::app()->getPrefix() . 'fb-admin-script', WIS_PLUGIN_DIR.'/admin/assets/js/jr-insta-admin.js',  array( 'jquery' ), WIS_Plugin::app()->getPluginVersion(), true );

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
		$instance['account']          = isset($new_instance['account']) ? $new_instance['account'] : null;
		$instance['images_number']    = isset($new_instance['images_number']) ? $new_instance['images_number'] : 20;
		$instance['refresh_hour']     = isset($new_instance['refresh_hour']) ? $new_instance['refresh_hour'] : 5;
		$instance['template']         = isset($new_instance['template']) ? $new_instance['template'] : 'slider';
		$instance['images_link']      = isset($new_instance['images_link']) ? $new_instance['images_link']  : 'image_link';
		$instance['orderby']          = isset($new_instance['orderby']) ? $new_instance['orderby'] : 'rand';

		return $instance;
	}


	/**
	 * Widget Settings Form
	 *
	 */
	public function form( $instance ) {

		$accounts = WIS_Plugin::app()->getOption( WIS_FACEBOOK_ACCOUNT_PROFILES_OPTION_NAME , array());
		$sliders = $this->sliders;
        $options_linkto = $this->options_linkto;

		$defaults = array(
			'title'            => __('Facebook feed', 'instagram-slider-widget'),
			'account'          => '',
			'images_number'    => 20,
			'refresh_hour'     => 5,
			'template'         => 'slider',
			'images_link'      => 'image_link',
			'orderby'          => 'date-DESC',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		?>
        <div class="jr-container">
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><strong><?php _e('Title:', 'instagram-slider-widget'); ?></strong></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'account' ); ?>"><strong><?php _e( 'Search Facebook for:', 'instagram-slider-widget' ); ?></strong></label>
                <span class="jr-search-for-container">
                    <?php
                    if(count($accounts))
                    {?>

                        <select id="<?php echo $this->get_field_id( 'account' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'account' ); ?>"><?php
                        foreach ($accounts as $acc)
                            {
	                            $selected = $instance['account'] == $acc['username'] ? "selected='selected'" : "";
	                            echo "<option value='{$acc['username']}' {$selected}>{$acc['username']}</option>";
                            }
                            ?>
                        </select><?php
                    }
                    else{
                        echo "<a href='".admin_url('admin.php?page=settings-wisw&tab=facebook')."'>".__('Add account in settings','instagram-slider-widget')."</a>";
                    }
                    ?>
                </span>
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
	                    /*if(!$this->WIS->is_premium())
                        {
                            ?>
                            <optgroup label="Available in PRO">
                                <option value='1' disabled="disabled">Slick</option>
                                <option value='2' disabled="disabled">Masonry</option>
                                <option value='3' disabled="disabled">Highlight</option>
                            </optgroup>
                            <?php
                        }*/
	                    ?>
                    </select>
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
	                    <?php
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
	        <p>
		        <label for="jr_insta_shortcode"><?php _e('Shortcode of this Widget:', 'instagram-slider-widget'); ?></label>
				<?php
				$widget_id = preg_replace( '/[^0-9]/', '', $this->id );
				if ( $widget_id != '' ) {
					?>
                    <input id="jr_insta_shortcode" onclick="this.setSelectionRange(0, this.value.length)" type="text" class="widefat" value="[jr_facebook id=&quot;<?php echo $widget_id ?>&quot;]" readonly="readonly" style="border:none; color:black; font-family:monospace;">
					<?php
				}
				else {
					?>
					<input id="jr_insta_shortcode" type="text" class="widefat" value="<?php echo __('Click Save and refresh page', 'instagram-slider-widget'); ?>" readonly="readonly" style="border:none; color:black; font-family:monospace;">
					<?php
				}
				?>
		        <span class="jr-description"><?php _e( 'Use this shortcode in any page or post to display images with this widget configuration!', 'instagram-slider-widget') ?></span>
	        </p>
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

		$atts = shortcode_atts( array( 'id' => '' ), $atts, 'jr_facebook' );
		$args = get_option( 'widget_wis_facebook_slider' );
		if ( isset($args[$atts['id']] ) ) {
			$args[$atts['id']]['widget_id'] = $atts['id'];
			return $this->display_images( $args[$atts['id']] );
		}
		return "";
	}

	/**
	 * Echoes the Display Instagram Images method
	 *
	 * @param  array $args
	 *
	 * @return void
	 */
	public function facebook_images( $args ) {
	    echo $this->display_images( $args );
	}

	/**
	 * Cron Trigger Function
	 * @param  [type] $username     [description]
	 * @param  [type] $refresh_hour [description]
	 * @param  [type] $images       [description]
	 */
	public function jr_cron_trigger( $username, $refresh_hour, $images ) {
		$search_for = array();
		$search_for['username'] =  $username;
		$this->instagram_data( $search_for, $refresh_hour, $images, true );
	}

	/**
	 * Runs the query for images and returns the html
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	private function display_images( $args ) {
		$account          = isset( $args['account'] ) && !empty( $args['account'] ) ? $args['account'] : false;
		$images_number    = isset( $args['images_number'] ) ? absint( $args['images_number'] ) : 20;
		$refresh_hour     = isset( $args['refresh_hour'] ) ? absint( $args['refresh_hour'] ) : 5;
		$template         = isset( $args['template'] ) ? $args['template'] : 'slider';
		$orderby          = isset( $args['orderby'] ) ? $args['orderby'] : 'rand';
		$images_link      = isset( $args['images_link'] ) ? $args['images_link'] : 'image_url';
		$widget_id        = isset( $args['widget_id'] ) ? $args['widget_id'] : preg_replace( '/[^0-9]/', '', $this->id );

        $images_data = $this->FACEBOOK->get_data( $account, $refresh_hour, $images_number );
        $account_data = $this->FACEBOOK->getAccountByName( $account);

        if ( is_array( $images_data ) && !empty( $images_data ) ) {
            if(isset($images_data['error'])) {
                return $images_data['error'];
            }

            if ( $orderby == 'rand' ) {
	            shuffle( $images_data );
            } else {
	            $orderby = explode( '-', $orderby );
	            $func = $orderby[0] == 'date' ? 'sort_timestamp_' . $orderby[1] : 'sort_popularity_' . $orderby[1];
	            usort( $images_data, array( $this, $func ) );
            }

	        $template_args = array(
		        'account'          => $account_data,
		        'template'         => $template,
		        'images_link'      => $images_link,
		        'posts'            => $images_data,
	        );

            $output = "";
	        switch($template)
            {
                case 'slider':
	                $output = $this->render_layout_template('facebook_slider_template', $template_args);
                    break;
            }
        }
        else {
	        $output = __( 'No images found!', 'instagram-slider-widget' );
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

}
?>
