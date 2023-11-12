<?php
namespace Elementor\PafeCustomControls;

use \Elementor\Base_Data_Control;

class Select_Files_Control extends Base_Data_Control {

	const Select_Files = 'pafe_custom_control_select_files';

	/**
	 * Set control type.
	 */
	public function get_type() {
		return self::Select_Files;
	}

	/**
	 * Enqueue control scripts and styles.
	 */
	public function enqueue() {

	}

	/**
	 * Set default settings
	 */
	protected function get_default_settings() {
		return [
			// 'options' => [],
			// 'multiple' => false,
			// 'get_fields' => false,
		];
	}
	
	/**
	 * control field markup
	 */
	public function content_template() {
		?>
		<div class="elementor-control-field">
			<# if ( data.label ) {#>
				<label for="<?php $this->print_control_uid(); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>
			<div class="elementor-control-input-wrapper elementor-control-unit-5">
				<?php
					$upload = wp_upload_dir();
					$upload_dir = $upload['basedir'];
					$dir = $upload_dir . '/piotnet-addons-for-elementor/widget-creator';
					$dir_tree = [];
				    $ffs = scandir($dir) ? scandir($dir) : [];

				    unset($ffs[array_search('.', $ffs, true)]);
				    unset($ffs[array_search('..', $ffs, true)]);

				    foreach($ffs as $ff){
				    	if (substr( $ff, 0, 1 ) !== ".") {
					    	$ffs_inside = scandir($dir.'/'.$ff) ? scandir($dir.'/'.$ff) : [];

					    	unset($ffs_inside[array_search('.', $ffs_inside, true)]);
					    	unset($ffs_inside[array_search('..', $ffs_inside, true)]);

					    	$dir_tree_item = [];

					    	foreach($ffs_inside as $ff_inside) {
					    		if (substr( $ff_inside, 0, 1 ) !== ".") {
						    		$dir_tree_item[] = $ff_inside;
						    	}
							}

					    	$dir_tree[$ff] = $dir_tree_item;
				    	}
				    }
				?>
				<ul class="pafe-widget-creator-assets-folder">
					<# var value = data.controlValue.split(','); console.log(value); #>
					<?php foreach ($dir_tree as $folder => $files) : ?>
						<li>
							<?php echo $folder; ?>
							<ul>
								<?php foreach ($files as $file) : ?>
									<li>
										<#
											var checked = ( -1 !== value.indexOf( '<?php echo $folder . '/' . $file; ?>' ) ) ? 'checked' : '';
										#>
										<label>
											<input type="checkbox" name="pafe_widget_creator_assets_files" {{ checked }} value="<?php echo $folder . '/' . $file; ?>"><?php echo $file; ?>
										</label>
									</li>
								<?php endforeach; ?>
							</ul>
						</li>
					<?php endforeach; ?>
				</ul>
				<input type="hidden" data-setting="{{ data.name }}" id="<?php $this->print_control_uid(); ?>" >
			</div>
		</div>
		<?php
	}
}