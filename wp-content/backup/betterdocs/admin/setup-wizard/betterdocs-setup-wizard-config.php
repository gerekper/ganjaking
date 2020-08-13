<?php
include plugin_dir_path( __FILE__ ) . 'inc/betterdocs-setup-wizard-helper.php';
include plugin_dir_path( __FILE__ ) . 'inc/class-betterdocs-setup-wizard.php';
// Getting Started
BetterDocsSetupWizard::setSection(array(
	'id'    	=> 'betterdocs_getting_started_settings',
	'title' 	=> __( 'Getting Started', 'betterdocs' ),
	'fields'	=> array(
		array(
			'id'      		=> 'getting_started',
            'title'   		=> __( 'Getting Started', 'betterdocs' ),
            'sub_title'   	=> __( 'Easily get started with this easy setup wizard and complete setting up your Knowledge Base.', 'betterdocs' ),
			'type'    		=> 'welcome',
			'video_url'     => 'https://www.youtube.com/embed/57BioKfROlo'
		),
	)
));

$existing_plugins = BetterDocsSetupWizard::knowledge_base_plugins();
if($existing_plugins){
	$variable = "";
// Migration
// $existing_plugins_data = BetterDocsSetupWizard::existing_plugins_data('echo-knowledge-base');
// var_dump($existing_plugins_data);
BetterDocsSetupWizard::setSection(array(
	'id'    	=> 'betterdocs_migration_settings',
	'title' 	=> __( 'Migration', 'betterdocs' ),
	'fields'	=> array(
		array(
			'id'      		=> 'migration_step',
            'sub_title'   	=> __( 'We detected another Knowledge Base Plugin installed in this site. For BetterDocs to work efficiently, we will migrate the data from the plugin listed below, and deactivate the plgugin, to avoid conflict.', 'betterdocs' ),
			'type'    		=> 'migration',
			'options'		=> [
				array(
					'id'      		=> $existing_plugins[0][0],
					'title'   		=> 'Migrate '.$existing_plugins[0][1],
					'type'    		=> 'checkbox',
					'default'    	=> 1,
				),
			]
		),
	)
));
}

// Setup Pages
BetterDocsSetupWizard::setSection(array(
	'id'    	=> 'betterdocs_setup_page_settings',
	'title' 	=> __( 'Setup Pages', 'betterdocs' ),
	'fields'	=> array(
		array(
			'id'      		=> 'builtin_doc_page',
            'title'   		=> __( 'Enable Built-in Documentation Page', 'betterdocs' ),
			'type'    		=> 'checkbox',
		),
		array(
			'id'      		=> 'docs_slug',
            'title'   		=> __( 'Page Slug', 'betterdocs' ),
			'type'    		=> 'text',
			'placeholder'   => 'Page Slug',
			'default'		=> 'docs'
		),
		array(
			'id'      		=> 'enable_disable',
            'title'   		=> __( 'Enable Instant Answer', 'betterdocs' ),
			'type'    		=> 'checkbox_pro_feature',
		)
	)
));

// Create Content
BetterDocsSetupWizard::setSection(array(
	'id'    	=> 'betterdocs_create_content_settings',
	'title' 	=> __( 'Create Content', 'betterdocs' ),
	'fields'	=> array(
		array(
			'id'      		=> 'content_step',
            'title'   		=> __( 'Create Documentation Content', 'betterdocs' ),
            'sub_title'   	=> __( 'Let\'s create some categories and articles. And then assign the articles to proper categories.', 'betterdocs' ),
			'type'    		=> 'link',
			'image_url'     => BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-setup-articles.png',
			'options'		=> [
				array(
					'title' => 'Create Categories',
					'url'	=> admin_url('edit-tags.php?taxonomy=doc_category&post_type=docs'),
					'feature_title' => 'Create Categories',
					'feature_content' => 'You can create Categories from <strong>BetterDocs &gt; Categories</strong>'
				),
				array(
					'title' => 'Create Articles',
					'url'	=> admin_url('post-new.php?post_type=docs'),
					'feature_title' => 'Create Articles',
					'feature_content' => 'You can create Articles from <strong>BetterDocs &gt; Add New</strong>'
				)
			]
		),
	)
));

// Customize
BetterDocsSetupWizard::setSection(array(
	'id'    	=> 'betterdocs_customize_settings',
	'title' 	=> __( 'Customize', 'betterdocs' ),
	'fields'	=> array(
		array(
			'id'      		=> 'customize_step',
            'title'   		=> __( 'Customize Everything', 'betterdocs' ),
            'sub_title'   	=> __( 'Take control of your settings and customize your documentation page, articles and archive pages live, with the power of Customizer', 'betterdocs' ),
			'type'    		=> 'link',
			'image_url'     => BETTERDOCS_ADMIN_URL . 'assets/img/setup-betterdocs-customizer.png',
			'options'		=> [
				array(
					'id'	=> 'bdgotocustomize',
					'title' => 'Go To Customizer',
					'url'	=>  betterdocs_setup_get_customizer_setting_url(),
					'feature_title' => 'Easy To Customize',
					'feature_content' => 'Customize Docs page, Articles, Archive page Live'
				),
				array(
					'title' => 'Go To Settings',
					'url'	=> betterdocs_get_admin_settings_url(),
					'feature_title' => 'Extensive Options Panel',
					'feature_content' => 'Take control of your pages with extensive settings options'
				)
			]
		),
	)
));

// Finalize
BetterDocsSetupWizard::setSection(array(
	'id'    	=> 'betterdocs_finalize_settings',
	'title' 	=> __( 'Finalize', 'betterdocs' ),
	'fields'	=> array(
		array(
			'id'      		=> 'finnilize_step',
            'title'   		=> __( 'Great Job!', 'betterdocs' ),
            'sub_title'   	=> __( 'Your documentation page is ready! Make sure to add more articles and assign them to proper categories and you are good to go.', 'betterdocs' ),
			'type'    		=> 'final_step',
			'image_url'     => BETTERDOCS_ADMIN_URL . 'assets/img/setup-finalize.svg',
			'options'		=> [
				array(
					'id'	=> 'bdgotodocspage',
					'title' => 'Visit Your Documentation Page',
					'url'	=> betterdocs_setup_docs_page_url()
				),
			]
		),
	)
));