<?php

/**
 * Database Table Check
 */
function yith_ywqa_update_db_1_0_2() {

	$ywqa_db_option = get_option( 'yith_ywqa_db_version', '1.0.0' );

	if ( $ywqa_db_option && version_compare( $ywqa_db_option, '1.0.2', '<' ) ) {

        if ( get_option( "ywqa_enable_search" ) ) {
            delete_option( "ywqa_enable_search");
        }

		update_option( 'yith_ywqa_db_version', '1.0.2' );
	}
}
add_action( 'admin_init', 'yith_ywqa_update_db_1_0_2' );


