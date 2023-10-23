<?php

function serverValidateMerchant( $validationURL ) {
	$data = array(
		'merchantIdentifier' => 'merchant.com.joseconti.devredsys',
		'domainName'         => 'devredsys.joseconti.com',
		'displayName'        => 'Jose Conti',
	);

	$data_string = json_encode( $data );

	$ch = curl_init( $validationURL );
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt(
		$ch,
		CURLOPT_HTTPHEADER,
		array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen( $data_string ),
		)
	);

	// Agrega la ruta a tus certificados
	curl_setopt( $ch, CURLOPT_SSLCERT, 'merchant_id.pem' );
	curl_setopt( $ch, CURLOPT_SSLKEY, 'merchant_id.key' );

	$result = curl_exec( $ch );

	if ( curl_errno( $ch ) ) {
		// Si hay un error, devuelve un JSON con el mensaje de error
		return json_encode( array( 'error' => curl_error( $ch ) ) );
	}

	curl_close( $ch );

	return $result; // Esto debería ser ya un JSON válido
}

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	$validationURL = $_POST['validationURL'];
	echo serverValidateMerchant( $validationURL );
}
