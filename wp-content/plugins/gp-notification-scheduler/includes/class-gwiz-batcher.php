<?php
namespace GP_Notification_Scheduler;

use GFAPI;

class Gwiz_Batcher {

	private $_args;

	public function __construct( $args ) {

		$this->_args = wp_parse_args( $args, array(
			'title'              => 'GW Batcher',
			'id'                 => 'gw-batcher',
			'show_form_selector' => false,
		) );

		add_action( 'wp_ajax_gw_batch_' . $this->_args['id'], array( $this, 'batch' ) );
		add_action( 'wp_ajax_nopriv_gw_batch_' . $this->_args['id'], array( $this, 'batch' ) );
		add_filter( 'gform_addon_navigation', array( $this, 'add_menu_item' ) );

	}

	function add_menu_item( $menu_items ) {
		$menu_items[] = array(
			'name'       => $this->_args['id'],
			'label'      => $this->_args['title'],
			'callback'   => array( $this, 'admin_page' ),
			'permission' => 'gform_full_access',
		);

		return $menu_items;
	}

	public function admin_page() {
		?>
		<style>
			h1 {
				font-family: sans-serif;
				margin-bottom: 20px;
			}

			#gwb-preview {
				border: 1px solid #ccc;
				height: 20px;
				width: 100%;
				margin-bottom: 20px;
				padding: 2px;
				border-radius: 4px;
			}

			#gwb-preview span {
				display: block;
				height: 100%;
				width: 0;
				background-color: #999;
				border-radius: 3px;
				transition: all 0.5s ease;
			}
		</style>

		<div class="wrap">
			<h2><?php echo $this->_args['title']; ?></h2>

			<div class="notice updated" id="gwb-success" style="display: none;">
				<p><strong>Success!</strong></p>
			</div>

			<div id="gwb-preview"><span></span></div>

			<?php
			if ( $this->_args['show_form_selector'] ) {
				$forms = GFAPI::get_forms( true, false, 'title', 'ASC' );

				echo '<select name="gwb-form" id="gwb-form">';

				echo '<option value="">All Forms</option>';

				foreach ( $forms as $form ) {
					echo '<option value="' . $form['id'] . '">' . $form['title'] . '</option>';
				}

				echo '</select>';
			}
			?>

			<button id="gwb-start" class="button-primary">Start Batch</button>
		</div>

		<script>

			var ajaxUrl = '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				action = 'gw_batch_<?php echo $this->_args['id']; ?>',
				nonce = '<?php echo wp_create_nonce( "gw_batch_{$this->_args['id']}" ); ?>',
				size = <?php echo $this->_args['size']; ?>;

			(function ($) {

				var $preview = $('#gwb-preview'),
					$start = $('#gwb-start');

				$start.click(function () {
					var formId = undefined;

					$start.prop('disabled', true);

					if ($('select#gwb-form').length) {
						formId = $('select#gwb-form').val();
					}

					gwBatch(size, 1, 0, null, formId);
				});

				function gwBatch(size, page, count, total, formId) {

					$.post(ajaxUrl, {
						action: action,
						nonce: nonce,
						size: size,
						page: page,
						total: total,
						count: count,
						form_id: formId,
					}, function (response) {

						if (response.error) {
							console.log(response.data);
						} else if (response.success) {
							if (typeof response.data == 'string' && response.data == 'done') {
								$preview.find('span').width('100%');

								$('#gwb-success').show(500);
								$preview.hide(500);
								$('#gwb-start, #gwb-form').hide(500);
							} else {
								$preview.find('span').width((response.data.count / response.data.total * 100) + '%');
								gwBatch(response.data.size, response.data.page, response.data.count, response.data.total);
							}
						}

					});

				}

			})(jQuery);
		</script>
		<?php
	}

	public function batch() {

		$action  = $_POST['action'];
		$nonce   = $_POST['nonce'];
		$form_id = rgar( $_POST, 'form_id' );

		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		$size   = $_POST['size'];
		$page   = $_POST['page'];
		$offset = ( $page * $size ) - $size;
		$count  = max( 0, (int) $_POST['count'] );

		if ( $form_id ) {
			$items = $this->_args['get_items']( $size, $offset, $form_id );
		} else {
			$items = $this->_args['get_items']( $size, $offset );
		}

		$total = $items['total'];
		$items = $items['items'];

		foreach ( $items as $item ) {
			$this->_args['process_item']( $item );
			$count ++;
		}

		if ( $count >= $total ) {
			$this->_args['on_finish']( $count, $total );

			wp_send_json_success( 'done' );
		} else {
			$page ++;
			wp_send_json_success( compact( 'size', 'page', 'count', 'total' ) );
		}

	}

}
