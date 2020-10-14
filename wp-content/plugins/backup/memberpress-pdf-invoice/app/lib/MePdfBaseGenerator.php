<?php
abstract class MePdfBaseGenerator {

  protected $paper_size;
  protected $paper_orientation;

  public function get_filename( $txn ) {
    return $txn->trans_num . '_invoice.pdf';
  }

  public function fetch_content( $invoice, $txn ) {
    $html = MePdfInvoicesCtrl::get_html_content( $invoice );
    return $html;
  }

  abstract protected function set_defaults();
  abstract protected function render( stdClass $invoice, $txn);
}
