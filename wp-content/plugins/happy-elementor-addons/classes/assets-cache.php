<?php
namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

class Assets_Cache {

	const FILE_PREFIX = 'ha-';

	const BASE_DIR = 'happyaddons';

	const CSS_DIR = 'css';

	protected static $is_common_loaded = false;

	/**
	 * @var int
	 */
	protected $post_id = 0;

	/**
	 * @var Widgets_Cache
	 */
	protected $widgets_cache = null;

	protected $upload_path;

	protected $upload_url;

	public function __construct( $post_id = 0, Widgets_Cache $widget_cache_instance = null ) {
		$this->post_id = $post_id;
		$this->widgets_cache = $widget_cache_instance;

		$upload_dir = wp_upload_dir();
		$this->upload_path = trailingslashit( $upload_dir['basedir'] );
		$this->upload_url = trailingslashit( $upload_dir['baseurl'] );
		// Mixed content issue overcome when using ssl
		$this->upload_url = ( is_ssl()? str_replace( 'http://', 'https://', $this->upload_url ): $this->upload_url );
	}

	public function get_widgets_cache() {
		if ( is_null( $this->widgets_cache ) ) {
			$this->widgets_cache = new Widgets_Cache( $this->get_post_id() );
		}
		return $this->widgets_cache;
	}

	public function get_cache_dir_name() {
		return trailingslashit( self::BASE_DIR ) . trailingslashit( self::CSS_DIR );
	}

	public function get_post_id() {
		return $this->post_id;
	}

	public function get_cache_dir() {
		return wp_normalize_path( $this->upload_path . $this->get_cache_dir_name() );
	}

	public function get_cache_url() {
		return $this->upload_url . $this->get_cache_dir_name();
	}

	public function get_file_name() {
		return $this->get_cache_dir() . self::FILE_PREFIX . "{$this->get_post_id()}.css";
	}

	public function get_file_url() {
		return $this->get_cache_url() . self::FILE_PREFIX . "{$this->get_post_id()}.css";
	}

	public function cache_exists() {
		return file_exists( $this->get_file_name() );
	}

	public function has() {
		if ( ! $this->cache_exists() ) {
			$this->save();
		}
		return $this->cache_exists();
	}

	public function delete() {
		if ( $this->cache_exists() ) {
			unlink( $this->get_file_name() );
		}
	}

	public function delete_all() {
		$files = glob( $this->get_cache_dir() . '*' );
		foreach ( $files as $file ) {
			if ( is_file( $file ) ) {
				unlink( $file );
			}
		}
	}

	public function enqueue() {
		$this->enqueue_common();

		if ( $this->has() ) {
			wp_enqueue_style(
				'happy-elementor-addons-' . $this->get_post_id(),
				$this->get_file_url(),
				[ 'elementor-frontend' ],
				HAPPY_ADDONS_VERSION . '.' . get_post_modified_time()
			);
		}
	}

	/**
	 * Added common style as inline style
	 *
	 * Optimize css loading by extracting the common styles from all widgets
	 * and load as inline style.
	 *
	 * @since 2.14.3
	 *
	 * @return void
	 */
	public function enqueue_common() {
		if ( self::$is_common_loaded ) {
			return;
		}

		$widgets_map = Widgets_Manager::get_widgets_map();
		$base_widget = isset( $widgets_map[ Widgets_Manager::get_base_widget_key() ] ) ? $widgets_map[ Widgets_Manager::get_base_widget_key() ] : [];

		// Get common css styles
		if ( ! isset( $base_widget['css'] ) || ! is_array( $base_widget['css'] ) ) {
			return;
		}

		// TODO: Update style handler, now it's dependent on elementor-frontend
		wp_add_inline_style(
			'elementor-frontend',
			$this->get_css( $base_widget['css'] )
		);

		self::$is_common_loaded = true;
	}

	public function enqueue_libraries() {
		$widgets_map = Widgets_Manager::get_widgets_map();
		$base_widget = isset( $widgets_map[ Widgets_Manager::get_base_widget_key() ] ) ? $widgets_map[ Widgets_Manager::get_base_widget_key() ] : [];

		if ( isset( $base_widget['vendor'], $base_widget['vendor']['css'] ) && is_array( $base_widget['vendor']['css'] ) ) {
			foreach ( $base_widget['vendor']['css'] as $vendor_css_handle ) {
				wp_enqueue_style( $vendor_css_handle );
			}
		}

		if ( isset( $base_widget['vendor'], $base_widget['vendor']['js'] ) && is_array( $base_widget['vendor']['js'] ) ) {
			foreach ( $base_widget['vendor']['js'] as $vendor_js_handle ) {
				wp_enqueue_script( $vendor_js_handle );
			}
		}

		/**
		 * Return early if there's no widget cache
		 */
		$widgets = $this->get_widgets_cache()->get();

		if ( empty( $widgets ) || ! is_array( $widgets ) ) {
			return;
		}

		foreach ( $widgets as $widget_key ) {
			if ( ! isset( $widgets_map[ $widget_key ], $widgets_map[ $widget_key ]['vendor'] ) ) {
				continue;
			}

			$vendor = $widgets_map[ $widget_key ]['vendor'];

			if ( isset( $vendor['css'] ) && is_array( $vendor['css'] ) ) {
				foreach ( $vendor['css'] as $vendor_css_handle ) {
					wp_enqueue_style( $vendor_css_handle );
				}
			}

			if ( isset( $vendor['js'] ) && is_array( $vendor['js'] ) ) {
				foreach ( $vendor['js'] as $vendor_js_handle ) {
					wp_enqueue_script( $vendor_js_handle );
				}
			}
		}
	}

	public function save() {
		$widgets           = $this->get_widgets_cache()->get();
		$widgets_map       = Widgets_Manager::get_widgets_map();
		$widgets_processed = [];
		$css               = '';

		foreach ( $widgets as $widget_key ) {
			if ( isset( $processed[ $widget_key ] ) ||
				! isset( $widgets_map[ $widget_key ], $widgets_map[ $widget_key ]['css'] )
			) {
				continue;
			}

			$is_pro = ( isset( $widgets_map[ $widget_key ]['is_pro'] ) && $widgets_map[ $widget_key ]['is_pro'] );
			$css   .= $this->get_css( $widgets_map[ $widget_key ]['css'], $is_pro );

			$widgets_processed[ $widget_key ] = true;
		}

		if ( empty( $css ) ) {
			return;
		}

		$css .= sprintf( '/** Widgets: %s **/', implode( ', ', array_keys( $widgets_processed ) ) );

		if ( ! is_dir( $this->get_cache_dir() ) ) {
			wp_mkdir_p( $this->get_cache_dir() );
		}

		file_put_contents( $this->get_file_name(), $css );
	}

	protected function get_css( $files_name, $is_pro = false ) {
		$css = '';

		foreach ( $files_name as $file_name ) {
			$file_path = HAPPY_ADDONS_DIR_PATH . "assets/css/widgets/{$file_name}.min.css";
			$file_path = apply_filters( 'happyaddons_get_styles_file_path', $file_path, $file_name, $is_pro );

			if ( is_readable( $file_path ) ) {
				$css .= file_get_contents( $file_path );
			};
		}

		return $css;
	}
}
