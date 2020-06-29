<?php
namespace WeDevs\PM_Pro\Modules\invoice\core\PDF;

use Dompdf\Dompdf;
use Dompdf\Options;

class PDF {

	private static $_instance;

    public static function getInstance() {
        if ( !self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

	public static function generator( $html, $options = [] ) {
		self::getInstance()->load_required_files();
		
		return self::getInstance()->render( $html, $options );
	}

	public function load_required_files() {
		
		require_once PM_PRO_INVOICE_PATH . '/includes/dompdf/vendor/autoload.php';
		require_once PM_PRO_INVOICE_PATH . '/includes/dompdf/autoload.inc.php';
	}

	public function render( $html, $options = [] ) {
		$default = [
			'stream'      => true,
			'defaultFont' => 'Helvetica'
		];

		$options = wp_parse_args( $options, $default );

		// instantiate and use the dompdf class
		$dompdf = new Dompdf();
		$dompdf->loadHtml( $html );

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper( 'A4', 'portrait');

		foreach ( $options as $key => $value) {
			$dompdf->set_option( $key, $value );
		}

		// Render the HTML as PDF
		$dompdf->render();

		if ( $options['stream'] === true ) {
			// Output the generated PDF to Browser
			$dompdf->stream();
		} else {
			// Output as base64
			return $dompdf->output();
		}
		
	}
}