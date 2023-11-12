<?php
	if ( ! defined( 'ABSPATH' ) ) { exit; }

    add_action( 'wp_ajax_pafe_forms_functions', 'pafe_forms_functions' );
	add_action( 'wp_ajax_nopriv_pafe_forms_functions', 'pafe_forms_functions' );

	function pafe_send_file_headers( $file_name, $file_size ) {
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=' . $file_name );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . $file_size );
	}

	function pafe_replace_elements_ids( $content ) {
		return \Elementor\Plugin::$instance->db->iterate_data( $content, function( $element ) {
			$element['id'] = \Elementor\Utils::generate_random_string();

			return $element;
		} );
	}

	function pafe_forms_functions() {
		$function = !empty($_GET['function']) ? $_GET['function'] : $_POST['function'];
		$template_id = !empty($_GET['id']) ? $_GET['id'] : '';
		$elementor = \Elementor\Plugin::$instance;

		if (!empty($template_id)) {
	        $document = $elementor->documents->get( $template_id );

			$template_data = $document->get_export_data();

			if ( empty( $template_data['content'] ) ) {
				return new \WP_Error( 'empty_template', 'The template is empty' );
			}

			$export_data = [
				'content' => $template_data['content'],
				'page_settings' => $template_data['settings'],
				'version' => \Elementor\DB::DB_VERSION,
				'title' => $document->get_main_post()->post_title,
				'type' => 'pafe-forms',
				'status' => 'publish'
			];
		}
        
        switch ($function) {
        	case 'export':

				$file_data = [
					'name' => 'elementor-' . $template_id . '-' . gmdate( 'Y-m-d' ) . '.json',
					'content' => wp_json_encode( $export_data ),
				];

				if ( is_wp_error( $file_data ) ) {
					return $file_data;
				}

				pafe_send_file_headers( $file_data['name'], strlen( $file_data['content'] ) );

				// Clear buffering just in case.
				@ob_end_clean();

				flush();

				// Output file contents.
				// PHPCS - Export widget json
				echo $file_data['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        		break;
        	
        	case 'duplicate':

        		$template = !empty($_POST['template']) ? $_POST['template'] : '';

        		if (!empty($template)) {
	        		if ($template == 'blank') {
						$export_data = [
							'content' => '',
							'page_settings' => [
								'template' => 'default'
							],
							'version' => \Elementor\DB::DB_VERSION,
							'title' => !empty($_POST['title']) ? $_POST['title'] : 'PAFE Form',
							'type' => 'pafe-forms',
							'status' => 'publish'
						];
					} else {
						$arrContextOptions=array(
						    "ssl"=>array(
						        "verify_peer"=>false,
						        "verify_peer_name"=>false,
						    ),
						);

						$file_url = WP_PLUGIN_DIR . '/piotnet-addons-for-elementor-pro/assets/forms/templates/' . $template . '/file.json';
						$file_content = file_get_contents( $file_url, false, stream_context_create($arrContextOptions) );
						$export_data = json_decode( $file_content, true );
					}
				}
				
				$my_post = array(
					'post_title'    => !empty($_POST['title']) ? $_POST['title'] : $export_data['title'] . ' (Copy)',
					'post_status'   => 'publish',
					'post_type'		=> 'pafe-forms',
				);

				if (!empty($template)) {
					$my_post['post_title'] = !empty($_POST['title']) ? $_POST['title'] : $export_data['title'];
				}

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
					echo '<script>window.location.href="' . admin_url( 'edit.php?post_type=pafe-forms' ) . '"</script>';
				}

        		break;
        }

		wp_die(); 
	}