<?php 

class GP_Terms_Of_Service extends GWPerk {

    public $version = GP_TERMS_OF_SERVICE_VERSION;
    protected $min_gravity_perks_version = '1.2.12';
    protected $min_gravity_forms_version = '1.9.18.2';

	private static $instance = null;

	public static function get_instance( $perk_file ) {
		if( null == self::$instance ) {
			self::$instance = new self( $perk_file );
		}
		return self::$instance;
	}

	public function __construct( $perk_file ) {

		parent::__construct( $perk_file );

		load_plugin_textdomain( 'gp-terms-of-service', false, basename( dirname( __file__ ) ) . '/languages/' );

	}

    public function init() {
        
        $this->add_tooltip($this->key('require_scroll'), __('<h6>Require Full Scroll</h6>Checking this option will disable the acceptance checkbox until the user has scrolled through the full Terms of Service.', 'gp-terms-of-service'));
        $this->add_tooltip($this->key('terms'), __('<h6>The Terms</h6>Specify the terms the user is agreeing to here.', 'gp-terms-of-services'));

	    require_once( 'includes/class-gf-field-terms-of-service.php' );
        
    }
    
}

class GWTermsOfService extends GP_Terms_Of_Service { };

function gp_terms_of_service() {
    return GP_Terms_Of_Service::get_instance( null );
}