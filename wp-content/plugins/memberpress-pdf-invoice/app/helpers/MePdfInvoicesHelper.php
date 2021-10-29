<?php
if ( ! defined( 'ABSPATH' ) ) {
  die( 'You are not allowed to call this page directly.' );}

class MePdfInvoicesHelper {

  /**
   * Uses dynamic attributes for admin options
   *
   * @param  mixed $attrs
   *
   * @return void
   */
  public static function get_dynamic_attrs() {
    $attrs = array(
      'biz_invoice_template'          => array(
        'default'     => 'simple',
        'validations' => array(),
      ),
      'biz_invoice_paper_size'        => array(
        'default'     => 'Letter',
        'validations' => array(),
      ),
      'biz_invoice_paper_orientation' => array(
        'default'     => 'P',
        'validations' => array(),
      ),
      'biz_invoice_font'              => array(
        'default'     => 'dejavusans',
        'validations' => array(),
      ),
      'biz_logo'                      => array(
        'default'     => '',
        'validations' => array(),
      ),
      'biz_email'                     => array(
        'default'     => '',
        'validations' => array(),
      ),
      'biz_phone'                     => array(
        'default'     => '',
        'validations' => array(),
      ),
      'biz_address_format'            => array(
        'default'     => '<h3>{$biz_name}</h3><br/>{$biz_address1}<br/>{$biz_address2}<br/>{$biz_city}, {$biz_state}<br/>{$biz_postcode} {$biz_country}',
        'validations' => array(),
      ),

      'biz_cus_address_format'        => array(
        'default'     => '{$user_full_name}<br/>{$user_address_single}<br/>{$user_email}',
        'validations' => array(),
      ),
      'biz_invoice_notes'             => array(
        'default'     => '<h3>NOTES:</h3><br/>Thank you for choosing {$biz_name}',
        'validations' => array(),
      ),
      'biz_invoice_footnotes'         => array(
        'default'     => 'Invoice was created on a computer and is valid without the signature and seal.<br/>{$site_domain}',
        'validations' => array(),
      ),
      'biz_invoice_no'                => array(
        'default'     => '{$invoice_num}',
        'validations' => array(),
      ),
      'biz_invoice_format'            => array(
        'default'     => '{$invoice_num}',
        'validations' => array(),
      ),
      'inv_starting_number'                => array(
        'default'     => '',
        'validations' => array(),
      ),
      'biz_invoice_color'                => array(
        'default'     => '#3993d1',
        'validations' => array(),
      ),
    );
    return $attrs;
  }

  /**
   * Invoice variables used
   *
   * @return array
   */
  public static function get_invoice_vars() {
    $vars = array_merge(
      MeprTransactionsHelper::get_email_vars(),
      array(
        'invoice_num',
        'user_address_single',
        'biz_phone',
        'biz_email',
        'site_domain',
      )
    );
    sort( $vars );
    return apply_filters( 'mepr_pdf_invoice_vars', $vars );
  }

  /**
   * Replaces variables with values from object
   *
   * @param  object $txn
   *
   * @return array
   */
  public static function get_invoice_params( $txn ) {
    return MeprTransactionsHelper::get_email_params( $txn );
  }

  /**
   * Replaces variables with values from object
   *
   * @param  object $txn
   *
   * @return array
   */
  public static function update_invoice_params( $txn ) {
    return MeprTransactionsHelper::get_email_params( $txn );

    return $params;
  }

  public static function get_name( $usr ) {
    $name = trim( $usr->full_name() );
    if ( ( $company_name = get_user_meta( $usr->ID, 'mepr_company', true ) ) ||
     ( $company_name = get_user_meta( $usr->ID, 'company', true ) ) ||
     ( $company_name = get_user_meta( $usr->ID, 'mepr-company', true ) ) ) {
      $name = $company_name;
    }

    return empty( $name ) ? $usr->user_email : $name;
  }

  /**
   * Returns acceptable paper sizes for MPDF
   *
   * @return mixed
   */
  public static function get_paper_sizes() {
    return array(
      'A0'        => esc_html__( 'A0 (841 x 1189mm)', 'memberpress-pdf-invoice' ),
      'A1'        => esc_html__( 'A1 (594 x 841mm)', 'memberpress-pdf-invoice' ),
      'A2'        => esc_html__( 'A2 (420 x 594mm)', 'memberpress-pdf-invoice' ),
      'A3'        => esc_html__( 'A3 (297 x 420mm)', 'memberpress-pdf-invoice' ),
      'A4'        => esc_html__( 'A4 (210 x 297mm)', 'memberpress-pdf-invoice' ),
      'A5'        => esc_html__( 'A5 (148 x 210mm)', 'memberpress-pdf-invoice' ),
      'A6'        => esc_html__( 'A6 (105 x 148mm)', 'memberpress-pdf-invoice' ),
      'A7'        => esc_html__( 'A7 (74 x 105mm)', 'memberpress-pdf-invoice' ),
      'A8'        => esc_html__( 'A8 (52 x 74mm)', 'memberpress-pdf-invoice' ),
      'A9'        => esc_html__( 'A9 (37 x 52mm)', 'memberpress-pdf-invoice' ),
      'A10'       => esc_html__( 'A10 (26 x 37mm)', 'memberpress-pdf-invoice' ),
      'B0'        => esc_html__( 'B0 (1414 x 1000mm)', 'memberpress-pdf-invoice' ),
      'B1'        => esc_html__( 'B1 (1000 x 707mm)', 'memberpress-pdf-invoice' ),
      'B2'        => esc_html__( 'B2 (707 x 500mm)', 'memberpress-pdf-invoice' ),
      'B3'        => esc_html__( 'B3 (500 x 353mm)', 'memberpress-pdf-invoice' ),
      'B4'        => esc_html__( 'B4 (353 x 250mm)', 'memberpress-pdf-invoice' ),
      'B5'        => esc_html__( 'B5 (250 x 176mm)', 'memberpress-pdf-invoice' ),
      'B6'        => esc_html__( 'B6 (176 x 125mm)', 'memberpress-pdf-invoice' ),
      'B7'        => esc_html__( 'B7 (125 x 88mm)', 'memberpress-pdf-invoice' ),
      'B8'        => esc_html__( 'B8 (88 x 62mm)', 'memberpress-pdf-invoice' ),
      'B9'        => esc_html__( 'B9 (62 x 44mm)', 'memberpress-pdf-invoice' ),
      'B10'       => esc_html__( 'B10 (44 x 31mm)', 'memberpress-pdf-invoice' ),
      'C0'        => esc_html__( 'C0 (1297 x 917mm)', 'memberpress-pdf-invoice' ),
      'C1'        => esc_html__( 'C1 (917 x 648mm)', 'memberpress-pdf-invoice' ),
      'C2'        => esc_html__( 'C2 (648 x 458mm)', 'memberpress-pdf-invoice' ),
      'C3'        => esc_html__( 'C3 (458 x 324mm)', 'memberpress-pdf-invoice' ),
      'C4'        => esc_html__( 'C4 (324 x 229mm)', 'memberpress-pdf-invoice' ),
      'C5'        => esc_html__( 'C5 (229 x 162mm)', 'memberpress-pdf-invoice' ),
      'C6'        => esc_html__( 'C6 (162 x 114mm)', 'memberpress-pdf-invoice' ),
      'C7'        => esc_html__( 'C7 (114 x 81mm)', 'memberpress-pdf-invoice' ),
      'C8'        => esc_html__( 'C8 (81 x 57mm)', 'memberpress-pdf-invoice' ),
      'C9'        => esc_html__( 'C9 (57 x 40mm)', 'memberpress-pdf-invoice' ),
      'C10'       => esc_html__( 'C10 (40 x 28mm)', 'memberpress-pdf-invoice' ),
      'RA0'       => esc_html__( 'RA0 (860 x 1220mm)', 'memberpress-pdf-invoice' ),
      'RA1'       => esc_html__( 'RA1 (610 x 860mm)', 'memberpress-pdf-invoice' ),
      'RA2'       => esc_html__( 'RA2 (430 x 610mm)', 'memberpress-pdf-invoice' ),
      'RA3'       => esc_html__( 'RA3 (305 x 430mm)', 'memberpress-pdf-invoice' ),
      'RA4'       => esc_html__( 'RA4 (215 x 305mm)', 'memberpress-pdf-invoice' ),
      'SRA0'      => esc_html__( 'SRA0 (900 x 1280mm)', 'memberpress-pdf-invoice' ),
      'SRA1'      => esc_html__( 'SRA1 (640 x 900mm)', 'memberpress-pdf-invoice' ),
      'SRA2'      => esc_html__( 'SRA2 (450 x 640mm)', 'memberpress-pdf-invoice' ),
      'SRA3'      => esc_html__( 'SRA3 (320 x 450mm)', 'memberpress-pdf-invoice' ),
      'SRA4'      => esc_html__( 'SRA4 (225 x 320mm)', 'memberpress-pdf-invoice' ),
      'Letter'    => esc_html__( 'Letter (216 x 279mm)', 'memberpress-pdf-invoice' ),
      'Legal'     => esc_html__( 'Legal (216 x 356mm)', 'memberpress-pdf-invoice' ),
      'Executive' => esc_html__( 'Executive (184 x 267mm)', 'memberpress-pdf-invoice' ),
      'Folio'     => esc_html__( 'Folio (210 x 330mm)', 'memberpress-pdf-invoice' ),
      'Ledger'    => esc_html__( 'Ledger (279 x 432mm)', 'memberpress-pdf-invoice' ),
      'Tabloid'   => esc_html__( 'Tabloid (279 x 432mm)', 'memberpress-pdf-invoice' ),
    );
  }

  /**
   * get_paper_orientation
   *
   * @return void
   */
  public static function get_paper_orientation() {
    return array(
      'P' => 'Portrait',
      'L' => 'Landscape',
    );
  }

  /**
   * get_fonts
   *
   * @return void
   */
  public static function get_fonts() {
    return apply_filters(
      'mepr_pdf_invoice_fonts',
      array(
        'core'       => esc_html__( 'System Fonts', 'memberpress-pdf-invoice' ),
        'dejavusans' => 'DejaVu Sans',
      )
    );
  }

  /**
   * Returns available PDF invoice templates.
   *
   * @return string
   */
  public static function get_templates() {
    $templates = array(
      'simple' => 'Simple',
      'modern' => 'Modern',
    );
    return MeprHooks::apply_filters( 'mepr-pdf-invoice-templates', $templates );
  }

  /**
   * Render a slim WP Editor
   *
   * @param  mixed $content
   * @param  mixed $editor_id
   * @param  mixed $textarea_name
   *
   * @return mixed
   */
  public static function render_editor( $content, $editor_id, $textarea_name ) {
    ob_start();

    $settings = array(
      'textarea_name' => $textarea_name,
      'quicktags'     => false,
      'media_buttons' => false,
      'editor_class'  => 'regular-text',
      'teeny'         => true,
      'textarea_rows' => 10,  // Has no visible effect if editor_height is set, default is 20
      'tinymce'       => array(
        'toolbar1' => 'formatselect,bold,underline,bullist,numlist,link,forecolor',
      ),
    );

    wp_editor( $content, $editor_id, $settings );

    // Store the contents of the buffer in a variable
    $editor_contents = ob_get_clean();

    // Return the content you want to the calling function
    return $editor_contents;
  }

  /**
   * Returns full country name
   *
   * @param  mixed $country_code
   *
   * @return string
   */
  public static function get_formatted_country( $country_code ) {
    if ( MeprHooks::apply_filters( 'mepr_pdf_invoice_full_biz_country', true ) ) {
      $countries    = MeprUtils::countries();
      $country_code = trim( preg_replace( '/\s*\([^)]*\)/', '', $countries[ $country_code ] ) ); // Removes brackets
    }

    return $country_code;
  }

  /* Convert hexdec color string to rgb(a) string */
  public static function hex2rgba($color, $opacity = false) {
    $default = 'rgb(0,0,0)';

    //Return default if no color provided
    if(empty($color))
      return $default;

    //Sanitize $color if "#" is provided
    if ($color[0] == '#' ) {
      $color = substr( $color, 1 );
    }

    //Check if color has 6 or 3 characters and get values
    if (strlen($color) == 6) {
      $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
    } elseif ( strlen( $color ) == 3 ) {
      $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
    } else {
      return $default;
    }

    //Convert hexadec to rgb
    $rgb =  array_map('hexdec', $hex);

    //Check if opacity is set(rgba or rgb)
    if($opacity){
      if(abs($opacity) > 1)
        $opacity = 1.0;
      $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
    } else {
      $output = 'rgb('.implode(",",$rgb).')';
    }

    //Return rgb(a) color string
    return $output;
  }

}
