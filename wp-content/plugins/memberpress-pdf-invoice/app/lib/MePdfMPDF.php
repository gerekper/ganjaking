<?php
class MePdfMPDF extends MePdfBaseGenerator {

  protected $config = array();
  protected $mpdf;

  public function __construct() {
    $this->set_defaults();
    $this->mpdf = new \Mpdf\Mpdf( $this->config );
    $this->mpdf->setLogger(new MePdfLogger());
  }

  public function set_defaults() {
    $mepr_options            = MeprOptions::fetch();
    $this->paper_size        = $mepr_options->attr( 'biz_invoice_paper_size' );
    $this->paper_orientation = $mepr_options->attr( 'biz_invoice_paper_orientation' );

    $this->config = array(
      'mode'     => $mepr_options->attr( 'biz_invoice_font' ) == 'core' ? 'c' : '+aCJK',
      'tempDir'  => $this->get_tempdir(),
      'fontdata' => $this->get_fonts(),
      'fontDir'  => $this->font_path(),
      'format'   => $this->paper_size . '-' . $this->paper_orientation,
      'autoScriptToLang'  => true,
      'autoLangToFont'    => true,
    );
  }

  public function render( stdClass $invoice, $txn ) {
    // Output HTML to browser, maybe to debug
    // MeprView::render( '/account/invoice/'.$invoice->template, get_defined_vars() );
    // exit();

    // Clean the output buffer so we don't end up with corrupted files
    if(ob_get_length()) {
      ob_clean();
    }

    $html = $this->fetch_content( $invoice, $txn );
    $this->mpdf->WriteHTML( $html );
    $this->mpdf->Output( $this->get_filename( $txn ), 'D' );
    $this->mpdf->cleanup();
  }

  /**
   * Saves PDF in tmp directory and returns file path
   *
   * @param  mixed $invoice
   * @param  mixed $txn
   * @return string
   */
  public function save( stdClass $invoice, $txn ) {

    // Clean the output buffer so we don't end up with corrupted files
    $html = $this->fetch_content( $invoice, $txn );

    try {
      $this->mpdf->WriteHTML($html);
      $filename = $this->get_tempdir() . $this->get_filename( $txn );
      $this->mpdf->Output( $filename, 'F');
      return $filename;
    } catch (\Mpdf\MpdfException $e) {
      return false;
    }
  }

  /**
   * get_fonts
   *
   * @return array
   */
  public function get_fonts() {
    return MeprHooks::apply_filters(
      'mepr-pdf-invoice-fonts',
      array(
        'dejavusans' => array(
          'R'          => 'DejaVuSans.ttf',
          'B'          => 'DejaVuSans-Bold.ttf',
          'I'          => 'DejaVuSans-Oblique.ttf',
          'BI'         => 'DejaVuSans-BoldOblique.ttf',
          'useOTL'     => 0xFF,
          'useKashida' => 75,
        ),
      )
    );
  }

  /**
   * font_path
   *
   * @return array
   */
  public function font_path() {
    $paths = array();

    $template_path   = get_template_directory();
    $stylesheet_path = get_stylesheet_directory();

    // Put child theme's first if one's being used
    if ( $stylesheet_path !== $template_path ) {
      $paths[] = "{$stylesheet_path}/fonts";
    }

    $paths[] = "{$template_path}/fonts";
    $paths[] = MPDFINVOICE_PATH . 'fonts';

    return $paths;
  }

  public function get_tempdir() {
    $upload     = wp_upload_dir();
    $upload_dir = $upload['basedir'];
    $upload_dir = $upload_dir . '/mepr/mpdf/';

    if ( is_dir( $upload_dir ) ) {
      return $upload_dir;
    }
    // Default
    return MPDFINVOICE_PATH . 'vendor/mpdf/mpdf/tmp';
  }

}
