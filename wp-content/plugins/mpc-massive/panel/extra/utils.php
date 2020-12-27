<?php
/*----------------------------------------------------------------------------*\
	UTILS
\*----------------------------------------------------------------------------*/

if ( !class_exists( 'MPC_Utils' ) ) {
	class MPC_Utils {
		/* Add .httaccess rules */
		static function add_htaccess_rules() {
			if ( ! is_admin() ) {
				return;
			}

			$htaccess_file = ABSPATH . '.htaccess';

			if ( ! file_exists( $htaccess_file ) ) {
				return;
            }

            $server_soft =  $_SERVER[ 'SERVER_SOFTWARE' ];

            if ( $server_soft === 'Apache' ) {
                // No Apache version info
                return;
            }

            preg_match( '/(Apache)([\s,\/]*?)(\d\.\d\.\d+)/', $server_soft, $matches );

            if ( isset( $matches[ 3 ] ) && version_compare(  $matches[ 3 ], '2.4.11', '<' ) ) {
                // Apache version lower then 2.4.11
                // without support for SubstituteMaxLineLength rule
                return;
            }

			$rules = array(
				'<ifModule mod_substitute.c>',
				'SubstituteMaxLineLength 10M',
                '</ifModule>'
            );

            require_once( ABSPATH . 'wp-admin/includes/misc.php' );

            if ( function_exists( 'insert_with_markers' ) ) {
                insert_with_markers( $htaccess_file, 'Massive Addons by MPC', $rules );
            }
        }

        /* log_it */
        static function log_it( $message ) {
            if ( WP_DEBUG === true ) {
                if ( is_array( $message ) || is_object( $message ) ) {
                    error_log( print_r( $message, true ) );
                } else {
                    error_log( $message );
                }
            }
        }
	}
}