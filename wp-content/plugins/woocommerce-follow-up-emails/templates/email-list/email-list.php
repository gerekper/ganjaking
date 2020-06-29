<div class="wrap">
	<div class="icon32"><img src="<?php echo esc_url( FUE_TEMPLATES_URL ) . '/images/send_mail.png'; ?>" /></div>

	<h2>
		<?php esc_html_e( 'Follow-Up Emails', 'follow_up_emails' ); ?>
		<a class="add-new-h2" href="post-new.php?post_type=follow_up_email"><?php esc_html_e( 'Create New Follow-Up', 'follow_up_emails' ); ?></a>
	</h2>

	<?php include FUE_TEMPLATES_DIR . '/email-list/notifications.php'; ?>

	<style type="text/css">
		@media screen and (max-width: 782px) {
			th.column-priority, td.column-priority {
				display: none !important;
			}
		}
		span.priority {
			display: inline;
			padding: 1px 7px;
			background: #EAF2FA;
			border-radius: 10px;
			border: 1px solid #ddd;
		}

		.ui-sortable tr {
			cursor: move;
		}

		table.fue th, table.fue td {
			overflow: visible !important;
		}

		div.row-actions span.edit.edit-control {
			display: inline-block;
			position: relative;
		}

		ul.fue-edit-action {
			display: none;
			position: absolute;
			top: 5px;
			left: 0;
			z-index: 100;
			width: 200px;
			padding: 0;
			border: 1px solid #DADADA;
			background: #FFF;
		}

		ul.fue-edit-action li {
			display: block;
			margin: 0;
		}

		ul.fue-edit-action li:hover {
			background: #E5EEF9;
		}

		ul.fue-edit-action li a {
			padding: 5px 10px !important;
			display: block;
		}

		div.row-actions span.edit.edit-control:hover {
			position: relative;
			overflow: visible;
		}

		div.row-actions span.edit.edit-control:hover ul.fue-edit-action {
			display: block;
		}
	</style>

	<form method="get" id="fue_emails_list">

		<div class="subsubsub_section">

			<?php
				include FUE_TEMPLATES_DIR . '/email-list/class-wc-fue-list-table.php';

				$fue_table = new WC_FUE_List_table();
				$fue_table->views();
				$fue_table->prepare_items();
				$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			?>
				<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
					<?php
					$fue_table->search_box( __( 'Find', 'follow_emails' ), 'fue-emails-list' );
					$fue_table->display();
					?>

        </div>

        <?php wp_nonce_field( 'fue_emails_actions', 'nonce_fue_emails_list' ); ?>
	</form>
</div>
