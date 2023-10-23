<?php
	if( !class_exists('UPTimelineApi') ){

		class UPTimelineApi{

			private static $_instance;

			public static function instance() {
				if ( is_null( self::$_instance ) ) {
					self::$_instance = new self();
				}
				return self::$_instance;

			}

			/*
			*	Gets the contents to be displayed on timeline
			* @Params array - Array containing details of timeline elements
			* @return string - HTML content of timeline element
			*/
			function get_timeline_content( $arr, $user_id = null ){

				if( !empty( $arr ) ){

					ob_start();
					include UPTIMELINE_PATH.'templates/template-timeline-'.$arr['action'].'.php';
					$output = ob_get_contents();
					ob_end_clean();
					return $output;
					
				}
			}
		}
	}
?>
