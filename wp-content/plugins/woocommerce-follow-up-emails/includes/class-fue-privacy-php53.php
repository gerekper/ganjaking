<?php
$exportfn = function( $email_address, $page ) use ( $db_table, $db_data ) {
	return FUE_Privacy::handle_db_export( $db_table, $db_data, $email_address, $page );
};

$erasefn  = function( $email_address, $page ) use ( $db_table, $db_data ) {
	return FUE_Privacy::handle_db_erase( $db_table, $db_data, $email_address, $page );
};
