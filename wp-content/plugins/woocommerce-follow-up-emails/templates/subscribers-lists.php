<div class="subscribers-container">
	<div class="subscribers-col1">
		<form id="subscribers-lists-filter" action="" method="get">
			<?php
			$list_table = new FUE_Subscribers_Lists_List_Table();
			$list_table->prepare_items();
			$list_table->messages();
			$list_table->display();
			?>
		</form>
	</div>
	<div class="subscribers-col2">
		<form action="admin-post.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="fue_subscribers_manage" />
			<?php wp_nonce_field( 'fue-subscribers-manage' ); ?>

			<div class="meta-box no-padding">
				<h3 class="handle"><?php esc_html_e('Create a List', 'follow_up_emails'); ?></h3>
				<div class="inside">
					<p>
						<input type="text" name="list_name" placeholder="List name" />
					</p>

					<div class="meta-box-actions">
						<input type="hidden" name="from_lists_table" value="1" />
						<input type="submit" name="button_create_list" class="button button-primary" value="<?php esc_attr_e('Create List', 'follow_up_emails'); ?>">
						<div class="clear"></div>
					</div>
				</div>
			</div>

		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$( '#delete-all-submit' ).on( 'click', function( e ) {
			if ( confirm("<?php esc_html_e('This will delete ALL lists! Continue?', 'follow_up_emails'); ?>") ) {
				return true;
			}
			return false;
		} );

		$( '.inline-edit' ).on( function( e ) {
			e.preventDefault();

			var id = $(this).data("id");
			$("#row_"+ id).fadeOut(function() {
				$("#edit_row_"+ id).fadeIn()
			});
		} );

		$( '.btn-cancel').on( 'click', function() {
			var tr = $(this).parents("tr.edit-row");
			var id = $(tr).data("id");

			$("#edit_row_"+ id).fadeOut(function() {
				$("#row_"+ id).fadeIn();
			});
		} );

		$( '.btn-save' ).on( 'click', function() {
			var tr = $(this).parents("tr.edit-row");
			var id = $(tr).data("id");

			var name    = $(tr).find("input.list-name").val();
			var access  = $(tr).find("select.list-access").val();

			$("#the-list").block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

			$.post( ajaxurl, {
				action: "fue_update_list",
				id: id,
				name: name,
				access: access
			}, function() {
				$("#row_"+ id +" td.column-list_name strong").html( name );

				var access_str = (access == 0) ? 'Private' : 'Public';
				$("#row_"+ id +" td.column-access").html( access_str );

				$("#edit_row_"+ id).fadeOut(function() {
					$("#row_"+ id).fadeIn();
				});

				$("#the-list").unblock();
			});
		} );

		$( 'a.submitdelete' ).on( 'click', function( e ) {
			e.preventDefault();

			if ( confirm('<?php esc_html_e('Really delete this list?', 'follow_up_emails'); ?>') ) {
				var tr = $(this).parents("tr.row");
				var id = $(tr).data("id");

				$("#the-list").block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

				$.post( ajaxurl, {
					action: "fue_delete_list",
					id: id
				}, function() {
					$("#row_"+ id).fadeOut();
					$("#spacer_row_"+ id).remove();
					$("#edit_row_"+ id).remove();

					$("#the-list").unblock();
				});
			}
		} );
	});
</script>
