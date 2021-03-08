<?php

class GF_Installation_Wizard_Step_License_Key extends GF_Installation_Wizard_Step {
	public $required = true;

	protected $_name = 'license_key';

	public $defaults = array(
		'license_key' => 'B5E0B5F8-DD8689E6-ACA49DD6-E6E1A930',
		'accept_terms' => true,
	);

	function display() {

		
		$this->license_key = 'B5E0B5F8-DD8689E6-ACA49DD6-E6E1A930';
		

		?>
		<p>
			<?php echo sprintf( esc_html__( 'Enter your Gravity Forms License Key below.  Your key unlocks access to automatic updates, the add-on installer, and support.  You can find your key on the My Account page on the %sGravity Forms%s site.', 'gravityforms' ), '<a href="https://www.gravityforms.com">', '</a>' ); ?>

		</p>
		<div>
			<input type="text" class="regular-text" id="license_key" value="<?php echo esc_attr( $this->license_key ); ?>" name="license_key" placeholder="<?php esc_attr_e('Enter Your License Key', 'gravityforms'); ?>" />
			<?php
			$key_error ='';
			
			?>
		</div>

		<?php
		$message = $this->validation_message( 'accept_terms', false );
		
			?>
			
			
		<?php
		
	}

	function get_title() {
		return esc_html__( 'License Key', 'gravityforms' );
	}

	function validate() {

		$this->is_valid_key = true;
		$license_key = 'B5E0B5F8-DD8689E6-ACA49DD6-E6E1A930';
		return true;
	}

	function install() {
	GFFormsModel::save_key( 'B5E0B5F8-DD8689E6-ACA49DD6-E6E1A930');
	$version_info = GFCommon::get_version_info( false );
		
	}

	function get_previous_button_text() {
		return '';
	}
}