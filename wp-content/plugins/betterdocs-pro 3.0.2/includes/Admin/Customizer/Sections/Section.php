<?php

namespace WPDeveloper\BetterDocsPro\Admin\Customizer\Sections;

use WPDeveloper\BetterDocs\Core\Settings;
use WPDeveloper\BetterDocsPro\Utils\Enqueue;
use WPDeveloper\BetterDocs\Admin\Customizer\Sanitizer;
use WPDeveloper\BetterDocs\Admin\Customizer\Sections\Section as FreeSection;

abstract class Section extends FreeSection {
    /**
     * Enqueue Manager
     *
     * @var Enqueue
     */
    protected $pro_assets;

    public function __construct( Sanitizer $sanitizer, Settings $settings ) {
        parent::__construct( $sanitizer, $settings );
        $this->pro_assets = betterdocs_pro()->assets;
    }
}
