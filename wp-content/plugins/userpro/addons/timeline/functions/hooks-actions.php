<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

if ( ! class_exists( 'UPTimelineHooksActions' ) ) {

	class UPTimelineHooksActions {
		private static $_instance;

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		function __construct() {
			if( userpro_timeline_get_option('enable_timeline') ){
				add_action( 'userpro_after_fields', array( $this, 'after_fields' ) );
			}
		}

		function after_fields( $args ) {
			if( $args['template'] == 'view' ){
			?>
			<div class='userpro-section userpro-column userpro-collapsible-1 userpro-collapsed-0 uptimeline-section'><?php _e('Timeline','userpro-media');?></div>
			<?php
			include_once UPTIMELINE_PATH.'templates/template-timeline.php';
		}
		}
	}

	UPTimelineHooksActions::instance();
}

?>
