<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Code is poetry.';
    exit;
}

//Gzip Compression
if( get_option("wpuf_gzip_comp") == 1 ) {
	
	function comp_gzip () {
		if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
			ob_start("ob_gzhandler"); 
		} else  {
			ob_start();
		}
	}
	
	add_action('init' ,'comp_gzip');
}

//HTML Page Minifier
if( get_option("wpuf_page_minifier") == 1 ) {
	if (!( is_admin() )) {
		function ss_page_minify($buffer){
			$search = array(
				'/[^\S ]+\</s',
				'/(\s)+/s',
				'~<!--//(.*?)-->~s'
			);
			$replace = array(
				'<',
				'\\1',
				''
			);
			$buffer = preg_replace($search, $replace, $buffer);
			return $buffer;
		}
		
		function wpuf_minify_page_func(){
			ob_start('ss_page_minify');
		}

		add_action('init','wpuf_minify_page_func');
	}
}

//Start Lazy Load Images
if( get_option("wpuf_lazy_load") == 1 ) {

	class wpuf_lazyload_func {

		protected static $enabled = true;
		
		static function init() {
			if ( is_admin() )
				return;

			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_scripts' ) );
			add_action( 'wp_head', array( __CLASS__, 'setup_filters' ), 9999 );
		}

		static function setup_filters() {
			add_filter( 'the_content', array( __CLASS__, 'add_image_placeholders' ), 99 );
			add_filter( 'post_thumbnail_html', array( __CLASS__, 'add_image_placeholders' ), 11 );
			add_filter( 'get_avatar', array( __CLASS__, 'add_image_placeholders' ), 11 );
		}

		static function add_scripts() {
			wp_enqueue_script( 'wpuf-lazyload', WPUF_URL.'functions/assets/js/lazy-load.js' , array( 'jquery', 'jquery-sonar' ), '1.0.0', true );
			wp_enqueue_script( 'jquery-sonar', WPUF_URL.'functions/assets/js/jquery.sonar.min.js', array( 'jquery' ), '1.0.0', true );
		}

		static function add_image_placeholders( $content ) {		
			if( is_feed() || is_preview() )
				return $content;

			if ( false !== strpos( $content, 'data-lazy-src' ) )
				return $content;

			$content = preg_replace_callback( '#<(img)([^>]+?)(>(.*?)</\\1>|[\/]?>)#si', array( __CLASS__, 'process_image' ), $content );

			return $content;
		}

		static function process_image( $matches ) {
			$old_attributes_str = $matches[2];
			$old_attributes = wp_kses_hair( $old_attributes_str, wp_allowed_protocols() );

			if ( empty( $old_attributes['src'] ) ) {
				return $matches[0];
			}

			$image_src = $old_attributes['src']['value'];

			$new_attributes = $old_attributes;
			unset( $new_attributes['src'], $new_attributes['data-lazy-src'] );

			$new_attributes_str = self::build_attributes_string( $new_attributes );

			return sprintf( '<img data-lazy-src="%1$s" %2$s><noscript>%3$s</noscript>', esc_url( $image_src ), $new_attributes_str, $matches[0] );
		}

		private static function build_attributes_string( $attributes ) {
			$string = array();
			foreach ( $attributes as $name => $attribute ) {
				$value = $attribute['value'];
				if ( '' === $value ) {
					$string[] = sprintf( '%s', $name );
				} else {
					$string[] = sprintf( '%s="%s"', $name, esc_attr( $value ) );
				}
			}
			return implode( ' ', $string );
		}

		static function get_url( $path = '' ) {
			return plugins_url( ltrim( $path, '/' ), __FILE__ );
		}
	}

	function wpuf_lazyload_func_add_placeholders( $content ) {
		return wpuf_lazyload_func::add_image_placeholders( $content );
	}

	add_action( 'init', array( 'wpuf_lazyload_func', 'init' ) );

}
//End Lazy Load

//Disable Emojis
if( get_option("wpuf_disable_emojis") == 1 ) {
	
	function disable_wp_emojicons() {
		
		// Disable Emojis
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

		// Disable Smilies
		add_filter( 'option_use_smilies', '__return_false' );
		
		//DNS Prefetch
		add_filter( 'emoji_svg_url', '__return_false' );

		//remove TinyMCE emojis
		add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
	}
  
	function disable_emojicons_tinymce( $plugins ) {
	  if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
	  } else {
			return array();
		}
	}
	
	add_action( 'init', 'disable_wp_emojicons' );

}

//Remove Scripts
if( get_option("wpuf_remove_jquery_migrate") == 1 ) {
	
function remove_jq_mig($scripts) {
    if(!is_admin()) {
        $scripts->remove( 'jquery');
        $scripts->add( 'jquery', false, array( 'jquery-core' ), '1.12.4' );
    }
}

add_filter( 'wp_default_scripts', 'remove_jq_mig' );
}

//Remove Woocommerce Styles and Scripts
if( get_option("wpuf_woo_remove_scripts") == 1 ) {
	
	function wpuf_woo_remove_scripts_func() {
		if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
			
			wp_dequeue_style( 'woocommerce_frontend_styles' );
			wp_dequeue_style( 'woocommerce-general');
			wp_dequeue_style( 'woocommerce-layout' );
			wp_dequeue_style( 'woocommerce-smallscreen' );
			wp_dequeue_style( 'woocommerce_fancybox_styles' );
			wp_dequeue_style( 'woocommerce_chosen_styles' );
			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
			wp_dequeue_style( 'select2' );
			wp_dequeue_script( 'wc-add-payment-method' );
			wp_dequeue_script( 'wc-lost-password' );
			wp_dequeue_script( 'wc_price_slider' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-add-to-cart' );
			wp_dequeue_script( 'wc-cart-fragments' );
			wp_dequeue_script( 'wc-credit-card-form' );
			wp_dequeue_script( 'wc-checkout' );
			wp_dequeue_script( 'wc-add-to-cart-variation' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-cart' );
			wp_dequeue_script( 'wc-chosen' );
			wp_dequeue_script( 'woocommerce' );
			wp_dequeue_script( 'prettyPhoto' );
			wp_dequeue_script( 'prettyPhoto-init' );
			wp_dequeue_script( 'jquery-blockui' );
			wp_dequeue_script( 'jquery-placeholder' );
			wp_dequeue_script( 'jquery-payment' );
			wp_dequeue_script( 'fancybox' );
			wp_dequeue_script( 'jqueryui' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'wpuf_woo_remove_scripts_func', 99 );
}

//Remove BuddyPress Styles and Scripts
if( get_option("wpuf_remove_bp_scripts") == 1 ) {
	function wpuf_remove_bp_scripts_func() {
		if ( ! is_buddypress() ) {
			wp_dequeue_style( 'bp-legacy-css' );
			wp_deregister_script('bp-jquery-query');
			wp_deregister_script('bp-confirm');
		}
	}
	
add_action( 'wp_enqueue_scripts', 'wpuf_remove_bp_scripts_func', 99 );
}

//Remove BBPress Styles and Scripts
if( get_option("wpuf_bbp_style_remover") == 1 ) {
	function wpuf_bbp_style_remover_func() {
		if ( class_exists('bbPress') ) {
		  if ( ! is_bbpress() ) {
			wp_dequeue_style('bbp-default');
			wp_dequeue_style( 'bbp_private_replies_style');
			wp_dequeue_script('bbpress-editor');
		  }
		}
	}
	add_action( 'wp_enqueue_scripts', 'wpuf_bbp_style_remover_func', 99 );

}

//Remove Author Links and Archives
if( get_option("wpuf_author_redirect") == 1 ) {
	
	function author_page_redirect() {
		if ( is_author() ) {
			wp_safe_redirect( home_url(), 301 );
			exit;
		}
	}

	add_action( 'template_redirect', 'author_page_redirect' );

	function author_remove_link() {
			# Return homepage URL
			return home_url();
	}

	add_filter( 'author_link', 'author_remove_link' );
}

//Remove Shortlinks
if( get_option("wpuf_remove_shortlinks") == 1 ) {
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
}

//Remove Query Strings
if( get_option("wpuf_remove_query_strings") == 1 ) {
	
	function remove_query_strings_ver1( $src ){	
		$find_query_and_remove = explode( '?ver', $src );
			return $find_query_and_remove[0];
	}
	
	if ( !is_admin() ) {
		add_filter( 'script_loader_src', 'remove_query_strings_ver1', 15, 1 );
		add_filter( 'style_loader_src', 'remove_query_strings_ver1', 15, 1 );
	}	
	
	function remove_query_strings_ver2( $src ){	
		$find_query_and_remove = explode( '&ver', $src );
			return $find_query_and_remove[0];
	}
	
	if ( !is_admin() ) {
		add_filter( 'script_loader_src', 'remove_query_strings_ver2', 15, 1 );
		add_filter( 'style_loader_src', 'remove_query_strings_ver2', 15, 1 );
	}
}

//Remove Feeds
if( get_option("wpuf_remove_feeds") == 1 ) {
	function wpuf_remove_feeds_func() {
	 wp_die( __( 'Feeds disabled.', 'ua-protection-lang' ) );
	}

	add_action('do_feed', 'wpuf_remove_feeds_func', 1);
	add_action('do_feed_rdf', 'wpuf_remove_feeds_func', 1);
	add_action('do_feed_rss', 'wpuf_remove_feeds_func', 1);
	add_action('do_feed_rss2', 'wpuf_remove_feeds_func', 1);
	add_action('do_feed_atom', 'wpuf_remove_feeds_func', 1);
	add_action('do_feed_rss2_comments', 'wpuf_remove_feeds_func', 1);
	add_action('do_feed_atom_comments', 'wpuf_remove_feeds_func', 1);
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'feed_links', 2 );
}

//Browser Caching
if( get_option("wpuf_browser_caching") == 1 ) {
	$cache_seconds = get_option('wpuf_browser_cache_time');
	$cache_gmt = gmdate("D, d M Y H:i:s", time() + $cache_seconds) . " GMT";
	header("Expires: $cache_gmt");
	header("Pragma: cache");
	header("Cache-Control: max-age=$cache_seconds");
}

//Header Scripts to Footer
if( get_option("wpuf_headtofooter_opt") == 1 ) {
	function wpuf_headtofooter_func() {
		
		global $wp_scripts;
		if ( isset ( $wp_scripts->registered ) && ! empty ( $wp_scripts->registered ) && is_array( $wp_scripts->registered ) ) {
			foreach ( $wp_scripts->registered as $idx => $script ) {
				if ( isset( $wp_scripts->registered[ $idx ]->extra ) && is_array( $wp_scripts->registered[ $idx ]->extra ) ) {
					
					$wp_scripts->registered[ $idx ]->extra[ 'group' ] = 1;
				}
			}
		}
	}

	add_action('wp_print_scripts', 'wpuf_headtofooter_func', 1000);
}

//Async and Defer Functions
if( get_option("wpuf_asydef_attr") == 1 ) {
	
	//ASYNC TAG
	function addAsync($url) {
		if ( FALSE === strpos( $url, '.js' )) {
			return $url;
		}
		
		return "$url' async='async"; 
	}

	add_filter( 'clean_url', 'addAsync', 11, 1);

} 

if( get_option("wpuf_asydef_attr") == 2 ) {

	//Def Tag
	function addDefer($url) {
		if ( FALSE === strpos( $url, '.js' )) {
			return $url;
		}
		
		return "$url' defer='defer"; 
	}

	add_filter( 'clean_url', 'addDefer', 11, 1);

}