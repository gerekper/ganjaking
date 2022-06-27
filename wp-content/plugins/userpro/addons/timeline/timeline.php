<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

if ( ! class_exists( 'UPTimelineMain' ) ) {

	class UPTimelineMain {

		private static $_instance;

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;

		}

		function __construct() {
			$this->define_constants();
			$this->include_files();

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script_styles' ) );

		}

		function define_constants() {
			define( 'UPTIMELINE_PATH', plugin_dir_path( __FILE__ ) );
			define( 'UPTIMELINE_URL', plugin_dir_url( __FILE__ ) );
		}

		function include_files() {
			if ( is_admin() ) {
				include_once UPTIMELINE_PATH . 'admin/admin.php';
			}
			include_once UPTIMELINE_PATH . 'functions/defaults.php';
			include_once UPTIMELINE_PATH . 'functions/api.php';
			include_once UPTIMELINE_PATH . 'functions/ajax.php';
			include_once UPTIMELINE_PATH . 'functions/hooks-filters.php';
			include_once UPTIMELINE_PATH . 'functions/hooks-actions.php';
		}

		function enqueue_script_styles(){

			wp_register_script( 'up_timeline_js', UPTIMELINE_URL.'assets/js/timeline.js','','',true );
			wp_enqueue_script( 'up_timeline_js' );

			wp_register_style( 'up_timeline_css', UPTIMELINE_URL.'assets/css/timeline.css' );
			wp_enqueue_style( 'up_timeline_css' );

		}
	}

	UPTimelineMain::instance();
}

?>
