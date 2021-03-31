<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.1
 * @copyright       (C) 2018 - 2021 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\Ungrabber\Unity;

use Exception;
use WP_Filesystem_Direct;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Collection of useful methods.
 *
 * @since 1.0.0
 *
 **/
final class Helper {

	/**
	 * The one true Helper.
	 *
     * @static
     * @since 1.0.0
	 * @var Helper
	 **/
	private static $instance;

	/**
	 * Initializes WordPress filesystem.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return object WP_Filesystem
	 **/
	public static function init_filesystem() {

		$credentials = [];

		if ( ! defined( 'FS_METHOD' ) ) {
			define( 'FS_METHOD', 'direct' );
		}

		$method = defined( 'FS_METHOD' ) ? FS_METHOD : false;

		/** FTP */
		if ( 'ftpext' === $method ) {

			/** If defined, set credentials, else set to NULL. */
			$credentials['hostname'] = defined( 'FTP_HOST' ) ? preg_replace( '|\w+://|', '', FTP_HOST ) : null;
			$credentials['username'] = defined( 'FTP_USER' ) ? FTP_USER : null;
			$credentials['password'] = defined( 'FTP_PASS' ) ? FTP_PASS : null;

			/** FTP port. */
			if ( null !== $credentials['hostname'] && strpos( $credentials['hostname'], ':' ) ) {
				list( $credentials['hostname'], $credentials['port'] ) = explode( ':', $credentials['hostname'], 2 );
				if ( ! is_numeric( $credentials['port'] ) ) {
					unset( $credentials['port'] );
				}
			} else {
				unset( $credentials['port'] );
			}

			/** Connection type. */
			if ( defined( 'FTP_SSL' ) && FTP_SSL ) {
				$credentials['connection_type'] = 'ftps';
			} elseif ( ! array_filter( $credentials ) ) {
				$credentials['connection_type'] = null;
			} else {
				$credentials['connection_type'] = 'ftp';
			}
		}

		/** The WordPress filesystem. */
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once wp_normalize_path( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem( $credentials );
		}

		return $wp_filesystem;

	}

	/**
	 * Get remote contents.
	 *
	 * @param  string $url  The URL we're getting our data from.
     *
     * @since 1.0.0
     * @access public
     *
	 * @return false|string The contents of the remote URL, or false if we can't get it.
	 **/
	public function get_remote( $url ) {

		$args = [
			'timeout'    => 30,
			'user-agent' => 'ungrabber-user-agent',
		];

		$response = wp_remote_get( $url, $args );
		if ( is_array( $response ) ) {
			return $response['body'];
		}

		/** Error while downloading remote file. */
		return false;

	}

	/**
	 * Write content to the destination file.
	 *
	 * @param $destination - The destination path.
	 * @param $content - The content to write in file.
     *
     * @since 1.0.0
     * @access public
     *
     * @return bool Returns true if the process was successful, false otherwise.
	 **/
	public function write_file( $destination, $content ) {

		/** Content for file is empty. */
		if ( ! $content ) { return false; }

		/** Build the path. */
		$path = wp_normalize_path( $destination );

		/** Define constants if undefined. */
		if ( ! defined( 'FS_CHMOD_DIR' ) ) {
			define( 'FS_CHMOD_DIR', ( 0755 & ~ umask() ) );
		}

		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );
		}

		/** Try to put the contents in the file. */
		global $wp_filesystem;

		$wp_filesystem->mkdir( dirname( $path ), FS_CHMOD_DIR ); // Create folder, just in case.

		$result = $wp_filesystem->put_contents( $path, $content, FS_CHMOD_FILE );

		/** We can't write file.  */
		if ( ! $result ) {
			return false;
		}

		return $result;

	}

	/**
	 * Send Action to our remote host.
	 *
	 * @param $action - Action to execute on remote host.
	 * @param $plugin - Plugin slug.
	 * @param $version - Plugin version.
     *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function send_action( $action, $plugin, $version ) {

		$domain = parse_url( site_url(), PHP_URL_HOST );
		$admin = base64_encode( get_option( 'admin_email' ) );
		$pid = get_option( 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id() );

		$url = 'https://merkulove.host/wp-content/plugins/mdp-purchase-validator/src/Merkulove/PurchaseValidator/Validate.php?';
		$url .= 'action=' . $action . '&'; // Action.
		$url .= 'plugin=' . $plugin . '&'; // Plugin Name.
		$url .= 'domain=' . $domain . '&'; // Domain Name.
		$url .= 'version=' . $version . '&'; // Plugin version.
		$url .= 'pid=' . $pid . '&'; // Purchase Code.
		$url .= 'admin_e=' . $admin;

        wp_remote_get( $url, [
            'timeout'   => 10,
            'blocking'  => false,
        ] );

	}

	/**
	 * Return allowed tags for wp_kses filtering with svg tags support.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return array
	 **/
	public static function get_kses_allowed_tags_svg() {

		/** Allowed HTML tags in post. */
		$kses_defaults = wp_kses_allowed_html( 'post' );

		/** Allowed HTML tags and attributes in svg. */
		$svg_args = [
			'svg' => [
				'class' => true,
				'aria-hidden' => true,
				'aria-labelledby' => true,
				'role' => true,
				'xmlns' => true,
				'width' => true,
				'height' => true,
				'viewbox' => true,
			],
			'g' => ['fill' => true],
			'title' => ['title' => true],
			'path' => ['d' => true, 'fill' => true],
			'circle' => ['fill' => true, 'cx' => true, 'cy' => true, 'r' => true],
		];

		return array_merge( $kses_defaults, $svg_args );

	}

    /**
     * Remove directory with all contents.
     *
     * @param $dir - Directory path to remove.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function remove_directory( $dir ) {

        require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
        require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );

        $fileSystemDirect = new WP_Filesystem_Direct( false );
        $fileSystemDirect->rmdir( $dir, true );

    }

    /**
     * Start session
     *
     * @since  1.0.0
     * @access public
     **/
    public static function start_session() {

        if ( ! session_id() ) {

            try {
                session_start();
            } catch ( Exception $e ) {}

        }

    }

    /**
     * Destroy the session
     *
     * @since  1.0.0
     * @access public
     **/
    public static function end_session() {

        try {
            session_destroy();
        } catch ( Exception $e ) {}

    }

    /**
     * Render inline svg by id or icon name.
     *
     * @param int|string $icon - media id, or icon name.
     *
     * @access public
     * @since  1.0.0
     * @return void|string
     **/
    public function get_inline_svg( $icon ) {

        /** If this users custom svg. */
        if ( is_numeric( $icon ) ) {
            $icon = get_attached_file( $icon );

        /** If icon from library. */
        } else {
            $icon = Plugin::get_path() . 'images/mdc-icons/' . $icon;
        }

        if ( ! is_file( $icon ) ) { return ''; }

        $svg_icon = file_get_contents( $icon );

        /** Escaping SVG with KSES. */
        $kses_defaults = wp_kses_allowed_html( 'post' );

        $svg_args = [
            'svg'   => [
                'class' => true,
                'aria-hidden' => true,
                'aria-labelledby' => true,
                'role' => true,
                'xmlns' => true,
                'width' => true,
                'height' => true,
                'viewbox' => true, // <= Must be lower case!
            ],
            'g'     => [ 'fill' => true ],
            'title' => [ 'title' => true ],
            'path'  => [ 'd' => true, 'fill' => true, ],
        ];

        $allowed_tags = array_merge( $kses_defaults, $svg_args );

        return wp_kses( $svg_icon, $allowed_tags );

    }

	/**
	 * Return list of Custom Post Types.
	 *
	 * @param array $cpt - Array with posts types to exclude.
	 *
	 * @since 1.0.0
	 * @access private	 *
	 * @return array
	 **/
	public function get_cpt( array $cpt ) {

		$defaults = [
			'exclude' => [],
		];

		$cpt = array_merge( $defaults, $cpt );

		$post_types_objects = get_post_types( [
			'public' => true,
		], 'objects'
		);

		/**
		 * Filters the list of post type objects used by Liker.
		 * @param array $post_types_objects List of post type objects.
		 **/
		$post_types_objects = apply_filters( 'ungrabber/post_type_objects', $post_types_objects );

		$cpt['options'] = [];

		foreach ( $post_types_objects as $cpt_slug => $post_type ) {

			if ( in_array( $cpt_slug, $cpt['exclude'], true ) ) {
				continue;
			}

			$cpt['options'][ $cpt_slug ] = $post_type->labels->name;

		}

		return $cpt['options'];

	}

	/**
	 * Main Helper Instance.
	 * Insures that only one instance of Helper exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
     * @return Helper
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

} // End Class Helper.
