<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

function pafe_import_widget( $files ) {
	$file_name = $files["widget_file"]["name"];
    $source = $files["widget_file"]["tmp_name"];
    $array = explode(".", $file_name);
	$name = $array[0];  
	$ext = isset($array[1]) ? $array[1] : '' ;
	$upload_dir = wp_upload_dir();
	$path = $upload_dir['basedir'] . '/piotnet-addons-for-elementor/widget-creator/';
	$location = $path . $file_name;

	if(move_uploaded_file($_FILES['widget_file']['tmp_name'], $location)) {
        $zip = new ZipArchive();
        if($zip->open($location)) {
			$zip->extractTo($path);
			$zip->close();
        }

        // import elementor json

        $json_file_content = file_get_contents( $path . $name . '.json' );
        $export_data = json_decode( $json_file_content, true );
		$elementor = \Elementor\Plugin::$instance;

		$my_post = array(
			'post_title'    => $export_data['title'],
			'post_status'   => 'publish',
			'post_type'		=> 'pafe-widget',
		);

		$post_id = wp_insert_post( $my_post );

		$document = $elementor->documents->get( $post_id );

		if ( is_wp_error( $document ) ) {
			/**
			 * @var \WP_Error $document
			 */
			return $document;
		}

		if ( ! empty( $export_data['content'] ) ) {
			$export_data['content'] = pafe_replace_elements_ids( $export_data['content'] );
		}

		$document->save( [
			'elements' => $export_data['content'],
			'settings' => $export_data['page_settings'],
		] );

		$template_id = $document->get_main_id();

		$document->set_is_built_with_elementor( '1' );

		unlink( $path . $name . '.json' );

        unlink($location);
    }
    if (! empty( $export_data['title'] )) {
        echo '<div class="pafe-dashboard__notice-import">Successfully Imported: ' . $export_data['title'] . '</div>';
    }
}

?>
<div class="pafe-dashboard pafe-dashboard--templates">
	<!-- <div class="pafe-dashboard__sidebar">
		<div class="pafe-editor__search active" data-pafe-dashboard-search>
			<input class="pafe-editor__search-input" data-pafe-dashboard-search-input type="text" placeholder="<?php echo __('Search Widget', 'pafe'); ?>">
			<div class="pafe-editor__search-icon">
				<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/e-search.svg'; ?>">
			</div>
		</div>
		<div class="pafe-dashboard__category">
			<?php
				$templates_categories = [
					//'all' => __('All Widgets', 'pafe'),
					'import' => __('Import', 'pafe'),
				];

				$tab = !empty($_GET['tab']) ? $_GET['tab'] : 'all';

				foreach ($templates_categories as $key => $templates_category) :
			?>
				<div class="pafe-dashboard__category-item<?php if($key == $tab) { echo ' active'; } ?>" data-pafe-dashboard-category='<?php echo $key;?>'><?php echo $templates_category; ?></div>
			<?php endforeach; ?>
		</div>
	</div> -->
	<div class="pafe-dashboard__content">
		<div class="pafe-dashboard__templates">
			<?php
				$templates = [
					[
						'title' => __('Blank Form', 'pafe'),
						'category' => 'customer-service',
						'blank' => true,
						'folder' => 'blank',
					],
					[
						'title' => __('Simple Contact Form', 'pafe'),
						'category' => 'business-operations',
						'folder' => 'simple-contact-form',
						'demo' => 'https://pafe.com/',
					],
					[
						'title' => __('Simple Contact Form 2', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'simple-contact-form',
						'demo' => 'https://pafe.com/',
					],
					[
						'title' => __('Simple Contact Form 3', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'simple-contact-form',
						'demo' => 'https://pafe.com/',
					],
					[
						'title' => __('Simple Contact Form 4', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'simple-contact-form',
					],
					[
						'title' => __('Simple Contact Form 5', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'simple-contact-form',
					],
					[
						'title' => __('Simple Contact Form 2', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'simple-contact-form',
						'demo' => 'https://pafe.com/',
					],
					[
						'title' => __('Simple Contact Form 3', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'simple-contact-form',
						'demo' => 'https://pafe.com/',
					],
					[
						'title' => __('Simple Contact Form 4', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'simple-contact-form',
					],
					[
						'title' => __('Simple Contact Form 5', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'simple-contact-form',
					],
				];

				foreach ($templates as $template) :
			?>
				<!-- <div class="pafe-dashboard__template<?php if($tab == 'all' || $tab == $template['category']) { echo ' active'; } ?>" data-pafe-dashboard-item-category="<?php echo $template['category']; ?>" data-pafe-dashboard-item-title="<?php echo strtolower($template['title']); ?>">
					<div class="pafe-dashboard__template-image" style="background-image:src('<?php echo plugin_dir_url( __FILE__ ) . '../assets/forms/templates/' . $template['folder'] . '/image.jpg'; ?>');">
						<div class="pafe-dashboard__template-buttons">
							<button class="pafe-dashboard__template-button" data-pafe-add-new-form="<?php echo $template['folder']; ?>"><?php if(!empty($template['blank'])) { echo __('Create Blank Form', 'pafe'); } else { echo __('Use Template', 'pafe'); } ?></button>
							<?php if(empty($template['blank']) && !empty($template['demo'])) : ?>
								<a class="pafe-dashboard__template-button pafe-dashboard__template-button--secondary" href="<?php echo $template['demo']; ?>" target="_blank"><?php echo __('View Demo', 'pafe'); ?></a>
							<?php endif; ?>
						</div>
					</div>
					<div class="pafe-dashboard__template-content">
						<div class="pafe-dashboard__template-title"><?php echo $template['title']; ?></div>
					</div>
				</div> -->
			<?php endforeach; ?>
			<div data-pafe-dashboard-item-category="import" class="active">
				<form method="post" enctype="multipart/form-data" action="">
					<?php
					wp_nonce_field( 'import_action', 'import_nonce' );
					?>
					<div class="pafe-dashboard__import-form">
						<input type="file" id="widget_file" name="widget_file">
						<input type="hidden" name="action" value="import_widget_file">
						<?php submit_button( __( 'Import Now', 'pafe' ) ); ?>
					</div>
					<?php
						if ( isset( $_POST['action'] ) ) {
							$import_action = $_POST['action'];
							if ( 'import_widget_file' === $import_action && isset( $_POST['import_nonce'] ) && wp_verify_nonce( $_POST['import_nonce'], 'import_action' ) ) {
								pafe_import_widget( $_FILES );
							}
						}
					?>
				</form>
			</div>
		</div>
	</div>
</div>
