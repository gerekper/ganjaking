<?php
/**
 * Add plugin update msg
 *
 */
function wi_plugin_update_check_msg( $links ) {

	if ( !is_admin() ) {
		return;
	}

	$html = "";

	$response = wp_remote_get( 'https://raw.githubusercontent.com/akshuvo/versions/master/wooinstant.json', array(
		'timeout' => 10,
		'headers' => array(
			'Accept' => 'application/json'
		) )
	);

	if ( is_array( $response ) && ! is_wp_error( $response ) ) {

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );
		$remote_version = $data->version;

		//! defined( 'ENVATO_MARKET_VERSION' )
		if( version_compare( WI_VERSION, $remote_version, '<' ) ){
			$html = '<tr class="plugin-update-tr active">
			    <td colspan="3" class="plugin-update colspanchange">
			        <div class="update-message notice inline notice-warning notice-alt">
			            <p>There is a new version of WooInstant available. Install/Activate <a href="https://envato.com/market-plugin/" target="_blank">Envato Market WordPress Plugin</a> to receive updates('.$remote_version.') automatically</p>
			        </div>
			    </td>
			</tr>';
		}
	}

	echo $html;

}
//add_filter( 'after_plugin_row_' . plugin_basename( __FILE__ ), 'wi_plugin_update_check_msg' );
add_filter( 'after_plugin_row_wooinstant/wooinstant.php', 'wi_plugin_update_check_msg' );