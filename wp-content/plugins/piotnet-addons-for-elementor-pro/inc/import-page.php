<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

function pafe_import( $file_content ) {
	$export_data = json_decode( $file_content, true );
	$elementor = \Elementor\Plugin::$instance;

	$my_post = array(
		'post_title'    => $export_data['title'],
		'post_status'   => 'publish',
		'post_type'		=> 'pafe-forms',
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

	$post_url = admin_url() . 'post.php?post=' . $post_id . '&action=elementor';

	echo '<div class="pafe-dashboard__notice-import">Successfully Imported: ' . '<a href="' . $post_url . '">' . $export_data['title'] . '</a></div>';
}

?>
<div class="pafe-dashboard pafe-dashboard--templates">
	<div class="pafe-dashboard__form-name">
		<div class="pafe-dashboard__form-name-title">Name Your Form</div>
		<div class="pafe-dashboard__form-name-input">
			<input type="text" name="form_name" placeholder="Enter your form name" data-pafe-add-new-form-name>
		</div>
	</div>
	<div class="pafe-dashboard__sidebar">
		<div class="pafe-editor__search active" data-pafe-dashboard-search>
			<input class="pafe-editor__search-input" data-pafe-dashboard-search-input type="text" placeholder="<?php echo __('Search Templates', 'pafe'); ?>">
			<div class="pafe-editor__search-icon">
				<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/icons/e-search.svg'; ?>">
			</div>
		</div>
		<div class="pafe-dashboard__category">
			<?php
				$templates_categories = [
					'all' => __('All Templates', 'pafe'),
					'import' => __('Import', 'pafe'),
				];

				$tab = !empty($_GET['tab']) ? $_GET['tab'] : 'all';

				foreach ($templates_categories as $key => $templates_category) :
			?>
				<div class="pafe-dashboard__category-item<?php if($key == $tab) { echo ' active'; } ?>" data-pafe-dashboard-category='<?php echo $key;?>'><?php echo $templates_category; ?></div>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="pafe-dashboard__content">
		<div class="pafe-dashboard__title"><?php echo __('Select a Template', 'pafe'); ?></div>
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
					],
					[
						'title' => __('Simple Multi Step Form', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'simple-multi-step-form',
						'demo' => 'https://pafe.piotnet.com/pafe-forms/simple-multi-step-form/',
					],
					[
						'title' => __('Calculation Form', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'calculation-form',
					],
					[
						'title' => __('Loan Calculator Form', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'loan-calculator-form',
					],
					[
						'title' => __('Conditional Logic Form', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'conditional-logic-form',
					],
					[
						'title' => __('Frontend Post Submissions Form', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'frontend-post-submissions-form',
					],
					[
						'title' => __('Repeater Fields', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'repeater-fields',
					],
					[
						'title' => __('Label Animation', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'label-animation',
					],
					[
						'title' => __('Inline Fields', 'pafe'),
						'category' => 'customer-service',
						'folder' => 'inline-fields',
					],
				];

				foreach ($templates as $template) :
			?>
				<div class="pafe-dashboard__template<?php if($tab == 'all' || $tab == $template['category']) { echo ' active'; } ?>" data-pafe-dashboard-item-category="<?php echo $template['category']; ?>" data-pafe-dashboard-item-title="<?php echo strtolower($template['title']); ?>">
					<div class="pafe-dashboard__template-image" style="background-image:url('<?php echo plugin_dir_url( __FILE__ ) . '../assets/forms/templates/' . $template['folder'] . '/image.jpg'; ?>');">
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
				</div>
			<?php endforeach; ?>
			<div data-pafe-dashboard-item-category="import"<?php if($tab == 'import') : ?> class="active"<?php endif; ?>>
				<form method="post" enctype="multipart/form-data" action="">
					<?php
					wp_nonce_field( 'import_action', 'import_nonce' );
					?>
					<div class="pafe-dashboard__import-form">
						<input type="file" id="json_file" name="json_file">
						<input type="hidden" name="action" value="import_json_file">
						<?php submit_button( __( 'Import Now', 'pafe' ) ); ?>
					</div>
					<?php
						if ( isset( $_POST['action'] ) ) {
							$import_action = $_POST['action'];
							if ( 'import_json_file' === $import_action && isset( $_POST['import_nonce'] ) && wp_verify_nonce( $_POST['import_nonce'], 'import_action' ) ) {
								$file_content = file_get_contents( $_FILES['json_file']['tmp_name'] );
								pafe_import( $file_content );
							}
						}
					?>
				</form>
			</div>
		</div>
	</div>
</div>
