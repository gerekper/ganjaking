<?php
/**
 * New Post form.
 *
 * @var array $fields The fields.
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Views
 */

global $post_type, $post_type_object;
?>
<div id="yith-wcbk-cpt-new-post">
	<div class="form-wrap">
		<h2><?php echo esc_html( $post_type_object->labels->add_new ); ?></h2>
		<form id="yith-wcbk-cpt-new-post-form">
			<input type="hidden" name="action" value="yith-wcbk-add-new-post">
			<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>">
			<?php wp_nonce_field( 'yith-wcbk-add-new-post', 'yith-wcbk-add-new-post-nonce' ); ?>

			<?php foreach ( $fields as $key => $field ) : ?>
				<?php
				$field['id'] = $field['id'] ?? ( 'yith-wcbk-cpt-new-post__' . $key );
				$label       = $field['label'];
				$description = $field['desc'] ?? '';
				unset( $field['label'] );
				unset( $field['desc'] );
				?>
				<div class="form-field">
					<label for="<?php echo esc_html( $field['id'] ); ?>"><?php echo esc_html( $label ); ?></label>
					<?php yith_plugin_fw_get_field( $field, true, false ); ?>
					<p class="description"><?php echo wp_kses_post( $description ); ?></p>
				</div>
			<?php endforeach; ?>

			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html( $post_type_object->labels->add_new ); ?>">
			</p>
		</form>
	</div>
</div>
<script type="text/javascript">
	(
		function ( $ ) {
			var views       = $( '.wrap ul.subsubsub' ),
				table       = $( 'form#posts-filter' ),
				newPostForm = $( '#yith-wcbk-cpt-new-post' ),
				left        = $( '<div id="col-left"></div>' ),
				right       = $( '<div id="col-right"></div>' );

			views.before( left );
			views.before( right );

			left.append( newPostForm );
			right.append( views );
			right.append( table );

		}
	)( jQuery );
</script>
