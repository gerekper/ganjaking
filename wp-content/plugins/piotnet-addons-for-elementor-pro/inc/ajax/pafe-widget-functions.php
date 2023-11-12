<?php
	if ( ! defined( 'ABSPATH' ) ) { exit; }

    add_action( 'wp_ajax_pafe_widget_functions', 'pafe_widget_functions' );
	add_action( 'wp_ajax_nopriv_pafe_widget_functions', 'pafe_widget_functions' );

	function pafe_widget_send_file_headers( $file_name ) {
		header('Content-Type: application/zip');
		header('Content-disposition: attachment; filename='.$file_name);
		header('Content-Length: ' . filesize($file_name));
		readfile($file_name);
	}

	function pafe_find_widget_creator_start($haystack, $needle, $widget_creator_pos) {
	    $offset = 0;
	    $allpos = array();
	    while (($pos = strpos($haystack, $needle, $offset)) !== FALSE) {
	        $offset   = $pos + 1;
	        if ($pos < $widget_creator_pos) {
	        	$allpos[] = $pos;
	        }
	    }
	    return $allpos[count($allpos) - 1];
	}

	function pafe_widget_functions() {
		$function = !empty($_GET['function']) ? $_GET['function'] : $_POST['function'];
		$template_id = !empty($_GET['id']) ? $_GET['id'] : '';
		$elementor_data = get_post_meta( $template_id, '_elementor_data', true);
		$widget_creator_pos = strpos($elementor_data, ',"widgetType":"pafe-widget-creator"');
		$files_url = [];

		if ($widget_creator_pos !== false) {
			$widget_start_pos = pafe_find_widget_creator_start($elementor_data, '"elType":"widget","settings":{"pafe_widget_creator_title"', $widget_creator_pos);
			$widget_creator_settings = json_decode( '{' . substr($elementor_data, $widget_start_pos, $widget_creator_pos - $widget_start_pos) . '}', true )['settings'];

			if (!empty($widget_creator_settings['pafe_widget_creator_assets'])) {
				$files_url = explode(',', $widget_creator_settings['pafe_widget_creator_assets']);
			}
		}

		$elementor = \Elementor\Plugin::$instance;

		if (!empty($template_id)) {
	        $document = $elementor->documents->get( $template_id );

			$template_data = $document->get_export_data();

			if ( empty( $template_data['content'] ) ) {
				return new \WP_Error( 'empty_template', 'The template is empty' );
			}

			if (!empty($widget_creator_settings['pafe_widget_creator_name'])) {
				$template_data_content = wp_json_encode($template_data['content']);
				$template_data_content = str_replace('"pafe_widget_creator_name":"' . $widget_creator_settings['pafe_widget_creator_name'] . '"', '"pafe_widget_creator_name":"' . $widget_creator_settings['pafe_widget_creator_name'] . '-copy"', $template_data_content);
			}
			
			$export_data = [
				'content' => json_decode($template_data_content, true),
				'page_settings' => $template_data['settings'],
				'version' => \Elementor\DB::DB_VERSION,
				'title' => $document->get_main_post()->post_title,
				'type' => 'pafe-widget',
				'status' => 'publish'
			];
		}
        
        switch ($function) {
        	case 'export':

        		$post = get_post($template_id);

				$file_data = [
					'name' => 'pafe-widget-' . $post->post_name . '-' . time() . '.json',
					'content' => wp_json_encode( $export_data ),
				];

				$zip = new ZipArchive();
				$zip_name = 'pafe-widget-' . $post->post_name . '-' . time() . ".zip";
				$zip->open($zip_name,  ZipArchive::CREATE);

				$zip->addFromString($file_data['name'],  $file_data['content']);

				$upload_dir = wp_upload_dir();

				foreach ($files_url as $file) {
					$path = $upload_dir['basedir'] . '/piotnet-addons-for-elementor/widget-creator/' . $file;
					if(file_exists($path)){
						$zip->addFromString($file, file_get_contents($path)); 
						$zip->deleteName( basename($file) );
					}
				}

				$zip->close();

				pafe_widget_send_file_headers( $zip_name );

				flush();

        		break;
        	
        	case 'duplicate':
				
				$my_post = array(
					'post_title'    => !empty($_POST['title']) ? $_POST['title'] : $export_data['title'] . ' (Copy)',
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

				if (!empty($template)) {
					echo admin_url( 'post.php?post=' . $post_id . '&action=elementor' );
				} else {
					echo '<script>window.location.href="' . admin_url( 'edit.php?post_type=pafe-widget' ) . '"</script>';
				}

        		break;
        }

		wp_die(); 
	}