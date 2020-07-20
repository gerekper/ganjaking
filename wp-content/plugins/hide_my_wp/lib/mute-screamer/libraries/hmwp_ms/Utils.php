<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Mute Screamer utils class
 */
class HMWP_MS_Utils {
	/**
	 * Client ip address
	 *
	 * @var string
	 */
	public static $ip = false;

	/**
	 * Load a template file
	 *
	 * @return void|string
	 */
	public static function view( $view, $vars = array(), $return = false ) {
		$found = false;

		// Look in Mute Screamer views and the current Wordpress theme directories
		for ( $i = 1; $i < 3; $i++ ) {
			$path = ($i % 2) ? HMWP_MS_PATH . '/views/' : TEMPLATEPATH . '/';
			$view_path = $path . $view . '.php';

			// Does the file exist?
			if ( file_exists( $view_path ) ) {
				$found = true;
				break;
			}
		}

		if ( $found === true ) {
			extract( $vars );
			ob_start();

			include( $view_path );

			// Return the data if requested
			if ( $return === true ) {
				$buffer = ob_get_contents();
				@ob_end_clean();
				return $buffer;
			}

			$output = ob_get_contents();
			@ob_end_clean();

			echo $output;
		} else if ( defined( 'WP_DEBUG' ) && WP_DEBUG == true ) {
			trigger_error( __( 'Unable to load the requested view.', 'mute-screamer' ), E_USER_ERROR );
		}
	}

	/**
	 * Create pagination links
	 *
	 * @return string
	 */
	public static function pagination( $current_page = 1, $total_pages = 0, $per_page = 0, $count = 0 )
	{
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'prev_text' => __( '&laquo;', 'mute-screamer' ),
			'next_text' => __( '&raquo;', 'mute-screamer' ),
			'total' => $total_pages,
			'current' => $current_page,
		) );

		if ( !$page_links ) {
			return '';
		}

		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'mute-screamer' ) . '</span>%s',
			number_format_i18n( ( $current_page - 1 ) * $per_page + 1 ),
			number_format_i18n( min( $current_page * $per_page, $count ) ),
			number_format_i18n( $count ),
			$page_links
		);

		return "<div class='tablenav-pages'>{$page_links_text}</div>";
	}

	/**
	 * Get intrusions per page option
	 *
	 * @return integer
	 */
	public static function hmwp_ms_intrusions_per_page() {
		$per_page = (int) get_user_option( 'hmwp_ms_intrusions_per_page' );

		// Set default if user option does not exist
		if ( !$per_page ) {
			$per_page = 20;
		}

		return $per_page;
	}

	/**
	 * Get the current site's upload path
	 *
	 * @return string
	 */
	public static function upload_path() {
		$upload_dir = wp_upload_dir();

		if ( ! isset( $upload_dir['basedir'] ) ) {
			return '';
		}

		return $upload_dir['basedir'];
	}

	/**
	 * Show admin notice if the uploads folder is not writable
	 *
	 * @return void
	 */
	public static function writable_notice() {
		echo '<div class="update-nag">' . sprintf( __( 'HMWP requires that your uploads folder %s is writable.', 'mute-screamer' ), self::upload_path() ) . '</div>';
	}

	/**
	 * Show admin notice for multisite install
	 *
	 * @return void
	 */
	/*public static function ms_notice() {
		echo '<div class="update-nag">' . __( 'Mute Screamer multisite install currently not supported.', 'mute-screamer' ) . '</div>';
	}*/


	/**
	 * Fetch ip address
	 *
	 * @return string
	 */
	public static function ip_address() {
		$ip = '0.0.0.0';

		if ( self::$ip )
			return self::$ip;


                foreach ( array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ) as $key ) {
                    if ( ! isset( $_SERVER[$key] ) )
                        continue;

                    foreach ( explode( ',', $_SERVER[$key] ) as $val ) {
                        $ip = trim( $val );

                        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {

                        }
                    }
                }

        //Most safe
        if(getenv('REMOTE_ADDR'))
            $ip = getenv('REMOTE_ADDR');


        self::$ip = $ip;
		return $ip;
	}

	/**
	 * Text diff. This is the same as wp_text_diff, the only
	 * difference is we use a custom text diff render class.
	 *
	 * @param string the left file to compare
	 * @param string the right file to compare
	 * @return string Rendered table of diff files
	 */
	public static function text_diff( $left_string, $right_string, $args = null ) {
		$defaults = array( 'title' => '', 'title_left' => '', 'title_right' => '' );
		$args = wp_parse_args( $args, $defaults );

		if ( ! class_exists( 'WP_Text_Diff_Renderer_Table' ) )
			require_once( ABSPATH . WPINC . '/wp-diff.php' );

		if ( ! class_exists( 'HMWP_MS_Text_Diff_Renderer_Table' ) )
			require_once( 'hmwp_ms/Text_Diff_Render.php' );

		$left_string  = normalize_whitespace( $left_string );
		$right_string = normalize_whitespace( $right_string );

		$left_lines  = explode( "\n", $left_string );
		$right_lines = explode( "\n", $right_string );

		$text_diff = new Text_Diff( $left_lines, $right_lines );
		$renderer  = new HMWP_MS_Text_Diff_Renderer_Table();
		$diff = $renderer->render( $text_diff );

		if ( !$diff )
			return '';

		$r  = "<table class='diff'>\n";
		$r .= "<col class='ltype' /><col class='content' /><col class='ltype' /><col class='content' />";

		if ( $args['title'] || $args['title_left'] || $args['title_right'] )
			$r .= '<thead>';
		if ( $args['title'] )
			$r .= "<tr class='diff-title'><th colspan='4'>$args[title]</th></tr>\n";
		if ( $args['title_left'] || $args['title_right'] ) {
			$r .= "<tr class='diff-sub-title'>\n";
			$r .= "\t<td></td><th>$args[title_left]</th>\n";
			$r .= "\t<td></td><th>$args[title_right]</th>\n";
			$r .= "</tr>\n";
		}
		if ( $args['title'] || $args['title_left'] || $args['title_right'] )
			$r .= "</thead>\n";

		$r .= "<tbody>\n$diff\n</tbody>\n";
		$r .= '</table>';

		return $r;
	}

	/**
	 * Fetch item from the GET array
	 *
	 * @param string
	 * @return mixed
	 */
	public static function get( $index = '' ) {
		return self::_fetch_from_array( $_GET, $index );
	}

	/**
	 * Fetch item from the POST array
	 *
	 * @param string
	 * @return mixed
	 */
	public static function post( $index = '' ) {
		return self::_fetch_from_array( $_POST, $index );
	}

	/**
	 * Fetch item from the SERVER array
	 *
	 * @param string
	 * @return mixed
	 */
	public static function server( $index = '' ) {
		return self::_fetch_from_array( $_SERVER, $index );
	}

	/**
	 * Fetch items from global arrays
	 *
	 * @param array
	 * @param string
	 * @return mixed
	 */
	private static function _fetch_from_array( $array, $index = '' ) {
		if ( ! isset( $array[$index] ) )
			return false;

		return $array[$index];
	}

	/**
	 * Is the current page wp-login.php?
	 *
	 * @return boolean
	 */
	public static function is_wp_login() {
		return ( strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) !== false );
	}
}
