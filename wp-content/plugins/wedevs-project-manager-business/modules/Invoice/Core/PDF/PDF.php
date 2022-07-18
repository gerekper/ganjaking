<?php
namespace WeDevs\PM_Pro\Modules\Invoice\Core\PDF;

use Mpdf\Mpdf;

class PDF {

	private static $_instance;

    public static function getInstance() {
        if ( !self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

	public static function generator( $html, $options = [] ) {
		return self::getInstance()->render( $html, $options );
	}

	public function render( $html, $options = [] ) {
		$default = [
			'default_font' => 'Helvetica',
			'orientation'  => 'P', // P or L
			'output'       => 'D', // I = inline, D = download, F = local file
		];

		$options = wp_parse_args( $options, $default );
		$mpdf = new Mpdf( $options );
		$mpdf->autoScriptToLang = true;
		$mpdf->autoLangToFont = true;
		$mpdf->shrink_tables_to_fit = 1;
		$mpdf->WriteHTML( $html );

		// Render the HTML as PDF
        $mpdf->output( '', $options['output'] );
        exit;
	}
}
